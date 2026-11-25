<?php
session_start();
date_default_timezone_set('America/Manaus');

$nomeMedica = 'Dra. Fernanda Montenegro';
$emailDemo = 'medico@infomedico.com';
$senhaDemo = '123456';

function e($valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

function ir(string $url): void {
    header('Location: ' . $url);
    exit;
}

function moeda($valor): string {
    return $valor === null || $valor === ''
        ? 'Não informado'
        : 'R$ ' . number_format((float)$valor, 2, ',', '.');
}

function numero($valor, int $casas = 1): string {
    return $valor === null || $valor === ''
        ? 'Não informado'
        : number_format((float)$valor, $casas, ',', '.');
}

function icone(string $nome, string $classe = ''): string {
    return '<svg class="' . e($classe) .
        '" aria-hidden="true"><use href="#icon-' .
        e($nome) . '"></use></svg>';
}

function dataBr(string $data): string {
    $obj = DateTimeImmutable::createFromFormat('!Y-m-d', $data);
    return $obj ? $obj->format('d/m/Y') : $data;
}

function nomeDia(string $data): string {
    $obj = DateTimeImmutable::createFromFormat('!Y-m-d', $data);

    if (!$obj) {
        return $data;
    }

    $dias = [
        'Domingo', 'Segunda-feira', 'Terça-feira',
        'Quarta-feira', 'Quinta-feira',
        'Sexta-feira', 'Sábado'
    ];

    return $dias[(int)$obj->format('w')];
}

function tituloDia(string $data): string {
    return nomeDia($data) . ', ' . dataBr($data);
}

function pacientePorId(array $pacientes, int $id): ?array {
    foreach ($pacientes as $paciente) {
        if ((int)$paciente['id'] === $id) {
            return $paciente;
        }
    }

    return null;
}

function novaNotificacao(
    string $tipo,
    string $titulo,
    string $mensagem,
    string $link
): void {
    $ids = array_column($_SESSION['notificacoes_demo'], 'id');

    array_unshift($_SESSION['notificacoes_demo'], [
        'id' => $ids ? max($ids) + 1 : 1,
        'tipo' => $tipo,
        'titulo' => $titulo,
        'mensagem' => $mensagem,
        'data' => date('d/m/Y'),
        'hora' => date('H:i'),
        'link' => $link,
        'lida' => false
    ]);
}

// ==========================================
// LOGIN
// ==========================================

$erroLogin = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'sair') {
        unset($_SESSION['medico_autenticado']);
        session_regenerate_id(true);
        ir('index.php');
    }

    if ($acao === 'login') {
        $email = trim((string)($_POST['usuario'] ?? ''));
        $senha = (string)($_POST['senha'] ?? '');

        if ($email === $emailDemo && $senha === $senhaDemo) {
            session_regenerate_id(true);
            $_SESSION['medico_autenticado'] = true;
            ir('index.php');
        }

        $erroLogin = 'E-mail ou senha incorretos.';
    }
}

$autenticado = ($_SESSION['medico_autenticado'] ?? false) === true;

// ==========================================
// PACIENTES INICIAIS
// ==========================================

$pacientesIniciais = [
    [
        'id' => 1,
        'nome' => 'Ana Souza',
        'idade' => 42,
        'telefone' => '(92) 99912-3401',
        'email' => 'ana.souza@email.com',
        'peso' => 68.5,
        'altura' => 165,
        'valor_consulta' => 180,
        'status' => 'Inativo',
        'tipo_atendimento' => 'Particular',
        'convenio' => '',
        'carteirinha' => '',
        'validade_carteirinha' => '',
        'forma_pagamento' => 'Pix'
    ],
    [
        'id' => 2,
        'nome' => 'Maria Blina',
        'idade' => 31,
        'telefone' => '(92) 98845-1102',
        'email' => 'maria.blina@email.com',
        'peso' => 60,
        'altura' => 162,
        'valor_consulta' => 200,
        'status' => 'Ativo',
        'tipo_atendimento' => 'Convênio',
        'convenio' => 'Unimed',
        'carteirinha' => '123456789',
        'validade_carteirinha' => '2027-12',
        'forma_pagamento' => ''
    ],
    [
        'id' => 3,
        'nome' => 'Maria G.',
        'idade' => 27,
        'telefone' => '(92) 99974-2203',
        'email' => 'maria.g@email.com',
        'peso' => 57.8,
        'altura' => 160,
        'valor_consulta' => 180,
        'status' => 'Ativo',
        'tipo_atendimento' => 'Convênio',
        'convenio' => 'Bradesco Saúde',
        'carteirinha' => '987654321',
        'validade_carteirinha' => '2028-06',
        'forma_pagamento' => ''
    ],
    [
        'id' => 4,
        'nome' => 'Pedro Hores',
        'idade' => 55,
        'telefone' => '(92) 98422-3388',
        'email' => 'pedro.h@email.com',
        'peso' => 85.2,
        'altura' => 175,
        'valor_consulta' => 220,
        'status' => 'Inativo',
        'tipo_atendimento' => 'Particular',
        'convenio' => '',
        'carteirinha' => '',
        'validade_carteirinha' => '',
        'forma_pagamento' => 'Dinheiro'
    ],
    [
        'id' => 5,
        'nome' => 'Pedro Lima',
        'idade' => 36,
        'telefone' => '(92) 99111-1010',
        'email' => 'pedro.lima@email.com',
        'peso' => 78,
        'altura' => 178,
        'valor_consulta' => 200,
        'status' => 'Ativo',
        'tipo_atendimento' => 'Particular',
        'convenio' => '',
        'carteirinha' => '',
        'validade_carteirinha' => '',
        'forma_pagamento' => 'Cartão de crédito'
    ],
    [
        'id' => 6,
        'nome' => 'Pedro Lima B.',
        'idade' => 29,
        'telefone' => '(92) 98447-3322',
        'email' => 'pedro.b@email.com',
        'peso' => 72.4,
        'altura' => 172,
        'valor_consulta' => 190,
        'status' => 'Ativo',
        'tipo_atendimento' => 'Convênio',
        'convenio' => 'SulAmérica',
        'carteirinha' => '456789123',
        'validade_carteirinha' => '2027-09',
        'forma_pagamento' => ''
    ]
];

// ==========================================
// HISTÓRICO DE CONSULTAS
// ==========================================

$consultasIniciais = [
    1 => [
        [
            'data' => '14/08/2026',
            'hora' => '09:30',
            'tipo' => 'Consulta clínica',
            'situacao' => 'Concluída',
            'descricao' => 'Avaliação de rotina e orientações gerais.'
        ],
        [
            'data' => '03/04/2026',
            'hora' => '10:00',
            'tipo' => 'Retorno',
            'situacao' => 'Concluída',
            'descricao' => 'Acompanhamento de exames.'
        ]
    ],
    2 => [
        [
            'data' => '10/11/2026',
            'hora' => '14:00',
            'tipo' => 'Consulta clínica',
            'situacao' => 'Concluída',
            'descricao' => 'Consulta de acompanhamento.'
        ],
        [
            'data' => '22/11/2026',
            'hora' => '15:30',
            'tipo' => 'Retorno',
            'situacao' => 'Agendada',
            'descricao' => 'Revisão de resultados.'
        ]
    ],
    3 => [
        [
            'data' => '05/11/2026',
            'hora' => '08:15',
            'tipo' => 'Primeira consulta',
            'situacao' => 'Concluída',
            'descricao' => 'Anamnese e avaliação inicial.'
        ]
    ],
    4 => [
        [
            'data' => '12/03/2026',
            'hora' => '11:00',
            'tipo' => 'Consulta clínica',
            'situacao' => 'Concluída',
            'descricao' => 'Avaliação periódica.'
        ]
    ],
    5 => [
        [
            'data' => '12/11/2026',
            'hora' => '16:00',
            'tipo' => 'Consulta clínica',
            'situacao' => 'Concluída',
            'descricao' => 'Revisão de sintomas e orientações.'
        ],
        [
            'data' => '25/11/2026',
            'hora' => '09:00',
            'tipo' => 'Retorno',
            'situacao' => 'Agendada',
            'descricao' => 'Acompanhamento programado.'
        ]
    ],
    6 => [
        [
            'data' => '29/10/2026',
            'hora' => '10:45',
            'tipo' => 'Primeira consulta',
            'situacao' => 'Concluída',
            'descricao' => 'Consulta inicial e orientações.'
        ]
    ]
];

// ==========================================
// PRONTUÁRIOS
// ==========================================

$prontuarioPadrao = [
    'queixa' => 'Não informada.',
    'historico' => 'Histórico clínico não informado.',
    'alergias' => 'Não informado.',
    'medicamentos' => 'Não informado.',
    'observacoes' => 'Nenhuma observação registrada.',
    'conduta' => 'Não informada.'
];

$prontuariosIniciais = [
    1 => [
        'queixa' => 'Avaliação clínica de rotina.',
        'historico' => 'Acompanhamento preventivo.',
        'alergias' => 'Nenhuma alergia informada.',
        'medicamentos' => 'Nenhum medicamento informado.',
        'observacoes' => 'Avaliação geral registrada.',
        'conduta' => 'Acompanhamento periódico.'
    ],
    2 => [
        'queixa' => 'Acompanhamento clínico.',
        'historico' => 'Consultas anteriores de rotina.',
        'alergias' => 'Nenhuma alergia informada.',
        'medicamentos' => 'Não informado.',
        'observacoes' => 'Revisão de exames agendada.',
        'conduta' => 'Retorno para avaliação.'
    ],
    3 => [
        'queixa' => 'Primeira avaliação.',
        'historico' => 'Anamnese inicial.',
        'alergias' => 'Não informado.',
        'medicamentos' => 'Não informado.',
        'observacoes' => 'Primeiro atendimento realizado.',
        'conduta' => 'Acompanhamento conforme necessidade.'
    ],
    4 => [
        'queixa' => 'Avaliação periódica.',
        'historico' => 'Consultas anteriores registradas.',
        'alergias' => 'Não informado.',
        'medicamentos' => 'Não informado.',
        'observacoes' => 'Atendimento de rotina.',
        'conduta' => 'Retorno conforme necessidade.'
    ],
    5 => [
        'queixa' => 'Consulta de acompanhamento.',
        'historico' => 'Retornos periódicos.',
        'alergias' => 'Nenhuma alergia informada.',
        'medicamentos' => 'Não informado.',
        'observacoes' => 'Acompanhamento agendado.',
        'conduta' => 'Reavaliação em consulta futura.'
    ],
    6 => [
        'queixa' => 'Consulta inicial.',
        'historico' => 'Primeiro atendimento.',
        'alergias' => 'Não informado.',
        'medicamentos' => 'Não informado.',
        'observacoes' => 'Registro inicial realizado.',
        'conduta' => 'Acompanhamento de rotina.'
    ]
];

// ==========================================
// AGENDA INICIAL
// ==========================================

$agendaInicial = [
    [
        'id' => 1,
        'paciente_id' => 2,
        'data' => '2026-11-17',
        'hora' => '08:00',
        'tipo' => 'Consulta de rotina',
        'status' => 'Confirmada',
        'observacao' => ''
    ],
    [
        'id' => 2,
        'paciente_id' => 5,
        'data' => '2026-11-17',
        'hora' => '09:30',
        'tipo' => 'Retorno',
        'status' => 'Confirmada',
        'observacao' => ''
    ],
    [
        'id' => 3,
        'paciente_id' => 3,
        'data' => '2026-11-17',
        'hora' => '11:00',
        'tipo' => 'Primeira consulta',
        'status' => 'Pendente',
        'observacao' => ''
    ],
    [
        'id' => 4,
        'paciente_id' => 1,
        'data' => '2026-11-18',
        'hora' => '08:30',
        'tipo' => 'Consulta clínica',
        'status' => 'Pendente',
        'observacao' => ''
    ],
    [
        'id' => 5,
        'paciente_id' => 6,
        'data' => '2026-11-18',
        'hora' => '10:00',
        'tipo' => 'Acompanhamento',
        'status' => 'Confirmada',
        'observacao' => ''
    ],
    [
        'id' => 6,
        'paciente_id' => 4,
        'data' => '2026-11-19',
        'hora' => '14:00',
        'tipo' => 'Consulta clínica',
        'status' => 'Cancelada',
        'observacao' => ''
    ],
    [
        'id' => 7,
        'paciente_id' => 2,
        'data' => '2026-11-20',
        'hora' => '15:30',
        'tipo' => 'Retorno',
        'status' => 'Confirmada',
        'observacao' => ''
    ]
];

