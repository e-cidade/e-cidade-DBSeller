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
require_once(modification("classes/db_rhfuncao_classe.php"));
require_once(modification("classes/db_rhregime_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_sql.php"));
parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
db_postmemory($HTTP_POST_VARS);

$clrhfuncao = new cl_rhfuncao;
$clrhregime = new cl_rhregime;
$clrhfuncao->rotulo->label();
$clrotulo = new rotulocampo;
$saldo = 0;

$selecao_cargo = "";
$lotacao_cargo = "";

if(isset($selecao)){
  $selecao_cargo = $selecao;
}

if(isset($lotacao)){
  $lotacao_cargo = $lotacao;
}

if(!isset($ano) || (isset($ano) && trim($ano)=="")){
  $ano = db_anofolha();
}
if(!isset($mes) || (isset($mes) && trim($mes)=="")){
  $mes = db_mesfolha();
}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
<style type="text/css">
<!--
.tabcols {
  font-size:11px;
}
.tabcols1 {
  text-align: right;
  font-size:11px;
}
.btcols {
	height: 17px;
	font-size:10px;
}
.links {
	font-weight: bold;
	color: #0033FF;
	text-decoration: none;
	font-size:10px;
    cursor: hand;
}
a.links:hover {
    color:black;
	text-decoration: underline;
}
.links2 {
	font-weight: bold;
	color: #0587CD;
	text-decoration: none;
	font-size:10px;
}
a.links2:hover {
    color:black;
	text-decoration: underline;
}
.nome {
  color:black;
}
a.nome:hover {
  color:blue;
}
-->
</style>

<script>
function js_MudaLink(nome) {
  document.getElementById('processando').style.visibility = 'visible';
  document.getElementById('processandoTD').innerHTML = '<h3>Aguarde, processando ...</h3>';
  for(i = 0;i < document.links.length;i++) {
    var L = document.links[i].id;
	if(L!=""){
 	  document.getElementById(L).style.backgroundColor = '#CCCCCC';
	  document.getElementById(L).hideFocus = true;
	}
  }
  document.getElementById(nome).style.backgroundColor = '#E8EE6F';
}

function js_relatorio(){

    var sFonte1 = "pes2_consrhfuncao002.php";
    var sFonte2 = "pes2_consrhfuncao003.php";
    var sFormatoEmissao = document.form1.tipoimpressao.value;

    if (sFormatoEmissao == "pdf"){

      <?php

        if(!empty($funcao)) {

          $jan  = "jan = window.open(sFonte2+";
          $jan .= "'?funcao='+document.form1.rh37_funcao.value+";
          $jan .= "'&ano=$ano&mes=$mes&colunas1=".@$colunas1."'+";
          $jan .= "'&lotacao=$lotacao_cargo'+";
          $jan .= "'&selecao=$selecao_cargo'+";
          $jan .= "'&formato_emissao='+sFormatoEmissao,";
          $jan .= "'width='+(screen.availWidth-5)+',height='+(screen.availHeight-40));";

          echo $jan;

        } else {

          $jan  = "jan = window.open(sFonte1+";
          $jan .= "'?ano=$ano&mes=$mes&colunas1=".@$colunas1."'+";
          $jan .= "'&lotacao=$lotacao_cargo'+";
          $jan .= "'&selecao=$selecao_cargo'+";
          $jan .= "'&formato_emissao='+sFormatoEmissao,";
          $jan .= "'width='+(screen.availWidth-5)+',height='+(screen.availHeight-40));";

          echo $jan;
        }

        ?>


    }
    else{

        var sAjaxUrl = "";
        var sNomeArquivo = '<?php echo "consrhfuncao".date("YmdHis").db_getsession("DB_id_usuario").".csv" ?>';
        var lFuncao = '<?php echo !empty($funcao) ? "true" : "false"; ?>';

        sAjaxUrl     = sFonte1;
        sAjaxUrl    += '?formato_emissao='+sFormatoEmissao;

        if (lFuncao == 'true'){
            sAjaxUrl     = sFonte2;
            sAjaxUrl    += '?formato_emissao='+sFormatoEmissao;
            sAjaxUrl    += '&funcao='+document.form1.rh37_funcao.value;
        }

        sAjaxUrl    += '&lotacao='+'<?php echo $lotacao_cargo?>';
        sAjaxUrl    += '&selecao='+'<?php echo $selecao_cargo?>';
        sAjaxUrl    += '&ano='+'<?php echo @$ano?>';
        sAjaxUrl    += '&mes='+'<?php echo @$mes?>';
        sAjaxUrl    += '&colunas1='+'<?php echo @$colunas1?>';

        js_divCarregando('Aguarde... Carregando Documento CSV','msgbox');
        var oAjax        = new Ajax.Request(
                          sAjaxUrl,
                          {
                            parameters:{
                              'sNomeArquivo':sNomeArquivo
                            },
                            method: 'get',
                            asynchronous:false,
                            onComplete : mostraArquivoCsv
                          });

    }
}

