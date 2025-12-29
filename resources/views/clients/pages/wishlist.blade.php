@extends('layouts.client')

@section('title', 'DANH SÁCH YÊU THÍCH')
@section('breadcrumb', 'VỀ CHÚNG TÔI')


@section('content')
   <div class="cart-container">
        @if ($wishlists->count() > 0)
            <div class="cart-wrapper">
                <!-- Bảng danh sách yêu thích -->
                <div class="cart-table-section">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Giá</th>
                                <th>Xóa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($wishlists as $item)
                                <tr class="cart-item" data-id="{{ $item->id }}">
                                    <td class="cart-product-info">
                                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}">
                                        <div class="product-details">
                                            <h4>
                                                <a href="{{ route('products.detail', $item->product->slug) }}">
                                                    {{ $item->product->name }}
                                                </a>
                                            </h4>
                                            <p>Danh mục: {{ $item->product->category->name }}</p>
                                        </div>
                                    </td>
                                    <td class="cart-price">{{ number_format($item->product->price, 0, ',', '.') }} VNĐ</td>
                                    <td class="cart-remove">
                                        <button class="btn-remove" onclick="removeFromWishlist({{ $item->product->id }})">🗑️</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="cart-actions">
                        <a href="{{ route('products.index') }}" class="btn-continue">← Tiếp tục mua sắm</a>
                    </div>
                </div>
            </div>
        @else
            <!-- Danh sách yêu thích trống -->
            <div class="cart-empty">
                <div class="empty-icon">❤️</div>
                <h3>Danh sách yêu thích trống</h3>
                <p>Bạn chưa có sản phẩm yêu thích nào</p>
                <a href="{{ route('products.index') }}" class="btn-shopping">Mua sắm ngay</a>
            </div>
        @endif
    </div>



@endsection


{{-- bất kỳ file nào cũng cần phải có @extends --}}