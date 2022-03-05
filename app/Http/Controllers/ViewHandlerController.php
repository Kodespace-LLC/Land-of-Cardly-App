<?php

namespace App\Http\Controllers;

use App\Adapter\Cardly;
use Illuminate\Http\Request;

class ViewHandlerController extends Controller
{
    public function homeview(){
        return view("index");
    }
    public function testing(Cardly $cardly){
        $preview=$cardly->PreviewCard();
        return response()->json($preview);

    }
}
