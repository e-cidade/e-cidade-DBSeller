<?php


namespace App\Domain\Financeiro\Planejamento\Services;

use App\Domain\Financeiro\Orcamento\Models\Dotacao as DotacaoApi;
use App\Domain\Financeiro\Orcamento\Models\Parametro;
use App\Domain\Financeiro\Orcamento\Models\Receita as ReceitaApi;
use App\Domain\Financeiro\Planejamento\Models\DetalhamentoDespesa;
use App\Domain\Financeiro\Planejamento\Models\EstimativaReceita;
use App\Domain\Financeiro\Planejamento\Models\Iniciativa;
use App\Domain\Financeiro\Planejamento\Models\Planejamento;
use App\Domain\Financeiro\Planejamento\Models\ProgramaEstrategico;
use App\Domain\Financeiro\Planejamento\Models\Valor;
use ECidade\Enum\Financeiro\Planejamento\TipoEnum;
use ECidade\Financeiro\Orcamento\Service\AcompanhamentoDesembolsoDespesaService;
use ECidade\Financeiro\Orcamento\Service\AcompanhamentoDesembolsoReceitaService;
use ECidade\Financeiro\Orcamento\Model\Dotacao;
use ECidade\Financeiro\Orcamento\Model\Receita;
use ECidade\Financeiro\Orcamento\Repository\DotacaoRepository;
use ECidade\Financeiro\Orcamento\Repository\ReceitaRepository;
use Exception;

class GerarOrcamentoService
{
    /**
     * @var Planejamento
     */
    private $planejamento;
    /**
     * @var integer
     */
    private $exercicio;
    /**
     * @var DotacaoRepository
     */
    private $repositoryDespesa;
    /**
     * @var ReceitaRepository
     */
    private $repositoryReceita;
    /**
     * @var integer
     */
    private $exercicioAtual;
    /**
     * @var integer
     */
    private $codigoNovaDotacao;
    /**
     * @var string
     */
    private $dataCriacao;

    /**
     * GerarOrcamentoService constructor.
     * @param $planejamento_id
     * @throws Exception
     */
    public function __construct($planejamento_id)
    {
        $planejamento = Planejamento::find($planejamento_id);
        if ($planejamento->pl2_tipo !== TipoEnum::LOA) {
            throw new Exception('Só é possivel gerar o orçamento através de uma LOA', 403);
        }

        $this->planejamento = $planejamento;
        $this->exercicio = $this->planejamento->pl2_ano_inicial;
        $this->dataCriacao = "{$this->exercicio}-01-01";
        $this->exercicioAtual = $this->planejamento->pl2_ano_inicial - 1;

        $this->repositoryDespesa = new DotacaoRepository();
        $this->repositoryReceita = new ReceitaRepository();

        $this->inicializarCodigoDotacao();
    }

    public function gerar()
    {

        if ($this->existeOrcamento()) {
            throw new Exception("Já existe orçamento para o exercício {$this->exercicio}.");
        }

        $this->excluir();

        $this->gerarDespesa();
        $this->gerarReceita();
    }

    /**
     * @throws Exception
     */
    public function excluir()
    {
        $this->excluirDotacoes();
        $this->excluirReceitas();
    }

    /**
     * @throws Exception
     */
    private function excluirDotacoes()
    {
        $acompanhamento = new AcompanhamentoDesembolsoDespesaService();
        $acompanhamento->excluir($this->planejamento->pl2_ano_inicial);

        $repository = new DotacaoRepository();
        $repository->scopeAno($this->planejamento->pl2_ano_inicial)
            ->excluirByScope();
    }

    /**
     * @throws Exception
     */
    private function excluirReceitas()
    {
        $acompanhamento = new AcompanhamentoDesembolsoReceitaService();
        $acompanhamento->excluir($this->planejamento->pl2_ano_inicial);

        $repository = new ReceitaRepository();
        $repository->scopeAno($this->planejamento->pl2_ano_inicial)
            ->excluirByScope();
    }

    /**
     * @return bool
     */
    private function existeOrcamento()
    {
        $exercicio = $this->planejamento->pl2_ano_inicial;
        $dotacao = DotacaoApi::query()->where('o58_anousu', '=', $exercicio)->first();
        $receita = ReceitaApi::query()->where('o70_anousu', '=', $exercicio)->first();

        if (!is_null($dotacao) && !is_null($receita)) {
            return true;
        }

        return false;
    }

