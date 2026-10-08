<?php

declare(strict_types=1);

namespace App\Models;

/**
 * MenuItemModel – Individual food and beverage menu items.
 */
class MenuItemModel extends BaseModel
{
    protected $table         = 'menu_items';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'restaurant_id', 'branch_id', 'category_id', 'name', 'code',
        'description', 'price', 'tax_rate_id', 'is_veg', 'preparation_time',
        'is_available', 'sort_order', 'is_active',
    ];
    protected $useTimestamps = true;

    /**
     * Get menu items with category and tax information for POS.
     *
     * @param int|null $categoryId
     * @param string|null $search
     * @return array<int, array<string, mixed>>
     */
    public function getItemsForPos(?int $categoryId = null, ?string $search = null): array
    {
        $builder = $this->select('menu_items.*, menu_categories.name AS category_name, tax_rates.rate AS tax_rate')
            ->join('menu_categories', 'menu_categories.id = menu_items.category_id', 'left')
            ->join('tax_rates', 'tax_rates.id = menu_items.tax_rate_id', 'left')
            ->where('menu_items.is_active', 1)
            ->where('menu_items.is_available', 1);

        if ($categoryId !== null && $categoryId > 0) {
            $builder->where('menu_items.category_id', $categoryId);
        }

        if (!empty($search)) {
            $builder->groupStart()
                ->like('menu_items.name', $search)
                ->orLike('menu_items.code', $search)
                ->groupEnd();
        }

        return $builder->orderBy('menu_items.sort_order', 'ASC')
            ->orderBy('menu_items.name', 'ASC')
            ->findAll();
    }

    /**
     * Get all active menu items for admin catalog (both in-stock and out-of-stock).
     *
     * @param int|null $categoryId
     * @param string|null $search
     * @return array<int, array<string, mixed>>
     */
    public function getItemsForAdmin(?int $categoryId = null, ?string $search = null): array
    {
        $builder = $this->select('menu_items.*, menu_categories.name AS category_name, tax_rates.rate AS tax_rate')
            ->join('menu_categories', 'menu_categories.id = menu_items.category_id', 'left')
            ->join('tax_rates', 'tax_rates.id = menu_items.tax_rate_id', 'left')
            ->where('menu_items.is_active', 1);

        if ($categoryId !== null && $categoryId > 0) {
            $builder->where('menu_items.category_id', $categoryId);
        }

        if (!empty($search)) {
            $builder->groupStart()
                ->like('menu_items.name', $search)
                ->orLike('menu_items.code', $search)
                ->groupEnd();
        }

        return $builder->orderBy('menu_items.sort_order', 'ASC')
            ->orderBy('menu_items.name', 'ASC')
            ->findAll();
    }
}
