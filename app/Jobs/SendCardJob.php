<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Adapter\Cardly;

class SendCardJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
   public $artwork_id, $template, $recipient, $quantity, $cardcustomdata,$bulk;
    /**
     * Create a new job instance.
     *
     * @return void
     */
  
    public function __construct($artwork_id, $template, $recipient, $quantity, $cardcustomdata,$bulk=[])
    {
        $this->bulk=$bulk;
        if( sizeof($bulk) < 1 )  {
            $this->bulk = false;
        }
        $this->artwork_id=$artwork_id;
        $this->template=$template;
        $this->recipient= $recipient;
        $this->quantity=$quantity;
        $this->cardcustomdata=$cardcustomdata;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Cardly $cardly)
    {

        if($this->bulk){
            $cardcustomdata=$this->cardcustomdata;
            $card_sent = 0;

                foreach($this->bulk as $index=>$recp){
                    if( $card_sent >= $this->quantity) {
                        break;
                    }
                   $cardcustomdata["Recipient_Name"]=$recp["firstName"];
                    $cardly->SendCard($this->artwork_id, $this->template, $recp, $this->quantity, $this->cardcustomdata);
                    $card_sent++;
                }
        }
        else{
            \Log::debug($this->artwork_id);
        $cardly->SendCard($this->artwork_id, $this->template, $this->recipient, $this->quantity, $this->cardcustomdata);
        }
        //
    }
}
