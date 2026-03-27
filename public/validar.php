<?php
declare(strict_types=1);

$autoload = __DIR__ . '/../vendor/autoload.php';
if (!is_file($autoload)) {
    header('Content-Type: text/plain; charset=utf-8');
    http_response_code(500);
    echo "Dependências não instaladas. Rode Composer na raiz do projeto.\n";
    exit;
}
require $autoload;

require __DIR__ . '/../config/conexao.php';

use App\Certificate\CertificateRepository;
use App\Certificate\CertificateService;
use App\QrCode\QrCodeService;

$hash = isset($_GET['h']) ? (string)$_GET['h'] : '';
$hash = trim($hash);

$cert = null;

try {
    $repo = new CertificateRepository(db());
    $qr = new QrCodeService(__DIR__ . '/qrcodes');
    $service = new CertificateService(
        $repo,
        $qr,
        (isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http') . '://' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost'),
        CHAVE_SECRETA
    );

    $cert = $service->buscarPorHash($hash);
} catch (Throwable $t) {
    header('Content-Type: text/plain; charset=utf-8');
    http_response_code(500);
    echo "Erro ao buscar certificado: " . $t->getMessage() . "\n";
    exit;
}

function e(string $v): string
{
    return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function fmtDate(\DateTimeImmutable $d): string
{
    return $d->format('d/m/Y');
}

$codigo = ($cert !== null) ? substr($cert->hash, 0, 12) : '';
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Validação de Certificado</title>
    <style>
        :root { color-scheme: light; }
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin: 0; background: #0b1220; color: #e8eefc; }
        .wrap { max-width: 860px; margin: 0 auto; padding: 28px 16px 48px; }
        .card { background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.14); border-radius: 14px; padding: 18px; }
        .title { display:flex; gap:10px; align-items:center; margin:0 0 12px; font-size: 22px; }
        .ok { color: #4ade80; }
        .bad { color: #fb7185; }
        .grid { display:grid; grid-template-columns: 1fr; gap: 10px; margin-top: 12px; }
        @media (min-width: 720px){ .grid { grid-template-columns: 1fr 1fr; } }
        .item { background: rgba(0,0,0,.18); border: 1px solid rgba(255,255,255,.10); border-radius: 12px; padding: 12px; }
        .lbl { font-size: 12px; opacity: .8; margin-bottom: 6px; }
        .val { font-size: 16px; font-weight: 600; }
        .foot { margin-top: 14px; font-size: 12px; opacity: .75; }
        a { color: #93c5fd; }
    </style>
</head>
<body>
<main class="wrap">
    <section class="card">
        <?php if ($cert !== null): ?>
            <h1 class="title"><span class="ok">✅</span> Certificado Válido</h1>
            <div class="grid">
                <div class="item">
                    <div class="lbl">Nome completo</div>
                    <div class="val"><?= e($cert->nome) ?></div>
                </div>
                <div class="item">
                    <div class="lbl">Curso</div>
                    <div class="val"><?= e($cert->curso ?? '-') ?></div>
                </div>
                <div class="item">
                    <div class="lbl">Data de emissão</div>
                    <div class="val"><?= e(fmtDate($cert->dataEmissao)) ?></div>
                </div>
                <div class="item">
                    <div class="lbl">Carga horária</div>
                    <div class="val"><?= e($cert->cargaHoraria ?? '-') ?></div>
                </div>
                <div class="item">
                    <div class="lbl">Instrutor</div>
                    <div class="val"><?= e($cert->instrutor ?? '-') ?></div>
                </div>
                <div class="item">
                    <div class="lbl">Código do certificado</div>
                    <div class="val"><?= e($codigo) ?></div>
                </div>
            </div>
            <div class="foot">Se você precisar validar novamente, use este hash completo: <code><?= e($cert->hash) ?></code></div>
        <?php else: ?>
            <h1 class="title"><span class="bad">❌</span> Certificado não encontrado ou inválido</h1>
            <div class="foot">Verifique se o link/QR está completo. Se o problema persistir, entre em contato com a instituição.</div>
        <?php endif; ?>
    </section>
</main>
</body>
</html>

