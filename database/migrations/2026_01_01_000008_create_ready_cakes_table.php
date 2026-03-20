<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ready_cakes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('cake_shape_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cake_flavor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cake_color_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cake_topping_id')->nullable()->constrained()->nullOnDelete();
            $table->string('custom_color_hex', 7)->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_customizable')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ready_cakes');
    }
};
