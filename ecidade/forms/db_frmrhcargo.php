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
//MODULO: pessoal
if (!isset($rh04_datainicial_dia)) {
    $rh04_datainicial_dia = '';
}
if (!isset($rh04_datainicial_mes)) {
    $rh04_datainicial_mes = '';
}
if (!isset($rh04_datainicial_ano)) {
    $rh04_datainicial_ano = '';
}
if (!isset($rh04_datafinal_dia)) {
    $rh04_datafinal_dia = '';
}
if (!isset($rh04_datafinal_mes)) {
    $rh04_datafinal_mes = '';
}
if (!isset($rh04_datafinal_ano)) {
    $rh04_datafinal_ano = '';
}

if (isset($rh04_datainicial)) {
    $dataInicio = $rh04_datainicial;
    if (!empty($dataInicio)) {
        $dataInicio = new DBDate($dataInicio);
        $rh04_datainicial = $dataInicio->convertTo(DBDate::DATA_PTBR);
        $rh04_datainicial_dia = $dataInicio->getDia();
        $rh04_datainicial_mes = $dataInicio->getMes();
        $rh04_datainicial_ano = $dataInicio->getAno();
    }
}

if (isset($rh04_datafinal)) {
    $dataFinal = $rh04_datafinal;
    if (!empty($dataFinal)) {
        $dataFinal = new DBDate($dataFinal);
        $rh04_datafinal = $dataFinal->convertTo(DBDate::DATA_PTBR);
        $rh04_datafinal_dia = $dataFinal->getDia();
        $rh04_datafinal_mes = $dataFinal->getMes();
        $rh04_datafinal_ano = $dataFinal->getAno();
    }
}

$clrhcargo->rotulo->label();
$labelButton = ($db_opcao == 1 ? "incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "alterar" : "excluir"));
?>
<div class='container'>
  <form name="form1" method="post" action="" onsubmit="return validarDados()">
   <fieldset>
      <legend><?= $labelLegend ?></legend>
      <fieldset style="border:none;">
        <table class="form-container">
          <tr>
            <td nowrap title="<?= $Trh04_codigo ?>">
              <?= $Lrh04_codigo ?>
            </td>
            <td> 
              <?php db_input('rh04_codigo', 5, $Irh04_codigo, true, 'text', 3, "") ?>
            </td>
          </tr>
          <tr>
            <td nowrap title="<?= $Trh04_descr ?>">
              <?= $Lrh04_descr ?>
            </td>
            <td>
              <?php db_input('rh04_descr', 40, $Irh04_descr, true, 'text', $db_opcao, "") ?>
            </td>
          </tr>
        </table>
       </fieldset>
       <fieldset class="separator">
         <legend>Dados eSocial</legend>
          <table class="form-container">
            <tr>
              <td title="Data de iní­cio da validade das informações para o eSocial." >
                  <label for="rh04_datainicial">Inicio de validade: </label>
              </td>
              <td>
                <?php
                  db_inputdata('rh04_datainicial', $rh04_datainicial_dia, $rh04_datainicial_mes, $rh04_datainicial_ano,true,'text', $db_opcao);
                ?>
              </td>
            </tr>
            <tr>
              <td title="Data de final da validade das informações para o eSocial." >
              <label for="rh04_datafinal">Fim de validade:</label>
              </td>
              <td> 
                <?php
                  db_inputdata('rh04_datafinal', $rh04_datafinal_dia, $rh04_datafinal_mes, $rh04_datafinal_ano, true, 'text', $db_opcao);
                ?>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <fieldset>
                  <legend>Descrição das atividades desempenhadas: </legend>
                  <?php
                    db_textarea('rh04_descricaoatividades', 3,55,0,true,'text',$db_opcao);
                  ?>
                </fieldset>
             </td>
          </table>          
       </fieldset>
   </fieldset>
        
   <input name="<?=$labelButton?>" type="submit" id="db_opcao" value="<?= ucfirst($labelButton)?>" <?= ($db_botao == false ? "disabled" : "") ?> >
   <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
        
  </form>
</div>

<script type="application/javascript">

function validarDados() {
    var datainicial = $F('rh04_datainicial');
    var datafinal = $F('rh04_datafinal');
    
    if (datainicial == '' ) {
        alert('Data inicial deve ser informada.');
        return false;
    }

    if (datafinal != '' ) {
        if (js_comparadata(datainicial, datafinal, '>')) {
            alert('Data inicial esta maior que a data final.');
            return false;
        }
    }
    
    return true;
}

function js_pesquisa() { 
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_rhcargo','func_rhcargo.php?funcao_js=parent.js_preenchepesquisa|rh04_codigo','Pesquisa',true);
}

function js_preenchepesquisa(chave){
  db_iframe_rhcargo.hide();
  <?php
  if($db_opcao!=1){
    echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
  }
  ?>
}

document.form1.rh04_descr.focus();
document.form1.rh04_descr.select();

</script>