<?php

namespace App\Domain\Educacao\CentralMatriculas\Controllers;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Educacao\CentralMatriculas\Services\AlocacaoAutomaticaService;
use App\Domain\Educacao\CentralMatriculas\Services\ListaEsperaService;
use App\Domain\Educacao\CentralMatriculas\Services\VagasPcdService;
use App\Http\Controllers\Controller;
use ECidade\Educacao\MatriculaOnline\Registry\CandidatoRegistry;
use ECidade\Educacao\MatriculaOnline\Registry\EscolasRegistry;
use ECidade\Educacao\MatriculaOnline\Registry\FaseRegistry;
use ECidade\Educacao\MatriculaOnline\Repository\CicloRepository;
use ECidade\Educacao\MatriculaOnline\Repository\ConfiguracaoEscolaRepository;
use ECidade\Educacao\MatriculaOnline\Repository\InscricaoRepository;
use ECidade\Educacao\MatriculaOnline\Repository\ListaEsperaRepository;
use ECidade\Educacao\MatriculaOnline\Repository\ListaEspera;
use App\Domain\Educacao\CentralMatriculas\Models\ListaEspera as ListaEsperaModel;
use ECidade\Educacao\MatriculaOnline\Service\PrevisaoVagas;
use ECidade\Enum\Educacao\MatriculaOnline\SituacoesListaEsperaEnum;
use Exception;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use JSON;
use stdClass;

class ListaEsperaController extends Controller
{
    protected $service;

    public function __construct(ListaEsperaService $service)
    {
        $this->service = $service;
    }

    public function getEtapasListaEspera()
    {
        return new DBJsonResponse(($this->service->getEtapasListaEspera()));
    }

    /**
     * @throws Exception
     */
    public function getEscolasListaEspera($etapa)
    {
        return new DBJsonResponse($this->service->getEscolasListaEspera($etapa));
    }

    /**
     * @throws Exception
     */
    public function getTurnosListaEspera($etapa, $escola)
    {
        return new DBJsonResponse($this->service->getTurnosListaEspera($etapa, $escola));
    }

    public function getCandidatosListaEspera($etapa, $escola, $turno)
    {
        return new DBJsonResponse($this->service->getCandidatosListaEspera($etapa, $escola, $turno));
    }

    /**
     * @return DBJsonResponse
     * @throws Exception
     */
    public function index(Request $request)
    {
        if (empty($request->get('fase'))) {
            throw new Exception('Informe a Fase');
        }
        if (empty($request->get('etapa'))) {
            throw new Exception('Informe a Etapa');
        }
        if (empty($request->get('escola'))) {
            throw new Exception('Informe a Escola');
        }

        $codigoFase = $request->get('fase');
        $codigoEtapa = $request->get('etapa');
        $codigoEscola = $request->get('escola');
        $codigoTurno = $request->get('turno');

        $listaEsperaRepository = new ListaEsperaRepository();
        $listaEsperaRepository->scopeFase(FaseRegistry::get($codigoFase));
        $listaEsperaRepository->scopeEtapa(new \Etapa($codigoEtapa));
        $listaEsperaRepository->scopeEscola(EscolasRegistry::get($codigoEscola));
        $listaEsperaRepository->scopeNaoAlocado();
        $listaEsperaRepository->scopeSituacao(new SituacoesListaEsperaEnum(SituacoesListaEsperaEnum::ATIVA));
        $listaEsperaRepository->scopeTurno(new \Turno($codigoTurno));
        $lista = $listaEsperaRepository->get();

        $listaEspera = [];
        foreach ($lista as $item) {
            $inscricaoRepository = new InscricaoRepository();
            $inscricaoRepository->scopeCandidato($item->getCandidato());
            $inscricao = $inscricaoRepository->findByScope();
            $listaEspera[] = (object)[
                "protocolo" => $inscricao->getProtocolo(),
                "classificacao" => $item->getClassificacao(),
                "redeorigem" => $item->getCandidato()->getRedeOrigem()->getDescricao(),
            ];
        }
        usort($listaEspera, function ($a, $b) {
            return (int)$a->classificacao > (int)$b->classificacao;
        });
        return new DBJsonResponse($listaEspera);
    }

    /**
     * @throws Exception
     */
    public function buscarVagasPcd(Request $request)
    {
        validaRequest($request->all(), [
            'fase' => 'required|integer',
            'etapa' => 'required|integer',
        ]);

        $fase = FaseRegistry::get($request->get('fase'));
        $etapa = new \Etapa($request->get('etapa'));

        $ciclo = $fase->getCiclo();
        $ciclosRepository = new CicloRepository();
        $ciclosRepository->carregarEnsinos($ciclo);

        $configuracaoEscolaRepository = new ConfiguracaoEscolaRepository();
        $configuracoesEscola = $configuracaoEscolaRepository
            ->scopeEnsinos($ciclo->getEnsinos())->scopeEtapa($etapa)->get();

        $vagasPcdService = new VagasPcdService();

        $listaVagasPcd = [];
        $turnos = [];
        foreach ($configuracoesEscola as $configuracaoEscola) {
            $vagaPcd = $vagasPcdService->buscarVagasPcd(
                $fase,
                $configuracaoEscola->getEscola(),
                $etapa,
                $configuracaoEscola->getTurno()
            );

            if (!array_key_exists($vagaPcd->mo64_escola, $listaVagasPcd)) {
                $listaVagasPcd[$vagaPcd->mo64_escola] = (object)[
                    'escola' => [
                        'mo53_codigo' => $vagaPcd->escola->mo53_codigo,
                        'mo53_nome' => $vagaPcd->escola->mo53_nome
                    ],
                    'vagas' => []
                ];
            }

            $previsaoVagas = PrevisaoVagas::resumo(
                $configuracaoEscola->getEscola()->getCodigo(),
                $configuracaoEscola->getTurno()->getCodigoTurno(),
                $etapa->getCodigo(),
                $fase->getCodigo(),
                $fase->getAno()
            );
            $listaVagasPcd[$vagaPcd->mo64_escola]->vagas[] = (object)[
                'turno' => $vagaPcd->mo64_turno,
                'vagas' => $vagaPcd->mo64_vagas,
                'deficientes' => $previsaoVagas->deficientes
            ];

            $turnos[$vagaPcd->mo64_turno] = (object)[
                'codigo' => $configuracaoEscola->getTurno()->getCodigoTurno(),
                'descricao' => $configuracaoEscola->getTurno()->getDescricao()
            ];
        }

        return new DBJsonResponse((object)[
            'vagasPcd' => array_values($listaVagasPcd),
            'turnos' => array_values($turnos)
        ]);
    }

