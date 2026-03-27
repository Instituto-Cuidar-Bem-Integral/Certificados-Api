<?php
// Página de listagem de certificados
?>
<div class="page-title">
    <h2>📋 Certificados Cadastrados</h2>
</div>

<div class="search-bar">
    <input type="text" id="searchInput" placeholder="🔍 Buscar por nome, curso ou hash...">
    <button onclick="searchCertificates()">Buscar</button>
</div>

<div class="table-container">
    <table id="certificatesTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Curso</th>
                <th>Data</th>
                <th>Hash</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="certificatesBody">
            <!-- Certificados serão carregados via JavaScript -->
        </tbody>
    </table>
</div>

<div id="emptyMessage" class="empty-message" style="display: none;">
    📭 Nenhum certificado encontrado
</div>

<script>
    // Carregar certificados ao iniciar
    document.addEventListener('DOMContentLoaded', loadCertificates);

    async function loadCertificates() {
        try {
            const response = await fetch('api.php?action=list');
            const data = await response.json();
            
            if (data.success) {
                renderCertificates(data.certificates);
            } else {
                showError('Erro ao carregar certificados');
            }
        } catch (error) {
            showError('Erro de conexão: ' + error.message);
        }
    }

    function renderCertificates(certificates) {
        const tbody = document.getElementById('certificatesBody');
        const emptyMessage = document.getElementById('emptyMessage');

        if (certificates.length === 0) {
            tbody.innerHTML = '';
            emptyMessage.style.display = 'block';
            return;
        }

        emptyMessage.style.display = 'none';
        tbody.innerHTML = certificates.map(cert => `
            <tr>
                <td>${cert.id}</td>
                <td>${cert.nome_aluno}</td>
                <td>${cert.nome_curso}</td>
                <td>${formatDate(cert.data_conclusao)}</td>
                <td class="hash-cell">${cert.hash_certificado}</td>
                <td><span class="status-badge">Ativo</span></td>
                <td>
                    <button class="action-btn btn-view" onclick="viewCertificate('${cert.hash_certificado}')">👁️ Ver</button>
                    <button class="action-btn btn-delete" onclick="deleteCertificate(${cert.id})">🗑️ Excluir</button>
                </td>
            </tr>
        `).join('');
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('pt-BR');
    }

    function searchCertificates() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#certificatesBody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    }

    function viewCertificate(hash) {
        // Carregar página de validação com o hash
        loadPage('validar');
        setTimeout(() => {
            document.getElementById('hashInput').value = hash;
            validateCertificate();
        }, 100);
    }

    async function deleteCertificate(id) {
        if (!confirm('Tem certeza que deseja excluir este certificado?')) {
            return;
        }

        try {
            const response = await fetch(`api.php?action=delete&id=${id}`, {
                method: 'DELETE'
            });
            const data = await response.json();

            if (data.success) {
                alert('✅ Certificado excluído com sucesso!');
                loadCertificates();
            } else {
                alert('❌ Erro ao excluir certificado: ' + data.message);
            }
        } catch (error) {
            alert('❌ Erro de conexão: ' + error.message);
        }
    }

    function showError(message) {
        const tbody = document.getElementById('certificatesBody');
        tbody.innerHTML = `<tr><td colspan="7" class="empty-message">❌ ${message}</td></tr>`;
    }
</script>