<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Garages;
use App\Models\Employees;
use Carbon\Carbon;
use Auth;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function create(Request $request, $garageid)
    {
        $ip = $request->ip();
        $user = Auth::user()->id;

        $request->validate([
            'user' => 'required|integer',
            'role' => 'required|integer',
        ]);

        $empIdByMobile = User::where('id', $request->user)->first();

        if (!$empIdByMobile) {
            return response()->json(['status' => false, 'message' => 'User not found', 'data' => null]);
        }

        if ($this->roleExistCheck($empIdByMobile->id)) {
            $garage = Employees::create([
                'user_id' => $empIdByMobile->id,
                'garage_id' => $garageid,
                'user_role' => $request->role,
                'added_by' => $user,
                'edited_by' => $user,
                'created_ip' => $ip,
                'last_ip' => $ip,
            ]);

            if ($garage) {
                $response = ['status' => true, 'message' => 'Employee created successfully', 'data' => null];
            } else {
                $response = ['status' => false, 'message' => 'Failed to create employee, Please try again', 'data' => null];
            }
        } else {
            $response = ['status' => false, 'message' => 'Employee already exists', 'data' => null];
        }

        return response()->json($response);
    }

    private function roleExistCheck($user)
    {
        $data = Employees::where('user_id', $user)->first();

        if ($data == null) {
            return true;
        } else {
            return false;
        }
    }

    public function updateEmployeeByGarage(Request $request, $garageid, $id)
    {
        $data = Employees::where(['id' => $id, 'garage_id' => $garageid])->update(['user_role' => $request->role]);

        if ($data) {
            return response()->json(['status' => true, 'message' => 'User role updated', 'data' => null]);
        } else {
            return response()->json(['status' => false, 'message' => 'Failed to update user role', 'data' => null]);
        }
    }

    public function get(Request $request, $empid)
    {
        $data = Employees::join('users', 'users.id', '=', 'employees.user_id')
            ->where('employees.id', $empid)
            ->where('employees.status', '1')
            ->where('employees.deleted', '0')
            ->select('employees.id', 'users.firstname', 'users.lastname', 'employees.user_role')
            ->first();

        if ($data) {
            $response = ['status' => true, 'message' => 'Employee data found', 'data' => $data];
        } else {
            $response = ['status' => false, 'message' => 'Employee data not found', 'data' => null];
        }

        return response()->json($response);
    }

    public function getEmployeeByGarage($id)
    {
        $data = Employees::join('users', 'users.id', '=', 'employees.user_id')
            ->where('employees.garage_id', $id)
            ->where('employees.status', '1')
            ->where('employees.deleted', '0')
            ->select('employees.id', 'users.firstname', 'users.lastname', 'users.mobile', 'users.email', 'employees.user_role')
            ->get();

        if ($data) {
            $response = ['status' => true, 'message' => 'Employee data found', 'data' => $data];
        } else {
            $response = ['status' => false, 'message' => 'Employee data not found', 'data' => null];
        }

        return response()->json($response);
    }

    public function remove(Request $request, $garageid, $empid)
    {
        $data = Employees::where('id', $empid)->delete();

        if ($data) {
            $response = ['status' => true, 'message' => 'Deleted successfully', 'data' => null];
        } else {
            $response = ['status' => false, 'message' => 'Employee not found, Please try again', 'data' => null];
        }

        return response()->json($response);
    }

    public function fetchMechanic($garage)
    {
        $result = Employees::join('users', 'users.id', 'employees.user_id')
            ->where(['employees.garage_id' => $garage, 'employees.user_role' => '3'])
            ->select('employees.id', 'users.firstname', 'users.lastname', 'users.mobile', 'employees.user_role')
            ->get();

        if ($result) {
            return response()->json(['status' => true, 'message' => 'Employee data found', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'message' => 'Employee data not found', 'data' => null]);
        }
    }
}
