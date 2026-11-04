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

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
require(modification("libs/db_utils.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_classesgenericas.php"));
include(modification("dbforms/db_funcoes.php"));
$clrotulo = new rotulocampo;
$clrotulo->label("e80_data");
$clrotulo->label("e83_codtipo");
$clrotulo->label("e80_codage");
$clrotulo->label("e50_codord");
$clrotulo->label("e50_numemp");
$clrotulo->label("e60_numemp");
$clrotulo->label("e60_codemp");
$clrotulo->label("z01_numcgm");
$clrotulo->label("z01_nome");
$clrotulo->label("e60_emiss");
$clrotulo->label("e82_codord");
$clrotulo->label("e87_descgera");
$clrotulo->label("o15_descr");
$clrotulo->label("o15_codigo");
$clrotulo->label("e21_sequencial");
$clrotulo->label("e21_descricao");
$db_opcao = 1;

$iAnoUsu   = db_getsession("DB_anousu");
$sFonteRel = "cai2_movimento_extra_financeiro002.php";
$sLabelMsg = "Nome Relatorio";
?>
<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
  </head>
  <body bgcolor=#CCCCCC  onLoad="a=1" >
  <br>
  <br>
    <form name='form1'>
      <center>
        <table border=0>
          <tr>
            <td>
              <fieldset>
                <legend>
                  <b>Contas Extras</b>
                </legend>
                <table border=0>
                  <tr>
                    <td nowrap  >
                      <b>Posição Até :</b>
                    </td>
                    <td nowrap >
                      <?php
                       db_inputdata("datainicial",null,null,null,true,"text", 1);
                      ?>
                      </td>
                  </tr>
                  <tr><td valign='top' colspan=4>&nbsp;</td></tr>
                </table>
              </fieldset>
            </td>
            <tr>
             <td> &nbsp;</td>
            </tr>
            <tr>
             <td align="center">
               <input title="Emite o Relatório para os Filtros Selecionados." type="button" name="BtnEmitir" value="Emitir"
                      onclick="js_emitir();"/>
             </td>
            </tr>
            <tr>
             <td> &nbsp;</td>
            </tr>
          </tr>
          <tr>
              <?php
              $oFiltroConta = new cl_arquivo_auxiliar;
          	  $oFiltroConta->cabecalho = "<strong>Recursos</strong>";
          	  $oFiltroConta->codigo = "o15_codigo";
          	  $oFiltroConta->descr  = "o15_descr";
          	  $oFiltroConta->isfuncnome = true;
          	  $oFiltroConta->nomeobjeto = 'listasaltes';
          	  $oFiltroConta->funcao_js = 'js_mostra';
          	  $oFiltroConta->funcao_js_hide = 'js_mostra1';
          	  $oFiltroConta->sql_exec  = "";
          	  $oFiltroConta->func_arquivo = "func_orctiporec.php";
          	  $oFiltroConta->nomeiframe = "db_iframe_recurso";
          	  $oFiltroConta->localjan = "";
          	  $oFiltroConta->db_opcao = 2;
          	  $oFiltroConta->tipo = 2;
          	  $oFiltroConta->top = 0;
          	  $oFiltroConta->linhas = 10;
          	  $oFiltroConta->vwhidth = "200";
          	  $oFiltroConta->funcao_gera_formulario();
              ?>
          </tr>
          <tr>
            <td colspan="4" style='text-align:center'>
              <!--<input type='button' value='Emitir' onclick='js_emitir()'> -->
            </td>
          </tr>
        </table>
      </center>
    </form>
  </body>
</html>
<?php
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>

