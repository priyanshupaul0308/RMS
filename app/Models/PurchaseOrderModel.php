<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * PurchaseOrderModel
 * 
 * Manages procurement orders, itemized lines, and receiving workflows.
 */
class PurchaseOrderModel extends Model
{
    protected $table            = 'purchase_orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'po_number',
        'restaurant_id',
        'branch_id',
        'supplier_id',
        'order_date',
        'expected_date',
        'status',
        'total_amount',
        'notes',
        'created_by',
        'approved_by',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate unique PO number (e.g. PO-2026-002)
     */
    public function generatePoNumber(): string
    {
        $year = date('Y');
        $lastPo = $this->like('po_number', "PO-{$year}-", 'after')
            ->orderBy('id', 'DESC')
            ->first();

        $nextSeq = 1;
        if ($lastPo && preg_match("/PO-{$year}-(\d+)/", $lastPo['po_number'], $matches)) {
            $nextSeq = (int)$matches[1] + 1;
        }

        return sprintf("PO-%s-%03d", $year, $nextSeq);
    }

    /**
     * Get list of POs with supplier details
     */
    public function getOrdersWithDetails(?int $branchId = 1): array
    {
        $builder = $this->db->table($this->table . ' po')
            ->select('po.*, s.name as supplier_name, s.phone as supplier_phone, s.email as supplier_email, u.first_name, u.last_name')
            ->join('suppliers s', 's.id = po.supplier_id', 'left')
            ->join('users u', 'u.id = po.created_by', 'left');

        if ($branchId !== null) {
            $builder->where('po.branch_id', $branchId);
        }

        return $builder->orderBy('po.id', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get single PO with item lines
     */
    public function getOrderWithItems(int $id): ?array
    {
        $po = $this->db->table($this->table . ' po')
            ->select('po.*, s.name as supplier_name, s.contact_person, s.phone as supplier_phone, s.email as supplier_email, s.address as supplier_address, s.tax_number as supplier_tax')
            ->join('suppliers s', 's.id = po.supplier_id', 'left')
            ->where('po.id', $id)
            ->get()
            ->getRowArray();

        if (!$po) {
            return null;
        }

        $items = $this->db->table('purchase_order_items poi')
            ->select('poi.*, i.name as item_name, i.sku, u.short_code as unit_code')
            ->join('inventory_items i', 'i.id = poi.inventory_item_id', 'left')
            ->join('inventory_units u', 'u.id = i.unit_id', 'left')
            ->where('poi.purchase_order_id', $id)
            ->get()
            ->getResultArray();

        $po['items'] = $items;
        return $po;
    }

    /**
     * Receive PO items and automatically increment current stock
     */
    public function receiveOrder(int $id, ?int $userId = 1): bool
    {
        $po = $this->getOrderWithItems($id);
        if (!$po || in_array($po['status'], ['received', 'cancelled'], true)) {
            return false;
        }

        $this->db->transStart();

        // 1. Mark PO as received
        $this->update($id, [
            'status' => 'received',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // 2. Create Goods Receipt Note (GRN)
        $grnNumber = 'GRN-' . date('Y') . '-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT);
        $this->db->table('goods_receipt_notes')->insert([
            'grn_number'        => $grnNumber,
            'purchase_order_id' => $id,
            'branch_id'         => $po['branch_id'],
            'supplier_id'       => $po['supplier_id'],
            'invoice_no'        => 'INV-' . strtoupper(substr(md5((string)time()), 0, 6)),
            'received_date'     => date('Y-m-d'),
            'total_amount'      => $po['total_amount'],
            'status'            => 'verified',
            'received_by'       => $userId,
            'notes'             => 'Auto-verified from PO receipt',
            'created_at'        => date('Y-m-d H:i:s')
        ]);
        $grnId = $this->db->insertID();

        // 3. Update each item's stock and insert GRN items
        foreach ($po['items'] as $item) {
            $qty = (float)$item['quantity_ordered'];

            // Update item stock
            $this->db->table('inventory_items')
                ->where('id', $item['inventory_item_id'])
                ->set('current_stock', "current_stock + {$qty}", false)
                ->update();

            // Insert GRN line
            $this->db->table('goods_receipt_items')->insert([
                'grn_id'            => $grnId,
                'inventory_item_id' => $item['inventory_item_id'],
                'quantity_received' => $qty,
                'unit_price'        => $item['unit_price'],
                'total_price'       => $item['total_price']
            ]);

            // Update PO item received quantity
            $this->db->table('purchase_order_items')
                ->where('id', $item['id'])
                ->update(['quantity_received' => $qty]);
        }

        $this->db->transComplete();

        return $this->db->transStatus();
    }
}
