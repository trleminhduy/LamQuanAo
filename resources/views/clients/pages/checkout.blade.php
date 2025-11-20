@extends('layouts.client')

@section('title', 'THANH TOÁN')
@section('breadcrumb', 'THANH TOÁN')


@section('content')
    <!-- WISHLIST AREA START -->
    <div class="ltn__checkout-area mb-105">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ltn__checkout-inner">
                        <div class="ltn__checkout-single-content mt-50">
                            <h4 class="title-2">Chi tiết thanh toán</h4>
                            <div class="select-address">
                                <div>
                                    <h6>Chọn địa chỉ khác</h6>
                                </div>
                                <div>
                                    <select name="address_id" id="list_address" class="input-item">
                                        @foreach ($addresses as $address)
                                            <option value="{{ $address->id }}"
                                                {{ $address->is_default ? 'selected' : '' }}>
                                                {{ $address->full_name }} - {{ $address->address }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <a href="{{ route('account') }}" class="btn theme-btn-1 btn-effect-1 text-uppercase">
                                        Thêm địa chỉ mới</a>
                                </div>
                            </div>
                            <div class="ltn__checkout-single-content-info">

                                <h6>Thông tin cá nhân</h6>
                                <div class="row">
                                    <div class="col-md-6">

                                        <input type="text" name="ltn__name" placeholder="Họ và tên"
                                            value="{{ $defaultAddress->full_name }}" readonly>
                                    </div>
                                    <div class="col-md-6">

                                        <input type="text" name="ltn__phone" placeholder="Số điện thoại"
                                            value="{{ $defaultAddress->phone }}" readonly>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-md-6">
                                        <h6>Địa chỉ</h6>
                                        <div class="input-item">
                                            <input type="text" name="ltn__address" placeholder="Số nhà và tên đường"
                                                value="{{ $defaultAddress->address }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <h6>Tỉnh / Thành phố</h6>
                                        <div class="input-item">
                                            <input type="text" name="ltn__city" placeholder="Tỉnh / Thành phố"
                                                value="{{ $defaultAddress->city }}" readonly>
                                        </div>
                                    </div>
                                </div>



                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="ltn__checkout-payment-method mt-50">
                        <h4 class="title-2">Phương thức thanh toán</h4>
                        <form action="{{ route('checkout.placeOrder') }}" method="POST">
                            @csrf
                            <div id="checkout_payment">
                                <input type="hidden" name="address_id" value="{{ $defaultAddress->id }}">

                                <div class="card">
                                    <h5 class="ltn__card-title">
                                        <input type="radio" name="payment_method" value="cash" id="payment_cod" checked>
                                        <label for="payment_cod">Thanh toán khi nhận hàng</label>
                                    </h5>
                                </div>


                                <div class="card">
                                    <h5 class="collapsed ltn__card-title">
                                        <input type="radio" name="payment_method" value="paypal" id="payment_paypal">
                                        <label for="payment_paypal">PayPal</label>
                                    </h5>
                                </div>


                                <div class="card">
                                    <h5 class="collapsed ltn__card-title">
                                        <input type="radio" name="payment_method" value="momo" id="payment_momo">
                                        <label for="payment_momo">Momo</label>
                                    </h5>
                                </div>
                            </div>
                        <button class="btn theme-btn-1 btn-effect-1 text-uppercase" type="submit" id="order_button_cash">Đặt hàng</button>
                        <div id="paypal-button-container"></div>

                        </form>

                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="shoping-cart-total mt-50">
                        <h4 class="title-2">Tổng giỏ hàng</h4>
                        <table class="table">
                            <tbody>
                                @foreach ($cartItems as $item)
                                    <tr>
                                        <td>{{ $item->productVariant->product->name }} <strong>×
                                                {{ $item->quantity }}</strong></td>
                                        <td>{{ number_format($item->productVariant->price * $item->quantity) }}đ</td>
                                    </tr>
                                @endforeach

                                <tr>
                                    <td>Phí vận chuyển</td>
                                    <td>{{ number_format($shippingFee) }}đ</td>
                                </tr>

                                <tr>
                                    <td><strong>Tổng tiền</strong></td>
                                    <td><strong id="total_price" data-amount="{{ $total }}">{{ number_format($total) }} VNĐ</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
  

@endsection


{{-- bất kỳ file nào cũng cần phải có @extends --}}
