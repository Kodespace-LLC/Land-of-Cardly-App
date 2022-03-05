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
public function PreviewCard (){
    $line_items=[
        "artwork"=>"e12c32ee-7460-f3b5-d54f-395556d21a18",
        "template"=>"test-template-for-aakash",
        "recipient"=>[
            
            "firstName" =>  "Aakash",
            "lastName" => "Ahmed",
            "address" => "Wah Cantt",
            "address2" => "New City",
            "city" => "United states of wah",
            "region" => "NL",
            "postcode" =>"a1a1a1",
            "country" => "CA"
        ],
        "style"=>[
            "align"=>"center",
            "color"=>"c20a69",
            "size"=>28,
            "verticalAlign"=>"bottom"


        ],
        "variables"=>[
            "message"=>"thisis a custom message for test on the right page",
            "leftPageText"=>"Cardly left page text test",
            "name"=>"land of cards"
        ]

        ];
        $temp=Http::withHeaders([
            'API-Key'=>$this->api_key
        ])->post('https://api.card.ly/v2/orders/preview',$line_items);
        $response=$temp->json();
        return($response);

}
}