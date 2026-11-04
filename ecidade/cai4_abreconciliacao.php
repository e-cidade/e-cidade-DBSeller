<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009 DBSeller Servicos de Informatica
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
	include(modification("classes/db_concilia_classe.php"));
	include(modification("classes/db_conciliapendcorrente_classe.php"));
	include(modification("classes/db_conciliapendextrato_classe.php"));
	include(modification("classes/db_corrente_classe.php"));
	include(modification("classes/db_extratolinha_classe.php"));

	$clcorrente             = new cl_corrente;
	$clextratolinha         = new cl_extratolinha;
	$clconcilia             = new cl_concilia;
	$clconciliapendcorrente = new cl_conciliapendcorrente;
	$clconciliapendextrato  = new cl_conciliapendextrato;

	$sqlerro = false;
	$erromsg = "";

	db_postmemory($HTTP_POST_VARS);

	// verifica se ja nao existe uma conciliacao aberta para a conta selecionada //
	$rsVerificaConcilacao = $clconcilia->sql_record($clconcilia->sql_query_file(null,"*",null," k68_contabancaria = $conta and k68_conciliastatus = 1 "));
	if ($clconcilia->numrows > 0) {
		//die("2|||Ja existe uma conciliacao aberta para esta conta, salve esta conciliacao antes de passar para a proxima ");
	}

  $sWhereReduz  = " select c61_reduz ";
  $sWhereReduz .= "   from contabancaria ";
  $sWhereReduz .= "        inner join conplanocontabancaria on conplanocontabancaria.c56_contabancaria = contabancaria.db83_sequencial and conplanocontabancaria.c56_anousu = " . db_getsession("DB_anousu") ;
  $sWhereReduz .= "        inner join conplanoreduz         on conplanoreduz.c61_codcon = conplanocontabancaria.c56_codcon ";
  $sWhereReduz .= "                                        and conplanoreduz.c61_anousu = conplanocontabancaria.c56_anousu ";
  $sWhereReduz .= "                                        and conplanoreduz.c61_reduz  = conplanocontabancaria.c56_reduz ";
  $sWhereReduz .= "  where contabancaria.db83_sequencial = {$conta} ";
//  $sWhereReduz .= "  and contabancaria.db83_instit = ".db_getsession("DB_instit");

  // select somando o valor total do corrente


	db_inicio_transacao();

	$rsTotalCorrente = $clcorrente->sql_record($clcorrente->sql_query_file(null,
                                                                         null,
                                                                         null,
                                                                         " coalesce(sum(k12_valor),0) as totalcorrente ",
                                                                         null,
                                                                         " k12_data = '".$data."' and k12_conta in ($sWhereReduz) "));
	if ($clcorrente->numrows > 0) {
		db_fieldsmemory($rsTotalCorrente,0);
	}

	// select somando o valor total do extrato
	$rsTotalExtrato = $clextratolinha->sql_record($clextratolinha->sql_query_file(null,
                                                                                " coalesce(sum(k86_valor),0) as totalextrato ",
                                                                                null,
                                                                                " k86_data = '".$data."' and k86_contabancaria = $conta "));
	if ($clextratolinha->numrows > 0){
		db_fieldsmemory($rsTotalExtrato,0);
	}



	$clconcilia->k68_data           = $data;
	$clconcilia->k68_contabancaria  = $conta;
	$clconcilia->k68_saldoextrato   = "$totalextrato";
	$clconcilia->k68_saldocorrente  = "$totalcorrente";
	$clconcilia->k68_conciliastatus = 1;
	$clconcilia->incluir(null);
	$erromsg = $clconcilia->erro_msg;
	if($clconcilia->erro_status == 0){
		$sqlerro = true;
	}


 // incluir autenticacoes do dia com pendencia do dia
        // select na corrente por data e conta e for duplicando as pendencias de conciliacoes anteriores para essa em questao
        $sqlPendCorrente  = " select k68_data as data_ultima_proc";
        $sqlPendCorrente .= "     from concilia ";
        $sqlPendCorrente .= "    where k68_data = (select k68_data
                                             from concilia
                                            where k68_data < '".$data."'
                                              and k68_contabancaria = $conta
                                            order by k68_data
                                             desc limit 1)";
        $sqlPendCorrente .= "      and k68_contabancaria = ".$conta ;

        $rsCorrente = $clconcilia->sql_record($sqlPendCorrente);
        $intNumrows = $clconcilia->numrows;
        for($i = 0; $i < $intNumrows; $i++ ){
            db_fieldsmemory($rsCorrente,$i);
        }

        // incluido para juntar todos os dias ate a conciliacao
        if( !isset($data_ultima_proc) or $data_ultima_proc == "" ){
            $data_ultima_proc = $data;
        }


	// select na corrente por data e conta e for duplicando as pendencias de conciliacoes anteriores para essa em questao
	$sqlPendCorrente  = " select conciliapendcorrente.*, k68_data as data_ultima_proc";
	$sqlPendCorrente .= "	  from concilia ";
	$sqlPendCorrente .= "	       inner join conciliapendcorrente on k89_concilia = k68_sequencial ";
	$sqlPendCorrente .= "	 where k68_data = (select k68_data
                                             from concilia
                                            where k68_data < '".$data."'
                                              and k68_contabancaria = $conta
                                            order by k68_data
                                             desc limit 1)";
	$sqlPendCorrente .= "	   and k68_contabancaria = ".$conta ;

	$rsCorrente = $clcorrente->sql_record($sqlPendCorrente);
	$intNumrows = $clcorrente->numrows;
	for($i = 0; $i < $intNumrows; $i++ ){
		db_fieldsmemory($rsCorrente,$i);
		$clconciliapendcorrente->k89_concilia       = $clconcilia->k68_sequencial;
		$clconciliapendcorrente->k89_id             = $k89_id;
		$clconciliapendcorrente->k89_data           = $k89_data;
		$clconciliapendcorrente->k89_autent         = $k89_autent;
		$clconciliapendcorrente->k89_justificativa  = $k89_justificativa;
		$clconciliapendcorrente->k89_conciliaorigem = 1;
		$clconciliapendcorrente->incluir(null);
		if($clconciliapendcorrente->erro_status == 0){
			$erromsg = $clconciliapendcorrente->erro_msg;
			$sqlerro = true;
			break;
		}
	}

        // incluir autenticacoes do dia com pendencia do dia

