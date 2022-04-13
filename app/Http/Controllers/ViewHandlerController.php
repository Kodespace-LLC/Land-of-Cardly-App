<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Jobs\SendCardJob;
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
            "leftmessage" => $request->input('leftmessage'),
            "font" => $request->input('font'),
            "verticalAlign" => $request->input('valignment'),
            "color" => str_replace("#", "", $request->input('color')),
            "writing" => $request->input('writing'),
            "size" => $request->input('size'),
            "align" => $request->input('align'),
            "writing" => $request->input('writing'),
            

        ];
        \Log::debug([$previewdata]);

        if( $request->has('recipient_name')) {
            $previewdata["recipient_name"] = $request->input("recipientname");
        }
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
        $savefile = time() . "_" . $file->getClientOriginalName();
        $path = "uploads";
        // now you have access to the file being uploaded
        //perform the upload operation.
        $file->move($path, $savefile);
        $csvdata = $this->readcsv($path, $savefile);
        \Log::debug($csvdata);
        //    if($csvdata){
        //        unlink(public_path($path."/".$savefile));
        //    }
        if ($csvdata) {
            return response()->json([
                "length" => count($csvdata),
                "filename" => $savefile
            ]);
        } else {
            return response()->json(
                [
                    "error" => "format"
                ]
            );
        }
    }
    public function readcsv($path, $savefile)
    {
        $recipientdata = [];
        $error = false;
        if (($open = fopen("/home/aakashahmed/cards.landofisraelart.com/uploads/" . $savefile, "r")) !== FALSE) {
            $first_row = fgetcsv($open, 1000, ",");
            if ($first_row[0] !== "First_Name") {
                $error = true;
            }
            \Log::debug([$first_row[0]]);
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

        if ($error) {
            return false;
        }
        return ($recipientdata);
    }
    // public function testjob(Request $request){
    //     $job = (new SendCardJob())->delay(Carbon::now()->addMinutes(2));

    //     dispatch($job);
    // }
}
