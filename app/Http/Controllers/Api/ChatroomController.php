<?php

namespace App\Http\Controllers\Api;

use App\Models\Chatroom;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChatroomRequest;
use App\Http\Requests\UpdateChatroomRequest;

class ChatroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $chatrooms = Chatroom::all();

        return response()->json(['message' => 'Chatroom List', 'status' => true, 'data' => $chatrooms], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChatroomRequest $request)
    {
        $data = $request->validated();
        $data['creator_id'] = auth()->id();

        Chatroom::create($data);

        return response()->json(['message' => 'Chatroom Created', 'status' => true], 201);
    }

    /**
     * @param Chatroom $chatroom
     * enter chatroom.
     */
    public function enter(Chatroom $chatroom)
    {
        $chatroom->users()->attach(auth()->id());

        return response()->json(['message' => 'Entered Chatroom', 'status' => true], 201);
    }

    /**
     * @param Chatroom $chatroom
     * enter chatroom.
     */
    public function leave(Chatroom $chatroom)
    {
        $chatroom->users()->detach(auth()->id());

        return response()->json(['message' => 'Left Chatroom', 'status' => true], 201);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Chatroom $chatroom)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateChatroomRequest $request, Chatroom $chatroom)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chatroom $chatroom)
    {
        //
    }
}
