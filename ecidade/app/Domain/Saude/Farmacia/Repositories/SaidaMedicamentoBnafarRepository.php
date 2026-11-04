<?php

namespace App\Domain\Saude\Farmacia\Repositories;

use App\Domain\Saude\Farmacia\Services\BnafarQueueService;

class SaidaMedicamentoBnafarRepository extends MedicamentoBnafarRepository
{
    public function getProcessamentos()
    {
        return BnafarQueueService::getNumProcess(2);
    }

    protected function getCamposAdicionais()
    {
        return [
            "'SAÍDA' as tipo",
            "'B' as tipo_produto",
            'm80_data as data_saida',
            "case when cgmdestino.z01_numcgm is null then 'unidade' else 'cgm' end as destino",
            'coalesce(cgmdestino.z01_numcgm, unidadeorigem.sd02_i_codigo) as id_estabelecimento',
            'trim(coalesce(cgmdestino.z01_cgccpf, unidadeorigem.sd02_cnpjcpf)) as cnpj_estabelecimento',
            'coalesce(cgmdestino.z01_nome, departorigem.descrdepto) as nome_estabelecimento',
            'trim(sd02_v_cnes) as cnes_estabelecimento',
            'fa01_i_codigo',
        ];
    }

    protected function scopeMovimentacao()
    {
        $this->scopes['movimentacao'] = 'm81_tipo = 2 AND far_retirada.fa04_i_codigo is null';
    }

    protected function newQueryMovimentacao()
    {
        return $this->newQueryEstoque()
            ->leftJoin('cgm as cgmdestino', 'cgmdestino.z01_numcgm', 'estoquemovimentacaobnafar.fa69_cgm')
            ->leftJoin(
                'unidades as unidadeorigem',
                'unidadeorigem.sd02_i_codigo',
                'estoquemovimentacaobnafar.fa69_unidade'
            )->leftJoin('db_depart as departorigem', 'departorigem.coddepto', 'unidadeorigem.sd02_i_codigo')
            ->leftJoin(
                'matestoqueinimeiari',
                'matestoqueinimeiari.m49_codmatestoqueinimei',
                'matestoqueinimei.m82_codigo'
            )->leftJoin('atendrequiitem', 'atendrequiitem.m43_codigo', 'matestoqueinimeiari.m49_codatendrequiitem')
            ->leftJoin('matrequiitem', 'matrequiitem.m41_codigo', 'atendrequiitem.m43_codmatrequiitem')
            ->leftJoin('matrequi', 'matrequi.m40_codigo', 'matrequiitem.m41_codmatrequi')
            ->leftJoin('far_retiradarequi', 'far_retiradarequi.fa07_i_matrequi', 'matrequi.m40_codigo')
            ->leftJoin('far_retirada', 'far_retirada.fa04_i_codigo', 'far_retiradarequi.fa07_i_retirada');
    }
}
