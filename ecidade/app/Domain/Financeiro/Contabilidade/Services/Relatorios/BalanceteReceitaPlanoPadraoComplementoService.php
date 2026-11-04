<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios;

use Illuminate\Support\Facades\DB;

class BalanceteReceitaPlanoPadraoComplementoService extends BalanceteReceitaEmentarioPadraoService
{
    /**
     * Essa função só retorna o label para aplicar no header do relatório
     * @return string
     */
    protected function getHeaderTitulo()
    {
        return 'BALANCETE DA RECEITA POR COMPLEMENTO';
    }

    protected function montaWhereFiltraRecurso()
    {
        $recursos = $this->filtrarRecursos->implode(',');
        return "recurso_lancamento in ($recursos)";
    }

    /**
     * Retorna os dados agrupados pelo plano de contas.
     * @return mixed
     */
    protected function getDados()
    {
        list($where, $dataInicio, $dataFinal) = $this->montaWhere();
        $where = str_replace('natureza', 'conta', $where);

        $where .= " and " . ($this->ementario === 'uniao' ? 'uniao is true' : 'uniao is false');

        $sql = "
        select conta as natureza,
               classe,
               nome as descricao,
               cp,
               instituicao,
               orgao,
               unidade,
               esfera,
               gestao,
               siconfi,
               complemento_lancamento as complemento,
               1 as ordem,
               ano,
               case when classe = 4 then conta else substr(conta, 2)::varchar end resto,
               reduzido,
               recurso_lancamento,
               fonte_recurso as subrecurso,
               fonte,
               valor_inicial,
               previsao_adicional_acumulado,
               previsao_atualizada,
               arrecadado_anterior,
               arrecadado_periodo,
               valor_a_arrecadar,
               arrecadado_acumulado,
               previsao_adicional
          from orcreceita
          join balancete_receita_complemento(
                  o70_anousu, o70_codfon, o70_concarpeculiar, '{$dataInicio}', '{$dataFinal}'
               ) as bl on o70_codfon = fonte and o70_anousu = ano
          join conplanoorcamento on (c60_codcon, c60_anousu) = (o70_codfon, o70_anousu)
          join planoreceitaconplanoorcamento on conplanoorcamento_codigo = c60_codigo
          join planoreceita on planoreceita.id = planoreceita_id and planoreceita.exercicio = o70_anousu
          where {$where}
          order by natureza, resto, siconfi, complemento_lancamento
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
            '%s#%s#%s',
            $receita->natureza,
            $receita->reduzido,
            $receita->recurso_lancamento
        );
    }
}
