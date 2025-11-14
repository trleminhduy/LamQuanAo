@extends('layouts.client')

@section('title', 'FAQ')
@section('breadcrumb', 'CÂU HỎI THƯỜNG GẶP')

@section('content')
<div class="ltn__faq-area mb-100 mt-50">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="section-title text-center mb-50">
                    <h2>Câu hỏi thường gặp</h2>
                    <p>Tìm câu trả lời cho những thắc mắc của bạn</p>
                </div>
                
                <div class="accordion" id="faqAccordion">
                    <!-- Câu hỏi 1 -->
                    <div class="card">
                        <h6 class="ltn__card-title" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true">
                            Làm thế nào để đặt hàng?
                        </h6>
                        <div id="faq1" class="collapse show" data-parent="#faqAccordion">
                            <div class="card-body">
                                <p>Bạn có thể đặt hàng bằng cách: Chọn sản phẩm → Thêm vào giỏ hàng → Điền thông tin giao hàng → Xác nhận đơn hàng. Chúng tôi sẽ liên hệ xác nhận trong vòng 24h.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Câu hỏi 2 -->
                    <div class="card">
                        <h6 class="collapsed ltn__card-title" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false">
                            Chính sách đổi trả như thế nào?
                        </h6>
                        <div id="faq2" class="collapse" data-parent="#faqAccordion">
                            <div class="card-body">
                                <p>Bạn có thể đổi/trả hàng trong vòng 7 ngày nếu sản phẩm còn nguyên tem mác, chưa qua sử dụng. Vui lòng liên hệ với chúng tôi để được hướng dẫn chi tiết.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Câu hỏi 3 -->
                    <div class="card">
                        <h6 class="collapsed ltn__card-title" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false">
                            Thời gian giao hàng bao lâu?
                        </h6>
                        <div id="faq3" class="collapse" data-parent="#faqAccordion">
                            <div class="card-body">
                                <p>Đơn hàng sẽ được giao trong vòng 2-5 ngày làm việc tùy theo khu vực. Nội thành TPHCM thường nhận hàng trong 1-2 ngày.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Câu hỏi 4 -->
                    <div class="card">
                        <h6 class="collapsed ltn__card-title" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false">
                            Các phương thức thanh toán được hỗ trợ?
                        </h6>
                        <div id="faq4" class="collapse" data-parent="#faqAccordion">
                            <div class="card-body">
                                <p>Chúng tôi hỗ trợ thanh toán COD (ship COD), chuyển khoản ngân hàng, và ví điện tử (Momo, ZaloPay).</p>
                            </div>
                        </div>
                    </div>

                    <!-- Câu hỏi 5 -->
                    <div class="card">
                        <h6 class="collapsed ltn__card-title" data-bs-toggle="collapse" data-bs-target="#faq5" aria-expanded="false">
                            Làm sao để kiểm tra size phù hợp?
                        </h6>
                        <div id="faq5" class="collapse" data-parent="#faqAccordion">
                            <div class="card-body">
                                <p>Mỗi sản phẩm đều có bảng size chi tiết. Bạn có thể tham khảo hoặc liên hệ với chúng tôi để được tư vấn size phù hợp nhất.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Câu hỏi 6 -->
                    <div class="card">
                        <h6 class="collapsed ltn__card-title" data-bs-toggle="collapse" data-bs-target="#faq6" aria-expanded="false">
                            Thông tin cá nhân có được bảo mật không?
                        </h6>
                        <div id="faq6" class="collapse" data-parent="#faqAccordion">
                            <div class="card-body">
                                <p>Chúng tôi cam kết bảo mật 100% thông tin cá nhân của khách hàng và chỉ sử dụng cho mục đích giao dịch.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Support -->
                <div class="need-support text-center mt-80">
                    <h3>Vẫn cần hỗ trợ?</h3>
                    <p class="mb-30">Liên hệ với chúng tôi 24/7</p>
                    <a href="contact.html" class="theme-btn-1 btn mb-20">Liên hệ ngay</a>
                    <h4><i class="fas fa-phone"></i> 0838567807</h4>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection