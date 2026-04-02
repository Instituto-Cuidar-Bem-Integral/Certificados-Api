<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/conexao.php';

use App\Certificate\CertificateRepository;
use App\Certificate\CertificateDTO;

$repository = new CertificateRepository(db());
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'list':
            handleList($repository);
            break;
        
        case 'create':
            handleCreate($repository);
            break;
        
        case 'validate':
            handleValidate($repository);
            break;
        
        case 'delete':
            handleDelete($repository);
            break;
        
        case 'list_atividades':
            handleListAtividades();
            break;
        
        case 'create_atividade':
            handleCreateAtividade();
            break;
        
        case 'update_atividade':
            handleUpdateAtividade();
            break;
        
        case 'delete_atividade':
            handleDeleteAtividade();
            break;
        
        default:
            jsonResponse([
                'success' => false,
                'message' => 'Ação não especificada ou inválida'
            ]);
    }
} catch (Exception $e) {
    jsonResponse([
        'success' => false,
        'message' => 'Erro interno: ' . $e->getMessage()
    ]);
}

function handleList(CertificateRepository $repository): void
{
    $certificates = $repository->listAll();
    
    // Convert DTOs to arrays
    $data = [];
    foreach ($certificates as $cert) {
        $data[] = [
            'id' => $cert->id,
            'hash_certificado' => $cert->hash,
            'nome_aluno' => $cert->nome,
            'nome_funcao' => $cert->funcao ?? '',
            'carga_horaria' => $cert->cargaHoraria ?? '',
            'data_conclusao' => $cert->dataEmissao->format('Y-m-d'),
            'atividade' => $cert->atividade ?? '',
            'criado_em' => $cert->criadoEm->format('Y-m-d H:i:s'),
        ];
    }
    
    jsonResponse([
        'success' => true,
        'certificates' => $data
    ]);
}

function handleCreate(CertificateRepository $repository): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse([
            'success' => false,
            'message' => 'Método não permitido'
        ]);
        return;
    }
    
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        jsonResponse([
            'success' => false,
            'message' => 'Dados inválidos'
        ]);
        return;
    }
    
    // Validate required fields
    $required = ['nome_aluno', 'cpf', 'nome_funcao', 'carga_horaria', 'data_conclusao'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            jsonResponse([
                'success' => false,
                'message' => "Campo obrigatório: {$field}"
            ]);
            return;
        }
    }
    
    // Generate hash
    $hash = hash('sha256', $data['nome_aluno'] . $data['cpf'] . $data['nome_funcao'] . time());
    
    // Check if has assinatura adicional
    $hasAssinaturaAdicional = isset($data['has_assinatura_adicional']) && $data['has_assinatura_adicional'] ? 1 : 0;
    
    // Insert certificate
    $id = $repository->insert(
        $hash,
        $data['nome_aluno'],
        $data['nome_funcao'],
        $data['data_conclusao'],
        $data['carga_horaria'],
        $hasAssinaturaAdicional
    );
    
    // Salvar assinatura adicional if enabled
    if ($hasAssinaturaAdicional && !empty($data['assinatura_adicional'])) {
        $assinaturaData = $data['assinatura_adicional'];
        $stmt = db()->prepare(
            'INSERT INTO assinatura_adicional (id_certificado, nome_instrutor, funcao, numero_registro) 
             VALUES (:id_certificado, :nome_instrutor, :funcao, :numero_registro)'
        );
        $stmt->execute([
            ':id_certificado' => (int)$id,
            ':nome_instrutor' => $assinaturaData['nome_instrutor'] ?? '',
            ':funcao' => $assinaturaData['funcao'] ?? '',
            ':numero_registro' => $assinaturaData['numero_registro'] ?? '',
        ]);
    }
    
    // Salvar atividades selecionadas na tabela certificados_atividades_grupo
    if (isset($data['atividades']) && is_array($data['atividades']) && !empty($data['atividades'])) {
        foreach ($data['atividades'] as $atividadeId) {
            if (!empty($atividadeId) && is_numeric($atividadeId)) {
                $stmt = db()->prepare(
                    'INSERT INTO certificados_atividades_grupo (id_cer_atividade, id_certificado) 
                     VALUES (:id_atividade, :id_certificado)'
                );
                $stmt->execute([
                    ':id_atividade' => (int)$atividadeId,
                    ':id_certificado' => (int)$id
                ]);
            }
        }
    }
    
    jsonResponse([
        'success' => true,
        'message' => 'Certificado cadastrado com sucesso',
        'id' => $id,
        'hash' => $hash
    ]);
}

