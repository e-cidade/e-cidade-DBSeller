<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios;

use Illuminate\Support\Facades\DB;

class BalanceteReceitaPADService extends BalanceteReceitaPlanoPadraoComplementoService
{
    protected $emissaoMGS = false;

    public function setModeloMgs($emissaoMGS)
    {
        $this->emissaoMGS = $emissaoMGS;
    }

    protected function getDados()
    {
        list($where, $dataInicio, $dataFinal) = $this->montaWhere();
        $where .= ' and uniao is false';

        $campoSubrecurso = $this->emissaoMGS ? 'fonte_recurso' : "'0000' as fonte_recurso";

        $sql = "
        with preparacao as (
            select
                   case when classe = 4 then conta else substr(conta, 2)::varchar end resto,
                   conta as natureza,
                   nome as descricao,
                   0 as orgao,
                   0 as unidade,
                   substr(siconfi, 2)::varchar as siconfi,
                   $campoSubrecurso,
                   case when o200_tribunal is true then complemento_lancamento
                       else 0
                   end as complemento_lancamento,
                   1 as ordem,
                   bl.cp,
                   bl.instituicao,
                   bl.ano,
                   00 as esfera,
                   bl.recurso_lancamento,
                   bl.reduzido,
                   bl.fonte,
                   bl.valor_inicial,
                   bl.previsao_adicional_acumulado,
                   bl.previsao_atualizada,
                   bl.arrecadado_anterior,
                   bl.arrecadado_periodo,
                   bl.valor_a_arrecadar,
                   bl.arrecadado_acumulado,
                   bl.previsao_adicional
              from orcreceita
              join balancete_receita_complemento(
                      o70_anousu, o70_codfon, o70_concarpeculiar, '{$dataInicio}', '{$dataFinal}'
                   ) as bl on o70_codfon = fonte and o70_anousu = ano
              join conplanoorcamento on (c60_codcon, c60_anousu) = (o70_codfon, o70_anousu)
              join planoreceitaconplanoorcamento on conplanoorcamento_codigo = c60_codigo
              join planoreceita on planoreceita.id = planoreceita_id
              join complementofonterecurso on o200_sequencial = complemento_lancamento
              where {$where}
       ), agrupar as (
        select natureza,
               descricao,
               orgao,
               unidade,
               cp,
               instituicao,
               siconfi,
               siconfi as gestao,
               fonte_recurso as subrecurso,
               complemento_lancamento as complemento,
               resto,
               ordem,
               esfera,
               ano,
               array_to_string(array_agg(recurso_lancamento), ',') as recurso_lancamento,
               array_to_string(array_agg(reduzido), ',') as reduzido,
               array_to_string(array_agg(fonte), ',') as fonte,
               sum(valor_inicial) as valor_inicial,
               sum(previsao_adicional_acumulado) as previsao_adicional_acumulado,
               sum(previsao_atualizada) as previsao_atualizada,
               sum(arrecadado_anterior) as arrecadado_anterior,
               sum(arrecadado_periodo) as arrecadado_periodo,
               sum(valor_a_arrecadar) as valor_a_arrecadar,
               sum(arrecadado_acumulado) as arrecadado_acumulado,
               sum(previsao_adicional) as previsao_adicional
           from preparacao
           group by 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14
       ) select * from agrupar order by natureza, siconfi, complemento
        ";

        return DB::select($sql);
    }

    /**
     * @param $receita
     * @return string
     */
    public function montaHashArvore($receita)
    {
        return sprintf(
            '%s#%s#%s#%s#%s#%s#%s#%s',
            $receita->natureza,
            $receita->cp,
            $receita->orgao,
            $receita->unidade,
            $receita->instituicao,
            $receita->subrecurso,
            $receita->siconfi,
            $receita->complemento
        );
    }
}
