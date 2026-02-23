<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt — {{ $order->order_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; padding: 20px; max-width: 400px; margin: auto; }
        .hotel-name { font-size: 18px; font-weight: bold; text-align: center; margin-bottom: 4px; }
        .header { text-align: center; border-bottom: 1px solid #333; padding-bottom: 10px; margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        td { padding: 4px 0; }
        .text-right { text-align: right; }
        .divider { border-top: 1px dashed #ccc; margin: 8px 0; }
        .total-row td { font-weight: bold; font-size: 13px; border-top: 1px solid #333; padding-top: 6px; }
        .footer { text-align: center; margin-top: 16px; border-top: 1px dashed #ddd; padding-top: 10px; font-size: 11px; color: #666; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>

<div class="header">
    <div class="hotel-name">🏨 HOTEL NAME</div>
    <p style="font-size: 11px;">Dar es Salaam, Tanzania</p>
    <p style="font-size: 13px; font-weight: bold; margin-top: 6px;">
        {{ strtoupper($order->location->name ?? 'RESTAURANT') }} RECEIPT
    </p>
    <p>Order: {{ $order->order_number }}</p>
    <p>{{ $order->created_at->format('d M Y H:i') }}</p>
    @if($order->table) <p>Table: {{ $order->table->table_number }}</p> @endif
</div>

<table>
    <thead>
        <tr>
            <td><strong>Item</strong></td>
            <td class="text-right"><strong>Qty</strong></td>
            <td class="text-right"><strong>Price</strong></td>
            <td class="text-right"><strong>Total</strong></td>
        </tr>
    </thead>
    <tbody>
        @foreach($order->items->where('status', '!=', 'cancelled') as $item)
        <tr>
            <td>{{ $item->menuItem->name }}</td>
            <td class="text-right">{{ $item->quantity }}</td>
            <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
            <td class="text-right">{{ number_format($item->subtotal, 2) }}</td>
        </tr>
        @if($item->notes)
        <tr><td colspan="4" style="font-size: 10px; color: #888; padding-left: 8px;">↳ {{ $item->notes }}</td></tr>
        @endif
        @endforeach
    </tbody>
</table>

<div class="divider"></div>

<table>
    <tr class="total-row">
        <td>TOTAL</td>
        <td class="text-right">USD {{ number_format($order->total, 2) }}</td>
    </tr>
    <tr>
        <td style="color: #666;">In TZS</td>
        <td class="text-right" style="color: #666;">TZS {{ number_format($order->total * $exchangeRate, 0) }}</td>
    </tr>
    @if($payment)
    <tr style="height:6px;"></tr>
    <tr>
        <td style="color: #666;">Paid ({{ ucfirst($payment->method) }})</td>
        <td class="text-right">
            {{ $payment->currency }} {{ number_format($payment->amount, 2) }}
        </td>
    </tr>
    @endif
</table>

<div class="footer">
    <p>Thank you for your visit!</p>
    <p style="margin-top: 4px;">Rate: 1 USD = {{ number_format($exchangeRate, 0) }} TZS</p>
    <p style="margin-top: 4px; font-size: 10px;">Served by: {{ auth()->user()->name }}</p>
</div>

<div class="no-print" style="margin-top: 16px; text-align: center;">
    <button onclick="window.print()"
            style="background: #2563eb; color: white; padding: 8px 20px; border: none; border-radius: 5px; cursor: pointer;">
        🖨️ Print
    </button>
</div>

</body>
</html>
