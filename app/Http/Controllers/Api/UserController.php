<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function categoriesIndex()
    {

        $categories = Category::with('prodcuts')->get();
        return response()->json([
            'status' => true,
            'categories' => $categories
        ], 200);
    }

    public function categoryShow($id)
    {
        // category Detail
        $category = Category::with('prodcuts')->find($id);
        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category Not Found.'
            ], 404);
        }

        // Return Json Response
        return response()->json([
            'status' => true,
            'category' => $category
        ], 200);
    }

    public function productsIndex()
    {

        $products = Product::all();
        return response()->json([
            'status' => true,
            'products' => $products
        ], 200);
    }

    public function productShow($id)
    {

        // Product Detail
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'product Not Found.'
            ], 404);
        }

        // Return Json Response
        return response()->json([
            'status' => true,
            'product' => $product
        ], 200);
    }

    public function storeOrder(Request $request)
    {
        $user = auth()->user();

        if ($user->role == '0') {
            try {
                $total_price = 0;
                $items = $request->all();


                foreach ($items['orders'] as $key => $value) {
                    $product = Product::find($value['product_id']);
                    $total_price += $product->product_price * $value['product_quantity'];
                    if ($product->product_quantity <  $value['product_quantity']) {
                        return response()->json([
                            'message' => $product->product_name . " quantity not enough"
                        ], 500);
                    }
                }

                // Create order
                $order = Order::create([
                    'user_id' => $user->id,
                    'total_price' => $total_price,
                ]);

                $order_id = $order->id;

                foreach ($items['orders'] as $key => $value) {
                    $item = Item::create([
                        'order_id' => $order_id,
                        'product_id' => $value['product_id'],
                        'product_quantity' => $value['product_quantity'],
                        'price' => $product->product_price * $value['product_quantity'],
                    ]);
                   Product::find($value['product_id'])->decrement('product_quantity', $value['product_quantity']);;

                }

                // Return Json Response
                return response()->json([
                    'status' => true,
                    'message' => "Order Created successfully",
                    'Order' => $order
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



    //log out
    public function userLogout(Request $request)
    {
        $user = auth()->user();

        if ($user->role == 0) {
            auth()->user()->tokens()->delete();
            return [
                'status' => true,
                'User' => 'User ' . $user->name,
                'message' => 'Logged out'
            ];
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
    }

    public function userUpdate(Request $request)
    {
        $user = auth()->user();

        if ($user->role == 0) {
            try {
                // Find user
                $user = User::find($user->id);
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

                $user->name = $request->name;
                $user->phone = $request->phone;
                $user->user_address = $request->user_address;
                $user->password = Hash::make($request->password);

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
}
