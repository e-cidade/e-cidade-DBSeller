<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2014  DBSeller Servicos de Informatica
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
use ECidade\Pdf\Pdf;

require_once(modification("libs/db_sql.php"));
require_once(modification('libs/db_stdlib.php'));
require_once(modification('libs/db_utils.php'));
require_once(modification('libs/db_conecta.php'));

db_postmemory($_GET);

$clconcilia             = new cl_concilia();
$clextrato              = new cl_extrato();
$clconciliapendcorrente = new cl_conciliapendcorrente();
$clconciliapendextrato  = new cl_conciliapendextrato();
$clsaltes               = new cl_saltes();
$clcontabancaria        = new cl_contabancaria();

$processa_geral = false;

$menorData = ConciliacaoBancaria::getMenorDataConciliacao();
$dataFim = DBDate::converter($datausuario);
$instituicao = db_getsession('DB_instit');
$iAnoUsu = db_getsession('DB_anousu');
$oInstituicao = new Instituicao($instituicao);

/**
 * Quando o relatório for uma reemissão, busca o código sequencial da tabela concilia.
 */
if (isset($lReemissao) && $lReemissao) {

  if( $iConta != ""){


    $sSqlCodConcilia  = "select k68_sequencial ";
    $sSqlCodConcilia .= "  from concilia";
    $sSqlCodConcilia .= " where k68_contabancaria = {$iConta} ";
    $sSqlCodConcilia .= "   and k68_data <= '{$sDataConciliacao}' ";
    $sSqlCodConcilia .= " order by k68_data desc limit 1 ";

    $rsCodigoConcilia = db_query($sSqlCodConcilia);
    if (pg_num_rows($rsCodigoConcilia) == 0){
        db_redireciona("db_erros.php?fechar=true&db_erro=Conta ($iConta) sem conciliação.");
    }

  }else{

          $sSqlCodConcilia  = "select k68_sequencial,k68_contabancaria as iConta  ";
          $sSqlCodConcilia .= "from concilia ";
          $sSqlCodConcilia .= " where  k68_contabancaria::varchar||k68_data in ";
          $sSqlCodConcilia .= "( select k68_contabancaria::varchar||max(k68_data) ";
          $sSqlCodConcilia .= "  from concilia ";
          $sSqlCodConcilia .= "  where k68_data <= '{$sDataConciliacao}' ";
          $sSqlCodConcilia .= "  group by k68_contabancaria )";

          $rsCodigoConcilia = db_query($sSqlCodConcilia);
          if (pg_num_rows($rsCodigoConcilia) == 0){
             db_redireciona('db_erros.php?fechar=true&db_erro=Não Existem contas concilicadas');
          }

          $processa_geral = true;
          $contas_nao_conciliadas = array();
  }


}else{

        $sqlDadosConcilia  = " select  k68_sequencial, k68_contabancaria as iConta from concilia ";
        $sqlDadosConcilia .= "        inner join conciliastatus on k68_conciliastatus = k95_sequencial";
        $sqlDadosConcilia .= "  where k68_sequencial = $concilia ";
        $rsCodigoConcilia  = $clconcilia->sql_record($sqlDadosConcilia);

        if( isset($oDadosConcilia->k68_data ) ){
          $sDataConciliacao = $oDadosConcilia->k68_data;
        }
}

$banco='';
$saldoextrato=0;
$lContaUnica = false;

$pdf = new Pdf();
$pdf->init(false);
$pdf->aliasNbPages();
$pdf->setFillColor(235);

$alt   = 4;
$fonte = 'arial';

$pdf->setFont($fonte,'b',8);


