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
require_once(modification("classes/db_issbase_classe.php"));
require_once(modification("classes/db_iptubase_classe.php"));
require_once(modification("classes/db_cgm_classe.php"));
db_postmemory($HTTP_SERVER_VARS);
db_postmemory($HTTP_POST_VARS);
$db_botao=1;
$db_opcao=1;
$cliptubase = new cl_iptubase;
$cliptubase->rotulo->label();
$clissbase = new cl_issbase;
$clissbase->rotulo->label("q02_inscr");
$clcgm = new cl_cgm;
$clcgm->rotulo->label("z01_numcgm");
$clcgm->rotulo->label("z01_nome");
?>

<html>

<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/classes/http/http.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor=#CCCCCC>
<form class="container" name="form1" method="post" action="dvr3_diversos005.php?pri=true" enctype="multipart/form-data" onSubmit="return js_verifica_campos_digitados();" >
  <fieldset>
    <legend>Procedimentos - Diversos</legend>
    <table class="form-container">
      <tr>   
        <td>
          <?php
            db_ancora($Lz01_nome,' js_cgm(true); ',1);
          ?>
         </td>
         <td> 
          <?php
            db_input('z01_numcgm',5,$Iz01_numcgm,true,'text',1,"onchange='js_cgm(false)'","z_numcgm");
            db_input('z01_nome',30,0,true,'text',3,"","z01_nomecgm");
          ?>
         </td>
      </tr>
      <tr>   
        <td>
          <?php
            db_ancora($Lj01_matric,' js_matri(true); ',1);
          ?>
        </td>
        <td> 
          <?php
            db_input('j01_matric',5,$Ij01_matric,true,'text',1,"onchange='js_matri(false)'");
            db_input('z01_nome',30,0,true,'text',3,"","z01_nomematri");
          ?>
        </td>
      </tr>
      <tr>   
        <td>
          <?php
            db_ancora($Lq02_inscr,' js_inscr(true); ',1);
          ?>
        </td>
        <td> 
          <?php
            db_input('q02_inscr',5,$Iq02_inscr,true,'text',1,"onchange='js_inscr(false)'");
            db_input('z01_nome',30,0,true,'text',3,"","z01_nomeinscr");
          ?>
        </td>
      </tr>
      <tr>
        <td><strong>Inclusão em lote:</strong></td>
        <td>
          <select style="width:83px;" name="selectImportarPlanilha" id="selectImportarPlanilha">
            <option value="">Selecionar</option>
            <option value="cgm">CGM</option>
            <option value="matric">Matrícula</option>
            <option value="inscr">Inscrição</option>
          </select>

          <input type="file" id="importarplanilha" name="importarplanilha" onchange="js_montaGridPLanilha()" style="width:298px; height: 23px;">
          <input type="hidden"id="dadosValidos" name="dadosValidos" >
          <input type="hidden"id="dadosInvalidos" name="dadosInvalidos" >

        </td>
      </tr>
    </table> 	 
  </fieldset>
  <input type="submit" name="pesquisar" value="Pesquisar" onclick="return js_testacamp();" >
  <input type="button" name="importar" value="Importar Lista" onclick="js_verificaPlanilha();" disabled='true'>
</form>
<?php
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
<br>
<fieldset id="fieldsetDadosValidos" style="width:15%;float:left;margin-left:33.2%;display:none">
  <legend>Dados Validos</legend>
    <div id="container_dados_validos"></div>
</fieldset>
<fieldset id="fieldsetDadosInvalidos" style="width:15%;float:left;display:none">
  <legend>Dados Inválidos ou Inexistentes</legend>
  <div id="container_dados_invalidos"></div>
</fieldset>

</body>
</html>

<script>
function js_testacamp(){
  var matri = document.form1.j01_matric.value;
  var inscr = document.form1.q02_inscr.value;
  var numcgm = document.form1.z_numcgm.value;
  if(matri == "" && inscr == "" && numcgm == ""){
    alert(_M("tributario.diversos.dvr3_diversos004.informe_campos"));   
    return false;  
  }
  if(!validarInclusaoEmLote()){
    return false;  
  }
  return true;  

} 

