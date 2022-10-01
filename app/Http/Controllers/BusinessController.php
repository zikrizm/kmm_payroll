<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Business;
use App\Utils\BusinessUtil;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\Api\ApiServices;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class BusinessController extends Controller
{
    private $apiService;
    private $buildRes;
    private $businessUtil;

    public function __construct(BusinessUtil $businessUtil, ApiServices $service, ResponseUtil $buildRes)
    {
        $this->businessUtil = $businessUtil;
        $this->apiService = $service;
        $this->buildRes = $buildRes;
    }

    /**
     * Shows business settings form
     *
     * @return \Illuminate\Http\Response
     */
    public function getBusinessRegister()
    {
        return view('business.register');
    }

    /**
     * Handles the registration of a new business and it's owner
     *
     * @return \Illuminate\Http\Response
     */
    public function storeBusinessRegister(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'country' => 'required|max:255',
                'state' => 'required|max:255',
                'city' => 'required|max:255',
                'zip_code' => 'required|max:7',
                'first_name' => 'required|max:255',
                'email' => 'sometimes|nullable|email|unique:users|max:255',
                'username' => 'required|min:4|max:255|unique:users',
                'password' => 'required|min:8|max:255',
            ]);

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                DB::beginTransaction();

                //Create owner.
                $owner_details = $request->only(['surname', 'first_name', 'last_name', 'username', 'email', 'password']);
                $owner_details['is_default'] = 1;
                $user = User::create_user($owner_details);

                $business_details = $request->only(['name', 'start_date']);

                $business_location = $request->only(['name', 'country', 'state', 'city', 'zip_code', 'full_address', 'website', 'mobile', 'alternate_number']);
                //Create the business
                $business_details['owner_id'] = $user->id;

                if (!empty($business_details['start_date'])) {
                    $business_details['start_date'] = Carbon::createFromFormat(
                        config('constants.default_date_format'),
                        $business_details['start_date']
                    )->toDateString();
                }

                //upload logo
                $logo_name = $this->businessUtil->uploadFile($request, 'business_logo', 'business_logos', 'image');
                if (!empty($logo_name)) {
                    $business_details['logo'] = $logo_name;
                }

                $business = Business::create_business($business_details);

                //Update user with business id
                $user->business_id = $business->id;
                $user->save();

                $this->businessUtil->newBusinessDefaultResources($business->id, $user->id);
                $new_location = $this->businessUtil->addLocation($business->id, $business_location);

                //create new permission with the new location
                Permission::create(['name' => 'location.' . $new_location->id]);

                DB::commit();

                return $this->buildRes->RESPONSE_REQ('success', null, 'business created succesfully');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Shows business settings form
     *
     * @return \Illuminate\Http\Response
     */
    public function getBusinessSettings(Request $request)
    {
        if (!auth()->user()->can('business_settings.access')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = Session::get('business_id');
        $business = Business::where('id', $business_id)->first();
        return view('business.settings', compact('business'));
    }

    /**
     * Handles the registration of a update business and it's owner
     *
     * @return \Illuminate\Http\Response
     */
    public function updateBusinessSettings(Request $request)
    {
        if (!auth()->user()->can('business_settings.access') || !$request->ajax()) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $validator->errors());
            } else {
                DB::beginTransaction();
                $business_details = $request->only(['name', 'start_date']);

                // start_date
                if (!empty($business_details['start_date'])) {
                    $business_details['start_date'] = $this->businessUtil->uf_date($business_details['start_date']);
                }

                // upload logo
                $logo_name = $this->businessUtil->uploadFile($request, 'business_logo', 'business_logos', 'image');
                if (!empty($logo_name)) {
                    $business_details['logo'] = $logo_name;
                }

                $business_id = Session::get('business_id');
                $business = Business::where('id', $business_id)->first();

                //Update business settings
                if (!empty($business_details['logo'])) {
                    $business->logo = $business_details['logo'];
                } else {
                    unset($business_details['logo']);
                }

                $business->fill($business_details);
                $business->save();

                DB::commit();
                return $this->buildRes->RESPONSE_REQ('success', null, 'business update succesfully');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            return $this->buildRes->RESPONSE_REQ('error', null, ['error' => 'something wrong']);
        }
    }

    /**
     * Rules validation group.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }
}
