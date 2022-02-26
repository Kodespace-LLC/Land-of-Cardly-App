<?php namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Osiset\ShopifyApp\Objects\Values\ShopDomain;
use App\Adapters\Cardly;
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
        $this->shop= User::Where('name',$shopDomain)->firstorFail();
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Cardly $cardly){
        $this->shopDomain=ShopDomain::fromNative($this->shopDomain);
        $shop=$this->shop;
        $order_data=$this->data;
       $order=Order::where('shopify_id',$order_data->order_number);
       if($order)
       {
           if($order->processed){
               return;
           }
           else{
               $order= new Order;
               $order->shopify_id=$order_data->id;
               $order->order_number=$order_data->order_number;
               $order->save();

           }
           
       }
    }
    \Log::debug("Processing order",[$this->data]);
    \Log::debug("Line items in the order",$order_data->line_items);
}
