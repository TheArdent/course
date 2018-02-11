<?php

namespace App\Http\Conversations;

use App\Models\Address;
use App\Repositories\Taxi;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;
use GuzzleHttp\Client;

class GetAddressConversation extends Conversation
{

    public $from_street = null;
    public $from_number = null;
    public $to_street   = null;
    public $to_number   = null;

    public function askFromLocation()
    {
        $keyboard = Keyboard::create(Keyboard::TYPE_KEYBOARD)->addRow(
            KeyboardButton::create('Send Location')->requestLocation()
        );

        $this->ask('От куда?', function (Answer $answer) {
//            $osm = app('osm');
            $osm = app('visicom');
            $this->say('Шукаю');

            if (isset($answer->getMessage()->getPayload()['location'])) {
                $addr = $osm->getAddress($answer->getMessage()->getLocation()->getLatitude(),
                    $answer->getMessage()->getLocation()->getLongitude());
            } else {
                $addr = $osm->validateAddress($answer->getText());
            }

            if ($addr) {
                $keyboard      = Keyboard::create(Keyboard::TYPE_KEYBOARD)->oneTimeKeyboard();
                $clear_address = [];

                foreach ($addr as $item) {
                    foreach (Taxi::researchAddress($item) as $street) {
                        if (array_search($item, $clear_address) === false) {
                            $clear_address[] = trim(preg_replace('/\s\s+/', '', $street));
                        }
                    }
                }

                foreach ($clear_address as $item) {
                    $keyboard->addRow(KeyboardButton::create($item)->callbackData($item));
                }
                $this->askFromNumber($keyboard);
            } else {
                $this->say('Невозможно определить адресс,введите его вручную или повторите попытку позже');
                $this->stopsConversation($answer->getMessage());
            }
        }, $keyboard->toArray());
    }

    protected function askFromNumber(Keyboard $keyboard)
    {
        $this->ask('Выберите адресс', function (Answer $answer) {
            $this->from_street = $answer->getText();

            $this->ask('Введите номер дома', function (Answer $answer) {
                $this->from_number = $answer->getText();

                $this->askToLocation();
            });
        }, $keyboard->toArray());
    }

    protected function askToNumber(Keyboard $keyboard)
    {
        $this->ask('Выберите адресс', function (Answer $answer) {
            $this->to_street = $answer->getText();

            $this->ask('Введите номер дома', function (Answer $answer) {
                $this->to_number = $answer->getText();

                $from = new Address([
                    'street' => $this->from_street,
                    'home'   => $this->from_number,
                ]);

                $to = new Address([
                    'street' => $this->to_street,
                    'home'   => $this->to_number,
                ]);


                $client = new Client([
                    'base_uri' => 'http://rainbow.evos.in.ua/',
                ]);

                $body = Taxi::getBody($from, $to);

                $prices = [];


                /** @var \App\Repositories\TaxiInterface $item */
                foreach (Taxi::getTaxiCompanies() as $item) {
                    $request = $item->getRequest();

                    $response = $client->send($request, [
                        'form_params' => $body,
                    ]);

                    $html = (string)$response->getBody();

                    $start_pos = strpos($html, '<span id="dCostBlock">');

                    if (strpos($html, '<span id="dCostBlock">') !== false) {
                        $span_start = $start_pos + 22;
                        $span_end   = strpos($html, '</span>', $span_start) - 8;

                        $price = intval(substr($html, $span_start, $span_end - $span_start));

                        $prices[$item->name] = $price;
                    }
                }

                if (! empty($prices)) {
                    asort($prices);
                    $min     = reset($prices);
                    $company = key($prices);

                    $this->say('Минимальная стоимость '.$min.'грн, компания - '.$company);
                } else {
                    $this->say('По Вашему запросу такси не найденно');
                }
            });
        }, $keyboard->toArray());
    }

    public function askToLocation()
    {
        $this->ask('Куда едем?', function (Answer $answer) {
//            $osm = app('osm');
            $osm = app('visicom');
            $this->say('Шукаю');

            $addr = $osm->validateAddress($answer->getText());

            if ($addr) {
                $keyboard      = Keyboard::create(Keyboard::TYPE_KEYBOARD)->oneTimeKeyboard();
                $clear_address = [];

                foreach ($addr as $item) {
                    foreach (Taxi::researchAddress($item) as $street) {
                        if (array_search($item, $clear_address) === false) {
                            $clear_address[] = trim(preg_replace('/\s\s+/', '', $street));
                        }
                    }
                }

                foreach ($clear_address as $item) {
                    $keyboard->addRow(KeyboardButton::create($item)->callbackData($item));
                }
                $this->askToNumber($keyboard);
            } else {
                $this->say('Невозможно определить адресс,введите его вручную или повторите попытку позже');
                $this->stopsConversation($answer->getMessage());
            }
        });
    }

    /**
     * @return mixed
     */
    public function run()
    {
        $this->askFromLocation();
    }
}