<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Categories;
use App\Models\Makes;
use App\Models\Models;
use App\Models\Part_brands;
use App\Models\Part_models;
use Carbon\Carbon;
use Auth;
use Illuminate\support\Str;
use Illuminate\Support\Facades\Storage;
// use Intervention\Image\Facades\Image;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function fetchAllCategories()
    {
        $result = Categories::all();
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }
    public function getMainCategories(Request $request)
    {
        $result = Categories::where(['parent_cat' => 0, 'status' => '1', 'deleted' => '0'])->get();
        // $result = Categories::where(['status' => '1', 'deleted' => '0'])->get();
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    public function getCategoryById($category)
    {
        $result = Categories::where(['id' => $category, 'status' => '1', 'deleted' => '0'])->first();
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    public function getSubCategories($parent)
    {
        $result = Categories::where(['parent_cat' => $parent, 'status' => '1', 'deleted' => '0'])->get();
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    // Create main category
    public function createCategory(Request $request)
    {
        $request->validate([
            'cat_name' => 'required|string|min:3',
            'garage' => 'required|integer',
            'parent_cat' => 'required|integer',
            'picture' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('picture')) {
            $filenameWithExt = $request->file('picture')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture')->getClientOriginalExtension();
            $fileNameToStore = 'garage_' . $request->cat_slug . '_' . time() . '.' . $extension;
            $path = $request->file('picture')->storeAs('public/images/categories', $fileNameToStore);
        } else {
            return response()->json(['status' => false, 'message' => 'Picture is required', 'data' => null]);
        }

        $data = [
            'cat_uid' => Str::random(20),
            'garage' => $request->garage,
            'parent_cat' => $request->parent_cat,
            'cat_slug' => $request->cat_name,
            'cat_name' => $request->cat_name,
            'picture' => 'storage/images/categories/' . $fileNameToStore,
            'added_by' => Auth::user()->id,
            'edited_by' => Auth::user()->id,
        ];

        $result = Categories::create($data);

        if ($result) {
            $cats = Categories::all();
            return response()->json(['status' => true, 'message' => 'Main category created successfully', 'data' => $cats]);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to create main category', 'data' => null]);
        }
    }

    public function updateCategory(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'cat_name' => 'required|string|min:3',
            'parent_cat' => 'required|integer',
            'picture' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $cat = Categories::where('id', $request->id)->first();

        if ($request->hasFile('picture')) {
            $filenameWithExt = $request->file('picture')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture')->getClientOriginalExtension();
            $fileNameToStore = 'garage_' . $request->cat_slug . '_' . time() . '.' . $extension;
            $path = $request->file('picture')->storeAs('public/images/categories', $fileNameToStore);

            $filePath = $cat->picture;

            $filePath = str_replace('storage/', 'public/', $filePath);

            if (Storage::disk('local')->exists($filePath)) {
                Storage::delete($filePath);
            }

            $data = [
                'parent_cat' => $request->parent_cat,
                'cat_slug' => $request->cat_name,
                'cat_name' => $request->cat_name,
                'picture' => 'storage/images/categories/' . $fileNameToStore,
                'edited_by' => Auth::user()->id,
            ];
        } else {
            $data = [
                'parent_cat' => $request->parent_cat,
                'cat_slug' => $request->cat_name,
                'cat_name' => $request->cat_name,
                'edited_by' => Auth::user()->id,
            ];
        }

        $result = Categories::where('id', $request->id)->update($data);

        if ($result) {
            $cats = Categories::all();
            return response()->json(['status' => true, 'message' => 'Category updated successfully', 'data' => $cats]);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to update category', 'data' => null]);
        }
    }

    // Create main Sub Category
    public function createSubCategory(Request $request, $parent)
    {
        $request->validate([
            'cat_name' => 'required|string|min:3',
        ]);
        $data = [
            'cat_uid' => Str::random(20),
            'parent_cat' => $parent,
            'cat_slug' => $request->cat_name,
            'cat_name' => $request->cat_name,
            'added_by' => Auth::user()->id,
            'edited_by' => Auth::user()->id,
        ];

        $result = Categories::create($data);

        if ($result) {
            return response()->json(['status' => true, 'message' => 'Main category created successfully', 'data' => null]);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to create main category', 'data' => null]);
        }
    }

    // Remove category - Soft delete feature added
    public function removeCategory($id)
    {
        $result = Categories::where('id', $id)->update(['status' => '0', 'deleted' => '1']);

        if ($result) {
            return response()->json(['status' => true, 'message' => 'Main category deleted successfully', 'data' => null]);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to updated main category', 'data' => null]);
        }
    }

    // Permenent remove Category record from database -  Dont use this function will create error in app
    public function delete($id)
    {
        $result = Categories::delete($id);

        if ($result) {
            return response()->json(['status' => true, 'message' => 'Main category deleted successfully', 'data' => null]);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to updated main category', 'data' => null]);
        }
    }

    public function AddVehicleBrand(Request $request)
    {
        $request->validate([
            'brand_name' => 'required|string|min:3',
            'category' => 'required|integer',
            'picture' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('picture')) {
            $filenameWithExt = $request->file('picture')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture')->getClientOriginalExtension();
            $fileNameToStore = 'garage_' . $request->brand_name . '_' . time() . '.' . $extension;
            $path = $request->file('picture')->storeAs('public/images/brands', $fileNameToStore);

            // $imagePath = public_path('public/images/part_brands'.$fileNameToStore);
            // $img = Image::make($imagePath);
            // $img->save($imagePath, 60);
            // $img->save(public_path('thumbnails/part_brands/' . $fileNameToStore));

            $data = [
                'brand_name' => $request->brand_name,
                'category' => $request->category,
                'picture' => 'storage/images/brands/' . $fileNameToStore,
                'added_by' => Auth::user()->id,
                'edited_by' => Auth::user()->id,
            ];

            $result = Makes::create($data);
            if ($result) {
                return response()->json(['status' => true, 'message' => 'Brand added successfully', 'data' => null]);
            } else {
                return response()->json(['status' => false, 'message' => 'Failed to add brand', 'data' => null]);
            }
        } else {
            return response()->json(['status' => false, 'message' => 'Picture is required', 'data' => null]);
        }
    }
    public function updateVehicleBrand(Request $request, $id)
    {
        $request->validate([
            'brand_name' => 'required|string|min:3',
            'category' => 'required|integer',
        ]);
        $brand = Makes::where('id', $id)->first();
        if (!$brand) {
            return response()->json(['status' => false, 'message' => 'Brand not found', 'data' => null]);
        }

        if ($request->hasFile('picture')) {
            $filenameWithExt = $request->file('picture')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture')->getClientOriginalExtension();
            $fileNameToStore = 'garage_' . $request->brand_name . '_' . time() . '.' . $extension;
            $path = $request->file('picture')->storeAs('public/images/brands', $fileNameToStore);

            $filePath = $brand->picture;

            $filePath = str_replace('storage/', 'public/', $filePath);

            if (Storage::disk('local')->exists($filePath)) {
                Storage::delete($filePath);
            }
            // $imagePath = public_path('public/images/part_brands'.$fileNameToStore);
            // $img = Image::make($imagePath);
            // $img->save($imagePath, 60);
            // $img->save(public_path('thumbnails/part_brands/' . $fileNameToStore));

            $data = [
                'brand_name' => $request->brand_name,
                'category' => $request->category,
                'picture' => 'storage/images/brands/' . $fileNameToStore,
                'edited_by' => Auth::user()->id,
            ];
        } else {
            $data = [
                'brand_name' => $request->brand_name,
                'category' => $request->category,
                'edited_by' => Auth::user()->id,
            ];
        }

        $result = Makes::where('id', $id)->update($data);
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Brand added successfully', 'data' => null]);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to add brand', 'data' => null]);
        }
    }
    public function deleteBrand($id)
    {
        $result = Makes::where('id', $id)->update(['status' => '0', 'deleted' => '1']);

        if ($result) {
            return response()->json(['status' => true, 'message' => 'Brand deleted successfully', 'data' => null]);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to delete brand', 'data' => null]);
        }
    }

    public function fetchAllBrands()
    {
        $result = Makes::select('id', 'brand_name', 'picture')
            ->where(['status' => '1', 'deleted' => '0'])
            ->orderBy('brand_name', 'asc')
            ->get();
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }
    public function fetchBrandById($id)
    {
        $result = Makes::where('id', $id)->first();
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }
    public function fetchBrandsByCategory($category)
    {
        $result = Makes::join('categories', 'categories.id', '=', 'makes.category')
            ->where(['makes.category' => $category, 'makes.status' => '1', 'makes.deleted' => '0'])
            ->select('makes.id', 'makes.brand_name', 'makes.picture', 'categories.cat_name')
            ->orderBy('makes.brand_name', 'asc')
            ->get();
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    public function AddVehicleModel(Request $request)
    {
        $request->validate([
            'brand' => 'required|integer',
            'model' => 'required|string|min:3',
            'picture' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('picture')) {
            $filenameWithExt = $request->file('picture')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture')->getClientOriginalExtension();
            $fileNameToStore = 'garage_' . $request->model . '_' . time() . '.' . $extension;
            $path = $request->file('picture')->storeAs('public/images/models', $fileNameToStore);

            // $imagePath = public_path('public/images/part_brands'.$fileNameToStore);
            // $img = Image::make($imagePath);
            // $img->save($imagePath, 60);
            // $img->save(public_path('thumbnails/part_brands/' . $fileNameToStore));

            $data = [
                'brand' => $request->brand,
                'model' => $request->model,
                'picture' => 'storage/images/models/' . $fileNameToStore,
                'added_by' => Auth::user()->id,
                'edited_by' => Auth::user()->id,
            ];

            $result = Models::create($data);
            if ($result) {
                return response()->json(['status' => true, 'message' => 'Model added successfully', 'data' => null]);
            } else {
                return response()->json(['status' => false, 'message' => 'Failed to add Model', 'data' => null]);
            }
        } else {
            return response()->json(['status' => false, 'message' => 'Picture is required', 'data' => null]);
        }
    }

    public function updateVehicleModel(Request $request, $id)
    {
        $request->validate([
            'brand' => 'required|integer',
            'model' => 'required|string|min:3',
        ]);
        $model = Models::where('id', $id)->first();
        if (!$model) {
            return response()->json(['status' => false, 'message' => 'Model not found', 'data' => null]);
        }

        if ($request->hasFile('picture')) {
            $filenameWithExt = $request->file('picture')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture')->getClientOriginalExtension();
            $fileNameToStore = 'garage_' . $request->model . '_' . time() . '.' . $extension;
            $path = $request->file('picture')->storeAs('public/images/models', $fileNameToStore);

            $filePath = $model->picture;

            $filePath = str_replace('storage/', 'public/', $filePath);

            if (Storage::disk('local')->exists($filePath)) {
                Storage::delete($filePath);
            }
            // $imagePath = public_path('public/images/part_brands'.$fileNameToStore);
            // $img = Image::make($imagePath);
            // $img->save($imagePath, 60);
            // $img->save(public_path('thumbnails/part_brands/' . $fileNameToStore));

            $data = [
                'brand' => $request->brand,
                'model' => $request->model,
                'picture' => 'storage/images/models/' . $fileNameToStore,
                'edited_by' => Auth::user()->id,
            ];
        } else {
            $data = [
                'brand' => $request->brand,
                'model' => $request->model,
                'edited_by' => Auth::user()->id,
            ];
        }

        $result = Models::where('id', $id)->update($data);
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Model updated successfully', 'data' => null]);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to update model', 'data' => null]);
        }
    }

    public function fetchModelById($id)
    {
        $result = Models::join('makes', 'makes.id', 'models.brand')->join('categories', 'categories.id', 'makes.category')->where('models.id', $id)->select('models.id', 'models.model', 'models.picture', 'models.brand', 'makes.category')->first();

        if ($result) {
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }
    public function fetchModelsByBrand($brand)
    {
        $result = Models::select('id', 'model', 'picture')
            ->where(['brand' => $brand, 'status' => '1', 'deleted' => '0'])
            ->get();
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    public function fetchAllPartBrands()
    {
        $result = Part_brands::join('categories', 'categories.id', '=', 'part_brands.category')->select('part_brands.id', 'part_brands.brand_name', 'part_brands.picture', 'categories.id as cat_id', 'categories.parent_cat', 'categories.cat_name')->get();
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    public function fetchPartBrandsByMainCategory($category)
    {
        $result = Part_brands::join('categories', 'categories.id', '=', 'part_brands.category')->select('part_brands.id', 'part_brands.brand_name', 'part_brands.picture', 'categories.id as cat_id', 'categories.parent_cat', 'categories.cat_name')->where('categories.parent_cat', $category)->get();

        if ($result) {
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }
    public function fetchPartBrandsByCategory($category)
    {
        $result = Part_brands::join('categories', 'categories.id', '=', 'part_brands.category')->select('part_brands.id', 'part_brands.brand_name', 'part_brands.picture', 'categories.id as cat_id', 'categories.parent_cat', 'categories.cat_name')->where('part_brands.category', $category)->get();

        if ($result) {
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    public function fetchPartModelsByBrand($brand)
    {
        $result = Part_models::where('brand', $brand)->get();

        if ($result) {
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    public function searchPartBrand(Request $request)
    {
        $result = Part_brands::join('categories', 'categories.id', '=', 'part_brands.category')
            ->select('part_brands.id', 'part_brands.brand_name', 'part_brands.picture', 'categories.id as cat_id', 'categories.parent_cat', 'categories.cat_name')
            ->where('part_brands.brand_name', 'like', '%' . $request->search . '%')
            ->get();
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    public function addPartBrand(Request $request)
    {
        if ($request->hasFile('picture')) {
            $filenameWithExt = $request->file('picture')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture')->getClientOriginalExtension();
            $fileNameToStore = 'garage_' . $request->brand_name . '_' . time() . '.' . $extension;
            $path = $request->file('picture')->storeAs('public/images/part_brands', $fileNameToStore);

            // $imagePath = public_path('public/images/part_brands'.$fileNameToStore);
            // $img = Image::make($imagePath);
            // $img->save($imagePath, 60);
            // $img->save(public_path('thumbnails/part_brands/' . $fileNameToStore));
        } else {
            return response()->json(['status' => false, 'message' => 'Picture is required', 'data' => null]);
        }

        $data = [
            'category' => $request->category,
            'brand_name' => $request->brand_name,
            'picture' => 'storage/images/part_brands/' . $fileNameToStore,
        ];

        $result = Part_brands::create($data);

        if ($result) {
            return response()->json(['status' => true, 'message' => 'Part brand created successfully', 'data' => null]);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to create part brand', 'data' => null]);
        }
    }

    public function updatePartBrand(Request $request, $id)
    {
        $brand = Part_brands::where('id', $id)->first();
        if (!$brand) {
            return response()->json(['status' => false, 'message' => 'Part brand not found', 'data' => null]);
        }
        if ($request->hasFile('picture')) {
            $filenameWithExt = $request->file('picture')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('picture')->getClientOriginalExtension();
            $fileNameToStore = 'garage_' . $request->brand_name . '_' . time() . '.' . $extension;
            $path = $request->file('picture')->storeAs('public/images/part_brands', $fileNameToStore);
            Storage::delete($brand->picture);
            $data = [
                'category' => $request->category,
                'brand_name' => $request->brand_name,
                'picture' => 'storage/images/part_brands/' . $fileNameToStore,
            ];
        } else {
            $data = [
                'category' => $request->category,
                'brand_name' => $request->brand_name,
            ];
        }
        $result = Part_brands::where('id', $id)->update($data);
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Part brand created successfully', 'data' => null]);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to create part brand', 'data' => null]);
        }
    }

    public function findPartBrandById($id)
    {
        $result = Part_brands::where('id', $id)->first();
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }
}
