@extends('layouts.client')

@section('title', 'TÀI KHOẢN')
@section('breadcrumb', 'TÀI KHOẢN')


@section('content')
    <!-- WISHLIST AREA START -->
    <div class="liton__wishlist-area pb-70">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!-- PRODUCT TAB AREA START -->
                    <div class="ltn__product-tab-area">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="ltn__tab-menu-list mb-50">
                                        <div class="nav">
                                            <a class="active show" data-bs-toggle="tab" href="#liton_tab_dashboard">Trang
                                                thống kê <i class="fas fa-home"></i></a>
                                            <a data-bs-toggle="tab" href="#liton_tab_orders">Đơn hàng <i
                                                    class="fas fa-file-alt"></i></a>
                                            <a data-bs-toggle="tab" href="#liton_tab_address">Địa chỉ <i
                                                    class="fas fa-map-marker-alt"></i></a>
                                            <a data-bs-toggle="tab" href="#liton_tab_account">Thông tin tài khoản <i
                                                    class="fas fa-user"></i></a>
                                            <a data-bs-toggle="tab" href="#liton_tab_password">Đổi mật khẩu<i
                                                    class="fas fa-user"></i></a>
                                            <a href="login.html" style="color: red">Đăng xuất <i
                                                    class="fas fa-sign-out-alt"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class="tab-content">
                                        <div class="tab-pane fade active show" id="liton_tab_dashboard">
                                            <div class="ltn__myaccount-tab-content-inner">
                                                <p>Hello <strong>{{ $user->email }}</strong> (không phải
                                                    <strong>{{ $user->email }}</strong>?
                                                    <small style="color: red"><a href="{{ route('logout') }}">Đăng
                                                            xuất</a></small> )
                                                </p>
                                                <p>Xem các đơn hàng gần đây của bạn ở đây <span>và
                                                    </span> quản lý địa chỉ giao hàng
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="liton_tab_orders">
                                            <div class="ltn__myaccount-tab-content-inner">
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>Đơn hàng</th>
                                                                <th>Ngày đặt</th>
                                                                <th>Trạng thái</th>
                                                                <th>Tổng tiền</th>
                                                                <th>Hành động</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>1</td>
                                                                <td>Jun 22, 2019</td>
                                                                <td>Pending</td>
                                                                <td>$3000</td>
                                                                <td><a href="cart.html">View</a></td>
                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="liton_tab_address">
                                            <div class="ltn__myaccount-tab-content-inner">
                                                <p>The following addresses will be used on the checkout page by
                                                    default.</p>
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>Tên người nhận</th>
                                                                <th>Địa chỉ</th>
                                                                <th>Thành phố</th>
                                                                <th>Số điện thoại</th>
                                                                <th>Mặc định</th>
                                                                <th>Hành động</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($addresses as $address)
                                                                <tr>
                                                                    <td>{{ $address->full_name }}</td>
                                                                    <td>{{ $address->address }}</td>
                                                                    <td>{{ $address->city }}</td>
                                                                    <td>{{ $address->phone }}</td>
                                                                    <td>
                                                                        @if ($address->is_default)
                                                                            <span class="badge bg-success"> Mặc định </span>
                                                                        @else
                                                                            <form action="{{route('account.addresses.update',$address->id)}}" method="POST"
                                                                                class="d-inline">
                                                                                @csrf
                                                                                @method('PUT')
                                                                                <button
                                                                                    class="btn btn-effect-1 btn-warning">
                                                                                    Chọn </button>

                                                                            </form>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        <form action="{{route('account.addresses.delete',$address->id)}}" method="POST"
                                                                            class="d-inline">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="btn btn-danger"
                                                                                onclick="return confirm('Bạn có chắc chắn xoá địa chỉ hiện tại?')">Xoá</button>

                                                                        </form>


                                                                    </td>
                                                                </tr>
                                                            @endforeach

                                                        </tbody>
                                                    </table>

                                                </div>
                                                <button class="btn theme-btn-1  text-uppercase rounded-2 mt-2"
                                                    data-bs-toggle="modal" data-bs-target="#addAddressModal">TẠO ĐỊA CHỈ
                                                    MỚI</button>
                                            </div>

                                        </div>
                                        <!-- Button trigger modal -->

                                        <!-- Modal -->
                                        <div class="modal fade" id="addAddressModal" tabindex="-1"
                                            aria-labelledby="addAddressModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content" style="padding: 5px 10px">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="addAddressModalLabel">Thêm địa chỉ mới
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('account.addresses.add') }}" method="POST"
                                                            id="addAddressForm">
                                                            @csrf
                                                            <div class="mb-3">
                                                                <label for="full_name" class="form-label"> Họ và
                                                                    tên:</label>
                                                                <input type="text" class="form-control" name="full_name"
                                                                    id="full_name" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="address" class="form-label"> Địa chỉ:</label>
                                                                <input type="text" class="form-control" name="address"
                                                                    id="address" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="city" class="form-label"> Thành phố:</label>
                                                                <input type="text" class="form-control" name="city"
                                                                    id="city" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="phone" class="form-label"> Số điện
                                                                    thoại:</label>
                                                                <input type="text" class="form-control" name="phone"
                                                                    id="phone" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="default" class="form-label"> Đặt làm địa chỉ
                                                                    mặc định:</label>
                                                                <input type="checkbox" class="form-check-input"
                                                                    name="default" id="default">
                                                            </div>
                                                            <button type="submit"
                                                                class="btn theme-btn-1 btn-effect-1 text-uppercase"> LƯU
                                                                ĐỊA
                                                                CHỈ</button>
                                                        </form>

                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="liton_tab_account">
                                            <div class="ltn__myaccount-tab-content-inner">

                                                <div class="ltn__form-box">
                                                    <form action="{{ route('account.update') }}" method="POST"
                                                        id="update_account" enctype="multipart/form-data">

                                                        {{-- @method('PUT') --}}

                                                        <div class="row mb-50">
                                                            <div class="col-md-12 text-center mb-3">
                                                                <div class="profile-pic-container">
                                                                    <img src="{{ asset('storage/' . $user->avatar) }}"
                                                                        alt="Avatar" id="preview-image"
                                                                        class="profile-pic">
                                                                    <input type="file" name="avatar" id="avatar"
                                                                        accept="image/*" class="d-none">
                                                                </div>

                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="ltn__name">Họ và tên:</label>
                                                                <input type="text" name="ltn__name" id="ltn__name"
                                                                    value="{{ $user->name }}" required>
                                                            </div>


                                                            <div class="col-md-6">
                                                                <label for="ltn__phone_number">Số điện thoại:</label>
                                                                <input type="number" name="ltn__phone_number"
                                                                    id="ltn__phone_number"
                                                                    value="{{ $user->phone_number }}" required>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <label for="ltn__email">Email :</label>
                                                                <input type="text" name="ltn__email" id="ltn__email"
                                                                    value="{{ $user->email }}" readonly>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <label for="ltn__address">Địa chỉ</label>
                                                                <input type="text" name="ltn__address"
                                                                    id="ltn__address" value="{{ $user->address }}"
                                                                    required>
                                                            </div>

                                                        </div>

                                                        <div class="btn-wrapper">
                                                            <button type="submit"
                                                                class="btn theme-btn-1 btn-effect-1 text-uppercase">CẬP
                                                                NHẬT</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="liton_tab_password">
                                            < <div class="ltn__form-box">
                                                <form action="{{ route('account.password-change') }}" method="POST"
                                                    id="change-password-form">
                                                    <fieldset>

                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <label>Mật khẩu hiện tại: <span style="color:red"> (Để
                                                                        trống nếu không muốn đổi mật khẩu!) <span></label>
                                                                <input type="password" name="current_password" required>
                                                                <label>Mật khẩu mới: <span style="color:red"> (Để trống nếu
                                                                        không muốn đổi mật khẩu!)</label>
                                                                <input type="password" name="new_password" required>
                                                                <label>Nhập lại mật khẩu mới:</label>
                                                                <input type="password" name="confirm_new_password"
                                                                    autocomplete="new-password" required>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                    <div class="btn-wrapper">
                                                        <button type="submit"
                                                            class="btn theme-btn-1 btn-effect-1 text-uppercase">Đổi mật
                                                            khẩu</button>
                                                    </div>
                                                </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- PRODUCT TAB AREA END -->
        </div>
    </div>
    </div>
    </div>
    <!-- WISHLIST AREA START -->
@endsection


{{-- bất kỳ file nào cũng cần phải có @extends --}}
