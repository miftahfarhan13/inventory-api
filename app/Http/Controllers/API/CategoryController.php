<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    //
    public function getCategories(Request $request)
    {
        try {
            $isPaginate = !empty($request->is_paginate) ? filter_var($request->query('is_paginate'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : true;

            if ($isPaginate) {
                $categories = Category::paginate($request->per_page ?? 15);
            } else {
                $categories = Category::all();
            }
            //return successful response
            return response()->json(['error' => false, 'result' => $categories], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function createCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()], 406);
        }

        $user = Auth::user();

        try {
            $category = new Category();
            $category->name = $request->input('name');
            $category->created_by = $user->id;
            $category->save();

            $category = Category::where('id', $category->id)->first();

            //return successful response
            return response()->json(['error' => false, 'result' => $category, 'message' => 'data saved'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function updateCategory($categoryId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()], 406);
        }

        try {
            $category = Category::find($categoryId);
            if (!$category) {
                return response()->json(['error' => true, 'message' => 'Category not found'], 406);
            }
            $category->name = $request->input('name');

            $category->save();

            //return successful response
            return response()->json(['error' => false, 'result' => $category, 'message' => 'data saved'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function deleteCategory($categoryId)
    {
        try {
            Category::where('id', $categoryId)->delete();
            //return successful response
            return response()->json(['error' => false, 'message' => 'data deleted'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }
}
