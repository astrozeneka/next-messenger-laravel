<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Google\Client as GoogleClient;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function google(Request $request)
    {
        $idToken = $request->input('id_token'); // sent from Flutter

        if (!$idToken) {
            return response()->json(['error' => 'Missing id_token'], 400);
        }
        
        $client = new GoogleClient(['client_id' => env('GOOGLE_CLIENT_ID')]);

        try {
            $payload = $client->verifyIdToken($idToken);
            if ($payload) {
                $googleId = $payload['sub'];
                $email = $payload['email'];
                $name = $payload['name'] ?? '';
                Log::info("Google user verified: $email (ID: $googleId, Name: $name)");

                $user = User::where('google_id', $googleId)->first();
                
                if (!$user) {
                    $existingUser = User::where('email', $email)->first();
                    if ($existingUser) {
                        $existingUser->google_id = $googleId;
                        $existingUser->save();
                        $user = $existingUser;
                    } else {
                        $user = User::create([
                            'name' => $name,
                            'email' => $email,
                            'google_id' => $googleId,
                            'password' => Hash::make(uniqid())
                        ]);
                    }
                }

                $token = $user->createToken('google-auth')->plainTextToken;

                return response()->json([
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'name' => $user->name,
                        'google_id' => $user->google_id
                    ]
                ]);

            } else {
                Log::warning("Google user verification failed: " . json_encode($payload));
                return response()->json(['error' => 'Invalid id_token'], 401);
            }
        } catch (\Exception $e) {
            Log::error("Google authentication error: " . $e->getMessage());
            return response()->json(['error' => 'Invalid id_token'], 401);
        }

    }
}