<?php

namespace App\Http\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;
use Log;

class GetAddressConversation extends Conversation
{

    public function askLocation()
    {
        $keyboard = Keyboard::create(Keyboard::TYPE_KEYBOARD)->addRow(
            KeyboardButton::create('Send Location')->requestLocation()
        );

        $this->ask('ОТ куда?', function (Answer $answer) {
            $osm = app('osm');
            $this->say('Шукаю');

            if (isset($answer->getMessage()->getPayload()['location'])) {
                $addr = $osm->getAddress($answer->getMessage()->getLocation()->getLatitude(),
                    $answer->getMessage()->getLocation()->getLongitude());

                if ($addr) {
                    foreach ($addr as $item) {
                        $this->say($item);
                    }
                } else {
                    $this->say('Невозможно определить адресс,введите его вручную или повторите попытку позже');
                }
            } else {
                $addr = $osm->validateAddress($answer->getText());

                if ($addr) {
                    foreach ($addr as $item) {
                        $this->say($item);
                    }
                } else {
                    $this->say('Невозможно определить адресс,введите его вручную или повторите попытку позже');
                }
            }

//            Log::info($answer->getValue());
//            Log::info($answer->getMessage()->getLocation()->getLongitude());
//            Log::info($answer->getMessage()->getLocation()->getLatitude());
//            Log::info('payload', json_encode($answer->getMessage()->getPayload()));
        }, $keyboard->toArray());
    }

    /**
     * @return mixed
     */
    public function run()
    {
        $this->askLocation();
    }
}