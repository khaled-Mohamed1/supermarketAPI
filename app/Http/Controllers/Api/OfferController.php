<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\StoreOfferRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $offers = Offer::latest()->get();
        return response()->json([
            'status' => true,
            'offers' => $offers
        ], 200);    }

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
     * @return JsonResponse
     */
    public function store(StoreOfferRequest $request)
    {
        try {
            $imageName = Str::random(32) . "." . $request->offer_image->getClientOriginalExtension();

            // Create offer
            $offer = Offer::create([
                'offer_name' => $request->offer_name,
                'offer_image' => 'http://node.tojar-gaza.com/storage/app/public/offers/' . $imageName,
                'offer_quantity' => $request->offer_quantity,
                'offer_price' => $request->offer_price,
            ]);

            // Save Image in Storage folder
            Storage::disk('public')->put('offers/' . $imageName, file_get_contents($request->offer_image));

            // Return Json Response
            return response()->json([
                'status' => true,
                'message' => "تم انشاء منتج العرض",
                'offer' => $offer
            ], 200);
        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function offerUpdate(Request $request)
    {
        try {
            // Find offer
            $offer = Offer::find($request->offer_id);
            if (!$offer) {
                return response()->json([
                    'status' => false,
                    'message' => 'المنتج غير موجود'
                ], 404);
            }

            $offer->offer_name = $request->offer_name;
            $offer->offer_quantity = $request->offer_quantity;
            $offer->offer_price = $request->offer_price;

            if ($request->offer_image) {
                // Public storage
                $storage = Storage::disk('public');

                // Old iamge delete
                if ($storage->exists('offers/' . $offer->offer_image))
                    $storage->delete('offers/' . $offer->offer_image);

                // Image name
                $imageName = Str::random(32) . "." . $request->offer_image->getClientOriginalExtension();

                $offer->offer_image = 'http://node.tojar-gaza.com/storage/app/public/offers/' . $imageName;

                // Image save in public folder
                $storage->put('offers/' . $imageName, file_get_contents($request->offer_image));
            }

            // Update offer
            $offer->save();

            // Return Json Response
            return response()->json([
                'status' => true,
                'message' => "تم تحديث منتج العرض",
                'offer' => $offer
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
     * @param  int  $id
     * @return JsonResponse
     */
    public function offerDelete(Request $request)
    {

        // Detail
        $offer = Offer::find($request->offer_id);
        if (!$offer) {
            return response()->json([
                'status' => false,
                'message' => 'المنتج غير موجود'
            ], 404);
        }

        // Public storage
        $storage = Storage::disk('public');

        // Iamge delete
        if ($storage->exists('offers/' . $offer->offer_image))
            $storage->delete('offers/' . $offer->offer_image);

        // Delete offer
        $offer->delete();

        // Return Json Response
        return response()->json([
            'status' => true,
            'message' => "تم حذف منتج العرض"
        ], 200);    }
}
