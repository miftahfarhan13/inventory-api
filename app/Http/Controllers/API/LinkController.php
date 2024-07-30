<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LinkController extends Controller
{
    //
    public function getLinks()
    {
        try {
            $links = Link::get();
            //return successful response
            return response()->json(['error' => false, 'result' => $links], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function updateLink($linkId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()], 406);
        }

        try {
            $link = Link::find($linkId);
            if (!$link) {
                return response()->json(['error' => true, 'message' => 'Link not found'], 406);
            }
            $link->url = $request->input('url');

            $link->save();

            //return successful response
            return response()->json(['error' => false, 'result' => $link, 'message' => 'data saved'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }
}
