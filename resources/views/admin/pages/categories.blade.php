@extends('layouts.admin')

@section('title', 'Quản lý người dùng')



@section('content')
    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Danh sách danh mục</h3>
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
                            <h2>Danh sách danh mục</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="card-box table-responsive">
                                        <p class="text-muted font-13 m-b-30">
                                            The Buttons extension for DataTables provides a common set of options, API
                                            methods and styling to display buttons on a page that will interact with a
                                            DataTable. The core library provides the based framework upon which plug-ins can
                                            built.
                                        </p>
                                        <table id="datatable-buttons" class="table table-striped table-bordered"
                                            style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>Hình ảnh</th>
                                                    <th>Tên danh mục</th>
                                                    <th>Slug</th>
                                                    <th>Mô tả</th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                            </thead>


                                            <tbody>
                                                @foreach ($categories as $category)
                                                    <tr id="category-row-{{ $category->id }}">
                                                        <td>
                                                            <img src="{{ asset('storage/' . $category->image) }}"
                                                                alt="{{ $category->name }}" width="50"
                                                                class="image-category">
                                                        </td>
                                                        <td>{{ $category->name }}</td>
                                                        <td>{{ $category->slug }}</td>
                                                        <td>{{ $category->description }}</td>
                                                        <td>
                                                            <a class="btn btn-app btn-update-category" data-toggle="modal"
                                                                data-target="#modalUpdate-{{ $category->id }}">
                                                                <i class="fa fa-pencil"> </i>Sửa
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <a class="btn btn-app btn-delete-category" data-id="{{ $category->id }}">
                                                                <i class="fa fa-trash"> </i>Xóa
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <!-- Modal -->
                                                    <div class="modal fade" id="modalUpdate-{{ $category->id }}"
                                                        tabindex="-1" aria-labelledby="categoryModalLabel"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h1 class="modal-title fs-5" id="categoryModalLabel">
                                                                        Chỉnh sửa danh mục</h1>
                                                                    <button type="button" class="btn-close"
                                                                        data-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form id="update-category" method="POST"
                                                                        enctype="multipart/form-data"
                                                                        class="form-horizontal form-label-left">
                                                                        @csrf
                                                                        <div class="item form-group">
                                                                            <label
                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                for="category-name">Tên danh
                                                                                mục
                                                                                <span class="required">*</span>
                                                                            </label>
                                                                            <div class="col-md-6 col-sm-6 ">
                                                                                <input type="text" id="category-name"
                                                                                    name="name" required="required"
                                                                                    class="form-control"
                                                                                    value="{{ $category->name }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="item form-group">
                                                                            <label
                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                for="category-description">Mô tả

                                                                                <span class="required">*</span>
                                                                            </label>
                                                                            <div class="col-md-6 col-sm-6 ">
                                                                                <input type="text"
                                                                                    id="category-description"
                                                                                    name="category-description"
                                                                                    required="required" class="form-control"
                                                                                    value="{{ $category->description }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="item form-group">
                                                                            <label
                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                for="category-slug">Slug

                                                                                <span class="required">*</span>
                                                                            </label>
                                                                            <div class="col-md-6 col-sm-6 ">
                                                                                <input type="text" id="category-slug"
                                                                                    name="category-slug" required="required"
                                                                                    class="form-control"
                                                                                    value="{{ $category->slug }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="item form-group">

                                                                            <label
                                                                                class="col-form-label col-md-3 col-sm-3 label-align"
                                                                                for="category-image">Hình
                                                                                ảnh</label>
                                                                            <div class="col-md-6 col-sm-6 ">
                                                                                <img src="{{ asset('storage/' . $category->image) }}"
                                                                                    alt="{{ $category->name }}"
                                                                                    id="image-preview"
                                                                                    class="image-preview">

                                                                                <label class="custom-file-upload"
                                                                                    for="category-image-{{ $category->id }}">
                                                                                    Chọn ảnh
                                                                                </label>
                                                                                <input type="file" name="image"
                                                                                    class="category-image"
                                                                                    id="category-image-{{ $category->id }}"
                                                                                    data-id="{{ $category->id }}"
                                                                                    accept="image/*">

                                                                            </div>
                                                                        </div>



                                                                    </form>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-dismiss="modal">Đóng</button>
                                                                    <button type="button"
                                                                        class="btn btn-primary btn-update-submit-category"
                                                                        data-id="{{ $category->id }}">Lưu</button>
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
