<?php

namespace App\Http\Controllers;

class HeartBeatController extends Controller
{
    public function heartbeat(){
        return response()->json(['status' => 'ok']);
    }
}
