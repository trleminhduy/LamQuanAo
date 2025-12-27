@extends('layouts.admin')

@section('title', 'Quản lý liên hệ')



@section('content')
    <!-- page content -->
    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">

            <div class="page-title">
                <div class="title_left">
                    <h3>Quản lý thông báo</h3>
                </div>


            </div>

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-9 col-sm-9 ">



                    <div class="" role="tabpanel" data-example-id="togglable-tabs">

                        <div id="myTabContent" class="tab-content">
                            <div role="tabpanel" class="tab-pane active " id="tab_content1" aria-labelledby="home-tab">

                                
                                <ul class="messages">
                                    @foreach ($notifications as $notification)
                                        <li>

                                            <div class="message_date">
                                                <p class="date text-info" style="font-size:20px">{{ \Carbon\Carbon::parse($notification->created_at)->format('d / m / Y') }}</p>
                                                
                                            </div>
                                            <div class="message_wrapper">
                                                <a href="{{ url('admin' . $notification->link) }}">
                                                    <h4 class="heading">{{ $notification->title }}</h4>
                                                </a>
                                                <blockquote class="message">{{ $notification->message }}</blockquote>
                                                <br />

                                            </div>
                                        </li>
                                    @endforeach


                                </ul>
                               


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- compose -->

        <!-- /compose -->
        <!-- /page content -->
    @endsection

    {{-- bất kỳ file nào cũng cần phải có @extends --}}
