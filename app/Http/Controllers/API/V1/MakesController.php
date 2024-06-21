<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Makes;
use App\Models\Models;
use Carbon\Carbon;
use Auth;
use Illuminate\support\Str;


class MakesController extends Controller{

    public function __construct(){
        $this->middleware('auth:api');
    }

    public function getMainCategories(Request $request){
        // $result = Categories::where(['parent_cat' => 0, 'status' => '1', 'deleted' => '0'])->get();
        $result = Categories::all();
        if($result){
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        }else{
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    public function getAllMakes(){
        $result = Makes::select('id', 'make_name')->get();

        if($result){
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        }else{
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    public function getModelByMake($makeid){
        $result = Models::where('make_id', $makeid)->select('id', 'model_name')->get();

        if($result){
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        }else{
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }
}
