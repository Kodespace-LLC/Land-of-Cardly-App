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
        \Log::debug("Processing order", [$this->data]);
        \Log::debug("Line items in the order", $order_data->line_items);
        foreach ($order_data->line_items as $line_item) {
            \Log::debug("line item name" . $line_item->name);
            \Log::debug("line items", [$line_item]);
            $product_id = $line_item->product_id;
            $res = $shop->api()->rest('GET', "/admin/products/$product_id/metafields.json");
            $artwork_id = "e12c32ee-7460-f3b5-d54f-395556d21a18";
            // foreach ($res["body"]["metafields"] as $m) {
            //     if ($m["namespace"] == "kodespace" && $m["key"] == "artwork_id") {
            //         $artwork_id = $m["value"];
            //     }
            // }
            $message="";
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
            $template="test-template-for-aakash";
            \Log::debug([$recipient]);
            $quantity=1;
            $cardly->SendCard($artwork_id,$message,$recipient,$quantity);
            // $cardly->PreviewCard($artwork_id,$message,$recipient,$template);



        }
    }
}
