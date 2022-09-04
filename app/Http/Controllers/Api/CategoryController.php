<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {
            $categories = Category::with('prodcuts')->get();
            return response()->json([
                'status' => true,
                'categories' => $categories,
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
    public function store(StoreCategoryRequest $request)
    {

        try {

            $imageName = Str::random(32) . "." . $request->category_image->getClientOriginalExtension();

            // Create Category
            $category = Category::create([
                'category_name' => $request->category_name,
                'category_image' => 'http://node.tojar-gaza.com/storage/app/public/categories/' . $imageName,
            ]);

            // Save Image in Storage folder
            Storage::disk('public')->put('categories/' . $imageName, file_get_contents($request->category_image));

            // Return Json Response
            return response()->json([
                'status' => true,
                'message' => "Category Created successfully",
                'category' => $category
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
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        // category Detail
        $category = Category::find($id);
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(StoreCategoryRequest $request, $id)
    {

        try {
            // Find category
            $category = Category::find($id);
            if (!$category) {
                return response()->json([
                    'status' => false,
                    'message' => 'Category Not Found.'
                ], 404);
            }

            $category->category_name = $request->category_name;

            if ($request->category_image) {
                // Public storage
                $storage = Storage::disk('public');

                // Old iamge delete
                if ($storage->exists('categories/' . $category->category_image))
                    $storage->delete('categories/' . $category->category_image);

                // Image name
                // $imageName = Str::random(32).".".$request->image->getClientOriginalExtension();
                $new_image = time() . $request->category_image->getClientOriginalName();

                $category->category_image = $new_image;

                // Image save in public folder
                $storage->put('categories/' . $new_image, file_get_contents($request->category_image));
            }

            // Update category
            $category->save();

            // Return Json Response
            return response()->json([
                'status' => true,
                'message' => "Category successfully updated.",
                'category' => $category
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
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        // Detail
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'category Not Found.'
            ], 404);
        }

        // Public storage
        $storage = Storage::disk('public');

        // Iamge delete
        if ($storage->exists('categories/' . $category->category_image))
            $storage->delete('categories/' . $category->category_image);

        // Delete category
        $category->delete();

        // Return Json Response
        return response()->json([
            'status' => true,
            'message' => "Category successfully deleted."
        ], 200);
    }
}
