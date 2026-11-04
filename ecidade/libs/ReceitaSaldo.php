<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

/**
 * Esta funcao retorna o recordset do saldo das receitas
 *
 * @param int $nivel Até qual o nível será apurado o saldo, pode ser:
 *    1 - classe
 *    2 - grupo
 *    3 - subgrupo
 *    4 - elemento
 *    5 - subelemento
 *    6 - item
 *    7 - subitem
 *    8 - subitem1
 *    9 - desdobramento1
 *    10 - desdobramento2
 *    11 - desdobramento3
 *    12 - recurso
 * @param int $tipo_nivel especifica a maneira de como será apurado o resultado, pode ser:
 *    1 - traz a árvore de elementos até o nível solicitado
 *        Ex.: 4.1                  200
 *             4.1.1                100
 *             4.1.1.1               50
 *             4.1.1.2               50
 *             4.1.2                100
 *             4.1.2.1               50
 *             4.1.2.2               50
 *    2 - traz o saldo do nível escolhido
 *        Ex.: 4.1.1.1               50
 *    3 - totaliza o saldo pelo nível escolhido
 *        Ex.: 0.0.0.1             1000
 * @param int $tipo_saldo 1 - SALDO INICIAL DA RECEITA - ORCAMENTO
 *                        2 - SALDO DA RECEITA  MENOS O ARRECADADO
 *                        3 - SALDO DA RECEITA  PELA CONTABILIDADE ...
 *                        4 - SALDO ACUMULADO POR MES
 * @param bool $descr retorna o record set com as descrições ou não, o default é 'true'
 * @param string $where condição
 * @param null $anousu ano do orçamento
 * @param null $dataini data inicial do intervalo
 * @param null $datafim data final do intervalo
 * @param bool $query retorna somente o sql, o default é retornar o recordset
 * @param string $campos
 * @param bool $comit
 * @param int $nivel_agrupar 0 - normal
 *                           1 - agrupa na receita
 *                           2 - nao agrupa na receita
 * @return string
 */
