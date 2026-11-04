<?php

namespace ECidade\RecursosHumanos\ESocial\Integracao\Formatter;

use Cedencia;
use ECidade\RecursosHumanos\ESocial\Entity\Servidor;
use ECidade\RecursosHumanos\ESocial\Service\ServidorService;
use ECidade\RecursosHumanos\Pessoal\Repository\ServidorMovimentacaoRepository;
use stdClass;
use CgmJuridico;
use DBPessoal;
use ECidade\RecursosHumanos\ESocial\Transformer\S2231;

/**
 * Class CessaoExercicioFormatter
 * @package ECidade\RecursosHumanos\ESocial\Integracao\Formatter
 */
class CessaoExercicioFormatter extends Formatter
{
    /**
     * @var Servidor
     */
    private $servidorAtual;

    /**
     * @var CgmJuridico
     */
    private $empregador;

    /**
     * @var Object
     */
    private $dadosCessao = null;

    /**
     * @var Boolean
     */
    private $enviadoAnteriormente = false;

    /**
     * @var Boolean
     */
    private $processaAtual = true;

    /**
     * @param  array $dados
     * @return mixed|stdClass[]
     * @throws \BusinessException
     * @throws \DBException
     */
    public function formatar($dados)
    {
        $dadosServidor = [];
        foreach ($dados as $servidor) {
            if ($servidor->temVinculoEmpregaticio() && $servidor->isAtivo()) {
                $servidorAtual = $this->processamento($servidor);
                if ($this->processaAtual) {
                    $dadosServidor[] = $servidorAtual;
                }
            }
        }
        return $dadosServidor;
    }

    /**
     * @param  $dadosFormatado
     * @return mixed
     * @throws \BusinessException
     * @throws \DBException
     */
    private function processamento($servidor)
    {
        $this->servidorAtual = $servidor;
        $dadoServidor = new stdClass();
        $dadoServidor->inscricao_empregador = $this->getEmpregador()->getCnpj();

        $matricula = $this->servidorAtual->getMatricula();
        $this->dadosCessao = new Cedencia($matricula);

        $dataObrigatoriedade = DBPessoal::getDataFaseEsocial(2)->getDate();
        $dataMovimentacao = $this->dadosCessao->getDataMovimentacao();

        // Servidor só poderá ser processado, caso a data da movimentação
        // seja posterior, ou igual a data de obrigatoriedade.
        if ((!isset($dataMovimentacao) || $dataMovimentacao->getDate() < $dataObrigatoriedade) &&
        !$this->dadosCessao->getTipoCedencia()) {
            $this->processaAtual = false;
        } else {
            $this->processaAtual = true;
        }

        $dadoServidor->referencia =  $matricula . '_' . $this->getEmpregador()->getCnpj();
        $cessaoRecibo = new S2231($matricula);

        /**
         * Validacao para verificar se o servidor foi enviado anteriormente.
         * Data inicio apenas -> Nao consulta na api
         * Data fim preenchida -> Consulta na api
         * Se tiver recibo montar identificador com dtTerm.
         */
        $this->enviadoAnteriormente = false;

        if (!empty($this->dadosCessao->getDataMovimentacao()) && empty($this->dadosCessao->getDataDevolucao())) {
            $dadoServidor->referencia .= '_inicio_' . $dataMovimentacao->getDate();
        } elseif (!empty($this->dadosCessao->getDataDevolucao())) {
            $refInicio = $dadoServidor->referencia .= '_inicio_' . $dataMovimentacao->getDate();
            $this->enviadoAnteriormente = $cessaoRecibo->buscarDados($refInicio);
            if ($this->enviadoAnteriormente) {
                $dadoServidor->referencia .= '_fim_' . $this->dadosCessao->getDataDevolucao()->getDate();
            }
        }

        $this->atualizarDadosServidor($dadoServidor);
        return $dadoServidor;
    }

