@extends('layouts.admin')

@section('title', 'Quản lý hoàn trả')

@section('content')
    <div class="right_col" role="main">
        <h1 class="h3 mb-4 text-gray-800">Danh sách yêu cầu hoàn trả</h1>

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable">
                      <thead>
    <tr>
        <th>ID</th>
        <th>Khách hàng</th>
        <th>Đơn hàng</th>
        <th>Sản phẩm</th>
        <th>Số lượng</th>
        <th>Số tiền</th>
        <th>Lý do & Ảnh minh chứng</th>  <!-- Sửa tiêu đề -->
        <th>Trạng thái</th>
        <th>Ngày yêu cầu</th>
        <th>Thao tác</th>
    </tr>
</thead>
<tbody>
    @foreach($refunds as $refund)
    <tr>
        <td>{{ $refund->id }}</td>
        <td>
            {{ $refund->user->name ?? 'N/A' }}
            <br>
            <small>{{ $refund->user->email ?? '' }}</small>
        </td>
        <td>
            <a href="{{ route('admin.orders-detail', $refund->orderItem->order->id) }}" target="_blank">
                #{{ $refund->orderItem->order->id }}
            </a>
        </td>
        <td>{{ $refund->orderItem->productVariant->product->name ?? 'N/A' }}</td>
        <td>{{ $refund->quantity }}</td>
        <td>{{ number_format($refund->amount, 0, ',', '.') }}đ</td>
        <td>
            <small>{{ Str::limit($refund->reason, 50) }}</small>
            <br>
            @if($refund->image)
                <a href="{{ asset('storage/' . $refund->image) }}" target="_blank">
                    <img src="{{ asset('storage/' . $refund->image) }}" 
                         alt="Minh chứng" 
                         style="max-width: 80px; cursor: pointer;" 
                         class="img-thumbnail mt-1">
                </a>
            @else
                <small class="text-muted">Không có ảnh</small>
            @endif
        </td>
        <td>
            @if($refund->status == 'pending')
                <span class="badge badge-warning">Chờ xử lý</span>
            @elseif($refund->status == 'approved')
                <span class="badge badge-success">Đã duyệt</span>
            @elseif($refund->status == 'rejected')
                <span class="badge badge-danger">Từ chối</span>
            @endif
        </td>
        <td>{{ $refund->created_at->format('d/m/Y H:i') }}</td>
        <td>
            @if($refund->status == 'pending')
                <button class="btn btn-success btn-sm" onclick="handleRefund({{ $refund->id }}, 'approve')">
                    <i class="fas fa-check"></i> Duyệt
                </button>
                <button class="btn btn-danger btn-sm" onclick="handleRefund({{ $refund->id }}, 'reject')">
                    <i class="fas fa-times"></i> Từ chối
                </button>
            @else
                <span class="text-muted">Đã xử lý</span>
            @endif
        </td>
    </tr>
    @endforeach
</tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function handleRefund(refundId, action) {
            const actionText = action === 'approve' ? 'duyệt' : 'từ chối';

            if (!confirm(`Bạn có chắc muốn ${actionText} yêu cầu hoàn trả này?`)) {
                return;
            }

            const url = action === 'approve' ?
                '{{ route('admin.refunds.approve') }}' :
                '{{ route('admin.refunds.reject') }}';

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id: refundId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    if (data.status) {
                        location.reload();
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Có lỗi xảy ra!');
                });
        }
    </script>
@endsection