// ==========================================
// NOTIFICAÇÕES INICIAIS
// ==========================================

$notificacoesIniciais = [
    [
        'id' => 1,
        'tipo' => 'consulta',
        'titulo' => 'Consulta confirmada',
        'mensagem' => 'Maria Blina confirmou sua consulta.',
        'data' => '23/11/2026',
        'hora' => '09:20',
        'link' => 'index.php?pagina=agenda',
        'lida' => false
    ],
    [
        'id' => 2,
        'tipo' => 'alerta',
        'titulo' => 'Consulta aguardando confirmação',
        'mensagem' => 'Maria G. possui uma consulta pendente.',
        'data' => '23/11/2026',
        'hora' => '08:45',
        'link' => 'index.php?pagina=agenda',
        'lida' => false
    ],
    [
        'id' => 3,
        'tipo' => 'paciente',
        'titulo' => 'Novo paciente cadastrado',
        'mensagem' => 'Pedro Lima B. foi adicionado à lista.',
        'data' => '22/11/2026',
        'hora' => '15:30',
        'link' => 'index.php?pagina=pacientes',
        'lida' => false
    ],
    [
        'id' => 4,
        'tipo' => 'retorno',
        'titulo' => 'Retorno agendado',
        'mensagem' => 'Pedro Lima possui um retorno.',
        'data' => '21/11/2026',
        'hora' => '14:10',
        'link' => 'index.php?historico=5',
        'lida' => true
    ],
    [
        'id' => 5,
        'tipo' => 'sistema',
        'titulo' => 'Bem-vindo ao Infomedico',
        'mensagem' => 'Seu painel está disponível.',
        'data' => '20/11/2026',
        'hora' => '10:00',
        'link' => 'index.php',
        'lida' => true
    ]
];

// ==========================================
// INICIALIZAÇÃO DAS SESSÕES
// ==========================================

if (!isset($_SESSION['pacientes_demo'])) {
    $_SESSION['pacientes_demo'] = $pacientesIniciais;
}

if (!isset($_SESSION['prontuarios_demo'])) {
    $_SESSION['prontuarios_demo'] = $prontuariosIniciais;
}

if (!isset($_SESSION['agenda_demo'])) {
    $_SESSION['agenda_demo'] = $agendaInicial;
}

if (!isset($_SESSION['notificacoes_demo'])) {
    $_SESSION['notificacoes_demo'] = $notificacoesIniciais;
}

if (!isset($_SESSION['consultas_demo'])) {
    $_SESSION['consultas_demo'] = $consultasIniciais;
}

// Mantém cadastros anteriores da sessão.

foreach ($_SESSION['pacientes_demo'] as &$pacienteSessao) {
    foreach ($pacientesIniciais as $inicial) {
        if ((int)$pacienteSessao['id'] === $inicial['id']) {
            foreach ($inicial as $campo => $valor) {
                if (!array_key_exists($campo, $pacienteSessao)) {
                    $pacienteSessao[$campo] = $valor;
                }
            }

            break;
        }
    }

    $padroes = [
        'peso' => null,
        'altura' => null,
        'valor_consulta' => null,
        'tipo_atendimento' => 'Não informado',
        'convenio' => '',
        'carteirinha' => '',
        'validade_carteirinha' => '',
        'forma_pagamento' => ''
    ];

    foreach ($padroes as $campo => $valor) {
        if (!array_key_exists($campo, $pacienteSessao)) {
            $pacienteSessao[$campo] = $valor;
        }
    }
}

unset($pacienteSessao);

// ==========================================
// EXEMPLOS DE CONSULTAS DO DIA
// ==========================================

if (!isset($_SESSION['dashboard_exemplos_criados'])) {
    $hojeExemplo = date('Y-m-d');
    $amanhaExemplo = date('Y-m-d', strtotime('+1 day'));

    $exemplos = [
        [
            'paciente_id' => 2,
            'data' => $hojeExemplo,
            'hora' => '08:30',
            'tipo' => 'Consulta de rotina',
            'status' => 'Confirmada',
            'observacao' => 'Atendimento demonstrativo.'
        ],
        [
            'paciente_id' => 3,
            'data' => $hojeExemplo,
            'hora' => '10:00',
            'tipo' => 'Primeira consulta',
            'status' => 'Pendente',
            'observacao' => ''
        ],
        [
            'paciente_id' => 5,
            'data' => $hojeExemplo,
            'hora' => '14:30',
            'tipo' => 'Retorno',
            'status' => 'Confirmada',
            'observacao' => 'Trazer exames anteriores.'
        ],
        [
            'paciente_id' => 6,
            'data' => $amanhaExemplo,
            'hora' => '09:00',
            'tipo' => 'Acompanhamento',
            'status' => 'Confirmada',
            'observacao' => ''
        ],
        [
            'paciente_id' => 2,
            'data' => $amanhaExemplo,
            'hora' => '11:00',
            'tipo' => 'Consulta clínica',
            'status' => 'Pendente',
            'observacao' => ''
        ]
    ];

    foreach ($exemplos as $exemplo) {
        $ocupado = false;

        foreach ($_SESSION['agenda_demo'] as $existente) {
            if (
                $existente['data'] === $exemplo['data'] &&
                $existente['hora'] === $exemplo['hora'] &&
                $existente['status'] !== 'Cancelada'
            ) {
                $ocupado = true;
                break;
            }
        }

        if ($ocupado) {
            continue;
        }

        $ids = array_column($_SESSION['agenda_demo'], 'id');
        $exemplo['id'] = $ids ? max($ids) + 1 : 1;

        $_SESSION['agenda_demo'][] = $exemplo;
    }

    $_SESSION['dashboard_exemplos_criados'] = true;
}

// ==========================================
// AÇÕES DAS NOTIFICAÇÕES
// ==========================================

if ($autenticado && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'marcar_notificacoes') {
        foreach ($_SESSION['notificacoes_demo'] as &$notificacao) {
            $notificacao['lida'] = true;
        }

        unset($notificacao);
        ir('index.php?pagina=notificacoes');
    }

    if ($acao === 'abrir_notificacao') {
        $id = (int)($_POST['id_notificacao'] ?? 0);
        $destino = 'index.php?pagina=notificacoes';

        foreach ($_SESSION['notificacoes_demo'] as &$notificacao) {
            if ((int)$notificacao['id'] === $id) {
                $notificacao['lida'] = true;
                $destino = $notificacao['link'];
                break;
            }
        }

        unset($notificacao);
        ir($destino);
    }
}

$totalNaoLidas = count(array_filter(
    $_SESSION['notificacoes_demo'],
    fn($n) => !$n['lida']
));

// ==========================================
// CADASTRAR PACIENTE
// ==========================================

$erroCadastro = '';

if (
    $autenticado &&
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    ($_POST['acao'] ?? '') === 'adicionar_paciente'
) {
    $nome = trim((string)($_POST['nome'] ?? ''));
    $idade = filter_var($_POST['idade'] ?? '', FILTER_VALIDATE_INT);
    $peso = filter_var($_POST['peso'] ?? '', FILTER_VALIDATE_FLOAT);
    $altura = filter_var($_POST['altura'] ?? '', FILTER_VALIDATE_FLOAT);
    $valor = filter_var($_POST['valor_consulta'] ?? '', FILTER_VALIDATE_FLOAT);

    $telefone = trim((string)($_POST['telefone'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));

    $tipoAtendimento = (string)($_POST['tipo_atendimento'] ?? '');
    $convenio = trim((string)($_POST['convenio'] ?? ''));
    $carteirinha = trim((string)($_POST['carteirinha'] ?? ''));
    $validade = trim((string)($_POST['validade_carteirinha'] ?? ''));
    $pagamento = trim((string)($_POST['forma_pagamento'] ?? ''));

    $status = ($_POST['status'] ?? '') === 'Inativo'
        ? 'Inativo'
        : 'Ativo';

    $formas = [
        'Pix', 'Dinheiro',
        'Cartão de crédito', 'Cartão de débito'
    ];

    if (
        $nome === '' ||
        $idade === false || $idade < 0 || $idade > 120 ||
        $peso === false || $peso <= 0 ||
        $altura === false || $altura <= 0 ||
        $valor === false || $valor < 0 ||
        ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL))
    ) {
        $erroCadastro = 'Confira os dados pessoais e o valor da consulta.';
    } elseif (!in_array($tipoAtendimento, ['Convênio', 'Particular'], true)) {
        $erroCadastro = 'Selecione Convênio ou Particular.';
    } elseif (
        $tipoAtendimento === 'Convênio' &&
        ($convenio === '' || $carteirinha === '')
    ) {
        $erroCadastro = 'Informe o convênio e a carteirinha.';
    } elseif (
        $tipoAtendimento === 'Convênio' &&
        $validade !== '' &&
        !preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $validade)
    ) {
        $erroCadastro = 'Validade da carteirinha inválida.';
    } elseif (
        $tipoAtendimento === 'Particular' &&
        !in_array($pagamento, $formas, true)
    ) {
        $erroCadastro = 'Selecione uma forma de pagamento.';
    } else {
        $ids = array_column($_SESSION['pacientes_demo'], 'id');
        $novoId = $ids ? max($ids) + 1 : 1;

        $_SESSION['pacientes_demo'][] = [
            'id' => $novoId,
            'nome' => $nome,
            'idade' => $idade,
            'telefone' => $telefone,
            'email' => $email,
            'peso' => $peso,
            'altura' => $altura,
            'valor_consulta' => $valor,
            'status' => $status,
            'tipo_atendimento' => $tipoAtendimento,
            'convenio' => $tipoAtendimento === 'Convênio' ? $convenio : '',
            'carteirinha' => $tipoAtendimento === 'Convênio' ? $carteirinha : '',
            'validade_carteirinha' => $tipoAtendimento === 'Convênio'
                ? $validade : '',
            'forma_pagamento' => $tipoAtendimento === 'Particular'
                ? $pagamento : ''
        ];

        $_SESSION['prontuarios_demo'][$novoId] = $prontuarioPadrao;
        $_SESSION['consultas_demo'][$novoId] = [];

        novaNotificacao(
            'paciente',
            'Novo paciente cadastrado',
            $nome . ' foi cadastrado no Infomedico.',
            'index.php?dados=' . $novoId
        );

        ir('index.php?pagina=pacientes&cadastrado=1');
    }
}

// ==========================================
// NOVO AGENDAMENTO
// ==========================================

$erroAgenda = '';

$tiposConsulta = [
    'Primeira consulta',
    'Consulta clínica',
    'Consulta de rotina',
    'Retorno',
    'Acompanhamento',
    'Avaliação de exames'
];

if (
    $autenticado &&
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    ($_POST['acao'] ?? '') === 'criar_agendamento'
) {
    $pacienteId = (int)($_POST['paciente_id'] ?? 0);
    $data = (string)($_POST['data_agendamento'] ?? '');
    $hora = (string)($_POST['hora_agendamento'] ?? '');
    $tipo = (string)($_POST['tipo_consulta'] ?? '');
    $status = (string)($_POST['status_agendamento'] ?? '');
    $observacao = trim((string)($_POST['observacao'] ?? ''));

    $paciente = pacientePorId(
        $_SESSION['pacientes_demo'],
        $pacienteId
    );

    $dataObj = DateTimeImmutable::createFromFormat('!Y-m-d', $data);

    $dataValida = $dataObj &&
        $dataObj->format('Y-m-d') === $data;

    $horaValida = preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $hora);
    $conflito = false;

    foreach ($_SESSION['agenda_demo'] as $item) {
        if (
            $item['data'] === $data &&
            $item['hora'] === $hora &&
            $item['status'] !== 'Cancelada'
        ) {
            $conflito = true;
            break;
        }
    }

    if (!$paciente) {
        $erroAgenda = 'Selecione um paciente cadastrado.';
    } elseif (!$dataValida || !$horaValida) {
        $erroAgenda = 'Informe uma data e um horário válidos.';
    } elseif ($data < date('Y-m-d')) {
        $erroAgenda = 'Escolha uma data de hoje em diante.';
    } elseif (
        !in_array($tipo, $tiposConsulta, true) ||
        !in_array($status, ['Pendente', 'Confirmada'], true)
    ) {
        $erroAgenda = 'Confira o tipo e o status da consulta.';
    } elseif ($conflito) {
        $erroAgenda = 'Já existe uma consulta nesse dia e horário.';
    } elseif (strlen($observacao) > 500) {
        $erroAgenda = 'A observação deve ter até 500 caracteres.';
    } else {
        $ids = array_column($_SESSION['agenda_demo'], 'id');
        $novoId = $ids ? max($ids) + 1 : 1;

        $_SESSION['agenda_demo'][] = [
            'id' => $novoId,
            'paciente_id' => $pacienteId,
            'data' => $data,
            'hora' => $hora,
            'tipo' => $tipo,
            'status' => $status,
            'observacao' => $observacao
        ];

        novaNotificacao(
            'consulta',
            'Novo agendamento',
            $paciente['nome'] . ' — ' .
                dataBr($data) . ' às ' . $hora,
            'index.php?pagina=agenda&data=' . $data
        );

        ir('index.php?pagina=agenda&agendado=1&data=' . $data);
    }
}

