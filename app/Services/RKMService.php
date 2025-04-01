<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class RKMService
{
    protected $client;
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'https://dev.anyargroup.co.id'; // URL API dari .env
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout'  => 10.0, // Timeout request
            'headers'  => [
                'Accept' => 'application/json',
            ]
        ]);
    }

    /**
     * Login ke API untuk mendapatkan token
     */
    public function login()
    {
        // $token = settings()->get('rkm_access');
        // if($token){
        //     return $token;
        // }

        try {
            $response = $this->client->post('/api/token', [
                'json' => [
                    'username' => 'v_rajawalihiyoto',
                    'password' => 'GhjbnM9876##!!s'
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            dd($data);
            // if ($token) /{
                // Simpan token di cache selama 1 jam
                // settings()->set('rkm_refresh', $data['refresh']);
                // settings()->set('rkm_access', $data['access']);
            return $data['access'];
            // }

            // return null;
             
        } catch (RequestException $e) {
            dd($e);
            return null;
        }
    }

    
    /**
     * Mengirim data ke API dengan token
     */
    public function sellOut(array $data)
    {
        $token = $this->login();

        try {
            $response = $this->client->get('/api/sellout', [
                'headers' => [
                    'Authorization' => "Bearer $token",
                    'Content-Type'  => 'application/json',
                ],
                'query' => $data
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return ['error' => 'Failed to send data', 'message' => $e->getMessage()];
        }
    }

    /**
     * Mengirim data ke API dengan token
     */
    public function sendData(array $data)
    {
        $token = $this->login();
        dd($token);
        try {
            $response = $this->client->post('/api/transcolorant', [
                'headers' => [
                    'Authorization' => "Bearer $token",
                    'Content-Type'  => 'application/json',
                ],
                'json' => $data
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return ['error' => 'Failed to send data', 'message' => $e->getMessage()];
        }
    }
}
