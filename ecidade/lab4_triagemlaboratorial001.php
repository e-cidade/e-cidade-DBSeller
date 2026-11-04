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

$clrotulo->label("la08_c_descr");
$clrotulo->label("la21_i_codigo");
$clrotulo->label("la23_i_codigo");
$clrotulo->label("la23_c_descr");
$clrotulo->label("la24_i_codigo");
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
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/webseller.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBToogle.widget.js"></script>        
    
  </head>
<style>
  .th-inner{
    color:white;
  }

  #data-table-triagens tbody td:nth-child(9) {
    max-width: 150px;
  }

</style>  
<body class="body-default">

  <div class="container">
    
    <input type="hidden" id="cgsId">
    <form name='form1'>

      <fieldset style="width: 540px">
        <legend>Triagem Laboratorial</legend>
        <table class="form-container">                
          <tr>
            <td></td>
            <td id="linhaIdade" style="text-align:right;">              
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
          <tr>
            <td nowrap title="<?=@$Tla24_i_setor?>">
              <?php
              db_ancora( '<b>Setor:</b>', "js_pesquisaSetor(true);", "" );
              ?>
            </td>
            <td>
              <?php
              db_input(
                  'la23_i_codigo',
                  10,
                  $Ila23_i_codigo,
                  true,
                  'text',
                  "",
                  " onchange='js_pesquisaSetor(false);'"
              ) ?>
              <?php
              db_input('la23_c_descr', 50, $Ila23_c_descr, true, 'text', 3, '') ?>
              <?php
              db_input('la24_i_codigo', 10, $Ila24_i_codigo, true, 'hidden', "", "") ?>
            </td>
          </tr>
          <tr>
            <td nowrap>
              <?php
                db_ancora ( '<b>Exame:</b>', "js_pesquisaExame(true);", "" );
              ?>
            </td>
            <td>
              <?php
              db_input(
                  'la08_i_codigo',
                  10,
                  @$Ila08_i_codigo,
                  true,
                  'text',
                  "",
                  " onchange='js_pesquisaExame(false);'"
              ) ?>
              <?php
              db_input('la21_i_codigo', 10, @$Ila21_i_codigo, true, 'hidden', "", "") ?>
              <?php
              db_input('la21_c_situacao', 10, @$Ila21_c_situacao, true, 'hidden', "", "") ?>
              <?php
              db_input('la08_c_descr', 50, @$Ila08_c_descr, true, 'text', 3, '') ?>
            </td>
          </tr>
        </table>

      </fieldset>
      
      <button type="button" id="pesquisar" name="pesquisar" onclick="pesquisarTriagens()">
        <i class="fas fa-search"></i>
        Pesquisar
       </button>
    </form>
  </div>

  <div id="divTriagens" class="subcontainer" style="width: 1200px;">
    <fieldset>
      <legend>Triagens</legend>
      <table id="data-table-triagens"
          class="table table-sm">
      </table>
    </fieldset>
  </div>  

  <div class="subcontainer">
    <button type="button" onclick="processarTriagem()">
      <i class="fas fa-save"></i>
      Processar
    </button>
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
  buscar: 'saude/laboratorio/triagem-laboratorial/buscar',
  processar: 'saude/laboratorio/triagem-laboratorial/processar',
};

jQuery('#codigoBarras').focus();

