<?php

namespace App\Utils;

use App\Models\User;
use App\Models\Business;
use Illuminate\Support\Carbon;
use App\Models\BusinessLocation;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class BusinessUtil extends Util
{
    /**
     * Initializes the BusinessUtil.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Adds a default settings/resources for a new business
     *
     * @param int $business_id
     * @param int $user_id
     *
     * @return boolean
     */
    public function newBusinessDefaultResources($business_id, $user_id)
    {
        $user = User::find($user_id);

        //create Admin role and assign to user
        $role = Role::create(['name' => 'Admin#' . $business_id, 'business_id' => $business_id, 'guard_name' => 'web', 'is_default' => 1])->givePermissionTo(Permission::all());
        $user->assignRole($role->name);

        //Create Cashier role for a new business
        $cashier_role = Role::create(['name' => 'Cashier#' . $business_id, 'business_id' => $business_id, 'guard_name' => 'web']);
        $cashier_role->syncPermissions(['user.view']);

        $business = Business::findOrFail($business_id);

        return true;
    }

    /**
     * Adds a new location to a business
     *
     * @param int $business_id
     * @param array $location_details
     * @param int $invoice_layout_id default null
     *
     * @return location object
     */
    public function addLocation($business_id, $location_details)
    {
        $location = BusinessLocation::create([
            'business_id' => $business_id,
            'name' => $location_details['name'],
            'city' => $location_details['city'],
            'state' => $location_details['state'],
            'zip_code' => $location_details['zip_code'],
            'country' => $location_details['country'],
            'mobile' => !empty($location_details['mobile']) ? $location_details['mobile'] : '',
            'alternate_number' => !empty($location_details['alternate_number']) ? $location_details['alternate_number'] : '',
            'website' => !empty($location_details['website']) ? $location_details['website'] : '',
            'email' => '',
        ]);
        return $location;
    }

    /**
     * Converts date in business format to mysql format
     *
     * @param string $date
     * @param bool $time (default = false)
     * @return strin
     */
    public function uf_date($date, $time = false)
    {
        $date_format = 'm/d/Y';
        $mysql_format = 'Y-m-d';
        // if ($time) {
        //     if (session('business.time_format') == 12) {
        //         $date_format = $date_format . ' h:i A';
        //     } else {
        //         $date_format = $date_format . ' H:i';
        //     }
        //     $mysql_format = 'Y-m-d H:i:s';
        // }

        return !empty($date_format) ? Carbon::createFromFormat($date_format, $date)->format($mysql_format) : null;
    }
}
