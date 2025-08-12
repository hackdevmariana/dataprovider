<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Http\Resources\V1\GroupResource;

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
}
