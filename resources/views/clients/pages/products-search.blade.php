@extends('layouts.client')

@section('title', 'TÌM KIẾM SẢN PHẨM')
@section('breadcrumb', 'TÌM KIẾM SẢN PHẨM')


@section('content')
    <!-- PRODUCT DETAILS AREA START -->
    <div class="ltn__product-area ltn__product-gutter mb-120">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title-area ltn__section-title-2--- text-center">
                        <h1 class="section-title">Kết quả tìm kiếm: "{{ $keyword }}"</h1>
                        <p>Tìm thấy {{ $products->total() }} sản phẩm</p>
                    </div>

                    <!-- Grid sản phẩm -->
                    <div class="products-grid" id="products-container">
                        @foreach ($products as $product)
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
                    @if($products->hasPages())
                    <div class="products-pagination">
                        {!! $products->links('clients.components.pagination.pagination_custom') !!}
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
    <!-- PRODUCT DETAILS AREA END -->
@endsection

{{-- bất kỳ file nào cũng cần phải có @extends --}}
