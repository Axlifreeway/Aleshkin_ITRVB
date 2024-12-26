<?php

namespace App\Auth;

use App\Repositories\TokenRepository;
use App\Models\Token;
use DateTime;
use Exception;

class TokenService {
    private TokenRepository $tokenRepository;

    public function __construct(TokenRepository $tokenRepository) {
        $this->tokenRepository = $tokenRepository;
    }

    public function validate(string $tokenValue): string {
        $token = $this->tokenRepository->get($tokenValue);

        if (!$token || $token->isExpired()) {
            throw new Exception('Invalid or expired token.');
        }

        return $token->userUuid;
    }

    public function logout(string $tokenValue): void {
        $token = $this->tokenRepository->get($tokenValue);

        if (!$token) {
            throw new Exception('Invalid token.');
        }

        $token->expiresAt = new DateTime();
        $this->tokenRepository->save($token);
    }
}