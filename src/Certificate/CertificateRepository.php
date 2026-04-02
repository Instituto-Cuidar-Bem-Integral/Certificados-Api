<?php
declare(strict_types=1);

namespace App\Certificate;

use PDO;

final class CertificateRepository
{
    public function __construct(private PDO $pdo) {}

    public function insert(
        string $hash,
        string $nome,
        ?string $funcao,
        string $dataEmissao,
        ?string $cargaHoraria,
        int $hasAssinaturaAdicional = 0,
    ): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO certificados (hash, nome, funcao, data_emissao, carga_horaria, has_assinatura_adicional)
             VALUES (:hash, :nome, :funcao, :data_emissao, :carga_horaria, :has_assinatura_adicional)'
        );

        $stmt->execute([
            ':hash' => $hash,
            ':nome' => $nome,
            ':funcao' => $funcao,
            ':data_emissao' => $dataEmissao,
            ':carga_horaria' => $cargaHoraria,
            ':has_assinatura_adicional' => $hasAssinaturaAdicional,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function findByHash(string $hash): ?CertificateDTO
    {
        $stmt = $this->pdo->prepare(
            'SELECT c.id,c.hash,c.nome,c.funcao,c.data_emissao,c.carga_horaria,c.has_assinatura_adicional,c.criado_em
        FROM certificados c 
        WHERE c.hash = :hash LIMIT 1');
        $stmt->execute([':hash' => $hash]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return CertificateDTO::fromRow($row);
    }

    /**
     * @return array<int,CertificateDTO>
     */
    public function listAll(int $limit = 200): array
    {
        $limit = max(1, min(1000, $limit));
        $stmt = $this->pdo->query('SELECT * FROM certificados ORDER BY id DESC LIMIT ' . (int)$limit);
        $rows = $stmt->fetchAll();

        $out = [];
        foreach ($rows as $row) {
            $out[] = CertificateDTO::fromRow($row);
        }
        return $out;
    }
}