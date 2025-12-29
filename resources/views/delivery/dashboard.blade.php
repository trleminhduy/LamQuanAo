@extends('layouts.admin')

@section('title', 'Dashboard - Nhân viên giao hàng')

@section('content')
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12">
            <h2>Xin chào, {{ Auth::guard('admin')->user()->name }}!</h2>
            <p class="text-muted">Nhân viên giao hàng</p>
        </div>
    </div>

    <!-- Thống kê -->
    <div class="row" style="display: inline-block; width: 100%;">
        <div class="tile_count">
            <div class="col-md-3 col-sm-4 tile_stats_count">
                <span class="count_top"><i class="fa fa-clock-o"></i> Chờ giao</span>
                <div class="count blue">{{ $assignedCount }}</div>
            </div>
            <div class="col-md-3 col-sm-4 tile_stats_count">
                <span class="count_top"><i class="fa fa-truck"></i> Đang giao</span>
                <div class="count orange">{{ $shippingCount }}</div>
            </div>
            <div class="col-md-3 col-sm-4 tile_stats_count">
                <span class="count_top"><i class="fa fa-check"></i> Đã giao</span>
                <div class="count green">{{ $deliveredCount }}</div>
            </div>
            <div class="col-md-3 col-sm-4 tile_stats_count">
                <span class="count_top"><i class="fa fa-check-circle"></i> Hoàn tất</span>
                <div class="count purple">{{ $completedCount }}</div>
            </div>
        </div>
    </div>

    <!-- Đơn hàng gần đây -->
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Đơn hàng gần đây</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Khách hàng</th>
                                <th>Địa chỉ</th>
                                <th>SĐT</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->user?->name ?? 'N/A' }}</td>
                                <td>{{ Str::limit($order->shippingAddress?->address ?? 'N/A', 40) }}</td>
                                <td>{{ $order->shippingAddress?->phone ?? 'N/A' }}</td>
                                <td>
                                    @if($order->status == 'assigned')
                                        <span class="badge badge-primary">Chờ giao</span>
                                    @elseif($order->status == 'shipping')
                                        <span class="badge badge-warning">Đang giao</span>
                                    @elseif($order->status == 'delivered')
                                        <span class="badge badge-success">Đã giao</span>
                                    @elseif($order->status == 'completed')
                                        <span class="badge badge-info">Hoàn tất</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.deliveries.showOrder', $order) }}" class="btn btn-sm btn-info">
                                        Chi tiết
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Chưa có đơn hàng nào</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