$sqlAutentica  = " select caixa,                                  ";
$sqlAutentica .= "        autent,                                 ";
$sqlAutentica .= "        arquivo,                                ";
$sqlAutentica .= "        data,                                   ";
$sqlAutentica .= "        sum(valor_debito) as valor_debito,      ";
$sqlAutentica .= "        sum(valor_credito) as valor_credito,    ";
$sqlAutentica .= "        cheque,                                 ";
$sqlAutentica .= "        credor,                                 ";
$sqlAutentica .= "        min(detalhe) as detalhe,                ";
$sqlAutentica .= "        classe,                                 ";
$sqlAutentica .= "        itemconciliacao,                        ";
$sqlAutentica .= "        erro,                                    ";
$sqlAutentica .= "        justificativa                                    ";
$sqlAutentica .= "   from ( select distinct ";
$sqlAutentica .= "                 caixa as caixa, ";
$sqlAutentica .= "                 autent as autent, ";
$sqlAutentica .= "                 arquivo as arquivo, ";
$sqlAutentica .= "                 data as data, ";
$sqlAutentica .= "                 valor_debito as valor_debito, ";
$sqlAutentica .= "                 valor_credito as valor_credito, ";
$sqlAutentica .= "                 receita as receita, ";
$sqlAutentica .= "                 cheque as cheque, ";
$sqlAutentica .= "                 credor as credor, ";
$sqlAutentica .= "                 detalhe as detalhe, ";
$sqlAutentica .= "                 case ";
$sqlAutentica .= "                   when x.classe = 'conciliado' then 'conciliado' ";
$sqlAutentica .= "                   when ( k86_data is not null and k86_documento is not null ) then 'preselecionado' ";
$sqlAutentica .= "                   else x.classe ";
$sqlAutentica .= "                 end as classe, ";
$sqlAutentica .= "                 itemconciliacao, ";
$sqlAutentica .= "                 justificativa, ";
$sqlAutentica .= "                 erro as erro ";
$sqlAutentica .= "            from ( ";