    /**
     * @throws Exception
     */
    private function gerarDespesa()
    {
        $detalhamento = $this->getDetalhamentoDespesa();

        $detalhamento->each(function (DetalhamentoDespesa $despesa) {
            $this->salvarDotacao($despesa);
        });
        $this->atualizaParametroCodigoDotacao();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    private function getDetalhamentoDespesa()
    {
        $detalhamento = collect([]);
        $this->planejamento->programas->map(function (ProgramaEstrategico $programaEstrategico) use ($detalhamento) {
            $programaEstrategico->iniciativas->map(function (Iniciativa $iniciativa) use ($detalhamento) {
                $iniciativa->detalhamentoDespesa->each(function (DetalhamentoDespesa $despesa) use ($detalhamento) {
                    $detalhamento->push($despesa);
                });
            });
        });
        return $detalhamento;
    }

    /**
     * @param DetalhamentoDespesa $despesa
     * @return integer
     * @throws Exception
     */
    private function getCodigoDotacao(DetalhamentoDespesa $despesa)
    {
        $dotacao = $this->repositoryDespesa->scopeAno($this->exercicioAtual)
            ->scopeOrgao($despesa->pl20_orcorgao)
            ->scopeUnidade($despesa->pl20_orcunidade)
            ->scopeFuncao($despesa->pl20_orcfuncao)
            ->scopeSubfuncao($despesa->pl20_orcsubfuncao)
            ->scopePrograma($despesa->iniciativa->programaEstrategico->pl9_orcprograma)
            ->scopeProjeto($despesa->iniciativa->pl12_orcprojativ)
            ->scopeElemento($despesa->pl20_orcelemento)
            ->scopeRecurso($despesa->pl20_recurso)
            ->scopeLocalizadorGastos($despesa->pl20_subtitulo)
            ->first();

        $this->repositoryDespesa->resetScopes();

        if ($dotacao instanceof Dotacao) {
            return $dotacao->getCodigoDotacao();
        }

        return ++$this->codigoNovaDotacao;
    }

    private function inicializarCodigoDotacao()
    {
        $parametro = Parametro::query()->find($this->exercicioAtual);
        $this->codigoNovaDotacao = $parametro->o50_coddot;
    }

    private function atualizaParametroCodigoDotacao()
    {
        $parametro = Parametro::find($this->exercicio);
        $parametro->o50_coddot = $this->codigoNovaDotacao;
        $parametro->save();
    }

    /**
     * @param DetalhamentoDespesa $despesa
     * @return null
     * @throws Exception
     */
    private function salvarDotacao(DetalhamentoDespesa $despesa)
    {
        $codigo = $this->getCodigoDotacao($despesa);
        $valor = $this->getValorExercicio($despesa->getValores());

        /**
         * @var $iniciativa
         */
        $iniciativa = $despesa->iniciativa;
        if (empty($valor)) {
            return false;
            // a Pedido do paulo foi removido a validação e ignorado previsão
            /*
            throw new Exception(sprintf(
                "Não foi informado previsão para estimativa da despesa.\n Programa: %s, Iniciativa: %s, Estrutural: %s",
                $iniciativa->programaEstrategico->getProgramaOrcamento()->formataCodigo(),
                $iniciativa->getIniciativaOrcamento()->formataCodigo(),
                $despesa->getEstrutural()
            ));
            */
        }

        $dotacao = new Dotacao();
        $dotacao->setCodigoDotacao($codigo)
            ->setAno($this->exercicio)
            ->setIdOrgao($despesa->pl20_orcorgao)
            ->setIdUnidade($despesa->pl20_orcunidade)
            ->setIdFuncao($despesa->pl20_orcfuncao)
            ->setIdSubfuncao($despesa->pl20_orcsubfuncao)
            ->setIdPrograma($iniciativa->programaEstrategico->pl9_orcprograma)
            ->setIdProjeto($iniciativa->pl12_orcprojativ)
            ->setIdElemento($despesa->pl20_orcelemento)
            ->setIdRecurso($despesa->pl20_recurso)
            ->setLocalizadorGastos($despesa->pl20_subtitulo)
            ->setCaracteristicaPeculiar($despesa->pl20_concarpeculiar)
            ->setEsferaOrcamentaria($despesa->pl20_esferaorcamentaria)
            ->setIdInstituicao($despesa->pl20_instituicao)
            ->setDataCriacao($this->dataCriacao)
            ->setValor($valor);

        $dotacaoRepository = new DotacaoRepository();
        $dotacao = $dotacaoRepository->save($dotacao);

        $this->cronogramaDesembolsoDespesa($dotacao, $despesa);
    }

    private function gerarReceita()
    {
        $receitas = $this->getDetalhamentoReceita();
        $receitas->each(function (EstimativaReceita $receita) {
            $this->salvarReceita($receita);
        });
    }

    private function getDetalhamentoReceita()
    {
        return $this->planejamento->estimativaReceita;
    }

    private function salvarReceita(EstimativaReceita $receita)
    {
        $codigo = $this->getCodigoReceita($receita);
        $valor = $this->getValorExercicio($receita->getValores());

        $novaReceita = new Receita();
        $novaReceita->setAno($this->exercicio)
            ->setReduzido($codigo)
            ->setIdFonte($receita->orcfontes_id)
            ->setTipoRecurso($receita->recurso_id)
            ->setValor($valor)
            ->setStatusReceita('false')
            ->setIdInstituicao($receita->instituicao_id)
            ->setCaracteriscaPeculiar($receita->concarpeculiar_id)
            ->setDataCriacao($this->dataCriacao)
            ->setIdOrgao($receita->orcorgao_id)
            ->setIdUnidade($receita->orcunidade_id)
            ->setEsferaOrcamentaria($receita->esferaorcamentaria);

        $existe = null;
        if (!is_null($codigo)) {
            $existe = $this->repositoryReceita
                ->scopeAno($this->exercicio)
                ->scopeReduzido($codigo)
                ->first();
            $this->repositoryReceita->resetScopes();
        }

        if (!is_null($existe)) {
            $this->repositoryReceita->update($novaReceita);
        } else {
            $this->repositoryReceita->save($novaReceita);
        }

        $this->cronogramaDesembolsoReceita($novaReceita, $receita);
    }

    private function getCodigoReceita(EstimativaReceita $estimativaReceita)
    {
        $receita = $this->repositoryReceita->scopeAno($this->exercicioAtual)
            ->scopeCaracteristicaPeculiar($estimativaReceita->concarpeculiar_id)
            ->scopeFonte($estimativaReceita->orcfontes_id)
            ->scopeInstituicao($estimativaReceita->instituicao_id)
            ->first();

        $this->repositoryReceita->resetScopes();

        if ($receita instanceof Receita) {
            return $receita->getReduzido();
        }

        return null;
    }

    /**
     * @param $valores
     * @return float|null
     */
    private function getValorExercicio($valores)
    {
        if ($valores->isEmpty()) {
            return null;
        }
        $valor = $valores->filter(function (Valor $valor) {
            return $valor->pl10_ano == $this->exercicio;
        })->shift();

        return $valor->pl10_valor;
    }

    protected function cronogramaDesembolsoDespesa(Dotacao $dotacao, DetalhamentoDespesa $despesa)
    {
        foreach ($despesa->cronogramaDesembolso as $cronograma) {
            if ($cronograma->exercicio != $dotacao->getAno()) {
                continue;
            }

            $acompanhamento = new AcompanhamentoDesembolsoDespesaService();
            $acompanhamento->setDotacao($dotacao);
            $acompanhamento->copiarCronogramaDetalhamento($cronograma);
        }
    }

    private function cronogramaDesembolsoReceita(Receita $receita, EstimativaReceita $estimativa)
    {
        foreach ($estimativa->cronogramaDesembolso as $cronograma) {
            if ($cronograma->exercicio != $receita->getAno()) {
                continue;
            }

            $acompanhamento = new AcompanhamentoDesembolsoReceitaService();
            $acompanhamento->setReceita($receita);
            $acompanhamento->copiarCronogramaDetalhamento($cronograma);
        }
    }
}
