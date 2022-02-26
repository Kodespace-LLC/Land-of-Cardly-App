<?php
namespace App\adapter;
use Illuminate\Support\Facades\Http;

class Cardly{
    private $api_key;
    public function _construct($apikey){
        $this->api_key=$apikey;
    }

}