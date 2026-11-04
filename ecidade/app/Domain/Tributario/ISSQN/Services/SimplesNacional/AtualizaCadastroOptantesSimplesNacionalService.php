<?php

namespace App\Domain\Tributario\ISSQN\Services\SimplesNacional;

use App\Domain\Tributario\ISSQN\Repository\SimplesNacional\AtualizaCadastroOptantesSimplesNacionalRepository;
use App\Domain\Tributario\ISSQN\Repository\SimplesNacional\AtualizaDataCadastrosRepository;
use DateTime;

class AtualizaCadastroOptantesSimplesNacionalService
{
    /**
     * @var ConsultaApiSimplesNacionalService
     */
    private $apiService;

    /**
     * @var AtualizaCadastroOptantesSimplesNacionalRepository
     */
    private $atualizaCadastroRepository;

    /**
     * @var AtualizaDataCadastrosRepository
     */
    private $horarioAtualizacoesRepository;

    /**
     * Horario para as atualizacoes periodias
     *
     * @var integer
     */
    private $horarioAtualizacoes;

    /**
     * Dia da semana para as atualizacoes periodias
     *
     * @var integer
     */
    private $diaDaSemanaAtualizacoes;

    /**
     * Dia do mes para as atualizacoes periodias
     *
     * @var integer
     */
    private $diaDoMesAtualizacoes;

    /**
     * Metodo construtor da classe
     */
    public function __construct(
        ConsultaApiSimplesNacionalService $apiService,
        AtualizaCadastroOptantesSimplesNacionalRepository $atualizaCadastroRepository,
        AtualizaDataCadastrosRepository $horarioAtualizacoesRepository
    ) {
        $this->apiService = $apiService;
        $this->atualizaCadastroRepository = $atualizaCadastroRepository;
        $this->horarioAtualizacoesRepository = $horarioAtualizacoesRepository;
    }

    /**
     * @return AtualizaCadastroOptantesSimplesNacionalService
     */
    public static function factory()
    {
        $apiService = new ConsultaApiSimplesNacionalService();
        $atualizaCadastroRepository = new AtualizaCadastroOptantesSimplesNacionalRepository();
        $horarioAtualizacoesRepository = new AtualizaDataCadastrosRepository();

        return new AtualizaCadastroOptantesSimplesNacionalService(
            $apiService,
            $atualizaCadastroRepository,
            $horarioAtualizacoesRepository
        );
    }

    /**
     * Executa esse servico de atualizacao cadastral.
     *
     * @return void
     */
    public function execute()
    {
        $infoAtualizacoes = $this->horarioAtualizacoesRepository->getHorarioAtualizacoes();
        $frequenciaAtualizacoes = $infoAtualizacoes->frequencia;

        $this->diaDaSemanaAtualizacoes = intval($infoAtualizacoes->diaDaSemana);
        $this->diaDoMesAtualizacoes = intval($infoAtualizacoes->diaDoMes);
        $this->horarioAtualizacoes = strtotime($infoAtualizacoes->horario);

        switch ($frequenciaAtualizacoes) {
            case 1:
                $this->atualizacaoDiaria();
                break;
            case 2:
                $this->atualizacaoSemanal();
                break;
            case 3:
                $this->atualizacaoMensal();
                break;
            default:
                break;
        }

        return;
    }

    /**
     * Executa a atualizacao diaria
     * caso horario esteja correto
     *
     * @return void
     */
    private function atualizacaoDiaria()
    {
        if ($this->horarioCorreto()) {
            $this->atualizaCadastros();
        }

        return;
    }

    /**
     * Executa a atualizacao semanal
     * caso dia da semana e horario
     * estejam corretos
     *
     * @return void
     */
    private function atualizacaoSemanal()
    {
        if ($this->diaDaSemanaCorreto() && $this->horarioCorreto()) {
            $this->atualizaCadastros();
        }

        return;
    }

    /**
     * Executa a atualizacao mensal
     * caso dia do mes e horario
     * estejam corretos
     *
     * @return void
     */
    private function atualizacaoMensal()
    {
        if ($this->diaDoMesCorreto() && $this->horarioCorreto()) {
            $this->atualizaCadastros();
        }

        return;
    }

    /**
     * Valida se horario de agora esta de acordo
     * com o horario que deveria ser a atualizacao
     *
     * @return boolean
     */
    private function horarioCorreto()
    {
        $horaAtual = strtotime(date('H:i:s'));
        $horarioAtualizacoes = $this->horarioAtualizacoes;
        $horarioAtualizacoesComAcrescimo = $horarioAtualizacoes + 3600;

        if ($horaAtual >= $horarioAtualizacoes && $horaAtual <= $horarioAtualizacoesComAcrescimo) {
            return true;
        }

        return false;
    }

    /**
     * Valida se o dia da semana de hoje esta de acordo
     * com o dia da semana que deveria ser a atualizacao
     *
     * @return boolean
     */
    private function diaDaSemanaCorreto()
    {
        $diaDaSemanaAtualizacoes = $this->diaDaSemanaAtualizacoes;
        $diaDaSemana = intval(date(
            'w',
            strtotime(Date('Y-m-d'))
        ));

        if ($diaDaSemana == $diaDaSemanaAtualizacoes) {
            return true;
        }

        return false;
    }

