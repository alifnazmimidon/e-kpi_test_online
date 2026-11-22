<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        if (str_contains(request()->getHost(), 'trycloudflare.com')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        View::composer('*', function ($view) {
            static $thresholds = null;

            if ($thresholds === null) {
                $row = null;

                try {
                    if (Schema::hasTable('kpi_settings')) {
                        $row = DB::table('kpi_settings')->first();
                    } elseif (Schema::hasTable('eom_thresholds')) {
                        $row = DB::table('eom_thresholds')->first();
                    }
                } catch (\Throwable $e) {
                    $row = null;
                }

                $thresholds = [
                    'gold'   => $row->gold   ?? $row->gold_threshold   ?? 85,
                    'silver' => $row->silver ?? $row->silver_threshold ?? 70,
                    'bronze' => $row->bronze ?? $row->bronze_threshold ?? 50,
                ];
            }

            $view->with('eomThresholds', $thresholds);
        });
    }
}