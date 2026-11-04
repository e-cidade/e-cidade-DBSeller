<?php

namespace App\Domain\Financeiro\Contabilidade\Repositories;

use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Financeiro\Contabilidade\Models\Lancamento;
use Illuminate\Support\Facades\DB;

use App\Domain\Financeiro\Contabilidade\Models\MovimentacaoAuditoria;
use App\Domain\RecursosHumanos\Pessoal\Repository\Helper\CompetenciaHelper;

/**
 * Class LancamentosContabeisRepository
 * @package App\Domain\Financeiro\Contabilidade\Repositories
 */
class LancamentosContabeisRepository extends BaseRepository
{
    protected $modelClass = Lancamento::class;

    /**
     * @var integer
     */
    private $mes;

    /**
     * @var integer
     */
    private $ano;

    /**
     * @var string
     */
    private $dataFinal;

    public function montaQueryMDE($params)
    {

        $this->mes = $params['mes'];
        $this->ano = $params['anousu'];
        $this->dataFinal = CompetenciaHelper::getFormatada($this->ano, $this->mes)->dataFinal;

        return DB::select($this->getIndiceEducacaoMde());
    }

    private function getIndiceEducacaoMde()
    {
        return "
            select 'total_das_receitas_com_impostos_e_transferencias' as titulo,
                extract(month from c70_data) as mes_competencia,
                sum(case when c53_tipo = 101 then round(c70_valor,2)*-1
                else round(c70_valor,2) end) as valor_ate_o_periodo
            from conlancam
                join conlancamrec on c74_codlan = c70_codlan
                join conlancamdoc on c71_codlan = c70_codlan
                join conhistdoc on c53_coddoc = c71_coddoc
                join orcreceita on o70_codrec = c74_codrec and o70_anousu = c74_anousu
                join orcfontes on o57_codfon = o70_codfon and o57_anousu = o70_anousu
                join orctiporec on o15_codigo = o70_codigo
            where o70_anousu = {$this->ano}
            and c53_tipo in (100,101)
            and c70_data <= '{$this->dataFinal}'
            and (o57_fonte like '4111%'
                or o57_fonte like '4171151%'
                or o57_fonte like '4171152%'
                or o57_fonte like '4172150%'
                or o57_fonte like '4172151%'
                or o57_fonte like '4172152%')
            group by 1,2

            union all

            select 'despesas_empenhadas_com_recursos_do_fundeb' as titulo,
                extract(month from c70_data) as mes_competencia,
                sum(case when c53_tipo = 11 then round(c70_valor,2)*-1
                else round(c70_valor,2) end) as valor_ate_o_periodo
            from conlancam
                join conlancamemp on c75_codlan = c70_codlan
                join conlancamdoc on c71_codlan = c70_codlan
                join conhistdoc on c53_coddoc = c71_coddoc
                join empempenho on e60_numemp = c75_numemp
                join orcdotacao on o58_coddot = e60_coddot and o58_anousu = e60_anousu
                join orcelemento on o56_codele = o58_codele and o56_anousu = o58_anousu
                join orctiporec on o15_codigo = o58_codigo
            where o58_anousu = {$this->ano}
            and c53_tipo in (10,11)
            and c70_data <= '{$this->dataFinal}'
            and substr(o15_recurso,2,3) in ('540','541','542','543')
            group by 1,2

            union all

            select 'despesas_liquidadas_com_recursos_do_fundeb' as titulo,
                extract(month from c70_data) as mes_competencia,
                sum(case when c53_tipo = 21 then round(c70_valor,2)*-1
                else round(c70_valor,2) end) as valor_ate_o_periodo
            from conlancam
                join conlancamemp on c75_codlan = c70_codlan
                join conlancamdoc on c71_codlan = c70_codlan
                join conhistdoc on c53_coddoc = c71_coddoc
                join empempenho on e60_numemp = c75_numemp
                join orcdotacao on o58_coddot = e60_coddot and o58_anousu = e60_anousu
                join orcelemento on o56_codele = o58_codele and o56_anousu = o58_anousu
                join orctiporec on o15_codigo = o58_codigo
            where o58_anousu = {$this->ano}
            and c53_tipo in (20,21)
            and c70_data <= '{$this->dataFinal}'
            and substr(o15_recurso,2,3) in ('540','541','542','543')
            group by 1,2

            union all

            select 'despesas_empenhadas_com_recursos_de_impostos' as titulo,
                extract(month from c70_data) as mes_competencia,
                sum(case when c53_tipo = 11 then round(c70_valor,2)*-1
                else round(c70_valor,2) end) as valor_ate_o_periodo
            from conlancam
                join conlancamemp on c75_codlan = c70_codlan
                join conlancamdoc on c71_codlan = c70_codlan
                join conhistdoc on c53_coddoc = c71_coddoc
                join empempenho on e60_numemp = c75_numemp
                join orcdotacao on o58_coddot = e60_coddot and o58_anousu = e60_anousu
                join orcelemento on o56_codele = o58_codele and o56_anousu = o58_anousu
                join orctiporec on o15_codigo = o58_codigo
            where o58_anousu = {$this->ano}
            and c53_tipo in (10,11)
            and c70_data <= '{$this->dataFinal}'
            and substr(o15_recurso,2,3) ='500'
            and o15_complemento = '1001'
            group by 1,2

            union all

            select 'despesas_liquidadas_com_recursos_de_impostos' as titulo,
                extract(month from c70_data) as mes_competencia,
                sum(case when c53_tipo = 21 then round(c70_valor,2)*-1
                else round(c70_valor,2) end) as valor_ate_o_periodo
            from conlancam
                join conlancamemp on c75_codlan = c70_codlan
                join conlancamdoc on c71_codlan = c70_codlan
                join conhistdoc on c53_coddoc = c71_coddoc
                join empempenho on e60_numemp = c75_numemp
                join orcdotacao on o58_coddot = e60_coddot and o58_anousu = e60_anousu
                join orcelemento on o56_codele = o58_codele and o56_anousu = o58_anousu
                join orctiporec on o15_codigo = o58_codigo
            where o58_anousu = {$this->ano}
            and c53_tipo in (20,21)
            and c70_data <= '{$this->dataFinal}'
            and substr(o15_recurso,2,3) = '500'
            and o15_complemento = '1001'
            group by 1,2

            union all

            select 'total_das_transferencias_recebidas_do_fundeb' as titulo,
                extract(month from c70_data) as mes_competencia,
                sum(case when c53_tipo = 101 then round(c70_valor,2)*-1
                else round(c70_valor,2) end) as valor_ate_o_periodo
            from conlancam
                join conlancamrec on c74_codlan = c70_codlan
                join conlancamdoc on c71_codlan = c70_codlan
                join conhistdoc on c53_coddoc = c71_coddoc
                join orcreceita on o70_codrec = c74_codrec and o70_anousu = c74_anousu
                join orcfontes on o57_codfon = o70_codfon and o57_anousu = o70_anousu
                join orctiporec on o15_codigo = o70_codigo
            where o70_anousu = {$this->ano}
            and c53_tipo in (100,101)
            and c70_data <= '{$this->dataFinal}'
            and o57_fonte like '41751%'
            group by 1,2

            union all

            select 'total_das_deducoes_do_fundeb' as titulo,
                extract(month from c70_data) as mes_competencia,
                sum(case when c53_tipo = 101 then round(c70_valor,2)*-1
                else round(c70_valor,2) end) as valor_ate_o_periodo
            from conlancam
                join conlancamrec on c74_codlan = c70_codlan
                join conlancamdoc on c71_codlan = c70_codlan
                join conhistdoc on c53_coddoc = c71_coddoc
                join orcreceita on o70_codrec = c74_codrec and o70_anousu = c74_anousu
                join orcfontes on o57_codfon = o70_codfon and o57_anousu = o70_anousu
                join orctiporec on o15_codigo = o70_codigo
            where o70_anousu = {$this->ano}
            and c53_tipo in (100,101)
            and c70_data <= '{$this->dataFinal}'
            and o57_fonte like '917%'
            group by 1,2

            union all

            select 'total_da_receita_de_complementacao_da_uniao' as titulo,
                extract(month from c70_data) as mes_competencia,
                sum(case when c53_tipo = 101 then round(c70_valor,2)*-1
                else round(c70_valor,2) end) as valor_ate_o_periodo
            from conlancam
                join conlancamrec on c74_codlan = c70_codlan
                join conlancamdoc on c71_codlan = c70_codlan
                join conhistdoc on c53_coddoc = c71_coddoc
                join orcreceita on o70_codrec = c74_codrec and o70_anousu = c74_anousu
                join orcfontes on o57_codfon = o70_codfon and o57_anousu = o70_anousu
                join orctiporec on o15_codigo = o70_codigo
            where o70_anousu = {$this->ano}
            and c53_tipo in (100,101)
            and c70_data <= '{$this->dataFinal}'
            and o57_fonte like '41715%'
            group by 1,2;
        ";
    }
}
