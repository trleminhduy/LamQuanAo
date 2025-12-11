@extends('layouts.admin')

@section('title', 'Dashboard')



@section('content')
    <!-- page content -->
    <div class="right_col" role="main">
        <!-- top tiles -->
        <div class="row" style="display: inline-block; width: 100%;">
            <div class="tile_count">
                <div class="col-md-2 col-sm-4  tile_stats_count">
                    <span class="count_top"><i class="fa fa-user"></i> Tổng số người dùng</span>
                    <div class="count">{{ $users->count() }}</div>

                </div>
                <div class="col-md-2 col-sm-4  tile_stats_count">
                    <span class="count_top"><i class="fa fa-bar-chart"></i> Tổng số lượng sản phẩm</span>
                    <div class="count">{{ $products->count() }}</div>

                </div>
                <div class="col-md-2 col-sm-4  tile_stats_count">
                    <span class="count_top"><i class="fa fa-shopping-cart"></i> Tổng số lượng đơn hàng</span>
                    <div class="count green">{{ $orders->count() }}</div>

                </div>
                <div class="col-md-2 col-sm-4  tile_stats_count" style="overflow: unset">
                    <span class="count_top"><i class="fa fa-money"></i> Tổng doanh thu</span>
                    <div class="count">{{ number_format($orders->sum('total_price'), 0, 0) }}
                        VNĐ
                    </div>

                </div>
            </div>
        </div>
        <!-- /top tiles -->



        <div class="row">
            <div class="col-md-4 col-sm-6  ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Doanh thu</h2>

                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <canvas id="revenueBarChart" 
                        data-labels = '@json($monthlyRevenues->pluck('month')->toArray())'
                        data-values = '@json($monthlyRevenues->pluck('revenue')->toArray())'>

                        </canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-4 ">
                <div class="x_panel tile fixed_height_10000 overflow_hidden">
                    <div class="x_title">
                        <h2>Danh mục</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <table class="" style="width:100%">
                            <tr>
                                <th style="width:37%;">
                                    <p>Top 5</p>
                                </th>
                                <th>
                                    <div class="col-lg-7 col-md-7 col-sm-7 ">
                                        <p class="">Danh mục</p>
                                    </div>
                                    <div class="col-lg-5 col-md-5 col-sm-5 ">
                                        <p class="">Sản phẩm</p>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <td>
                                    <canvas class="canvasDoughnutCategory" height="200" width="200"
                                        data-labels = '@json($categories->pluck('name'))'
                                        data-counts = '@json($categories->map(fn($category) => $category->products->count()))'
                                        style="margin: 15px 10px 10px 0"></canvas>
                                </td>
                                <td>
                                    <table class="tile_info">
                                        @foreach ($categories as $index => $category)
                                            <tr>
                                                <td>
                                                    <p><i class="fa fa-square"
                                                            style="color: {{ [
                                                                '#BDC3C7',
                                                                '#9B59B6',
                                                                '#E74C3C',
                                                                '#26B99A',
                                                                '#3498DB',
                                                                '#E67E22',
                                                                '#95A5A6',
                                                                '#8E44AD',
                                                                '#C0392B',
                                                                '#16A085',
                                                                '#2980B9',
                                                                '#F39C12',
                                                                '#7F8C8D',
                                                                '#2C3E50',
                                                                '#D35400',
                                                                '#27AE60',
                                                                '#34495E',
                                                                '#F1C40F',
                                                            ][$index % 18] }}"></i>{{ $category->name }}
                                                    </p>
                                                </td>
                                                <td>{{ $category->products->count() }} </td>
                                            </tr>
                                        @endforeach

                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-4  ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Sản phẩm bán chạy nhất </h2>

                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>

                                    <th>Tên sản phẩm</th>
                                    <th>Giá</th>
                                    <th>Số lượng đã bán</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topSellingProducts as $item)
                                    <tr>
                                        <th scope="row">{{ $item->id }}</th>

                                        <td>{{ $item->name }}</td>
                                        <td>{{ number_format($item->price, 0, ',', '.') }} đ</td>
                                        <td>{{ $item->total_sold ?? 0 }}</td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-4  ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Người dùng mới </h2>

                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên khách hàng</th>
                                    <th>Số điện thoại</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 0; $i < min(3, $users->count()); $i++)
                                    <tr>
                                        <td scope="row">{{ $users[$i]->id }}</td>
                                        <td>{{ $users[$i]->name }}</td>
                                        <td>{{ $users[$i]->phone_number }}</td>
                                        <td>
                                            @if ($users[$i]->status == 'banned')
                                                <span class="custom-badge badge badge-warning">Bị chặn</span>
                                            @elseif ($users[$i]->status == 'deleted')
                                                <span class="custom-badge badge badge-danger">Đã xoá</span>
                                            @elseif ($users[$i]->status == 'pending')
                                                <span class="custom-badge badge badge-primary">Đang chờ kích hoạt</span>
                                            @else
                                                <span class="custom-badge badge badge-success">Hoạt động</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endfor

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-4  ">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Đơn hàng mới </h2>

                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tên khách hàng</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày đặt hàng</th>
                                    <th>Xem chi tiết</th>

                                </tr>
                            </thead>
                            <tbody>

                                @for ($i = 0; $i < min(3, $orders->count()); $i++)
                                    <tr>
                                        <td scope="row">{{ $orders[$i]->id }}</td>
                                        <td>{{ $orders[$i]->shippingAddress->full_name }}</td>
                                        <td>{{ number_format($orders[$i]->total_price, 0, ',', '.') }} đ</td>
                                        <td>
                                            @if ($orders[$i]->status == 'pending')
                                                <span class="custom-badge badge badge-warning">Chờ xác nhận</span>
                                            @elseif ($orders[$i]->status == 'cancelled')
                                                <span class="custom-badge badge badge-danger">Đã huỷ</span>
                                            @elseif ($orders[$i]->status == 'processing')
                                                <span class="custom-badge badge badge-primary">Đang giao</span>
                                            @else
                                                <span class="custom-badge badge badge-success">Hoàn thành</span>
                                            @endif
                                        </td>
                                        <td>{{ $orders[$i]->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.orders-detail', ['id' => $orders[$i]->id]) }}"
                                                class="btn btn-primary" target="_blank">
                                                Xem chi tiết
                                            </a>
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>


        </div>


    </div>
    <!-- /page content -->
@endsection

{{-- bất kỳ file nào cũng cần phải có @extends --}}
