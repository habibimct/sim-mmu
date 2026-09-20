<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OfflineSyncRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OfflineSyncTestController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sync_id' => ['required', 'uuid'],
            'type' => ['required', 'string', 'max:50'],
            'payload' => ['required', 'array'],
        ]);

        $existing = OfflineSyncRequest::where(
            'sync_id',
            $validated['sync_id']
        )->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'message' => 'Request sinkronisasi sudah pernah diterima.',
                'ack' => [
                    'sync_id' => $existing->sync_id,
                    'status' => 'duplicate',
                ],
                'server_time' => now()->toIso8601String(),
            ]);
        }

        $syncRequest = OfflineSyncRequest::create([
            'sync_id' => $validated['sync_id'],
            'type' => $validated['type'],
            'status' => 'acknowledged',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data offline berhasil diterima oleh server.',
            'ack' => [
                'sync_id' => $syncRequest->sync_id,
                'status' => 'acknowledged',
            ],
            'server_time' => now()->toIso8601String(),
        ]);
    }
}
