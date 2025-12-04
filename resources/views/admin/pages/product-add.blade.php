@extends('layouts.admin')

@section('title', 'Quản lý sản phẩm')



@section('content')
    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Tạo sản phẩm</h3>
                </div>

                <div class="title_right">
                    <div class="col-md-5 col-sm-5  form-group pull-right top_search">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Tìm tên...">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button">Tìm</button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Thêm sản phẩm mới</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <br />
                            <form action="{{ route('admin.product.add') }}" id="add-product" method="POST" enctype="multipart/form-data"
                                class="form-horizontal form-label-left">
                                @csrf

                                <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align" for="product-name">Tên sản
                                        phẩm
                                        <span class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 ">
                                        <input type="text" id="product-name" name="name" required="required"
                                            class="form-control ">
                                    </div>
                                </div>

                                {{-- Chọn danh mục --}}
                                 <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align" for="product-category">Danh mục
                                        <span class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 ">
                                        <select name="category_id" id="category_id" class="form-control" required>
                                            <option value="">-- Chọn danh mục --</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Chọn nhà cung cấp --}}
                                 <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align" for="product-supplier">Nhà cung cấp
                                    </label>
                                    <div class="col-md-6 col-sm-6 ">
                                        <select name="supplier_id" id="supplier_id" class="form-control">
                                            <option value="">-- Chưa xác định --</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align"
                                        for="product-description">Mô tả
                                        <span class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 ">
                                        <input type="text" id="product-description" name="description"
                                            required="required" class="form-control">
                                    </div>
                                </div>

                                {{-- Giá --}}
                                 <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align" for="product-price">Giá tiền
                                        <span class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 ">
                                        <input type="text" id="product-price" name="price" required="required"
                                            class="form-control ">
                                    </div>
                                </div>

                                {{-- Số lượng --}}
                                    <div class="item form-group">
                                        <label class="col-form-label col-md-3 col-sm-3 label-align" for="product-stock">Số lượng
                                            <span class="required">*</span>
                                        </label>
                                        <div class="col-md-6 col-sm-6 ">
                                            <input type="number" id="product-stock" name="stock" required="required"
                                                class="form-control ">
                                        </div>
                                    </div>

                                <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align" for="product-images">Hình
                                        ảnh</label>
                                    <div class="col-md-6 col-sm-6 ">

                                        <label class="custom-file-upload" for="product-images"> Chọn ảnh

                                        </label>
                                        <input type="file" name="images[]" id="product-images" accept="image/*" multiple>
                                        <div id="image-preview-container"></div>

                                    </div>
                                </div>

                                <div class="ln_solid"></div>
                                <div class="item form-group">
                                    <div class="col-md-6 col-sm-6 offset-md-3">

                                        {{-- <button class="btn btn-primary" type="reset">Reset</button> --}}
                                        <button type="submit" class="btn btn-success">Thêm sản phẩm</button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /page content -->
@endsection

{{-- bất kỳ file nào cũng cần phải có @extends --}}
