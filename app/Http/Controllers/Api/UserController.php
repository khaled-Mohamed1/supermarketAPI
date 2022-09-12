<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Offer;
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

    public function categoryShow(Request $request)
    {
        // category Detail
        $category = Category::with('prodcuts')->find($request->category_id);
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

    public function productShow(Request $request)
    {

        // Product Detail
        $product = Product::find($request->product_id);
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

    public function offerIndex()
    {

        $offers = Offer::all();
        return response()->json([
            'status' => true,
            'offers' => $offers
        ], 200);
    }

    public function mostOrders()
    {

        $products = Product::where('order_qty', '>', '0')->take(5)->get();
        return response()->json([
            'status' => true,
            'products' => $products
        ], 200);
    }


    public function storeOrder(Request $request)
    {

            try {
                $total_price = 0;
                $items = $request->all();

                foreach ($items['orders'] as $key => $value) {
                    if($value['product_status'] == true){
                        $offer = Offer::find($value['product_id']);
                        if (!$offer) {
                            return response()->json([
                                'status' => false,
                                'message' => 'offer Not Found.'
                            ], 404);
                        }
                        $total_price += $offer->offer_price * $value['product_quantity'];
                        if ($offer->offer_quantity <  $value['product_quantity']) {
                            return response()->json([
                                'message' => $offer->offer_name . " quantity not enough in offer"
                            ], 500);
                        }
                    }else{
                        $product = Product::find($value['product_id']);
                        if (!$product) {
                            return response()->json([
                                'status' => false,
                                'message' => 'product Not Found.'
                            ], 404);
                        }
                        $total_price += $product->product_price * $value['product_quantity'];
                        if ($product->product_quantity <  $value['product_quantity']) {
                            return response()->json([
                                'message' => $product->product_name . " quantity not enough"
                            ], 500);
                        }
                    }
                }

                foreach ($items['orders'] as $key => $value) {
                    if(!$value['product_status'] == true){
                        $product = Product::find($value['product_id']);
                        $product->order_qty = $value['product_quantity'] + $product->order_qty;
                        $product->save();
                    }

                }



                // Create order
                $order = Order::create([
                    'user_id' => $request->user_id,
                    'total_price' => $total_price,
                ]);

                $order_id = $order->id;

                foreach ($items['orders'] as $key => $value) {
                    if($value['product_status'] == true){
                        $item = Item::create([
                            'order_id' => $order_id,
                            'offer_id' => $value['product_id'],
                            'product_quantity' => $value['product_quantity'],
                            'price' => $offer->offer_price * $value['product_quantity'],
                        ]);
                        Offer::find($value['product_id'])->decrement('offer_quantity', $value['product_quantity']);;

                    }else{
                        $item = Item::create([
                            'order_id' => $order_id,
                            'product_id' => $value['product_id'],
                            'product_quantity' => $value['product_quantity'],
                            'price' => $product->product_price * $value['product_quantity'],
                        ]);
                        Product::find($value['product_id'])->decrement('product_quantity', $value['product_quantity']);;
                    }

                }

                // Return Json Response
                return response()->json([
                    'status' => true,
                    'message' => "Order Created successfully",
                    'Order' => $order
                ], 200);
            }catch (\Exception $e){
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage()
                ], 500);
            }

    }



    //log out
    public function userLogout(Request $request)
    {
        try {
//            $request->user()->currentAccessToken()->delete();
            $request->user()->tokens()->delete();
            return [
                'status' => true,
                'message' => 'User Logged out'
            ];
        }catch (\Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }


    }

    public function userUpdate(Request $request)
    {

            try {
                // Find user
                $user = User::find($request->user_id);
                if (!$user) {
                    return response()->json([
                        'status' => false,
                        'message' => 'User Not Found.'
                    ], 404);
                }

                if($user->phone === $request->phone){

                    $user->name = $request->name;
                    $user->phone = $request->phone;
                    $user->user_address = $request->user_address;
                }else{
                    $validateUser = Validator::make(
                        $request->all(),
                        [
                            'name' => 'required',
                            'phone' => 'required|numeric|unique:users',
                            'user_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                            'user_address' => 'required',
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
                }



                if ($request->user_image) {
                    // Public storage
                    $storage = Storage::disk('public');

                    // Old iamge delete
                    if ($storage->exists('users/' . $user->user_image))
                        $storage->delete('users/' . $user->user_image);

                    // Image name
                    $imageName = Str::random(32) . "." . $request->user_image->getClientOriginalExtension();

                    $user->user_image = 'http://node.tojar-gaza.com/storage/app/public/users/' . $imageName;

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
            }catch (\Exception $e){
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage()
                ], 500);
            }

    }

    public function getOrder(Request $request)
    {

        try {
            $user = User::find($request->user_id);
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User Not Found.'
                ], 404);
            }
            $orders = Order::with('items')->where('user_id',  $request->user_id)->get();
            return response()->json([
                'status' => true,
                'orders' => $orders,
            ], 200);
        }catch (\Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }

    }
}
