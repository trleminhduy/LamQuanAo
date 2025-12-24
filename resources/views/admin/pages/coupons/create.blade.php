@extends('layouts.admin')

@section('title', 'Thêm mã giảm giá')

@section('content')
    <div class="right_col" role="main">
        <div class="page-title">
            <div class="title_left">
                <h3>Thêm mã giảm giá mới</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_content">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                        <form action="{{ route('coupons.store') }}" method="POST" class="form-horizontal">
                            @csrf

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Mã coupon <span class="required">*</span></label>
                                <div class="col-md-6">
                                    <input type="text" name="code" class="form-control" required>
                                    
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Loại giảm giá <span class="required">*</span></label>
                                <div class="col-md-6">
                                    <label class="radio-inline">
                                        <input type="radio" name="discount_type" value="percent" checked> Phần trăm (%)
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="discount_type" value="amount"> Số tiền (VNĐ)
                                    </label>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Giá trị giảm <span class="required">*</span></label>
                                <div class="col-md-6">
                                    <input type="number" name="discount_value" class="form-control" step="0.01"
                                        min="0"  required>
                                    
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Ngày bắt đầu <span class="required">*</span></label>
                                <div class="col-md-6">
                                    <input type="date" name="start_date" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Ngày kết thúc <span class="required">*</span></label>
                                <div class="col-md-6">
                                    <input type="date" name="end_date" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Trạng thái</label>
                                <div class="col-md-6">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="status" value="1" checked> Kích hoạt ngay
                                    </label>
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <div class="form-group row">
                                <div class="col-md-6 offset-md-3">
                                    <a href="{{ route('coupons.index') }}" class="btn btn-secondary">Hủy</a>
                                    <button type="submit" class="btn btn-success">Lưu mã giảm giá</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
