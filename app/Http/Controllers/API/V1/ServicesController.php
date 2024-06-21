<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Auth;
use App\Models\User;
use App\Models\Services;
use App\Models\Categories;

class ServicesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    // Create Parts
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'picture' => 'required|image|mimes:jpeg,png,jpg|max:5000',
            'category' => 'required|integer',
            'garage' => 'required|integer',
            'cost' => 'required|integer|not_in:0',
            'discount' => 'required|integer',
            'tax' => 'required|integer',  
        ]);

        if ($request->hasFile('picture')) {
            $filenameWithExt = $request->file('picture')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture')->getClientOriginalExtension();
            $fileNameToStore = 'service_'. time() . '.' . $extension;
            $path = $request->file('picture')->storeAs('public/images/services', $fileNameToStore);
        } else {
            return response()->json(['status' => false, 'message' => 'Picture is required', 'data' => null]);
        }

        $data = [
            'name' => $request->name,
            'picture' => 'storage/images/services/' . $fileNameToStore,
            'garage' => $request->garage,
            'category' => $request->category,
            'cost' => $request->cost,
            'discount' => $request->discount,
            'tax' => $request->tax,
            'added_by' => Auth::user()->id,
            'edited_by' => Auth::user()->id,
        ];

        $result = Services::create($data);
        if($result){
            return response()->json(['status' => true, 'message' => 'Service created successfully', 'data' => null]);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to create service', 'data' => null]);
        }
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'picture' => 'required|image|mimes:jpeg,png,jpg|max:5000',
            'category' => 'required|integer',
            'cost' => 'required|integer|not_in:0',
            'discount' => 'required|integer|not_in:0',
            'tax' => 'required|integer',  
        ]);

        $service = Services::find($id)->first();

        if(!$service){
            return response()->json(["status" => false, "message" => "Service not found for id", "data" => null]);
        }

        if ($request->hasFile('picture')) {
            $filenameWithExt = $request->file('picture')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture')->getClientOriginalExtension();
            $fileNameToStore = 'service' . '_' . time() . '.' . $extension;
            $path = $request->file('picture')->storeAs('public/images/services', $fileNameToStore);

            $filePath = $service->picture;

            $filePath = str_replace('storage/', 'public/', $filePath);

            if (Storage::disk('local')->exists($filePath)) {
                Storage::delete($filePath);
            }
            $data = [
                'name' => $request->name,
                'picture' => 'storage/images/services/' . $fileNameToStore,
                'category' => $request->category,
                'cost' => $request->cost,
                'discount' => $request->discount,
                'tax' => $request->tax,
                'edited_by' => Auth::user()->id,
            ];

        }else{
            $data = [
                'name' => $request->name,
                'category' => $request->category,
                'cost' => $request->cost,
                'discount' => $request->discount,
                'tax' => $request->tax,
                'edited_by' => Auth::user()->id,
            ];
        }

        $result = Services::where('id', $id)->update($data);
        if($result){
            return response()->json(['status' => true, 'message' => 'Service updated successfully', 'data' => null]);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to update service', 'data' => null]);
        }
    }
    public function fetch(){
        $result = Services::join('categories', 'categories.id', 'services.category')
                ->select('services.*', 'categories.cat_name')
                ->get();
        if($result){
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }
    public function fetchByCategory($garage, $category){
        $result = Services::join('categories', 'categories.id', 'services.category')
                ->select('services.*', 'categories.cat_name')
                ->where(['services.category' => $category, 'services.garage' => $garage, 'services.status' => '1', 'services.deleted' => '0'])->get();
        if($result){
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }
    public function fetchAllServicesByGarage($garage){
        $result = Services::join('categories', 'categories.id', 'services.category')
        ->select('services.*', 'categories.cat_name')
        ->where(['services.garage' => $garage, 'services.status' => '1', 'services.deleted' => '0'])->get();
        if($result){
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    public function fetchById($id){
        $result = Services::where('id',$id)->first();
        if($result){
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    public function delete($id){
        $result = Services::find($id)->update(['status' => '0', 'deleted' => '1']);
        if($result){
            return response()->json(['status' => true, 'message' => 'Service deleted successfully', 'data' => null]);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to delete service, Please try again', 'data' => null]);
        }
    }
}
