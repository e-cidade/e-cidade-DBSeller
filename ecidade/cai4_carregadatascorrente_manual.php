<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2014  DBselller Servicos de Informatica
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

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_corrente_classe.php"));

db_postmemory($_POST);
db_postmemory($_SERVER);
$clcorrente = new cl_corrente;

$strRetorno = "";
$pipe       = "";
$sWhereData = "";
$sWhereExtrato = "";
$traco      = "-";

$sqlDadosConta  = " select distinct ";
$sqlDadosConta .= "        db83_sequencial as reduzido,";
$sqlDadosConta .= "        db83_descricao  as descricao,";
$sqlDadosConta .= "        db90_codban as banco,";
$sqlDadosConta .= "        db83_conta||'-'||db83_dvconta as contabancaria,";
$sqlDadosConta .= "        db89_codagencia||'-'||db89_digito as agencia,";
$sqlDadosConta .= "        db83_contaunica as conta_unica";
$sqlDadosConta .= "   from contabancaria ";
$sqlDadosConta .= "        inner join bancoagencia on bancoagencia.db89_sequencial = contabancaria.db83_bancoagencia ";
$sqlDadosConta .= "        inner join db_bancos    on db_bancos.db90_codban        = bancoagencia.db89_db_bancos ";
$sqlDadosConta .= "  where contabancaria.db83_sequencial = {$conta} ";
$rsDadosConta   = db_query($sqlDadosConta);
if ($rsDadosConta && pg_num_rows($rsDadosConta) > 0) {
    db_fieldsmemory($rsDadosConta, 0);
}

if (isset($sData) && $sData != "") {
    $sWhereData = " and k12_data > '".implode("-", array_reverse(explode("/", $sData)))."'";
    $sWhereExtrato = " and k86_data > '".implode("-", array_reverse(explode("/", $sData)))."'";
}

$sWhereReduz  = " select c61_reduz ";
$sWhereReduz .= "   from contabancaria ";
$sWhereReduz .= "        inner join conplanocontabancaria on conplanocontabancaria.c56_contabancaria = contabancaria.db83_sequencial and conplanocontabancaria.c56_anousu = " . db_getsession('DB_anousu');
$sWhereReduz .= "        inner join conplanoreduz         on conplanoreduz.c61_codcon                = conplanocontabancaria.c56_codcon ";
$sWhereReduz .= "                                        and conplanoreduz.c61_anousu                = conplanocontabancaria.c56_anousu ";
$sWhereReduz .= "                                        and conplanoreduz.c61_reduz                 = conplanocontabancaria.c56_reduz ";
$sWhereReduz .= "                                        and conplanoreduz.c61_anousu                = ".db_getsession('DB_anousu');
//se for conta unica a instituicao nao deve ser considerada
if ($conta_unica == "f") {
    $sWhereReduz .= "                                        and conplanoreduz.c61_instit                = ".db_getsession('DB_instit');
}
$sWhereReduz .= "  where contabancaria.db83_sequencial = {$conta} ";

$sqlData = "";


$sqlData  = "          select distinct k12_data ";
$sqlData .= "            from corrente ";
$sqlData .= "            left join conciliacor           on conciliacor.k84_data             = corrente.k12_data ";
$sqlData .= "                                           and conciliacor.k84_id               = corrente.k12_id ";
$sqlData .= "                                           and conciliacor.k84_autent           = corrente.k12_autent ";
$sqlData .= "           inner join conplanoreduz         on conplanoreduz.c61_reduz          = corrente.k12_conta ";
$sqlData .= "                                           and conplanoreduz.c61_anousu         = ".db_getsession('DB_anousu');
//se for conta unica a instituicao nao deve ser considerada
if ($conta_unica == "f") {
    $sqlData .= "                                           and conplanoreduz.c61_instit         = ".db_getsession('DB_instit');
}
$sqlData .= "           inner join conplanocontabancaria on conplanocontabancaria.c56_codcon = conplanoreduz.c61_codcon ";
$sqlData .= "                                           and conplanocontabancaria.c56_anousu = conplanoreduz.c61_anousu ";
$sqlData .= "                                           and conplanocontabancaria.c56_reduz  = conplanoreduz.c61_reduz ";
$sqlData .= "            left join concilia              on concilia.k68_data                = corrente.k12_data ";
$sqlData .= "                                           and concilia.k68_contabancaria       = conplanocontabancaria.c56_contabancaria ";
$sqlData .= "           where ( conciliacor.k84_data is null and conciliacor.k84_id is null and conciliacor.k84_autent is null )";
$sqlData .= "             and ( concilia.k68_data is null and concilia.k68_contabancaria is null ) ";
$sqlData .= "             and corrente.k12_conta in ($sWhereReduz) ";