// ==========================================
// CONFIRMAR OU CANCELAR CONSULTA
// ==========================================

if (
    $autenticado &&
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    ($_POST['acao'] ?? '') === 'alterar_status_agenda'
) {
    $id = (int)($_POST['agendamento_id'] ?? 0);
    $novoStatus = (string)($_POST['novo_status'] ?? '');
    $origem = (string)($_POST['origem'] ?? 'agenda');

    if (in_array($novoStatus, ['Confirmada', 'Cancelada'], true)) {
        foreach ($_SESSION['agenda_demo'] as &$item) {
            if (
                (int)$item['id'] === $id &&
                $item['status'] !== 'Cancelada' &&
                $item['status'] !== $novoStatus
            ) {
                $ocupado = false;

                if ($novoStatus === 'Confirmada') {
                    foreach ($_SESSION['agenda_demo'] as $outro) {
                        if (
                            (int)$outro['id'] !== $id &&
                            $outro['data'] === $item['data'] &&
                            $outro['hora'] === $item['hora'] &&
                            $outro['status'] !== 'Cancelada'
                        ) {
                            $ocupado = true;
                            break;
                        }
                    }
                }

                if ($ocupado) {
                    break;
                }

                $item['status'] = $novoStatus;

                $paciente = pacientePorId(
                    $_SESSION['pacientes_demo'],
                    (int)$item['paciente_id']
                );

                novaNotificacao(
                    $novoStatus === 'Cancelada' ? 'alerta' : 'consulta',
                    $novoStatus === 'Cancelada'
                        ? 'Consulta cancelada'
                        : 'Consulta confirmada',
                    ($paciente['nome'] ?? 'Paciente') .
                        ' — ' . dataBr($item['data']) .
                        ' às ' . $item['hora'],
                    'index.php?pagina=agenda&data=' . $item['data']
                );

                break;
            }
        }

        unset($item);
    }

    ir($origem === 'inicio' ? 'index.php' : 'index.php?pagina=agenda');
}

// ==========================================
// PACIENTE SELECIONADO
// ==========================================

$busca = trim((string)($_GET['busca'] ?? ''));

$pacientesFiltrados = $_SESSION['pacientes_demo'];

if ($busca !== '') {
    $pacientesFiltrados = array_filter(
        $pacientesFiltrados,
        fn($p) => stripos($p['nome'], $busca) !== false
    );
}

$idSelecionado = (int)(
    $_GET['dados'] ??
    $_GET['historico'] ??
    $_GET['prontuario'] ??
    0
);

$pacienteSelecionado = pacientePorId(
    $_SESSION['pacientes_demo'],
    $idSelecionado
);

$historico = $_SESSION['consultas_demo'][$idSelecionado] ?? [];

$prontuario = $_SESSION['prontuarios_demo'][$idSelecionado]
    ?? $prontuarioPadrao;

// ==========================================
// CONTROLE DAS TELAS
// ==========================================

$pagina = (string)($_GET['pagina'] ?? 'inicio');

$mostrarCadastro = isset($_GET['novo']) || $erroCadastro !== '';

$mostrarDados = isset($_GET['dados']) &&
    $pacienteSelecionado !== null;

$mostrarHistorico = isset($_GET['historico']) &&
    $pacienteSelecionado !== null;

$mostrarProntuario = isset($_GET['prontuario']) &&
    $pacienteSelecionado !== null;

$mostrarAgenda = $pagina === 'agenda';
$mostrarNotificacoes = $pagina === 'notificacoes';

$mostrarNovoAgendamento = $mostrarAgenda &&
    (isset($_GET['novo_agendamento']) || $erroAgenda !== '');

$mostrarInicio = $pagina === 'inicio' &&
    !$mostrarCadastro &&
    !$mostrarDados &&
    !$mostrarHistorico &&
    !$mostrarProntuario;

$mostrarPacientes = !$mostrarInicio &&
    !$mostrarAgenda &&
    !$mostrarNotificacoes;

$tituloPagina = 'INÍCIO';

if ($mostrarAgenda) $tituloPagina = 'AGENDA';
if ($mostrarNotificacoes) $tituloPagina = 'NOTIFICAÇÕES';
if ($mostrarPacientes) $tituloPagina = 'PACIENTES';
if ($mostrarDados) $tituloPagina = 'DADOS PESSOAIS';
if ($mostrarHistorico) $tituloPagina = 'HISTÓRICO';
if ($mostrarProntuario) $tituloPagina = 'PRONTUÁRIO';

// ==========================================
// DADOS DO DASHBOARD
// ==========================================

$hoje = date('Y-m-d');
$agora = date('H:i');
$fimDaSemana = date('Y-m-d', strtotime('+7 days'));

$consultasHoje = array_values(array_filter(
    $_SESSION['agenda_demo'],
    fn($a) => $a['data'] === $hoje
));

usort(
    $consultasHoje,
    fn($a, $b) => strcmp($a['hora'], $b['hora'])
);

$totalHoje = count($consultasHoje);

$confirmadasHoje = count(array_filter(
    $consultasHoje,
    fn($a) => $a['status'] === 'Confirmada'
));

$pendentesHoje = count(array_filter(
    $consultasHoje,
    fn($a) => $a['status'] === 'Pendente'
));

$canceladasHoje = count(array_filter(
    $consultasHoje,
    fn($a) => $a['status'] === 'Cancelada'
));

$pendenciasHoje = array_values(array_filter(
    $consultasHoje,
    fn($a) => $a['status'] === 'Pendente'
));

$proximoAtendimento = null;

foreach ($consultasHoje as $item) {
    if (
        $item['status'] !== 'Cancelada' &&
        $item['hora'] >= $agora
    ) {
        $proximoAtendimento = $item;
        break;
    }
}

$totalPacientes = count($_SESSION['pacientes_demo']);

$pacientesAtivos = count(array_filter(
    $_SESSION['pacientes_demo'],
    fn($p) => $p['status'] === 'Ativo'
));

$proximosSeteDias = count(array_filter(
    $_SESSION['agenda_demo'],
    fn($a) =>
        $a['data'] > $hoje &&
        $a['data'] <= $fimDaSemana &&
        $a['status'] !== 'Cancelada'
));

// ==========================================
// AGENDA AGRUPADA POR DIA
// ==========================================

$filtroData = (string)($_GET['data'] ?? '');
$filtroStatus = (string)($_GET['status'] ?? '');

$agendaFiltrada = array_values(array_filter(
    $_SESSION['agenda_demo'],
    fn($a) =>
        ($filtroData === '' || $a['data'] === $filtroData) &&
        ($filtroStatus === '' || $a['status'] === $filtroStatus)
));

usort(
    $agendaFiltrada,
    fn($a, $b) => strcmp(
        $a['data'] . ' ' . $a['hora'],
        $b['data'] . ' ' . $b['hora']
    )
);

$agendaPorDia = [];

foreach ($agendaFiltrada as $item) {
    $agendaPorDia[$item['data']][] = $item;
}

$totalAgenda = count($agendaFiltrada);

$confirmadasAgenda = count(array_filter(
    $agendaFiltrada,
    fn($a) => $a['status'] === 'Confirmada'
));

$pendentesAgenda = count(array_filter(
    $agendaFiltrada,
    fn($a) => $a['status'] === 'Pendente'
));

$canceladasAgenda = count(array_filter(
    $agendaFiltrada,
    fn($a) => $a['status'] === 'Cancelada'
));
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Infomedico | <?= e($nomeMedica) ?></title>
    <link rel="stylesheet" href="./style.css">
</head>

<body class="<?= !$autenticado ? 'pagina-login' : '' ?>">

<!-- ==========================================
     ÍCONES SVG
========================================== -->

<svg class="svg-biblioteca" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">

    <symbol id="icon-medical" viewBox="0 0 24 24">
        <rect x="3" y="3" width="18" height="18" rx="5"/>
        <path d="M12 7v10M7 12h10"/>
    </symbol>

    <symbol id="icon-home" viewBox="0 0 24 24">
        <path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z"/>
        <path d="M9 21v-8h6v8"/>
    </symbol>

    <symbol id="icon-calendar" viewBox="0 0 24 24">
        <rect x="3" y="5" width="18" height="16" rx="2"/>
        <path d="M16 3v4M8 3v4M3 10h18"/>
        <path d="m9 16 2 2 4-4"/>
    </symbol>

    <symbol id="icon-users" viewBox="0 0 24 24">
        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
        <path d="M22 21v-2a4 4 0 0 1 3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
    </symbol>

    <symbol id="icon-bell" viewBox="0 0 24 24">
        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
        <path d="M10 21h4"/>
    </symbol>

    <symbol id="icon-logout" viewBox="0 0 24 24">
        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
        <path d="m16 17 5-5-5-5M21 12H9"/>
    </symbol>

    <symbol id="icon-search" viewBox="0 0 24 24">
        <circle cx="11" cy="11" r="8"/>
        <path d="m21 21-4.35-4.35"/>
    </symbol>

    <symbol id="icon-file" viewBox="0 0 24 24">
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
        <path d="M14 2v6h6M8 13h8M8 17h8"/>
    </symbol>

    <symbol id="icon-id" viewBox="0 0 24 24">
        <rect x="2" y="4" width="20" height="16" rx="2"/>
        <circle cx="8" cy="10" r="2"/>
        <path d="M4.5 17a3.5 3.5 0 0 1 7 0M14 9h5M14 13h5"/>
    </symbol>

    <symbol id="icon-plus" viewBox="0 0 24 24">
        <path d="M12 5v14M5 12h14"/>
    </symbol>

    <symbol id="icon-arrow-right" viewBox="0 0 24 24">
        <path d="M5 12h14m-6-6 6 6-6 6"/>
    </symbol>

    <symbol id="icon-arrow-left" viewBox="0 0 24 24">
        <path d="M19 12H5m6 6-6-6 6-6"/>
    </symbol>

    <symbol id="icon-check" viewBox="0 0 24 24">
        <path d="m5 12 4 4L19 6"/>
    </symbol>

    <symbol id="icon-user-plus" viewBox="0 0 24 24">
        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
        <path d="M19 8v6M16 11h6"/>
    </symbol>

    <symbol id="icon-alert" viewBox="0 0 24 24">
        <path d="m12 3-10 18h20L12 3z"/>
        <path d="M12 9v5M12 18h.01"/>
    </symbol>

    <symbol id="icon-refresh" viewBox="0 0 24 24">
        <path d="M20 7v5h-5M4 17v-5h5"/>
        <path d="M5 9a8 8 0 0 1 14-2M19 15a8 8 0 0 1-14 2"/>
    </symbol>

    <symbol id="icon-info" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"/>
        <path d="M12 11v5M12 7h.01"/>
    </symbol>

    <symbol id="icon-chevron-right" viewBox="0 0 24 24">
        <path d="m9 18 6-6-6-6"/>
    </symbol>

    <symbol id="icon-heart" viewBox="0 0 24 24">
        <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8z"/>
    </symbol>

    <symbol id="icon-phone" viewBox="0 0 24 24">
        <path d="M22 16.9v3a2 2 0 0 1-2.2 2A19 19 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7l.5 3a2 2 0 0 1-.6 1.8L7 10a16 16 0 0 0 7 7l1.5-2a2 2 0 0 1 1.8-.6l3 .5a2 2 0 0 1 1.7 2z"/>
    </symbol>

    <symbol id="icon-card" viewBox="0 0 24 24">
        <rect x="2" y="5" width="20" height="14" rx="2"/>
        <path d="M2 10h20M6 15h5"/>
    </symbol>

    <symbol id="icon-money" viewBox="0 0 24 24">
        <rect x="2" y="5" width="20" height="14" rx="2"/>
        <circle cx="12" cy="12" r="3"/>
        <path d="M6 9h.01M18 15h.01"/>
    </symbol>

    <symbol id="icon-clipboard" viewBox="0 0 24 24">
        <rect x="4" y="4" width="16" height="18" rx="2"/>
        <rect x="9" y="2" width="6" height="4" rx="1"/>
        <path d="M8 11h8M8 15h8M8 19h5"/>
    </symbol>

    <symbol id="icon-activity" viewBox="0 0 24 24">
        <path d="M3 12h4l3-7 4 14 3-7h4"/>
    </symbol>

    <symbol id="icon-pill" viewBox="0 0 24 24">
        <path d="M10 3a5 5 0 0 1 7 0l4 4a5 5 0 0 1 0 7l-7 7a5 5 0 0 1-7 0l-4-4a5 5 0 0 1 0-7z"/>
        <path d="m8 8 8 8"/>
    </symbol>

    <symbol id="icon-list" viewBox="0 0 24 24">
        <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
    </symbol>

    <symbol id="icon-clock" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="9"/>
        <path d="M12 7v5l3 2"/>
    </symbol>

    <symbol id="icon-x" viewBox="0 0 24 24">
        <path d="M18 6 6 18M6 6l12 12"/>
    </symbol>

    <symbol id="icon-arrow-up-right" viewBox="0 0 24 24">
        <path d="M7 17 17 7M8 7h9v9"/>
    </symbol>
