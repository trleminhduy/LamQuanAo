@extends('layouts.admin')

@section('title', 'Tất cả biến thể sản phẩm')

@section('content')
    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Tất cả biến thể sản phẩm</h3>
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Danh sách tất cả biến thể <small>({{ $variants->count() }} biến thể)</small></h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="card-box table-responsive">
                                        <table id="datatable-buttons" class="table table-striped table-bordered"
                                            style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>STT</th>
                                                    <th>Sản phẩm</th>
                                                    <th>Hình ảnh</th>
                                                    <th>Size</th>
                                                    <th>Màu sắc</th>
                                                    <th>Giá</th>
                                                    <th>Tồn kho</th>
                                                    <th>Hành động</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($variants as $index => $variant)
                                                    <tr id="variant-row-{{ $variant->id }}">
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>
                                                            <a href="{{ route('admin.variants.index', $variant->product_id) }}" 
                                                               class="text-primary">
                                                                {{ $variant->product->name }}
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <img src="{{ $variant->product->image_url }}" 
                                                                 alt="{{ $variant->product->name }}"
                                                                 width="50" class="image-product">
                                                        </td>
                                                        <td>{{ $variant->size->name }}</td>
                                                        <td>
                                                            <span class="badge" style="background-color: {{ $variant->color->code }}; color: white; padding: 5px 10px;">
                                                                {{ $variant->color->name }}
                                                            </span>
                                                        </td>
                                                        <td>{{ number_format($variant->price, 0, ',', '.') }} VNĐ</td>
                                                        <td>{{ $variant->stock }}</td>
                                                        <td>
                                                            <a class="btn btn-sm btn-warning btn-edit-variant-global" 
                                                               data-id="{{ $variant->id }}"
                                                               data-price="{{ $variant->price }}"
                                                               data-stock="{{ $variant->stock }}"
                                                               data-product="{{ $variant->product->name }}"
                                                               data-size="{{ $variant->size->name }}"
                                                               data-color="{{ $variant->color->name }}">
                                                                <i class="fa fa-pencil"></i> Sửa
                                                            </a>
                                                            <a class="btn btn-sm btn-danger btn-delete-variant-global" 
                                                               data-id="{{ $variant->id }}"
                                                               data-product="{{ $variant->product->name }}">
                                                                <i class="fa fa-trash"></i> Xóa
                                                            </a>
                                                        </td>
                                                    </tr>
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

    <!-- Modal Sửa Biến Thể -->
    <div class="modal fade" id="editVariantGlobalModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa biến thể</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="edit-variant-global-form">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="variant_id" id="edit-variant-global-id">
                        
                        <div class="form-group">
                            <label>Sản phẩm</label>
                            <input type="text" class="form-control" id="edit-variant-global-product" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label>Size - Màu</label>
                            <input type="text" class="form-control" id="edit-variant-global-size-color" readonly>
                        </div>

                        <div class="form-group">
                            <label>Giá <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="price" id="edit-variant-global-price" 
                                   min="0" required>
                        </div>

                        <div class="form-group">
                            <label>Tồn kho <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="stock" id="edit-variant-global-stock" 
                                   min="0" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /page content -->
@endsection