function ReceitaSaldo(
    $nivel = 11,
    $tipo_nivel = 1,
    $tipo_saldo = 2,
    $descr = true,
    $where = '',
    $anousu = null,
    $dataini = null,
    $datafim = null,
    $query = false,
    $campos = ' * ',
    $comit = true,
    $nivel_agrupar = 0,
    $filtroPrevisao=''
) {

    //echo "Antes:".$nivel."<br>";
    if ($nivel > 8) {
        $nivel++;
    }
    //echo "Depois:".$nivel."<br>";
    if ($anousu == null || $anousu == "") {
        $anousu = db_getsession("DB_anousu");
    }

    if ($dataini == null) {
        $dataini = date('Y-m-d', db_getsession('DB_datausu'));
    }

    if ($datafim == null) {
        $datafim = date('Y-m-d', db_getsession('DB_datausu'));
    }

    if ($where != '') {
        $condicao = " and " . $where;
    } else {
        $condicao = "";
    }


    if ($comit == true) {
        db_query('begin');
    }

    db_query("drop table if exists work_receita");
    $sql = "create table work_receita as ";
    if ($nivel_agrupar == 1) {
        $sql .= "

      select o57_fonte,
             classe,
             grupo,
             subgrupo,
             elemento,
             subelemento,
             item,
             subitem,
             subitem1,
             desdobramento1,
             desdobramento2,
             desdobramento3,
             0 as o70_codrec,
             '0' as o70_concarpeculiar,
             0 as o70_codigo,
             gestao,
             sum(saldo_inicial) as saldo_inicial,
             sum(saldo_prevadic_acum) as saldo_prevadic_acum,
             sum(saldo_inicial_prevadic) as saldo_inicial_prevadic,
             sum(saldo_anterior) as saldo_anterior,
             sum(saldo_arrecadado) as saldo_arrecadado,
             sum(saldo_a_arrecadar) as saldo_a_arrecadar,
             sum(saldo_arrecadado_acumulado) as saldo_arrecadado_acumulado,
             sum(saldo_prev_anterior) as saldo_prev_anterior

      from (";
    }
    $sql .= "
            select o57_fonte,";
    if ($nivel_agrupar == 1 || $nivel_agrupar == 2) {
        $sql .= "
         case when substr(o57_fonte,1,1)='9' then '4' else substr(o57_fonte,1,1) end::int4 as classe,
      ";
    } else {
        if ($nivel_agrupar == 0) {
            $sql .= " substr(o57_fonte,1,1)::int4 as classe, ";
        }
    }
    $sql .= "
    substr(o57_fonte,2,1)::int4  as grupo,
    substr(o57_fonte,3,1)::int4  as subgrupo,
    substr(o57_fonte,4,1)::int4  as elemento,
    substr(o57_fonte,5,1)::int4  as subelemento,
    substr(o57_fonte,6,2)::int4  as item,
    substr(o57_fonte,8,1)::int4  as subitem,
    substr(o57_fonte,9,1)::int4  as subitem1,
    substr(o57_fonte,10,2)::int4 as desdobramento1,
    substr(o57_fonte,12,2)::int4 as desdobramento2,
    substr(o57_fonte,14,2)::int4 as desdobramento3,
    o70_codrec,
    o70_concarpeculiar,
    o70_codigo,
    gestao,
    cast(coalesce(nullif(substr(fc_receitasaldo, 3,12),''),'0') as float8) as saldo_inicial,
    cast(coalesce(nullif(substr(fc_receitasaldo,16,12),''),'0') as float8) as saldo_prevadic_acum,
    cast(coalesce(nullif(substr(fc_receitasaldo,29,12),''),'0') as float8) as saldo_inicial_prevadic,
    cast(coalesce(nullif(substr(fc_receitasaldo,42,12),''),'0') as float8) as saldo_anterior,
    cast(coalesce(nullif(substr(fc_receitasaldo,55,12),''),'0') as float8) as saldo_arrecadado,
    cast(coalesce(nullif(substr(fc_receitasaldo,68,12),''),'0') as float8) as saldo_a_arrecadar,
    cast(coalesce(nullif(substr(fc_receitasaldo,81,12),''),'0') as float8) as saldo_arrecadado_acumulado,
    cast(coalesce(nullif(substr(fc_receitasaldo,94,12),''),'0') as float8) as saldo_prev_anterior
    from(select o70_anousu, o70_codrec, o70_codfon, o70_codigo, o70_valor, o70_reclan,
                o70_instit, o70_concarpeculiar,
                o57_codfon, o57_anousu, o57_fonte, o57_descr, o57_finali,
                fc_receitasaldo($anousu,o70_codrec,$tipo_saldo,'$dataini','$datafim'),
                gestao
    from orcreceita d
    inner join orcfontes e on d.o70_codfon = e.o57_codfon and e.o57_anousu = d.o70_anousu
      inner join orctiporec on d.o70_codigo = orctiporec.o15_codigo
      inner join fonterecurso on orctiporec.o15_codigo = fonterecurso.orctiporec_id and exercicio = d.o70_anousu
    where o70_anousu = $anousu
    $condicao
    order by o57_fonte
    ) as x
    {$filtroPrevisao}
    ";


    if ($nivel_agrupar == 1) {
        $sql .= "
      ) as x
      group by classe,
               grupo,
               subgrupo,
               elemento,
               subelemento,
               item,
               subitem,
               subitem1,
               desdobramento1,
               desdobramento2,
               desdobramento3,
               o70_codrec,
               o70_codigo,
               gestao

      ";
    }
//die($sql);
    db_query($sql);
    $result = db_query("select * from work_receita");
    $receitas = array();
    db_utils::makeCollectionFromRecord($result, function ($dados) use (&$receitas) {

        $estrutural = new \ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalReceita($dados->o57_fonte);

        $receita = new \stdClass();
        $nivel = $estrutural->getNivel();
        $receita->estrutural = $estrutural->getEstrutural();
        $receita->codrec = $dados->o70_codrec;
        $receita->concarpeculiar = $dados->o70_concarpeculiar;
        $receita->recurso = $dados->o70_codigo;
        $receita->gestao = $dados->gestao;
        $receita->nivel = $nivel;
        $receita->saldo_inicial = $dados->saldo_inicial;
        $receita->saldo_prevadic_acum = $dados->saldo_prevadic_acum;
        $receita->saldo_inicial_prevadic = $dados->saldo_inicial_prevadic;
        $receita->saldo_anterior = $dados->saldo_anterior;
        $receita->saldo_arrecadado = $dados->saldo_arrecadado;
        $receita->saldo_a_arrecadar = $dados->saldo_a_arrecadar;
        $receita->saldo_arrecadado_acumulado = $dados->saldo_arrecadado_acumulado;
        $receita->saldo_prev_anterior = $dados->saldo_prev_anterior;
        $receitas[$estrutural->getEstrutural() . $dados->o70_concarpeculiar] = $receita;

        while ($nivel != 1) {
            $estrutural = new \ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalReceita($estrutural->getCodigoEstruturalPai());
            if (empty($receitas[$estrutural->getEstrutural()])) {
                $receita = new \stdClass();
                $receita->estrutural = $estrutural->getEstrutural();
                $receita->codrec = 0;
                $receita->concarpeculiar = 0;
                $receita->recurso = 0;
                $receita->saldo_inicial = 0;
                $receita->saldo_prevadic_acum = 0;
                $receita->nivel = $estrutural->getNivel();
                $receita->saldo_inicial_prevadic = 0;
                $receita->saldo_anterior = 0;
                $receita->saldo_arrecadado = 0;
                $receita->saldo_a_arrecadar = 0;
                $receita->saldo_arrecadado_acumulado = 0;
                $receita->saldo_prev_anterior = 0;
                $receitas[$estrutural->getEstrutural()] = $receita;
            }

            $receita = $receitas[$estrutural->getEstrutural()];
            $receita->gestao = $dados->gestao;
            $receita->saldo_inicial += $dados->saldo_inicial;
            $receita->saldo_prevadic_acum += $dados->saldo_prevadic_acum;
            $receita->saldo_inicial_prevadic += $dados->saldo_inicial_prevadic;
            $receita->saldo_anterior += $dados->saldo_anterior;
            $receita->saldo_arrecadado += $dados->saldo_arrecadado;
            $receita->saldo_a_arrecadar += $dados->saldo_a_arrecadar;
            $receita->saldo_arrecadado_acumulado += $dados->saldo_arrecadado_acumulado;
            $receita->saldo_prev_anterior += $dados->saldo_prev_anterior;
            $nivel = $estrutural->getNivel();
        }
    });

    ksort($receitas);

    db_query("drop table work_receita;");
    $sqlTabelaWorkReceita = "create table work_receita
         (o57_fonte varchar,
          nivel int,
          classe int,
          grupo int,
          subgrupo int,
          elemento int,
          subelemento int,
          item int,
          subitem int,
          subitem1 int,
          desdobramento1 int,
          desdobramento2 int,
          desdobramento3  int,
          o70_codrec int default 0,
          o70_concarpeculiar int default 0,
          o70_codigo int default 0,
          gestao varchar,
          saldo_inicial numeric default 0,
          saldo_prevadic_acum numeric default 0,
          saldo_inicial_prevadic numeric default 0,
          saldo_anterior numeric default 0,
          saldo_arrecadado numeric default 0,
          saldo_a_arrecadar numeric default 0,
          saldo_arrecadado_acumulado numeric default 0,
          saldo_prev_anterior numeric default 0
          );
          create index w_receita_o57_fonte_in on work_receita(o57_fonte);
          create index w_receita_nivel_in on work_receita(nivel);";

    $rsTabela = db_query($sqlTabelaWorkReceita);
    $insert = "insert into  work_receita values ";
    $listaInserts = array();

    if (count($receitas) > 0) {
        foreach ($receitas as $receita) {
            $campos = array(
                $receita->estrutural,
                $receita->nivel,
                substr($receita->estrutural, 0, 1),
                substr($receita->estrutural, 1, 1),
                substr($receita->estrutural, 2, 1),
                substr($receita->estrutural, 3, 1),
                substr($receita->estrutural, 4, 1),
                substr($receita->estrutural, 5, 2),
                substr($receita->estrutural, 7, 1),
                substr($receita->estrutural, 8, 1),
                substr($receita->estrutural, 9, 2),
                substr($receita->estrutural, 11, 2),
                substr($receita->estrutural, 13, 2),
                $receita->codrec,
                $receita->concarpeculiar,
                $receita->recurso,
                $receita->gestao,
                $receita->saldo_inicial,
                $receita->saldo_prevadic_acum,
                $receita->saldo_inicial_prevadic,
                $receita->saldo_anterior,
                $receita->saldo_arrecadado,
                $receita->saldo_a_arrecadar,
                $receita->saldo_arrecadado_acumulado
            );
            $listaInserts[] = " ( " . implode(",", $campos) . " ) ";
        }

        $insert .= implode(",", $listaInserts);
        db_query($insert);
    }

    $where = array();
    switch ($tipo_nivel) {
        case 1:
            $where[] = "nivel <= {$nivel}";
            break;

        case 2:
        case 3:
            $where[] = "nivel = {$nivel}";
            break;
    }
    $where = !empty($where) ? " where " . implode(" and ", $where) : '';
    $sql2 = "select work_receita.*, o57_descr, o15_descr
               from work_receita
                   left join orcfontes on work_receita.o57_fonte = orcfontes.o57_fonte
                                      and o57_anousu  = {$anousu}
                   left join orctiporec on o15_codigo  = o70_codigo
                   {$where}
              order by orcfontes.o57_fonte,
                       o70_concarpeculiar,
                       o70_codigo,
                       o15_descr
            ";

    db_query("
        update work_receita
           set saldo_a_arrecadar = round(((saldo_inicial + saldo_prevadic_acum) - saldo_arrecadado_acumulado), 2);
    ");

    if ($comit == true) {
        db_query('commit');
    }

    $resultreceita = $sql2;
    if ($query == false) {
        $resultreceita = db_query($sql2);
    }

    return $resultreceita;
}
