<?php

namespace App\Http\Controllers;

use App\Http\Conversations\GetAddressConversation;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Cache\RedisCache;
use BotMan\BotMan\Drivers\DriverManager;

class TelegramController extends Controller
{

    public function index()
    {
        DriverManager::loadDriver(\BotMan\Drivers\Telegram\TelegramDriver::class);


// Create an instance
        $botman = app('botman');

//        $botman->hears('/start', function (BotMan $bot) {
//            $bot->reply('Добро пожаловать в TelegramTaxiBot');
//        });

// Give the bot something to listen for.
        $botman->hears('Hi', function (BotMan $bot) {
            $bot->startConversation(new GetAddressConversation());
        });

// Start listening
        $botman->listen();
    }
}