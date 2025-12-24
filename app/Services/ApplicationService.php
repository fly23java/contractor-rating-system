<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Tender;

class ApplicationService
{
    // Technical criteria (for 70% rule)
    const TECHNICAL_CRITERIA = [
        'quality', 'experience', 'contract_terms', 'field_experience',
        'executive_capability', 'post_service', 'safety'
    ];

    /**
     * Get weights from tender
     */
    private function getWeights(Tender $tender)
    {
        return [
            'price' => $tender->weight_price,
            'quality' => $tender->weight_quality,
            'financial_capability' => $tender->weight_financial_capability,
            'experience' => $tender->weight_experience,
            'contract_terms' => $tender->weight_contract_terms,
            'field_experience' => $tender->weight_field_experience,
            'executive_capability' => $tender->weight_executive_capability,
            'post_service' => $tender->weight_post_service,
            'guarantees' => $tender->weight_guarantees,
            'safety' => $tender->weight_safety,
        ];
    }

    /**
     * Calculate all grades and scores for applications in a tender
     */
    public function calculateTenderScores(Tender $tender)
    {
        $applications = $tender->applications()->where('is_excluded', false)->get();

        if ($applications->count() < 2) {
            return; // Need at least 2 to calculate min/max
        }

        // Step 1: Get min/max for each criterion
        $ranges = $this->calculateRanges($applications);

        // Step 2: Get tender weights
        $weights = $this->getWeights($tender);

        // Step 3: Calculate grades for each application
        foreach ($applications as $app) {
            $this->calculateGrades($app, $ranges);
            $this->calculateWeightedScore($app, $weights, $tender);
            $this->checkExclusionRules($app, $weights);
            $app->save();
        }
    }

    /**
     * Calculate min/max ranges from all applications
     */
    private function calculateRanges($applications)
    {
        return [
            'price' => [
                'min' => $applications->min('price_value'),
                'max' => $applications->max('price_value'),
            ],
            'quality' => ['min' => 0, 'max' => 100], // Fixed scale
            'financial_capability' => [
                'min' => $applications->min('financial_capability'),
                'max' => $applications->max('financial_capability'),
            ],
            'experience' => [
                'min' => $applications->min('experience_projects'),
                'max' => $applications->max('experience_projects'),
            ],
            'contract_terms' => ['min' => 0, 'max' => 100],
            'field_experience' => ['min' => 0, 'max' => 5],
            'executive_capability' => ['min' => 0, 'max' => 100],
            'post_service' => ['min' => 0, 'max' => 24], // months
            'guarantees' => [
                'min' => $applications->min('guarantees_value'),
                'max' => $applications->max('guarantees_value'),
            ],
            'safety' => ['min' => 50, 'max' => 95],
        ];
    }

    /**
     * Convert raw value to 1-5 grade using formula
     */
    private function convertToGrade($value, $min, $max)
    {
        if ($min == $max) return 5; // All same, give max grade
        
        // For price: INVERT (lower is better)
        // Formula: Grade = 1 + 4 × (max - value) / (max - min)
        // For others: Grade = 1 + 4 × (value - min) / (max - min)
        
        return round(1 + 4 * ($value - $min) / ($max - $min), 2);
    }

    private function convertPriceToGrade($price, $min, $max)
    {
        // World Bank Formula: (Lowest Price / Contractor Price) * Grade Scale
        // If min price is 0 (unlikely but safe check), return 0
        if ($min <= 0) return 0;

        // Formula: (Lowest Price / Contractor Price) * 5
        // Example: Lowest=100k, Contractor=120k -> (100/120)*5 = 4.17
        $grade = ($min / $price) * 5;

        return round($grade, 2);
    }

    /**
     * Calculate all grades for an application
     */
    private function calculateGrades(Application $app, $ranges)
    {
        $app->price_grade = $this->convertPriceToGrade(
            $app->price_value, 
            $ranges['price']['min'], 
            $ranges['price']['max']
        );

        $app->quality_grade = $this->convertToGrade(
            $app->quality_points, 
            $ranges['quality']['min'], 
            $ranges['quality']['max']
        );

        $app->financial_capability_grade = $this->convertToGrade(
            $app->financial_capability, 
            $ranges['financial_capability']['min'], 
            $ranges['financial_capability']['max']
        );

        $app->experience_grade = $this->convertToGrade(
            $app->experience_projects, 
            $ranges['experience']['min'], 
            $ranges['experience']['max']
        );

        $app->contract_terms_grade = $this->convertToGrade(
            $app->contract_terms_points, 
            $ranges['contract_terms']['min'], 
            $ranges['contract_terms']['max']
        );

        $app->field_experience_grade = $this->convertToGrade(
            $app->field_experience_projects, 
            $ranges['field_experience']['min'], 
            $ranges['field_experience']['max']
        );

        $app->executive_capability_grade = $this->convertToGrade(
            $app->executive_capability_points, 
            $ranges['executive_capability']['min'], 
            $ranges['executive_capability']['max']
        );

        $app->post_service_grade = $this->convertToGrade(
            $app->post_service_months, 
            $ranges['post_service']['min'], 
            $ranges['post_service']['max']
        );

        $app->guarantees_grade = $this->convertToGrade(
            $app->guarantees_value, 
            $ranges['guarantees']['min'], 
            $ranges['guarantees']['max']
        );

        $app->safety_grade = $this->convertToGrade(
            $app->safety_points, 
            $ranges['safety']['min'], 
            $ranges['safety']['max']
        );
    }

