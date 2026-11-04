<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2020\BalanceteRubricaAnteriorBuilder2020;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2022\BalanceteRubricaAnteriorBuilder2022;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2023\BalanceteRubricaAnteriorBuilder2023;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Layouts\LayoutPad;
use Exception;
use Instituicao;

class BalanceteRubricaAnteriorService extends PadService
{
    protected $fileName = 'BRUB_ANT.TXT';

    /**
     * @var int
     */
    protected $ano;

    /**
     * BalanceteReceitaAnteriorService constructor.
     * @param Instituicao[] $instituicoes
     * @param integer $ano para calculo
     */
    public function __construct(array $instituicoes, $ano)
    {
        $this->instituicoes = $instituicoes;
        $this->ano = $ano;
    }

    /**
     * @return string
     */
    protected function getSql()
    {
        $ano = $this->ano;

        $primeiroBi = "c70_data >= '{$ano}-01-01' and c70_data < '{$ano}-03-01'";
        $segundoBi = "c70_data >= '{$ano}-03-01' and c70_data < '{$ano}-05-01'";
        $terceiroBi = "c70_data >= '{$ano}-05-01' and c70_data < '{$ano}-07-01'";
        $quartoBi = "c70_data >= '{$ano}-07-01' and c70_data < '{$ano}-09-01'";
        $quintoBi = "c70_data >= '{$ano}-09-01' and c70_data < '{$ano}-11-01'";
        $sextoBi = "c70_data >= '{$ano}-11-01' and c70_data <= '{$ano}-12-31'";

        $where = "o58_instit in ({$this->getListaInstituicoes()}) and o58_anousu = {$ano}";
        $sql = "
          select o58_orgao,
                 o58_unidade,
                 o58_funcao,
                 o58_subfuncao,
                 o58_programa,
                 o58_projativ,
                 o58_codele,
                 o56_elemento,
                 o15_recurso,
                 complemento,
                 codigo_siconfi,
                 round( sum((bi1_empenhado - bi1_extorno_empenho)   ) , 2) as bi1_empenhado,
                 round( sum((bi1_liquidado - bi1_extorno_liquidacao)) , 2) as bi1_liquidado,
                 round( sum((bi1_pagamento - bi1_extorno_pagamento) ) , 2) as bi1_pagamento,
                 round( sum((bi2_empenhado - bi2_extorno_empenho)   ) , 2) as bi2_empenhado,
                 round( sum((bi2_liquidado - bi2_extorno_liquidacao)) , 2) as bi2_liquidado,
                 round( sum((bi2_pagamento - bi2_extorno_pagamento) ) , 2) as bi2_pagamento,
                 round( sum((bi3_empenhado - bi3_extorno_empenho)   ) , 2) as bi3_empenhado,
                 round( sum((bi3_liquidado - bi3_extorno_liquidacao)) , 2) as bi3_liquidado,
                 round( sum((bi3_pagamento - bi3_extorno_pagamento) ) , 2) as bi3_pagamento,
                 round( sum((bi4_empenhado - bi4_extorno_empenho)   ) , 2) as bi4_empenhado,
                 round( sum((bi4_liquidado - bi4_extorno_liquidacao)) , 2) as bi4_liquidado,
                 round( sum((bi4_pagamento - bi4_extorno_pagamento) ) , 2) as bi4_pagamento,
                 round( sum((bi5_empenhado - bi5_extorno_empenho)   ) , 2) as bi5_empenhado,
                 round( sum((bi5_liquidado - bi5_extorno_liquidacao)) , 2) as bi5_liquidado,
                 round( sum((bi5_pagamento - bi5_extorno_pagamento) ) , 2) as bi5_pagamento,
                 round( sum((bi6_empenhado - bi6_extorno_empenho)   ) , 2) as bi6_empenhado,
                 round( sum((bi6_liquidado - bi6_extorno_liquidacao)) , 2) as bi6_liquidado,
                 round( sum((bi6_pagamento - bi6_extorno_pagamento) ) , 2) as bi6_pagamento
          from (
          select o58_orgao,
	     	     o58_unidade,
	     		 o58_funcao,
	     		 o58_subfuncao,
	     		 o58_programa,
	     	     o58_projativ,
	     		 o58_codele,
	     		 o56_elemento,
	     		 codigo_siconfi,
	     		 o15_recurso,
                 case
                    when o200_sequencial is not null
                         and o200_sequencial in (3110, 3120, 3140, 3150, 3160, 1111, 1121, 2111, 2121, 0)
                        then o200_sequencial
                    else 0
                 end as complemento,

	     	     coalesce(sum(case when {$primeiroBi} and c53_tipo = 10 then c70_valor end), 0) as bi1_empenhado,
	     	     coalesce(sum(case when {$primeiroBi} and c53_tipo = 11 then c70_valor end), 0) as bi1_extorno_empenho,
	     	     coalesce(sum(case when {$primeiroBi} and c53_tipo = 20 then c70_valor end), 0) as bi1_liquidado,
	     	     coalesce(sum(case when {$primeiroBi} and c53_tipo = 21 then c70_valor end), 0)
	     	         as bi1_extorno_liquidacao,
	     	     coalesce(sum(case when {$primeiroBi} and c53_tipo = 30 then c70_valor end), 0) as bi1_pagamento,
	     	     coalesce(sum(case when {$primeiroBi} and c53_tipo = 31 then c70_valor end), 0)
	     	         as bi1_extorno_pagamento,

                 coalesce(sum(case when {$segundoBi} and c53_tipo = 10 then c70_valor end), 0) as bi2_empenhado,
                 coalesce(sum(case when {$segundoBi} and c53_tipo = 11 then c70_valor end), 0) as bi2_extorno_empenho,
                 coalesce(sum(case when {$segundoBi} and c53_tipo = 20 then c70_valor end), 0) as bi2_liquidado,
                 coalesce(sum(case when {$segundoBi} and c53_tipo = 21 then c70_valor end), 0)
                     as bi2_extorno_liquidacao,
                 coalesce(sum(case when {$segundoBi} and c53_tipo = 30 then c70_valor end), 0) as bi2_pagamento,
                 coalesce(sum(case when {$segundoBi} and c53_tipo = 31 then c70_valor end), 0)
                     as bi2_extorno_pagamento,

                 coalesce(sum(case when {$terceiroBi} and c53_tipo = 10 then c70_valor end), 0) as bi3_empenhado,
                 coalesce(sum(case when {$terceiroBi} and c53_tipo = 11 then c70_valor end), 0) as bi3_extorno_empenho,
                 coalesce(sum(case when {$terceiroBi} and c53_tipo = 20 then c70_valor end), 0) as bi3_liquidado,
                 coalesce(sum(case when {$terceiroBi} and c53_tipo = 21 then c70_valor end), 0)
                     as bi3_extorno_liquidacao,
                 coalesce(sum(case when {$terceiroBi} and c53_tipo = 30 then c70_valor end), 0) as bi3_pagamento,
                 coalesce(sum(case when {$terceiroBi} and c53_tipo = 31 then c70_valor end), 0)
                     as bi3_extorno_pagamento,

                 coalesce(sum(case when {$quartoBi} and c53_tipo = 10 then c70_valor end), 0) as bi4_empenhado,
                 coalesce(sum(case when {$quartoBi} and c53_tipo = 11 then c70_valor end), 0) as bi4_extorno_empenho,
                 coalesce(sum(case when {$quartoBi} and c53_tipo = 20 then c70_valor end), 0) as bi4_liquidado,
                 coalesce(sum(case when {$quartoBi} and c53_tipo = 21 then c70_valor end), 0) as bi4_extorno_liquidacao,
                 coalesce(sum(case when {$quartoBi} and c53_tipo = 30 then c70_valor end), 0) as bi4_pagamento,
                 coalesce(sum(case when {$quartoBi} and c53_tipo = 31 then c70_valor end), 0) as bi4_extorno_pagamento,

                 coalesce(sum(case when {$quintoBi} and c53_tipo = 10 then c70_valor end), 0) as bi5_empenhado,
                 coalesce(sum(case when {$quintoBi} and c53_tipo = 11 then c70_valor end), 0) as bi5_extorno_empenho,
                 coalesce(sum(case when {$quintoBi} and c53_tipo = 20 then c70_valor end), 0) as bi5_liquidado,
                 coalesce(sum(case when {$quintoBi} and c53_tipo = 21 then c70_valor end), 0) as bi5_extorno_liquidacao,
                 coalesce(sum(case when {$quintoBi} and c53_tipo = 30 then c70_valor end), 0) as bi5_pagamento,
                 coalesce(sum(case when {$quintoBi} and c53_tipo = 31 then c70_valor end), 0) as bi5_extorno_pagamento,

                 coalesce(sum(case when {$sextoBi} and c53_tipo = 10 then c70_valor end), 0) as bi6_empenhado,
                 coalesce(sum(case when {$sextoBi} and c53_tipo = 11 then c70_valor end), 0) as bi6_extorno_empenho,
                 coalesce(sum(case when {$sextoBi} and c53_tipo = 20 then c70_valor end), 0) as bi6_liquidado,
                 coalesce(sum(case when {$sextoBi} and c53_tipo = 21 then c70_valor end), 0) as bi6_extorno_liquidacao,
                 coalesce(sum(case when {$sextoBi} and c53_tipo = 30 then c70_valor end), 0) as bi6_pagamento,
                 coalesce(sum(case when {$sextoBi} and c53_tipo = 31 then c70_valor end), 0) as bi6_extorno_pagamento
            from orcdotacao
           inner join conlancamdot on o58_anousu = c73_anousu
                  and o58_coddot = c73_coddot
           inner join conlancamdoc on c73_codlan = c71_codlan
           inner join conhistdoc on c53_coddoc = c71_coddoc
           inner join conlancam on c70_codlan = c73_codlan
           inner join conlancamele on c67_codlan = c73_codlan
           inner join conlancamcomplementorecurso on o201_codlan = c70_codlan
           left  join complementofonterecurso on o200_sequencial = o201_complemento
           inner join orcelemento on o58_anousu = o56_anousu
                  and o56_codele = c67_codele
           inner join conlancamemp on c75_codlan = c70_codlan
           inner join orctiporec on o58_codigo = o15_codigo
           inner join fonterecurso on fonterecurso.orctiporec_id = o15_codigo
                  and fonterecurso.exercicio = o58_anousu
           where {$where}
	      group by o58_orgao,
                   o58_unidade,
                   o58_funcao,
                   o58_subfuncao,
                   o58_programa,
                   o58_projativ,
                   o58_codele,
                   o56_elemento,
                   o200_sequencial,
                   o15_recurso,
                   codigo_siconfi
          ) as x
          group by o58_orgao ,
         o58_unidade,
         o58_funcao,
         o58_subfuncao,
         o58_programa,
         o58_projativ,
         o58_codele,
         o56_elemento,
         codigo_siconfi,
         o15_recurso,
         complemento
          order by o56_elemento
        ";
        return $sql;
    }

    /**
     * @return LayoutPad[]
     * @throws Exception
     */
    protected function getDados()
    {
        $sql = $this->getSql();

        //echo "$sql"; die();
        $rs = db_query($sql);
        if (!$rs || pg_num_rows($rs) === 0) {
            throw new Exception("Erro ao buscar receitas.");
        }

        while ($state = pg_fetch_array($rs)) {
            $builder = $this->getBuilder();
            $state['exercicio'] = $this->ano;
            yield $builder->addDados($state)->build();
        }
    }

    /**
     * @return BalanceteRubricaAnteriorBuilder2020|BalanceteRubricaAnteriorBuilder2022
     * @throws Exception
     */
    protected function getBuilder()
    {
        $ano = $this->ano + 1;
        switch ($ano) {
            case 2020:
            case 2021:
                return new BalanceteRubricaAnteriorBuilder2020();
            case 2022:
                return new BalanceteRubricaAnteriorBuilder2022();
            case 2023:
                return new BalanceteRubricaAnteriorBuilder2023();
            default:
                throw new Exception("Layout {$this->fileName} não foi implementado para o ano {$ano}.");
        }
    }
}
