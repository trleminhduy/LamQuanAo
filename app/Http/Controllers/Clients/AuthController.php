<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Mail\ActivationMail;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('clients.pages.register');
    }

    public function register(Request $request) //chỗ này phải có request tại mình submit lên
    {
        //validate

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|',


        ], [
            'name.required' => 'Tên là bắt buộc',
            'email.required' => 'Email là bắt buộc',
            'email.unique' => 'Email đã được sử dụng',
            'password.required' => 'Mật khẩu là bắt buộc',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',

        ]);

        //Check if email exist
        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser) {

            if ($existingUser->isPending()) {
                toastr()->error('Email đã được đăng ký và đang chờ kích hoạt');
                return redirect()->route('register');
            }
            return redirect()->route('register');
        }

        //Create token active

        $token = Str::random(64);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'pending',
            'role_id' => 3,
            'activation_token' => $token,
        ]);

        //Gửi mail xác thực
        Mail::to($user->email)->send(new ActivationMail($token, $user));

        toastr()->success('Đăng ký tài khoản thành công. Vui lòng kiểm tra lại email của bạn để kích hoạt tài khoản');
        return redirect()->route('login');
    }
    public function activate($token)
    {
        $user = User::where('activation_token', $token)->first();

        if ($user) {
            $user->status = 'active';
            $user->activation_token = null;
            $user->save();


            toastr()->success('Kích hoạt tài khoản thành công');
            return redirect()->route('login');
        }

        toastr()->error('Token không hợp lệ hoặc đã hết hạn');
        return redirect()->back();
    }

    public function showloginForm()
    {
        return view('clients.pages.login');
    }


    public function login(Request $request)
    { //chỗ này phải có request tại mình submit lên
        $request->validate([

            'email' => 'required|email',
            'password' => 'required|min:6|',


        ], [

            'email.required' => 'Email là bắt buộc',
            'email.email' => 'Email không hợp lệ',
            'password.required' => 'Mật khẩu là bắt buộc',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',

        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'status' => 'active'])) {
            if (in_array(Auth::user()->role->name, ['customer'])) {
                $request->session()->regenerate();
                
                // Chuyển giỏ hàng từ session sang database
                $this->mergeSessionCartToDatabase();
                
                toastr()->success('Đăng nhập thành công');

                return redirect()->route('home');
            } else {
                Auth::logout();
                toastr()->error('Bạn không có quyền truy cập vào tài khoản này');
                return redirect()->back();
            }
        }

        toastr()->error('Thông tin đăng nhập không chính xác hoặc tài khoản chưa được kích hoạt');
        return redirect()->back();
    }
    
    // Chuyển giỏ hàng từ session sang database khi đăng nhập
    private function mergeSessionCartToDatabase()
    {
        // Lấy giỏ hàng từ session
        $sessionCart = session()->get('cart', []);
        
        if (empty($sessionCart)) {
            return; // Không có gì trong session thì thôi
        }
        
        $userId = Auth::id();
        
        foreach ($sessionCart as $variantId => $cartData) {
            // Kiểm tra variant còn tồn tại không
            $variant = ProductVariant::find($variantId);
            if (!$variant) continue;
            
            // Kiểm tra sản phẩm đã có trong giỏ database chưa
            $cartItem = CartItem::where('user_id', $userId)
                ->where('product_variant_id', $variantId)
                ->first();
            
            if ($cartItem) {
                // Nếu đã có thì cộng thêm số lượng
                $newQuantity = $cartItem->quantity + $cartData['quantity'];
                
                // Kiểm tra tồn kho
                if ($newQuantity <= $variant->stock) {
                    $cartItem->quantity = $newQuantity;
                    $cartItem->save();
                }
            } else {
                // Nếu chưa có thì tạo mới
                if ($cartData['quantity'] <= $variant->stock) {
                    CartItem::create([
                        'user_id' => $userId,
                        'product_variant_id' => $variantId,
                        'quantity' => $cartData['quantity']
                    ]);
                }
            }
        }
        
        // Xóa giỏ hàng trong session sau khi đã chuyển
        session()->forget('cart');
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        toastr()->success('Đăng xuất thành công');
        return redirect()->route('login');
    }
}
