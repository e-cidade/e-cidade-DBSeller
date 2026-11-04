<?php

use ECidade\Educacao\Escola\Registry\ComponenteCurricularRegistry;
use ECidade\Educacao\Escola\Registry\ConteudoDesenvolvidoRegistry;
use ECidade\Educacao\Escola\Registry\EnsinoRegistry;
use ECidade\Educacao\Escola\Repository\TurmasRegentesEscolaRepository;
use ECidade\Educacao\Escola\Service\ConteudoDesenvolvidoService;
use ECidade\Educacao\Escola\Service\HabilidadeDesenvolvidaService;
use ECidade\Educacao\Secretaria\BNCC\Registry\DisciplinaRegistry;
use ECidade\Educacao\Secretaria\BNCC\Repository\DisciplinaEquivalenteRepository;
use ECidade\Educacao\Secretaria\BNCC\Repository\EtapasEquivalenteRepository;
use ECidade\Educacao\Secretaria\BNCC\Resource\DisciplinaResource;
use ECidade\Educacao\Secretaria\BNCC\Resource\HabilidadeEducacaoInfantilResource;
use ECidade\Educacao\Secretaria\BNCC\Resource\HabilidadeEnsinoFundamentalResource;
use ECidade\Educacao\Secretaria\BNCC\Service\HabilidadeEducacaoInfantilService;
use ECidade\Educacao\Secretaria\BNCC\Service\HabilidadeEnsinoFundamentalService;
use ECidade\Educacao\Secretaria\Services\ParametrosGlobaisService;
use ECidade\Enum\Educacao\BNCC\EnsinoEnum;
use ECidade\Enum\Educacao\Escola\TipoEnsinoEnum;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("dbforms/db_funcoes.php"));

$parametros = JSON::requestParameters();
$retorno = (object)['erro' => false, 'mensagem' => ''];

$escola = db_getsession("DB_coddepto");
$dataLogin = date("Y-m-d", db_getsession('DB_datausu'));

