<?php

namespace App\Services\Api;

use App\Services\Api\NetworkUtils;
use Illuminate\Support\Facades\Log;
use App\Exceptions\DataNotFoundException;

class ApiServices extends NetworkUtils
{

    public function get_token_zkteco()
    {
        $data = [
            'username' => 'admin',
            'password' => 'ciptakanjuara123',
        ];
        $res = $this->emitter('POST', '/jwt-api-token-auth', $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            throw new DataNotFoundException($res->msg);
        } else {
            return $res['data'];
        }
    }


    public function get_employees()
    {
        $data = [
            'page' => null,
            'page_size' => null,
            'emp_code' => null,
            'emp_code_incontains' => null,
            'first_name' => null,
            'first_name_incontains' => null,
            'last_name_incontains' => null,
            'last_name' => null,
            'department' => null,
            'areas' => null,
        ];
        $res = $this->emitter('GET', "/personnel/api/employees/", $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new DataNotFoundException($res['msg']);
        } else {
            return $res['data'];
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
            // throw new DataNotFoundException($res['msg']);
        } else {
            return $res['data'];
        }
    }
}
