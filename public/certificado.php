<?php
declare(strict_types=1);

// Gera PDF do certificado via mPDF.

$autoload = __DIR__ . '/../vendor/autoload.php';
if (!is_file($autoload)) {
    header('Content-Type: text/plain; charset=utf-8');
    http_response_code(500);
    echo "Dependências não instaladas. Rode Composer na raiz do projeto.\n";
    echo "Depois instale/extensão GD (mPDF exige): sudo apt install php8.3-gd\n";
    exit;
}
require $autoload;
require __DIR__ . '/../config/conexao.php';

use App\Certificate\CertificateRepository;

$hash = isset($_GET['h']) ? trim((string)$_GET['h']) : '';
if ($hash === '') {
    header('Content-Type: text/plain; charset=utf-8');
    http_response_code(400);
    echo "Hash do certificado é obrigatório.\n";
    exit;
}

function e(string $v): string
{
    return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function fileToDataUri(?string $path): string
{
    if ($path === null || !is_file($path)) {
        return '';
    }

    $mime = mime_content_type($path) ?: 'image/png';
    $contents = file_get_contents($path);

    if ($contents === false) {
        return '';
    }

    return 'data:' . $mime . ';base64,' . base64_encode($contents);
}

function formatDateLongPtBr(DateTimeInterface $date): string
{
    static $months = [
        1 => 'janeiro',
        2 => 'fevereiro',
        3 => 'março',
        4 => 'abril',
        5 => 'maio',
        6 => 'junho',
        7 => 'julho',
        8 => 'agosto',
        9 => 'setembro',
        10 => 'outubro',
        11 => 'novembro',
        12 => 'dezembro',
    ];

    $month = $months[(int)$date->format('n')] ?? '';

    return $date->format('j') . ' de ' . $month . ' de ' . $date->format('Y');
}


/**
 * Template do certificado no novo modelo (conforme PDF de referência).
 * Design limpo sem borda decorativa azul.
 */
final class CertificatePdfTemplate
{
    public const REFERENCE_WIDTH_MM = 297.0;
    public const REFERENCE_HEIGHT_MM = 210.0;
    public const PAGE_WIDTH_MM = 254.0;
    public const PAGE_HEIGHT_MM = 142.875;
    public const BLEED_MM = 0.8;

    // Cores do novo modelo
    public const COLOR_PRIMARY = '#1a3c6e';    // Azul escuro principal
    public const COLOR_ACCENT = '#2d6cb5';     // Azul destaque
    public const COLOR_GREEN = '#4a8c3f';      // Verde institucional
    public const COLOR_GREEN_LIGHT = '#6daa5d'; // Verde claro para linhas
    public const COLOR_TEXT = '#1a1a1a';        // Texto principal
    public const COLOR_TEXT_LIGHT = '#555555'; // Texto secundário

    public static function createMpdf(): \Mpdf\Mpdf
    {
        $tempDir = sys_get_temp_dir() . '/certificado-mpdf';
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        return new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => [self::PAGE_WIDTH_MM, self::PAGE_HEIGHT_MM],
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'tempDir' => $tempDir,
        ]);
    }

    /**
     * Gera o HTML da página frontal do certificado (modelo novo).
     */
    public static function buildFrontPageHtml(array $data): string
    {
        $mm = static fn(float $value): string => self::mm($value);
        $sx = static fn(float $value): string => self::sx($value);
        $sy = static fn(float $value): string => self::sy($value);
        $pt = static fn(float $value): string => self::pt($value);
        $qr = static fn(float $value): string => self::scaledNumber($value);
        $bleed = self::mm(self::BLEED_MM);
        $fullBleedWidth = self::mm(self::PAGE_WIDTH_MM + (self::BLEED_MM * 2));
        $fullBleedHeight = self::mm(self::PAGE_HEIGHT_MM + (self::BLEED_MM * 2));
        $sidebarBleedWidth = self::mm((71.0 * (self::PAGE_WIDTH_MM / self::REFERENCE_WIDTH_MM)) + self::BLEED_MM);
        $nome = e((string)($data['nome'] ?? ''));
        $funcao = e((string)($data['funcao'] ?? ''));
        $horas = e((string)($data['horas'] ?? ''));
        $cidade = e((string)($data['cidade'] ?? ''));
        $dataEmissao = e((string)($data['data_emissao'] ?? ''));
        $assinanteNome = e((string)($data['assinante_nome'] ?? ''));
        $assinanteCargo = e((string)($data['assinante_cargo'] ?? ''));
        $instrutorNome = e((string)($data['instrutor_nome'] ?? ''));
        $instrutorCargo = e((string)($data['instrutor_cargo'] ?? ''));
        $mostrarInstrutor = (bool)($data['mostrar_instrutor'] ?? false);
        $qrUrl = e((string)($data['validar_url'] ?? ''));
        $logoSrc = (string)($data['logo_src'] ?? '');
        $sidebarSrc = (string)($data['sidebar_src'] ?? '');

        $logoHtml = $logoSrc !== ''
            ? '<img src="' . $logoSrc . '" alt="Logo Instituto Cuidar Bem Integral" style="width: ' . $sx(44.0) . '; height: auto; display: block; margin: 0 auto;">'
            : '';
        $instrutorHtml = '';
        if ($mostrarInstrutor && $instrutorNome !== '' && $instrutorCargo !== '') {
            $instrutorHtml = '<div style="font-family: arial; font-size: ' . $pt(12.8) . '; font-weight: bold; color: #4d4d4d; line-height: 1.15;">' . $instrutorNome . '</div>'
                . '<div style="font-family: arial; font-size: ' . $pt(11.2) . '; color: #666666; line-height: 1.15;">' . $instrutorCargo . '</div>';
        }

        return <<<HTML
<div style="
    position: absolute;
    left: -{$bleed};
    top: -{$bleed};
    width: {$fullBleedWidth};
    height: {$fullBleedHeight};
    background-color: #ffffff;
"></div>

<div style="
    position: absolute;
    left: -{$bleed};
    top: -{$bleed};
    width: {$sidebarBleedWidth};
    height: {$fullBleedHeight};
    background-image: url({$sidebarSrc});
    background-repeat: no-repeat;
    background-position: left top;
    background-image-resize: 6;
"></div>

<div style="
    position: absolute;
    left: {$sx(8.0)};
    top: {$sy(13.0)};
    width: {$sx(50.0)};
    text-align: center;
">
    {$logoHtml}
</div>

<div style="
    position: absolute;
    left: {$sx(11.6)};
    top: {$sy(122.5)};
    width: {$sx(43.0)};
    color: #ffffff;
    font-family: arial;
    font-size: {$pt(33.5)};
    line-height: 1.34;
">
    Instituto<br>
    Cuidar<br>
    Bem<br>
    Integral
</div>

<div style="
    position: absolute;
    left: {$sx(84.0)};
    top: {$sy(19.5)};
    color: #4d88db;
    font-family: arial;
    font-size: {$pt(58.5)};
    line-height: 0.9;
    letter-spacing: {$sx(0.65)};
    font-weight: normal;
">CERTIFICADO</div>

<div style="
    position: absolute;
    left: {$sx(242.5)};
    top: {$sy(12.2)};
    width: {$sx(40.0)};
    text-align: right;
">
    <barcode code="{$qrUrl}" type="QR" size="{$qr(1.68)}" error="M" disableborder="1" />
</div>

<div style="
    position: absolute;
    left: {$sx(88.0)};
    top: {$sy(59.5)};
    font-family: arial;
    font-size: {$pt(17.0)};
    color: #5c5c5c;
">Certificamos que</div>

<div style="
    position: absolute;
    left: {$sx(88.0)};
    top: {$sy(74.6)};
    font-family: arial;
    font-size: {$pt(33.8)};
    font-weight: bold;
    color: #75d54f;
">{$nome}</div>

<div style="
    position: absolute;
    left: {$sx(88.0)};
    top: {$sy(94.0)};
    width: {$sx(198.5)};
    font-family: arial;
    font-size: {$pt(16.1)};
    color: #5a5a5a;
    line-height: 1.13;
    text-align: left;
">
    Atuou como voluntário(a) na função de {$funcao}, cumprindo carga horária total de {$horas}, contribuindo para as atividades institucionais do Instituto Cuidar Bem - Integral.
</div>

<div style="
    position: absolute;
    left: {$sx(88.0)};
    top: {$sy(131.5)};
    font-family: arial;
    font-size: {$pt(16.1)};
    color: #5a5a5a;
">{$cidade}, {$dataEmissao}.</div>

<div style="
    position: absolute;
    left: {$sx(90.0)};
    top: {$sy(177.8)};
    width: {$sx(84.0)};
    text-align: center;
">
    <div style="font-family: arial; font-size: {$pt(13.2)}; font-weight: bold; color: #4d4d4d; line-height: 1.15;">{$assinanteNome}</div>
    <div style="font-family: arial; font-size: {$pt(11.4)}; color: #666666; line-height: 1.15;">{$assinanteCargo}</div>
</div>

<div style="
    position: absolute;
    left: {$sx(214.0)};
    top: {$sy(177.8)};
    width: {$sx(43.0)};
    text-align: center;
">
    {$instrutorHtml}
</div>
HTML;
    }

    /**
     * Gera o HTML da página de detalhes (verso do certificado).
     */
    public static function buildDetailsPageHtml(array $data): string
    {
        $mm = static fn(float $value): string => self::mm($value);
        $sx = static fn(float $value): string => self::sx($value);
        $sy = static fn(float $value): string => self::sy($value);
        $pt = static fn(float $value): string => self::pt($value);
        $bleed = self::mm(self::BLEED_MM);
        $fullBleedWidth = self::mm(self::PAGE_WIDTH_MM + (self::BLEED_MM * 2));
        $fullBleedHeight = self::mm(self::PAGE_HEIGHT_MM + (self::BLEED_MM * 2));
        $contentHtml = (string)($data['content_html'] ?? '');
        $footerContato = e((string)($data['footer_contato'] ?? ''));
        $backgroundSrc = (string)($data['background_src'] ?? '');

        return <<<HTML
<div style="
    position: absolute;
    left: -{$bleed};
    top: -{$bleed};
    width: {$fullBleedWidth};
    height: {$fullBleedHeight};
    background-image: url({$backgroundSrc});
    background-repeat: no-repeat;
    background-position: left top;
    background-image-resize: 6;
"></div>

<div style="
    position: absolute;
    left: {$sx(6.0)};
    top: {$sy(6.0)};
    width: {$sx(285.0)};
    height: {$sy(198.0)};
    background-color: #ffffff;
"></div>

<div style="
    position: absolute;
    left: {$sx(6.0)};
    top: {$sy(14.0)};
    width: {$sx(285.0)};
    text-align: center;
    font-family: dejavusans;
    font-size: {$pt(25.0)};
    font-weight: bold;
    color: #4d88db;
">Principais atividades desenvolvidas</div>

<div style="
    position: absolute;
    left: {$sx(6.0)};
    top: {$sy(49.0)};
    right: {$sx(6.0)};
    bottom: {$sy(24.0)};
    font-family: dejavusans;
">
    {$contentHtml}
</div>

<div style="
    position: absolute;
    left: {$sx(6.0)};
    right: {$sx(6.0)};
    bottom: {$sy(14.0)};
    text-align: center;
    font-family: dejavusans;
    font-size: {$pt(11.5)};
    color: #5d5d5d;
">
    {$footerContato}
</div>
HTML;
    }

    /**
     * Gera HTML para lista de atividades.
     */
    public static function buildListHtml(array $items): string
    {
        if (empty($items)) {
            return '<div style="font-size: ' . self::pt(10.5) . '; color: #666666;">Nenhuma atividade vinculada a este certificado.</div>';
        }

        $paddingLeft = self::sx(6.0);
        $fontSize = self::pt(11.0);
        $itemMargin = self::sy(2.0);
        $bulletColor = self::COLOR_GREEN_LIGHT;

        $itemsHtml = implode('', array_map(
            static fn($item): string => '<li style="margin: 0 0 ' . $itemMargin . '; color: ' . self::COLOR_TEXT . '; list-style: none; position: relative; padding-left: ' . self::sx(5.0) . '; line-height: 1.45;"><span style="position: absolute; left: 0; color: ' . $bulletColor . '; font-weight: bold;">&#8226;</span>' . e((string)$item) . '</li>',
            $items
        ));

        return '<ul style="margin: 0; padding-left: ' . $paddingLeft . '; font-size: ' . $fontSize . ';">' . $itemsHtml . '</ul>';
    }

    /**
     * Converte valor para string com unidade mm.
     */
    public static function mm(float $value): string
    {
        return rtrim(rtrim(sprintf('%.3F', $value), '0'), '.') . 'mm';
    }

    public static function pt(float $value): string
    {
        return rtrim(rtrim(sprintf('%.2F', $value * self::fontScale()), '0'), '.') . 'pt';
    }

    private static function sx(float $value): string
    {
        return self::mm($value * (self::PAGE_WIDTH_MM / self::REFERENCE_WIDTH_MM));
    }

    private static function sy(float $value): string
    {
        return self::mm($value * (self::PAGE_HEIGHT_MM / self::REFERENCE_HEIGHT_MM));
    }

    private static function scaledNumber(float $value): string
    {
        return rtrim(rtrim(sprintf('%.2F', $value * self::fontScale()), '0'), '.');
    }

    private static function fontScale(): float
    {
        return sqrt(
            (self::PAGE_WIDTH_MM / self::REFERENCE_WIDTH_MM)
            * (self::PAGE_HEIGHT_MM / self::REFERENCE_HEIGHT_MM)
        );
    }
}

