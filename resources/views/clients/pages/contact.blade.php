@extends('layouts.client')

@section('title', 'LIÊN HỆ')
@section('breadcrumb', 'LIÊN HỆ')


@section('content')
 <!-- CONTACT MESSAGE AREA START -->
        <div class="ltn__contact-message-area ">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ltn__form-box contact-form-box box-shadow white-bg">
                            <h4 class="title-2">Liên hệ</h4>
                            <form id="contact-form" action="{{ route('contact') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-item input-item-name ltn__custom-icon">
                                            <input type="text" name="name" placeholder="Họ và tên" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-item input-item-phone ltn__custom-icon">
                                            <input type="text" name="phone" placeholder="Số điện thoại" required >
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-item input-item-email ltn__custom-icon">
                                            <input type="email" name="email" placeholder="Đia chỉ email" required>
                                        </div>
                                    </div>
                                   
                                    </div>
                                </div>
                                <div class="input-item input-item-textarea ltn__custom-icon">
                                    <textarea name="message" placeholder="Nhập tin nhắn" required></textarea>
                                </div>
                               
                                <div class="btn-wrapper mt-0">
                                    <button class="btn theme-btn-1  text-uppercase" type="submit">Gửi</button>
                                </div>
                                <p class="form-messege mb-0 mt-20"></p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- CONTACT MESSAGE AREA END -->

@endsection


{{-- bất kỳ file nào cũng cần phải có @extends --}}