function js_verificaPlanilha() {
  const selectImportarPlanilha = document.getElementById('selectImportarPlanilha').value;

  if (selectImportarPlanilha == "") {
    alert("Selecione uma origem.");
    return false;
  }

  const file = document.getElementById('importarplanilha');
  const reader = new FileReader();
  reader.readAsText(file.files[0]);

  reader.onload = function(csv) {
    var linhaCSV = csv.target.result.split('\n');
    const aLabels = linhaCSV[0].split(",");

    var indice = null;

    for (let index = 0; index < aLabels.length; index++) {
      const element = aLabels[index];

      if (selectImportarPlanilha == element) {
        indice = index;
      }

    }

    if (indice == null) {
      alert("Coluna não encontrada no documento selecionado");
      return false;
    } else {
      document.form1.submit();
    }

  };
}


function js_montaGridPLanilha() {

  const selectImportarPlanilha = document.getElementById('selectImportarPlanilha').value;

  if (selectImportarPlanilha == "") {
    alert("Selecione uma origem.");
    return false;
  } else {

    // Pega e manipula dados da planilha
    const file = document.getElementById('importarplanilha');
    const reader = new FileReader();
    reader.readAsText(file.files[0]);

    reader.onload = function(csv) {
      var linhaCSV = csv.target.result.split('\n');
      const aLabels = linhaCSV[0].split(",");

      var indice = null;

      for (let index = 0; index < aLabels.length; index++) {
        const element = aLabels[index];

        if (selectImportarPlanilha == element) {
          indice = index;
        }

      }

      if (indice == null) {
        alert("Coluna não encontrada no documento selecionado");
        document.getElementById('importarplanilha').value = "";
        return false;
      } else {
        document.form1.pesquisar.disabled = true;
        document.form1.importar.disabled = false;
        document.form1.selectImportarPlanilha.required = true;

        // Revela os fieldsets
        document.getElementById("fieldsetDadosValidos").style.display = 'block';
        document.getElementById("fieldsetDadosInvalidos").style.display = 'block';

        // Cria as Grids para inserir os dados da planilha
        const oGridDadosValidosCollection = new Collection().setId('id');
        const oGridDadosInvalidosCollection = new Collection().setId('id');

        const oGridDadosValidos = DatagridCollection.create(oGridDadosValidosCollection).configure({"order" : false, "height" : "400px"});
        const oGridDadosInvalidos = DatagridCollection.create(oGridDadosInvalidosCollection).configure({"order" : false, "height" : "400px"});

        oGridDadosValidos.addColumn('matric',   {label : 'Válidos',   "width" : "15%"}).setOption("align","center");
        oGridDadosInvalidos.addColumn('matric',   {label : 'Inválidos',   "width" : "15%"}).setOption("align","center");

        oGridDadosValidos.show($('container_dados_validos'));
        oGridDadosInvalidos.show($('container_dados_invalidos'));

        const arrayEnvioValidos = [];
        const arrayEnvioInvalidos = [];
        const arrayDadosValidos = [];
        const arrayDadosInvalidos = [];

        oGridDadosValidos.clear();
        oGridDadosInvalidos.clear();

        var oDadosValidos = {};
        var oDadosInvalidos = {};

        for (var i=1; i < linhaCSV.length; i++) {
          
          const aLinha = linhaCSV[i].split(',');

          const item = aLinha[indice];

          if (item == undefined || item == "") {
            continue;
          }

          // Verifica se os campos da planilha são números inteiros
          if (Number.isInteger(parseInt(item, 10))) {
            arrayDadosValidos.push(item);
          } else {
            arrayDadosInvalidos.push(item);
          }

        }

        // Verifica os dados válidos se são existentes nas tabelas do banco
        var request = new AjaxRequest(
          'dvr3_diversos004.RPC.php',
          {
            exec: 'verificaDadosValidos',
            tipoDados: selectImportarPlanilha,
            dadosValidos: arrayDadosValidos
          },
          function(response) {

            if (response.erro) {
              alert(response.sMensagem);
              return;
            }

            // Insere na grid de Dados Válidos
            for (var i=0; i < response.arrayDadosExistentes.length; i++) {

              const aLinha = response.arrayDadosExistentes[i].split(',');
              const item = aLinha[0];

              arrayEnvioValidos.push(item);
              oDadosValidos = {id: i, matric: item};
              oGridDadosValidosCollection.add(oDadosValidos);

            }

            // Desmembra array de Dados Invalidos (que não são numeros inteiros) da planilha e insere na array de dados inexistentes
            for (var i=0; i < arrayDadosInvalidos.length; i++) {
              const aLinha = arrayDadosInvalidos[i].split(',');
              const item = aLinha[0];
              response.arrayDadosInexistentes.push(item);
            }

            // Insere na grid de Dados Inválidos ou Inexistentes
            for (var i=0; i < response.arrayDadosInexistentes.length; i++) {

              const aLinha = response.arrayDadosInexistentes[i].split(',');
              const item = aLinha[0];

              arrayEnvioInvalidos.push(item);
              oDadosInvalidos = {id: i, matric: item};
              oGridDadosInvalidosCollection.add(oDadosInvalidos);

            }
          }
        );

        request
        .setMessage('Aguarde, buscando dados da planilha.')
        .asynchronous(false)
        .execute();

        oGridDadosValidos.reload();
        oGridDadosInvalidos.reload();

        document.getElementById('dadosValidos').value = JSON.stringify(arrayEnvioValidos);
        document.getElementById('dadosInvalidos').value = JSON.stringify(arrayEnvioInvalidos);
      }
    };

  }

}

