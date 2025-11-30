<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollection;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::with(['profile', 'roles'])
            ->paginate(15);

        return new UserCollection($users);
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
    {
        // Validate using JSON Schema
        $schema = UserResource::schema();
        $validation = \Masum\JsonSchema\Schema::validate($request->all(), $schema);
        
        if (!$validation['valid']) {
            return response()->json([
                'errors' => $validation['errors']
            ], 422);
        }

        $user = User::create($request->all());

        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['profile', 'roles', 'posts']);
        
        return new UserResource($user);
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, User $user)
    {
        // Partial validation for update
        $schema = UserResource::schema();
        $validation = \Masum\JsonSchema\Schema::validate($request->all(), $schema);
        
        if (!$validation['valid']) {
            return response()->json([
                'errors' => $validation['errors']
            ], 422);
        }

        $user->update($request->all());

        return new UserResource($user);
    }

    /**
     * Remove the specified resource.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
            'meta' => [
                'deleted_user' => new UserResource($user)
            ]
        ]);
    }

     // Advanced Controller with Conditional Includes
    public function showWithIncludes(Request $request, User $user)
    {
        // Load relationships based on query parameters
        $includes = explode(',', $request->get('include', ''));
        
        if (in_array('profile', $includes)) {
            $user->load('profile');
        }
        
        if (in_array('roles', $includes)) {
            $user->load('roles');
        }
        
        if (in_array('posts', $includes)) {
            $user->load(['posts' => function ($query) {
                $query->published()->latest();
            }]);
        }

        $resource = new UserResource($user);
        
        // Add included resources to meta
        return $resource->additional([
            'meta' => [
                'includes' => $includes,
                'available_includes' => ['profile', 'roles', 'posts', 'comments'],
            ]
        ]);
    }
}