<?php

namespace App\Http\Controllers\Api;




//اخي الكريم محمد شعبان .............


use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function create(Request $request)
    {
        try {
            //Validated
            if ($request->hasFile('user_image')) {
                $validateUser = Validator::make(
                    $request->all(),
                    [
                        'name' => 'required',
                        'phone' => 'required|numeric|unique:users',
                        'user_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                        'user_address' => 'required',
                        'password' => 'required'
                    ]
                );

                if ($validateUser->fails()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'validation error',
                        'errors' => $validateUser->errors()
                    ], 401);
                }

                //image
                // $new_image = time() . $request->user_image->getClientOriginalName();
                $imageName = Str::random(32) . "." . $request->user_image->getClientOriginalExtension();


                $user = User::create([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'user_address' => $request->user_address,
                    'user_image' => $imageName,
                    'password' => Hash::make($request->password),
                ]);

                // Save Image in Storage folder
                Storage::disk('public')->put('users/' . $imageName, file_get_contents($request->user_image));
            }

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    //login admin
    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'phone' => 'required|numeric',
                    'password' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if (!Auth::attempt($request->only(['phone', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Phone & Password does not match with our record.',
                ], 401);
            }

            $user = User::where('phone', $request->phone)->first();

            if ($user->role == '1') {

                return response()->json([
                    'status' => true,
                    'message' => 'Admin Logged In Successfully',
                    'token' => $user->createToken("API TOKEN")->plainTextToken
                ], 200);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'User Logged In Successfully',
                    'token' => $user->createToken("API TOKEN")->plainTextToken
                ], 200);
            }
        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $admin = auth()->user();

        if ($admin->role == '1') {
            auth()->user()->tokens()->delete();
            return [
                'status' => true,
                'Admin' => 'Admin ' . $admin->name,
                'message' => 'Logged out'
            ];
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
    }

    public function getUsers(Request $request)
    {
        $admin = auth()->user();

        if ($admin->role == '1') {
            try {
                $users = User::where('role', '=', '0')->get();
                return response()->json([
                    'status' => true,
                    'users' => $users,
                ], 200);
            } catch (\Exception $e) {
                // Return Json Response
                return response()->json([
                    'message' => "Something went really wrong!"
                ], 500);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
    }

    public function updateUser(Request $request, $id)
    {
        $admin = auth()->user();

        if ($admin->role == 1) {
            try {
                // Find user
                $user = User::find($id);
                if (!$user) {
                    return response()->json([
                        'status' => false,
                        'message' => 'User Not Found.'
                    ], 404);
                }

                $validateUser = Validator::make(
                    $request->all(),
                    [
                        'name' => 'required',
                        'phone' => 'required|numeric|unique:users',
                        'user_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                        'user_address' => 'required'
                    ]
                );

                if ($validateUser->fails()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'validation error',
                        'errors' => $validateUser->errors()
                    ], 401);
                }

                $user->name = $request->name;
                $user->phone = $request->phone;
                $user->user_address = $request->user_address;

                if ($request->user_image) {
                    // Public storage
                    $storage = Storage::disk('public');

                    // Old iamge delete
                    if ($storage->exists('users/' . $user->user_image))
                        $storage->delete('users/' . $user->user_image);

                    // Image name
                    $imageName = Str::random(32) . "." . $request->user_image->getClientOriginalExtension();

                    $user->user_image = $imageName;

                    // Image save in public folder
                    $storage->put('users/' . $imageName, file_get_contents($request->user_image));
                }

                // Update user
                $user->save();

                // Return Json Response
                return response()->json([
                    'status' => true,
                    'message' => "User successfully updated.",
                    'user' => $user
                ], 200);
            } catch (\Exception $e) {
                // Return Json Response
                return response()->json([
                    'status' => false,
                    'message' => "Something went really wrong!"
                ], 500);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
    }

    public function deleteUser($id)
    {
        $admin = auth()->user();

        if ($admin->role == '1') {
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'user Not Found.'
                ], 404);
            } elseif ($user->role == '1') {
                return response()->json([
                    'status' => false,
                    'message' => 'can not delete admin.'
                ], 404);
            }

            // Public storage
            $storage = Storage::disk('public');

            // Iamge delete
            if ($storage->exists('users/' . $user->user_image))
                $storage->delete('users/' . $user->user_image);

            // Delete user
            $user->delete();

            // Return Json Response
            return response()->json([
                'status' => true,
                'message' => "Users successfully deleted."
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
    }

    public function statistics()
    {
        $admin = auth()->user();

        if ($admin->role == '1') {
            try {
                $orders = Order::count();
                $sales = Order::sum('total_price');
                $debts = User::sum('user_debt_amount');
                $product_qty = Product::where('product_quantity', '0')->get();
                return response()->json([
                    'status' => true,
                    'orders' => $orders,
                    'sales' => $sales - $debts,
                    'debts' => $debts,
                    'product_qty' => $product_qty,
                ], 200);
            } catch (\Exception $e) {
                // Return Json Response
                return response()->json([
                    'message' => "Something went really wrong!"
                ], 500);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
    }

    public function usersDebts()
    {
        $admin = auth()->user();

        if ($admin->role == '1') {
            try {
                $users = User::where('role', '0')->where('user_debt_amount', '>', '0')->get();
                return response()->json([
                    'status' => true,
                    'users' => $users,
                ], 200);
            } catch (\Exception $e) {
                // Return Json Response
                return response()->json([
                    'message' => "Something went really wrong!"
                ], 500);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
    }

    public function updateUserDebt(Request $request, $id)
    {
        $admin = auth()->user();

        if ($admin->role == '1') {
            try {
                $user = User::find($id);

                $user->user_debt_amount = $request->debt;
                $user->save();

                return response()->json([
                    'status' => true,
                    'message' => "UserDebt successfully updated.",
                    'users' => $user,
                ], 200);
            } catch (\Exception $e) {
                // Return Json Response
                return response()->json([
                    'message' => "Something went really wrong!"
                ], 500);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
    }
}