jQuery(document).ready(jQuery => {
    
  tabelaTriagens.bootstrapTable({
    height: 300,       
    search: true,
    detailView: true,
    columns: [
        {
          checkbox: true,
          width: 20
        },
        {
          field: 'laboratorio',
          title: 'Laboratório',
          halign: 'center',
          align: 'left',
          width: 150,
          formatter: (a, data) => {
            return data.laboratorio;
          }
        },        
        {
          field: 'codigo',
          title: 'Código',
          halign: 'center',
          align: 'left',
          visible:false,
          width: 50,
          formatter: (a, data) => {
              return data.codigo;
          }
        },       
        {
          field: 'amostra',
          title: 'Amostra',
          halign: 'center',
          align: 'center',
          width: 100,
          formatter: (a, data) => {
            return data.codigo + ' - '+data.amostra
          }
        },
        {
          field: 'coleta',
          title: 'Coleta',
          halign: 'center',
          align: 'center',
          width: 100,
          formatter: (a, data) => {
              return data.coleta
          }
        },        
        {
          field: 'hora',
          title: 'Hora',
          halign: 'center',
          align: 'center',
          width: 60,
          formatter: (a, data) => {
              return data.hora
          }
        },
        {
          field: 'recebida',
          title: 'Amostra Recebida',
          halign: 'center',
          align: 'center',
          width: 80,
          formatter: (a, data) => {
            let btnConfirmacaoAmostra = '<input type="radio" id="btnConfirmacaoAmostra'+data.codigo+'"';
            btnConfirmacaoAmostra += ' value="" onclick="processaConfirmacaoAmostra('+data.codigo+')">';
            return `${btnConfirmacaoAmostra}`              
          }
        },     
        {
          field: 'rejeitada',
          title: 'Amostra Rejeitada',
          halign: 'center',
          align: 'center',
          width: 80,
          formatter: (a, data) => {
            let btnRejeicaoAmostra = '<input type="radio" id="btnRejeicaoAmostra'+data.codigo+'"';
            btnRejeicaoAmostra += ' value="" onclick="processaRejeicaoAmostra('+data.codigo+')">';
            return `${btnRejeicaoAmostra}`   
          }
        },
        {
          field: 'tipoRejeicao',
          title: 'Motivo Rejeição',
          halign: 'center',
          align: 'center',
          width: 150,
          formatter: (a, data) => {
            var motivosRejeicao = data.motivosRejeicaoAmostra;
            var rejeicaoHtml = '<select id="motivoRejeicaoAmostra'+data.codigo+'"'+' style="display:none"';
            rejeicaoHtml += 'onchange = "processaMotivoRejeicaoAmostra('+data.codigo+')">';
            rejeicaoHtml += '<option value="0">Selecione</option>';            
            for(var i =0;i<motivosRejeicao.length;i++){
              rejeicaoHtml += '<option value="'+motivosRejeicao[i].codigo+'">';
              rejeicaoHtml += motivosRejeicao[i].descricao+'</option>';
            }        
            rejeicaoHtml += '</select>';
            rejeicaoHtml += '<textArea style="display:none" id="outroMotivoRejeicao'+data.codigo+'">';
            rejeicaoHtml += '</textArea>';
            return `${rejeicaoHtml}`
          }
        }    
    ],
    idField: 'codigo',
    detailFormatter: (index, row) => {
      var detailHtml = '<ul>';
      var exames = row.exames;
      for(var i =0;i<exames.length;i++){
        detailHtml += '<li>'+exames[i].descricao+'</li>';
      }
      detailHtml += '</ul>';
      return `${detailHtml}`;
    }   
  });
});

function js_pesquisaRequisicao( mostra ) {

  var sUrl = 'func_lab_requisicao.php?autoriza=2';
  sUrl += '&iLaboratorioLogado&permissaoPorResponsavelLab&lSomenteColetados';

  if( mostra == true ) {
    sUrl += '&funcao_js=parent.js_mostraRequisicao1|la22_i_codigo|z01_v_nome|z01_i_cgsund';
    js_OpenJanelaIframe('', 'db_iframe_lab_requisicao', sUrl, 'Pesquisa Requisição', true);
  } else {

    if( document.form1.la22_i_codigo.value != '' ) {

      sUrl += '&pesquisa_chave='+document.form1.la22_i_codigo.value;
      sUrl += '&funcao_js=parent.js_mostraRequisicao'

      js_OpenJanelaIframe('', 'db_iframe_lab_requisicao', sUrl, 'Pesquisa Requisição', false);
    } else {
      apagaIdadeTriagens();
      document.form1.z01_v_nome2.value = '';
    }
  }
}

function js_mostraRequisicao( chave, erro,cgs ) {

  document.form1.z01_v_nome2.value = chave;
  document.getElementById('cgsId').value = cgs;  
  if( erro == true ) {
    
    document.form1.la22_i_codigo.value = '';
    apagaIdadeTriagens();
    return;
  }
  pesquisarTriagens();   
  existeCgsUnd();
}

function js_mostraRequisicao1( chave1, chave2,chave3 ) {

  document.form1.la22_i_codigo.value = chave1;
  document.form1.z01_v_nome2.value   = chave2;
  document.getElementById('cgsId').value = chave3;

  db_iframe_lab_requisicao.hide();
  pesquisarTriagens(); 
  existeCgsUnd();
}

function js_pesquisaSetor(lMostra) {

  if (jQuery('#la22_i_codigo').val().length == 0) {
    alert("Preencha primeiro a Requisição.");
    jQuery('#la23_i_codigo').val('');
    return;
  }

  var sGet = "la22_i_codigo= " +jQuery('#la22_i_codigo').val();        
  if(jQuery('#la08_i_codigo').val().length > 0){            
    sGet += "&exames="+jQuery('#la08_i_codigo').val();
  }

  if (lMostra) {  
    sGet += '&funcao_js=parent.js_mostraSetor1|la24_i_setor|la23_c_descr|la24_i_codigo';
    js_OpenJanelaIframe('', 'db_iframe_lab_labsetor', 'func_lab_labsetor.php?' + sGet, 'Pesquisa', true);
  } else {
    if (jQuery('#la23_i_codigo').val() != '') {    
      sGet += '&pesquisa_chave=' + jQuery('#la23_i_codigo').val() + '&funcao_js=parent.js_mostraSetor';
      js_OpenJanelaIframe('', 'db_iframe_lab_labsetor', 'func_lab_labsetor.php?' + sGet, 'Pesquisa', false);
    } else {
      jQuery('#la23_c_descr').val('');
    }
  }
}

