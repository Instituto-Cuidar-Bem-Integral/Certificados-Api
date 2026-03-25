<?php
declare(strict_types=1);

// Gera PDF do certificado via mPDF.
// Suporta modo mock: `?mock=1&h=<hash-do-mock>` (use hashes exibidos no listar mock).

$autoload = __DIR__ . '/../vendor/autoload.php';
if (!is_file($autoload)) {
    header('Content-Type: text/plain; charset=utf-8');
    http_response_code(500);
    echo "Dependências não instaladas. Rode Composer em /certificados.\n";
    echo "Depois instale/extensão GD (mPDF exige): sudo apt install php8.3-gd\n";
    exit;
}
require $autoload;
require __DIR__ . '/../config/database.php';

use App\Certificate\CertificateRepository;

function e(string $v): string
{
    return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
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

function drawCertificateCorner(\Mpdf\Mpdf $mpdf, float $x, float $y, float $dirX, float $dirY): void
{
    $outerLength = 11.0;
    $innerOffset = 3.6;
    $innerLength = 7.2;
    $markerSize = 3.4;
    $markerInset = 0.9;
    $markerCore = 1.4;

    $mpdf->SetLineWidth(0.7);
    $mpdf->Line($x, $y, $x + ($outerLength * $dirX), $y);
    $mpdf->Line($x, $y, $x, $y + ($outerLength * $dirY));

    $innerX = $x + ($innerOffset * $dirX);
    $innerY = $y + ($innerOffset * $dirY);

    $mpdf->SetLineWidth(0.22);
    $mpdf->Line($innerX, $innerY, $innerX + ($innerLength * $dirX), $innerY);
    $mpdf->Line($innerX, $innerY, $innerX, $innerY + ($innerLength * $dirY));

    $markerX = $dirX > 0 ? $x : $x - $markerSize;
    $markerY = $dirY > 0 ? $y : $y - $markerSize;

    $mpdf->SetFillColor(29, 95, 147);
    $mpdf->Rect($markerX, $markerY, $markerSize, $markerSize, 'F');
    $mpdf->SetFillColor(255, 255, 255);
    $mpdf->Rect($markerX + $markerInset, $markerY + $markerInset, $markerCore, $markerCore, 'F');
}

function drawCertificateBorder(\Mpdf\Mpdf $mpdf): void
{
    $pageWidth = CertificatePdfTemplate::PAGE_WIDTH_MM;
    $pageHeight = CertificatePdfTemplate::PAGE_HEIGHT_MM;
    $outerInset = 8.0;
    $innerInset = 11.8;
    $blue = [29, 95, 147];

    $mpdf->SetDrawColor($blue[0], $blue[1], $blue[2]);
    $mpdf->SetFillColor($blue[0], $blue[1], $blue[2]);

    $mpdf->SetLineWidth(0.7);
    $mpdf->Rect($outerInset, $outerInset, $pageWidth - ($outerInset * 2), $pageHeight - ($outerInset * 2));

    $mpdf->SetLineWidth(0.22);
    $mpdf->Rect($innerInset, $innerInset, $pageWidth - ($innerInset * 2), $pageHeight - ($innerInset * 2));

    drawCertificateCorner($mpdf, 5.8, 5.8, 1.0, 1.0);
    drawCertificateCorner($mpdf, $pageWidth - 5.8, 5.8, -1.0, 1.0);
    drawCertificateCorner($mpdf, 5.8, $pageHeight - 5.8, 1.0, -1.0);
    drawCertificateCorner($mpdf, $pageWidth - 5.8, $pageHeight - 5.8, -1.0, -1.0);
}

final class CertificatePdfTemplate
{
    public const PAGE_WIDTH_MM = 297.0;
    public const PAGE_HEIGHT_MM = 210.0;

    public static function createMpdf(): \Mpdf\Mpdf
    {
        return new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
        ]);
    }

    public static function buildFrontPageHtml(array $data): string
    {
        $mm = static fn(float $value): string => self::mm($value);

        $blue = '#4d90e6';
        $green = '#6c9b43';

        $padding = $mm(9.0) . ' ' . $mm(16.0) . ' ' . $mm(10.0);
        $logoWidth = $mm(27.0);
        $logoBottom = $mm(1.6);
        $institutionFont = $mm(3.5);
        $institutionSpacing = $mm(0.08);
        $titleFont = $mm(14.8);
        $titleTop = $mm(3.4);
        $titleDividerWidth = $mm(162.0);
        $titleDividerBorder = $mm(0.4) . ' solid ' . $green;
        $titleDividerMargin = $mm(1.1) . ' auto ' . $mm(1.2);
        $subtitleFont = $mm(4.35);
        $nameFont = $mm(8.3);
        $nameBottom = $mm(0.6);
        $nameDividerWidth = $mm(124.0);
        $nameDividerMargin = $mm(1.2) . ' auto ' . $mm(3.0);
        $bodyFont = $mm(4.65);
        $bodyLine = $mm(6.6);
        $bodySide = $mm(6.0);
        $bodyBottom = $mm(4.0);
        $dateDividerWidth = $mm(122.0);
        $dateDividerBorder = $mm(0.4) . ' solid ' . $green;
        $dateDividerBottom = $mm(2.6);
        $dateFont = $mm(4.7);
        $dateBottom = $mm(15.0);
        $signatureLine = $mm(0.28) . ' solid #80ad5a';
        $signatureMargin = '0 ' . $mm(9.0) . ' ' . $mm(1.6);
        $signatureNameFont = $mm(5.1);
        $signatureCargoFont = $mm(4.0);

        $nome = e((string)$data['nome']);
        $cidadeUf = e((string)$data['cidade_uf']);
        $dataExtenso = e((string)$data['data_extenso']);
        $atividade = trim((string)($data['atividade'] ?? ''));
        $cargaHoraria = trim((string)($data['carga_horaria'] ?? ''));
        $periodoInicio = trim((string)($data['periodo_inicio_extenso'] ?? ''));
        $periodoFim = trim((string)($data['periodo_fim_extenso'] ?? ''));
        $assinatura1Nome = e((string)$data['assinatura_1_nome']);
        $assinatura1Cargo = e((string)$data['assinatura_1_cargo']);
        $assinatura2Nome = e((string)$data['assinatura_2_nome']);
        $assinatura2Cargo = e((string)$data['assinatura_2_cargo']);
        $instituicao = e((string)$data['instituicao']);
        $logoSrc = trim((string)($data['logo_src'] ?? ''));

        $logoHtml = $logoSrc !== ''
            ? '<img style="width: ' . $logoWidth . '; height: auto; display: block; margin: 0 auto ' . $logoBottom . ';" src="' . e($logoSrc) . '" alt="Logo Instituto Cuidar Bem">'
            : '';

        $bodyParts = ['Atuou como voluntária'];
        if ($atividade !== '') {
            $bodyParts[] = 'na função de <span style="color: ' . $blue . '; font-weight: 700;">' . e($atividade) . '</span>';
        }
        if ($cargaHoraria !== '') {
            $bodyParts[] = 'com carga horária total de <span style="color: ' . $blue . '; font-weight: 700;">' . e($cargaHoraria) . ' horas</span>';
        }
        if ($periodoInicio !== '' && $periodoFim !== '') {
            $bodyParts[] = 'no período de <span style="color: ' . $blue . '; font-weight: 700;">' . e($periodoInicio) . '</span> a <span style="color: ' . $blue . '; font-weight: 700;">' . e($periodoFim) . '</span>';
        }

        $bodyHtml = implode(', ', $bodyParts) . ', contribuindo com as atividades institucionais do Instituto Cuidar Bem - Integral.';

        return <<<HTML
<div style="font-family: serif; color: #111; padding: {$padding}; box-sizing: border-box;">
    <div style="text-align: center;">
        {$logoHtml}
        <div style="font-size: {$institutionFont}; font-weight: 700; color: {$blue}; letter-spacing: {$institutionSpacing};">{$instituicao}</div>
    </div>
    <div style="text-align: center; font-size: {$titleFont}; line-height: 1; margin-top: {$titleTop};">CERTIFICADO</div>
    <div style="width: {$titleDividerWidth}; border-top: {$titleDividerBorder}; margin: {$titleDividerMargin};"></div>
    <div style="text-align: center; font-family: sans-serif; font-size: {$subtitleFont}; font-weight: 700;">Certificamos que</div>
    <div style="text-align: center; font-size: {$nameFont}; font-weight: 700; color: {$blue}; margin-bottom: {$nameBottom};">{$nome}</div>
    <div style="width: {$nameDividerWidth}; border-top: {$titleDividerBorder}; margin: {$nameDividerMargin};"></div>
    <div style="font-size: {$bodyFont}; line-height: {$bodyLine}; text-align: center; margin: 0 {$bodySide} {$bodyBottom};">{$bodyHtml}</div>
    <table align="center" style="width: {$dateDividerWidth}; border-collapse: collapse; margin: 0 auto {$dateDividerBottom};">
        <tr>
            <td style="width: 49%; border-top: {$dateDividerBorder};"></td>
            <td style="width: 2%; text-align: center; color: {$green}; font-size: {$mm(4.1)}; line-height: 0;">&#8226;</td>
            <td style="width: 49%; border-top: {$dateDividerBorder};"></td>
        </tr>
    </table>
    <div style="text-align: center; font-size: {$dateFont}; margin-bottom: {$dateBottom};">{$cidadeUf}, {$dataExtenso}</div>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 39%; text-align: center; vertical-align: top;">
                <div style="border-top: {$signatureLine}; margin: {$signatureMargin};"></div>
                <div style="font-size: {$signatureNameFont}; font-weight: 700;">{$assinatura2Nome}</div>
                <div style="font-size: {$signatureCargoFont};">{$assinatura2Cargo}</div>
            </td>
            <td style="width: 22%;"></td>
            <td style="width: 39%; text-align: center; vertical-align: top;">
                <div style="border-top: {$signatureLine}; margin: {$signatureMargin};"></div>
                <div style="font-size: {$signatureNameFont}; font-weight: 700;">{$assinatura1Nome}</div>
                <div style="font-size: {$signatureCargoFont};">{$assinatura1Cargo}</div>
            </td>
        </tr>
    </table>
</div>
HTML;
    }

    public static function buildDetailsPageHtml(array $data): string
    {
        $mm = static fn(float $value): string => self::mm($value);

        $atividadesHtml = self::buildListHtml(
            $data['atividades_lista'] ?? [],
            [
                'Apoiar as rotinas do setor',
                'Registrar atividades realizadas',
                'Dar suporte em processos de recrutamento',
                'Elaborar relatórios simples',
                'Apoiar demandas administrativas do setor',
            ]
        );
        $competenciasHtml = self::buildListHtml(
            $data['competencias_lista'] ?? [],
            [
                'Organização e atenção aos detalhes',
                'Comunicação interpessoal',
                'Trabalho em equipe',
                'Responsabilidade e comprometimento',
                'Conhecimento básico de rotinas administrativas de RH',
            ]
        );

        $padding = $mm(10.0) . ' ' . $mm(14.0) . ' ' . $mm(4.0);
        $titleMargin = $mm(2.2) . ' 0 ' . $mm(5.4);
        $descriptionFont = $mm(5.1);
        $descriptionLine = $mm(7.4);
        $descriptionMargin = '0 ' . $mm(4.3) . ' ' . $mm(8.6);
        $contentLeft = $mm(2.0);
        $labelGap = $mm(3.5);
        $sectionGap = $mm(6.0);
        $footerGap = $mm(20.0);
        $footerLine = $mm(0.35) . ' solid #666';
        $footerMargin = '0 ' . $mm(4.0) . ' 0';
        $footerPaddingTop = $mm(2.4);
        $footerFont = $mm(3.35);

        $descricaoPill = self::buildBluePill('DESCRIÇÃO', 6.5, 10.6, true);
        $atividadesPill = self::buildBluePill('ATIVIDADES', 5.4, 7.1);
        $competenciasPill = self::buildBluePill('COMPETÊNCIAS', 5.4, 7.1);
        $descricao = e((string)$data['descricao']);
        $footerContato = e((string)$data['footer_contato']);

        return <<<HTML
<div style="font-family: serif; color: #111; padding: {$padding}; box-sizing: border-box;">
    <div style="text-align: center; margin: {$titleMargin};">{$descricaoPill}</div>
    <div style="font-size: {$descriptionFont}; line-height: {$descriptionLine}; text-align: center; margin: {$descriptionMargin};">{$descricao}</div>
    <div style="width: 58%; margin-left: {$contentLeft};">
        <div style="margin-bottom: {$labelGap};">{$atividadesPill}</div>
        {$atividadesHtml}
        <div style="height: {$sectionGap};"></div>
        <div style="margin-bottom: {$labelGap};">{$competenciasPill}</div>
        {$competenciasHtml}
    </div>
    <div style="height: {$footerGap};"></div>
    <div style="border-top: {$footerLine}; margin: {$footerMargin}; padding-top: {$footerPaddingTop}; text-align: center; font-family: sans-serif; font-size: {$footerFont}; font-weight: 700;">{$footerContato}</div>
</div>
HTML;
    }

    private static function buildListHtml(array $items, array $fallbackItems): string
    {
        $mm = static fn(float $value): string => self::mm($value);
        $list = $items !== [] ? $items : $fallbackItems;
        $paddingLeft = $mm(5.6);
        $fontSize = $mm(4.9);
        $lineHeight = $mm(6.8);
        $itemMargin = $mm(1.5);

        $itemsHtml = implode('', array_map(
            static fn($item): string => '<li style="margin: 0 0 ' . $itemMargin . ';">' . e((string)$item) . '</li>',
            $list
        ));

        return <<<HTML
<ul style="margin: 0; padding-left: {$paddingLeft}; font-size: {$fontSize}; line-height: {$lineHeight};">{$itemsHtml}</ul>
HTML;
    }

    private static function buildBluePill(string $text, float $fontSizeMm, float $paddingXmm, bool $center = false): string
    {
        $mm = static fn(float $value): string => self::mm($value);
        $align = $center ? ' align="center"' : '';
        $fontSize = $mm($fontSizeMm);
        $padding = $mm(1.1) . ' ' . $mm($paddingXmm);
        $radius = $mm(4.0);
        $label = e($text);

        return <<<HTML
<table{$align} style="border-collapse: separate;">
    <tr>
        <td style="font-family: serif; font-size: {$fontSize}; font-weight: 700; color: #fff; background: #4d90e6; padding: {$padding}; border-radius: {$radius};">{$label}</td>
    </tr>
</table>
HTML;
    }

    private static function mm(float $value): string
    {
        return rtrim(rtrim(sprintf('%.2F', $value), '0'), '.') . 'mm';
    }
}

