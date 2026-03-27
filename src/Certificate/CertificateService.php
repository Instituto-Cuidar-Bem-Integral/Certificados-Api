<?php
declare(strict_types=1);

namespace App\Certificate;

use App\QrCode\QrCodeService;

final class CertificateService
{
    public function __construct(
        private CertificateRepository $repo,
        private QrCodeService $qr,
        private string $baseUrlValidacao,
        private string $secretKey,
    ) {}

    /**
     * @return array{hash:string,id:int,qr_path:string,validar_url:string}
     */
    public function cadastrar(
        string $nome,
        ?string $funcao,
        string $dataEmissao,
        ?string $cargaHoraria,
        ?string $atividade,
        ?string $instrutor,
    ): array
    {
        $nome = trim($nome);
        $funcao = $funcao !== null ? trim($funcao) : null;
        $dataEmissao = trim($dataEmissao);
        $cargaHoraria = $cargaHoraria !== null ? trim($cargaHoraria) : null;
        $atividade = $atividade !== null ? trim($atividade) : null;
        $instrutor = $instrutor !== null ? trim($instrutor) : null;

        if ($nome === '') {
            throw new \InvalidArgumentException('Nome é obrigatório.');
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataEmissao)) {
            throw new \InvalidArgumentException('Data de emissão inválida (use YYYY-MM-DD).');
        }

        $hash = $this->gerarHash($nome, $funcao, $dataEmissao, $cargaHoraria, $atividade, $instrutor);
        $id = $this->repo->insert($hash, $nome, $funcao, $dataEmissao, $cargaHoraria, $atividade, $instrutor);

        $validarUrl = rtrim($this->baseUrlValidacao, '/') . '/public/validar.php?h=' . rawurlencode($hash);
        $qrPath = $this->qr->generatePng($validarUrl, $hash);

        return [
            'hash' => $hash,
            'id' => $id,
            'qr_path' => $qrPath,
            'validar_url' => $validarUrl,
        ];
    }

    public function buscarPorHash(string $hash): ?CertificateDTO
    {
        $hash = trim($hash);
        if ($hash === '' || !preg_match('/^[a-f0-9]{64}$/', $hash)) {
            return null;
        }
        return $this->repo->findByHash($hash);
    }

    private function gerarHash(
        string $nome,
        ?string $funcao,
        string $dataEmissao,
        ?string $cargaHoraria,
        ?string $atividade,
        ?string $instrutor,
    ): string
    {
        // Se você quiser mudar os "ingredientes", é só ajustar esta string base.
        $nonce = bin2hex(random_bytes(16));
        $base = implode('|', [
            $nome,
            $funcao ?? '',
            $dataEmissao,
            $cargaHoraria ?? '',
            $atividade ?? '',
            $instrutor ?? '',
            $nonce,
        ]);

        return hash_hmac('sha256', $base, $this->secretKey);
    }
}
