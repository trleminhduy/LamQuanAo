@extends('layouts.client')

@section('title', 'ĐƠN HÀNG CỦA BẠN')
@section('breadcrumb', 'ĐƠN HÀNG CỦA BẠN')

@section('content')
    <div class="ltn__checkout-area mb-105">
        <div class="container">
            <h2 class="mb-4">Đơn hàng của bạn</h2>

            @foreach ($orders as $order)
                <div class="ltn__checkout-single-content mt-50 border p-4">
                    <h5>Đơn hàng #{{ $order->id }}</h5>
                    <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Trạng thái:</strong>
                        @switch($order->status)
                            @case('pending')
                                <span class="badge bg-warning">Đang chờ xác nhận</span>
                            @break

                            @case('confirmed')
                                <span class="badge bg-info">Đã xác nhận</span>
                            @break

                            @case('shipping')
                                <span class="badge bg-primary">Đang giao hàng</span>
                            @break

                            @case('delivered')
                                <span class="badge bg-success">Đã giao hàng</span>
                            @break
                             @case('processing')
                                <span class="badge bg-success">Đang xử lý</span>
                            @break

                            @case('cancelled')
                                <span class="badge bg-danger">Đã hủy</span>
                            @break
                            @case('delivering')
                                <span class="badge bg-danger">Đang giao hàng</span>
                            @break

                            @default
                                <span class="badge bg-secondary">{{ $order->status }}</span>
                        @endswitch
                    </p>
                    <p><strong>Tổng tiền:</strong> <span
                            class="text-danger">{{ number_format($order->total_price, 0, ',', '.') }}đ</span></p>

                    <div class="mt-3">
                        <strong>Sản phẩm:</strong>
                        @foreach ($order->items as $item)
                            <div>- {{ $item->productVariant->product->name }} x{{ $item->quantity }}</div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
