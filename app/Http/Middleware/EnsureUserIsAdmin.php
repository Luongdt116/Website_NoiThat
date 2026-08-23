<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Middleware 'admin': chỉ cho phép user có is_admin = true đi qua
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Chưa đăng nhập -> đá sang trang đăng nhập
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        // Đăng nhập rồi nhưng không phải admin -> chặn với lỗi 403
        if (! auth()->user()->is_admin) {
            abort(403, 'Chỉ quản trị viên mới được truy cập khu vực này.');
        }

        return $next($request);
    }
}