if (!isset($lImplantaConcilia)) {
    $sqlData .= "           and corrente.k12_data  > ( select min(k68_data) from concilia where k68_contabancaria = $conta )";
}

$sqlData .= "             $sWhereData ";
$sqlData .= "           union all ";
$sqlData .= "          select distinct corlanc.k12_data ";
$sqlData .= "            from corlanc ";
$sqlData .= "                 inner join slip                  on corlanc.k12_codigo               = slip.k17_codigo ";
$sqlData .= "                  left join conciliacor           on conciliacor.k84_data             = corlanc.k12_data ";
$sqlData .= "                                                 and conciliacor.k84_id               = corlanc.k12_id    ";
$sqlData .= "                                                 and conciliacor.k84_autent           = corlanc.k12_autent ";
$sqlData .= "                 inner join conplanoreduz         on conplanoreduz.c61_reduz          = corlanc.k12_conta ";
$sqlData .= "                                                 and conplanoreduz.c61_anousu         = ".db_getsession("DB_anousu");
//se for conta unica a instituicao nao deve ser considerada
if ($conta_unica == "f") {
    $sqlData .= "                                                 and conplanoreduz.c61_instit         = ".db_getsession("DB_instit");
}
$sqlData .= "                 inner join conplanocontabancaria on conplanocontabancaria.c56_codcon = conplanoreduz.c61_codcon ";
$sqlData .= "                                                 and conplanocontabancaria.c56_anousu = conplanoreduz.c61_anousu ";
$sqlData .= "                                                 and conplanocontabancaria.c56_reduz  = conplanoreduz.c61_reduz ";
$sqlData .= "                  left join concilia              on concilia.k68_data                = corlanc.k12_data ";
$sqlData .= "                                                 and concilia.k68_contabancaria       = conplanocontabancaria.c56_contabancaria ";
$sqlData .= "                                                 and concilia.k68_contabancaria       = {$conta}";
$sqlData .= "           where                                                                 ";

/**
 *  Adicionada validacao, para verificar se a conciliacao foi feita para a conta determinada
 */
$sqlData .= "                not exists ( select 1                                                                                      ";
$sqlData .= "                               from conciliacor                                                                            ";
$sqlData .= "                                    inner join conciliaitem on conciliaitem.k83_sequencial = conciliacor.k84_conciliaitem  ";
$sqlData .= "                                    inner join concilia     on concilia.k68_sequencial     = conciliaitem.k83_concilia     ";
$sqlData .= "                              where k84_data    = corlanc.k12_data                                                         ";
$sqlData .= "                                and k84_id      = corlanc.k12_id                                                           ";
$sqlData .= "                                and k84_autent  = corlanc.k12_autent                                                       ";
$sqlData .= "                                and concilia.k68_contabancaria = {$conta}                                                  ";
$sqlData .= "                              union                                                      ";
$sqlData .= "                             select 1                                                    ";
$sqlData .= "                               from conciliapendcorrente                                 ";
$sqlData .= "                                    inner join concilia on k68_sequencial = k89_concilia ";
$sqlData .= "                              where k89_id     = corlanc.k12_id                          ";
$sqlData .= "                                and k89_data   = corlanc.k12_data                        ";
$sqlData .= "                                and k89_autent = corlanc.k12_autent                      ";
$sqlData .= "                                and concilia.k68_contabancaria = {$conta} )              ";