$row = null;
try {
    $repo = new CertificateRepository(db());
    $dto = $repo->findByHash($hash);
    if ($dto) {
        $row = [
            'id' => $dto->id,
            'hash' => $dto->hash,
            'nome' => $dto->nome,
            'funcao' => $dto->funcao,
            'data_emissao' => $dto->dataEmissao->format('Y-m-d'),
            'carga_horaria' => $dto->cargaHoraria,
            'has_assinatura_adicional' => $dto->hasAssinaturaAdicional ?? 0,
            'atividade' => $dto->funcao,
            'criado_em' => $dto->criadoEm->format('Y-m-d H:i:s'),
            'descricao' => null,
            'atividades_lista' => [],
            'cidade_uf' => 'Rio de Janeiro / RJ',
            'periodo_inicio' => null,
            'periodo_fim' => null,
            'assinatura_1_nome' => 'Yuri Rocha de Jesus',
            'assinatura_1_cargo' => 'Vice-Presidente Executivo',
            'assinatura_2_nome' => 'Vitor de Souza Silva',
            'assinatura_2_cargo' => 'Presidente Executivo',
            'instituicao' => 'INSTITUTO CUIDAR BEM - INTEGRAL',
        ];
    }
} catch (Throwable $t) {
    header('Content-Type: text/plain; charset=utf-8');
    http_response_code(500);
    echo "Erro ao buscar certificado: " . $t->getMessage() . "\n";
    exit;
}

