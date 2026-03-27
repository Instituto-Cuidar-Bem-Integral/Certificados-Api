<div class="page-title">
    <h2>🎓 Novo Certificado</h2>
</div>

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