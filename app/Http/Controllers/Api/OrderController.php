<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {


        try {
            $orders = Order::with('items.OfferItem','items.ProductItem','UserOrder')
                ->orWhere('status', '=', 'انتظار')
                ->orWhere('status', '=','تم القبول')
                ->latest()->get();
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderShow(Request $request)
    {

        $order = Order::with('items')->find($request->order_id);
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderUpdate(Request $request)
    {

        try {
            // Find order
            $order = Order::find($request->order_id);
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
        }catch (\Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function orderUpdateStatus(Request $request)
    {

        try {
            // Find order
            $order = Order::find($request->order_id);
            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order Not Found.'
                ], 404);
            }

            $order->status = $request->status;


            $order->save();

            // Return Json Response
            return response()->json([
                'status' => true,
                'message' => "Order successfully updated.",
                'order' => $order
            ], 200);
        }catch (\Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderDelete(Request $request)
    {

        // Detail
        $order = Order::find($request->order_id);
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

    public function putDebt(Request $request)
    {

        $order = Order::select('user_id', 'total_price')->find($request->order_id);
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

        Notification::create([
            'user_id' => $user->id,
            'notice_description'=> 'الرجاء من حضرتكم السيد: '.$user->name.' تسديد الدين بمبلغ ' . $user->user_debt_amount,
        ]);
        // Return Json Response
        return response()->json([
            'status' => true,
            'message' => "Debt send to user.",
            'order' => $order->total_price,
        ], 200);
    }
}
