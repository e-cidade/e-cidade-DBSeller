<?php
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("dbforms/db_funcoes.php"));

require_once(modification("classes/db_arquivo_classe.php"));

if (is_null($z01_nome) || $z01_nome == "") {
    $cliptubase = new cl_iptubase;
    $result = $cliptubase->sql_record($cliptubase->sql_query($j01_matric));
    if ($cliptubase->numrows != 0) {
        $iptubase = db_utils::fieldsMemory($result, 0);
        $z01_nome = $iptubase->z01_nome;
    }
}
?>

<html>
<head>
  <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta   http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta   http-equiv="Expires"      content="0">

  <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
  <script rel="script" type="text/javascript" src="scripts/strings.js"></script>
  <script rel="script" type="text/javascript" src="scripts/prototype.js"></script>
  <script rel="script" type="text/javascript" src="scripts/arrays.js"></script>
  <script rel="script" type="text/javascript" src="scripts/widgets/DBFileUpload.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/classes/http/http.js"></script>

  <link href="estilos.css"            rel="stylesheet" type="text/css">
  <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">

</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<table align="center" width="790" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top" bgcolor="#CCCCCC">
    <center>
      <form id='formDownload'>
        <fieldset>
          <legend>
            <b>Dados da Matrícula</b>
          </legend>
          <table border="0" cellspacing="0" cellpadding="0" width=100%>
            <tr>
              <td width="208"><strong>Matrícula do Imóvel:</strong></td>
              <td><?php
                db_input('j01_matric', 10, 0, true, 'text', 3, "");
                db_input('z01_nome', 35, 0, true, 'text', 3, "");
                ?>            
              </td>
            </tr>
          </table>
        </fieldset>
        <br>
        <fieldset>
          <legend>
            <b>Anexos</b>
          </legend>
          <table border="0" cellspacing="0" cellpadding="0" width=100%>
            <tr>            
              <td>
                <fieldset>
                  <legend>
                    Enviar Arquivo
                  </legend>
                  <table class="form-container">                                      
                    <tr>
                      <td width="192"><strong>Arquivo: </strong></td>
                      <td>                      
                        <div id="ctnUpload"></div>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="3" title="Descrição do arquivo j151_descricao">
                        <fieldset style="margin-top:5px;">
                          <legend>
                            Descrição do arquivo:
                          </legend>
                          <?php db_input('db59_sequencial', 10, 0, false, 'hidden', 3, ""); ?>
                          <?php db_textarea('j151_descricao', 5, 101, '', true, 'text', ""); ?>
                          <input type="hidden" id="exec" name="exec">
                        </fieldset>
                      </td>
                    </tr>                    
                  </table>
                </fieldset>
                <div style="margin-top:10px;">
                  <center>
                    <input type="button" value="Salvar" id="btnSalvar">
                    <input type="button" value="Limpar" id="btnLimpar">                    
                  </center>
                </div>
              </td>
            </tr>
            <tr>            
              <td>
                <br>
                <fieldset>
                  <legend>
                    Lista de Anexos
                  </legend>
                  <div id="container_arquivos"></div>                  
                </fieldset>
                <div style="margin-top:5px;">
                  <center><input type="button" value="Download" id="btnDownload"></center>
                </div>
              </td>
            </tr>
          </table>
        </fieldset>
      </form>
    </center>
    </td>
  </tr>
