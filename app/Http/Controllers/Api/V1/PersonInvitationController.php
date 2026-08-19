<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PersonInvitation\StorePersonInvitationRequest;
use App\Http\Resources\PersonInvitationResource;
use App\Models\Person;
use App\Services\PersonInvitationCreateService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;

final class PersonInvitationController extends Controller
{
    /**
     * @throws Throwable
     */
    public function store(StorePersonInvitationRequest $request, Person $person): JsonResponse
    {
        $user = $request->user();

        if (! $user->isTechLead()) {
            throw new AccessDeniedHttpException;
        }

        $result = app(PersonInvitationCreateService::class)->create(
            person: $person,
            inviter: $user,
            expiresInDays: $request->integer('expires_in_days', 7),
        );

        return (new PersonInvitationResource($result['invitation']))
            ->additional(['token' => $result['token']])
            ->response()
            ->setStatusCode(201);
    }
}