</svg>

<?php if (!$autenticado): ?>

    <!-- ======================================
         LOGIN
    ======================================= -->

    <main class="login-layout">

        <section class="login-apresentacao">

            <div class="login-marca">

                <img src="./logo_png.jpeg" alt=" Logo Infomedico" class="logo-imagem">

                <div>
                    <strong>INFOMEDICO</strong>
                    <small>TECNOLOGIA E SAÚDE EM SUAS MÃOS</small>
                </div>

            </div>

            <div class="login-mensagem">

                <span class="login-tag">ÁREA DO PROFISSIONAL</span>

                <h1>
                    Cuidar de pessoas começa com uma boa organização.
                </h1>

                <p>
                    Seus pacientes, consultas e compromissos
                    em um único lugar.
                </p>

            </div>

            <span class="login-rodape">
                Infomedico • Ambiente de demonstração
            </span>

        </section>

        <section class="login-formulario-area">

            <div class="login-card">

                <div class="login-icone">
                    <?= icone('medical') ?>
                </div>

                <span class="sobrelinha">BEM-VINDA DE VOLTA</span>

                <h2>Acesse sua conta</h2>

                <p class="login-subtitulo">
                    Entre com seus dados para acessar o painel médico.
                </p>

                <?php if ($erroLogin !== ''): ?>

                    <div class="alerta erro" role="alert">
                        <?= e($erroLogin) ?>
                    </div>

                <?php endif; ?>

                <form
                    method="POST"
                    action="index.php"
                    class="login-form"
                >

                    <input type="hidden" name="acao" value="login">

                    <label for="usuario">E-mail</label>

                    <input
                        type="email"
                        id="usuario"
                        name="usuario"
                        placeholder="medico@infomedico.com"
                        autocomplete="username"
                        required
                    >

                    <label for="senha">Senha</label>

                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        placeholder="Digite sua senha"
                        autocomplete="current-password"
                        required
                    >

                    <button type="submit" class="login-entrar">
                        ENTRAR
                        <?= icone('arrow-right', 'icone-botao') ?>
                    </button>

                </form>

                <div class="login-demo">

                    <strong>Dados para testar:</strong>

                    <span>E-mail: <?= e($emailDemo) ?></span>
                    <span>Senha: <?= e($senhaDemo) ?></span>

                </div>

            </div>

        </section>

    </main>

