<?php

namespace App\Repositories;


use App\Models\Address;

interface TaxiInterface
{

    /**
     * TaxiInterface constructor.
     *
     * @param Address $from
     * @param Address $to
     */
	public function __construct(Address $from, Address $to);

	public function sendRequest();

	public function parseRespond();

	public function getCost();
}