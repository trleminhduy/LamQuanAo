@extends('layouts.admin')

@section('title', 'Sửa mã giảm giá')

@section('content')
    <div class="right_col" role="main">
        <div class="page-title">
            <div class="title_left">
                <h3>Sửa mã giảm giá: {{ $coupon->code }}</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_content">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                @foreach($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                        <form action="{{ route('coupons.update', $coupon->id) }}" method="POST" class="form-horizontal">
                            @csrf
                            @method('PUT')

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Mã coupon <span class="required">*</span></label>
                                <div class="col-md-6">
                                    <input type="text" name="code" class="form-control" value="{{ $coupon->code }}" required>
                                    
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Loại giảm giá <span class="required">*</span></label>
                                <div class="col-md-6">
                                    <label class="radio-inline">
                                        <input type="radio" name="discount_type" value="percent" {{ $coupon->discount_type == 'percent' ? 'checked' : '' }}> Phần trăm (%)
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="discount_type" value="amount" {{ $coupon->discount_type == 'amount' ? 'checked' : '' }}> Số tiền (VNĐ)
                                    </label>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Giá trị giảm <span class="required">*</span></label>
                                <div class="col-md-6">
                                    <input type="number" name="discount_value" class="form-control" step="0.01" min="0" value="{{ $coupon->discount_value }}" required>
                                    
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Ngày bắt đầu <span class="required">*</span></label>
                                <div class="col-md-6">
                                    <input type="date" name="start_date" class="form-control" value="{{ $coupon->start_date }}" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Ngày kết thúc <span class="required">*</span></label>
                                <div class="col-md-6">
                                    <input type="date" name="end_date" class="form-control" value="{{ $coupon->end_date }}" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Trạng thái</label>
                                <div class="col-md-6">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="status" value="1" {{ $coupon->status ? 'checked' : '' }}> Kích hoạt
                                    </label>
                                </div>
                            </div>

                            <div class="ln_solid"></div>

                            <div class="form-group row">
                                <div class="col-md-6 offset-md-3">
                                    <a href="{{ route('coupons.index') }}" class="btn btn-secondary">Hủy</a>
                                    <button type="submit" class="btn btn-success">Cập nhật</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection