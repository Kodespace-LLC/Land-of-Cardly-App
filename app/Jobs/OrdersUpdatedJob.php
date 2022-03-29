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
                    $cardcustomdata["font"] = $property->value;
                }
                if ($property->name == "writing") {
                    $cardcustomdata["writing"] = $property->value;
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
            if($bulkaddress){
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
                    $cardly->SendCard($artwork_id,$template,$recipient,$quantity,$cardcustomdata);
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
                $cardly->SendCard($artwork_id,$template,$recipient,$quantity,$cardcustomdata);
            }
           
            //  \Log::debug([$template]);
            //  \Log::debug([$artwork_id]);

           
            // $cardly->PreviewCard($artwork_id,$message,$recipient,$template);



        }
    }
}
