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
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("dbforms/db_classesgenericas.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_utils.php"));

$clrotulo = new rotulocampo;
$clrotulo->label('r44_selec');
$clrotulo->label("z01_nome");

$oDaoRhPessoal = new cl_rhpessoal();
?>
<html>
  <head>
    <?php
      db_app::load("scripts.js, estilos.css"); 
    ?>
  </head>
  
  <body>
    <div class="container">
    <form name="form1" method="post" action="" >
      <fieldset>
        <legend>Relatório de Pensão por Dependente</legend>
        <table class="form-container">
          <tr> 
            <td title="Seleção:" >
              <?php
                db_ancora($Lr44_selec,"js_pesquisasel(true)",1);
              ?>
            </td>
            <td>
              <?php
                db_input('r44_selec',4,$Ir44_selec,true,'text',2,'onchange="js_pesquisasel(false)"');
                db_input('r44_descr',40,$Ir44_selec,true,'text',3,'');
              ?>
            </td>
          </tr>
          <tr>
            <td>
              Ano / Mês Inicial:
            </td>
            <td>
              
              <?php
                db_input('anoInicial', 4, true, "text", 1, "", "", "", "", "", 4);
                echo " / ";
                db_input('mesInicial', 2, true, "text", 1, "", "", "", "", "", 2);
              ?>
            </td>
          </tr>
          <tr>
            <td>
              Ano / Mês Final:
            </td>
            <td>
              <?php
                db_input('anoFinal', 4, true, "text", 1, "", "", "", "", "", 4);
                echo " / ";
                db_input('mesFinal', 2, true, "text", 1, "", "", "", "", "", 2);
              ?>
            </td>
          </tr>
          <tr>
            <td title="Ordem" >
              Ordem :
            </td>
            <td>
              <?php
                
                $xv = array("nome"      => "Nome",
                            "matricula" => "Matrícula");
                db_select('ordem', $xv, true, 4, "");
              ?>
            </td>
          </tr>
         <tr>
            <td title="Quebra" >
              Quebra de página:
            </td>
            <td>
              <?php
                
                $xv = array("semquebra"  => "Sem Quebra",
                            "servidor"   => "Servidor",
                            "dependente" => "Dependente");
                db_select('quebra', $xv, true, 4, "");
              ?>
            </td>
          </tr>
          <tr>
            <td title="Observação" >
              Mostrar Observações :
            </td>
            <td>
              <?php
                
                $xv = array("f" => "NÃO",
                            "t" => "SIM");
                db_select('mostraobservacao', $xv, true, 4, "");
              ?>
            </td>
          </tr>          
          <tr>
            <td colspan="2">
              <table style="width: 100%">
                <tr>
                  <td>
                    <?php 
                      
                      $aux                                  = new cl_arquivo_auxiliar;
                      $aux->cabecalho                       = "MATRÍCULAS SELECIONADAS";
                      $aux->codigo                          = "rh01_regist";
                      $aux->descr                           = "z01_nome";
                      $aux->nomeobjeto                      = 'matriculas_selecionadas';
                      $aux->obrigarselecao                  = false;
                      $aux->funcao_js                       = 'js_mostra';
                      $aux->funcao_js_hide                  = 'js_mostra1';
                      $aux->func_arquivo                    = "func_rhpessoal.php";
                      $aux->nomeiframe                      = "db_iframe_rhpessoal";
                      $aux->executa_script_apos_incluir     = "document.form1.rh01_regist.focus();";
                      $aux->mostrar_botao_lancar            = true;
                      $aux->executa_script_lost_focus_campo = "js_insSelectmatriculas_selecionadas()";
                      $aux->executa_script_change_focus     = "document.form1.rh01_regist.focus();";
                      $aux->passar_query_string_para_func   = "&instit=" . db_getsession("DB_instit");
                      $aux->localjan                        = "";
                      $aux->db_opcao                        = 2;
                      $aux->tipo                            = 2;
                      $aux->top                             = 20;
                      $aux->linhas                          = 10;
                      $aux->vwidth                          = "360";
                      $aux->funcao_gera_formulario();
                      
                    ?>
                  </td>
                </tr> 
              </table>
            </td>
          </tr>
        </table>
        <?php db_menu(); ?>
      </fieldset>
      <input  name="emite" id="emite" type="button" value="Processar" onclick="js_emite();" >
    </form>
    </div>
  </body>
