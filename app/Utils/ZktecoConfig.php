<?php

namespace App\Utils;

use Rats\Zkteco\Lib\ZKTeco;
use Illuminate\Support\Facades\Log;
use Laradevsbd\Zkteco\Http\Library\ZktecoLib;

class ZktecoConfig
{

    public $zk;

    /**
     * Initializes the Zkteco.
     *
     * @return void
     */
    public function __construct()
    {
        // $zk = new ZktecoLib('192.168.2.111', 4370);
        // if ($zk->connect()) {
        //     $attendance = $zk->getAttendance();
        //     Log::info($attendance);
        // }

        // $zk = new ZKTeco('192.168.2.111');
        // $zk->connect();
        // $dm = $zk->getAttendance();
        // Log::info($dm);
    }
}
