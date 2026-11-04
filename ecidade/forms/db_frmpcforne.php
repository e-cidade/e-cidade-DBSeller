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

//MODULO: compras
$clpcforne->rotulo->label();
$cl_cgmtipoempresa->rotulo->label();
$cltipoempresa->rotulo->label();

$clrotulo = new rotulocampo;
$clrotulo->label("z01_nome");

$sCampos         = "db98_sequencial as z03_tipoempresa, db98_descricao";
$sSqlTipoEmpresa = $cltipoempresa->sql_query_file(null, $sCampos, 'db98_sequencial', "");
$rsTipoEmpresa   = $cltipoempresa->sql_record($sSqlTipoEmpresa);

if ($db_opcao == 1) {
    $db_action       = "com1_pcforne004.php";
    $sLegendFieldset = "Cadastro";
    $iOpcaoInputNume = 1;
} elseif ($db_opcao == 2 || $db_opcao == 22) {
    $db_action       = "com1_pcforne005.php";
    $sLegendFieldset = "Alteração";
    $iOpcaoInputNume = 22;
} elseif ($db_opcao == 3 || $db_opcao == 33) {
    $db_action       = "com1_pcforne006.php";
    $sLegendFieldset = "Exclusão";
    $iOpcaoInputNume = 3;
}

if (!isset($z03_sequencial)) {
    $z03_sequencial = '';
}
?>

<form name="form1" method="post" action="<?php echo $db_action?>">
<center>
<fieldset style="width: 800px;">
<legend style="font-weight: bold;"><?php echo $sLegendFieldset;?> de Fornecedores</legend>
<table border="0">
  <tr>
    <td nowrap title="<?php echo @$Tpc60_numcgm?>">
       <?php
        db_ancora(@$Lpc60_numcgm, "js_pesquisapc60_numcgm(true);", ($db_opcao==1?$db_opcao:3));
        ?>
    </td>
    <td>
      <?php
        db_input('pc60_numcgm', 8, $Ipc60_numcgm, true, 'text', ($db_opcao==1?$db_opcao:3), " onchange='js_pesquisapc60_numcgm(false);'")
        ?>
       <?php
        db_input('z01_nome', 67, $Iz01_nome, true, 'text', $iOpcaoInputNume, '')
        ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?php echo @$Tpc60_dtlanc?>">
       <?php echo @$Lpc60_dtlanc?>
    </td>
    <td>
      <?php
        db_inputdata('pc60_dtlanc', date("d", db_getsession("DB_datausu")), date("m", db_getsession("DB_datausu")), date("Y", db_getsession("DB_datausu")), true, 'text', 3, "")
        ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?php echo @$Tpc60_obs?>">
       <?php echo @$Lpc60_obs?>
    </td>
    <td>
<?php
db_textarea('pc60_obs', 2, 78, $Ipc60_obs, true, 'text', $db_opcao, "")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?php echo @$Tpc60_bloqueado?>">
       <?php echo @$Lpc60_bloqueado?>
    </td>
    <td>
<?php
$x = array("f"=>"Não","t"=>"Sim");
db_select('pc60_bloqueado', $x, true, $db_opcao, "");
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?php echo @$Tpc60_indicativocprb?>">
      <?php echo @$Lpc60_indicativocprb?>
    </td>
    <td>
      <?php
        $opcoes = array("f"=>"Não", "t"=>"Sim");
        db_select('pc60_indicativocprb', $opcoes, true, $db_opcao);
        ?>
    </td>
  </tr>

  <?php if (in_array($db_opcao, [2, 22, 3, 33])) : ?>
  <tr>
    <td nowrap title='<?php echo @$Tz03_tipoempresa?>'>
      <strong>Tipo de Empresa:</strong>
    </td>
    <td colspan="3" nowrap="nowrap">
        <?php db_selectrecord('z03_tipoempresa', $rsTipoEmpresa, true, $db_opcao, '', '', '', '', '', 2); ?>
        <input name="z03_sequencial" type="hidden" value="<?php echo $z03_sequencial ?>">
    </td>
  </tr>
  <?php endif; ?>

</table>
</fieldset>
<br />
<input name="<?php echo ($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?php echo ($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?php echo ($db_botao==false?"disabled":"")?> >
<input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
</center>
</form>
<script>
function js_pesquisapc60_numcgm(mostra){
  if(mostra===true){
    js_OpenJanelaIframe('CurrentWindow.corpo.iframe_pcforne','db_iframe_nomes','func_nome.php?funcao_js=parent.js_mostracgm1|z01_numcgm|z01_nome','Pesquisa',true,'0');
  }else{
     if(document.form1.pc60_numcgm.value != ''){
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_pcforne','db_iframe_nomes','func_nome.php?pesquisa_chave='+document.form1.pc60_numcgm.value+'&funcao_js=parent.js_mostracgm','Pesquisa',false,'0','1','775','390');
     }else{
       document.form1.z01_nome.value = '';
     }
  }
}
function js_mostracgm(erro,chave){
  document.form1.z01_nome.value = chave;
  if(erro===true){
    document.form1.pc60_numcgm.focus();
    document.form1.pc60_numcgm.value = '';
  }
}
function js_mostracgm1(chave1,chave2){
  document.form1.pc60_numcgm.value = chave1;
  document.form1.z01_nome.value = chave2;
  db_iframe_nomes.hide();
}
function js_pesquisa(){
  js_OpenJanelaIframe('CurrentWindow.corpo.iframe_pcforne','db_iframe_pcforne','func_pcforne.php?funcao_js=parent.js_preenchepesquisa|pc60_numcgm','Pesquisa',true,'0');
}
function js_preenchepesquisa(chave){
  db_iframe_pcforne.hide();
  <?php
    if ($db_opcao!=1) {
        echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
    }
    ?>
}
</script>
