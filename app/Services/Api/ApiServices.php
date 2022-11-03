<?php

namespace App\Services\Api;

use Exception;
use DOMDocument;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use App\Models\ZktecoSettings;
use App\Services\Api\NetworkUtils;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;

class ApiServices extends NetworkUtils
{
    protected $csrftoken_zkteco;

    public function get_token_zkteco()
    {
        $zkteco_setting = ZktecoSettings::where('is_login', true)->first();
        if ($zkteco_setting) {
            $data = [
                'username' => $zkteco_setting->username,
                'password' => $zkteco_setting->password,
            ];

            $res = $this->emitter('POST', "/jwt-api-token-auth/", $data);
            if ($res['response'] < 200 || $res['response'] >= 300) {
                throw new Exception(serialize($res['msg']));
            } else {
                return $res;
            }
        } else {
            return $this->buildRes->RESPONSE_REQ('error', null, 'No user logged into bio time API');
        }
    }

    public function get_csrftoken()
    {
        // YIYlNRqnJ9lt6431uK7vfHYXyuvcW1E0kvOe3pGazs3u9eZJ0iMGvtcr5DN65EMr
        $client = new Client();
        $request = new Psr7\Request('GET', config('constants.api_zkteco') . '/vlRegister/', ['Cookie' => 'csrftoken=']);
        $res = $client->sendAsync($request)->wait();
        $cookie = $res->getHeaderLine('Set-Cookie');
        // Cookie::queue(Cookie::make('csrftoken_zkteco', explode('=', explode(';', $cookie)[0])[1], 60));
        $this->csrftoken_zkteco = explode('=', explode(';', $cookie)[0])[1];
    }

    public function get_csrfmiddlewaretoken()
    {
        $this->get_csrftoken();
        $client = new Client();
        $request = new Psr7\Request('GET', config('constants.api_zkteco') . '/vlRegister/', ['Cookie' => 'csrftoken=' . $this->csrftoken_zkteco]);
        $res = $client->sendAsync($request)->wait();
        $content = $res->getBody();

        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($content);
        $inputs = $doc->getElementsByTagName("input");
        $csrfmiddlewaretoken = '';
        foreach ($inputs as $input) {
            if ($input->getAttribute("name") == "csrfmiddlewaretoken") {
                $csrfmiddlewaretoken = $input->getAttribute("value");
            }
        }

        return $csrfmiddlewaretoken;
    }


