<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ViewHandlerController extends Controller
{
    public function homeview(){
        return view("index");
    }
}
