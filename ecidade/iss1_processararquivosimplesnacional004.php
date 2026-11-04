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

 require_once modification("libs/db_stdlib.php");
 require_once modification("libs/db_utils.php");
 require_once modification("libs/db_app.utils.php");
 require_once modification("libs/db_conecta.php");
 require_once modification("libs/db_sessoes.php");
 require_once modification("dbforms/db_funcoes.php");
 $oPost = db_utils::postMemory($_POST);
 ?>
 <!doctype html>
 <html lang="pt-BR">
 
 <head>
   <meta charset="iso-8859-1">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <link rel="stylesheet" type="text/css" href="estilos.css" />
   <link rel="stylesheet" type="text/css" href="estilos/DBtab.style.css" />
   <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet" />
   <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet" />
   <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.core.css" rel="stylesheet" />
   <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.bootstrap.css" rel="stylesheet" />
   <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
   <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
   <script rel="script" type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
   <script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
   
 </head>
 
 <body class="body-default">
    <div class="container">
     <div class="alert alert-primary text-left" role="alert">
       Esta rotina tem como finalidade comunicar-se com a Receita Federal através de arquivos
     </div>  
     <div id="modalSituacao" class="container">
      <input type="hidden" id="sequencial"> 
      <fieldset>
        <table class="form-container">
          <tr>
            <td><label for="selectSituacao">Apto: </label></td>
            <td>
              <select id="selectSituacao">
                <option value="t">Sim</option>
                <option value="f">Não</option>
              </select>
            </td>
          </tr>
          <tr>
            <td colspan="2"><label for="justificativa">Justificativa Altera&ccedil;&aacute;o: </label></td>
          </tr>
          <tr>
            <td colspan="2">
              <textarea id="justificativa" cols="40"></textarea>
            </td>
          </tr>
        </table>
      </fieldset>

      <button class="btn btn-light" id="btnLancarSituacao">
        <i class="far fa-check-circle" aria-hidden="true"></i>
        Lançar Situação
      </button>
    </div>   

    <div id="modalArquivos" class="container">
      <input type="hidden" id="sequencialretorno"> 
      <fieldset>
      <div style="width: 800px;">
         <table id="data-table-arquivos" style="width: 100%"></table>
      </div>   
      </fieldset>

    </div>   

     <input type="hidden" value="<?php echo $oPost->q64_datalimitevencimentos; ?>" id="dtLimite">
     <input type="hidden" value="<?php echo $oPost->lReprocessamento; ?>" id="lReprocessamento">
     <input type="hidden" value="<?php echo $oPost->q64_sequencial; ?>" id="q64_arquivo"/>
       <fieldset id="ctnTable" style="margin-top: 20px;">
         <legend>Geração Arquivos do Simples Nacional</legend>
         <table>
          <tr>
            <td>
              <label style="font-weight: bold;">CNAE:</label>
            </td>
            <td>
              <?php
                db_select('q142_cnae', array(), '', 1, "");
              ?>         
            </td>
          </tr>
         </table>         
         <div style="width: 1000px;">
           <table id="data-table" style="width: 100%"></table>
         </div>
       </fieldset>
       <button name="btnGerar" id="btnGerar">
        <i class="fas fa-file-download"></i>
        Gerar
       </button>
       <button name="btnRelatorio" id="btnRelatorio">
        <i class="fas fa-file-pdf" aria-hidden="true"></i>
        Relatório Inconsistência
      </button>
      <button name="btnListaArquivos" id="btnListaArquivos">
        <i class="fas fa-file-download"></i>
        Arquivos Gerados
      </button>
      <button onclick="history.back();">
      <i class="fas fa-arrow-left"></i> 
        Voltar
      </button>
     </div>