    public function update_employee_photo($data)
    {
        $data = [
            'csrftoken' => $this->csrftoken_zkteco,
            'user_capture' => $data['user_capture'] ?? null,
            'employee_code' => $data['employee_code'] ?? null,
            'csrfmiddlewaretoken' => $data['csrfmiddlewaretoken'] ?? null,
            'remark' => $data['remark'] ?? null,
        ];
        $res = $this->emitterGuzzle('POST', "/vlRegister/", $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }


    public function get_employees($data)
    {
        $data = [
            'page' => $data['page'] ?? null,
            'page_size' => $data['page_size'] ?? null,
            'emp_code' => $data['emp_code'] ?? null,
            'emp_code_icontains' => $data['emp_code_icontains'] ?? null,
            'first_name' => $data['first_name'] ?? null,
            'first_name_icontains' => $data['first_name_icontains'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'last_name_icontains' => $data['last_name_icontains'] ?? null,
            'employee_icontains' => $data['employee_icontains'] ?? null,
            'department' => $data['department'] ?? null,
            'departments' => $data['departments'] ?? null,
            'areas' => $data['areas'] ?? null,
            'ordering' => $data['ordering'] ?? null,
        ];

        $res = $this->emitter('GET', "/personnel/api/employees/", $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
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
            "id" => null,
            "emp_code" => $data["emp_code"] ?? null,
            "first_name" => $data["first_name"] ?? null,
            "last_name" => $data["last_name"] ?? null,
            "nickname" => $data["nickname"] ?? null,
            "hire_date" => $data["hire_date"] ?? null,
            "birthday" => $data["birthday"] ?? null,
            "gender" => $data["gender"] ?? null,

            "verify_mode" => $data["verify_mode"] ?? null,
            "emp_type" => $data["emp_type"] ?? null,
            "contact_tel" => $data["contact_tel"] ?? null,
            "office_tel" => $data["office_tel"] ?? null,
            "mobile" => $data["mobile"] ?? null,
            "national" => $data["national"] ?? null,
            "city" => $data["city"] ?? null,
            "address" => $data["address"] ?? null,
            "postcode" => $data["postcode"] ?? null,
            "email" => $data["email"] ?? null,
            "religion" => $data["religion"] ?? null,
            "app_status" => $data["app_status"] ?? null,
            "app_role" => $data["app_role"] ?? null,

            "department" => $data["department"] ?? null,
            "position" =>  null, // ** Posisi dikirim null karena di set dbLocal
            "area" => $data["area"] ?? null,
        ];
        $res = $this->emitter('POST', "/personnel/api/employees/", $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            return $res;
        } else {
            $res['msg'] = ['success' => 'Add employee succesfully'];
            return $res;
        }
    }

    public function update_employee($data)
    {
        $data = [
            "id" => $data["id"] ?? null,
            "emp_code" => $data["emp_code"] ?? null,
            "first_name" => $data["first_name"] ?? null,
            "last_name" => $data["last_name"] ?? null,
            "nickname" => $data["nickname"] ?? null,
            "hire_date" => $data["hire_date"] ?? null,
            "birthday" => $data["birthday"] ?? null,
            "gender" => $data["gender"] ?? null,

            "verify_mode" => $data["verify_mode"] ?? null,
            "emp_type" => $data["emp_type"] ?? null,
            "contact_tel" => $data["contact_tel"] ?? null,
            "office_tel" => $data["office_tel"] ?? null,
            "mobile" => $data["mobile"] ?? null,
            "national" => $data["national"] ?? null,
            "city" => $data["city"] ?? null,
            "address" => $data["address"] ?? null,
            "postcode" => $data["postcode"] ?? null,
            "email" => $data["email"] ?? null,
            "religion" => $data["religion"] ?? null,
            "app_status" => $data["app_status"] ?? null,
            "app_role" => $data["app_role"] ?? null,

            "department" => $data["department"] ?? null,
            "position" => null, // ** Posisi dikirim null karena di set dbLocal
            "area" => $data["area"] ?? null,
        ];
        $res = $this->emitter('PUT', "/personnel/api/employees/" . $data['id'] . "/", $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            return $res;
        } else {
            $res['msg'] = ['success' => 'Update employee succesfully'];
            return $res;
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


    public function get_resign($data)
    {
        $data = [
            'page' => $data['page'] ?? null,
            'page_size' => $data['page_size'] ?? null,
            'employee' => $data['employee'] ?? null,
            'resign_type' => $data['resign_type'] ?? null,
            'resign_date' => $data['resign_date'] ?? null,
            'ordering' => $data['ordering'] ?? null,
        ];
        $res = $this->emitter('GET', "/personnel/api/resigns/", $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }

    public function read_resign($id)
    {
        $res = $this->emitter('GET', "/personnel/api/resigns/" . $id . "/", null);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            return $res;
        } else {
            return $res['data'];
        }
    }

    public function create_resign($data)
    {
        $data = [
            'id' => null,
            'resign_type' => $data['resign_type'] ?? null,
            'disableatt' => $data['disableatt'] ?? null,
            'resign_date' => $data['resign_date'] ?? null,
            'employee' => $data['employee'] ?? null,
            'reason' => $data['reason'] ?? null,
        ];
        $res = $this->emitter('POST', "/personnel/api/resigns/", $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // return $res;
        } else {
            $res['msg'] = ['success' => 'Add resign succesfully'];
            return $res;
        }
    }

    public function update_resign($data)
    {
        $data = [
            'id' => $data['id'],
            'resign_type' => $data['resign_type'] ?? null,
            'disableatt' => $data['disableatt'] ?? null,
            'resign_date' => $data['resign_date'] ?? null,
            'employee' => $data['employee'] ?? null,
            'reason' => $data['reason'] ?? null,
        ];
        $res = $this->emitter('PUT', "/personnel/api/resigns/" . $data['id'] . '/', $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // return $res;
        } else {
            $res['msg'] = ['success' => 'Update resign succesfully'];
            return $res;
        }
    }

    public function delete_resign($id)
    {
        $res = $this->emitter('DELETE', "/personnel/api/resigns/" . $id . "/", null);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            return $res;
        } else {
            $res['msg'] = ['success' => 'Delete resign succesfully'];
            return $res;
        }
    }

    public function reinstatement($data)
    {
        $data = [
            'resigns' => $data['resigns'] ?? null,
        ];
        $res = $this->emitter('PUT', "/personnel/api/resigns/reinstatement/", $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            return $res;
        } else {
            $res['msg'] = ['success' => 'Reinstatement succesfully'];
            return $res;
        }
    }

    public function get_departments($data)
    {
        return config('constants.department');
        $data = [
            'page' => $data['page'] ?? null,
            'page_size' => $data['page_size'] ?? null,
            'dept_code' => $data['dept_code'] ?? null,
            'dept_name' => $data['dept_name'] ?? null,
            'dept_code_icontains' => $data['dept_code_icontains'] ?? null,
            'dept_name_icontains' => $data['dept_name_icontains'] ?? null,
            'department_icontains' => $data['department_icontains'] ?? null,
            'ordering' => $data['ordering'] ?? null,
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
        return config('constants.department_one');

        $res = $this->emitter('GET', "/personnel/api/departments/" . $id . "/", null);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            return $res;
        } else {
            return $res['data'];
        }
    }

    public function create_department($data)
    {
        $data = [
            'id' => null,
            'dept_code' => $data['dept_code'] ?? null,
            'dept_name' => $data['dept_name'] ?? null,
            'parent_dept' => $data['parent_dept'] ?? null,
        ];
        $res = $this->emitter('POST', "/personnel/api/departments/", $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            return $res;
        } else {
            $res['msg'] = ['success' => 'Add department succesfully'];
            return $res;
        }
    }

    public function update_department($data)
    {
        $data = [
            'id' => $data['id'],
            'dept_code' => $data['dept_code'],
            'dept_name' => $data['dept_name'],
            'parent_dept' => $data['parent_dept'] ?? null,
        ];
        $res = $this->emitter('PUT', "/personnel/api/departments/" . $data['id'] . '/', $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            return $res;
        } else {
            $res['msg'] = ['success' => 'Update department succesfully'];
            return $res;
        }
    }

    public function delete_department($id)
    {
        $res = $this->emitter('DELETE', "/personnel/api/departments/" . $id . "/", null);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            return $res;
        } else {
            $res['msg'] = ['success' => 'Delete department succesfully'];
            return $res;
        }
    }

    public function get_areas($data)
    {
        $data = [
            'page' => $data['page'] ?? null,
            'page_size' => $data['page_size'] ?? null,
            'area_code' => $data['area_code'] ?? null,
            'area_name' => $data['area_name'] ?? null,
            'area_code_icontains' => $data['area_code_icontains'] ?? null,
            'area_name_icontains' => $data['area_name_icontains'] ?? null,
            'ordering' => $data['ordering'] ?? null,
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
            'parent_area' => $data['parent_area'] ?? null,
        ];
        $res = $this->emitter('POST', "/personnel/api/areas/", $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            return $res;
        } else {
            $res['msg'] = ['success' => 'Update area succesfully'];
            return $res;
        }
    }

    public function update_area($data)
    {
        $data = [
            'id' => $data['id'],
            'area_code' => $data['area_code'],
            'area_name' => $data['area_name'],
            'parent_area' => $data['parent_area'] ?? null,
        ];
        $res = $this->emitter('PUT', "/personnel/api/areas/" . $data['id'] . '/', $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            return $res;
        } else {
            $res['msg'] = ['success' => 'Update area succesfully'];
            return $res;
        }
    }

    public function delete_area($id)
    {
        $res = $this->emitter('DELETE', "/personnel/api/areas/" . $id . "/", null);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            return $res;
        } else {
            $res['msg'] = ['success' => 'Delete area succesfully'];
            return $res;
        }
    }

    public function get_positions($data)
    {
        $data = [
            'page' => $data['page'] ?? null,
            'page_size' => $data['page_size'] ?? null,
            'position_code' => $data['position_code'] ?? null,
            'position_name' => $data['position_name'] ?? null,
            'position_code_icontains' => $data['position_code_icontains'] ?? null,
            'position_name_icontains' => $data['position_name_icontains'] ?? null,
            'ordering' => $data['ordering'] ?? null,
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
            'position_code' => $data['position_code'],
            'position_name' => $data['position_name'],
            'parent_position' => $data['parent_position'] ?? null,
        ];
        $res = $this->emitter('POST', "/personnel/api/positions/", $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            return $res;
        } else {
            $res['msg'] = ['success' => 'Add position succesfully'];
            return $res;
        }
    }

    public function update_position($data)
    {
        $data = [
            'id' => $data['id'],
            'position_code' => $data['position_code'],
            'position_name' => $data['position_name'],
            'parent_position' => $data['parent_position'] ?? null,
        ];
        $res = $this->emitter('PUT', "/personnel/api/positions/" . $data['id'] . '/', $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            return $res;
        } else {
            $res['msg'] = ['success' => 'Update position succesfully'];
            return $res;
        }
    }

    public function delete_position($id)
    {
        $res = $this->emitter('DELETE', "/personnel/api/positions/" . $id . "/", null);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            return $res;
        } else {
            $res['msg'] = ['success' => 'Delete position succesfully'];
            return $res;
        }
    }

