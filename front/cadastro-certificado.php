<?php
// Página de Cadastro de Certificados
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Certificados - Instituto Cuidar Bem</title>
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

        /* Dropdown de atividades */
        .atividades-dropdown {
            position: relative;
        }

        .atividades-trigger {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            background: #fff;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .atividades-trigger:hover,
        .atividades-trigger.active {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .atividades-trigger .arrow {
            transition: transform 0.3s ease;
            font-size: 0.8rem;
        }

        .atividades-trigger.active .arrow {
            transform: rotate(180deg);
        }

        .atividades-list {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 2px solid #e0e0e0;
            border-top: none;
            border-radius: 0 0 10px 10px;
            max-height: 250px;
            overflow-y: auto;
            z-index: 100;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .atividades-list.open {
            display: block;
        }

        .atividades-list label {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .atividades-list label:hover {
            background: #f0f0ff;
        }

        .atividades-list input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 12px;
            cursor: pointer;
            accent-color: #667eea;
        }

        /* Área de atividades selecionadas */
        .selected-atividades {
            margin-top: 15px;
            min-height: 50px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            border: 2px dashed #d0d0d0;
        }

        .selected-atividades.has-items {
            border-style: solid;
            border-color: #667eea;
            background: #f0f0ff;
        }

        .selected-atividades-title {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .selected-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin: 4px;
            transition: transform 0.2s ease;
        }

        .selected-tag:hover {
            transform: scale(1.05);
        }

        .selected-tag .remove-btn {
            background: rgba(255, 255, 255, 0.3);
            border: none;
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            transition: background 0.2s ease;
        }

        .selected-tag .remove-btn:hover {
            background: rgba(255, 0, 0, 0.6);
        }

        .empty-message {
            color: #999;
            font-style: italic;
            font-size: 0.9rem;
            text-align: center;
            padding: 10px;
        }

        .no-activities,
        .loading-text {
            color: #666;
            text-align: center;
            padding: 20px;
        }

        /* Assinatura adicional */
        .assinatura-adicional-section {
            margin-top: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border: 2px solid #e0e0e0;
        }

        .assinatura-adicional-section h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }

        .assinatura-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            cursor: pointer;
        }

        .assinatura-toggle input[type="checkbox"] {
            width: 20px;
            height: 20px;
            accent-color: #667eea;
        }

        .assinatura-fields {
            display: none;
        }

        .assinatura-fields.visible {
            display: block;
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
        <h1>🎓 Sistema de Certificados</h1>
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
                    </div>

                    <!-- Assinatura Adicional -->
                    <div class="form-group">
                        <div class="assinatura-adicional-section">
                            <h3>🖊️ Assinatura Adicional (Instrutor/Responsável)</h3>
                            <label class="assinatura-toggle">
                                <input type="checkbox" id="hasAssinaturaAdicional" onchange="toggleAssinaturaCampos()">
                                <span>Adicionar assinatura de responsável (médico, advogado, etc.)</span>
                            </label>
                            <div class="assinatura-fields" id="assinaturaFields">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="nomeInstrutor">👤 Nome do Instrutor *</label>
                                        <input type="text" id="nomeInstrutor" placeholder="Nome completo do instrutor">
                                    </div>
                                    <div class="form-group">
                                        <label for="funcaoInstrutor">📚 Função do Responsável *</label>
                                        <input type="text" id="funcaoInstrutor" placeholder="Ex: Médico, Advogado, etc.">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="numeroRegistro">📄 Nº Registro Profissional</label>
                                    <input type="text" id="numeroRegistro" placeholder="CRM, OAB, etc.">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>📚 Atividades (selecione uma ou mais)</label>
                        <div class="atividades-dropdown" id="atividadesDropdown">
                            <div class="atividades-trigger" id="atividadesTrigger" onclick="toggleDropdown()">
                                <span id="triggerText">Selecione as atividades...</span>
                                <span class="arrow">▼</span>
                            </div>
                            <div class="atividades-list" id="atividadesList">
                                <p class="loading-text">Carregando atividades...</p>
                            </div>
                        </div>
                        <div class="selected-atividades" id="selectedAtividades">
                            <div class="selected-atividades-title">Atividades selecionadas:</div>
                            <div class="empty-message" id="emptyMessage">Nenhuma atividade selecionada</div>
                            <div id="tagsContainer"></div>
                        </div>
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
        </div>
    </main>

    <footer>
        <p>© 2026 Instituto Cuidar Bem - Todos os direitos reservados</p>
    </footer>

    <script>
        // Array para armazenar atividades selecionadas
        let atividadesSelecionadas = [];
        let todasAtividades = [];

        // Carregar atividades ao iniciar
        document.addEventListener('DOMContentLoaded', loadAtividades);

        // Fechar dropdown ao clicar fora
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('atividadesDropdown');
            if (!dropdown.contains(e.target)) {
                closeDropdown();
            }
        });

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

        function toggleDropdown() {
            const list = document.getElementById('atividadesList');
            const trigger = document.getElementById('atividadesTrigger');
            list.classList.toggle('open');
            trigger.classList.toggle('active');
        }

        function closeDropdown() {
            const list = document.getElementById('atividadesList');
            const trigger = document.getElementById('atividadesTrigger');
            list.classList.remove('open');
            trigger.classList.remove('active');
        }

        async function loadAtividades() {
            try {
                const response = await fetch('../api.php?action=list_atividades');
                const data = await response.json();
                
                const list = document.getElementById('atividadesList');
                
                if (data.success && data.atividades.length > 0) {
                    todasAtividades = data.atividades;
                    let html = '';
                    data.atividades.forEach(atividade => {
                        const isSelected = atividadesSelecionadas.some(a => a.id === atividade.id);
                        html += `
                            <label>
                                <input type="checkbox" value="${atividade.id}" ${isSelected ? 'checked' : ''} onchange="toggleAtividade(${atividade.id}, '${atividade.nome.replace(/'/g, "\\'")}', this.checked)">
                                ${atividade.nome}
                            </label>
                        `;
                    });
                    list.innerHTML = html;
                } else {
                    list.innerHTML = '<p class="no-activities">Nenhuma atividade cadastrada. <a href="atividades.php">Cadastre atividades primeiro</a>.</p>';
                }
            } catch (error) {
                console.error('Erro ao carregar atividades:', error);
                document.getElementById('atividadesList').innerHTML = '<p class="no-activities" style="color: red;">Erro ao carregar atividades.</p>';
            }
        }

        function toggleAtividade(id, nome, checked) {
            if (checked) {
                if (!atividadesSelecionadas.some(a => a.id === id)) {
                    atividadesSelecionadas.push({ id, nome });
                }
            } else {
                atividadesSelecionadas = atividadesSelecionadas.filter(a => a.id !== id);
            }
            renderTags();
        }

        function removeAtividade(id) {
            atividadesSelecionadas = atividadesSelecionadas.filter(a => a.id !== id);
            renderTags();
            
            // Atualizar checkbox correspondente
            const checkbox = document.querySelector(`#atividadesList input[value="${id}"]`);
            if (checkbox) {
                checkbox.checked = false;
            }
        }

        function renderTags() {
            const tagsContainer = document.getElementById('tagsContainer');
            const emptyMessage = document.getElementById('emptyMessage');
            const selectedArea = document.getElementById('selectedAtividades');
            const triggerText = document.getElementById('triggerText');
            
            if (atividadesSelecionadas.length === 0) {
                tagsContainer.innerHTML = '';
                emptyMessage.style.display = 'block';
                selectedArea.classList.remove('has-items');
                triggerText.textContent = 'Selecione as atividades...';
            } else {
                emptyMessage.style.display = 'none';
                selectedArea.classList.add('has-items');
                triggerText.textContent = `${atividadesSelecionadas.length} atividade(s) selecionada(s)`;
                
                let html = '';
                atividadesSelecionadas.forEach(ativ => {
                    html += `
                        <span class="selected-tag">
                            ${ativ.nome}
                            <button class="remove-btn" onclick="removeAtividade(${ativ.id})" title="Remover">✕</button>
                        </span>
                    `;
                });
                tagsContainer.innerHTML = html;
            }
        }

        // Submissão do formulário
        document.getElementById('certificateForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Coletar IDs das atividades selecionadas
            const atividadesIds = atividadesSelecionadas.map(a => a.id);
            
            // Check assinatura adicional
            const hasAssinaturaAdicional = document.getElementById('hasAssinaturaAdicional').checked;
            const assinaturaAdicional = hasAssinaturaAdicional ? {
                nome_instrutor: document.getElementById('nomeInstrutor').value,
                funcao: document.getElementById('funcaoInstrutor').value,
                numero_registro: document.getElementById('numeroRegistro').value
            } : null;
            
            // Coletar dados do formulário
            const data = {
                nome_aluno: document.getElementById('nome_aluno').value,
                cpf: document.getElementById('cpf').value,
                nome_funcao: document.getElementById('nome_funcao').value,
                carga_horaria: document.getElementById('carga_horaria').value,
                data_conclusao: document.getElementById('data_conclusao').value,
                has_assinatura_adicional: hasAssinaturaAdicional ? 1 : 0,
                assinatura_adicional: assinaturaAdicional,
                atividades: atividadesIds
            };
            
            try {
                const response = await fetch('../api.php?action=create', {
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

        function toggleAssinaturaCampos() {
            const checkbox = document.getElementById('hasAssinaturaAdicional');
            const fields = document.getElementById('assinaturaFields');
            if (checkbox.checked) {
                fields.classList.add('visible');
            } else {
                fields.classList.remove('visible');
            }
        }

        function clearForm() {
            document.getElementById('certificateForm').reset();
            document.getElementById('data_conclusao').valueAsDate = new Date();
            document.getElementById('hashDisplay').style.display = 'none';
            
            // Limpar atividades selecionadas
            atividadesSelecionadas = [];
            renderTags();
            
            // Limpar checkboxes
            document.querySelectorAll('#atividadesList input[type="checkbox"]').forEach(cb => {
                cb.checked = false;
            });
            
            // Esconder campos de assinatura adicional
            document.getElementById('hasAssinaturaAdicional').checked = false;
            document.getElementById('assinaturaFields').classList.remove('visible');
        }
    </script>
</body>
</html>