for ( $lista = 0 ; $lista < pg_num_rows($rsCodigoConcilia); $lista ++ ) {


    $aQuadroJustificativas = array();
    
	$concilia = db_utils::fieldsMemory($rsCodigoConcilia, $lista)->k68_sequencial;
	if( $processa_geral == true ){
	  $iConta = db_utils::fieldsMemory($rsCodigoConcilia, $lista)->iconta;
	}

	/*
	 * Verificamos se trata-se de uma conta unica
	 * Sendo conta Unica nao sera validada a instituicao, retornando os dados consolidados
	 */
	$rsContaUnica = $clcontabancaria->sql_record($clcontabancaria->sql_query_file($iConta, "db83_contaunica"));
	$lContaUnica = (db_utils::fieldsMemory($rsContaUnica,0)->db83_contaunica == "f"?false:true);

	$sqlDadosConcilia  = " select * from concilia ";
	$sqlDadosConcilia .= "        inner join conciliastatus on k68_conciliastatus = k95_sequencial";
	$sqlDadosConcilia .= "  where k68_sequencial = $concilia ";
	$rsDadosConcilia   = $clconcilia->sql_record($sqlDadosConcilia);

	if($clconcilia->numrows > 0){
	  $oDadosConcilia = db_utils::fieldsMemory($rsDadosConcilia,0);
	}
	///// verifica se tem lancammentos entre as datas para nao emitir
	// concilicao com datas pendentes no intervalo

		$sqlConta    = " select distinct k12_data from ( ";
		$sqlConta   .= " select distinct k12_data";
		$sqlConta   .= "   from contabancaria ";
		$sqlConta   .= "        inner join conplanocontabancaria on conplanocontabancaria.c56_contabancaria = contabancaria.db83_sequencial ";
		$sqlConta   .= "                                        and conplanocontabancaria.c56_anousu = $iAnoUsu";
		$sqlConta   .= "        inner join conplanoreduz on c61_codcon = c56_codcon  ";
		$sqlConta   .= "                                and c61_anousu = $iAnoUsu";
		if (!$lContaUnica) {
		    $sqlConta   .= "                            and c61_instit = $instituicao";
		}
		$sqlConta   .= "        inner join conplano on c61_codcon = c60_codcon and c61_anousu = c60_anousu ";
		$sqlConta   .= "        left  join corrente on k12_conta       = c61_reduz ";
		$sqlConta   .= "        left  join concilia on db83_sequencial = k68_contabancaria ";
		$sqlConta   .= "  where k68_contabancaria  =  {$iConta} ";
		$sqlConta   .= "    and k12_data between '{$oDadosConcilia->k68_data}'::date+1 and '{$sDataConciliacao}'";

		$sqlConta   .= " union ";

		$sqlConta   .= " select distinct k12_data ";
		$sqlConta   .= "   from contabancaria ";
		$sqlConta   .= "        inner join conplanocontabancaria on conplanocontabancaria.c56_contabancaria = contabancaria.db83_sequencial ";
		$sqlConta   .= "                                        and conplanocontabancaria.c56_anousu = $iAnoUsu";
		$sqlConta   .= "        inner join conplanoreduz on c61_codcon = c56_codcon  ";
		$sqlConta   .= "                                and c61_anousu = $iAnoUsu ";
		if (!$lContaUnica) {
		    $sqlConta   .= "                            and c61_instit = $instituicao";
		}
		$sqlConta   .= "        inner join conplano on c61_codcon = c60_codcon and c61_anousu = c60_anousu ";
		$sqlConta   .= "        inner join corlanc on k12_conta       = c61_reduz ";
		$sqlConta   .= "        left  join concilia on db83_sequencial = k68_contabancaria ";
		$sqlConta   .= "  where k68_contabancaria = {$iConta}  ";
		$sqlConta   .= "    and k12_data between '{$oDadosConcilia->k68_data}'::date+1 and '{$sDataConciliacao}'";

		$sqlConta   .= " union ";

		$sqlConta   .= " select distinct k86_data  ";
		$sqlConta   .= "   from contabancaria ";
		$sqlConta   .= "        inner join conplanocontabancaria on conplanocontabancaria.c56_contabancaria = contabancaria.db83_sequencial ";
		$sqlConta   .= "                                        and conplanocontabancaria.c56_anousu = $iAnoUsu ";
		$sqlConta   .= "        inner join conplanoreduz on c61_codcon = c56_codcon  ";
		$sqlConta   .= "                                and c61_anousu = $iAnoUsu ";
		if (!$lContaUnica) {
		    $sqlConta   .= "                            and c61_instit = $instituicao";
		}
		$sqlConta   .= "        inner join conplano on c61_codcon = c60_codcon and c61_anousu = c60_anousu ";
		$sqlConta   .= "        inner join extratolinha on k86_contabancaria = c56_contabancaria  ";
		$sqlConta   .= "        left  join concilia on db83_sequencial = k68_contabancaria ";
		$sqlConta   .= "  where k68_contabancaria = {$iConta}  ";
		$sqlConta   .= "    and k86_data between '{$oDadosConcilia->k68_data}'::date+1 and '{$sDataConciliacao}'";
		$sqlConta   .= "  ) as x order by k12_data";


		$rsContas    = $clsaltes->sql_record($sqlConta);
		$numrows     = $clsaltes->numrows;
		$d="";
		for($i=0;$i<$numrows;$i++){
		   $d .= db_formatar(pg_result($rsContas,$i,0),'d');
		   if( $i+1 <= $numrows ) {
			 $d .= " - ";
		   }
		}
		if( $numrows > 0 ){
  		   if( $processa_geral ) {
	              $contas_nao_conciliadas[$iConta] = $d  ;
		      continue;
		   }else{
		      db_redireciona('db_erros.php?fechar=true&db_erro=Existem lancamentos não conciliados nas datas: '.$d);
		   }
		}

	if ( substr($oDadosConcilia->k68_data,0,4) <= 2012 && false) {

	  $sqlTotalExtrato  = " select k97_saldofinal as  saldoextrato         ";
	  $sqlTotalExtrato .= "   from extratosaldo                            ";
	  $sqlTotalExtrato .= "  where k97_contabancaria  = $oDadosConcilia->k68_contabancaria ";
	  $sqlTotalExtrato .= "    and k97_dtsaldofinal  <= '{$oDadosConcilia->k68_data}'      ";
	  $sqlTotalExtrato .= "  order by k97_dtsaldofinal desc limit 1        ";

	} else {

	  $sqlData  = " select k97_dtsaldofinal ";
	  $sqlData .= " from extratosaldo a ";
	  $sqlData .= " where a.k97_contabancaria = $oDadosConcilia->k68_contabancaria and  '{$oDadosConcilia->k68_data}' >= a.k97_dtsaldofinal ";
	  $sqlData .= " order by a.k97_dtsaldofinal desc, a.k97_extrato desc limit 1";

	  $sqlTotalExtrato  = " select case when k97_situacao = 'D' then k97_saldofinal*-1 else k97_saldofinal end as saldoextrato         ";
	  $sqlTotalExtrato .= " from extratosaldo                             ";
	  $sqlTotalExtrato .= " where k97_contabancaria  = $oDadosConcilia->k68_contabancaria ";
	  $sqlTotalExtrato .= " and k97_dtsaldofinal   = ( $sqlData )         ";
	  $sqlTotalExtrato .= " order by k97_dtsaldofinal desc, k97_extrato desc limit 1 ";

	}
	$rsTotalExtrato   = $clextrato->sql_record($sqlTotalExtrato);

	if ($clextrato->numrows > 0) {
	    $saldoextrato = db_utils::fieldsMemory($rsTotalExtrato,0)->saldoextrato;
	}

	$sqlDadosConcilia = " select count(*) as quantidade from concilia where k68_contabancaria = (select k68_contabancaria from concilia where k68_sequencial = $concilia)";
	$rsDadosConcilia  = $clconcilia->sql_record($sqlDadosConcilia);

	if ($clconcilia->numrows > 0) {

	  $quantidade = db_utils::fieldsMemory($rsDadosConcilia,0)->quantidade;
	  if ($quantidade > 2 && $oDadosConcilia->k95_fechada == 'f' ) {
	    if( $processa_geral){
		    continue;
            }else{
		    db_redireciona('db_erros.php?fechar=true&db_erro=Salve a conciliacao antes de emitir o relatorio.');
	    }
	  }
	}

	$sqlDadosConta  = " select distinct ";
	$sqlDadosConta .= "        db90_codban     as banco, ";
	$sqlDadosConta .= "        db83_sequencial as reduzido,";
	$sqlDadosConta .= "        db83_descricao  as descricao,";
	$sqlDadosConta .= "        db83_conta||'-'||db83_dvconta as conta,";
	$sqlDadosConta .= "        db89_codagencia||'-'||db89_digito as agencia,";
	$sqlDadosConta .= "        ( select array_to_string( array_accum(distinct c61_reduz),', ')
                                   from conplanocontabancaria
                                   inner join conplanoreduz on c61_anousu = c56_anousu
                                                           and c61_reduz = c56_reduz
                                                           and c61_codcon = c56_codcon
                                   where c56_contabancaria = {$oDadosConcilia->k68_contabancaria}
                                     and c56_anousu = $iAnoUsu ) as reduzido_contabil ";
	$sqlDadosConta .= "   from contabancaria ";
	$sqlDadosConta .= "        inner join bancoagencia on bancoagencia.db89_sequencial = contabancaria.db83_bancoagencia ";
	$sqlDadosConta .= "        inner join db_bancos    on db_bancos.db90_codban        = bancoagencia.db89_db_bancos ";
	$sqlDadosConta .= "  where contabancaria.db83_sequencial = {$oDadosConcilia->k68_contabancaria} ";
	$rsDadosConta   = db_query($sqlDadosConta);
	if($rsDadosConta && pg_num_rows($rsDadosConta) > 0){
	  $oDadosConta = db_utils::fieldsMemory($rsDadosConta,0);
	}
	
	$nTotContaCaixa = 0;
	
	$sSqlReduz  = " select distinct c61_reduz, c61_instit ";
	$sSqlReduz .= "   from contabancaria ";
	$sSqlReduz .= "        inner join conplanocontabancaria on conplanocontabancaria.c56_contabancaria = contabancaria.db83_sequencial
                                                           and conplanocontabancaria.c56_anousu = $iAnoUsu";
	$sSqlReduz .= "        inner join conplanoreduz         on conplanoreduz.c61_codcon = conplanocontabancaria.c56_codcon ";
	$sSqlReduz .= "                                        and conplanoreduz.c61_anousu = conplanocontabancaria.c56_anousu ";
	$sSqlReduz .= "                                        and conplanoreduz.c61_anousu = $iAnoUsu";
	if (!$lContaUnica) {
	    $sSqlReduz .= "                                    and conplanoreduz.c61_instit = $instituicao";
	}
	$sSqlReduz .= "  where contabancaria.db83_sequencial = {$oDadosConcilia->k68_contabancaria} ";
	$rsReduz    = db_query($sSqlReduz);
	
	if( $rsReduz && pg_num_rows($rsReduz) > 0 ) {
	    
	    for ($i = 0; $i <  pg_num_rows($rsReduz); $i++) {
	        
	        $oDadosReduz = db_utils::fieldsmemory($rsReduz,$i);
	        
	        $sqlSaldoContaCaixa = "select substr(fc_saltessaldo({$oDadosReduz->c61_reduz},'{$oDadosConcilia->k68_data}','{$oDadosConcilia->k68_data}',null,{$oDadosReduz->c61_instit}),41,13)::float as saldocontacaixa";
	        $rsSaldoContaCaixa  = $clsaltes->sql_record($sqlSaldoContaCaixa);
	        
	        if ($clsaltes->numrows > 0) {
	            $nTotContaCaixa   += db_utils::fieldsmemory($rsSaldoContaCaixa,0)->saldocontacaixa;
	        }
	    }
	}
	
	
	$sqlPendenciasExtrato  = "  select k86_data, ";
	$sqlPendenciasExtrato .= "         k86_documento, ";
	$sqlPendenciasExtrato .= "         k86_observacao as k86_historico, ";
	$sqlPendenciasExtrato .= "         k86_valor, ";
	$sqlPendenciasExtrato .= "         case "; 
    $sqlPendenciasExtrato .= "           when k86_bancohistmov = 1 ";
    $sqlPendenciasExtrato .= "             then ";
    $sqlPendenciasExtrato .= "               case "; 
    $sqlPendenciasExtrato .= "                 when k86_tipo = 'D' then 'C' ";
    $sqlPendenciasExtrato .= "                 when k86_tipo = 'C' then 'D' ";
    $sqlPendenciasExtrato .= "                 else k86_tipo ";
    $sqlPendenciasExtrato .= "               end ";
    $sqlPendenciasExtrato .= "           else k86_tipo ";  
    $sqlPendenciasExtrato .= "         end as k86_tipo, ";
	$sqlPendenciasExtrato .= "         case ";
	$sqlPendenciasExtrato .= "           when k85_tipoinclusao = 2 then 'outros'";
	$sqlPendenciasExtrato .= "           when k86_tipo = 'D' and k86_bancohistmov = 1 then 'credito' ";
	$sqlPendenciasExtrato .= "           when k86_tipo = 'D' and k86_bancohistmov <> 1 then 'debito' ";
	$sqlPendenciasExtrato .= "           when k86_tipo = 'C' and k86_bancohistmov = 1 then 'debito' ";
	$sqlPendenciasExtrato .= "           when k86_tipo = 'C' and k86_bancohistmov <> 1 then 'credito' ";
	$sqlPendenciasExtrato .= "         end as tipo, ";
	$sqlPendenciasExtrato .= "         k86_bancohistmov ";
	$sqlPendenciasExtrato .= "    from conciliapendextrato ";
	$sqlPendenciasExtrato .= "         inner join extratolinha on k86_sequencial = k88_extratolinha ";
	$sqlPendenciasExtrato .= "         inner join extrato      on k85_sequencial = k86_extrato ";
	$sqlPendenciasExtrato .= "   where k88_concilia = $concilia ";
	$sqlPendenciasExtrato .= "   order by tipo,k86_data ";
	
	$rsConciliaExtrato           = $clconciliapendextrato->sql_record($sqlPendenciasExtrato);
	$intNumRowsPendenciaExtrato  = $clconciliapendextrato->numrows;
	
	/*
	 * Precorre os registros apenas para somar os valores de debitos e credito
	 */
	$vlrTotalDebitosPendenciaExtrato  = 0;
	$vlrTotalCreditosPendenciaExtrato = 0;
	
	for ($i = 0; $i < $intNumRowsPendenciaExtrato ;$i++) {
	    $oDados = db_utils::fieldsMemory($rsConciliaExtrato,$i);
	    if($oDados->k86_tipo == 'C'){
	        $valor = abs($oDados->k86_valor);
	        $vlrTotalCreditosPendenciaExtrato += $valor;
	    } else {
	        $valor = ($oDados->k86_valor * -1);
	        $vlrTotalDebitosPendenciaExtrato += $valor;
	    }
	}
	
	
	/*
	 * Pendencias do caixa
	 */
	$sqlPendenciascaixa  = "  select max(case                                     ";
	$sqlPendenciascaixa .= "           when richeque       is not null            "; // O relatório considere como cheque pendente
	$sqlPendenciascaixa .= "            and richeque <> 0                          "; // somente quando o valor estiver a CRÉDITO na tesouraria.
	$sqlPendenciascaixa .= "            and rivalorcredito <> 0                    "; //
	$sqlPendenciascaixa .= "           then 'cheque'                              "; //
	$sqlPendenciascaixa .= "           when rnvalordebito  is not null            "; // Caso o cheque não esteja a CRÉDITO na tesouraria
	$sqlPendenciascaixa .= "            and rnvalordebito <> 0                     "; // O registro é considerado como:
	$sqlPendenciascaixa .= "             or richeque is not null                  "; // (-) Pendencias contabilizadas a debito
	$sqlPendenciascaixa .= "            and richeque <> 0                          "; //
	$sqlPendenciascaixa .= "            and rnvalordebito <> 0                     "; //
	$sqlPendenciascaixa .= "           then 'debito'                              "; //
	$sqlPendenciascaixa .= "           when rivalorcredito is not null            "; //
	$sqlPendenciascaixa .= "            and rivalorcredito <> 0                    "; //
	$sqlPendenciascaixa .= "           then 'credito'                             "; //
	$sqlPendenciascaixa .= "         end) as tipo,                                 ";
	$sqlPendenciascaixa .= "         ricaixa,                                     ";
	$sqlPendenciascaixa .= "         riautent,                                    ";
	$sqlPendenciascaixa .= "         ridata,                                      ";
	$sqlPendenciascaixa .= "         (select e60_codemp||'/'||e60_anousu
					                    from empempenho
					                    where e60_numemp = riempenho ) as riempenho, ";
	$sqlPendenciascaixa .= "         riordem,                                     ";
	$sqlPendenciascaixa .= "         riplanilha,                                  ";
	$sqlPendenciascaixa .= "         rislip,                                      ";
	$sqlPendenciascaixa .= "         richeque as cheque,                          ";
	$sqlPendenciascaixa .= "         max(case                                     ";
	$sqlPendenciascaixa .= "           when rnvalordebito  is not null            ";
	$sqlPendenciascaixa .= "            and rnvalordebito <> 0                    ";
	$sqlPendenciascaixa .= "           then 'D'                                   ";
	$sqlPendenciascaixa .= "           else 'C'                                   ";
	$sqlPendenciascaixa .= "         end) as tipomov,                             ";
	$sqlPendenciascaixa .= "         sum(case                                     ";
	$sqlPendenciascaixa .= "           when rnvalordebito  is not null            ";
	$sqlPendenciascaixa .= "            and rnvalordebito <> 0                    ";
	$sqlPendenciascaixa .= "           then rnvalordebito                         ";
	$sqlPendenciascaixa .= "           else rivalorcredito                        ";
	$sqlPendenciascaixa .= "         end) as valor,                               ";
	$sqlPendenciascaixa .= "        k89_justificativa,                            ";
	$sqlPendenciascaixa .= "        rtdetalhe as detalhe                          ";
	$sqlPendenciascaixa .= "    from conciliapendcorrente                         ";
	$sqlPendenciascaixa .= "         inner join fc_extratocaixa($instituicao , $oDadosConcilia->k68_contabancaria, '$menorData', '$dataFim', false ) on ricaixa  = k89_id ";
	$sqlPendenciascaixa .= "                        and riautent = k89_autent ";
	$sqlPendenciascaixa .= "                        and ridata   = k89_data ";
	
	$sqlPendenciascaixa .= "WHERE k89_concilia = $concilia
                              AND NOT EXISTS (   SELECT 1
                                                   FROM corgrupocorrente
                                                  WHERE k105_autent = k89_autent
                                                    AND k105_id = k89_id
                                                    AND k105_data = k89_data
                                                    AND ( ( k105_corgrupotipo in (2, 3, 5, 6)
                                                             AND extract(YEAR FROM k105_data) <= 2012)
                                                            OR (k105_corgrupotipo in (2, 3) )
                                                         )
                                                   ) ";
	$sqlPendenciascaixa .= "    group by ricaixa,
                                         riautent,
                                         ridata,
                                         riempenho,
                                         riordem,
                                         riplanilha,
					                     rislip,
                                         richeque,
                                         k89_justificativa,
                                         rtdetalhe";
	$sqlPendenciascaixa .= "   order by tipo,
                                        ridata,
                                        ricaixa,
                                        riautent ";
	
	$rsConciliaCorrente  = $clconciliapendcorrente->sql_record($sqlPendenciascaixa);
	$intNumRowsPedenciaCaixa          = $clconciliapendcorrente->numrows;
	
	/*
	 * Precorre os registros apenas para somar os valores de debitos e credito
	 */
	$vlrTotalDebitosPendenciaCaixa  = 0;
	$vlrTotalCreditosPendenciaCaixa = 0;
	
	for ($i = 0; $i < $intNumRowsPedenciaCaixa ;$i++) {
	    $oDados = db_utils::fieldsMemory($rsConciliaCorrente,$i);
	    if ($oDados->tipo == 'debito') {
	        $valor = ($oDados->valor * -1);
	        $vlrTotalDebitosPendenciaCaixa += $valor;
	    } else {
	        $valor = abs($oDados->valor);
	        $vlrTotalCreditosPendenciaCaixa += $valor;
	    }
	}
	

	$pdf->addTitulo($oInstituicao->getDescricao(),1);
	$pdf->addTitulo("DEMONSTRATIVO DA CONCILIAÇÃO BANCÁRIA",2);
	$pdf->addTitulo("PERÍODO ATÉ : ".db_formatar($oDadosConcilia->k68_data,'d'),3);
	$data = $oDadosConcilia->k68_data;
	if (isset($lReemissao) && $lReemissao) {

	  if (isset($datausuario) && $datausuario != "") {
	      $pdf->addTitulo("PERÍODO ATÉ : {$datausuario}",3);
	    $data = $datausuario;
	  }
	  if( $oDadosConcilia->k68_data != $datausuario ){
	      $pdf->addTitulo("ÚLTIMA CONCILICAO : ".db_formatar($oDadosConcilia->k68_data,'d'),4);
	  }
	}
	
	$troca                  = 1;
	$total                  = 0;
	$totalPendenciasCaixa   = 0;
	$totalPendenciasExtrato = 0;
	
	$pdf->addPage();

	// dados da conta bancaria
	$pdf->cell(189,$alt,"DADOS DA CONTA BANCÁRIA ",0,1,"L",0);
	$pdf->ln(1);

	$pdf->setFont($fonte,'b',8);
	$pdf->cell(25,$alt,"BANCO : ",0,0,"L",0);
	$pdf->setFont($fonte,'',8);
	$pdf->cell(35,$alt,$oDadosConta->banco,0,0,"L",0);
	$pdf->setFont($fonte,'b',8);
	$pdf->cell(25,$alt,"SEQ. CONTA : ",0,0,"L",0);
	$pdf->setFont($fonte,'',8);
	$pdf->cell(75,$alt,(isset($oDadosConta->reduzido) ? $oDadosConta->reduzido : null),0,1,"L",0);

	$pdf->setFont($fonte,'b',8);
	$pdf->cell(25,$alt,"AGÊNCIA : ",0,0,"L",0);
	$pdf->setFont($fonte,'',8);
	$pdf->cell(35,$alt,(isset($oDadosConta->agencia) ? $oDadosConta->agencia : null),0,0,"L",0);
	$pdf->setFont($fonte,'b',8);
	$pdf->cell(40,$alt,"REDUZIDO CONTABIL: ",0,0,"L",0);
	$pdf->setFont($fonte,'',8);
	$pdf->multicell(60,$alt,(isset($oDadosConta->reduzido_contabil) ? $oDadosConta->reduzido_contabil : null),0,1,"L",0);

	$pdf->setFont($fonte,'b',8);
	$pdf->cell(25,$alt,"CONTA : ",0,0,"L",0);
	$pdf->setFont($fonte,'',8);
	$pdf->cell(35,$alt,(isset($oDadosConta->conta) ? $oDadosConta->conta : null),0,0,"L",0);
	$pdf->setFont($fonte,'b',8);
	$pdf->cell(25,$alt,"DESCRIÇÃO : ",0,0,"L",0);
	$pdf->setFont($fonte,'',8);
	$pdf->cell(75,$alt,(isset($oDadosConta->descricao) ? $oDadosConta->descricao : null),0,1,"L",0);

	$pdf->ln(2);
	$pdf->setFont($fonte,'b',8);
	$pdf->cell(94,$alt,"SALDO BANCÁRIO ",'BT',0,"C",1);
	$pdf->cell(94,$alt,"SALDO DA CONTABILIDADE (E-CIDADE)",'BT',1,"C",1);
	$pdf->setFont($fonte,'',8);
	
	$pdf->cell(50,$alt,"Saldo Final do Extrato: ",0,0,"L",0);
	$pdf->cell(44,$alt,db_formatar($saldoextrato,'f'),"R",0,"R",0);
	
	$pdf->cell(50,$alt,"Saldo Final na Tesouraria: ",0,0,"L",0);
	$pdf->cell(44,$alt,db_formatar($nTotContaCaixa,'f'),0,1,"R",0);
	
	/*
	 * Por solicitacao do cliente as informacoes de Extrato e Caixa foram invertidas
	 */ 
	$pdf->cell(50,$alt,"Créditos Não Lançados pelo Banco: ",0,0,"L",0);
	//$pdf->cell(44,$alt,db_formatar($vlrTotalCreditosPendenciaExtrato,'f'),"R",0,"R",0);
	$pdf->cell(44,$alt,db_formatar((abs($vlrTotalCreditosPendenciaCaixa)*-1),'f'),"R",0,"R",0);
	
	$pdf->cell(50,$alt,"Créditos Não Contabilizados: ",0,0,"L",0);
	//$pdf->cell(44,$alt,db_formatar($vlrTotalCreditosPendenciaCaixa,'f'),"",1,"R",0);
	$pdf->cell(44,$alt,db_formatar((abs($vlrTotalCreditosPendenciaExtrato)*-1),'f'),"",1,"R",0);
	
	$pdf->cell(50,$alt,"Débitos Não Lançados pelo Banco: ",0,0,"L",0);
	//$pdf->cell(44,$alt,db_formatar($vlrTotalDebitosPendenciaExtrato,'f'),"R",0,"R",0);
	$pdf->cell(44,$alt,db_formatar(abs($vlrTotalDebitosPendenciaCaixa),'f'),"R",0,"R",0);

	$pdf->cell(50,$alt,"Débitos Não Contabilizados: ",0,0,"L",0);
	//$pdf->cell(44,$alt,db_formatar($vlrTotalCreditosPendenciaCaixa,'f'),"",1,"R",0);
    $pdf->cell(44,$alt,db_formatar(abs($vlrTotalDebitosPendenciaExtrato),'f'),"",1,"R",0);

	$pdf->setFont($fonte,'b',8);
	$pdf->cell(50,$alt,"Saldo Bancário Conciliado: ",0,0,"L",0);
	//$vlrTotalExtrato = abs($saldoextrato)-abs($vlrTotalCreditosPendenciaExtrato)+abs($vlrTotalDebitosPendenciaExtrato);
	$vlrTotalExtrato = abs($saldoextrato)-abs($vlrTotalCreditosPendenciaCaixa)+abs($vlrTotalDebitosPendenciaCaixa);
	$pdf->cell(44,$alt,db_formatar($vlrTotalExtrato, "f"),"R",0,"R",0);
	
	$pdf->cell(50,$alt,"Saldo Contabil Conciliado: ",0,0,"L",0);
	//$vlrTotalCaixa = $nTotContaCaixa+$vlrTotalCreditosPendenciaCaixa-abs($vlrTotalDebitosPendenciaCaixa);
	$vlrTotalCaixa = $nTotContaCaixa-abs($vlrTotalCreditosPendenciaExtrato)+abs($vlrTotalDebitosPendenciaExtrato);
	$pdf->cell(44,$alt,db_formatar($vlrTotalCaixa, "f"),"",0,"R",0);
	
	$pdf->ln(10);
	$pdf->setFont($fonte,'b',8);
	$pdf->cell(94,$alt,"Créditos Não Contabilizados",'BTR',0,"C",1);
	$pdf->cell(94,$alt,"Débitos Não Contabilizados",'BT',1,"C",1);
	
	$pdf->cell(46,$alt,"Documento",'BTR',0,"C",0);
	$pdf->cell(24,$alt,"Data",'BTR',0,"C",0);
	$pdf->cell(24,$alt,"Valor",'BTR',0,"C",0);
	
	$pdf->cell(46,$alt,"Documento",'BTR',0,"C",0);
	$pdf->cell(24,$alt,"Data",'BTR',0,"C",0);
	$pdf->cell(24,$alt,"Valor",'BT',1,"C",0);
	
	$iPosicaoYDebito  = $pdf->getY();
	$iPosicaoYCredito = $pdf->getY();
	
	/*
	 * Precorre os registros para mostrar as informações das pendencias do extrato
	 */
	for ($i = 0; $i < $intNumRowsPendenciaExtrato ;$i++) {
	    
	    $oDados = db_utils::fieldsMemory($rsConciliaExtrato,$i);
	    
	    if ($pdf->getY() > $pdf->getH() - 30 ) {
	        
	        $pdf->addpage();
	        $pdf->setFont($fonte,'b',8);
	        $pdf->cell(46,$alt,"Documento",'BTR',0,"C",0);
	        $pdf->cell(24,$alt,"Data",'BTR',0,"C",0);
	        $pdf->cell(24,$alt,"Valor",'BTR',0,"C",0);
	        
	        $pdf->cell(46,$alt,"Documento",'BTR',0,"C",0);
	        $pdf->cell(24,$alt,"Data",'BTR',0,"C",0);
	        $pdf->cell(24,$alt,"Valor",'BT',1,"C",0);
	        
	        $iPosicaoYDebito  = $pdf->getY();
	        $iPosicaoYCredito = $pdf->getY();
	        
	    }
	    
	    if($oDados->k86_tipo == 'C'){
	        $valor = abs($oDados->k86_valor);
	        //controle das colunas Credito/Debito
	        $pdf->SetXY(10,$iPosicaoYCredito);
	        //
	    } else {
	        $valor = ($oDados->k86_valor * -1);
	        //controle das colunas Credito/Debito
	        $pdf->SetXY(104,$iPosicaoYDebito);
	        //
	    }
	    
	    $pdf->setFont($fonte,'',7);
	    
	    $pdf->cell(46,$alt,$oDados->k86_documento             ,0,0,"C",0);
	    $pdf->cell(24,$alt,db_formatar($oDados->k86_data,'d') ,0,0,"C",0);
	    $pdf->cell(24,$alt,db_formatar($valor,'f')            ,"",1,"R",0);
	    
	     /**
	      * Desabilitado
	      * Imprime a justificativa
	      *
	     if (!empty($oDados->k86_historico) && (isset($justificativa) && $justificativa == 1)) {
	         $pdf->setX(10);
	         if ($oDados->tipo == 'debito') {
	             $pdf->setX(104);
	         }
	         $pdf->setFont($fonte,'',6);
	         $pdf->MultiCell(90, $alt, str_replace("\n", " ", $oDados->k86_historico));
	     }*/
	    if (!empty($oDados->k86_historico) && (isset($justificativa) && $justificativa == 1)) {
	      $oJustificativa = new stdClass();
	      $oJustificativa->data = db_formatar($oDados->k86_data,'d');
	      $oJustificativa->valor = db_formatar($valor,'f');
	      $oJustificativa->justificativa = $oDados->k86_historico;
	      $aQuadroJustificativas[] = $oJustificativa;
	    }
	     
	    //controle das colunas Credito/Debito
	    if ($oDados->k86_tipo == 'C') {
	        $iPosicaoYCredito = $pdf->getY();
	    } else {
	        $iPosicaoYDebito  = $pdf->getY();
	    }
	    
    }
    
    $pdf->setXY(10, ($iPosicaoYCredito>$iPosicaoYDebito?$iPosicaoYCredito:$iPosicaoYDebito));
    
    $pdf->setFont($fonte,'b',8);
	$pdf->cell(70,$alt,"Total",'BT',0,"R",0);
	$pdf->cell(24,$alt,db_formatar($vlrTotalCreditosPendenciaExtrato,'f'),'BTR',0,"R",0);
	
	$pdf->cell(70,$alt,"Total",'BT',0,"R",0);
	$pdf->cell(24,$alt,db_formatar($vlrTotalDebitosPendenciaExtrato,'f'),'BT',1,"R",0);
	$pdf->setFont($fonte,'',8);
	
	$pdf->ln(4);
	
	$pdf->setFont($fonte,'b',8);
	$pdf->cell(94,$alt,"Créditos Não Lançados pelo Banco",'BTR',0,"C",1);
	$pdf->cell(94,$alt,"Débitos Não Lançados pelo Banco",'BT',1,"C",1);
	
	$pdf->cell(24,$alt,"Documento",'BTR',0,"C",0);
	$pdf->cell(24,$alt,"Tipo",'BTR',0,"C",0);
	$pdf->cell(22,$alt,"Data",'BTR',0,"C",0);
	$pdf->cell(24,$alt,"Valor",'BTR',0,"C",0);
	
	$pdf->cell(24,$alt,"Documento",'BTR',0,"C",0);
	$pdf->cell(24,$alt,"Tipo",'BTR',0,"C",0);
	$pdf->cell(22,$alt,"Data",'BTR',0,"C",0);
	$pdf->cell(24,$alt,"Valor",'BT',1,"C",0);
	
	$iPosicaoYDebito  = $pdf->getY();
	$iPosicaoYCredito = $pdf->getY();
	
	/*
	 * Precorre os registros para mostrar as informações das pendencias do caixa
	 */
	for ($i = 0; $i < $intNumRowsPedenciaCaixa; $i++) {
	    
	    $oDados = db_utils::fieldsMemory($rsConciliaCorrente,$i);
	    
	    $documento = "";
	    $tipo_documento = "";
	    if (!empty($oDados->riordem)) {
	        $documento = $oDados->riordem;
	        $tipo_documento = "OP";
	    } else if (!empty($oDados->riplanilha)) {
	        $documento = $oDados->riplanilha;
	        $tipo_documento = "Planilha";
	    } else if (!empty($oDados->rislip)) {
	        $documento = $oDados->rislip;
	        $tipo_documento = "Slip";
	    }
	    
	    if ($pdf->getY() > $pdf->getH() - 30 ) {
	        
	        $pdf->addpage();
	        $pdf->setFont($fonte,'b',8);
	        $pdf->cell(24,$alt,"Documento",'BTR',0,"C",0);
	        $pdf->cell(24,$alt,"Tipo",'BTR',0,"C",0);
	        $pdf->cell(22,$alt,"Data",'BTR',0,"C",0);
	        $pdf->cell(24,$alt,"Valor",'BTR',0,"C",0);
	        
	        $pdf->cell(24,$alt,"Documento",'BTR',0,"C",0);
	        $pdf->cell(24,$alt,"Tipo",'BTR',0,"C",0);
	        $pdf->cell(22,$alt,"Data",'BTR',0,"C",0);
	        $pdf->cell(24,$alt,"Valor",'BT',1,"C",0);
	        
	        $iPosicaoYDebito  = $pdf->getY();
	        $iPosicaoYCredito = $pdf->getY();
	        
	    }
	    
	    if ($oDados->tipo == 'debito') {
	        $valor = ($oDados->valor * -1);
	        //controle das colunas Credito/Debito
	        $pdf->SetXY(104,$iPosicaoYDebito);
	        //
	    } else {
	        $valor = abs($oDados->valor);
	        //controle das colunas Credito/Debito
	        $pdf->SetXY(10,$iPosicaoYCredito);
	        //
	    }
	    
	    $pdf->setFont($fonte,'',7);
	    
	    $pdf->cell(24,$alt,$documento                       ,"",0,"C",0);
	    $pdf->cell(24,$alt,$tipo_documento                  ,0,0,"C",0);
	    $pdf->cell(22,$alt,db_formatar($oDados->ridata,'d') ,0,0,"C",0);
	    $pdf->cell(24,$alt,db_formatar($valor,'f')  ,"",1,"R",0);
	    
	    /**
	     * Desabilitado
	     * Imprime a justificativa
	     *
	    if (!empty($oDados->k89_justificativa) && (isset($justificativa) && $justificativa == 1)) {
	      $historico = " Justitifcativa: $oDados->k89_justificativa ";
	      $pdf->setFont($fonte,'',6);
	      $pdf->MultiCell(90, $alt, str_replace("\n", " ", $historico));
	    }
	    */
	    if (!empty($oDados->k89_justificativa) && (isset($justificativa) && $justificativa == 1)) {
	        $oJustificativa = new stdClass();
	        $oJustificativa->data = db_formatar($oDados->ridata,'d');
	        $oJustificativa->valor = db_formatar($valor,'f');
	        $oJustificativa->justificativa = $oDados->k89_justificativa;
	        $aQuadroJustificativas[] = $oJustificativa;
	    }
	    
	    //controle das colunas Credito/Debito
	    if ($oDados->tipo == 'debito') {
	        $iPosicaoYDebito  = $pdf->getY();
	    } else {
	        $iPosicaoYCredito = $pdf->getY();
	    }
	    
	}
	
	$pdf->setXY(10, ($iPosicaoYCredito > $iPosicaoYDebito?$iPosicaoYCredito:$iPosicaoYDebito));
	
	$pdf->setFont($fonte,'b',8);
	$pdf->cell(70,$alt,"Total",'BT',0,"R",0);
	$pdf->cell(24,$alt,db_formatar($vlrTotalCreditosPendenciaCaixa,'f'),'BTR',0,"R",0);
	
	$pdf->cell(70,$alt,"Total",'BT',0,"R",0);
	$pdf->cell(24,$alt,db_formatar($vlrTotalDebitosPendenciaCaixa,'f'),'BT',1,"R",0);
	$pdf->setFont($fonte,'',8);
	
	if (count($aQuadroJustificativas) > 0) {
	
	    $pdf->ln(4);
	        
	    $pdf->setFont($fonte,'b',8);
	    $pdf->cell(188,$alt,"Justificativa campo Não Contabilizados",1,1,"C",0);
	    $pdf->cell(22,$alt,"Data",1,0,"C",0);
	    $pdf->cell(24,$alt,"Valor",1,0,"C",0);
	    $pdf->cell(142,$alt,"Observação",1,1,"C",0);
	    $pdf->setFont($fonte,'',8);
	    foreach ($aQuadroJustificativas as $justificativa) {
	        
	        $iAlturaLinha = 4;
	        if (strlen(str_replace("\n", " ", $justificativa->justificativa)) > 92) { 
	          $iAlturaLinha = round((strlen(str_replace("\n", " ", $justificativa->justificativa))/70))*$alt;
	        }
	        $pdf->cell(22,$iAlturaLinha,$justificativa->data,1,0,"C",0);
	        $pdf->cell(24,$iAlturaLinha,$justificativa->valor,1,0,"R",0);
	        $pdf->MultiCell(142, $alt, str_replace("\n", " ", strtoupper($justificativa->justificativa)),1);
	    }
	}
	
}

$pdf->output();