    public function get_devices($data)
    {
        $data = [
            'page' => $data['page'] ?? null,
            'page_size' => $data['page_size'] ?? null,
            'sn' => $data['sn'] ?? null,
            'alias' => $data['alias'] ?? null,
            'state' => $data['state'] ?? null,
            'area' => $data['area'] ?? null,
            'sn_icontains' => $data['sn_icontains'] ?? null,
            'alias_icontains' => $data['alias_icontains'] ?? null,
            'ordering' => $data['ordering'] ?? null,
        ];
        $res = $this->emitter('GET', "/iclock/api/terminals/", $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }

    public function read_device($id)
    {
        $res = $this->emitter('GET', "/iclock/api/terminals/" . $id . "/", null);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            // throw new ResponseExeception($res['msg']);
        } else {
            return $res['data'];
        }
    }

    public function create_device($data)
    {
        $data = [
            'id' => null,
            'sn' => $data['sn'] ?? null,
            'alias' => $data['alias'] ?? null,
            'ip_address' => $data['ip_address'] ?? null,
            'area' => $data['area'] ?? null,
            'is_attendance' => $data['is_attendance'] ?? null,
            'terminal_tz' => $data['terminal_tz'] ?? null,
        ];
        $res = $this->emitter('POST', "/iclock/api/terminals/", $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            return $res;
        } else {
            $res['msg'] = ['success' => 'Update device succesfully'];
            return $res;
        }
    }

