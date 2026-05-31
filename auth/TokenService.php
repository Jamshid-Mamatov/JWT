<?php

class TokenService
{
    private const ACCESS_TOKEN_TTL = 900;      // 15 minutes
    private const REFRESH_TOKEN_TTL = 604800;  // 7 days

    private JWT $jwt;
    private string $issuer;


    public function __construct(JWT $jwt, string $issuer='http://localhost:8080')
    {
        $this->jwt = $jwt;
        $this->issuer = $issuer;
    }

    public function createTokenPair(array $user){
        $accessToken = $this->createAccessToken($user);
        $refreshToken =  $this->createRefreshToken($user);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
        ];
    }
    private function createAccessToken(array $user){
        $now = time();

        return $this->jwt->encode([
            // Registered claims
            'iss' => $this->issuer,
            'sub' => (string) $user['id'],
            'iat' => $now,
            'exp' => $now + self::ACCESS_TOKEN_TTL,
            'nbf' => $now,
            'jti' => bin2hex(random_bytes(16)),

            // Token type
            'type' => 'access',

            // User claims
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ]);
    }
    private function createRefreshToken(array $user){
        $now = time();

        return $this->jwt->encode([
            // Registered claims
            'iss' => $this->issuer,
            'sub' => (string) $user['id'],
            'iat' => $now,
            'exp' => $now + self::REFRESH_TOKEN_TTL,
            'nbf' => $now,
            'jti' => bin2hex(random_bytes(16)),

            // Token type - IMPORTANT for validation
            'type' => 'refresh'
        ]);
    }


    public function verifyAccessToken(string $accessToken){
        $payload = $this->jwt->decode($accessToken);
        if(($payload['type'] ?? '') !== 'access'){
            throw new \http\Exception\RuntimeException('invalid token type');
        }
        return $payload;

    }

    public function refresh(string $refreshToken,UserRepository $userRepository){
        $payload = $this->jwt->decode($refreshToken);
        if(($payload['type'] ?? '') !== 'refresh'){
            throw new \http\Exception\RuntimeException('invalid token type');
        }

        $userId = (int)$payload['sub'];
        $user = $userRepository->findById($userId);
        if(!$user){
            throw new \http\Exception\RuntimeException('user not found');
        }

        return $this->createTokenPair($user);
    }


    public function getAccessTokenTTL(){
        return self::ACCESS_TOKEN_TTL;
    }

    public function getRefreshTokenTTL(){
        return self::REFRESH_TOKEN_TTL;
    }



}