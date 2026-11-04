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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_classesgenericas.php"));
require_once(modification("dbforms/db_funcoes.php"));

db_postmemory($_POST);

$oRhLotaVincRubrica = new cl_rhlotavincrubrica;
$oRhLota            = new cl_rhlota;

$oIframeManutencao = new cl_iframe_alterar_excluir;


try {
    
    /*
     * Validacao dados do formulario
     */
    if (isset($incluir) || isset($alterar)) {
        
        if (empty($rh239_rhrubricas)) {
            $sMsgErro = "Informe a rubrica";
            throw new Exception($sMsgErro);
        }
        
        $oRhLotaVincRubrica->rh239_sequencial  = null;
        $oRhLotaVincRubrica->rh239_rhlota      = $rh239_rhlota;
        $oRhLotaVincRubrica->rh239_rhrubricas  = $rh239_rhrubricas;
        $oRhLotaVincRubrica->rh239_instituicao = db_getsession("DB_instit");
        
    }

    if(isset($incluir)) {
        
        db_inicio_transacao();

        $oRhLotaVincRubrica->incluir(null);
        if ($oRhLotaVincRubrica->erro_status == '0') {
            throw new Exception($oRhLotaVincRubrica->erro_msg);
        }
        
        db_fim_transacao();
        
    } else if (isset($alterar)) {
        
        db_inicio_transacao();
        
        $oRhLotaVincRubrica->rh239_sequencial = $rh239_sequencial;
        $oRhLotaVincRubrica->alterar($rh239_sequencial);
        if ($oRhLotaVincRubrica->erro_status == '0') {
            throw new Exception($oRhLotaVincRubrica->erro_msg);
        }
        
        db_fim_transacao();
        
        $db_opcao = 22;
        
        
    } else if (isset($excluir)) {
        
        db_inicio_transacao();
        
        $oRhLotaVincRubrica->excluir($rh239_sequencial);
        if ($oRhLotaVincRubrica->erro_status == '0') {
            throw new Exception($oRhLotaVincRubrica->erro_msg);
        }
        
        db_fim_transacao();
        
        $db_opcao = 33;
        
    }
    
} catch (Exception $oErro) {
    
    db_fim_transacao(true);
    
    $oRhLotaVincRubrica->erro_status = '0';
    $oRhLotaVincRubrica->erro_msg = $oErro->getMessage();
    
}

$db_opcao = 1;
$db_botao = true;

if (isset($opcao)) {
    
    if ($opcao == "alterar") {
        
        $db_botao = true;
        $db_opcao = 2;
        
    } else if ($opcao == "excluir") {
        
        $db_opcao = 3;
        $db_botao = true;
        
    }
    
}

/*
 * Buscamos os dados do registro escolhido para alteracao/exclusao
 */
if (isset($rh239_sequencial) && (!isset($excluir) && !isset($alterar) && !isset($incluir))) {
    
    $sSql = $oRhLotaVincRubrica->sql_query($rh239_sequencial, "rhlotavincrubrica.*, r70_descr, rh27_descr");
    $rsDados = $oRhLotaVincRubrica->sql_record($sSql);
    db_fieldsmemory($rsDados, 0);
}

if (isset($rh239_rhlota)) {
    
    $rsDados = $oRhLota->sql_record($oRhLota->sql_query_file($rh239_rhlota));
    db_fieldsmemory($rsDados, 0);
    
}

