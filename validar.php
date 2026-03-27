<?php
// Página de validação de certificados (acessada via QR Code)
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/conexao.php';

use App\Certificate\CertificateRepository;

$hash = isset($_GET['hash']) ? trim((string)$_GET['hash']) : '';
$certificate = null;
$error = null;

if ($hash !== '') {
    try {
        $repo = new CertificateRepository(db());
        $certificate = $repo->findByHash($hash);
    } catch (Throwable $t) {
        $error = 'Erro ao buscar certificado: ' . $t->getMessage();
    }
}

function formatDate($dateString) {
    if (!$dateString) return '-';
    $date = new DateTimeImmutable($dateString);
    return $date->format('d/m/Y');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validar Certificado - Instituto Cuidar Bem</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        header h1 {
            color: white;
            font-size: 2rem;
            margin-bottom: 5px;
        }

        header p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1rem;
        }

        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 800px;
            width: 100%;
        }

        .page-title {
            color: #333;
            margin-bottom: 30px;
            font-size: 1.8rem;
            text-align: center;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }

        .search-container {
            margin-bottom: 30px;
        }

        .search-container label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .search-input-group {
            display: flex;
            gap: 10px;
        }

        .search-input-group input {
            flex: 1;
            padding: 15px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            font-family: monospace;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .search-input-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .search-input-group button {
            padding: 15px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .search-input-group button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .certificate-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            margin-top: 30px;
            display: none;
            border-left: 5px solid #667eea;
        }

        .certificate-card.show {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .certificate-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
        }

        .certificate-header h3 {
            color: #333;
            font-size: 1.5rem;
        }

        .status-badge {
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .status-valid {
            background: #d4edda;
            color: #155724;
        }

        .status-invalid {
            background: #f8d7da;
            color: #721c24;
        }

        .certificate-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-item {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .info-item label {
            display: block;
            color: #666;
            font-size: 0.85rem;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .info-item span {
            color: #333;
            font-size: 1rem;
        }

        .hash-display {
            background: #e9ecef;
            padding: 15px;
            border-radius: 10px;
            font-family: monospace;
            word-break: break-all;
            margin-top: 15px;
        }

        .hash-display strong {
            display: block;
            margin-bottom: 10px;
            color: #333;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            text-align: center;
            display: none;
        }

        .error-message.show {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        .info-message {
            background: #e7f3ff;
            color: #004085;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            text-align: center;
            display: none;
        }

        .info-message.show {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        .qr-code-container {
            text-align: center;
            margin-top: 20px;
        }

        .qr-code-container img {
            max-width: 200px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }

        footer {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 20px;
            text-align: center;
            color: rgba(255, 255, 255, 0.8);
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        @media (max-width: 768px) {
            .search-input-group {
                flex-direction: column;
            }

            .container {
                padding: 20px;
            }

            .certificate-info {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>✅ Validar Certificado</h1>
        <p>Instituto Cuidar Bem</p>
    </header>

    <main>
        <div class="container">
            <h2 class="page-title">🔍 Verificar Autenticidade</h2>
            
            <div class="search-container">
                <label for="hashInput">🔑 Hash do Certificado:</label>
                <div class="search-input-group">
                    <input type="text" id="hashInput" 
                           placeholder="Cole o hash do certificado aqui..."
                           value="<?php echo htmlspecialchars($hash); ?>">
                    <button onclick="validateCertificate()">🔍 Validar</button>
                </div>
            </div>

            <?php if ($certificate): ?>
                <div id="certificateCard" class="certificate-card show">
                    <div class="certificate-header">
                        <h3>📜 Certificado Encontrado</h3>
                        <span id="statusBadge" class="status-badge status-valid">✅ Válido</span>
                    </div>

                    <div class="certificate-info">
                        <div class="info-item">
                            <label>👤 Nome do Aluno</label>
                            <span><?php echo htmlspecialchars($certificate->nome); ?></span>
                        </div>

                        <div class="info-item">
                            <label>📚 Curso</label>
                            <span><?php echo htmlspecialchars($certificate->curso ?? '-'); ?></span>
                        </div>

                        <div class="info-item">
                            <label>⏱️ Carga Horária</label>
                            <span><?php echo $certificate->cargaHoraria ? htmlspecialchars($certificate->cargaHoraria) . ' horas' : '-'; ?></span>
                        </div>

                        <div class="info-item">
                            <label>📅 Data de Conclusão</label>
                            <span><?php echo formatDate($certificate->dataEmissao->format('Y-m-d')); ?></span>
                        </div>

                        <div class="info-item">
                            <label>👨‍🏫 Instrutor</label>
                            <span><?php echo htmlspecialchars($certificate->instrutor ?? '-'); ?></span>
                        </div>

                        <div class="info-item">
                            <label>📄 CPF</label>
                            <span><?php echo htmlspecialchars($certificate->atividade ?? '-'); ?></span>
                        </div>
                    </div>

                    <div class="hash-display">
                        <strong>🔑 Hash do Certificado:</strong>
                        <span><?php echo htmlspecialchars($certificate->hash); ?></span>
                    </div>

                    <div class="qr-code-container">
                        <strong>📱 QR Code:</strong>
                        <div id="qrcode"></div>
                    </div>
                </div>
            <?php elseif ($error): ?>
                <div id="errorMessage" class="error-message show">
                    ❌ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php elseif ($hash === ''): ?>
                <div id="infoMessage" class="info-message show">
                    ℹ️ Insira o hash do certificado para verificar sua autenticidade
                </div>
            <?php else: ?>
                <div id="errorMessage" class="error-message show">
                    ❌ Certificado não encontrado
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>© 2026 Instituto Cuidar Bem - Todos os direitos reservados</p>
    </footer>

    <script>
        async function validateCertificate() {
            const hash = document.getElementById('hashInput').value.trim();
            
            if (!hash) {
                showError('Por favor, insira o hash do certificado');
                return;
            }

            try {
                const response = await fetch(`api.php?action=validate&hash=${encodeURIComponent(hash)}`);
                const data = await response.json();
                
                if (data.success) {
                    showCertificate(data.certificate);
                } else {
                    showError(data.message || 'Certificado não encontrado');
                }
            } catch (error) {
                showError('Erro de conexão: ' + error.message);
            }
        }

        function showCertificate(cert) {
            document.getElementById('certificateCard').classList.add('show');
            document.getElementById('errorMessage').classList.remove('show');
            document.getElementById('infoMessage').classList.remove('show');

            document.getElementById('certNome').textContent = cert.nome_aluno || '-';
            document.getElementById('certCurso').textContent = cert.nome_curso || '-';
            document.getElementById('certCarga').textContent = cert.carga_horaria ? `${cert.carga_horaria} horas` : '-';
            document.getElementById('certData').textContent = formatDate(cert.data_conclusao) || '-';
            document.getElementById('certInstrutor').textContent = cert.instrutor || '-';
            document.getElementById('certCpf').textContent = cert.cpf || '-';
            document.getElementById('certHash').textContent = cert.hash_certificado || '-';

            // Gerar QR Code
            generateQRCode(window.location.href);
        }

        function showError(message) {
            document.getElementById('certificateCard').classList.remove('show');
            document.getElementById('errorMessage').classList.add('show');
            document.getElementById('infoMessage').classList.remove('show');
            document.getElementById('errorMessage').textContent = '❌ ' + message;
        }

        function formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('pt-BR');
        }

        function generateQRCode(url) {
            // Usar API pública para gerar QR Code
            const qrApi = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(url)}`;
            document.getElementById('qrcode').innerHTML = `<img src="${qrApi}" alt="QR Code" />`;
        }

        // Copiar hash ao clicar
        document.getElementById('certHash')?.addEventListener('click', function() {
            navigator.clipboard.writeText(this.textContent);
            alert('✅ Hash copiado!');
        });

        if (document.getElementById('certHash')) {
            document.getElementById('certHash').style.cursor = 'pointer';
            document.getElementById('certHash').title = 'Clique para copiar';
        }
    </script>
</body>
</html>