<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        RedirectIfAuthenticated::redirectUsing(function () {
            $user = Auth::user();
            if (! $user) return route('home');

            return match ($user->role) {
                'admin'    => route('admin.index'),
                'pending'  => route('pending'),
                'rejected' => route('rejected'),
                default    => route('dashboard'),
            };
        });
    }
}
