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
        ?string $atividade,
        ?string $instrutor,
    ): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO certificados (hash, nome, funcao, data_emissao, carga_horaria, atividade, instrutor)
             VALUES (:hash, :nome, :funcao, :data_emissao, :carga_horaria, :atividade, :instrutor)'
        );

        $stmt->execute([
            ':hash' => $hash,
            ':nome' => $nome,
            ':funcao' => $funcao,
            ':data_emissao' => $dataEmissao,
            ':carga_horaria' => $cargaHoraria,
            ':atividade' => $atividade,
            ':instrutor' => $instrutor,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function findByHash(string $hash): ?CertificateDTO
    {
        $stmt = $this->pdo->prepare('SELECT * FROM certificados WHERE hash = :hash LIMIT 1');
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

