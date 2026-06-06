<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $schema) {
            $schema->id();
            $schema->foreignId('user_id')->constrained()->onDelete('cascade');
            $schema->foreignId('unit_id')->constrained()->onDelete('cascade');
            $schema->text('body');
            $schema->boolean('is_from_admin')->default(false);
            $schema->boolean('is_automated')->default(false);
            $schema->timestamp('read_at')->nullable();
            $schema->timestamps();
            $schema->softDeletes();

            // Index for efficient thread retrieval
            $schema->index(['user_id', 'unit_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
