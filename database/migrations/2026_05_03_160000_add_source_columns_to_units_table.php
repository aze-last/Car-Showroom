<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('units', function (Blueprint $table): void {
            $table->string('source_name')->nullable()->after('fuel_type');
            $table->string('source_external_id')->nullable()->after('source_name');
            $table->string('source_url')->nullable()->after('source_external_id');

            $table->index(['source_name', 'source_external_id']);
        });
    }

    public function down(): void
    {
        Schema::table('units', function (Blueprint $table): void {
            $table->dropIndex(['source_name', 'source_external_id']);
            $table->dropColumn([
                'source_name',
                'source_external_id',
                'source_url',
            ]);
        });
    }
};
