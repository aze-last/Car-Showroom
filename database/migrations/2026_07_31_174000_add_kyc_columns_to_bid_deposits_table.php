<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bid_deposits', function (Blueprint $table) {
            $table->string('full_name')->nullable()->after('auction_id');
            $table->string('phone')->nullable()->after('full_name');
            $table->timestamp('phone_verified_at')->nullable()->after('phone');
            $table->string('email')->nullable()->after('phone_verified_at');
            $table->text('address')->nullable()->after('email');
            $table->decimal('latitude', 10, 7)->nullable()->after('address');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('bid_deposits', function (Blueprint $table) {
            $table->dropColumn([
                'full_name',
                'phone',
                'phone_verified_at',
                'email',
                'address',
                'latitude',
                'longitude',
            ]);
        });
    }
};
