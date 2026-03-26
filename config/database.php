<?php
declare(strict_types=1);

/**
 * Configuração única do projeto:
 * - Prioriza variáveis de ambiente reais e depois `.env`.
 * - CHAVE_SECRETA é usada para gerar o hash HMAC do certificado.
 */

/**
 * @return array<string,string>
 */
function loadProjectEnvFile(): array
{
    static $values = null;

    if (is_array($values)) {
        return $values;
    }

    $values = [];
    $envFile = dirname(__DIR__) . '/.env';

    if (!is_file($envFile) || !is_readable($envFile)) {
        return $values;
    }

    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!is_array($lines)) {
        return $values;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        if ($key === '') {
            continue;
        }

        $firstChar = $value[0] ?? '';
        $lastChar = $value !== '' ? $value[strlen($value) - 1] : '';
        if (($firstChar === '"' && $lastChar === '"') || ($firstChar === "'" && $lastChar === "'")) {
            $value = substr($value, 1, -1);
        }

        $values[$key] = $value;
    }

    return $values;
}

function envValue(string $key, string $default = ''): string
{
    $serverValue = $_SERVER[$key] ?? null;
    if (is_string($serverValue) && $serverValue !== '') {
        return $serverValue;
    }

    $envValue = getenv($key);
    if (is_string($envValue) && $envValue !== '') {
        return $envValue;
    }

    $fileValues = loadProjectEnvFile();
    if (array_key_exists($key, $fileValues) && $fileValues[$key] !== '') {
        return $fileValues[$key];
    }

    return $default;
}

if (!defined('DB_HOST')) {
    define('DB_HOST', envValue('DB_HOST', '127.0.0.1'));
}

if (!defined('DB_NAME')) {
    define('DB_NAME', envValue('DB_NAME', 'certificados'));
}

if (!defined('DB_USER')) {
    define('DB_USER', envValue('DB_USER', 'root'));
}

if (!defined('DB_PASS')) {
    define('DB_PASS', envValue('DB_PASS', ''));
}

if (!defined('DB_CHARSET')) {
    define('DB_CHARSET', envValue('DB_CHARSET', 'utf8mb4'));
}

if (!defined('CHAVE_SECRETA')) {
    define('CHAVE_SECRETA', envValue('CERT_SECRET', 'TROQUE-ESTA-CHAVE-EM-PRODUCAO'));
}

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);

    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}