<?php else: ?>

    <!-- ======================================
         CABEÇALHO
    ======================================= -->

    <header class="cabecalho">

        <h1>
            <?= e(strtoupper($nomeMedica)) ?>
        </h1>

        <div class="logo">

            <img src="./logo_png.jpeg" alt="Logo Infomedico" class="logo-imagem">

            <div>
                <strong>INFOMEDICO</strong>
                <small>TECNOLOGIA E SAÚDE EM SUAS MÃOS</small>
            </div>

        </div>

    </header>

    <div class="estrutura">

        <!-- ==================================
             MENU LATERAL
        =================================== -->

        <aside class="menu-lateral">

            <nav aria-label="Menu principal">

                <a
                    href="index.php"
                    class="menu-item <?= $mostrarInicio ? 'selecionado' : '' ?>"
                >
                    <?= icone('home', 'menu-icone') ?>
                    <span>Início</span>
                </a>

                <a
                    href="index.php?pagina=agenda"
                    class="menu-item <?= $mostrarAgenda ? 'selecionado' : '' ?>"
                >
                    <?= icone('calendar', 'menu-icone') ?>
                    <span>Agenda</span>
                </a>

                <a
                    href="index.php?pagina=pacientes"
                    class="menu-item <?= $mostrarPacientes ? 'selecionado' : '' ?>"
                >
                    <?= icone('users', 'menu-icone') ?>
                    <span>Pacientes</span>
                </a>

                <a
                    href="index.php?pagina=notificacoes"
                    class="menu-item <?= $mostrarNotificacoes ? 'selecionado' : '' ?>"
                >
                    <?= icone('bell', 'menu-icone') ?>
                    <span>Notificações</span>

                    <?php if ($totalNaoLidas > 0): ?>

                        <span class="notificacao-contador">
                            <?= $totalNaoLidas ?>
                        </span>

                    <?php endif; ?>

                </a>

            </nav>

            <form
                method="POST"
                action="index.php"
                class="form-sair"
            >

                <input type="hidden" name="acao" value="sair">

                <button type="submit">

                    <?= icone('logout', 'menu-icone') ?>

                    <span>Sair da conta</span>

                </button>

            </form>

        </aside>

        <main class="conteudo">

            <?php if ($mostrarInicio): ?>

                <!-- ==========================
                     DASHBOARD INICIAL
                =========================== -->

                <section class="dashboard-boas-vindas">

                    <div class="boas-vindas-texto">

                        <span class="sobrelinha">
                            PAINEL DO CONSULTÓRIO
                        </span>

                        <h2>
                            Bem-vinda, <?= e($nomeMedica) ?>!
                        </h2>

                        <p>
                            Acompanhe suas consultas e organize
                            os atendimentos do dia.
                        </p>

                        <div class="dashboard-data">

                            <?= icone('calendar', 'icone-botao') ?>

                            <?= e(tituloDia($hoje)) ?>

                        </div>

                    </div>

                    <div class="boas-vindas-icone">
                        <?= icone('medical') ?>
                    </div>

                </section>

                <!-- RESUMO DO DIA -->

                <div class="dashboard-secao-cabecalho">

                    <div>
                        <span class="sobrelinha">RESUMO DIÁRIO</span>
                        <h2>Visão geral de hoje</h2>
                    </div>

                    <a
                        href="index.php?pagina=agenda&data=<?= e($hoje) ?>"
                        class="link-azul"
                    >
                        Ver agenda do dia
                        <?= icone('arrow-right', 'icone-botao') ?>
                    </a>

                </div>

                <section class="dashboard-indicadores">

                    <article class="painel indicador-card">

                        <span class="indicador-card-icone azul">
                            <?= icone('calendar') ?>
                        </span>

                        <span class="indicador-card-label">
                            Consultas hoje
                        </span>

                        <strong><?= $totalHoje ?></strong>

                        <small>Total de agendamentos</small>

                    </article>

                    <article class="painel indicador-card">

                        <span class="indicador-card-icone verde">
                            <?= icone('check') ?>
                        </span>

                        <span class="indicador-card-label">
                            Confirmadas
                        </span>

                        <strong><?= $confirmadasHoje ?></strong>

                        <small>Consultas confirmadas</small>

                    </article>

                    <article class="painel indicador-card">

                        <span class="indicador-card-icone laranja">
                            <?= icone('clock') ?>
                        </span>

                        <span class="indicador-card-label">
                            Pendentes
                        </span>

                        <strong><?= $pendentesHoje ?></strong>

                        <small>Aguardando confirmação</small>

                    </article>

                    <article class="painel indicador-card">

                        <span class="indicador-card-icone vermelho">
                            <?= icone('x') ?>
                        </span>

                        <span class="indicador-card-label">
                            Canceladas
                        </span>

                        <strong><?= $canceladasHoje ?></strong>

                        <small>Consultas canceladas</small>

                    </article>

                </section>

                <section class="dashboard-grid-principal">

                    <div class="dashboard-coluna-principal">

                        <!-- CONSULTAS DE HOJE -->

                        <section class="painel dashboard-painel">

                            <div class="dashboard-painel-cabecalho">

                                <div>
                                    <span class="sobrelinha">
                                        ATENDIMENTOS
                                    </span>

                                    <h3>Consultas de hoje</h3>
                                </div>

                                <span class="dashboard-contador">
                                    <?= $totalHoje ?> consulta(s)
                                </span>

                            </div>

                            <?php if ($consultasHoje): ?>

                                <div class="dashboard-consultas">

                                    <?php foreach ($consultasHoje as $consulta): ?>

                                        <?php
                                        $p = pacientePorId(
                                            $_SESSION['pacientes_demo'],
                                            (int)$consulta['paciente_id']
                                        );
                                        ?>

                                        <article class="dashboard-consulta">

                                            <div class="dashboard-consulta-hora">

                                                <?= icone('clock', 'icone-botao') ?>

                                                <strong>
                                                    <?= e($consulta['hora']) ?>
                                                </strong>

                                            </div>

                                            <div class="dashboard-consulta-info">

                                                <?php if ($p): ?>

                                                    <a
                                                        href="index.php?dados=<?= (int)$p['id'] ?>"
                                                        class="link-paciente"
                                                    >
                                                        <?= e($p['nome']) ?>
                                                    </a>

                                                <?php else: ?>

                                                    <strong>
                                                        Paciente não encontrado
                                                    </strong>

                                                <?php endif; ?>

                                                <small>
                                                    <?= e($consulta['tipo']) ?>
                                                </small>

                                                <?php if ($consulta['hora'] < $agora): ?>

                                                    <span class="consulta-horario-passado">
                                                        Horário já passou
                                                    </span>

                                                <?php endif; ?>

                                            </div>

                                            <div class="dashboard-consulta-lateral">

                                                <span class="agenda-status <?= strtolower($consulta['status']) ?>">
                                                    <?= e($consulta['status']) ?>
                                                </span>

                                                <?php if ($p): ?>

                                                    <a
                                                        href="index.php?historico=<?= (int)$p['id'] ?>"
                                                        class="dashboard-link-historico"
                                                    >
                                                        Histórico
                                                    </a>

                                                <?php endif; ?>

                                            </div>

                                        </article>

                                    <?php endforeach; ?>

                                </div>

                            <?php else: ?>

                                <div class="dashboard-vazio">

                                    <?= icone('calendar') ?>

                                    <h4>
                                        Nenhuma consulta para hoje
                                    </h4>

                                    <p>
                                        Você pode criar um agendamento
                                        para organizar seu dia.
                                    </p>

                                    <a
                                        href="index.php?pagina=agenda&novo_agendamento=1"
                                        class="botao-primario"
                                    >
                                        Novo agendamento
                                    </a>

                                </div>

                            <?php endif; ?>

                            <div class="dashboard-painel-rodape">

                                <a
                                    href="index.php?pagina=agenda&data=<?= e($hoje) ?>"
                                    class="link-azul"
                                >
                                    Visualizar agenda completa
                                    <?= icone('arrow-right', 'icone-botao') ?>
                                </a>

                            </div>

                        </section>

                        <!-- PENDÊNCIAS -->

                        <section class="painel dashboard-painel">

                            <div class="dashboard-painel-cabecalho">

                                <div>
                                    <span class="sobrelinha">
                                        ACOMPANHAMENTO
                                    </span>

                                    <h3>
                                        Consultas aguardando confirmação
                                    </h3>
                                </div>

                                <span class="dashboard-contador">
                                    <?= $pendentesHoje ?> pendente(s)
                                </span>

                            </div>

                            <?php if ($pendenciasHoje): ?>

                                <div class="dashboard-pendencias">

                                    <?php foreach ($pendenciasHoje as $consulta): ?>

                                        <?php
                                        $p = pacientePorId(
                                            $_SESSION['pacientes_demo'],
                                            (int)$consulta['paciente_id']
                                        );
                                        ?>

                                        <div class="dashboard-pendencia">

                                            <span class="pendencia-icone">
                                                <?= icone('alert') ?>
                                            </span>

                                            <div class="pendencia-info">

                                                <strong>
                                                    <?= e($p['nome'] ?? 'Paciente') ?>
                                                </strong>

                                                <small>
                                                    Hoje às
                                                    <?= e($consulta['hora']) ?>
                                                    • <?= e($consulta['tipo']) ?>
                                                </small>

                                            </div>

                                            <form
                                                method="POST"
                                                action="index.php"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="acao"
                                                    value="alterar_status_agenda"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="agendamento_id"
                                                    value="<?= (int)$consulta['id'] ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="novo_status"
                                                    value="Confirmada"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="origem"
                                                    value="inicio"
                                                >

                                                <button
                                                    type="submit"
                                                    class="botao-confirmar-dashboard"
                                                >
                                                    <?= icone('check', 'icone-botao') ?>
                                                    Confirmar
                                                </button>

                                            </form>

                                        </div>

                                    <?php endforeach; ?>

                                </div>

                            <?php else: ?>

                                <div class="dashboard-mensagem">

                                    <?= icone('check') ?>

                                    <div>
                                        <strong>Tudo em dia!</strong>

                                        <p>
                                            Não há consultas pendentes
                                            para hoje.
                                        </p>
                                    </div>

                                </div>

                            <?php endif; ?>

                        </section>

                    </div>

                    <!-- COLUNA LATERAL -->

                    <aside class="dashboard-coluna-lateral">

                        <!-- PRÓXIMO ATENDIMENTO -->

                        <section class="painel proximo-atendimento">

                            <span class="sobrelinha">
                                PRÓXIMO ATENDIMENTO
                            </span>

                            <div class="proximo-icone">
                                <?= icone('clock') ?>
                            </div>

                            <?php if ($proximoAtendimento): ?>

                                <?php
                                $proximoPaciente = pacientePorId(
                                    $_SESSION['pacientes_demo'],
                                    (int)$proximoAtendimento['paciente_id']
                                );
                                ?>

                                <span class="proximo-hora">
                                    <?= e($proximoAtendimento['hora']) ?>
                                </span>

                                <h3>
                                    <?= e($proximoPaciente['nome'] ?? 'Paciente') ?>
                                </h3>

                                <p>
                                    <?= e($proximoAtendimento['tipo']) ?>
                                </p>

                                <span class="agenda-status <?= strtolower($proximoAtendimento['status']) ?>">
                                    <?= e($proximoAtendimento['status']) ?>
                                </span>

                                <a
                                    href="index.php?pagina=agenda&data=<?= e($hoje) ?>"
                                    class="botao-primario proximo-botao"
                                >
                                    Ver na agenda
                                </a>

                            <?php else: ?>

                                <h3>
                                    Sem próximos atendimentos
                                </h3>

                                <p>
                                    Não há mais consultas agendadas
                                    para o restante do dia.
                                </p>

                                <a
                                    href="index.php?pagina=agenda"
                                    class="botao-secundario proximo-botao"
                                >
                                    Ver agenda
                                </a>

                            <?php endif; ?>

                        </section>

                        <!-- AÇÕES RÁPIDAS -->

                        <section class="painel acoes-rapidas">

                            <span class="sobrelinha">
                                ACESSO RÁPIDO
                            </span>

                            <h3>Ações rápidas</h3>

                            <?php
                            $acoesRapidas = [
                                [
                                    'link' => 'index.php?pagina=agenda&novo_agendamento=1',
                                    'icone' => 'calendar',
                                    'titulo' => 'Novo agendamento',
                                    'descricao' => 'Marcar uma consulta'
                                ],
                                [
                                    'link' => 'index.php?novo=1',
                                    'icone' => 'user-plus',
                                    'titulo' => 'Cadastrar paciente',
                                    'descricao' => 'Novo registro'
                                ],
                                [
                                    'link' => 'index.php?pagina=pacientes',
                                    'icone' => 'users',
                                    'titulo' => 'Ver pacientes',
                                    'descricao' => 'Consultar cadastros'
                                ],
                                [
                                    'link' => 'index.php?pagina=notificacoes',
                                    'icone' => 'bell',
                                    'titulo' => 'Notificações',
                                    'descricao' => $totalNaoLidas . ' não lida(s)'
                                ]
                            ];
                            ?>

                            <?php foreach ($acoesRapidas as $acao): ?>

                                <a
                                    href="<?= e($acao['link']) ?>"
                                    class="acao-rapida"
                                >

                                    <span class="acao-rapida-icone">
                                        <?= icone($acao['icone']) ?>
                                    </span>

                                    <span>
                                        <strong>
                                            <?= e($acao['titulo']) ?>
                                        </strong>

                                        <small>
                                            <?= e($acao['descricao']) ?>
                                        </small>
                                    </span>

                                    <?= icone('chevron-right', 'acao-seta') ?>

                                </a>

                            <?php endforeach; ?>

                        </section>

                    </aside>

                </section>

                <!-- INDICADORES ADICIONAIS -->

                <section class="dashboard-inferior">

                    <article class="painel dashboard-info-card">

                        <span class="dashboard-info-icone">
                            <?= icone('users') ?>
                        </span>

                        <div>

                            <span class="sobrelinha">PACIENTES</span>

                            <strong>
                                <?= $pacientesAtivos ?>
                                <span>ativos</span>
                            </strong>

                            <p>
                                <?= $totalPacientes ?> cadastrados no total
                            </p>

                        </div>

                        <a
                            href="index.php?pagina=pacientes"
                            aria-label="Visualizar pacientes"
                        >
                            <?= icone('arrow-up-right') ?>
                        </a>

                    </article>

                    <article class="painel dashboard-info-card">

                        <span class="dashboard-info-icone">
                            <?= icone('calendar') ?>
                        </span>

                        <div>

                            <span class="sobrelinha">
                                PRÓXIMOS 7 DIAS
                            </span>

                            <strong>
                                <?= $proximosSeteDias ?>
                                <span>consultas</span>
                            </strong>

                            <p>
                                Agendamentos futuros não cancelados
                            </p>

                        </div>

                        <a
                            href="index.php?pagina=agenda"
                            aria-label="Visualizar agenda"
                        >
                            <?= icone('arrow-up-right') ?>
                        </a>

                    </article>

                </section>

                <p class="nota-demo">
                    Painel demonstrativo com dados da sessão PHP.
                </p>

            <?php elseif ($mostrarNotificacoes): ?>

                <!-- ==========================
                     NOTIFICAÇÕES
                =========================== -->

                <section class="pagina-introducao">

                    <div>
                        <span class="sobrelinha">
                            CENTRAL DE AVISOS
                        </span>

                        <h2>Notificações</h2>

                        <p>
                            Acompanhe as atualizações do consultório.
                        </p>
                    </div>

                    <span class="notificacoes-total">
                        <?= $totalNaoLidas ?> não lida(s)
                    </span>

                </section>

                <section class="painel notificacoes-painel">

                    <div class="painel-barra">

                        <div>
                            <span class="sobrelinha">
                                ATUALIZAÇÕES RECENTES
                            </span>

                            <h3>Suas notificações</h3>
                        </div>

                        <?php if ($totalNaoLidas > 0): ?>

                            <form
                                method="POST"
                                action="index.php"
                            >

                                <input
                                    type="hidden"
                                    name="acao"
                                    value="marcar_notificacoes"
                                >

                                <button
                                    type="submit"
                                    class="botao-secundario"
                                >
                                    <?= icone('check', 'icone-botao') ?>
                                    Marcar todas como lidas
                                </button>

                            </form>

                        <?php endif; ?>

                    </div>

                    <div class="notificacoes-lista">

                        <?php
                        $iconesNotificacoes = [
                            'consulta' => 'calendar',
                            'alerta' => 'alert',
                            'paciente' => 'user-plus',
                            'retorno' => 'refresh',
                            'sistema' => 'info'
                        ];
                        ?>

                        <?php foreach ($_SESSION['notificacoes_demo'] as $n): ?>

                            <form
                                method="POST"
                                action="index.php"
                            >

                                <input
                                    type="hidden"
                                    name="acao"
                                    value="abrir_notificacao"
                                >

                                <input
                                    type="hidden"
                                    name="id_notificacao"
                                    value="<?= (int)$n['id'] ?>"
                                >

                                <button
                                    type="submit"
                                    class="notificacao-item <?= !$n['lida'] ? 'nao-lida' : '' ?>"
                                >

                                    <span class="notificacao-icone <?= e($n['tipo']) ?>">

                                        <?= icone($iconesNotificacoes[$n['tipo']] ?? 'info') ?>

                                    </span>

                                    <span class="notificacao-conteudo">

                                        <span class="notificacao-titulo-linha">

                                            <strong>
                                                <?= e($n['titulo']) ?>
                                            </strong>

                                            <?php if (!$n['lida']): ?>

                                                <span class="notificacao-ponto"></span>

                                            <?php endif; ?>

                                        </span>

                                        <span class="notificacao-mensagem">
                                            <?= e($n['mensagem']) ?>
                                        </span>

                                        <small>
                                            <?= e($n['data']) ?>
                                            às <?= e($n['hora']) ?>
                                        </small>

                                    </span>

                                    <?= icone('chevron-right', 'notificacao-seta') ?>

                                </button>

                            </form>

                        <?php endforeach; ?>

                    </div>

                </section>

                <p class="nota-demo">
                    Notificações fictícias para demonstração.
                </p>

            <?php elseif ($mostrarNovoAgendamento): ?>

                <!-- ==========================
                     NOVO AGENDAMENTO
                =========================== -->

                <div class="pagina-topo">

                    <a
                        href="index.php?pagina=agenda"
                        class="voltar"
                    >
                        <?= icone('arrow-left', 'icone-botao') ?>
                        Voltar para agenda
                    </a>

                    <span class="etiqueta">
                        NOVO AGENDAMENTO
                    </span>

                </div>

                <section class="painel formulario-painel">

                    <div class="formulario-cabecalho">

                        <span class="formulario-icone">
                            <?= icone('calendar') ?>
                        </span>

                        <div>
                            <span class="sobrelinha">
                                AGENDA MÉDICA
                            </span>

                            <h2>Agendar nova consulta</h2>

                            <p>
                                Selecione um paciente e informe
                                os dados do atendimento.
                            </p>
                        </div>

                    </div>

                    <?php if ($erroAgenda !== ''): ?>

                        <div class="alerta erro" role="alert">
                            <?= e($erroAgenda) ?>
                        </div>

                    <?php endif; ?>

                    <form
                        method="POST"
                        action="index.php?pagina=agenda&novo_agendamento=1"
                        class="formulario-cadastro"
                    >

                        <input
                            type="hidden"
                            name="acao"
                            value="criar_agendamento"
                        >

                        <div class="formulario-secao-titulo">

                            <span class="sobrelinha">PACIENTE</span>
                            <h3>Selecionar paciente</h3>

                        </div>

                        <div class="campo campo-largo">

                            <label for="paciente_id">
                                Paciente cadastrado <span>*</span>
                            </label>

                            <select
                                name="paciente_id"
                                id="paciente_id"
                                required
                            >

                                <option value="">
                                    Selecione um paciente
                                </option>

                                <?php foreach ($_SESSION['pacientes_demo'] as $p): ?>

                                    <option
                                        value="<?= (int)$p['id'] ?>"
                                        <?= (string)($_POST['paciente_id'] ?? '') === (string)$p['id'] ? 'selected' : '' ?>
                                    >
                                        <?= e($p['nome']) ?> — <?= e($p['status']) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                            <small class="ajuda-formulario">
                                Pacientes cadastrados aparecem
                                automaticamente nesta lista.
                            </small>

                        </div>

                        <div class="formulario-secao-titulo">

                            <span class="sobrelinha">
                                DATA E HORÁRIO
                            </span>

                            <h3>Quando será a consulta?</h3>

                        </div>

                        <div class="campo">

                            <label for="data_agendamento">
                                Data <span>*</span>
                            </label>

                            <input
                                type="date"
                                id="data_agendamento"
                                name="data_agendamento"
                                min="<?= e($hoje) ?>"
                                value="<?= e($_POST['data_agendamento'] ?? $hoje) ?>"
                                required
                            >

                        </div>

                        <div class="campo">

                            <label for="hora_agendamento">
                                Horário <span>*</span>
                            </label>

                            <input
                                type="time"
                                id="hora_agendamento"
                                name="hora_agendamento"
                                value="<?= e($_POST['hora_agendamento'] ?? '09:00') ?>"
                                required
                            >

                        </div>

                        <div class="formulario-secao-titulo">

                            <span class="sobrelinha">ATENDIMENTO</span>
                            <h3>Informações da consulta</h3>

                        </div>

                        <div class="campo">

                            <label for="tipo_consulta">
                                Tipo de consulta <span>*</span>
                            </label>

                            <select
                                id="tipo_consulta"
                                name="tipo_consulta"
                                required
                            >

                                <option value="">
                                    Selecione o tipo
                                </option>

                                <?php foreach ($tiposConsulta as $tipo): ?>

                                    <option
                                        value="<?= e($tipo) ?>"
                                        <?= ($_POST['tipo_consulta'] ?? '') === $tipo ? 'selected' : '' ?>
                                    >
                                        <?= e($tipo) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <div class="campo">

                            <label for="status_agendamento">
                                Status inicial <span>*</span>
                            </label>

                            <select
                                id="status_agendamento"
                                name="status_agendamento"
                                required
                            >

                                <option
                                    value="Pendente"
                                    <?= ($_POST['status_agendamento'] ?? 'Pendente') === 'Pendente' ? 'selected' : '' ?>
                                >
                                    Pendente
                                </option>

                                <option
                                    value="Confirmada"
                                    <?= ($_POST['status_agendamento'] ?? '') === 'Confirmada' ? 'selected' : '' ?>
                                >
                                    Confirmada
                                </option>

                            </select>

                        </div>

                        <div class="campo campo-largo">

                            <label for="observacao">
                                Observações (opcional)
                            </label>

                            <textarea
                                id="observacao"
                                name="observacao"
                                rows="4"
                                maxlength="500"
                                placeholder="Ex.: Paciente levará exames anteriores."
                            ><?= e($_POST['observacao'] ?? '') ?></textarea>

                        </div>

                        <div class="acoes-formulario">

                            <a
                                href="index.php?pagina=agenda"
                                class="botao-secundario"
                            >
                                Cancelar
                            </a>

                            <button
                                type="submit"
                                class="botao-primario"
                            >
                                <?= icone('check', 'icone-botao') ?>
                                Salvar agendamento
                            </button>

                        </div>

                    </form>

                </section>

            <?php elseif ($mostrarAgenda): ?>

                <!-- ==========================
                     AGENDA
                =========================== -->

                <section class="pagina-introducao">

                    <div>
                        <span class="sobrelinha">
                            ORGANIZAÇÃO DO CONSULTÓRIO
                        </span>

                        <h2>Agenda de consultas</h2>

                        <p>
                            Visualize os atendimentos separados
                            por dia e organize novos agendamentos.
                        </p>
                    </div>

                    <a
                        href="index.php?pagina=agenda&novo_agendamento=1"
                        class="botao-primario"
                    >
                        <?= icone('plus', 'icone-botao') ?>
                        Novo agendamento
                    </a>

                </section>

                <?php if (isset($_GET['agendado'])): ?>

                    <div class="alerta sucesso" role="status">
                        Agendamento criado com sucesso!
                    </div>

                <?php endif; ?>

                <section class="agenda-resumo">

                    <div class="resumo-card">
                        <span>Total</span>
                        <strong><?= $totalAgenda ?></strong>
                        <small>Consultas encontradas</small>
                    </div>

                    <div class="resumo-card">
                        <span>Confirmadas</span>
                        <strong class="numero-verde">
                            <?= $confirmadasAgenda ?>
                        </strong>
                        <small>Atendimentos confirmados</small>
                    </div>

                    <div class="resumo-card">
                        <span>Pendentes</span>
                        <strong class="numero-laranja">
                            <?= $pendentesAgenda ?>
                        </strong>
                        <small>Aguardando confirmação</small>
                    </div>

                    <div class="resumo-card">
                        <span>Canceladas</span>
                        <strong class="numero-vermelho">
                            <?= $canceladasAgenda ?>
                        </strong>
                        <small>Atendimentos cancelados</small>
                    </div>

                </section>

                <section class="painel filtros-agenda">

                    <div class="filtros-titulo">
                        <?= icone('search') ?>
                        <h3>Filtrar agendamentos</h3>
                    </div>

                    <form
                        method="GET"
                        action="index.php"
                        class="agenda-filtro"
                    >

                        <input
                            type="hidden"
                            name="pagina"
                            value="agenda"
                        >

                        <div class="campo-filtro">

                            <label for="data">Data</label>

                            <input
                                type="date"
                                name="data"
                                id="data"
                                value="<?= e($filtroData) ?>"
                            >

                        </div>

                        <div class="campo-filtro">

                            <label for="status">Status</label>

                            <select name="status" id="status">

                                <option value="">
                                    Todos os status
                                </option>

                                <?php foreach (
                                    ['Confirmada', 'Pendente', 'Cancelada']
                                    as $opcao
                                ): ?>

                                    <option
                                        value="<?= e($opcao) ?>"
                                        <?= $filtroStatus === $opcao ? 'selected' : '' ?>
                                    >
                                        <?= e($opcao) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <button
                            type="submit"
                            class="botao-primario"
                        >
                            Filtrar
                        </button>

                        <a
                            href="index.php?pagina=agenda"
                            class="botao-secundario"
                        >
                            Limpar
                        </a>

                    </form>

                </section>

                <?php if ($agendaPorDia): ?>

                    <?php foreach ($agendaPorDia as $dia => $consultasDia): ?>

                        <section class="grupo-dia">

                            <div class="grupo-dia-cabecalho">

                                <div class="grupo-dia-titulo">

                                    <span class="grupo-dia-icone">
                                        <?= icone('calendar') ?>
                                    </span>

                                    <div>
                                        <span class="sobrelinha">
                                            AGENDA DO DIA
                                        </span>

                                        <h3>
                                            <?= e(tituloDia($dia)) ?>
                                        </h3>
                                    </div>

                                </div>

                                <span class="grupo-dia-total">
                                    <?= count($consultasDia) ?> consulta(s)
                                </span>

                            </div>

                            <div class="painel agenda-tabela-rolagem">

                                <table class="agenda-tabela">

                                    <thead>
                                        <tr>
                                            <th>HORÁRIO</th>
                                            <th>PACIENTE</th>
                                            <th>TIPO</th>
                                            <th>STATUS</th>
                                            <th>AÇÕES</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        <?php foreach ($consultasDia as $consulta): ?>

                                            <?php
                                            $p = pacientePorId(
                                                $_SESSION['pacientes_demo'],
                                                (int)$consulta['paciente_id']
                                            );
                                            ?>

                                            <tr>

                                                <td class="horario-celula">
                                                    <?= icone('clock', 'icone-botao') ?>

                                                    <strong>
                                                        <?= e($consulta['hora']) ?>
                                                    </strong>
                                                </td>

                                                <td>

                                                    <?php if ($p): ?>

                                                        <a
                                                            href="index.php?dados=<?= (int)$p['id'] ?>"
                                                            class="link-paciente"
                                                        >
                                                            <?= e($p['nome']) ?>
                                                        </a>

                                                        <small class="tipo-pagamento">
                                                            <?= e($p['tipo_atendimento']) ?>
                                                        </small>

                                                    <?php else: ?>

                                                        Paciente não encontrado

                                                    <?php endif; ?>

                                                </td>

                                                <td>

                                                    <?= e($consulta['tipo']) ?>

                                                    <?php if ($consulta['observacao'] !== ''): ?>

                                                        <small class="observacao-agenda">
                                                            <?= e($consulta['observacao']) ?>
                                                        </small>

                                                    <?php endif; ?>

                                                </td>

                                                <td>

                                                    <span class="agenda-status <?= strtolower($consulta['status']) ?>">
                                                        <?= e($consulta['status']) ?>
                                                    </span>

                                                </td>

                                                <td>

                                                    <div class="acoes-agenda">

                                                        <?php if ($consulta['status'] === 'Pendente'): ?>

                                                            <form
                                                                method="POST"
                                                                action="index.php"
                                                            >

                                                                <input
                                                                    type="hidden"
                                                                    name="acao"
                                                                    value="alterar_status_agenda"
                                                                >

                                                                <input
                                                                    type="hidden"
                                                                    name="agendamento_id"
                                                                    value="<?= (int)$consulta['id'] ?>"
                                                                >

                                                                <input
                                                                    type="hidden"
                                                                    name="novo_status"
                                                                    value="Confirmada"
                                                                >

                                                                <button
                                                                    type="submit"
                                                                    class="acao-confirmar"
                                                                    title="Confirmar consulta"
                                                                    aria-label="Confirmar consulta"
                                                                >
                                                                    <?= icone('check') ?>
                                                                </button>

                                                            </form>

                                                        <?php endif; ?>

                                                        <?php if ($consulta['status'] !== 'Cancelada'): ?>

                                                            <form
                                                                method="POST"
                                                                action="index.php"
                                                                onsubmit="return confirm('Deseja cancelar esta consulta?');"
                                                            >

                                                                <input
                                                                    type="hidden"
                                                                    name="acao"
                                                                    value="alterar_status_agenda"
                                                                >

                                                                <input
                                                                    type="hidden"
                                                                    name="agendamento_id"
                                                                    value="<?= (int)$consulta['id'] ?>"
                                                                >

                                                                <input
                                                                    type="hidden"
                                                                    name="novo_status"
                                                                    value="Cancelada"
                                                                >

                                                                <button
                                                                    type="submit"
                                                                    class="acao-cancelar"
                                                                    title="Cancelar consulta"
                                                                    aria-label="Cancelar consulta"
                                                                >
                                                                    <?= icone('x') ?>
                                                                </button>

                                                            </form>

                                                        <?php else: ?>

                                                            <span class="sem-acoes">
                                                                —
                                                            </span>

                                                        <?php endif; ?>

                                                    </div>

                                                </td>

                                            </tr>

                                        <?php endforeach; ?>

                                    </tbody>

                                </table>

                            </div>

                        </section>

                    <?php endforeach; ?>

                <?php else: ?>

                    <section class="estado-vazio">

                        <?= icone('calendar', 'icone-vazio') ?>

                        <h3>Nenhum agendamento encontrado</h3>

                        <p>
                            Não existem consultas para os filtros selecionados.
                        </p>

                        <a
                            href="index.php?pagina=agenda&novo_agendamento=1"
                            class="botao-primario"
                        >
                            <?= icone('plus', 'icone-botao') ?>
                            Criar agendamento
                        </a>

                    </section>

                <?php endif; ?>

                <p class="nota-demo">
                    Agenda demonstrativa armazenada na sessão PHP.
                </p>

            <?php elseif ($mostrarDados): ?>

                <!-- ==========================
                     DADOS PESSOAIS
                =========================== -->

                <div class="pagina-topo">

                    <a
                        href="index.php?pagina=pacientes"
                        class="voltar"
                    >
                        <?= icone('arrow-left', 'icone-botao') ?>
                        Voltar para pacientes
                    </a>

                    <span class="etiqueta">
                        CADASTRO DO PACIENTE
                    </span>

                </div>

                <section class="painel detalhes-paciente">

                    <div class="avatar">
                        <?= e(mb_strtoupper(mb_substr($pacienteSelecionado['nome'], 0, 1))) ?>
                    </div>

                    <div class="dados-paciente">

                        <span class="sobrelinha">
                            INFORMAÇÕES PESSOAIS
                        </span>

                        <h2>
                            <?= e($pacienteSelecionado['nome']) ?>
                        </h2>

                        <p>
                            Paciente #<?= (int)$pacienteSelecionado['id'] ?>

                            <span class="separador">•</span>

                            <?= (int)$pacienteSelecionado['idade'] ?> anos
                        </p>

                    </div>

                    <span class="chip <?= strtolower($pacienteSelecionado['status']) ?>">
                        <?= e($pacienteSelecionado['status']) ?>
                    </span>

                </section>

                <div class="secao-titulo">

                    <div>
                        <span class="sobrelinha">
                            INFORMAÇÕES CADASTRAIS
                        </span>

                        <h2>Dados pessoais</h2>
                    </div>

                </div>

                <section class="dados-grid">

                    <article class="painel dados-card">

                        <div class="dados-card-topo">
                            <?= icone('id', 'prontuario-icone') ?>

                            <span class="sobrelinha">
                                IDENTIFICAÇÃO
                            </span>
                        </div>

                        <h3>Informações pessoais</h3>

                        <div class="dados-linha">
                            <span>Nome completo</span>

                            <strong>
                                <?= e($pacienteSelecionado['nome']) ?>
                            </strong>
                        </div>

                        <div class="dados-linha">
                            <span>Idade</span>

                            <strong>
                                <?= (int)$pacienteSelecionado['idade'] ?> anos
                            </strong>
                        </div>

                        <div class="dados-linha">
                            <span>Status</span>

                            <strong>
                                <?= e($pacienteSelecionado['status']) ?>
                            </strong>
                        </div>

                    </article>

                    <article class="painel dados-card">

                        <div class="dados-card-topo">
                            <?= icone('heart', 'prontuario-icone') ?>

                            <span class="sobrelinha">
                                INFORMAÇÕES FÍSICAS
                            </span>
                        </div>

                        <h3>Medidas do paciente</h3>

                        <div class="dados-linha">
                            <span>Peso</span>

                            <strong>
                                <?= numero($pacienteSelecionado['peso']) ?>
                                <?= $pacienteSelecionado['peso'] !== null ? 'kg' : '' ?>
                            </strong>
                        </div>

                        <div class="dados-linha">
                            <span>Altura</span>

                            <strong>
                                <?= numero($pacienteSelecionado['altura'], 0) ?>
                                <?= $pacienteSelecionado['altura'] !== null ? 'cm' : '' ?>
                            </strong>
                        </div>

                    </article>

                    <article class="painel dados-card">

                        <div class="dados-card-topo">
                            <?= icone('phone', 'prontuario-icone') ?>

                            <span class="sobrelinha">
                                CONTATO
                            </span>
                        </div>

                        <h3>Informações de contato</h3>

                        <div class="dados-linha">
                            <span>Telefone</span>

                            <strong>
                                <?= e($pacienteSelecionado['telefone'] ?: 'Não informado') ?>
                            </strong>
                        </div>

                        <div class="dados-linha">
                            <span>E-mail</span>

                            <strong>
                                <?= e($pacienteSelecionado['email'] ?: 'Não informado') ?>
                            </strong>
                        </div>

                    </article>

                    <article class="painel dados-card">

                        <div class="dados-card-topo">
                            <?= icone('card', 'prontuario-icone') ?>

                            <span class="sobrelinha">
                                ATENDIMENTO
                            </span>
                        </div>

                        <h3>Convênio ou particular</h3>

                        <div class="dados-linha">
                            <span>Tipo de atendimento</span>

                            <strong>
                                <?= e($pacienteSelecionado['tipo_atendimento']) ?>
                            </strong>
                        </div>

                        <?php if ($pacienteSelecionado['tipo_atendimento'] === 'Convênio'): ?>

                            <div class="dados-linha">
                                <span>Convênio</span>

                                <strong>
                                    <?= e($pacienteSelecionado['convenio']) ?>
                                </strong>
                            </div>

                            <div class="dados-linha">
                                <span>Número da carteirinha</span>

                                <strong>
                                    <?= e($pacienteSelecionado['carteirinha']) ?>
                                </strong>
                            </div>

                            <div class="dados-linha">
                                <span>Validade da carteirinha</span>

                                <strong>
                                    <?php
                                    $validadePaciente = $pacienteSelecionado['validade_carteirinha'];

                                    if (preg_match('/^\d{4}-\d{2}$/', $validadePaciente)) {
                                        [$ano, $mes] = explode('-', $validadePaciente);
                                        echo e($mes . '/' . $ano);
                                    } else {
                                        echo 'Não informada';
                                    }
                                    ?>
                                </strong>
                            </div>

                        <?php elseif ($pacienteSelecionado['tipo_atendimento'] === 'Particular'): ?>

                            <div class="dados-linha">

                                <span>Forma de pagamento</span>

                                <strong>
                                    <?= e($pacienteSelecionado['forma_pagamento'] ?: 'Não informada') ?>
                                </strong>

                            </div>

                        <?php endif; ?>

                        <div class="dados-linha">

                            <span>Valor da consulta</span>

                            <strong class="valor-consulta">
                                <?= moeda($pacienteSelecionado['valor_consulta']) ?>
                            </strong>

                        </div>

                        <div class="dados-linha">

                            <span>Profissional responsável</span>

                            <strong>
                                <?= e($nomeMedica) ?>
                            </strong>

                        </div>

                    </article>

                </section>

                <div class="dados-acoes">

                    <a
                        href="index.php?prontuario=<?= (int)$pacienteSelecionado['id'] ?>"
                        class="botao-secundario"
                    >
                        <?= icone('clipboard', 'icone-botao') ?>
                        Ver prontuário
                    </a>

                    <a
                        href="index.php?historico=<?= (int)$pacienteSelecionado['id'] ?>"
                        class="botao-primario"
                    >
                        <?= icone('file', 'icone-botao') ?>
                        Histórico de consultas
                    </a>

                </div>

            <?php elseif ($mostrarHistorico): ?>

                <!-- ==========================
                     HISTÓRICO DO PACIENTE
                =========================== -->

                <div class="pagina-topo">

                    <a
                        href="index.php?pagina=pacientes"
                        class="voltar"
                    >
                        <?= icone('arrow-left', 'icone-botao') ?>
                        Voltar para pacientes
                    </a>

                    <span class="etiqueta">
                        PRONTUÁRIO • DEMONSTRAÇÃO
                    </span>

                </div>

                <section class="painel detalhes-paciente">

                    <div class="avatar">
                        <?= e(mb_strtoupper(mb_substr($pacienteSelecionado['nome'], 0, 1))) ?>
                    </div>

                    <div class="dados-paciente">

                        <span class="sobrelinha">
                            HISTÓRICO DE CONSULTAS
                        </span>

                        <h2>
                            <?= e($pacienteSelecionado['nome']) ?>
                        </h2>

                        <p>
                            <?= (int)$pacienteSelecionado['idade'] ?> anos

                            <span class="separador">•</span>

                            <?= e($pacienteSelecionado['telefone'] ?: 'Telefone não informado') ?>
                        </p>

                        <p>
                            <?= e($pacienteSelecionado['email'] ?: 'E-mail não informado') ?>
                        </p>

                    </div>

                    <span class="chip <?= strtolower($pacienteSelecionado['status']) ?>">
                        <?= e($pacienteSelecionado['status']) ?>
                    </span>

                </section>

                <div class="prontuario-acoes">

                    <a
                        href="index.php?prontuario=<?= (int)$pacienteSelecionado['id'] ?>"
                        class="botao-primario"
                    >
                        <?= icone('clipboard', 'icone-botao') ?>
                        Ver prontuário
                    </a>

                </div>

                <div class="secao-titulo">

                    <div>
                        <span class="sobrelinha">ACOMPANHAMENTO</span>
                        <h2>Consultas registradas</h2>
                    </div>

                    <span class="contador">
                        <?= count($historico) ?> registro(s)
                    </span>

                </div>

                <?php if ($historico): ?>

                    <div class="lista-consultas">

                        <?php foreach ($historico as $consulta): ?>

                            <article class="consulta-card">

                                <div class="data-consulta">

                                    <?= icone('calendar', 'icone-consulta') ?>

                                    <strong>
                                        <?= e($consulta['data']) ?>
                                    </strong>

                                    <small>
                                        <?= e($consulta['hora']) ?>
                                    </small>

                                </div>

                                <div class="consulta-info">

                                    <h3>
                                        <?= e($consulta['tipo']) ?>
                                    </h3>

                                    <p>
                                        <?= e($consulta['descricao']) ?>
                                    </p>

                                    <small>
                                        <?= e($nomeMedica) ?> • Clínica geral
                                    </small>

                                </div>

                                <span class="selo <?= $consulta['situacao'] === 'Agendada' ? 'agendada' : 'concluida' ?>">
                                    <?= e($consulta['situacao']) ?>
                                </span>

                            </article>

                        <?php endforeach; ?>

                    </div>

                <?php else: ?>

                    <div class="estado-vazio">

                        <?= icone('file', 'icone-vazio') ?>

                        <h3>Nenhuma consulta registrada</h3>

                        <p>
                            Este paciente ainda não possui
                            consultas no histórico.
                        </p>

                    </div>

                <?php endif; ?>

                <div class="dados-acoes">

                    <a
                        href="index.php?dados=<?= (int)$pacienteSelecionado['id'] ?>"
                        class="botao-secundario"
                    >
                        <?= icone('id', 'icone-botao') ?>
                        Ver dados pessoais
                    </a>

                </div>

            <?php elseif ($mostrarProntuario): ?>

                <!-- ==========================
                     PRONTUÁRIO
                =========================== -->

                <div class="pagina-topo">

                    <a
                        href="index.php?historico=<?= (int)$pacienteSelecionado['id'] ?>"
                        class="voltar"
                    >
                        <?= icone('arrow-left', 'icone-botao') ?>
                        Voltar para histórico
                    </a>

                    <span class="etiqueta">
                        PRONTUÁRIO • DEMONSTRAÇÃO
                    </span>

                </div>

                <section class="painel detalhes-paciente">

                    <div class="avatar">
                        <?= e(mb_strtoupper(mb_substr($pacienteSelecionado['nome'], 0, 1))) ?>
                    </div>

                    <div class="dados-paciente">

                        <span class="sobrelinha">
                            PRONTUÁRIO DO PACIENTE
                        </span>

                        <h2>
                            <?= e($pacienteSelecionado['nome']) ?>
                        </h2>

                        <p>
                            <?= (int)$pacienteSelecionado['idade'] ?> anos

                            <span class="separador">•</span>

                            Paciente #<?= (int)$pacienteSelecionado['id'] ?>
                        </p>

                        <p>
                            Profissional: <?= e($nomeMedica) ?>
                        </p>

                    </div>

                    <span class="chip <?= strtolower($pacienteSelecionado['status']) ?>">
                        <?= e($pacienteSelecionado['status']) ?>
                    </span>

                </section>

                <div class="secao-titulo">

                    <div>
                        <span class="sobrelinha">
                            REGISTRO CLÍNICO
                        </span>

                        <h2>Informações do prontuário</h2>
                    </div>

                </div>

                <?php
                $camposProntuario = [
                    ['queixa', 'Queixa principal', 'clipboard'],
                    ['historico', 'Histórico clínico', 'activity'],
                    ['alergias', 'Alergias', 'alert'],
                    ['medicamentos', 'Medicamentos em uso', 'pill'],
                    ['observacoes', 'Observações clínicas', 'file'],
                    ['conduta', 'Conduta e orientações', 'list']
                ];
                ?>

                <section class="prontuario-grid">

                    <?php foreach ($camposProntuario as [$campo, $titulo, $simbolo]): ?>

                        <article class="painel prontuario-card">

                            <div class="prontuario-card-topo">

                                <?= icone($simbolo, 'prontuario-icone') ?>

                                <h3>
                                    <?= e($titulo) ?>
                                </h3>

                            </div>

                            <p>
                                <?= e($prontuario[$campo] ?? 'Não informado.') ?>
                            </p>

                        </article>

                    <?php endforeach; ?>

                </section>

                <div class="dados-acoes">

                    <a
                        href="index.php?dados=<?= (int)$pacienteSelecionado['id'] ?>"
                        class="botao-secundario"
                    >
                        <?= icone('id', 'icone-botao') ?>
                        Dados pessoais
                    </a>

                    <a
                        href="index.php?historico=<?= (int)$pacienteSelecionado['id'] ?>"
                        class="botao-primario"
                    >
                        <?= icone('calendar', 'icone-botao') ?>
                        Histórico de consultas
                    </a>

                </div>

                <p class="nota-demo">
                    Prontuário fictício utilizado apenas para demonstração.
                </p>

            <?php elseif ($mostrarCadastro): ?>

                <!-- ==========================
                     CADASTRO DE PACIENTE
                =========================== -->

                <div class="pagina-topo">

                    <a
                        href="index.php?pagina=pacientes"
                        class="voltar"
                    >
                        <?= icone('arrow-left', 'icone-botao') ?>
                        Voltar para pacientes
                    </a>

                    <span class="etiqueta">NOVO CADASTRO</span>

                </div>

                <section class="painel formulario-painel">

                    <div class="formulario-cabecalho">

                        <span class="formulario-icone">
                            <?= icone('user-plus') ?>
                        </span>

                        <div>
                            <span class="sobrelinha">
                                GESTÃO DE PACIENTES
                            </span>

                            <h2>Adicionar novo paciente</h2>

                            <p>
                                Preencha os dados pessoais
                                e de atendimento.
                            </p>
                        </div>

                    </div>

                    <?php if ($erroCadastro !== ''): ?>

                        <div class="alerta erro" role="alert">
                            <?= e($erroCadastro) ?>
                        </div>

                    <?php endif; ?>

                    <form
                        method="POST"
                        action="index.php?novo=1"
                        class="formulario-cadastro"
                        id="formularioCadastro"
                    >

                        <input
                            type="hidden"
                            name="acao"
                            value="adicionar_paciente"
                        >

                        <div class="formulario-secao-titulo">

                            <span class="sobrelinha">
                                INFORMAÇÕES PESSOAIS
                            </span>

                            <h3>Dados do paciente</h3>

                        </div>

                        <div class="campo campo-largo">

                            <label for="nome">
                                Nome completo <span>*</span>
                            </label>

                            <input
                                id="nome"
                                name="nome"
                                type="text"
                                maxlength="100"
                                placeholder="Ex.: João da Silva"
                                value="<?= e($_POST['nome'] ?? '') ?>"
                                required
                            >

                        </div>

                        <div class="campo">

                            <label for="idade">
                                Idade <span>*</span>
                            </label>

                            <input
                                id="idade"
                                name="idade"
                                type="number"
                                min="0"
                                max="120"
                                value="<?= e($_POST['idade'] ?? '') ?>"
                                required
                            >

                        </div>

                        <div class="campo">

                            <label for="telefone">Telefone</label>

                            <input
                                id="telefone"
                                name="telefone"
                                type="tel"
                                placeholder="(92) 99999-9999"
                                value="<?= e($_POST['telefone'] ?? '') ?>"
                            >

                        </div>

                        <div class="campo campo-largo">

                            <label for="email">E-mail</label>

                            <input
                                id="email"
                                name="email"
                                type="email"
                                placeholder="paciente@email.com"
                                value="<?= e($_POST['email'] ?? '') ?>"
                            >

                        </div>

                        <div class="formulario-secao-titulo">

                            <span class="sobrelinha">
                                INFORMAÇÕES FÍSICAS
                            </span>

                            <h3>Medidas do paciente</h3>

                        </div>

                        <div class="campo">

                            <label for="peso">
                                Peso (kg) <span>*</span>
                            </label>

                            <input
                                id="peso"
                                name="peso"
                                type="number"
                                min="0.1"
                                step="0.1"
                                placeholder="Ex.: 70.5"
                                value="<?= e($_POST['peso'] ?? '') ?>"
                                required
                            >

                        </div>

                        <div class="campo">

                            <label for="altura">
                                Altura (cm) <span>*</span>
                            </label>

                            <input
                                id="altura"
                                name="altura"
                                type="number"
                                min="0.1"
                                step="0.1"
                                placeholder="Ex.: 175"
                                value="<?= e($_POST['altura'] ?? '') ?>"
                                required
                            >

                        </div>

                        <div class="formulario-secao-titulo">

                            <span class="sobrelinha">
                                INFORMAÇÕES DO ATENDIMENTO
                            </span>

                            <h3>Convênio ou particular</h3>

                            <p class="ajuda-formulario">
                                Selecione uma opção para exibir
                                os campos correspondentes.
                            </p>

                        </div>

                        <div class="campo campo-largo">

                            <span class="titulo-campo">
                                Tipo de atendimento <span>*</span>
                            </span>

                            <div class="opcoes-atendimento">

                                <label
                                    class="opcao-atendimento"
                                    id="labelConvenio"
                                >

                                    <input
                                        type="checkbox"
                                        id="tipo_convenio"
                                        name="tipo_atendimento"
                                        value="Convênio"
                                        <?= ($_POST['tipo_atendimento'] ?? '') === 'Convênio' ? 'checked' : '' ?>
                                    >

                                    <span class="opcao-conteudo">

                                        <?= icone('card') ?>

                                        <span>
                                            <strong>Convênio</strong>
                                            <small>Plano de saúde</small>
                                        </span>

                                    </span>

                                </label>

                                <label
                                    class="opcao-atendimento"
                                    id="labelParticular"
                                >

                                    <input
                                        type="checkbox"
                                        id="tipo_particular"
                                        name="tipo_atendimento"
                                        value="Particular"
                                        <?= ($_POST['tipo_atendimento'] ?? '') === 'Particular' ? 'checked' : '' ?>
                                    >

                                    <span class="opcao-conteudo">

                                        <?= icone('money') ?>

                                        <span>
                                            <strong>Particular</strong>
                                            <small>Pagamento direto</small>
                                        </span>

                                    </span>

                                </label>

                            </div>

                        </div>

                        <div
                            id="camposConvenio"
                            class="campos-condicionais campo-largo"
                            hidden
                        >

                            <div class="formulario-secao-titulo">

                                <span class="sobrelinha">
                                    PLANO DE SAÚDE
                                </span>

                                <h3>Informações do convênio</h3>

                            </div>

                            <div class="campo campo-largo">

                                <label for="convenio">
                                    Convênio <span>*</span>
                                </label>

                                <input
                                    id="convenio"
                                    name="convenio"
                                    type="text"
                                    placeholder="Ex.: Unimed"
                                    value="<?= e($_POST['convenio'] ?? '') ?>"
                                >

                            </div>

                            <div class="campo">

                                <label for="carteirinha">
                                    Número da carteirinha <span>*</span>
                                </label>

                                <input
                                    id="carteirinha"
                                    name="carteirinha"
                                    type="text"
                                    value="<?= e($_POST['carteirinha'] ?? '') ?>"
                                >

                            </div>

                            <div class="campo">

                                <label for="validade_carteirinha">
                                    Validade da carteirinha
                                </label>

                                <input
                                    id="validade_carteirinha"
                                    name="validade_carteirinha"
                                    type="month"
                                    value="<?= e($_POST['validade_carteirinha'] ?? '') ?>"
                                >

                            </div>

                        </div>

                        <div
                            id="camposParticular"
                            class="campos-condicionais campo-largo"
                            hidden
                        >

                            <div class="formulario-secao-titulo">

                                <span class="sobrelinha">
                                    PAGAMENTO DIRETO
                                </span>

                                <h3>Informações do particular</h3>

                            </div>

                            <div class="campo campo-largo">

                                <label for="forma_pagamento">
                                    Forma de pagamento <span>*</span>
                                </label>

                                <select
                                    id="forma_pagamento"
                                    name="forma_pagamento"
                                >

                                    <option value="">Selecione</option>

                                    <?php foreach ([
                                        'Pix',
                                        'Dinheiro',
                                        'Cartão de crédito',
                                        'Cartão de débito'
                                    ] as $forma): ?>

                                        <option
                                            value="<?= e($forma) ?>"
                                            <?= ($_POST['forma_pagamento'] ?? '') === $forma ? 'selected' : '' ?>
                                        >
                                            <?= e($forma) ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>

                        </div>

                        <div class="campo campo-largo">

                            <label for="valor_consulta">
                                Valor da consulta (R$) <span>*</span>
                            </label>

                            <input
                                id="valor_consulta"
                                name="valor_consulta"
                                type="number"
                                min="0"
                                step="0.01"
                                placeholder="Ex.: 180.00"
                                value="<?= e($_POST['valor_consulta'] ?? '') ?>"
                                required
                            >

                        </div>

                        <fieldset class="campo campo-largo grupo-status">

                            <legend>Status do paciente</legend>

                            <label>
                                <input
                                    type="radio"
                                    name="status"
                                    value="Ativo"
                                    <?= ($_POST['status'] ?? 'Ativo') === 'Ativo' ? 'checked' : '' ?>
                                >
                                Ativo
                            </label>

                            <label>
                                <input
                                    type="radio"
                                    name="status"
                                    value="Inativo"
                                    <?= ($_POST['status'] ?? '') === 'Inativo' ? 'checked' : '' ?>
                                >
                                Inativo
                            </label>

                        </fieldset>

                        <div class="acoes-formulario">

                            <a
                                href="index.php?pagina=pacientes"
                                class="botao-secundario"
                            >
                                Cancelar
                            </a>

                            <button
                                type="submit"
                                class="botao-primario"
                            >
                                Salvar paciente
                                <?= icone('arrow-right', 'icone-botao') ?>
                            </button>

                        </div>

                    </form>

                </section>

            <?php else: ?>

                <!-- ==========================
                     LISTA DE PACIENTES
                =========================== -->

                <?php if (isset($_GET['cadastrado'])): ?>

                    <div class="alerta sucesso" role="status">
                        Paciente cadastrado com sucesso!
                    </div>

                <?php endif; ?>

                <form
                    method="GET"
                    action="index.php"
                    class="barra-pesquisa"
                >

                    <input
                        type="hidden"
                        name="pagina"
                        value="pacientes"
                    >

                    <button
                        type="submit"
                        class="icone-pesquisa"
                        aria-label="Pesquisar"
                    >
                        <?= icone('search') ?>
                    </button>

                    <input
                        type="search"
                        name="busca"
                        placeholder="Digite o nome do paciente"
                        value="<?= e($busca) ?>"
                        aria-label="Nome do paciente"
                    >

                </form>

                <section class="tabela-container">

                    <table class="tabela-pacientes">

                        <thead>
                            <tr>
                                <th>PACIENTE</th>
                                <th>STATUS</th>
                                <th>DADOS PESSOAIS</th>
                                <th>HISTÓRICO DE CONSULTAS</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if ($pacientesFiltrados): ?>

                                <?php foreach ($pacientesFiltrados as $p): ?>

                                    <tr>

                                        <td>
                                            <?= e($p['nome']) ?>
                                        </td>

                                        <td>

                                            <span class="status">

                                                <span class="indicador <?= strtolower($p['status']) ?>"></span>

                                                <?= e($p['status']) ?>

                                            </span>

                                        </td>

                                        <td class="coluna-acao">

                                            <a
                                                href="index.php?dados=<?= (int)$p['id'] ?>"
                                                class="botao-tabela"
                                                title="Ver dados pessoais"
                                                aria-label="Ver dados pessoais de <?= e($p['nome']) ?>"
                                            >
                                                <?= icone('id') ?>
                                            </a>

                                        </td>

                                        <td class="coluna-acao">

                                            <a
                                                href="index.php?historico=<?= (int)$p['id'] ?>"
                                                class="botao-tabela"
                                                title="Ver histórico"
                                                aria-label="Ver histórico de <?= e($p['nome']) ?>"
                                            >
                                                <?= icone('file') ?>
                                            </a>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="4" class="sem-pacientes">
                                        Nenhum paciente encontrado.
                                    </td>
                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </section>

                <div class="area-botao">

                    <a
                        href="index.php?novo=1"
                        class="botao-adicionar"
                    >
                        <?= icone('plus', 'icone-botao') ?>

                        ADICIONAR NOVO PACIENTE

                        <?= icone('arrow-right', 'icone-botao') ?>
                    </a>

                </div>

            <?php endif; ?>

        </main>

    </div>

