<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ZktecoTokenExpiredException extends Exception
{
    public const MESSAGE = 'Sesi berakhir, silakan login kembali.';

    public function __construct(string $message = self::MESSAGE)
    {
        parent::__construct($message);
    }

    /**
     * End the payroll session so a swallowed exception still logs the user out.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public static function flushSession(Request $request): void
    {
        Auth::logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $request->session()->save();
        }
    }

    /**
     * Flush the local session and return a 401 JSON or login redirect.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $message
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public static function logoutAndRespond(Request $request, ?string $message = null)
    {
        $message = $message ?: self::MESSAGE;

        self::flushSession($request);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'unauthorized',
                'redirect' => '/login',
                'msg' => $message,
            ], 401);
        }

        return redirect('/login')->withErrors(['username' => $message]);
    }
}
