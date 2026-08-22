<?php
namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index() {
        $stats = [
            'orders' => Order::count(),
            'revenue' => Order::where('status', '!=', 'cancelled')->sum('total'),
            'products' => Product::count(),
            'users' => User::count(),
        ];
        return view('admin.dashboard', compact('stats'));
    }
}
