<?php

namespace App\Repositories;


use App\Models\Address;
use GuzzleHttp\Client;

class OSM
{

    /**
     * @var Client
     */
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://nominatim.openstreetmap.org/',
        ]);
    }

    /**
     * @param $lat
     * @param $lon
     * @return Address|bool
     */
    public function getAddress($lat, $lon)
    {
        $data = [
            'accept-language' => 'ru',
            'zoom'            => '18',
            'format'          => 'json',
            'addressdetails'  => '1',
            'countrycodes'    => 'ua',
            'lat'             => $lat,
            'lon'             => $lon
        ];

        $response = $this->client->get('reverse?'.http_build_query($data));

        $response = json_decode((string)$response->getBody());

        if (! $response->address) {
            return false;
        }

        return new Address([
            'street' => $response->address->road,
            'home'   => $response->address->house_number,
        ]);
    }


    /**
     * @param $address
     * @return bool|\Generator
     */
    public function validateAddress($address)
    {
        $data = [
            'accept-language' => 'ru',
            'zoom'            => 18,
            'format'          => 'json',
            'addressdetails'  => '1',
            'countrycodes'    => 'ua',
            'q'               => 'Киев,'.$address,
        ];

        $response = $this->client->get('search?'.http_build_query($data));

        $response = json_decode((string)$response->getBody());

        if (empty($response)) {
            return false;
        }

        foreach ($response as $item) {
            yield new Address([
                'street' => $item->address->road,
                'house'  => $item->address->house_number,
            ]);
        }
    }
}