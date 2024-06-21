<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customers;
use App\Models\Vehicles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function createCustomer(Request $request, $garage)
    {
        $check = User::where('mobile', $request->mobile)->first();

        $ip = $request->ip();
        if (isset($check)) {
            $request->validate([
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'mobile' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'country' => 'integer|nullable',
                'state' => 'integer|nullable',
                'city' => 'integer|nullable',
                'street' => 'string|max:255|nullable',
                'zipcode' => 'string|max:255|nullable',
                'lat' => 'numeric|nullable',
                'lng' => 'numeric|nullable',
            ]);

            $customer = Customers::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'country' => $request->country,
                'state' => $request->state,
                'city' => $request->city,
                'street' => $request->street,
                'zipcode' => $request->zipcode,
                // 'lat' => $request->lat,
                // 'lng' => $request->lng,
                'added_by' => Auth::user()->id,
                'edited_by' => Auth::user()->id,
                'user_id' => $check->id,
                'garage_id' => $garage,
            ]);

            if ($customer) {
                return response()->json([
                    'status' => true,
                    'message' => 'Customer registered successfully',
                    'data' => $customer,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to register customer',
                    'data' => null,
                ]);
            }
        } else {
            $request->validate([
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'mobile' => 'required|string|max:255',
                'email' => 'required|email|max:255',
            ]);

            $user = User::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'first_ip' => $ip,
                'last_ip' => $ip,
                'first_login' => Carbon::now(),
                'last_login' => Carbon::now(),
                'user_type' => 4,
                'password' => Hash::make('Password@123'),
            ]);

            $request->validate([
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'mobile' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'country' => 'integer|nullable',
                'state' => 'integer|nullable',
                'city' => 'integer|nullable',
                'street' => 'string|max:255|nullable',
                'zipcode' => 'string|max:255|nullable',
                'lat' => 'numeric|nullable',
                'lng' => 'numeric|nullable',
            ]);

            $customer = Customers::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'country' => $request->country,
                'state' => $request->state,
                'city' => $request->city,
                'street' => $request->street,
                'zipcode' => $request->zipcode,
                'lat' => $request->lat,
                'lng' => $request->lng,
                'added_by' => Auth::user()->id,
                'edited_by' => Auth::user()->id,
                'user_id' => $user->id,
                'garage_id' => $garage,
            ]);

            if ($customer) {
                return response()->json([
                    'status' => true,
                    'message' => 'Customer registered successfully',
                    'data' => $customer,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to register customer',
                    'data' => null,
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'User created successfully',
            'data' => null,
        ]);
    }

    public function updateCustomer(Request $request, $customerid)
    {
        $check = User::find($customerid)->first();

        $ip = $request->ip();
        if (isset($check)) {
            $request->validate([
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'mobile' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'country' => 'integer|nullable',
                'state' => 'integer|nullable',
                'city' => 'integer|nullable',
                'street' => 'string|max:255|nullable',
                'zipcode' => 'string|max:255|nullable',
                'lat' => 'numeric|nullable',
                'lng' => 'numeric|nullable',
            ]);

            $customer = Customers::where('id', $customerid)->update([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'country' => $request->country,
                'state' => $request->state,
                'city' => $request->city,
                'street' => $request->street,
                'zipcode' => $request->zipcode,
                'edited_by' => Auth::user()->id,
            ]);

            if ($customer) {
                return response()->json([
                    'status' => true,
                    'message' => 'Customer updated successfully',
                    'data' => $customer,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to update customer',
                    'data' => null,
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update, customer not found',
                'data' => null,
            ]);
        }

    }

    public function getAllCustomers(Request $request, $garage)
    {
        $data = Customers::join('users', 'customers.user_id', '=', 'users.id')
            ->leftjoin('states', 'customers.state', '=', 'states.id')
            ->leftjoin('cities', 'customers.city', '=', 'cities.id')
            ->where(['users.user_type' => '4', 'customers.garage_id' => $garage])
            ->select('customers.user_id', 'customers.id as customer_id', 'customers.garage_id', 'customers.firstname', 'customers.lastname', 'customers.mobile', 'customers.email', 'cities.name as cityname', 'states.name as statename')
            ->paginate(20);

        if ($data) {
            return response()->json(['status' => true, 'message' => 'data found', 'data' => $data]);
        } else {
            return response()->json(['status' => false, 'message' => 'data not found', 'data' => $data]);
        }
    }

    public function fetchCustomerById($id)
    {
        $result = Customers::where(['id' => $id, 'status' => '1', 'deleted' => '0'])->first();
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }

    public function searchCustomer(Request $request){
        $data = Customers::join('users', 'customers.user_id', '=', 'users.id')
        ->leftjoin('states', 'customers.state', '=', 'states.id')
        ->leftjoin('cities', 'customers.city', '=', 'cities.id')
        ->where('customers.firstname', 'like', '%' . $request->search . '%')
        ->orWhere('customers.lastname', 'like', '%' . $request->search . '%')
        ->orWhere('customers.mobile', 'like', '%' . $request->search . '%')
        // ->orWhere('customers.email', 'like', '%' . $request->search . '%')
        ->select('customers.user_id', 'customers.id as customer_id', 'customers.garage_id', 'customers.firstname', 'customers.lastname', 'customers.mobile', 'customers.email', 'cities.name as cityname', 'states.name as statename')
        ->paginate(20);

        if ($data) {
            return response()->json(['status' => true, 'message' => 'Data found', 'data' => $data]);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found', 'data' => null]);
        }
    }
}
