<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The original enum ('scheduled', 'live', 'completed', 'cancelled') is too
     * restrictive: reserve enforcement needs a distinct 'reserve_not_met' state
     * (and 'active' is already used in code). A plain string keeps the valid
     * values enforced in application code (Auction::STATUSES).
     */
    public function up(): void
    {
        Schema::table('auctions', function (Blueprint $table) {
            $table->string('status', 32)->default('scheduled')->change();
        });
    }

    public function down(): void
    {
        Schema::table('auctions', function (Blueprint $table) {
            $table->enum('status', ['scheduled', 'live', 'completed', 'cancelled'])->default('scheduled')->change();
        });
    }
};