try {
    db_inicio_transacao();

    $configuracao = ParametrosGlobaisService::get();

    switch ($parametros->acao) {
        case 'buscarRegente':
            $codigo = db_getsession('DB_id_usuario');

            $usuarioSistema = UsuarioSistemaRepository::getPorCodigo($codigo);

            $retorno->cgm = (object)[
                'codigo' => $usuarioSistema->getCGM()->getCodigo(),
                'usuario' => $codigo,
                'nome' => $usuarioSistema->getCGM()->getNome(),
            ];
            break;
        case 'buscarTurmas':
            if (empty($parametros->cgm)) {
                throw new Exception("Informe o CGM do professor.");
            }

            $cgm = $parametros->cgm;
            $campos = [
                'turma.ed57_i_codigo as codigo_turma',
                'trim(turma.ed57_c_descr) as nome_turma',
                'serie.ed11_i_codigo as codigo_etapa',
                'trim(serie.ed11_c_descr) as nome_etapa',
                'ed59_i_codigo as codigo_regencia',
                'ed232_c_descr as nome_regencia',
                'ed232_i_codigo as codigo_disciplina'
            ];
            $orderBy = ['ed11_i_sequencia', 'ed57_c_descr'];
            $repository = new TurmasRegentesEscolaRepository();
            $dados = $repository->get($parametros->cgm, $escola, $dataLogin, $campos, $orderBy);

            $turmas = [];
            foreach ($dados as $dado) {
                $turmaTurnoReferente = [];
                $turno = [];
                $keyTurma = $dado['codigo_turma'];
                if (!array_key_exists($keyTurma, $turmas)) {
                    $turma = new Turma($keyTurma);
                    $etapas = $turma->getEtapas();
                    $etapaTurma = array_shift($etapas);
                    $codigoEnsino = $etapaTurma->getEtapa()->getEnsino()->getCodigo();
                    $ensino = EnsinoRegistry::get($codigoEnsino);

                    /* Busca os turnos daquela turma, se for integral busca os turnos referentes */
                    $turnoTurma = $turma->getTurno();
                    $turno = (object)[
                        "codigo" => $turnoTurma->getCodigoTurno(),
                        "descricao" => $turnoTurma->getDescricao()
                    ];

                    foreach($turnoTurma->getTurnoReferente() as $referencia) {
                        $turmaTurnoReferente[] = (object)[
                            "codigo" => $referencia,
                            "descricao" => Turno::getDescricaoTurno($referencia),
                        ];
                    }

                    $std = (object)[
                        'codigo' => $dado['codigo_turma'],
                        'nome' => $dado['nome_turma'],
                        'ensino_globalizado' => $turma->getBaseCurricular()->getControleFrequencia() === 'G',
                        'ensinoInfantil' => $ensino->getTipoEnsino()->value() === TipoEnsinoEnum::ENSINO_INFANTIL,
                        "turno" => $turno,
                        'turnosReferentes' => $turmaTurnoReferente,
                        'etapas' => [],

                    ];
                    $turmas[$keyTurma] = $std;
                }

                $keyEtapa = $dado['codigo_etapa'];
                if (!array_key_exists($keyEtapa, $turmas[$keyTurma]->etapas)) {
                    $stdEtapa = (object)[
                        'codigo' => $dado['codigo_etapa'],
                        'nome' => $dado['nome_etapa'],
                        'regencias' => []
                    ];
                    $turmas[$keyTurma]->etapas[$keyEtapa] = $stdEtapa;
                }

                $keyDisciplina = $dado['codigo_regencia'];


                if (!array_key_exists($keyDisciplina, $turmas[$keyTurma]->etapas[$keyEtapa]->regencias)) {
                    $stdRegencia = (object)[
                        'codigo' => $dado['codigo_regencia'],
                        'codigo_disciplina' => $dado['codigo_disciplina'],
                        'nome' => $dado['nome_regencia']
                    ];


                    $turmas[$keyTurma]->etapas[$keyEtapa]->regencias[] = $stdRegencia;
                }
            }

            $turmas = array_values($turmas);
            foreach ($turmas as $turma) {
                $turma->etapas = array_values($turma->etapas);
            }
            $retorno->turmas = $turmas;
            break;

        case 'buscarHabilidades':
            if (empty($parametros->etapa)) {
                throw new Exception("Informe o código da etapa do e-cidade.");
            }
            if (empty($parametros->disciplinaBncc)) {
                throw new Exception("Informe o código da disciplina da BNCC.");
            }
            if (empty($parametros->turma)) {
                throw new Exception("Informe o código da Turma.");
            }

            $disciplinaBncc = DisciplinaRegistry::get($parametros->disciplinaBncc);
            $etapa = EtapaRepository::getEtapaByCodigo($parametros->etapa);
            $ensino = EnsinoRegistry::get($etapa->getEnsino()->getCodigo());
            $turma = TurmaRepository::getTurmaByCodigo($parametros->turma);
            $ano = $turma->getCalendario()->getAnoExecucao();
            if ($ensino->getTipoEnsino()->value() === TipoEnsinoEnum::ENSINO_INFANTIL) {
                $habilidadeEducacaoInfantilService = new HabilidadeEducacaoInfantilService($configuracao, $ano);
                $dados = $habilidadeEducacaoInfantilService->buscarHabilidades($disciplinaBncc);
                $retorno->habilidades = HabilidadeEducacaoInfantilResource::toJsonTree($dados);
            } else {
                $etapasEquivalenteRepository = new EtapasEquivalenteRepository();
                $equivalencias = $etapasEquivalenteRepository->scopeEtapaEcidade($parametros->etapa)->get();

                $etapasBncc = [];
                foreach ($equivalencias as $equivalencia) {
                    $etapasBncc[] = $equivalencia->getBnccEtapa();
                }

                $habilidades = new HabilidadeEnsinoFundamentalService($configuracao, $ano);

                $retorno->habilidades = HabilidadeEnsinoFundamentalResource::toJsonTree(
                    $habilidades->buscarHabilidades($disciplinaBncc, $etapasBncc, $parametros->registroAula)
                );
            }
            break;
        case 'buscarDisciplinaBNCC':
            if (empty($parametros->regencia)) {
                throw new Exception("Não foi informado a disciplina.");
            }
            if (empty($parametros->disciplina)) {
                throw new Exception("Não foi informado a disciplina.");
            }

            $disciplinas = [];

            $ensino = new EnsinoEnum(EnsinoEnum::ENSINO_FUNDAMENTAL);
            if ($parametros->ensinoInfantil == 'true') {
                $ensino = new EnsinoEnum(EnsinoEnum::ENSINO_INFANTIL);
            }

            $disciplinasBuscar = [$parametros->disciplina];

            $regencia = new Regencia($parametros->regencia);
            if ($regencia->getFrequenciaGlobal() === 'FA') {
                $disciplinasBuscar = [];
                $regencias = $regencia->getTurma()->getDisciplinas();

                foreach ($regencias as $regencia) {
                    $disciplinasBuscar[] = $regencia->getDisciplina()->getCodigoDisciplinaGeral();
                }
            }

            foreach ($disciplinasBuscar as $codigoDisciplina) {
                $disciplina = ComponenteCurricularRegistry::get($codigoDisciplina);
                $disciplinaEquivalenteRepository = new DisciplinaEquivalenteRepository();
                $equivalenciasBncc = $disciplinaEquivalenteRepository
                    ->scopeDisciplinaEcidade($disciplina)
                    ->scopeEnsino($ensino)
                    ->get();

                if (empty($equivalenciasBncc)) {
                    throw new Exception(sprintf(
                        "Não foi configurado a equivalência da disciplina %s com uma disciplina da BNCC.",
                        $disciplina->getNome()
                    ));
                }

                foreach ($equivalenciasBncc as $equivalenciaBncc) {
                    $disciplinaBncc = $equivalenciaBncc->getDisciplinaBncc();
                    $disciplinas[$disciplinaBncc->getCodigo()] = $disciplinaBncc;
                }
            }
            $retorno->disciplinasBNCC = DisciplinaResource::toArray($disciplinas);
            break;
        case 'buscarConteudoDesenvolido':
            if (empty($parametros->regencia)) {
                throw new Exception("Informe a regência.");
            }

            if (empty($parametros->data)) {
                throw new Exception("Informe a data.");
            }

            $regencia = RegenciaRepository::getRegenciaByCodigo($parametros->regencia);
            $turnosReferente = $regencia->getTurma()->getTurnoReferente();
            $turnoReferente = $turnosReferente[$parametros->turnoReferencia];

            $data = new DateTime($parametros->data);
            $service = new ConteudoDesenvolvidoService();
            $retorno->conteudo = null;
            $retorno->codigo = null;

            $conteudoDesenvolvido = $service->buscarConteudo($regencia, $data, $turnoReferente->ed336_codigo);
            if (!is_null($conteudoDesenvolvido)) {
                $retorno->conteudo = $conteudoDesenvolvido->getConteudo();
                $retorno->codigo = $conteudoDesenvolvido->getCodigo();
            }

            break;

        case 'buscarHabilidadesDesenvolvida':
            $habilidadeService = new HabilidadeDesenvolvidaService();
            $habilidades = $habilidadeService->getHabilidadesDesenvolvidas(
                DisciplinaRegistry::get($parametros->disciplinaBncc),
                ConteudoDesenvolvidoRegistry::get($parametros->codigo)
            );

            $toArray = [];
            foreach ($habilidades as $habilidade) {
                if ($configuracao->isReferencialCurricularEstadual()) {
                    $habilidadesReferencial = $habilidade->getHabilidadesReferencial();
                    foreach ($habilidadesReferencial as $habilidadeDesenvolvidaReferencial) {
                        $toArray[] = $habilidadeDesenvolvidaReferencial->getReferencialCurricular()
                            ->getCodigoReferencial();
                    }
                } else {
                    $toArray[] = $habilidade->getCodigoHabilidade();
                }
            }

            $retorno->habilidades = $toArray;
            break;
        case 'salvarConteudo':
            $service = new ConteudoDesenvolvidoService();
            $conteudoDesenvolvido = $service->salvarFromRpc($parametros);
            $retorno->codigo = $conteudoDesenvolvido->getCodigo();
            $retorno->mensagem = "Conteúdo Desenvolvido salvo com sucesso.";
            break;
        case 'salvarHabilidades':
            $habilidadeService = new HabilidadeDesenvolvidaService();
            $habilidadeService->salvarFromRpc($parametros);

            $retorno->mensagem = "Habilidades salva com sucesso.";
            break;
        case 'excluirConteudoDesenvolvido':
            $conteudoDesenvolvidoService = new ConteudoDesenvolvidoService();
            $conteudoDesenvolvidoService->excluir($parametros->codigo);

            $retorno->mensagem = 'Conteúdo excluido com sucesso.';
            break;
    }
} catch (Exception $erro) {
    $retorno->mensagem = $erro->getMessage();
    $retorno->erro = true;
}

db_fim_transacao($retorno->erro);
echo JSON::create()->stringify($retorno);

