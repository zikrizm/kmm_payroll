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

    public function read_employee($id)
    {
        $res = $this->emitter('GET', "/personnel/api/employees/" . $id . "/", null);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }

    public function create_employee($data)
    {
        $data = [
            'id' => null,
            'emp_code' => $data['emp_code'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'area' => $data['area'],
            'department' => $data['department'],
            'hire_date' => $data['hire_date'],
            'gender' => $data['gender'],
            'mobile' => $data['mobile'],
            'national' => $data['national'],
            'address' => $data['address'],
            'email' => $data['email'],
            'app_status' => $data['app_status'],
            'app_role' => $data['app_role'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'nickname' => $data['nickname'],
        ];
        $res = $this->emitter('POST', "/personnel/api/employees/", $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }

    public function update_employee($data)
    {
        $data = [
            'emp_code' => $data['emp_code'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'area' => $data['area'],
            'department' => $data['department'],
            'hire_date' => $data['hire_date'],
            'gender' => $data['gender'],
            'mobile' => $data['mobile'],
            'national' => $data['national'],
            'address' => $data['address'],
            'email' => $data['email'],
            'app_status' => $data['app_status'],
            'app_role' => $data['app_role'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'nickname' => $data['nickname'],
        ];
        $res = $this->emitter('POST', "/personnel/api/employees/" . $data['id'] . "/", $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }

    public function adjust_employee($data)
    {
        $data = [
            'employees' => $data['employees'],
            'areas' => $data['areas'],
        ];
        $res = $this->emitter('POST', "/personnel/api/employees/adjust_area/", $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }

    public function adjust_department($data)
    {
        $data = [
            'departments' => $data['departments'],
            'areas' => $data['areas'],
        ];
        $res = $this->emitter('POST', "/personnel/api/employees/adjust_department/", $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }

    public function adjust_regsin($data)
    {
        $data = [
            'employees' => $data['employees'],
            'resign_date' => $data['resign_date'],
            'resign_type' => $data['resign_type'],
            'reason' => $data['reason'],
            'disableatt' => $data['disableatt'],
        ];
        $res = $this->emitter('POST', "/personnel/api/employees/adjust_regsin/", $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }

    public function delete_employee($id)
    {
        $res = $this->emitter('DELETE', "/personnel/api/employees/" . $id . "/", null);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }

    public function get_departments($data)
    {
        $data = [
            'page' => $data['page'],
            'page_size' => $data['page_size'],
            'dept_code' => $data['dept_code'],
            'dept_name' => $data['dept_name'],
            'dept_code_incontains' => $data['dept_code_incontains'],
            'dept_name_incontains' => $data['dept_name_incontains'],
            'ordering' => $data['ordering'],
        ];
        $res = $this->emitter('GET', "/personnel/api/departments/", $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }

    public function read_department($id)
    {
        $res = $this->emitter('GET', "/personnel/api/departments/" . $id . "/", null);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }

    public function create_department($data)
    {
        $data = [
            'id' => null,
            'dept_code' => $data['dept_code'],
            'dept_name' => $data['dept_name'],
            'parent_dept' => $data['parent_dept'],
        ];
        $res = $this->emitter('POST', "/personnel/api/departments/", $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }

    public function update_department($data)
    {
        $data = [
            'dept_code' => $data['dept_code'],
            'dept_name' => $data['dept_name'],
        ];
        $res = $this->emitter('PUT', "/personnel/api/departments/" . $data['id'] . '/', $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }

    public function delete_department($id)
    {
        $res = $this->emitter('DELETE', "/personnel/api/departments/" . $id . "/", null);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }

    public function get_areas($data)
    {
        $data = [
            'page' => $data['page'],
            'page_size' => $data['page_size'],
            'area_code' => $data['dept_code'],
            'area_name' => $data['dept_name'],
            'ordering' => $data['ordering'],
        ];
        $res = $this->emitter('GET', "/personnel/api/areas/", $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }

    public function read_area($id)
    {
        $res = $this->emitter('GET', "/personnel/api/areas/" . $id . "/", null);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }

    public function create_area($data)
    {
        $data = [
            'id' => null,
            'area_code' => $data['area_code'],
            'area_name' => $data['area_name'],
            'parent_area' => $data['parent_area'],
        ];
        $res = $this->emitter('POST', "/personnel/api/areas/", $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }

    public function update_area($data)
    {
        $data = [
            'area_code' => $data['area_code'],
            'area_name' => $data['area_name'],
        ];
        $res = $this->emitter('PUT', "/personnel/api/areas/" . $data['id'] . '/', $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }

    public function delete_area($id)
    {
        $res = $this->emitter('DELETE', "/personnel/api/areas/" . $id . "/", null);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }

    public function get_positions($data)
    {
        $data = [
            'page' => $data['page'],
            'page_size' => $data['page_size'],
            'position_code' => $data['position_code'],
            'position_name' => $data['position_name'],
            'ordering' => $data['ordering'],
        ];
        $res = $this->emitter('GET', "/personnel/api/positions/", $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }

    public function read_position($id)
    {
        $res = $this->emitter('GET', "/personnel/api/positions/" . $id . "/", null);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }

    public function create_position($data)
    {
        $data = [
            'id' => null,
            'position_code' => $data['area_code'],
            'position_name' => $data['area_name'],
            'parent_position' => $data['parent_area'],
        ];
        $res = $this->emitter('POST', "/personnel/api/positions/", $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }

    public function update_position($data)
    {
        $data = [
            'position_code' => $data['position_code'],
            'position_name' => $data['position_name'],
        ];
        $res = $this->emitter('PUT', "/personnel/api/positions/" . $data['id'] . '/', $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }

    public function delete_position($id)
    {
        $res = $this->emitter('DELETE', "/personnel/api/positions/" . $id . "/", null);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }

    public function get_transactions($data)
    {
        $data = [
            'page' => $data['page'],
            'page_size' => $data['page_size'],
            'emp_code' => $data['emp_code'],
            'terminal_sn' => $data['terminal_sn'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
        ];
        $res = $this->emitter('GET', "/iclock/api/transactions/", $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }

    public function read_transaction($id)
    {
        $res = $this->emitter('GET', "/iclock/api/transactions/" . $id . "/", null);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }

    public function delete_transaction($id)
    {
        $res = $this->emitter('DELETE', "/iclock/api/transactions/" . $id . "/", null);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }
}