function js_mostraSetor(sDescricaoSetor, lErro, iCodigoLabSetor) {

  jQuery('#la23_c_descr').val(sDescricaoSetor);
  jQuery('#la24_i_codigo').val(iCodigoLabSetor);
  
  if (lErro) {      
    jQuery('#la23_i_codigo').val('');
    jQuery('#la24_i_codigo').val('');
    return;
  }
}

function js_mostraSetor1(iCodigoSetor, sDescricaoSetor, iCodigoLabSetor) {
  jQuery('#la23_i_codigo').val(iCodigoSetor);
  jQuery('#la23_c_descr').val(sDescricaoSetor);
  jQuery('#la24_i_codigo').val(iCodigoLabSetor);
  db_iframe_lab_labsetor.hide();
}

function js_pesquisaExame(mostra) {  
  
  if (document.form1.la22_i_codigo.value == '') {

    alert('Escolha uma requisição primeiro.');
    return false;
  }

  sPesq = 'la21_i_requisicao=' + document.form1.la22_i_codigo.value;
  if (document.form1.la23_i_codigo.value != '') {
    sPesq += '&la24_i_setor=' + document.form1.la23_i_codigo.value;    
  }         

  if (mostra == true) {
    js_OpenJanelaIframe(
      '',
      'db_iframe_lab_requiitem',
      'func_lab_requiitem.php?' + sPesq + '&funcao_js=parent.js_mostraExame1|la08_i_codigo|la08_c_descr'
      + '|la21_i_codigo|la21_c_situacao',
      'Pesquisa',
      true
    );
  } else {
  
    if (document.form1.la08_i_codigo.value != '') {
        js_OpenJanelaIframe(
          '',
          'db_iframe_lab_requiitem',
          'func_lab_requiitem.php?' + sPesq + '&pesquisa_chave=' + document.form1.la08_i_codigo.value
          + '&funcao_js=parent.js_mostraExame',
          'Pesquisa',
          false
        );
    } else {    
      document.form1.la08_c_descr.value = '';
      document.form1.la21_c_situacao.value = '';
    }
  }
}

function js_mostraExame(chave, erro, requiitem, situacao) {

  document.form1.la08_c_descr.value = chave;
  
  if (erro == true) {    
    document.form1.la08_i_codigo.value = '';
  } else {
  
    document.form1.la21_i_codigo.value = requiitem;
    document.form1.la21_c_situacao.value = situacao;
  }
}

function js_mostraExame1(chave1, chave2, requiitem, situacao) {
  document.form1.la08_i_codigo.value = chave1;
  document.form1.la08_c_descr.value = chave2;
  document.form1.la21_i_codigo.value = requiitem;
  document.form1.la21_c_situacao.value = situacao;
  db_iframe_lab_requiitem.hide();

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
    document.form1.la22_i_codigo.dispatchEvent(new Event('change',{}));
    return false;
  }
  
  alert('Código de barras inválido!');
  jQuery('#codigoBarras').val('');      
}

function processaRejeicaoAmostra(codigo){
  jQuery('#btnConfirmacaoAmostra'+codigo).prop('checked',false);
  jQuery('#motivoRejeicaoAmostra'+codigo).css('display','inline');
}

function processaMotivoRejeicaoAmostra(codigo){
  jQuery('#outroMotivoRejeicao'+codigo).css('display','none');   
  if(jQuery('#motivoRejeicaoAmostra'+codigo).find('option:selected').html() == 'Outros Motivos'){    
    jQuery('#outroMotivoRejeicao'+codigo).css('display','inline'); 
  }
}

function processaConfirmacaoAmostra(codigo){  
  jQuery('#btnRejeicaoAmostra'+codigo).prop('checked',false);
  jQuery('#motivoRejeicaoAmostra'+codigo).css('display','none');  
  jQuery('#outroMotivoRejeicao'+codigo).css('display','none');
}