if (isset($exclusao) && $exclusao == "true") {
    $db_opcao = 33;
    $db_botao = false;
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
<body>
<div class="container">
<form name="form1" method="post" action="">
 <table class="form-container">
  <tr>
    <td>
      <table>
       <tr>
        <td>
         <fieldset>
           <legend>Vinculo Lotação/Rubricas</legend>
           <table border="0">
             <tr style="display: none">
               <td nowrap title="sequencial">
                  <b>Sequencial: </b>
               </td>
               <td> 
           	 <?php 
           	   db_input('rh239_sequencial',5,1,true,'text',3,""); 
           	 ?>
               </td>
             </tr>
             
             <tr>
               <td nowrap title="Lotação">
                  <b><?php db_ancora("Lotação:", 'js_pesquisaLotacao()', @$db_opcao); ?></b>
               </td>
               <td>
                 <?php
                  db_input('rh239_rhlota',5,1,true,'text',3,"");
                  db_input('r70_descr',40,0,true,'text',3,"");
           	     ?>
               </td>
             </tr>
             
             <tr>
               <td title="Rubrica">
                 <b><?php db_ancora("Rubrica:", 'js_pesquisaRubrica(true)', @$db_opcao); ?></b>
               </td>
               <td>
                 <?php
                   db_input("rh239_rhrubricas", 5, 1, true, "text", @$db_opcao, "onchange='js_pesquisaRubrica(false);'");
                   db_input("rh27_descr", 40, "", true, "text", 3);
                 ?>
               </td>
             </tr>
             
           </table>
         </fieldset>
        </td>
       </tr>
       <tr>
        <td align="center">
          <input name="<?php echo ($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" 
                 type="submit" 
                 id="db_opcao" 
                 value="<?php echo ($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>"
                 <?php echo ($db_botao==false?"disabled":"")?> >
          <input name="cancelar" type="button" id="cancelar" value="Cancelar" onclick="js_cancelarOperacao()">
        </td>
       </tr>
       <tr>
         <td height="100%">
             <table width="100%">
               <tr>
                 <td>
                   <?php
                   
                   if (isset($rh239_rhlota) && !empty($rh239_rhlota)) {
                       
                     $chavepri= array("rh239_sequencial"=>null);
                     $sWhereIframeManutencao = " rhlotavincrubrica.rh239_instituicao = ".db_getsession("DB_instit")." and rhlotavincrubrica.rh239_rhlota = {$rh239_rhlota}";
                     if(isset($rh239_sequencial) && trim($rh239_sequencial) != ""){
                         $sWhereIframeManutencao .= " and rhlotavincrubrica.rh239_sequencial <> ".$rh239_sequencial;
                         $chavepri= array("rh239_sequencial"=>$rh239_sequencial);
                     }
                     
                     $sCampos = "distinct rh239_sequencial,rh239_rhlota,r70_estrut,r70_descr,rh239_rhrubricas,rh27_descr";
                     $sSqlIframeManutencao = $oRhLotaVincRubrica->sql_query(null,$sCampos,null,$sWhereIframeManutencao);
                     
                     $oIframeManutencao->chavepri = $chavepri;
                     $oIframeManutencao->sql      = $sSqlIframeManutencao;
                     $oIframeManutencao->opcoes   = (($db_opcao == 3 || $db_opcao == 33)?4:1);
                     $oIframeManutencao->campos   = "rh239_sequencial,rh239_rhlota,r70_estrut,r70_descr,rh239_rhrubricas,rh27_descr";
                     $oIframeManutencao->legenda  = "Rubricas Vinculadas";
                     $oIframeManutencao->alignlegenda  = "left";
                     $oIframeManutencao->iframe_height = "350";
                     $oIframeManutencao->iframe_width  = "780";
                     $oIframeManutencao->iframe_alterar_excluir(1);
                     
                   }
                   ?>
                 </td>
               </tr>
             </table>
         </td>
       </tr>
      </table>
    </td>
  </tr>
 </table>
</form>
</div>
</body>
</html>
<script>
<?php
if (!isset($rh239_rhlota) || empty($rh239_rhlota)) {
  echo " js_pesquisaLotacao();";
}
?>
/**
 * Funções de Pesquisa Ancora 
 */
function js_pesquisaLotacao() {
	js_OpenJanelaIframe("",'db_iframe_lotacao',"func_rhlota.php?funcao_js=parent.js_preencheLotacao|r70_codigo&instit=<?php echo db_getsession('DB_instit')?>",'Pesquisa',true);
}

function js_preencheLotacao( iCodigo) {
  location.href='pes1_rhlotavincrubrica001.php?rh239_rhlota='+iCodigo;
}

function js_pesquisaRubrica( lMostra ) {

  if ( lMostra ) {
    js_OpenJanelaIframe("",'db_iframe_rubrica','func_rhrubricas.php?funcao_js=parent.js_preencheRubrica|rh27_rubric|rh27_descr&fixas=false','Pesquisa',true);
  } else {
    js_OpenJanelaIframe("",'db_iframe_rubrica','func_rhrubricas.php?pesquisa_chave='+document.form1.rhrubrica.value+'&funcao_js=parent.js_preencheRubrica1','Pesquisa',false);
  }
}

function js_preencheRubrica( iCodigo, sDescricao ) {

  document.form1.rh239_rhrubricas.value  = iCodigo;
  document.form1.rh27_descr.value = sDescricao;
  db_iframe_rubrica.hide();
}

function js_preencheRubrica1( sDescricao, lErro ) {

  if ( !lErro ) {
	document.form1.rh27_descr.value = sDescricao;
  } else {
	document.form1.rh239_rhrubricas.value = "";
	document.form1.rh27_descr.value = sDescricao;
  }    
}

function js_cancelarOperacao() {
  location.href='pes1_rhlotavincrubrica001.php?rh239_rhlota='+document.form1.rh239_rhlota.value;
}

</script>

<?php
if (isset ($incluir) || isset($alterar) || isset($excluir)) {
   db_msgbox($oRhLotaVincRubrica->erro_msg);
   db_redireciona("pes1_rhlotavincrubrica001.php?rh239_rhlota={$rh239_rhlota}");
}
?>