    public function update_device($data)
    {
        $data = [
            'id' => $data['id'],
            'sn' => $data['sn'] ?? null,
            'alias' => $data['alias'] ?? null,
            'ip_address' => $data['ip_address'] ?? null,
            'area' => $data['area'] ?? null,
            'is_attendance' => $data['is_attendance'] ?? null,
            'terminal_tz' => $data['terminal_tz'] ?? null,
        ];
        $res = $this->emitter('PUT', "/iclock/api/terminals/" . $data['id'] . '/', $data);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            return $res;
        } else {
            $res['msg'] = ['success' => 'Update device succesfully'];
            return $res;
        }
    }

    public function delete_device($id)
    {
        $res = $this->emitter('DELETE', "/iclock/api/terminals/" . $id . "/", null);
        if ($res['response'] < 200 || $res['response'] >= 300) {
            return $res;
        } else {
            $res['msg'] = ['success' => 'Delete area succesfully'];
            return $res;
        }
    }

    public function get_transactions($data)
    {
        $data = [
            'page' => $data['page'] ?? null,
            'page_size' => $data['page_size'] ?? null,
            'emp_code' => $data['emp_code'] ?? null,
            'terminal_sn' => $data['terminal_sn'] ?? null,
            'terminal_alias' => $data['terminal_alias'] ?? null,
            'start_time' => $data['start_time'] ?? null,
            'end_time' => $data['end_time'] ?? null,
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
