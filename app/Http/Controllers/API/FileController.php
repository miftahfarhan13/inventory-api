<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FileController extends Controller
{
    //
    public function storeImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:png,jpg,jpeg,webp|max:512',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()], 406);
        }

        $fileName = time() . '-' . str_replace(" ", "_", $request->file->getClientOriginalName());

        $request->file->move(public_path('uploads/images'), $fileName);

        $currUrl = url('');

        /*  
            Write Code Here for
            Store $fileName name in DATABASE from HERE 
        */

        return response()->json(['error' => false, 'result' => $currUrl . '/uploads/images/' . $fileName], 200);
    }

    public function storeFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:pdf,docx,xlsx,csv',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()], 406);
        }

        $fileName = time() . '-' . str_replace(" ", "_", $request->file->getClientOriginalName());

        $request->file->move(public_path('uploads/files'), $fileName);

        $currUrl = url('');

        /*  
            Write Code Here for
            Store $fileName name in DATABASE from HERE 
        */

        return response()->json(['error' => false, 'result' => $currUrl . '/uploads/files/' . $fileName], 200);
    }
}
