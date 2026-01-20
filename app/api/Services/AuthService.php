<?php

namespace Api\Services;

use Api\Entities\User;
use Api\Repositories\UserRepository;

class AuthService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository = null)
    {
        $this->userRepository = $userRepository ?? new UserRepository();
    }

    public function register(array $data): User
    {
        $this->validateRegistrationData($data);

        if ($this->userRepository->findByEmail($data['email'])) {
            throw new \Exception('Email already exists');
        }
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $user = new User($data['name'], $data['email'], $hashedPassword);

        return $this->userRepository->create($user);
    }

    public function login(array $data): User
    {
        $this->validateLoginData($data);

        $user = $this->userRepository->findByEmail($data['email']);

        if (!$user || !$user->verifyPassword($data['password'])) {
            throw new \Exception('Invalid credentials');
        }

        return $user;
    }

    private function validateRegistrationData(array $data): void
    {
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            throw new \InvalidArgumentException('Name, email and password are required');
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }

        if (strlen($data['password']) < 6) {
            throw new \InvalidArgumentException('Password must be at least 6 characters');
        }
    }

    private function validateLoginData(array $data): void
    {
        if (empty($data['email']) || empty($data['password'])) {
            throw new \InvalidArgumentException('Email and password are required');
        }
    }
}
