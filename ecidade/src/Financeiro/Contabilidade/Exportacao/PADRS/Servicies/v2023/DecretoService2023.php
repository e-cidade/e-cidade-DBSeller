<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2023;

use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2023\DecretoBuilder2023;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2022\DecretoService2022;

class DecretoService2023 extends DecretoService2022
{
    protected function suplementacoesRemanejamentoRecursos()
    {
        $sql = "
        with suplementacao_1015 as (
          select count(*), o46_codsup
            from orcsuplem
            join orcsuplemval on orcsuplemval.o47_codsup = orcsuplem.o46_codsup
            join orcsuplemtipo on orcsuplemtipo.o48_tiposup = orcsuplem.o46_tiposup
            join orcsuplemlan ON orcsuplemlan.o49_codsup= o46_codsup
           where o46_tiposup = 1015
            and o49_data BETWEEN '{$this->dataInicial}' AND '{$this->dataFinal}'
            and o46_instit in ({$this->getListaInstituicoes()})
          group by o46_codsup
          having count(*) = 2
        ), casos_suplementacao as (
          select o46_codsup,
                 o47_valor as valor_suplementacao,
                 codigo_siconfi as recurso_suplementacao,
                 codigo_siconfi
            from orcsuplemval
            join suplementacao_1015 on suplementacao_1015.o46_codsup = orcsuplemval.o47_codsup
            join orcdotacao on (o58_anousu, o58_coddot) = (o47_anousu, o47_coddot)
            join orctiporec on o15_codigo = o58_codigo
            join fonterecurso on fonterecurso.orctiporec_id = orctiporec.o15_codigo
                 and fonterecurso.exercicio = o58_anousu
           where o47_valor > 0
        ), casos_reducao as (
         select o46_codsup,
                o47_valor *-1 as valor_reducao,
                codigo_siconfi as recurso_reducao,
                codigo_siconfi
            from orcsuplemval
            join suplementacao_1015 on suplementacao_1015.o46_codsup = orcsuplemval.o47_codsup
            join orcdotacao on (o58_anousu, o58_coddot) = (o47_anousu, o47_coddot)
            join orctiporec on o15_codigo = o58_codigo
            join fonterecurso on fonterecurso.orctiporec_id = orctiporec.o15_codigo
                 and fonterecurso.exercicio = o58_anousu
           where o47_valor < 0
        ), com_recursos_diferentes as (
          select casos_suplementacao.o46_codsup,
                 valor_suplementacao,
                 recurso_suplementacao,
                 casos_suplementacao.codigo_siconfi,
                 valor_reducao ,
                 recurso_reducao
            from casos_suplementacao, casos_reducao
           where casos_suplementacao.o46_codsup = casos_reducao.o46_codsup
             and casos_suplementacao.recurso_suplementacao != casos_reducao.recurso_reducao
        )
        select com_recursos_diferentes.*,
               com_recursos_diferentes.valor_suplementacao as valor_alteracao_orcamentaria,
               o45_numlei AS numero_lei,
               o45_dataini AS data_lei,
               o39_numero AS numero_decreto,
               o39_data AS data_decreto,
               orcsuplem.o46_codsup AS codigo_suplementacao,
               o46_tiposup AS tipo_suplementacao,
               case when (
                    select count(distinct(dt.o58_instit))
                      from orcsuplemval s
                      join orcdotacao dt on dt.o58_coddot = s.o47_coddot
                                        and dt.o58_anousu = s.o47_anousu
                     where s.o47_codsup = orcsuplem.o46_codsup
                     group by s.o47_codsup
                    ) = 1 then false
                  else true
               end as entre_entidades,
               0 as valor_credito,
               0 as valor_saldo_reaberto,
               null as data_reabertura_credito_adicional
          from com_recursos_diferentes
          join orcsuplem on orcsuplem.o46_codsup = com_recursos_diferentes.o46_codsup
          join orcprojeto on orcprojeto.o39_codproj = orcsuplem.o46_codlei
          join orclei on orclei.o45_codlei = orcprojeto.o39_codlei
          ";

        $rs = db_query($sql);
        if (pg_num_rows($rs) > 0) {
            while ($dados = pg_fetch_assoc($rs)) {
                $this->suplementacoesRemanejamentoRecursos[$dados['o46_codsup']] = $dados;
            }
        }
    }
    /**
     * @param $rs
     * @return array
     */
    protected function agrupaValoresDecreto($rs)
    {

        $suplementacoes = [];
        while ($dados = pg_fetch_assoc($rs)) {
            // cria um hash agrupador pelo código da suplementacao e do recurso
            if (array_key_exists($dados['o46_codsup'], $this->suplementacoesRemanejamentoRecursos)) {
                continue;
            }
            $hash = "{$dados['o46_codsup']}#{$dados['codigo_siconfi']}#{$dados['o15_recurso']}";

            if (!array_key_exists($hash, $suplementacoes)) {
                $suplementacoes[$hash] = $dados;
            } else {
                $suplementacoes[$hash]['entre_entidades'] = $dados['entre_entidades'] == 't';
                $suplementacoes[$hash]['valor_credito'] += $dados['valor_credito'];
                $suplementacoes[$hash]['valor_reducao'] += $dados['valor_reducao'];

                $suplementacoes[$hash]['valor_alteracao_orcamentaria'] += $dados['valor_alteracao_orcamentaria'];
                $suplementacoes[$hash]['valor_saldo_reaberto'] += $dados['valor_saldo_reaberto'];
            }

            if ($dados['reducao'] == 't') {
                $suplementacoes[$hash]['recurso_reducao'] = $dados['codigo_siconfi'];
            } else {
                $suplementacoes[$hash]['recurso_suplementacao'] = $dados['codigo_siconfi'];
            }

            if (!empty($dados['data_reabertura_credito_adicional'])) {
                $suplementacoes[$hash]['data_reabertura_credito_adicional'] =
                    $dados['data_reabertura_credito_adicional'];
            }
        }
        return $suplementacoes;
    }

    protected function getBuilder()
    {
        return new DecretoBuilder2023();
    }
}
