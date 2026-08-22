<?php
namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index() { return view('admin.users.index', ['users' => User::latest()->paginate(15)]); }
    public function toggle(int $id) {
        $u = User::findOrFail($id);
        $u->update(['is_active' => !$u->is_active]);
        return back();
    }
}
