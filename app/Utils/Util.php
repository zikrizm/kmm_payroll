<?php

namespace App\Utils;

use Illuminate\Support\Carbon;

class Util
{
    /**
     * Initializes the Util.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Uploads document to the server if present in the request
     * @param obj $request, string $file_name, string dir_name
     *
     * @return string
     */
    public function uploadFile($request, $file_name, $dir_name, $file_type = 'document')
    {
        //If app environment is demo return null
        if (config('app.env') == 'demo') {
            return null;
        }

        $uploaded_file_name = null;
        if ($request->hasFile($file_name) && $request->file($file_name)->isValid()) {

            //Check if mime type is image
            if ($file_type == 'image') {
                if (strpos($request->$file_name->getClientMimeType(), 'image/') === false) {
                    throw new \Exception("Invalid image file");
                }
            }

            if ($file_type == 'document') {
                if (!in_array($request->$file_name->getClientMimeType(), array_keys(config('constants.document_upload_mimes_types')))) {
                    throw new \Exception("Invalid document file");
                }
            }

            if ($request->$file_name->getSize() <= config('constants.document_size_limit')) {
                $new_file_name = time() . '_' . $request->$file_name->getClientOriginalName();
                if ($request->$file_name->storeAs($dir_name, $new_file_name)) {
                    $uploaded_file_name = $new_file_name;
                }
            }
        }

        return $uploaded_file_name;
    }

    public function generateDateRange(Carbon $start_date, Carbon $end_date)
    {
        $dates = [];

        for ($date = $start_date->copy(); $date->lte($end_date); $date->addDay()) {
            $dates[] = $date->format('Y-m-d');
        }

        return $dates;
    }

    // function _group_by_date_and_sort($array, $key)
    // {
    //     $return = array();
    //     foreach ($array as $val) {
    //         $date = Carbon::parse($val[$key])->format('Y-m-d');
    //         if (!empty($date)) {
    //             $return[$date][] = $val;
    //             usort($return[$date], function ($a, $b) use ($key) {
    //                 return strtotime($a[$key]) - strtotime($b[$key]);
    //             });
    //         }
    //     }
    //     return $return;
    // }
}
