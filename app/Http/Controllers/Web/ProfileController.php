<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

// Thông tin tài khoản: user tự cập nhật tên/email + đổi mật khẩu
class ProfileController extends Controller
{
    // Trang "Tài khoản của tôi"
    public function show(Request $r)
    {
        return view('profile.show', ['user' => $r->user()]);
    }

    // Cập nhật họ tên + email (đổi mật khẩu nằm ở form riêng bên dưới)
    public function update(Request $r)
    {
        $user = $r->user();

        $data = $r->validate([
            'name' => 'required|string|max:255',
            // unique trừ chính email hiện tại của user (khi không đổi email vẫn hợp lệ)
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
        ], [
            'email.unique' => 'Email này đã được tài khoản khác sử dụng.',
        ]);

        $user->update($data);

        return back()->with('success', 'Đã cập nhật thông tin tài khoản.');
    }

    // Đổi mật khẩu: phải nhập đúng mật khẩu hiện tại
    public function updatePassword(Request $r)
    {
        $user = $r->user();

        $data = $r->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
            'password.min' => 'Mật khẩu mới tối thiểu 6 ký tự.',
        ]);

        if (! Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        $user->update(['password' => Hash::make($data['password'])]);

        return back()->with('success', 'Đã đổi mật khẩu thành công.');
    }
}
