<?php

namespace App\Utils;

use Illuminate\Support\Facades\Log;

class ResponseUtil extends Util
{
    /**
     * Initializes the ResponseUtil.
     *
     * @return void
     */
    public function __construct()
    {
    }



    /**
     * Added default message response list
     *
     * @param string $status
     * @param int $code
     *
     * @return string
     */
    public function LIB_MSG_RESPONSE($status, $code)
    {
        $msg = '';
        if ($status == 'error') {
            switch ($code) {
                case 100:
                    $msg = 'Data not found';
                    break;
                case 101:
                    $msg = 'Email atau Password salah';
                    break;
                case 102:
                    $msg = 'Email sudah terdaftar';
                    break;
                case 103:
                    $msg = 'Your email has not been verified, please check your email';
                    break;
                case 104:
                    $msg = 'User not found';
                    break;
                case 105:
                    $msg = 'Email verification is failed';
                    break;
                case 106:
                    $msg = 'Please login to access this page';
                    break;
                case 107:
                    $msg = 'Token Invalid';
                    break;
                case 108:
                    $msg = 'Old password does not match';
                    break;
                case 109:
                    $msg = 'Headers is empty';
                    break;
                case 130:
                    $msg = 'Internet Connection Problem';
                    break;
                case 131:
                    $msg = 'Something wrong';
                default:
                    $msg = null;
            }
        } else {
            switch ($code) {
                case 100:
                    $msg = 'Login berhasil';
                    break;
                case 101:
                    $msg = 'add user successfully';
                    break;
                case 102:
                    $msg = 'update user successful';
                    break;
                case 103:
                    $msg = 'delete user successfully';
                    break;
                default:
                    $msg = null;
            }
        }

        return $msg;
    }

    /**
     * Build message response req
     *
     * @param string $status
     * @param collection $code
     * @param string $msg
     *
     * @return collection
     */
    public function RESPONSE_REQ($status, $data, $msg)
    {
        if ($status == 'error') {
            $e = $this->ERROR_RESPONSE($status, $data, $msg);
            return ['response' => $e->code, 'data' =>  $e->data, 'status' =>  $e->status, 'msg' =>  $e->msg];
        } else if ($status == 'unauthorized') {
            $e = $this->ERROR_RESPONSE($status, $data, $msg);
            return ['response' => 401, 'data' =>  $e->data, 'status' =>  $e->status, 'msg' =>  $e->msg];
        } else {
            $s = $this->SUCCESS_RESPONSE($status, $data, $msg);
            return ['response' => $s->code, 'data' => $s->data, 'status' =>  $s->status, 'msg' =>  $s->msg];
        }
    }

    /**
     * Build message response error
     *
     * @param string $status
     * @param collection $code
     * @param string $msg
     *
     * @return collection
     */
    public function ERROR_RESPONSE($status, $data, $msg)
    {
        return (object)['code' => 500, 'data' => $data, 'status' => $status, 'msg' => $msg];
    }

    /**
     * Build message response success
     *
     * @param string $status
     * @param collection $code
     * @param string $msg
     *
     * @return Obj
     */
    public function SUCCESS_RESPONSE($status, $data, $msg)
    {
        return (object)['code' => 200, 'data' => $data, 'status' => $status, 'msg' => $msg];
    }
}
