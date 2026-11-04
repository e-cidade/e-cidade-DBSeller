<?php
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2009  DBselller Servicos de Informatica             
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

require_once (modification("libs/db_stdlib.php"));
require_once (modification("libs/db_conecta.php"));
require_once (modification("libs/db_sessoes.php"));
require_once (modification("libs/db_sql.php"));
require_once(modification("libs/db_utils.php"));

parse_str($_SERVER['QUERY_STRING']);

$sWhere = " and (e60_vlrliq > e60_vlrpag)";

$head4 = "Orgão : TODOS";
if ($orgao != "") {
  $head4 = "Orgao: {$orgao}";	
  $sWhere .= " and o58_orgao = $orgao";
} 

$head5 = "Unidade : TODAS";
if ($unidade != "") {
  $head5 = "Unidade : {$unidade}";
  $sWhere .= " and o58_unidade = $unidade";
}

$head6 = "Recurso : TODOS";
if ($recurso != "") {
  $head6 = "Recurso : {$recurso}";	
  $sWhere .= " and o58_codigo = $recurso";
}

$head7 = "Anexo : TODOS";
if ($anexo != "") {
	$head7 = "Anexo : {$anexo}";
	if ($anexo == 5) {
		$sWhere .= " and e60_anousu < ".db_getsession("DB_anousu");
	} else {
		$sWhere .= " and o58_localizadorgastos = $anexo";
	}
}

$head9 = "";
if (isset($exercicio) && !empty($exercicio)) {
	$head9 = "Exercício: {$exercicio}";
	$sWhere .= " and e60_anousu = {$exercicio}"; 
}

$sHashOrgaoUnidadeCategoria = "";
$head1 = "RELATÓRIO DE LIQUIDAÇÕES APTAS AO REPASSE";
$head3 = "FILTROS:";

if ($formato == "PDF") {
  $oPdf = new PDF();
  $oPdf->addTitulo($head1,1);
  $oPdf->addTitulo($head3,3);
  $oPdf->addTitulo($head4,4);
  $oPdf->addTitulo($head5,5);
  $oPdf->addTitulo($head6,6);
  $oPdf->addTitulo($head7,7);
  $oPdf->addTitulo($head9,9);
  $oPdf->AliasNbPages();
  $oPdf->setfillcolor(235);
  $oPdf->init(false);
} else {
	$arquivoCSV = fopen("tmp/emp2_liquidacoesaptasrepassefinanceiro.csv", "w+");
}

