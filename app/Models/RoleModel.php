<?php

declare(strict_types=1);

namespace App\Models;

/**
 * RoleModel – Manages RBAC roles.
 */
class RoleModel extends BaseModel
{
    protected $table      = 'roles';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;

    protected $allowedFields = ['name', 'slug', 'description', 'is_active'];

    protected $validationRules = [
        'name' => 'required|max_length[60]',
        'slug' => 'required|max_length[60]|is_unique[roles.slug,id,{id}]',
    ];

    /**
     * Get all active roles for dropdowns.
     */
    public function getActiveRoles(): array
    {
        return $this->where('is_active', 1)->orderBy('id', 'ASC')->findAll();
    }
}