if (!$row) {
    header('Content-Type: text/plain; charset=utf-8');
    http_response_code(404);
    echo "Certificado não encontrado.\n";
    exit;
}

$dataEmissao = new DateTimeImmutable((string)$row['data_emissao']);
$dataFmt = $dataEmissao->format('d/m/Y');
$dataExtenso = formatDateLongPtBr($dataEmissao);
$codigo = substr((string)$row['hash'], 0, 12);

$nome = (string)$row['nome'];
$funcao = (string)($row['funcao'] ?? '');
$atividade = (string)($row['atividade'] ?? '');
$carga = (string)($row['carga_horaria'] ?? '');
$instituicao = (string)($row['instituicao'] ?? 'INSTITUTO CUIDAR BEM - INTEGRAL');
$cidadeUf = (string)($row['cidade_uf'] ?? '');
$desc = (string)($row['descricao'] ?? '');
$periodoInicioExtenso = !empty($row['periodo_inicio'])
    ? formatDateLongPtBr(new DateTimeImmutable((string)$row['periodo_inicio']))
    : '';
$periodoFimExtenso = !empty($row['periodo_fim'])
    ? formatDateLongPtBr(new DateTimeImmutable((string)$row['periodo_fim']))
    : '';

$hasAssinaturaAdicional = (int)($row['has_assinatura_adicional'] ?? 0);

$atividadesLista = is_array($row['atividades_lista'] ?? null) ? $row['atividades_lista'] : [];
try {
    $stmt = db()->prepare(
        'SELECT ca.atividade 
         FROM certificados_atividades_grupo cag
         INNER JOIN certificados_atividades ca ON ca.id = cag.id_cer_atividade
         WHERE cag.id_certificado = :id_certificado
         ORDER BY ca.id'
    );
    $stmt->execute([':id_certificado' => (int)$row['id']]);
    $atividadesLista = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Throwable $e) {
    // Se ocorrer erro, continua sem atividades
}

// Buscar assinatura adicional se habilitada
$assinaturaAdicional = null;
if ($hasAssinaturaAdicional) {
    try {
        $stmt = db()->prepare(
            'SELECT nome_instrutor, funcao, numero_registro 
             FROM assinatura_adicional 
             WHERE id_certificado = :id_certificado LIMIT 1'
        );
        $stmt->execute([':id_certificado' => (int)$row['id']]);
        $assinaturaAdicional = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (Throwable $e) {
        // Se ocorrer erro, continua sem assinatura adicional
    }
}

$ass1Nome = (string)($row['assinatura_1_nome'] ?? '');
$ass1Cargo = (string)($row['assinatura_1_cargo'] ?? '');
$ass2Nome = (string)($row['assinatura_2_nome'] ?? '');
$ass2Cargo = (string)($row['assinatura_2_cargo'] ?? '');
$logoPath = realpath(__DIR__ . '/assets/logo-instituto-cuidar-bem-white.png')
    ?: realpath(__DIR__ . '/assets/logo-instituto-cuidar-bem.png');
$logoSrc = fileToDataUri($logoPath ?: null);
$sidebarSrc = fileToDataUri(realpath(__DIR__ . '/assets/sidebar_gradient.png') ?: null);
$page2BgSrc = fileToDataUri(realpath(__DIR__ . '/assets/page2_bg_gradient.png') ?: null);

$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
    . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
$scriptName = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? '/Cuidado-Integral-Api/public/certificado.php'));
$publicDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
$appBasePath = preg_replace('#/public$#', '', $publicDir) ?: '';
$validarUrl = $baseUrl . $appBasePath . '/validar.php?h=' . rawurlencode($hash);

