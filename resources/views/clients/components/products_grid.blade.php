@foreach($products as $product)
<div class="product-card">
    <a href="{{ route('products.detail', $product->slug) }}">
        <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
    </a>
    <div class="product-card-info">
        <h3><a href="{{ route('products.detail', $product->slug) }}">{{ $product->name }}</a></h3>
        <div class="product-rating">⭐⭐⭐⭐⭐</div>
        <p class="product-card-price">{{ number_format($product->price, 0, ',', '.') }} VNĐ</p>
        <div class="product-card-actions">
            <button class="btn-quick-view" title="Xem nhanh">👁️</button>
            <a href="{{ route('products.detail', $product->slug) }}" class="btn-add-to-cart" title="Thêm vào giỏ">🛒</a>
            <button class="btn-add-wishlist" title="Yêu thích">♡</button>
        </div>
    </div>
</div>
@endforeach
