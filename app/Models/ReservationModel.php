<?php

declare(strict_types=1);

namespace App\Models;

/**
 * ReservationModel – Table reservations and guest scheduling.
 */
class ReservationModel extends BaseModel
{
    protected $table         = 'reservations';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'restaurant_id', 'branch_id', 'table_id', 'customer_name',
        'customer_phone', 'customer_email', 'guest_count',
        'reservation_date', 'reservation_time', 'status',
        'special_requests', 'created_by',
    ];
    protected $useTimestamps = true;

    /**
     * Get reservations with table details.
     *
     * @param string|null $date
     * @param int|null $branchId
     * @param string|null $status
     * @return array<int, array<string, mixed>>
     */
    public function getReservationsWithDetails(?string $date = null, ?int $branchId = null, ?string $status = null): array
    {
        $builder = $this->select('reservations.*, restaurant_tables.table_number, floors.name AS floor_name, users.first_name AS staff_name')
            ->join('restaurant_tables', 'restaurant_tables.id = reservations.table_id', 'left')
            ->join('floors',            'floors.id = restaurant_tables.floor_id', 'left')
            ->join('users',             'users.id = reservations.created_by', 'left');

        if ($branchId !== null) {
            $builder->where('reservations.branch_id', $branchId);
        }

        if (!empty($date)) {
            $builder->where('reservations.reservation_date', $date);
        }

        if (!empty($status) && $status !== 'all') {
            $builder->where('reservations.status', $status);
        }

        return $builder->orderBy('reservations.reservation_date', 'DESC')
            ->orderBy('reservations.reservation_time', 'ASC')
            ->findAll();
    }
}