    public function salvarVagasPcd(Request $request)
    {
        validaRequest($request->all(), [
            'fase' => 'required|integer',
            'etapa' => 'required|integer',
            'vagas' => 'required|string'
        ]);

        $fase = FaseRegistry::get($request->get('fase'));
        $etapa = new \Etapa($request->get('etapa'));
        $vagasPcd = JSON::create()->parse(str_replace("\\", "", $request->get('vagas')));

        $vagasPcdService = new VagasPcdService();
        $vagasPcdService->salvarVagasPcd($fase, $etapa, $vagasPcd);

        return new DBJsonResponse([], 'Vagas PCD salvas com sucesso');
    }

    /**
     * @throws Exception
     */
    public function processarAlocacoes(Request $request)
    {
        validaRequest($request->all(), [
            'fase' => 'required|integer',
        ]);

        $fase = FaseRegistry::get($request->get('fase'));
        if (!$fase->isProcessada()) {
            $mensagem = 'A fase ainda não foi processada, a fase deve ser processada para gerar a lista de espera';
            throw new Exception($mensagem);
        }
        if ($fase->isEncerrada()) {
            throw new Exception('A fase já foi encerrada');
        }

        Log::info("Começando processamento de alocacoes.");
        $alocacaoAutomaticaService = new AlocacaoAutomaticaService();
        $alocacaoAutomaticaService->setFase($fase);
        $alocacaoAutomaticaService->processarAlocacoes();
        return new DBJsonResponse([], 'Alocações processadas com sucesso');
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    public function listaImpedida(Request $request)
    {
        $faseRepository = new \ECidade\Educacao\MatriculaOnline\Repository\FaseRepository();
        $fases = $faseRepository->scopeProcessada()->scopeAtiva()->get();

        $listasImpedidas = [];
        foreach ($fases as $fase) {
            $alocacaoAutomaticaService = new AlocacaoAutomaticaService();
            $alocacaoAutomaticaService->setFase($fase);
            $listasImpedidas = array_merge($listasImpedidas, $alocacaoAutomaticaService->buscarListaImpedida());
        }

        return new DBJsonResponse($listasImpedidas);
    }

    /**
     * @param Request $request
     * @return DBJsonResponse
     * @throws Exception
     */
    /* public function cancelarInscricao(Request $request)
    {
        validaRequest($request->all(), [
            'lista' => 'required|integer',
            'doc' => 'required|numeric',
        ]);

        if (!isCpf($request->doc)) {
            throw new Exception('CPF inválido');
        }

        $lista = ListaEsperaModel::where('mo18_sequencial', $request->lista)
            ->with('base')
            ->first();

        if ($request->doc !== $lista->base->mo01_cpfresp) {
            throw new Exception('O CPF do responsavel é inválido');
        }

        $data = date('d/m/Y');
        $hora = date('H:i');
        $cpf = formatCpf($request->doc);

        db_inicio_transacao();

        $parametros = new stdClass();
        $parametros->codigoListaEspera = $request->lista;
        $observacao = "Protocolo cancelado pelo responsável de CPF {$cpf} na data {$data} no horário {$hora}";
        $parametros->observacao = $observacao;
        $parametros->tipoSituacao = 3;
        $parametros->retiraTodasListas = true;
        $parametros->notificarCandidato = false;
        ListaEspera::atualizarCandidato($parametros);

        $inscricoes = (new InscricaoRepository())->scopeCandidato(CandidatoRegistry::get($lista->mo18_base))->get();

        if (empty($inscricoes[0])) {
            throw new Exception('Erro ao consultar fase.');
        }

        $fase = FaseRegistry::get($inscricoes[0]->getFase()->getCodigo());

        $listaEscolaEtapaTurno = ListaEspera::buscarEtapaEscolaEnsinoPorFase(new \Fase($fase->getCodigo()));

        foreach ($listaEscolaEtapaTurno as $etapaEscolaTurno) {
            $listaEsperaService = new ListaEsperaService();
            $listaEsperaService->setFase($fase)
                ->setEscola($etapaEscolaTurno['escola'])
                ->setEtapa($etapaEscolaTurno['etapa'])
                ->setTurno($etapaEscolaTurno['turno'])
                ->classificar();
        }

        db_fim_transacao();

        return new DBJsonResponse([], 'Cancelamento realizado com sucesso.');
    } */
}
