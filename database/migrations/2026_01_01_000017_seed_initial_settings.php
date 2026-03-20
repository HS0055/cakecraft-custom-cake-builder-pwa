<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        // General
        $this->migrator->add('general.store_name', 'CakeCraft');
        $this->migrator->add('general.store_email', 'admin@cakecraft.test');
        $this->migrator->add('general.store_phone', '');
        $this->migrator->add('general.store_address', '');
        $this->migrator->add('general.pagination_limit', 15);

        // Currency
        $this->migrator->add('currency.currency_code', 'USD');
        $this->migrator->add('currency.currency_symbol', '$');

        // Fulfillment
        $this->migrator->add('fulfillment.enable_pickup', true);
        $this->migrator->add('fulfillment.enable_delivery', false);
        $this->migrator->add('fulfillment.default_preparation_time', 0);
        $this->migrator->add('fulfillment.types', [
            'pickup' => 'Pickup',
            'delivery' => 'Delivery',
        ]);

        // Payment
        $this->migrator->add('payment.enable_stripe', false);
        $this->migrator->add('payment.enable_paypal', false);
        $this->migrator->add('payment.enable_cash', true);
        $this->migrator->add('payment.stripe_public_key', null);
        $this->migrator->add('payment.stripe_secret_key', null);
        $this->migrator->add('payment.stripe_webhook_secret', null);
        $this->migrator->add('payment.paypal_client_id', null);
        $this->migrator->add('payment.paypal_secret', null);
        $this->migrator->add('payment.paypal_mode', 'sandbox');
        $this->migrator->add('payment.methods', [
            'cash' => 'Cash',
            'card' => 'Card',
            'online' => 'Online',
        ]);

        // Branding
        $this->migrator->add('branding.logo_url', null);
        $this->migrator->add('branding.favicon_url', null);

        // Appearance
        $this->migrator->add('appearance.primary_color', '#ff70a2');
        $this->migrator->add('appearance.admin_sidebar_color', '#2D2D2D');

        // System
        $this->migrator->add('system.maintenance_mode', false);

        // Order
        $this->migrator->add('order.sources', [
            'call_center' => 'Call Center',
            'web' => 'Web',
            'mobile' => 'Mobile',
        ]);
        $this->migrator->add('order.statuses', [
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'paid' => 'Paid',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ]);
        $this->migrator->add('order.tax_percentage', 0.0);
        $this->migrator->add('order.delivery_fee', 0.0);

        // Ready Cake
        $this->migrator->add('ready_cake.default_is_active', true);
        $this->migrator->add('ready_cake.default_is_customizable', true);
    }
};
