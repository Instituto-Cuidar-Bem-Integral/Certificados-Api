<?php
// Página principal - Cadastro e Listagem de Certificados
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Certificados - Instituto Cuidar Bem</title>
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
            padding: 40px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .section-title {
            color: #333;
            margin-bottom: 30px;
            font-size: 1.8rem;
            text-align: center;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }

        .form-section {
            margin-bottom: 50px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 1rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-container {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            display: none;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            display: none;
        }

        .hash-display {
            background: #e9ecef;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
            font-family: monospace;
            word-break: break-all;
            display: none;
        }

        .hash-display strong {
            color: #333;
            display: block;
            margin-bottom: 10px;
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

        .btn-pdf {
            background: #28a745;
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

        footer {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 20px;
            text-align: center;
            color: rgba(255, 255, 255, 0.8);
            margin-top: 40px;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .container {
                padding: 20px;
            }

            .btn-container {
                flex-direction: column;
            }

            .search-bar {
                flex-direction: column;
            }

            th, td {
                padding: 10px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>🎓 Sistema de Certificados</h1>
        <p>Instituto Cuidar Bem</p>
    </header>

    <main>
        <div class="container">
            <!-- Seção de Cadastro -->
            <div class="form-section">
                <h2 class="section-title">📝 Cadastrar Novo Certificado</h2>
                
                <div id="successMessage" class="success-message">
                    ✅ Certificado cadastrado com sucesso!
                </div>

                <div id="errorMessage" class="error-message">
                    ❌ Erro ao cadastrar certificado
                </div>

                <form id="certificateForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nome_aluno">👤 Nome do Aluno *</label>
                            <input type="text" id="nome_aluno" name="nome_aluno" required 
                                   placeholder="Digite o nome completo do aluno">
                        </div>

                        <div class="form-group">
                            <label for="cpf">📄 CPF *</label>
                            <input type="text" id="cpf" name="cpf" required 
                                   placeholder="000.000.000-00" maxlength="14">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="nome_funcao">📚 Nome da Função *</label>
                            <input type="text" id="nome_funcao" name="nome_funcao" required 
                                   placeholder="Digite o nome da função">
                        </div>

                        <div class="form-group">
                            <label for="carga_horaria">⏱️ Carga Horária *</label>
                            <input type="number" id="carga_horaria" name="carga_horaria" required 
                                   placeholder="Ex: 40" min="1">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="data_conclusao">📅 Data de Conclusão *</label>
                            <input type="date" id="data_conclusao" name="data_conclusao" required>
                        </div>

                        <div class="form-group">
                            <label for="instrutor">👨‍🏫 Instrutor *</label>
                            <input type="text" id="instrutor" name="instrutor" required 
                                   placeholder="Nome do instrutor">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descricao">📝 Descrição da Função</label>
                        <textarea id="descricao" name="descricao" 
                                  placeholder="Descreva o conteúdo da função..."></textarea>
                    </div>

                    <div class="btn-container">
                        <button type="button" class="btn btn-secondary" onclick="clearForm()">
                            🔄 Limpar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            💾 Cadastrar
                        </button>
                    </div>
                </form>

                <div id="hashDisplay" class="hash-display">
                    <strong>🔑 Hash do Certificado:</strong>
                    <span id="hashValue"></span>
                </div>
            </div>

            <!-- Seção de Listagem -->
            <div class="form-section">
                <h2 class="section-title">📋 Certificados Cadastrados</h2>
                
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="🔍 Buscar por nome, função ou hash...">
                    <button onclick="searchCertificates()">Buscar</button>
                </div>

                <div class="table-container">
                    <table id="certificatesTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Função</th>
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
            </div>
        </div>
    </main>

    <footer>
        <p>© 2026 Instituto Cuidar Bem - Todos os direitos reservados</p>
    </footer>

    <script>
        // Carregar certificados ao iniciar
        document.addEventListener('DOMContentLoaded', loadCertificates);

        // Formatação automática do CPF
        document.getElementById('cpf').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            e.target.value = value;
        });

        // Definir data atual como padrão
        document.getElementById('data_conclusao').valueAsDate = new Date();

        // Submissão do formulário
        document.getElementById('certificateForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            try {
                const response = await fetch('api.php?action=create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showSuccess(result.hash);
                    clearForm();
                    loadCertificates(); // Recarregar lista
                } else {
                    showError(result.message);
                }
            } catch (error) {
                showError('Erro de conexão: ' + error.message);
            }
        });

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
                    <td>${cert.nome_funcao}</td>
                    <td>${formatDate(cert.data_conclusao)}</td>
                    <td class="hash-cell">${cert.hash_certificado}</td>
                    <td><span class="status-badge">Ativo</span></td>
                    <td>
                        <button class="action-btn btn-view" onclick="viewCertificate('${cert.hash_certificado}')">👁️ Ver</button>
                        <button class="action-btn btn-pdf" onclick="openPdf('${cert.hash_certificado}')">📄 PDF</button>
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
            // Abrir página de validação em nova aba
            window.open(`validar.php?h=${encodeURIComponent(hash)}`, '_blank');
        }

        function openPdf(hash) {
            // Abrir PDF do certificado em nova aba
            window.open(`public/certificado.php?h=${encodeURIComponent(hash)}`, '_blank');
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

        function showSuccess(hash) {
            document.getElementById('successMessage').style.display = 'block';
            document.getElementById('errorMessage').style.display = 'none';
            document.getElementById('hashDisplay').style.display = 'block';
            document.getElementById('hashValue').textContent = hash;
            
            setTimeout(() => {
                document.getElementById('successMessage').style.display = 'none';
            }, 5000);
        }

        function showError(message) {
            document.getElementById('errorMessage').textContent = '❌ ' + message;
            document.getElementById('errorMessage').style.display = 'block';
            document.getElementById('successMessage').style.display = 'none';
            
            setTimeout(() => {
                document.getElementById('errorMessage').style.display = 'none';
            }, 5000);
        }

        function clearForm() {
            document.getElementById('certificateForm').reset();
            document.getElementById('data_conclusao').valueAsDate = new Date();
            document.getElementById('hashDisplay').style.display = 'none';
        }
    </script>
</body>
</html>