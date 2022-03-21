<?php

namespace App\Http\Controllers;

use App\Adapter\Cardly;
use Illuminate\Http\Request;

class ViewHandlerController extends Controller
{
    public function homeview()
    {
        return view("index");
    }
    // public function testing(Cardly $cardly){
    //     $preview=$cardly->PreviewCard();
    //     return response()->json($preview);

    // }
    public function preview(Request $request, Cardly $cardly)
    {
        $previewdata = [
            "message" => $request->input('message'),
            "font" => $request->input('font'),
            "verticalAlign" => $request->input('valignment'),
            "color" => str_replace("#", "", $request->input('color')),
            "writing" => $request->input('writing'),
            "size" => $request->input('size'),
            "align" => $request->input('align'),
            "writing" => $request->input('writing')

        ];
        $data = $cardly->PreviewCard($previewdata);
        return response()->json($data);
    }
    public function getfont(Cardly $cardly)
    {
        $data = $cardly->fontformat();
        return response($data);
    }
    public function getwriting(Cardly $cardly)
    {
        $write = $cardly->writingstyle();
        return response($write);
    }
    public function csvfile(Request $request)
    {
        
            $file = $request->file('csv');
            // \Log::debug($file);
            $savefile= $file->getClientOriginalName();
            $path="uploads";
            // now you have access to the file being uploaded
            //perform the upload operation.
            $file->move($path,$savefile);
           $csvdata= $this->readcsv($path,$savefile);
           \Log::debug($csvdata);
           if($csvdata){
               unlink(public_path($path."/".$savefile));
           }
           return response()->json($csvdata);
            
    }
    public function readcsv($path,$savefile){
        $emails=[];
        if(($open=fopen(public_path()."/".$path."/".$savefile,"r"))!==FALSE){
            while(($data=fgetcsv($open,1000,","))!==FALSE){
                $emails[]=$data;
                

            }
            fclose($open);
        }
       
        return($emails);
    }
}