// pendentes
  $sqlAutentica .= "                   select distinct ";
  $sqlAutentica .= "                          ricaixa           as caixa, ";
  $sqlAutentica .= "                          riautent          as autent, ";
  $sqlAutentica .= "                          ( select e75_codgera from conlancamcorrente inner join corlanc on c86_id = k12_id and c86_data = k12_data and c86_autent = k12_autent inner join conlancamslip on c84_slip = k12_codigo and c84_conlancam = c86_conlancam inner join empageslip on e89_codigo = k12_codigo inner join empagedadosretmov on e76_codmov = e89_codmov and e76_processado = true and e76_dataefet = k12_data inner join empagedadosret on e75_codret = e76_codret inner join empagedadosretmovocorrencia on e02_empagedadosret = e76_codret and e02_empagedadosretmov = e76_codmov and e02_errobanco in (2, 269) where corlanc.k12_data = ridata and corlanc.k12_id = ricaixa and corlanc.k12_autent = riautent union select e75_codgera from conlancamcorgrupocorrente inner join corgrupocorrente on k105_sequencial = c23_corgrupocorrente inner join corempagemov  on k12_id = k105_id and k12_data = k105_data and k12_autent = k105_autent inner join empagedadosretmov on e76_codmov = k12_codmov and e76_processado = true and e76_dataefet = k105_data inner join empagedadosret on e75_codret = e76_codret inner join empagedadosretmovocorrencia on e02_empagedadosret = e76_codret and e02_empagedadosretmov = e76_codmov and e02_errobanco in (2, 269) where corempagemov.k12_data = ridata and corempagemov.k12_id = ricaixa and corempagemov.k12_autent = riautent ) as arquivo, ";
  $sqlAutentica .= "                          ridata            as data, ";
  $sqlAutentica .= "                          rnvalordebito     as valor_debito, ";
  $sqlAutentica .= "                          rivalorcredito    as valor_credito, ";
  $sqlAutentica .= "                          rireceita         as receita, ";
  $sqlAutentica .= "                          richeque          as cheque, ";
  $sqlAutentica .= "                          rtcredor          as credor, ";
  $sqlAutentica .= "                          rtdetalhe         as detalhe, ";
  $sqlAutentica .= "                          k89_justificativa as justificativa, ";
  $sqlAutentica .= "                          'pendente'        as classe,";
  $sqlAutentica .= "                          0                 as itemconciliacao, ";
  $sqlAutentica .= "                          rberro            as erro ";
  $sqlAutentica .= "                     from conciliapendcorrente ";
  $sqlAutentica .= "                          inner join concilia on k68_sequencial = k89_concilia ";
  $sqlAutentica .= "                          inner join ( select * from fc_extratocaixa(" . db_getsession('DB_instit') . ",$conta,'" . $data_ultima_proc. "'::date + 1,'" . $data . "',false ) ) as x ";
  $sqlAutentica .= "                                                          on ricaixa  = k89_id ";
  $sqlAutentica .= "                                                         and riautent = k89_autent ";
  $sqlAutentica .= "                                                         and ridata   = k89_data ";
  $sqlAutentica .= "                           left join conciliacor          on ricaixa  = k84_id ";
  $sqlAutentica .= "                                                         and riautent = k84_autent ";
  $sqlAutentica .= "                                                         and ridata   = k84_data ";
  $sqlAutentica .= "                           left join conciliaitem         on k83_sequencial = k84_conciliaitem ";
  $sqlAutentica .= "                                                         and k83_concilia = (select k68_sequencial ";
  $sqlAutentica .= "                                                                               from concilia  ";
  $sqlAutentica .= "                                                                              where k68_contabancaria = {$conta} ";
  $sqlAutentica .= "                                                                                and k68_data = '" . $data . "' ) ";
  $sqlAutentica .= "                     where ( k83_sequencial is null ) ";
  $sqlAutentica .= "                       and k68_sequencial = " . $clconcilia->k68_sequencial;

  $sqlAutentica .= "                     union all ";

