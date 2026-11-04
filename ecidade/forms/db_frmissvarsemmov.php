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

//MODULO: issqn
require_once modification('dbforms/db_classesgenericas.php');
$cliframe_seleciona = new cl_iframe_seleciona;
$clrotulo           = new rotulocampo;
$clrotulo->label('q07_inscr');
$clrotulo->label('z01_nome');
$clrotulo->label('k00_histtxt');
$Iz01_numcgm = "";
?>

<form name="form1" method="post" action="">
  <fieldset>
    <legend>Cancelamento ISSQN Variável - Inclusão</legend>
    <table border="0" align="center">
      <tr>   
        <td title="<?php echo $Tq07_inscr ?>">
          <?php db_ancora($Lq07_inscr, ' js_inscr(true); ', 1) ?>
        </td>    
        <td title="<?php echo $Tq07_inscr ?>" colspan="4">
          <?php
           db_input('q07_inscr', 10, $Iq07_inscr, true, 'text', 1, 'onchange="js_inscr(false)"');
           isset($q07_inscr) ? $inscricao = $q07_inscr : '';
           db_input('inscricao', 6, $Iq07_inscr, true, 'hidden', 1);
           db_input('z01_nome', 81, 0, true, 'text', 3);
          ?>
        </td>
      </tr>

      <tr>
        <td title="CGM">
            <?php db_ancora("CGM",' js_cgm(true); ',1); ?>
        </td>
        <td> 
          <?php
            db_input('z01_numcgm',10,$Iz01_numcgm,true,'text',1,"onchange='js_cgm(false)'","z_numcgm");
            db_input('z01_nome',81,0,true,'text',3,"","nomeCGM");
          ?>
        </td>
      </tr>

      <tr>
        <td><b>Observação:</b></td>
        <td nowrap title="<?php echo $Tk00_histtxt ?>">
          <?php db_textarea('k00_histtxt', 3, 95, $Ik00_histtxt, true, 'text', $db_opcao, '', '','') ?>
        </td>
      </tr>
    </table>
    <br>
    <table border="0">
      <tr>   
        <td> 
          <?php
          $cliframe_seleciona->legenda = 'LANÇAMENTOS';

          $pesquisarPorInscricao = isset($q07_inscr) && $q07_inscr != '';
          $pesquisarPorCgm = isset($z_numcgm) && $z_numcgm != '';
          
          if ($pesquisarPorInscricao) { 
          
            $cliframe_seleciona->sql = "SELECT
                ISSVAR.*,
                ARREINSCR.*,
                ISSBASE.*
            FROM
                ARREINSCR
                INNER JOIN ISSVAR ON ISSVAR.Q05_NUMPRE = ARREINSCR.K00_NUMPRE
                INNER JOIN ARRECAD ON ARRECAD.K00_NUMPRE = Q05_NUMPRE
                AND K00_NUMPAR = ISSVAR.Q05_NUMPAR
                INNER JOIN ISSBASE ON Q02_INSCR = K00_INSCR
            WHERE
                K00_INSCR = $q07_inscr AND Q05_VALOR = 0
            ORDER BY
                Q05_ANO,
                Q05_MES";
          } else if($pesquisarPorCgm){

            $cliframe_seleciona->sql = "SELECT distinct
                ISSVAR.*
            FROM
                caixa.arrenumcgm
                inner join ISSVAR on ISSVAR.Q05_NUMPRE = arrenumcgm.K00_NUMPRE
                INNER JOIN ARRECAD ON ARRECAD.K00_NUMPRE = Q05_NUMPRE
            WHERE
            arrenumcgm.k00_numcgm = $z_numcgm AND Q05_VALOR = 0 and q05_numpar = k00_numpar
            ORDER BY
                Q05_ANO,
                Q05_MES";
          }
          
          $cliframe_seleciona->campos        = 'q05_numpre,q05_numpar,q05_mes,q05_ano,q05_valor,q05_histor,';
          $cliframe_seleciona->campos       .= 'q05_aliq,q05_bruto,q05_vlrinf';
          $cliframe_seleciona->textocabec    = 'darkblue';
          $cliframe_seleciona->textocorpo    = 'black';
          $cliframe_seleciona->fundocabec    = '#aacccc';
          $cliframe_seleciona->fundocorpo    = '#ccddcc';
          $cliframe_seleciona->iframe_height = '250';
          $cliframe_seleciona->iframe_width  = '715';
          $cliframe_seleciona->iframe_nome   = 'atividades';
          $cliframe_seleciona->chaves        = 'q05_numpre,q05_numpar';
          $cliframe_seleciona->iframe_seleciona($db_opcao);    
          ?>
        </td>
      </tr>
    </table>
  </fieldset>
  
  <br>
  <center>
    <input name="lancar" type="submit" onclick="return js_verifica();" id="db_opcao" value="Lançar" <?php echo ($db_botao == false ? 'disabled' : '') ?>>
  </center>
