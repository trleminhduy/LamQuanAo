@extends('layouts.client')

@section('title', 'ĐƠN HÀNG CỦA BẠN')
@section('breadcrumb', 'ĐƠN HÀNG CỦA BẠN')

@section('content')
<div class="ltn__checkout-area mb-105">
    <div class="container">
        <h2 class="mb-4">Đơn hàng của bạn</h2>
        
        @foreach($orders as $order)
        <div class="ltn__checkout-single-content mt-50 border p-4">
            <h5>Đơn hàng #{{ $order->id }}</h5>
            <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Trạng thái:</strong> {{ $order->status }}</p>
            <p><strong>Tổng tiền:</strong> <span class="text-danger">{{ number_format($order->total_price, 0, ',', '.') }}đ</span></p>
            
            <div class="mt-3">
                <strong>Sản phẩm:</strong>
                @foreach($order->items as $item)
                <div>- {{ $item->productVariant->product->name }} x{{ $item->quantity }}</div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection