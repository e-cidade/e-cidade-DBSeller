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
require_once(modification("classes/db_lab_laboratorio_classe.php"));
require_once(modification('libs/db_utils.php'));
require_once(modification("libs/db_stdlibwebseller.php"));
$oConfig = loadConfig("lab_parametros");
$daoLabLaboratorio = new cl_lab_laboratorio;
$clrotulo          = new rotulocampo;

$iUsuario = db_getsession('DB_login');
$iDepto = db_getsession('DB_coddepto');

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
<script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
 <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBLancador.widget.js"></script>
 <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" >

<table valign="top" marginwidth="0" width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="430" align="center" valign="top" bgcolor="#CCCCCC">
      <center>
      <br><br>
      <fieldset style='width: 75%;'> <legend><b>Relatório de Pendências da Triagem</b></legend>
      <form name='form1'>
        <table border="0">
          <tr>
              <td nowrap>
                <b style="margin-left: 5px"> Inicio:</b>
                  <?php db_inputdata('la02_d_datainicio',
                      @$la02_d_datainicio_dia,
                      @$la02_d_datainicio_mes,
                      @$la02_d_datainicio_ano,
                      true,
                      'text',
                      1);?>
                  <b style="margin-left: 30px">Fim:</b>
                  <?php db_inputdata('la02_d_datafim',
                      @$la02_d_datafim_dia,
                      @$la02_d_datasaida_mes,
                      @$la02_d_datasaida_ano,
                      true,
                      'text',
                      1);?>
              </td>
          </tr>
          <tr>
            <td align="right" colspan="4">
              <?php
              $unidadesDispensadasTriagem = $oConfig->la49_unidadesdispensadastriagem;
              $whereUnidades = "la02_i_codigo not in ($unidadesDispensadasTriagem)";              
              $sSql           = $daoLabLaboratorio->sql_query("","la02_i_codigo,la02_c_descr",null,$whereUnidades);
              $rsLaboratorios = db_query($sSql);
              $labs = [];
              
              for($i=0; $i < pg_num_rows($rsLaboratorios); $i++){
                $unidade = db_utils::fieldsMemory($rsLaboratorios,$i);
                $labs[$unidade->la02_i_codigo] = $unidade->la02_c_descr;                 
              }              
              db_multiploselect("la02_i_codigo",
                                "la02_c_descr",
                                "nselecionados",
                                "sselecionados",
                                $labs,
                                array(),
                                10,
                                400, '', '', 'true', 'setTamanhoSelect();');
              ?>
            </td>
          </tr>       
        </table>
        <div class="container">
            <table>
                <tr>
                    <td>
                        <label><b>Situação amostra:</b></label>
                    </td>
                    <td>                        
                        <?php 
                          $daoMotivosRejeicao = new cl_motivosrejeicaoamostra;
                          $rsMotivosRejeicao = db_query($daoMotivosRejeicao->sql_query_file(null, "la73_sequencial,la73_descricao"));
                          $aMotivos = ['0' => "Todos"];
            
                          for($i=0;$i<pg_num_rows($rsMotivosRejeicao);$i++){
                            $motivoRejeicao = db_utils::fieldsMemory($rsMotivosRejeicao,$i);               
                            $aMotivos[] = $motivoRejeicao->la73_descricao;
                          }              
                          db_select("situacaoAmostra", $aMotivos, true, $iOpcao);
                        ?>                     
                    </td>
                </tr>
                <tr>
                    <td>
                      <label><b>Totalização por amostra:</b></label>
                    </td>
                    <td>
                        <?php
                        $aTotalizacaoPorAmostra = [
                          "t" => "Sim",
                          "f" => "Não"                          
                        ];
                        db_select('totalizacaoAmostra', $aTotalizacaoPorAmostra, true, 1, "");
                        ?>
                    </td>  
                </tr>
            </table>                          
        </div>            
      </form>
      </fieldset>
      <div class="container">
          <button type="button" id="gerar" name="gerar" style="height: 25px;" onclick="js_gerar()">
            <i class="fas fa-file"></i>
            Gerar relatório
          </button>
      </div>         
      </center>
    </td>
 </tr>
</table>
<?php
  db_menu();
?>
</body>
</html>
<script type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
<script rel="script" type="text/javascript" src="scripts/session.js"></script>
<script>
const dataInicial = document.getElementById('la02_d_datainicio');
const dataFinal = document.getElementById('la02_d_datafim');
const selectSelecionados = document.getElementById('sselecionados');
const situacaoAmostra = document.getElementById('situacaoAmostra');
const routes = {
  gerarRelatorio: 'saude/laboratorio/triagem-laboratorial/exportar-pendencias',  
};

function js_gerar() {
    if (!validaCampos()) {
        return;
    }

    let selecionados = [];
    for (let selecionado of selectSelecionados.options) {
        selecionados.push(selecionado.value);
    }
    
    const formData = new FormData();
    formData.append('dataInicial', dataInicial.value);
    formData.append('dataFinal', dataFinal.value);
    formData.append('totalizacaoAmostra', totalizacaoAmostra.value);
    
    if(selecionados.length > 0){
      formData.append('laboratorios', selecionados);
    }
         
    if(situacaoAmostra.value != '0'){
      formData.append('situacaoAmostra', situacaoAmostra.value);
    }
      
    js_divCarregando('Aguarde, gerando o relatório com as pendências da triagem', 'msgbox');

    HttpClient.post(`${PHPSession.requestApi}/${routes.gerarRelatorio}`, {body: formData}).then(response => {
        js_removeObj('msgbox');
        if (response.error) {      
          alert('Ocorreu algum problema ao gerar o relatório!');
          return;
        }

        const download = new DBDownload();
        download.addFile(response.data.path, response.data.name);
        download.show();    
    })    
}

function validaCampos() {

    if (dataInicial.value == "" && dataFinal.value == "") {
        alert('Informe o período!');
        return false;
	  }

    if(dataFinal.value.length  > 0 && dataInicial.value.length == 0){
        alert('Necessário informar a data inicial');
        return false;
    }

	  if (js_formatar(dataInicial.value, 'd') > js_formatar(dataFinal.value, 'd')) {
	  	  alert('A data inicial não pode ser maior que a data final!');
        return false;
	  }

    if(selectSelecionados.options.length == 0){
      alert('Informe, pelo menos, um laboratório!');
      return false;
    }

    return true;
}

function setTamanhoSelect() {
    const leftSelect = document.getElementById('nselecionados');
    const rightSelect = document.getElementById('sselecionados');

    leftSelect.style.cssText = 'height: 224px; width:400px';
    rightSelect.style.cssText = 'height: 224px; width:400px';
}
setTamanhoSelect()
</script>
