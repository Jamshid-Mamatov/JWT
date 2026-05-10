<?php

require_once __DIR__ . '/autoload.php';
//echo $_ENV['JWT_SECRET'] ?? 'not found';
//print_r(getenv('JWT_SECRET'));
$signer = new HmacSigner(secret: getenv('JWT_SECRET'), algorithm: 'HS256');
print_r($signer);
