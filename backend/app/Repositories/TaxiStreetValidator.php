<?php

namespace App\Repositories;


use App\Models\Address;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

class TaxiStreetValidator
{
    static public function researchAddress($address)
    {
        $client = new Client();
        $response = $client->request('GET','http://rainbow.evos.in.ua/uk-UA/adfe0530-4bd0-4ac2-98bd-db25ef337af4/Address/Find?q=' . $address . '&limit=5');

        $response = (string)$response->getBody();

        if (strpos($response,"<span disabled='disabled'>") !== false)
            return false;

        $response = array_filter(explode(PHP_EOL, $response));

        return $response;
    }
}