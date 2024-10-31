<?php

namespace App\Http\Controllers\Api;

use App\Models\Attachment;
use App\Models\Chatroom;
use App\Models\Message;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Chatroom $chatroom)
    {
        $messages = $chatroom->messages()->with(['user', 'attachments'])->get();

        return response()->json([
            'message' => 'Messages retrieved successfully',
            'status' => true,
            'data' => $messages
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMessageRequest $request)
    {
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

        return response()->json(['message' => 'Message sent successfully', 'status' => true], 201);
    }
}
