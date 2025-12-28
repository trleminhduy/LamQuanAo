<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use App\Models\Color;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    // Hiển thị tất cả biến thể từ tất cả sản phẩm
    public function listAll()
    {
        $variants = ProductVariant::with(['product', 'size', 'color'])
            ->orderBy('product_id', 'asc')
            ->get();
        
        return view('admin.pages.all-variants', compact('variants'));
    }

    // Hiển thị danh sách biến thể của sản phẩm
    public function index($productId)
    {
        $product = Product::with(['variants.size', 'variants.color'])->findOrFail($productId);
        $sizes = Size::all();
        $colors = Color::all();
        
        return view('admin.pages.product-variants', compact('product', 'sizes', 'colors'));
    }

    // Thêm biến thể mới
    public function addVariant(Request $request, $productId)
    {
        $request->validate([
            'size_id' => 'required|exists:sizes,id',
            'color_id' => 'required|exists:colors,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $product = Product::findOrFail($productId);

        // Kiểm tra tổng biến thể không vượt quá stock sản phẩm gốc
        $currentTotalStock = ProductVariant::where('product_id', $productId)->sum('stock');
        $newTotalStock = $currentTotalStock + $request->stock;

        if ($newTotalStock > $product->stock) {
            return response()->json([
                'status' => false,
                'message' => "Vượt quá số lượng trong kho! Hiện đã phân bổ: {$currentTotalStock}/{$product->stock}. Chỉ còn " . ($product->stock - $currentTotalStock) . " sản phẩm có thể thêm.",
            ]);
        }

        // Kiểm tra variant đã tồn tại chưa 
        $existingVariant = ProductVariant::where('product_id', $productId)
            ->where('size_id', $request->size_id)
            ->where('color_id', $request->color_id)
            ->first();

        if ($existingVariant) {
            return response()->json([
                'status' => false,
                'message' => 'Biến thể này đã tồn tại (Size + Màu trùng)',
            ]);
        }

        // Tạo variant mới
        ProductVariant::create([
            'product_id' => $productId,
            'size_id' => $request->size_id,
            'color_id' => $request->color_id,
            'price' => $request->price,
            'stock' => $request->stock,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Thêm biến thể thành công',
        ]);
    }

    // Cập nhật biến thể
    public function updateVariant(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $variant = ProductVariant::findOrFail($request->variant_id);
        $product = Product::findOrFail($variant->product_id);
        
        // Tính tổng stock KHÔNG BAO GỒM variant đang sửa
        $currentTotalStock = ProductVariant::where('product_id', $variant->product_id)
            ->where('id', '!=', $variant->id)
            ->sum('stock');
        
        $newTotalStock = $currentTotalStock + $request->stock;
        
        if ($newTotalStock > $product->stock) {
            return response()->json([
                'status' => false,
                'message' => "Vượt quá số lượng trong kho! Tổng sẽ là: {$newTotalStock}/{$product->stock}. Vui lòng giảm số lượng.",
            ]);
        }

        $variant->update([
            'price' => $request->price,
            'stock' => $request->stock,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật biến thể thành công',
            'data' => [
                'id' => $variant->id,
                'price' => $variant->price,
                'stock' => $variant->stock,
            ]
        ]);
    }

    // Xoá biến thể
    public function deleteVariant(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
        ]);

        $variant = ProductVariant::findOrFail($request->variant_id);
        $productId = $variant->product_id;

        // Kiểm tra xem variant có trong giỏ hàng không
        if ($variant->cartItems()->count() > 0) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể xoá biến thể vì đã có trong giỏ hàng',
            ]);
        }

        // Kiểm tra xem variant có trong đơn hàng không
        if ($variant->orderItems()->count() > 0) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể xoá biến thể vì đã có trong đơn hàng',
            ]);
        }

        $variant->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xoá biến thể thành công. Lưu ý: Stock sản phẩm gốc không tự động cập nhật, vui lòng kiểm tra lại nếu cần.',
        ]);
    }
}
