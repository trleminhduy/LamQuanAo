<div class="ltn__utilize-menu-head">
    <span class="ltn__utilize-menu-title">Giỏ hàng</span>
    <button class="ltn__utilize-close">×</button>
</div>

<div class="mini-cart-product-area ltn__scrollbar" id="mini-cart-items">
    <!-- Nội dung sẽ được load bằng JavaScript -->
    <div class="mini-cart-loading" style="text-align: center; padding: 20px;">
        <p>Đang tải...</p>
    </div>
</div>

<div class="mini-cart-footer" id="mini-cart-footer" style="display: none;">
    <div class="mini-cart-sub-total">
        <h5>Tổng tiền: <span id="mini-cart-total">0 VNĐ</span></h5>
    </div>
    <div class="btn-wrapper">
        <a href="{{ route('cart.index') }}" class="theme-btn-1 btn btn-effect-1">Xem giỏ hàng</a>
        <a href="#" class="theme-btn-2 btn btn-effect-2">Thanh toán</a>
    </div>
</div>

<div class="mini-cart-empty" id="mini-cart-empty" style="display: none; text-align: center; padding: 40px 20px;">
    <p style="font-size: 16px; color: #666;">Giỏ hàng trống</p>
    <a href="{{ route('products.index') }}" class="theme-btn-1 btn" style="margin-top: 15px;">Mua sắm ngay</a>
</div>
