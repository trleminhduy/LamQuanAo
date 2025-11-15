@extends('layouts.client')

@section('title', 'DỊCH VỤ')
@section('breadcrumb', 'DỊCH VỤ')

@section('content')

<!-- Dịch vụ -->
<div class="service-container">
    <div class="service-intro">
        <div class="service-intro-text">
            <h6>// DỊCH VỤ UY TÍN</h6>
            <h1>Chúng tôi Uy tín & Chuyên nghiệp</h1>
            <p>Cửa hàng thời trang của chúng tôi cam kết mang đến cho bạn những xu hướng mới nhất, phong
                cách đa dạng và chất lượng vượt trội với mức giá hợp lý.</p>
            <p>Chúng tôi tin rằng thời trang không chỉ là quần áo mà còn là cách bạn thể hiện bản thân. Từ
                trang phục thường ngày đến các set đồ sang trọng, bộ sưu tập của chúng tôi được chọn lọc kỹ
                lưỡng để phù hợp với mọi dịp và phong cách sống.</p>
            
            <div class="service-highlights">
                <ul>
                    <li>✓ Giao hàng nhanh chóng & miễn phí</li>
                    <li>✓ Đội ngũ tư vấn chuyên nghiệp</li>
                    <li>✓ Chất liệu cao cấp</li>
                    <li>✓ Sản phẩm đa dạng</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Các dịch vụ -->
    <div class="services-grid">
        <div class="service-item">
            <div class="service-icon">🚚</div>
            <h3>Giao hàng miễn phí</h3>
            <p>Miễn phí giao hàng cho đơn từ 500.000đ. Giao hàng nhanh trong 2-3 ngày.</p>
        </div>

        <div class="service-item">
            <div class="service-icon">💳</div>
            <h3>Thanh toán an toàn</h3>
            <p>Hỗ trợ nhiều hình thức thanh toán: COD, chuyển khoản, ví điện tử.</p>
        </div>

        <div class="service-item">
            <div class="service-icon">🔄</div>
            <h3>Đổi trả dễ dàng</h3>
            <p>Chính sách đổi trả trong 7 ngày nếu sản phẩm không vừa ý.</p>
        </div>

        <div class="service-item">
            <div class="service-icon">👔</div>
            <h3>Chất lượng đảm bảo</h3>
            <p>Sản phẩm chính hãng, chất liệu cao cấp, kiểm tra kỹ trước khi giao.</p>
        </div>

        <div class="service-item">
            <div class="service-icon">💬</div>
            <h3>Tư vấn nhiệt tình</h3>
            <p>Đội ngũ tư vấn viên sẵn sàng hỗ trợ 24/7 qua hotline và chat.</p>
        </div>

        <div class="service-item">
            <div class="service-icon">🎁</div>
            <h3>Ưu đãi hấp dẫn</h3>
            <p>Chương trình khuyến mãi, tích điểm thành viên, voucher giảm giá.</p>
        </div>
    </div>

    <!-- Cam kết -->
    <div class="service-commitment">
        <h2>Cam kết của chúng tôi</h2>
        <div class="commitment-grid">
            <div class="commitment-item">
                <h4>🌟 Chất lượng</h4>
                <p>100% sản phẩm chính hãng, nhập khẩu từ các thương hiệu uy tín</p>
            </div>
            <div class="commitment-item">
                <h4>⚡ Nhanh chóng</h4>
                <p>Xử lý đơn hàng trong 24h, giao hàng nhanh trên toàn quốc</p>
            </div>
            <div class="commitment-item">
                <h4>❤️ Tận tâm</h4>
                <p>Luôn lắng nghe và đáp ứng mọi nhu cầu của khách hàng</p>
            </div>
        </div>
    </div>
</div>

@endsection
