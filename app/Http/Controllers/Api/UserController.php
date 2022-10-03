<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Notification;
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

        $categories = Category::with('products')->latest()->get();
        return response()->json([
            'status' => true,
            'categories' => $categories
        ], 200);
    }

    public function categoryShow(Request $request)
    {
        // category Detail
        $category = Category::with('products')->find($request->category_id);
        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'التصنيف غير موجود'
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

        $products = Product::latest()->get();
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
                'message' => 'المنتج غير موجود'
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

        $offers = Offer::latest()->get();
        return response()->json([
            'status' => true,
            'offers' => $offers
        ], 200);
    }

    public function mostOrders()
    {

        $products = Product::where('order_qty', '>', '0')->take(5)->latest()->get();
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
                $object = json_decode($request->orders);

                foreach ($object as $key => $value) {
                    if($value->product_status == true){
                        $offer = Offer::find($value->product_id);
                        if (!$offer) {
                            return response()->json([
                                'status' => false,
                                'message' => 'منتج العرض غير موجود'
                            ], 404);
                        }
                        $total_price += $offer->offer_price * $value->product_quantity;
                        if ($offer->offer_quantity <  $value->product_quantity) {
                            return response()->json([
                                'message' => $offer->offer_name . " الكمية غير كافية لمنتج العرض"
                            ], 500);
                        }
                    }else{
                        $product = Product::find($value->product_id);
                        if (!$product) {
                            return response()->json([
                                'status' => false,
                                'message' => 'المنتج غير موجود'
                            ], 404);
                        }
                        $total_price += $product->product_price * $value->product_quantity;
                        if ($product->product_quantity <  $value->product_quantity) {
                            return response()->json([
                                'message' => $product->product_name . " الكمية غير كافية"
                            ], 500);
                        }
                    }
                }

                foreach ($object as $key => $value) {
                    if(!$value->product_status == true){
                        $product = Product::find($value->product_id);
                        $product->order_qty = $value->product_quantity + $product->order_qty;
                        $product->save();
                    }

                }



                // Create order
                $order = Order::create([
                    'user_id' => $request->user_id,
                    'total_price' => $total_price,
                    'delivery_method' => $request->delivery_method,
                ]);

                $order_id = $order->id;

                foreach ($object as $key => $value) {
                    if($value->product_status == true){
                        $item = Item::create([
                            'order_id' => $order_id,
                            'offer_id' => $value->product_id,
                            'product_quantity' => $value->product_quantity,
                            'price' => $offer->offer_price * $value->product_quantity,
                        ]);
                        Offer::find($value->product_id)->decrement('offer_quantity', $value->product_quantity);
                        $offer = Offer::find($value->product_id);
                        if($offer->offer_quantity == 0){
                            $offer->delete();
                        }
                        //test
                    }else{
                        $item = Item::create([
                            'order_id' => $order_id,
                            'product_id' => $value->product_id,
                            'product_quantity' => $value->product_quantity,
                            'price' => $product->product_price * $value->product_quantity,
                        ]);
                        Product::find($value->product_id)->decrement('product_quantity', $value->product_quantity);
                    }

                }

                Cart::where('user_id', $request->user_id)->truncate();

                // Return Json Response
                return response()->json([
                    'status' => true,
                    'message' => "تم انشاء الطلبية",
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
                'message' => 'تم تسجيل الخروج'
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
                        'message' => 'لم يتم العثور على المستخدم'
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
                    'message' => "تم تحديث بيانات المستخدم",
                    'user' => $user
                ], 200);
            }catch (\Exception $e){
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage()
                ], 500);
            }

    }

    public function deptUser(Request $request){
        try {
            $user = User::findOrFail($request->user_id);
            return response()->json([
                'status' => true,
                'dept' => $user->user_debt_amount,
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
                    'message' => 'لم يتم العثور على المستخدم'
                ], 404);
            }
            $orders = Order::with('items')->where('user_id',  $request->user_id)->latest()->get();
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

    public function showNotification(Request $request){
        try {
            $notification = Notification::where('user_id', $request->user_id)->latest()->get();

            return response()->json([
                'status' => true,
                'Notification' => $notification,
            ], 200);
        }catch (\Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
