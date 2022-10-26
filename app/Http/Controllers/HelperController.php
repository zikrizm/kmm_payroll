<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelperController extends Controller
{
    public function upload_csv()
    {
        $render =  view('partials.modals.upload_csv')->render();

        return $this->buildRes->RESPONSE_REQ('success', $render, null);
    }
}