</form>

<script type="text/javascript">
  function js_verifica() { 
    inscr = new Number(document.form1.q07_inscr.value);
    cgm = new Number(document.form1.z_numcgm.value);

    if (inscr == '' && cgm == '') {
      alert('Insira uma inscrição ou um CGM');
      return false;
    }

    if (cgm != '' &&  cgm == '0' &&  inscr != '' && inscr == '0') {
      alert('Inscrição ou CGM invalido');
      return false;
    }

    if (cgm != '' && isNaN(cgm) == true || inscr != '' && isNaN(inscr) == true) {
      alert('Inscrição ou CGM invalido');
      return false;
    }
    
    if (inscr != document.form1.inscricao.value) {
      return false;
    }
    
    obj = atividades.document.getElementsByTagName('INPUT');
    
    var marcado = false;
    
    for (var i = 0; i < obj.length; i++) {
      if (obj[i].type == 'checkbox') {
        if (obj[i].checked == true) {
          id      = obj[i].id.substr(6);     
          marcado = true; 
        }
      }
    }
    
    if (!marcado) {
      alert('Selecione um lançamento.');
      return false;
    }
    return js_gera_chaves();
  }
  
  function js_inscr(mostra) {
    var inscr = document.form1.q07_inscr.value;
    if (mostra) {
      js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_inscr','func_issbase.php?funcao_js=parent.js_mostrainscr|q02_inscr|z01_nome','Pesquisa',true);
    } else {
      if (inscr != '') {
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_inscr','func_issbase.php?pesquisa_chave='+inscr+'&funcao_js=parent.js_mostrainscr1','Pesquisa',false);
      } else {
        document.form1.z01_nome.value = '';
        document.form1.submit();  
      }
    }
  }

  function js_mostrainscr(chave1,chave2) {
    document.form1.q07_inscr.value = chave1;
    document.form1.z01_nome.value  = chave2;
    atividades.location.href       = 'iss1_tabativbaixaiframe.php?q07_inscr=' +chave1+ '&z01_nome=' +chave2;
    document.form1.submit(); 
    db_iframe_inscr.hide();
  }
  
  function js_mostrainscr1(chave,erro) {
    document.form1.z01_nome.value = chave; 
    if (erro) {
      document.form1.q07_inscr.focus(); 
      document.form1.q07_inscr.value = ''; 
    } else {
      document.form1.submit();
    }
  }

function js_cgm(mostra){
  const cgm = document.form1.z_numcgm.value;
  if(mostra){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe2','func_nome.php?funcao_js=parent.js_mostracgm|0|1','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe2','func_nome.php?pesquisa_chave='+cgm+'&funcao_js=parent.js_mostracgm1','Pesquisa',false);
  }
}

function js_mostracgm(chave1,chave2){
  document.form1.z_numcgm.value = chave1;
  document.form1.nomeCGM.value  = chave2;
  atividades.location.href      = 'iss1_tabativbaixaiframe.php?q07_inscr=' +chave1+ '&z01_nome=' +chave2;
  document.form1.submit(); 
  db_iframe2.hide();
}

function js_mostracgm1(erro, nome) {
  if (erro) {
    document.form1.z_numcgm.value = '';
  }
  document.form1.nomeCGM.value = nome;
  document.form1.submit();
} 
  
  <?php
    if (isset($q07_inscr) && $q07_inscr != '' || isset($z_numcgm) && $z_numcgm != '') {
  ?>
    document.form1.lancar.disabled      = false;
    document.form1.k00_histtxt.disabled = false;
  <?php
    } else {
  ?>
    document.form1.lancar.disabled      = true;
    document.form1.k00_histtxt.disabled = true;
  <?php
    }
  ?>
</script>