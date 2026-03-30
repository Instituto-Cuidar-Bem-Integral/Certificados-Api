<?php
// Página de validação de certificados (acessada via QR Code)
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/conexao.php';

use App\Certificate\CertificateRepository;

$hash = isset($_GET['hash']) ? trim((string)$_GET['hash']) : '';
$certificate = null;
$error = null;
$isValid = false;

if ($hash !== '') {
    try {
        $repo = new CertificateRepository(db());
        $certificate = $repo->findByHash($hash);
        $isValid = $certificate !== null;
    } catch (Throwable $t) {
        $error = 'Erro ao buscar certificado: ' . $t->getMessage();
    }
}

function formatDate(DateTimeInterface $date): string
{
    return $date->format('d/m/Y');
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

function e(string $v): string
{
    return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Autenticidade - Instituto Cuidar Bem</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --blue-primary: #4d90e6;
            --blue-dark: #1d5f93;
            --green-primary: #6c9b43;
            --green-light: #80ad5a;
            --white: #ffffff;
            --gray-light: #f5f5f5;
            --gray-medium: #e0e0e0;
            --gray-dark: #666666;
            --black: #111111;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: var(--gray-light);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: var(--black);
        }

        /* Header */
        .header {
            background: var(--white);
            padding: 30px 20px;
            text-align: center;
            border-bottom: 3px solid var(--blue-primary);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header-logo {
            max-width: 120px;
            height: auto;
            margin-bottom: 15px;
        }

        .header-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--blue-primary);
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .header-subtitle {
            font-size: 0.9rem;
            color: var(--gray-dark);
            font-weight: 500;
        }

        /* Main Content */
        .main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .container {
            background: var(--white);
            border-radius: 20px;
            padding: 50px 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        .page-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--black);
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .page-subtitle {
            font-size: 0.95rem;
            color: var(--gray-dark);
            margin-bottom: 40px;
            line-height: 1.6;
        }

        /* QR Code Container */
        .qr-container {
            margin: 30px 0;
        }

        .qr-image {
            width: 250px;
            height: 250px;
            margin: 0 auto 20px;
            border: 4px solid var(--blue-primary);
            border-radius: 15px;
            padding: 15px;
            background: var(--white);
            box-shadow: 0 5px 20px rgba(77, 144, 230, 0.2);
        }

        .qr-image canvas,
        .qr-image img {
            width: 100%;
            height: 100%;
        }

        .qr-label {
            font-size: 0.9rem;
            color: var(--gray-dark);
            font-weight: 500;
        }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.1rem;
            margin: 20px 0;
        }

        .status-valid {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border: 2px solid #80ad5a;
        }

        .status-invalid {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border: 2px solid #dc3545;
        }

        .status-icon {
            font-size: 1.5rem;
        }

        /* Certificate Info */
        .cert-info {
            margin: 30px 0;
            text-align: left;
        }

        .cert-info-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--blue-primary);
            margin-bottom: 20px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid var(--gray-medium);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--gray-dark);
        }

        .info-value {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--black);
            text-align: right;
            max-width: 60%;
        }

        /* Hash Display */
        .hash-display {
            background: var(--gray-light);
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            font-size: 0.75rem;
            word-break: break-all;
            color: var(--gray-dark);
            border: 1px solid var(--gray-medium);
        }

        .hash-label {
            font-family: 'Montserrat', sans-serif;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--black);
            margin-bottom: 8px;
            display: block;
        }

        /* Button */
        .btn-verify {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: var(--blue-primary);
            color: var(--white);
            border: none;
            padding: 18px 40px;
            font-size: 1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            margin-top: 20px;
            box-shadow: 0 5px 20px rgba(77, 144, 230, 0.4);
        }

        .btn-verify:hover {
            background: var(--blue-dark);
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(77, 144, 230, 0.5);
        }

        /* Error State */
        .error-container {
            text-align: center;
        }

        .error-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }

        .error-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #721c24;
            margin-bottom: 10px;
        }

        .error-text {
            font-size: 0.95rem;
            color: var(--gray-dark);
            line-height: 1.6;
        }

        /* Footer */
        .footer {
            background: var(--blue-dark);
            color: var(--white);
            padding: 30px 20px;
            text-align: center;
        }

        .footer-title {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .footer-text {
            font-size: 0.85rem;
            opacity: 0.8;
            line-height: 1.6;
        }

        .footer-copy {
            font-size: 0.75rem;
            opacity: 0.6;
            margin-top: 15px;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .container {
                padding: 30px 20px;
            }

            .page-title {
                font-size: 1.3rem;
            }

            .qr-image {
                width: 200px;
                height: 200px;
            }

            .info-row {
                flex-direction: column;
                gap: 5px;
            }

            .info-value {
                text-align: left;
                max-width: 100%;
            }

            .btn-verify {
                padding: 15px 30px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <img src="public/assets/logo-instituto-cuidar-bem.png" alt="Logo Instituto Cuidar Bem" class="header-logo">
        <h1 class="header-title">VERIFICAR AUTENTICIDADE</h1>
        <p class="header-subtitle">Este QR Code foi gerado em conformidade com o</p>
    </header>

    <!-- Main Content -->
    <main class="main">
        <div class="container">
            <?php if ($isValid && $certificate): ?>
                <!-- Certificate Valid -->
                <h2 class="page-title">Certificado Autêntico</h2>
                <p class="page-subtitle">INSTITUTO CUIDAR BEM - INTEGRAL</p>

                <div class="status-badge status-valid">
                    <span class="status-icon">✅</span>
                    <span>Certificado Válido</span>
                </div>

                <!-- QR Code -->
                <div class="qr-container">
                    <div class="qr-image" id="qrcode"></div>
                    <p class="qr-label">Escaneie o código acima</p>
                </div>

                <!-- Certificate Info -->
                <div class="cert-info">
                    <h3 class="cert-info-title">Detalhes do Certificado</h3>
                    
                    <div class="info-row">
                        <span class="info-label">Nome do Aluno</span>
                        <span class="info-value"><?php echo e($certificate->nome); ?></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Atividade</span>
                        <span class="info-value"><?php echo e($certificate->atividade ?? '-'); ?></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Carga Horária</span>
                        <span class="info-value"><?php echo $certificate->cargaHoraria ? e($certificate->cargaHoraria) . ' horas' : '-'; ?></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Data de Emissão</span>
                        <span class="info-value"><?php echo formatDate($certificate->dataEmissao); ?></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Instrutor</span>
                        <span class="info-value"><?php echo e($certificate->instrutor ?? '-'); ?></span>
                    </div>
                </div>

                <!-- Hash -->
                <div class="hash-display">
                    <span class="hash-label">Hash do Certificado:</span>
                    <?php echo e($certificate->hash); ?>
                </div>

                <a href="#" class="btn-verify" onclick="window.print(); return false;">
                    🖨️ Imprimir Comprovante
                </a>

            <?php else: ?>
                <!-- Certificate Invalid or Not Found -->
                <div class="error-container">
                    <div class="error-icon">❌</div>
                    <h2 class="error-title">Certificado Não Encontrado</h2>
                    <p class="error-text">
                        <?php if ($error): ?>
                            <?php echo e($error); ?>
                        <?php elseif ($hash === ''): ?>
                            Nenhum código de verificação foi informado.<br>
                            Por favor, escaneie o QR Code do certificado.
                        <?php else: ?>
                            O certificado informado não foi encontrado em nossa base de dados.<br>
                            Verifique se o código está correto e tente novamente.
                        <?php endif; ?>
                    </p>

                    <?php if ($hash !== ''): ?>
                        <div class="hash-display">
                            <span class="hash-label">Hash Consultado:</span>
                            <?php echo e($hash); ?>
                        </div>
                    <?php endif; ?>

                    <a href="https://institutocuidarbem.com.br" class="btn-verify">
                        🏠 Visitar Site
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p class="footer-title">INSTITUTO CUIDAR BEM - INTEGRAL</p>
        <p class="footer-text">
            contato@institutocuidarbem.com.br | +55 21 99777-9584
        </p>
        <p class="footer-copy">© 2026 Instituto Cuidar Bem - Todos os direitos reservados</p>
    </footer>

    <!-- QR Code Generator -->
    <?php if ($isValid && $certificate): ?>
    <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
    <script>
        // Generate QR Code
        var qr = qrcode(0, 'M');
        qr.addData(window.location.href);
        qr.make();
        document.getElementById('qrcode').innerHTML = qr.createImgTag(5, 10);
    </script>
    <?php endif; ?>
</body>
</html>