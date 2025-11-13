@extends('layouts.client')

@section('title', 'CHI TIẾT SẢN PHẨM')
@section('breadcrumb', 'CHI TIẾT SẢN PHẨM')


@section('content')
    <!-- SHOP DETAILS AREA START -->
    <div class="ltn__shop-details-area pb-85">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="ltn__shop-details-inner mb-60">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="ltn__shop-details-img-gallery">
                                    <div class="ltn__shop-details-large-img">
                                        {{-- Ảnh siêu to bự start here --}}
                                        <div class="single-large-img">
                                            @foreach ($product->images as $image)
                                                <a href="{{ asset('storage/' . $image->image) }}"
                                                    data-rel="lightcase:myCollection">
                                                    <img src="{{ asset('storage/' . $image->image) }}"
                                                        alt="{{ $product->name }}">
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                    {{-- Ảnh nhỏ hơn start here --}}
                                    <div class="ltn__shop-details-small-img slick-arrow-2">
                                        @foreach ($product->images as $image)
                                            <div class="single-small-img">
                                                <img src="{{ asset('storage/' . $image->image) }}"
                                                    alt="{{ $product->name }}">
                                            </div>
                                        @endforeach

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="modal-product-info shop-details-info pl-0">
                                    <div class="product-ratting">
                                        <ul>
                                            <li><a href="#"><i class="fas fa-star"></i></a></li>

                                            <li class="review-total"> <a href="#"> ( 95 Reviews )</a></li>
                                        </ul>
                                    </div>
                                    <h3>{{ $product->name }}</h3>
                                    <div class="product-price">
                                        <span>{{ number_format($product->price, 0, ',', '.') }} VNĐ</span>

                                    </div>
                                    <div class="modal-product-meta ltn__product-details-menu-1">
                                        <ul>
                                            <li>
                                                <strong>Danh mục:</strong>
                                                <span>
                                                    <a href="javascript:void(0)">{{ $product->category->name }}</a>
                                                </span>
                                            </li>
                                        </ul>
                                    </div>

                                    {{-- Chọn màu sắc --}}
                                    <div class="ltn__product-details-menu-1 mb-20">
                                        <ul>
                                            <li>
                                                <strong>Màu sắc:</strong>
                                                <div class="ltn__color-widget mt-10">
                                                    <ul>
                                                        @if($product->variants && $product->variants->count() > 0)
                                                            @foreach($product->variants->unique('color_id') as $variant)
                                                                <li class="{{ strtolower($variant->color->name) }}" 
                                                                    data-color-id="{{ $variant->color_id }}"
                                                                    title="{{ $variant->color->name }}"
                                                                    style="cursor: pointer;">
                                                                    <a href="javascript:void(0)"></a>
                                                                </li>
                                                            @endforeach
                                                        @else
                                                            {{-- Màu mặc định khi chưa có variants --}}
                                                            <li class="black" title="Đen"><a href="javascript:void(0)"></a></li>
                                                            <li class="white" title="Trắng"><a href="javascript:void(0)"></a></li>
                                                            <li class="red" title="Đỏ"><a href="javascript:void(0)"></a></li>
                                                            <li class="blue" title="Xanh dương"><a href="javascript:void(0)"></a></li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>

                                    {{-- Chọn kích thước và số lượng --}}
                                    <div class="ltn__product-details-menu-2">
                                        <ul>
                                            <li>
                                                <select class="nice-select" id="product-size">
                                                    @if($product->variants && $product->variants->count() > 0)
                                                        @foreach($product->variants->unique('size_id') as $variant)
                                                            <option value="{{ $variant->size_id }}">{{ $variant->size->name }}</option>
                                                        @endforeach
                                                    @else
                                                        {{-- Size mặc định khi chưa có variants --}}
                                                        <option value="s">S</option>
                                                        <option value="m">M</option>
                                                        <option value="l">L</option>
                                                        <option value="xl">XL</option>
                                                        <option value="xxl">XXL</option>
                                                    @endif
                                                </select>
                                            </li>
                                            <li>
                                                <label class="mb-0 me-2"><strong>Số lượng:</strong></label>
                                                <div class="cart-plus-minus">
                                                    <input type="text" value="02" name="qtybutton"
                                                        class="cart-plus-minus-box">
                                                </div>
                                            </li>
                                            <li>
                                                <a href="#" class="theme-btn-1 btn btn-effect-1"
                                                    title="Thêm vào giỏ hàng" data-bs-toggle="modal"
                                                    data-bs-target="#add_to_cart_modal">
                                                    <i class="fas fa-shopping-cart"></i>
                                                    <span>THÊM VÀO GIỎ HÀNG</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="ltn__product-details-menu-3">
                                        <ul>
                                            <li>
                                                <a href="#" class="" title="Wishlist" data-bs-toggle="modal"
                                                    data-bs-target="#liton_wishlist_modal">
                                                    <i class="far fa-heart"></i>
                                                    <span>Thêm vào yêu thích</span>
                                                </a>
                                            </li>

                                        </ul>
                                    </div>
                                    <hr>
                                    <div class="ltn__social-media">
                                        <ul>
                                            <li>Chia sẻ:</li>
                                            <li><a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                                            </li>
                                            <li><a href="#" title="Twitter"><i class="fab fa-twitter"></i></a></li>
                                            <li><a href="#" title="Linkedin"><i class="fab fa-linkedin"></i></a>
                                            </li>
                                            <li><a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                                            </li>

                                        </ul>
                                    </div>
                                    <hr>
                                    <div class="ltn__safe-checkout">
                                        <h5>Thanh toán an toàn đảm bảo</h5>
                                        <img src="{{ asset('assets/clients/img/icons/payment-2.png') }}"
                                            alt="Payment Image">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Shop Tab Start -->
                    <div class="ltn__shop-details-tab-inner ltn__shop-details-tab-inner-2">
                        <div class="ltn__shop-details-tab-menu">
                            <div class="nav">
                                <a class="active show" data-bs-toggle="tab" href="#liton_tab_details_description">Mô tả sản
                                    phẩm</a>
                                <a data-bs-toggle="tab" href="#liton_tab_details_reviews" class="">Đánh giá</a>
                            </div>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="liton_tab_details_description">
                                <div class="ltn__shop-details-tab-content-inner">
                                    <h4 class="title-2">Mô tả.</h4>
                                    <p> {{ $product->description }} </p>

                                </div>
                            </div>
                            <div class="tab-pane fade" id="liton_tab_details_reviews">
                                <div class="ltn__shop-details-tab-content-inner">
                                    <h4 class="title-2">Đánh giá của khách hàng</h4>
                                    <div class="product-ratting">
                                        <ul>
                                            <li><a href="#"><i class="fas fa-star"></i></a></li>
                                            <li><a href="#"><i class="fas fa-star"></i></a></li>

                                            <li class="review-total"> <a href="#"> ( 95 Reviews )</a></li>
                                        </ul>
                                    </div>
                                    <hr>
                                    <!-- comment-area -->
                                    <div class="ltn__comment-area mb-30">
                                        <div class="ltn__comment-inner">
                                            <ul>
                                                <li>
                                                    <div class="ltn__comment-item clearfix">
                                                        <div class="ltn__commenter-img">
                                                            <img src="img/testimonial/1.jpg" alt="Image">
                                                        </div>
                                                        <div class="ltn__commenter-comment">
                                                            <h6><a href="#">Adam Smit</a></h6>
                                                            <div class="product-ratting">
                                                                <ul>
                                                                    <li><a href="#"><i class="fas fa-star"></i></a>
                                                                    </li>
                                                                    <li><a href="#"><i class="fas fa-star"></i></a>
                                                                    </li>
                                                                    <li><a href="#"><i class="fas fa-star"></i></a>
                                                                    </li>
                                                                    <li><a href="#"><i
                                                                                class="fas fa-star-half-alt"></i></a>
                                                                    </li>
                                                                    <li><a href="#"><i class="far fa-star"></i></a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing
                                                                elit. Doloribus, omnis fugit corporis iste magnam
                                                                ratione.</p>
                                                            <span class="ltn__comment-reply-btn">September 3,
                                                                2020</span>
                                                        </div>
                                                    </div>
                                                </li>

                                            </ul>
                                        </div>
                                    </div>
                                    <!-- comment-reply -->
                                    <div class="ltn__comment-reply-area ltn__form-box mb-30">
                                        <form action="#">
                                            <h4 class="title-2">Thêm đánh giá</h4>
                                            <div class="mb-30">
                                                <div class="add-a-review">
                                                    <h6>Số sao:</h6>
                                                    <div class="product-ratting">
                                                        <ul>
                                                            <li><a href="#"><i class="fas fa-star"></i></a></li>
                                                            <li><a href="#"><i class="fas fa-star"></i></a></li>
                                                            <li><a href="#"><i class="fas fa-star"></i></a></li>
                                                            <li><a href="#"><i class="fas fa-star-half-alt"></i></a>
                                                            </li>
                                                            <li><a href="#"><i class="far fa-star"></i></a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="input-item input-item-textarea ltn__custom-icon">
                                                <textarea placeholder="Type your comments...."></textarea>
                                            </div>
                                            <div class="input-item input-item-name ltn__custom-icon">
                                                <input type="text" placeholder="Type your name....">
                                            </div>
                                            <div class="input-item input-item-email ltn__custom-icon">
                                                <input type="email" placeholder="Type your email....">
                                            </div>
                                            <div class="input-item input-item-website ltn__custom-icon">
                                                <input type="text" name="website" placeholder="Type your website....">
                                            </div>
                                            <label class="mb-0"><input type="checkbox" name="agree"> Save my name,
                                                email, and website in this browser for the next time I
                                                comment.</label>
                                            <div class="btn-wrapper">
                                                <button class="btn theme-btn-1 btn-effect-1 text-uppercase"
                                                    type="submit">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Shop Tab End -->
                </div>
            </div>
        </div>
    </div>
    <!-- SHOP DETAILS AREA END -->

    <!-- PRODUCT SLIDER AREA START -->
    <div class="ltn__product-slider-area ltn__product-gutter pb-70">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title-area ltn__section-title-2">

                        <h1 class="section-title">Sản phẩm liên quan<span>.</span></h1>
                    </div>
                </div>
            </div>
            <div class="row ltn__related-product-slider-one-active slick-arrow-1">

                {{-- Duyệt sản phẩm tương tự --}}
                @foreach ($relatedProducts as $relatedProduct)
                    <!-- ltn__product-item -->
                        <div class="col-lg-12">
                            <div class="ltn__product-item ltn__product-item-3 text-center">
                                <div class="product-img">
                                    <a href="{{ route('products.detail', $relatedProduct->slug) }}">
                                        <img src="{{ $relatedProduct->image_url }}" alt="{{ $relatedProduct->name }}"></a>

                                    {{-- <div class="product-badge">
                                        <ul>
                                            <li class="sale-badge">New</li>
                                        </ul>
                                    </div> --}}
                                    <div class="product-hover-action">
                                        <ul>
                                            <li>
                                                <a href="javascript:void(0)" title="Xem nhanh" data-bs-toggle="modal"
                                                    data-bs-target="#quick_view_modal-{{ $relatedProduct->id }}">
                                                    <i class="far fa-eye"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)" title="Thêm vào giỏ hàng"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#add_to_cart_modal-{{ $relatedProduct->id }}">
                                                    <i class="fas fa-shopping-cart"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)" title="Yêu thích" data-bs-toggle="modal"
                                                    data-bs-target="#liton_wishlist_modal-{{ $relatedProduct->id }}">
                                                    <i class="far fa-heart"></i></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="product-info">
                                    <div class="product-ratting">
                                        <ul>
                                            <li><a href="#"><i class="fas fa-star"></i></a></li>

                                        </ul>
                                    </div>
                                    <h2 class="product-title"><a
                                            href="{{ route('products.detail', $relatedProduct->slug) }}">{{ $relatedProduct->name }}</a>
                                    </h2>

                                    <div class="product-price">
                                        <span>{{ number_format($relatedProduct->price, 0, ',', '.') }} VND</span>

                                    </div>
                                </div>
                            </div>
                        </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- PRODUCT SLIDER AREA END -->

