<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    // Show chat screen with all rooms
    public function index(): View
    {
        $rooms = ChatRoom::where('is_active', true)
            ->with(['latestMessage.user'])
            ->get();

        // Auto-join user to all rooms if not already
        $user = auth()->user();
        foreach ($rooms as $room) {
            if (! $room->users()->where('user_id', $user->id)->exists()) {
                $room->users()->attach($user->id);
            }
        }

        $activeRoom = $rooms->first();
        $messages   = $activeRoom
            ? $activeRoom->messages()
                ->with('user')
                ->where('is_deleted', false)
                ->latest()
                ->limit(50)
                ->get()
                ->reverse()
                ->values()
            : collect();

        return view('chat.index', compact('rooms', 'activeRoom', 'messages'));
    }

    // Load messages for a specific room (AJAX)
    public function messages(ChatRoom $room): JsonResponse
    {
        $messages = $room->messages()
            ->with('user')
            ->where('is_deleted', false)
            ->latest()
            ->limit(50)
            ->get()
            ->reverse()
            ->values()
            ->map(fn ($msg) => [
                'id'         => $msg->id,
                'message'    => $msg->message,
                'user_id'    => $msg->user_id,
                'user_name'  => $msg->user->name,
                'user_role'  => $msg->user->role,
                'user_initial' => strtoupper(substr($msg->user->name, 0, 1)),
                'mine'       => $msg->user_id === auth()->id(),
                'time'       => $msg->created_at->format('H:i'),
                'date'       => $msg->created_at->diffForHumans(),
            ]);

        // Update last read
        $room->users()->updateExistingPivot(auth()->id(), [
            'last_read_at' => now(),
        ]);

        return response()->json($messages);
    }

    // Send a message (AJAX)
    public function send(Request $request, ChatRoom $room): JsonResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $msg = ChatMessage::create([
            'chat_room_id' => $room->id,
            'user_id'      => auth()->id(),
            'message'      => trim($request->message),
            'type'         => 'text',
        ]);

        $msg->load('user');

        return response()->json([
            'id'           => $msg->id,
            'message'      => $msg->message,
            'user_id'      => $msg->user_id,
            'user_name'    => $msg->user->name,
            'user_role'    => $msg->user->role,
            'user_initial' => strtoupper(substr($msg->user->name, 0, 1)),
            'mine'         => true,
            'time'         => $msg->created_at->format('H:i'),
            'date'         => 'Just now',
        ]);
    }

    // Poll for new messages since last id (AJAX polling)
    public function poll(Request $request, ChatRoom $room): JsonResponse
    {
        $sinceId = (int) $request->query('since', 0);

        $messages = $room->messages()
            ->with('user')
            ->where('is_deleted', false)
            ->where('id', '>', $sinceId)
            ->oldest()
            ->limit(20)
            ->get()
            ->map(fn ($msg) => [
                'id'           => $msg->id,
                'message'      => $msg->message,
                'user_id'      => $msg->user_id,
                'user_name'    => $msg->user->name,
                'user_role'    => $msg->user->role,
                'user_initial' => strtoupper(substr($msg->user->name, 0, 1)),
                'mine'         => $msg->user_id === auth()->id(),
                'time'         => $msg->created_at->format('H:i'),
                'date'         => $msg->created_at->diffForHumans(),
            ]);

        return response()->json($messages);
    }
}