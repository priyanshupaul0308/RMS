<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KotModel;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * KdsController – Real-Time Kitchen Display System (KDS) & Cooking Timers.
 */
class KdsController extends BaseController
{
    protected KotModel       $kotModel;
    protected OrderItemModel $orderItemModel;
    protected OrderModel     $orderModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface  $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface            $logger
    ): void {
        parent::initController($request, $response, $logger);
        $this->kotModel       = new KotModel();
        $this->orderItemModel = new OrderItemModel();
        $this->orderModel     = new OrderModel();
    }

    /**
     * KDS Screen View.
     */
    public function index(): string
    {
        $this->authorize('kds.view');

        $stations = db_connect()->table('kitchen_stations')
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();

        $activeKots = $this->kotModel->getActiveKotsForKds(null, $this->currentBranchId);

        return $this->renderView('admin/kds/index', [
            'pageTitle'  => 'Kitchen Display System (KDS)',
            'stations'   => $stations,
            'activeKots' => $activeKots,
        ]);
    }

    /**
     * AJAX Polling Endpoint: Returns live tickets and active count.
     */
    public function feed(): ResponseInterface
    {
        $this->authorize('kds.view');

        $station = $this->request->getGet('station');
        $tickets = $this->kotModel->getActiveKotsForKds($station, $this->currentBranchId);

        return $this->jsonSuccess([
            'tickets'      => $tickets,
            'active_count' => count($tickets),
            'server_time'  => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Start cooking on a KOT ticket.
     */
    public function start(int $kotId): ResponseInterface
    {
        $this->authorize('kds.view');

        $kot = $this->kotModel->find($kotId);
        if (!$kot) {
            return $this->jsonError('Ticket not found.');
        }

        if ($this->currentBranchId && (int)($kot['branch_id'] ?? 0) !== $this->currentBranchId) {
            return $this->jsonError('Unauthorized branch access to this ticket.');
        }

        $success = $this->kotModel->startCooking($kotId);
        if (!$success) {
            return $this->jsonError('Ticket could not be updated.');
        }

        $this->kotModel->writeAudit('kds', 'start_cooking', 'kots', $kotId, null, [
            'kot_number' => $kot['kot_number'],
            'time'       => date('Y-m-d H:i:s'),
        ], "Cooking started for {$kot['kot_number']}");

        return $this->jsonSuccess(['status' => 'preparing'], "Preparation started for {$kot['kot_number']}");
    }

    /**
     * Bump / Mark KOT ready in the kitchen.
     */
    public function bump(int $kotId): ResponseInterface
    {
        $this->authorize('kds.view');

        $kot = $this->kotModel->find($kotId);
        if (!$kot) {
            return $this->jsonError('Ticket not found.');
        }

        if ($this->currentBranchId && (int)($kot['branch_id'] ?? 0) !== $this->currentBranchId) {
            return $this->jsonError('Unauthorized branch access to this ticket.');
        }

        $this->kotModel->bumpKot($kotId, $this->currentUserId);

        $this->kotModel->writeAudit('kds', 'bump_ticket', 'kots', $kotId, null, [
            'kot_number' => $kot['kot_number'],
            'time'       => date('Y-m-d H:i:s'),
        ], "Ticket {$kot['kot_number']} bumped (marked ready)");

        // Push in-app notification
        try {
            (new \App\Models\NotificationModel())->createNotification(
                null,
                'kitchen',
                "Kitchen: {$kot['kot_number']} Ready!",
                "Order items are cooked and ready to be served.",
                ['kot_id' => $kotId, 'kot_number' => $kot['kot_number']],
                (int)($kot['branch_id'] ?? ($this->currentBranchId ?: 1))
            );
        } catch (\Throwable $ne) {
            log_message('error', 'Notification dispatch failed: ' . $ne->getMessage());
        }

        return $this->jsonSuccess([], "Ticket {$kot['kot_number']} marked Ready!");
    }

    /**
     * Toggle individual item prep status in KDS.
     */
    public function toggleItem(int $itemId): ResponseInterface
    {
        $this->authorize('kds.view');

        $item = $this->orderItemModel->find($itemId);
        if (!$item) {
            return $this->jsonError('Item not found.');
        }

        $newStatus = ($item['status'] === 'ready') ? 'preparing' : 'ready';
        $this->orderItemModel->update($itemId, ['status' => $newStatus]);

        return $this->jsonSuccess(['status' => $newStatus], "Item marked {$newStatus}");
    }
}
