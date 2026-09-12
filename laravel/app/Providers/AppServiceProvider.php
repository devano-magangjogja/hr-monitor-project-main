<?php

namespace App\Providers;

use App\Models\AppSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        \Carbon\Carbon::setLocale('id');
        Paginator::defaultView('vendor.pagination.tailwind');

        // Inject settings ke semua view — dengan fallback aman jika tabel belum ada
        View::composer('*', function ($view) {
            try {
                $appName       = AppSetting::get('app_name', 'Republikweb.net');
                $appLogo       = AppSetting::get('app_logo');
                $appLogoBanner = AppSetting::get('app_logo_banner');
            } catch (\Exception $e) {
                $appName       = 'Republikweb.net';
                $appLogo       = null;
                $appLogoBanner = null;
            }

            $view->with([
                'appName'       => $appName,
                'appLogo'       => $appLogo,
                'appLogoBanner' => $appLogoBanner,
            ]);
        });
    }
}
