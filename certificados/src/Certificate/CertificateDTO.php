<?php
declare(strict_types=1);

namespace App\Certificate;

readonly class CertificateDTO
{
    public function __construct(
        public ?int $id,
        public string $hash,
        public string $nome,
        public ?string $curso,
        public \DateTimeImmutable $dataEmissao,
        public ?string $cargaHoraria,
        public ?string $atividade,
        public ?string $instrutor,
        public \DateTimeImmutable $criadoEm,
    ) {}

    /**
     * @param array<string,mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            isset($row['id']) ? (int)$row['id'] : null,
            (string)$row['hash'],
            (string)$row['nome'],
            isset($row['curso']) ? (string)$row['curso'] : null,
            new \DateTimeImmutable((string)$row['data_emissao']),
            isset($row['carga_horaria']) ? (string)$row['carga_horaria'] : null,
            isset($row['atividade']) ? (string)$row['atividade'] : null,
            isset($row['instrutor']) ? (string)$row['instrutor'] : null,
            new \DateTimeImmutable((string)$row['criado_em']),
        );
    }
}