// Dados para o template do certificado (novo modelo)
$instrutorNome = (string)($row['instrutor_nome'] ?? '');
$instrutorFuncao = (string)($row['instrutor_funcao'] ?? '');
$instrutorRegistro = (string)($row['instrutor_registro'] ?? '');

if ($hasAssinaturaAdicional && $assinaturaAdicional) {
    $instrutorNome = (string)($assinaturaAdicional['nome_instrutor'] ?? '');
    $instrutorFuncao = (string)($assinaturaAdicional['funcao'] ?? '');
    $instrutorRegistro = (string)($assinaturaAdicional['numero_registro'] ?? '');
}

$cidadeExibicao = trim((string)preg_replace('/\s*\/\s*[A-Z]{2}$/u', '', $cidadeUf));
if ($cidadeExibicao === '') {
    $cidadeExibicao = $cidadeUf;
}

$cargaExibicao = trim($carga);
if ($cargaExibicao !== '' && !preg_match('/hora/i', $cargaExibicao)) {
    $cargaExibicao .= ' horas';
}

$detailsParts = [];
if ($desc !== '') {
    $detailsParts[] = '<div style="font-size: ' . CertificatePdfTemplate::pt(11.0) . '; line-height: 1.55; color: #5a5a5a;">' . nl2br(e($desc)) . '</div>';
}
if (!empty($atividadesLista)) {
    $detailsParts[] = CertificatePdfTemplate::buildListHtml($atividadesLista);
}
if ($detailsParts === []) {
    $detailsParts[] = '<div style="font-size: ' . CertificatePdfTemplate::pt(10.5) . '; color: #666666;">Nenhuma atividade vinculada a este certificado.</div>';
}

$detailsHtml = implode('', $detailsParts);

$html1 = CertificatePdfTemplate::buildFrontPageHtml([
    'nome' => $nome,
    'funcao' => $funcao,
    'horas' => $cargaExibicao,
    'cidade' => $cidadeExibicao,
    'data_emissao' => $dataExtenso,
    'logo_src' => $logoSrc,
    'sidebar_src' => $sidebarSrc,
    'validar_url' => $validarUrl,
    'instrutor_nome' => $instrutorNome,
    'instrutor_cargo' => $instrutorFuncao,
    'mostrar_instrutor' => $hasAssinaturaAdicional === 1,
    'assinante_nome' => $ass1Nome !== '' ? $ass1Nome : 'Yuri Rocha de Jesus',
    'assinante_cargo' => strtoupper($ass1Cargo !== '' ? $ass1Cargo : 'VICE-PRESIDENTE EXECUTIVO'),
]);

$html2 = CertificatePdfTemplate::buildDetailsPageHtml([
    'background_src' => $page2BgSrc,
    'content_html' => $detailsHtml,
    'footer_contato' => 'contato@institutocuidarbem.com.br | +55 21 99777-9584',
]);

try {
    $mpdf = CertificatePdfTemplate::createMpdf();

    $mpdf->SetTitle('Certificado - ' . $nome);
    $mpdf->WriteHTML($html1);
    $mpdf->AddPage();
    $mpdf->WriteHTML($html2);

    $filename = 'certificado-' . $codigo . '.pdf';
    $mpdf->Output($filename, \Mpdf\Output\Destination::INLINE);
} catch (Throwable $t) {
    header('Content-Type: text/plain; charset=utf-8');
    http_response_code(500);
    echo "Falha ao gerar PDF com mPDF.\n";
    echo "Erro: " . $t->getMessage() . "\n";
    echo "Dica: o mPDF exige ext-gd (instale php-gd e rode composer update).\n";
}