</table>
<script rel="script" type="text/javascript">
  const 
    urlRpc = 'cad4_anexomatriculaimovel.RPC.php';
    oMatricula = $('j01_matric');
    btnSalvar =  $('btnSalvar');
    btnLimpar =  $('btnLimpar');
    btnDownload =  $('btnDownload');
    fileUpload = new DBFileUpload({callBack: retornoEnvioArquivo, labelButton: 'Arquivo'});
    oArquivosCollection = new Collection().setId('db59_sequencial');
    oGridArquivos = DatagridCollection.create(oArquivosCollection).configure("order", false);
    inputFile = initFileUpload();
    inputSequencial = $('db59_sequencial');
    inputDescricao = $('j151_descricao');

  initGrid();

  btnLimpar.addEventListener('click', event => {
    limparDados();
  });

  btnSalvar.addEventListener('click', event => {
    if(!validarDados()){
      return false;
    }

    if(inputSequencial.value !== ''){
      fileUpload.filePath  = fileUpload.extension  = undefined;
    }

    oParametros = {
      'exec' : 'salvar',
      'db59_sequencial' : inputSequencial.value,
      'j151_descricao' : inputDescricao.value,
      'j01_matric' : oMatricula.value,
      'filePath': fileUpload.filePath,
      'extension' : fileUpload.extension
    }

    salvar(oParametros);    
  });

  btnDownload.addEventListener('click', event => {
     var 
        form = $('formDownload'),
        hiddenField = $("exec"),
        arrCheckbox = [];

      hiddenField.setAttribute("value", "multipledownload");
      form.setAttribute("target", "_blank");
      form.setAttribute("method", "POST");
      form.setAttribute("action", urlRpc);

      var aLinhas = oGridArquivos.grid.getSelection("object");

      if(empty(aLinhas)){
        alert('Nenhum arquivo selecionado!');
      } else {
        for (var linha of aLinhas){
          var checkbox = document.createElement("input");        
          checkbox.setAttribute("type", "hidden");
          checkbox.setAttribute("checked", "checked");
          checkbox.setAttribute("name", `sequencialdownload[]`);
          checkbox.setAttribute("value", `${linha.aCells[1].getValue()}`);
          form.appendChild(checkbox);
          arrCheckbox.push(checkbox);
        }

        form.submit();

        for (var checkbox of arrCheckbox){
          checkbox.remove();
        }  
      }

  });

  function createFormData(oParametros){
    var formData = new FormData();
    for(parametro in oParametros){
      if(oParametros[parametro] instanceof Array){
        formData.append(`${parametro}[]`, oParametros[parametro]);
      } else {
        formData.append(parametro, oParametros[parametro]);
      }
    }
    return formData;
  }

  function salvar(oParametros){
    var formData = createFormData(oParametros);

    return new Promise((resolve, reject) => {
      HttpClient.post(urlRpc, {body: formData}).then(response => {
        alert(response.mensagem);
        limparDados();
        atualizaGrid();
      });
    });
  }  

  function retornoEnvioArquivo(retorno) {
    if (retorno.error) {
        alert(retorno.error);
        $('btnSalvar').disabled = true;

        return false;
    }
    $('btnSalvar').disabled = false;
  }

  function getArquivos(){
    var 
      oParametros = {
        'exec' : 'listar',
        'j01_matric' : oMatricula.value
      },
      formData = createFormData(oParametros);

    return HttpClient.post(urlRpc, {body: formData}).then(response => {
      return response.arrArquivos;
    });    
  }

  function atualizaGrid(){
    getArquivos().then(response => {
      oArquivosCollection.clear();
      for (var oArquivo of response) {

        oArquivosCollection.add({
          db59_sequencial    : oArquivo.db59_sequencial,
          j151_descricao     : oArquivo.j151_descricao,
        });
      }
      oGridArquivos.reload();
    });
  }

  function limparDados(){
    var btnArquivo = $('ctnUpload').querySelector('.btnUploadFile');

    inputFile.value = '';
    inputSequencial.value = '';
    inputDescricao.value = '';
    inputFile.disabled = false;
    btnArquivo.disabled = false;

  }

  function validarDados(){
    if(inputDescricao.value === ''){
      alert('Descrição Obrigatória!');
      return false;
    } else if (inputSequencial.value === '' && inputFile.value === ''){
      alert('Arquivo inválido');
      return false;
    }
    return true;
  }  

  function initFileUpload(){

    fileUpload.show($('ctnUpload'));
    const inputFile = $('ctnUpload').querySelector('.inputUploadFile');
    inputFile.addClassName('field-size5');

    return inputFile;
  }

  function initGrid(){
    
    oGridArquivos.grid.setCheckbox(1);
    oGridArquivos.addColumn("db59_sequencial",   {label : "Código",   "width" : "60px"}).setOption("align","center");
    oGridArquivos.addColumn("j151_descricao", {label : "Descrição", "width" : "440px"});

    oGridArquivos.addAction("Alterar", null, function(oEvento, oItem) {           
      var btnArquivo = $('ctnUpload').querySelector('.btnUploadFile');

      limparDados();
      inputSequencial.value = oItem.db59_sequencial;
      inputDescricao.value = oItem.j151_descricao;
      inputFile.disabled = true;      
      btnArquivo.disabled = true;

    });

    oGridArquivos.addAction("Excluir", null, function(oEvento, oItem) {

      if (!confirm("Deseja remover o arquivo?")) {
        return false;
      }

      var 
        oParametros = {
          "exec"        : "excluir",
          'j01_matric'  :    oMatricula.value,
          'db59_sequencial' : oItem.db59_sequencial
        },
        formData = createFormData(oParametros);

      HttpClient.post(urlRpc, {body: formData}).then(response => {
        alert(response.mensagem);
        atualizaGrid();
      });
    });

    oGridArquivos.addAction("Download", null, function(event, oItem) {    
      var 
        form = $('formDownload'),
        inputOldValue = inputSequencial.value,
        hiddenField = $("exec");

      hiddenField.setAttribute("value", "download");
      inputSequencial.value = oItem.db59_sequencial;

      form.setAttribute("target", "_blank");
      form.setAttribute("method", "POST");
      form.setAttribute("action", urlRpc);

      form.submit();

      inputSequencial.value = inputOldValue;

    });

    oGridArquivos.show($("container_arquivos"));
    atualizaGrid();

  }  
</script>
</body>
</html>
