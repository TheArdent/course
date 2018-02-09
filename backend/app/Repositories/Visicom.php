<?php

namespace App\Repositories;


use App\Models\Address;
use GuzzleHttp\Client;

class Visicom
{

    /**
     * @var Client
     */
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'http://api.visicom.ua/',
        ]);
    }

    /**
     * @param $lat
     * @param $lon
     * @return array|bool
     */
    public function getAddress($lat, $lon)
    {
        $data = [
            'key'     => env('VISICOM_KEY'),
            'near'    => $lon.','.$lat,
            'country' => 'UA',
            'limit'   => 5,
            'radius'  => 20
        ];

        try {
            $response = $this->client->get('data-api/3.0/ru/search/adr_address.json?'.http_build_query($data));
            $response = json_decode((string)$response->getBody());

            return $this->decodeResponse($response);
        } catch (\Exception $e) {
            \Log::error('Failed to get address from Visicom : '.$e->getMessage());

            return false;
        }
    }


    /**
     * @param $address
     * @return array|bool
     */
    public function validateAddress($address)
    {
        $data = [
            'text'    => $address,
            'limit'   => 5,
            'country' => 'UA',
            'key'     => env('VISICOM_KEY'),
        ];

        try {
            $response = $this->client->get('data-api/3.0/ru/search/adr_address.json?'.http_build_query($data));;

            $response = json_decode((string)$response->getBody());

            if (empty($response)) {
                return false;
            }

            return $this->decodeResponse($response);
        } catch (\Exception $e) {
            \Log::error('Failed to validate address from Visicom : '.$e->getMessage());

            return false;
        }
    }

    /**
     * @param \stdClass $response
     * @return array|bool
     */
    protected function decodeResponse(\stdClass $response)
    {
        if (is_string($response))
            $response = json_decode($response);

        if (strcasecmp($response->type, 'Feature') === 0) {
            return [$response->properties->street];
        }
        if (strcasecmp($response->type, 'FeatureCollection') === 0) {
            $data = [];
            foreach ($response->features as $item) {
                if (isset($item->properties->street) && strcasecmp('Киев',
                        $item->properties->settlement) === 0 && array_search($item->properties->street,
                        $data) === false) {
                    $data[] = $item->properties->street;
                }
            }

            return $data;
        }

        return false;
    }
}