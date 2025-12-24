@extends('layouts.client')

@section('title', 'GIỎ HÀNG')
@section('breadcrumb', 'GIỎ HÀNG')

@section('content')

    <div class="cart-container">
        @if ($cartItems->count() > 0)
            <div class="cart-wrapper">
                <!-- Bảng giỏ hàng -->
                <div class="cart-table-section">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Giá</th>
                                <th>Số lượng</th>
                                <th>Tổng</th>
                                <th>Xóa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cartItems as $item)
                                <tr class="cart-item" data-id="{{ $item->id }}">
                                    <td class="cart-product-info">
                                        <img src="{{ $item->productVariant->product->firstImage?->image ? asset('storage/' . $item->productVariant->product->firstImage->image) : asset('storage/uploads/products/default-product.png') }}"
                                            alt="{{ $item->productVariant->product->name }}">
                                        <div class="product-details">
                                            <h4>{{ $item->productVariant->product->name }}</h4>
                                            <p>
                                                <span>Size: {{ $item->productVariant->size->name }}</span> |
                                                <span>Màu: {{ $item->productVariant->color->name }}</span>
                                            </p>
                                        </div>
                                    </td>
                                    <td class="cart-price">{{ number_format($item->productVariant->price, 0, ',', '.') }}
                                        VNĐ</td>
                                    <td class="cart-quantity">
                                        <div class="quantity-control">
                                            <button class="qty-btn decrease"
                                                onclick="updateQuantity({{ $item->id }}, -1)">-</button>
                                            <span class="qty-display">{{ $item->quantity }}</span>
                                            <button class="qty-btn increase"
                                                onclick="updateQuantity({{ $item->id }}, 1)">+</button>
                                            <input type="hidden" class="qty-input" value="{{ $item->quantity }}"
                                                data-max="{{ $item->productVariant->stock }}">
                                        </div>
                                    </td>
                                    <td class="cart-item-total">
                                        {{ number_format($item->productVariant->price * $item->quantity, 0, ',', '.') }}
                                        VNĐ</td>
                                    <td class="cart-remove">
                                        <button class="btn-remove" onclick="removeItem({{ $item->id }})">🗑️</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="cart-actions">
                        <a href="{{ route('products.index') }}" class="btn-continue">← Tiếp tục mua sắm</a>
                        <button class="btn-clear" onclick="clearCart()">Xóa giỏ hàng</button>
                    </div>
                </div>

                <!-- Tổng đơn hàng -->
                <div class="cart-summary">
                    <h3>Tổng đơn hàng</h3>
                    <div class="summary-row">
                        <span>Tạm tính:</span>
                        <span class="grand-total">{{ number_format($total, 0, ',', '.') }} VNĐ</span>
                    </div>
                    <div class="summary-row">
                        <span>Phí vận chuyển:</span>
                        <span>30.000đ</span>
                    </div>
                    <div class="summary-row total">
                        <span>Tổng cộng:</span>
                        <span class="grand-total">{{ number_format($total + 30000, 0, ',', '.') }} VNĐ</span>
                    </div>
                    <a href="@if (Auth::check()) {{ route('checkout.index') }} @else {{ route('guest.checkout.index') }} @endif"
                        class="btn-checkout">Tiến hành thanh toán </a>
                </div>
            </div>
        @else
            <!-- Giỏ hàng trống -->
            <div class="cart-empty">
                <div class="empty-icon">🛒</div>
                <h3>Giỏ hàng trống</h3>
                <p>Bạn chưa có sản phẩm nào trong giỏ hàng</p>
                <a href="{{ route('products.index') }}" class="btn-shopping">Mua sắm ngay</a>
            </div>
        @endif
    </div>

    <script>
        // Cập nhật số lượng (tăng/giảm)
        function updateQuantity(cartItemId, change) {
            let row = document.querySelector(`.cart-item[data-id="${cartItemId}"]`);
            let input = row.querySelector('.qty-input');
            let display = row.querySelector('.qty-display');
            let newQty = parseInt(input.value) + change;
            let max = parseInt(input.dataset.max);

            if (newQty < 1) newQty = 1;
            if (newQty > max) {
                toastr.warning('Vượt quá số lượng tồn kho!');
                return;
            }

            input.value = newQty;
            display.textContent = newQty;
            changeQuantity(cartItemId, newQty);
        }

        // Thay đổi số lượng trực tiếp
        function changeQuantity(cartItemId, quantity) {
            $.ajax({
                url: `/cart/update/${cartItemId}`,
                type: 'PUT',
                data: {
                    quantity: quantity,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Cập nhật giá tiền từng dòng
                        $(`.cart-item[data-id="${cartItemId}"] .cart-item-total`).text(response.itemTotal +
                            ' VNĐ');
                        // Cập nhật số lượng hiển thị
                        $(`.cart-item[data-id="${cartItemId}"] .qty-display`).text(quantity);
                        // Cập nhật tổng tiền
                        $('.grand-total').text(response.grandTotal + ' VNĐ');
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('Có lỗi xảy ra!');
                }
            });
        }

        // Xóa sản phẩm
        function removeItem(cartItemId) {
            if (!confirm('Bạn có chắc muốn xóa sản phẩm này?')) return;

            $.ajax({
                url: `/cart/remove/${cartItemId}`,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $(`.cart-item[data-id="${cartItemId}"]`).fadeOut(300, function() {
                            $(this).remove();
                            // Nếu giỏ hàng trống, reload trang
                            if ($('.cart-item').length === 0) {
                                location.reload();
                            } else {
                                // Tính lại tổng tiền
                                recalculateTotal();
                            }
                        });
                        toastr.success(response.message);
                    }
                },
                error: function() {
                    toastr.error('Có lỗi xảy ra!');
                }
            });
        }

        // Xóa toàn bộ giỏ hàng
        function clearCart() {
            if (!confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?')) return;

            $.ajax({
                url: '/cart/clear',
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                }
            });
        }

        // Tính lại tổng tiền
        function recalculateTotal() {
            let total = 0;
            $('.cart-item').each(function() {
                let price = parseInt($(this).find('.cart-price').text().replace(/[^0-9]/g, ''));
                let qty = parseInt($(this).find('.qty-input').val());
                total += price * qty;
            });
            $('.grand-total').text(total.toLocaleString('vi-VN') + ' VNĐ');
        }
    </script>

@endsection
