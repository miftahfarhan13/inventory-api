<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'role' => 'required|string',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->plainTextToken;
        $success['name'] =  $user->name;

        return $this->sendResponse($success, 'User register successfully.');
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = User::where('email', $request->email)->first();
            $success['token'] =  $user->createToken('MyApp')->plainTextToken;
            $success['name'] =  $user->name;

            return $this->sendResponse($success, 'User login successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }

    public function me()
    {
        $user = Auth::user();

        if ($user) {
            return response()->json(['result' => $user]);
        } else {
            return response()->json(['error' => true, 'message' => 'User not found'], 406);
        }
    }

    public function logout(Request $request)
    {
        // auth()->logout();
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function deleteUser($id)
    {
        try {
            // $countUsers = Users::where('store_id', $categoryId)->count();
            // if ($countUsers > 0) {
            //     return response()->json(['error' => true, 'message' => 'gagal menghapus toko, masih terdapat akun yang menggunakan toko ini'], 406);
            // }
            $user = User::where('id', $id)->first();

            if (!$user) {
                return response()->json(['message' => 'User tidak ditemukan'], 401);
            }

            $user->delete();
            //return successful response
            return response()->json(['error' => false, 'message' => 'data deleted'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }
}
