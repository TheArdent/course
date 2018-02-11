<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Repositories\Taxi;
use GuzzleHttp\Client;

Route::get('/', function () {

//    $t = \App\Repositories\TaxiStreetValidator::researchAddress('Старокиевская');
//
//    $clear_address = [];
//
//    $addr = [
//        'Старокиевская'
//    ];
//
//    foreach ($addr as $item) {
//        foreach (TaxiStreetValidator::researchAddress($item) as $street) {
//            if (array_search($item,$clear_address) === false)
//                $clear_address[] = trim(preg_replace('/\s\s+/', '', $street));
//        }
//    }

//    dd($clear_address);


//    $v = new \App\Repositories\Visicom();
//    dd($v->validateAddress('Старокиевская 9'));
//    dd($v->getAddress(50.452736, 30.472681));


    //OSM
//    $address = 'Старокиевская 1';
//
//    $osm = app('osm');
//    $addr = $osm->validateAddress($address);
//
//
//    dd($addr);
//    $taxi_address = [];
//
//    foreach ($addr as $item){
//        $taxi_address = array_merge($taxi_address,Taxi838::researchAddress(explode(' ',$item)[0]));
//    }
//
//    //BOT
//    $from = new \App\Models\Address([
//        'street' => 'СТАРОКИЕВСКАЯ УЛ.',
//        'home'   => 9
//    ]);
//
//
//    $to = new \App\Models\Address([
//        'street' => 'ПЕРОВА БУЛЬВ.',
//        'home'   => 3
//    ]);
//
//    $client = new Client([
//        'base_uri' => 'http://rainbow.evos.in.ua/',
//    ]);
//
//    $body = Taxi::getBody($from, $to);
//
//    $prices = [];
//
//    /** @var \App\Repositories\TaxiInterface $item */
//    foreach (Taxi::getTaxiCompanies() as $item) {
//        $request = $item->getRequest();
//
//        $response = $client->send($request, [
//            'form_params' => $body,
//        ]);
//
//        $html = (string)$response->getBody();
//
//        $start_pos = strpos($html, '<span id="dCostBlock">');
//
//        if (strpos($html, '<span id="dCostBlock">') !== false) {
//            $span_start = $start_pos + 22;
//            $span_end   = strpos($html, '</span>', $span_start) - 8;
//
//            $price = intval(substr($html, $span_start, $span_end - $span_start));
//
//            $prices[$item->name] = $price;
//        }
//    }
//
//    asort($prices);
//
//    $min = reset($prices);
//    $company = key($prices);
//
//    dd($min,$company);

});

Route::post('/telegram', 'TelegramController@index');