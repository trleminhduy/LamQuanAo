@extends('layouts.client_home')

@section('title', 'TRANG CHỦ')

@section('content')

    <!-- BANNER AREA START -->
    <div class="ltn__banner-area" style="margin-top: 80px;">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ltn__banner-item">
                        <img src="{{ asset('assets/clients/img/banner/clothes-banner.jpg') }}" alt="Banner"
                            style="width: 100%; border-radius: 8px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- BANNER AREA END -->

   


    <!-- CATEGORY AREA START -->
    <div class="ltn__category-area section-bg-1-- ltn__primary-bg before-bg-1 bg-image bg-overlay-theme-black-5--0 pt-115 pb-90"
        data-bg="img/bg/5.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title-area ltn__section-title-2 text-center">
                        <h1 class="section-title white-color---">Tin tức mới nhất</h1>
                    </div>
                </div>
            </div>
            <div class="row ltn__category-slider-active slick-arrow-1">
                @foreach ($categories as $category)
                    <div class="col-12">
                        <div class="ltn__category-item ltn__category-item-3 text-center">
                            <div class="ltn__category-item-img">
                                <a href="shop.html">
                                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}">
                                </a>
                            </div>
                            <div class="ltn__category-item-name">
                                <h5><a href="shop.html">{{ $category->name }}</a></h5>
                                <h6>({{ $category->products->count() }} Sản phẩm)</h6>
                            </div>
                        </div>
                    </div>
                @endforeach


            </div>
        </div>
    </div>
    <!-- CATEGORY AREA END -->

    <!-- PRODUCT TAB AREA START (product-item-3) -->
    <div class="ltn__product-tab-area ltn__product-gutter pt-115 pb-70">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title-area ltn__section-title-2 text-center">
                        <h1 class="section-title">Sản phẩm</h1>
                    </div>
                    <div class="ltn__tab-menu ltn__tab-menu-2 ltn__tab-menu-top-right-- text-uppercase text-center">
                        <div class="nav">
                            @foreach ($categories as $index => $category)
                                <a class="{{ $index == 0 ? 'active show' : '' }}" data-bs-toggle="tab"
                                    href="#tab_{{ $category->id }}"> {{ $category->name }}</a>
                            @endforeach
                        </div>
                    </div>
                    <div class="tab-content">
                        @foreach ($categories as $index => $category)
                            <div class="tab-pane fade {{ $index == 0 ? 'active show' : '' }}"
                                id="tab_{{ $category->id }}">
                                <div class="ltn__product-tab-content-inner">
                                    <div class="row ltn__tab-product-slider-one-active slick-arrow-1">
                                        <!-- ltn__product-item -->
                                        @foreach ($category->products as $product)
                                            <div class="col-lg-12">
                                                <div class="ltn__product-item ltn__product-item-3 text-center">
                                                    <div class="product-img">
                                                        <a href="#"><img src="{{ $product->image_url }}"
                                                                alt="{{ $product->name }}"></a>
                                                        {{-- <div class="product-badge">
                                                    <ul>
                                                        <li class="sale-badge">-19%</li>
                                                    </ul>
                                                </div> --}}
                                                        <div class="product-hover-action">
                                                            <ul>
                                                                <li>
                                                                    <a href="#" title="Xem nhanh"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#quick_view_modal-{{ $product->id }}">
                                                                        <i class="far fa-eye"></i>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="#" title="Thêm vào giỏ hàng"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#add_to_cart_modal-{{ $product->id }}">
                                                                        <i class="fas fa-shopping-cart"></i>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="#" title="Yêu thích"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#liton_wishlist_modal-{{ $product->id }}">
                                                                        <i class="far fa-heart"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="product-info">
                                                        <div class="product-ratting">
                                                            <ul>
                                                                <li><a href="#"><i class="fas fa-star"></i></a></li>
                                                                <li><a href="#"><i class="fas fa-star"></i></a></li>
                                                                <li><a href="#"><i class="fas fa-star"></i></a></li>
                                                                <li><a href="#"><i
                                                                            class="fas fa-star-half-alt"></i></a>
                                                                </li>
                                                                <li><a href="#"><i class="far fa-star"></i></a></li>
                                                                <li class="review-total"> <a href="#"> (24)</a></li>
                                                            </ul>
                                                        </div>
                                                        <h2 class="product-title"><a
                                                                href="product-details.html">{{ $product->name }}</a></h2>
                                                        <div class="product-price">
                                                            <span>{{ number_format($product->price, 0, ',', '.') }} VNĐ</span>

                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        @endforeach

                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- PRODUCT TAB AREA END -->



    <!-- PRODUCT AREA START (product-item-3) -->
    <div class="ltn__product-area ltn__product-gutter pt-115 pb-70">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title-area ltn__section-title-2 text-center">
                        <h1 class="section-title">Sản phẩm bán chạy</h1>
                    </div>
                </div>
            </div>
            <div class="row ltn__tab-product-slider-one-active--- slick-arrow-1">
                <!-- ltn__product-item -->
                {{-- @foreach ($bestSellingProducts as $product)
                    
                @endforeach --}}


                <!--  -->
            </div>
        </div>
    </div>
    <!-- PRODUCT AREA END -->

    <!-- CALL TO ACTION START (call-to-action-4) -->
    <div class="ltn__call-to-action-area ltn__call-to-action-4 bg-image pt-115 pb-120"
        data-bg="{{ asset('assets/clients/img/bg/6.jpg') }}">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="call-to-action-inner call-to-action-inner-4 text-center">
                        <div class="section-title-area ltn__section-title-2">
                            <h6 class="section-subtitle ltn__secondary-color">Bạn có thắc mắc gì không? </h6>
                            <h1 class="section-title white-color">0838567807</h1>
                        </div>
                        <div class="btn-wrapper">
                            <a href="tel:+0838567807" class="theme-btn-1 btn btn-effect-1">GỌI NGAY</a>
                            <a href="contact.html" class="btn btn-transparent btn-effect-4 white-color">LIÊN HỆ</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- CALL TO ACTION END -->


    <div class="ltn__blog-area pt-115 pb-70">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title-area ltn__section-title-2 text-center">
                        <h1 class="section-title white-color---">Tin tức mới nhất</h1>
                    </div>
                </div>
            </div>
            <div class="row  ltn__blog-slider-one-active slick-arrow-1 ltn__blog-item-3-normal">
                <!-- Blog Item -->
                <div class="col-lg-12">
                    <div class="ltn__blog-item ltn__blog-item-3">
                        <div class="ltn__blog-img">
                            <a href="blog-details.html"><img src="{{ asset('assets/clients/img/blog/blog1.jpg') }}"
                                    alt="#" style="width: 100%; height: 250px; object-fit: cover;"></a>
                        </div>
                        <div class="ltn__blog-brief">
                            <div class="ltn__blog-meta">
                                <ul>
                                    <li class="ltn__blog-author">
                                        <a href="#"><i class="far fa-user"></i>by: Admin</a>
                                    </li>
                                    <li class="ltn__blog-tags">
                                        <a href="#"><i class="fas fa-tags"></i>Services</a>
                                    </li>
                                </ul>
                            </div>
                            <h3 class="ltn__blog-title"><a href="blog-details.html">Xu hướng thời trang mùa thu đông
                                    2025</a></h3>
                            <div class="ltn__blog-meta-btn">
                                <div class="ltn__blog-meta">
                                    <ul>
                                        <li class="ltn__blog-date"><i class="far fa-calendar-alt"></i>24 Tháng 6, 2025
                                        </li>
                                    </ul>
                                </div>
                                <div class="ltn__blog-btn">
                                    <a href="blog-details.html">Đọc thêm</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Blog Item -->
                <div class="col-lg-12">
                    <div class="ltn__blog-item ltn__blog-item-3">
                        <div class="ltn__blog-img">
                            <a href="blog-details.html"><img src="{{ asset('assets/clients/img/blog/blog1.jpg') }}"
                                    alt="#" style="width: 100%; height: 250px; object-fit: cover;"></a>
                        </div>
                        <div class="ltn__blog-brief">
                            <div class="ltn__blog-meta">
                                <ul>
                                    <li class="ltn__blog-author">
                                        <a href="#"><i class="far fa-user"></i>bởi: Admin</a>
                                    </li>
                                    <li class="ltn__blog-tags">
                                        <a href="#"><i class="fas fa-tags"></i>Thời trang</a>
                                    </li>
                                </ul>
                            </div>
                            <h3 class="ltn__blog-title"><a href="blog-details.html">Cách phối đồ đẹp cho mùa hè</a></h3>
                            <div class="ltn__blog-meta-btn">
                                <div class="ltn__blog-meta">
                                    <ul>
                                        <li class="ltn__blog-date"><i class="far fa-calendar-alt"></i>23 Tháng 10, 2025
                                        </li>
                                    </ul>
                                </div>
                                <div class="ltn__blog-btn">
                                    <a href="blog-details.html">Đọc thêm</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Blog Item -->
                <div class="col-lg-12">
                    <div class="ltn__blog-item ltn__blog-item-3">
                        <div class="ltn__blog-img">
                            <a href="blog-details.html"><img src="{{ asset('assets/clients/img/blog/blog1.jpg') }}"
                                    alt="#" style="width: 100%; height: 250px; object-fit: cover;"></a>
                        </div>
                        <div class="ltn__blog-brief">
                            <div class="ltn__blog-meta">
                                <ul>
                                    <li class="ltn__blog-author">
                                        <a href="#"><i class="far fa-user"></i>bởi: Admin</a>
                                    </li>
                                    <li class="ltn__blog-tags">
                                        <a href="#"><i class="fas fa-tags"></i>Thời trang</a>
                                    </li>
                                </ul>
                            </div>
                            <h3 class="ltn__blog-title"><a href="blog-details.html">Bí quyết chọn quần áo phù hợp với dáng
                                    người</a></h3>
                            <div class="ltn__blog-meta-btn">
                                <div class="ltn__blog-meta">
                                    <ul>
                                        <li class="ltn__blog-date"><i class="far fa-calendar-alt"></i>14 Tháng 11,
                                            2025</li>
                                    </ul>
                                </div>
                                <div class="ltn__blog-btn">
                                    <a href="blog-details.html">Đọc thêm</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>






@endsection
