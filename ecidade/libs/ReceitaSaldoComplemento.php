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
 * Essa função foi criada para ser usada no PAD. Portanto tomei a liberdade de altera-la. Caso outra matriz a use
 * em plugins, teria que verificar o impacto.
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
function ReceitaSaldoComplemento(
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
    $nivel_agrupar = 0
)
{

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

    $campoClasse = "null as classe, ";
    if ($nivel_agrupar == 1 || $nivel_agrupar == 2) {
        $campoClasse = "case when substr(o57_fonte,1,1)='9' then '4' else substr(o57_fonte,1,1) end::int4 as classe,";
    } elseif ($nivel_agrupar == 0) {
        $campoClasse = "substr(o57_fonte,1,1)::int4 as classe, ";
    }

    $camposNivel0 = "
        o57_fonte,
        {$campoClasse}
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
        complemento,
        recurso,
        codigo_siconfi,
        saldo_inicial,
        saldo_prevadic_acum,
        saldo_inicial_prevadic,
        saldo_anterior,
        saldo_arrecadado,
        saldo_a_arrecadar,
        saldo_arrecadado_acumulado,
        saldo_prev_anterior
    ";

    $camposNivel1 = "
        o57_fonte,
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
        '0' as recurso,
        '0' as complemento,
        '0' as codigo_siconfi,
        sum(saldo_inicial) as saldo_inicial,
        sum(saldo_prevadic_acum) as saldo_prevadic_acum,
        sum(saldo_inicial_prevadic) as saldo_inicial_prevadic,
        sum(saldo_anterior) as saldo_anterior,
        sum(saldo_arrecadado) as saldo_arrecadado,
        sum(saldo_a_arrecadar) as saldo_a_arrecadar,
        sum(saldo_arrecadado_acumulado) as saldo_arrecadado_acumulado,
        sum(saldo_prev_anterior) as saldo_prev_anterior
    ";

    $query = " select * from agrupado_nivel_0 ";
    if ($nivel_agrupar == 1) {
        $query = " select * from agrupado_nivel_1 ";
    }

    db_query("drop table if exists work_receita");

    $sql = "
    create table work_receita as
    with dados as (
        SELECT o70_codrec,
               o57_fonte,
               o15_recurso,
               o70_concarpeculiar,
               case
                 when complemento_lancamento.o200_sequencial is not null and complemento_lancamento.o200_tribunal is true
                   then complemento_lancamento.o200_sequencial
                 when complemento_lancamento.o200_sequencial IS NULL
                   then null
                 when complemento_lancamento.o200_tribunal IS FALSE THEN 0
               end as complemento_lancamento,
               case
                 when complemento_recurso.o200_tribunal is true
                   then complemento_recurso.o200_sequencial
                 else 0
               end as complemento_recurso,
               codigo_siconfi,
               c70_valor,
               o70_valor,
               c53_tipo,
               c70_data
          FROM orcamento.orcreceita
         inner join orcamento.orcfontes on orcfontes.o57_codfon = orcreceita.o70_codfon
                and orcfontes.o57_anousu = orcreceita.o70_anousu
         inner join orcamento.orctiporec recurso on o70_codigo = recurso.o15_codigo
         inner join orcamento.fonterecurso on orctiporec_id = recurso.o15_codigo
                    and fonterecurso.exercicio = orcreceita.o70_anousu
         inner join orcamento.complementofonterecurso as complemento_recurso on complemento_recurso.o200_sequencial = recurso.o15_complemento
          left join contabilidade.conlancamrec on conlancamrec.c74_anousu = orcreceita.o70_anousu
                and conlancamrec.c74_codrec = orcreceita.o70_codrec
                and conlancamrec.c74_data between '{$anousu}-01-01' and '{$datafim}'
          left join contabilidade.conlancam on c74_codlan = c70_codlan
          left join contabilidade.conlancamcomplementorecurso on o201_codlan = c70_codlan
          left join orcamento.complementofonterecurso as complemento_lancamento on complemento_lancamento.o200_sequencial = o201_complemento
          left join contabilidade.conlancamdoc on c71_codlan = c74_codlan
          left join contabilidade.conhistdoc on c53_coddoc = c71_coddoc
         where o70_anousu = {$anousu} {$condicao}
    ), soma_valores as (
        select o70_codrec,
               o57_fonte,
               o15_recurso as recurso,
               o70_concarpeculiar,
               case
                 when complemento_lancamento is null then complemento_recurso
                 else complemento_lancamento
               end as complemento,
               codigo_siconfi,
               case
                   when (complemento_lancamento = complemento_recurso) or complemento_lancamento is null
                     then o70_valor
                   else 0
               end as saldo_inicial,
               coalesce(sum(
                 case
                   WHEN c53_tipo = 110 and c70_data between '{$dataini}' AND '{$datafim}'
                     THEN ROUND(C70_VALOR,2)::numeric
                   WHEN c53_tipo = 111 and c70_data between '{$dataini}' AND '{$datafim}'
                     THEN ROUND(C70_VALOR*-1,2)::numeric END ),0
               ) AS saldo_inicial_prevadic,
               coalesce(sum(
                 case c53_tipo
                    when 110 THEN ROUND(C70_VALOR,2)::FLOAT8
                    WHEN 111 THEN ROUND(C70_VALOR*-1,2)::FLOAT8 END ),0
               ) AS saldo_prevadic_acum,
               coalesce(sum(
                 case
                    WHEN c53_tipo = 100 and c70_data < '{$dataini}' THEN ROUND(C70_VALOR,2)::numeric
                    WHEN c53_tipo = 101 and c70_data < '{$dataini}' THEN ROUND(C70_VALOR*-1,2)::numeric END ),0
               ) AS saldo_anterior,
               coalesce(sum(
                 case
                    WHEN c53_tipo = 100 and c70_data between '{$dataini}' AND '{$datafim}' THEN ROUND(C70_VALOR,2)::numeric
                    WHEN c53_tipo = 101 and c70_data between '{$dataini}' AND '{$datafim}' THEN ROUND(C70_VALOR*-1,2)::numeric END ),0
               ) AS saldo_arrecadado,
               coalesce(sum(
                 case c53_tipo
                    WHEN 100 THEN ROUND(C70_VALOR,2)::numeric
                    WHEN 101 THEN ROUND(C70_VALOR*-1,2)::numeric END ),0
               ) AS saldo_arrecadado_acumulado,
               o70_valor - coalesce(sum(
                 case c53_tipo
                    WHEN 100 THEN ROUND(C70_VALOR,2)::numeric
                    WHEN 101 THEN ROUND(C70_VALOR*-1,2)::numeric END ),0
               ) AS saldo_a_arrecadar,
               sum(0) as saldo_prev_anterior
          from dados
         group by o70_codrec,
                  o57_fonte,
                  o15_recurso,
                  o70_concarpeculiar,
                  complemento,
                  codigo_siconfi,
                  o70_valor,
                  saldo_inicial
        order by o57_fonte, o70_concarpeculiar, o15_recurso
    ), agrupado_nivel_0 as (
      select {$camposNivel0}
        from soma_valores
    ), agrupado_nivel_1 as (
      select {$camposNivel1}
        from agrupado_nivel_0
       group by o57_fonte,
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
                o70_codrec,
                recurso,
                codigo_siconfi
    ) $query
    ";


    if ($comit == true) {
        db_query('begin');
    }

    db_query("drop table if exists work_receita");
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
        $receita->complemento = $dados->complemento;
        $receita->recurso = $dados->recurso;
        $receita->codigo_siconfi = $dados->codigo_siconfi;
        $receita->nivel = $nivel;
        $receita->saldo_inicial = $dados->saldo_inicial;
        $receita->saldo_prevadic_acum = $dados->saldo_prevadic_acum;
        $receita->saldo_inicial_prevadic = $dados->saldo_inicial_prevadic;
        $receita->saldo_anterior = $dados->saldo_anterior;
        $receita->saldo_arrecadado = $dados->saldo_arrecadado;
        $receita->saldo_a_arrecadar = $dados->saldo_a_arrecadar;
        $receita->saldo_arrecadado_acumulado = $dados->saldo_arrecadado_acumulado;
        $receita->saldo_prev_anterior = $dados->saldo_prev_anterior;
        $receitas[$estrutural->getEstrutural() . $dados->o70_concarpeculiar . $dados->complemento] = $receita;


        while ($nivel != 1) {
            $estrutural = new \ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalReceita($estrutural->getCodigoEstruturalPai());
            if (empty($receitas[$estrutural->getEstrutural()])) {
                $receita = new \stdClass();
                $receita->estrutural = $estrutural->getEstrutural();
                $receita->codrec = 0;
                $receita->concarpeculiar = '0';
                $receita->recurso = '0';
                $receita->codigo_siconfi = '0';
                $receita->complemento = 0;
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
          o70_concarpeculiar varchar default '000',
          recurso varchar default '0',
          codigo_siconfi varchar default '0',
          complemento int default 0,
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
                "'{$receita->concarpeculiar}'",
                "'{$receita->recurso}'",
                "'{$receita->codigo_siconfi}'",
                $receita->complemento,
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
    $sql2 = "
        select work_receita.*, o57_descr, coalesce(o70_instit, 0) as o70_instit, coalesce(codtrib, '0') as orgao_unidade
          from work_receita
          left join orcreceita on orcreceita.o70_codrec = work_receita.o70_codrec and o70_anousu = {$anousu}
          left join orcfontes on work_receita.o57_fonte = orcfontes.o57_fonte and o57_anousu  = {$anousu}
          left join db_config on db_config.codigo = orcreceita.o70_instit
        {$where}
         order by orcfontes.o57_fonte, o70_concarpeculiar, recurso
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
