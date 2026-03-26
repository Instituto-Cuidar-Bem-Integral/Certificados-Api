<?php
declare(strict_types=1);

/**
 * Mocks centralizados para o módulo de certificados.
 * Remoção futura: basta deletar a pasta `mocks/` e remover o fallback nas páginas.
 *
 * @return array<int,array<string,mixed>>
 */
function mock_certificados_rows(): array
{
    $now = new DateTimeImmutable('now');

    return [
        [
            'id' => 101,
            'hash' => hash('sha256', 'mock-101'),
            'nome' => 'Renata Wagner Bastos',
            'curso' => 'Voluntariado - RH',
            'data_emissao' => '2026-03-18',
            'carga_horaria' => '6',
            'atividade' => 'Auxiliar de Recursos Humanos',
            'instrutor' => 'Yuri Rocha de Jesus',
            'criado_em' => $now->format('Y-m-d H:i:s'),
            'descricao' => 'Atuação no apoio às rotinas do setor de Recursos Humanos, contribuindo para a organização administrativa, gestão de voluntários e suporte aos processos internos do Instituto.',
            'atividades_lista' => [
                'Apoiar as rotinas do setor de Recursos Humanos',
                'Registrar atividades realizadas',
                'Dar suporte em processos de recrutamento',
                'Elaborar relatórios simples',
                'Apoiar demandas administrativas do setor',
            ],
            'competencias_lista' => [
                'Organização e atenção aos detalhes',
                'Comunicação interpessoal',
                'Trabalho em equipe',
                'Responsabilidade e comprometimento',
                'Conhecimento básico de rotinas administrativas de RH',
            ],
            'cidade_uf' => 'Rio de Janeiro / RJ',
            'periodo_inicio' => '2026-03-07',
            'periodo_fim' => '2026-03-18',
            'assinatura_1_nome' => 'Yuri Rocha de Jesus',
            'assinatura_1_cargo' => 'Vice-Presidente Executivo',
            'assinatura_2_nome' => 'Vitor de Souza Silva',
            'assinatura_2_cargo' => 'Presidente Executivo',
            'instituicao' => 'INSTITUTO CUIDAR BEM - INTEGRAL',
        ],
        [
            'id' => 100,
            'hash' => hash('sha256', 'mock-100'),
            'nome' => 'João Pedro Almeida',
            'curso' => 'Apoio Administrativo',
            'data_emissao' => '2026-03-10',
            'carga_horaria' => '12',
            'atividade' => 'Apoio às rotinas administrativas',
            'instrutor' => 'Vitor de Souza Silva',
            'criado_em' => $now->sub(new DateInterval('P2D'))->format('Y-m-d H:i:s'),
            'descricao' => 'Participação em tarefas administrativas e apoio à equipe em rotinas internas, contribuindo para a organização de documentos e atendimento.',
            'atividades_lista' => [
                'Organização de documentos',
                'Apoio em atendimento',
                'Controle de planilhas',
            ],
            'competencias_lista' => [
                'Atenção aos detalhes',
                'Proatividade',
                'Boa comunicação',
            ],
            'cidade_uf' => 'Rio de Janeiro / RJ',
            'periodo_inicio' => '2026-03-01',
            'periodo_fim' => '2026-03-10',
            'assinatura_1_nome' => 'Yuri Rocha de Jesus',
            'assinatura_1_cargo' => 'Vice-Presidente Executivo',
            'assinatura_2_nome' => 'Vitor de Souza Silva',
            'assinatura_2_cargo' => 'Presidente Executivo',
            'instituicao' => 'INSTITUTO CUIDAR BEM - INTEGRAL',
        ],
        [
            'id' => 99,
            'hash' => hash('sha256', 'mock-99'),
            'nome' => 'Maria Clara Souza',
            'curso' => 'Comunicação Institucional',
            'data_emissao' => '2026-02-25',
            'carga_horaria' => null,
            'atividade' => 'Criação de conteúdo e apoio em eventos',
            'instrutor' => null,
            'criado_em' => $now->sub(new DateInterval('P10D'))->format('Y-m-d H:i:s'),
            'descricao' => 'Apoio em comunicação institucional, auxiliando na produção de conteúdo e organização de ações e eventos.',
            'atividades_lista' => [
                'Criação de conteúdo',
                'Apoio em eventos',
            ],
            'competencias_lista' => [
                'Criatividade',
                'Trabalho em equipe',
            ],
            'cidade_uf' => 'Rio de Janeiro / RJ',
            'periodo_inicio' => '2026-02-15',
            'periodo_fim' => '2026-02-25',
            'assinatura_1_nome' => 'Yuri Rocha de Jesus',
            'assinatura_1_cargo' => 'Vice-Presidente Executivo',
            'assinatura_2_nome' => 'Vitor de Souza Silva',
            'assinatura_2_cargo' => 'Presidente Executivo',
            'instituicao' => 'INSTITUTO CUIDAR BEM - INTEGRAL',
        ],
    ];
}

/**
 * @return array<string,mixed>|null
 */
function mock_certificado_by_hash(string $hash): ?array
{
    $hash = trim($hash);
    foreach (mock_certificados_rows() as $row) {
        if ((string)$row['hash'] === $hash) {
            return $row;
        }
    }
    return null;
}
