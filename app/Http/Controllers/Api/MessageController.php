<?php

namespace App\Http\Controllers\Api;

use App\Events\NewMessage;
use App\Models\Attachment;
use App\Models\Chatroom;
use App\Models\Message;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use Log;


/**
 * @OA\Server(
 *     url="http://localhost:8000/api"
 * )
 */

 /**
 * @OA\Schema(
 *     schema="Message",
 *     type="object",
 *     required={"id", "user_id", "chatroom_id", "content", "created_at", "updated_at"},
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="user_id", type="integer", format="int64", example=1),
 *     @OA\Property(property="chatroom_id", type="integer", format="int64", example=1),
 *     @OA\Property(property="content", type="string", example="Hello, world!"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-31T12:34:56Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-31T12:34:56Z"),
 * )
 */

class MessageController extends Controller
{
   /**
     * @OA\Get(
     *     path="/chatrooms/{chatroom}/messages",
     *     operationId="getMessages",
     *     tags={"Chatroom"},
     *     summary="Retrieve messages for a specific chatroom",
     *     description="Fetches all messages along with their users and attachments for the specified chatroom.",
     *     @OA\Parameter(
     *         name="chatroom",
     *         in="path",
     *         required=true,
     *         description="ID of the chatroom",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Messages retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Messages List"),
     *             @OA\Property(property="status", type="boolean", example=true),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Chatroom not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Chatroom not found"),
     *             @OA\Property(property="status", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred"),
     *             @OA\Property(property="status", type="boolean", example=false)
     *         )
     *     )
     * )
    */
    public function index(Chatroom $chatroom)
    {
        $messages = $chatroom->messages()->with(['user', 'attachments'])->get();

        return response()->json([
            'message' => 'Messages List',
            'status' => true,
            'data' => $messages
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/chatrooms/{chatroom}/messages",
     *     operationId="storeMessage",
     *     tags={"Chatroom"},
     *     summary="Send a new message in a chatroom",
     *     description="Stores a new message in the specified chatroom and broadcasts it to other users.",
     *     @OA\Parameter(
     *         name="chatroom",
     *         in="path",
     *         required=true,
     *         description="ID of the chatroom",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"content"},
     *             @OA\Property(property="content", type="string", example="Hello, World!"),
     *             @OA\Property(property="attachments", type="array", @OA\Items(
     *                 type="array",
     *                 @OA\Property(property="file", type="string", format="binary"),
     *                 @OA\Items(ref="#/components/schemas/Message")
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Message sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Message sent successfully"),
     *             @OA\Property(property="status", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unsupported file type",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unsupported file type"),
     *             @OA\Property(property="status", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred"),
     *             @OA\Property(property="status", type="boolean", example=false)
     *         )
     *     )
     * )
    */
    public function store(StoreMessageRequest $request)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = auth()->id();
            
            $message = Message::create($data);

            if (isset($data['attachments'])) {
                foreach ($data['attachments'] as $file) {
                    $fileType = $file->getClientMimeType();
                    $storagePath = '';
            
                    if (strpos($fileType, 'image/') === 0) {
                        $storagePath = 'pictures';
                    } elseif (strpos($fileType, 'video/') === 0) {
                        $storagePath = 'videos';
                    } elseif (strpos($fileType, 'application/') === 0 || strpos($fileType, 'text/') === 0) {
                        $storagePath = 'documents';
                    } else {
                        return response()->json(['message' => 'Unsupported file type', 'status' => false], 422);
                    }
            
                    $attachment = new Attachment();
                    $attachment->message_id = $message->id;
                    $attachment->file_path = $file->store($storagePath);
                    $attachment->file_type = $fileType;
                    $attachment->original_name = $file->getClientOriginalName();
                    $attachment->save();
                }
            }
            
            broadcast(new NewMessage($message))->toOthers();

            return response()->json(['message' => 'Message sent successfully', 'status' => true], 201);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }
}
