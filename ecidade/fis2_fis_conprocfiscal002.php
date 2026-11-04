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
include(modification("classes/db_fis_procfiscal_classe.php"));
include(modification("classes/db_fis_procfiscalinscr_classe.php"));
include(modification("classes/db_fis_procfiscalmatric_classe.php"));
include(modification("classes/db_fis_procfiscalsani_classe.php"));
include(modification("classes/db_fis_procfiscalcgm_classe.php"));
include(modification("classes/db_fis_procfiscalprot_classe.php"));
include(modification("classes/db_fis_procfiscalfiscais_classe.php"));
$clprocfiscal       = new cl_fis_procfiscal;
$clprocfiscalinscr  = new cl_fis_procfiscalinscr;
$clprocfiscalmatric = new cl_fis_procfiscalmatric;
$clprocfiscalsani   = new cl_fis_procfiscalsani;
$clprocfiscalcgm    = new cl_fis_procfiscalcgm;
$clprocfiscalprot   = new cl_fis_procfiscalprot;
$clprocfiscal->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("descrdepto");
$clrotulo->label("nomeinst");
$clrotulo->label("y33_descricao");
$clrotulo->label("j01_matric");
$clrotulo->label("z01_nome");
$clrotulo->label("z01_numcgm");
$clrotulo->label("q02_inscr");
$clrotulo->label("y80_codsani");
$clrotulo->label("p58_codproc");
$clrotulo->label("y100_sequencial");
db_postmemory($HTTP_POST_VARS);
$db_opcao = 1;
$db_botao = true;

$sql = "
				select  y100_sequencial,
				        y101_numcgm,
								z01_nome,
								y100_dtinicial,
								y100_dtfinal,
								y100_obs,
								y100_coddepto, 
								descrdepto,
								y100_procfiscalcadtipo,
								y33_descricao,
								y103_inscr,
				        y102_matric
				from fiscalizacao.fis_procfiscal 
				inner join db_depart     on db_depart.coddepto = fis_procfiscal.y100_coddepto
				inner join fiscalizacao.fis_procfiscalcgm on y101_procfiscal    = y100_sequencial
				inner join cgm           on cgm.z01_numcgm     = y101_numcgm
				inner join fiscalizacao.fis_procfiscalcadtipo on y100_procfiscalcadtipo = y33_sequencial
				left  join fiscalizacao.fis_procfiscalmatric on y102_procfiscal = y100_sequencial 
    		left  join fiscalizacao.fis_procfiscalinscr  on y103_procfiscal = y100_sequencial
				where y100_sequencial = $procfiscal
       ";

// die($sql);	
		
$result = pg_query($sql);
$linhas = pg_num_rows($result);
db_fieldsmemory($result,0);

$sqldata = "select * from fiscalizacao.fis_processoprorrogacaofinalizacao where processo_fiscal = $procfiscal order by 1 desc limit 1";
$resultdata = pg_query($sqldata);
db_fieldsmemory($resultdata,0);
if($data_prorrogacao != null ){
	$y100_dtfinal = $data_prorrogacao;
}else{
	$y100_dtfinal = $data_fim;
}
$sqlvistoria = "								
								select y71_inscr,y73_numcgm,y72_matric,y70_codvist,y70_tipovist,y77_descricao,y70_data,y70_parcial,y41_descr
								from fiscalizacao.fis_procfiscalvistorias 
								inner join fiscalizacao.fis_vistorias on y70_codvist = y109_codvist 
								inner join fiscalizacao.fis_tipovistorias on y77_codtipo = y70_tipovist 
								left join fiscalizacao.fis_vistmatric on y72_codvist=y70_codvist 
								left join fiscalizacao.fis_vistcgm on y73_codvist=y70_codvist 
								left join fiscalizacao.fis_vistinscr on y71_codvist=y70_codvist 
								left join fiscalizacao.fis_fandam on y39_codandam = (select max(y68_codandam) from fiscalizacao.fis_vistoriaandam where y68_codvist = y70_codvist) 
								left join fiscalizacao.fis_tipoandam on y41_codtipo = y39_codtipo
								where y109_procfiscal =	$procfiscal					";
								
$rsvistorias     = pg_query($sqlvistoria);								
$linhasvistorias = pg_num_rows($rsvistorias);			


$sqlintimacao = "
									 select y36_numcgm,y35_matric,y34_inscr,y30_codnoti,y30_data,y30_nome,y41_descr 
									 from fiscalizacao.fis_procfiscalnotificacao 
									 inner join fiscalizacao.fis_fiscal on y30_codnoti = y110_notificacaofiscal 
									 left join fiscalizacao.fis_fiscalcgm on y36_codnoti=y30_codnoti 
									 left join fiscalizacao.fis_fiscalmatric on y35_codnoti=y30_codnoti 
									 left join fiscalizacao.fis_fiscalinscr on y34_codnoti=y30_codnoti
									 inner join fiscalizacao.fis_fiscalintimacao on in01_codnoti = y30_codnoti
									 left join fiscalizacao.fis_fandam on y39_codandam = (select max(y49_codandam) from fiscalizacao.fis_fiscalandam where y49_codnoti = y30_codnoti) 
									 left join fiscalizacao.fis_tipoandam on y41_codtipo = y39_codtipo
									 where y110_procfiscal =$procfiscal and in01_intimacao = true";
$rsintimacao     = pg_query($sqlintimacao);								
$linhasintimacao = pg_num_rows($rsintimacao);

