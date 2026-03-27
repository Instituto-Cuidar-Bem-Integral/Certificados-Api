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
$funcao = '';
$dataEmissao = '';
$cargaHoraria = '';
$atividade = '';
$instrutor = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = isset($_POST['nome']) ? (string)$_POST['nome'] : '';
    $funcao = isset($_POST['funcao']) ? (string)$_POST['funcao'] : '';
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
            $funcao !== '' ? $funcao : null,
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

<?php if (!empty($errors)): ?>
    <div style="color: red; margin-bottom: 10px;">
        <strong>Erro:</strong>
        <?php foreach ($errors as $err): ?>
            <div><?= e((string)$err) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (is_array($result)): ?>
    <div style="color: green; margin-bottom: 10px;">
        <strong>✅ Certificado cadastrado</strong>
        <div>Hash: <?= e($result['hash']) ?></div>
        <div>Link de validação: <a href="<?= e($result['validar_url']) ?>" target="_blank">abrir validação</a></div>
        <div>PDF: <a href="<?= e($result['pdf_url']) ?>" target="_blank">gerar/abrir PDF</a></div>
    </div>
<?php endif; ?>

<form method="post" action="">
    <div>
        <label for="nome">Nome completo *</label>
        <input id="nome" name="nome" value="<?= e($nome) ?>" required>
    </div>
    <div>
        <label for="funcao">Função</label>
        <input id="funcao" name="funcao" value="<?= e($funcao) ?>">
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
    <div style="margin-top:14px">
        <button type="submit">Cadastrar e gerar QR</button>
    </div>
</form>

