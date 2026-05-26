<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bids', function (Blueprint $row) {
            $row->id();
            $row->foreignId('auction_id')->constrained()->cascadeOnDelete();
            $row->foreignId('user_id')->constrained()->cascadeOnDelete();
            $row->unsignedBigInteger('amount_php');
            $row->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bids');
    }
};
