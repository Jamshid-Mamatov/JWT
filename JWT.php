<?php

class JWT
{
    private const REGISTERED_CLAIMS = [
        'iss',
        'sub',
        'aud',
        'exp',
        'nbf',
        'iat',
        'jti',
    ];


    public function __construct(private readonly SignerInterface $signer){}

    public function encode(array $payload, array $headers = []):string
    {
        $header = array_merge([
            'typ' => 'JWT',
            'alg' => $this->signer->algorithmId(),
        ], $headers);

        $headerEncoded = Base64URL::encode(json_encode($header));
        $payloadEncoded = Base64URL::encode(json_encode($payload));

        $signingInput = "{$headerEncoded}.{$payloadEncoded}";
        echo 'signingInput: ' . $signingInput . "\n";
        $signatureEncoded = Base64URL::encode($this->signer->sign($signingInput));


        return "{$signingInput}.{$signatureEncoded}";
    }


    public function decode(
        string $token,
        bool   $verifyExpiry  = true,
        array  $requiredClaims = []
    ):array
    {
       $parts = explode('.', $token);

       if(count($parts) !== 3){
           throw new \InvalidArgumentException('Invalid JWT');
       }

       [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;
        $header = json_decode(Base64URL::decode($headerEncoded), true);
        if (($header['alg'] ?? '') !== $this->signer->algorithmId()) {
            // CRITICAL: Reject algorithm mismatch
            // The classic "alg:none" attack exploits servers that accept any alg
            throw new \RuntimeException(
                "Algorithm mismatch: token uses [{$header['alg']}], signer expects [{$this->signer->algorithmId()}]"
            );
        }

        $signingInput = "{$headerEncoded}.{$payloadEncoded}";
        $signature = Base64URL::decode($signatureEncoded);

        if(!$this->signer->verify($signingInput,$signature)){
            throw new \RuntimeException('Invalid signature');
        }

        $payload = json_decode(Base64URL::decode($payloadEncoded),true);

        $now=time();

        if($verifyExpiry && isset($payload['exp']) && $payload['exp']<$now){
            throw new \RuntimeException('Expired token');
        }

        if(isset($payload['nbf']) && $payload['nbf']>$now){
            throw new \RuntimeException('Token not yet active');
        }

        foreach ($requiredClaims as $claim) {
            if(!array_key_exists($claim,$payload)){
                throw new \RuntimeException("Missing required claim: {$claim}");
            }
        }

        return $payload;

    }

    public static function buildPayload(
        string  $subject,
        array   $customClaims = [],
        int     $expiresInSeconds = 3600,
        ?string $issuer   = null,
        ?string $audience = null,
    ): array{
        $now = time();
       return array_merge([
            $customClaims,[
                'sub' => $subject,
                'iat' => $now,
                'exp' => $now + $expiresInSeconds,
                'nbf' => $now,
                'iss' => $issuer,
                'aud' => $audience,
                'jti' => bin2hex(random_bytes(16)),
            ]
        ]);
    }


}