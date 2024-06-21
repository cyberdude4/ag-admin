<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Garages;
use Carbon\Carbon;
use Illuminate\Support\Str;

class GarageController extends Controller
{

    public function __construct(){
        $this->middleware('auth:api');
    }

    public function fetchGarage(Request $request){
        $user = Auth::user()->id;

        // return response()->json($user);
        $data = Garages::where('user_id', $user)->get();

        if($data){
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $data]);
        }else{
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    public function fetchGarageById(Request $request, $id){
        $user = Auth::user()->id;

        // return response()->json($user);
        $data = Garages::where(['id' => $id, 'user_id' => $user])->first();

        if($data){
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $data]);
        }else{
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    public function create(Request $request){

        $request->validate([
            'name' => 'required|string|min:3',
            'country' => 'required|integer',
            'state' => 'required|integer',
            'city' => 'required|integer',
            'street' => 'required|string',
            'pincode' => 'required|string',
            // 'lat' => 'required||numeric|between:-90,90',
            // 'long' => 'required|numeric|between:-180,180',
            'tax_number' => 'required|unique:garages|string',
            'pancard' => 'required|unique:garages|string',
            // 'refer_by' => 'string',
        ]);

        $ip = $request->ip();
        $user = Auth::user()->id;
        $reference = '12345';
        if(isset($request->refer_by)){
            $reference = $request->refer_by;
        }

        $garage = Garages::create([
            'user_id' => $user,
            'slug' => Str::random(20),
            'name' => $request->name,
            'country' => $request->country,
            'state' => $request->state,
            'city' => $request->city,
            'street' => $request->street,
            'pincode' => $request->pincode,
            'tax_number' => $request->tax_number,
            'pancard' => $request->pancard,
            'refer_by' => $reference,
            'refer_id' => Str::random(10),
            'added_by' => $user,
            'edited_by' => $user,
            'created_ip' => $ip,
            'last_ip' => $ip
        ]);


        if($garage){
            return response()->json(['status' => true, 'message' => 'Garage created successfully, please wait for approval', 'data' => $garage]);
        }else{
            return response()->json(['status' => false, 'message' => 'Failed to create garage, Please check your inputs', 'data' => null]);
        }
        
    }

    public function update(Request $request, $id){

        $request->validate([
            'name' => 'required|string|min:3',
            'country' => 'required|integer',
            'state' => 'required|integer',
            'city' => 'required|integer',
            'street' => 'required|string',
            'pincode' => 'required|string',
            'tax_number' => 'required|string',
            'pancard' => 'required|string',
        ]);

        $ip = $request->ip();
        $user = Auth::user()->id;

        $garage = Garages::where(['id' => $id])->update([
            'name' => $request->name,
            'country' => $request->country,
            'state' => $request->state,
            'city' => $request->city,
            'street' => $request->street,
            'pincode' => $request->pincode,
            'tax_number' => $request->tax_number,
            'pancard' => $request->pancard,
            'edited_by' => $user,
            'last_ip' => $ip
        ]);

        if($garage){
            return response()->json(['status' => true, 'message' => 'Garage details updated successfully', 'data' => null]);
        }else{
            return response()->json(['status' => false, 'message' => 'Failed to update garage details', 'data' => null]);
        }
        
    }
    
    public function get(Request $request){

    }
    public function remove(Request $request){

    }

}
