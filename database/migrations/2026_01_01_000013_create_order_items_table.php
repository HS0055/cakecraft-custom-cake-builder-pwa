<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            // Ready Cake Option
            $table->foreignId('ready_cake_id')->nullable()->constrained('ready_cakes')->nullOnDelete();

            // Custom Cake Option
            $table->foreignId('cake_shape_id')->nullable()->constrained('cake_shapes')->nullOnDelete();
            $table->foreignId('cake_flavor_id')->nullable()->constrained('cake_flavors')->nullOnDelete();
            $table->foreignId('cake_color_id')->nullable()->constrained('cake_colors')->nullOnDelete();
            $table->foreignId('cake_topping_id')->nullable()->constrained('cake_toppings')->nullOnDelete();

            // Pricing Snapshot
            $table->decimal('base_price', 10, 2)->default(0);
            $table->decimal('extra_price', 10, 2)->default(0);
            $table->decimal('topping_price', 10, 2)->default(0);
            $table->decimal('final_price', 10, 2);
            $table->integer('quantity')->default(1);

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
