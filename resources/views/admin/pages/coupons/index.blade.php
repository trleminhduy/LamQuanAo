@extends('layouts.admin')

@section('title', 'Quản lý mã giảm giá')

@section('content')
    <div class="right_col" role="main">
        <div class="page-title">
            <div class="title_left">
                <h3>Danh sách mã giảm giá</h3>
            </div>
            <div class="title_right">
                <a href="{{ route('coupons.create') }}" class="btn btn-success">
                    <i class="fa fa-plus"></i> Thêm mã mới
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
                                    <th>Mã</th>
                                    <th>Loại</th>
                                    <th>Giá trị</th>
                                    <th>Thời gian</th>
                                    <th>Trạng thái</th>
                                    <th>Lượt sử dụng</th>
                                    <th>Hành động</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($coupons as $coupon)
                                    <tr id="coupon-row-{{ $coupon->id }}">
                                        <td><strong>{{ $coupon->code }}</strong></td>
                                        <td>
                                            @if ($coupon->discount_type == 'percent')
                                                <span class="label label-info">%</span>
                                            @else
                                                <span class="label label-warning">VNĐ</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $coupon->discount_type == 'percent' ? $coupon->discount_value . '%' : number_format($coupon->discount_value) . 'đ' }}
                                        </td>
                                        <td>{{ date('d/m/Y', strtotime($coupon->start_date)) }} -
                                            {{ date('d/m/Y', strtotime($coupon->end_date)) }}</td>
                                        <td>
                                            <span class="label label-{{ $coupon->status ? 'success' : 'default' }}">
                                                {{ $coupon->status ? 'Hoạt động' : 'Tắt' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($coupon->usage_limit)
                                                {{ $coupon->used_count }} / {{ $coupon->usage_limit }}
                                                @if ($coupon->used_count >= $coupon->usage_limit)
                                                    <span class="badge badge-danger">Hết lượt</span>
                                                @endif
                                            @else
                                                Không giới hạn
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('coupons.edit', $coupon->id) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <button class="btn btn-sm btn-danger btn-delete-coupon"
                                                data-id="{{ $coupon->id }}">
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
