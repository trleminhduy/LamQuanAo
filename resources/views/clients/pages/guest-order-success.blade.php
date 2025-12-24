@extends('layouts.client')

@section('title', 'ĐẶT HÀNG THÀNH CÔNG')
@section('breadcrumb', 'ĐẶT HÀNG THÀNH CÔNG')

@section('content')
<div class="ltn__checkout-area mb-105">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="text-success">✓ Đặt hàng thành công!</h2>
            <p>Mã đơn hàng: <strong>#{{ $order->id }}</strong></p>
            <p>Số điện thoại: <strong>{{ $order->guest_phone }}</strong></p>
            <p class="text-muted">(Lưu lại số điện thoại để tra cứu đơn hàng)</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="ltn__checkout-single-content mt-50">
                    <h4 class="title-2">Thông tin đơn hàng</h4>
                    
                    <p><strong>Người nhận:</strong> {{ $order->guest_name }}</p>
                    <p><strong>Địa chỉ:</strong> {{ $order->shippingAddress->address }}</p>
                    <p><strong>Tổng tiền:</strong> <span class="text-danger">{{ number_format($order->total_price, 0, ',', '.') }}đ</span></p>
                    <p><strong>Thanh toán:</strong> {{ $order->payment->payment_method == 'cod' ? 'COD' : 'Chuyển khoản' }}</p>
                    
                    <div class="text-center mt-4">
                        <a href="{{ route('home') }}" class="btn theme-btn-1 btn-effect-1 text-uppercase">Về trang chủ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection