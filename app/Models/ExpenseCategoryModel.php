<?php

declare(strict_types=1);

namespace App\Models;

/**
 * ExpenseCategoryModel - Operational expense categories
 */
class ExpenseCategoryModel extends BaseModel
{
    protected $table            = 'expense_categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $allowedFields    = [
        'restaurant_id',
        'name',
        'description',
        'is_active',
        'created_at',
    ];

    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[80]',
    ];

    /**
     * Get active categories.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getActiveCategories(int $restaurantId = 1): array
    {
        return $this->where('restaurant_id', $restaurantId)
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->findAll();
    }
}