$sqlnotificacao = "
									 select y36_numcgm,y35_matric,y34_inscr,y30_codnoti,y30_data,y30_nome,y41_descr 
									 from fiscalizacao.fis_procfiscalnotificacao 
									 inner join fiscalizacao.fis_fiscal on y30_codnoti = y110_notificacaofiscal 
									 left join fiscalizacao.fis_fiscalcgm on y36_codnoti=y30_codnoti 
									 left join fiscalizacao.fis_fiscalmatric on y35_codnoti=y30_codnoti 
									 left join fiscalizacao.fis_fiscalinscr on y34_codnoti=y30_codnoti
									 inner join fiscalizacao.fis_fiscalintimacao on in01_codnoti = y30_codnoti
									 left join fiscalizacao.fis_fandam on y39_codandam = (select max(y49_codandam) from fiscalizacao.fis_fiscalandam where y49_codnoti = y30_codnoti) 
									 left join fiscalizacao.fis_tipoandam on y41_codtipo = y39_codtipo
									 where y110_procfiscal =$procfiscal and in01_intimacao = false";

$rsnotificacao     = pg_query($sqlnotificacao);								
$linhasnotificacao = pg_num_rows($rsnotificacao);
$sqlauto = "select y54_numcgm,y53_matric,y52_inscr,y50_codauto,y50_data,y50_nome,y41_descr,
			(select pl11_autoret from fiscalizacao.fis_retauto where pl11_autoold = y50_codauto) as pl11_autoret
            from fiscalizacao.fis_procfiscalauto 
						inner join fiscalizacao.fis_auto       on y50_codauto = y111_auto 
						left  join fiscalizacao.fis_autocgm    on y54_codauto = y50_codauto 
						left  join fiscalizacao.fis_automatric on y53_codauto = y50_codauto 
						left  join fiscalizacao.fis_autoinscr  on y52_codauto = y50_codauto 
						left join fiscalizacao.fis_fandam on y39_codandam = (select max(y58_codandam) from fiscalizacao.fis_autoandam where y58_codauto = y50_codauto) 
						left join fiscalizacao.fis_tipoandam on y41_codtipo = y39_codtipo
						where y111_procfiscal = $procfiscal ";
$rsauto     = pg_query($sqlauto);								
$linhasauto = pg_num_rows($rsauto);	

$sqllancamento = "SELECT nl06_numcgm,
			       nl05_matric,
			       nl04_inscr,
			       nl01_codlanc,
			       nl01_data,
			       nl01_nome,
			       y41_descr
			FROM fiscalizacao.fis_procfiscallanc
			INNER JOIN fiscalizacao.fis_lancamento ON nl09_lanc = nl01_codlanc
			LEFT JOIN fiscalizacao.fis_lanccgm ON nl06_codlanc = nl01_codlanc
			LEFT JOIN fiscalizacao.fis_lancmatric ON nl05_codlanc = nl01_codlanc
			LEFT JOIN fiscalizacao.fis_lancinscr ON nl04_codlanc = nl01_codlanc
			LEFT  JOIN fiscalizacao.fis_fandam ON y39_codandam =
			  (SELECT max(nl19_codandam)
			   FROM fiscalizacao.fis_lancandam
			   WHERE nl19_codlanc = nl01_codlanc)
			LEFT  JOIN fiscalizacao.fis_tipoandam ON y41_codtipo = y39_codtipo
			WHERE nl09_procfiscal = $procfiscal ";
$rslancamento     = pg_query($sqllancamento);								
$linhaslancamento = pg_num_rows($rslancamento);	
	
$sqllevanta = "SELECT y62_inscr,
       y93_numcgm,
       y60_codlev,
       y60_data,

  (SELECT y117_auto
   FROM fiscalizacao.fis_autolevanta
   WHERE y117_levanta = y60_codlev) AS y117_auto,

  (SELECT nl15_lancamento
   FROM fiscalizacao.fis_lanclevanta
   WHERE nl15_levanta = y60_codlev) AS nl15_notificacao,
       (CASE
            WHEN
                   (SELECT y117_auto
                    FROM fiscalizacao.fis_autolevanta
                    WHERE y117_levanta = y60_codlev) IS NULL THEN 'Notificacão de Lançamento'
            ELSE 'Auto de Infração'
        END) AS tipo,       

  (SELECT pl12_levantaret
   FROM fiscalizacao.fis_retautolevanta
   WHERE pl12_levantaold = y60_codlev) AS pl12_levantaret
FROM fiscalizacao.fis_procfiscallevanta
INNER JOIN fiscalizacao.fis_levanta ON y60_codlev = y112_levanta
LEFT JOIN fiscalizacao.fis_levcgm ON y93_codlev = y60_codlev
LEFT JOIN fiscalizacao.fis_levinscr ON y62_codlev = y60_codlev
WHERE y112_procfiscal = $procfiscal";

$rslevanta     = pg_query($sqllevanta);								
$linhaslevanta = pg_num_rows($rslevanta);		

$sqlvarfix = "select q33_codigo,q33_inscr,z01_nome as nome_lanc ,q33_data 
              from fiscalizacao.fis_procfiscalvarfix 
							inner join varfix on q33_codigo=y113_varfix 
							inner join issbase on q33_inscr=q02_inscr 
							inner join cgm on q02_numcgm=z01_numcgm 
							where y113_procfiscal = $procfiscal ";
