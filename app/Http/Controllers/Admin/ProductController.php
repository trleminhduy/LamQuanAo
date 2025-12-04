<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class ProductController extends Controller
{
   public function showFormAddProduct()
   {
      $categories = Category::all();
      $suppliers = Supplier::all();
      return view('admin.pages.product-add', compact('categories', 'suppliers'));
   }

   public function addProduct(Request $request)
   {

      $request->validate([
         'category_id' => 'required|exists:categories,id',
         'supplier_id' => 'nullable|exists:suppliers,id',
         'name' => 'required|string|max:255',
         'description' => 'nullable|string',
         'price' => 'required|numeric|min:0',
         'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
      ]);

      $slug = Str::slug($request->name) . '-' . time();

      //Tạo sản phẩm
      $product = Product::create([
         'category_id' => $request->category_id,
         'supplier_id' => $request->supplier_id,
         'name' => $request->name,
         'slug' => $slug,
         'description' => $request->description,
         'price' => $request->price,
         'stock' => $request->stock ?? 0,
         'status' => 'in_stock'
      ]);

      //Xử lý ảnh
      if ($request->hasFile('images')) {
         $isFirst = true;
         foreach ($request->file('images') as $image) {
            $fileName = now()->timestamp . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

            // Lưu ảnh trực tiếp không resize
            $path = $image->storeAs('uploads/products', $fileName, 'public');

            // Lưu thông tin ảnh vào database
            ProductImage::create([
               'product_id' => $product->id,
               'image' => $path,
               'is_main' => $isFirst, // Ảnh đầu tiên là ảnh chính
            ]);

            $isFirst = false;
         }
      }

      return redirect()->route('admin.product.add')->with('success', 'Đã thêm sản phẩm thành công');
   }

   public function index()
   {
      $products = Product::with('category', 'supplier', 'images')->get();
      $suppliers = Supplier::all();
      $categories = Category::all();
      return view('admin.pages.products', compact('products', 'suppliers', 'categories'));
   }

   public function updateProduct(Request $request)
   {
      $request->validate([
         'id' => 'required|exists:products,id',
         'category_id' => 'required|exists:categories,id',
         'supplier_id' => 'nullable|exists:suppliers,id',
         'name' => 'required|string|max:255',
         'slug' => 'nullable|string|max:255',
         'description' => 'nullable|string',
         'price' => 'required|numeric|min:0',
         'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
      ]);
      $product = Product::findOrFail($request->id);
      $product->update([
         'category_id' => $request->category_id,
         'supplier_id' => $request->supplier_id,
         'name' => $request->name,
         'slug' => Str::slug($request->name) . '-' . time(),
         'description' => $request->description,
         'price' => $request->price,
         'stock' => $request->stock ?? $product->stock,
      ]);
      //Xử lý ảnh
      if ($request->hasFile('images')) {
         //Xoá ảnh cũ
         $oldImages = ProductImage::where('product_id', $product->id)->get();
         foreach ($oldImages as $oldImage) {
            Storage::disk('public')->delete('uploads/' . $oldImage->image);
         }
         ProductImage::where('product_id', $product->id)->delete();

         $isFirst = true;
         foreach ($request->file('images') as $image) {
            $fileName = now()->timestamp . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            // Lưu ảnh trực tiếp không resize
            $path = $image->storeAs('uploads/products', $fileName, 'public');
            // Lưu thông tin ảnh vào database
            ProductImage::create([
               'product_id' => $product->id,
               'image' => $path,
               'is_main' => $isFirst, // Ảnh đầu tiên là ảnh chính
            ]);
            $isFirst = false;
         }
      }

      return response()->json([
         'status' => true,
         'message' => 'Cập nhật sản phẩm thành công',
         'data' => [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'price' => $product->price,
            'stock' => $product->stock,
            'category_name' => $product->category ? $product->category->name : null,
            'supplier' => $product->supplier ? $product->supplier->name : null,
            'image' => $product->images->map(fn($img) => asset('storage/' . $img->image)),
            'status' => $product->status == 'in_stock' ? 'Còn hàng' : 'Hết hàng',
         ]
      ]);
   }
}
