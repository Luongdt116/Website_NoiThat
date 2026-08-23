<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

// Test trang Tài khoản: cập nhật thông tin + đổi mật khẩu
class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::create([
            'name' => 'Khách test', 'email' => 'khach@test.vn',
            'password' => bcrypt('password'), 'is_admin' => false, 'is_active' => true,
        ]);
    }

    /**
     * Đổi họ tên thành công.
     */
    public function test_user_can_update_name(): void
    {
        $this->actingAs($this->user)
            ->put('/tai-khoan', ['name' => 'Tên mới', 'email' => 'khach@test.vn']);

        $this->assertDatabaseHas('users', ['id' => $this->user->id, 'name' => 'Tên mới']);
    }

    /**
     * Không lấy được email của người khác.
     */
    public function test_cannot_take_existing_email(): void
    {
        User::create([
            'name' => 'Người khác', 'email' => 'bandau@test.vn',
            'password' => bcrypt('password'), 'is_admin' => false, 'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)
            ->put('/tai-khoan', ['name' => 'Khách test', 'email' => 'bandau@test.vn']);

        $response->assertSessionHasErrors('email');
        // Email của user không đổi
        $this->assertDatabaseHas('users', ['id' => $this->user->id, 'email' => 'khach@test.vn']);
    }

    /**
     * Giữ nguyên email hiện tại phải hợp lệ (unique bỏ qua chính mình).
     */
    public function test_keeping_own_email_is_valid(): void
    {
        $this->actingAs($this->user)
            ->put('/tai-khoan', ['name' => 'Khách test', 'email' => 'khach@test.vn'])
            ->assertSessionHas('success');
    }

    /**
     * Đổi mật khẩu sai mật khẩu hiện tại → bị chặn, mật khẩu giữ nguyên.
     */
    public function test_wrong_current_password_blocks_change(): void
    {
        $response = $this->actingAs($this->user)
            ->put('/tai-khoan/mat-khau', [
                'current_password' => 'sai-roi',
                'password' => 'newpass123',
                'password_confirmation' => 'newpass123',
            ]);

        // Bị chặn với lỗi trên trường current_password, mật khẩu cũ không đổi
        $response->assertSessionHasErrors('current_password');
        $this->user->refresh();
        $this->assertTrue(Hash::check('password', $this->user->password));
        $this->assertFalse(Hash::check('newpass123', $this->user->password));
    }

    /**
     * Đổi mật khẩu đúng trình tự: nhập đủ current + new + xác nhận khớp,
     * sau đó đăng nhập lại bằng mật khẩu mới được.
     */
    public function test_password_change_succeeds_and_relogin_works(): void
    {
        $this->actingAs($this->user)
            ->put('/tai-khoan/mat-khau', [
                'current_password' => 'password',
                'password' => 'newpass123',
                'password_confirmation' => 'newpass123',
            ])->assertSessionHas('success');

        $this->user->refresh();
        $this->assertTrue(Hash::check('newpass123', $this->user->password));

        // Đăng nhập lại bằng mật khẩu mới
        $this->post('/dang-nhap', ['email' => 'khach@test.vn', 'password' => 'newpass123']);
        $this->assertAuthenticatedAs($this->user);
    }
}
