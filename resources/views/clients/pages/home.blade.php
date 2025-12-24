@extends('layouts.client_home')

@section('title', 'TRANG CHỦ')

@section('content')

<!-- Banner -->
<div class="home-banner">
    <div class="home-container">
        <img src="{{ asset('assets/clients/img/banner/clothes-banner.jpg') }}" alt="Banner">
    </div>
</div>

<!-- Danh mục -->
<div class="home-container">
    <div class="home-section-title">
        <h2>Danh mục sản phẩm</h2>
    </div>
    <div class="home-categories">
        @foreach ($categories as $category)
        <div class="home-category-item">
            <a href="shop.html">
                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}">
            </a>
            <h3><a href="shop.html">{{ $category->name }}</a></h3>
            <p>({{ $category->products->count() }} Sản phẩm)</p>
        </div>
        @endforeach
    </div>
</div>

<!-- Sản phẩm -->
<div class="home-container">
    <div class="home-section-title">
        <h2>Sản phẩm nổi bật</h2>
    </div>
    
    <!-- Tabs -->
    <div class="home-product-tabs">
        @foreach ($categories as $index => $category)
        <button class="home-tab-button {{ $index == 0 ? 'active' : '' }}" 
                onclick="showProducts({{ $category->id }})">
            {{ $category->name }}
        </button>
        @endforeach
    </div>

    <!-- Danh sách sản phẩm theo tab -->
    @foreach ($categories as $index => $category)
    <div id="products-{{ $category->id }}" 
         class="home-products" 
         style="display: {{ $index == 0 ? 'flex' : 'none' }}">
        @foreach ($category->products as $product)
        <div class="home-product-item">
            <a href="{{ route('products.detail', $product->slug) }}">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
            </a>
            <div class="home-product-info">
                <h4>{{ $product->name }}</h4>
                <p class="home-product-price">{{ number_format($product->price, 0, ',', '.') }} VNĐ</p>
            </div>
        </div>
        @endforeach
    </div>
    @endforeach
</div>

<!-- Call to Action -->
<div class="home-container">
    <div class="home-cta">
        <h2>Bạn có thắc mắc gì không?</h2>
        <p>Liên hệ với chúng tôi ngay</p>
        <a href="tel:0838567807">Gọi: 0838567807</a>
        {{-- <a href="{{ route('contact') }}">Liên hệ</a> --}}
    </div>
</div>



<script>
// JavaScript đơn giản cho tabs
function showProducts(categoryId) {
    // Ẩn tất cả sản phẩm
    var allProducts = document.querySelectorAll('.home-products');
    for (var i = 0; i < allProducts.length; i++) {
        allProducts[i].style.display = 'none';
    }
    
    // Bỏ active tất cả buttons
    var allButtons = document.querySelectorAll('.home-tab-button');
    for (var i = 0; i < allButtons.length; i++) {
        allButtons[i].classList.remove('active');
    }
    
    // Hiện sản phẩm được chọn
    document.getElementById('products-' + categoryId).style.display = 'flex';
    
    // Active button được click
    event.target.classList.add('active');
}
</script>

@endsection
