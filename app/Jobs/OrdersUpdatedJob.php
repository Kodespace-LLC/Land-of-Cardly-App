<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Osiset\ShopifyApp\Objects\Values\ShopDomain;
use App\Adapter\Cardly;
use App\Models\Order;
use App\Models\User;
use stdClass;

class OrdersUpdatedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Shop's myshopify domain
     *
     * @var ShopDomain|string
     */
    public $shopDomain;
    public $shop;

    /**
     * The webhook data
     *
     * @var object
     */
    public $data;

    /**
     * Create a new job instance.
     *
     * @param string   $shopDomain The shop's myshopify domain.
     * @param stdClass $data       The webhook data (JSON decoded).
     *
     * @return void
     */
    public function __construct($shopDomain, $data)
    {
        $this->shopDomain = $shopDomain;
        $this->shop = User::Where('name', $shopDomain)->firstorFail();
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Cardly $cardly)
    {
        $this->shopDomain = ShopDomain::fromNative($this->shopDomain);
        $shop = $this->shop;
        $order_data = $this->data;
        $order = Order::where('shopify_id', $order_data->order_number)->first();
        if ($order) {
            if ($order->processed) {
                return;
            } else {
                $order = new Order;
                $order->shopify_id = $order_data->id;
                $order->order_number = $order_data->order_number;
                $order->save();
            }
        }
        $quantity = null;
        $bulkaddress = false;
        $savefile = null;
        $writingstyle=[
                        [
                            "id"=>"13a31bc8-e872-73c9-150d-5eda2654cbc6",
                            "name"=>"Perfection"
                        ],
                        [
                            "id"=>"f9d06541-8a42-3a87-e101-037b2e1632ba",
                            "name"=>"Neat"
                        ],
                        [
                            "id"=>"987d50c7-25e1-3b1c-aa78-6f60b1411fe5",
                            "name"=>"Average"
                        ],
                        [
                            "id"=>"e1792ebb-3063-9ce3-9c66-fc9b6e4e413c",
                            "name"=>"Messy"
                        ],
                        [
                            "id"=>"598b756f-0801-6a29-e8f3-4caf1b0242a5",
                            "name"=>"Dark"
                        ],
        ];
        $fontstyle = [
            [
                "id" => "27ad2d0d-3394-127f-9b99-c5c625a549b1",
                "name" => "Annie",

            ],
            [
                "id" => "9a30db69-97f3-bcdd-0015-c2c8c14aa2b9",
                "name" => "Aurora",

            ],
            [
                "id" => "9a30db69-97f3-bcdd-0015-c2c8c14aa2b9",
                "name" => "Aurora",

            ],
            [
                "id"=> "eb32b4ea-cc66-38e9-cd1f-60c481adb3c4",
                "name"=> "Benny",
            ],
            [
                "id"=> "90e7f5e9-7335-0e58-92e1-500fbabbc6c4",
                "name"=> "Cammy",

            ],
            [
                "id"=> "cba8c15d-3786-0b90-cdbc-a604190247c0",
                "name"=> "Caudex",

            ],
            [
                "id"=> "1db8e103-1ee3-21c5-1465-d6bfe5d50117",
                "name"=> "Dawn",

            ],
            [
                "id"=> "3c30f469-1c4b-dc5a-b326-bfcf1ed52cb0",
                "name"=> "Duean",
            ],
            [
                "id"=> "6e0b1e32-acfc-a661-bd6e-260e21a05b2b",
                "name"=> "Farah",

            ],
            [
                "id"=> "d95fd8ec-9682-3dcf-9ce4-2c28d75708e8",
                "name"=> "Fell English",
            ],
            [
                "id"=> "7174de61-5bd6-dcb6-8d95-a29ce5faeafa",
                "name"=> "Grace",
            ],
            [
                "id"=> "9caa0adc-fe12-ad82-5e47-9d68ab16ce64",
                "name"=> "Hahmlet",

            ],
            [
                "id"=> "0f1c630e-11a1-3069-c0d4-0ae23e1ad93e",
                "name"=> "Hans",

            ],
            [
                "id"=> "227b649f-cb5c-3476-cdea-1e744ec5e70a",
                "name"=> "Jenna",

            ],
            [
                "id"=> "cf092aae-6681-5852-1cb7-dc6ea6aaed1a",
                "name"=> "Kosugi Maru",

            ],
            [
                "id"=> "6212a600-bdaf-0856-cf14-68dd47fbf389",
                "name"=> "Lewis",

            ],
            [
                "id"=> "5f25b37f-ff41-c026-02c7-228bd9e5e63a",
                "name"=> "Merriweather",
            ],
            [
                "id"=> "bd0f7b6a-f175-482c-33a5-83b3d3fc4b1f",
                "name"=> "Montserrat",

            ],
            [
                "id"=> "e0197e08-f822-fa5a-aeb7-4d4afbf0cfcc",
                "name"=> "Morgan",

            ],
            [
                "id"=> "073d8bd5-0b54-b35d-daf0-cc4f3a274fb6",
                "name"=> "Newsreader",

            ],
            [
                 "id"=> "bc912ebc-52fc-18d1-1564-8774e9561219",
            "name"=> "Open Sans",

            ],
            [
                "id"=> "53a6accc-4696-d9cd-e501-d7c2f613284c",
                "name"=> "Rajdhani",

            ],
            [
                "id"=> "9c72453c-2692-083e-ace5-390b708edbac",
                "name"=> "Roboto",

            ],
            [
                "id"=> "917c8df6-a9dd-9500-a84a-36161db90b7a",
                "name"=> "Roboto Mono",

            ],
            [
                "id"=> "09f02071-d90d-1330-77ac-bbe8563ddb63",
                "name"=> "Rosie",

            ],
            [
                "id"=> "ccc21871-4a68-a1f2-b4f8-2b8b600037a1",
                "name"=> "Sara",

            ],


        ];
        // \Log::debug($bulkaddress);
        // \Log::debug("Processing order", [$this->data]);
        // \Log::debug("Line items in the order", $order_data->line_items);
        foreach ($order_data->line_items as $line_item) {
            $cardcustomdata = [];
            foreach ($line_item->properties as $property) {
                if ($property->name == "message") {
                    $cardcustomdata["message"] = $property->value;
                }
                if ($property->name == "message-left") {
                    $cardcustomdata["leftPageText"] = $property->value;
                }
                if ($property->name == "align") {
                    $cardcustomdata["align"] = $property->value;
                }
                if ($property->name == "font") {
                    $fontsyler=$property->value;
                    foreach($fontstyle as $id )
                    {
                        if($id["name"]==$fontsyler)
                        {
                            $cardcustomdata["font"] = $id["id"];

                        }
                    }
                    
                }
                if ($property->name == "writing") {
                    $writingname= $property->value;
                    foreach($writingstyle as $id){
                        if($id["name"]==$writingname){
                            $cardcustomdata["writing"] =$id["id"];
                        }

                    }
                }
                if ($property->name == "size") {
                    $cardcustomdata["size"] = (int)$property->value;
                }
                if ($property->name == "v-alignment") {
                    $cardcustomdata["v-alignment"] = $property->value;
                }
                if ($property->name == "color") {
                    $cardcustomdata["color"] = str_replace("#", "", $property->value);
                }
                if ($property->name == "filename") {
                    $bulkaddress = true;
                    $savefile = $property->value;
                }
            }
            $path = "uploads";
            \Log::debug($cardcustomdata);
            if ($bulkaddress) {
                function readcsv($path, $savefile)
                {
                    $recipientdata = [];
                    if (($open = fopen(public_path() . "/" . $path . "/" . $savefile, "r")) !== FALSE) {
                        while (($data = fgetcsv($open, 1000, ",")) !== FALSE) {
                            $recipientdata[] = [
                                "firstName" => $data[0],
                                "lastName" => $data[1],
                                "address" => $data[2],
                                "address2" => $data[3],
                                "city" => $data[4],
                                "region" => $data[5],
                                "postcode" => $data[6],
                                "country" => $data[7],
                            ];
                        }
                        fclose($open);
                    }

                    return ($recipientdata);
                }
                $csvdata = readcsv($path, $savefile);
            }
            // \Log::debug($csvdata);
            $quantity = $line_item->quantity;
            // \Log::debug($quantity);
            \Log::debug("cardcustomdata is", [$cardcustomdata]);
            // \Log::debug("line item name" . $line_item->name);
            // \Log::debug("line items", [$line_item]);
            $product_id = $line_item->product_id;
            $res = $shop->api()->rest('GET', "/admin/products/$product_id/metafields.json");
            $artwork_id = null;
            $template = null;
            foreach ($res["body"]["metafields"] as $m) {
                if ($m["namespace"] == "kodespace" && $m["key"] == "artwork_id") {
                    $artwork_id = $m["value"];
                }
                if ($m["namespace"] == "kodespace" && $m["key"] == "template_id") {
                    $template = $m["value"];
                }
            }
            if ($bulkaddress) {
                foreach ($csvdata as $csvdata) {
                    $recipient = [
                        "firstName" => $csvdata['firstName'],
                        "lastName" => $csvdata['lastName'],
                        "address" => $csvdata['address'],
                        "address2" => $csvdata['address2'],
                        "city" => $csvdata['city'],
                        "region" => $csvdata['region'],
                        "postcode" => $csvdata['postcode'],
                        "country" =>  $csvdata['country']
                    ];
                    \Log::debug([$recipient]);
                    $cardly->SendCard($artwork_id, $template, $recipient, $quantity, $cardcustomdata);
                }
            } elseif (!$bulkaddress) {
                $rec = $order_data->shipping_address;
                $recipient = [

                    "firstName" =>  $rec->first_name,
                    "lastName" => $rec->last_name,
                    "address" => $rec->address1,
                    "address2" => $rec->address2,
                    "city" => $rec->city,
                    "region" => $rec->province,
                    "postcode" => $rec->zip,
                    "country" => $rec->country_code
                ];
                $cardly->SendCard($artwork_id, $template, $recipient, $quantity, $cardcustomdata);
            }

            //  \Log::debug([$template]);
            //  \Log::debug([$artwork_id]);


            // $cardly->PreviewCard($artwork_id,$message,$recipient,$template);



        }
    }
}
