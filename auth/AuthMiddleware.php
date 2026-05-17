<?php

class AuthMiddleware
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function extractToken(string $authHeader){
        if(empty($authHeader)){
            return null;
        }

        if(!str_starts_with($authHeader, 'Bearer ')){
            return null;
        }

        return substr($authHeader, 7);
    }

    public function authenticate(string $authHeader){
        $token = $this->extractToken($authHeader);

        if($token === null){
            throw new \RuntimeException('Missing or invalid Authorization header');
        }
        try{
            $paylod = $this->authService->verifyToken($token);

            return $paylod;
        }catch( Exception $e ){
            throw new RuntimeException('Unauthorized: '.$e->getMessage());
        }
    }

    public function requireRole(array $payload, string $role ){

        if(!isset($payload['role'])||$payload['role']!==$role){
            throw new \RuntimeException('Forbidden: requires ' . $role . ' role');
        }
    }

    public function requireAnyRole(array $payload, array $roles){
        if(!isset($payload['roles'])||!in_array($payload['roles'],$roles)){
            throw new \RuntimeException('Forbidden: requires one of: ' . implode(', ', $roles));

        }
    }

}