function mostraArquivoCsv(oAjax){

    js_removeObj("msgbox");

    if (oAjax.status == 200) {
        sNomeArquivo = oAjax.request.parameters.sNomeArquivo;
        sCaminhoArquivo = "tmp/"+sNomeArquivo;
        var oDownload = new DBDownload();
        oDownload.addFile(sCaminhoArquivo, sNomeArquivo);
        oDownload.show();

    } else {
       alert("Problema ao carregar CSV");
    }

}

</script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<div id="DDD"></div>
<div id="processando" style="position:absolute; left:25px; top:107px; width:975px; height:400px; z-index:1; visibility: hidden; background-color: #FFFFFF; layer-background-color: #FFFFFF; border: 1px none #000000;">
<Table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" valign="middle" id="processandoTD" onclick="document.getElementById('processando').style.visibility='hidden'">
    </td>
  </tr>
</Table>
</div>
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr>
    <td width="360">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>

    <center>
    <?php
	  if(isset($funcao) && trim($funcao)!=""){

      $where = " ";
       if(isset($colunas1) && $colunas1!=""){
         $where .= " and rh30_codreg in (".$colunas1.") ";
       }

       if (isset($lotacao) && !empty($lotacao)){
        $where .= " and rhlota.r70_codigo = $lotacao ";
       }

      //verificamos se foi informada selecao, buscamos a condicao e aplicamos na consulta
      if(isset($selecao) && !empty($selecao)) {

        $oSelecao = new Selecao($selecao);
        $where .= " and rhpessoalmov.rh02_regist in (select rhpessoalmov.rh02_regist
                                          from rhpessoal
                                               inner join rhpessoalmov   on rhpessoal.rh01_regist     = rhpessoalmov.rh02_regist
                                                                        and rhpessoalmov.rh02_anousu  = " . $ano. "
                                                                        and rhpessoalmov.rh02_mesusu  = " . $mes . "
                                                                        and rhpessoalmov.rh02_instit  = " . db_getsession("DB_instit") . "
                                               left join  rhlota         on rhlota.r70_codigo         = rhpessoalmov.rh02_lota
                                                                        and rhlota.r70_instit         = rhpessoalmov.rh02_instit
                                               left join  rhregime       on rhregime.rh30_codreg      = rhpessoalmov.rh02_codreg
                                               left join  rhpescargo     on rhpescargo.rh20_seqpes    = rhpessoalmov.rh02_seqpes
                                               left join  rhpespadrao    on rhpespadrao.rh03_seqpes   = rhpessoalmov.rh02_seqpes
                                                                        and rhpespadrao.rh03_anousu   = rhpessoalmov.rh02_anousu
                                                                        and rhpespadrao.rh03_mesusu   = rhpessoalmov.rh02_mesusu
                                               left join  rhpesrescisao  on rhpesrescisao.rh05_seqpes = rhpessoalmov.rh02_seqpes
                                        where " . $oSelecao->getWhere() . ")";

      }



	  	$porfuncao = true;

        $sql1 = "
				select
           rh37_funcao,
           rh37_descr,
           rh37_vagas,
           count(rh01_regist) as ocupados,
           rh30_vinculo as r01_tpvinc
     from rhfuncao
          inner join rhpessoalmov  on rhpessoalmov.rh02_funcao  = rhfuncao.rh37_funcao
		                              and rhpessoalmov.rh02_anousu  = $ano
		                              and rhpessoalmov.rh02_mesusu  = $mes
		                              and rhpessoalmov.rh02_instit  = ".db_getsession("DB_instit")."
		      inner join rhpessoal     on rhpessoal.rh01_regist     = rhpessoalmov.rh02_regist
          left  join rhpesrescisao on rhpesrescisao.rh05_seqpes = rhpessoalmov.rh02_seqpes
          inner join rhregime  on rhregime.rh30_codreg  = rhpessoalmov.rh02_codreg
		                          and rhregime.rh30_instit  = rhpessoalmov.rh02_instit
          inner join cgm       on cgm.z01_numcgm        = rhpessoal.rh01_numcgm
          inner join rhlota    on rhlota.r70_codigo     = rhpessoalmov.rh02_lota
		                          and rhlota.r70_instit     = rhpessoalmov.rh02_instit
          where rh37_funcao = $funcao
			      and rh37_instit = ".db_getsession("DB_instit")."
           and rh05_seqpes is null
					 $where
           group by
               rh37_funcao,
               rh37_descr,
               rh30_vinculo,
               rh37_vagas
		";
		 $result_funcao = db_query($sql1);
        if(pg_numrows($result_funcao) == 0){
      	  db_msgbox("Cargo não encontrado");
      	  echo "<script>location.href = 'pes3_consrhfuncao001.php'</script>";
        }else{
          db_fieldsmemory($result_funcao,0);
          $ocup = 0;
          for($i=0;$i<pg_numrows($result_funcao);$i++){
            db_fieldsmemory($result_funcao,$i);
            $ocup += $ocupados;
          }
          if($rh37_vagas != 0){
             $saldo = $rh37_vagas - $ocup;
          }
          $ocupados = $ocup;
        }
	  }else{
	  	$porfuncao = false;
      $result_funcoes = $clrhfuncao->sql_record($clrhfuncao->sql_query_file(null,db_getsession("DB_instit"),"rh37_funcao,
                                                                                                               rh37_descr,
                                                                                                               rh37_vagas",
                                                                                                            "rh37_funcao"));
      if($clrhfuncao->numrows == 0){
        db_msgbox("Nenhum cargo encontrado");
        echo "<script>location.href = 'pes3_consrhfuncao001.php'</script>";
      }
	  }

   $result_regime = null;
   if (!empty($colunas1)){
    $result_regime = $clrhregime->sql_record($clrhregime->sql_query_file(null, "rh30_vinculo","", " rh30_instit = ".db_getsession('DB_instit')." and rh30_codreg in (".$colunas1.")"));
   }

   $colunas = "";
   $virgula = "";
   for($x = 0; $x < $clrhregime->numrows; $x ++) {
     db_fieldsmemory($result_regime, $x);
     $colunas .= $virgula.strtolower($rh30_vinculo);
     $virgula = ",";
   }
	?>
	    <form name='form1'>
        <table width="100%" height="90%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td colspan="2">
	          <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                   <?php
                   if($porfuncao == true){
                   ?>
                     <td nowrap class="tabcols" width="10%" align="right">
                       <strong style=\"color:blue\">
                         <?php
                         db_ancora("$Lrh37_funcao","","3");
                         ?>
                       </strong>
                     </td>
                     <td class="tabcols" nowrap width="30%">
                       <?php
                       db_input('rh37_funcao', 8, $Irh37_funcao, true, 'text', 3);
                       ?>
                       <?php
                       db_input('rh37_descr', 30, $Irh37_descr, true, 'text', 3);
                       ?>
                     </td>
                     <td width="60%">
                       <table border="0" cellspacing="0" cellpadding="0">
                         <tr>
                           <td colspan="2"></td>
                         </tr>
                         <tr>
                           <td colspan="2"></td>
                         </tr>
                         <tr>
                           <td nowrap class="tabcols" align="right">
                             <strong class="links2">
                               VAGAS:  &nbsp;&nbsp;
                             </strong>
                           </td>
                           <td nowrap class="tabcols" align="right">
                             <strong  class="links2">
                               <?php echo $rh37_vagas?>
                             </strong>
                           </td>
                         </tr>
                         <tr>
                           <td class="tabcols" nowrap align="right">
                             <strong  class="links2">
                               OCUPADAS: &nbsp;&nbsp;
                             </strong>
                           </td>
                           <td nowrap class="tabcols" align="right">
                             <strong  class="links2">
                               <?php echo $ocupados?>
                             </strong>
                           </td>
                         </tr>
                         <tr>
                           <td class="tabcols" nowrap align="right">
                             <strong  class="links2">
                               SALDO: &nbsp;&nbsp;
                             </strong>
                           </td>
                           <td nowrap class="tabcols" align="right">
                             <strong  class="links2">
                               <?php echo $saldo?>
                             </strong>
                           </td>
                         </tr>
                         <tr>
                           <td class="tabcols" nowrap align="right" colspan="2">
                             <strong  class="links2">
                               <?php
                               db_ancora("VER CARGOS","location.href = 'pes3_consrhfuncao002.php';","1");
                               ?>
                             </strong>
                           </td>
                         </tr>
                       </table>
                     </td>
                   <?php
                   }else{
                   ?>
                     <td nowrap class="tabcols">
                       <BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                       <b>TODOS OS CARGOS</b>
                     </td>
                   <?php
                   }
                   ?>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td colspan="2" align="center"  height="90%"  valign="middle">
	          <table width="100%" height="90%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="center">
                    <?php
                    $qry = "";
                    $rog = "?";
                    if(isset($funcao) && trim($funcao)!=""){
                      $qry .= $rog."funcao=$funcao";
                      $rog = "&";
                    }
                    if(isset($ano) && trim($ano)!=""){
                      $qry .= $rog."ano=$ano";
                      $rog = "&";
                    }
                    if(isset($mes) && trim($mes)!=""){
                      $qry .= $rog."mes=$mes";
                      $rog = "&";
                    }
                    if(isset($colunas1) && trim($colunas1)!=""){
                      $qry .= $rog."colunas1=$colunas1";
                      $rog = "&";
                    }

                    if(isset($lotacao) && !empty($lotacao)){
                      $qry .= $rog."lotacao=$lotacao";
                      $rog = "&";
                    }

                    if(isset($selecao) && !empty($selecao)){
                      $qry .= $rog."selecao=$selecao";
                      $rog = "&";
                    }

                    ?>
                    <iframe id="registros" height="95%" width="95%" name="registros" src="pes3_consrhfuncao021.php<?php echo $qry?>"></iframe>
                    <?php
                    if(isset($funcao) && trim($funcao)!=""){
                    ?>
                    <input type="hidden" name="funcao"  value="<?php echo $funcao?>">
                    <?php
                    }
                    ?>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td colspan="2" align="center">
              <input name="retornar" type="button" id="retornar" value="Nova Pesquisa" title="Inicio da Consulta" onclick="location.href='pes3_consrhfuncao001.php'">
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              <input name="pesquisar" type="submit" id="pesquisar"  title="Atualiza a Consulta" value="Atualizar">
              &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;
              <label><b>Tipo da geração</b></label>
              <?php
              $aOpcoesFiltro = array(
                'pdf' => 'PDF',
                'csv' => 'CSV',
              );
              ?>
              <?php db_select("tipoimpressao", $aOpcoesFiltro, true, 1); ?>
	            &nbsp;&nbsp;
              <input name="imprimir" type="button" id="imprimir" value="Gerar" title="Imprimir" onclick="js_relatorio();">
              <strong>
                &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;
                Período:
              </strong>
              &nbsp;&nbsp;
       	      <?php
    	      db_input("ano",4,'',true,'text',4)
	          ?>
	          &nbsp;/&nbsp;
	          <?php
    	      db_input("mes",2,'',true,'text',4);
    	      db_input("colunas1",2,'',true,'hidden',3);
	          ?>
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
