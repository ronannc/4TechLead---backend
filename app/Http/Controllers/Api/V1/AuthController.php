<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\InvalidCredentialsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AcceptPersonInvitationRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\Auth\AcceptPersonInvitationService;
use App\Services\Auth\LoginService;
use App\Services\Auth\LogoutService;
use App\Services\Auth\RegisterUserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

final class AuthController extends Controller
{
    /**
     * @throws Throwable
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = app(RegisterUserService::class)->register($request->validated());

        return (new UserResource($result['user']))
            ->additional(['token' => $result['token']])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @throws InvalidCredentialsException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = app(LoginService::class)->login($request->validated());

        return (new UserResource($result['user']))
            ->additional(['token' => $result['token']])
            ->response();
    }

    /**
     * @throws Throwable
     */
    public function acceptPersonInvitation(AcceptPersonInvitationRequest $request): JsonResponse
    {
        $result = app(AcceptPersonInvitationService::class)->accept($request->validated());

        return (new UserResource($result['user']->load('person')))
            ->additional(['token' => $result['token']])
            ->response()
            ->setStatusCode(201);
    }

    public function logout(Request $request): JsonResponse
    {
        app(LogoutService::class)->logout($request->user());

        return response()->json(status: 204);
    }

    public function me(Request $request): JsonResponse
    {
        return (new UserResource($request->user()))->response();
    }
}
