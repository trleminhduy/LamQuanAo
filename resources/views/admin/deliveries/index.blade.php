@extends('layouts.admin')

@section('title', 'Quản lý giao hàng')

@section('content')
    <div class="right_col" role="main">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h3">Quản lý giao hàng</h1>
            </div>

            <!-- Filter -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <select name="status" class="form-select">
                                <option value="">Tất cả trạng thái</option>
                                <option value="pending">Chờ xử lý</option>
                                <option value="processing">Đang chuẩn bị</option>
                                <option value="assigned">Đã phân công</option>
                                <option value="shipping">Đang giao</option>
                                <option value="delivered">Đã giao</option>
                                <option value="completed">Hoàn tất</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="delivery_user_id" class="form-select">
                                <option value="">Tất cả nhân viên</option>
                                @foreach ($deliveryUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">Lọc</button>
                        </div>
                    </form>
                </div>
            </div>

        <!-- Table -->
        <div class="card">
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Khách hàng</th>
                            <th>Địa chỉ giao</th>
                            <th>Nhân viên giao</th>
                            <th>Trạng thái</th>
                            <th>Thời gian</th>
                            <th>Liên hệ</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->user?->name ?? 'N/A' }}</td>
                                <td>{{ $order->shippingAddress?->address ?? 'N/A' }}</td>
                                <td>
                                    @if ($order->deliveryUser)
                                        <span class="custom-badge badge-info">{{ $order->deliveryUser->name }}</span>
                                    @else
                                        <span class="text-muted">Chưa phân công</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($order->status == 'assigned')
                                        <span class="custom-badge badge-info">Đã phân công</span>
                                    @elseif($order->status == 'shipping')
                                        <span class="custom-badge badge-warning">Đang giao</span>
                                    @elseif($order->status == 'delivered')
                                        <span class="custom-badge badge-success">Đã giao</span>
                                    @elseif($order->status == 'completed')
                                        <span class="custom-badge badge-success">Đã hoàn thành</span>
                                    @endif
                                </td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $order->shippingAddress?->phone ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('admin.deliveries.assignForm', $order) }}"
                                        class="btn btn-sm btn-primary">
                                        @if ($order->deliveryUser)
                                            Đổi người giao
                                        @else
                                            Phân công
                                        @endif
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Không có đơn hàng</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-end mt-3">
                    {{ $orders->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

<style>
    
    .pagination {
        margin-bottom: 0;
    }
    .pagination .page-link {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    .pagination .page-item {
        margin: 0 2px;
    }
</style>