    /**
     * Valida se o dia do mes de hoje esta de acordo
     * com o dia do mes que deveria ser a atualizacao
     *
     * @return boolean
     */
    private function diaDoMesCorreto()
    {
        $diaDoMes = intval(Date('d'));
        $diaDoMesAtualizacoes = $this->diaDoMesAtualizacoes;

        if ($diaDoMes == $diaDoMesAtualizacoes) {
            return true;
        }

        return false;
    }

    /**
     * Metodo para atualizar cadastros dos optantes pelo simples nacional
     * de acordo com os dados atualizados vindos de uma api externa que
     * consulta a Receita Federal.
     */
    public function atualizaCadastros()
    {
        $cnpjsBuscados = $this->atualizaCadastroRepository->cnpjsCadastradosIssBase();
        $dadosApi = $this->apiService->getDadosCadastros($cnpjsBuscados);

        foreach ($dadosApi as $registroApi) {
            //dados vindos da api
            $cnpjApi = $registroApi['cnpj'];
            $porteApi = $registroApi['porte'];
            $cnpjSimplesNacionalApi = $registroApi['simples']['simples'];
            $dataOpcaoSimplesApi = $registroApi['simples']['dataOpcaoSimples'] != null ?
                (new DateTime($registroApi['simples']['dataOpcaoSimples']))->format('Y/m/d') :
                null;
            $dataExclusaoSimplesApi = ($registroApi['simples']['dataExclusaoSimples'] != null ?
                (new DateTime($registroApi['simples']['dataExclusaoSimples']))->format('Y/m/d') :
                null) != null ? (new DateTime($registroApi['simples']['dataExclusaoSimples']))
                ->format('Y/m/d') :
                Date('Y-m-d');
            $dataOpcaoMeiApi = $registroApi['mei']['dataOpcaoMei'] != null ?
                (new DateTime($registroApi['mei']['dataOpcaoMei']))->format('Y/m/d') :
                null;
            $dataOpcaoSimplesApi = $porteApi == 3 ? $dataOpcaoMeiApi : $dataOpcaoSimplesApi;

            //dados vindos do ecidade
            $registroEcidade = $this->atualizaCadastroRepository->consultaCadastroCnpj($cnpjApi)[0];
            $dataOpcaoEcidade = $registroEcidade->datainclusaoisscadsimples != null ?
                (new DateTime($registroEcidade->datainclusaoisscadsimples))->format('Y/m/d') :
                null;
            $sequencialIssCadSimples = $registroEcidade->sequencialisscadsimples;
            $inscricaoIssBaseEcidade = $registroEcidade->inscricao;
            $optanteSimplesEcidade = $registroEcidade->cadastradocomosimples;
            $comDataDeBaixaEcidade = $optanteSimplesEcidade ? $registroEcidade->comdatadebaixa :
                false;
            $porteEcidade =  $registroEcidade->porte;

            if ($cnpjSimplesNacionalApi) {
                if ($optanteSimplesEcidade) {
                    if ($comDataDeBaixaEcidade) {
                        // API: Simples Nacional
                        // ECIDADE: Simples Nacional, com data de baixa
                        // ACAO: Inclui registro isscadsimples
                        $this->atualizaCadastroRepository->incluiRegistroIssCadSimples(
                            $inscricaoIssBaseEcidade,
                            $dataOpcaoSimplesApi,
                            $porteApi
                        );
                    } else {
                        // API: Simples Nacional
                        // ECIDADE: Simples Nacional, sem data de baixa
                        // CONDICAO: Se porte da api for diferente do porte do ecidade
                        // ACAO: Inclui baixa e novo registro isscadsimples
                        if ($porteApi != $porteEcidade
                            || $dataOpcaoSimplesApi != $dataOpcaoEcidade) {
                            $this->atualizaCadastroRepository->incluiRegistroBaixaIssCadSimplesBaixa(
                                $sequencialIssCadSimples,
                                $dataExclusaoSimplesApi
                            );
                            $this->atualizaCadastroRepository->incluiRegistroIssCadSimples(
                                $inscricaoIssBaseEcidade,
                                $dataOpcaoSimplesApi,
                                $porteApi
                            );
                        }
                    }
                } else {
                    // API: Simples Nacional
                    // ECIDADE: Nao e Simples
                    // ACAO: Inclui registro isscadsimples
                    $this->atualizaCadastroRepository->incluiRegistroIssCadSimples(
                        $inscricaoIssBaseEcidade,
                        $dataOpcaoSimplesApi,
                        $porteApi
                    );
                }
            } else {
                if ($optanteSimplesEcidade) {
                    if ($comDataDeBaixaEcidade) {
                        // API: Nao e Simples
                        // ECIDADE: Simples Nacional, com data de baixa
                        // ACAO: Sem acao
                    } else {
                        // API: Nao e Simples
                        // ECIDADE: Simples Nacional, sem data de baixa
                        // ACAO: Inclui baixa
                        $this->atualizaCadastroRepository->incluiRegistroBaixaIssCadSimplesBaixa(
                            $sequencialIssCadSimples,
                            $dataExclusaoSimplesApi
                        );
                    }
                } else {
                    // API: Nao e Simples
                    // ECIDADE: Nao e Simples
                    // ACAO: Sem acao
                }
            }
        }
    }
}
