<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unit_status_logs', function (Blueprint $table): void {
            $table->uuid('request_id')->nullable()->after('to_status');
            $table->string('reason')->nullable()->after('request_id');

            $table->index('request_id');
        });
    }

    public function down(): void
    {
        Schema::table('unit_status_logs', function (Blueprint $table): void {
            $table->dropIndex(['request_id']);
            $table->dropColumn(['request_id', 'reason']);
        });
    }
};
