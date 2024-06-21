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
use App\Models\Parts;
use App\Models\Parts_images;
use App\Models\Stocks;

class PartsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['partImagesUploader']]);
    }

    // Create Parts
    public function create(Request $request)
    {
        $request->validate([
            'sku' => 'required|string|unique:parts',
            'part_number' => 'required|integer|unique:parts',
            'garage_id' => 'required|integer',
            'category' => 'required|integer',
            'vehicle_brand' => 'required|integer',
            'vehicle_model' => 'required|integer',
            'part_brand' => 'required|integer',
            'part_model' => 'required|string',
            'part_type' => 'string',
            'part_slug' => 'string',
            'part_description' => 'required|string',
            'part_warranty' => 'required|integer',
            'part_guarantee' => 'required|integer',
            'purchase_price' => 'required|integer|not_in:0',
            'sale_price' => 'required|integer|not_in:0',
            'discount_percent' => 'required|integer',
            'tax_percent' => 'required|integer',
            'rack' => 'required|integer',
            'stock' => 'required|integer',
        ]);

        $data = [
            'sku' => $request->sku,
            'part_number' => $request->part_number,
            'garage_id' => $request->garage_id,
            'category' => $request->category,
            'vehicle_brand' => $request->vehicle_brand,
            'vehicle_model' => $request->vehicle_model,
            'part_brand' => $request->part_brand,
            'part_model' => $request->part_model,
            'part_type' => $request->part_type,
            'part_slug' => $request->part_slug,
            'part_description' => $request->part_description,
            'part_warranty' => $request->part_warranty,
            'part_guarantee' => $request->part_guarantee,
            'purchase_price' => $request->purchase_price,
            'sale_price' => $request->sale_price,
            'discount_percent' => $request->discount_percent,
            'tax_percent' => $request->tax_percent,
            'added_by' => Auth::user()->id,
            'edited_by' => Auth::user()->id,
        ];
        try {
            DB::beginTransaction();
            $result = Parts::create($data);
            Stocks::create([
                'part_id' => $result->id,
                'garage_id' => $request->garage_id,
                'rack' => $request->rack,
                'quantity' => $request->stock,
                'added_by' => Auth::user()->id,
                'edited_by' => Auth::user()->id,
            ]);
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Part created successfully', 'data' => $result]);
        } catch (\Exception $exp) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Failed to create Part', 'data' => $exp]);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'sku' => 'required|string',
            'part_number' => 'required|integer',
            'category' => 'required|integer',
            'vehicle_brand' => 'required|integer',
            'vehicle_model' => 'required|integer',
            'part_brand' => 'required|integer',
            'part_model' => 'required|string',
            'part_type' => 'string',
            'part_slug' => 'string',
            'part_description' => 'required|string',
            'part_warranty' => 'required|integer',
            'part_guarantee' => 'required|integer',
            'purchase_price' => 'required|numeric|between:1,9999999999.99',
            'sale_price' => 'required|numeric|between:1,9999999999.99',
            'discount_percent' => 'required|integer',
            'tax_percent' => 'required|integer',
        ]);

        $data = [
            'sku' => $request->sku,
            'part_number' => $request->part_number,
            'category' => $request->category,
            'vehicle_brand' => $request->vehicle_brand,
            'vehicle_model' => $request->vehicle_model,
            'part_brand' => $request->part_brand,
            'part_model' => $request->part_model,
            'part_type' => $request->part_type,
            'part_slug' => $request->part_slug,
            'part_description' => $request->part_description,
            'part_warranty' => $request->part_warranty,
            'part_guarantee' => $request->part_guarantee,
            'purchase_price' => $request->purchase_price,
            'sale_price' => $request->sale_price,
            'discount_percent' => $request->discount_percent,
            'tax_percent' => $request->tax_percent,
            'edited_by' => Auth::user()->id,
        ];

        $result = Parts::where('id', $id)->update($data);
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Part details updated successfully', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to update Part details', 'data' => $exp]);
        }
    }

    public function fetch()
    {
        $result = Parts::all();
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    public function getPartsByGarage($garage)
    {
        $result = Parts::join('stocks', 'stocks.part_id', '=', 'parts.id')
            ->join('makes', 'makes.id', '=', 'parts.vehicle_brand')
            ->join('models', 'models.id', '=', 'parts.vehicle_model')
            ->join('part_brands', 'part_brands.id', '=', 'parts.part_brand')
            ->join('parts_images', function ($join) {
                $join->on('parts_images.part_id', '=', 'parts.id')->whereRaw('parts_images.id = (SELECT MIN(id) FROM parts_images WHERE part_id = parts.id)');
            })
            ->where('parts.garage_id', $garage)
            ->select('parts.id', 'stocks.id as stock_id', 'parts.category', 'parts.part_brand', 'parts.part_number', 'parts.part_model', 'parts.part_description', 'parts.purchase_price', 'parts.sale_price', 'parts.discount_percent', 'stocks.rack', 'stocks.quantity', 'part_brands.brand_name as part_brandname', 'makes.brand_name as vehicle_brandname', 'models.model as vehicle_modelname', 'parts_images.picture')
            ->paginate(20);

        if ($result) {
            return response()->json([
                'status' => true,
                'message' => 'Data found',
                'data' => [
                    'data' => $result->items(),
                    'meta' => [
                        'current_page' => $result->currentPage(),
                        'per_page' => $result->perPage(),
                        'total' => $result->total(),
                    ],
                ],
            ]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    public function addStock(Request $request)
    {
        $request->validate([
            'garage_id' => 'required|integer',
            'part_id' => 'required|integer',
            'rack' => 'required|integer',
            'quantity' => 'required|integer',
        ]);

        $data = [
            'garage_id' => $request->garage_id,
            'part_id' => $request->part_id,
            'rack' => $request->rack,
            'quantity' => $request->quantity,
        ];

        $result = Stocks::create($data);

        if ($result) {
            return response()->json(['status' => true, 'message' => 'Stock added successfully', 'data' => null]);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to add stock', 'data' => null]);
        }
    }

    public function updateStock(Request $request, $stockid)
    {
        $request->validate([
            'rack' => 'required|integer',
            'quantity' => 'required|integer',
        ]);

        $data = ['rack' => $request->rack, 'quantity' => $request->quantity];
        $result = Stocks::where('id', $stockid)->update($data);

        if ($result) {
            return response()->json(['status' => true, 'message' => 'Stock added successfully', 'data' => null]);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to add stock', 'data' => null]);
        }
    }

    public function fetchGaragePartById($garage, $id)
    {
        $data = Parts::join('stocks', 'stocks.part_id', '=', 'parts.id')
            ->join('categories', 'categories.id', '=', 'parts.category')
            ->select('parts.*', 'stocks.quantity', 'stocks.rack', 'categories.parent_cat as maincat_id')
            ->where(['parts.id' => $id, 'parts.garage_id' => $garage])
            ->first();

        if ($data) {
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $data]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    public function partView($garage, $id)
    {
        $data = Parts::join('stocks', 'stocks.part_id', '=', 'parts.id')
            ->join('categories', 'categories.id', '=', 'parts.category')
            ->join('part_brands', 'part_brands.id', '=', 'parts.part_brand')
            ->join('makes', 'makes.id', '=', 'parts.vehicle_brand')
            ->join('models', 'models.id', '=', 'parts.vehicle_model')
            ->join('users as added', 'added.id', '=', 'parts.added_by')
            ->join('users as edited', 'edited.id', '=', 'parts.edited_by')
            ->where(['parts.id' => $id, 'parts.garage_id' => $garage])
            ->select('parts.sku', 'parts.part_number', 'parts.part_model', 'parts.part_warranty', 'parts.part_guarantee', 'parts.purchase_price', 'parts.sale_price', 'parts.discount_percent', 'parts.tax_percent', 'stocks.quantity', 'stocks.rack', 'categories.cat_name as category', 'makes.brand_name as vehicle_brand', 'models.model as vehicle_model', 'makes.brand_name as vehicle_brand', 'part_brands.brand_name as part_brand', 'added.firstname as creater_firstname', 'added.lastname as creater_lastname', 'edited.firstname as editor_firstname', 'edited.lastname as editor_lastname')
            ->first();

        if ($data) {
            $images = Parts_images::where('part_id', $id)
                ->select('id', 'picture')
                ->get();
            $data['images'] = $images;
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $data]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    public function getPartsByMainCategory($garage, $category)
    {
        $result = Parts::join('categories', 'categories.id', '=', 'parts.category')
            ->leftjoin('stocks', 'stocks.part_id', '=', 'parts.id')
            ->leftJoin('parts_images', function ($join) {
                $join->on('parts_images.part_id', '=', 'parts.id')->whereRaw('parts_images.id = (SELECT MIN(id) FROM parts_images WHERE part_id = parts.id)');
            })
            ->where('parts.garage_id', $garage)
            ->where('categories.parent_cat', $category)
            ->select('parts.id', 'stocks.id as stock_id', 'parts.category', 'parts.part_number', 'parts.part_brand', 'parts.part_model', 'parts.part_description', 'parts.purchase_price', 'parts.sale_price', 'parts.discount_percent', 'stocks.rack', 'stocks.quantity', 'parts_images.picture')
            ->paginate(20);

        if ($result) {
            return response()->json([
                'status' => true,
                'message' => 'Data found',
                'data' => [
                    'data' => $result->items(),
                    'meta' => [
                        'current_page' => $result->currentPage(),
                        'per_page' => $result->perPage(),
                        'total' => $result->total(),
                    ],
                ],
            ]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    public function getPartsBySubCategory($garage, $category)
    {
        $result = Parts::join('stocks', 'stocks.part_id', '=', 'parts.id')
            ->where('parts.garage_id', $garage)
            ->where('parts.category', $category)
            ->select('parts.id', 'stocks.id as stock_id', 'parts.category', 'parts.part_brand', 'parts.part_model', 'parts.part_description', 'parts.purchase_price', 'parts.sale_price', 'parts.discount_percent', 'stocks.rack', 'stocks.quantity')
            ->paginate(20);

        if ($result) {
            return response()->json([
                'status' => true,
                'message' => 'Data found',
                'data' => [
                    'data' => $result->items(),
                    'meta' => [
                        'current_page' => $result->currentPage(),
                        'per_page' => $result->perPage(),
                        'total' => $result->total(),
                    ],
                ],
            ]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    public function fetchPartById($id)
    {
        $result = Parts::where('id', $id)->first();
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    public function partImagesUploader(Request $request, $id)
    {
        $request->validate([
            'images.*' => 'image|mimes:jpeg,png,jpg|max:5000',
        ]);

        $part = Parts::where('id', $id)->first();
        if (!$part) {
            return response()->json(['status' => false, 'message' => 'Part not found', 'data' => null]);
        }
        if ($request->hasFile('images')) {

            // $imagePath = public_path('public/images/parts'.$fileNameToStore);
            // $img = Image::make($imagePath);
            // $img->save($imagePath, 60);
            // $img->save(public_path('public/thumbnails/parts/' . $fileNameToStore));

            foreach ($request->file('images') as $image) {
                $uniqueString = Str::uuid()->toString();
                $extension = $image->getClientOriginalExtension();
                $imageName = 'part_' . $part->id . '_' . $uniqueString . '.' . $extension;

                $image->storeAs('public/images/parts', $imageName);
                  
                $data = [
                    'part_id' => $id,
                    'picture' => 'storage/images/parts/' . $imageName,
                    'added_by' => Auth::user()->id,
                    'edited_by' => auth::user()->id,
                ];
                $res = Parts_images::create($data);
            }

            $uploadStatus = Parts_images::where(['part_id' => $id])->get();
            return response()->json(['status' => true, 'message' => 'Image successfuly uploaded', 'data' => $uploadStatus]);
        } else {
            return response()->json(['status' => false, 'message' => 'Please upload image']);
        }
    }

    public function removePartImage($pid, $id)
    {
        $image = Parts_images::where(['id' => $id, 'part_id' => $pid])->first();
        if (!$image) {
            return response()->json(['status' => false, 'message' => 'Image not found', 'data' => null]);
        }
        $fileToDelete = public_path($image->picture);
        File::delete($fileToDelete);
        $res = $image->delete();
        if ($res) {
            $result = Parts_images::where(['part_id' => $pid])->get();
            return response()->json(['status' => true, 'message' => 'Image successfuly deleted', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to delete image', 'data' => null]);
        }
    }

    public function fetchPartImages($id)
    {
        $result = Parts_images::where('part_id', $id)
            ->select('id', 'picture')
            ->get();
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    public function fetchStocks($partid)
    {
        $result = Parts::join('categories', 'categories.id', '=', 'parts.category')
            ->join('part_brands', 'part_brands.id', '=', 'parts.part_brand')
            ->join('makes', 'makes.id', '=', 'parts.vehicle_brand')
            ->join('models', 'models.id', '=', 'parts.vehicle_model')
            ->join('stocks', 'stocks.part_id', 'parts.id')
            ->where('stocks.part_id', $partid)
            ->select('parts.sku', 'parts.part_number', 'parts.part_model', 'parts.part_warranty', 'parts.part_guarantee', 'parts.purchase_price', 'parts.sale_price', 'parts.discount_percent', 'parts.tax_percent', 'stocks.quantity', 'stocks.rack', 'categories.cat_name as category', 'makes.brand_name as vehicle_brand', 'models.model as vehicle_model', 'makes.brand_name as vehicle_brand', 'part_brands.brand_name as part_brand', 'stocks.rack', 'stocks.quantity')
            ->first();
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    public function updateStocks($id, Request $request)
    {
        $data = $request->all();
        $result = Stocks::where('part_id', $id)->update($data);
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Data updated', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not updated', 'data' => null]);
        }
    }

    public function fetchPartsByMakeModel($garage, $make, $model){
        $data = Parts::join('makes', 'makes.id', '=', 'parts.vehicle_brand')
                ->join('models', 'models.id', '=', 'parts.vehicle_model')
                ->join('part_brands', 'part_brands.id', '=', 'parts.part_brand')
                ->select(
                    'parts.id',
                    'parts.sku as sku',
                    'parts.part_number as part_number',
                    'parts.part_model as part_model',
                    'part_brands.brand_name as part_brandname',
                    'makes.brand_name as vehicle_brandname',
                    'models.model as vehicle_modelname'
                )
                ->where(['parts.garage_id' => $garage, 'parts.vehicle_brand' => $make, 'parts.vehicle_model' => $model])
                ->get();

        if ($data) {
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $data]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }
}
