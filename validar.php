<?php
// Página de validação de certificados
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/conexao.php';

use App\Certificate\CertificateRepository;

$hash = isset($_GET['h']) ? trim((string)$_GET['h']) : '';
$certificate = null;
$error = null;
$isValid = false;
$institutionName = 'Instituto Cuidar Bem - Integral';
$institutionResponsible = 'Vitor de Souza Silva';
$institutionContact = 'contato@institutocuidarbem.com.br | institutocuidarbem.com.br';

// Debug: Log do hash recebido
error_log("=== DEBUG VALIDAR ===");
error_log("Hash recebido: " . $hash);

if ($hash !== '') {
    try {
        // Debug: Verificar conexão com banco
        error_log("Tentando conectar ao banco...");
        $pdo = db();
        error_log("Conexão estabelecida!");
        
        $repo = new CertificateRepository($pdo);
        
        // Debug: Verificar se a tabela existe
        $stmt = $pdo->query("SHOW TABLES LIKE 'certificados'");
        $tables = $stmt->fetchAll();
        error_log("Tabelas encontradas: " . json_encode($tables));
        
        // Debug: Listar todos os certificados
        $stmt = $pdo->query("SELECT id, hash, nome FROM certificados LIMIT 5");
        $allCerts = $stmt->fetchAll();
        error_log("Certificados no banco: " . json_encode($allCerts));
        
        // Debug: Buscar certificado específico
        error_log("Buscando certificado com hash: " . $hash);
        $certificate = $repo->findByHash($hash);
        
        if ($certificate) {
            error_log("✅ Certificado encontrado: " . $certificate->nome);
            $isValid = true;
        } else {
            error_log("❌ Certificado NÃO encontrado para hash: " . $hash);
        }
    } catch (Throwable $t) {
        $error = 'Erro ao buscar certificado: ' . $t->getMessage();
        error_log("❌ ERRO: " . $t->getMessage());
        error_log("Stack trace: " . $t->getTraceAsString());
    }
} else {
    error_log("Hash vazio!");
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
            line-height: 1.6;
        }

        /* Main Content */
        .main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 48px 20px;
        }

        .container {
            background: var(--white);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 18px 50px rgba(29, 95, 147, 0.12);
            max-width: 760px;
            width: 100%;
            border: 1px solid rgba(77, 144, 230, 0.12);
        }

        .page-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--black);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .page-subtitle {
            font-size: 0.95rem;
            color: var(--gray-dark);
            margin-bottom: 28px;
            line-height: 1.6;
        }

        .hero {
            text-align: center;
            margin-bottom: 28px;
        }

        .summary-text {
            font-size: 0.9rem;
            color: var(--gray-dark);
            line-height: 1.7;
            max-width: 560px;
            margin: 0 auto;
        }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 22px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1rem;
            margin: 0 0 18px;
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
            margin: 0;
        }

        .cert-info-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--blue-primary);
            margin-bottom: 18px;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .info-card {
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            border: 1px solid rgba(77, 144, 230, 0.16);
            border-radius: 16px;
            padding: 18px;
        }

        .info-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--gray-dark);
            margin-bottom: 8px;
        }

        .info-value {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--black);
            line-height: 1.5;
            overflow-wrap: anywhere;
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 14px;
            flex-wrap: wrap;
            margin-top: 28px;
        }

        .btn-secondary {
            background: transparent;
            color: var(--blue-dark);
            border: 1px solid rgba(29, 95, 147, 0.24);
            box-shadow: none;
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
            padding: 16px 28px;
            font-size: 0.95rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 5px 20px rgba(77, 144, 230, 0.4);
        }

        .btn-verify:hover {
            background: var(--blue-dark);
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(77, 144, 230, 0.5);
        }

        .btn-secondary:hover {
            background: rgba(77, 144, 230, 0.08);
            color: var(--blue-dark);
            box-shadow: none;
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
                padding: 28px 20px;
            }

            .page-title {
                font-size: 1.3rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .btn-verify {
                width: 100%;
                padding: 15px 24px;
                font-size: 0.9rem;
            }

            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <img src="public/assets/logo-instituto-cuidar-bem.png" alt="Logo Instituto Cuidar Bem" class="header-logo">
        <h1 class="header-title">VERIFICAR AUTENTICIDADE</h1>
        <p class="header-subtitle">Confirmação oficial dos dados do certificado emitido pelo Instituto Cuidar Bem.</p>
    </header>

    <!-- Main Content -->
    <main class="main">
        <div class="container">
            <?php if ($isValid && $certificate): ?>
                <!-- Certificate Valid -->
                <div class="hero">
                    <div class="status-badge status-valid">
                        <span class="status-icon">✅</span>
                        <span>Certificado Válido</span>
                    </div>
                    <h2 class="page-title">Certificado Autêntico</h2>
                    <p class="page-subtitle"><?php echo e($institutionName); ?></p>
                    <p class="summary-text">Os dados abaixo foram localizados em nossa base e confirmam a autenticidade deste certificado.</p>
                </div>

                <!-- Certificate Info -->
                <div class="cert-info">
                    <h3 class="cert-info-title">Detalhes do Certificado</h3>
                    <div class="info-grid">
                        <div class="info-card">
                            <span class="info-label">Nome</span>
                            <span class="info-value"><?php echo e($certificate->nome); ?></span>
                        </div>

                        <div class="info-card">
                            <span class="info-label">Função</span>
                            <span class="info-value"><?php echo e($certificate->funcao ?? '-'); ?></span>
                        </div>

                        <div class="info-card">
                            <span class="info-label">Nome da Instituição</span>
                            <span class="info-value"><?php echo e($institutionName); ?></span>
                        </div>

                        <div class="info-card">
                            <span class="info-label">Nome do Responsável</span>
                            <span class="info-value"><?php echo e($institutionResponsible); ?></span>
                        </div>

                        <div class="info-card">
                            <span class="info-label">Contato</span>
                            <span class="info-value"><?php echo e($institutionContact); ?></span>
                        </div>

                        <div class="info-card">
                            <span class="info-label">Carga Horária</span>
                            <span class="info-value"><?php echo $certificate->cargaHoraria ? e($certificate->cargaHoraria) . ' horas' : '-'; ?></span>
                        </div>

                        <div class="info-card">
                            <span class="info-label">Data de Emissão</span>
                            <span class="info-value"><?php echo formatDate($certificate->dataEmissao); ?></span>
                        </div>
                    </div>
                </div>

                <div class="actions">
                    <a href="#" class="btn-verify" onclick="window.print(); return false;">
                        🖨️ Imprimir Comprovante
                    </a>
                    <a href="https://institutocuidarbem.com.br" class="btn-verify btn-secondary">
                        🌐 Visitar Site
                    </a>
                </div>

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
                            Use o link de validação completo para consultar este certificado.
                        <?php else: ?>
                            O certificado informado não foi encontrado em nossa base de dados.<br>
                            Verifique se o código está correto e tente novamente.
                        <?php endif; ?>
                    </p>
                    <div class="actions">
                        <a href="https://institutocuidarbem.com.br" class="btn-verify">
                            🏠 Visitar Site
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p class="footer-title"><?php echo e($institutionName); ?></p>
        <p class="footer-text">
            contato@institutocuidarbem.com.br | +55 21 99777-9584
        </p>
        <p class="footer-copy">© 2026 Instituto Cuidar Bem - Todos os direitos reservados</p>
    </footer>

</body>
</html>
