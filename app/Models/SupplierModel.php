<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * SupplierModel
 * 
 * Manages restaurant vendors, purveyors, and payment terms.
 */
class SupplierModel extends Model
{
    protected $table            = 'suppliers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'restaurant_id',
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'tax_number',
        'payment_terms',
        'is_active',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get active suppliers with total item count and PO stats
     */
    public function getSuppliersWithStats(int $restaurantId = 1): array
    {
        return $this->db->table($this->table . ' s')
            ->select('s.*, COUNT(DISTINCT i.id) as item_count, COUNT(DISTINCT p.id) as total_pos')
            ->join('inventory_items i', 'i.supplier_id = s.id AND i.is_active = 1', 'left')
            ->join('purchase_orders p', 'p.supplier_id = s.id', 'left')
            ->where('s.restaurant_id', $restaurantId)
            ->where('s.is_active', 1)
            ->groupBy('s.id')
            ->orderBy('s.name', 'ASC')
            ->get()
            ->getResultArray();
    }
}