$hash = isset($_GET['h']) ? trim((string)$_GET['h']) : '';
$useMock = isset($_GET['mock']) && (string)$_GET['mock'] === '1';

$row = null;
$loadError = null;

if ($useMock) {
    require_once __DIR__ . '/../mocks/certificados.php';
    $row = mock_certificado_by_hash($hash);
} else {
    try {
        $repo = new CertificateRepository(db());
        $dto = $repo->findByHash($hash);
        if ($dto) {
            $row = [
                'id' => $dto->id,
                'hash' => $dto->hash,
                'nome' => $dto->nome,
                'curso' => $dto->curso,
                'data_emissao' => $dto->dataEmissao->format('Y-m-d'),
                'carga_horaria' => $dto->cargaHoraria,
                'atividade' => $dto->atividade,
                'instrutor' => $dto->instrutor,
                'criado_em' => $dto->criadoEm->format('Y-m-d H:i:s'),
                // Campos “estendidos” podem ser preenchidos depois no banco.
                'descricao' => null,
                'atividades_lista' => [],
                'competencias_lista' => [],
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
        $loadError = $t->getMessage();
        require_once __DIR__ . '/../mocks/certificados.php';
        $row = mock_certificado_by_hash($hash);
        $useMock = $row !== null;
    }
}

if (!$row) {
    header('Content-Type: text/plain; charset=utf-8');
    http_response_code(404);
    echo "Certificado não encontrado.\n";
    if ($loadError) {
        echo "Erro no banco: {$loadError}\n";
        echo "Tente: ?mock=1&h=<hash-do-mock>\n";
    }
    exit;
}

$dataEmissao = new DateTimeImmutable((string)$row['data_emissao']);
$dataFmt = $dataEmissao->format('d/m/Y');
$dataExtenso = formatDateLongPtBr($dataEmissao);
$codigo = substr((string)$row['hash'], 0, 12);

$nome = (string)$row['nome'];
$curso = (string)($row['curso'] ?? '');
$atividade = (string)($row['atividade'] ?? '');
$carga = (string)($row['carga_horaria'] ?? '');
$instrutor = (string)($row['instrutor'] ?? '');
$instituicao = (string)($row['instituicao'] ?? 'INSTITUTO CUIDAR BEM - INTEGRAL');
$cidadeUf = (string)($row['cidade_uf'] ?? '');
$desc = (string)($row['descricao'] ?? '');
$atividadesLista = is_array($row['atividades_lista'] ?? null) ? $row['atividades_lista'] : [];
$competenciasLista = is_array($row['competencias_lista'] ?? null) ? $row['competencias_lista'] : [];
$periodoInicioExtenso = !empty($row['periodo_inicio'])
    ? formatDateLongPtBr(new DateTimeImmutable((string)$row['periodo_inicio']))
    : '';
$periodoFimExtenso = !empty($row['periodo_fim'])
    ? formatDateLongPtBr(new DateTimeImmutable((string)$row['periodo_fim']))
    : '';

$ass1Nome = (string)($row['assinatura_1_nome'] ?? '');
$ass1Cargo = (string)($row['assinatura_1_cargo'] ?? '');
$ass2Nome = (string)($row['assinatura_2_nome'] ?? '');
$ass2Cargo = (string)($row['assinatura_2_cargo'] ?? '');
$logoPath = realpath(__DIR__ . '/assets/logo-instituto-cuidar-bem.png');
$logoSrc = $logoPath ? str_replace(DIRECTORY_SEPARATOR, '/', $logoPath) : '';

$html1 = CertificatePdfTemplate::buildFrontPageHtml([
    'nome' => $nome,
    'atividade' => $atividade,
    'carga_horaria' => $carga,
    'cidade_uf' => $cidadeUf,
    'data_fmt' => $dataFmt,
    'data_extenso' => $dataExtenso,
    'periodo_inicio_extenso' => $periodoInicioExtenso,
    'periodo_fim_extenso' => $periodoFimExtenso,
    'assinatura_1_nome' => $ass1Nome,
    'assinatura_1_cargo' => $ass1Cargo,
    'assinatura_2_nome' => $ass2Nome,
    'assinatura_2_cargo' => $ass2Cargo,
    'instituicao' => $instituicao,
    'codigo' => $codigo,
    'logo_src' => $logoSrc,
]);

$html2 = CertificatePdfTemplate::buildDetailsPageHtml([
    'descricao' => $desc !== '' ? $desc : 'Descrição (mock): atividades e responsabilidades desempenhadas.',
    'atividades_lista' => $atividadesLista,
    'competencias_lista' => $competenciasLista,
    'footer_contato' => 'contato@institutocuidarbem.com.br | +55 21 99777-9584',
]);

try {
    $mpdf = CertificatePdfTemplate::createMpdf();

    $mpdf->SetTitle('Certificado - ' . $nome);
    $mpdf->WriteHTML($html1);
    drawCertificateBorder($mpdf);
    $mpdf->AddPage('L');
    $mpdf->WriteHTML($html2);
    drawCertificateBorder($mpdf);

    $filename = 'certificado-' . $codigo . '.pdf';
    $mpdf->Output($filename, \Mpdf\Output\Destination::INLINE);
} catch (Throwable $t) {
    header('Content-Type: text/plain; charset=utf-8');
    http_response_code(500);
    echo "Falha ao gerar PDF com mPDF.\n";
    echo "Erro: " . $t->getMessage() . "\n";
    echo "Dica: o mPDF exige ext-gd (instale php-gd e rode composer update).\n";
}
