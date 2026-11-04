<?php

namespace App\Domain\Financeiro\Planejamento\Services\Relatorios;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Financeiro\Contabilidade\Services\BalanceteDespesaService;
use App\Domain\Financeiro\Planejamento\Models\Comissao;
use App\Domain\Financeiro\Planejamento\Models\Planejamento;
use App\Domain\Financeiro\Planejamento\Relatorios\AcompanhamentoLdoPdf;
use App\Domain\Financeiro\Planejamento\Relatorios\AcompanhamentoPpaPdf;
use Carbon\Carbon;
use Dompdf\Exception;
use Illuminate\Support\Facades\DB;
use stdClass;

abstract class AcompanhamentoService
{
    /**
     * @var Planejamento
     */
    protected $planejamento;

    /**
     * @var array
     */
    protected $execiciosPlanejamento;

    /**
     * @var array
     */
    protected $dados = [];
    /**
     * @var array
     */
    protected $filtros = [];

    protected $apresentarExecucao = false;

    protected $execucaoDespesa = [];

    abstract protected function processarDados();

    abstract protected function organizaDados();

    public function setFiltros(array $filtros)
    {
        $this->filtros = $filtros;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function emitir()
    {
        $this->processar();
        /** fazer if tipo plano e instanciar pdf certo */
        $pdf = $this->getPdf();
        $pdf->setFiltros($this->filtros);
        $pdf->setDados($this->dados);

        $titulo = $this->planejamento->pl2_tipo === 'PPA' ? 'Acompanhamento PPA' : 'Acompanhamento LDO';
        return $pdf->emitir($titulo);
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function processar()
    {
        $this->processarFiltros();
        $this->processarDados();
        $this->organizaDadosImpressao();
    }

    protected function processarFiltros()
    {
        if (!isset($this->filtros['planejamento_id'])) {
            throw new Exception('Não foi informado o planejamento.');
        }

        $this->planejamento = Planejamento::find($this->filtros['planejamento_id']);
        if (isset($this->filtros['apresentarExecucao']) && $this->filtros['apresentarExecucao'] == 1) {
            $this->apresentarExecucao = true;
            $this->executaBalanceteDespesa();
        }

        $this->filtros['isPPA'] = $this->planejamento->pl2_tipo === 'PPA';
    }


    protected function organizaDadosImpressao()
    {
        $this->organizaPlanejamento();
        $this->organizaDados();
        $this->organizaComissao();
    }

    private function organizaPlanejamento()
    {
        $this->dados['planejamento'] = $this->planejamento->toArray();
        $this->dados['planejamento']['exercicios'] = $this->planejamento->execiciosPlanejamento();
        $this->dados['planejamento']['missao'] = $this->planejamento->pl2_missao;
        $this->dados['planejamento']['visao'] = $this->planejamento->pl2_visao;
        $this->dados['planejamento']['valores'] = $this->planejamento->pl2_valores;
    }

    private function organizaComissao()
    {
        $cgms = $this->planejamento->comissoes->map(function (Comissao $comissao) {
            return $comissao->cgm->z01_nome;
        })->toArray();

        $this->dados['planejamento']['comissao'] = $cgms;
    }

    public function executaBalanceteDespesa()
    {
        $service = new BalanceteDespesaService();
        $instituicoes = DBConfig::select('codigo')->get()->map(function (DBConfig $instituicao) {
            return $instituicao->codigo;
        });

        foreach ($this->planejamento->execiciosPlanejamento() as $exercicio) {
            $dataInicio = "$exercicio-01-01";
            $dataFim = "$exercicio-12-31";

            $sql = $service->setAno($exercicio)
                ->setFiltrarInstituicoes($instituicoes)
                ->setFiltroDataInicio(Carbon::createFromFormat('Y-m-d', $dataInicio))
                ->setFiltroDataFinal(Carbon::createFromFormat('Y-m-d', $dataFim))
                ->setFiltro($this->montaFiltroDespesa())
                ->sqlPrincipal();

            $this->execucaoDespesa[$exercicio] = DB::select($sql);
        }
    }

    private function montaFiltroDespesa()
    {
        if (empty($this->filtros['orcprograma_id']) && empty($this->filtros['orcorgao_id'])) {
            return null;
        }
        $filtro = new stdClass();
        if (!empty($this->filtros['orcorgao_id'])) {
            $filtro->orgao = new stdClass();
            $filtro->orgao->aOrgaos = [$this->filtros['orcorgao_id']];
            $filtro->orgao->operador = 'in';
        }

        if (!empty($this->filtros['orcprograma_id'])) {
            $filtro->programa = new stdClass();
            $filtro->programa->aProgramas = [$this->filtros['orcprograma_id']];
            $filtro->programa->operador = 'in';
        }

        return $filtro;
    }

    private function getPdf()
    {
        if ($this->planejamento->pl2_tipo === 'PPA') {
            return new AcompanhamentoPpaPdf();
        }

        return new AcompanhamentoLdoPdf();
    }
}
