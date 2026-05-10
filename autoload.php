<?php
// autoload.php

// ── .env loader ────────────────────────────────────────────────────────────
function loadEnv(string $path = __DIR__ . '/.env'): void
{
    if (!file_exists($path)) {
        throw new \RuntimeException(".env file not found at: {$path}");
    }

    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        // Skip comments
        if (str_starts_with(trim($line), '#')) continue;
        // Skip lines without =
        if (!str_contains($line, '=')) continue;

        [$key, $value] = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value, " \t\"'");  // strip quotes and whitespace

        // Only set if not already defined by the real environment
        // (real env vars take priority over .env file)
        if (getenv($key) === false) {
            putenv("{$key}={$value}");
            $_ENV[$key]    = $value;
            $_SERVER[$key] = $value;
        }
    }
}

loadEnv();

// ── PSR-4 autoloader ───────────────────────────────────────────────────────
spl_autoload_register(function (string $class): void {
    $file = __DIR__ . '/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});