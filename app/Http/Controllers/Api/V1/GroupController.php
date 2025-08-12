<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Http\Resources\V1\GroupResource;
use App\Http\Requests\StoreGroupRequest;

/**
 * @OA\Tag(
 *     name="Groups",
 *     description="Group management"
 * )
 */
class GroupController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/groups",
     *     summary="Get all groups",
     *     tags={"Groups"},
     *     @OA\Response(response=200, description="List of groups")
     * )
     */
    public function index()
    {
        return GroupResource::collection(Group::paginate(20));
    }

    /**
     * @OA\Get(
     *     path="/api/v1/groups/{id}",
     *     summary="Get a group by ID",
     *     tags={"Groups"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Group found"),
     *     @OA\Response(response=404, description="Group not found")
     * )
     */
    public function show($id)
    {
        $group = Group::findOrFail($id);
        return new GroupResource($group);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/groups",
     *     summary="Create a new group (public)",
     *     tags={"Groups"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *         )
     *     ),
     *     @OA\Response(response=201, description="Group created"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreGroupRequest $request)
    {
        $group = \App\Models\Group::create($request->validated());
        return (new GroupResource($group))->response()->setStatusCode(201);
    }
}