function handleValidate(CertificateRepository $repository): void
{
    $hash = $_GET['h'] ?? '';
    
    if (empty($hash)) {
        jsonResponse([
            'success' => false,
            'message' => 'Hash não fornecido'
        ]);
        return;
    }
    
    $certificate = $repository->findByHash($hash);
    
    if (!$certificate) {
        jsonResponse([
            'success' => false,
            'message' => 'Certificado não encontrado'
        ]);
        return;
    }
    
    jsonResponse([
        'success' => true,
        'certificate' => [
            'id' => $certificate->id,
            'hash_certificado' => $certificate->hash,
            'nome_aluno' => $certificate->nome,
            'nome_funcao' => $certificate->funcao ?? '',
            'carga_horaria' => $certificate->cargaHoraria ?? '',
            'data_conclusao' => $certificate->dataEmissao->format('Y-m-d'),
            'cpf' => $certificate->atividade ?? '',
            'criado_em' => $certificate->criadoEm->format('Y-m-d H:i:s'),
        ]
    ]);
}

function handleDelete(CertificateRepository $repository): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        jsonResponse([
            'success' => false,
            'message' => 'Método não permitido'
        ]);
        return;
    }
    
    $id = $_GET['id'] ?? '';
    
    if (empty($id) || !is_numeric($id)) {
        jsonResponse([
            'success' => false,
            'message' => 'ID inválido'
        ]);
        return;
    }
    
    $stmt = db()->prepare('DELETE FROM certificados WHERE id = :id');
    $stmt->execute([':id' => (int)$id]);
    
    if ($stmt->rowCount() > 0) {
        jsonResponse([
            'success' => true,
            'message' => 'Certificado excluído com sucesso'
        ]);
    } else {
        jsonResponse([
            'success' => false,
            'message' => 'Certificado não encontrado'
        ]);
    }
}

function handleListAtividades(): void
{
    // Lista todas as atividades da tabela certificados_atividades
    $stmt = db()->query('SELECT id, atividade as nome FROM certificados_atividades ORDER BY id ASC');
    $atividades = $stmt->fetchAll();
    
    jsonResponse([
        'success' => true,
        'atividades' => $atividades
    ]);
}

function handleCreateAtividade(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse([
            'success' => false,
            'message' => 'Método não permitido'
        ]);
        return;
    }
    
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data || empty($data['atividade'])) {
        jsonResponse([
            'success' => false,
            'message' => 'Nome da atividade é obrigatório'
        ]);
        return;
    }
    
    // Inserir na tabela certificados_atividades com a coluna 'atividade'
    $stmt = db()->prepare('INSERT INTO certificados_atividades (atividade) VALUES (:atividade)');
    $stmt->execute([
        ':atividade' => $data['atividade']
    ]);
    
    jsonResponse([
        'success' => true,
        'message' => 'Atividade cadastrada com sucesso',
        'id' => (int)db()->lastInsertId()
    ]);
}

function handleUpdateAtividade(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
        jsonResponse([
            'success' => false,
            'message' => 'Método não permitido'
        ]);
        return;
    }
    
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data || empty($data['id']) || empty($data['atividade'])) {
        jsonResponse([
            'success' => false,
            'message' => 'ID e nome da atividade são obrigatórios'
        ]);
        return;
    }
    
    $stmt = db()->prepare('UPDATE certificados_atividades SET atividade = :atividade WHERE id = :id');
    $stmt->execute([
        ':id' => (int)$data['id'],
        ':atividade' => $data['atividade']
    ]);
    
    if ($stmt->rowCount() > 0) {
        jsonResponse([
            'success' => true,
            'message' => 'Atividade atualizada com sucesso'
        ]);
    } else {
        jsonResponse([
            'success' => false,
            'message' => 'Atividade não encontrada'
        ]);
    }
}

function handleDeleteAtividade(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        jsonResponse([
            'success' => false,
            'message' => 'Método não permitido'
        ]);
        return;
    }
    
    $id = $_GET['id'] ?? '';
    
    if (empty($id) || !is_numeric($id)) {
        jsonResponse([
            'success' => false,
            'message' => 'ID inválido'
        ]);
        return;
    }
    
    $stmt = db()->prepare('DELETE FROM certificados_atividades WHERE id = :id');
    $stmt->execute([':id' => (int)$id]);
    
    if ($stmt->rowCount() > 0) {
        jsonResponse([
            'success' => true,
            'message' => 'Atividade excluída com sucesso'
        ]);
    } else {
        jsonResponse([
            'success' => false,
            'message' => 'Atividade não encontrada'
        ]);
    }
}

function jsonResponse(array $data): void
{
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}