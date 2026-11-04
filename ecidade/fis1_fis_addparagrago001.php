<?php 
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2009  DBselller Servicos de Informatica             
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

require(modification("libs/db_utils.php"));
require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
require(modification("libs/db_sessoes.php"));
require(modification("libs/db_usuariosonline.php"));
require(modification("dbforms/db_funcoes.php"));
require(modification("classes/db_fis_paragrafo_classe.php"));

$clparagrafo = new cl_fis_paragrafo;

db_postmemory($HTTP_GET_VARS);

if(isset($pTipo) && $pTipo == 1){
  $getLabel = 'Parágrafos para Auto';
}else if(isset($pTipo) && $pTipo == 2){
  $getLabel = 'Parágrafos para Intimação';
}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<style type="text/css">
  select {
    width: 400px;
    height: 100px;
  }
  .desc {
    text-align: right;
    font-weight: bold;
  }
  .none {
    display: none;
  }
</style>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
<div class="container">
  <form action="">
    <fieldset>
      <legend><?=$getLabel?></legend>
      <table>
        <?php
          $sWhere = 'pl09_status = true ';
          if(isset($pParagrafo) && $pParagrafo != ''){
            $sWhere .= ' and pl09_paragrafo = '.$pParagrafo.' ';
          }
          if(isset($pTipo) && ($pTipo == 1 || $pTipo == 2 || $pTipo == 3)){
            $sWhere .= ' and pl09_tipo = '.$pTipo.' ';
          }

          $sCamposParag = 'pl09_paragrafo, pl09_codigo, pl09_resumo, pl09_texto';
          $pSql         = $clparagrafo->sql_query(null, $sCamposParag, 'pl09_resumo desc', $sWhere);
          $pRs          = $clparagrafo->sql_record($pSql);
          $pRows        = $clparagrafo->numrows;

        ?>
        <tr class="<?=((isset($pParagrafo) && $pParagrafo == 1 || $pParagrafo == '') ? '' : 'none ')?> relato">
          <td class="desc">RELATO</td>
          <td>
            <select name="relato" id="relato" multiple="">
              <?php
                for($i=0; $i<$pRows; $i++){
                  db_fieldsmemory($pRs, $i);
                  if($pl09_paragrafo == 1){
                    echo '<option value="'.$pl09_codigo.'">'.$pl09_resumo.'</option>';
                  }
                }
              ?>
            </select>

            <?php
              for($i=0; $i<$pRows; $i++){
                db_fieldsmemory($pRs, $i);
                if($pl09_paragrafo == 1){
                  echo '<div style="display:none"> <textarea name="t'.$pl09_codigo.'" id="t'.$pl09_codigo.'" cols="30" rows="10" tipo="'.$pl09_paragrafo.'">'.$pl09_texto.'</textarea> </div>';
                }
              }
            ?>
          </td>
        </tr>
          
        <tr class="<?=((isset($pParagrafo) && $pParagrafo == 2 || $pParagrafo == '') ? '' : 'none ')?> infrigencia">
          <td class="desc">INFRIGÊNCIA</td>
          <td>
            <select name="infrigencia" id="infrigencia" multiple="">
              <?php
                for($i=0; $i<$pRows; $i++){
                  db_fieldsmemory($pRs, $i);
                  if($pl09_paragrafo == 2){
                    echo '<option value="'.$pl09_codigo.'">'.$pl09_resumo.'</option>';
                  }
                }
              ?>
            </select>
            <?php
              for($i=0; $i<$pRows; $i++){
                db_fieldsmemory($pRs, $i);
                if($pl09_paragrafo == 2){
                  echo '<div style="display:none"> <textarea name="t'.$pl09_codigo.'" id="t'.$pl09_codigo.'" cols="30" rows="10" tipo="'.$pl09_paragrafo.'">'.$pl09_texto.'</textarea> </div>';
                }
              }
            ?>
          </td>
        </tr>
          
        <tr class="<?=((isset($pParagrafo) && $pParagrafo == 3 || $pParagrafo == '') ? '' : 'none ')?> sansao">
          <td class="desc">SANÇÃO</td>
          <td>
            <select name="sancao" id="sancao" multiple="">
              <?php
                for($i=0; $i<$pRows; $i++){
                  db_fieldsmemory($pRs, $i);
                  if($pl09_paragrafo == 3){
                    echo '<option value="'.$pl09_codigo.'">'.$pl09_resumo.'</option>';
                  }
                }
              ?>
            </select>
            <?php
              for($i=0; $i<$pRows; $i++){
                db_fieldsmemory($pRs, $i);
                if($pl09_paragrafo == 3){
                  echo '<div style="display:none"> <textarea name="t'.$pl09_codigo.'" id="t'.$pl09_codigo.'" cols="30" rows="10" tipo="'.$pl09_paragrafo.'">'.$pl09_texto.'</textarea> </div>';
                }
              }
            ?>
          </td>
        </tr>
          
        <tr class="<?=((isset($pParagrafo) && $pParagrafo == 4 || $pParagrafo == '') ? '' : 'none ')?> baselegal">
          <td class="desc">BASE LEGAL</td>
          <td>
            <select name="baselegal" id="baselegal" multiple="">
              <?php
                for($i=0; $i<$pRows; $i++){
                  db_fieldsmemory($pRs, $i);
                  if($pl09_paragrafo == 4){
                    echo '<option value="'.$pl09_codigo.'">'.$pl09_resumo.'</option>';
                  }
                }
              ?>
            </select>
            <?php
              for($i=0; $i<$pRows; $i++){
                db_fieldsmemory($pRs, $i);
                if($pl09_paragrafo == 4){
                  echo '<div style="display:none"> <textarea name="t'.$pl09_codigo.'" id="t'.$pl09_codigo.'" cols="30" rows="10" tipo="'.$pl09_paragrafo.'">'.$pl09_texto.'</textarea> </div>';
                }
              }
            ?>
          </td>
        </tr>
        
        <tr>
          <td></td>
          <td><input type="button" value=" Ok "></td>
        </tr>

      </table>
    </fieldset>
  </form>
  <script type="text/javascript" src="scripts/jquery-2.1.1.min.js"></script>
  <script type="text/javascript">
    var $a = jQuery.noConflict();
    jQuery(document).ready(function($a){

      function addParagrafo(texto,tipo){
        var addParagrafo = $a('#'+tipo+' textarea', window.parent.document).val();
        if(addParagrafo != ""){
          var quebraLinha = '\n';
        } else {
          var quebraLinha = '';
        }
        $a('#'+tipo+' textarea', window.parent.document).val(addParagrafo + quebraLinha + quebraLinha + texto).removeAttr('disabled');
        $a('#'+tipo+' input', window.parent.document).removeAttr('disabled');
        $a('#'+tipo, window.parent.document).show();
      }

      $a('input:button').click(function() {
        $a('select option:selected').each(function (i, e) {
          var codigo   = e.value;
          var addTipo  = $a('#t'+codigo).attr('tipo');
          var addTexto = $a('#t'+codigo).val();

          if(addTipo == 1){
            var tipo_paragrafo = "paraRelato";
            var addDesc  = 'RELATO';

          }else if(addTipo == 2){
            var tipo_paragrafo = "paraInfringencia";
            var addDesc  = 'INFRIGÊNCIAO';

          } else if(addTipo == 3){
            var tipo_paragrafo = "paraSancao";
            var addDesc  = 'SANÇÃO';

          } else if(addTipo == 4){
            var tipo_paragrafo = "paraBaseLegal";
            var addDesc  = 'BASE LEGAL';

          }

          addParagrafo(addTexto,tipo_paragrafo);
          window.parent.db_iframe_addparagrafo.hide();
         
        });
      });

    });
  </script>
</div>
</body>
</html>