$rsvarfix     = pg_query($sqlvarfix);								
$linhasvarfix = pg_num_rows($rsvarfix);		

$sqlFiscais = "SELECT DISTINCT ON (db_usuarios.id_usuario) db_usuarios.id_usuario, db_usuarios.nome, fis_procfiscalfiscais.y106_principal, fis_processofiscalativo.ativo 
FROM fiscalizacao.fis_procfiscalfiscais
INNER JOIN fiscalizacao.fis_procfiscal ON fis_procfiscal.y100_sequencial = fis_procfiscalfiscais.y106_procfiscal 
INNER JOIN fiscalizacao.fis_cadfiscais ON fis_procfiscalfiscais.y106_cadfiscais = fis_cadfiscais.id_usuario
INNER JOIN db_usuarios ON db_usuarios.id_usuario = fis_cadfiscais.id_usuario
INNER JOIN fiscalizacao.fis_processofiscalativo ON fis_processofiscalativo.processo_fiscal = fis_procfiscal.y100_sequencial and fis_processofiscalativo.fiscal = y106_cadfiscais
WHERE fis_procfiscal.y100_coddepto = ".db_getsession('DB_coddepto')." and y106_procfiscal = $procfiscal AND fis_procfiscal.y100_instit = ".db_getsession('DB_instit');
// die($sqlFiscais);
$resultFiscais = pg_query($sqlFiscais);
$rowFiscais = pg_num_rows($resultFiscais);

$sqlAnexos = "select  	p58_numero || '/' || p58_ano as p58_numero,
						p58_dtproc,
						p51_descr
				from fiscalizacao.fis_processosfiscais 
				inner join protprocesso on pf01_processo = p58_codproc
				inner join tipoproc on p58_codigo = p51_codigo
					where pf01_procfiscal = $procfiscal";
$resultAnexos = pg_query($sqlAnexos);
$rowAnexos = pg_num_rows($resultAnexos);

$sqlProcAdm = "select
		     p58_numero, p58_ano 
		     from fiscalizacao.fis_procfiscalprot 
		     inner join protprocesso on y105_protprocesso = p58_codproc 
		     where y105_procfiscal = $procfiscal";
$resultProcAdm = db_query($sqlProcAdm);
db_fieldsmemory($resultProcAdm, 0);	


$sPlugin = "select data_abertura, case when situacao = 1 then data_prorrogacao when situacao = 2 then data_fim end as data_fim, case when situacao = 1 then 'Prorrogado'  when situacao = 2 then 'Finalizado' end as situacao, nome from fiscalizacao.fis_processoprorrogacaofinalizacao 
inner join fiscalizacao.fis_procfiscal on y100_sequencial = processo_fiscal
/*inner join fiscalizacao.fis_procfiscalfiscais on y100_sequencial = y106_procfiscal
INNER JOIN fiscalizacao.fis_cadfiscais ON fis_procfiscalfiscais.y106_cadfiscais = fis_cadfiscais.id_usuario*/
INNER JOIN db_usuarios ON db_usuarios.id_usuario = fis_processoprorrogacaofinalizacao.id_usuario
where processo_fiscal = $procfiscal";
$rsPlugin = pg_query($sPlugin);
$rowPlugin = pg_num_rows($rsPlugin);
//die($sPlugin);
?>

<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<br>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<center>
	<form name="form1" method="post" action="<?=$db_action?>">
<table width="790" border="0" cellspacing="0" cellpadding="0">
   <tr>
    <td colspan = "2">
     &nbsp;
    </td>
  </tr> 

	<tr>
    <td colspan = "2">
    	<fieldset><Legend align="center"><b>Dados do Processo Fiscal</b></legend>
        <table border="0" width="100%" >
        	<tr>
        		<td width="20%" ><b>Processo fiscal:</b></td>
					  <td><?=$procfiscal?>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Tipo:</b> <?=$y100_procfiscalcadtipo?> - <?=$y33_descricao?> </td>
					</tr>
        	<tr>
        		<td><b>Processo Administrativo:</b></td>
					<td><?=$p58_numero."/".$p58_ano; ?></td>
					</tr>
					<tr>	
			<td><b>Data inicial:</b></td>
					  <td><?=db_formatar($y100_dtinicial,"d")?> </td>
					</tr>
					<tr>
        		<td><b>Data final:</b></td>
					  <td><?=db_formatar($y100_dtfinal,"d")?></td>
					</tr>
					<tr>
        		<td><b>Departamento:</b></td>
					  <td><?=$y100_coddepto?> - <?=$descrdepto?></td>
					</tr>
					<tr>
        		<td><b>Observação:</b></td>
					  <td><?=$y100_obs?></td>
					</tr>
			
        </table>
			</fieldset>
			<fieldset><Legend align="center"><b>Dados do Contribuinte</b></legend>
        <table border="0" width="100%" >
