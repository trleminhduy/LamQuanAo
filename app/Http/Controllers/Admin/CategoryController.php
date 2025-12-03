<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage as Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function showFormAddCategory()
    {
        return view('admin.pages.categories-add');
    }
    public function addCategory(Request $request)
    {
        //validate
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',

        ]);

        //Xử lý ảnh
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image');
            $fileName = now()->timestamp . '_' . uniqid() . '.' . $imagePath->getClientOriginalExtension();
            $imagePath = $imagePath->storeAs('uploads/categories', $fileName, 'public');
        }

        Category::create([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')),
            'description' => $request->input('description'),
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.categories.add')->with('success', 'Đã thêm danh mục thành công');
    }

    public function index()
    {
        $categories = Category::all();
        return view('admin.pages.categories', compact('categories'));
    }

    public function updateCategory(Request $request)
    {
        try {
            $category = Category::findOrFail($request->category_id);
            if (!$category) {
                return response()->json([
                    'status' => false,
                    'message' => 'Danh mục không tồn tại',
                ], 404);
            }
            //Cập nhật all
            $category->name = $request->name;
            $category->description = $request->input('category-description');
            $category->slug = $request->input('category-slug');

            if ($request->hasFile('image')) {
                //Xử lý ảnh

                //Xoá ảnh cũ
                if ($category->image) {
                    Storage::disk('public')->delete($category->image);
                }
                $imagePath = $request->file('image');
                $fileName = now()->timestamp . '_' . uniqid() . '.' . $imagePath->getClientOriginalExtension();
                $imagePath = $imagePath->storeAs('uploads/categories', $fileName, 'public');
                $category->image = $imagePath;
            }
            $category->save();
            return response()->json(['status' => true, 'message' => 'Cập nhật danh mục thành công', 'data'=>[
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'image' => $category->image ? asset('storage/' . $category->image) : null,
            ]]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Cập nhật danh mục thất bại: ' . $e->getMessage()]);
        }
    }
}
