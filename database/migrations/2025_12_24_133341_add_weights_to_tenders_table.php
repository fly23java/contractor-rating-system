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
        Schema::table('tenders', function (Blueprint $table) {
            // Add weight fields for each criterion (percentages)
            $table->decimal('weight_price', 5, 2)->default(11.16)->after('status');
            $table->decimal('weight_quality', 5, 2)->default(10.90)->after('weight_price');
            $table->decimal('weight_financial_capability', 5, 2)->default(10.90)->after('weight_quality');
            $table->decimal('weight_experience', 5, 2)->default(10.87)->after('weight_financial_capability');
            $table->decimal('weight_contract_terms', 5, 2)->default(10.75)->after('weight_experience');
            $table->decimal('weight_field_experience', 5, 2)->default(10.61)->after('weight_contract_terms');
            $table->decimal('weight_executive_capability', 5, 2)->default(10.47)->after('weight_field_experience');
            $table->decimal('weight_post_service', 5, 2)->default(8.60)->after('weight_executive_capability');
            $table->decimal('weight_guarantees', 5, 2)->default(8.32)->after('weight_post_service');
            $table->decimal('weight_safety', 5, 2)->default(7.43)->after('weight_guarantees');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenders', function (Blueprint $table) {
            $table->dropColumn([
                'weight_price', 'weight_quality', 'weight_financial_capability',
                'weight_experience', 'weight_contract_terms', 'weight_field_experience',
                'weight_executive_capability', 'weight_post_service', 'weight_guarantees', 'weight_safety'
            ]);
        });
    }
};
