<?php

namespace App\Utils;

use App\Models\User;
use App\Models\Business;
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
            'zip_code' => $location_details['zip_code'],
            'mobile' => !empty($location_details['mobile']) ? $location_details['mobile'] : '',
            'website' => !empty($location_details['website']) ? $location_details['website'] : '',
            'full_address' => $location_details['full_address'],
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
        
    }
}
