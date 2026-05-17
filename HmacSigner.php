<?php

class HmacSigner implements SignerInterface
{

    private array $algMap = [
        'HS256' => 'sha256',
        'HS384' => 'sha384',
        'HS512' => 'sha512'
    ];

    public function __construct(private readonly string $secret, private readonly string $algorithm = 'HS256')
    {
//        print_r(array_key_exists($this->algorithm,$this->algMap)*1);
        if(!array_key_exists($this->algorithm,$this->algMap)){
            throw new \InvalidArgumentException('Invalid algorithm');
        }

//        print_r(strlen($this->secret));
        if(strlen($this->secret)<32){
            throw new \InvalidArgumentException('Invalid secret');
        }

    }
    public function sign(string $data): string
    {
        return hash_hmac($this->algMap[$this->algorithm],$data,$this->secret,true);
    }

    public function verify(string $data, string $signature): bool
    {
        $expectedSignature = $this->sign($data);

        return hash_equals($expectedSignature,$signature);
    }

    public function algorithmId(): string
    {
        return $this->algorithm;
    }
}