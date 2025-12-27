<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function showFormAddSupplier()
    {
        $suppliers = Supplier::all();
        return view('admin.pages.supplier-add', compact('suppliers'));
    }

    public function addSupplier(Request $request)
    {

        //trùng nhà cung cấp thì ko thêm đc
        $existingSupplier = Supplier::where('name', $request->name)->first();
        if ($existingSupplier) {
            return redirect()->route('admin.supplier.add')->with('error', 'Nhà cung cấp đã tồn tại!');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
        ]);

        Supplier::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'description' => $request->description,

        ]);
        return redirect()->route('admin.supplier.add')->with('success', 'Thêm nhà cung cấp thành công!');
    }

    //index
    public function index()
    {
        $suppliers = Supplier::all();
        return view('admin.pages.supplier', compact('suppliers'));
    }



    //form sửa ncc
    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('admin.pages.supplier-edit', compact('supplier'));
    }
    public function updateSupplier(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
        ]);
        $supplier = Supplier::find($request->id);
        if (!$supplier) {
            return redirect()->route('admin.suppliers.index')->with('error', 'Nhà cung cấp không tồn tại!');
        }
        $supplier->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'description' => $request->description,
        ]);
        return redirect()->route('admin.suppliers.index')->with('success', 'Cập nhật nhà cung cấp thành công!');
    }
    //xoá ncc

    public function deleteSupplier(Request $request)
    {
        $supplier = Supplier::find($request->id);
        if (!$supplier) {
            return response()->json(['error' => 'Nhà cung cấp không tồn tại!'], 404);
        }
        $supplier->delete();
        return response()->json(['status' => true, 'message' => 'Xoá nhà cung cấp thành công!']);
    }
}
