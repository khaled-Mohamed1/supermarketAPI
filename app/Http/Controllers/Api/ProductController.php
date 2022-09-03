<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreProductRequest;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $products = Product::all();
        return response()->json([
            'status' => true,
            'products' => $products
        ], 200);
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
    public function store(StoreProductRequest $request)
    {

        try {
            $imageName = Str::random(32) . "." . $request->product_image->getClientOriginalExtension();

            // Create product
            $product = Product::create([
                'category_id' => $request->category_id,
                'product_name' => $request->product_name,
                'product_image' => 'http://shaker.tojar-gaza.com/storage/app/public/products/' . $imageName,
                'product_description' => $request->product_description,
                'product_quantity' => $request->product_quantity,
                'product_price' => $request->product_price,
            ]);

            // Save Image in Storage folder
            Storage::disk('public')->put('products/' . $imageName, file_get_contents($request->product_image));

            // Return Json Response
            return response()->json([
                'status' => true,
                'message' => "Product Created successfully",
                'product' => $product
            ], 200);
        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(StoreProductRequest $request, $id)
    {

        try {
            // Find product
            $product = Product::find($id);
            if (!$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'product Not Found.'
                ], 404);
            }

            $product->category_id = $request->category_id;
            $product->product_name = $request->product_name;
            $product->product_description = $request->product_description;
            $product->product_quantity = $request->product_quantity;
            $product->product_price = $request->product_price;

            if ($request->product_image) {
                // Public storage
                $storage = Storage::disk('public');

                // Old iamge delete
                if ($storage->exists('products/' . $product->product_image))
                    $storage->delete('products/' . $product->product_image);

                // Image name
                // $imageName = Str::random(32).".".$request->image->getClientOriginalExtension();
                $new_image = time() . $request->product_image->getClientOriginalName();

                $product->product_image = $new_image;

                // Image save in public folder
                $storage->put('products/' . $new_image, file_get_contents($request->product_image));
            }

            // Update product
            $product->save();

            // Return Json Response
            return response()->json([
                'status' => true,
                'message' => "product successfully updated.",
                'product' => $product
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
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        // Detail
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'product Not Found.'
            ], 404);
        }

        // Public storage
        $storage = Storage::disk('public');

        // Iamge delete
        if ($storage->exists('products/' . $product->product_image))
            $storage->delete('products/' . $product->product_image);

        // Delete product
        $product->delete();

        // Return Json Response
        return response()->json([
            'status' => true,
            'message' => "product successfully deleted."
        ], 200);
    }
}
