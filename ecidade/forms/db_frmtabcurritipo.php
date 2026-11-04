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

//MODULO: recursoshumanos
$cltabcurritipo->rotulo->label();
?>
<form name="form1" method="post" action="">
<center>
<table border="0">
  <tr>
    <td nowrap title="<?=@$Th02_codigo?>">
       <?=@$Lh02_codigo?>
    </td>
    <td>
<?php
db_input('h02_codigo',5,$Ih02_codigo,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Th02_descr?>">
       <?=@$Lh02_descr?>
    </td>
    <td>
<?php
db_input('h02_descr',60,$Ih02_descr,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Th02_obs?>">
       <?=@$Lh02_obs?>
    </td>
    <td>
        <?php
        db_textarea('h02_obs',5,70,$Ih02_obs,true,'text',$db_opcao,"");
        ?>
    </td>
  </tr>
  <tr>

    <td>
      <?php
        db_ancora("<b>Tipo de treinamento/Capacitacao da tabela 28 do esocial:</b>","pesquisaOpcoes('tabela28','h02_tipotreinamento')",$db_opcao,"width: 100%;");
      ?>
    </td>
    <td>
      <?php
        db_input("h02_tipotreinamento",10,$Ih02_tipotreinamento,true,'hidden',$db_opcao);
        db_input("h84_descricao",40,$Ih84_descricao,true,'text',3);
      ?>
    </td>
  </tr>
  </table>
  </center>
<input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
<input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
</form>
<script>
function js_pesquisa(){
  js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_tabcurritipo','func_tabcurritipo.php?funcao_js=parent.js_preenchepesquisa|h02_codigo','Pesquisa',true);
}
function js_preenchepesquisa(chave){
  db_iframe_tabcurritipo.hide();
  <?php
  if($db_opcao!=1){
    echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
  }
  ?>
}

function pesquisaOpcoes(sArquivoDados,sIdCampo) {

parametros  = "sArquivoDados="+sArquivoDados;
parametros += "&sIdCampo="+sIdCampo;
parametros += "&funcao_js=retornoOpcoes1";

// console.log(parametros)

  js_OpenJanelaIframe('',
                      'db_iframe_opcoes',
                      'func_rhlocaltrabopcoesesocial.php?'+parametros,
                      'Pesquisar Opcoes',
                      true);



}

/*
* Funcao de retorno da opcao escolhida
*/
function retornoOpcoes1(sIdCampo,iCodigo,sDescricao) {

var sIdCampoDescricao = "h84_descricao";
var objCampo = eval(`document.form1.${sIdCampo}`);

var objCampoDescricao = eval(`document.form1.${sIdCampoDescricao}`);

objCampo.value = iCodigo;
objCampoDescricao.value = sDescricao;

  db_iframe_opcoes.hide();

}


</script>
