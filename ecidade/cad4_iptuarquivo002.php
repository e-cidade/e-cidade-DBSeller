<?php
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("dbforms/db_funcoes.php"));

require_once(modification("classes/db_arquivo_classe.php"));
$j01_matric = $parametro;
?>

<table align="center" width="790" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top" bgcolor="#CCCCCC">
    <center>
      <form id='formDownload'>
        <?php db_input('j01_matric', 10, 0, false, 'hidden', 3, ""); ?>
        <input type="hidden" id='exec' name='exec'>
        <input type="hidden" id='db59_sequencial' name='db59_sequencial' >
        <fieldset>
          <legend>          
            <b>Anexos</b>
          </legend>
          <div id="container_arquivos"></div>          
          <div style="margin-top:5px;">
            <center><input type="button" value="Download" id="btnDownload"></center>
          </div>
        </fieldset>
      </form>
    </center>
    </td>
  </tr>
</table>
<script rel="script" type="text/javascript">
  const 
    urlRpc = 'cad4_anexomatriculaimovel.RPC.php';
    oMatricula = $('j01_matric'),
    btnDownload =  $('btnDownload'),
    oArquivosCollection = new Collection().setId('db59_sequencial'),
    oGridArquivos = DatagridCollection.create(oArquivosCollection).configure("order", false);

  initGrid();

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

  function initGrid(){
    
    oGridArquivos.grid.setCheckbox(1);
    oGridArquivos.addColumn("db59_sequencial",   {label : "Código",   "width" : "100px"}).setOption("align","center");
    oGridArquivos.addColumn("j151_descricao", {label : "Descrição", "width" : "500px"});    

    oGridArquivos.addAction("Download", null, function(event, oItem) {    
      var 
        form = $('formDownload'),
        hiddenField = $('exec'),
        inputSequencial = $('db59_sequencial');

      hiddenField.setAttribute("value", "download");
      inputSequencial.value = oItem.db59_sequencial;

      form.setAttribute("target", "_blank");
      form.setAttribute("method", "POST");
      form.setAttribute("action", urlRpc);

      form.submit();      

    });

    oGridArquivos.show($("container_arquivos"));
    atualizaGrid();

  }  
</script>
