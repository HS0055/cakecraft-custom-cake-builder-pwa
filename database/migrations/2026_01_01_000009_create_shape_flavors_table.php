<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shape_flavors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cake_shape_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cake_flavor_id')->constrained()->cascadeOnDelete();
            $table->string('full_image')->nullable();
            $table->string('cut_image')->nullable();
            $table->decimal('extra_price', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shape_flavors');
    }
};
