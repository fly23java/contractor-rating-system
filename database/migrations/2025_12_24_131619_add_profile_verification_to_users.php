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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_profile_verified')->default(false)->after('role');
            $table->text('profile_verification_notes')->nullable()->after('is_profile_verified');
            $table->timestamp('profile_verified_at')->nullable()->after('profile_verification_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_profile_verified', 'profile_verification_notes', 'profile_verified_at']);
        });
    }
};