@endsection

@push('styles')
<style>
    /* Align size selector and quantity on one line */
    .ltn__product-details-menu-2 ul {
        display: flex;
        align-items: center;
        gap: 16px; /* space between items */
        flex-wrap: wrap;
    }
    .ltn__product-details-menu-2 ul li {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .ltn__product-details-menu-2 ul li label {
        margin: 0 8px 0 0 !important; /* keep inline with control */
        line-height: 1.2;
        display: inline-flex !important;
        align-items: center;
        width: auto !important;
    }
    .ltn__product-details-menu-2 .nice-select {
        margin: 0;
    }
    .ltn__product-details-menu-2 .cart-plus-minus {
        margin: 0;
    }
    /* Style cho color selector */
    .ltn__color-widget ul li {
        position: relative;
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }
    
    .ltn__color-widget ul li:hover,
    .ltn__color-widget ul li.active {
        border-color: #333;
        transform: scale(1.1);
    }
    
    .ltn__color-widget ul li.active::after {
        content: '✓';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #fff;
        font-weight: bold;
        font-size: 14px;
        text-shadow: 0 0 2px rgba(0,0,0,0.5);
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Chọn màu sắc
        $('.ltn__color-widget ul li').click(function() {
            $('.ltn__color-widget ul li').removeClass('active');
            $(this).addClass('active');
            
            let colorId = $(this).data('color-id');
            let colorName = $(this).attr('title');
            console.log('Đã chọn màu:', colorName, 'ID:', colorId);
        });
        
        // Chọn kích thước
        $('#product-size').change(function() {
            let sizeId = $(this).val();
            let sizeName = $(this).find('option:selected').text();
            console.log('Đã chọn size:', sizeName, 'ID:', sizeId);
        });
        
        // Tự động chọn màu đầu tiên
        $('.ltn__color-widget ul li:first').addClass('active');
    });
</script>
@endpush

{{-- bất kỳ file nào cũng cần phải có @extends --}}
