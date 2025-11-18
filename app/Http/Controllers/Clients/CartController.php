<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Hiển thị giỏ hàng
    public function index()
    {
        $cartItems = CartItem::with(['productVariant.product.firstImage', 'productVariant.size', 'productVariant.color'])
            ->where('user_id', Auth::id())
            ->get();

        // Tính tổng tiền
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item->productVariant->price * $item->quantity;
        }

        return view('clients.pages.cart', compact('cartItems', 'total'));
    }

    // Thêm sản phẩm vào giỏ hàng
    public function add(Request $request)
    {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $variant = ProductVariant::find($request->product_variant_id);

        // Kiểm tra tồn kho
        if ($variant->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không đủ số lượng trong kho!'
            ]);
        }

        // Nếu CHƯA đăng nhập - lưu vào SESSION
        if (!Auth::check()) {
            // Lấy giỏ hàng từ session (nếu chưa có thì tạo mảng rỗng)
            $cart = session()->get('cart', []);
            
            $variantId = $request->product_variant_id;
            
            // Nếu sản phẩm đã có trong giỏ - cộng thêm số lượng
            if (isset($cart[$variantId])) {
                $newQuantity = $cart[$variantId]['quantity'] + $request->quantity;
                
                if ($newQuantity > $variant->stock) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Vượt quá số lượng tồn kho!'
                    ]);
                }
                
                $cart[$variantId]['quantity'] = $newQuantity;
            } else {
                // Thêm sản phẩm mới vào giỏ
                $cart[$variantId] = [
                    'product_variant_id' => $variantId,
                    'quantity' => $request->quantity
                ];
            }
            
            // Lưu lại vào session
            session()->put('cart', $cart);
            
            // Đếm tổng số lượng
            $cartCount = array_sum(array_column($cart, 'quantity'));
            
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm sản phẩm vào giỏ hàng!',
                'cartCount' => $cartCount
            ]);
        }

        // Nếu ĐÃ đăng nhập - lưu vào DATABASE
        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('product_variant_id', $request->product_variant_id)
            ->first();

        if ($cartItem) {
            // Cộng thêm số lượng
            $newQuantity = $cartItem->quantity + $request->quantity;
            
            if ($newQuantity > $variant->stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vượt quá số lượng tồn kho!'
                ]);
            }
            
            $cartItem->quantity = $newQuantity;
            $cartItem->save();
        } else {
            // Tạo mới
            CartItem::create([
                'user_id' => Auth::id(),
                'product_variant_id' => $request->product_variant_id,
                'quantity' => $request->quantity
            ]);
        }

        // Đếm tổng số sản phẩm trong giỏ
        $cartCount = CartItem::where('user_id', Auth::id())->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm sản phẩm vào giỏ hàng!',
            'cartCount' => $cartCount
        ]);
    }

    // Cập nhật số lượng
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = CartItem::where('user_id', Auth::id())->findOrFail($id);
        $variant = $cartItem->productVariant;

        if ($request->quantity > $variant->stock) {
            return response()->json([
                'success' => false,
                'message' => 'Vượt quá số lượng tồn kho!'
            ]);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        // Tính lại tổng tiền
        $itemTotal = $variant->price * $cartItem->quantity;
        
        $cartItems = CartItem::with('productVariant')->where('user_id', Auth::id())->get();
        $grandTotal = 0;
        foreach ($cartItems as $item) {
            $grandTotal += $item->productVariant->price * $item->quantity;
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật số lượng!',
            'itemTotal' => number_format($itemTotal, 0, ',', '.'),
            'grandTotal' => number_format($grandTotal, 0, ',', '.')
        ]);
    }

    // Xóa sản phẩm khỏi giỏ hàng
    public function remove($id)
    {
        // Nếu CHƯA đăng nhập: xóa trong SESSION theo variantId
        if (!Auth::check()) {
            $cart = session()->get('cart', []);

            if (!isset($cart[$id])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sản phẩm không tồn tại trong giỏ hàng!'
                ], 404);
            }

            unset($cart[$id]);
            session()->put('cart', $cart);

            $cartCount = array_sum(array_column($cart, 'quantity'));

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa sản phẩm khỏi giỏ hàng!',
                'cartCount' => $cartCount
            ]);
        }

        // ĐÃ đăng nhập: xóa trong DATABASE theo cart_item id của user hiện tại
        $cartItem = CartItem::where('user_id', Auth::id())->findOrFail($id);
        $cartItem->delete();

        $cartCount = CartItem::where('user_id', Auth::id())->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng!',
            'cartCount' => $cartCount
        ]);
    }

    // Xóa toàn bộ giỏ hàng
    public function clear()
    {
        // Nếu CHƯA đăng nhập: xóa SESSION cart
        if (!Auth::check()) {
            session()->forget('cart');
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa toàn bộ giỏ hàng!'
            ]);
        }

        // ĐÃ đăng nhập: xóa DB cart items
        CartItem::where('user_id', Auth::id())->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa toàn bộ giỏ hàng!'
        ]);
    }

    // Mini cart - Lấy thông tin giỏ hàng cho header
    public function miniCart()
    {
        // Nếu CHƯA đăng nhập - đọc từ SESSION
        if (!Auth::check()) {
            $cart = session()->get('cart', []);
            
            if (empty($cart)) {
                return response()->json([
                    'count' => 0,
                    'items' => [],
                    'total' => 0
                ]);
            }
            
            // Lấy thông tin variant (tối đa 5 sản phẩm)
            $variantIds = array_keys($cart);
            $variants = ProductVariant::with(['product.firstImage', 'size', 'color'])
                ->whereIn('id', array_slice($variantIds, 0, 5))
                ->get()
                ->keyBy('id');
            
            $items = [];
            $totalPrice = 0;
            $totalQuantity = 0;
            
            foreach ($cart as $variantId => $cartData) {
                if (!isset($variants[$variantId])) continue;
                
                $variant = $variants[$variantId];
                $quantity = $cartData['quantity'];
                $subtotal = $variant->price * $quantity;
                
                $totalQuantity += $quantity;
                $totalPrice += $subtotal;
                
                // Chỉ lấy 5 sản phẩm đầu để hiển thị
                if (count($items) < 5) {
                    $items[] = [
                        'id' => $variantId, // Dùng variantId làm id tạm
                        'name' => $variant->product->name,
                        'image' => $variant->product->firstImage->image_url ?? asset('assets/clients/img/default.jpg'),
                        'size' => $variant->size->name,
                        'color' => $variant->color->name,
                        'price' => $variant->price,
                        'quantity' => $quantity,
                        'subtotal' => $subtotal
                    ];
                }
            }
            
            return response()->json([
                'count' => $totalQuantity,
                'items' => $items,
                'total' => $totalPrice,
                'formatted_total' => number_format($totalPrice, 0, ',', '.') . ' VNĐ'
            ]);
        }
        
        // Nếu ĐÃ đăng nhập - đọc từ DATABASE
        $cartItems = CartItem::with(['productVariant.product.firstImage', 'productVariant.size', 'productVariant.color'])
            ->where('user_id', Auth::id())
            ->take(5)
            ->get();

        $totalQuantity = CartItem::where('user_id', Auth::id())->sum('quantity');
        $totalPrice = 0;

        $items = [];
        foreach ($cartItems as $item) {
            $totalPrice += $item->productVariant->price * $item->quantity;
            
            $items[] = [
                'id' => $item->id,
                'name' => $item->productVariant->product->name,
                'image' => $item->productVariant->product->firstImage->image_url ?? asset('assets/clients/img/default.jpg'),
                'size' => $item->productVariant->size->name,
                'color' => $item->productVariant->color->name,
                'price' => $item->productVariant->price,
                'quantity' => $item->quantity,
                'subtotal' => $item->productVariant->price * $item->quantity
            ];
        }

        return response()->json([
            'count' => $totalQuantity,
            'items' => $items,
            'total' => $totalPrice,
            'formatted_total' => number_format($totalPrice, 0, ',', '.') . ' VNĐ'
        ]);
    }
}
