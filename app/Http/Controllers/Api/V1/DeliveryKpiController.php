<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeliveryKpi\IndexDeliveryKpiRequest;
use App\Models\DeliveryCase;
use App\Services\DeliveryKpiSummaryService;
use Illuminate\Http\JsonResponse;

final class DeliveryKpiController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(
        IndexDeliveryKpiRequest $request,
        DeliveryKpiSummaryService $summaryService,
    ): JsonResponse {
        $this->authorize('viewAny', DeliveryCase::class);

        $tenantId = $request->user()?->tenant_id;

        abort_if($tenantId === null, 403);

        return response()->json([
            'data' => $summaryService->summarize($tenantId, $request->validated()),
        ]);
    }
}
