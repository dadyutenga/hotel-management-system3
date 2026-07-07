<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add critical database indexes for query performance.
     *
     * These indexes target the most frequently filtered columns identified
     * during the performance audit (2026-07-05). Each index eliminates
     * sequential scans on high-traffic tables.
     */
    public function up(): void
    {
        // ── rooms ────────────────────────────────────────────────────────
        // Frequently filtered by status (available/occupied/dirty/out_of_order)
        // and joined via room_type_id
        DB::statement('CREATE INDEX IF NOT EXISTS idx_rooms_status ON rooms (status)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_rooms_is_active ON rooms (is_active)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_rooms_room_type_id ON rooms (room_type_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_rooms_cleaning_assigned_to ON rooms (cleaning_assigned_to)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_rooms_status_is_active ON rooms (status, is_active)');

        // ── reservations ──────────────────────────────────────────────────
        // Daily arrival reports, availability checks, front desk views
        DB::statement('CREATE INDEX IF NOT EXISTS idx_reservations_status ON reservations (status)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_reservations_check_in_date ON reservations (check_in_date)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_reservations_check_out_date ON reservations (check_out_date)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_reservations_room_id ON reservations (room_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_reservations_guest_id ON reservations (guest_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_reservations_booking_id ON reservations (booking_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_reservations_created_by ON reservations (created_by)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_reservations_created_at ON reservations (created_at)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_reservations_status_check_in ON reservations (status, check_in_date)');

        // ── bookings ──────────────────────────────────────────────────────
        // Revenue queries, daily check-in/out lists, guest lookups
        DB::statement('CREATE INDEX IF NOT EXISTS idx_bookings_guest_id ON bookings (guest_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_bookings_room_id ON bookings (room_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_bookings_reservation_id ON bookings (reservation_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_bookings_created_by ON bookings (created_by)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_bookings_created_at ON bookings (created_at)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_bookings_total_amount ON bookings (total_amount)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_bookings_status_amount ON bookings (status, total_amount)');

        // ── orders (restaurant/bar) ──────────────────────────────────────
        // Daily sales, order listing, settlement reports
        DB::statement('CREATE INDEX IF NOT EXISTS idx_orders_status ON orders (status)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_orders_location_id ON orders (location_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_orders_table_id ON orders (table_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_orders_booking_id ON orders (booking_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_orders_room_id ON orders (room_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_orders_created_by ON orders (created_by)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_orders_created_at ON orders (created_at)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_orders_settled_at ON orders (settled_at)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_orders_settled_by ON orders (settled_by)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_orders_order_type ON orders (order_type)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_orders_order_source ON orders (order_source)');

        // ── laundry_orders ────────────────────────────────────────────────
        // Workflow filtering by status, daily reports
        DB::statement('CREATE INDEX IF NOT EXISTS idx_laundry_orders_status ON laundry_orders (status)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_laundry_orders_booking_id ON laundry_orders (booking_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_laundry_orders_created_at ON laundry_orders (created_at)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_laundry_orders_received_by ON laundry_orders (received_by)');

        // ── stock_levels ──────────────────────────────────────────────────
        // Low stock alerts, reorder queries
        DB::statement('CREATE INDEX IF NOT EXISTS idx_stock_levels_quantity ON stock_levels (quantity)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_stock_levels_location_id ON stock_levels (location_id)');

        // ── stock_movements ──────────────────────────────────────────────
        // Audit trail, location-based lookups
        DB::statement('CREATE INDEX IF NOT EXISTS idx_stock_movements_created_by ON stock_movements (created_by)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_stock_movements_location_id ON stock_movements (location_id)');

        // ── products ──────────────────────────────────────────────────────
        // Active product filtering, reorder level checks
        DB::statement('CREATE INDEX IF NOT EXISTS idx_products_is_active ON products (is_active)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_products_reorder_level ON products (reorder_level)');

        // ── users ────────────────────────────────────────────────────────
        // Role-based authorization checks, active staff queries
        DB::statement('CREATE INDEX IF NOT EXISTS idx_users_role_id ON users (role_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_users_is_active ON users (is_active)');

        // ── store_notifications ──────────────────────────────────────────
        // Unread notification queries
        DB::statement('CREATE INDEX IF NOT EXISTS idx_store_notifications_type ON store_notifications (type)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_store_notifications_reference ON store_notifications (reference_type, reference_id)');

        // ── stock_transfers ──────────────────────────────────────────────
        // Transfer workflow queries
        DB::statement('CREATE INDEX IF NOT EXISTS idx_stock_transfers_status ON stock_transfers (status)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_stock_transfers_from_location ON stock_transfers (from_location_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_stock_transfers_to_location ON stock_transfers (to_location_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_stock_transfers_requested_by ON stock_transfers (requested_by)');

        // ── order_items ──────────────────────────────────────────────────
        // Order detail lookups
        DB::statement('CREATE INDEX IF NOT EXISTS idx_order_items_order_id ON order_items (order_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_order_items_menu_item_id ON order_items (menu_item_id)');

        // ── payments ─────────────────────────────────────────────────────
        // Payment history and reports
        DB::statement('CREATE INDEX IF NOT EXISTS idx_payments_created_at ON payments (created_at)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_payments_created_by ON payments (created_by)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_payments_reference_id ON payments (reference_id)');

        // ── menu_items ───────────────────────────────────────────────────
        // Menu display filtering
        DB::statement('CREATE INDEX IF NOT EXISTS idx_menu_items_category_id ON menu_items (category_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_menu_items_is_available ON menu_items (is_available)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_menu_items_is_active ON menu_items (is_active)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_menu_items_destination ON menu_items (destination)');

        // ── suppliers ────────────────────────────────────────────────────
        // Supplier lookups
        DB::statement('CREATE INDEX IF NOT EXISTS idx_suppliers_name ON suppliers (name)');

        // ── local_purchase_orders ────────────────────────────────────────
        // LPO workflow
        DB::statement('CREATE INDEX IF NOT EXISTS idx_lpo_supplier_id ON local_purchase_orders (supplier_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_lpo_created_by ON local_purchase_orders (created_by)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_lpo_created_at ON local_purchase_orders (created_at)');

        // ── goods_received_notes ────────────────────────────────────────
        // GRN workflow
        DB::statement('CREATE INDEX IF NOT EXISTS idx_grn_lpo_id ON goods_received_notes (lpo_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_grn_supplier_id ON goods_received_notes (supplier_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_grn_received_by ON goods_received_notes (received_by)');
    }

    /**
     * Reverse the migration — drop all created indexes.
     */
    public function down(): void
    {
        $indexes = [
            'rooms' => ['status', 'is_active', 'room_type_id', 'cleaning_assigned_to', 'status_is_active'],
            'reservations' => ['status', 'check_in_date', 'check_out_date', 'room_id', 'guest_id', 'booking_id', 'created_by', 'created_at', 'status_check_in'],
            'bookings' => ['guest_id', 'room_id', 'reservation_id', 'created_by', 'created_at', 'total_amount', 'status_amount'],
            'orders' => ['status', 'location_id', 'table_id', 'booking_id', 'room_id', 'created_by', 'created_at', 'settled_at', 'settled_by', 'order_type', 'order_source'],
            'laundry_orders' => ['status', 'booking_id', 'created_at', 'received_by'],
            'stock_levels' => ['quantity', 'location_id'],
            'stock_movements' => ['created_by', 'location_id'],
            'products' => ['is_active', 'reorder_level'],
            'users' => ['role_id', 'is_active'],
            'store_notifications' => ['type', 'reference'],
            'stock_transfers' => ['status', 'from_location', 'to_location', 'requested_by'],
            'order_items' => ['order_id', 'menu_item_id'],
            'payments' => ['created_at', 'created_by', 'reference_id'],
            'menu_items' => ['category_id', 'is_available', 'is_active', 'destination'],
            'suppliers' => ['name'],
            'local_purchase_orders' => ['supplier_id', 'created_by', 'created_at'],
            'goods_received_notes' => ['lpo_id', 'supplier_id', 'received_by'],
        ];

        foreach ($indexes as $table => $names) {
            foreach ($names as $name) {
                DB::statement("DROP INDEX IF EXISTS idx_{$table}_{$name}");
            }
        }
    }
};
