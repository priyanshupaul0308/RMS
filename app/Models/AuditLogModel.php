<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * AuditLogModel
 * 
 * Enterprise audit trail tracking every staff action, security event, and system mutation.
 */
class AuditLogModel extends Model
{
    protected $table            = 'audit_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'restaurant_id',
        'branch_id',
        'user_id',
        'module',
        'action',
        'record_type',
        'record_id',
        'old_values',
        'new_values',
        'description',
        'ip_address',
        'user_agent',
        'created_at'
    ];

    protected $useTimestamps = false;

    /**
     * Get filterable audit log stream
     */
    public function getLogs(?string $module = null, ?int $userId = null, int $limit = 100): array
    {
        $builder = $this->db->table($this->table . ' a')
            ->select('a.*, u.first_name, u.last_name, u.email, r.name as role_name')
            ->join('users u', 'u.id = a.user_id', 'left')
            ->join('roles r', 'r.id = u.role_id', 'left');

        if ($module !== null && $module !== '') {
            $builder->where('a.module', $module);
        }

        if ($userId !== null) {
            $builder->where('a.user_id', $userId);
        }

        return $builder->orderBy('a.id', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Helper to log an event quickly
     */
    public function recordEvent(string $module, string $action, string $description, ?int $userId = 1, ?string $recordType = null, ?int $recordId = null): bool
    {
        $request = service('request');
        return (bool)$this->insert([
            'restaurant_id' => 1,
            'branch_id'     => 1,
            'user_id'       => $userId,
            'module'        => $module,
            'action'        => $action,
            'record_type'   => $recordType,
            'record_id'     => $recordId,
            'description'   => $description,
            'ip_address'    => $request->getIPAddress(),
            'user_agent'    => substr($request->getUserAgent()->getAgentString(), 0, 500),
            'created_at'    => date('Y-m-d H:i:s')
        ]);
    }
}
