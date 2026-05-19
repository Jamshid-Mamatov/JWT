<?php

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/auth/UserRepository.php';
require_once __DIR__ . '/auth/AuthService.php';
require_once __DIR__ . '/auth/AuthMiddleware.php';

echo " JWT Login flow demo";

//setup
$signer = new HmacSigner(secret: getenv('JWT_SECRET'));
$jwt = new Jwt($signer);
$userRepository = new UserRepository();
$authService = new AuthService($jwt, $userRepository,'https://myapp.com');
$middleware = new AuthMiddleware($authService);

//succes
echo " Auth Login flow demo successful login\n\n";

try{
    $result = $authService->login('jamshid@example.com', 'secret123');
    echo 'Login successful';
    echo "Token: " . substr($result['token'], 0, 50) . "...\n";
    echo "Expires in: " . $result['expires_in'] . " seconds\n";
    echo "Type: " . $result['token_type'] . "\n\n";
    $validToken = $result['token'];
}catch (Exception $e){
    echo "Token failed: ".$e->getMessage()."\n\n\n";
}

//wrong password

echo "Failur";
try{
    $result = $authService->login('jamshid@example.com', 'secret12de3');
    echo 'Login successful';
    echo "Token: " . substr($result['token'], 0, 50) . "...\n";
    echo "Expires in: " . $result['expires_in'] . " seconds\n";
    echo "Type: " . $result['token_type'] . "\n\n";
    $validToken = $result['token'];
}catch (Exception $e){
    echo "Token failed: ".$e->getMessage()."\n\n\n";
}