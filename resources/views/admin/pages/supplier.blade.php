@extends('layouts.admin')

@section('title', 'Quản lý nhà cung cấp')

@section('content')
    <div class="right_col" role="main">
        <div class="page-title">
            <div class="title_left">
                <h3>Danh sách nhà cung cấp</h3>
            </div>
            <div class="title_right">
                <a href="{{ route('admin.supplier.add') }}" class="btn btn-success">
                    <i class="fa fa-plus"></i> Thêm nhà cung cấp mới
                </a>
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

                        <table id="datatable-buttons" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Tên</th>
                                    <th>Email</th>
                                    <th>Điện thoại</th>
                                    <th>Địa chỉ</th>
                                    <th>Mô tả</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($suppliers as $supplier)
                                    <tr id="supplier-row-{{ $supplier->id }}">
                                        <td><strong>{{ $supplier->name }}</strong></td>
                                        <td>{{ $supplier->email }}</td>
                                        <td>{{ $supplier->phone }}</td>
                                        <td>{{ $supplier->address }}</td>
                                        <td>{{ $supplier->description }}</td>
                                             <td>
                                            <a href="{{ route('admin.supplier.edit', $supplier->id) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                        <button class="btn btn-sm btn-danger btn-delete-supplier"
                                                data-id="{{ $supplier->id }}">
                                                <i class="fa fa-trash"></i>
                                            </button>
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
@endsection
