<?php

declare(strict_types=1);

namespace App\Models;

/**
 * OrderPaymentModel – Multi-tender split payment records.
 */
class OrderPaymentModel extends BaseModel
{
    protected $table         = 'order_payments';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'order_id', 'payment_method_id', 'amount', 'reference_number', 'received_by',
    ];
    protected $useTimestamps = true;
}
