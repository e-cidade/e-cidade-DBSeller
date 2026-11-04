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

//MODULO: fiscal
$cltipoandam->rotulo->label();
?>
<style type="text/css">
  fieldset {border-radius:7px;padding:20px;}
</style>
<br />
<br />
<form name="form1" method="post" action="" class="container">
<fieldset>
<legend>Tipo de andamento</legend>
<table border="0" align="center" class="form-container">
  <tr>
    <td nowrap title="<?=@$Ty41_codtipo?>">
       <?=@$Ly41_codtipo?>
    </td>
    <td>
      <?php
        db_input('y41_codtipo',10,$Iy41_codtipo,true,'text',3,"")
      ?>
    </td>
  </tr>

  <tr>
    <td align="right" title="<?=@$Tgrupo?>">
      <?php
        db_ancora("<strong>Grupo:</strong>","js_pesquisagrupo(true);",$db_opcao);
      ?>
    </td>

    <td>
      <?php
        db_input('grupo',10,@$Igrupo,true,'text',$db_opcao," onchange='js_pesquisagrupo(false);'",'','','','14');
        db_input('grupo_desc',40,$grupo_desc,true,'text',3,'');
      ?>
    </td>
  </tr>

  <tr>
    <td nowrap title="<?=@$Ty41_descr?>">
       <?=@$Ly41_descr?>
    </td>
    <td>
    <?php
      db_input('y41_descr',50,$Iy41_descr,true,'text',$db_opcao,"")
    ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ty41_obs?>">
       <?=@$Ly41_obs?>
    </td>
    <td>
    <?php
      db_textarea('y41_obs',0,48,$Iy41_obs,true,'text',$db_opcao,"")
    ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Ty41_permcalc?>">
       <?=@$Ly41_permcalc?>
    </td>
    <td>
    <?php
      $x = array('t'=>'Sim','f'=>'Não');
      db_select('y41_permcalc',$x,true,$db_opcao,"");
    ?>
    </td>
  </tr>
  <?php
    // sequencial da tabela parametros andamento.
    db_input('sequencial', 20, $sequencial, true, 'hidden', 3, '');
  ?>
  <?php
    $tipoandam = (isset($tipoandam) and $tipoandam != '') ? $tipoandam : $y41_codtipo;
    db_input('tipoandam', 20, $tipoandam, true, 'hidden', 3, '');
  ?>
  <tr>
    <td><b>Tem data de ciência?</b></td>
    <td>
      <?php db_select('tem_data_ciencia',@$x,true,$db_opcao,""); ?>
    </td>
  </tr>
  <tr>
    <?php if($tem_data_recurso == null or $tem_data_recurso == ''){$tem_data_recurso = 'f';} ?>
    <td><b>Tem prazo de recurso?</b></td>
    <td><?php db_select('tem_data_recurso',@$x,true,$db_opcao,""); ?></td>
  </tr>
  <tr>
    <td title="Data Fim">
       <strong>Data Fim:</strong>
    </td>
    <td>
      <?php
      db_inputdata('fi31_data',@$fi31_data_dia,@$fi31_data_mes,@$fi31_data_ano,true,'text',$db_opcao);
      ?>
    </td>
  </tr>

  </table>
</fieldset>
<br />
<input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
&nbsp;&nbsp;
<input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
</form>
<script>
function js_pesquisa(){
  js_OpenJanelaIframe('','db_iframe_tipoandam','func_fis_tipoandam.php?funcao_js=parent.js_preenchepesquisa|y41_codtipo','Pesquisa',true);
}
function js_preenchepesquisa(chave){
  db_iframe_tipoandam.hide();
  <?php
  if($db_opcao!=1){
    echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?abas=1&chavepesquisa='+chave";
  }
  ?>
}

// -----------------------------------------------------------
function js_pesquisagrupo(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_grupo','func_fis_grupotipoandamento.php?funcao_js=parent.js_mostragrupo1|sequencial|descricao|manual_automatico|tipo_peca','Pesquisa',true);
  }else{
     if(document.form1.grupo.value != ''){
        js_OpenJanelaIframe('','db_iframe_grupo','func_fis_grupotipoandamento.php?pesquisa_chave='+document.form1.grupo.value+'&funcao_js=parent.js_mostragrupo','Pesquisa',false);
     }else{
       document.form1.grupo.value = '';
     }
  }
}

function js_mostragrupo(chave, sDescricao, lErro){

  document.form1.grupo_desc.value = sDescricao;

  if( lErro == true ){

    document.form1.grupo.focus();
    document.form1.grupo.value = '';
  }
}

function js_mostragrupo1(chave1,chave2,chave3,chave4){

  document.form1.grupo.value = chave1;
  document.form1.grupo_desc.value = chave2+' / '+chave3+' / '+chave4;
  db_iframe_grupo.hide();
}
</script>
