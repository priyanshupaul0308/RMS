<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Validation\ValidationInterface;

/**
 * BaseModel – Foundation for all RMS models.
 *
 * Provides:
 *  - Branch-level data scoping (multi-tenancy per branch)
 *  - Restaurant-level scoping
 *  - Soft-delete support
 *  - Audit trail helper
 *  - Common query helpers
 */
abstract class BaseModel extends Model
{
    // -------------------------------------------------------------------------
    // Common column names present on most tables
    // -------------------------------------------------------------------------
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';

    // Soft deletes – opt-in per model
    protected $useSoftDeletes   = false;
    protected $deletedField     = 'deleted_at';

    // Default DB return type
    protected $returnType = 'array';

    // -------------------------------------------------------------------------
    // Scoping helpers – set by AuthFilter after login
    // -------------------------------------------------------------------------
    protected ?int $currentRestaurantId = null;
    protected ?int $currentBranchId     = null;
    protected ?int $currentUserId       = null;

    // -------------------------------------------------------------------------
    // Constructor
    // -------------------------------------------------------------------------
    public function __construct(
        ?ConnectionInterface $db         = null,
        ?ValidationInterface $validation = null
    ) {
        parent::__construct($db, $validation);

        // Pull scope from session if available (CodeIgniter session service)
        if (session()->has('restaurant_id')) {
            $this->currentRestaurantId = (int) session('restaurant_id');
        }
        if (session()->has('branch_id')) {
            $this->currentBranchId = (int) session('branch_id');
        }
        if (session()->has('user_id')) {
            $this->currentUserId = (int) session('user_id');
        }
    }

    // -------------------------------------------------------------------------
    // Scoped query builders
    // -------------------------------------------------------------------------

    /**
     * Return a query builder scoped to the current restaurant.
     */
    public function scopedToRestaurant(): static
    {
        if ($this->currentRestaurantId !== null && $this->db->fieldExists('restaurant_id', $this->table)) {
            $this->where("{$this->table}.restaurant_id", $this->currentRestaurantId);
        }
        return $this;
    }

    /**
     * Return a query builder scoped to the current branch.
     * Admins (branch_id = NULL) bypass this filter.
     */
    public function scopedToBranch(): static
    {
        if ($this->currentBranchId !== null && $this->db->fieldExists('branch_id', $this->table)) {
            $this->where("{$this->table}.branch_id", $this->currentBranchId);
        }
        return $this;
    }

    /**
     * Convenience: scope to both restaurant and branch.
     */
    public function scoped(): static
    {
        return $this->scopedToRestaurant()->scopedToBranch();
    }

    // -------------------------------------------------------------------------
    // Pagination helper
    // -------------------------------------------------------------------------

    /**
     * Paginate with sensible defaults.
     *
     * @param int|null $perPage
     * @param string   $group   Pagination group name
     * @return array<int, mixed>
     */
    public function paginate(?int $perPage = null, string $group = 'default', ?int $page = null, int $segment = 0): ?array
    {
        $perPage = $perPage ?? (int) config('App')->defaultPerPage;
        return parent::paginate($perPage, $group, $page, $segment);
    }

    // -------------------------------------------------------------------------
    // Audit trail helper
    // -------------------------------------------------------------------------

    /**
     * Write an audit log entry.
     *
     * @param string     $module
     * @param string     $action
     * @param string     $recordType
     * @param int|null   $recordId
     * @param mixed      $oldValues
     * @param mixed      $newValues
     * @param string|null $description
     */
    public function writeAudit(
        string           $module,
        string           $action,
        string           $recordType  = '',
        int|string|null  $recordId    = null,
        mixed            $oldValues   = null,
        mixed            $newValues   = null,
        ?string          $description = null
    ): void {
        $this->db->table('audit_logs')->insert([
            'restaurant_id' => $this->currentRestaurantId,
            'branch_id'     => $this->currentBranchId,
            'user_id'       => $this->currentUserId,
            'module'        => $module,
            'action'        => $action,
            'record_type'   => $recordType ?: $this->table,
            'record_id'     => $recordId !== null ? (int) $recordId : null,
            'old_values'    => $oldValues !== null ? json_encode($oldValues, JSON_UNESCAPED_UNICODE) : null,
            'new_values'    => $newValues !== null ? json_encode($newValues, JSON_UNESCAPED_UNICODE) : null,
            'description'   => $description,
            'ip_address'    => service('request')->getIPAddress(),
            'user_agent'    => service('request')->getUserAgent()->getAgentString(),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
    }

    // -------------------------------------------------------------------------
    // Utility: find one by field
    // -------------------------------------------------------------------------

    /**
     * Find first record matching a key=>value pair.
     *
     * @param array<string, mixed> $where
     */
    public function findOneWhere(array $where): ?array
    {
        $result = $this->where($where)->first();
        return $result ?: null;
    }

    // -------------------------------------------------------------------------
    // Toggle active status helper
    // -------------------------------------------------------------------------

    /**
     * Toggle the `is_active` boolean field for a record.
     */
    public function toggleActive(int $id): bool
    {
        $record = $this->find($id);
        if ($record === null) {
            return false;
        }
        return $this->update($id, ['is_active' => (int) !$record['is_active']]);
    }
}
