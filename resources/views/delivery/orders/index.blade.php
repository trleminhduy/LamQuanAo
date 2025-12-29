@extends('layouts.admin')

@section('title', 'Đơn hàng của tôi')

@section('content')
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Đơn hàng của tôi</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Danh sách đơn hàng được giao</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Khách hàng</th>
                                        <th>Địa chỉ giao</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($orders as $order)
                                        <tr>
                                            <td>#{{ $order->id }}</td>
                                            <td>
                                                {{ $order->user?->name ?? 'N/A' }}<br>
                                                <small class="text-muted">{{ $order->user?->phone_number ?? 'N/A' }}</small>
                                            </td>
                                            <td>
                                                {{ $order->shippingAddress->address }}, 
                                               
                                            </td>
                                            <td>{{ number_format($order->total_price) }}đ</td>
                                            <td>
                                                @if($order->status == 'assigned')
                                                    <span class="badge badge-warning">Chờ giao</span>
                                                @elseif($order->status == 'shipping')
                                                    <span class="badge badge-info">Đang giao</span>
                                                @elseif($order->status == 'completed')
                                                    <span class="badge badge-success">Đã giao</span>                                                @elseif($order->status == 'completed')
                                                    <span class="badge badge-primary">Hoàn tất</span>                                                @endif
                                            </td>
                                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('admin.deliveries.showOrder', $order) }}" class="btn btn-sm btn-primary">
                                                    <i class="fa fa-eye"></i> Chi tiết
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Chưa có đơn hàng nào được giao</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($orders->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $orders->links('pagination::bootstrap-4') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
