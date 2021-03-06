<?php

namespace App\Adapter;

use Illuminate\Support\Facades\Http;

class Cardly
{
    private $api_key;
    public function __construct($apikey)
    {
        $this->api_key = $apikey;
    }
    public function SendCard($artwork_id, $template, $recipient, $quantity, $cardcustomdata)
    {
        $line_items = [
            "artwork" => $artwork_id,
            "template" => $template,
            "quantity" => $quantity,
            "recipient" => $recipient,
            "variables" => [

                "message" => $cardcustomdata["message"],
                "leftPageText" => $cardcustomdata["leftPageText"],
                "recipientname" => $cardcustomdata["Recipient_Name"],
                "greeting" => $cardcustomdata["greetingtext"]
            ],
            "style" => [
                "align" => $cardcustomdata["align"],
                "color" => $cardcustomdata["color"],
                "size" => $cardcustomdata["size"],
                "font" => $cardcustomdata["font"],
                "writing" => $cardcustomdata["writing"],
                "verticalAlign" => $cardcustomdata["v-alignment"]
            ],
        ];
        \Log::debug([$line_items]);
        $send = Http::withHeaders([
            'API-Key' => $this->api_key


        ])->post('https://api.card.ly/v2/orders/place', [
            "lines" => [$line_items]
        ]);
        $response = $send->json();
        \Log::debug([$response]);
    }
    public function PreviewCard($data)
    {
        $line_items = [
            "artwork" => $data["artwork_id"],
            "template" => $data["template_id"],
            "recipient" => [

                "firstName" =>  "Aakash",
                "lastName" => "Ahmed",
                "address" => "Wah Cantt",
                "address2" => "New City",
                "city" => "United states of wah",
                "region" => "NL",
                "postcode" => "a1a1a1",
                "country" => "CA"
            ],
            "style" => [
                "align" => $data["align"],
                "color" => $data["color"],
                "size" => (int) $data["size"],
                "font" => $data["font"],
                "writing" => $data["writing"],
                "verticalAlign" => $data["verticalAlign"]


            ],
            "variables" => [
                "message" => $data["message"],
                "leftPageText" => $data["leftmessage"],
                "greeting" => $data["greetingmessage"],
                "recipientname" => "aakash"


            ]

        ];
        // \Log::debug($data);
        $temp = Http::withHeaders([
            'API-Key' => $this->api_key
        ])->post('https://api.card.ly/v2/orders/preview', $line_items);
        $response = $temp->json();
        return ($response);
    }
    public function fontformat()
    {
        $font = Http::withHeaders([
            'API-Key' => $this->api_key
        ])->get('https://api.card.ly/v2/fonts');
        $response = $font->json();
        return ($response);
    }
    public function writingstyle()
    {
        $writingstyle = Http::withHeaders([
            'API-Key' => $this->api_key
        ])->get('https://api.card.ly/v2/writing-styles');
        $response = ($writingstyle)->json();
        return ($response);
    }
}