    /**
     * Calculate weighted total score
     */
    private function calculateWeightedScore(Application $app, $weights, Tender $tender)
    {
        $total = 0;
        $technical = 0;
        $financial = 0;

        foreach ($weights as $criterion => $weight) {
            $gradeField = $criterion . '_grade';
            $grade = $app->$gradeField ?? 0;
            $weighted = $grade * ($weight / 100);
            
            $total += $weighted;

            if (in_array($criterion, self::TECHNICAL_CRITERIA)) {
                $technical += $weighted;
            } else {
                $financial += $weighted;
            }
        }

        $app->weighted_total = round($total * 100, 2); // Convert to percentage
        $app->technical_score = round($technical * 100, 2);
        $app->financial_score = round($financial * 100, 2);
    }

    /**
     * Check exclusion rules
     */
    private function checkExclusionRules(Application $app, $weights)
    {
        // Rule 1: Missing any criterion
        $criteria = [
            'price_value', 'quality_points', 'financial_capability', 'experience_projects',
            'contract_terms_points', 'field_experience_projects', 'executive_capability_points',
            'post_service_months', 'guarantees_value', 'safety_points'
        ];

        foreach ($criteria as $field) {
            if (is_null($app->$field)) {
                $app->is_excluded = true;
                $app->exclusion_reason = 'Missing data for: ' . $field;
                $app->excluded_at = now();
                return;
            }
        }

        // Rule 2: Technical score < 70% of maximum
        // Rule 2: Technical score < 70% of maximum possible technical score
        // Maximum normalized technical score is 5 (since all grades are converted to 0-5)
        // Weighted Technical Score = Sum(Grade * Weight). Max possible is Sum(5 * Weight) = 5 * TotalTechnicalWeight
        
        $totalTechnicalWeight = array_sum(array_intersect_key($weights, array_flip(self::TECHNICAL_CRITERIA)));
        $maxPossibleTechnicalScore = 5 * ($totalTechnicalWeight / 100); // e.g., if tech weight is 60%, max score is 5 * 0.6 = 3.0

        // Actual technical score is already weighted (e.g., 2.1)
        // Threshold is 70% of max possible
        $threshold = $maxPossibleTechnicalScore * 0.70;

        // However, current implementation: technical_score is normalized to 0-100 scale in calculateWeightedScore?
        // Let's check calculateWeightedScore:
        // $app->technical_score = round($technical * 100, 2); -> This sums (Grade * Weight/100).
        // Max Grade is 5. So if Tech Weights sum to 60%, Max Technical is 5 * 0.6 = 3. 
        // Then technical_score is 3 * 100 = 300? No.
        // Let's re-verify calculateWeightedScore logic.
        
        // RE-VERIFY LOGIC:
        // $weighted = $grade * ($weight / 100);  -> Grade (0-5) * (Weight (e.g. 11.26)/100) = 5 * 0.1126 = 0.563
        // $technical += $weighted;
        // Total possible $technical (if all 5) = 5 * (TotalTechWeight/100). 
        // If TotalTechWeight is ~70%, Max possible is 3.5.
        // We want to exclude if score < 70% of that Max.
        
        $currentTechnicalScore = $app->technical_score / 100; // Convert back from percentage representation if needed, OR just use the raw sums
        // Wait, calculateWeightedScore saves: $app->technical_score = round($technical * 100, 2);
        // So if $technical is 2.5, saved is 250.
        // Let's stick to using the raw $technical variable if possible, but here we are in a separate method.
        // We need to re-calculate or assume consistency.
        
        // Easier: technical_score field stores the weighted sum * 100.
        // Max possible = 5 * TotalTechnicalWeight.
        
        if ($app->technical_score < (5 * $totalTechnicalWeight * 0.70)) {
             $app->is_excluded = true;
             $app->exclusion_reason = 'Technical score below 70% threshold (' . $app->technical_score . ' < ' . (5 * $totalTechnicalWeight * 0.70) . ')';
             $app->excluded_at = now();
             return;
        }

        // Rule 3: Price validation (if tender has min/max price set)
        $tender = $app->tender;
        if ($tender->min_price && $app->price_value < $tender->min_price) {
            $app->is_excluded = true;
            $app->exclusion_reason = 'Price below minimum acceptable';
            $app->excluded_at = now();
            return;
        }

        if ($tender->max_price && $app->price_value > ($tender->max_price * 2)) {
            $app->is_excluded = true;
            $app->exclusion_reason = 'Price exceeds 200% of reference price';
            $app->excluded_at = now();
            return;
        }
    }
}
