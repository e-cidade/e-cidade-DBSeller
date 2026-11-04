<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2016  DBseller Servicos de Informatica
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

$clrotulo = new rotulocampo;
$clrotulo->label('q02_inscr');
$clrotulo->label("z01_nome");
$clrotulo->label("z01_numcgm");
?>
 <form action="" method="post" name="formularioComparativoRetencao" id="formularioComparativoRetencao">
     <fieldset>
       <legend>Inconsistências de Retenções</legend>
       <table class="form-container">
         <tr>
           <td><a href="" id="labelInscricao">Inscrição Municipal:</a></td>
           <td>
             <?php
               db_input("q02_inscr", 1,  1, true, "text", 1, null, null, null, "width:94px");
               db_input("z01_nome", 1,  1, true, "text", 1);
             ?>
           </td>
         </tr>
         <tr>
           <label for="cgm">
               <td nowrap title="<?= $Tz01_numcgm ?>">
                   <?php
                   db_ancora("CGM: ", "js_pesquisa_numcgm(true);", 1);
                   ?>
               </td>
           </label>
           <td>
               <?php
               $Sz01_numcgm = "CGM";
               db_input('z01_numcgm', 11, 1, true, 'text', 1, " onchange='js_pesquisa_numcgm(false);'");
               ?>
               <?php
               db_input('z01_nomecmg', 47, '', true, 'text', 3);
               ?>
           </td>
         </tr>
         <tr>
           <td><label for="comp_mes">Compêtencia:</label></td>
           <td>
              <?php
                $GLOBALS['Tcomp_mes'] = 'Mês de compêtencia';
                $GLOBALS['Scomp_mes'] = 'Mês';
                db_input("comp_mes", 1, 1, null, true, null, 1, null, null, "width:94px", 2);
              ?>
              /
              <?php
                $GLOBALS['Tcomp_ano'] = 'Ano de compêtencia';
                $GLOBALS['Scomp_ano'] = 'Ano';
                db_input("comp_ano", 1, 1, null, true, null, 1, null, null, "width:94px", 4);
              ?>
            </td>
         </tr>
       </table>
     </fieldset>
     <input type="button" value="Processar" name="processar" id="processar" onclick="return js_processar()" />
     <input type="button" value="Limpar" name="limpar" id="limpar" onclick="formularioComparativoRetencao.reset()" />
</form>
<script type="text/javascript">

  const MENSAGEM = 'tributario.fiscal.comparativoretencao.';
  const RPC      = 'fis2_comparativoretencao_novo.RPC.php';

  var oLookUpInscricao = new DBLookUp($('labelInscricao'), $('q02_inscr'), $('z01_nome'), {
    'sArquivo'              : 'func_issbase.php',
    'sObjetoLookUp'         : 'db_iframe_issbase',
    'sLabel'                : 'Pesquisar Inscrição',
    'oBotaoParaDesabilitar' : $('excluir')
  });

  function js_pesquisa_numcgm(mostra) {

      if (mostra == true) {
          js_OpenJanelaIframe('CurrentWindow.corpo', 'func_nome', 'func_cgm.php?funcao_js=parent.js_mostracgm1|z01_numcgm|z01_nome', 'Pesquisa', true);
      } else {
          if (document.formularioComparativoRetencao.z01_numcgm.value != '') {
              js_OpenJanelaIframe('CurrentWindow.corpo', 'func_nome', 'func_cgm.php?lNovoDetalhe=1&pesquisa_chave=' + document.formularioComparativoRetencao.z01_numcgm.value + '&funcao_js=parent.js_mostracgm', 'Pesquisa', false);
          } else {
              document.formularioComparativoRetencao.z01_nomecmg.value = '';
          }
      }

  }

  function js_mostracgm(chave, erro) {

      document.formularioComparativoRetencao.z01_nomecmg.value = chave;
      if (erro == true) {
          document.formularioComparativoRetencao.z01_nomecmg.value = '';
          document.formularioComparativoRetencao.z01_numcgm.focus();
      }
  }

  function js_mostracgm1(chave1, chave2) {

      document.formularioComparativoRetencao.z01_numcgm.value = chave1;
      document.formularioComparativoRetencao.z01_nomecmg.value = chave2;
      func_nome.hide();
  }


  function js_processar(){

    var oParametros = {
        sExecucao : 'getComparativoRetencao',
        sInscr    : $F('q02_inscr'),
        iCompMes  : $F('comp_mes'),
        iCompAno  : $F('comp_ano'),
        sCgm  : $F('z01_numcgm'),
    }

    new AjaxRequest(RPC, oParametros, function(oRetorno, lErro) {

      if(lErro) {

        alert(oRetorno.sMessage);
        return false;
      }

      var oDownloadWindow = new DBDownload();
      oDownloadWindow.addFile(oRetorno.sArquivo, "Relatório de Inconsistências de Retenções (novo)");
      oDownloadWindow.show();

    }).setMessage('Carregando...').execute();

  }
</script>