</html>
<?php
  
  if ( isset( $ordem ) ) {
    echo "<script>
       js_emite();
       </script>";
  }
  
  $func_iframe = new janela('db_iframe', '');
  $func_iframe->posX           = 1;
  $func_iframe->posY           = 20;
  $func_iframe->largura        = 780;
  $func_iframe->altura         = 430;
  $func_iframe->titulo         = 'Pesquisa';
  $func_iframe->iniciarVisivel = false;
  
  $func_iframe->mostrar();

?>
<script>
function js_emite () {

  if ($F('anoInicial') > $F('anoFinal')) {
	  alert("Ano Inicial maior que o Ano Final");
	  return false;
  }

  sUrl  = 'pes2_penalimendependente002.php';
  sUrl += '?iSelecao=' + document.form1.r44_selec.value;
  sUrl += '&iMesInicial=' + document.form1.mesInicial.value;
  sUrl += '&iAnoInicial=' + document.form1.anoInicial.value; 
  sUrl += '&iMesFinal=' + document.form1.mesFinal.value;
  sUrl += '&iAnoFinal=' + document.form1.anoFinal.value;
  sUrl += '&sOrdem=' + document.form1.ordem.value;
  sUrl += '&sQuebra=' + document.form1.quebra.value;
  sUrl += '&aMatriculas=' + js_campo_recebe_valores();
  sUrl += '&lMostraObservacao=' + document.form1.mostraobservacao.value;
  oJanela = window.open(sUrl, '', 'width=' + (screen.availWidth - 5) + ',height=' + (screen.availHeight - 40) + ', scrollbars=1, location=0 ');
  oJanela.moveTo(0, 0);
  return true;

}


function js_insere_matri () {
  
  var valor = document.getElementById('matriculas_selecionadas_text').value.trim();
  
  if ( valor == '' ) {
    if ( st ) {
      clearTimeout(st);
    }
    return false;
  }
  
  var array = valor.split(",");
  
  for ( var i = 0; i < array.length; i++ ) {
    document.getElementById('rh01_regist').value = array[i];
    js_BuscaDadosArquivomatriculas_selecionadas(false);
    document.getElementById('matriculas_selecionadas_text').value = 
      ( array.slice( i + 1, array.length ).implode(',') ).trim();
    var st = setTimeout(js_insere_matri, 500);
    break;
  }
  
}

function js_pesquisasel ( mostra ){
  if ( mostra == true ) {
    js_OpenJanelaIframe('CurrentWindow.corpo',
  	                    'db_iframe_selecao',
  	                    'func_selecao.php?funcao_js=parent.js_mostrasel1|r44_selec|r44_descr',
  	                    'Pesquisa',
  	                    true);
  } else {
    if ( document.form1.r44_selec.value != '' ) {
      js_OpenJanelaIframe('CurrentWindow.corpo',
                          'db_iframe_selecao',
                          'func_selecao.php?pesquisa_chave=' + document.form1.r44_selec.value + 
                            '&funcao_js=parent.js_mostrasel',
                          'Pesquisa',
                          false);
    } else {
      document.form1.r44_descr.value = '';
    }
  }
}

function js_mostrasel ( chave, erro ) {
  document.form1.r44_descr.value = chave;
  if ( erro == true ) {
    document.form1.r44_selec.focus(); 
    document.form1.r44_selec.value = '';
  }
}


function js_mostrasel1 ( chave1, chave2 ) {
  document.form1.r44_selec.value = chave1;
  document.form1.r44_descr.value = chave2;
  db_iframe_selecao.hide();
}


function js_pesquisasel ( mostra ) {
  if ( mostra == true ) {
    js_OpenJanelaIframe('CurrentWindow.corpo',
                        'db_iframe_selecao',
                        'func_selecao.php?funcao_js=parent.js_mostrasel1|r44_selec|r44_descr',
                        'Pesquisa',
                        true);
  } else {
    if (document.form1.r44_selec.value != '') {
      js_OpenJanelaIframe('CurrentWindow.corpo',
                          'db_iframe_selecao',
                          'func_selecao.php?pesquisa_chave=' + document.form1.r44_selec.value +
                            '&funcao_js=parent.js_mostrasel',
                          'Pesquisa',
                          false);
    } else {
      document.form1.r44_descr.value = '';
    }
  }
}
  
function js_mostrasel ( chave, erro ) {
  document.form1.r44_descr.value = chave;
  if ( erro == true ) {
    document.form1.r44_selec.focus(); 
    document.form1.r44_selec.value = '';
  }
}

function js_mostrasel1 ( chave1, chave2 ) {
  document.form1.r44_selec.value = chave1;
  document.form1.r44_descr.value = chave2;
  db_iframe_selecao.hide();
}
</script>