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
    private $libRes;


    /**
     * Initializes the networkUtil.
     *
     * @return void
     */
    public function __construct(ResponseUtil $libRes)
    {
        $this->api_zkteco = 'http://192.168.1.5:80';
        $this->libRes = $libRes;
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
    public function emitter($METHOD, $URL, $data)
    {
        try {
            $client = new Client();
            $req = $client->request($METHOD, $this->api_zkteco . $URL, [
                'request.options' =>  ['timeout' => 1, 'connect_timeout' => 1],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'JWT ' . Session::get('token_zkteco'),
                ],
                'form_params' => $data,
            ]);
            if ($req->getStatusCode() == 200) {
                $return_list = $this->libRes->RESPONSE_REQ('success', json_decode($req->getBody(), true), null);
            }
        } catch (RequestException $e) {
            $status = $e->getResponse()->getStatusCode();
            $msg = [$status => [$e->getMessage()]];
            $return_list = $this->libRes->RESPONSE_REQ('error', null, $msg);
        } catch (Exception $e) {
            $return_list = $this->libRes->RESPONSE_REQ('error', null, ['something_wrong' => ['something_wrong']]);
        }

        return  $return_list;
    }

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
        //     return $this->libRes->RESPONSE_REQ('success', $req->json(), null);
        // } else if ($req->status() < 400) {
        //     return $this->libRes->RESPONSE_REQ('success', $req->json(), null);
        // } else {
        //     return $this->libRes->RESPONSE_REQ('error', null, $req->json());
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