<tr>
<td><b>Consulta:</b></td>
<td>
<?php 
$sTipoProc  = " select y103_inscr as q02_inscr, y102_matric as j01_matric, y104_codsani as y80_codsani, y101_numcgm as z01_numcgm  from fiscalizacao.fis_procfiscal ";
$sTipoProc .= "     left join fiscalizacao.fis_procfiscalinscr  on y103_procfiscal = y100_sequencial";
$sTipoProc .= "     left join fiscalizacao.fis_procfiscalmatric on y102_procfiscal = y100_sequencial";
$sTipoProc .= "     left join fiscalizacao.fis_procfiscalsani   on y104_procfiscal = y100_sequencial";
$sTipoProc .= "     left join fiscalizacao.fis_procfiscalcgm    on y101_procfiscal = y100_sequencial";
$sTipoProc .= "     where y100_sequencial = ".$procfiscal;
$rsTipoProc = db_query($sTipoProc);
db_fieldsmemory($rsTipoProc,0);
 if($q02_inscr != ""){
    unset($z01_numcgm );
}elseif($j01_matric != ""){
    unset($z01_numcgm );
}elseif($y104_codsani != ""){
    unset($z01_numcgm );
}


if(isset($z01_numcgm) && $z01_numcgm != ""){

  include(modification("classes/db_cgm_classe.php"));
  $clcgm  = new cl_cgm;

  $result = $clcgm->sql_record($clcgm->sql_query_ender($z01_numcgm));
  if($clcgm->numrows > 0){
    db_fieldsmemory($result,0);
   }
  $dados = "<a onClick=\"js_abre('prot3_conscgm002.php?fechar=func_nome&numcgm=$z01_numcgm');return false\" href=''>CGM: ".$z01_numcgm." &nbsp;|&nbsp;".@$z01_nome."</a>";
}elseif(isset($j01_matric) && $j01_matric != ""){

  include(modification("classes/db_iptubase_classe.php"));
  $cliptubase = new cl_iptubase;
  $result     = $cliptubase->sql_record($cliptubase->proprietario_query($j01_matric));
  if($cliptubase->numrows > 0){

    db_fieldsmemory($result,0);
  }
  $dados = "<a onClick=\"js_abre('cad3_conscadastro_002.php?cod_matricula=$j01_matric');return false\" href=''>matrícula: ".$j01_matric." &nbsp;|&nbsp;".@$z01_nome."</a>";
}elseif(isset($q02_inscr)  && $q02_inscr  != ""){

  
  include(modification("classes/db_issbase_classe.php"));
  $clissbase = new cl_issbase;

  $result    = $clissbase->sql_record($clissbase->empresa_query($q02_inscr));
  if($clissbase->numrows > 0){

    db_fieldsmemory($result,0);

    include(modification("classes/db_cgm_classe.php"));
    $clcgm = new cl_cgm;
    $result = $clcgm->sql_record($clcgm->sql_query($q02_numcgm));
    if($clcgm->numrows > 0){
      db_fieldsmemory($result,0);
    }
  }
  $dados = "<a onClick=\"js_abre('iss3_consinscr003.php?numeroDaInscricao=$q02_inscr');return false\" href=''>inscrição: ".$q02_inscr." &nbsp;|&nbsp;".@$z01_nome."</a>";
}

 echo "<strong>".@$dados."&nbsp;</strong>";;
