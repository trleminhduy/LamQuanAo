<?php

namespace App\Http\Controllers\Clients;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;



class AccountController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $addresses = ShippingAddress::where('user_id', Auth::id())->get();
        $orders = Order::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

        return view('clients.pages.account', compact('user', 'addresses', 'orders'));
    }


    //Update information cap nhat lai thong tin
    public function update(Request $request)
    {
        $request->validate([
            'ltn__name' => 'required|string|max:255',
            'ltn__phone_number' => 'nullable|string|max:15',
            'ltn__address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $userId = Auth::id();

        // 1. Update thông tin cơ bản
        User::where('id', $userId)->update([
            'name' => $request->ltn__name,
            'phone_number' => $request->ltn__phone_number,
            'address' => $request->ltn__address,
        ]);

        // 2. Xử lý avatar riêng
        if ($request->hasFile('avatar')) {
            $user = User::find($userId);

            // Xóa ảnh cũ
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Upload ảnh mới tới storage/app/public/users/tenfile.jpg
            $file = $request->file('avatar');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $avatarPath = $file->storeAs('uploads/users', $filename, 'public');

            // Update avatar
            $user->avatar = $avatarPath;
            $user->save();
        }

        $user = User::find($userId);

        return redirect()->route('account')->with('success', 'Cập nhật thông tin thành công!');
    }

    //Change password
    public function changePassword(Request $request)
    {

        $request->validate(
            [
                'current_password' => 'required',
                'new_password' => 'required|min:6',
                'confirm_new_password' => 'required|same:new_password'
            ],
            [
                'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại',
                'new_password.required' => 'Mật khẩu mới không được để trống',
                'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự',
                'confirm_new_password.required' => 'Vui lòng nhập mật khẩu mới',
                'confirm_new_password.same' => 'Mật khẩu nhập lại không khớp',
            ]
        );
        $userId = Auth::id();
        $user = User::find($userId);

        //Check mật khẩu hiện tại không trùng khớp với mật khẩu 
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng!']);
        }

        //Nếu đúng mật khẩu
        $user->update(['password' => Hash::make($request->new_password)]);

        return redirect()->route('account')->with('success', 'Đổi mật khẩu thành công!');
    }

    public function addAddress(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',

        ]);

        // nếu người dùng set địa chỉ mặc định mới thì địa chỉ khác sẽ is_default = 0
        if ($request->has('default')) {
            ShippingAddress::where('user_id', Auth::id())->update(['is_default' => 0]);
        }


        ShippingAddress::create([
            'user_id' => Auth::id(),
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'is_default' => $request->has('default') ? 1 : 0

        ]);

        return back()->with('success', 'Địa chỉ đã được thêm');
    }

    public function updatePrimaryAddress($id)
    {

        //Tìm địa chỉ cần update
        $address = ShippingAddress::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        // Sau khi update thì set tất cả địa chỉ còn lại bằng is_default = 0
        ShippingAddress::where('user_id', Auth::id())->update(['is_default' => 0]);

        // Update địa chỉ chọn bằng 1 (mặc định)
        $address->update(['is_default' => 1]);

        toastr()->success('Địa chỉ mặc định đã được cập nhật');
        return back();
    }

    public function deleteAddress($id)
    {
        ShippingAddress::where('id', $id)->where('user_id', Auth::id())->delete();
        toastr()->success('Địa chỉ đã được xoá');
        return back();
    }
}
