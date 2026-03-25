<?php
declare(strict_types=1);

/**
 * Configuração única do projeto:
 * - Use variáveis de ambiente para evitar segredos no repositório.
 * - CHAVE_SECRETA é usada para gerar o hash HMAC do certificado.
 */

const DB_HOST = '127.0.0.1';
const DB_NAME = 'certificados';
const DB_USER = 'root';
const DB_PASS = '';
const DB_CHARSET = 'utf8mb4';

// Em produção, defina via variável de ambiente CERT_SECRET.
const CHAVE_SECRETA = 'TROQUE-ESTA-CHAVE-EM-PRODUCAO';

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