<?php endif; ?>

<!-- ==========================================
     CAMPOS DINÂMICOS DO CADASTRO
========================================== -->

<script>
document.addEventListener('DOMContentLoaded', function () {
    const convenio = document.getElementById('tipo_convenio');
    const particular = document.getElementById('tipo_particular');

    if (!convenio || !particular) {
        return;
    }

    const camposConvenio = document.getElementById('camposConvenio');
    const camposParticular = document.getElementById('camposParticular');

    const inputConvenio = document.getElementById('convenio');
    const inputCarteirinha = document.getElementById('carteirinha');
    const inputValidade = document.getElementById('validade_carteirinha');
    const inputPagamento = document.getElementById('forma_pagamento');

    const labelConvenio = document.getElementById('labelConvenio');
    const labelParticular = document.getElementById('labelParticular');

    const formulario = document.getElementById('formularioCadastro');

    function atualizarCampos() {
        camposConvenio.hidden = !convenio.checked;
        camposParticular.hidden = !particular.checked;

        labelConvenio.classList.toggle('ativo', convenio.checked);
        labelParticular.classList.toggle('ativo', particular.checked);

        inputConvenio.disabled = !convenio.checked;
        inputCarteirinha.disabled = !convenio.checked;
        inputValidade.disabled = !convenio.checked;
        inputPagamento.disabled = !particular.checked;

        inputConvenio.required = convenio.checked;
        inputCarteirinha.required = convenio.checked;
        inputPagamento.required = particular.checked;
    }

    convenio.addEventListener('change', function () {
        if (convenio.checked) {
            particular.checked = false;
        }

        atualizarCampos();
    });

    particular.addEventListener('change', function () {
        if (particular.checked) {
            convenio.checked = false;
        }

        atualizarCampos();
    });

    formulario.addEventListener('submit', function (evento) {
        if (!convenio.checked && !particular.checked) {
            evento.preventDefault();
            alert('Selecione Convênio ou Particular.');
            convenio.focus();
        }
    });

    atualizarCampos();
});
</script>

</body>
</html>




