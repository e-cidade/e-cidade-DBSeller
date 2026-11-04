<?php 
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2015  DBselller Servicos de Informatica             
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
$clparagrafo->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("nome");
?>
<br>
<div class="container">
<form name="form1" method="post" action="">
  <fieldset>
    <legend>Paragráfos</legend>
    <table>
      <?php
        if ( $db_opcao != 1 ){
      ?>
      <tr>
        <td nowrap title="<?=@$Ipl09_codigo?>"><?=@$Lpl09_codigo?></td>
        <td> 
          <?php 
          db_input("pl09_codigo",10,$Ipl09_codigo,true,'text',3,"");
          ?>
        </td>
      </tr>
      <?php
        }
      ?>
    
      <tr>
        <td nowrap title="<?=@$Ipl09_tipo?>"><?=@$Lpl09_tipo?></td>
        <td> 
          <?php 
          db_input('pl09_descr',50,$Ipl09_descr,true,'hidden',$db_opcao,"");
          db_input('pl09_codigo',50,$Ipl09_codigo,true,'hidden',$db_opcao,"");
    
          $aTipo = array( "0" => "SELECIONE",
                          "1" => "AUTO",
                          "2" => "INTIMAÇÃO",
                          "3" => "NOTIFICAÇÃO"
                        );
          db_select("pl09_tipo",$aTipo,true,$db_opcao);
          ?>
        </td>
      </tr>
    
      <tr>
        <td nowrap title="<?=@$Ipl09_paragrafo?>"><?=@$Lpl09_paragrafo?></td>
        <td>
          <?php 
          $aParagrafo = array( "0" => "SELECIONE",
                               "1" => "RELATO",
                               "2" => "INFRINGÊNCIA",
                               "3" => "SANÇÃO",
                               "4" => "BASE LEGAL"
                              );
          db_select("pl09_paragrafo",$aParagrafo,true,$db_opcao);
          ?>
        </td>
      </tr>
      
      <tr>
        <td nowrap title="<?=@$Ipl09_resumo?>"><?=@$Lpl09_resumo?></td>
        <td> 
          <?php 
          db_input("pl09_resumo",63,$Ipl09_resumo,true,'text',$db_opcao,"");
          ?>
        </td>
      </tr>
    
      <tr>
        <td nowrap title="<?=@$Ipl09_texto?>"><?=@$Lpl09_texto?></td>
        <td><?php  db_textarea('pl09_texto',10,61,$Ipl09_texto,true,'text',$db_opcao,""); ?></td>
      </tr>
        <?php
          if ( $db_opcao == 2 || $db_opcao == 22 ){
        ?>
      <tr>
        <td nowrap title="<?=@$Ipl09_status?>"><?=@$Lpl09_status ?></td>
        <td>
          <?php 
            $aStatusParag = array( "t" => "ATIVO",
                                   "f" => "INATIVO",
                                 );
            db_select("pl09_status",$aStatusParag,true,$db_opcao, "style='width:70px;'");
          ?>
        </td>
      </tr>
      <?php
        }
      ?>
    </table>
  </fieldset>
<input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
<?php if($db_opcao != 1){ ?>
  <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
<?php } ?>
</form>
</div>
<script type="text/javascript" src="scripts/jquery-2.1.1.min.js"></script>
<script type="text/javascript">
  var $a = jQuery.noConflict();
  jQuery(document).ready(function($) {
    $a('#pl09_paragrafo').change(function() {
      var descr = $a('#pl09_paragrafo option:selected').html();
      $a('#pl09_descr').val(descr);
    });
  });
</script>

<script>
function js_pesquisa(){
  js_OpenJanelaIframe('top.corpo','db_iframe_tipoandam','func_fis_paragrafo.php?funcao_js=parent.js_preenchepesquisa|pl09_codigo','Pesquisa',true);
}
function js_preenchepesquisa(chave){
  db_iframe_tipoandam.hide();
  <?php 
  if($db_opcao!=1){
    echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
  }
  ?>
}
</script>