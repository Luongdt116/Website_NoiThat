<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\OrderService;

class AdminOrderController extends Controller
{
    public function __construct(private OrderService $orders) {}

    public function index()
    {
        return view('admin.orders.index', ['orders' => $this->orders->list()]);
    }

    public function show($id)
    {
        return view('admin.orders.show', ['order' => $this->orders->find($id)]);
    }

    public function confirm($id)
    {
        $this->orders->updateStatus($id, 'confirmed');

        return back();
    }

    public function ship($id)
    {
        $this->orders->updateStatus($id, 'shipping');

        return back();
    }

    public function complete($id)
    {
        $this->orders->updateStatus($id, 'completed');

        return back();
    }

    public function cancel($id)
    {
        $this->orders->updateStatus($id, 'cancelled');

        return back();
    }
}
