<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action'); // created, updated, deleted
            $table->string('group'); // payment, branding, etc.
            $table->string('key'); // setting key
            $table->text('old_value')->nullable(); // JSON or text
            $table->text('new_value')->nullable(); // JSON or text
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'group', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings_audit_logs');
    }
};
