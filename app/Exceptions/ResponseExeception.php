<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class ResponseExeception extends Exception
{
    public $message;
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Report the exception.
     *
     * @return bool|null
     */
    public function report()
    {
    }

    public function getMessages()
    {
        return $this->message;
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
    }
}
