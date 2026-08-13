<?php

namespace App\Providers;

use App\Models\AppSetting;
use App\Models\WaGroup;
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
                $appName    = AppSetting::get('app_name', 'HR-DWMS');
                $appLogo    = AppSetting::get('app_logo');
                $appWaGroups = WaGroup::active()->get();
            } catch (\Exception $e) {
                $appName     = 'HR-DWMS';
                $appLogo     = null;
                $appWaGroups = collect();
            }

            $view->with([
                'appName'     => $appName,
                'appLogo'     => $appLogo,
                'appWaGroups' => $appWaGroups,
            ]);
        });
    }
}
