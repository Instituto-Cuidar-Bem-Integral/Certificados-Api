<?php
// Script de teste para verificar conexão com banco de dados
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/conexao.php';

use App\Certificate\CertificateRepository;

echo "=== Teste de Conexão com Banco de Dados ===\n\n";

try {
    $pdo = db();
    echo "✅ Conexão estabelecida com sucesso!\n\n";
    
    // Verificar se a tabela existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'certificados'");
    $tables = $stmt->fetchAll();
    
    if (count($tables) > 0) {
        echo "✅ Tabela 'certificados' encontrada!\n\n";
        
        // Listar todos os certificados
        $repo = new CertificateRepository($pdo);
        $certificates = $repo->listAll();
        
        echo "📋 Total de certificados: " . count($certificates) . "\n\n";
        
        if (count($certificates) > 0) {
            echo "📜 Certificados cadastrados:\n";
            foreach ($certificates as $cert) {
                echo "  - ID: {$cert->id}\n";
                echo "    Nome: {$cert->nome}\n";
                echo "    Hash: {$cert->hash}\n";
                echo "    Data: {$cert->dataEmissao->format('d/m/Y')}\n\n";
            }
            
            // Testar busca por hash
            $firstCert = $certificates[0];
            echo "🔍 Testando busca por hash: {$firstCert->hash}\n";
            
            $found = $repo->findByHash($firstCert->hash);
            if ($found) {
                echo "✅ Certificado encontrado via findByHash!\n";
                echo "   Nome: {$found->nome}\n";
            } else {
                echo "❌ Certificado NÃO encontrado via findByHash!\n";
            }
        } else {
            echo "ℹ️  Nenhum certificado cadastrado ainda.\n";
        }
    } else {
        echo "❌ Tabela 'certificados' NÃO encontrada!\n";
        echo "   Execute o schema SQL: sql/schema.sql\n";
    }
    
} catch (Throwable $t) {
    echo "❌ Erro: " . $t->getMessage() . "\n";
    echo "   Trace: " . $t->getTraceAsString() . "\n";
}

echo "\n=== Fim do Teste ===\n";