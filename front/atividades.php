<?php
// Página de Cadastro de Atividades
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Atividades - Instituto Cuidar Bem</title>
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

        nav {
            background: rgba(0, 0, 0, 0.2);
            padding: 10px;
            text-align: center;
        }

        nav a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            margin: 0 10px;
            border-radius: 8px;
            transition: background 0.3s ease;
        }

        nav a:hover {
            background: rgba(255, 255, 255, 0.2);
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

        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-group input:focus {
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

        .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: transform 0.3s ease;
        }

        .action-btn:hover {
            transform: translateY(-2px);
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-edit {
            background: #ffc107;
            color: #333;
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
            .container {
                padding: 20px;
            }

            .btn-container {
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
        <h1>📚 Cadastro de Atividades</h1>
        <p>Instituto Cuidar Bem</p>
    </header>

    <nav>
        <a href="cadastro-certificado.php">📝 Cadastrar Certificado</a>
        <a href="atividades.php">📚 Cadastrar Atividades</a>
        <a href="../index.php">📋 Listar Certificados</a>
    </nav>

    <main>
        <div class="container">
            <div class="form-section">
                <h2 class="section-title">📝 Cadastrar Nova Atividade</h2>
                
                <div id="successMessage" class="success-message">
                    ✅ Atividade cadastrada com sucesso!
                </div>

                <div id="errorMessage" class="error-message">
                    ❌ Erro ao cadastrar atividade
                </div>

                <form id="atividadeForm">
                    <div class="form-group">
                        <label for="nome_atividade">📚 Nome da Atividade *</label>
                        <input type="text" id="nome_atividade" name="nome_atividade" required 
                               placeholder="Digite o nome da atividade">
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
            </div>

            <div class="form-section">
                <h2 class="section-title">📋 Atividades Cadastradas</h2>
                
                <div class="table-container">
                    <table id="atividadesTable">
                        <thead>
                            <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="atividadesBody">
                            <!-- Atividades serão carregadas via JavaScript -->
                        </tbody>
                    </table>
                </div>

                <div id="emptyMessage" class="empty-message" style="display: none;">
                    📭 Nenhuma atividade cadastrada
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>© 2026 Instituto Cuidar Bem - Todos os direitos reservados</p>
    </footer>

    <script>
        // Carregar atividades ao iniciar
        document.addEventListener('DOMContentLoaded', loadAtividades);

        async function loadAtividades() {
            try {
                const response = await fetch('../api.php?action=list_atividades');
                const data = await response.json();
                
                if (data.success) {
                    renderAtividades(data.atividades);
                } else {
                    showError('Erro ao carregar atividades');
                }
            } catch (error) {
                showError('Erro de conexão: ' + error.message);
            }
        }

        function renderAtividades(atividades) {
            const tbody = document.getElementById('atividadesBody');
            const emptyMessage = document.getElementById('emptyMessage');

            if (atividades.length === 0) {
                tbody.innerHTML = '';
                emptyMessage.style.display = 'block';
                return;
            }

            emptyMessage.style.display = 'none';
            tbody.innerHTML = atividades.map(ativ => `
                        <tr>
                            <td>${ativ.id}</td>
                            <td>${ativ.nome}</td>
                            <td>
                                <button class="action-btn btn-edit" onclick="editAtividade(${ativ.id}, '${ativ.nome.replace(/'/g, "\\'")}')">✏️ Editar</button>
                                <button class="action-btn btn-delete" onclick="deleteAtividade(${ativ.id})">🗑️ Excluir</button>
                            </td>
                        </tr>
            `).join('');
        }

        // Submissão do formulário
        document.getElementById('atividadeForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = {
                atividade: formData.get('nome_atividade')
            };
            
            try {
                const response = await fetch('../api.php?action=create_atividade', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showSuccess();
                    clearForm();
                    loadAtividades();
                } else {
                    showError(result.message);
                }
            } catch (error) {
                showError('Erro de conexão: ' + error.message);
            }
        });

        function editAtividade(id, nome) {
            document.getElementById('nome_atividade').value = nome;
            
            // Mudar o botão para atualizar
            const form = document.getElementById('atividadeForm');
            form.onsubmit = async function(e) {
                e.preventDefault();
                
                const data = {
                    id: id,
                    atividade: document.getElementById('nome_atividade').value
                };
                
                try {
                    const response = await fetch('../api.php?action=update_atividade', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        showSuccess();
                        clearForm();
                        loadAtividades();
                        // Restaurar o comportamento original do formulário
                        form.onsubmit = null;
                    } else {
                        showError(result.message);
                    }
                } catch (error) {
                    showError('Erro de conexão: ' + error.message);
                }
            };
        }

        async function deleteAtividade(id) {
            if (!confirm('Tem certeza que deseja excluir esta atividade?')) {
                return;
            }

            try {
                const response = await fetch(`../api.php?action=delete_atividade&id=${id}`, {
                    method: 'DELETE'
                });
                const data = await response.json();

                if (data.success) {
                    alert('✅ Atividade excluída com sucesso!');
                    loadAtividades();
                } else {
                    alert('❌ Erro ao excluir atividade: ' + data.message);
                }
            } catch (error) {
                alert('❌ Erro de conexão: ' + error.message);
            }
        }

        function showSuccess() {
            document.getElementById('successMessage').style.display = 'block';
            document.getElementById('errorMessage').style.display = 'none';
            
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
            document.getElementById('atividadeForm').reset();
        }
    </script>
</body>
</html>