function pesquisarTriagens(){
  
  
  if(document.form1.la22_i_codigo.value.length == 0){
    alert('Informe o número da requisição!');
    return false;
  }
  
  js_divCarregando('Aguarde, pesquisando as triagens da amostra', 'msgbox');

  const formData = new FormData();
  formData.append('codRequisicao', document.form1.la22_i_codigo.value);
  formData.append('setor', document.form1.la23_i_codigo.value);
  formData.append('exame', document.form1.la08_i_codigo.value);

  HttpClient.post(`${PHPSession.requestApi}/${routes.buscar}`, {body: formData}).then(response => {
    js_removeObj('msgbox');
    if (response.error) {      
      alert('Ocorreu algum problema ao buscar as triagens da amostra!');
      return;
    }
    if(response.data.length == 0){      
      limparFiltros();
    }
    tabelaTriagens.bootstrapTable('load', response.data);
  })
}

function processarTriagem(){
    
  let resultTriagens = tabelaTriagens.bootstrapTable('getSelections');  

  if(resultTriagens.length == 0){
    alert('Selecione, pelo menos, uma amostra para ser processada!');
    return false;
  }
  
  let idUsuario = '<?php echo db_getsession("DB_id_usuario");?>';
  let requestTriagens = [];
  for(let resultTriagem of resultTriagens){
    
    let codigo = resultTriagem.codigo;
    let codigoRequisicao = document.form1.la22_i_codigo.value;
    let recebida = jQuery('#btnConfirmacaoAmostra'+codigo).prop('checked');
    let rejeitada = jQuery('#btnRejeicaoAmostra'+codigo).prop('checked');
    
    let motivoRejeicao = jQuery('#motivoRejeicaoAmostra'+codigo).val();
    let outroMotivoRejeicao = jQuery('#outroMotivoRejeicao'+codigo).val();

    if(!recebida && !rejeitada){
      alert('Necessário receber ou rejeitar a amostra '+resultTriagem.amostra+'!');
      return false;
    }
    
    if(motivoRejeicao == 8 && outroMotivoRejeicao.length == 0){
      let msg = 'Necessário informar qual o outro motivo de rejeição da amostra '+resultTriagem.amostra+'!';
      msg += ' Favor, não deixar o campo em branco.';
      alert(msg);
      return false;
    }

    let itensRequisicao = [];
    for(let exame of resultTriagem.exames){
      itensRequisicao.push(exame.itemRequisicao);
    }

    requestTriagens.push({
      'codigoMaterial':codigo,
      'codigoRequisicao':codigoRequisicao,
      'itensRequisicao':itensRequisicao,
      'recebida':recebida,      
      'motivoRejeicao':motivoRejeicao,
      'outroMotivoRejeicao':outroMotivoRejeicao,
      'idUsuario':idUsuario
    });
  }
  
  const formData = new FormData();
  formData.append('requestTriagens',JSON.stringify(requestTriagens));

  js_divCarregando('Aguarde, processando as triagens da amostra', 'msgbox');
  HttpClient.post(
    `${PHPSession.requestApi}/${routes.processar}`,
    {
      body: formData
    }
  ).then(response => {
    js_removeObj('msgbox');
    if (response.error) {      
      alert('Ocorreu algum problema ao processar as triagens da amostra!');
      return;
    } 
    alert('Triagem processada com sucesso!');
    pesquisarTriagens();    
  })
}

function limparFiltros(){
  document.form1.la22_i_codigo.value = "";
  document.form1.z01_v_nome2.value = "";
  document.form1.la23_i_codigo.value = "";
  document.form1.la23_c_descr.value = "";
  document.form1.la08_i_codigo.value = "";
  document.form1.la08_c_descr.value = "";
  apagaIdadeTriagens();
}

function existeCgsUnd() {

  let cgsUnd = document.getElementById('cgsId').value;

  if (cgsUnd !== '') {
    let parametros = {
        'sExecucao': 'buscarDadosCgs',
        'iCgs': cgsUnd
    };

    let dadosRequisicao = {};

    dadosRequisicao.method = 'post';
    dadosRequisicao.parameters = 'json=' + Object.toJSON(parametros);
    dadosRequisicao.onComplete = function (resposta) {
        let retorno = JSON.parse(resposta.responseText);        
        retornaIdadePaciente(retorno);
    };

    const rpc = 'sau4_cgs.RPC.php';
    new Ajax.Request(rpc, dadosRequisicao);
  }
}

function retornaIdadePaciente(retorno) {  

  let idadeCompleta = retorno.oCgs.sIdadeCompleta.urlDecode();
  document.getElementById('linhaIdade').innerHTML = `<p id="idadeCompleta">Idade: <span style="color: red;">${idadeCompleta}</span></p>`;
}

function apagaIdadeTriagens() {
  let tagIdadeCompleta = document.querySelector("#idadeCompleta")
  if (tagIdadeCompleta) {
    tagIdadeCompleta.remove();
  }
  tabelaTriagens.bootstrapTable('load', []);
}


</script>
