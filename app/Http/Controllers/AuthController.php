<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * register a new user via the API
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "email" => "required|email",
            "password" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json(['status_code' => 400, 'message' => 'Bad Request']);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json([
            'status_code' => 201, 'message' => 'User Created Successfully!'
        ]);
    }

    /**
     * log in the user via the API
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => "required|email",
            "password" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json(['status_code' => 400, 'message' => 'Bad Request']);
        }

        $credentials = request(["email", "password"]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                "status_code" => 500,
                "message" => "Unauthorized"
            ]);
        }

        $user = User::where('email', $request->email)->first();
        $tokenResult = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            "status_code" => 200,
            "token" => $tokenResult
        ]);
    }

    /**
     * logout the authenticated user
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            "status_code" => 200,
            "message" => "Token Deleted Successfully!"
        ]);
    }
}
