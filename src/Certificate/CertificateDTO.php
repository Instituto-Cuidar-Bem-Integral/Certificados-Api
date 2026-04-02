<?php
declare(strict_types=1);

namespace App\Certificate;

readonly class CertificateDTO
{
    public function __construct(
        public ?int $id,
        public string $hash,
        public string $nome,
        public ?string $funcao,
        public \DateTimeImmutable $dataEmissao,
        public ?string $cargaHoraria,
        public int $hasAssinaturaAdicional,
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
            isset($row['funcao']) ? (string)$row['funcao'] : null,
            new \DateTimeImmutable((string)$row['data_emissao']),
            isset($row['carga_horaria']) ? (string)$row['carga_horaria'] : null,
            isset($row['has_assinatura_adicional']) ? (int)$row['has_assinatura_adicional'] : 0,
            new \DateTimeImmutable((string)$row['criado_em']),
        );
    }
}