$sSql = "select dados.*, 
                ac16_acordocategoria,
                ac50_descricao  
           from ( select distinct 
                         o58_orgao, 
         		         o58_unidade,
         		         o41_descr,
                         e50_codord, 
                         e60_codemp,
                         e60_anousu, 
                         e60_anousu,
                         e60_numemp,
                         o15_codigo,
                         o15_recurso,
                         o15_descr,
                         o56_elemento,
                         o56_descr, 
                         coalesce(ac54_acordo,ac45_acordo) as acordo, 
                         e71_codnota,
                         e70_valor,
         		         e70_vlrliq,
                         e70_vlranu, 
                         e53_vlrpag,
         		         cgm.z01_cgccpf,
                         cgm.z01_nome, 
                         ano, 
                         lpad(mes, 2, 0) as mes,
                         case when e60_anousu < ".db_getsession("DB_anousu")." then 5 else o58_localizadorgastos end as anexo,
                         cc30_ordem,
         		         cc31_classificacaocredores as codigo_lista,
         		         cc30_descricao as descr_lista,
                         o58_codigo,
         		         plugins.empnotagestor.dtatesto,
         		         g.z01_nome as gestor,
         		         plugins.empnotagestor.dtvencimento as dtvencimento_pag
                    from pagordemnota 
                         inner join empnota                           on e69_codnota                 = e71_codnota
                         inner join empnotaele                        on e70_codnota                 = e71_codnota 
                          left join plugins.empnotagestor             on e69_codnota                  = plugins.empnotagestor.empnota
                          left join cgm g                             on plugins.empnotagestor.gestor = g.z01_numcgm 
                         inner join pagordem                          on e50_codord                  = e71_codord
                         inner join pagordemele                       on e53_codord                  = e50_codord
                          left join plugins.liquidacaocompetencia     on pagordem                    = e50_codord    
                         inner join empempenho                        on e60_numemp                  = e50_numemp
         		         inner join plugins.controleinternoempenhoempempenho on e60_numemp = controleinternoempenhoempempenho.empempenho
         		         inner join plugins.controleinternoempenho           on controleinternoempenhoempempenho.controleinternoempenho = controleinternoempenho.sequencial
                         inner join classificacaocredoresempenho     on e60_numemp                  = cc31_empempenho
         		         inner join classificacaocredores            on cc31_classificacaocredores  = cc30_codigo
                         inner join cgm                              on cgm.z01_numcgm              = e60_numcgm
                         inner join orcdotacao                       on o58_coddot                  = e60_coddot
                                                                    and o58_instit                  = e60_instit
                                                                    and o58_anousu                  = e60_anousu
                         inner join orctiporec                       on o15_codigo                  = o58_codigo
                         inner join fonterecurso                     on fonterecurso.orctiporec_id  = o58_codigo
                                                                    and exercicio                   = o58_anousu
                         inner join empelemento                      on empelemento.e64_numemp      = e60_numemp
                         inner join orcelemento                      on orcelemento.o56_codele      = e64_codele
                                                                    and orcelemento.o56_anousu      = e60_anousu
                         inner join orcunidade                       on o41_orgao                   = o58_orgao
                                                                    and o41_unidade                 = o58_unidade
                                                                    and o41_anousu                  = o58_anousu
                                                                    and o41_instit                  = o58_instit 
                          left join acordoempempenho                 on ac54_empempenho             = e60_numemp 
                          left join empempaut                        on e61_numemp                  = e60_numemp                                        
                          left join acordoempautoriza                on e61_autori                  = ac45_empautoriza
                   where (e70_vlrliq - e70_vlranu) > e53_vlrpag 
                         {$sWhere} ) as dados

                left join acordo on ac16_sequencial = acordo
                left join acordocategoria on ac50_sequencial = ac16_acordocategoria
          order by o58_orgao, o58_unidade, cc30_ordem, codigo_lista, ac16_acordocategoria, dtvencimento_pag";
            
