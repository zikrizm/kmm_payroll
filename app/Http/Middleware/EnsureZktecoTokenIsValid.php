<?php

namespace App\Http\Middleware;

use Closure;
use App\Exceptions\ZktecoTokenExpiredException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class EnsureZktecoTokenIsValid
{
    /**
     * Reject the request when the BioTime JWT is missing or expired.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = Session::get('token_zkteco');

        if ($this->isInvalidToken($token)) {
            return ZktecoTokenExpiredException::logoutAndRespond($request);
        }

        return $next($request);
    }

    /**
     * Token is invalid when empty, malformed, or past its exp claim.
     * Tokens without exp are left to BioTime 401 handling.
     *
     * @param  mixed  $token
     * @return bool
     */
    private function isInvalidToken($token): bool
    {
        if (!is_string($token) || $token === '') {
            return true;
        }

        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return true;
        }

        $payload = json_decode($this->base64UrlDecode($parts[1]), true);
        if (!is_array($payload)) {
            return true;
        }

        if (!isset($payload['exp'])) {
            return false;
        }

        return (int) $payload['exp'] <= time();
    }

    private function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        $decoded = base64_decode(strtr($data, '-_', '+/'), true);

        return $decoded === false ? '' : $decoded;
    }
}
