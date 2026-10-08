<?php

declare(strict_types=1);

namespace App\Models;

/**
 * SettingsModel – Global and branch-level configuration store.
 */
class SettingsModel extends BaseModel
{
    protected $table      = 'settings';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'restaurant_id', 'branch_id', 'group', 'key', 'value',
        'value_type', 'label', 'description', 'is_public', 'updated_by',
    ];

    /**
     * Get a single setting value by key.
     *
     * @param string   $key
     * @param int      $restaurantId
     * @param int|null $branchId     Null = global setting
     * @param mixed    $default
     */
    public function getValue(string $key, int $restaurantId, ?int $branchId = null, mixed $default = null): mixed
    {
        // Branch-level first, then global
        if ($branchId !== null) {
            $setting = $this->where([
                'restaurant_id' => $restaurantId,
                'branch_id'     => $branchId,
                'key'           => $key,
            ])->first();

            if ($setting !== null) {
                return $this->castValue($setting['value'], $setting['value_type']);
            }
        }

        $setting = $this->where([
            'restaurant_id' => $restaurantId,
            'branch_id'     => null,
            'key'           => $key,
        ])->first();

        return $setting !== null
            ? $this->castValue($setting['value'], $setting['value_type'])
            : $default;
    }

    /**
     * Set/upsert a setting value.
     */
    public function setValue(
        string $key,
        mixed  $value,
        int    $restaurantId,
        ?int   $branchId  = null,
        string $valueType = 'string',
        ?int   $updatedBy = null
    ): bool {
        $existing = $this->where([
            'restaurant_id' => $restaurantId,
            'branch_id'     => $branchId,
            'key'           => $key,
        ])->first();

        $data = [
            'value'      => (string) $value,
            'value_type' => $valueType,
            'updated_by' => $updatedBy,
        ];

        if ($existing !== null) {
            return $this->update($existing['id'], $data);
        }

        return $this->insert(array_merge($data, [
            'restaurant_id' => $restaurantId,
            'branch_id'     => $branchId,
            'key'           => $key,
        ])) !== false;
    }

    /**
     * Get all settings for a group.
     *
     * @return array<string, mixed>
     */
    public function getAllSettingsGrouped(int $restaurantId, ?int $branchId = null): array
    {
        $builder = $this->where('restaurant_id', $restaurantId);
        if ($branchId !== null) {
            $builder->groupStart()
                ->where('branch_id', $branchId)
                ->orWhere('branch_id', null)
                ->groupEnd();
        } else {
            $builder->where('branch_id', null);
        }
        $results = $builder->findAll();

        $grouped = [];
        foreach ($results as $row) {
            $grp = $row['group'] ?? 'general';
            $grouped[$grp][$row['key']] = $this->castValue($row['value'], $row['value_type']);
        }
        return $grouped;
    }

    public function getGroup(string $group, int $restaurantId, ?int $branchId = null): array
    {
        $results = $this->where([
            'restaurant_id' => $restaurantId,
            'group'         => $group,
        ])->findAll();

        $settings = [];
        foreach ($results as $row) {
            $settings[$row['key']] = $this->castValue($row['value'], $row['value_type']);
        }
        return $settings;
    }

    private function castValue(mixed $value, string $type): mixed
    {
        return match ($type) {
            'integer' => (int) $value,
            'decimal' => (float) $value,
            'boolean' => (bool) $value,
            'json'    => json_decode((string) $value, true),
            default   => $value,
        };
    }
}
