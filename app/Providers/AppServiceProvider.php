<?php

namespace App\Providers;

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
        Blade::directive('convert2', function ($money) {
            return "<?php echo explode('.',number_format($money, 2))[0]; ?>";
        });
        Blade::directive('dateFormat', function ($date) {
            return "<?php echo date('d/m/Y', strtotime($date)); ?>";
        });
        Blade::directive('dateFormat2', function ($date) {
            return "<?php echo date('d-M-Y', strtotime($date)); ?>";
        });
        Blade::directive('dateFormatFull', function ($date) {
            return "<?php echo Carbon\Carbon::createFromFormat('Y-m-d', $date)->isoFormat('D MMMM Y') ?>";
        });
        Blade::directive('global_date', function ($data = null) {
            if($data)
            return "<?php echo Carbon\Carbon::createFromFormat('Y-m-d', $data)->isoFormat('dddd, D MMMM Y') ?>";
            else return '';
        });
        Blade::directive('date', function ($date) {
            return "<?php echo Carbon\Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y') ?>";
        });
    }
}
