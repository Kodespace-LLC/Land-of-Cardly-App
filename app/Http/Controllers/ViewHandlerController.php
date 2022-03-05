<?php

namespace App\Http\Controllers;

use App\Adapter\Cardly;
use Illuminate\Http\Request;

class ViewHandlerController extends Controller
{
    public function homeview(){
        return view("index");
    }
    // public function testing(Cardly $cardly){
    //     $preview=$cardly->PreviewCard();
    //     return response()->json($preview);

    // }
    public function preview(Request $request,Cardly $cardly){
        $previewdata=[
            "message"=>$request->input('message'),
            "font"=>$request->input('style'),
            "verticalAlign"=>$request->input('valignment'),
            "color"=>str_replace("#","",$request->input('color')),
            "writing"=>$request->input('writing'),
            "size"=>$request->input('font'),
            "align"=>$request->input('align')
            
        ];
         $data=$cardly->PreviewCard($previewdata);
        return response()->json($data);

    }
}