$rsDados = db_query($sSql);
$iLinhas = pg_num_rows($rsDados);
if ($iLinhas == 0) {
	db_redireciona("db_erros.php?fechar=true&db_erro=Nenhum registro encontrado.");
}
$iSubTotalLiquidado = 0;
$iSubTotalAnulado = 0;
$iTotalLiquidado = 0;
$iTotalAnulado = 0;
for ($iInd = 0; $iInd < $iLinhas; $iInd ++) {
	
	$oDados =  db_utils::fieldsMemory($rsDados, $iInd);

	if ($formato == "PDF") {
		
	  if ( $oPdf->gety() > $oPdf->geth() - 32 || $iInd == 0) {
	  	$oPdf->addpage("L");
	  	escreveCabecalho($oPdf);
	  }
	  
	  $sIdOrgaoUnidadeCategoria  = $oDados->o58_orgao."|".$oDados->o58_unidade."|".$oDados->codigo_lista;
	  $sIdOrgaoUnidadeCategoria .= "|".$oDados->o15_codigo."|".$oDados->ac16_acordocategoria;
	  if ($sHashOrgaoUnidadeCategoria != $sIdOrgaoUnidadeCategoria) {
	  	escreveOrgaoUnidade($oPdf, $oDados, $iInd, $iSubTotalLiquidado, $iSubTotalAnulado);
	  }

	  $iSubTotalLiquidado = $oDados->e70_vlrliq + $iSubTotalLiquidado;
	  $iSubTotalAnulado = $oDados->e70_vlranu + $iSubTotalAnulado;

	  $sHashOrgaoUnidadeCategoria = $sIdOrgaoUnidadeCategoria;
	  escreveDados($oPdf, $oDados, $iSubTotalLiquidado-$iSubTotalAnulado);
	  
	  if ($iInd == $iLinhas-1) {
	    escreverSubTotal($oPdf, $iInd, $iSubTotalLiquidado, $iSubTotalAnulado);
	  	escreverTotal($oPdf, $iTotalLiquidado, $iTotalAnulado); 		
	  }

	} else {
		
		if ($iInd == 0) {
		 $sLinha = "$head3 $head4 $head5 $head6 $head7 $head8\n\n";
		 fwrite($arquivoCSV, $sLinha);
		 
		 $sLinha =  "Exercício | Empenho | Nota | Anexo | Recurso | OP | Vlr. Liquidado | Vlr. Anulado | Competência | CPF/CNPJ | Nome \n";	
		 fwrite($arquivoCSV, $sLinha);
		}
		
		$sIdOrgaoUnidade = $oDados->o58_orgao."|".$oDados->o58_unidade;
		if ($sHashOrgaoUnidade != $sIdOrgaoUnidade) {
			$sLinha = "\nOrgao/Unidade: ".$oDados->o58_orgao."/".$oDados->o58_unidade." - ".$oDados->o41_descr."\n";	
		    fwrite($arquivoCSV, $sLinha);
		}
		$sHashOrgaoUnidade = $sIdOrgaoUnidade;
		
		$sLinha = $oDados->e60_anousu
		          ."|".$oDados->e60_codemp
		          ."|".$oDados->e71_codnota
		          ."|".$oDados->anexo
		          ."|".$oDados->o58_codigo
		          ."|".$oDados->e50_codord
		          ."|".db_formatar($oDados->e70_vlrliq, "f","", 1)
		          ."|".db_formatar($oDados->e70_vlranu, "f","", 1)
		          ."|".$oDados->mes."/".$oDados->ano             
		          ."|".db_formatar(trim($oDados->z01_cgccpf), ((strlen(trim($oDados->z01_cgccpf)) <= 11)?"cpf":"cnpj"))
		          ."|".strtoupper($oDados->z01_nome)."\n";
		fwrite($arquivoCSV, $sLinha);
	}
}

function escreveCabecalho($oPdf) {
	
	$oPdf->setfont('arial','b',8);
	$oPdf->cell(22,5,"Empenho",       "",0,"C",1);
	$oPdf->cell(15,5,"Nota",          "",0,"C",1);
	$oPdf->cell(5,5,"Anx",            "",0,"C",1);
	$oPdf->cell(12,5,"Fnt",           "",0,"C",1);
	$oPdf->cell(12,5,"OP",            "",0,"C",1);
	$oPdf->cell(15,5,"Liquidado",     "",0,"R",1);
	$oPdf->cell(15,5,"Anul.",         "",0,"R",1);
	$oPdf->cell(15,5,"Compet.",       "",0,"C",1);
	$oPdf->cell(25,5,"CPF/CNPJ",      "",0,"C",1);
	$oPdf->cell(50,5,"Nome",          "",0,"L",1);
	$oPdf->cell(13,5,"Dt. Atesto",    "",0,"C",1);
	$oPdf->cell(40,5,"Gestor",        "",0,"L",1);
	$oPdf->cell(20,5,"Acumulador",    "",0,"R",1);
	$oPdf->cell(20,5,"Vencimento",    "",1,"C",1);
	$oPdf->setfont('arial','',8);
	
}

function escreverSubTotal($oPdf, $iInd, $iSubTotalLiquidado, $iSubTotalAnulado) {
	if($iInd != 0) {
		$oPdf->setfont('arial','b',7);
		$oPdf->cell(66,5,"SUBTOTAL", "",0,"C",1);
		$oPdf->cell(15,5, db_formatar($iSubTotalLiquidado, "f","", 1) , 0, 0,"R",1);
		$oPdf->cell(15,5, db_formatar($iSubTotalAnulado, "f","", 1) , 0, 0,"R",1);
		$oPdf->cell(183,5, "" , 0, 1,"R",1);
	}
		$GLOBALS['iTotalLiquidado'] += $iSubTotalLiquidado;
		$GLOBALS['iTotalAnulado'] += $iSubTotalAnulado;
		$GLOBALS['iSubTotalLiquidado'] = 0;
		$GLOBALS['iSubTotalAnulado'] = 0;
}

