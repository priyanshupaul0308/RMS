<?php

declare(strict_types=1);

namespace App\Models;

/**
 * MenuCategoryModel – Grouping categories for items (Starters, Main Course, etc.)
 */
class MenuCategoryModel extends BaseModel
{
    protected $table         = 'menu_categories';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'restaurant_id', 'branch_id', 'name', 'slug',
        'icon', 'description', 'sort_order', 'is_active',
    ];
    protected $useTimestamps = true;

    /**
     * Get active categories with item counts.
     *
     * @param int|null $restaurantId
     * @return array<int, array<string, mixed>>
     */
    public function getActiveCategoriesWithCount(?int $restaurantId = null): array
    {
        return $this->select('menu_categories.*, COUNT(menu_items.id) AS item_count')
            ->join('menu_items', 'menu_items.category_id = menu_categories.id AND menu_items.is_active = 1', 'left')
            ->where('menu_categories.is_active', 1)
            ->groupBy('menu_categories.id')
            ->orderBy('menu_categories.sort_order', 'ASC')
            ->findAll();
    }
}
