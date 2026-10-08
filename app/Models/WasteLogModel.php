<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * WasteLogModel
 * 
 * Logs raw material spoilage, kitchen waste, burnt items, and financial loss impact.
 */
class WasteLogModel extends Model
{
    protected $table            = 'waste_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'branch_id',
        'inventory_item_id',
        'quantity',
        'cost_impact',
        'waste_reason',
        'logged_by',
        'notes',
        'created_at'
    ];

    protected $useTimestamps = false;

    /**
     * Get waste logs with item, unit, and staff names
     */
    public function getLogsWithDetails(?int $branchId = 1, ?string $reason = null): array
    {
        $builder = $this->db->table($this->table . ' w')
            ->select('w.*, i.name as item_name, i.sku, u.short_code as unit_code, usr.first_name, usr.last_name')
            ->join('inventory_items i', 'i.id = w.inventory_item_id', 'left')
            ->join('inventory_units u', 'u.id = i.unit_id', 'left')
            ->join('users usr', 'usr.id = w.logged_by', 'left');

        if ($branchId !== null) {
            $builder->where('w.branch_id', $branchId);
        }

        if ($reason !== null && $reason !== '') {
            $builder->where('w.waste_reason', $reason);
        }

        return $builder->orderBy('w.id', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Log waste, compute cost impact, and decrement stock
     */
    public function recordWaste(int $itemId, float $quantity, string $reason, ?string $notes = null, ?int $userId = 1): bool
    {
        $item = $this->db->table('inventory_items')->where('id', $itemId)->get()->getRowArray();
        if (!$item) {
            return false;
        }

        $costImpact = round($quantity * (float)$item['unit_cost'], 2);

        $this->db->transStart();

        // 1. Decrement current stock
        $this->db->table('inventory_items')
            ->where('id', $itemId)
            ->set('current_stock', "GREATEST(0, current_stock - {$quantity})", false)
            ->update();

        // 2. Insert waste log
        $this->insert([
            'branch_id'         => $item['branch_id'],
            'inventory_item_id' => $itemId,
            'quantity'          => $quantity,
            'cost_impact'       => $costImpact,
            'waste_reason'      => $reason,
            'logged_by'         => $userId,
            'notes'             => $notes,
            'created_at'        => date('Y-m-d H:i:s')
        ]);

        $this->db->transComplete();

        return $this->db->transStatus();
    }

    /**
     * Get monthly total cost of waste
     */
    public function getMonthlyLoss(int $branchId = 1): float
    {
        $res = $this->db->table($this->table)
            ->selectSum('cost_impact')
            ->where('branch_id', $branchId)
            ->where('created_at >=', date('Y-m-01 00:00:00'))
            ->get()
            ->getRowArray();

        return (float)($res['cost_impact'] ?? 0.0);
    }
}
