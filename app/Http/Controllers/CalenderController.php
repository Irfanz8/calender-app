<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CalenderController extends Controller{

  public function index(Request $request){

      $response = Http::get(env('DEV_API').'calenders', []);
      $result = json_decode($response->body())->data;
      $data = [];
            if($request->ajax()) {
              for ($a = 0; $a < count($result); $a++) {
                $data[] = [
                    "id" => $result[$a]->id,
                    "title" => $result[$a]->attributes->title,
                    "description" => $result[$a]->attributes->description,
                    "start" => $result[$a]->attributes->start,
                    "end" => $result[$a]->attributes->end,
                ];
        
              }
  
             return response()->json($data);
        }
  
        return view('calender');
    }
 
  public function filter(Request $request){
    $response = Http::get(env('DEV_API').'calenders', [
      'filters[title][$eq]'=> $request->name,
      'filters[start][$eq]' => $request->start
    ]);
    $result = json_decode($response->body())->data;
    $data = false;
    if (count($result) == 0) {
      $data = true;
    }else{
      $data = false;
    }
    return response()->json($data);
  }
  public function store(Request $request){

      $data =['data'=>[
        'title' => $request->title,
        'description' => $request->description,
        'start' => $request->start,
        'end' => $request->end,
      ]];

      $response = Http::post(env('DEV_API').'calenders', 
        $data);
    $result = json_decode($response->body())->data;
    
    return response()->json($result);

  }

  public function update(Request $request){
    $data =['data'=>[
      'title' => $request->title,
      'description' => $request->description,
      'start' => $request->start,
      'end' => $request->end,
    ]];

    $response = Http::put(env('DEV_API').'calenders/'.$request->id, 
      $data);
    $result = json_decode($response->body())->data;
  
    return response()->json($result);
  }

  public function delete(Request $request){
    $response = Http::delete(env('DEV_API').'calenders/'.$request->id, []);
    $result = json_decode($response->body())->data;
  
    return response()->json($result);
  }
  
}
