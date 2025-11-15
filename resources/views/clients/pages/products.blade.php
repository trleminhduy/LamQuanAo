@extends('layouts.client')

@section('title', 'SẢN PHẨM')
@section('breadcrumb', 'SẢN PHẨM')

@section('content')

<!-- Trang sản phẩm -->
<div class="products-page-container">
    <div class="products-wrapper">
        <!-- Sidebar -->
        <div class="products-sidebar">
            <!-- Danh mục -->
            <div class="sidebar-widget">
                <h4>Danh mục sản phẩm</h4>
                <ul class="category-list">
                    <li><a href="javascript:void(0)" class="category-filter active" data-id="">Tất cả sản phẩm</a></li>
                    @foreach($categories as $category)
                    <li><a href="javascript:void(0)" class="category-filter" data-id="{{ $category->id }}">{{ $category->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- Lọc theo giá -->
            <div class="sidebar-widget">
                <h4>Lọc theo giá</h4>
                <div class="price-filter">
                    <label>Giá từ:</label>
                    <input type="number" id="min-price" placeholder="0">
                    <label>Đến:</label>
                    <input type="number" id="max-price" placeholder="10000000">
                    <button onclick="filterByPrice()">Lọc</button>
                </div>
            </div>

            <!-- Kích thước -->
            <div class="sidebar-widget">
                <h4>Kích thước</h4>
                <div class="size-filter">
                    <button class="size-btn">S</button>
                    <button class="size-btn">M</button>
                    <button class="size-btn">L</button>
                    <button class="size-btn">XL</button>
                    <button class="size-btn">XXL</button>
                </div>
            </div>

            <!-- Màu sắc -->
            <div class="sidebar-widget">
                <h4>Màu sắc</h4>
                <div class="color-filter">
                    <span class="color-circle black"></span>
                    <span class="color-circle white"></span>
                    <span class="color-circle red"></span>
                    <span class="color-circle blue"></span>
                    <span class="color-circle green"></span>
                    <span class="color-circle yellow"></span>
                    <span class="color-circle pink"></span>
                    <span class="color-circle gray"></span>
                </div>
            </div>

            <!-- Tìm kiếm -->
            <div class="sidebar-widget">
                <h4>Tìm kiếm</h4>
                <div class="search-box">
                    <input type="text" placeholder="Nhập từ khóa...">
                    <button>🔍</button>
                </div>
            </div>
        </div>

        <!-- Danh sách sản phẩm -->
        <div class="products-main">
            <!-- Thanh công cụ -->
            <div class="products-toolbar">
                <div class="view-options">
                    <button class="view-grid active">⊞</button>
                </div>
                <div class="sort-options">
                    <select id="sort-by" onchange="sortProducts()">
                        <option value="default">Sắp xếp mặc định</option>
                        <option value="latest">Sản phẩm mới</option>
                        <option value="price_asc">Giá: thấp đến cao</option>
                        <option value="price_desc">Giá: cao đến thấp</option>
                    </select>
                </div>
            </div>

            <!-- Loading spinner -->
            <div id="loading-spinner" style="display: none;">
                <div class="spinner"></div>
            </div>

            <!-- Grid sản phẩm -->
            <div class="products-grid" id="products-container">
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
                            <button class="btn-add-to-cart" title="Thêm vào giỏ">🛒</button>
                            <button class="btn-add-wishlist" title="Yêu thích">♡</button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Phân trang -->
            <div class="products-pagination">
                {!! $products->links('clients.components.pagination.pagination_custom') !!}
            </div>
        </div>
    </div>
</div>

<script>
// Lọc theo danh mục
var categoryFilters = document.querySelectorAll('.category-filter');
categoryFilters.forEach(function(filter) {
    filter.addEventListener('click', function() {
        categoryFilters.forEach(function(f) { f.classList.remove('active'); });
        this.classList.add('active');
        
        var categoryId = this.getAttribute('data-id');
        filterProducts(categoryId);
    });
});

// Lọc theo giá
function filterByPrice() {
    var minPrice = document.getElementById('min-price').value;
    var maxPrice = document.getElementById('max-price').value;
    console.log('Lọc giá:', minPrice, '-', maxPrice);
    // Thêm logic lọc ở đây
}

// Sắp xếp sản phẩm
function sortProducts() {
    var sortBy = document.getElementById('sort-by').value;
    console.log('Sắp xếp theo:', sortBy);
    // Thêm logic sắp xếp ở đây
}

// Lọc sản phẩm
function filterProducts(categoryId) {
    var container = document.getElementById('products-container');
    var spinner = document.getElementById('loading-spinner');
    
    spinner.style.display = 'block';
    container.style.opacity = '0.5';
    
    // Gọi API lọc sản phẩm
    var url = '{{ route("products.filter") }}?category_id=' + categoryId;
    
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        container.innerHTML = data.products;
        spinner.style.display = 'none';
        container.style.opacity = '1';
    })
    .catch(error => {
        console.error('Lỗi:', error);
        spinner.style.display = 'none';
        container.style.opacity = '1';
    });
}
</script>

@endsection
