@extends('layouts.client')

@section('title', 'TÀI KHOẢN')
@section('breadcrumb', 'TÀI KHOẢN')

@section('content')
<div class="account-container">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="account-sidebar">
                    <ul class="account-menu">
                        <li>
                            <a href="javascript:void(0)" class="tab-link active" data-tab="dashboard">
                                <i class="fas fa-home"></i> Trang thống kê
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" class="tab-link" data-tab="orders">
                                <i class="fas fa-file-alt"></i> Đơn hàng
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" class="tab-link" data-tab="addresses">
                                <i class="fas fa-map-marker-alt"></i> Địa chỉ
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" class="tab-link" data-tab="account">
                                <i class="fas fa-user"></i> Thông tin tài khoản
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" class="tab-link" data-tab="password">
                                <i class="fas fa-lock"></i> Đổi mật khẩu
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('logout') }}" style="color: red">
                                <i class="fas fa-sign-out-alt"></i> Đăng xuất
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="account-content">
                    <!-- Dashboard Tab -->
                    <div class="tab-pane active" id="dashboard">
                        <h4>Xin chào, {{ $user->name }}!</h4>
                        <p>Email: <strong>{{ $user->email }}</strong></p>
                        <p>Từ trang này, bạn có thể xem các đơn hàng gần đây, quản lý địa chỉ giao hàng và chỉnh sửa thông tin tài khoản.</p>
                        <p>Bạn không phải là {{ $user->email }}? <a href="{{ route('logout') }}" style="color: red;">Đăng xuất</a></p>
                    </div>

                    <!-- Orders Tab -->
                    <div class="tab-pane" id="orders">
                        <h4>Đơn hàng của tôi</h4>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Mã đơn hàng</th>
                                        <th>Ngày đặt</th>
                                        <th>Trạng thái</th>
                                        <th>Tổng tiền</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 30px;">
                                            Chưa có đơn hàng nào
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Addresses Tab -->
                    <div class="tab-pane" id="addresses">
                        <h4>Địa chỉ giao hàng</h4>
                        <p>Quản lý các địa chỉ giao hàng của bạn.</p>
                        
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
                                    @forelse($addresses as $address)
                                        <tr>
                                            <td>{{ $address->full_name }}</td>
                                            <td>{{ $address->address }}</td>
                                            <td>{{ $address->city }}</td>
                                            <td>{{ $address->phone }}</td>
                                            <td>
                                                @if($address->is_default)
                                                    <span class="badge bg-success">Mặc định</span>
                                                @else
                                                    <form action="{{ route('account.addresses.update', $address->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-warning">Chọn</button>
                                                    </form>
                                                @endif
                                            </td>
                                            <td>
                                                <form action="{{ route('account.addresses.delete', $address->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa địa chỉ này?')">Xóa</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" style="text-align: center; padding: 30px;">
                                                Chưa có địa chỉ nào. Vui lòng thêm địa chỉ mới.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <button class="btn theme-btn-1 text-uppercase" onclick="openModal('addAddressModal')">
                            <i class="fas fa-plus"></i> Thêm địa chỉ mới
                        </button>
                    </div>

                    <!-- Account Info Tab -->
                    <div class="tab-pane" id="account">
                        <h4>Thông tin tài khoản</h4>
                        <div class="ltn__form-box">
                            <form action="{{ route('account.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12 text-center mb-4">
                                        <div class="profile-pic-container">
                                            <img src="{{ asset('storage/' . $user->avatar) }}" 
                                                 alt="Avatar" 
                                                 id="preview-image" 
                                                 class="profile-pic"
                                                 onclick="document.getElementById('avatar').click()">
                                            <input type="file" name="avatar" id="avatar" accept="image/*" style="display: none;">
                                        </div>
                                        <p style="margin-top: 10px; font-size: 12px; color: #666;">Click vào ảnh để thay đổi</p>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="ltn__name">Họ và tên: *</label>
                                        <input type="text" name="ltn__name" id="ltn__name" value="{{ $user->name }}" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="ltn__phone_number">Số điện thoại:</label>
                                        <input type="text" name="ltn__phone_number" id="ltn__phone_number" value="{{ $user->phone_number }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label for="ltn__email">Email: *</label>
                                        <input type="email" name="ltn__email" id="ltn__email" value="{{ $user->email }}" readonly style="background: #f5f5f5;">
                                    </div>

                                    <div class="col-md-6">
                                        <label for="ltn__address">Địa chỉ:</label>
                                        <input type="text" name="ltn__address" id="ltn__address" value="{{ $user->address }}">
                                    </div>
                                </div>

                                <div class="btn-wrapper">
                                    <button type="submit" class="btn theme-btn-1 btn-effect-1 text-uppercase">
                                        Cập nhật thông tin
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Password Tab -->
                    <div class="tab-pane" id="password">
                        <h4>Đổi mật khẩu</h4>
                        <div class="ltn__form-box">
                            <form action="{{ route('account.password-change') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="current_password">Mật khẩu hiện tại: *</label>
                                        <input type="password" name="current_password" id="current_password" placeholder="Nhập mật khẩu hiện tại" required>
                                    </div>

                                    <div class="col-md-12">
                                        <label for="new_password">Mật khẩu mới: *</label>
                                        <input type="password" name="new_password" id="new_password" placeholder="Nhập mật khẩu mới" required>
                                    </div>

                                    <div class="col-md-12">
                                        <label for="confirm_new_password">Xác nhận mật khẩu mới: *</label>
                                        <input type="password" name="confirm_new_password" id="confirm_new_password" placeholder="Nhập lại mật khẩu mới" required>
                                    </div>
                                </div>

                                <div class="btn-wrapper">
                                    <button type="submit" class="btn theme-btn-1 btn-effect-1 text-uppercase">
                                        Đổi mật khẩu
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm địa chỉ -->
<div class="modal" id="addAddressModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Thêm địa chỉ mới</h5>
                <button type="button" class="btn-close" onclick="closeModal('addAddressModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form action="{{ route('account.addresses.add') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Họ và tên: *</label>
                        <input type="text" class="form-control" name="full_name" id="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Địa chỉ: *</label>
                        <input type="text" class="form-control" name="address" id="address" required>
                    </div>
                    <div class="mb-3">
                        <label for="city" class="form-label">Thành phố: *</label>
                        <input type="text" class="form-control" name="city" id="city" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Số điện thoại: *</label>
                        <input type="text" class="form-control" name="phone" id="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="default">
                            <input type="checkbox" name="default" id="default"> Đặt làm địa chỉ mặc định
                        </label>
                    </div>
                    <button type="submit" class="btn theme-btn-1 btn-effect-1 text-uppercase">Lưu địa chỉ</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Tab switching
    document.addEventListener('DOMContentLoaded', function() {
        const tabLinks = document.querySelectorAll('.tab-link');
        const tabPanes = document.querySelectorAll('.tab-pane');

        tabLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all links and panes
                tabLinks.forEach(l => l.classList.remove('active'));
                tabPanes.forEach(p => p.classList.remove('active'));

                // Add active class to clicked link
                this.classList.add('active');

                // Show corresponding tab pane
                const tabId = this.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });

        // Avatar preview
        const avatarInput = document.getElementById('avatar');
        if (avatarInput) {
            avatarInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('preview-image').src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
    });

    // Modal functions
    function openModal(modalId) {
        document.getElementById(modalId).classList.add('show');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('show');
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            e.target.classList.remove('show');
        }
    });
</script>

@endsection
