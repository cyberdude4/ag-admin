<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Makes;
use App\Models\Models;
use App\Models\Garages;
use App\Models\Vehicles;
use Carbon\Carbon;
use Auth;
use Illuminate\support\Str;


class VehiclesController extends Controller{

    public function __construct(){
        $this->middleware('auth:api');
    }

    public function addVehicle(Request $request, $garage){

        $request->validate([
            'category' => 'required|integer',
            'make' => 'required|integer',
            'model' => 'required|integer',
            'vehicle_number' => 'required|string',
            'engine_number' => 'required|string',
            'chasis_number' => 'required|string',
            'purchase_date' => 'required|date',
            'fuel_level' => 'required|integer',
            'odometer' => 'required|integer',
            'owner_id' => 'required|integer',
        ]);

        $result = Vehicles::create([
            'garage' => $garage,
            'category' => $request->category,
            'make' => $request->make,
            'model' => $request->model,
            'vehicle_number' => $request->vehicle_number,
            'engine_number' => $request->engine_number,
            'chasis_number' => $request->chasis_number,
            'purchase_date' => $request->purchase_date,
            'fuel_level' => $request->fuel_level,
            'odometer' => $request->odometer,
            'owner_id' => $request->owner_id,
            'added_by' => auth()->user()->id,
            'edited_by' => auth()->user()->id,
        ]);

        if($result){
            return response()->json(['status' => true, 'message' => 'Vehicle added successfully', 'data' => null]);
        }else{
            return response()->json(['status' => false, 'message' => 'Failed to add vehicle', 'data' => null]);
        }
    }

    public function updateVehicle(Request $request, $vehicle){

        $request->validate([
            'category' => 'required|integer',
            'make' => 'required|integer',
            'model' => 'required|integer',
            'vehicle_number' => 'required|string',
            'engine_number' => 'required|string',
            'chasis_number' => 'required|string',
            'purchase_date' => 'required|date',
            'fuel_level' => 'required|integer',
            'odometer' => 'required|integer',
            'owner_id' => 'required|integer',
        ]);

        $result = Vehicles::find($vehicle)->update([
            'category' => $request->category,
            'make' => $request->make,
            'model' => $request->model,
            'vehicle_number' => $request->vehicle_number,
            'engine_number' => $request->engine_number,
            'chasis_number' => $request->chasis_number,
            'purchase_date' => $request->purchase_date,
            'fuel_level' => $request->fuel_level,
            'odometer' => $request->odometer,
            'edited_by' => auth()->user()->id,
        ]);

        if($result){
            return response()->json(['status' => true, 'message' => 'Vehicle updated successfully', 'data' => null]);
        }else{
            return response()->json(['status' => false, 'message' => 'Failed to update vehicle', 'data' => null]);
        }
    }
    public function fetchVehiclesByUserid($user){
        $result = Vehicles::where('owner_id', $user)->get();
        if($result){
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        }else{
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    public function fetchVehicleById($id){
        $result = Vehicles::find($id)->first();
        if($result){
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        }else{
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    public function fetchMyVehicles(){
        $result = Vehicles::where('owner_id', auth()->user()->id)->get();
        if($result){
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        }else{
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    public function fetchVehiclesByUser($user){
        $result = Vehicles::join('makes', 'vehicles.make', '=', 'makes.id')
        ->leftjoin('models', 'vehicles.model', '=', 'models.id')
        ->leftjoin('customers', 'customers.id', '=', 'vehicles.owner_id')
        ->select('vehicles.*', 'makes.brand_name', 'models.model', 'customers.firstname', 'customers.lastname')
        ->where('vehicles.owner_id', $user)->get();

        if($result){
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        }else{
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    public function fetchVehiclesByGarage($garage){
        $result = Vehicles::join('makes', 'vehicles.make', '=', 'makes.id')
        ->leftjoin('models', 'vehicles.model', '=', 'models.id')
        ->leftjoin('customers', 'customers.id', '=', 'vehicles.owner_id')
        ->select('vehicles.*', 'makes.brand_name', 'models.model', 'customers.firstname', 'customers.lastname')
        ->where('garage', $garage)->get();

        if($result){
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        }else{
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }
}
