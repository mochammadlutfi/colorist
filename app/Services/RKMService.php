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
        $token = settings()->get('rkm_access');
        if ($token) {
            return $token;
        }

        try {
            $response = $this->client->post('/api/token', [
                'json' => [
                    'username' => 'v_rajawalihiyoto',
                    'password' => 'GhjbnM9876##!!'
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            if (isset($data['access'])) {
                // Simpan token di cache selama 1 jam
                settings()->set('rkm_refresh', $data['refresh']);
                settings()->set('rkm_access', $data['access']);
                return $data['access'];
            }

            return null;
        } catch (RequestException $e) {
            return null;
        }
    }

    /**
     * Mengirim data ke API dengan token
     */
    public function sellOut(array $data)
    {
        return $this->sendRequest('GET', '/api/sellout', ['query' => $data]);
    }

    /**
     * Mengirim data ke API dengan token
     */
    public function sendData(array $data)
    {
        return $this->sendRequest('POST', '/api/transcolorant', ['json' => $data]);
    }

    /**
     * Fungsi umum untuk mengirim request dengan penanganan 401 Unauthenticated
     */
    private function sendRequest($method, $url, $options = [])
    {
        $token = $this->login();
        $options['headers'] = [
            'Authorization' => "Bearer $token",
            'Content-Type'  => 'application/json',
        ];

        try {
            $response = $this->client->request($method, $url, $options);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->hasResponse() && $e->getResponse()->getStatusCode() === 401) {
                // Jika 401, lakukan relogin dan coba lagi
                settings()->forget('rkm_access');
                $token = $this->login();
                $options['headers']['Authorization'] = "Bearer $token";
                
                $response = $this->client->request($method, $url, $options);
                return json_decode($response->getBody(), true);
            }
            
            return ['error' => 'Failed to send data', 'message' => $e->getMessage()];
        }
    }
}