</div>
   <?php db_menu() ?>
 
   <script rel="script" type="text/javascript" src="scripts/session.js"></script>
   <script type="text/javascript" src="scripts/classes/bootstrapTable/detailFormaterTable.js"></script>
   <script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
   <script type="text/javascript" src="assets/bootstrap-table/popper.min.js"></script>
   <script type="text/javascript" src="assets/bootstrap-4.5.3/js/bootstrap.js"></script>
   <script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
   <script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
   <script type="text/javascript" src="assets/bootstrap-table/extensions/filter-control/bootstrap-table-filter-control.min.js"></script>
   <script type="text/javascript" src='extension/package/Desktop/assets/vendors/alertify/alertify.js'></script>
   <script type="text/javascript">    
     var sRPC       = 'iss1_processararquivosimplesnacional.RPC.php'
     var MENSAGENS  = 'tributario.issqn.iss1_processararquivosimplesnacional.';

     $.noConflict();
     jQuery(document).ready(function($) {
       
      const empresasModificadas   = []
      const buttonGerar           = jQuery('#btnGerar')
      const buttonRelatorio       = jQuery('#btnRelatorio')
      const buttonLancarSituacao  = jQuery('#btnLancarSituacao')
      const buttonListaArquivos   = jQuery('#btnListaArquivos')
      const iArquivo              = jQuery('#q64_arquivo').val()
      const lReprocessamento      = jQuery('#lReprocessamento').val();
      const dtLimite              = jQuery('#dtLimite').val();
      const oSelect               = jQuery('#q142_cnae')
      const sequencial            = jQuery('#sequencial')
      var windowSituacao          = new windowAux('windowSituacao', 'Atualizar Situação', 500, 300);
      var windowArquivos          = new windowAux('windowArquivos', 'Arquivos Gerados', 900, 550);
      oSelect.disabled = true;
      oSelect.css('padding', '5px')
             .css('margin','10px')
      oSelect.append('<option value="0">SELECIONE</option>')

      const modalSituacao  = document.getElementById("modalSituacao")
      const modalArquivos  = document.getElementById("modalArquivos")
      const selectSituacao = jQuery("#selectSituacao")
      const justificativa  = jQuery("#justificativa")
      windowSituacao.setContent(modalSituacao)
      windowSituacao.allowCloseWithEsc(true)
      windowSituacao.setShutDownFunction(function() {

        if (!!windowSituacao.oDBMask) {
          windowSituacao.oDBMask.destroy();
        }  

        windowSituacao.hide();
      })
      
      windowArquivos.setContent(modalArquivos)
      windowArquivos.allowCloseWithEsc(true)
      windowArquivos.setShutDownFunction(function() {

        if (!!windowArquivos.oDBMask) {
          windowArquivos.oDBMask.destroy();
        }  

        windowArquivos.hide();
      })

      const formatterDescription  = (value, row, index) => {
         
        return value.urlDecode()
      }

      function adicionaEmpresaAlteracao(linha, empresaAlterar) {        
        
        if(linha.q142_apto == empresaAlterar.q142_apto) { 

          indexRemove = null
          empresasModificadas.find((element, index) => { if(element.q142_sequencial == linha.q142_sequencial) indexRemove = index})
          if(indexRemove != null) {

            empresasModificadas.splice(indexRemove, 1) 
          }
          table.bootstrapTable('updateByUniqueId', {id: linha.q142_sequencial, 
                                                   row: {q142_apto: linha.q142_apto, 
                                                         q142_observacao: linha.q142_observacao
                                                        }
                                                   })   
          return
        }

        linha.q142_apto       = selectSituacao.val()
        linha.q142_observacao = justificativa.val()
        
        table.bootstrapTable('updateByUniqueId', {id: linha.q142_sequencial, row: {q142_apto: linha.q142_apto, q142_observacao: linha.q142_observacao}}) 
        empresasModificadas.push(empresaAlterar);
        return
      }
       
      function resetaEmpresaAlteracao() { 
        while(empresasModificadas.length > 0) {
          empresasModificadas.pop()
        }
      }

      const formatterAlterar = (value, row, index) => {
         
         let sHtml = '<a class="alterarSituacao" href="javascript:void(0)" title="Alterar Situação">'
             sHtml+= '  <i class="fas fa-edit"></i>'
             sHtml+= '</a>'
             return  sHtml 
      }

      const formatterDownload = (value, row, index) => {
         
         let sHtml = '<a class="downloadArquivo" href="javascript:void(0)" title="Download arquivo">'
             sHtml+= '  <i class="fas fa-download"></i>'
             sHtml+= '</a>'
             return  sHtml 
      }

      const formatterBoolean = (value, row, index) => {
       
        value = value=='t'?'Sim':'Não'
        html = `<span class="selectApto">${value}</span>`
        
        return html
      }
       
       window.operateEvents = {  
        
        'click .alterarSituacao': function(e, value, row, index) { 
          
          sequencial.val(row.q142_sequencial)
          selectSituacao.val(row.q142_apto)
          justificativa.val('')
          windowSituacao.show(0, 0, true)
        },

        'click .downloadArquivo': function(e, value, row, index) { 
          
          console.log(e, value, row, index)
          //js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_download', 'db_download.php?arquivo=tmp/' + row.q182_nomearquivo, 'Download de arquivos', false);
          const formDataDownload = new FormData();      
          formDataDownload.append('acao', 'downloadArquivo');
          formDataDownload.append('id_arquivo', row.q182_sequencial);
          let mensagem = `Download Arquivo ${row.q182_nomearquivo}...` 
          HttpClient.post(sRPC, {
                         body: formDataDownload,
                         reportProgress: true,
                         reportMessage: mensagem
          }).then(function(response) {
             
            if (response.erro) {
              alert( response.mensagem.urlDecode() );
              return;
            }
          
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_download', 'db_download.php?arquivo=' + response.urlArquivo, 'Download de arquivos', false);
          });          
           
        }  
      }  

       var table   = jQuery('#data-table');
       var colunas = [{
                       title: 'Código',
                       field: 'q142_sequencial',
                       align: 'center',
                       valign: 'middle',
                       visible: false
                     },
                     {
                       title:  'CNPJ',
                       field:  'q142_cnpj',
                       align:  'center',
                       valign: 'middle'
                     },
                     {
                       title:   'CNAE',
                       field:   'q142_cnae',
                       align:   'center',
                       valign:  'middle',
                     },
                     {
                       title: 'Descri&ccedil;&atilde;o',
                       field: 'q142_observacao',
                       align: 'center',
                       valign: 'middle',
                       formatter: formatterDescription
                     },
                     {
                       title: 'Apto',
                       field: 'q142_apto',
                       align: 'center',
                       valign: 'middle',
                       formatter: formatterBoolean,
                       events: window.operateEvents
                     },
                     {
                       title: "Ação",
                       field: 'acoes',
                       align: 'center',
                       valign: 'middle',
                       width: '130',
                       events: window.operateEvents,
                       formatter: formatterAlterar
                     } 
                    ]
 
       table.createTable = function() {
        
         table.bootstrapTable('destroy').bootstrapTable({
           columns: colunas,
           locale: 'pt-BR',
           height: 450,
           pagination: true,
           pageSize: 10,
           pageList: [10, 20, 30, 40, 50, 'All'],
           showButtonText: true,
           uniqueId : "q142_sequencial", //auxilia a alteração das colunas para identificar a linha
           class: "table table-sm"
         })        
       }
       
       table.createTable()

       var tableArquivos   = jQuery('#data-table-arquivos');
       var colunasArquivos = [{
                       title: 'Identificador',
                       field: 'q182_sequencial',
                       align: 'center',
                       valign: 'middle',
                       visible: false
                     },
                     {
                       title:  'Usuário Processou',
                       field:  'nome',
                       align:  'center',
                       valign: 'middle',
                       formatter: formatterDescription
                     },
                     {
                       title:  'Nome Arquivo',
                       field:  'q182_nomearquivo',
                       align:  'center',
                       valign: 'middle'
                     },
                     {
                       title: "Ação",
                       field: 'acoes',
                       align: 'center',
                       valign: 'middle',
                       width: '130',
                       events: window.operateEvents,
                       formatter: formatterDownload
                     } 
                    ]
 
         tableArquivos.createTable = function() {
        
         tableArquivos.bootstrapTable('destroy').bootstrapTable({
           columns: colunasArquivos,
           locale: 'pt-BR',
           height: 300,
           pagination: true,
           pageSize: 5,
           showButtonText: true,
           uniqueId : "q182_sequencial", //auxilia a alteração das colunas para identificar a linha
           class: "table table-sm"
         })        
       }
       
       tableArquivos.createTable()
       const formDataValidacao   = new FormData();      
       
       formDataValidacao.append('acao', 'validacaoAutomatica');
       formDataValidacao.append('iArquivo', iArquivo);
       formDataValidacao.append('lReprocessamento', lReprocessamento);
       formDataValidacao.append('dtLimite', dtLimite);
       js_divCarregando('Validando os dados', 'loading_message');
       HttpClient.post(sRPC, {
                       body: formDataValidacao,
                       reportProgress: false
       }).then(function(response) {
         if (response.erro) {
           js_removeObj('loading_message'); 
           alert( response.mensagem.urlDecode() );
           return;
         }
         
         if(lReprocessamento == 1) {
           
           response.aEmpresasModificadas.map( empresa => {

             empresasModificadas.push(empresa)  
           })
           
         }
         
         js_removeObj('loading_message');
         if( jQuery('#q64_arquivo').val() == '' ){

          alert (  _M( MENSAGENS + 'arquivo_obrigatorio' ) );
          jQuery('#q142_cnae').disabled = true;
          return;
         }

         const formDataCnae = new FormData();         
         formDataCnae.append('acao', 'getCnae');
         formDataCnae.append('iArquivo', iArquivo);         
         HttpClient.post(sRPC, {
                     body: formDataCnae,
                     reportMessage: 'Aguarde, Carregando CNAE ...'
         }).then(function(response) {

           jQuery('#q142_cnae').disabled       = false;
           if (response.erro) {
             alert( response.mensagem.urlDecode() );
             return;
           }

           for(var iCnae=0; iCnae < response.aCnaes.length; iCnae++ ){
  
             var oDadosCnae    = response.aCnaes[iCnae];
             var oOpcao        = document.createElement("option");
                 oOpcao.value  = oDadosCnae.q71_estrutural;
                 oOpcao.text   = oDadosCnae.q71_descr.urlDecode();
                 oOpcao.style.padding = '10px';
                 oSelect.append(oOpcao);
           } 
         });

         
       });

      oSelect.on('change', () => {
        
        if (oSelect.val() == 0) {
           
           table.bootstrapTable('destroy')
           return;
         }
 
         const formDataEmpresa = new FormData();      
         formDataEmpresa.append('acao', 'getEmpresas');
         formDataEmpresa.append('iArquivo', iArquivo);
         formDataEmpresa.append('estrutural', oSelect.val());
         HttpClient.post(sRPC, {
                         body: formDataEmpresa,
                         reportMessage: 'Buscando empresas ...'
        }).then(function(response) {
             
          if (response.erro) {
            alert( response.mensagem.urlDecode() );
            return;
          }
          
          table.createTable()
          table.bootstrapTable('load', response.aEmpresas)
        });

      })
      
      buttonGerar.on('click', () => {
        
        const formData  = new FormData();
        let iArquivo    = jQuery('#q64_arquivo').val()
        formData.append('acao', 'gerar');
        formData.append('iArquivo', iArquivo);
        formData.append('aEmpresas', JSON.stringify(empresasModificadas));
        js_divCarregando('Processando os dados', 'loading_message');
        HttpClient.post(sRPC, {
            body: formData,
            reportProgress: false
         }).then(function(response) {
            
            if (response.erro) {

              alert(response.mensagem.urlDecode());
              js_removeObj('loading_message');
              return;
            }
            
            resetaEmpresaAlteracao()
            table.createTable()
            oSelect.val('0')              
            js_removeObj('loading_message');
            alert (  _M( MENSAGENS + 'arquivo_processado' ) );
          });
      }) 

      buttonLancarSituacao.on('click', () => {
         
         if(justificativa.val() == '' ) {
           alert('Necessário justificativa para alteração')
           return false
         }
         
         const formDataEmpresa = new FormData();      
         formDataEmpresa.append('acao', 'getRegistro');
         formDataEmpresa.append('sequencial', sequencial.val());         
         HttpClient.post(sRPC, {
                        body: formDataEmpresa,
                        reportMessage: 'Verificando empresa ...'
        }).then(function(response) {
             
          if (response.erro) {
            alert( response.mensagem.urlDecode() );
            return;
          }
          
          linha = response.registro
          empresaAlterar = {q142_sequencial: linha.q142_sequencial, 
                           q142_cnpj      : linha.q142_cnpj,
                           q142_apto      : selectSituacao.val(),
                           q142_observacao: justificativa.val()
                          }

          adicionaEmpresaAlteracao(linha, empresaAlterar)        
          windowSituacao.destroy();
         });
         
       }) 

       buttonRelatorio.on('click', () => {

        const formData  = new FormData();
        let iArquivo = jQuery('#q64_arquivo').val()
        formData.append('acao', 'getRelatorio');
        formData.append('iArquivo', iArquivo);
        HttpClient.post(sRPC, {
            body: formData,
            reportProgress: true,
            reportMessage: 'Gerando Relatório ...'
         }).then(function(response) {
         
          if (response.erro) {

            alert( response.mensagem.urlDecode() );
            return;
          }  

          if (response.sInconsistencias) {
             
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_download', 'db_download.php?arquivo=' + response.sInconsistencias, 'Download de arquivos', false);            
          }
        })
      })
      
      buttonListaArquivos.on('click', () => {
        
        const formData  = new FormData();
        let iArquivo = jQuery('#q64_arquivo').val()
        formData.append('acao', 'buscarArquivos');
        formData.append('iArquivo', iArquivo);
        js_divCarregando('Buscando lista dos arquivos gerados', 'loading_message')
        HttpClient.post(sRPC, {
            body: formData,
            reportProgress: false 
         }).then(function(response) {
          
          js_removeObj('loading_message'); 
          if (response.erro) {

            alert( response.mensagem.urlDecode() );
            return;
          }  
          
          console.log(response)
          tableArquivos.bootstrapTable('load', response.listaArquivos)
          windowArquivos.show(0, 0, true)
         }) 
        
      })
    })
      
   </script>
 </body> 
 </html>