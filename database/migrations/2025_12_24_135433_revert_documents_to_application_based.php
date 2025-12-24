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
        Schema::table('contractor_documents', function (Blueprint $table) {
            // Remove user_id and add application_id back
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            
            $table->foreignId('application_id')->after('id')->constrained()->onDelete('cascade');
        });
        
        // Remove profile verification from users
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_profile_verified', 'profile_verification_notes', 'profile_verified_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contractor_documents', function (Blueprint $table) {
            $table->dropForeign(['application_id']);
            $table->dropColumn('application_id');
            
            $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_profile_verified')->default(false);
            $table->text('profile_verification_notes')->nullable();
            $table->timestamp('profile_verified_at')->nullable();
        });
    }
};
