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
require_once(modification('libs/db_utils.php'));

$cllab_labsetor   = new cl_lab_labsetor;
$cllab_requisicao = new cl_lab_requisicao;
$cllab_exame      = new cl_lab_exame;
$clrotulo         = new rotulocampo;


$clrotulo->label("la22_i_codigo");
$clrotulo->label("z01_v_nome");

$iUsuario = db_getsession('DB_login');
$iDepto   = db_getsession('DB_coddepto');
?>
<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <link type="text/css" href="estilos.css" rel="stylesheet">
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet"/>
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet"/>
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>    
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBToogle.widget.js"></script>        
    
  </head>
<style>
  .th-inner{
    color:white;
  }

</style>  
<body class="body-default">

  <div class="container">

    <form name='form1'>

      <fieldset style="width: 540px">
        <legend>Filtros</legend>
        <table class="form-container">   
          <tr>
            <td>
              <label><b>Período:</b></label>
            </td>
            <td>              
              <?php db_inputdata('dataInicial', null, null, null, true, 'text', 1); ?> 
              <label>Até:</label>
              <?php db_inputdata('dataFinal', null, null, null, true, 'text', 1); ?>
            </td>
          </tr>   
          <tr>
            <td nowrap title="<?=@$Tla22_i_codigo?>">
              <?php  db_ancora ( '<strong>Requisição:</strong>', "js_pesquisaRequisicao(true);", "" );?>
            </td>
            <td>
            <?php
              db_input ( 'la22_i_codigo', 10, $Ila22_i_codigo, true, 'text', "", "onchange='js_pesquisaRequisicao(false);'" );
              db_input ( 'z01_v_nome2',   50, @$Iz01_v_nome,    true, 'text', 3 );
            ?>
            </td>
          </tr>

          <tr>
            <td style="padding-right: 15px;">
              <label for="codigoBarras"><b>Código de Barras:</b></label>
            </td>
            <td>
              <input id="codigoBarras" 
                  name = "codigoBarras" 
                  type="text" 
                  value=""                                                   
                  onchange="js_processaCodigoBarras()"/>
            </td>
          </tr>
        </table>
      </fieldset>
      
      <div class="container">
        <button type="button" id="pesquisar" name="pesquisar" onclick="pesquisarTriagens()">
          <i class="fas fa-search"></i>
          Pesquisar
         </button>

        <button type="button" id="limpar" name="limpar" onclick="limparTela()">
         <i class="fas fa-trash"></i>
         Limpar
        </button>  
      </div>
     
    </form>
  </div>

  <div id="divTriagens" class="subcontainer" style="width: 1000px;">
    <fieldset>
      <legend>Triagens</legend>
      <table id="data-table-triagens"
          class="table table-sm">
      </table>
    </fieldset>
  </div>  
  
  <?php
    db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
  ?>
</body>
</html>
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script rel="script" type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript">

const tabelaTriagens = jQuery('#data-table-triagens');
const routes = {
  buscar: 'saude/laboratorio/triagem-laboratorial/buscar-requisicoes'  
};

jQuery(document).ready(jQuery => {
    
  jQuery('#codigoBarras').focus();  
  tabelaTriagens.bootstrapTable({
    height: 300,       
    search: true,
    detailView: true,
    columns: [
        {
          field: 'requisicao',
          title: 'Requisição',
          halign: 'center',
          align: 'left',
          width: 50,
          formatter: (a, data) => {
              return data.codigoRequisicao;
          }
        },     
        {
          field: 'laboratorio',
          title: 'Laboratório',
          halign: 'center',
          align: 'center',
          width: 150,
          formatter: (a, data) => {
            return data.laboratorio;
          }
        },            
        {
          field: 'paciente',
          title: 'Paciente',
          halign: 'center',
          align: 'left',
          width: 150,
          formatter: (a, data) => {
            return data.paciente;
          }
        },   
        {
          field: 'idadePaciente',
          title: 'Idade Paciente',
          halign: 'center',
          align: 'center',
          width: 150,
          formatter: (a, data) => {
            return data.idadePaciente;
          }
        },                 
        {
          field: 'dataRequisicao',
          title: 'Data Requisição',
          halign: 'center',
          align: 'center',
          width: 120,
          formatter: (a, data) => {
              return data.dataRequisicao
          }
        }
    ],
    idField: 'codigo',
    detailFormatter: (index, row) => {

      let exames = row.exames;

      let detailHtml = '<table class="tableSituacaoExames" style="width:100%">';
      detailHtml += '<tr>';
      detailHtml += '<th style="width:75%;text-align:center;font-size:14px">Exames</th>';
      detailHtml += '<th style="width:25%;text-align:center;font-size:14px">Situação Exames</th>';
      detailHtml += '</tr>';;
      detailHtml += '<tr><td style="width:78.8%;height:5px">';
      detailHtml += '<label></label>';
      detailHtml += '</td>';
      detailHtml += '<td style="width:21.2%;">';
      detailHtml += '<label</label>';
      detailHtml += '</td>';
      detailHtml += '</tr>';  
      
      let backgroundColorStyle = '';
      for(var i =0;i<exames.length;i++){
        
        /**
         * Cores específicas para cada situação
         */
        switch(exames[i].situacao){
          case  '90 - Nova Coleta':
          case  '40 - Nova Coleta':            
            backgroundColorStyle = 'background-color: gold';
            break;
          case  '70 - Entregue':
          case  '60 - Conferido':
            backgroundColorStyle = 'background-color: #80b554';
            break;
          case  '45 - Em processamento':
            backgroundColorStyle = 'background-color: lightblue';
            break;
          case  '80 - Amostra Rejeitada - Nova Coleta':
            backgroundColorStyle = 'background-color: lightcoral';
            break;

          default:
            backgroundColorStyle = ''
        }
        
        detailHtml += '<tr><td style="height:20px;text-align:center;border:solid 1.5px black">';
        detailHtml += '<label>'+exames[i].descricao+'</label>';
        detailHtml += '</td>';
        detailHtml += '<td style="text-align:center;border:solid 1.5px black;'+backgroundColorStyle+'">';
        detailHtml += '<label>'+exames[i].situacao+'</label>';
        detailHtml += '</td>';
        detailHtml += '</tr>';         
      }

      detailHtml += '</table>';
      return `${detailHtml}`;
    }   
  });
});

