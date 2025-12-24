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
            // Remove application_id foreign key
            $table->dropForeign(['application_id']);
            $table->dropColumn('application_id');
            
            // Add user_id instead (contractor's profile)
            $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contractor_documents', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            
            $table->foreignId('application_id')->after('id')->constrained()->onDelete('cascade');
        });
    }
};
