<?php

declare(strict_types=1);

namespace App\Models;

/**
 * FloorModel – Floor infrastructure and dining areas.
 */
class FloorModel extends BaseModel
{
    protected $table         = 'floors';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'restaurant_id', 'branch_id', 'name', 'floor_number',
        'sort_order', 'is_active',
    ];
    protected $useTimestamps = true;

    /**
     * Get active floors with tables for a branch.
     *
     * @param int|null $branchId
     * @return array<int, array<string, mixed>>
     */
    public function getFloorsWithTables(?int $branchId = null): array
    {
        $builder = $this->where('is_active', 1)->orderBy('sort_order', 'ASC');
        if ($branchId !== null) {
            $builder->groupStart()
                ->where('branch_id', $branchId)
                ->orWhere('branch_id IS NULL')
                ->groupEnd();
        }
        $floors = $builder->findAll();
        if (empty($floors)) {
            return [];
        }

        $floorIds = array_column($floors, 'id');
        $tableModel = new TableModel();
        $allTables = $tableModel->whereIn('floor_id', $floorIds)
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        $tablesByFloor = [];
        foreach ($allTables as $t) {
            $tablesByFloor[(int)$t['floor_id']][] = $t;
        }

        foreach ($floors as &$floor) {
            $floor['tables'] = $tablesByFloor[(int)$floor['id']] ?? [];
        }

        return $floors;
    }
}