// conciliados
$sqlAutentica .= "                    select distinct ";
$sqlAutentica .= "                           ricaixa        as caixa, ";
$sqlAutentica .= "                           riautent       as autent, ";
$sqlAutentica .= "                           ( select e75_codgera from conlancamcorrente inner join corlanc on c86_id = k12_id and c86_data = k12_data and c86_autent = k12_autent inner join conlancamslip on c84_slip = k12_codigo and c84_conlancam = c86_conlancam inner join empageslip on e89_codigo = k12_codigo inner join empagedadosretmov on e76_codmov = e89_codmov and e76_processado = true and e76_dataefet = k12_data inner join empagedadosret on e75_codret = e76_codret inner join empagedadosretmovocorrencia on e02_empagedadosret = e76_codret and e02_empagedadosretmov = e76_codmov and e02_errobanco in (2, 269) where corlanc.k12_data = ridata and corlanc.k12_id = ricaixa and corlanc.k12_autent = riautent union select e75_codgera from conlancamcorgrupocorrente inner join corgrupocorrente on k105_sequencial = c23_corgrupocorrente inner join corempagemov  on k12_id = k105_id and k12_data = k105_data and k12_autent = k105_autent inner join empagedadosretmov on e76_codmov = k12_codmov and e76_processado = true and e76_dataefet = k105_data inner join empagedadosret on e75_codret = e76_codret inner join empagedadosretmovocorrencia on e02_empagedadosret = e76_codret and e02_empagedadosretmov = e76_codmov and e02_errobanco in (2, 269) where corempagemov.k12_data = ridata and corempagemov.k12_id = ricaixa and corempagemov.k12_autent = riautent ) as arquivo, ";
$sqlAutentica .= "                           ridata         as data, ";
$sqlAutentica .= "                           rnvalordebito  as valor_debito, ";
$sqlAutentica .= "                           rivalorcredito as valor_credito, ";
$sqlAutentica .= "                           rireceita      as receita, ";
$sqlAutentica .= "                           richeque       as cheque, ";
$sqlAutentica .= "                           rtcredor       as credor, ";
$sqlAutentica .= "                           rtdetalhe      as detalhe, ";
$sqlAutentica .= "                           ''             as justificativa, ";
$sqlAutentica .= "                           'conciliado'   as classe, ";
$sqlAutentica .= "                           k83_sequencial as itemconciliacao, ";
$sqlAutentica .= "                           rberro         as erro ";
$sqlAutentica .= "                      from conciliacor ";
$sqlAutentica .= "                           inner join conciliaitem on k83_sequencial = k84_conciliaitem ";
$sqlAutentica .= "                           inner join concilia     on k83_concilia   = k68_sequencial ";
$sqlAutentica .= "                           inner join fc_extratocaixa(".db_getsession('DB_instit').",$conta,'".$data_ultima_proc."'::date + 1,'".$data."',false ) ";
$sqlAutentica .= "                                                   on k84_id     = ricaixa ";
$sqlAutentica .= "                                                  and k84_autent = riautent ";
$sqlAutentica .= "                                                  and k84_data   = ridata ";
$sqlAutentica .= "                     where k68_sequencial = ".$clconcilia->k68_sequencial;

$sqlAutentica .= "                     union all ";





