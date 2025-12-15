@extends('layouts.client')

@section('title', 'CHI TIẾT ĐƠN HÀNG')
@section('breadcrumb', 'CHI TIẾT ĐƠN HÀNG')


@section('content')
    <div class="liton__shopping-cart-area mb-120">
        <div class="container mt-4">
            <h3> Chi tiết đơn hàng # {{ $order->id }}</h3>
            <p> Ngày đặt {{ $order->created_at->format('d/m/Y H:i') }} </p>
            <p> Trạng thái:
                @if ($order->status == 'pending')
                    <span class="badge bg-warning"> Chờ xác nhận </span>
                @elseif($order->status == 'processing')
                    <span class="badge bg-primary"> Đang xử lý </span>
                @elseif($order->status == 'assigned')
                    <span class="badge bg-info"> Đã phân công giao hàng </span>
                @elseif($order->status == 'shipping')
                    <span class="badge bg-warning text-dark"> Đang giao hàng </span>
                @elseif($order->status == 'delivered')
                    <span class="badge bg-success"> Đã giao hàng </span>
                @elseif($order->status == 'completed')
                    <span class="badge bg-success"> Hoàn thành </span>
                @elseif($order->status == 'cancelled')
                    <span class="badge bg-danger"> Đã huỷ </span>
                @endif
            </p>
            <p> Phương thức thanh toán:
                @if ($order->payment && $order->payment->payment_method == 'cash')
                    <span class="badge bg-primary"> Thanh toán khi nhận hàng </span>
                @elseif($order->payment && $order->payment->payment_method == 'vnpay')
                    <span class="badge bg-primary"> Thanh toán qua VNPay </span>
                @elseif($order->payment && $order->payment->payment_method == 'momo')
                    <span class="badge bg-primary"> Thanh toán qua MoMo </span>
                @elseif($order->payment && $order->payment->payment_method == 'paypal')
                    <span class="badge bg-primary"> Thanh toán qua PayPal </span>
                @endif
            </p>
            <p> Tổng tiền (bao gồm phí vận chuyển):
                {{ number_format($order->total_price, 0, ',', '.') }} đ

            </p>
            <h4 class="mt-4">
                Sản phẩm trong đơn hàng
            </h4>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Ảnh</th>
                            <th>Sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>
                                    <img src="{{ asset('storage/' . $item->productVariant->product->image) }}"
                                        alt="" width="50">
                                </td>
                                <td>{{ $item->productVariant->product->name }}</td>
                                <td>{{ number_format($item->productVariant->price, 0, ',', '.') }} đ</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->productVariant->price * $item->quantity, 0, ',', '.') }} đ</td>
                            </tr>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <h4 class="mt-4">
                    Thông tin giao hàng
                </h4>
                <p> Người nhận:{{ $order->shippingAddress->full_name }} </p>
                <p> Địa chỉ:{{ $order->shippingAddress->address }} </p>
                <p> Thành phố: {{ $order->shippingAddress->city }}</p>
                <p> Số điện thoại:{{ $order->shippingAddress->phone }} </p>

                @if ($order->status == 'pending')
                    <form action="{{ route('order.cancel', $order->id) }}" method="POST"
                        onsubmit="return confirm('Bạn có chắc chắn huỷ đơn hàng này?')">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm mt-3"> Huỷ đơn hàng </button>
                    </form>
                @endif

                @if ($order->status == 'delivered')
                    <div class="alert alert-success mt-3">
                        <i class="fas fa-truck"></i> Đơn hàng đã được giao! Vui lòng xác nhận đã nhận hàng.
                    </div>
                    <form action="{{ route('orders.confirmReceived', $order->id) }}" method="POST"
                        onsubmit="return confirm('Xác nhận bạn đã nhận được hàng?')">
                        @csrf
                        <button type="submit" class="btn btn-success btn-lg mt-2">
                            <i class="fas fa-check-circle"></i> Đã nhận được hàng
                        </button>
                    </form>
                @endif

                @if ($order->status == 'completed')
                    <h4 class="mt-4">
                        Đánh giá sản phẩm
                    </h4>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Đánh giá</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>{{ $item->productVariant->product->name }}</td>
                                    <td>
                                        <a href="{{ route('products.detail', $item->productVariant->product->slug) }}"
                                            class="btn theme-btn-1 btn-effect-1">Đánh giá</a>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

            </div>
        </div>
    </div>
@endsection


{{-- bất kỳ file nào cũng cần phải có @extends --}}
