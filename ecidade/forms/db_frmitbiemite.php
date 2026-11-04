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

//MODULO: itbi
require_once(modification("classes/db_db_config_classe.php"));
$clitbicancela->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("it01_guia");
$cldb_config = new cl_db_config;
$clparitbi   = new cl_paritbi;

?>
<form name="form1" method="post" action="">
  <fieldset>
    <legend><b>Emite Guia de ITBI</b></legend>
    <table align="center" border="0">
      <tr>
        <td nowrap title="<?=@$Tit16_guia?>">
           <?php
             db_ancora(@$Lit01_guia,"js_pesquisait01_guia(true);",$db_opcao);
           ?>
        </td>
        <td> 
    			<?php
    			  db_input('it01_guia',10,$Iit01_guia,false,'text',3," onkeyup=\"js_onlyNumbers(this, event);\" onblur=\"js_onlyNumbers(this, event);\"");
    			?>
        </td>
      </tr>

      <?php
      $rsparitbi = $clparitbi->sql_record($clparitbi->sql_query_file(db_getsession("DB_anousu"), "it24_emiteguiaquitacao"));
      db_fieldsmemory($rsparitbi, 0);
      $bEmiteDeclarQuit = $it24_emiteguiaquitacao == 't' ? true : false;
      ?>
      <tr>
        <td align='right'>
          <b>Tipo :</b>
        </td>
        <td colspan='3'>
          <?php
            if ($bEmiteDeclarQuit) {
                $aTipo = array( 'n'=>'Guia normal',
                                'q'=>'Declaração de quitação' );
            } else {
              $aTipo = array( 'n'=>'Guia normal' );
            }
            db_select('tipoguia',$aTipo,true,2," style='width:275px;'"); 
          ?>          
        </td>
      </tr>

    </table>
  </fieldset>
  <input name="emitirguia" type="button" id="emitirguia" value="Emitir Guia" <?= ($db_botao == false ? "disabled" : "") ?>
    onClick="return js_emitir()" />
</form>
<script>
  function js_onlyNumbers(oObjeto, e) {

    oObjeto.value = oObjeto.value.replace(/[^0-9]/g, '');
  }

  var mensagem = "tributario.itbi.db_frmitbiemite.";

  function js_emitir() {

    if (isNaN($F('it01_guia')) || $F('it01_guia') == '') {

      alert(_M(mensagem + "codigo_itbi_invalido"));
      $('it01_guia').value = '';
      $('it01_guia').focus();
      return false;
    }


    var sArquivo = "reciboitbi.php";
    var tipoguia = $F('tipoguia');

    if (tipoguia == 'q') {

      sArquivo = "reciboitbiGuiaQuitacao.php";
    }



    var iGuia = document.form1.it01_guia.value;
    var iTipo = document.form1.tipoguia.value;
    var sParam = "toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,height=" +
      (screen.height - 100) + ",width=" + (screen.width - 100);

    if (iGuia != "") {
      window.open(sArquivo + '?itbi=' + iGuia + '&tipoguia=' + iTipo, "", sParam);
      return true;
    } else {
      alert('Escolha uma guia para emissão!')
      return false;
    }
  }

  function js_pesquisait01_guia(mostra) {
    if (mostra == true) {
      var sUrl = 'func_itbiliberadomostrarguias.php?mostrarguias=l&funcao_js=parent.js_mostraitbi1|it01_guia|it01_guia';
      js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframeitbi', sUrl, 'Pesquisa', true);
    }
  }

  function js_mostraitbi1(chave1, chave2) {
    document.form1.it01_guia.value = chave1;
    db_iframeitbi.hide();
  }
</script>
