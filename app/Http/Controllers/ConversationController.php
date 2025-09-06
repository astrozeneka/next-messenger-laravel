<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ConversationController extends Controller
{
    public function getUsers($conversationId): JsonResponse
    {
        $conversation = Conversation::with(['users.publicKeys'])->find($conversationId);

        if (!$conversation) {
            return response()->json([
                'error' => 'Conversation not found'
            ], 404);
        }

        $usersWithPublicKeys = $conversation->users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'public_keys' => $user->publicKeys->map(function ($publicKey) {
                    return [
                        'id' => $publicKey->id,
                        'public_key_value' => $publicKey->public_key_value,
                        'created_at' => $publicKey->created_at,
                    ];
                }),
            ];
        });

        return response()->json([
            'conversation_id' => $conversation->id,
            'users' => $usersWithPublicKeys
        ]);
    }
}