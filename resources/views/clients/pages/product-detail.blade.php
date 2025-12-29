@extends('layouts.client')

@section('title', 'CHI TIẾT SẢN PHẨM')
@section('breadcrumb', 'CHI TIẾT SẢN PHẨM')

@section('content')

    <!-- Chi tiết sản phẩm -->
    <div class="product-detail-container">
        <div class="product-detail-wrapper">
            <!-- Hình ảnh sản phẩm -->
            <div class="product-images">
                <div class="main-image">
                    @if ($product->images && $product->images->count() > 0)
                        <img src="{{ asset('storage/' . $product->images->first()->image) }}" alt="{{ $product->name }}"
                            id="mainProductImage">
                    @else
                        <img src="{{ asset('assets/clients/img/product/default.jpg') }}" alt="{{ $product->name }}">
                    @endif
                </div>

                @if ($product->images && $product->images->count() > 1)
                    <div class="thumbnail-images">
                        @foreach ($product->images as $image)
                            <img src="{{ asset('storage/' . $image->image) }}" alt="{{ $product->name }}"
                                onclick="changeMainImage(this.src)">
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Thông tin sản phẩm -->
            <div class="product-info-detail">
                <h1>{{ $product->name }}</h1>

                <div class="product-rating">

                </div>

                <div class="product-price-detail">
                    <span id="product-price">
                        @if ($product->variants && $product->variants->count() > 0)
                            {{ number_format($product->variants->first()->price, 0, ',', '.') }} VNĐ
                        @else
                            {{ number_format($product->price, 0, ',', '.') }} VNĐ
                        @endif
                    </span>
                </div>

                <div class="product-category">
                    <strong>Danh mục:</strong> {{ $product->category->name }}
                </div>

                <!-- Chọn màu -->
                <div class="product-options">
                    <label><strong>Màu sắc:</strong></label>
                    <div class="color-options">
                        @if ($product->variants && $product->variants->count() > 0)
                            @foreach ($product->variants->unique('color_id') as $variant)
                                <div class="color-item {{ strtolower($variant->color->name) }}"
                                    data-color-id="{{ $variant->color_id }}" title="{{ $variant->color->name }}">
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Chọn size -->
                <div class="product-options-row">
                    <div class="option-item">
                        <label><strong>Kích thước:</strong></label>
                        <select id="product-size">
                            @if ($product->variants && $product->variants->count() > 0)
                                @foreach ($product->variants->unique('size_id') as $variant)
                                    <option value="{{ $variant->size_id }}">{{ $variant->size->name }}</option>
                                @endforeach
                            @else
                                <option value="s">S</option>
                                <option value="m">M</option>
                                <option value="l">L</option>
                                <option value="xl">XL</option>
                            @endif
                        </select>
                    </div>
                    <div class="option-item">
                        <label><strong>Số lượng:</strong></label>
                        <div class="quantity-control">
                            <button type="button" onclick="decreaseQty()">-</button>
                            <span id="quantity-display" class="quantity-display">1</span>
                            <button type="button" onclick="increaseQty()">+</button>
                            <input type="hidden" name="quantity" id="quantity" value="1">
                        </div>
                    </div>
                    {{-- tồn kho --}}
                    <div class="option-item">
                        <label><strong>Còn lại:</strong></label>
                        <div class="quantity-control">
                            <span id="stock-quantity" class="stock-quantity">
                                @if ($product->variants && $product->variants->count() > 0)
                                    {{ $product->variants->first()->stock }}
                                @else
                                    {{ $product->stock }}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Nút hành động -->
                <div class="product-actions">
                    <button class="btn-add-cart">Thêm vào giỏ hàng</button>
                    <button class="btn-wishlist">♡ Yêu thích</button>
                </div>

                <!-- Chia sẻ -->
                <div class="product-share">
                    <strong>Chia sẻ:</strong>
                    <a href="#">Facebook</a>
                    <a href="#">Twitter</a>
                    <a href="#">Instagram</a>
                </div>
            </div>
        </div>

        <!-- Mô tả và đánh giá -->
        <div class="product-tabs">
            <div class="tab-buttons">
                <button class="tab-btn active" onclick="showTab('description')">Mô tả sản phẩm</button>
                <button class="tab-btn" onclick="showTab('reviews')">Đánh giá</button>
            </div>

            <div id="description" class="tab-content active">
                <h3>Mô tả</h3>
                <p>{{ $product->description }}</p>
            </div>

            <div id="reviews" class="tab-content">
                <div id="reviews-list">
                    @include('clients.components.includes.review-list', ['product' => $product])
                </div>
                {{-- tách riêng để load đánh giá --}}

                <div class="add-review">
                    <h4>Thêm đánh giá</h4>
                    <form id="review-form" data-product-id="{{ $product->id }}">
                        <div class="rating-input">
                            <label>Số sao:</label>
                            <div class="rating-stars">
                                @for ($i = 1; $i <= 5; $i++)
                                    <a href="javascript:void(0)" class="rating-star" data-value="{{ $i }}">
                                        <i class="far fa-star"></i>
                                    </a>
                                @endfor
                            </div>
                        </div>
                        <input type="hidden" name="rating" id="rating-value" value="0">
                        <textarea placeholder="Nhập đánh giá của bạn..." id="review-content"></textarea>

                        <button type="submit">Gửi đánh giá</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Sản phẩm liên quan -->
    <div class="related-products">
        <h2>Sản phẩm liên quan</h2>
        <div class="related-products-grid">
            @foreach ($relatedProducts as $relatedProduct)
                <div class="related-product-item">
                    <a href="{{ route('products.detail', $relatedProduct->slug) }}">
                        <img src="{{ $relatedProduct->image_url }}" alt="{{ $relatedProduct->name }}">
                    </a>
                    <div class="related-product-info">
                        <h4><a
                                href="{{ route('products.detail', $relatedProduct->slug) }}">{{ $relatedProduct->name }}</a>
                        </h4>
                        <p class="related-product-price">{{ number_format($relatedProduct->price, 0, ',', '.') }} VNĐ</p>
                        {{-- <div class="related-product-actions">
                            <button class="btn-quick-view">👁️</button>
                            <button class="btn-add-cart">🛒</button>
                            <button class="btn-wishlist">♡</button>
                        </div> --}}
                    </div>
                </div>
            @endforeach
        </div>
    </div>



    <script>
        // Truyền data từ PHP sang JS
        window.productVariants = @json($product->variants);
        window.cartAddUrl = '{{ route('cart.add') }}';
        window.loginUrl = '{{ route('login') }}';
    </script>

    @push('scripts')
        <script src="{{ asset('assets/clients/js/product-detail.js') }}"></script>
    @endpush
@endsection



{{-- bất kỳ file nào cũng cần phải có @extends --}}
