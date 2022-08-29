<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
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

        $admin = auth()->user();

        if ($admin->role == '1') {
            try {
                $orders = Order::with('items')->get();
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
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
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
        $admin = auth()->user();

        if ($admin->role == '1') {
            // category Detail
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
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
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
        $admin = auth()->user();

        if ($admin->role == '1') {
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


                // Update category
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
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
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
        $admin = auth()->user();

        if ($admin->role == '1') {
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
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
    }
}
