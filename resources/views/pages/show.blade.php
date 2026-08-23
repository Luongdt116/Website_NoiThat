@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <h2 class="mb-4"><i class="bi bi-shield-check text-wood"></i> {{ $title }}</h2>

        @php($policy = [
            'doi-tra' => [
                ['icon' => 'arrow-repeat', 'title' => 'Đổi trả trong 7 ngày', 'text' => 'Sản phẩm bị lỗi do nhà sản xuất hoặc không đúng mô tả trên website được đổi mới hoặc hoàn tiền trong vòng 7 ngày kể từ khi nhận hàng. Sản phẩm phải còn nguyên tem nhãn, chưa qua sử dụng.'],
                ['icon' => 'cash-coin', 'title' => 'Hoàn tiền COD', 'text' => 'Với đơn thanh toán COD, tiền được hoàn trực tiếp cho khách sau khi cửa hàng nhận lại hàng và kiểm tra tình trạng. Thời gian xử lý 3–5 ngày làm việc.'],
                ['icon' => 'truck', 'title' => 'Miễn phí vận chuyển đổi trả', 'text' => 'Cửa hàng chịu toàn bộ chi phí vận chuyển hai chiều nếu lỗi thuộc về bên bán. Trường hợp khách đổi ý, phí vận chuyển do khách chi trả.'],
            ],
            'giao-hang' => [
                ['icon' => 'truck', 'title' => 'Giao hàng toàn quốc', 'text' => 'Giao hàng đến 63 tỉnh thành trên cả nước. Nội thành giao trong 1–2 ngày, tỉnh khác 2–5 ngày làm việc kể từ khi đơn được xác nhận.'],
                ['icon' => 'cash-stack', 'title' => 'Thanh toán COD', 'text' => 'Khách hàng chỉ thanh toán khi nhận được hàng và kiểm tra kỹ sản phẩm. Nhân viên giao hàng hỗ trợ mở gói kiểm tra trước khi thu tiền.'],
                ['icon' => 'box-seam', 'title' => 'Đóng gói an toàn', 'text' => 'Nội thất được bọc mút xốp, đóng thùng gỗ chống va đập. Nếu hàng hư hỏng do vận chuyển, cửa hàng gửi thay miễn phí.'],
            ],
            'bao-mat' => [
                ['icon' => 'lock', 'title' => 'Bảo vệ thông tin cá nhân', 'text' => 'Thông tin họ tên, số điện thoại, địa chỉ chỉ dùng để xử lý đơn hàng. Chúng tôi không bán hay chia sẻ dữ liệu cho bên thứ ba vì mục đích thương mại.'],
                ['icon' => 'shield-lock', 'title' => 'Mật khẩu được mã hóa', 'text' => 'Mật khẩu tài khoản được mã hóa một chiều (bcrypt) — kể cả quản trị viên cũng không xem được. Hãy dùng mật khẩu mạnh và không chia sẻ cho người khác.'],
                ['icon' => 'chat-dots', 'title' => 'Liên hệ về quyền riêng tư', 'text' => 'Bạn có quyền yêu cầu xóa tài khoản cùng dữ liệu liên quan. Gửi yêu cầu qua email của cửa hàng, chúng tôi phản hồi trong vòng 48 giờ.'],
            ],
        ][$slug])

        <div class="list-group list-group-flush">
            @foreach($policy as $item)
                <div class="list-group-item px-0 py-3">
                    <h5 class="mb-2"><i class="bi bi-{{ $item['icon'] }} text-wood me-2"></i>{{ $item['title'] }}</h5>
                    <p class="text-muted mb-0">{{ $item['text'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
