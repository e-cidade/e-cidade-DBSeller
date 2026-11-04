<?php

namespace App\Domain\Saude\Farmacia\Repositories;

use App\Domain\Saude\Farmacia\Services\BnafarQueueService;
use Illuminate\Database\Query\Builder;

class DispensacaoMedicamentoBnafarRepository extends MedicamentoBnafarRepository
{
    public function getProcessamentos()
    {
        return BnafarQueueService::getNumProcess(3);
    }

    protected function getCamposAdicionais()
    {
        return [
            'fa06_i_codigo',
            "'DISPENSAÇÃO' as tipo",
            'm70_coddepto',
            "'B' as tipo_produto",
            'm80_data as data_dispensacao',
            'z01_i_cgsund as id_paciente',
            'z01_v_nome as nome_paciente',
            "(select trim(s115_c_cartaosus)
               from cgs_cartaosus where s115_i_cgs = fa04_i_cgsund
               order by (s115_c_tipo is null or nullif('', s115_c_tipo) is null), s115_c_tipo limit 1) as cns_paciente",
            'trim(cgs_und.z01_v_cgccpf) as cpf_paciente',
            'fa01_i_codigo',
        ];
    }

    protected function scopeMovimentacao()
    {
        $this->scopes['movimentacao'] = 'm81_tipo = 2 AND m81_codtipo = 17 AND far_retirada.fa04_i_codigo is not null';
    }

    protected function newQueryMovimentacao()
    {
        return $this->newQueryEstoque()
            ->leftJoin(
                'matestoqueinimeiari',
                'matestoqueinimeiari.m49_codmatestoqueinimei',
                'matestoqueinimei.m82_codigo'
            )->leftJoin('atendrequiitem', 'atendrequiitem.m43_codigo', 'matestoqueinimeiari.m49_codatendrequiitem')
            ->leftJoin('matrequiitem', 'matrequiitem.m41_codigo', 'atendrequiitem.m43_codmatrequiitem')
            ->leftJoin('matrequi', 'matrequi.m40_codigo', 'matrequiitem.m41_codmatrequi')
            ->leftJoin('far_retiradarequi', 'far_retiradarequi.fa07_i_matrequi', 'matrequi.m40_codigo')
            ->leftJoin('far_retirada', 'far_retirada.fa04_i_codigo', 'far_retiradarequi.fa07_i_retirada')
            ->leftJoin('far_retiradaitens', function (Builder $query) {
                $query->on('far_retiradaitens.fa06_i_retirada', 'far_retirada.fa04_i_codigo');
                $query->on('far_retiradaitens.fa06_i_matersaude', 'far_matersaude.fa01_i_codigo');
            })->leftJoin('ambulatorial.cgs_und', 'cgs_und.z01_i_cgsund', 'fa04_i_cgsund');
    }
}
