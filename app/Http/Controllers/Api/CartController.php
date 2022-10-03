<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function cartList(Request $request): JsonResponse
    {
        $total = 0;
        $cartItems = Cart::where('user_id', $request->user_id)->with('ProductCart', 'OfferCart')->latest()->get();
        $cartCount = $cartItems->count();
        foreach ($cartItems as $cartItem){
            if($cartItem->ProductCart == null){
                $total = $total + $cartItem->OfferCart->offer_price * $cartItem->product_quantity;
            }elseif($cartItem->OfferCart == null){
                $total = $total + $cartItem->ProductCart->product_price * $cartItem->product_quantity;
            }
        }

        return response()->json([
            'status' => true,
            'TotalPrice' => $total,
            'CartItems' => $cartItems,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addToCart(Request $request): JsonResponse
    {

        try {

            // Create cart
            if($request->offer_id){
                $item = Cart::where('offer_id', '=', $request->offer_id)->first();
                if ($item === null) {
                    $cart = Cart::create([
                        'user_id' => $request->user_id,
                        'offer_id' => $request->offer_id,
                    ]);

                }else{
                    return response()->json([
                        'status' => false,
                        'message' => 'المنتج غير موجود',
                    ], 404);
                }

            }elseif($request->product_id){
                $item = Cart::where('product_id', '=', $request->product_id)->first();
                if ($item === null) {
                    $cart = Cart::create([
                        'user_id' => $request->user_id,
                        'product_id' => $request->product_id,
                    ]);

                }else{
                    return response()->json([
                        'status' => false,
                        'message' => 'المنتج غير موجود',
                    ], 404);
                }
            }


            // Return Json Response
            return response()->json([
                'status' => true,
                'message' => "تم اضافة المنتج للسلة",
                'cart' => $cart
            ], 200);

        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Cart $cart
     * @return JsonResponse
     */
    public function updateCart(Request $request): JsonResponse
    {
        try {
            $cart = Cart::find($request->cart_id);
            if($request->product_quantity == -1){
                if($cart->product_quantity == 1){
                    return response()->json([
                        'status' => true,
                        'message' => "لا يمكن اخفاض الكمية",
                        'cart' => $cart
                    ], 200);
                }else{
                    Cart::find($request->cart_id)->increment('product_quantity', $request->product_quantity);
                    $cart = Cart::find($request->cart_id);
                }
            }else{
                Cart::find($request->cart_id)->increment('product_quantity', $request->product_quantity);
                $cart = Cart::find($request->cart_id);
            }


            // Return Json Response
            return response()->json([
                'status' => true,
                'message' => "تم تحديث المنتج",
                'cart' => $cart

            ], 200);
        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'status' => false,
                'message' => "Something went really wrong!"
            ], 500);
        }    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Cart $cart
     * @return JsonResponse
     */
    public function removeCart(Request $request): JsonResponse
    {
        // Detail
        $cart = Cart::find($request->cart_id);
        if (!$cart) {
            return response()->json([
                'status' => false,
                'message' => 'المنتج غير موجود'
            ], 404);
        }

        // Delete cart
        $cart->delete();

        // Return Json Response
        return response()->json([
            'status' => true,
            'message' => "تم حذف المنتج من السلة"
        ], 200);
    }

    public function clearAllCart(Request $request): JsonResponse
    {

        Cart::where('user_id', $request->user_id)->truncate();

        // Return Json Response
        return response()->json([
            'status' => true,
            'message' => "تم تصفية السلة"
        ], 200);    }
}
