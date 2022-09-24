<?php

namespace App\Services\Api;

use Exception;
use App\Models\ZktecoSettings;
use App\Services\Api\NetworkUtils;
use Illuminate\Support\Facades\Log;
use App\Exceptions\DataNotFoundException;
use App\Exceptions\ResponseExeception;

class ApiServices extends NetworkUtils
{

    public function get_token_zkteco()
    {
        $zkteco_setting = ZktecoSettings::where('is_login', true)->first();
        if ($zkteco_setting) {
            $data = [
                'username' => $zkteco_setting->username,
                'password' => $zkteco_setting->password,
            ];

            $res = $this->emitter('POST', "jwt-api-token-auth/", $data);
            if ($res['response'] < 200 || $res['response'] >= 300) {
                throw new Exception(serialize($res['msg']));
            } else {
                return $res;
            }
        } else {
            return $this->buildRes->RESPONSE_REQ('error', null, 'No user logged into bio time API');
        }
    }


    public function get_employees()
    {
        $data = [
            "page" => null,
            "page_size" => null,
            "emp_code" => null,
            "emp_code_incontains" => null,
            "first_name" => null,
            "first_name_incontains" => null,
            "last_name_incontains" => null,
            "last_name" => null,
            "department" => null,
            "areas" => null,
        ];
        $res = $this->emitter('GET', "/personnel/api/employees/", $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new Exception(serialize($res['msg']));
            return collect($res);
        } else {
            return collect($res);
        }
    }

    public function get_departments()
    {
        $data = [
            'page' => null,
            'page_size' => null,
            'dept_code' => null,
            'dept_name' => null,
            'dept_code_incontains' => null,
            'dept_name_incontains' => null,
            'ordering' => null,
        ];
        $res = $this->emitter('GET', "/personnel/api/departments/", $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }

    public function create_department()
    {
        $data = [
            'id' => null,
            'dept_code' => null,
            'dept_name' => null,
            'parent_dept' => null,
        ];
        $res = $this->emitter('POST', "/personnel/api/departments/", $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }
}
