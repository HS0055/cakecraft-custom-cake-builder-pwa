<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->string('order_source'); // call_center, web, mobile
            $table->string('fulfillment_type'); // pickup, delivery
            $table->dateTime('scheduled_at');
            $table->text('address_text')->nullable();
            $table->string('payment_method'); // cash, card, online
            $table->text('notes')->nullable();
            $table->decimal('subtotal_price', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2)->default(0);
            $table->string('payment_id')->nullable();
            $table->string('status')->default('pending'); // pending, confirmed, paid, in_progress, completed, cancelled
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
