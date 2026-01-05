@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng')



@section('content')
    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Chi tiết hoá đơn</h3>
                </div>

                <div class="title_right">
                    <div class="col-md-5 col-sm-5   form-group pull-right top_search">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search for...">
                            <span class="input-group-btn">
                                <button class="btn btn-secondary" type="button">Go!</button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Chi tiết hoá đơn</h2>

                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <section class="content invoice">
                                <!-- title row -->
                                <div class="row">
                                    <div class="  invoice-header">
                                        <h1>
                                            <i class="fa fa-globe"></i> Hoá đơn.
                                            <small class="pull-right">Ngày:
                                                {{ $order->created_at->format('d/m/Y H:i') }}</small>
                                        </h1>
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- info row -->
                                <div class="row invoice-info">
                                    <!-- /.col -->
                                    <div class="col-sm-4 invoice-col">
                                        Từ
                                        <address>
                                            <strong>Shop Quần Áo</strong>
                                            <br>769/35/30, Phạm Thế Hiển
                                            <br>Chánh Hưng,TP.HCM
                                            <br>Số điện thoại: 0838567807
                                            <br>Email: trleminhduy@gmail.com
                                        </address>
                                    </div>
                                    <div class="col-sm-4 invoice-col">
                                        Đến
                                        <address>
                                            <strong>{{ $order->shippingAddress->full_name }}</strong>
                                            <br>{{ $order->shippingAddress->address }}
                                            <br>Số điện thoại: {{ $order->shippingAddress->phone }}
                                            <br>Thành phố: {{ $order->shippingAddress->city }}

                                        </address>
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-sm-4 invoice-col">
                                        <b>Order ID: {{ $order->id }}</b>

                                        <br>
                                        <b>Email người đặt:</b> {{ $order->user->email ?? 'N/A' }}
                                        <br>
                                        <b>Tài khoản đặt:</b> {{ $order->user->name ?? 'N/A' }}
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->

                                <!-- Table row -->
                                <div class="row">
                                    <div class="  table">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Ảnh</th>
                                                    <th>Sản phẩm</th>
                                                    <th>Giá </th>
                                                    <th>Số lượng</th>
                                                    <th>Thành tiền</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($order->items as $item)
                                                    <tr>
                                                        <td>
                                                            <img src="{{ $item->productVariant->product->image_url }}"
                                                                alt="{{ $item->productVariant->product->name }}"
                                                                width="80" height="80" style="object-fit: cover;">
                                                        </td>
                                                        <td>{{ $item->productVariant->product->name }}</td>
                                                        <td>{{ number_format($item->productVariant->price, 0, ',', '.') }}
                                                            đ</td>
                                                        <td>{{ $item->quantity }}</td>
                                                        <td>{{ number_format($item->productVariant->price * $item->quantity, 0, ',', '.') }}
                                                            đ</td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->

                                <div class="row">

                                    <div class="col-md-6">
                                        <p class="lead">Phương thức thanh toán:</p>
                                        @if ($order->payment && $order->payment->payment_method == 'momo')
                                            <img src="{{ asset('assets/admin/images/momo.png') }}" class="image-payment"
                                                alt="MoMo">
                                        @elseif($order->payment && $order->payment->payment_method == 'vnpay')
                                            <img src="{{ asset('assets/admin/images/vnpay.jpg') }}" class="image-payment"
                                                alt="VNPay">
                                        @elseif($order->payment && $order->payment->payment_method == 'paypal')
                                            <img src="{{ asset('assets/admin/images/paypal.png') }}" class="image-payment"
                                                alt="Paypal">
                                        @else
                                            <img src="{{ asset('assets/admin/images/cod.png') }}" class="image-payment"
                                                alt="COD">
                                        @endif


                                    </div>
                                    <!-- /.col -->
                                    <div class="col-md-6">
                                        <p class="lead">Tiền hàng</p>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <th style="width:50%">Tiền hàng:</th>
                                                        <td>{{ number_format($order->total_price - 30000, 0, ',', '.') }} đ
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Tiền ship</th>
                                                        <td>30.000 đ</td>
                                                    </tr>

                                                    <tr>
                                                        <th>Tổng cộng:</th>
                                                        <td>{{ number_format($order->total_price, 0, ',', '.') }} đ</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->

                                <div class="row no-print">
                                    <div>
                                        @if ($order->status != 'cancelled')
                                            <button class="btn btn-default" onclick="window.print();"><i
                                                    class="fa fa-print"></i> In hoá đơn</button>
                                            <button class="btn btn-success pull-right send-invoice-mail"
                                                data-id="{{ $order->id }}"><i class="fa fa-credit-send"></i> Gửi hoá đơn
                                            </button>

                                            {{-- Nút gửi GHN --}}
                                            @if (!$order->ghn_order_code && $order->status != 'cancelled')
                                                <button class="btn btn-primary pull-right send-to-ghn"
                                                    data-id="{{ $order->id }}" style="margin-right: 5px;">
                                                    <i class="fa fa-truck"></i> Gửi GHN
                                                </button>
                                            @elseif($order->ghn_order_code)
                                                <button class="btn btn-info pull-right" disabled style="margin-right: 5px;">
                                                    <i class="fa fa-check"></i> Đã gửi GHN: {{ $order->ghn_order_code }}
                                                </button>

                                                {{-- 
                                                <button class="btn btn-warning pull-right view-tracking"
                                                    data-id="{{ $order->id }}" style="margin-right: 5px;">
                                                    <i class="fa fa-map-marker"></i> Tracking
                                                </button>
                                                --}}
                                            @endif

                                            @if ($order->status == 'pending')
                                                <button class="btn btn-danger pull-right cancel-order "
                                                    data-id="{{ $order->id }}"><i class="fa fa-remove"> Huỷ đơn hàng</i>
                                                </button>
                                            @endif
                                        @else
                                            <button class="btn btn-danger " style="cursor: not-allowed;"><i
                                                    class="fa fa-info"></i> Đơn hàng đã huỷ</button>
                                        @endif
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /page content -->

    {{-- 
    <div class="modal fade" id="trackingModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-truck"></i> Tracking Đơn Hàng</h4>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body" id="tracking-content">
                    <div class="text-center">
                        <i class="fa fa-spinner fa-spin fa-3x"></i>
                        <p>Đang tải thông tin tracking</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    --}}