    /**
     * @param  $dadoServidor
     * @throws \BusinessException
     * @throws \DBException
     * @throws \Exception
     */
    private function atualizarDadosServidor(&$dadoServidor)
    {
        $servidorMovimentacaoRepository = new ServidorMovimentacaoRepository();
        $servidorMovimentacaoModel = $servidorMovimentacaoRepository
            ->scopeAno($this->servidorAtual->getAnoCompetencia())
            ->scopeMes($this->servidorAtual->getMesCompetencia())
            ->scopeMatricula($this->servidorAtual->getMatricula())
            ->first();

        $codigoSequencialEndereco = $this->servidorAtual->getCgm()->getEnderecoPrimario();
        if (!is_numeric($codigoSequencialEndereco)) {
            $matricula = $this->servidorAtual->getMatricula();
            $nome = $this->servidorAtual->getCgm()->getNome();
            throw new \Exception("Nenhum endereço encontrado para o servidor {$matricula}-{$nome}. Favor revisar.");
        }

        $servidorService = new ServidorService($this->servidorAtual, $servidorMovimentacaoModel, $dadoServidor);
        $servidorEntity = $servidorService->buscarDadosServidor();

        $this->removerCamposNaoUtilizados($dadoServidor);
        $this->atualizarGrupoCessao($dadoServidor, $servidorEntity);
    }

    private function removerCamposNaoUtilizados(&$dadoServidor)
    {
        if (!(empty($dadoServidor->trabalhador) && empty($dadoServidor->vinculo))) {
            unset($dadoServidor->trabalhador);
            unset($dadoServidor->vinculo);
        }
    }

    /**
     * @param $dadoServidor
     * @param Servidor $servidorEntity
     */
    private function atualizarGrupoCessao(&$dadoServidor, Servidor $servidorEntity)
    {
        $dadoServidor->ideVinculo = [];

        $dadosVinculo = $servidorEntity->getVinculoTrabalho();
        $dadosPessoaisServidor = $servidorEntity->getDadosTrabalhador();

        $dadoServidor->ideVinculo['matricula'] = $dadosVinculo['matricula'];
        if ($this->servidorAtual->getMatriculaAnterior() != "") {
            $dadoServidor->ideVinculo['matricula'] = $this->servidorAtual->getMatriculaAnterior();
        }
        $dadoServidor->ideVinculo['cpfTrab'] = $dadosPessoaisServidor['cpfTrab'];

        $dadoServidor->infoCessao = [];
        $dadoServidor->infoCessao['iniCessao'] = [];

        if (!$this->enviadoAnteriormente) {
            $dataMovimentacao = $this->dadosCessao->getDataMovimentacao()
                ? $this->dadosCessao->getDataMovimentacao()->getDate() : '';
            $dadoServidor->infoCessao['iniCessao']['dtIniCessao'] = $dataMovimentacao;
            $dadoServidor->infoCessao['iniCessao']['cnpjCess'] = $this->dadosCessao->getCnpjCedencia();
            $dadoServidor->infoCessao['iniCessao']['respRemun'] = $this->dadosCessao->getServidorCedido();
        } else {
            $dadoServidor->infoCessao['fimCessao'] = [];
            $dtDevolucao = $this->dadosCessao->getDataDevolucao()
                ? $this->dadosCessao->getDataDevolucao()->getDate() : '';
            $dadoServidor->infoCessao['fimCessao']['dtTermCessao'] = $dtDevolucao;
        }
        $this->regraIniCessao($dadoServidor);
    }

    private function regraIniCessao(&$dadoServidor)
    {
        if (empty($dadoServidor->infoCessao['iniCessao']['dtIniCessao']) &&
            empty($dadoServidor->infoCessao['iniCessao']['cnpjCess']) &&
            empty($dadoServidor->infoCessao['iniCessao']['respRemun'])
        ) {
            unset($dadoServidor->infoCessao['iniCessao']);
        }

        if (empty($dadoServidor->infoCessao['fimCessao']['dtTermCessao'])) {
            unset($dadoServidor->infoCessao['fimCessao']);
        }
    }

    /**
     * Get the value of empregador
     *
     * @return  CgmJuridico
     */
    public function getEmpregador()
    {
        return $this->empregador;
    }

    /**
     * Set the value of empregador
     *
     * @param  CgmJuridico  $empregador
     *
     * @return  self
     */
    public function setEmpregador(CgmJuridico $empregador)
    {
        $this->empregador = $empregador;
    }
}
