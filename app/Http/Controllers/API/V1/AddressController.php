<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Response;
use Redirect;
use App\Models\{Country, State, City};

class AddressController extends Controller{

    public function fetchCountries(){
        $data = Country::get(["name", "id"]);
        return response()->json(['status' =>  true, 'message' => 'data found', 'data' => $data]);
    }

    public function fetchState(Request $request){
        $data = State::where("country_id", $request->id)->get(["name", "id"]);
        return response()->json(['status' => true, 'message' => 'data found', 'data' => $data]);
    }

    public function fetchCity(Request $request){
        $data = City::where("state_id", $request->id)->get(["name", "id"]);
        return response()->json(['status' => true, 'message' => 'data found', 'data' => $data]);
    }

}