<?php

namespace App\Domain\Saude\Farmacia\Strategies;

use App\Domain\Saude\Farmacia\Contracts\MedicamentoBnafarRepository;
use App\Domain\Saude\Farmacia\Contracts\ProcedimentoBnafar;
use App\Domain\Saude\Farmacia\Validators\BnafarValidator;
use DBCompetencia;
use Exception;
use Generator;
use Illuminate\Support\Collection;
use UnidadeProntoSocorro;

abstract class ProcedimentoBnafarStrategy implements ProcedimentoBnafar
{
    const ENTRADA = 1;
    const SAIDA = 2;
    const DISPENSACAO = 3;

    /**
     * @var integer
     */
    protected $idUnidade;

    /**
     * @var string
     */
    protected $cnesUnidade;

    /**
     * @var MedicamentoBnafarRepository
     */
    protected $repository;

    /**
     * @var integer
     */
    protected $codigoMovimentacao;

    /**
     * @var BnafarValidator
     */
    private $validator;

    final public function __construct(
        UnidadeProntoSocorro $unidade,
        MedicamentoBnafarRepository $repository,
        BnafarValidator $validator
    ) {
        $this->idUnidade = $unidade->getCodigo();
        $this->cnesUnidade = $unidade->getCNES();
        $this->repository = $repository;
        $this->validator = $validator;
    }

    /**
     * @param integer $codigoMovimentacao
     * @return ProcedimentoBnafar
     */
    final public function setCodigoMovimentacao($codigoMovimentacao)
    {
        $this->codigoMovimentacao = $codigoMovimentacao;
        return $this;
    }

    /**
     * @param \DateTime[] $periodo
     * @return object
     */
    final public function getSituacaoEnvio(array $periodo)
    {
        $retorno = (object)[
            'tipo' => $this->getTipo(),
            'situacao' => 'AGUARDANDO ENVIO',
            'corSituacao' => '#1165a0',
            'permiteEnvio' => true
        ];

        $repository = $this->repository->scopePeriodo($periodo)->scopeUnidade($this->idUnidade);

        $inQueue = $repository->getProcessamentos();
        if ($inQueue > 0) {
            $retorno->situacao = 'EM PROCESSAMENTO';
            $retorno->corSituacao = '#ff6f00';
            $retorno->permiteEnvio = false;
            return $retorno;
        }

        $dados = $repository->get();
        if (count($dados) == 0) {
            $retorno->situacao = 'SEM DADOS';
            $retorno->corSituacao = '#989898';
            $retorno->permiteEnvio = false;
            return $retorno;
        }

        $dados = $repository->scopeSomenteInconsistencias()->get();
        if (count($dados) > 0) {
            $retorno->situacao = 'POSSUI INCONSISTÊNCIAS';
            $retorno->corSituacao = '#ff2e2e';
            $retorno->permiteEnvio = false;
            return $retorno;
        }

        $dados = $this->buscarDados();
        if (count($dados) == 0) {
            $retorno->situacao = 'CONCLUÍDO';
            $retorno->corSituacao = '#51a011';
            $retorno->permiteEnvio = false;
            return $retorno;
        }

        return $retorno;
    }

    /**
     * @return object
     * @throws Exception
     */
    public function processar()
    {
        if ($this->codigoMovimentacao === null) {
            throw new Exception('Informe o código da movimentação.');
        }
        $dados = $this->buscarDados($this->codigoMovimentacao);

        if ($dados->isEmpty()) {
            throw new Exception('Não foram encontrados registros da movimentação informada.');
        }

        return $this->formatar($dados)->current()[0];
    }

    /**
     * @param array $periodo
     * @return Generator
     */
    public function processarLote(array $periodo)
    {
        $dados = $this->buscarDados(null, $periodo);

        return $this->formatar($dados);
    }

    /**
     * @param array $periodo
     * @return Collection
     */
    public function verificarInconsistencias(array $periodo)
    {
        $movimentacoes = $this->buscarDados(null, $periodo);
        foreach ($movimentacoes as $key => $movimentacao) {
            $this->validator->validar($movimentacao);
            if (!$this->validator->temInconsistencia()) {
                unset($movimentacoes[$key]);
                continue;
            }

            $movimentacao->erros = $this->validator->getErros();
            $movimentacao->erro_bnafar = false;
        }

        return $movimentacoes->concat($this->repository->getInconsistencias());
    }

    /**
     * @param integer $idEstoqueMovimentacao
     * @param array $periodo
     * @return Collection
     */
    final protected function buscarDados($idEstoqueMovimentacao = null, $periodo = [])
    {
        $repository = $this->repository->scopeUnidade($this->idUnidade)->scopeSomentePendentes();

        if ($idEstoqueMovimentacao !== null) {
            $repository->scopeEstoqueMovimentacao($idEstoqueMovimentacao);
        }

        if (!empty($periodo)) {
            $repository->scopePeriodo($periodo);
        }

        return $repository->get();
    }

    /**
     * @param Collection $dados
     * @return Generator
     */
    abstract protected function formatar(Collection $dados);
}
