<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ReservationModel;
use App\Models\TableModel;
use App\Models\FloorModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * ReservationController – Guest reservations, seating, and table allocations.
 */
class ReservationController extends BaseController
{
    protected ReservationModel $reservationModel;
    protected TableModel       $tableModel;
    protected FloorModel       $floorModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface  $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface            $logger
    ): void {
        parent::initController($request, $response, $logger);
        $this->reservationModel = new ReservationModel();
        $this->tableModel       = new TableModel();
        $this->floorModel       = new FloorModel();
    }

    /**
     * Reservations schedule list.
     */
    public function index(): string
    {
        $this->authorize('reservations.view');

        $date   = $this->request->getGet('date') ?: date('Y-m-d');
        $status = $this->request->getGet('status') ?: 'all';

        $reservations = $this->reservationModel->getReservationsWithDetails($date, $this->currentBranchId, $status);
        $tablesQuery = $this->tableModel->where('is_active', 1);
        if ($this->currentBranchId) {
            $tablesQuery->where('branch_id', $this->currentBranchId);
        }
        $tables = $tablesQuery->findAll();

        return $this->renderView('admin/reservations/index', [
            'pageTitle'    => 'Table Reservations',
            'reservations' => $reservations,
            'currentDate'  => $date,
            'status'       => $status,
            'tables'       => $tables,
        ]);
    }

    /**
     * Store new table reservation.
     */
    public function store(): ResponseInterface
    {
        $this->authorize('reservations.create');

        $rules = [
            'customer_name'    => 'required|max_length[100]',
            'customer_phone'   => 'required|max_length[20]',
            'guest_count'      => 'required|integer',
            'reservation_date' => 'required|valid_date',
            'reservation_time' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $tableId = $this->request->getPost('table_id') ?: null;

        $data = [
            'restaurant_id'    => $this->currentRestaurantId,
            'branch_id'        => $this->currentBranchId ?: 1,
            'table_id'         => $tableId ? (int)$tableId : null,
            'customer_name'    => $this->request->getPost('customer_name'),
            'customer_phone'   => $this->request->getPost('customer_phone'),
            'customer_email'   => $this->request->getPost('customer_email') ?: null,
            'guest_count'      => (int)$this->request->getPost('guest_count'),
            'reservation_date' => $this->request->getPost('reservation_date'),
            'reservation_time' => $this->request->getPost('reservation_time'),
            'status'           => 'confirmed',
            'special_requests' => $this->request->getPost('special_requests') ?: null,
            'created_by'       => $this->currentUserId,
        ];

        $newId = $this->reservationModel->insert($data);

        // Mark table as reserved if date is today
        if ($tableId && $data['reservation_date'] === date('Y-m-d')) {
            $this->tableModel->updateStatus((int)$tableId, 'reserved');
        }

        $this->reservationModel->writeAudit('reservations', 'create', 'reservations', (int)$newId, null, $data,
            "Reservation for {$data['customer_name']} on {$data['reservation_date']}");

        return redirect()->route('admin.reservations.index')->with('success', 'Reservation booked successfully.');
    }

    /**
     * Guest arrives: Mark reservation seated and table occupied.
     */
    public function seat(int $id): ResponseInterface
    {
        $this->authorize('reservations.edit');

        $res = $this->reservationModel->find($id);
        if (!$res) {
            return redirect()->back()->with('error', 'Reservation not found.');
        }

        if ($this->currentBranchId && (int)($res['branch_id'] ?? 0) !== $this->currentBranchId) {
            return redirect()->back()->with('error', 'Unauthorized branch access to this reservation.');
        }

        $tableId = (int)$res['table_id'];
        if (!$tableId) {
            return redirect()->back()->with('error', 'No table assigned to this reservation.');
        }

        $this->reservationModel->update($id, ['status' => 'seated']);
        $this->tableModel->updateStatus($tableId, 'occupied');

        return redirect()->to(site_url('admin/pos'))->with('success', "Guest seated at Table. Ready for ordering.");
    }

    /**
     * Cancel reservation.
     */
    public function cancel(int $id): ResponseInterface
    {
        $this->authorize('reservations.delete');

        $res = $this->reservationModel->find($id);
        if (!$res) {
            return redirect()->back()->with('error', 'Reservation not found.');
        }

        if ($this->currentBranchId && (int)($res['branch_id'] ?? 0) !== $this->currentBranchId) {
            return redirect()->back()->with('error', 'Unauthorized branch access to this reservation.');
        }

        $this->reservationModel->update($id, ['status' => 'cancelled']);

        if (!empty($res['table_id'])) {
            $this->tableModel->updateStatus((int)$res['table_id'], 'available');
        }

        return redirect()->back()->with('success', 'Reservation cancelled.');
    }
}
