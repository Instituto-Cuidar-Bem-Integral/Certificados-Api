<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Certificado - Instituto Cuidar Bem</title>
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

        .nav-back {
            display: inline-block;
            margin-top: 10px;
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            transition: background 0.3s ease;
        }

        .nav-back:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        main {
            padding: 40px 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .page-title {
            color: #333;
            margin-bottom: 30px;
            font-size: 1.8rem;
            text-align: center;
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
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
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
        }
    </style>
</head>
<body>
    <header>
        <h1>📝 Cadastrar Certificado</h1>
        <p>Instituto Cuidar Bem</p>
        <a href="index.php" class="nav-back">← Voltar ao Menu</a>
    </header>

    <main>
        <div class="container">
            <h2 class="page-title">🎓 Novo Certificado</h2>
            
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
                        <label for="nome_curso">📚 Nome do Curso *</label>
                        <input type="text" id="nome_curso" name="nome_curso" required 
                               placeholder="Digite o nome do curso">
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
                    <label for="descricao">📝 Descrição do Curso</label>
                    <textarea id="descricao" name="descricao" 
                              placeholder="Descreva o conteúdo do curso..."></textarea>
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
    </main>

    <footer>
        <p>© 2026 Instituto Cuidar Bem - Todos os direitos reservados</p>
    </footer>

    <script>
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
                } else {
                    showError(result.message);
                }
            } catch (error) {
                showError('Erro de conexão: ' + error.message);
            }
        });

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