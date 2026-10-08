<?php

declare(strict_types=1);

namespace App\Models;

/**
 * ChartOfAccountsModel - Manages financial accounts (Assets, Liabilities, Equity, Revenue, Expenses)
 */
class ChartOfAccountsModel extends BaseModel
{
    protected $table            = 'chart_of_accounts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $allowedFields    = [
        'restaurant_id',
        'code',
        'name',
        'type',
        'category',
        'current_balance',
        'is_active',
        'created_at',
    ];

    /**
     * Get all active accounts ordered by code.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAccounts(int $restaurantId = 1): array
    {
        return $this->where('restaurant_id', $restaurantId)
            ->where('is_active', 1)
            ->orderBy('code', 'ASC')
            ->findAll();
    }
}
