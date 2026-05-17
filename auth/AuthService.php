<?php

class AuthService
{
    private JWT $jwt;
    private UserRepository $userRepository;
    private string $issuer;

    public function __construct(JWT $jwt, UserRepository $userRepository, string $issuer)
    {
        $this->jwt = $jwt;
        $this->userRepository = $userRepository;
        $this->issuer = $issuer;
    }

    public function login(string $email, string $password)
    {
        $user = $this->userRepository->findByEmail($email);

        if($user === null){
            throw new RuntimeException('Invalid credentials');
        }

        if(!password_verify($password, $user->password)){
            throw new RuntimeException('Invalid credentials');
        }

        $token = $this->createToken($user);

        return [
            'token' => $token,
            'expires_in' => 3600,
            'token_type' => 'Bearer'
        ];
    }

    private function createToken($user)
    {

        $now = time();

        $payload = [
            // Registered claims
            'iss' => $this->issuer,
            'sub' => (string) $user['id'],
            'iat' => $now,
            'exp' => $now + 3600, // 1 hour
            'nbf' => $now,
            'jti' => bin2hex(random_bytes(16)),

            // Custom claims (public user info only - never include password!)
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
        return $this->jwt->encode($payload);
    }

    public function verifyToken(string $token)
    {
        return $this->jwt->decode($token, true);
    }

}