@extends('layouts.admin')

@section('title', 'Quản lý liên hệ')



@section('content')
    <!-- page content -->
    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">

            <div class="page-title">
                <div class="title_left">
                    <h3>Quản lý liên hệ</h3>
                </div>


            </div>

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Quản lý liên hệ</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>

                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div class="row">
                                <div class="col-sm-3 mail_list_column" style="overflow-y: scroll; max-height: 500px;">
                                    <label class="badge bg-green"
                                        style="width: 100%; line-height: 2; font-size:16px;">Liên hệ khách hàng</label>
                                    @foreach ($contacts as $contact)
                                        <a href="javascript:void(0);" class="contact-item"
                                            data-name="{{ $contact->full_name }}" data-email="{{ $contact->email }}"
                                            data-message="{{ $contact->message }}" data-id="{{ $contact->id }}" data-is_reply="{{ $contact->is_reply ? '1' : '0' }}">
                                            <div class="mail-list">
                                                <div class="left">
                                                    <i class="fa fa-circle"
                                                        style="color:{{ $contact->is_reply ? 'green' : 'red' }}"></i>
                                                </div>
                                                <div class="right">
                                                    <h3 style="font-size:14px">{{ $contact->full_name }}
                                                        <small>{{ $contact->created_at->format('d/m/Y ') }}</small>
                                                    </h3>
                                                    <p>{{ Str::limit($contact->message, 50) }}</p>
                                                </div>
                                            </div>


                                        </a>
                                    @endforeach

                                </div>
                                <!-- /MAIL LIST -->

                                <!-- CONTENT MAIL -->
                                <div class="col-sm-9 mail_view" style="display:none">
                                    <div class="inbox-body">

                                        <div class="sender-info" style="border-bottom: 1px solid #ddd">
                                            <div class="row">
                                                <div class="col-md-12">

                                                    <span></span> Từ
                                                    <strong>tôi</strong>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="view-mail" >
                                            <p> </p>



                                            <div class="btn-group">
                                                <button id="compose" class="btn btn-sm btn-primary" type="button"><i
                                                        class="fa fa-reply"></i> Trả lời</button>

                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <!-- /CONTENT MAIL -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- compose -->
    <div class="compose col-md-6  ">
        <div class="compose-header">
            Phản hồi liên hệ
            <button type="button" class="close compose-close">
                <span>×</span>
            </button>
        </div>

        <div class="compose-body">
            <div id="alerts"></div>
            <textarea id="editor-contact" name="reply_message" class="form-control" style="min-height: 150px"></textarea>
        </div>

        <div class="compose-footer">
            <button class="send-reply-contact btn btn-sm btn-success" type="button">Gửi</button>
        </div>
    </div>
    <!-- /compose -->
    <!-- /page content -->
@endsection

{{-- bất kỳ file nào cũng cần phải có @extends --}}
