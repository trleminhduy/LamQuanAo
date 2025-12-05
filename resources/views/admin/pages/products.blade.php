@extends('layouts.admin')

@section('title', 'Quản lý sản phẩm')



@section('content')
    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Danh sách sản phẩm</h3>
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
                            <h2>Danh sách sản phẩm</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="card-box table-responsive">
                                        <p class="text-muted font-13 m-b-30">

                                        </p>
                                        <table id="datatable-buttons" class="table table-striped table-bordered"
                                            style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>Hình ảnh</th>
                                                    <th>Tên sản phẩm</th>
                                                    <th>Nhà cung cấp</th>
                                                    <th>Danh mục</th>
                                                    <th>Slug</th>
                                                    <th>Mô tả</th>
                                                    <th>Giá</th>
                                                    <th>Số lượng</th>
                                                    <th>Trạng thái</th>
                                                    <th>Hành động</th>
                                                    <th> Hành động</th>
                                                </tr>
                                            </thead>


                                            <tbody>
                                                @foreach ($products as $product)
                                                    <tr id="product-row-{{ $product->id }}">
                                                        <td>
                                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                                                width="50" class="image-product">
                                                        </td>
                                                        <td>{{ $product->name }}</td>
                                                        {{-- <td>{{ $product->product->name }}</td> --}}
                                                        <td>{{ $product->supplier->name ?? 'N/A' }}</td>
                                                        <td>{{ $product->category->name }}</td>
                                                        <td>{{ $product->slug }}</td>
                                                        <td>{{ $product->description }}</td>
                                                        <td>{{ number_format($product->price, 0, ',', '.') }} VNĐ</td>
                                                        <td>{{ $product->stock }} <small>({{ $product->variants->count() }} biến thể)</small></td>
                                                        <td>{{ $product->status == 'in_stock' ? 'Còn hàng' : 'Hết hàng' }}
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('admin.variants.index', $product->id) }}" class="btn btn-app btn-info">
                                                                <i class="fa fa-tags"> </i>Biến thể
                                                            </a>
                                                            <a class="btn btn-app btn-update-product" data-toggle="modal"
                                                                data-target="#modalUpdate-{{ $product->id }}">
                                                                <i class="fa fa-pencil"> </i>Sửa
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <a class="btn btn-app btn-delete-product"
                                                                data-id="{{ $product->id }}">
                                                                <i class="fa fa-trash"> </i>Xóa
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <!-- Modal -->
                                                    <div class="modal fade" id="modalUpdate-{{ $product->id }}"
                                                        tabindex="-1" aria-labelledby="productModalLabel"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h1 class="modal-title fs-5" id="productModalLabel">
                                                                        Chỉnh sửa sản phẩm</h1>
                                                                    <button type="button" class="btn-close"
                                                                        data-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form id="update-product" method="POST"
                                                                        enctype="multipart/form-data"
                                                                        class="form-horizontal form-label-left">
                                                                        @csrf
                                                                        <div class="item form-group">
                                                                            <label
                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                for="product-name">Tên sản phẩm
                                                                                <span class="required">*</span>
                                                                            </label>
                                                                            <div class="col-md-6 col-sm-6 ">
                                                                                <input type="text" id="product-name"
                                                                                    name="name" required="required"
                                                                                    class="form-control"
                                                                                    value="{{ $product->name }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="item form-group">
                                                                            <label
                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                for="product-name">Nhà cung cấp
                                                                                <span class="">*</span>
                                                                            </label>
                                                                            <div class="col-md-6 col-sm-6 ">
                                                                                <select name="supplier_id" id="supplier_id"
                                                                                    class="form-control">
                                                                                    <option value="">-- Chưa xác định
                                                                                        --</option>
                                                                                    @foreach ($suppliers as $supplier)
                                                                                        <option value="{{ $supplier->id }}"
                                                                                            {{ $product->supplier_id == $supplier->id ? 'selected' : '' }}>
                                                                                            {{ $supplier->name }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="item form-group">
                                                                            <label
                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                for="product-category">Danh mục

                                                                                <span class="required">*</span>
                                                                            </label>
                                                                            <div class="col-md-6 col-sm-6 ">
                                                                                <select name="category_id" id="category_id"
                                                                                    class="form-control" required>
                                                                                    @foreach ($categories as $category)
                                                                                        <option value="{{ $category->id }}"
                                                                                            {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                                                            {{ $category->name }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="item form-group">
                                                                            <label
                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                for="product-description">Mô tả

                                                                                <span class="required">*</span>
                                                                            </label>
                                                                            <div class="col-md-6 col-sm-6 ">
                                                                                <input type="text"
                                                                                    id="product-description"
                                                                                    name="description"
                                                                                    required="required" class="form-control"
                                                                                    value="{{ $product->description }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="item form-group">
                                                                            <label
                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                for="product-price">Giá tiền

                                                                                <span class="required">*</span>
                                                                            </label>
                                                                            <div class="col-md-6 col-sm-6 ">
                                                                                <input type="number" id="product-price"
                                                                                    name="price"
                                                                                    required="required"
                                                                                    class="form-control"
                                                                                    value="{{ intval($product->price) }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="item form-group">
                                                                            <label
                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                for="product-stock">Số lượng

                                                                                <span class="required">*</span>
                                                                            </label>
                                                                            <div class="col-md-6 col-sm-6 ">
                                                                                <input type="number" id="product-stock"
                                                                                    name="stock"
                                                                                    required="required"
                                                                                    class="form-control"
                                                                                    value="{{ $product->stock }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="item form-group">
                                                                            <label
                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                for="product-slug">Slug

                                                                                <span class="required">*</span>
                                                                            </label>
                                                                            <div class="col-md-6 col-sm-6 ">
                                                                                <input type="text" id="product-slug"
                                                                                    name="product-slug"
                                                                                    required="required"
                                                                                    class="form-control"
                                                                                    value="{{ $product->slug }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="item form-group">

                                                                            <label
                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                for="product-images">Hình
                                                                                ảnh</label>
                                                                            <div class="col-md-6 col-sm-6 ">
                                                                                <label class="custom-file-upload"
                                                                                    for="product-images-{{ $product->id }}">
                                                                                    Chọn ảnh
                                                                                </label>
                                                                                <input type="file" name="images[]"
                                                                                    class="product-images"
                                                                                    id="product-images-{{ $product->id }}"
                                                                                    data-id="{{ $product->id }}"
                                                                                    accept="image/*" multiple>

                                                                                <div id="image-preview-container-{{ $product->id }}"
                                                                                    class="image-preview-container image-preview-listproduct"
                                                                                    data-id="{{ $product->id }}">
                                                                                    @foreach ($product->images as $image)
                                                                                        <img src="{{ asset('storage/' . $image->image) }}"
                                                                                            alt="Ảnh sản phẩm"
                                                                                            style="width: 150px; height: 100px; margin: 5px; border-radius:5px;">
                                                                                    @endforeach

                                                                                </div>

                                                                            </div>
                                                                        </div>



                                                                    </form>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-dismiss="modal">Đóng</button>
                                                                    <button type="button"
                                                                        class="btn btn-primary btn-update-submit-product"
                                                                        data-id="{{ $product->id }}">Lưu</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /page content -->
@endsection

{{-- bất kỳ file nào cũng cần phải có @extends --}}
