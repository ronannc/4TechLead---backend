<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExternalNotification\IndexExternalNotificationRequest;
use App\Http\Resources\ExternalNotificationResource;
use App\Models\ExternalNotification;
use Illuminate\Http\JsonResponse;

final class ExternalNotificationController extends Controller
{
    public function index(IndexExternalNotificationRequest $request): JsonResponse
    {
        $this->authorize('viewAny', ExternalNotification::class);

        $validated = $request->validated();
        $filters = (array) ($validated['filters'] ?? []);

        $query = ExternalNotification::query()
            ->with('integrationSystem')
            ->filter($filters)
            ->search($validated['search'] ?? null)
            ->orderByDesc('received_at')
            ->orderByDesc('id');

        return ExternalNotificationResource::collection(
            $query->paginate(
                perPage: min(100, max(1, (int) ($validated['per_page'] ?? 15))),
                page: max(1, (int) ($validated['page'] ?? 1)),
            )
        )->response();
    }

    public function show(ExternalNotification $notification): JsonResponse
    {
        $this->authorize('view', $notification);

        return (new ExternalNotificationResource($notification->load('integrationSystem')))->response();
    }
}
