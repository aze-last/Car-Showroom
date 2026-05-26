<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auctions', function (Blueprint $row) {
            $row->id();
            $row->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $row->string('lot_number')->unique();
            $row->dateTime('start_at');
            $row->dateTime('end_at');
            $row->unsignedBigInteger('reserve_price_php');
            $row->unsignedBigInteger('starting_bid_php');
            $row->unsignedBigInteger('current_bid_php')->default(0);
            $row->enum('status', ['scheduled', 'live', 'completed', 'cancelled'])->default('scheduled');
            $row->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auctions');
    }
};
