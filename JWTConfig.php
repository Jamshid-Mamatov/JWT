<?php

class JWTConfig
{
    private array $config;

    public function __construct(string $envFile='.env')
    {
        $this->config = $this->parseEnvFile($envFile);
    }

    private function parseEnvFile(string $envFile): array
    {
        if(!file_exists($envFile)){
            throw new \InvalidArgumentException("Environment file not found: $envFile");
        }
        $result=[];
        $lines=file($envFile,FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line){
            $parts=explode('=',$line,2);
            if(count($parts)===2){
                $result[trim($parts[0])]=trim($parts[1]);
            }
        }
        return $result;
    }

    public function get(string $key): string
    {
        return $this->config[$key] ?? getenv($key) ?: '';
    }

    public function getHmacSigner(): HmacSigner
    {
        return new HmacSigner( secret:$this->getRequired('JWT_SECRET'), algorithm: $this->get('JWT_ALGORITHM', 'HS256'),);
    }

    public function getRsaSigner(): RsaSigner
    {
        return new RsaSigner(
            privateKey: $this->getRequired('JWT_PRIVATE_KEY'),
            publicKey: $this->getRequired('JWT_PUBLIC_KEY'),
            algorithm: $this->getRequired('JWT_ALGORITHM',"RS256")
        );
    }

    private function getRequired(string $key): string
    {
        $value = $this->get($key);
        if (empty($value)) {
            throw new \InvalidArgumentException("Missing required environment variable: $key");
        }
        return $value;
    }


    public function generateSecret(int $bytes = 64): string
    {
        return bin2hex(random_bytes($bytes));
    }

    public static function generateRsaKeyPair(int $bits = 2048): array
    {
        $key = openssl_pkey_new([
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
            'private_key_bits' => $bits,
        ]);

        openssl_pkey_export($key, $privateKey);
        $publicKey = openssl_pkey_get_details($key)['key'];

        return [
            'private_key' => $privateKey,
            'public_key' => $publicKey,
        ];
    }
}