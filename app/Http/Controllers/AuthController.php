<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    const ROLE_USER = 1;
    const ROLE_ADMIN = 2;
    const ROLE_SUPER_ADMIN = 3;

    /////////////////////////////////////////////////////////////////////////////////
    ////////////<----------------- REGISTER NEW USER ---------------->///////////////
    /////////////////////////////////////////////////////////////////////////////////

    public function register(Request $request)
    {
        try {
            Log::info('creating new user');

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
                'last_name' => 'string|max:100',
                'nick_name' => 'required|string|max:100|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'gender' => 'string|min:4|max:6',
                'age' => 'integer|max:200',
                'country' => 'string',
                'favourite_author' => 'string',
                'favourite_genre' => 'string',
                'currently_reading' => 'string',
                'facebook_account' => 'string',
                'twitter_account' => 'string',
                'instagram_account' => 'string',
                'profile_img' => 'url'
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "validation failed",
                        "error" => $validator->errors()
                    ],
                    400
                );
            }

            $user = User::create([
                'name' => $request->get('name'),
                'last_name' => $request->get('last_name'),
                'nick_name' => $request->get('nick_name'),
                'email' => $request->get('email'),
                'password' => bcrypt($request->password),
                'gender' => $request->get('gender'),
                'age' => $request->get('age'),
                'country' => $request->get('country'),
                'favourite_author' => $request->get('favourite_author'),
                'favourite_genre' => $request->get('favourite_genre'),
                'currently_reading' => $request->get('currently_reading'),
                'facebook_account' => $request->get('facebook_account'),
                'twitter_account' => $request->get('twitter_account'),
                'instagram_account' => $request->get('instagram_account'),
                'profile_img' => $request->get('profile_img'),
            ]);

            $users = User::all();

            // Por defecto se asigna al primer usuario creado los roles "Admin" y "Super_Admin", apartir de ahí
            // a todos los demás usarios se le asignará automáticamente el role de "User"
            if (count($users) == 1) {

                $user->roles()->attach(self::ROLE_ADMIN);
                $user->roles()->attach(self::ROLE_SUPER_ADMIN);
            } else {
                $user->roles()->attach(self::ROLE_USER);
            }

            return response()->json(compact('user'), 201);
        } catch (\Exception $exception) {

            Log::error("Error registering new user: " . $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage()
                ],
                500
            );
        }
    }

    /////////////////////////////////////////////////////////////////////////////////
    /////////////////<------------------- LOGIN ----------------->///////////////////
    /////////////////////////////////////////////////////////////////////////////////    

    public function login(Request $request)
    {
        try {
            Log::info('login user');

            $input = $request->only('email', 'password');
            $jwt_token = null;

            if (!$jwt_token = JWTAuth::attempt($input)) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Invalid Email or Password',
                    ],
                    Response::HTTP_UNAUTHORIZED
                );
            }

            return response()->json(
                [
                    'success' => true,
                    'token' => $jwt_token,
                ],
                200
            );
        } catch (\Exception $exception) {

            Log::error("Error login user: " . $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage()
                ],
                500
            );
        }
    }

    /////////////////////////////////////////////////////////////////////////////////
    ////////////////<------------------- LOGOUT  ----------------->//////////////////
    /////////////////////////////////////////////////////////////////////////////////

    public function logout(Request $request)
    {
        try {
            Log::info('Trying log out');

            JWTAuth::invalidate($request->token);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'User logged out successfully'
                ],
                200
            );
        } catch (\Exception $exception) {

            Log::error("Error login out: " . $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage()
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
