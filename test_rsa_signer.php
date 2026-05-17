<?php

require_once __DIR__ . '/autoload.php';

$keyPair = JWTConfig::generateRsaKeyPair();

echo "=== Generated Keys ===\n";
echo "Private Key (KEEP SECRET!):\n" . substr($keyPair['private_key'], 0, 100) . "...\n\n";
echo "Public Key (safe to share):\n" . substr($keyPair['public_key'], 0, 100) . "...\n\n";


$signer = new RsaSigner($keyPair['private_key'], $keyPair['public_key'],algorithm: 'RS256');

$jwt = new JWT($signer);

// Create token (uses PRIVATE key)
$token = $jwt->encode(['user_id' => 1, 'name' => 'Jamshid']);
echo "=== Token ===\n$token\n\n";

// Verify token (uses PUBLIC key)
$payload = $jwt->decode($token);
echo "=== Decoded Payload ===\n";
print_r($payload);