$sqlData .= "             and ( k68_data is null and k68_contabancaria is null ) ";
$sqlData .= "             and (    slip.k17_credito  in ( $sWhereReduz ) 
                                or slip.k17_debito   in ( $sWhereReduz ) ) ";

if (!isset($lImplantaConcilia)) {
    $sqlData .= "           and corlanc.k12_data  > ( select min(k68_data) from concilia where k68_contabancaria = $conta ) ";
}

$sqlData .= " {$sWhereData} ";
$sqlData .= "           union all ";
$sqlData .= "          select distinct k86_data ";
$sqlData .= "            from extratolinha ";
$sqlData .= "            left join conciliaextrato on k87_extratolinha  = k86_sequencial ";
$sqlData .= "            left join concilia        on k68_data          = k86_data ";
$sqlData .= "                                     and k68_contabancaria = k86_contabancaria ";
$sqlData .= "           where k87_extratolinha is null ";
$sqlData .= "             and (k68_data is null and k68_contabancaria is null)";
$sqlData .= " {$sWhereExtrato} ";

if (!isset($lImplantaConcilia)) {
    $sqlDatan   = " select min(k12_data) as k12_data";
    $sqlDatan  .= "   from ( ";
    $sqlDatan  .= $sqlData;
}

if (!isset($lImplantaConcilia)) {
    $sqlDatan .= "             and k86_data  > ( select min(k68_data) from concilia where k68_contabancaria = $conta ) ";
}
$sqlData .= "             and k86_contabancaria = $conta ";

if (!isset($lImplantaConcilia)) {
    $sqlDatan .= "             and k86_contabancaria = $conta ";
    $sqlDatan .= "        ) as x ";
}

if ($acao == "mes") {
    $sqlDataOrdenada  = " select max(k12_data) as k12_data
  	                 from  ($sqlData) as x 
	                 where extract(month from k12_data)::varchar || extract(year from k12_data)::varchar in (";
    $sqlDataOrdenada .= "   select extract(month from k12_data)::varchar ||  extract(year from k12_data)::varchar  ";
    $sqlDataOrdenada .= "    from ({$sqlDatan}) as xx ";
    $sqlDataOrdenada .= "  order by k12_data desc limit 1 ) " ;
} else {
    if (isset($lImplantaConcilia)) {
        $sqlDataOrdenada  = " select k12_data as k12_data ";
        $sqlDataOrdenada .= "    from ({$sqlData}) as xx ";
        $sqlDataOrdenada .= "  order by k12_data desc " ;
    } else {
        $sqlDataOrdenada  = " select k12_data as k12_data ";
        $sqlDataOrdenada .= "    from ({$sqlData}) as xx ";
        $sqlDataOrdenada .= "  order by k12_data limit 1 " ;
    }
}


$rsDatas = $clcorrente->sql_record($sqlDataOrdenada);
$numrows = $clcorrente->numrows;
if ($numrows > 0) {
    for ($i=0; $i<$numrows; $i++) {
        db_fieldsmemory($rsDatas, $i);
        $strRetorno .= $pipe.$k12_data.';'.db_formatar($k12_data, 'd');
        $pipe = "|";
    }

    if (substr($sData, 5, 2) != substr($k12_data, 5, 2)) {
        if (($diferenca + 0) != 0) {
            $strRetorno = "1;Conciliacao do mes nao esta fechada. Verifique Diferenca! Proxima Data: [".db_formatar($k12_data, 'd')."]";
        }
    }
} else {
    $strRetorno .= '0;Sem datas disponiveis para a conta selecionada';
}

echo $strRetorno;
