<?php

namespace App\Services\Api;

use Exception;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use App\Utils\ResponseUtil;
use App\Exceptions\ApiException;
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
        $this->api_zkteco = config('constants.api_zkteco');
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
    public function emitter($method, $url, $data)
    {
        try {
            // **
            // * HTTP CLIENT HEADER ----->
            // *

            $token = 'JWT ' . Session::get('token_zkteco');
            $headers = [
                'Content-Type' => 'application/json',
                'Authorization' => $token,
            ];
            Log::info($this->api_zkteco);
            if (strtolower($method) == 'get') {
                // **
                // * HTTP CLIENT GET ----->
                // *
                $res = Http::withHeaders($headers)->timeout(20)
                    ->get($this->api_zkteco . $url, $data);
            } else if (strtolower($method) == 'post') {
                // **
                // * HTTP CLIENT POST ----->
                // *
                $res = Http::withHeaders($headers)->timeout(20)
                    ->post($this->api_zkteco . $url, $data);
            } else if (strtolower($method) == 'put') {
                // **
                // * HTTP CLIENT PUT ----->
                // *
                $res = Http::withHeaders($headers)->timeout(20)
                    ->put($this->api_zkteco . $url, $data);
            } else if (strtolower($method) == 'delete') {
                // **
                // * HTTP CLIENT DELETE ----->
                // *
                $res = Http::withHeaders($headers)->timeout(20)
                    ->delete($this->api_zkteco . $url, $data);
            }

            // Log::info(Session::get('token_zkteco'));
            // Log::info($res->body());
            // Log::info($res->status());
            // Log::info($res->failed());

            $data = json_decode($res->body(), true);
            if ($res->failed()) {
                return $this->buildRes->RESPONSE_REQ('error', null, $data);
            }
            return $this->buildRes->RESPONSE_REQ('success', $data, null);
        } catch (ConnectionException $e) {
            // **
            // * HTTP CLIENT TIMEOUT ----->
            // *
            return $this->buildRes->RESPONSE_REQ('error', null, ['connection_timeout' => ['Not connected to bio time API']]);
        } catch (Exception $e) {
            $return_list = $this->buildRes->RESPONSE_REQ('error', null, ['something_wrong' => ['Something wrong']]);
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
            if ($res->getStatusCode() >= 300) {
                // is HTTP status code (for non-exceptions) 
                $statusCode = $res->getStatusCode();
                return $this->buildRes->RESPONSE_REQ('error', null, $data);
            } else {
                return $this->buildRes->RESPONSE_REQ('success', $data, null);
            }
        } catch (TooManyRedirectsException $e) {
            // handle too many redirects
        } catch (ClientException | ServerException $e) {
            // ClientException - A GuzzleHttp\Exception\ClientException is thrown for 400 level errors if the http_errors request option is set to true.
            // ServerException - A GuzzleHttp\Exception\ServerException is thrown for 500 level errors if the http_errors request option is set to true.
            if ($e->hasResponse()) {
                // is HTTP status code, e.g. 500 
                $statusCode = $e->getResponse()->getStatusCode();
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
}
