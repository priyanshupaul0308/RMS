<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KotModel;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * KotController – Kitchen Order Ticket records and 80mm Thermal Printing.
 */
class KotController extends BaseController
{
    protected KotModel       $kotModel;
    protected OrderModel     $orderModel;
    protected OrderItemModel $orderItemModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface  $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface            $logger
    ): void {
        parent::initController($request, $response, $logger);
        $this->kotModel       = new KotModel();
        $this->orderModel     = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
    }

    /**
     * KOT Tickets List.
     */
    public function index(): string
    {
        $this->authorize('kot.view');

        $kots = $this->kotModel->select('kots.*, orders.order_number, orders.order_type, restaurant_tables.table_number, COUNT(order_items.id) AS item_count')
            ->join('orders',            'orders.id = kots.order_id', 'left')
            ->join('restaurant_tables', 'restaurant_tables.id = orders.table_id', 'left')
            ->join('order_items',       'order_items.order_id = orders.id', 'left')
            ->groupBy('kots.id')
            ->orderBy('kots.id', 'DESC')
            ->findAll(50);

        return $this->renderView('admin/kot/index', [
            'pageTitle' => 'Kitchen Order Tickets (KOT)',
            'kots'      => $kots,
        ]);
    }

    /**
     * Printable 80mm Thermal Kitchen Order Ticket.
     */
    public function print(int $id): string|ResponseInterface
    {
        $this->authorize('kot.view');

        $kot = $this->kotModel->select('kots.*, orders.order_number, orders.order_type, orders.notes AS order_notes, restaurant_tables.table_number, floors.name AS floor_name, users.first_name AS waiter_name')
            ->join('orders',            'orders.id = kots.order_id', 'left')
            ->join('restaurant_tables', 'restaurant_tables.id = orders.table_id', 'left')
            ->join('floors',            'floors.id = restaurant_tables.floor_id', 'left')
            ->join('users',             'users.id = orders.waiter_id', 'left')
            ->where('kots.id', $id)
            ->first();

        if (!$kot) {
            return redirect()->route('admin.kot.index')->with('error', 'KOT not found.');
        }

        $items = $this->orderItemModel->where('order_id', $kot['order_id'])->findAll();

        // Increment reprint count if printed again
        $this->kotModel->update($id, [
            'reprint_count' => (int)$kot['reprint_count'] + 1,
            'printed_at'    => date('Y-m-d H:i:s'),
        ]);

        return view('admin/kot/print', [
            'kot'   => $kot,
            'items' => $items,
        ]);
    }
}
