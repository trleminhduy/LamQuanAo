@extends('layouts.admin')

@section('title', 'Sửa mã giảm giá')

@section('content')
    <div class="right_col" role="main">
        <div class="page-title">
            <div class="title_left">
                <h3>Sửa nhà cung cấp: {{ $supplier->name }}</h3>
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

                        <form action="{{ route('admin.supplier.update', $supplier->id) }}" method="POST"
                            class="form-horizontal">

                            <input type="hidden" name="id" value="{{ $supplier->id }}">
                            @csrf
                            @method('POST')

                            <div class="form-group row">
                                <label class="col-md-1 col-form-label">Tên nhà cung cấp <span
                                        class="required">*</span></label>
                                <div class="col-md-6">
                                    <input type="text" name="name" class="form-control" value="{{ $supplier->name }}"
                                        required>

                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="col-md-1 col-form-label">Email <span class="required">*</span></label>
                                <div class="col-md-6">
                                    <input type="text" name="email" class="form-control" value="{{ $supplier->email }}"
                                        required>

                                </div>
                            </div>
                                <div class="form-group row">
                                    <label class="col-md-1 col-form-label">Điện thoại <span
                                            class="required">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" name="phone" class="form-control"
                                            value="{{ $supplier->phone }}" required>

                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-1 col-form-label">Địa chỉ <span class="required">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" name="address" class="form-control"
                                            value="{{ $supplier->address }}" required>

                                    </div>
                                </div>
                                <div class="form-group row ">
                                    <label class="col-md-1 col-form-label">Mô tả <span class="required">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" name="description" class="form-control"
                                            value="{{ $supplier->description }}" required>

                                    </div>
                                </div>
                            </div>



                            <div class="ln_solid"></div>

                            <div class="form-group row">
                                <div class="col-md-6 offset-md-3">
                                    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">Hủy</a>
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
