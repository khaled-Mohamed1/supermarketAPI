<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class UserController extends Controller
{
    public function categoriesIndex()
    {
        $admin = auth()->user();

        if ($admin->role == '0') {
            $categories = Category::with('prodcuts')->get();
            return response()->json([
                'status' => true,
                'categories' => $categories
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
    }

    public function categoryShow($id)
    {
        $admin = auth()->user();

        if ($admin->role == '0') {
            // category Detail
            $category = Category::with('prodcuts')->find($id);
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
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
    }

    public function productsIndex()
    {
        $admin = auth()->user();

        if ($admin->role == '0') {
            $products = Product::all();
            return response()->json([
                'status' => true,
                'products' => $products
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
    }

    public function productShow($id)
    {
        $admin = auth()->user();

        if ($admin->role == '0') {
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
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
    }



    //log out
    public function userLogout(Request $request)
    {
        $user = auth()->user();

        if ($user->role == 0) {
            auth()->user()->tokens()->delete();
            return [
                'status' => true,
                'User' => 'User ' . $user->name,
                'message' => 'Logged out'
            ];
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
    }
}
