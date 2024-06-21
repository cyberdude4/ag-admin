<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\API\V1\AddressController;
use App\Http\Controllers\API\V1\GarageController;
use App\Http\Controllers\API\V1\EmployeeController;
use App\Http\Controllers\API\V1\CategoryController;
use App\Http\Controllers\API\V1\PartsController;
use App\Http\Controllers\API\V1\MakesController;
use App\Http\Controllers\API\V1\CustomerController;
use App\Http\Controllers\API\V1\VehiclesController;
use App\Http\Controllers\API\V1\ServicesController;
use App\Http\Controllers\API\V1\OrdersController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::middleware('auth:sanctum')->get('/user-profile', function (Request $request) {
//     return $request->user();
// });

Route::prefix('v1')->group(function () {
    
    Route::controller(AuthController::class)->group(function () {
        Route::post('login', 'login');
        Route::post('register', 'register');
        Route::post('logout', 'logout');
        Route::post('refresh', 'refresh');
        Route::get('user-profile', 'profile');
    });
    // Users Routes

    Route::post('user/search/mobile', [AuthController::class, 'searchUserByMobile']);
    
    // Address Routes
    Route::get('countries', [AddressController::class, 'fetchCountries']);
    Route::post('states', [AddressController::class, 'fetchState']);
    Route::post('cities', [AddressController::class, 'fetchCity']);
    
    Route::get('garages', [GarageController::class, 'fetchGarage']);
    Route::get('garage/{id}', [GarageController::class, 'fetchGarageById']);
    Route::post('garage', [GarageController::class, 'create']);
    Route::put('garage/{id}/update', [GarageController::class, 'update']);
    
    Route::post('garage/{id}/employee', [EmployeeController::class, 'create']);
    Route::put('garage/{id}/employee/{uid}', [EmployeeController::class, 'updateEmployeeByGarage']);
    Route::delete('garage/{id}/employee/{uid}', [EmployeeController::class, 'remove']);
    Route::get('garage/{id}/employees', [EmployeeController::class, 'getEmployeeByGarage']);
    Route::get('garage/employee/{id}/', [EmployeeController::class, 'get']);
    Route::get('garage/{id}/mechanics', [EmployeeController::class, 'fetchMechanic']);

    Route::get('garage/{id}/customers', [CustomerController::class, 'getAllCustomers']);
    Route::post('garage/{id}/customers', [CustomerController::class, 'createCustomer']);
    
    Route::get('categories', [CategoryController::class, 'fetchAllCategories']);
    Route::get('categories/main', [CategoryController::class, 'getMainCategories']);
    Route::post('categories', [CategoryController::class, 'createCategory']);
    Route::post('categories/update', [CategoryController::class, 'updateCategory']);
    Route::get('categories/{id}', [CategoryController::class, 'getCategoryById']);
    Route::delete('categories/{id}', [CategoryController::class, 'removeCategory']);
    
    Route::get('categories/{id}/sub', [CategoryController::class, 'getSubCategories']);
    Route::post('categories/{id}/sub', [CategoryController::class, 'createSubCategory']);
    
    Route::post('garage/parts', [PartsController::class, 'create']);
    Route::get('garage/{id}/parts', [PartsController::class, 'getPartsByGarage']);
    Route::get('garage/{id}/parts/{uid}', [PartsController::class, 'fetchGaragePartById']);
    Route::get('garage/{id}/parts/{uid}/view', [PartsController::class, 'partView']);
    Route::get('garage/{id}/categories/{cid}/parts', [PartsController::class, 'getPartsByMainCategory']);

    // Vehicle brands and models route
    Route::get('brands', [CategoryController::class, 'fetchAllBrands']);
    Route::post('brands', [CategoryController::class, 'AddVehicleBrand']);
    Route::get('brands/{id}', [CategoryController::class, 'fetchBrandById']);
    Route::post('brands/{id}', [CategoryController::class, 'updateVehicleBrand']);
    Route::get('category/{id}/brands', [CategoryController::class, 'fetchBrandsByCategory']);

    Route::post('models', [CategoryController::class, 'AddVehicleModel']);
    Route::post('models/{id}', [CategoryController::class, 'updateVehicleModel']);
    Route::get('models/{id}', [CategoryController::class, 'fetchModelById']);
    Route::get('brands/{id}/models', [CategoryController::class, 'fetchModelsByBrand']);


    // Vehicles Route
    Route::get('vehicles/{id}', [VehiclesController::class, 'fetchVehicleById']);
    Route::put('vehicles/{id}', [VehiclesController::class, 'updateVehicle']);
    Route::get('garage/{id}/vehicles', [VehiclesController::class, 'fetchVehiclesByGarage']);
    Route::post('garage/{id}/vehicles/add', [VehiclesController::class, 'addVehicle']);
    Route::get('users/{id}/vehicles', [VehiclesController::class, 'fetchVehiclesByUser']);
    
    // Parts Routes
    Route::get('parts/{id}/view', [PartsController::class, 'fetchPartById']);    
    Route::put('parts/{id}', [PartsController::class, 'update']);
    Route::get('parts/{id}/stocks', [PartsController::class, 'fetchStocks']);
    Route::put('parts/{id}/stocks', [PartsController::class, 'updateStocks']);
    Route::get('garage/{id}/brand/{bid}/model/{mid}/parts', [PartsController::class, 'fetchPartsByMakeModel']);

    // parts categories, Brands & Models
    Route::get('parts/brands', [CategoryController::class, 'fetchAllPartBrands']);
    Route::get('parts/brands/{id}', [CategoryController::class, 'findPartBrandById']);
    Route::post('parts/brands/update/{id}', [CategoryController::class, 'updatePartBrand']);
    Route::post('parts/brands', [CategoryController::class, 'addPartBrand']);
    Route::get('parts/maincategories/{id}/brands', [CategoryController::class, 'fetchPartBrandsByMainCategory']);
    Route::get('parts/categories/{id}/brands', [CategoryController::class, 'fetchPartBrandsByCategory']);
    Route::get('parts/brands/{brand}/models', [CategoryController::class, 'fetchPartModelsByBrand']);
    Route::post('parts/brands/search', [CategoryController::class, 'searchPartBrand']);


    // Part's images routes
    Route::post('parts/{id}/uploader', [PartsController::class, 'partImagesUploader']);
    Route::get('parts/{id}/images', [PartsController::class, 'fetchPartImages']);
    Route::delete('parts/{id}/images/{uid}', [PartsController::class, 'removePartImage']);
   
    // Customers Routes
    Route::get('customers/{id}', [CustomerController::class, 'fetchCustomerById']);
    Route::put('customers/{id}', [CustomerController::class, 'updateCustomer' ]);
    Route::post('customers/search', [CustomerController::class, 'searchCustomer']);

    // Services Routes
    Route::post('services', [ServicesController::class, 'create']);
    Route::post('services/{id}', [ServicesController::class, 'update']);
    Route::get('services/{id}', [ServicesController::class, 'fetchById']);
    Route::delete('services/{id}', [ServicesController::class, 'delete']);
    Route::get('services', [ServicesController::class, 'fetch']);
    Route::get('garage/{id}/services', [ServicesController::class, 'fetchAllServicesByGarage']);
    Route::get('garage/{id}/category/{uid}/services', [ServicesController::class, 'fetchByCategory']);

    // Orders route

    Route::post('orders', [OrdersController::class, 'initOrder']);
    Route::get('garage/{id}/orderscount', [OrdersController::class, 'ordersCount']);
    Route::get('garage/{id}/orders/{tid}', [OrdersController::class, 'fetchOrdersByType']);
    Route::get('orders/{id}/details', [OrdersController::class, 'fetchOrderDetailById']);
    Route::get('orders/{id}/review', [OrdersController::class, 'fetchOrderReview']);
    Route::post('orders/{id}/placeorder', [OrdersController::class, 'placeOrder']);
    Route::post('orders/{id}/mechanic', [OrdersController::class, 'assignMechanic']);
    Route::post('orders/{id}/services', [OrdersController::class, 'addOrderService']);
    Route::get('orders/{id}/services', [OrdersController::class, 'fetchOrderServices']);
    Route::delete('orders/{id}/services/{sid}', [OrdersController::class, 'deleteOrderServices']);

    Route::post('orders/{id}/parts', [OrdersController::class, 'addOrderParts']);
    Route::get('orders/{id}/parts', [OrdersController::class, 'getOrderParts']);
    Route::delete('orders/{id}/parts/{pid}', [OrdersController::class, 'removeOrderPart']);

    Route::post('orders/{id}/parts/{pid}/quantity', [OrdersController::class, 'partQuantityChange']);


});
