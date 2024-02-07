<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:45',
        ]);

        $category = Category::create($validated);

        $response['status'] = true;
        $response['message'] = 'Create category success.';
        $response['data'] = $category;

        return response()->json($response);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:45',
        ]);

        $category = Category::findOrFail($id);
        $category->update($validated);

        $response['status'] = true;
        $response['message'] = 'Update category success.';
        $response['data'] = $category;

        return response()->json($response);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        $response['status'] = true;
        $response['message'] = 'Delete category success.';
        $response['data'] = $category;

        return response()->json($response);
    }
}
