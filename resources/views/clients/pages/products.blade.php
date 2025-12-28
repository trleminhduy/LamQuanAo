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
                    <input type="text" class="amount" readonly value="0 - 1,000,000 vnđ" style="border:0; color:#f6931f; font-weight:bold; width:100%; margin-bottom:10px;">
                    <div class="slider-range"></div>
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
                    <select id="sort-by">
                        <option value="default">Sắp xếp mặc định</option>
                        <option value="latest">Sản phẩm mới</option>
                        <option value="price_asc">Giá: thấp đến cao</option>
                        <option value="price_desc">Giá: cao đến thấp</option>
                    </select>
                </div>
            </div>

            <!-- Loading spinner -->
            <div id="loading-spinner" style="display:none; text-align:center; padding:20px;">
                <div class="spinner"></div>
                <p>Đang tải...</p>
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
                        <div class="product-rating">Chưa có</div>
                        <p class="product-card-price">{{ number_format($product->price, 0, ',', '.') }} VNĐ</p>
                        <div class="product-card-actions">
                            <button class="btn-quick-view" title="Xem nhanh">👁️</button>
                            <a href="{{ route('products.detail', $product->slug) }}" class="btn-add-to-cart" title="Thêm vào giỏ">🛒</a>
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

@endsection





