<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    const ROLE_ADMIN = 2;

    /////////////////////////////////////////////////////////////////////////////////
    //////////////<------------------- MY PROFILE ----------------->/////////////////
    /////////////////////////////////////////////////////////////////////////////////

    public function showMyProfile()
    {
        try {
            Log::info('Showing my profile');

            return response()->json(auth()->user(), 200);;

        } catch (Exception $exception) {

            Log::error("Error showing my profile" . $exception->getMessage());

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
    ////////////<------------------- SHOW ALL USERS ----------------->///////////////
    /////////////////////////////////////////////////////////////////////////////////

    public function getUsers()
    {
        try {
            Log::info('Retrieving all users');

            $users = User::orderBy('name', 'asc')
                ->get()
                ->toArray();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Users retrieved successfully',
                    'data' => $users
                ]
            );
        } catch (Exception $exception) {

            Log::error("Error retrieveing users" . $exception->getMessage());

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
    ///////////<------------------- EDIT MY PROFILE ------------------>//////////////
    /////////////////////////////////////////////////////////////////////////////////

    public function editMyProfile(Request $request)
    {
        try {
            Log::info('Updating User');

            $validator = Validator::make($request->all(), [
                'name' => 'string|max:100',
                'last_name' => 'string|max:100',
                'nick_name' => 'string|max:100|unique:users',
                'email' => 'string|email|max:255|unique:users',
                'password' => 'string|min:6',
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
                        'success' => false,
                        'message' => $validator->errors()
                    ],
                    400
                );
            }

            $userId = auth()->user()->id;

            $user = User::query()->find($userId);

            if (!$user) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => "User doesn't exists"
                    ],
                    404
                );
            }

            $name = $request->input('name');
            $lastName = $request->input('last_name');
            $nickName = $request->input('nick_name');
            $email = $request->input('email');
            $password = $request->input('password');
            $gender = $request->input('gender');
            $age = $request->input('age');
            $country = $request->input('country');
            $favouriteAuthor = $request->input('favourite_author');
            $currentlyReading = $request->input('currently_reading');
            $facebookAccount = $request->input('facebook_account');
            $twitterAccount = $request->input('twitter_account');
            $instagramAccount = $request->input('instagram_account');
            $profileImg = $request->input('profile_img');

            if (isset($name)) {
                $user->name = $name;
            };
            if (isset($lastName)) {
                $user->last_name = $lastName;
            };
            if (isset($nickName)) {
                $user->nick_name = $nickName;
            };
            if (isset($email)) {
                $user->email = $email;
            };
            if (isset($password)) {
                $user->password = $password;
            };
            if (isset($gender)) {
                $user->gender = $gender;
            };
            if (isset($age)) {
                $user->age = $age;
            };
            if (isset($country)) {
                $user->country = $country;
            };
            if (isset($favouriteAuthor)) {
                $user->favourite_author = $favouriteAuthor;
            };
            if (isset($currentlyReading)) {
                $user->currently_reading = $currentlyReading;
            };
            if (isset($facebookAccount)) {
                $user->facebook_account = $facebookAccount;
            };
            if (isset($twitterAccount)) {
                $user->twitter_account = $twitterAccount;
            };
            if (isset($instagramAccount)) {
                $user->instagram_account = $instagramAccount;
            };
            if (isset($profileImg)) {
                $user->profile_img = $profileImg;
            };

            $user->save();

            return response()->json(
                [
                    'success' => true,
                    'message' => "User " . $userId . " changed"
                ],
                200
            );
        } catch (\Exception $exception) {

            Log::error("Error modifing User data: " . $exception->getMessage());

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
    ///////////<------------------- DELETE MY PROFILE ------------------>////////////
    /////////////////////////////////////////////////////////////////////////////////

    public function deleteMyProfile()
    {
        try {
            Log::info('Deleting User profile');

            $userId = auth()->user()->id;

            $user = User::query()
                ->where('user_id', '=', $userId)
                ->find($userId);

            if (!$user) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => "User doesn't exists"
                    ],
                    404
                );
            }

            $user->delete();

            return response()->json(
                [
                    'success' => true,
                    'message' => "User " . $userId . " profile deleted"
                ],
                200
            );
        } catch (\Exception $exception) {

            Log::error("Error deleting profile: " . $exception->getMessage());

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
    ////////////<------------------- ADD ADMIN ROLE ----------------->///////////////
    /////////////////////////////////////////////////////////////////////////////////

    public function addAdminRoleToUser($id)
    {
        try {
            $user = User::find($id);

            $user->roles()->attach(self::ROLE_ADMIN);

            return response()->json(
                [
                    'success' => true,
                    'message' => "Admin role added"
                ],
                200
            );
        } catch (\Exception $exception) {

            Log::error("Error updating Admin role: " . $exception->getMessage());

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
    ////////////<----------------- REMOVE ADMIN ROLE ---------------->///////////////
    /////////////////////////////////////////////////////////////////////////////////

    public function removeAdminRoleToUser($id)
    {
        try {
            $user = User::find($id);

            $user->roles()->detach(self::ROLE_ADMIN);

            return response()->json(
                [
                    'success' => true,
                    'message' => "Admin role removed"
                ],
                200
            );
        } catch (\Exception $exception) {

            Log::error("Error removing Admin role: " . $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => $exception->getMessage()
                ],
                500
            );
        }
    }
}
