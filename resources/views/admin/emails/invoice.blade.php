<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Hoá đơn mua hàng</title>
</head>

<body>
    {{-- Hoá đơn mua hàng --}}
    <h2>Hoá đơn mua hàng #{{ $order->id }}</h2>
    <p>Xin chào {{ $order->user->name }},</p>
    <p>Cảm ơn bạn đã mua hàng tại cửa hàng của chúng tôi. Dưới đây là chi tiết hoá đơn của bạn:</p>
    <h3>Thông tin đơn hàng:</h3>
    <ul>
        <li>Mã đơn hàng: {{ $order->id }}</li>
        <li>Ngày đặt hàng: {{ $order->created_at->format('d/m/Y H:i') }}</li>
        <li>Tổng tiền: {{ number_format($order->total_price, 0, ',', '.') }} VND</li>
        <li style="color:red">Phương thức thanh toán: {{ $order->payment->payment_method }}</li>
    </ul>
    <h3>Chi tiết sản phẩm:</h3>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                
                <th>Sản phẩm</th>
                <th>Số lượng</th>
                <th>Giá</th>
                <th> Phí ship</th>
                <th>Tổng cộng</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                @if ($item->productVariant && $item->productVariant->product)
                    <tr>
            
                        <td>{{ $item->productVariant->product->name }} ({{ $item->productVariant->size->name ?? '' }} -
                            {{ $item->productVariant->color->name ?? '' }})</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->price, 0, ',', '.') }} VND</td>
                        <td> 30.000 VND</td>
                        <td>{{ number_format($item->price * $item->quantity +30000, 0, ',', '.') }} VND</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
    <h3>Địa chỉ giao hàng:</h3>
    <p>
        {{ $order->shippingAddress->full_name }}<br>
        {{ $order->shippingAddress->address }}<br>
        {{ $order->shippingAddress->city }}<br>
        SĐT: {{ $order->shippingAddress->phone }}
    </p>


</body>

</html>