?>
</td>
</tr>
        	<tr>
        		<td  width="20%"><b>Contribuinte:</b></td>
					  <td><?=$z01_nome?> </td>
					</tr>
        	<tr>
        		<td><b>CGM:</b></td>
					  <td><?=$y101_numcgm?> </td>
					</tr>	
					<tr>
        		<td><b>Matrícula:</b></td>
					  <td><?=$y102_matric?> </td>
					</tr>	
					<tr>
        		<td><b>Inscrição:</b></td>
					  <td><?=$y103_inscr?> </td>
					</tr>	

        </table>
			</fieldset>
				<?php  if($linhasvistorias > 0){ ?>
			<fieldset><Legend align="center"><b>Vistorias</b></legend>
			  
				<?php  
				 	echo "
					<table border='0' width='100%' class='tab_cinza'>
				 	 <tr>
					   <th>Vistoria</th>
						 <th>Tipo de vistoria</th>
						 <th>Data</th>
						 <th>Parcial</th>
						 <th>Vistoria por</th>
						 <th>Andamento</th>
					 </tr>";
				 	 for($v=0 ; $v<$linhasvistorias ; $v++){
				 	 	 db_fieldsmemory($rsvistorias, $v);
						 
						 if($y70_parcial=="t"){
						 	$y70_parcial = "Sim";
						 }else{
						 	$y70_parcial = "Não";
						 }
						 	echo "
						 	 <tr>
							   <td><a onClick=\"js_abre('fis3_fis_consultavist002.php?y70_codvist=$y70_codvist'); return false;\" href=''>$y70_codvist</a></td>
								 <td>$y77_descricao</td>
								 <td>".db_formatar($y70_data,"d")."</td>
								 <td>$y70_parcial</td>";
							 if($y73_numcgm!=""){
							 	 echo "<td>CGM - $y73_numcgm</td>";
							 }elseif($y72_matric!=""){
							 	 echo "<td>Matricula - $y72_matric</td>";
							 }elseif($y71_inscr!=""){
							 	 echo "<td>Inscrição - $y71_inscr</td>";
							 }
							 echo "<td>$y41_descr</td>";
							 	 
							echo "	 
							 </tr>";
						 
				 	 }
					
				 }
				 // else{
				 // 	echo "
				 // 	 <table border='0' width='100%' >
					//    <tr><td align='center'> <b>Nenhuma vistoria encontrada para este processo fiscal!.</b></td></tr>
					// 	 ";
				 // }
		?>
		<?php  if($linhasvistorias > 0){ ?>
   		</table>
			</fieldset>
			<?php  }?>

			<?php  if($rowPlugin > 0){ ?>
			<fieldset><Legend align="center"><b>Movimentações do Processo Fiscal</b></legend>
			  
				<?php  
				 	echo "
					<table border='0' width='100%' class='tab_cinza'>
				 	 <tr>
					   <th> Data de lançamento</th>
						 <th>Tipo de lançamento</th>
						 <th>Data Tipo lançamento</th>
						 <th>Usuário</th>
					 </tr>";
				 	 for($px=0 ; $px<$rowPlugin ; $px++){
				 	 	 db_fieldsmemory($rsPlugin, $px);
						 	echo "
						 	 <tr>
							   <td>".db_formatar($data_abertura,"d")."</td>
								 <td>$situacao</td>
								 <td>".db_formatar($data_fim,"d")."</td>
								 <td>$nome</td>";
							echo "	 
							 </tr>";
						 
				 	 }
					
				 }
				 // else{
				 // 	echo "
				 // 	 <table border='0' width='100%' >
					//    <tr><td align='center'> <b>Nenhuma vistoria encontrada para este processo fiscal!.</b></td></tr>
					// 	 ";
				 // }
		?>
		<?php  if($rowPlugin > 0){ ?>
   		</table>
			</fieldset>
			<?php  }?>
				<?php  if($rowAnexos > 0){ ?>
			<fieldset><Legend align="center"><b>Processos Anexos</b></legend>
			  
				<?php  
				 	echo "
					<table border='0' width='100%' class='tab_cinza'>
				 	 <tr>
					     <th>Processos Anexos</th>
						 <th>Data</th>
						 <th>Assunto</th>
					 </tr>";
				 	 for($p=0 ; $p<$rowAnexos; $p++){
				 	 	 db_fieldsmemory($resultAnexos, $p);
						 	echo "
						 	 <tr>
							     <td>$p58_numero</td>
								 <td>".db_formatar($p58_dtproc,"d")."</td>
								 <td>$p51_descr</td>";
							echo "	 
							 </tr>";
						 
				 	 }
					
				 }
				 // else{
				 // 	echo "
				 // 	 <table border='0' width='100%' >
					//    <tr><td align='center'> <b>Nenhum processo anexo encontrado para este processo fiscal!.</b></td></tr>
					// 	 ";
		
		if($rowAnexos > 0){ 
		?>
   		</table>
			</fieldset>
		<?php }?>	
				<?php  if($linhasintimacao > 0){ ?>
			<fieldset><Legend align="center"><b>Intimação fiscal</b></legend>
				<?php  
				 	echo "
					<table border='0' width='100%' class='tab_cinza'>
				 	 <tr>
					   <th>Codigo da Intimação</th>
						 <th>Nome</th>
						 <th>Data</th>
						 <!--<th>Intimado por</th>-->
						 <th>Andamento</th>
						 <th>Imprimir </th>
					 </tr>";
				 	 for($n=0 ; $n<$linhasintimacao ; $n++){
				 	 	 db_fieldsmemory($rsintimacao, $n);
						 echo "
						 	 <tr>
							   <td><a onClick=\"js_abre('fis3_fis_consnotificinf002.php?codfiscal=$y30_codnoti&intimacao=1'); return false;\" href=''>$y30_codnoti</a></td>
								 <td>$y30_nome</td>
								 <td>".db_formatar($y30_data,"d")."</td>";
								if($y34_inscr!=""){
							 	// echo "<td>Inscrição - $y34_inscr</td>";
							 }elseif($y35_matric!=""){
							 //	 echo "<td>Matricula - $y35_matric</td>";
							 }elseif($y36_numcgm!=""){
							 //	 echo "<td>CGM - $y36_numcgm</td>";
							 }
							 echo "<td>$y41_descr</td>";
							$Imprime = consultaRelatorio($y30_codnoti,1);
							if( $Imprime == true ){
								echo "<td><center><input name='imprimir' type='button' value='Imprimir' onClick=\"js_abre2('fis2_fis_fiscalinf002.php?codfiscal=$y30_codnoti&intimacao=1'); return false;\"> </center> </td>";	
							}else{
								echo "<td><center> -</center> </td>";
							}
							echo "
							 </tr>";
				 	 }
				 }
				 // else{
				 // 	echo "
				 // 	 <table border='0' width='100%' >
					//    <tr><td align='center'> <b>Nenhuma intimação encontrada para este processo fiscal!.</b></td></tr>
					// 	 ";
				if($linhasintimacao > 0){
		?> 
   		</table>
			</fieldset>
			<?php  } ?>
			<?php 	 if($linhasnotificacao > 0){?>
			<fieldset><Legend align="center"><b>Notificação</b></legend>
				<?php  
				 	echo "
					<table border='0' width='100%' class='tab_cinza'>
				 	 <tr>
					   <th>Codigo da Notificação</th>
						 <th>Nome</th>
						 <th>Data</th>
						 <!--<th>Notificação por</th>-->
						 <th>Andamento</th>
						 <th>Imprimir</th>
					 </tr>";
				 	 for($n=0 ; $n<$linhasnotificacao ; $n++){
				 	 	 db_fieldsmemory($rsnotificacao, $n);
						 echo "
						 	 <tr>
							    <td><a onClick=\"js_abre('fis3_fis_consnotificinf002.php?codfiscal=$y30_codnoti'); return false;\" href=''>$y30_codnoti</a></td>
								 <td>$y30_nome</td>
								 <td>".db_formatar($y30_data,"d")."</td>";
								if($y34_inscr!=""){
							 //	 echo  "<td>Inscrição - $y34_inscr</td>";
							 }elseif($y35_matric!=""){
							 //	 echo "<td>Matricula - $y35_matric</td>";
							 }elseif($y36_numcgm!=""){
							 //	 echo "<td>CGM - $y36_numcgm</td>";
							 }
							 echo "<td>$y41_descr</td>";
							$Imprime = consultaRelatorio($y30_codnoti,0);
							if( $Imprime == true ){
       								 echo "<td><center><input name='imprimir' type='button' value='Imprimir' onClick=\"js_abre2('fis2_fis_fiscalinf002.php?codfiscal=$y30_codnoti'); return false;\"> </center> </td>";
							}else{
        							echo "<td><center> -</center> </td>";
							}
							 echo "
							 </tr>";
				 	 }
				 }
				 // else{
				 // 	echo "
				 // 	 <table border='0' width='100%' >
					//    <tr><td align='center'> <b>Nenhuma notificação encontrada para este processo fiscal!.</b></td></tr>
					// 	 ";
				 // }
		if($linhasnotificacao > 0){
		?> 
   		</table>
			</fieldset>
			<?php }?>
			
				<?php  if($linhaslevanta > 0){ ?>
			<fieldset><Legend align="center"><b>Levantamento fiscal</b></legend>
				<?php   
				 	echo "
					<table border='0' width='100%' class='tab_cinza'>
				 	 <tr>
					   <th>Codigo</th>
						 <th>Codigo Retificador</th>
             <th>Data</th>
						 <th>Tipo Vínculo</th>
						 <th>Imprimir</th>
					 </tr>";
				 	 for($l=0 ; $l<$linhaslevanta ; $l++){
				 	 	 db_fieldsmemory($rslevanta, $l);
						 echo "
						 	 <tr>
							   <td>$y60_codlev</td>
                 <td>$pl12_levantaret</td>
								 <td>".db_formatar($y60_data,"d")."</td>";							 	 						 	 

							 	 if($tipo == 'Notificacão de Lançamento'){
							 	 	echo "<td> $nl15_notificacao - $tipo </td>";	
							 	 }else{
							 	 	echo "<td> $y117_auto - $tipo</td>";	
							 	 }

							 	 $ImprimeL = consultaRelatorioLevanta( $y60_codlev );
								if( $ImprimeL == true ){
	       								 echo "<td><center><input name='imprimir' type='button' value='Imprimir' onClick=\"js_abre2('fis2_fis_levantamento002.php?codlev=$y60_codlev'); return false;\"> </center> </td>";
								}else{
	        							echo "<td><center> -</center> </td>";
								}
							 echo "
							 </tr>";
				 	 }
				 }
				 // else{
				 // 	echo "
				 // 	 <table border='0' width='100%' >
					//    <tr><td align='center'> <b>Nenhum Levantamento fiscal encontrado para este processo fiscal!.</b></td></tr>
					// 	 ";
				 // }
			if($linhaslevanta > 0){ 
		?>
   		</table>
			</fieldset>
			<?php }?>
			<?php  if($linhasauto > 0){ ?>
			<fieldset><Legend align="center"><b>Auto de infração</b></legend>
				<?php  
				 	echo "
					<table border='0' width='100%' class='tab_cinza'>
				 	 <tr>
					   <th>Codigo do auto</th>
						 <th>Auto Retificador</th>
						 <th>Data</th>
						 <!--<th>Auto de infração por</th>-->
						 <th>Andamento</th>
						 <th>Imprimir</th>
					 </tr>";
				 	 for($a=0 ; $a<$linhasauto ; $a++){
				 	 	 db_fieldsmemory($rsauto, $a); 
						 echo "
						 	 <tr>
							   <td><a onClick=\"js_abre('fis3_fis_consautoinf002.php?codauto=$y50_codauto'); return false;\" href=''>$y50_codauto</a></td>
								 <td>$pl11_autoret</td>
								 <td>".db_formatar($y50_data,"d")."</td>";
								if($y54_numcgm!=""){
							 //	 echo "<td>CGM - $y54_numcgm</td>";
							 }elseif($y53_matric!=""){
							 //	 echo "<td>Matricula - $y53_matric</td>";
							 }elseif($y52_inscr!=""){
							 //	 echo "<td>Inscrição - $y52_inscr</td>";
							 }else{
							 //	echo "<td></td>";
							 }
							 echo "<td>$y41_descr</td>";
							$Imprime2 = consultaRelatorioAuto($y50_codauto);
 
if( $Imprime2 == true ){
        echo "<td><center>
<input name='imprimir' type='button' value='Imprimir' onClick=\"js_abre2('fis2_fis_autoinf002.php?codauto=$y50_codauto'); return false;\"> </center> </td>";
}else{
        echo "<td> <center>-</center> </td>";
}
							 echo "
							 </tr>";
				 	 }
				 }
				 // else{
				 // 	echo "
				 // 	 <table border='0' width='100%' >
					//    <tr><td align='center'> <b>Nenhum auto de infração encontrada para este processo fiscal!.</b></td></tr>
					// 	 ";
				 // }
				 if($linhasauto > 0){
		?>
   		</table>
			</fieldset>
			<?php }?>
			<!-- </fieldset> -->

			<?php  if($linhaslancamento > 0){ ?>
			<fieldset><Legend align="center"><b>Notificação de Lançamento</b></legend>
				<?php  
				 	echo "
					<table border='0' width='100%' class='tab_cinza'>
				 	 <tr>
					   <th>Codigo da Notificação</th>
						 <th>Lancamento Retificador</th>
						 <th>Data</th>
						 <th>Andamento</th>
						 <th>Imprimir</th>
					 </tr>";
				 	 for($a=0 ; $a<$linhaslancamento ; $a++){
				 	 	 db_fieldsmemory($rslancamento, $a); 
						 echo "
						 	 <tr>
							   <td><a onClick=\"js_abre('fis3_fis_conslancamento002.php?codlanc=$nl01_codlanc'); return false;\" href=''>$nl01_codlanc</a></td>
								 <td>-----</td>
								 <td>".db_formatar($nl01_data,"d")."</td>";
							 echo "<td>$y41_descr</td>";
							$Imprime2 = consultaRelatorioLancamento($nl01_codlanc);
 
						if( $Imprime2 == true ){
						        echo "<td><center>
						<input name='imprimir' type='button' value='Imprimir' onClick=\"js_abre2('fis2_fis_notlanc002.php?codlanc=$nl01_codlanc'); return false;\"> </center> </td>";
						}else{
						        echo "<td> <center>-</center> </td>";
						}
													 echo "
													 </tr>";
					 }
				 	}
				 if($linhaslancamento > 0){
		?>
   		</table>
			</fieldset>
			<?php }?>			
				 <?php  if($linhasvarfix > 0){?>
			<fieldset><Legend align="center"><b>Lançamento de estimativa</b></legend>
				<?php    
				 	echo "
					<table border='0' width='100%' class='tab_cinza'>
				 	 <tr>
					   <th>Codigo</th>
						 <th>Data</th>
						 <th>Inscrição</th>
						 <th>Nome</th>
					 </tr>";
				 	 for($e=0 ; $e<$linhasvarfix ; $e++){
				 	 	 db_fieldsmemory($rsvarfix, $e);
						 echo "
						 	 <tr>
							   <td>$q33_codigo</td>
								 <td>".db_formatar($q33_data,"d")."</td>
								 <td>$q33_inscr</td>
								 <td>$nome_lanc</td>
							 </tr>";
				 	 }
				 }
				 // else{
				 // 	echo "
				 // 	 <table border='0' width='100%' >
					//    <tr><td align='center'> <b>Nenhum Lançamento de estimativa encontrado para este processo fiscal!.</b></td></tr>
					// 	 ";
				 // }
		if($linhasvarfix > 0){		
		?>
   		</table>
			</fieldset>
			<?php }?>
				<?php 	if ($rowFiscais > 0) { ?>
			<fieldset>
				<legend align="center">Fiscais</legend>
					<?php 
							echo '<table border="0" width="100%" class="tab_cinza">';
							echo '<tr><th>Código</th><th>Nome</th><th>Principal</th><th>Ativo no processo</th></tr>';
							
							for ($i=0; $i < $rowFiscais; $i++) { 
								db_fieldsmemory($resultFiscais, $i);

								$principal = ($y106_principal == 't') ? 'Sim' : 'Não';
								$ativo = ($ativo == 't') ? 'Sim' : 'Não';

								echo '<tr><td>'.$id_usuario.'</td>
								<td>'.$nome.'</td>
								<td>'.$principal.'</td>
								<td>'.$ativo.'</td></tr>';
							}
						} 
						// else {
						// 	echo '<table border="0" width="100%">';
						// 	echo '<tr><td align="center"> <b>Nenhum fiscal encontrado para este processo fiscal!.</b></td></tr>';
						// }
					if ($rowFiscais > 0) {
					?>
				</table>
			</fieldset>
			<?php  } ?>
    </td>
  </tr> 
  <tr>
    <td colspan = "2">
     &nbsp;
    </td>
  </tr> 
  