// registros normais
$sqlAutentica .= "                    select distinct ";
$sqlAutentica .= "                           ricaixa        as caixa, ";
$sqlAutentica .= "                           riautent       as autent, ";
$sqlAutentica .= "                           ( select e75_codgera from conlancamcorrente inner join corlanc on c86_id = k12_id and c86_data = k12_data and c86_autent = k12_autent inner join conlancamslip on c84_slip = k12_codigo and c84_conlancam = c86_conlancam inner join empageslip on e89_codigo = k12_codigo inner join empagedadosretmov on e76_codmov = e89_codmov and e76_processado = true and e76_dataefet = k12_data inner join empagedadosret on e75_codret = e76_codret inner join empagedadosretmovocorrencia on e02_empagedadosret = e76_codret and e02_empagedadosretmov = e76_codmov and e02_errobanco in (2, 269) where corlanc.k12_data = ridata and corlanc.k12_id = ricaixa and corlanc.k12_autent = riautent union select e75_codgera from conlancamcorgrupocorrente inner join corgrupocorrente on k105_sequencial = c23_corgrupocorrente inner join corempagemov  on k12_id = k105_id and k12_data = k105_data and k12_autent = k105_autent inner join empagedadosretmov on e76_codmov = k12_codmov and e76_processado = true and e76_dataefet = k105_data inner join empagedadosret on e75_codret = e76_codret inner join empagedadosretmovocorrencia on e02_empagedadosret = e76_codret and e02_empagedadosretmov = e76_codmov and e02_errobanco in (2, 269) where corempagemov.k12_data = ridata and corempagemov.k12_id = ricaixa and corempagemov.k12_autent = riautent ) as arquivo, ";
$sqlAutentica .= "	 		                     ridata         as data, ";
$sqlAutentica .= "  			                   rnvalordebito  as valor_debito, ";
$sqlAutentica .= "  			                   rivalorcredito as valor_credito, ";
$sqlAutentica .= "			                     rireceita      as receita, ";
$sqlAutentica .= "			                     richeque       as cheque, ";
$sqlAutentica .= "			                     rtcredor       as credor, ";
$sqlAutentica .= "			                     rtdetalhe      as detalhe, ";
$sqlAutentica .= "                           ''             as justificativa, ";
$sqlAutentica .= "                           'normal'       as classe, ";
$sqlAutentica .= "                           0              as itemconciliacao, ";
$sqlAutentica .= "			                     rberro         as erro ";
$sqlAutentica .= "                      from fc_extratocaixa(".db_getsession('DB_instit').",$conta,'".$data_ultima_proc."'::date + 1,'".$data."',false ) ";
$sqlAutentica .= "                           left join conciliacor          on ricaixa    = k84_id       ";
$sqlAutentica .= "                                                         and riautent   = k84_autent   ";
$sqlAutentica .= "                                                         and ridata     = k84_data     ";
$sqlAutentica .= "                           left join conciliaitem         on k83_sequencial = k84_conciliaitem ";
$sqlAutentica .= "                                                         and k83_concilia = (select k68_sequencial ";
$sqlAutentica .= "                                                                               from concilia  ";
$sqlAutentica .= "                                                                              where k68_contabancaria = {$conta} ";
$sqlAutentica .= "                                                                                and k68_data = '".$data."' ) ";
$sqlAutentica .= "                           left join conciliapendcorrente on k89_id     = ricaixa    ";
$sqlAutentica .= "                                                         and k89_autent = riautent   ";
$sqlAutentica .= "			                                                   and k89_data   = ridata     ";
$sqlAutentica .= "                                                         and k89_concilia = (select k68_sequencial ";
$sqlAutentica .= "                                                                               from concilia  ";
$sqlAutentica .= "                                                                              where k68_contabancaria = {$conta} ";
$sqlAutentica .= "                                                                                and k68_data = '".$data."' ) ";
//$sqlAutentica .= "	                                                       and conciliaitem.k83_concilia = conciliapendcorrente.k89_concilia ";
$sqlAutentica .= "                     where ( k89_id is null and k89_autent is null and k89_data is null ) ";
$sqlAutentica .= "                       and ( k83_sequencial is null ) ";

$sqlAutentica .= "                       and not exists (select 1  ";
$sqlAutentica .= "                                         from conciliacor ";
$sqlAutentica .= "                                         inner join conciliaitem  on k83_sequencial    = k84_conciliaitem ";
$sqlAutentica .= "                                         inner join concilia      on k68_sequencial    = k83_concilia ";
$sqlAutentica .= "                                                                and k68_contabancaria = {$conta} ";
$sqlAutentica .= "                                                                and k68_data          = '".$data."' ";
$sqlAutentica .= "                                                              where k84_id     = ricaixa ";
$sqlAutentica .= "                                                                and k84_autent = riautent ";
$sqlAutentica .= "                                                                and k84_data   = ridata ) ";

$sqlAutentica .= "                 ) as x ";
$sqlAutentica .= "                 left join extratolinha         on lpad(trim(x.cheque::varchar),20,'0') = lpad(trim(k86_documento),20,'0') ";
$sqlAutentica .= "                                               and k86_contabancaria = $conta ";
$sqlAutentica .= "                                               and (k86_data = x.data or k86_data <= '".$data."') ";
$sqlAutentica .= "                                               and x.cheque <> 0 ";
$sqlAutentica .= "                                               and k86_documento <> '0' ";
$sqlAutentica .= "        ) as x ";
$sqlAutentica .= " where not exists (select 1
                                       from corgrupocorrente
                                      where k105_autent = autent
                                        and k105_id     = caixa
                                        and k105_data   = data
                                        and k105_corgrupotipo in (2,3,5,6)
                                        and extract(year from k105_data) <= 2012 )  ";
