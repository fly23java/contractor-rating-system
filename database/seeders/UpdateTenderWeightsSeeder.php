<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tender;

use App\Services\ApplicationService;

class UpdateTenderWeightsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $weights = [
            'weight_price' => 11.16,
            'weight_quality' => 10.90,
            'weight_financial_capability' => 10.90,
            'weight_experience' => 10.87,
            'weight_contract_terms' => 10.75,
            'weight_field_experience' => 10.61,
            'weight_executive_capability' => 10.47,
            'weight_post_service' => 8.60,
            'weight_guarantees' => 8.32,
            'weight_safety' => 7.42,
        ];

        Tender::query()->update($weights);

        // Recalculate scores for all tenders
        $service = new ApplicationService();
        foreach (Tender::all() as $tender) {
            $service->calculateTenderScores($tender);
        }
    }
}