@endsection

{{-- bất kỳ file nào cũng cần phải có @extends --}}

{{-- 
<script>
(function() {
    'use strict';
    
    // Đợi cả window và jQuery load xong
    function initTracking() {
        if (typeof jQuery === 'undefined') {
            console.log('jQuery chưa load, đợi...');
            setTimeout(initTracking, 100);
            return;
        }
        
        console.log('jQuery loaded, init tracking...');
        
        jQuery(document).ready(function($) {
            console.log('Document ready, binding events...');
            
            // Handle tracking button click
            $(document).on('click', '.view-tracking', function(e) {
                e.preventDefault();
                var orderId = $(this).data('id');
                console.log('Tracking button clicked, Order ID:', orderId);
                
                $('#trackingModal').modal('show');
                
                $.ajax({
                    url: '/admin/api/orders/' + orderId + '/tracking',
                    method: 'GET',
                    success: function(response) {
                        console.log('Response received:', response);
                        
                        if (response.success) {
                            var html = '<div class="alert alert-info">';
                            html += '<h5><strong>Trạng thái:</strong> ' + (response.current_status || 'N/A') + '</h5>';
                            html += '<p><strong>Dự kiến giao:</strong> ' + (response.expected_delivery_time || 'Chưa có') + '</p>';
                            html += '</div><h5 class="mt-3">Lịch sử vận chuyển:</h5><div style="padding-left: 20px; border-left: 2px solid #ddd;">';
                            
                            if (response.log && response.log.length > 0) {
                                response.log.forEach(function(log, i) {
                                    html += '<div style="margin-bottom: 20px; position: relative;">';
                                    html += '<div style="position: absolute; left: -27px; width: 12px; height: 12px; border-radius: 50%; background: ' + (i === 0 ? '#28a745' : '#6c757d') + '; border: 2px solid white;"></div>';
                                    html += '<div style="padding-left: 15px;"><strong>' + (log.status || 'N/A') + '</strong><br>';
                                    html += '<small class="text-muted">' + (log.updated_date || '') + '</small><br>';
                                    if (log.location) html += '<span class="text-info">📍 ' + log.location + '</span>';
                                    html += '</div></div>';
                                });
                            } else {
                                html += '<p class="text-muted">Chưa có thông tin vận chuyển</p>';
                            }
                            
                            html += '</div>';
                            $('#tracking-content').html(html);
                        } else {
                            $('#tracking-content').html('<div class="alert alert-warning">' + response.message + '</div>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                        $('#tracking-content').html('<div class="alert alert-danger">Lỗi khi tải tracking: ' + error + '</div>');
                    }
                });
            });
        });
    }
    
    // Bắt đầu init
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTracking);
    } else {
        initTracking();
    }
})();
</script>
--}}
