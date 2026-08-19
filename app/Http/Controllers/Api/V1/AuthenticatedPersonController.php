<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DevelopmentPlanResource;
use App\Http\Resources\PersonResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class AuthenticatedPersonController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $person = $request->user()->person;

        if ($person === null) {
            throw new NotFoundHttpException;
        }

        return (new PersonResource($person))->response();
    }

    public function developmentPlans(Request $request): JsonResponse
    {
        $person = $request->user()->person;

        if ($person === null) {
            throw new NotFoundHttpException;
        }

        $plans = $person->developmentPlans()
            ->with('items')
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 15));

        return DevelopmentPlanResource::collection($plans)->response();
    }
}
