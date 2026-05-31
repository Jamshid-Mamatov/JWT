<?php

require_once __DIR__. "/autoload.php";
require_once __DIR__. "/auth/UserRepository.php";
require_once __DIR__. "/auth/TokenService.php";

$signer = new HmacSigner(getenv('JWT_SECRET'),algorithm: 'HS256');
$jwt = new JWT($signer);
$userRepository = new UserRepository();
$tokenService = new TokenService($jwt);

$user=$userRepository->findByEmail('jamshid@example.com');
$tokenPair=$tokenService->createTokenPair($user);

echo "=== Token Pair Created ===\n";
echo "Access Token (15 min): " . substr($tokenPair['access_token'], 0, 60) . "...\n";
echo "Refresh Token (7 days): " . substr($tokenPair['refresh_token'], 0, 60) . "...\n\n";

// Simulate token refresh
echo "=== Refreshing Token ===\n";
$newTokenPair = $tokenService->refresh($tokenPair['refresh_token'], $userRepository);
echo "New Access Token: " . substr($newTokenPair['access_token'], 0, 60) . "...\n\n";

// Verify access token
echo "=== Verifying Access Token ===\n";
$payload = $tokenService->verifyAccessToken($newTokenPair['access_token']);
echo "User: " . $payload['name'] . "\n";
echo "Role: " . $payload['role'] . "\n";
echo "Type: " . $payload['type'] . "\n";