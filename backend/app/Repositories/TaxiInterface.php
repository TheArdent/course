<?php

namespace App\Repositories;

use GuzzleHttp\Psr7\Request;

interface TaxiInterface
{

    /**
     * @return Request
     */
    public function getRequest();
}