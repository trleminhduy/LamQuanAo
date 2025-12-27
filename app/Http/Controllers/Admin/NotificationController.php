<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('is_read', 0)->latest('created_at')->get();

        return view('admin.pages.notifications', compact('notifications'));
    }
}