function js_matri(mostra){
  var matri=document.form1.j01_matric.value;
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe3','func_iptubase.php?funcao_js=parent.js_mostramatri|0|1','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe3','func_iptubase.php?pesquisa_chave='+matri+'&funcao_js=parent.js_mostramatri1','Pesquisa',false);
  }
}

function js_mostramatri(chave1,chave2){
  document.form1.j01_matric.value = chave1;
  document.form1.z01_nomematri.value = chave2;
  db_iframe3.hide();
}

function js_mostramatri1(chave,erro){
  document.form1.z01_nomematri.value = chave; 
  if(erro==true){ 
    document.form1.j01_matric.focus(); 
    document.form1.j01_matric.value = ''; 
  }
}

function js_inscr(mostra){
  var inscr=document.form1.q02_inscr.value;
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe','func_issbase.php?funcao_js=parent.js_mostrainscr|q02_inscr|z01_nome','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe','func_issbase.php?pesquisa_chave='+inscr+'&funcao_js=parent.js_mostrainscr1','Pesquisa',false);
  }
}

function js_mostrainscr(chave1,chave2){
  document.form1.q02_inscr.value = chave1;
  document.form1.z01_nomeinscr.value = chave2;
  db_iframe.hide();
}

function js_mostrainscr1(chave,erro){
  document.form1.z01_nomeinscr.value = chave; 
  if(erro==true){ 
    document.form1.q02_inscr.focus(); 
    document.form1.q02_inscr.value = ''; 
  }
}

function js_cgm(mostra){
  var cgm=document.form1.z_numcgm.value;
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe2','func_nome.php?funcao_js=parent.js_mostracgm|0|1','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe2','func_nome.php?pesquisa_chave='+cgm+'&funcao_js=parent.js_mostracgm1','Pesquisa',false);
  }
}
function js_mostracgm(chave1,chave2){
  document.form1.z_numcgm.value = chave1;
  document.form1.z01_nomecgm.value = chave2;
  db_iframe2.hide();
}
function js_mostracgm1(erro,chave){
  document.form1.z01_nomecgm.value = chave; 
  if(erro==true){ 
    document.form1.z_numcgm.focus(); 
    document.form1.z_numcgm.value = ''; 
  }
}

function validarInclusaoEmLote(){
  const inclusaoLote = document.getElementById('selectImportarPlanilha').value;
  const arquivo = document.getElementById('importarplanilha').value;
  const inclusaoSemArquivo = inclusaoLote != "" && arquivo == "";
  const arquivoSemInclusao = arquivo != "" && inclusaoLote == "";

  if(inclusaoSemArquivo){
    alert("Informe um arquivo.");   
    return false
  }
  if(arquivoSemInclusao){
    alert("Selecione um tipo de inclusão.");   
    return false
  }
    return true
}

$("z_numcgm").addClassName("field-size2");
$("z01_nomecgm").addClassName("field-size7");
$("j01_matric").addClassName("field-size2");
$("z01_nomematri").addClassName("field-size7");
$("q02_inscr").addClassName("field-size2");
$("z01_nomeinscr").addClassName("field-size7");
</script>

<?php
if(isset($dado) && $dado=="inscr"){
  db_msgbox(_M("tributario.diversos.dvr3_diversos004.inscricao_invalida"));
}  
if(isset($dado) && $dado=="matric"){
  db_msgbox(_M("tributario.diversos.dvr3_diversos004.matriula_invalida"));
}  
if(isset($dado) && $dado=="numcgm"){
  db_msgbox(_M("tributario.diversos.dvr3_diversos004.numcgm_invalido"));
}  
?>
