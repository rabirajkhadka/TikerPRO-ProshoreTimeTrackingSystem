<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignRoleRequest;
use App\Http\Requests\MemberInviteRequest;
use App\Services\InviteService;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Models\User;
use Mockery\Exception;
use App\Http\Resources\AdminResource;
use App\Http\Resources\RoleResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function deleteUser($id)
    {
        $user = User::where('id', $id)->first();

        $deleteStatus = UserService::checkUserIdExists($id);

        if (!$deleteStatus) {
            return response()->json([
                'message' => 'User does not exist with given id'
            ], Response::HTTP_NOT_FOUND);
        }

        $roles = $user->roles()->pluck('role');

        if ($roles->contains('admin')) {

            return response()->json([
                'message' => 'Admin User cannot be deleted'
            ], 403);
        }

        if ($user->delete()) {

            return response()->json([
                'message' => 'User deleted Successfully'
            ], 200);
        }

        return response()->json([
            'message' => 'Oops Something Went Wrong'
        ], 403);
    }

    public function viewAllUsers()
    {
        $users = User::latest()->get();

        return response()->json([
            'total' => count($users),
            'users' => AdminResource::collection($users)
        ], 200);
    }

    public function viewUserRole(Request $request)
    {
        $role = User::find($request->id)->roles;

        return response()->json([
            'total' => count($role),
            'roles' => RoleResource::collection($role)
        ], 200);
    }


    /**
     * @param AssignRoleRequest $request
     * @return JsonResponse
     */

    public function assignRoles(AssignRoleRequest $request): JsonResponse
    {
        try {
            $role = $this->userService->assignUserRole($request->validated());
            $result = [
                'status' => Response::HTTP_OK,
                'message' => 'User role updated',
                'user' => $role,
            ];
        } catch (ModelNotFoundException $e) {
            $result = [
                'status' => Response::HTTP_NOT_FOUND,
                'error' => "User not Found"
            ];

            Log::error($e->getMessage());
        } catch (Exception $e) {
            $result = [
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'error' => 'Something went wrong'
            ];

            Log::error($e->getMessage());
        }
        return response()->json($result, $result['status']);
    }

    public function inviteOthers(MemberInviteRequest $request, InviteService $inviteService)
    {
        $validated = $request->safe()->only(['role_id', 'email', 'user_id', 'name']);
        $status = $inviteService->invite($validated['name'], $validated['email'], $validated['role_id'], $validated['user_id']);

        if (!$status) {
            return response()->json([
                'message' => 'User could not be invited'
            ], 500);
        }

        return response()->json([
            'message' => 'User invited successfully'
        ], 200);
    }

    public function updateUserStatus(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        try {
            if (!$user->activeStatus) {
                $user->activeStatus = true;
            } else {
                $user->activeStatus = false;
            }
            $user->save();
            $result = [
                'status' => 200,
                'message' => 'User status updated'
            ];
        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }
        return response()->json($result, $result['status']);
    }
}
