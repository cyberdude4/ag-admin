<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use App\Models\Garages;
use App\Models\Categories;
use App\Models\Country;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'refresh']]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('mobile', 'password');
        $token = Auth::attempt($credentials);

        if (!$token) {
            return response()->json([
                'status' => false,
                'message' => 'Please enter valid credensials',
            ]);
        }

        $user = Auth::user();

        if ($user) {
            $countries = Country::all();
            $categories = Categories::all();
            $garage = Garages::where('user_id', $user->id)->first();
            return response()->json([
                'status' => true,
                'user' => $user,
                'garages' => $garage,
                'categories' => $categories,
                'countries' => $countries,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ],
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Please enter valid credensials',
            ]);
        }
    }

    public function register(Request $request)
    {
        if (User::where('mobile', $request->mobile)->count() > 0) {
            return response()->json(['status' => false, 'message' => 'Mobile number already registered', 'data' => null]);
        }
        if (User::where('email', $request->email)->count() > 0) {
            return response()->json(['status' => false, 'message' => 'Email Address already registered', 'data' => null]);
        }

        $request->validate([
            'firstname' => 'required|string|min:3|max:50',
            'lastname' => 'required|string|min:3|max:50',
            'mobile' => 'required|unique:users|numeric',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|required_with:confirm_password|same:confirm_password|string|min:6',
            'confirm_password' => 'min:6',
        ]);

        $ip = $request->ip();
        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'first_ip' => $ip,
            'last_ip' => $ip,
            'first_login' => Carbon::now(),
            'last_login' => Carbon::now(),
            'password' => Hash::make($request->password),
        ]);

        if ($user) {
            $categories = Categories::all();
            $countries = Country::all();
            $garage = Garages::where('user_id', $user->id)->first();

            $token = Auth::login($user);

            // User::where('id', $user->id)->update(['refreshtoken' => $refreshToken]);
            return response()->json([
                'status' => true,
                'message' => 'User created successfully',
                'user' => $user,
                'countries' => $countries,
                'authorisation' => [
                    'token' => $token,
                    'token_type' => 'bearer',
                ],
            ]);
        } else {
            return response()->json(['status' => false, 'message' => 'Something went wrong', 'data' => null]);
        }
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => true,
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => true,
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ],
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'status' => true,
            'message' => 'Profile matched',
            'user' => Auth::user(),
        ]);
    }

    public function searchUserByMobile(Request $request)
    {
        $users = User::where('mobile', 'LIKE', '%' . $request->search . '%')
                ->select(
                    'id', 'firstname', 'lastname', 'mobile', 'email'
                )->get();
        if ($users) {
            return response()->json([
                'status' => true,
                'message' => 'Users found',
                'data' => $users,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Users not found',
                'data' => null,
            ]);
        }
    }
}
