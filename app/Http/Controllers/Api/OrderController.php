<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


        try {
            $orders = Order::with('items')->where('status', '=', 'انتظار')->get();
            return response()->json([
                'status' => true,
                'orders' => $orders,
            ], 200);
        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $order = Order::with('items')->find($id);
        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order Not Found.'
            ], 404);
        }

        // Return Json Response
        return response()->json([
            'status' => true,
            'order' => $order
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {

        try {
            // Find order
            $order = Order::find($id);
            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order Not Found.'
                ], 404);
            }

            $order->status = 'تم القبول';


            $order->save();

            // Return Json Response
            return response()->json([
                'status' => true,
                'message' => "Order successfully updated.",
                'order' => $order
            ], 200);
        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'status' => false,
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        // Detail
        $order = Order::find($id);
        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'order Not Found.'
            ], 404);
        }

        // Delete category
        $order->delete();

        // Return Json Response
        return response()->json([
            'status' => true,
            'message' => "Order successfully deleted."
        ], 200);
    }

    public function putDebt($id)
    {

        $order = Order::select('user_id', 'total_price')->find($id);
        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order Not Found.'
            ], 404);
        }

        $user = User::find($order->user_id);

        $user->user_debt_amount = $user->user_debt_amount + $order->total_price;

        // Update debt
        $user->save();
        // Return Json Response
        return response()->json([
            'status' => true,
            'message' => "Debt send to user.",
            'order' => $order->total_price,
        ], 200);
    }
}
