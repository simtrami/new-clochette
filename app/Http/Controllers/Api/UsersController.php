<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        return UserResource::collection(User::paginate(10));
    }

    /**
     * @param User $user
     * @return UserResource
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * @param Request $request
     * @return UserResource
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        $data['password'] = Hash::make($data['password']);

        $user = new User($data);

        $user->save();

        return new UserResource($user);
    }

    /**
     * @param User $user
     * @param Request $request
     * @return UserResource
     */
    public function update(User $user, Request $request)
    {
        $data = $request->validate([
            'name' => 'string',
            'username' => [
                'string',
                Rule::unique('users')->ignore($user)
            ],
            'email' => [
                'email',
                Rule::unique('users')->ignore($user)
            ],
            'password' => 'confirmed',
        ]);

        if ($request->has('password')) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return new UserResource($user);
    }

    /**
     * @param User $user
     * @return ResponseFactory|JsonResponse|Response
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();
        } catch (Exception $err) {
            return response()->json($err, 500);
        }

        return response(null, 204);
    }
}
