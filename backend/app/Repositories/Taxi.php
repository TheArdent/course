<?php

namespace App\Repositories;

use App\Models\Address;
use GuzzleHttp\Client;

class Taxi
{

    /**
     * @param $address
     * @return array|bool|mixed|\Psr\Http\Message\ResponseInterface|string
     */
    static public function researchAddress($address)
    {
        $client   = new Client();
        $response = $client->request('GET',
            'http://rainbow.evos.in.ua/uk-UA/adfe0530-4bd0-4ac2-98bd-db25ef337af4/Address/Find?q='.$address.'&limit=5');

        $response = (string)$response->getBody();

        if (strpos($response, "<span disabled='disabled'>") !== false) {
            return false;
        }

        $response = array_filter(explode(PHP_EOL, $response));

        return $response;
    }

    /**
     * @return array
     */
    static public function getTaxiCompanies()
    {
        return [
            new DeluxTaxi,
            new Taxi838,
        ];
    }

    /**
     * @param Address $from
     * @param Address $to
     * @return array
     */
    static public function getBody(Address $from, Address $to)
    {
        return [
            'LocationFrom.Address'         => $from->street,
            'LocationFrom.AddressNumber'   => $from->home,
            'LocationFrom.Entrance'        => '',
            'LocationFrom.IsStreet'        => 'True',
            'LocationFrom.Comment'         => '',
            'IsRouteUndefined'             => 'false',
            'LocationsTo[0].Address'       => $to->street,
            'LocationsTo[0].AddressNumber' => $to->home,
            'LocationsTo[0].IsStreet'      => 'True',
            'ReservationType'              => 'None',
            'ReservationDate'              => '',
            'ReservationTime'              => '',
            'IsWagon'                      => 'false',
            'IsMinibus'                    => 'false',
            'IsPremium'                    => 'false',
            'IsConditioner'                => 'false',
            'IsBaggage'                    => 'false',
            'IsAnimal'                     => 'false',
            'IsCourierDelivery'            => 'false',
            'IsReceipt'                    => 'false',
            'UserFullName'                 => '',
            'UserPhone'                    => '',
            'AdditionalCost'               => '',
            'OrderUid'                     => '',
            'Cost'                         => '',
            'UserBonuses'                  => '',
            'calcCostInProgress'           => 'False',
            'IsPayBonuses'                 => 'False',
        ];
    }
}