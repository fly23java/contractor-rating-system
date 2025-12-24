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
        Schema::table('applications', function (Blueprint $table) {
            // Raw input values for 10 criteria
            $table->decimal('price_value', 15, 2)->nullable(); // السعر (actual price)
            $table->decimal('quality_points', 5, 2)->nullable(); // الجودة (0-100)
            $table->decimal('financial_capability', 15, 2)->nullable(); // القدرة المالية (budget size)
            $table->integer('experience_projects')->nullable(); // الخبرة (number of projects)
            $table->decimal('contract_terms_points', 5, 2)->nullable(); // الشروط التعاقدية (0-100)
            $table->integer('field_experience_projects')->nullable(); // الخبرة في المجال (0-5)
            $table->decimal('executive_capability_points', 5, 2)->nullable(); // القدرة التنفيذية (0-100)
            $table->integer('post_service_months')->nullable(); // الالتزام بالخدمات اللاحقة (months)
            $table->decimal('guarantees_value', 15, 2)->nullable(); // الضمانات (monetary value)
            $table->decimal('safety_points', 5, 2)->nullable(); // السلامة والأمان (50-95)

            // Calculated grades (1-5 scale)
            $table->decimal('price_grade', 3, 2)->nullable();
            $table->decimal('quality_grade', 3, 2)->nullable();
            $table->decimal('financial_capability_grade', 3, 2)->nullable();
            $table->decimal('experience_grade', 3, 2)->nullable();
            $table->decimal('contract_terms_grade', 3, 2)->nullable();
            $table->decimal('field_experience_grade', 3, 2)->nullable();
            $table->decimal('executive_capability_grade', 3, 2)->nullable();
            $table->decimal('post_service_grade', 3, 2)->nullable();
            $table->decimal('guarantees_grade', 3, 2)->nullable();
            $table->decimal('safety_grade', 3, 2)->nullable();

            // Scores
            $table->decimal('technical_score', 8, 2)->nullable(); // Technical criteria sum
            $table->decimal('financial_score', 8, 2)->nullable(); // Financial criteria sum
            $table->decimal('weighted_total', 8, 2)->nullable(); // Final weighted total
            
            // Exclusion
            $table->boolean('is_excluded')->default(false);
            $table->text('exclusion_reason')->nullable();
            $table->timestamp('excluded_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'price_value', 'quality_points', 'financial_capability', 'experience_projects',
                'contract_terms_points', 'field_experience_projects', 'executive_capability_points',
                'post_service_months', 'guarantees_value', 'safety_points',
                'price_grade', 'quality_grade', 'financial_capability_grade', 'experience_grade',
                'contract_terms_grade', 'field_experience_grade', 'executive_capability_grade',
                'post_service_grade', 'guarantees_grade', 'safety_grade',
                'technical_score', 'financial_score', 'weighted_total',
                'is_excluded', 'exclusion_reason', 'excluded_at'
            ]);
        });
    }
};
