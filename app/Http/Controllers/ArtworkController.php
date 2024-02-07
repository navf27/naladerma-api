<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use Illuminate\Http\Request;

class ArtworkController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'label' => 'required',
            'img_link' => 'required',
        ]);

        $artwork = Artwork::create($request->all());

        $response['status'] = true;
        $response['message'] = 'Create artwork success.';
        $response['data'] = $artwork;

        return response()->json($response);
    }

    public function show($id)
    {
        $artwork = Artwork::with('events:id,name')->findOrFail($id);

        $response['status'] = true;
        $response['data'] = $artwork;

        return response()->json($response);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'max:255',
            'label' => 'max:45',
            'img_link' => 'max:255',
        ]);

        $artwork = Artwork::findOrFail($id);
        $artwork->update($request->all());

        $response['status'] = true;
        $response['message'] = 'Update artwork success.';
        $response['data'] = $artwork;

        return response()->json($response);
    }

    public function setOnGallery($id)
    {
        $artwork = Artwork::findOrFail($id);
        $artwork['on_gallery'] = !$artwork['on_gallery'];
        $artwork->save();

        $response['status'] = true;
        $response['message'] = 'Set on gallery success.';
        $response['data'] = $artwork;

        return response()->json($response);
    }

    public function destroy($id)
    {
        $artwork = Artwork::findOrFail($id);
        $artwork->delete();

        $response['status'] = true;
        $response['message'] = 'Delete artwork success.';
        $response['data'] = $artwork;

        return response()->json($response);
    }
}
