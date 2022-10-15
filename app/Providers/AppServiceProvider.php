<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        Blade::directive('zkPhoto', function ($url) {
            return config('constants.api_zkteco') . $url;
        });

        Blade::if('activemenu', function ($menu) {
            $is_active = '';
            $segment_first = request()->segment(1);
            $segment_second = request()->segment(2);

            $sub_menu_users = ['user', 'role'];
            $sub_menu_employees = ['employee', 'kasbon', 'resign'];
            $sub_menu_shifts = ['break-time', 'timetable', 'shift', 'holiday'];
            $sub_menu_attendances = ['device', 'transaction', 'transaction-report'];
            $sub_menu_organizations = ['department', 'position', 'area'];
            $sub_menu_settings = ['setting', 'location'];

            switch ($menu) {
                case 'users':
                    $is_active = in_array($segment_first, $sub_menu_users);
                    break;
                case 'users_sub_menu':
                    $is_active = in_array($segment_first, $sub_menu_users);
                    break;
                case 'employees':
                    $is_active = in_array($segment_first, $sub_menu_employees);
                    break;
                case 'shifts':
                    $is_active = in_array($segment_first, $sub_menu_shifts);
                    break;
                case 'attendances':
                    $is_active = in_array($segment_first, $sub_menu_attendances);
                    break;
                case 'organization':
                    $is_active = in_array($segment_first, $sub_menu_organizations);
                    break;
                case 'setting':
                    $is_active = in_array($segment_first, $sub_menu_settings);
                    break;
                default:
                    $is_active = ($segment_first == $menu);
                    break;
            }

            return $is_active;
        });


        Blade::directive('urlPagination', function ($url) {
            return str_replace(config('constants.api'), 'http://localhost/', $url);
        });

        Blade::directive('zkPhoto', function ($url) {
            return config('constants.api') . $url;
        });

        Blade::directive('convert', function ($money) {
            return "Rp. <?php echo number_format($money,0, ',', '.'); ?>";
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




        // Blade directive to convert NIK.
        Blade::directive('NIK', function ($nik) {
            return "<?php echo preg_replace('/(?<=\d)(?=(\d{4})+$)/', ' ', $nik);?>";
        });
    }
}
