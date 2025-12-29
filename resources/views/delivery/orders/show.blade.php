@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng #' . $order->id)

@section('content')
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Chi tiết đơn hàng #{{ $order->id }}</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_content">
                        <!-- Thông tin khách hàng -->
                        <div class="row mb-3" style="font-size:18px">
                            <div class="col-md-6">
                                <h5>Thông tin khách hàng</h5>
                                <p><strong>Tên:</strong> {{ $order->shippingAddress->full_name }}</p>
                                <p><strong>SĐT:</strong> {{ $order->shippingAddress->phone }}</p>
                                <p><strong>Địa chỉ:</strong> {{ $order->shippingAddress->address }}</p>
                              
                            </div>
                            <div class="col-md-6" style="font-size: 18px">
                                <h5>Thông tin đơn hàng</h5>
                                <p><strong>Mã đơn:</strong> #{{ $order->id }}</p>
                                <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                                <p><strong style="color:red">Tổng tiền:</strong> {{ number_format($order->total_price) }}đ</p>
                                <p><strong>Trạng thái:</strong> 
                                    @if($order->status == 'assigned')
                                        <span class="badge badge-warning">Chờ giao</span>
                                    @elseif($order->status == 'shipping')
                                        <span class="badge badge-info">Đang giao</span>
                                    @else
                                        <span class="badge badge-success">Đã giao</span>
                                    @endif
                                </p>
                                @if($order->delivery_note)
                                    <p><strong style="color: red">Ghi chú:</strong> {{ $order->delivery_note }}</p>
                                @endif
                                @if($order->delivery_started_at)
                                    <p><strong>Bắt đầu giao:</strong> {{ $order->delivery_started_at->format('d/m/Y H:i') }}</p>
                                @endif
                                @if($order->delivery_completed_at)
                                    <p><strong>Hoàn tất giao:</strong> {{ $order->delivery_completed_at->format('d/m/Y H:i') }}</p>
                                @endif
                            </div>
                        </div>

                        <hr>

                        <!-- Sản phẩm -->
                        <h5>Danh sách sản phẩm</h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Size</th>
                                        <th>Màu</th>
                                        <th>Số lượng</th>
                                        <th>Giá</th>
                                        <th>Tổng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                    <tr>
                                        <td>{{ $item->productVariant->product->name }}</td>
                                        <td>{{ $item->productVariant->size->name }}</td>
                                        <td>{{ $item->productVariant->color->name }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format($item->price) }}đ</td>
                                        <td>{{ number_format($item->price * $item->quantity) }}đ</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>Tổng cộng:</strong></td>
                                        <td><strong>{{ number_format($order->total_price) }}đ</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Buttons hành động -->
                        <div class="mt-4">
                            @if($order->status == 'assigned')
                                <form method="POST" action="{{ route('admin.deliveries.start', $order) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success" onclick="return confirm('Bắt đầu giao hàng cho đơn này?')">
                                        <i class="fa fa-truck"></i> Bắt đầu giao hàng
                                    </button>
                                </form>
                            @elseif($order->status == 'shipping')
                                <!-- Nút hoàn thành giao hàng -->
                                <form method="POST" action="{{ route('admin.deliveries.complete', $order) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success" onclick="return confirm('Xác nhận đã giao hàng thành công?')">
                                        <i class="fa fa-check-circle"></i> Hoàn thành giao hàng
                                    </button>
                                </form>
                                
                                <span class="mx-2">hoặc</span>
                                
                                <!-- Form báo khách không nhận -->
                                <form method="POST" action="{{ route('delivery.orders.customerRejected', $order) }}" style="display:inline;">
                                    @csrf
                                    <select name="reason" class="form-control" style="display:inline-block; width:auto;" required>
                                        <option value="">-- Chọn lý do không giao được --</option>
                                        <option value="Không nghe máy">Không nghe máy</option>
                                        <option value="Khách không có nhà">Khách không có nhà</option>
                                        <option value="Từ chối nhận hàng">Từ chối nhận hàng</option>
                                        <option value="Sai địa chỉ">Sai địa chỉ</option>
                                        <option value="Đổi ý không mua">Đổi ý không mua</option>
                                    </select>
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Xác nhận khách không nhận hàng?')">
                                        <i class="fa fa-times-circle"></i> Khách không nhận
                                    </button>
                                </form>
                            @else
                                <div class="alert alert-success">
                                    <i class="fa fa-check"></i> Đơn hàng đã được giao thành công!
                                    @if($order->delivery_completed_at)
                                        <p class="mb-0 mt-2">Thời gian hoàn tất: {{ $order->delivery_completed_at->format('d/m/Y H:i') }}</p>
                                    @endif
                                </div>
                            @endif

                            <a href="{{ route('admin.deliveries.myOrders') }}" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Quay lại danh sách
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
