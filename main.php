<?php

require_once __DIR__ . '/autoload.php';
//echo $_ENV['JWT_SECRET'] ?? 'not found';
//print_r(getenv('JWT_SECRET'));
$signer = new HmacSigner(secret: getenv('JWT_SECRET'), algorithm: 'HS256');

$jwt = new Jwt($signer);

$token = $jwt->encode([
    'sub' => 'user_123',           // Subject (user ID)
    'iss' => 'https://myapp.com',  // Issuer
    'aud' => 'https://api.myapp.com', // Audience
    'iat' => time(),               // Issued at
    'exp' => time() + 3600,        // Expires in 1 hour
    'nbf' => time(),               // Valid from now
    'jti' => bin2hex(random_bytes(16)), // Unique ID

    // Custom claims
    'name' => 'Jamshid',
    'role' => 'developer'
]);


echo "Token: " . $token . "\n";
echo "\n--- Tampering test ---\n";

//$parts= explode('.',$token);
//
//
//echo "Original payload: " . $parts[1] . "\n";
//
//$parts[1] = $parts[1]."X";
//$tamperedToken = implode('.',$parts);
//
//echo "Tampered payload: " . $parts[1] . "\n";

try{
    $result = $jwt->decode($token);
    echo "Result: success ";
    print_r($result);
}catch (Exception $e){
    echo "Error: " . $e->getMessage();
}
//$payload = $jwt->decode($token);
//print_r($payload);