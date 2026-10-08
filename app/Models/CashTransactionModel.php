<?php

declare(strict_types=1);

namespace App\Models;

/**
 * CashTransactionModel – Manual cash in / cash out and petty cash ledger.
 */
class CashTransactionModel extends BaseModel
{
    protected $table         = 'cash_transactions';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'register_id', 'type', 'amount', 'reason', 'user_id',
    ];
    protected $useTimestamps = true;
}
