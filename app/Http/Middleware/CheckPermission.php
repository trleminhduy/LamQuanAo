<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        // Phân biệt admin/delivery vs user guard
        if ($request->is('admin') || $request->is('admin/*') || $request->is('delivery') || $request->is('delivery/*')) {
            // Admin & Delivery routes - dùng guard admin
            if (!Auth::guard('admin')->check()) {
                return redirect()->route('admin.login');
            }
            $user = Auth::guard('admin')->user();
        } else {
            // User routes - dùng guard web
            if (!Auth::check()) {
                return redirect()->route('login');
            }
            $user = Auth::user();
        }
        
        // Admin có tất cả quyền
        if ($user->role && $user->role->name === 'admin') {
            return $next($request);
        }

        // Kiểm tra permission
        if (!$user->role || !$user->role->permissions()->where('name', $permission)->exists()) {
            abort(403, 'Bạn không có quyền truy cập');
        }

        return $next($request);
    }
}
