<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * InventoryItemModel
 * 
 * Manages raw materials, stock levels, unit costs, and low-stock alerts.
 */
class InventoryItemModel extends Model
{
    protected $table            = 'inventory_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'restaurant_id',
        'branch_id',
        'category_id',
        'unit_id',
        'supplier_id',
        'name',
        'sku',
        'current_stock',
        'min_stock_level',
        'ideal_stock_level',
        'unit_cost',
        'is_active',
        'created_at',
        'updated_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get inventory items with category, unit, and supplier names
     */
    public function getItemsWithDetails(?int $branchId = 1, ?int $categoryId = null, bool $onlyLowStock = false): array
    {
        $builder = $this->db->table($this->table . ' i')
            ->select('i.*, c.name as category_name, u.name as unit_name, u.short_code as unit_code, s.name as supplier_name')
            ->join('inventory_categories c', 'c.id = i.category_id', 'left')
            ->join('inventory_units u', 'u.id = i.unit_id', 'left')
            ->join('suppliers s', 's.id = i.supplier_id', 'left')
            ->where('i.is_active', 1);

        if ($branchId !== null) {
            $builder->where('i.branch_id', $branchId);
        }

        if ($categoryId !== null) {
            $builder->where('i.category_id', $categoryId);
        }

        if ($onlyLowStock) {
            $builder->where('i.current_stock <= i.min_stock_level');
        }

        return $builder->orderBy('i.name', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get count of low stock items
     */
    public function getLowStockCount(int $branchId = 1): int
    {
        return $this->db->table($this->table)
            ->where('branch_id', $branchId)
            ->where('is_active', 1)
            ->where('current_stock <= min_stock_level')
            ->countAllResults();
    }

    /**
     * Adjust stock level and record in stock_adjustments table
     */
    public function adjustStock(int $itemId, float $qtyChange, string $type, string $reason, ?int $userId = 1): bool
    {
        $item = $this->find($itemId);
        if (!$item) {
            return false;
        }

        $prevStock = (float)$item['current_stock'];
        $newStock = ($type === 'in') ? ($prevStock + $qtyChange) : max(0.0, $prevStock - $qtyChange);

        $this->db->transStart();

        $this->update($itemId, ['current_stock' => $newStock]);

        $this->db->table('stock_adjustments')->insert([
            'branch_id'         => $item['branch_id'],
            'inventory_item_id' => $itemId,
            'adjustment_type'   => $type,
            'quantity'          => $qtyChange,
            'previous_stock'    => $prevStock,
            'new_stock'         => $newStock,
            'reason'            => $reason,
            'adjusted_by'       => $userId,
            'created_at'        => date('Y-m-d H:i:s')
        ]);

        $this->db->transComplete();

        return $this->db->transStatus();
    }
}
