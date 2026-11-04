<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (c) 2014  DBSeller Servicos de Informatica
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

//MODULO: protocolo
$cltipoproc->rotulo->label();

if (isset($p51_prottipodocumentoprocesso)) {
  $sql = "
    SELECT
      p91_descricao
    FROM 
      prottipodocumentoprocesso
    WHERE
      p91_sequencial = {$p51_prottipodocumentoprocesso}
  ";
  $pgObj = db_query($sql);

  $p91_descricao = pg_fetch_assoc($pgObj, 0)['p91_descricao'];
}

?>
<form name="form1" method="post" action="">
<center>
<table border="0">
  <tr>
    <td nowrap title="<?=@$Tp51_codigo?>">
       <?=@$Lp51_codigo?>
    </td>
    <td> 
<?php
db_input('p51_codigo',3,$Ip51_codigo,true,'text',3,"")
?>
    <td>
  <tr>
  <tr>
    <td nowrap title="<?=@$Tp51_descr?>">
       <?=@$Lp51_descr?>
    </td>
    <td> 
<?php
db_input('p51_descr',60,$Ip51_descr,true,'text',$db_opcao,"")
?>
    <td>
  <tr>

  <tr>
    <td nowrap title="<?php echo @$Tp51_prottipodocumentoprocesso ?>">
      <?php
        db_ancora(@$Lp51_prottipodocumentoprocesso, "js_pesquisa_tipo_documento_processo(true);", $db_opcao);
      ?>
    </td>
    <td nowrap>
      <?php
        db_input('p51_prottipodocumentoprocesso', 10, $Ip51_prottipodocumentoprocesso, false, 'text', 3 ," onchange='js_pesquisa_tipo_documento_processo(false);'");
        db_input('p91_descricao', 40, $Iz01_nome, true, 'text', 3, '');
      ?>
    </td>
  </tr>

  <tr>
    <td nowrap title="<?=@$Tp51_dtlimite?>">
       <?=@$Lp51_dtlimite?>
    </td>
    <td> 
<?php
$matriz = array('t'=>"Sim",'f'=>"Nao");
db_inputdata('p51_dtlimite',@$p51_dtlimite_dia,@$p51_dtlimite_mes,@$p51_dtlimite_ano,true,'text',$db_opcao,"");
?>
    </td>
  </tr>
  </table>
  </center>
  <div style="text-align:center; margin-top:15px;">
    <input name="db_opcao" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
    <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
  </div>
</form>
<script>
  function js_pesquisa() {
    js_OpenJanelaIframe(
      '',
      'db_iframe',
      'func_tipoproc_todos.php?grupo=1&funcao_js=parent.js_preenchepesquisa|0',
      'Pesquisa Tipo de Processo',
      true
    );
  }
  function js_preenchepesquisa(chave){
    db_iframe.hide();
    location.href = '<?=basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])?>'+"?chavepesquisa="+chave;
  }

  function js_pesquisa_tipo_documento_processo(mostra){
    var url = "func_prottipodocumentoprocesso.php";
    var parametros = "?funcao_js=parent.js_mostra_tipo_documento_processo";

    parametros += !mostra ? `&pesquisa_chave=${document.form1.p51_prottipodocumentoprocesso.value}` : '|0|1';
    
    js_OpenJanelaIframe("", "iframe_tipodocumento", url + parametros, "Pesquisa Tipo de Documento", mostra);
  }

  function js_mostra_tipo_documento_processo(chave1, chave2) {
    document.form1.p51_prottipodocumentoprocesso.value = chave1;
    document.form1.p91_descricao.value = chave2;
    iframe_tipodocumento.hide();
  }
</script>
<?php

if($db_opcao == 22 || $db_opcao == 33){
  ?>
  <script>
    js_pesquisa();
  </script>
  <?php
}
?>