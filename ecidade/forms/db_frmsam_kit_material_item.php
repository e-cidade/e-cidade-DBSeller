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

//MODULO: Samu
$oDaoKitMaterialItem->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("m60_descr");
$clrotulo->label("sm03_descr");
$clrotulo->label("sm04_kit_material");

?>
<fieldset><legend><b>Materiais:</b></legend>
  <form name="form1" id="form1" method="post" action="">
    <center>
      <table border="0">
        <tr>
          <td nowrap title="<?=@$Tsm04_sequencial?>">
            <?=@$Lsm04_sequencial?>
          </td>
          <td> 
            <?php
            db_input('sm04_sequencial',10,$Ism04_sequencial,true,'text',3,"")
            ?>
          </td>
        </tr>
        <tr>
          <td nowrap title="<?=@$Tsm04_kit_material?>">
            <?php
            db_ancora(@$Lsm04_kit_material,"js_pesquisasm04_kit_material(true);",3);
            ?>
          </td>
          <td> 
            <?php
            db_input('sm04_kit_material',10,$Ism04_kit_material,true,'text',3," onchange='js_pesquisasm04_kit_material(false);'")
            ?>
            <?php
            db_input('sm03_descr',40,$Ism03_descr,true,'text',3,'')
            ?>
          </td>
        </tr>
        <tr>
          <td nowrap title="<?=@$Tsm04_material?>">
            <?php
            db_ancora(@$Lsm04_material,"js_pesquisasm04_material(true);",$db_opcao);
            ?>
          </td>
          <td> 
            <?php
            db_input('sm04_material',10,$Ism04_material,true,'text',$db_opcao," onchange='js_pesquisasm04_material(false);'")
            ?>
            <?php
            db_input('m60_descr',40,$Im60_descr,true,'text',3,'')
            ?>
          </td>
        </tr>
        <tr>
          <td nowrap title="<?=@$Tsm04_quantidade?>">
            <?=@$Lsm04_quantidade?>
          </td>
          <td> 
            <?php
            db_input('sm04_quantidade',10,$Ism04_quantidade,true,'text',$db_opcao,"onchange ='js_validaValores();'")
            ?>
          </td>
        </tr>
      </table>
    </center>
    <input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" 
       type="submit" 
       id="db_opcao" 
       value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>"
       <?=($db_botao==false?"disabled":"")?> > 
    <input name="cancelar" type="button" id="pesquisar" value="Cancelar" onclick="js_cancelar();" >

<?php
  //Grid Aterar/Excluir

  if (isset($sm04_kit_material) && @$sm04_kit_material != "") {
    
    $sCampos = " sm04_sequencial, m60_descr, sm04_quantidade";
    $sWhere  = " sm04_kit_material = $sm04_kit_material ";
    $oGridAltExc->sql = $oDaoKitMaterialItem->sql_query(null,$sCampos,null,$sWhere);

  }
  $chavepri                   = array("sm04_sequencial"=>@$sm04_sequencial);
  $oGridAltExc->chavepri      = $chavepri;
  $oGridAltExc->legenda       = "Registros Materiais";
  $oGridAltExc->campos        = "sm04_sequencial, m60_descr, sm04_quantidade";
  $oGridAltExc->msg_vazio     = "Não foi encontrado nenhum registro.";
  $oGridAltExc->textocabec    = "darkblue";
  $oGridAltExc->textocorpo    = "black";
  $oGridAltExc->fundocabec    = "#aacccc";
  $oGridAltExc->fundocorpo    = "#ccddcc";
  $oGridAltExc->iframe_width  = "100%";
  $oGridAltExc->iframe_height = "130";
  $oGridAltExc->opcoes        = 1;
  $oGridAltExc->iframe_alterar_excluir(1);
  
?>

</form>
<script>
function js_validaValores() {
  sDoc        = document.form1.sm04_quantidade;
  iQtd        = sDoc.value;
  sValorBotao = document.form1.db_opcao.value;
  sIncluir    = "Incluir";

  if (sValorBotao == sIncluir && iQtd <= 0) {

    alert("Não podem ser adicionados valores nulos");
    sDoc.value = '';
    sDoc.focus();

  }


}

function js_cancelar() {
  
  sStr = '?sm04_kit_material=<?=$sm04_kit_material?>&sm03_descr=<?=$sm03_descr?>';
  document.location.href = 'sam1_sam_kit_material_item001.php'+sStr;

}

function js_pesquisasm04_material(mostra) {

  if (mostra == true ) {

    js_OpenJanelaIframe('','db_iframe_matmater','func_matmater.php?funcao_js=parent.js_mostramatmater1|m60_codmater|m60_descr','Pesquisa',true);
  } else {

     if ($('sm04_material').value != '') {

        js_OpenJanelaIframe('','db_iframe_matmater','func_matmater.php?pesquisa_chave='+$('sm04_material').value
                                +'&funcao_js=parent.js_mostramatmater','Pesquisa',false);
     }else{
       $('m60_descr').value = ''; 
     }
  }
}
function js_mostramatmater(chave,erro){

  $('m60_descr').value = chave; 
  if (erro == true) {

    $('sm04_material').focus(); 
    $('sm04_material').value = '';

  }

}

function js_mostramatmater1(chave1,chave2) {

  $('sm04_material').value = chave1;
  $('m60_descr').value     = chave2;
  db_iframe_matmater.hide();

}
</script>