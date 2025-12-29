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
                    {{-- đơn từ đây --}}
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>
                                    <img src="{{ $item->productVariant->product->firstImage?->image ? asset('storage/' . $item->productVariant->product->firstImage->image) : asset('storage/uploads/products/default-product.png') }}"
                                        alt="{{ $item->productVariant->product->name }}" width="50">
                                </td>
                                <td>
                                    {{ $item->productVariant->product->name }}
                                    <br>
                                    <small class="text-muted">
                                        Size: {{ $item->productVariant->size->name ?? 'N/A' }} -
                                        Màu: {{ $item->productVariant->color->name ?? 'N/A' }}
                                    </small>

                                    {{-- Hiển thị trạng thái refund nếu có --}}
                                    {{-- @if ($item->refund)
                                        <br>
                                        @if ($item->refund->status == 'pending')
                                            <span class="badge bg-warning">Đang chờ duyệt hoàn trả</span>
                                        @elseif($item->refund->status == 'approved')
                                            <span class="badge bg-success">Đã duyệt hoàn trả</span>
                                        @elseif($item->refund->status == 'rejected')
                                            <span class="badge bg-danger">Từ chối hoàn trả</span>
                                        @endif
                                    {{-- @endif --}}
                                </td>
                                <td>{{ number_format($item->productVariant->price, 0, ',', '.') }} đ</td>
                                <td>{{ $item->quantity }}</td>
                                <td>
                                    {{ number_format($item->productVariant->price * $item->quantity, 0, ',', '.') }} đ

                                    {{-- Button yêu cầu hoàn trả --}}
                                    {{-- @if ($order->status == 'delivered' && !$item->refund)
                                        <br>
                                        <button class="btn btn-sm btn-outline-danger mt-2"
                                            onclick="openRefundModal({{ $item->id }}, '{{ $item->productVariant->product->name }}', {{ $item->quantity }})">
                                            <i class="fas fa-undo"></i> Hoàn trả
                                        </button>
                                    {{-- @endif --}}
                                </td>
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
                    @php
                        // Kiểm tra xem có sản phẩm nào đang hoàn trả hoặc đã được duyệt hoàn trả không
                        $hasRefund = $order->items->filter(function($item) {
                            return $item->refund && in_array($item->refund->status, ['pending', 'approved']);
                        })->isNotEmpty();
                    @endphp

                    @if (!$hasRefund)
                        <div class="alert alert-success mt-3">
                            <i class="fas fa-truck"></i> Đơn hàng đã được giao! Vui lòng xác nhận đã nhận hàng.
                        </div>
                        <form action="{{ route('orders.confirmReceived', $order->id) }}" method="POST"
                            onsubmit="return confirm('Xác nhận bạn đã nhận được hàng? Sẽ không thể hoàn trả sau khi bấm')">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg mt-2">
                                <i class="fas fa-check-circle"></i> Đã nhận được hàng
                            </button>
                        </form>
                    @else
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-info-circle"></i> Có sản phẩm đang trong quá trình hoàn trả. Vui lòng đợi xử lý hoàn tất.
                        </div>
                    @endif
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


    {{-- Modal yêu cầu hoàn trả --}}
<div class="modal fade" id="refundModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yêu cầu hoàn trả sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="refundForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="refund_order_item_id" name="order_item_id">
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Sản phẩm:</strong></label>
                        <p id="refund_product_name"></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Số lượng hoàn trả <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="quantity" id="refund_quantity" min="1" required>
                        <small class="text-muted">Tối đa: <span id="max_quantity"></span></small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Lý do hoàn trả <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="reason" rows="3" placeholder="VD: Sản phẩm bị lỗi, không đúng size..." required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Ảnh minh chứng <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="image" id="refund_image" accept="image/*" required>
                        <small class="text-muted">Chụp rõ sản phẩm lỗi/hư hỏng (tối đa 2MB)</small>
                        <div class="mt-2">
                            <img id="image_preview" style="max-width: 200px; display: none;" class="img-thumbnail">
                        </div>
                    </div>
                    
                    <div class="alert alert-warning">
                        <small><i class="fas fa-info-circle"></i> Chỉ được yêu cầu hoàn trả trong vòng <strong>3 ngày</strong> kể từ khi nhận hàng.</small>
                    </div>
                    
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="fas fa-paper-plane"></i> Gửi yêu cầu hoàn trả
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let refundModal;

document.addEventListener('DOMContentLoaded', function() {
    refundModal = new bootstrap.Modal(document.getElementById('refundModal'));
    
    // Preview ảnh khi chọn
    document.getElementById('refund_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('image_preview');
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });
});

function openRefundModal(orderItemId, productName, maxQty) {
    document.getElementById('refund_order_item_id').value = orderItemId;
    document.getElementById('refund_product_name').textContent = productName;
    document.getElementById('refund_quantity').value = 1;
    document.getElementById('refund_quantity').max = maxQty;
    document.getElementById('max_quantity').textContent = maxQty;
    document.getElementById('image_preview').style.display = 'none';
    document.getElementById('refundForm').reset();
    refundModal.show();
}

document.getElementById('refundForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Hiển thị loading
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
    
    fetch('{{ route('refund.store') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        
        if(data.status) {
            alert(data.message);
            refundModal.hide();
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(err => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        console.error(err);
        alert('Có lỗi xảy ra, vui lòng thử lại!');
    });
});
</script>
@endsection


{{-- bất kỳ file nào cũng cần phải có @extends --}}