function escreverTotal($oPdf, $iTotalLiquidado, $iTotalAnulado) {
	$oPdf->setfont('arial','b',7);
	$oPdf->ln(2);
	$oPdf->cell(66,5,"TOTAL", "",0,"C", 1);
	$oPdf->cell(15,5, db_formatar($iTotalLiquidado, "f","", 1), 0, 0,"R", 1);
	$oPdf->cell(23,5, db_formatar($iTotalAnulado, "f","", 1), 0, 0,"C", 1);
	$oPdf->cell(175,5, "", 0, 1,"R", 1);
}

function escreveOrgaoUnidade($oPdf, $oDados, $iInd, $iSubTotalLiquidado, $iSubTotalAnulado) {
	escreverSubTotal($oPdf, $iInd, $iSubTotalLiquidado, $iSubTotalAnulado);
	
	$sInfo = "{$oDados->o58_orgao}.{$oDados->o58_unidade} - $oDados->o41_descr / Lista: $oDados->descr_lista";
	if (!empty($oDados->ac50_descricao)) {
	  $sInfo .= " - {$oDados->ac50_descricao}";
    }
	$oPdf->setfont('arial','b',8);
	$oPdf->ln(2);
	$oPdf->cell(275,5,$sInfo,"",1,"L",0);
	$oPdf->setfont('arial','',8);
	
}

function escreveDados($oPdf, $oDados, $iAcumulo) {
	
	$oPdf->setfont('arial','',7);
	$oPdf->cell(22,5,str_pad($oDados->e60_codemp, 5, "0", STR_PAD_LEFT)."/{$oDados->e60_anousu}",           0,0,"C",0);
	$oPdf->cell(15,5,$oDados->e71_codnota,                         0,0,"C",0);
	$oPdf->cell(5,5,$oDados->anexo,                                0,0,"C",0);
	$oPdf->cell(12,5,str_pad($oDados->o58_codigo, 7, "0", STR_PAD_LEFT),                          0,0,"C",0);
	$oPdf->cell(12,5,$oDados->e50_codord,                          0,0,"C",0);
	$oPdf->cell(15,5,db_formatar($oDados->e70_vlrliq, "f","", 1) , 0,0,"R",0);
	$oPdf->cell(15,5,db_formatar($oDados->e70_vlranu, "f","", 1) , 0,0,"R",0);
	$oPdf->cell(15,5,$oDados->mes."/".$oDados->ano,                0,0,"C",0);
	$oPdf->cell(25,5,db_formatar(trim($oDados->z01_cgccpf), ((strlen(trim($oDados->z01_cgccpf)) <= 11)?"cpf":"cnpj")) ,0,0,"C",0);
	$oPdf->cell(50,5, substr($oDados->z01_nome, 0, 28).((strlen($oDados->z01_nome)>28)?'...':''), 0,0,"L",0);
	if(empty($oDados->dtatesto)) {
	  $oPdf->cell(13,5,'00/00/0000', 0,0,"C",0);
	} else {
	  $oPdf->cell(13,5,db_formatar($oDados->dtatesto, "d","", 1)   , 0,0,"C",0);
	}
	if(empty($oDados->gestor)) $oDados->gestor = "SEM GESTOR VINCULADO";
	$oPdf->cell(40,5,substr($oDados->gestor, 0, 25).((strlen($oDados->gestor)>25)?'...':''), 0,0,"L",0);
	$oPdf->cell(20,5,db_formatar($iAcumulo, "f","", 1)   , 0,0,"R",0);
	if(empty($oDados->dtatesto)) {
	  $oPdf->cell(20,5,'00/00/0000', 0,1,"C",0);
	} else {
	  $oPdf->cell(20,5,db_formatar($oDados->dtvencimento_pag, 'd') , 0,1,"C",0);
	}
	
}

if ($formato == "PDF") { 
	$oPdf->Output();
} else {
	fclose($arquivoCSV);
	
	echo "<script>
			  location.href = \"db_download.php?arquivo=tmp/emp2_liquidacoesaptasrepassefinanceiro.csv\";
		  </script>";
	
}

?>
