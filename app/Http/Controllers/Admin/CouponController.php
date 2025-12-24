<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::orderBy('created_at', 'desc')->get();
        return view('admin.pages.coupons.index', compact('coupons'));
    }

    //thêm cp
    public function create()
    {
        return view('admin.pages.coupons.create');
    }

    //Lưu cp
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'discount_type' => 'required|in:percent,amount',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        Coupon::create([
            'code' => strtoupper($request->code),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->has('status') ? 1 : 0,
        ]);

        return redirect()->route('coupons.index')->with('success', 'Đã thêm mã giảm giá thành công');
    }

    //form sửa cp
    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);
        return view('admin.pages.coupons.edit', compact('coupon'));
    }

    //cập nhật cp
    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'discount_type' => 'required|in:percent,amount',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $coupon->update([
            'code' => $request->code,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->has('status') ? 1 : 0,
        ]);

        return redirect()->route('coupons.index')->with('success', 'Đã cập nhật mã giảm giá thành công');
    }

    //Xoá cp
    public function destroy($id){
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return response()->json([
            'status' => true,
            'message' => 'Đã xoá mã giảm giá thành công',
        ]);
    }
}
