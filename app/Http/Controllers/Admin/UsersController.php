<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::with('role')->paginate(10);
        return view('admin.pages.users', compact('users'));
    }

    // Nâng cấp người dùng thành nhân viên
    public function upgrade(Request $request)
    {
        $userId = $request->user_id;
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Người dùng không tồn tại.']);
        }

        $user->role_id = 2; 
        $user->save();

        return response()->json(['status' => true, 'message' => 'Đã upgrade thành nhân viên.']);
    }
    //unblock hoặc block
    public function updateStatus(Request $request)
    {
        $userId = $request->user_id;
        $status = $request->status; 
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Người dùng không tồn tại.']);
        }

        $user->status = $status;
        $user->save();
        return response()->json(['status' => true, 'message' => 'Cập nhật trạng thái thành công.']);
    }
}
