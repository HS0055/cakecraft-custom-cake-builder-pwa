<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cake_colors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('hex_code');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cake_colors');
    }
};
