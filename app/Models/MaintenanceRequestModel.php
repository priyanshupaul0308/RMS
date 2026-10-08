<?php

declare(strict_types=1);

namespace App\Models;

/**
 * MaintenanceRequestModel - Work orders, breakdown repairs, and maintenance cost tracking
 */
class MaintenanceRequestModel extends BaseModel
{
    protected $table            = 'maintenance_requests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'equipment_id',
        'branch_id',
        'title',
        'priority',
        'description',
        'reported_by',
        'assigned_to_vendor',
        'cost',
        'status',
        'reported_at',
        'completed_at',
        'resolution_notes',
        'invoice_number',
    ];

    protected $validationRules = [
        'equipment_id' => 'required|is_natural_no_zero',
        'title'        => 'required|min_length[3]|max_length[150]',
        'priority'     => 'required|in_list[low,medium,high,emergency]',
        'description'  => 'required',
    ];

    /**
     * Get maintenance requests joined with equipment and user info.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getRequests(int $branchId = 1, ?string $status = null): array
    {
        $builder = $this->select('maintenance_requests.*, equipment.name as equipment_name, equipment.location, equipment.model_number, users.first_name, users.last_name')
            ->join('equipment', 'equipment.id = maintenance_requests.equipment_id')
            ->join('users', 'users.id = maintenance_requests.reported_by', 'left')
            ->where('maintenance_requests.branch_id', $branchId);

        if ($status) {
            $builder->where('maintenance_requests.status', $status);
        }

        return $builder->orderBy('maintenance_requests.reported_at', 'DESC')->findAll();
    }

    /**
     * Update request status and equipment state.
     */
    public function updateRequestStatus(int $id, string $status, float $cost = 0.0, ?string $notes = null, ?string $invoiceNo = null): bool
    {
        $data = ['status' => $status];
        if ($status === 'completed') {
            $data['completed_at']      = date('Y-m-d H:i:s');
            $data['cost']              = $cost;
            $data['resolution_notes']  = $notes;
            $data['invoice_number']    = $invoiceNo;
        }

        $res = $this->update($id, $data);

        if ($res) {
            $req = $this->find($id);
            if ($req) {
                $eqId = (int) $req['equipment_id'];
                $newEqStatus = match ($status) {
                    'in_progress' => 'under_maintenance',
                    'completed'   => 'operational',
                    'cancelled'   => 'operational',
                    default       => 'under_maintenance',
                };

                $eqUpdate = ['status' => $newEqStatus];
                if ($status === 'completed') {
                    $eqUpdate['last_serviced_date'] = date('Y-m-d');
                    $eqUpdate['next_service_due']   = date('Y-m-d', strtotime('+3 months'));
                }

                $this->db->table('equipment')->where('id', $eqId)->update($eqUpdate);
            }
        }

        return (bool) $res;
    }
}
