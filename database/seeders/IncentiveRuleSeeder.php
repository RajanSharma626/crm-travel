<?php

namespace Database\Seeders;

use App\Models\IncentiveRule;
use Illuminate\Database\Seeder;

class IncentiveRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default Fixed Percentage Rule
        IncentiveRule::create([
            'name' => 'Default Sales Commission',
            'rule_type' => 'fixed_percentage',
            'fixed_percentage' => 5.00, // 5% commission
            'fixed_amount' => null,
            'params' => null,
            'min_profit_threshold' => 1000.00, // Minimum profit of ₹1000 to trigger incentive
            'active' => true,
        ]);

        // Tiered Percentage Rule
        IncentiveRule::create([
            'name' => 'Tiered Commission Structure',
            'rule_type' => 'tiered_percentage',
            'fixed_percentage' => null,
            'fixed_amount' => null,
            'params' => [
                [
                    'min' => 0,
                    'max' => 10000,
                    'percentage' => 3
                ],
                [
                    'min' => 10000,
                    'max' => 50000,
                    'percentage' => 5
                ],
                [
                    'min' => 50000,
                    'max' => 100000,
                    'percentage' => 7
                ],
                [
                    'min' => 100000,
                    'max' => PHP_INT_MAX,
                    'percentage' => 10
                ]
            ],
            'min_profit_threshold' => 5000.00,
            'active' => false, // Not active by default, admin can activate
        ]);

        // Fixed Amount Rule (for special promotions)
        IncentiveRule::create([
            'name' => 'Fixed Bonus Promotion',
            'rule_type' => 'fixed_amount',
            'fixed_percentage' => null,
            'fixed_amount' => 5000.00, // Fixed ₹5000 bonus
            'params' => null,
            'min_profit_threshold' => 20000.00,
            'active' => false,
        ]);

        $this->command->info('Incentive rules created successfully!');
    }
}
