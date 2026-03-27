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

<style>
    .page-title {
        color: #333;
        margin-bottom: 30px;
        font-size: 1.8rem;
        text-align: center;
    }

    .search-bar {
        display: flex;
        gap: 10px;
        margin-bottom: 30px;
    }

    .search-bar input {
        flex: 1;
        padding: 12px 20px;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        font-size: 1rem;
        transition: border-color 0.3s ease;
    }

    .search-bar input:focus {
        outline: none;
        border-color: #667eea;
    }

    .search-bar button {
        padding: 12px 24px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 1rem;
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .search-bar button:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
    }

    .table-container {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px;
        text-align: left;
        font-weight: 600;
    }

    th:first-child {
        border-radius: 10px 0 0 0;
    }

    th:last-child {
        border-radius: 0 10px 0 0;
    }

    td {
        padding: 15px;
        border-bottom: 1px solid #e0e0e0;
        color: #333;
    }

    tr:hover {
        background: #f8f9fa;
    }

    .hash-cell {
        font-family: monospace;
        font-size: 0.85rem;
        word-break: break-all;
        max-width: 200px;
    }

    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        background: #d4edda;
        color: #155724;
    }

    .action-btn {
        padding: 8px 16px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.9rem;
        transition: transform 0.3s ease;
        margin-right: 5px;
    }

    .action-btn:hover {
        transform: translateY(-2px);
    }

    .btn-view {
        background: #17a2b8;
        color: white;
    }

    .btn-delete {
        background: #dc3545;
        color: white;
    }

    .empty-message {
        text-align: center;
        padding: 40px;
        color: #666;
        font-size: 1.1rem;
    }

    @media (max-width: 768px) {
        .search-bar {
            flex-direction: column;
        }

        th, td {
            padding: 10px;
            font-size: 0.9rem;
        }
    }
</style>

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