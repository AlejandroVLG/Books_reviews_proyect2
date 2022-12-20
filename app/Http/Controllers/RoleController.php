<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /////////////////////////////////////////////////////////////////////////////////
    ///////////<------------------- NEW ROLE CREATED ----------------->//////////////
    /////////////////////////////////////////////////////////////////////////////////

    public function newRole(Request $request)
    {
        try {
            Log::info('Creating role');

            $validator = Validator::make($request->all(), [
                'role' => ['required', 'string', 'max:255', 'min:3']
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

            $roleName = $request->input("role");

            $role = new Role();

            $role->role = $roleName;

            $role->save();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'New role created'
                ],
                201
            );
        } catch (\Exception $exception) {

            Log::error("Error creating role: " . $exception->getMessage());

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
    /////////////<------------------- DELETE ROLE ----------------->/////////////////
    /////////////////////////////////////////////////////////////////////////////////

    public function deleteRole($id)
    {
        try {
            Log::info('Deleting Role');

            $role = Role::query()->find($id);

            if (!$role) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => "Role doesn't exists"
                    ],
                    404
                );
            }

            $role->delete();

            return response()->json(
                [
                    'success' => true,
                    'message' => "Role " . $id . " deleted"
                ],
                200
            );
        } catch (\Exception $exception) {

            Log::error("Error deleting role: " . $exception->getMessage());

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
