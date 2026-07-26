<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Message;
use App\Models\Booking;
use App\Events\MessageSent;
use App\Events\UserTyping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function index()
    {
        return view('vendor.chat.index');
    }

    /**
     * Get all customers who have booked vendor's properties
     */
    public function getCustomers()
    {
        try {
            if (!Auth::check()) {
                return response()->json(['error' => 'Not logged in'], 401);
            }
            
            $vendorId = Auth::id();
            
            // Get all unique customers who booked vendor's properties
            // Using 'customer_id' instead of 'user_id'
            $customerIds = Booking::where('vendor_id', $vendorId)
                ->pluck('customer_id')
                ->unique()
                ->values()
                ->toArray();
            
            // Also include admin if admin has messaged the vendor
            $adminMessages = Message::where('receiver_id', $vendorId)
                ->orWhere('sender_id', $vendorId)
                ->get()
                ->pluck('sender_id', 'receiver_id')
                ->flatten()
                ->unique()
                ->toArray();
            
            $allUserIds = array_unique(array_merge($customerIds, $adminMessages));
            
            if (empty($allUserIds)) {
                return response()->json([]);
            }
            
            $customers = User::whereIn('id', $allUserIds)->get();
            
            $result = [];
            foreach ($customers as $customer) {
                // Get last message
                $lastMessage = Message::where(function($q) use ($vendorId, $customer) {
                    $q->where('sender_id', $vendorId)->where('receiver_id', $customer->id);
                })->orWhere(function($q) use ($vendorId, $customer) {
                    $q->where('sender_id', $customer->id)->where('receiver_id', $vendorId);
                })->latest()->first();
                
                // Get unread count
                $unreadCount = Message::where('sender_id', $customer->id)
                    ->where('receiver_id', $vendorId)
                    ->where('is_read', false)
                    ->count();
                
                $result[] = [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'role' => $customer->role ?? 'customer',
                    'last_message' => $lastMessage ? $lastMessage->message : null,
                    'last_message_time' => $lastMessage ? $lastMessage->created_at->diffForHumans() : null,
                    'unread_count' => $unreadCount,
                ];
            }
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            Log::error('Chat Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get messages between vendor and customer
     */
    public function getMessages($userId)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['error' => 'Not logged in'], 401);
            }
            
            $vendorId = Auth::id();
            
            // Verify vendor can chat with this user
            $canChat = Booking::where('vendor_id', $vendorId)
                ->where('customer_id', $userId)
                ->exists();
            
            $isAdmin = User::where('id', $userId)->where('role', 'admin')->exists();
            
            if (!$canChat && !$isAdmin && $userId != $vendorId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $messages = Message::where(function($q) use ($vendorId, $userId) {
                $q->where('sender_id', $vendorId)->where('receiver_id', $userId);
            })->orWhere(function($q) use ($vendorId, $userId) {
                $q->where('sender_id', $userId)->where('receiver_id', $vendorId);
            })->with(['sender', 'receiver'])
              ->orderBy('created_at', 'asc')
              ->get();
            
            // Mark messages as read
            Message::where('sender_id', $userId)
                ->where('receiver_id', $vendorId)
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);
            
            return response()->json($messages);
            
        } catch (\Exception $e) {
            Log::error('Get Messages Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Send message to customer
     */
    public function sendMessage(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['error' => 'Not logged in'], 401);
            }
            
            $vendorId = Auth::id();
            
            $request->validate([
                'receiver_id' => 'required|exists:users,id',
                'message' => 'required|string|max:1000',
            ]);
            
            // Verify vendor can message this user
            $canChat = Booking::where('vendor_id', $vendorId)
                ->where('customer_id', $request->receiver_id)
                ->exists();
            
            $isAdmin = User::where('id', $request->receiver_id)->where('role', 'admin')->exists();
            
            if (!$canChat && !$isAdmin) {
                return response()->json(['error' => 'You can only chat with your customers'], 403);
            }

            $message = Message::create([
                'sender_id' => $vendorId,
                'receiver_id' => $request->receiver_id,
                'message' => $request->message,
                'is_read' => false,
            ]);

            $message->load('sender');

            broadcast(new MessageSent($message))->toOthers();

            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $message->sender->name,
                    'created_at' => $message->created_at->diffForHumans(),
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Send Message Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get unread messages count
     */
    public function getUnreadCount()
    {
        if (!Auth::check()) {
            return response()->json(['count' => 0]);
        }
        
        $count = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->count();
            
        return response()->json(['count' => $count]);
    }

    public function typing(Request $request)
    {
        $request->validate(['receiver_id' => 'required|integer']);
        broadcast(new UserTyping(Auth::id(), $request->receiver_id))->toOthers();
        return response()->json(['status' => 'ok']);
    }
}