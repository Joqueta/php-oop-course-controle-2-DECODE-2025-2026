<?php

namespace Api\Controllers;

use Api\Services\AuthService;

class AuthController extends ApiController
{
    private AuthService $authService;

    public function __construct(?AuthService $authService = null)
    {
        $this->authService = $authService ?? new AuthService();
    }

    public function register(): void
    {
        try {
            $data = $this->getJsonInput();
            $user = $this->authService->register($data);

            $this->sendSuccess($user->toArray(), 'User registered successfully', 201);
        } catch (\InvalidArgumentException $e) {
            $this->sendError($e->getMessage(), 400);
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 409);
        }
    }

    public function login(): void
    {
        try {
            $data = $this->getJsonInput();
            $user = $this->authService->login($data);

            $this->sendSuccess([
                'user' => $user->toArray(),
                'message' => 'Login successful'
            ]);
        } catch (\InvalidArgumentException $e) {
            $this->sendError($e->getMessage(), 400);
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 401);
        }
    }
}
