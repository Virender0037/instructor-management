<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Message;

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
        View::composer(['layouts.superadmin', 'layouts.instructor'], function ($view) {
        $user = auth()->user();

        if (!$user) {
            $view->with('unreadCount', 0);
            return;
        }

        $unread = Message::where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->count();

        $view->with('unreadCount', $unread);
    });
    }
}
