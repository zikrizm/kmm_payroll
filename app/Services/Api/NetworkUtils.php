<?php

namespace App\Services\Api;

use Exception;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use App\Utils\ResponseUtil;
use App\Exceptions\ApiException;
use App\Exceptions\ZktecoTokenExpiredException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Auth\AuthenticationException;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Client\ConnectionException;
use GuzzleHttp\Exception\TooManyRedirectsException;


class NetworkUtils
{
    private $api_zkteco;
    private $buildRes;

    /**
     * Initializes the networkUtil.
     *
     * @return void
     */
    public function __construct(ResponseUtil $buildRes)
    {
        $this->api_zkteco = config('constants.api_zkteco_internal');
        $this->buildRes = $buildRes;
    }

    /**
     * Added default resource settings for network utilities
     *
     * @param string $method
     * @param string $url
     * @param array $data
     *
     * @return
     */
    public function emitter($method, $url, $data, $withAuth = true)
    {
        try {
            // **
            // * HTTP CLIENT HEADER ----->
            // *

            $headers = ['Content-Type' => 'application/json'];
            if ($withAuth && Session::get('token_zkteco')) {
                $headers['Authorization'] = 'JWT ' . Session::get('token_zkteco');
            }
            $requestUrl = $this->api_zkteco . $url;
            Log::info('[ZKTeco] Request', [
                'method' => $method,
                'url' => $requestUrl,
                'http_host' => request()->getHost(),
            ]);

            if (strtolower($method) == 'get') {
                // **
                // * HTTP CLIENT GET ----->
                // *
                $res = Http::withHeaders($headers)->timeout(20)
                    ->get($requestUrl, $data);
            } else if (strtolower($method) == 'post') {
                // **
                // * HTTP CLIENT POST ----->
                // *
                $res = Http::withHeaders($headers)->timeout(20)
                    ->post($requestUrl, $data);
            } else if (strtolower($method) == 'put') {
                // **
                // * HTTP CLIENT PUT ----->
                // *
                $res = Http::withHeaders($headers)->timeout(20)
                    ->put($requestUrl, $data);
            } else if (strtolower($method) == 'delete') {
                // **
                // * HTTP CLIENT DELETE ----->
                // *
                $res = Http::withHeaders($headers)->timeout(20)
                    ->delete($requestUrl, $data);
            } else {
                return $this->buildRes->RESPONSE_REQ('error', null, ['something_wrong' => ['Unsupported HTTP method']]);
            }

            // Log::info(Session::get('token_zkteco'));
            // Log::info($res->body());
            // Log::info($res->status());
            // Log::info($res->failed());

            $data = json_decode($res->body(), true);
            $this->rejectIfZktecoUnauthorized($res->status(), $data, $withAuth);
            if ($res->failed()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $data);
            }
            return $this->buildRes->RESPONSE_REQ('success', $data, null);
        } catch (ZktecoTokenExpiredException $e) {
            throw $e;
        } catch (ConnectionException $e) {
            // **
            // * HTTP CLIENT TIMEOUT ----->
            // *
            return $this->buildRes->RESPONSE_REQ('error', null, ['connection_timeout' => ['Not connected to bio time API']]);
        } catch (Exception $e) {
            Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());
            return $this->buildRes->RESPONSE_REQ('error', null, ['something_wrong' => ['Something wrong']]);
        }
    }

    /**
     * Added default resource settings for network utilities
     *
     * @param string $method
     * @param string $url
     * @param array $data
     *
     * @return
     */
    public function emitterGuzzle($method, $url, $data)
    {
        try {
            $client = new Client([
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'JWT ' . Session::get('token_zkteco'),
                ]
            ]);

            $options = ['multipart' => []];
            if (!empty($data)) {
                // ** Build data multipart.
                foreach ($data as $key => $value) {
                    if (request()->hasFile($key) && request()->file($key)->isValid()) {
                        $options['multipart'][] = [
                            'name'      => $key,
                            'image_path'=> $value->getPathname(),
                            'image_mime'=> $value->getmimeType(),
                            'image_org' => $value->getClientOriginalName(),
                            'contents'  => fopen($value->getPathname(), 'r'),
                        ];
                    } else {
                        $options['multipart'][] = [
                            'name'      => $key,
                            'contents'  => $value ?? null,
                        ];
                    }
                }
            }
            
            $request = new Psr7\Request($method, $this->api_zkteco . $url, ['Cookie' => 'csrftoken=' . $data['csrftoken']]);
            $res = $client->sendAsync($request, $options)->wait();

            // Log::info($res->getStatusCode());
            // Log::info($res->getBody());

            $data = json_decode($res->getBody(), true);
            $this->rejectIfZktecoUnauthorized($res->getStatusCode(), $data);
            if ($res->getStatusCode() >= 300) {
                // is HTTP status code (for non-exceptions) 
                $statusCode = $res->getStatusCode();
                return $this->buildRes->RESPONSE_REQ('error', null, $data);
            } else {
                return $this->buildRes->RESPONSE_REQ('success', $data, null);
            }
        } catch (ZktecoTokenExpiredException $e) {
            throw $e;
        } catch (TooManyRedirectsException $e) {
            // handle too many redirects
        } catch (ClientException | ServerException $e) {
            // ClientException - A GuzzleHttp\Exception\ClientException is thrown for 400 level errors if the http_errors request option is set to true.
            // ServerException - A GuzzleHttp\Exception\ServerException is thrown for 500 level errors if the http_errors request option is set to true.
            if ($e->hasResponse()) {
                // is HTTP status code, e.g. 500 
                $statusCode = $e->getResponse()->getStatusCode();
                $body = json_decode((string) $e->getResponse()->getBody(), true);
                $this->rejectIfZktecoUnauthorized($statusCode, $body);
            }
        } catch (ConnectException $e) {
            // ConnectException - A GuzzleHttp\Exception\ConnectException exception is thrown in the event of a networking error. This may be any libcurl error, including certificate problems
            $handlerContext = $e->getHandlerContext();
            if ($handlerContext['errno'] ?? 0) {
                // this is the libcurl error code, not the HTTP status code!!!
                // for example 6 for "Couldn't resolve host"
                $errno = (int)($handlerContext['errno']);
            }
            // get a description of the error (which will include a link to libcurl page)
            $errorMessage = $handlerContext['error'] ?? $e->getMessage();
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
        }
    }

    /**
     * Logout and abort when BioTime rejects the JWT.
     * Login token requests ($withAuth = false) are not treated as session expiry.
     *
     * @param  mixed  $status
     * @param  mixed  $body
     * @param  bool  $withAuth
     * @return void
     *
     * @throws \App\Exceptions\ZktecoTokenExpiredException
     */
    private function rejectIfZktecoUnauthorized($status, $body, bool $withAuth = true): void
    {
        if (!$this->isZktecoUnauthorized($status, $body, $withAuth)) {
            return;
        }

        ZktecoTokenExpiredException::flushSession(request());
        throw new ZktecoTokenExpiredException();
    }

    /**
     * @param  mixed  $status
     * @param  mixed  $body
     * @param  bool  $withAuth
     * @return bool
     */
    private function isZktecoUnauthorized($status, $body, bool $withAuth = true): bool
    {
        if (!$withAuth) {
            return false;
        }

        if ((int) $status === 401) {
            return true;
        }

        $haystack = '';
        if (is_array($body) || is_object($body)) {
            $haystack = json_encode($body) ?: '';
        } elseif (is_string($body)) {
            $haystack = $body;
        }

        $haystack = strtolower($haystack);

        return str_contains($haystack, 'signature has expired')
            || str_contains($haystack, 'token is expired')
            || str_contains($haystack, 'invalid token')
            || str_contains($haystack, 'authentication credentials were not provided');
    }
}
