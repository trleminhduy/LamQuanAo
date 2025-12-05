@extends('layouts.admin')

@section('title', 'Quản lý biến thể - ' . $product->name)

@section('content')
    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Quản lý biến thể: {{ $product->name }}</h3>
                </div>
                <div class="title_right">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Quay lại danh sách sản phẩm
                    </a>
                </div>
            </div>

            <div class="clearfix"></div>

            <!-- Thông tin sản phẩm -->
            <div class="row">
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Thông tin sản phẩm</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <p><strong>Giá gốc:</strong> {{ number_format($product->price, 0, ',', '.') }} VNĐ</p>
                            <p><strong>Tổng tồn kho:</strong> <span id="total-stock">{{ $product->stock }}</span> sản phẩm</p>
                            <p><strong>Danh mục:</strong> {{ $product->category->name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form thêm biến thể -->
            <div class="row">
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Thêm biến thể mới</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <form id="add-variant-form" class="form-horizontal form-label-left">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Size <span class="required">*</span></label>
                                            <select name="size_id" class="form-control" required>
                                                <option value="">-- Chọn size --</option>
                                                @foreach ($sizes as $size)
                                                    <option value="{{ $size->id }}">{{ $size->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Màu sắc <span class="required">*</span></label>
                                            <select name="color_id" class="form-control" required>
                                                <option value="">-- Chọn màu --</option>
                                                @foreach ($colors as $color)
                                                    <option value="{{ $color->id }}">{{ $color->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Giá <span class="required">*</span></label>
                                            <input type="number" name="price" class="form-control" 
                                                   value="{{ $product->price }}" required min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Tồn kho <span class="required">*</span></label>
                                            <input type="number" name="stock" class="form-control" 
                                                   value="0" required min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="submit" class="btn btn-success btn-block">
                                                <i class="fa fa-plus"></i> Thêm
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danh sách biến thể -->
            <div class="row">
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Danh sách biến thể ({{ $product->variants->count() }})</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Size</th>
                                        <th>Màu sắc</th>
                                        <th>Giá</th>
                                        <th>Tồn kho</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody id="variants-table-body">
                                    @forelse ($product->variants as $index => $variant)
                                        <tr id="variant-row-{{ $variant->id }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $variant->size->name }}</td>
                                            <td>{{ $variant->color->name }}</td>
                                            <td class="variant-price">{{ number_format($variant->price, 0, ',', '.') }} VNĐ</td>
                                            <td class="variant-stock">{{ $variant->stock }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-primary btn-edit-variant" 
                                                        data-id="{{ $variant->id }}"
                                                        data-price="{{ $variant->price }}"
                                                        data-stock="{{ $variant->stock }}"
                                                        data-size="{{ $variant->size->name }}"
                                                        data-color="{{ $variant->color->name }}">
                                                    <i class="fa fa-pencil"></i> Sửa
                                                </button>
                                                <button class="btn btn-sm btn-danger btn-delete-variant" 
                                                        data-id="{{ $variant->id }}">
                                                    <i class="fa fa-trash"></i> Xóa
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="no-variants-row">
                                            <td colspan="6" class="text-center">Chưa có biến thể nào</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal sửa biến thể -->
    <div class="modal fade" id="editVariantModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa biến thể</h5>
                    <button type="button" class="btn-close" data-dismiss="modal"></button>
                </div>
                <form id="edit-variant-form">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="variant_id" id="edit-variant-id">
                        <div class="form-group">
                            <label>Size - Màu</label>
                            <input type="text" class="form-control" id="edit-variant-info" readonly>
                        </div>
                        <div class="form-group">
                            <label>Giá <span class="required">*</span></label>
                            <input type="number" name="price" id="edit-price" class="form-control" required min="0">
                        </div>
                        <div class="form-group">
                            <label>Tồn kho <span class="required">*</span></label>
                            <input type="number" name="stock" id="edit-stock" class="form-control" required min="0">
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
@endsection
