<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
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
            'nome_curso' => $cert->funcao ?? '',
            'carga_horaria' => $cert->cargaHoraria ?? '',
            'data_conclusao' => $cert->dataEmissao->format('Y-m-d'),
            'instrutor' => $cert->instrutor ?? '',
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
    $required = ['nome_aluno', 'cpf', 'nome_curso', 'carga_horaria', 'data_conclusao', 'instrutor'];
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
    $hash = hash('sha256', $data['nome_aluno'] . $data['cpf'] . $data['nome_curso'] . time());
    
    // Insert certificate
    $id = $repository->insert(
        $hash,
        $data['nome_aluno'],
        $data['nome_curso'],
        $data['data_conclusao'],
        $data['carga_horaria'],
        $data['descricao'] ?? null,
        $data['instrutor']
    );
    
    jsonResponse([
        'success' => true,
        'message' => 'Certificado cadastrado com sucesso',
        'id' => $id,
        'hash' => $hash
    ]);
}

function handleValidate(CertificateRepository $repository): void
{
    $hash = $_GET['hash'] ?? '';
    
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
            'nome_curso' => $certificate->funcao ?? '',
            'carga_horaria' => $certificate->cargaHoraria ?? '',
            'data_conclusao' => $certificate->dataEmissao->format('Y-m-d'),
            'instrutor' => $certificate->instrutor ?? '',
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

function jsonResponse(array $data): void
{
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}