</table>
</form>
</center>
<?php 
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>

</body>
</html>

<script>
function js_abre(pagina){
  js_OpenJanelaIframe('','db_iframe_consulta',pagina,'Pesquisa',true);
}

function js_abre2(pagina2){
jan = window.open(pagina2,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
   jan.moveTo(0,0);

}

	
	
</script>

<?php 

/* Funções */


function consultaRelatorio ( $codigo = null ,$intimacao = null ){
        
    require_once(modification('classes/db_fis_fiscal_estendida_classe.php'));
    $clfiscal = new cl_fis_fiscal_estendida;

    $sGestor  = 'select * from fiscalizacao.fis_cadgestorfiscal where id_usuario = '.db_getsession('DB_id_usuario');
    $rsGestor = pg_query( $sGestor );
    $iGestor  = pg_num_rows( $rsGestor );

    if( isset( $intimacao ) && $intimacao == 1 ){
            $whereIntimacao = ' and y30_codnoti in(select in01_codnoti from fiscalizacao.fis_fiscalintimacao where in01_intimacao = true) ';
    }else{
            $whereIntimacao = ' and y30_codnoti in(select in01_codnoti from fiscalizacao.fis_fiscalintimacao where in01_intimacao = false) ';
    }

    $where2 .= ' where (case when (fis_grupotipoandamento.sequencial = 4 or fis_grupotipoandamento.sequencial = 2) then ' ;
    $where2 .= '  fis_processofiscalativo.fiscal = '.db_getsession('DB_id_usuario');
    $where2 .= " and (fis_datalimitefiscal.data is null or fis_datalimitefiscal.data > CURRENT_DATE) ";
    $where2 .= " and fis_processofiscalativo.ativo = 't' ";
	$where2 .= " when  y30_codnoti not in (select y49_codnoti from fiscalizacao.fis_fiscalandam) then";
	$where2 .= " db_usuarios.id_usuario =".db_getsession('DB_id_usuario');
	$where2 .= " and (fis_datalimitefiscal.data is null or fis_datalimitefiscal.data > CURRENT_DATE)";
	$where2 .= " and fis_processofiscalativo.ativo = 't'";
	$where2 .= " else 1=1 end ) ";
        

	$sql = $clfiscal->sql_query_info("","y30_codnoti"," y30_instit = ".db_getsession('DB_instit')." and y30_codnoti = ".$codigo ." and y30_setor=".db_getsession("DB_coddepto").$whereIntimacao, $where2);

 	$rsQuery = db_query( $sql );
	
	if( $rsQuery  && pg_num_rows($rsQuery) > 0){
                return true;
        }else{
                return false;
        }
}

function consultaRelatorioAuto ( $codigo ){

        require_once modification("classes/db_fis_auto_classe.php");
        $clauto = new cl_fis_auto;
        
        $where   = "";
        $where2  = "";
        $where2 .= ' where (case when (fis_grupotipoandamento.sequencial = 3) then ' ;
        $where2 .= ' (db_usuarios.id_usuario = '.db_getsession('DB_id_usuario');
        $where2 .= " and (fis_datalimitefiscal.data is null or fis_datalimitefiscal.data > CURRENT_DATE) ";
        $where2 .= " and fis_processofiscalativo.ativo = 't') or (y56_id_usuario = ".db_getsession('DB_id_usuario').")";
        $where2 .= " when  y50_codauto not in (select y58_codauto from fiscalizacao.fis_autoandam) then";
        $where2 .= " (db_usuarios.id_usuario =".db_getsession('DB_id_usuario');
        $where2 .= " and (fis_datalimitefiscal.data is null or fis_datalimitefiscal.data > CURRENT_DATE)";
        $where2 .= " and fis_processofiscalativo.ativo = 't') or (y56_id_usuario = ".db_getsession('DB_id_usuario').")";
        $where2 .= " else 1=1 end ) ";



        $sql = $clauto->sql_query_busca(null," y50_instit = ".db_getsession('DB_instit')." and dl_Auto = ". $codigo." and x.y50_setor=".db_getsession("DB_coddepto").$where.' order by dl_Auto desc', $where2);
        $rsQuery = db_query( $sql );
        if( $rsQuery  && pg_num_rows( $rsQuery ) > 0){

                return true;

        }else{

                return false;

        }

}

function consultaRelatorioLancamento ( $codigo ){

        require_once modification("classes/db_fis_lancamento_classe.php");
        $cllancamento = new cl_fis_lancamento;
        
        $where   = "";
        $where2  = "";
        $where2 .= ' where (case when (fis_grupotipoandamento.sequencial = 21) then ' ;
        $where2 .= ' (db_usuarios.id_usuario = '.db_getsession('DB_id_usuario');
        $where2 .= " and (fis_datalimitefiscal.data is null or fis_datalimitefiscal.data > CURRENT_DATE) ";
        $where2 .= " and fis_processofiscalativo.ativo = 't') or (nl14_id_usuario = ".db_getsession('DB_id_usuario').")";
        $where2 .= " when  nl01_codlanc not in (select nl19_codlanc from fiscalizacao.fis_lancandam) then";
        $where2 .= " (db_usuarios.id_usuario =".db_getsession('DB_id_usuario');
        $where2 .= " and (fis_datalimitefiscal.data is null or fis_datalimitefiscal.data > CURRENT_DATE)";
        $where2 .= " and fis_processofiscalativo.ativo = 't') or (nl14_id_usuario = ".db_getsession('DB_id_usuario').")";
        $where2 .= " else 1=1 end ) ";



        $sql = $cllancamento->sql_query_busca(null," nl01_instit = ".db_getsession('DB_instit')." and dl_Notificacao_Lancamento = ". $codigo." and x.nl01_setor=".db_getsession("DB_coddepto").$where.' order by dl_Notificacao_Lancamento desc', $where2);

        $rsQuery = db_query( $sql );       
        if( $rsQuery  && pg_num_rows( $rsQuery ) > 0){

                return true;

        }else{

                return false;

        }

}

function consultaRelatorioLevanta ( $codigo ){

	require_once(modification("classes/db_fis_levanta_estendida_classe.php"));
	$cllevanta = new cl_fis_levanta_estendida;

	$dataAtual = date('Y-m-d');
	$dbwhere = "";

	$where2  = ' where  ' ;
	$where2 .= ' (db_usuarios.id_usuario = '.db_getsession('DB_id_usuario');
	$where2 .= " and (fis_datalimitefiscal.data is null or fis_datalimitefiscal.data > '$dataAtual') ";
	$where2 .= " and fis_processofiscalativo.ativo = 't' or  ";
	$where2 .= " y60_importado is true )";
	$where2 .= " and y60_codlev = $codigo";
	$where2 .= " ";

	$sql = $cllevanta->sql_query_pesquisa(null,"*","",$dbwhere, $where2);
	$rsQuery = db_query( $sql );

	if ( pg_num_rows( $rsQuery ) > 0 ){

		return true;

	}else{

		return false;

	}
}
?>
