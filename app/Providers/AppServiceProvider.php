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

        Blade::directive('activemenu', function ($menu) {
            $is_active_class = '';
            $segment_first = request()->segment(1);
            $sub_menu_organizations = ['department', 'position', 'area'];
            $sub_menu_users = ['user', 'role'];
            $sub_menu_shifts = ['break-time', 'timetable', 'shift'];
            $sub_menu_settings = ['setting', 'location'];

            switch ($menu) {
                case 'organization':
                    $is_active_class = in_array($segment_first, $sub_menu_organizations) ? 'bg-gray-100 active' : 'hover:bg-gray-50';
                    break;
                case 'organization_sub_menu':
                    $is_active_class = in_array($segment_first, $sub_menu_organizations) ? '' : 'hidden';
                    break;
                case 'user':
                    $is_active_class = in_array($segment_first, $sub_menu_users) ? 'bg-gray-100 active' : 'hover:bg-gray-50';
                    break;
                case 'user_sub_menu':
                    $is_active_class = in_array($segment_first, $sub_menu_users) ? '' : 'hidden';
                    break;
                case 'shift':
                    $is_active_class = in_array($segment_first, $sub_menu_shifts) ? 'bg-gray-100 active' : 'hover:bg-gray-50';
                    break;
                case 'shift_sub_menu':
                    $is_active_class = in_array($segment_first, $sub_menu_shifts) ? '' : 'hidden';
                    break;
                case 'setting':
                    $is_active_class = in_array($segment_first, $sub_menu_settings) ? 'bg-gray-100 active' : 'hover:bg-gray-50';
                    break;
                case 'setting_sub_menu':
                    $is_active_class = in_array($segment_first, $sub_menu_settings) ? '' : 'hidden';
                    break;
                default:
                    $is_active_class = ($segment_first == $menu)  ? 'bg-gray-100' : 'hover:bg-gray-50';
                    break;
            }

            return $is_active_class;
        });

        Blade::directive('activeSubMenu', function ($url) {
            return config('constants.api_zkteco') . $url;
        });

        Blade::directive('zkPhoto', function ($url) {
            return config('constants.api_zkteco') . $url;
        });

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
