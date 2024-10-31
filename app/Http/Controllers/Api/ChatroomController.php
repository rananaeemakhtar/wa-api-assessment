<?php

namespace App\Http\Controllers\Api;

use App\Models\Chatroom;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChatroomRequest;
use App\Http\Requests\UpdateChatroomRequest;

/**
 * @OA\Server(
 *     url="http://localhost:8000/api"
 * )
 */
class ChatroomController extends Controller
{
    /**
     * @OA\Get(
     *     path="/chatrooms",
     *     operationId="getChatrooms",
     *     tags={"Chatroom"},
     *     summary="Get list of chatrooms",
     *     description="Retrieves all chatrooms.",
     *     @OA\Response(
     *         response=200,
     *         description="List of chatrooms",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Chatroom List"),
     *             @OA\Property(property="status", type="boolean", example=true),
     *         )
     *     )
     * )
    */
    public function index()
    {
        $chatrooms = Chatroom::all();

        return response()->json(['message' => 'Chatroom List', 'status' => true, 'data' => $chatrooms], 200);
    }

    /**
     * @OA\Post(
     *     path="/chatrooms",
     *     operationId="storeChatroom",
     *     tags={"Chatroom"},
     *     summary="Create a new chatroom",
     *     description="Stores a newly created chatroom.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="General Chat"),
     *             @OA\Property(property="description", type="string", example="A chatroom for general discussions")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Chatroom created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Chatroom Created"),
     *             @OA\Property(property="status", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="status", type="boolean", example=false)
     *         )
     *     )
     * )
    */
    public function store(StoreChatroomRequest $request)
    {
        $data = $request->validated();
        $data['creator_id'] = auth()->id();

        Chatroom::create($data);

        return response()->json(['message' => 'Chatroom Created', 'status' => true], 201);
    }

    /**
     * @OA\Post(
     *     path="/chatrooms/{chatroom}/enter",
     *     operationId="enterChatroom",
     *     tags={"Chatroom"},
     *     summary="Enter a chatroom",
     *     description="Allows a user to enter a specified chatroom.",
     *     @OA\Parameter(
     *         name="chatroom",
     *         in="path",
     *         required=true,
     *         description="ID of the chatroom to enter",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully entered chatroom",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Entered Chatroom"),
     *             @OA\Property(property="status", type="boolean", example=true)
     *         )
     *     )
     * )
    */
    public function enter(Chatroom $chatroom)
    {
        $chatroom->users()->attach(auth()->id());

        return response()->json(['message' => 'Entered Chatroom', 'status' => true], 201);
    }

    /**
     * @OA\Post(
     *     path="/chatrooms/{chatroom}/leave",
     *     operationId="leaveChatroom",
     *     tags={"Chatroom"},
     *     summary="Leave a chatroom",
     *     description="Allows a user to leave a specified chatroom.",
     *     @OA\Parameter(
     *         name="chatroom",
     *         in="path",
     *         required=true,
     *         description="ID of the chatroom to leave",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successfully left chatroom",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Left Chatroom"),
     *             @OA\Property(property="status", type="boolean", example=true)
     *         )
     *     )
     * )
    */
    public function leave(Chatroom $chatroom)
    {
        $chatroom->users()->detach(auth()->id());

        return response()->json(['message' => 'Left Chatroom', 'status' => true], 201);
    }
}
