<?php

namespace App\Domain\Saude\Farmacia\Repositories;

use App\Domain\Saude\Farmacia\Services\BnafarQueueService;

class EntradaMedicamentoBnafarRepository extends MedicamentoBnafarRepository
{
    public function getProcessamentos()
    {
        return BnafarQueueService::getNumProcess(1);
    }

    protected function getCamposAdicionais()
    {
        return [
            "'ENTRADA' as tipo",
            "'B' as tipo_produto",
            'matestoqueini.m80_data as data_entrada',
            'trim(coalesce(e69_numero, m79_notafiscal)) as numero_documento',
            'coalesce(e69_dtnota, m79_data) as data_documento',
            "CASE WHEN fa69_cgm is null THEN 'unidade' ELSE 'cgm' END as origem",
            'coalesce(cgmorigem.z01_numcgm, unidadeorigem.sd02_i_codigo) as id_estabelecimento',
            'trim(coalesce(cgmorigem.z01_cgccpf, unidadeorigem.sd02_cnpjcpf)) as cnpj_estabelecimento',
            'coalesce(cgmorigem.z01_nome, departorigem.descrdepto) as nome_estabelecimento',
            'trim(unidadeorigem.sd02_v_cnes) as cnes_estabelecimento',
            'transf.m80_codigo as codigo_transferencia',
            'transf.m80_data as data_transferencia'
        ];
    }

    protected function scopeMovimentacao()
    {
        $this->scopes['movimentacao'] = 'm81_tipo = 1';
    }

    protected function newQueryMovimentacao()
    {
        return $this->newQueryEstoque()
            ->leftJoin(
                'protocolo.cgm as cgmorigem',
                'cgmorigem.z01_numcgm',
                'estoquemovimentacaobnafar.fa69_cgm'
            )->leftJoin(
                'ambulatorial.unidades as unidadeorigem',
                'unidadeorigem.sd02_i_codigo',
                'estoquemovimentacaobnafar.fa69_unidade'
            )->leftJoin(
                'configuracoes.db_depart as departorigem',
                'departorigem.coddepto',
                'unidadeorigem.sd02_i_codigo'
            )->leftJoin(
                'material.matestoqueitemnotafiscalmanual',
                'matestoqueitemnotafiscalmanual.m79_matestoqueitem',
                'matestoqueitem.m71_codlanc'
            )->leftJoin(
                'material.matestoqueitemnota',
                'matestoqueitemnota.m74_codmatestoqueitem',
                'matestoqueitem.m71_codlanc'
            )->leftJoin('empenho.empnota', 'empnota.e69_codnota', 'matestoqueitemnota.m74_codempnota')
            // Joins para descobrir a movimentação de transferência, caso a mesma possua
            ->leftJoin(
                'material.matestoqueinill as ligaentradasaida',
                'ligaentradasaida.m87_matestoqueini',
                'matestoqueini.m80_codigo'
            )->leftJoin(
                'material.matestoqueinil as ligasaida',
                'ligasaida.m86_codigo',
                'ligaentradasaida.m87_matestoqueinil'
            )->leftJoin(
                'material.matestoqueinill as ligatransfsaida',
                'ligatransfsaida.m87_matestoqueini',
                'ligasaida.m86_matestoqueini'
            )->leftJoin(
                'material.matestoqueinil as ligatransf',
                'ligatransf.m86_codigo',
                'ligatransfsaida.m87_matestoqueinil'
            )->leftJoin(
                'material.matestoqueini as transf',
                'transf.m80_codigo',
                'ligatransf.m86_matestoqueini'
            );
    }
}
