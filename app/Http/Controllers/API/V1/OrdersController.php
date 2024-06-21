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
use App\Models\Orders;
use App\Models\Orderservices;
use App\Models\Parts;
use App\Models\Orderparts;
use App\Models\Orderworkflow;
use App\Models\Employees;

class OrdersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function initOrder(Request $request)
    {
        $request->validate([
            'garage' => 'required|integer',
            'vehicle' => 'required|integer',
            'user' => 'required|integer',
            'pick_vehicle' => 'required',
            'delivery_date' => 'required',
        ]);


        $pickup_date = null;
        if ($request->pickup_date) {
            $original_pickup_date = $request->pickup_date;
            $carbon_pickup_date = Carbon::parse($original_pickup_date);
            $pickup_date = $carbon_pickup_date->format('Y-m-d H:i:s');
        }


        if ($request->delivery_date) {
            $original_delivery_date = $request->delivery_date;
            $carbon_delivery_date = Carbon::parse($original_delivery_date);
            $delivery_date = $carbon_delivery_date->format('Y-m-d H:i:s');
        }

        $data = [
            'garage' => $request->garage,
            'vehicle' => $request->vehicle,
            'user' => $request->user,
            'total_amount' => 0,
            'paid_amount' => 0,
            'due_amount' => 0,
            'currency' => 'INR',
            'pick_vehicle' => $request->pick_vehicle,
            'pickup_date' => $pickup_date,
            'delivery_date' => $delivery_date,
            'order_number' => Str::random(10),
            'first_ip' => $request->ip(),
            'last_ip' => $request->ip(),
            'added_by' => Auth::user()->id,
            'edited_by' => Auth::user()->id,
        ];

        $result = Orders::create($data);

        if ($result) {
            return response()->json(['status' => true, 'message' => 'Order initiated successfully in draft', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to initiate order, Please try again', 'data' => null]);
        }
    }


    public function fetchOrdersByType($garage, $type){
        $data = Orders::join('users', 'users.id', '=', 'orders.user')
                ->select(
                    'orders.id',
                    'users.firstname',
                    'users.lastname',
                    'users.mobile',
                    'orders.order_number',
                    'orders.status',
                    'orders.created_at',
                    )
                ->where(['orders.garage' => $garage, 'orders.status' => $type])->get();
        if ($data) {
            return response()->json(['status' => true, 'message' => 'Orders found successfully', 'data' => $data]);
        } else {
            return response()->json(['status' => false, 'message' => 'Orders not found', 'data' => null]);
        }
    }
    
    public function fetchOrderDetailById($id)
    {
        $result = Orders::join('users', 'orders.user', '=', 'users.id')
            ->join('vehicles', 'orders.vehicle', '=', 'vehicles.id')
            ->join('categories', 'categories.id', '=', 'vehicles.category')
            ->join('makes', 'vehicles.make', '=', 'makes.id')
            // ->join('orderworkflow', 'orderworkflow.order', '=', 'orders.id')
            ->leftjoin('models', 'vehicles.model', '=', 'models.id')
            ->where(['orders.id' => $id])
            // ->where(['orders.id' => $id, 'orderworkflow.type' => 'work'])
            ->select('orders.*', 'users.firstname', 'users.lastname', 'users.mobile', 'vehicles.purchase_date', 'vehicles.vehicle_number', 'vehicles.fuel_level', 'vehicles.odometer', 'makes.id as vehicle_brandid', 'makes.brand_name as vehicle_brand', 'models.id as vehicle_modelid', 'models.model as vehicle_model', 'categories.id as category')
            ->first();

        if ($result) {
            return response()->json(['status' => true, 'message' => 'Order found successfully', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Order not found', 'data' => null]);
        }
    }

    public function addOrderService(Request $request, $order)
    {
        $request->validate([
            'vehicle' => 'required|integer',
            'service' => 'required|integer',
        ]);

        $check = Orderservices::where([
            'order' => $order,
            'vehicle' => $request->vehicle,
            'service' => $request->service,
        ])->count();

        if ($check) {
            $serviceList = Orderservices::join('services', 'orderservices.service', '=', 'services.id')
                ->where('order', $order)
                ->select('orderservices.id', 'services.name', 'orderservices.cost', 'orderservices.discount', 'orderservices.tax')
                ->get();

            return response()->json(['status' => false, 'message' => 'Order service already added', 'data' => $serviceList]);
        }

        $service = Services::where('id', $request->service)->first();
        $data = [
            'order' => $order,
            'vehicle' => $request->vehicle,
            'service' => $service->id,
            'quantity' => $request->quantity,
            'cost' => $service->cost,
            'discount' => $service->discount,
            'tax' => $service->tax,
        ];

        $result = Orderservices::create($data);

        if ($result) {
            $services = Orderservices::join('services', 'orderservices.service', '=', 'services.id')
                ->where('order', $order)
                ->select('orderservices.id', 'services.name', 'orderservices.cost', 'orderservices.discount', 'orderservices.tax')
                ->get();
            return response()->json(['status' => true, 'message' => 'Order service added successfully', 'data' => $services]);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to add order service, Please try again', 'data' => null]);
        }
    }

    public function fetchOrderServices($order)
    {
        $result = Orderservices::join('services', 'orderservices.service', '=', 'services.id')
            ->where('orderservices.order', $order)
            ->select('orderservices.id', 'services.name', 'orderservices.cost', 'orderservices.discount', 'orderservices.tax')
            ->get();
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Order service added successfully', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to add order service, Please try again', 'data' => null]);
        }
    }

    public function deleteOrderServices($order, $id){
        $result = Orderservices::find($id)->delete();
        if($result){
            return response()->json(['status' => true, 'message' => 'Order service deleted successfully', 'data' => null]);
        }else{
            return response()->json(['status' => false, 'message' => 'Failed to delete order service, Please try again', 'data' => null]);
        }
    }

    public function addOrderParts(Request $request, $order){
        $request->validate([
            'vehicle' => 'required|integer',
            'part' => 'required|integer',
        ]);

        $partCheck = Parts::where('id', $request->part)->first();

        if(!$partCheck){
            return response()->json(['status' => false, 'message' => 'Part not found', 'data' => null]);
        }

        // update quantity if part already exists
        $orderPartCheck = Orderparts::where(['order' => $order, 'part' => $request->part])->first();

        if($orderPartCheck){
            $result = $orderPartCheck->increment('quantity');
        }else{
            $data = [
                'order' => $order,
                'vehicle' => $request->vehicle,
                'part' => $request->part,
                'quantity' => $request->quantity,
                'cost' => $partCheck->sale_price,
                'discount' => $partCheck->discount_percent,
                'tax' => $partCheck->tax_percent,
            ];

            $result = Orderparts::create($data);
        }

        if ($result) {

            $parts = Orderparts::join('parts', 'orderparts.part', '=', 'parts.id')
                ->join('part_brands', 'part_brands.id', '=', 'parts.part_brand')
                ->select(
                    'orderparts.id',
                    'orderparts.part',
                    'part_brands.brand_name', 
                    'parts.part_model',
                    'parts.part_number',
                    'orderparts.cost', 
                    'orderparts.discount',
                    'orderparts.tax',
                    'orderparts.quantity', 
                )->where('orderparts.order', $order)->get();

            return response()->json(['status' => true, 'message' => 'Order part added successfully', 'data' => $parts]);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to add order part, Please try again', 'data' => null]);
        }
    }

    public function getOrderParts($order){
        $parts = Orderparts::join('parts', 'orderparts.part', '=', 'parts.id')
        ->join('part_brands', 'part_brands.id', '=', 'parts.part_brand')
        ->select(
            'orderparts.id',
            'orderparts.part',
            'part_brands.brand_name', 
            'parts.part_model',
            'parts.part_number',
            'orderparts.cost', 
            'orderparts.discount',
            'orderparts.tax',
            'orderparts.quantity', 
        )->where('orderparts.order', $order)->get();

        if ($parts) {
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $parts]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    public function partQuantityChange(Request $request, $order, $part){
        $request->validate([
            'type' => 'string',
        ]);

        if($request->type == 'increase'){
            $result = DB::table('orderparts')->where('id', $part)->increment('quantity');
        }else{
            $result = DB::table('orderparts')->where('id', $part)->decrement('quantity');
        }

        if($result){
            $parts = Orderparts::join('parts', 'orderparts.part', '=', 'parts.id')
            ->join('part_brands', 'part_brands.id', '=', 'parts.part_brand')
            ->select(
                'orderparts.id',
                'orderparts.part',
                'part_brands.brand_name', 
                'parts.part_model',
                'parts.part_number',
                'orderparts.cost', 
                'orderparts.discount',
                'orderparts.tax',
                'orderparts.quantity', 
            )->where('orderparts.order', $order)->get();

            return response()->json(['status' => true, 'message' => 'data updated', 'data' => $parts]);
        }else{
            return response()->json(['status' => false, 'message' => 'data not updated', 'data' => null]);
        }
    }

    public function removeOrderPart($order, $part){
        $result = Orderparts::where('id', $part)->delete();
        if($result){
            return response()->json(['status' => true, 'message' => 'Part removed', 'data' => null]);
        }else{
            return response()->json(['status' => false, 'message' => 'Unable to remove part', 'data' => null]);
        }
    }

    public function assignMechanic(Request $request, $order){
        $request->validate([
            'user' => 'required|integer',
        ]);

        $workcheck = Orderworkflow::where(['order' => $order, 'user' => $request->user])->first();
    
        if($workcheck){
            $workcheck->update(['user' => $request->user]);
            return response()->json(['status' => true, 'message' => 'Mechanic changed', 'data' => null]);
        }

        $result = Orderworkflow::create([
            'order' => $order,
            'user' => $request->user,
        ]);

        if($result){
            return response()->json(['status' => true, 'message' => 'Mechanic assigned', 'data' => null]);
        }else{
            return response()->json(['status' => false, 'message' => 'Failed to assign mechanic', 'data' => null]);
        }
    }

    public function fetchOrderReview($order){
        $result = Orders::join('users', 'users.id', '=', 'orders.user')
        ->join('vehicles', 'vehicles.id', '=', 'orders.vehicle')
        ->where('orders.id', $order)
        ->select(
            'orders.id',
            'orders.order_number',
            'users.firstname',
            'users.lastname',
            'users.mobile',
            'orders.created_at',
            'orders.status'
        )->first();

        if($result){
            $parts = Orderparts::join('parts', 'orderparts.part', '=', 'parts.id')
                    ->join('part_brands', 'part_brands.id', '=', 'parts.part_brand')
                    ->select(
                        'part_brands.brand_name as brandName',
                        'parts.part_model as brandModel',
                        'parts.part_number',
                        'parts.sku',
                        'orderparts.cost', 
                        'orderparts.discount',
                        'orderparts.tax',
                        'orderparts.quantity', 
                        )
                    ->where('orderparts.order', $order)->get();
            
            $services = Orderservices::join('services', 'orderservices.service', '=', 'services.id')
                        ->select(
                            'services.name',
                            'orderservices.cost', 
                            'orderservices.discount',
                            'orderservices.tax',
                        )->where('orderservices.order', $order)->get();

            return response()->json(['status' => true, 'message' => 'Data found', 'data' => [
                'order' => $result,
                'services' => $services,
                'parts' => $parts
            ]]);
        }else{
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    public function calculateService($cost, $discountPercent, $taxPercent){
        $discount = $cost - ($cost * ($discountPercent / 100));
        $tax = $discount * ($taxPercent / 100);
        $total = $discount + $tax;
        return [
            'discount' => $discount,
            'tax' => $tax,
            'total' => $total
        ];
    }

    public function calculateParts($cost, $discountPercent, $taxPercent, $quantity){
        $totalCost = $cost * $quantity;
        $discountedPricePerItem = $cost - ($cost * ($discountPercent / 100));
        $totalDiscountedPrice = $discountedPricePerItem * $quantity;
        $taxAmountPerItem = ($discountedPricePerItem * ($taxPercent / 100));
        $totalTaxAmount = $taxAmountPerItem * $quantity;
        $totalAmount = $totalDiscountedPrice + $totalTaxAmount;

        return [
            'discount' => $totalDiscountedPrice,
            'tax' => $totalTaxAmount,
            'total' => $totalAmount
        ];
    }

    public function placeOrder($order){

        $result = Orders::where('id', $order)->first();
        if($result){
            $parts = Orderparts::where('order', $order)->get();
            $services = Orderservices::where('order', $order)->get();

            $discount = 0;
            $tax = 0;
            $total = 0;

            if($services){
                foreach($services as $service){
                    $serviceCalc = $this->calculateService($service->cost, $service->discount, $service->tax);
                    $discount = $discount + ($serviceCalc['total'] - $serviceCalc['discount']);
                    $tax = $tax + $serviceCalc['tax'];
                    $total = $total + $serviceCalc['total'];
                }
            }

            if($parts){
                foreach($parts as $part){
                    $partCalc = $this->calculateParts($part->cost, $part->discount, $part->tax, $part->quantity);
                    $discount = $discount + ($partCalc['total'] - $partCalc['discount']);
                    $tax = $tax + $partCalc['tax'];
                    $total = $total + $partCalc['total'];
                }
            }

            $result->gst = ceil($tax);
            $result->discount = ceil($discount);
            $result->total_amount = ceil($total);
            $result->due_amount = ceil($total);
            $result->status = 'accepted';
            $result->save();

            return response()->json(['status' => true, 'message' => 'Order placed successfuly', 'data' => $result]);
        }else{
            return response()->json(['status' => false, 'message' => 'Failed to place order', 'data' => null]);
        }
    }

    public function ordersCount($garage){
        $accepted = Orders::where(['garage' => $garage, 'status' => 'accepted'])->count();
        $drafted = Orders::where(['garage' => $garage, 'status' => 'drafted'])->count();
        $inprocess = Orders::where(['garage' => $garage, 'status' => 'inprocess'])->count();
        $open = Orders::where(['garage' => $garage, 'status' => 'open'])->count();
        $ready = Orders::where(['garage' => $garage, 'status' => 'ready'])->count();
        $completed = Orders::where(['garage' => $garage, 'status' => 'completed'])->count();
        $unpaid = Orders::where(['garage' => $garage, 'status' => 'unpaid'])->count();
        $canceled = Orders::where(['garage' => $garage, 'status' => 'cancel'])->count();
        $rejected = Orders::where(['garage' => $garage, 'status' => 'rejected'])->count();

        $users = User::count();
        $employees = Employees::count();

        return response()->json(['status' => true, 'message' => 'Data found', 'data' => [
            'drafted' => $drafted,
            'open' => $open,
            'accepted' => $accepted,
            'inprocess' => $inprocess,
            'ready' => $ready,
            'completed' => $completed,
            'unpaid' => $unpaid,
            'canceled' => $canceled,
            'rejected' => $rejected,
            'users' => $users,
            'employees' => $employees
        ]]);

    }
}
