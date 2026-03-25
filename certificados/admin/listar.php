<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/database.php';

use App\Certificate\CertificateRepository;
use App\Certificate\CertificateDTO;

function e(string $v): string
{
    return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$useMock = isset($_GET['mock']) && (string)$_GET['mock'] === '1';
$loadError = null;

/**
 * @return array<int,CertificateDTO>
 */
function mockItems(): array
{
    require_once __DIR__ . '/../mocks/certificados.php';
    /** @var array<int,array<string,mixed>> $rows */
    $rows = mock_certificados_rows();
    return array_map(static fn(array $r) => CertificateDTO::fromRow($r), $rows);
}

$items = [];
if ($useMock) {
    $items = mockItems();
} else {
    try {
        $repo = new CertificateRepository(db());
        $items = $repo->listAll(300);
        if (count($items) === 0) {
            $items = mockItems();
            $useMock = true;
        }
    } catch (Throwable $t) {
        $items = mockItems();
        $useMock = true;
        $loadError = $t->getMessage();
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Admin - Listar Certificados</title>
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin: 0; background: #0b1220; color:#e8eefc; }
        .wrap { max-width: 1100px; margin: 0 auto; padding: 24px 16px 48px; }
        .top { display:flex; justify-content: space-between; align-items: baseline; gap: 12px; flex-wrap: wrap; }
        a { color:#93c5fd; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .card { background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.14); border-radius: 14px; padding: 18px; margin-top: 14px; overflow-x:auto; }
        table { width: 100%; border-collapse: collapse; min-width: 860px; }
        th, td { text-align:left; padding: 10px 10px; border-bottom: 1px solid rgba(255,255,255,.10); vertical-align: top; }
        th { font-size: 12px; opacity: .8; letter-spacing: .02em; text-transform: uppercase; }
        td { font-size: 14px; }
        code { background: rgba(0,0,0,.25); padding: 2px 6px; border-radius: 8px; }
        .muted { opacity: .75; font-size: 12px; }
        .pill { display:inline-block; font-size: 12px; padding: 4px 10px; border-radius: 999px; border: 1px solid rgba(255,255,255,.18); background: rgba(255,255,255,.06); }
    </style>
</head>
<body>
<main class="wrap">
    <div class="top">
        <h1 style="margin:0">Certificados</h1>
        <nav style="display:flex;gap:12px;flex-wrap:wrap;align-items:center">
            <a href="cadastrar.php">Cadastrar novo</a>
            <?php if ($useMock): ?>
                <span class="pill">MOCK</span>
            <?php endif; ?>
        </nav>
    </div>

    <?php if ($loadError): ?>
        <section class="card" style="border-color: rgba(251,113,133,.35); background: rgba(251,113,133,.08)">
            <strong>Usando mock</strong>
            <div class="muted" style="margin-top:6px">Falha ao carregar do banco: <?= e($loadError) ?></div>
            <div class="muted" style="margin-top:6px">Para forçar mock: <code>?mock=1</code></div>
        </section>
    <?php elseif ($useMock): ?>
        <section class="card" style="border-color: rgba(74,222,128,.35); background: rgba(74,222,128,.08)">
            <strong>Usando mock</strong>
            <div class="muted" style="margin-top:6px">Para desativar, remova <code>?mock=1</code> e configure o banco.</div>
        </section>
    <?php endif; ?>

    <section class="card">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Curso</th>
                    <th>Data emissão</th>
                    <th>Carga horária</th>
                    <th>Instrutor</th>
                    <th>Código</th>
                    <th>QR</th>
                    <th>Validação</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $c): ?>
                <tr>
                    <td><?= (int)$c->id ?></td>
                    <td><?= e($c->nome) ?></td>
                    <td><?= e($c->curso ?? '-') ?></td>
                    <td><?= e($c->dataEmissao->format('d/m/Y')) ?></td>
                    <td><?= e($c->cargaHoraria ?? '-') ?></td>
                    <td><?= e($c->instrutor ?? '-') ?></td>
                    <td><code><?= e(substr($c->hash, 0, 12)) ?></code></td>
                    <td>
                        <?php $qrRel = '../public/qrcodes/' . $c->hash . '.png'; ?>
                        <a href="<?= e($qrRel) ?>" target="_blank" rel="noopener">ver</a>
                        <div class="muted"><?= e($c->hash) ?></div>
                    </td>
                    <td>
                        <?php $valRel = '../public/validar.php?h=' . rawurlencode($c->hash); ?>
                        <a href="<?= e($valRel) ?>" target="_blank" rel="noopener">validar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</main>
</body>
</html>