$sqlAutentica .= "  group by caixa, autent, arquivo, data, cheque, credor, classe, itemconciliacao, erro, justificativa ";
$sqlAutentica .= "  order by data, autent";


	$rsCorrente = $clcorrente->sql_record($sqlAutentica);
	$intNumrows = $clcorrente->numrows;
	for($i = 0; $i < $intNumrows; $i++ ){
		db_fieldsmemory($rsCorrente,$i);
		$clconciliapendcorrente->k89_concilia       = $clconcilia->k68_sequencial;
		$clconciliapendcorrente->k89_id             = $caixa;
		$clconciliapendcorrente->k89_data           = $data;
		$clconciliapendcorrente->k89_autent         = $autent;
		$clconciliapendcorrente->k89_justificativa  = '';
		$clconciliapendcorrente->k89_conciliaorigem = 1;
		$clconciliapendcorrente->incluir(null);
		if($clconciliapendcorrente->erro_status == 0){
			$erromsg = $clconciliapendcorrente->erro_msg;
			$sqlerro = true;
			break;
		}
	}


	// busca lancamentos do extratolinha incluido como pendencias futuras
	//
	//
	$sqlPendExtrato  = " select k86_sequencial ";
	$sqlPendExtrato .= "	 from extratolinha ";
	$sqlPendExtrato .= "	where k86_data  = ( select k68_data ";
  $sqlPendExtrato .= "                        from concilia ";
  $sqlPendExtrato .= "                       where k68_data = '".$data."' ";
  $sqlPendExtrato .= "                         and k68_contabancaria = $conta ";
  $sqlPendExtrato .= "                       order by k68_data  ";
  $sqlPendExtrato .= "                        desc limit 1 ) ";
  $sqlPendExtrato .= "	  and k86_contabancaria = ".$conta ;
	$rsExtrato = $clextratolinha->sql_record($sqlPendExtrato);
	$intNumrowsextrato = $clextratolinha->numrows;
	for($i = 0; $i < $intNumrowsextrato; $i++ ){
		db_fieldsmemory($rsExtrato,$i);
		$clconciliapendextrato->k88_extratolinha   = $k86_sequencial;
		$clconciliapendextrato->k88_concilia       = $clconcilia->k68_sequencial;
		$clconciliapendextrato->k88_conciliaorigem = 1;
		$clconciliapendextrato->k88_justificativa  = '';
		$clconciliapendextrato->incluir(null);
		if($clconciliapendextrato->erro_status == 0){
			$erromsg = $clconciliapendextrato->erro_msg;
			$sqlerro = true;
			break;
		}
	}





	// mesma coisa que o for a cima porem com as pendencias de extrato
	$sqlPendExtrato  = " select conciliapendextrato.* ";
	$sqlPendExtrato .= "	 from concilia ";
	$sqlPendExtrato .= "		    inner join conciliapendextrato on k88_concilia = k68_sequencial ";
	$sqlPendExtrato .= "	where k68_data  = ( select k68_data ";
  $sqlPendExtrato .= "                        from concilia ";
  $sqlPendExtrato .= "                       where k68_data < '".$data."' ";
  $sqlPendExtrato .= "                         and k68_contabancaria = $conta ";
  $sqlPendExtrato .= "                       order by k68_data  ";
  $sqlPendExtrato .= "                        desc limit 1 ) ";
  $sqlPendExtrato .= "	  and k68_contabancaria = ".$conta ;
	$rsExtrato = $clextratolinha->sql_record($sqlPendExtrato);
	$intNumrowsextrato = $clextratolinha->numrows;
	for($i = 0; $i < $intNumrowsextrato; $i++ ){
		db_fieldsmemory($rsExtrato,$i);
		$clconciliapendextrato->k88_extratolinha   = $k88_extratolinha;
		$clconciliapendextrato->k88_concilia       = $clconcilia->k68_sequencial;
		$clconciliapendextrato->k88_conciliaorigem = 1;
		$clconciliapendextrato->k88_justificativa  = $k88_justificativa;
		$clconciliapendextrato->incluir(null);
		if($clconciliapendextrato->erro_status == 0){
			$erromsg = $clconciliapendextrato->erro_msg;
			$sqlerro = true;
			break;
		}
	}




	db_fim_transacao($sqlerro);

	if($sqlerro){
		echo "2|||".$erromsg;
	}else{
		echo "1|||Processamento concluido com sucesso.|||".$clconcilia->k68_sequencial;
	}

?>
