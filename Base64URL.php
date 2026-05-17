<?php

class Base64URL
{

    public static function encode(string $data): string
    {
        $base64 = base64_encode($data);
        $urlSafe = strtr($base64, '+/', '-_');

        return rtrim($urlSafe,'=');

    }

    public static function decode(string $data): string
    {
        $base64 = strtr($data, '-_', '+/');

        $padLength = strlen($base64) % 4;
        if ($padLength !== 0) {
            $base64 .= str_repeat('=', 4 - $padLength);
        }

        $decoded=base64_decode($base64,true);

        if($decoded===false){
            throw new \http\Exception\InvalidArgumentException('Invalid base64 string'.$data);
        }

        return $decoded;
    }
}