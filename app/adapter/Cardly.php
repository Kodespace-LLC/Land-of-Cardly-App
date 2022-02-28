<?php
namespace App\Adapter;
use Illuminate\Support\Facades\Http;

class Cardly{
    private $api_key;
    public function __construct($apikey){
        $this->api_key=$apikey;
    }
public function SendCard($artwork_id,$message,$recipient,$quantity){
    $line_items=[
        "artwork"=>$artwork_id,
        "quantity"=>$quantity,
        "recipient"=>$recipient,
        "variables" => [
            
            "message" => $message
        ],
    ];
    \Log::debug($this->api_key);
    $send=Http::withHeaders([
        'API-Key'=>$this->api_key

    ])->post('https://api.card.ly/v2/orders/place',[
        "lines"=>[$line_items]
    ]);
    $response=$send->json();
    \Log::debug([$response]);

}
}