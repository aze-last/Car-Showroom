<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('is_employee')->default(false)->after('is_admin');
            $table->string('job_title')->default('Showroom Staff')->after('name');
            $table->string('phone', 32)->nullable()->after('email');
            $table->string('preferred_locale', 10)->default('en_PH')->after('phone');
            $table->string('preferred_timezone', 64)->default('Asia/Manila')->after('preferred_locale');

            $table->index('is_employee');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex(['is_employee']);
            $table->dropColumn([
                'is_employee',
                'job_title',
                'phone',
                'preferred_locale',
                'preferred_timezone',
            ]);
        });
    }
};
