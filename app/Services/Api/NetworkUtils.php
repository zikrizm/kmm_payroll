<?php

namespace App\Services\Api;

use Exception;
use GuzzleHttp\Client;
use App\Utils\ResponseUtil;
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Auth\AuthenticationException;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Client\ConnectionException;
use Guzzle\Http\Exception\ClientErrorResponseException;

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


            if (strtolower($method) == 'get') {
                // **
                // * HTTP CLIENT GET ----->
                // *
                $res = Http::withHeaders($headers)->timeout(2)
                    ->get($this->api_zkteco . $url, $data);
            } else if (strtolower($method) == 'post') {
                // **
                // * HTTP CLIENT POST ----->
                // *
                $res = Http::withHeaders($headers)->timeout(2)
                    ->post($this->api_zkteco . $url, $data);
            }

            Log::info(Session::get('token_zkteco'));
            Log::info($res->body());
            Log::info($res->status());
            Log::info($res->failed());

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
     * @param string $METHOD
     * @param string $URL
     * @param collection $data
     *
     * @return
     */
    // public function emitter($METHOD, $URL, $data)
    // {
    //     try {
    //     $client = new Client();
    //     $req = $client->request('POST', 'http://192.168.2.20:80/jwt-api-token-auth/', [
    //         'request.options' =>  ['timeout' => 1, 'connect_timeout' => 1],
    //         'headers' => [
    //             'Content-Type' => 'application/json',
    //             // 'Authorization' => 'JWT ' . Session::get('token_zkteco'),
    //         ],
    //         'form_params' => [
    //             'username' => 'admin',
    //             'password' => 'ciptakanjuara123',
    //         ],
    //     ]);
    //     if ($req->getStatusCode() == 200) {
    //         $return_list = $this->buildRes->RESPONSE_REQ('success', json_decode($req->getBody(), true), null);
    //     }
    //     } catch (RequestException $e) {
    //         $status = $e->getResponse()->getStatusCode();
    //         $msg = [$status => [$e->getMessage()]];
    //         $return_list = $this->buildRes->RESPONSE_REQ('error', null, $msg);
    //     } catch (Exception $e) {
    //         $return_list = $this->buildRes->RESPONSE_REQ('error', null, ['something_wrong' => ['something_wrong']]);
    //     }

    //     return  $return_list;
    // }

    /**
     * Handle status code response
     *
     * @param Response $req
     *
     * @return 
     */
    public function handleStatusCode($req)
    {
        // if ($req->status() < 400) {
        //     return $this->buildRes->RESPONSE_REQ('success', $req->json(), null);
        // } else if ($req->status() < 400) {
        //     return $this->buildRes->RESPONSE_REQ('success', $req->json(), null);
        // } else {
        //     return $this->buildRes->RESPONSE_REQ('error', null, $req->json());
        // }
        // if ($status <= 400 || $status > 500) {}
        // $msg = '';
        // switch ($status) {
        //     case 404:
        //         $msg = '404 not found';
        //         break;
        //     case 401:
        //         throw new AuthenticationException();
        //         break;
        //     default:
        //         break;
        // }

        // if ($msg) throw new ApiException($msg);
    }
}
