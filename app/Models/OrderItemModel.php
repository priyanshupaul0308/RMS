<?php

declare(strict_types=1);

namespace App\Models;

/**
 * OrderItemModel – Line items in an order with price, tax, and KOT tracking.
 */
class OrderItemModel extends BaseModel
{
    protected $table         = 'order_items';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'order_id', 'menu_item_id', 'item_name', 'unit_price',
        'quantity', 'subtotal', 'tax_amount', 'total',
        'special_notes', 'status', 'kot_id',
    ];
    protected $useTimestamps = true;
}
