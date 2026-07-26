<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use App\Events\UserTyping;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ChatController extends Controller
{
    // ─── GET CONVERSATIONS ────────────────────────────────────────────────────

    #[OA\Get(
        path: '/api/chat/conversations',
        summary: 'List all conversations',
        description: 'Returns all users the authenticated user has chatted with, including last message and unread count.',
        tags: ['Chat'],
        security: [['bearerAuth' => []]]
    )]
    #[OA\Response(
        response: 200,
        description: 'List of conversations',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(
                    property: 'conversations',
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(
                                property: 'user',
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'id',    type: 'integer', example: 5),
                                    new OA\Property(property: 'name',  type: 'string',  example: 'John Doe'),
                                    new OA\Property(property: 'email', type: 'string',  example: 'john@example.com'),
                                    new OA\Property(property: 'role',  type: 'string',  example: 'customer'),
                                ]
                            ),
                            new OA\Property(property: 'last_message',      type: 'string',  example: 'Hello!'),
                            new OA\Property(property: 'last_message_time', type: 'string',  example: '2 minutes ago'),
                            new OA\Property(property: 'last_message_at',   type: 'string',  format: 'date-time'),
                            new OA\Property(property: 'unread_count',      type: 'integer', example: 3),
                        ]
                    )
                ),
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
    public function conversations(Request $request)
    {
        $authId = $request->user()->id;

        $userIds = Message::where('sender_id', $authId)
            ->orWhere('receiver_id', $authId)
            ->get()
            ->flatMap(fn($m) => [$m->sender_id, $m->receiver_id])
            ->unique()
            ->filter(fn($id) => $id != $authId)
            ->values();

        $users = User::whereIn('id', $userIds)->get();

        $conversations = $users->map(function ($user) use ($authId) {
            $lastMessage = Message::where(function ($q) use ($authId, $user) {
                $q->where('sender_id', $authId)->where('receiver_id', $user->id);
            })->orWhere(function ($q) use ($authId, $user) {
                $q->where('sender_id', $user->id)->where('receiver_id', $authId);
            })->latest()->first();

            $unreadCount = Message::where('sender_id', $user->id)
                ->where('receiver_id', $authId)
                ->where('is_read', false)
                ->count();

            return [
                'user' => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'role'  => $user->role ?? 'customer',
                ],
                'last_message'      => $lastMessage?->message,
                'last_message_time' => $lastMessage?->created_at?->diffForHumans(),
                'last_message_at'   => $lastMessage?->created_at,
                'unread_count'      => $unreadCount,
            ];
        })->sortByDesc('last_message_at')->values();

        return response()->json([
            'success'       => true,
            'conversations' => $conversations,
        ]);
    }

    // ─── GET MESSAGES ─────────────────────────────────────────────────────────

    #[OA\Get(
        path: '/api/chat/messages/{userId}',
        summary: 'Get messages with a user',
        description: 'Returns all messages between the authenticated user and the specified user. Also marks incoming messages as read.',
        tags: ['Chat'],
        security: [['bearerAuth' => []]]
    )]
    #[OA\Parameter(
        name: 'userId',
        in: 'path',
        required: true,
        description: 'ID of the user to fetch messages with',
        schema: new OA\Schema(type: 'integer', example: 5)
    )]
    #[OA\Response(
        response: 200,
        description: 'Messages fetched successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(
                    property: 'with_user',
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id',    type: 'integer', example: 5),
                        new OA\Property(property: 'name',  type: 'string',  example: 'John Doe'),
                        new OA\Property(property: 'email', type: 'string',  example: 'john@example.com'),
                        new OA\Property(property: 'role',  type: 'string',  example: 'customer'),
                    ]
                ),
                new OA\Property(
                    property: 'messages',
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id',         type: 'integer', example: 101),
                            new OA\Property(property: 'message',    type: 'string',  example: 'Hello there!'),
                            new OA\Property(property: 'sender_id',  type: 'integer', example: 1),
                            new OA\Property(property: 'is_mine',    type: 'boolean', example: true),
                            new OA\Property(property: 'is_read',    type: 'boolean', example: false),
                            new OA\Property(property: 'created_at', type: 'string',  format: 'date-time'),
                            new OA\Property(property: 'time',       type: 'string',  example: '10:45 AM'),
                            new OA\Property(property: 'date',       type: 'string',  example: '2024-05-31'),
                        ]
                    )
                ),
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
    #[OA\Response(response: 404, description: 'User not found')]
    public function getMessages(Request $request, $userId)
    {
        $authId    = $request->user()->id;
        $otherUser = User::findOrFail($userId);

        $messages = Message::where(function ($q) use ($authId, $userId) {
            $q->where('sender_id', $authId)->where('receiver_id', $userId);
        })->orWhere(function ($q) use ($authId, $userId) {
            $q->where('sender_id', $userId)->where('receiver_id', $authId);
        })->orderBy('created_at', 'asc')->get()->map(function ($msg) use ($authId) {
            return [
                'id'         => $msg->id,
                'message'    => $msg->message,
                'sender_id'  => $msg->sender_id,
                'is_mine'    => $msg->sender_id == $authId,
                'is_read'    => $msg->is_read,
                'created_at' => $msg->created_at,
                'time'       => $msg->created_at->format('h:i A'),
                'date'       => $msg->created_at->toDateString(),
            ];
        });

        // Mark incoming messages as read
        Message::where('sender_id', $userId)
            ->where('receiver_id', $authId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json([
            'success'   => true,
            'with_user' => [
                'id'    => $otherUser->id,
                'name'  => $otherUser->name,
                'email' => $otherUser->email,
                'role'  => $otherUser->role ?? 'customer',
            ],
            'messages' => $messages,
        ]);
    }

    // ─── SEND MESSAGE ─────────────────────────────────────────────────────────

    #[OA\Post(
        path: '/api/chat/send',
        summary: 'Send a message',
        description: 'Send a message to another user. Also broadcasts a real-time WebSocket event to the receiver.',
        tags: ['Chat'],
        security: [['bearerAuth' => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['receiver_id', 'message'],
            properties: [
                new OA\Property(property: 'receiver_id', type: 'integer', example: 5,              description: 'ID of the message recipient'),
                new OA\Property(property: 'message',     type: 'string',  example: 'Hello there!', description: 'Message content (max 2000 chars)'),
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Message sent successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(
                    property: 'message',
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id',          type: 'integer', example: 101),
                        new OA\Property(property: 'message',     type: 'string',  example: 'Hello there!'),
                        new OA\Property(property: 'sender_id',   type: 'integer', example: 1),
                        new OA\Property(property: 'receiver_id', type: 'integer', example: 5),
                        new OA\Property(property: 'is_mine',     type: 'boolean', example: true),
                        new OA\Property(property: 'is_read',     type: 'boolean', example: false),
                        new OA\Property(property: 'created_at',  type: 'string',  format: 'date-time'),
                        new OA\Property(property: 'time',        type: 'string',  example: '10:45 AM'),
                        new OA\Property(property: 'date',        type: 'string',  example: '2024-05-31'),
                        new OA\Property(
                            property: 'sender',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id',   type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string',  example: 'Admin User'),
                            ]
                        ),
                    ]
                ),
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
    #[OA\Response(response: 422, description: 'Validation error')]
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
            'message'     => 'required|string|max:2000',
        ]);

        $sender = $request->user();

        $message = Message::create([
            'sender_id'   => $sender->id,
            'receiver_id' => $request->receiver_id,
            'message'     => $request->message,
            'is_read'     => false,
        ]);

        $message->load('sender');

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'success' => true,
            'message' => [
                'id'          => $message->id,
                'message'     => $message->message,
                'sender_id'   => $message->sender_id,
                'receiver_id' => $message->receiver_id,
                'is_mine'     => true,
                'is_read'     => false,
                'created_at'  => $message->created_at,
                'time'        => $message->created_at->format('h:i A'),
                'date'        => $message->created_at->toDateString(),
                'sender'      => [
                    'id'   => $sender->id,
                    'name' => $sender->name,
                ],
            ],
        ], 201);
    }

    // ─── TYPING INDICATOR ─────────────────────────────────────────────────────

    #[OA\Post(
        path: '/api/chat/typing',
        summary: 'Broadcast typing indicator',
        description: 'Fires a real-time WebSocket event so the receiver sees a "User is typing..." indicator.',
        tags: ['Chat'],
        security: [['bearerAuth' => []]]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['receiver_id'],
            properties: [
                new OA\Property(property: 'receiver_id', type: 'integer', example: 5, description: 'ID of the user who will see the typing indicator'),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Typing event broadcasted',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
    #[OA\Response(response: 422, description: 'Validation error')]
    public function typing(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
        ]);

        broadcast(new UserTyping($request->user()->id, $request->receiver_id))->toOthers();

        return response()->json(['success' => true]);
    }

    // ─── UNREAD COUNT ─────────────────────────────────────────────────────────

    #[OA\Get(
        path: '/api/chat/unread-count',
        summary: 'Get total unread message count',
        description: 'Returns the total number of unread messages for the authenticated user across all conversations.',
        tags: ['Chat'],
        security: [['bearerAuth' => []]]
    )]
    #[OA\Response(
        response: 200,
        description: 'Unread message count',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(property: 'count',   type: 'integer', example: 7),
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
    public function unreadCount(Request $request)
    {
        $count = Message::where('receiver_id', $request->user()->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'count'   => $count,
        ]);
    }

    // ─── DELETE MESSAGE ───────────────────────────────────────────────────────

    #[OA\Delete(
        path: '/api/chat/messages/{messageId}',
        summary: 'Delete a message',
        description: 'Permanently deletes a message. Only the sender of the message can delete it.',
        tags: ['Chat'],
        security: [['bearerAuth' => []]]
    )]
    #[OA\Parameter(
        name: 'messageId',
        in: 'path',
        required: true,
        description: 'ID of the message to delete',
        schema: new OA\Schema(type: 'integer', example: 101)
    )]
    #[OA\Response(
        response: 200,
        description: 'Message deleted successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'boolean', example: true),
                new OA\Property(property: 'message', type: 'string',  example: 'Message deleted successfully.'),
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Unauthenticated')]
    #[OA\Response(response: 404, description: 'Message not found or not yours')]
    public function deleteMessage(Request $request, $messageId)
    {
        $message = Message::where('id', $messageId)
            ->where('sender_id', $request->user()->id)
            ->firstOrFail();

        $message->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully.',
        ]);
    }
}
