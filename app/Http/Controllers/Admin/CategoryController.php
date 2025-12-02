<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
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
            'name'=>$request->input('name'),
            'slug'=> Str::slug($request->input('name')),
            'description' => $request->input('description'),
            'image' => $imagePath,
        ]);
        return redirect()->route('admin.categories.add')->with('success', 'Đã thêm danh mục thành công');
    }
}
