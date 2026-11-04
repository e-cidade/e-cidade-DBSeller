<?php

namespace App\Domain\Saude\Farmacia\Repositories;

use App\Domain\Saude\Farmacia\Contracts\MedicamentoBnafarRepository as IMedicamentoBnafarRepository;
use App\Domain\Saude\Farmacia\Models\BnafarErro;

abstract class MedicamentoBnafarRepository extends MedicamentoRepository implements IMedicamentoBnafarRepository
{
    protected $scopes;

    /**
     * @return array
     */
    abstract protected function getCamposAdicionais();

    /**
     * @return void
     */
    abstract protected function scopeMovimentacao();

    final public function get()
    {
        $this->scopeMovimentacao();
        $campos = implode(', ', $this->getCampos());
        $where = implode(' AND ', $this->scopes);

        $query = $this->newQueryMovimentacao()->selectRaw($campos);
        if ($where) {
            $query->whereRaw($where);
        }

        $query->orderBy('matestoqueini.m80_data');

        return $query->get();
    }

    final public function getInconsistencias()
    {
        $this->scopeSomenteInconsistencias()->scopeMovimentacao();
        $movimentacoes = $this->get();

        foreach ($movimentacoes as $movimentacao) {
            $erros = [];
            BnafarErro::where(function ($query) use ($movimentacao) {
                $query->where('fa73_matestoqueini', $movimentacao->codigo_origem);
                $query->where('fa73_matestoqueitem', $movimentacao->codigo_origem_item);
            })->orWhere('fa73_matestoqueini', $movimentacao->codigo_origem)
                ->get()
                ->each(function (BnafarErro $erro) use (&$erros) {
                    if ($erro->fa73_campo !== null) {
                        $erros[$erro->fa73_campo] = $erro->fa73_descricao;
                        return;
                    }
                    $erros[] = $erro->fa73_descricao;
                });
            $movimentacao->erros = $erros;
            $movimentacao->erro_bnafar = true;
        }

        return $movimentacoes;
    }

    public function scopeUnidade($idUnidade)
    {
        $this->scopes['unidade'] = "matestoque.m70_coddepto = {$idUnidade}";

        return $this;
    }

    public function scopeSomentePendentes()
    {
        $this->scopes['pendentes'] = "not exists (
            select 1 from bnafarenvios
            left join bnafarinconsistencias on bnafarinconsistencias.fa71_bnafarenvio = bnafarenvios.fa70_id
            left join bnafarconferencias on bnafarconferencias.fa72_bnafarinconsistencia = bnafarinconsistencias.fa71_id
            where bnafarenvios.fa70_matestoqueini = m82_matestoqueini
                AND (
                    (bnafarenvios.fa70_codigobnafar is not null OR bnafarenvios.fa70_protocolo is not null)
                    OR (bnafarinconsistencias.fa71_id is not null AND bnafarconferencias.fa72_id is null)
                )
        )";

        return $this;
    }

    public function scopeSomenteInconsistencias()
    {
        $this->scopes['pendentes'] = "exists (
            select 1 from bnafarenvios
            left join bnafarinconsistencias on bnafarinconsistencias.fa71_bnafarenvio = bnafarenvios.fa70_id
            left join bnafarconferencias on bnafarconferencias.fa72_bnafarinconsistencia = bnafarinconsistencias.fa71_id
            where bnafarenvios.fa70_matestoqueini = m82_matestoqueini
                AND bnafarinconsistencias.fa71_id is not null
                AND bnafarconferencias.fa72_id is null
        )";

        return $this;
    }

    public function scopeEstoqueMovimentacao($idEstoqueMovimentacao)
    {
        $this->scopes['estoquemovimentacao'] = "fa69_matestoqueini = {$idEstoqueMovimentacao}";

        return $this;
    }

    public function scopePeriodo(array $periodo)
    {
        list($periodoInicio, $periodoFim) = [$periodo[0]->format('Y-m-d'), $periodo[1]->format('Y-m-d')];
        $this->scopes['competencia'] = "matestoqueini.m80_data between '{$periodoInicio}' and '{$periodoFim}'";

        return $this;
    }

    private function getCampos()
    {
        $camposDefault = [
            'm82_codigo',
            'fa58_codigo as id_produto',
            'fa58_catmat as numero_produto',
            'fa58_descricao as descricao_produto',
            'm82_quant as quantidade',
            'm77_dtvalidade as data_validade',
            'm77_lote as lote',
            'round(m89_precomedio, 4) as valor_unitario',
            'm82_matestoqueini as codigo_origem',
            'm82_matestoqueitem as codigo_origem_item',
            'estoquemovimentacaobnafar.fa69_tipomovimentacao as movimentacao',
            'trim(cgmfabricante.z01_cgccpf) as cnpj_fabricante',
            'matfabricante.m76_sequencial as id_fabricante',
            'matfabricante.m76_nome as nome_fabricante',
            'm81_descr',
            'fa01_i_codmater',
            'm60_descr'
        ];

        return array_merge($camposDefault, $this->getCamposAdicionais());
    }

    protected function newQueryMovimentacao()
    {
        return $this->newQueryEstoque();
    }

    final protected function newQueryEstoque()
    {
        return $this->newQuery()
            ->join('farmacia.medicamentos', 'medicamentos.fa58_codigo', 'far_matersaude.fa01_medicamentos')
            ->join('material.matmater', 'matmater.m60_codmater', 'far_matersaude.fa01_i_codmater')
            ->join('material.matestoque', 'matestoque.m70_codmatmater', 'matmater.m60_codmater')
            ->join('material.matestoqueitem', 'matestoqueitem.m71_codmatestoque', 'matestoque.m70_codigo')
            ->join('material.matestoqueinimei', 'matestoqueinimei.m82_matestoqueitem', 'matestoqueitem.m71_codlanc')
            ->join('material.matestoqueini', 'matestoqueini.m80_codigo', 'matestoqueinimei.m82_matestoqueini')
            ->join('material.matestoquetipo', 'matestoquetipo.m81_codtipo', 'matestoqueini.m80_codtipo')
            ->leftJoin(
                'farmacia.estoquemovimentacaobnafar',
                'estoquemovimentacaobnafar.fa69_matestoqueini',
                'matestoqueini.m80_codigo'
            )->leftJoin('matestoqueitemfabric', 'matestoqueitemfabric.m78_matestoqueitem', 'matestoqueitem.m71_codlanc')
            ->leftJoin('matfabricante', 'matfabricante.m76_sequencial', 'matestoqueitemfabric.m78_matfabricante')
            ->leftJoin('cgm as cgmfabricante', 'cgmfabricante.z01_numcgm', 'matfabricante.m76_numcgm')
            ->leftJoin('matestoqueitemlote', 'matestoqueitemlote.m77_matestoqueitem', 'matestoqueitem.m71_codlanc')
            ->leftJoin('db_depart', 'db_depart.coddepto', 'matestoqueini.m80_coddepto')
            ->leftJoin('matestoqueinimeipm', 'matestoqueinimeipm.m89_matestoqueinimei', 'matestoqueinimei.m82_codigo');
    }
}
