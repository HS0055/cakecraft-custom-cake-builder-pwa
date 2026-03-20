<?php

use App\Models\System;
use App\Services\PwaIconGenerator;
use Illuminate\Support\Facades\DB;
use App\Settings\AppearanceSettings;
use App\Settings\BrandingSettings;
use App\Settings\CurrencySettings;
use App\Settings\FulfillmentSettings;
use App\Settings\GeneralSettings;
use App\Settings\PaymentSettings;
use App\Settings\OrderSettings;
use App\Settings\ReadyCakeSettings;
use App\Settings\SystemSettings;
use App\Settings\SocialMediaSettings;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts::admin', ['title' => 'System Settings'])] class extends Component {
    use WithFileUploads;

    // General
    public $store_name;
    public $store_email;
    public $store_phone;
    public $store_address;
    public $pagination_limit;

    // Currency
    public $currency_code;
    public $currency_symbol;

    // Fulfillment
    public $enable_pickup;
    public $enable_delivery;
    public $default_preparation_time;

    // Payment
    public $enable_stripe;
    public $stripe_public_key;
    public $stripe_secret_key;
    public $stripe_webhook_secret;

    public $enable_paypal;
    public $paypal_client_id;
    public $paypal_secret;
    public $paypal_mode;
    public $enable_cash;
    public $payment_methods = [];

    // Order
    public $order_sources = [];
    public $order_statuses = [];
    public $tax_percentage;
    public $delivery_fee;

    // Ready Cake
    public $ready_cake_default_is_active;
    public $ready_cake_default_is_customizable;

    // Branding
    public $logo;
    public $favicon;
    public $logo_url;
    public $favicon_url;

    // Appearance
    public $primary_color;
    public $admin_sidebar_color;

    // System
    public $maintenance_mode;

    // Social Media
    public $facebook_url;
    public $instagram_url;
    public $twitter_url;
    public $tiktok_url;
    public $whatsapp_number;

    public $activeTab = 'general';

    public function mount(
        GeneralSettings $general,
        CurrencySettings $currency,
        FulfillmentSettings $fulfillment,
        PaymentSettings $payment,
        BrandingSettings $branding,
        AppearanceSettings $appearance,
        SystemSettings $systemSettings,
        OrderSettings $orderSettings,
        ReadyCakeSettings $readyCakeSettings,
        SocialMediaSettings $socialMediaSettings
    ) {
        $this->authorize('view settings');
        $this->store_name = $general->store_name ?: 'Bawaneh Bakery';
        $this->store_email = $general->store_email ?: 'hello@bawaneh.com';
        $this->store_phone = $general->store_phone ?: '+1 (555) 123-4567';
        $this->store_address = $general->store_address ?: "123 Cake Blvd\nAmman, Jordan";
        $this->pagination_limit = $general->pagination_limit ?: 15;

        $this->currency_code = $currency->currency_code ?: 'JOD';
        $this->currency_symbol = $currency->currency_symbol ?: 'د.ا';

        $this->enable_pickup = $fulfillment->enable_pickup ?? true;
        $this->enable_delivery = $fulfillment->enable_delivery ?? true;
        $this->default_preparation_time = $fulfillment->default_preparation_time ?: 45;

        $this->enable_stripe = $payment->enable_stripe ?? false;
        $this->stripe_public_key = $payment->stripe_public_key ?: 'pk_test_demo_key_example';
        $this->stripe_secret_key = $payment->stripe_secret_key ?: 'sk_test_demo_key_example';
        $this->stripe_webhook_secret = $payment->stripe_webhook_secret ?: 'whsec_demo_key_example';

        $this->enable_paypal = $payment->enable_paypal ?? false;
        $this->paypal_client_id = $payment->paypal_client_id ?: 'demo_paypal_client_id';
        $this->paypal_secret = $payment->paypal_secret ?: 'demo_paypal_secret_key';
        $this->paypal_mode = $payment->paypal_mode ?: 'sandbox';
        $this->enable_cash = $payment->enable_cash ?? true;

        // Branding - Fetch current URLs if available
        $system = System::first();
        if ($system) {
            $this->logo_url = $system->getFirstMediaUrl('logo');
            $this->favicon_url = $system->getFirstMediaUrl('favicon');
        }

        $this->primary_color = $appearance->primary_color ?: '#F43F5E'; // Rose
        $this->admin_sidebar_color = $appearance->admin_sidebar_color ?: '#1C1917'; // Dark espresso

        $this->maintenance_mode = $systemSettings->maintenance_mode ?? false;

        // Order
        $this->order_sources = $orderSettings->sources ?: ['web' => 'Website', 'pos' => 'In-Store POS', 'app' => 'Mobile App'];
        $this->order_statuses = $orderSettings->statuses ?: ['processing' => 'Processing', 'ready' => 'Ready', 'completed' => 'Completed', 'cancelled' => 'Cancelled'];
        $this->tax_percentage = $orderSettings->tax_percentage ?: 16.00;
        $this->delivery_fee = $orderSettings->delivery_fee ?: 5.00;

        // Ready Cake
        $this->ready_cake_default_is_active = $readyCakeSettings->default_is_active ?? true;
        $this->ready_cake_default_is_customizable = $readyCakeSettings->default_is_customizable ?? true;

        // Social Media
        $this->facebook_url = $socialMediaSettings->facebook_url;
        $this->instagram_url = $socialMediaSettings->instagram_url;
        $this->twitter_url = $socialMediaSettings->twitter_url;
        $this->tiktok_url = $socialMediaSettings->tiktok_url;
        $this->whatsapp_number = $socialMediaSettings->whatsapp_number;

        // Payment Methods (Labels)
        $this->payment_methods = $payment->methods;
    }

    public function save(
        GeneralSettings $general,
        CurrencySettings $currency,
        FulfillmentSettings $fulfillment,
        PaymentSettings $payment,
        BrandingSettings $branding,
        AppearanceSettings $appearance,
        SystemSettings $systemSettings,
        OrderSettings $orderSettings,
        ReadyCakeSettings $readyCakeSettings,
        SocialMediaSettings $socialMediaSettings
    ) {
        $this->authorize('update settings');
        $this->validate([
            'store_name' => 'required|string|max:255',
            'store_email' => 'required|email:rfc|max:255',
            'store_phone' => 'nullable|string|max:255',
            'store_address' => 'nullable|string|max:255',
            'pagination_limit' => ['required', 'integer', 'min:1', 'max:100'],

            'currency_code' => 'required|string|size:3',
            'currency_symbol' => 'required|string|max:10',

            'enable_pickup' => 'boolean',
            'enable_delivery' => 'boolean',
            'default_preparation_time' => 'nullable|integer|min:0',

            'enable_stripe' => 'boolean',
            'stripe_public_key' => 'required_if:enable_stripe,true|nullable|string',
            'stripe_secret_key' => 'required_if:enable_stripe,true|nullable|string',

            'enable_paypal' => 'boolean',
            'paypal_client_id' => 'required_if:enable_paypal,true|nullable|string',
            'paypal_secret' => 'required_if:enable_paypal,true|nullable|string',
            'paypal_mode' => 'required_if:enable_paypal,true|in:sandbox,live',

            'primary_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'admin_sidebar_color' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],

            'maintenance_mode' => 'boolean',

            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'delivery_fee' => 'nullable|numeric|min:0',

            'ready_cake_default_is_active' => 'boolean',
            'ready_cake_default_is_customizable' => 'boolean',

            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'tiktok_url' => 'nullable|url|max:255',
            'whatsapp_number' => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($general, $currency, $fulfillment, $payment, $branding, $appearance, $systemSettings, $orderSettings, $readyCakeSettings, $socialMediaSettings) {
            // Helper to capture state
            $captureState = function ($settingObject, $properties) {
                $state = [];
                foreach ($properties as $prop) {
                    $state[$prop] = $settingObject->$prop;
                }
                return $state;
            };

            // Capture Old States
            $oldGeneral = $captureState($general, ['store_name', 'store_email', 'store_phone', 'store_address']);
            $oldCurrency = $captureState($currency, ['currency_code', 'currency_symbol']);
            $oldFulfillment = $captureState($fulfillment, ['enable_pickup', 'enable_delivery', 'default_preparation_time']);
            $oldPayment = $captureState($payment, ['enable_stripe', 'stripe_public_key', 'stripe_secret_key', 'stripe_webhook_secret', 'enable_paypal', 'paypal_client_id', 'paypal_secret', 'paypal_mode', 'enable_cash']);
            $oldBranding = $captureState($branding, ['logo_url', 'favicon_url']);
            $oldAppearance = $captureState($appearance, ['primary_color', 'admin_sidebar_color']);
            $oldSystem = $captureState($systemSettings, ['maintenance_mode']);
            $oldOrder = $captureState($orderSettings, ['sources', 'statuses', 'tax_percentage', 'delivery_fee']);
            $oldReadyCake = $captureState($readyCakeSettings, ['default_is_active', 'default_is_customizable']);
            $oldSocialMedia = $captureState($socialMediaSettings, ['facebook_url', 'instagram_url', 'twitter_url', 'tiktok_url', 'whatsapp_number']);


            // SAVE General
            $general->store_name = $this->store_name;
            $general->store_email = $this->store_email;
            $general->store_phone = $this->store_phone;
            $general->store_address = $this->store_address;
            $general->pagination_limit = (int) $this->pagination_limit;
            $general->save();
            $this->logChanges('general', $oldGeneral, $captureState($general, array_keys($oldGeneral)));

            // SAVE Currency
            $currency->currency_code = $this->currency_code;
            $currency->currency_symbol = $this->currency_symbol;
            $currency->save();
            $this->logChanges('currency', $oldCurrency, $captureState($currency, array_keys($oldCurrency)));

            // SAVE Fulfillment
            $fulfillment->enable_pickup = $this->enable_pickup;
            $fulfillment->enable_delivery = $this->enable_delivery;
            $fulfillment->default_preparation_time = (int) $this->default_preparation_time;
            $fulfillment->save();
            $this->logChanges('fulfillment', $oldFulfillment, $captureState($fulfillment, array_keys($oldFulfillment)));

            // SAVE Payment
            $payment->enable_stripe = $this->enable_stripe;
            $payment->stripe_public_key = $this->stripe_public_key;
            $payment->stripe_secret_key = $this->stripe_secret_key;
            $payment->stripe_webhook_secret = $this->stripe_webhook_secret;
            $payment->enable_paypal = $this->enable_paypal;
            $payment->paypal_client_id = $this->paypal_client_id;
            $payment->paypal_secret = $this->paypal_secret;
            $payment->paypal_mode = $this->paypal_mode;
            $payment->enable_cash = $this->enable_cash;
            $payment->save();
            $this->logChanges('payment', $oldPayment, $captureState($payment, array_keys($oldPayment)));

            // Handle Media Uploads & Branding
            $system = System::first();
            if (!$system) {
                $system = System::create(['id' => 1]);
            }

            if ($this->logo) {
                $logoRealPath = $this->logo->getRealPath();
                $system->addMedia($this->logo)->toMediaCollection('logo');
                $branding->logo_url = $system->getFirstMediaUrl('logo');

                // Regenerate PWA icons from new logo
                $mediaPath = $system->getFirstMedia('logo')?->getPath();
                if ($mediaPath && file_exists($mediaPath)) {
                    PwaIconGenerator::generateFromLogo($mediaPath);
                } elseif ($logoRealPath && file_exists($logoRealPath)) {
                    PwaIconGenerator::generateFromLogo($logoRealPath);
                }
            }

            if ($this->favicon) {
                $faviconRealPath = $this->favicon->getRealPath();
                $system->addMedia($this->favicon)->toMediaCollection('favicon');
                $branding->favicon_url = $system->getFirstMediaUrl('favicon');

                // Update public/favicon.ico and fallback logos
                $mediaPath = $system->getFirstMedia('favicon')?->getPath();
                if ($mediaPath && file_exists($mediaPath)) {
                    PwaIconGenerator::updateFavicon($mediaPath);
                } elseif ($faviconRealPath && file_exists($faviconRealPath)) {
                    PwaIconGenerator::updateFavicon($faviconRealPath);
                }
            }
            $branding->save();
            $this->logChanges('branding', $oldBranding, $captureState($branding, array_keys($oldBranding)));


            // SAVE Appearance
            $appearance->primary_color = $this->primary_color;
            $appearance->admin_sidebar_color = $this->admin_sidebar_color;
            $appearance->save();
            $this->logChanges('appearance', $oldAppearance, $captureState($appearance, array_keys($oldAppearance)));

            // SAVE System
            $systemSettings->maintenance_mode = $this->maintenance_mode;
            $systemSettings->save();
            $this->logChanges('system', $oldSystem, $captureState($systemSettings, array_keys($oldSystem)));

            // SAVE Order
            $orderSettings->sources = $this->order_sources;
            $orderSettings->statuses = $this->order_statuses;
            $orderSettings->tax_percentage = (float) $this->tax_percentage;
            $orderSettings->delivery_fee = (float) $this->delivery_fee;
            $orderSettings->save();
            $this->logChanges('order', $oldOrder, $captureState($orderSettings, array_keys($oldOrder)));

            // SAVE Ready Cake
            $readyCakeSettings->default_is_active = $this->ready_cake_default_is_active;
            $readyCakeSettings->default_is_customizable = $this->ready_cake_default_is_customizable;
            $readyCakeSettings->save();
            $this->logChanges('ready_cake', $oldReadyCake, $captureState($readyCakeSettings, array_keys($oldReadyCake)));

            // SAVE Social Media
            $socialMediaSettings->facebook_url = $this->facebook_url;
            $socialMediaSettings->instagram_url = $this->instagram_url;
            $socialMediaSettings->twitter_url = $this->twitter_url;
            $socialMediaSettings->tiktok_url = $this->tiktok_url;
            $socialMediaSettings->whatsapp_number = $this->whatsapp_number;
            $socialMediaSettings->save();
            $this->logChanges('social_media', $oldSocialMedia, $captureState($socialMediaSettings, array_keys($oldSocialMedia)));
        }); // end DB::transaction

        session()->flash('success', __('admin.settings.saved_successfully'));

        // Refresh URLs
        $system = System::first();
        $this->logo_url = $system?->getFirstMediaUrl('logo') ?? '';
        $this->favicon_url = $system?->getFirstMediaUrl('favicon') ?? '';
        $this->logo = null;
        $this->favicon = null;
    }

    private function logChanges(string $group, array $oldValues, array $newValues): void
    {
        foreach ($oldValues as $key => $oldVal) {
            $newVal = $newValues[$key] ?? null;

            // Strict comparison to detect changes
            if ($oldVal !== $newVal) {
                \App\Models\SettingsAuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'updated',
                    'group' => $group,
                    'key' => $key,
                    'old_value' => $oldVal,
                    'new_value' => $newVal,
                    'ip_address' => request()->ip(),
                ]);
            }
        }
    }

    public function resetTab(string $tab)
    {
        switch ($tab) {
            case 'general':
                $this->store_name = 'CakeCraft';
                $this->store_email = 'admin@cakecraft.test';
                $this->store_phone = '';
                $this->store_address = '';
                $this->pagination_limit = 15;
                break;
            case 'currency':
                $this->currency_code = 'USD';
                $this->currency_symbol = '$';
                break;
            case 'order':
                $this->order_sources = ['call_center' => 'Call Center', 'web' => 'Web', 'mobile' => 'Mobile'];
                $this->order_statuses = ['pending' => 'Pending', 'confirmed' => 'Confirmed', 'paid' => 'Paid', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled'];
                $this->tax_percentage = 0.0;
                $this->delivery_fee = 0.0;
                break;
            case 'fulfillment':
                $this->enable_pickup = true;
                $this->enable_delivery = false;
                $this->default_preparation_time = 0;
                break;
            case 'payment':
                $this->enable_stripe = false;
                $this->stripe_public_key = null;
                $this->stripe_secret_key = null;
                $this->stripe_webhook_secret = null;
                $this->enable_paypal = false;
                $this->paypal_client_id = null;
                $this->paypal_secret = null;
                $this->paypal_mode = 'sandbox';
                $this->enable_cash = true;
                break;
            case 'branding':
                $this->logo = null;
                $this->favicon = null;
                $this->logo_url = null;
                $this->favicon_url = null;
                break;
            case 'appearance':
                $this->primary_color = '#F43F5E';
                $this->admin_sidebar_color = '#2D2D2D';
                break;
            case 'social_media':
                $this->facebook_url = null;
                $this->instagram_url = null;
                $this->twitter_url = null;
                $this->tiktok_url = null;
                $this->whatsapp_number = null;
                break;
            case 'ready_cake':
                $this->ready_cake_default_is_active = true;
                $this->ready_cake_default_is_customizable = true;
                break;
            case 'system':
                $this->maintenance_mode = false;
                break;
        }

        // Let the user know the forms were reset but not saved yet
        $this->dispatch('reset-notification');
    }
};
