<?php

namespace App\Services\Billing;

use App\Helpers\CurrencyHelper;
use App\Models\Booking;
use App\Models\BookingCharge;
use App\Models\BuffetSale;
use App\Models\LaundryOrder;
use App\Models\Order;
use App\Services\AccountingService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ModuleBillingService
{
    public function syncBookingChargesForBooking(Booking $booking, ?string $actorId = null): void
    {
        Order::query()
            ->where('booking_id', $booking->id)
            ->whereIn('status', ['served', 'charged'])
            ->get()
            ->each(fn (Order $order) => $this->syncOrderCharge($order, $actorId));

        LaundryOrder::query()
            ->where('booking_id', $booking->id)
            ->whereIn('status', ['delivered', 'charged'])
            ->get()
            ->each(fn (LaundryOrder $order) => $this->syncLaundryCharge($order, $actorId));
    }

    public function syncOrderCharge(Order $order, ?string $actorId = null): ?BookingCharge
    {
        if (!$order->booking_id || $order->status === 'cancelled') {
            return null;
        }

        [$chargeType, $source, $description] = $this->describeOrderCharge($order);
        [$amountUsd, $exchangeRate] = $this->convertTzsToUsd((float) $order->total);

        $charge = BookingCharge::query()
            ->where('booking_id', $order->booking_id)
            ->where('reference_id', $order->id)
            ->where('source', $source)
            ->first();

        if ($charge?->status === 'paid') {
            if ((float) $charge->amount_tzs !== (float) $order->total) {
                Log::warning('Paid module charge total mismatch after sync attempt.', [
                    'order_id' => $order->id,
                    'charge_id' => $charge->id,
                    'charge_total_tzs' => $charge->amount_tzs,
                    'order_total_tzs' => $order->total,
                ]);
            }

            return $charge;
        }

        $payload = [
            'booking_id' => $order->booking_id,
            'order_id' => $order->id,
            'charge_type' => $chargeType,
            'source' => $source,
            'reference_id' => $order->id,
            'description' => $description,
            'amount' => $amountUsd,
            'currency' => 'USD',
            'amount_tzs' => $order->total,
            'status' => 'unpaid',
            'created_by' => $actorId,
        ];

        if ($charge) {
            $charge->update(collect($payload)->except('created_by')->all());
        } else {
            $charge = BookingCharge::create($payload);
        }

        $order->update(['billed_to_folio_at' => now(), 'billing_error' => null]);

        return $charge;
    }

    public function syncLaundryCharge(LaundryOrder $order, ?string $actorId = null): ?BookingCharge
    {
        if (!$order->booking_id || $order->status === 'cancelled') {
            return null;
        }

        [$amountUsd] = $this->convertTzsToUsd((float) $order->total);

        $charge = BookingCharge::query()
            ->where('booking_id', $order->booking_id)
            ->where('reference_id', $order->id)
            ->where('source', 'laundry')
            ->first();

        if ($charge?->status === 'paid') {
            return $charge;
        }

        $payload = [
            'booking_id' => $order->booking_id,
            'charge_type' => 'laundry',
            'source' => 'laundry',
            'reference_id' => $order->id,
            'description' => "Laundry Order {$order->order_number} - {$order->items()->count()} item(s)",
            'amount' => $amountUsd,
            'currency' => 'USD',
            'amount_tzs' => $order->total,
            'status' => 'unpaid',
            'created_by' => $actorId,
        ];

        if ($charge) {
            $charge->update(collect($payload)->except('created_by')->all());
        } else {
            $charge = BookingCharge::create($payload);
        }

        return $charge;
    }

    public function voidChargeForOrder(Order $order): void
    {
        BookingCharge::query()
            ->where('reference_id', $order->id)
            ->whereIn('status', ['unpaid'])
            ->delete();
    }

    public function voidChargeForLaundry(LaundryOrder $order): void
    {
        BookingCharge::query()
            ->where('reference_id', $order->id)
            ->where('source', 'laundry')
            ->whereIn('status', ['unpaid'])
            ->delete();
    }

    public function finalizeCharges(Collection $charges, string $paymentMethod, string $actorId): void
    {
        foreach ($charges as $charge) {
            if (in_array($charge->charge_type, ['restaurant', 'bar', 'room_service'], true)) {
                $orderId = $charge->order_id ?? $charge->reference_id;
                $order = $orderId ? Order::find($orderId) : null;

                if ($order && $order->status !== 'settled') {
                    $nextBartenderStatus = $order->bartender_status
                        ? ($order->bartender_status === 'cancelled' ? 'cancelled' : 'served')
                        : null;

                    $order->update([
                        'status' => 'settled',
                        'bartender_status' => $nextBartenderStatus,
                        'bartender_status_updated_at' => $nextBartenderStatus ? now() : $order->bartender_status_updated_at,
                        'payment_method' => $paymentMethod,
                        'settled_by' => $actorId,
                        'settled_at' => now(),
                        'guest_completed_at' => $order->booking_id ? now() : $order->guest_completed_at,
                    ]);

                    app(AccountingService::class)->postRestaurantSettlement(
                        orderNo: $order->order_number,
                        orderId: $order->id,
                        amount: (float) $order->total,
                        paymentMethod: $paymentMethod,
                        actorId: $actorId
                    );
                }
            }

            if ($charge->charge_type === 'laundry') {
                $laundryOrder = $charge->reference_id ? LaundryOrder::find($charge->reference_id) : null;

                if ($laundryOrder && $laundryOrder->status !== 'settled') {
                    $laundryOrder->update([
                        'status' => 'settled',
                        'payment_method' => $paymentMethod,
                        'settled_by' => $actorId,
                        'settled_at' => now(),
                    ]);

                    app(AccountingService::class)->postLaundrySettlement(
                        orderNo: $laundryOrder->order_number,
                        orderId: $laundryOrder->id,
                        amount: (float) $laundryOrder->total,
                        paymentMethod: $paymentMethod,
                        actorId: $actorId
                    );
                }
            }

            // Finalize BuffetSale charges (charge_type='restaurant', reference_id = BuffetSale id)
            if ($charge->charge_type === 'restaurant' && !$charge->order_id && $charge->reference_id) {
                $buffetSale = BuffetSale::find($charge->reference_id);
                if ($buffetSale && $buffetSale->status === 'charged') {
                    $buffetSale->update([
                        'status' => 'settled',
                        'settled_at' => now(),
                    ]);
                }
            }
        }
    }

    protected function describeOrderCharge(Order $order): array
    {
        if ($order->order_source === 'room_service') {
            return ['room_service', 'room_service', "Room service drinks {$order->order_number}"];
        }

        $isBar = str_contains(strtolower($order->location?->code ?? ''), 'bar');

        if ($isBar) {
            return ['bar', 'bar', "Bar order {$order->order_number}"];
        }

        return ['restaurant', 'restaurant', "Restaurant order {$order->order_number}"];
    }

    protected function convertTzsToUsd(float $amountTzs): array
    {
        $exchangeRate = CurrencyHelper::getExchangeRate();

        return [round($amountTzs / $exchangeRate, 2), $exchangeRate];
    }
}
