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
        Schema::table('units', function (Blueprint $table) {
            $table->integer('year')->nullable()->after('description');
            $table->integer('mileage')->nullable()->after('year');
            $table->string('transmission')->nullable()->after('mileage');
            $table->string('fuel_type')->nullable()->after('transmission');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn(['year', 'mileage', 'transmission', 'fuel_type']);
        });
    }
};
