@extends('layouts.admin')

@section('title', 'Quản lý nhà cung cấp')



@section('content')
    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Tạo nhà cung cấp</h3>
                </div>

              
            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Thêm nhà cung cấp mới</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <br />
                            <form action="{{ route('admin.supplier.add') }}" id="add-supplier" method="POST" enctype="multipart/form-data"
                                class="form-horizontal form-label-left">
                                @csrf

                                <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align" for="supplier-name">Tên nhà cung cấp
                                        
                                        <span class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 ">
                                        <input type="text" id="supplier-name" name="name" required="required"
                                            class="form-control ">
                                    </div>
                                </div>

                                 <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align" for="supplier-email">Email nhà cung cấp
                                        
                                       
                                    </label>
                                    <div class="col-md-6 col-sm-6 ">
                                        <input type="text" id="supplier-email" name="email" required="required"
                                            class="form-control ">
                                    </div>
                                </div>
                                 <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align" for="supplier-phone">Số điện thoại nhà cung cấp
                                        
                                        <span class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 ">
                                        <input type="text" id="supplier-phone" name="phone" required="required"
                                            class="form-control ">
                                    </div>
                                </div>
                                 <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align" for="supplier-address">Địa chỉ nhà cung cấp
                                        
                                       
                                    </label>
                                    <div class="col-md-6 col-sm-6 ">
                                        <input type="text" id="supplier-address" name="address" required="required"
                                            class="form-control ">
                                    </div>
                                </div>
                                <div class="item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3 label-align"
                                        for="supplier-description">Mô tả
                                        
                                    </label>
                                    <div class="col-md-6 col-sm-6 ">
                                        <input type="text" id="supplier-description" name="description"
                                            required="" class="form-control">
                                    </div>

                             


                              
                                <div class="item form-group">
                                 

                                        {{-- <button class="btn btn-primary" type="reset">Reset</button> --}}
                                        <button type="submit" class="btn btn-success">Thêm nhà cung cấp</button>
                                    </div>
                                <
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
