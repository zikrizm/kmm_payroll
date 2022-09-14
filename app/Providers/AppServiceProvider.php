<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('convert', function ($money) {
            return "Rp. <?php echo number_format($money, 2); ?>";
        });

        //Blade directive to convert.
        Blade::directive('formatDate', function ($date) {
            return "Carbon\Carbon::createFromTimestamp(strtotime($date))->format('m/d/y')";
        });

        //Blade directive to convert.
        Blade::directive('format_date_full', function ($date) {
            return "<?php echo Carbon\Carbon::createFromFormat('Y-m-d', $date)->isoFormat('D MMMM Y') ?>";
        });

        Blade::directive('global_date', function ($data = null) {
            if ($data)
                return "<?php echo Carbon\Carbon::createFromFormat('Y-m-d', $data)->isoFormat('dddd, D MMMM Y') ?>";
            else return '';
        });

        Blade::directive('date', function ($date) {
            return "<?php echo Carbon\Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y') ?>";
        });
    }
}
