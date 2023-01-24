<?php

function responseJson($message, $optional = []){
    $array = [
        'status' => 200,
    ];

    if($message){
        $array['message'] = $message;
    }

    if($optional){
        $array = array_merge($array, $optional);
    }
    return response()->json($array);
}

function image($path){
    if($path){
        return config('app.url')."/".str_replace('public/', '', $path);
    }
}
