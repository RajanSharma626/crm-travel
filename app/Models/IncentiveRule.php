<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IncentiveRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'rule_type',
        'params',
        'fixed_percentage',
        'fixed_amount',
        'min_profit_threshold',
        'active',
    ];

    protected $casts = [
        'params' => 'array',
        'fixed_percentage' => 'decimal:2',
        'fixed_amount' => 'decimal:2',
        'min_profit_threshold' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function incentives()
    {
        return $this->hasMany(Incentive::class);
    }

    /**
     * Calculate incentive amount based on profit
     */
    public function calculateIncentive($profit)
    {
        if ($profit < $this->min_profit_threshold) {
            return 0;
        }

        switch ($this->rule_type) {
            case 'fixed_percentage':
                return $profit * ($this->fixed_percentage / 100);

            case 'fixed_amount':
                return $this->fixed_amount;

            case 'tiered_percentage':
                if (!$this->params || !is_array($this->params)) {
                    return 0;
                }
                foreach ($this->params as $tier) {
                    if ($profit >= ($tier['min'] ?? 0) && $profit <= ($tier['max'] ?? PHP_INT_MAX)) {
                        return $profit * (($tier['percentage'] ?? 0) / 100);
                    }
                }
                return 0;

            default:
                return 0;
        }
    }
}
