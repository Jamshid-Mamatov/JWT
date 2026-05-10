<?php

interface SignerInterface
{
    public function sign(string $data): string;
    public function verify(string $data, string $signature): bool;
    public function algorithmId(): string;
}