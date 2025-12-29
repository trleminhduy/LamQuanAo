@extends('layouts.client')

@section('title', 'THANH TOÁN')
@section('breadcrumb', 'THANH TOÁN')

@section('content')
    <div class="ltn__checkout-area mb-105">
        <div class="container">
            <form action="{{ route('guest.checkout.placeOrder') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-lg-7">
                        <div class="ltn__checkout-single-content mt-50">
                            <h4 class="title-2">Thông tin người nhận</h4>
                            <div class="ltn__checkout-single-content-info">
                                <input type="text" name="full_name" placeholder="Họ tên *" required>
                                <input type="text" name="phone" placeholder="Số điện thoại *" pattern="0[0-9]{9}"
                                    title="Số điện thoại phải là 10 chữ số và bắt đầu bằng 0" maxlength="10" required>
                                <input type="email" name="email" placeholder="Email (không bắt buộc)">
                            </div>
                        </div>

                        <div class="ltn__checkout-single-content mt-50">
                            <h4 class="title-2">Địa chỉ giao hàng</h4>
                            <div class="ltn__checkout-single-content-info">
                                <select name="province" id="province" required>
                                    <option value="">Chọn tỉnh/thành *</option>
                                </select>

                                <div class="row">
                                    <div class="col-md-6">
                                        <select name="district" id="district" required disabled>
                                            <option value="">Chọn quận/huyện *</option>
                                        </select>
                                        <input type="hidden" name="district_id" id="district_id">
                                    </div>

                                    <div class="col-md-6">
                                        <select name="ward" id="ward" required disabled>
                                            <option value="">Chọn phường/xã *</option>
                                        </select>
                                        <input type="hidden" name="ward_code" id="ward_code">
                                    </div>
                                </div>

                                <input type="text" name="address" placeholder="Địa chỉ cụ thể *" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="ltn__checkout-payment-method mt-50">
                            <h4 class="title-2">Phương thức thanh toán</h4>

                            <div id="checkout_payment">
                                <div class="card">
                                    <h5 class="ltn__card-title">
                                        <input type="radio" name="payment_method" value="cod" id="cod" checked>
                                        <label for="cod">Thanh toán khi nhận hàng</label>
                                    </h5>
                                </div>

                                <div class="card">
                                    <h5 class="ltn__card-title">
                                        <input type="radio" name="payment_method" value="vnpay" id="vnpay">
                                        <label for="vnpay">VNPay</label>
                                    </h5>
                                </div>
                            </div>

                            <button type="submit" class="btn theme-btn-1 btn-effect-1 text-uppercase mt-3">Đặt
                                hàng</button>
                        </div>

                        <div class="shoping-cart-total mt-50">
                            <h4 class="title-2">Tổng đơn hàng</h4>
                            <table class="table">
                                <tbody>
                                    @foreach ($cartItems as $item)
                                        <tr>
                                            <td>{{ $item->productVariant->product->name }} <strong>×
                                                    {{ $item->quantity }}</strong></td>
                                            <td>{{ number_format($item->productVariant->price * $item->quantity, 0, ',', '.') }}đ
                                            </td>
                                        </tr>
                                    @endforeach

                                    <tr>
                                        <td>Tạm tính</td>
                                        <td><strong>{{ number_format($subTotal, 0, ',', '.') }}đ</strong></td>
                                    </tr>

                                    <tr>
                                        <td>Phí vận chuyển</td>
                                        <td><strong>{{ number_format($shippingFee, 0, ',', '.') }}đ</strong></td>
                                    </tr>

                                    <tr class="order-total">
                                        <td>Tổng cộng</td>
                                        <td><strong>{{ number_format($total, 0, ',', '.') }}đ</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const province = document.getElementById('province');
            const district = document.getElementById('district');
            const ward = document.getElementById('ward');
            const districtIdInput = document.getElementById('district_id');
            const wardCodeInput = document.getElementById('ward_code');

            // Load tỉnh
            fetch('/api/ghn/provinces')
                .then(res => res.json())
                .then(data => {
                    console.log('Provinces data:', data);
                    if (data.status || data.success) {
                        province.innerHTML = '<option value="">Chọn tỉnh/thành</option>';
                        data.data.forEach(p => {
                            province.innerHTML +=
                                `<option value="${p.ProvinceName}" data-id="${p.ProvinceID}">${p.ProvinceName}</option>`;
                        });
                    } else {
                        console.error('API error:', data);
                    }
                })
                .catch(err => console.error('Lỗi load tỉnh:', err));

            // Chọn tỉnh -> load quận
            province.addEventListener('change', function() {
                const provinceId = this.options[this.selectedIndex]?.dataset.id;

                district.innerHTML = '<option value="">Chọn quận/huyện</option>';
                district.disabled = false;
                ward.innerHTML = '<option value="">Chọn phường/xã</option>';
                ward.disabled = true;

                if (!provinceId) return;

                fetch('/api/ghn/districts', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            province_id: provinceId
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        console.log('Districts data:', data);
                        if (data.status || data.success) {
                            data.data.forEach(d => {
                                district.innerHTML +=
                                    `<option value="${d.DistrictName}" data-id="${d.DistrictID}">${d.DistrictName}</option>`;
                            });
                        }
                    })
                    .catch(err => console.error('Lỗi load quận:', err));
            });

            // Chọn quận -> load phường
            district.addEventListener('change', function() {
                const districtId = this.options[this.selectedIndex]?.dataset.id;
                districtIdInput.value = districtId;

                ward.innerHTML = '<option value="">Chọn phường/xã</option>';
                ward.disabled = false;

                if (!districtId) return;

                fetch('/api/ghn/wards', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            district_id: districtId
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        console.log('Wards data:', data);
                        if (data.status || data.success) {
                            data.data.forEach(w => {
                                ward.innerHTML +=
                                    `<option value="${w.WardName}" data-code="${w.WardCode}">${w.WardName}</option>`;
                            });
                        }
                    })
                    .catch(err => console.error('Lỗi load phường:', err));
            });

            // Chọn phường -> lưu ward_code
            ward.addEventListener('change', function() {
                const wardCode = this.options[this.selectedIndex]?.dataset.code;
                wardCodeInput.value = wardCode;
            });

            // Validate số điện thoại: Chặn chữ, chỉ cho số
            const phoneInput = document.getElementById('phone');
            phoneInput.addEventListener('input', function(e) {
                // Xóa tất cả ký tự không phải số
                this.value = this.value.replace(/[^0-9]/g, '');

                // Kiểm tra format ngay
                if (this.value.length === 10 && !this.value.startsWith('0')) {
                    this.setCustomValidity('Số điện thoại phải bắt đầu bằng 0');
                } else if (this.value.length > 0 && this.value.length < 10) {
                    this.setCustomValidity('Số điện thoại phải đủ 10 số');
                } else {
                    this.setCustomValidity('');
                }
            });
        });
    </script>
@endsection
