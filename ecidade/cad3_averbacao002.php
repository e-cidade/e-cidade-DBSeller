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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_averbacao_classe.php"));
require_once(modification("classes/db_averbacgm_classe.php"));
require_once(modification("classes/db_averbacgmold_classe.php"));
require_once(modification("classes/db_averbaregimovel_classe.php"));
require_once(modification("classes/db_averbaprocesso_classe.php"));
require_once(modification("classes/db_iptubase_classe.php"));
require_once(modification("classes/db_propri_classe.php"));
require_once(modification("classes/db_promitente_classe.php"));
require_once(modification("classes/db_averbatipo_classe.php"));
require_once(modification("classes/db_averbaformalpartilha_classe.php"));
require_once(modification("classes/db_averbadecisaojudicial_classe.php"));
require_once(modification("classes/db_averbaguia_classe.php"));
require_once(modification("classes/db_cgm_classe.php"));
require_once(modification("classes/db_averbaescritura_classe.php"));

$claverbaescritura           = new cl_averbaescritura;
$claverbadecisaojudicial     = new cl_averbadecisaojudicial;
$claverbaformalpartilha      = new cl_averbaformalpartilha;
$claverbaguia                = new cl_averbaguia;
$clcgm                       = new cl_cgm;
$claverbaregimovel           = new cl_averbaregimovel;
$claverbaprocesso            = new cl_averbaprocesso;
$claverbacao                 = new cl_averbacao;
$claverbatipo                = new cl_averbatipo;
$claverbacgm                 = new cl_averbacgm;
$claverbacgmold              = new cl_averbacgmold;
$cliptubase                  = new cl_iptubase;
$clpropri                    = new cl_propri;
$clpromitente                = new cl_promitente;

db_postmemory($HTTP_POST_VARS);

$claverbaformalpartilha->rotulo->label();
$claverbaescritura->rotulo->label();
$claverbadecisaojudicial->rotulo->label();
$clcgm->rotulo->label();
$claverbaguia->rotulo->label();

if(isset($codigo)){
   $db_opcao = 3;
   $db_botao = true;
	 $chavepesquisa = $codigo;
	 $result = $claverbacao->sql_record($claverbacao->sql_query($chavepesquisa));
   db_fieldsmemory($result,0);
   $result_proc = $claverbaprocesso->sql_record($claverbaprocesso->sql_query($j75_codigo,"j77_codproc,p58_requer, p58_numero"));
   if ($claverbaprocesso->numrows>0){
   	db_fieldsmemory($result_proc,0);
   }
   $result_reg = $claverbaregimovel->sql_record($claverbaregimovel->sql_query($j75_codigo));
   if ($claverbaregimovel->numrows>0){
   	db_fieldsmemory($result_reg,0);
   }
   $result_escr = $claverbaescritura->sql_record($claverbaescritura->sql_query(null,"*",null,"j94_averbacao =".$j75_codigo));
  	if ($claverbaescritura->numrows>0){
  		db_fieldsmemory($result_escr,0);
  	}
	if($j93_averbagrupo == 6){
	  $sqlguia = "select averbaguia.*,averbaguiaitbi.*,it03_nome
							  from averbaguia
							  left join averbaguiaitbi on j104_sequencial=j103_averbaguia
							  left join itbinome on j104_guia = it03_guia
							  where j104_averbacao = $chavepesquisa
								      and upper(it03_tipo) = 'C'
											and it03_princ is true ";

	  $resultguia = db_query($sqlguia);
	  $linhasguia = pg_num_rows($resultguia);
	  if($linhasguia>0){
		db_fieldsmemory($resultguia,0);
		$nome = $it03_nome;
		$guia=1;
	  }else{
	  	//se não encotrar é pr é sem guia itbi
			$sqlGuiaSemItbi = "select * from averbaguia where j104_averbacao = $chavepesquisa ";
			$rsGuiaSemItbi  = db_query($sqlGuiaSemItbi);
			$linhasGuiaSemItbi = pg_num_rows($rsGuiaSemItbi);
			if($linhasGuiaSemItbi>0){
				db_fieldsmemory($rsGuiaSemItbi,0);
				$guianao = $j104_guia;
			  $guia = 2;
			}
	  }
	}
	if($j93_averbagrupo == 5){
	  $sqlsentenca = "select * from averbadecisaojudicial
	                  where j101_averbacao = $chavepesquisa";
      $resultsentenca = db_query($sqlsentenca);
	  $linhassentenca = pg_num_rows($resultsentenca);
	  if($linhassentenca>0){
		db_fieldsmemory($resultsentenca,0);

	  }
	}
	if($j93_averbagrupo == 4){
	  $sqlformal = "select * 
                    from averbaformalpartilha
	             left join averbaformalpartilhacgm on j102_averbaformalpartilha = j100_sequencial
	                 where j100_averbacao = $chavepesquisa";
	  $resultformal = db_query($sqlformal);
	  $linhasformal = pg_num_rows($resultformal);
	  if($linhasformal>0){
		db_fieldsmemory($resultformal,0);
		$z01_numcgm1 = $j102_numcgm;
	  }

	}
   if ($j75_situacao==2){
   	$db_opcao = 3;
   	$db_botao = false;
   }
$sql="select nome as j75_responsavel_a from db_usuarios where id_usuario=$j75_responsavel";

db_fieldsmemory(db_query($sql),0);

}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >

<form name="form1" method="post" action="" class="container">

	<?php
	$cadastromunicipal = true;
	include(modification("forms/db_frmaverbacao.php"));
	?>

  	<iframe name="itens" id="itens" src="cad3_averbacgm001.php?codigo=<?=@$codigo?>" class="container" width="720" height="300" marginwidth="0" marginheight="0" frameborder="0"></iframe>

</form>

</body>
</html>
