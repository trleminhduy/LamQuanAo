<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use App\Listeners\MergeGuestChatAfterLogin;
use Illuminate\Support\Facades\View;
use App\View\Composers\NotificationComposer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Đăng ký event listener cho chat merge sau khi login
        Event::listen(
            Login::class,
            MergeGuestChatAfterLogin::class
        );

        // Share notifications cho top-navigation
        View::composer('admin.partials.top-navigation', NotificationComposer::class);
    }
}
