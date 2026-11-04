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

//MODULO: fiscal
require_once(modification("classes/db_fis_tipofiscaliza_classe.php"));
require_once(modification("classes/db_db_depart_classe.php"));
require_once(modification("classes/db_fis_autotipo_classe.php"));
require_once(modification("classes/db_fis_fiscalprocrec_classe.php"));
require_once(modification("classes/db_fis_fandam_classe.php"));

$cltipofiscaliza = new cl_fis_tipofiscaliza;
$cldb_depart     = new cl_db_depart;
$clautotipo      = new cl_fis_autotipo;
$clfiscalprocrec = new cl_fis_fiscalprocrec;
$clfandam        = new cl_fis_fandam;

$clautotipo->rotulo->label();
$clfandam->rotulo->label();

$clrotulo = new rotulocampo;

db_app::load("scripts.js, strings.js, datagrid.widget.js, windowAux.widget.js,dbautocomplete.widget.js");
db_app::load("dbmessageBoard.widget.js, prototype.js, dbtextField.widget.js, dbcomboBox.widget.js");
db_app::load("estilos.css, grid.style.css, AjaxRequest.js");
?>
<form name="form1" method="post" action="">
  <fieldset style="margin: 40px auto 10px; width: 700px;">
    <legend>
      <strong>Emissão em Lote</strong>
    </legend>
    <table border="0" width="100%">
    <tr>
      <td nowrap title="Lote">
      <?php 
        db_ancora("<b>Lote:</b>","js_lote(true);",1);
      ?>
      </td> 
      <td>  
      <?php 
        db_input('y122_codigo',6,'',true,'text',1," onchange='js_lote(false);'");
        db_input('y122_descr',40,'',true,'text',3);
      ?>
      </td>
    </tr>
    </table>
  </fieldset>
  <center>
    <input name="imprimir" type="submit" id="imprimir" value="Imprimir">
  </center>

</form>
<script type="text/javascript">

function js_lote(lMostra) {

    if (lMostra == true) {
      js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_lote', 'func_fis_levantlotearq.php?funcao_js=parent.js_preencheLote|t43_codlote|y29_tipofisc|tipopeca|y122_pecafiscal', 'Pesquisa Lote', true);
    } else {
      if (document.form1.y122_codigo.value != '') {
        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_lote', 'func_fis_levantlotearq.php?pesquisa_chave=' +document.form1.y122_codigo.value+ '&funcao_js=parent.js_mostraLote', 'Pesquisa Lote', false);
      } else {
        document.form1.y122_descr.value = '';
        js_limpacampos();
      }
    }
}


function js_preencheLote(codigo, descricao, objpeca, objpecafiscal) {
  document.form1.y122_codigo.value = codigo;
  document.form1.y122_descr.value  = descricao;
  db_iframe_lote.hide();
}

function js_mostraLote(sDescricao, objpeca, objpecafiscal, lErro) {

  if (lErro) {
    document.form1.y122_codigo.value = "";
    document.form1.y122_descr.value  = "";
    document.form1.tipoPeca.value    = "";
    document.form1.linha.value       = "";
    document.form1.pecafiscal.value  = "";
    return false; 
  }

  document.form1.y122_codigo.focus();
  document.form1.y122_descr.value  = sDescricao;
}

</script>