@extends('layouts.admin')

@section('title', 'Quản lý đơn hàng')



@section('content')
    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Quản lý đơn hàng</h3>
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
                            <h2>Danh sách đơn hàng</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="card-box table-responsive">
                                        <p class="text-muted font-13 m-b-30">

                                        </p>
                                        <table id="datatable-buttons" class="table table-striped table-bordered"
                                            style="width:100%; text-align: center;">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Tài khoản</th>
                                                    <th>Thông tin người đặt</th>
                                                    <th>Tổng tiền</th>
                                                    <th>Trạng thái đơn hàng</th>
                                                    <th>Trạng thái thanh toán</th>
                                                    <th>Chi tiết hoá đơn</th>
                                                    <th>Hành động</th>

                                                </tr>
                                            </thead>


                                            <tbody>
                                                @foreach ($orders as $order)
                                                    <tr>
                                                        <td>
                                                            {{ $order->id }}
                                                        </td>
                                                        <td>{{ $order->user->name ?? 'N/A' }}</td>

                                                        <td><a href="javascript:void(0)" data-toggle="modal"
                                                                data-target="#addressShippingModal-{{ $order->id }}">{{ $order->shippingAddress->full_name }}</a>
                                                        </td>
                                                        <td>{{ number_format($order->total_price, 0, ',', '.') }} VNĐ</td>

                                                        <td class="order-status">
                                                            @if ($order->status == 'pending')
                                                                <span class="custom-badge badge-warning">Chờ xác nhận</span>
                                                            @elseif ($order->status == 'processing')
                                                                <span class="custom-badge badge-info">Đang tiến hành giao
                                                                    hàng</span>
                                                            @elseif ($order->status == 'completed')
                                                                <span class="custom-badge badge-success">Đơn đã hoàn tất</span>
                                                            @elseif ($order->status == 'delivered')
                                                                <span class="custom-badge badge-success">Hoàn thành giao</span>
                                                            @elseif ($order->status == 'cancelled')
                                                                <span class="custom-badge badge-danger">Đã hủy</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($order->payment->status == 'pending')
                                                                <span class="custom-badge badge-secondary">Chờ thanh
                                                                    toán</span>
                                                            @else
                                                                <span class="custom-badge badge-success">Đã thanh
                                                                    toán</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-info" data-toggle="modal"
                                                                data-target="#orderItemsModal-{{ $order->id }}">
                                                                Xem</button>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group">

                                                                <button type="button"
                                                                    class="btn btn-primary dropdown-toggle dropdown-toggle-split"
                                                                    data-toggle="dropdown" aria-haspopup="true"
                                                                    aria-expanded="false">

                                                                </button>
                                                                <div class="dropdown-menu">
                                                                    @if ($order->status == 'pending')
                                                                        <a class="dropdown-item confirm-order"
                                                                            href="javascript:void(0)"
                                                                            data-id="{{ $order->id }}">Xác nhận</a>
                                                                    @endif

                                                                    <a class="dropdown-item" target="_blank"
                                                                        href="{{ route('admin.orders-detail', ['id' => $order->id]) }}">Xem
                                                                        chi tiết
                                                                    </a>

                                                                </div>
                                                            </div>
                                                        </td>

                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                        @foreach ($orders as $order)
                                            <!-- Modal Order Item -->

                                            <div class="modal fade" id="orderItemsModal-{{ $order->id }}" tabindex="-1"
                                                aria-labelledby="orderItemsModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h1 class="modal-title fs-5" id="orderItemsModalLabel">
                                                                Chi tiết hoá đơn</h1>
                                                            <button type="button" class="btn-close" data-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <table class="table table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th>#</th>
                                                                        <th>Tên sản phẩm</th>
                                                                        <th>Số lượng</th>
                                                                        <th>Đơn giá</th>
                                                                        <th>Thành tiền</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @php $index = 1; @endphp

                                                                    @foreach ($order->items as $item)
                                                                        <tr>
                                                                            <td>
                                                                                {{ $index++ }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $item->productVariant->product->name }}
                                                                            </td>
                                                                            <td>
                                                                                {{ $item->quantity }}
                                                                            </td>
                                                                            <td>
                                                                                {{ number_format($item->price, 0, ',', '.') }}
                                                                                VNĐ
                                                                            </td>
                                                                            <td>
                                                                                {{ number_format($item->quantity * $item->price, 0, ',', '.') }}
                                                                                VNĐ
                                                                            </td>

                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Modal Address --}}
                                            <div class="modal fade" id="addressShippingModal-{{ $order->id }}"
                                                tabindex="-1" aria-labelledby="addressShippingModalLabel"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h1 class="modal-title fs-5" id="addressShippingModalLabel">
                                                                Thông tin giao hàng</h1>
                                                            <button type="button" class="btn-close" data-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Người nhận: {{ $order->shippingAddress->full_name }}</p>
                                                            <p>Số điện thoại: {{ $order->shippingAddress->phone }}</p>
                                                            <p>Địa chỉ: {{ $order->shippingAddress->address }}</p>
                                                            <p>Thành phố: {{ $order->shippingAddress->city }}</p>

                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
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
