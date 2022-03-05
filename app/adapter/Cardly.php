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
public function PreviewCard ($data){
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
            "align"=>$data["align"],
            "color"=>$data["color"],
            "size"=> $data["size"],
            // "font"=>"227b649f-cb5c-3476-cdea-1e744ec5e70a-1",
            "writing"=> $data["writing"],
            "verticalAlign"=>$data["verticalAlign"]


        ],
        "variables"=>[
            "message"=>$data["message"],
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