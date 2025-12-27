<?php

namespace App\View\Composers;

use App\Models\Notification;
use Illuminate\View\View;

class NotificationComposer
{
    public function compose(View $view)
    {
        // Lấy 5 thông báo chưa đọc mới nhất
        $notifications = Notification::where('is_read', 0)
            ->latest('created_at')
            ->limit(5)
            ->get();

        $view->with('notifications', $notifications);
    }
}
