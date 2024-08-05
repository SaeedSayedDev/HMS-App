<?php

namespace App\Traits;

use App\Models\FirebaseToken;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait NotificationTrait
{
    protected function notify($user_id, $type, $title, $body = null)
    {
        DB::beginTransaction();
        try {
            Notification::create([
                'user_id' => $user_id,
                'type' => $type,
                'title' => $title,
                'body' => $body,
            ]);

            $firebaseTokens = FirebaseToken::where('user_id', $user_id)->pluck('fcm_token')->all();
            $data = json_encode([
                "registration_ids" => $firebaseTokens,
                "notification" => [
                    'title' => $title,
                    'body' => $body,
                    'sound' => 'default',
                    'badge' => '1',
                ],
            ]);
            $headers = [
                'Authorization: key=' . env('FCM_TOKEN'),
                'Content-Type: application/json',
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_exec($ch);
            curl_close($ch);

            DB::commit();
            return response()->json(['message' => 'Notification sent successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Notification sending failed: ' . $e->getMessage());
            return response()->json(['message' => 'Notification sending failed'], 500);
        }
    }
}