function js_pesquisaRequisicao( mostra ) {

  var sUrl = 'func_lab_requisicao.php?autoriza=2';
  sUrl += '&iLaboratorioLogado&permissaoPorResponsavelLab';

  if( mostra == true ) {
    sUrl += '&funcao_js=parent.js_mostrarRequisicao1|la22_i_codigo|z01_v_nome';
    js_OpenJanelaIframe('', 'db_iframe_lab_requisicao', sUrl, 'Pesquisa Requisio', true);
  } else {

    if( document.form1.la22_i_codigo.value != '' ) {

      sUrl += '&pesquisa_chave='+document.form1.la22_i_codigo.value;
      sUrl += '&funcao_js=parent.js_mostrarRequisicao'

      js_OpenJanelaIframe('', 'db_iframe_lab_requisicao', sUrl, 'Pesquisa Requisio', false);
    } else {
      document.form1.z01_v_nome2.value = '';
    }
  }
}

function js_mostrarRequisicao( chave, erro ) {

  document.form1.z01_v_nome2.value = chave;
  if( erro == true ) {    
    document.form1.la22_i_codigo.value = '';
    return false;
  }
  pesquisarTriagens();
}

function js_mostrarRequisicao1( chave1, chave2 ) {

  document.form1.la22_i_codigo.value = chave1;
  document.form1.z01_v_nome2.value   = chave2;
  db_iframe_lab_requisicao.hide();
  pesquisarTriagens();
}


function js_processaCodigoBarras(event){

  let codigoBarras = jQuery('#codigoBarras').val(); 
  
  if(codigoBarras.length == 0) {
    jQuery('#la22_i_codigo').val('');
    jQuery('#z01_v_nome').val('');
    return false;
  } 

  if([12,14].includes(codigoBarras.length)){            
    let codigoRequisicao = parseInt(codigoBarras.substring(3, 12), 10);
    jQuery('#la22_i_codigo').val(codigoRequisicao);
    jQuery('#la22_i_codigo').trigger('change');
    return false;
  }
  
  alert('Código de barras inválido!');
  jQuery('#codigoBarras').val('');     
}

function pesquisarTriagens(){
  
  const formData = new FormData();

  let codigoRequisicao = document.form1.la22_i_codigo.value;
  let dataInicial = document.form1.dataInicial.value;
  let dataFinal = document.form1.dataFinal.value;    
  let departamentoUsuario = '<?php echo db_getsession('DB_coddepto')?>';

  let temCodigoRequisicao = document.form1.la22_i_codigo.value.length > 0;
  let temDataInicial = document.form1.dataInicial.value.length > 0;
  let temDataFinal = document.form1.dataFinal.value.length > 0;

  if(!temCodigoRequisicao && !temDataInicial && !temDataFinal){
    alert("Informe, pelo menos, um filtro!");
    return false;
  }

  if(temDataFinal && !temDataInicial){
    alert("Informe, também, a data inicial!");
    return false;
  }

  formData.append('departamentoUsuario', departamentoUsuario);

  if(temCodigoRequisicao){
    formData.append('codigoRequisicao', codigoRequisicao);
  }
  
  if(temDataInicial){
    formData.append('dataInicial', dataInicial);
  }
  
  if(temDataFinal){
    formData.append('dataFinal',dataFinal);
  }
  
  js_divCarregando('Aguarde, pesquisando as requisições', 'msgbox');
  HttpClient.post(`${PHPSession.requestApi}/${routes.buscar}`, {body: formData}).then(response => {
    js_removeObj('msgbox');
    if (response.error) {      
      alert('Ocorreu algum problema ao buscar as requisições!');
      return;
    }
    tabelaTriagens.bootstrapTable('load', response.data);
  })
}

function limparTela(){
  document.form1.la22_i_codigo.value = "";
  document.form1.z01_v_nome2.value = "";
  jQuery('#dataInicial').val('');
  jQuery('#dataFinal').val('');
  tabelaTriagens.bootstrapTable('load', []);
}

</script>