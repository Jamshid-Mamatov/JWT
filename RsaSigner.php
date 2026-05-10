<?php

class RsaSigner implements SignerInterface
{

    private array $algMap = [
        'RS256' => OPENSSL_ALGO_SHA256,
        'RS384' => OPENSSL_ALGO_SHA384,
        'RS512' => OPENSSL_ALGO_SHA512,
    ];

    public function __construct(private readonly string $privateKey, private readonly string $publicKey, private readonly string $algorithm = 'RS256')
    {
        if(!in_array($this->algorithm,$this->algMap)){
            throw new \InvalidArgumentException('Invalid algorithm');
        }
    }
    public function sign(string $data): string
    {
        $key = openssl_pkey_get_private($this->privateKey);

        if($key===false){
            throw new \InvalidArgumentException('Invalid private key');
        }
        $success=openssl_sign($data, $signature, $key, $this->algMap[$this->algorithm]);
        if(!$success){
            throw new \RuntimeException("RSA signing failed: " . openssl_error_string());
        }
        return $signature;
    }

    public function verify(string $data, string $signature): bool
    {
        $key = openssl_pkey_get_public($this->publicKey);

        if($key===false){
            throw new \InvalidArgumentException('Invalid public key');
        }
        return openssl_verify($data, $signature, $key, $this->algMap[$this->algorithm])===1;
    }

    public function algorithmId(): string
    {
        return $this->algorithm;
    }
}