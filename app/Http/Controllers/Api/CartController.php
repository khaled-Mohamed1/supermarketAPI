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
        $cartItems = Cart::where('user_id', $request->user_id)->with('ProductCart', 'OfferCart')->get();
        $cartCount = $cartItems->count();

        return response()->json([
            'status' => true,
            'CartCount' => $cartCount,
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
                        'message' => 'item is exist Found.',
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
                        'message' => 'item is exist Found.',
                    ], 404);
                }
            }


            // Return Json Response
            return response()->json([
                'status' => true,
                'message' => "cart Created successfully",
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
                'message' => "Cart successfully updated.",
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
                'message' => 'item Not Found.'
            ], 404);
        }

        // Delete cart
        $cart->delete();

        // Return Json Response
        return response()->json([
            'status' => true,
            'message' => "Cart successfully deleted."
        ], 200);
    }

    public function clearAllCart(Request $request): JsonResponse
    {

        Cart::where('user_id', $request->user_id)->truncate();

        // Return Json Response
        return response()->json([
            'status' => true,
            'message' => "Cart successfully Cleared."
        ], 200);    }
}