<script>
function js_gerar_relatorio(){

  //var sel_instit  = new Number(document.form1.db_selinstit.value);
  var sel_periodo = document.form1.datainicial.value;
  var oRecursos      = $('listasaltes');
  <?php

    if(!file_exists($sFonteRel)) {
      echo "alert('Relatório não disponível para o exercício $iAnoUsu');";
      echo "return false;";
    }

  ?>

  if (sel_periodo == "0" || sel_periodo == "" || sel_periodo == null){
    alert("Selecione uma posição");
    document.form1.datainicial.focus();
    return false;
  }


  var query = "";
  var obj   = document.form1;
  var sListaContas = "";
  var vir          = "";
  for(var y = 0; y < oContas.options.length; y++){

    sListaContas += vir + oContas.options[y].value;
    vir = ",";
  }

   // query  = "db_selinstit="+obj.db_selinstit.value;
    query += "&periodo="+obj.datainicial.value;

    obj = document.form1;

    jan = window.open('<?=$sFonteRel?>?'+query,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
    jan.moveTo(0,0);
//  }

}
</script>

<?//////////////////////////// ?>

<script>


function js_emitir(){


  if ($F('datainicial') == "") {

    alert('Selecione uma posição');
    document.form1.datainicial.focus();
    return false;

  }
  var oRecursos      = $('listasaltes');
  var sListaContas = "";
  var vir          = "";
  for(var y = 0; y < oRecursos.options.length; y++){

    sListaContas += vir + oRecursos.options[y].value;
    vir = ",";

  }

  var oParametro         = new Object();
  oParametro.periodo     = $F('datainicial');
  /*
  oParametro.datafinal   = $F('datafinal');
  oParametro.iPagamento  = $F('pagamento');
  oParametro.iOrdemIni   = $F('e82_codord');
  oParametro.iOrdemFim   = $F('e82_codord02');
  oParametro.iRecurso    = $F('o15_codigo');
  oParametro.iRetencao   = $F('e21_sequencial');
  oParametro.iNumCgm     = $F('z01_numcgm');
  oParametro.order       = $F('order');
  oParametro.group       = $F('group');
  */
  oParametro.sRecursos     = sListaContas;

  var sFiltros = JSON.stringify(oParametro);

  var sUrlRelatorio = "cai2_movimento_extra_financeiro002.php?sFiltros="+sFiltros;
  window.open(sUrlRelatorio, '', 'width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
    jan.moveTo(0,0);

}

function js_pesquisaz01_numcgm(mostra){
  if(mostra==true){

    js_OpenJanelaIframe('','func_nome','func_nome.php?funcao_js=parent.js_mostracgm1|z01_numcgm|z01_nome','Pesquisa',true);
  }else{

     if(document.form1.z01_numcgm.value != ''){
        js_OpenJanelaIframe('','func_nome','func_nome.php?pesquisa_chave='+document.form1.z01_numcgm.value+'&funcao_js=parent.js_mostracgm','Pesquisa',false);
     }else{
       document.form1.z01_nome.value = '';
     }

  }
}

function js_mostracgm1(chave,chave1) {

  document.form1.z01_nome.value   = chave1;
  document.form1.z01_numcgm.value = chave;
  func_nome.hide();

}

function js_mostracgm(erro,chave) {

  document.form1.z01_nome.value = chave;
  if(erro==true) {

    document.form1.z01_numcgm.focus();
    document.form1.z01_numcgm.value = '';

  }
}

function js_pesquisae21_sequencial(lMostra){

  var sFuncao = '';
  if(lMostra){
    sFuncao = "funcao_js=parent.js_mostraretencao1|e21_sequencial|e21_descricao";
  } else {

    if ($F('e21_sequencial') == "") {

      $('e21_descricao').value = '';
      return ;

    }
    sFuncao ="pesquisa_chave="+$F('e21_sequencial')+"&funcao_js=parent.js_mostraretencao";

  }
  js_OpenJanelaIframe('CurrentWindow.corpo',
                      'db_iframe_retencaotiporec',
                      'func_retencaotiporec.php?'+sFuncao,
                      'Consulta Retencoes', lMostra);
}

function js_mostraretencao1(val, val1) {

  $('e21_sequencial').value = val;
  $('e21_descricao').value  = val1;
  db_iframe_retencaotiporec.hide();

}

function js_mostraretencao(chave, erro) {

  $('e21_descricao').value  = chave;
  if(erro) {

    $('e21_sequencial').focus();
    $('e21_sequencial').value = '';

  }
}

function js_pesquisae82_codord(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_pagordem','func_pagordem.php?funcao_js=parent.js_mostrapagordem1|e50_codord','Pesquisa',true);
  }else{
    ord01 = new Number(document.form1.e82_codord.value);
    ord02 = new Number(document.form1.e82_codord02.value);
    if(ord01 > ord02 && ord01 != "" && ord02 != ""){
      alert("Selecione uma ordem menor que a segunda!");
      document.form1.e82_codord.focus();
      document.form1.e82_codord.value = '';
    }
  }
}
function js_mostrapagordem1(chave1){
  document.form1.e82_codord.value = chave1;
  db_iframe_pagordem.hide();
}
//-----------------------------------------------------------
//---ordem 02
function js_pesquisae82_codord02(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_pagordem','func_pagordem.php?funcao_js=parent.js_mostrapagordem102|e50_codord','Pesquisa',true);
  }else{
    ord01 = new Number(document.form1.e82_codord.value);
    ord02 = new Number(document.form1.e82_codord02.value);
    if(ord01 > ord02 && ord02 != ""  && ord01 != ""){
      alert("Selecione uma ordem maior que a primeira");
      document.form1.e82_codord02.focus();
      document.form1.e82_codord02.value = '';
    }
  }
}
function js_mostrapagordem102(chave1,chave2){
  document.form1.e82_codord02.value = chave1;
  db_iframe_pagordem.hide();
}
function js_pesquisae60_codemp(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_empempenho','func_empempenho.php?funcao_js=parent.js_mostraempempenho2|e60_codemp','Pesquisa',true);
  }else{
   // js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_empempenho02','func_empempenho.php?pesquisa_chave='+document.form1.e60_numemp.value+'&funcao_js=parent.js_mostraempempenho','Pesquisa',false);
  }
}

function js_mostraconta1(chave1,chave){
    document.form1.k13_descr.value = chave1;
  if(chave){
    document.form1.k13_conta.value = '';
    document.form1.k13_conta.focus();
  }else{
    ;
    document.form1.db_lanca.onclick = js_insSelectsaltes;
  }
  func_saltes.hide();
}


function js_pesquisac62_codrec(mostra){
   if(mostra==true){
       js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_orctiporec','func_orctiporec.php?funcao_js=parent.js_mostraorctiporec1|o15_codigo|o15_descr','Pesquisa',true);
   }else{
       if(document.form1.o15_codigo.value != ''){
           js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_orctiporec','func_orctiporec.php?pesquisa_chave='+document.form1.o15_codigo.value+'&funcao_js=parent.js_mostraorctiporec','Pesquisa',false);
       }else{
           document.form1.o15_descr.value = '';
       }
   }
}
function js_mostraorctiporec(chave,erro){
   document.form1.o15_descr.value = chave;
   if(erro==true){
      document.form1.o15_codigo.focus();
      document.form1.o15_codigo.value = '';
   }
}

function js_mostraorctiporec1(chave1,chave2){
    document.form1.o15_codigo.value = chave1;
    document.form1.o15_descr.value = chave2;
    db_iframe_orctiporec.hide();
}
</script>