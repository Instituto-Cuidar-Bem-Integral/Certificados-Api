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

require __DIR__ . '/../config/database.php';

use App\Certificate\CertificateRepository;
use App\Certificate\CertificateService;
use App\QrCode\QrCodeService;

function e(string $v): string
{
    return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$errors = [];
$result = null;

$nome = '';
$curso = '';
$dataEmissao = '';
$cargaHoraria = '';
$atividade = '';
$instrutor = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = isset($_POST['nome']) ? (string)$_POST['nome'] : '';
    $curso = isset($_POST['curso']) ? (string)$_POST['curso'] : '';
    $dataEmissao = isset($_POST['data_emissao']) ? (string)$_POST['data_emissao'] : '';
    $cargaHoraria = isset($_POST['carga_horaria']) ? (string)$_POST['carga_horaria'] : '';
    $atividade = isset($_POST['atividade']) ? (string)$_POST['atividade'] : '';
    $instrutor = isset($_POST['instrutor']) ? (string)$_POST['instrutor'] : '';

    try {
        $repo = new CertificateRepository(db());
        $qr = new QrCodeService(__DIR__ . '/../public/qrcodes');
        $service = new CertificateService(
            $repo,
            $qr,
            (isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http') . '://' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost'),
            CHAVE_SECRETA
        );

        $result = $service->cadastrar(
            $nome,
            $curso !== '' ? $curso : null,
            $dataEmissao,
            $cargaHoraria !== '' ? $cargaHoraria : null,
            $atividade !== '' ? $atividade : null,
            $instrutor !== '' ? $instrutor : null,
        );
        $result['pdf_url'] = '../public/certificado.php?h=' . rawurlencode($result['hash']);
    } catch (Throwable $t) {
        $errors[] = $t->getMessage();
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Admin - Cadastrar Certificado</title>
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin: 0; background: #0b1220; color:#e8eefc; }
        .wrap { max-width: 980px; margin: 0 auto; padding: 24px 16px 48px; }
        .top { display:flex; justify-content: space-between; align-items: baseline; gap: 12px; flex-wrap: wrap; }
        a { color:#93c5fd; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .card { background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.14); border-radius: 14px; padding: 18px; margin-top: 14px; }
        .grid { display:grid; grid-template-columns: 1fr; gap: 12px; }
        @media (min-width: 820px){ .grid { grid-template-columns: 1fr 1fr; } }
        label { display:block; font-size: 12px; opacity: .85; margin-bottom: 6px; }
        input, textarea { width: 100%; box-sizing:border-box; border-radius: 12px; border: 1px solid rgba(255,255,255,.18); background: rgba(0,0,0,.22); color:#e8eefc; padding: 10px 12px; outline: none; }
        textarea { min-height: 92px; resize: vertical; }
        .btn { display:inline-block; border: 1px solid rgba(255,255,255,.18); background: rgba(59,130,246,.18); color:#e8eefc; padding: 10px 14px; border-radius: 12px; cursor:pointer; font-weight: 600; }
        .btn:hover { background: rgba(59,130,246,.26); }
        .msg-ok { border: 1px solid rgba(74,222,128,.35); background: rgba(74,222,128,.10); padding: 12px; border-radius: 12px; }
        .msg-bad { border: 1px solid rgba(251,113,133,.35); background: rgba(251,113,133,.10); padding: 12px; border-radius: 12px; }
        code { background: rgba(0,0,0,.25); padding: 2px 6px; border-radius: 8px; }
        .qr { margin-top: 10px; display:flex; gap:12px; align-items: center; flex-wrap: wrap; }
        .qr img { width: 140px; height: 140px; border-radius: 10px; border: 1px solid rgba(255,255,255,.14); background: #fff; }
        .pill { display:inline-block; font-size: 12px; padding: 4px 10px; border-radius: 999px; border: 1px solid rgba(255,255,255,.18); background: rgba(255,255,255,.06); }
    </style>
</head>
<body>
<main class="wrap">
    <div class="top">
        <h1 style="margin:0">Cadastrar certificado</h1>
        <nav style="display:flex;gap:12px;flex-wrap:wrap;align-items:center">
            <a href="listar.php">Listar certificados</a>
        </nav>
    </div>

    <?php if (!empty($errors)): ?>
        <section class="card msg-bad">
            <strong>Erro</strong>
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= e((string)$err) ?></li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endif; ?>

    <?php if (is_array($result)): ?>
        <section class="card msg-ok">
            <strong>✅ Certificado cadastrado</strong>
            <div style="margin-top:8px">Hash: <code><?= e($result['hash']) ?></code></div>
            <div style="margin-top:6px">Link de validação: <a href="<?= e($result['validar_url']) ?>" target="_blank" rel="noopener">abrir validação</a></div>
            <div style="margin-top:6px">PDF: <a href="<?= e($result['pdf_url']) ?>" target="_blank" rel="noopener">gerar/abrir PDF</a></div>
            <div class="qr">
                <img src="../public/qrcodes/<?= e($result['hash']) ?>.png" alt="QR Code">
                <div>
                    <div>Arquivo: <code><?= e($result['qr_path']) ?></code></div>
                    <div style="opacity:.8;font-size:12px;margin-top:6px">O QR foi gerado em `public/qrcodes/{hash}.png`.</div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="card">
        <form method="post" action="">
            <div class="grid">
                <div>
                    <label for="nome">Nome completo *</label>
                    <input id="nome" name="nome" value="<?= e($nome) ?>" required>
                </div>
                <div>
                    <label for="curso">Curso</label>
                    <input id="curso" name="curso" value="<?= e($curso) ?>">
                </div>
                <div>
                    <label for="data_emissao">Data de emissão (YYYY-MM-DD) *</label>
                    <input id="data_emissao" name="data_emissao" value="<?= e($dataEmissao) ?>" placeholder="2026-03-18" required>
                </div>
                <div>
                    <label for="carga_horaria">Carga horária</label>
                    <input id="carga_horaria" name="carga_horaria" value="<?= e($cargaHoraria) ?>" placeholder="6 horas">
                </div>
                <div>
                    <label for="instrutor">Instrutor</label>
                    <input id="instrutor" name="instrutor" value="<?= e($instrutor) ?>">
                </div>
                <div>
                    <label for="atividade">Atividade</label>
                    <input id="atividade" name="atividade" value="<?= e($atividade) ?>">
                </div>
            </div>

            <div style="margin-top:14px">
                <button class="btn" type="submit">Cadastrar e gerar QR</button>
            </div>
        </form>
    </section>
</main>
</body>
</html>

