<?php
// Página de validação de certificados
?>
<div class="page-title">
    <h2>🔍 Verificar Autenticidade</h2>
</div>

<div class="search-container">
    <label for="hashInput">🔑 Hash do Certificado:</label>
    <div class="search-input-group">
        <input type="text" id="hashInput" 
               placeholder="Cole o hash do certificado aqui...">
        <button onclick="validateCertificate()">🔍 Validar</button>
    </div>
</div>

<div id="certificateCard" class="certificate-card">
    <div class="certificate-header">
        <h3>📜 Certificado Encontrado</h3>
        <span id="statusBadge" class="status-badge status-valid">✅ Válido</span>
    </div>

    <div class="certificate-info">
        <div class="info-item">
            <label>👤 Nome do Aluno</label>
            <span id="certNome">-</span>
        </div>

        <div class="info-item">
            <label>📚 Curso</label>
            <span id="certCurso">-</span>
        </div>

        <div class="info-item">
            <label>⏱️ Carga Horária</label>
            <span id="certCarga">-</span>
        </div>

        <div class="info-item">
            <label>📅 Data de Conclusão</label>
            <span id="certData">-</span>
        </div>

        <div class="info-item">
            <label>👨‍🏫 Instrutor</label>
            <span id="certInstrutor">-</span>
        </div>

        <div class="info-item">
            <label>📄 CPF</label>
            <span id="certCpf">-</span>
        </div>
    </div>

    <div class="hash-display">
        <strong>🔑 Hash do Certificado:</strong>
        <span id="certHash">-</span>
    </div>

    <div class="qr-code-container">
        <strong>📱 QR Code:</strong>
        <div id="qrcode"></div>
    </div>
</div>

<div id="errorMessage" class="error-message">
    ❌ Certificado não encontrado ou inválido
</div>

<div id="infoMessage" class="info-message">
    ℹ️ Insira o hash do certificado para verificar sua autenticidade
</div>

<script>
    // Verificar se há hash na URL
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const hash = urlParams.get('hash');
        
        if (hash) {
            document.getElementById('hashInput').value = hash;
            validateCertificate();
        } else {
            document.getElementById('infoMessage').classList.add('show');
        }
    });

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
    document.getElementById('certHash').addEventListener('click', function() {
        navigator.clipboard.writeText(this.textContent);
        alert('✅ Hash copiado!');
    });

    document.getElementById('certHash').style.cursor = 'pointer';
    document.getElementById('certHash').title = 'Clique para copiar';
</script>