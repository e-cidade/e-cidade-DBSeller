<?php
require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_utils.php");
require_once modification("libs/db_app.utils.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("dbforms/db_funcoes.php");
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
  <script type="text/javascript" src="scripts/prototype.js"></script>
  <script type="text/javascript" src="scripts/widgets/DBAbasItem.widget.js"></script>
  <script type="text/javascript" src="scripts/widgets/DBAbas.widget.js"></script>
  <script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>

  <style>
    #form1 button{
      margin-left: 4px
    }
  </style>
</head>

<body class="body-default">
  <div id="ctnAbaEnvio" class="container">
    <div class="alert alert-primary text-left" role="alert">
      Esta rotina tem como finalidade comunicar-se com o sistema de compras públicas para a modalidade pregão
    </div>
    <!-- Inicio modals -->
    <div id="modalSituacao" class="container">
      <fieldset>
        <table class="form-container">
          <tr>
            <td><label for="selectSituacao">Selecione e/ou confirme a situação correta: </label></td>
          </tr>
          <tr>
            <td>
              <select id="selectSituacao"></select>
            </td>
          </tr>
        </table>
      </fieldset>
      <button class="btn btn-light" id="btnLancarSituacao">
        <i class="far fa-check-circle" aria-hidden="true"></i>
        Lançar Situação
      </button>
    </div>

    <div id="modalDocumentos" class="container">

    </div>

    <!-- <div id="modalEditar" class="container">

    </div> -->

    <div id="modalItens" class="container">
        <div class="alert alert-primary text-left" role="alert">
        Esta lista permitirirá a configuração de exclusividade dos itens,
        sobrepondo a configuração da licitação, cotas reservadas não serão permitidas.
        </div>
        <fieldset style="margin-top: 20px;">
          <legend>Configuração dos itens:</legend>
          <div style="width: 900px;">
            <table id="data-table-itens" style="width: 100%">
          </div>
          </table>
        </fieldset>
    </div>

    <!-- Fim modals -->
    <div style="width: 1000px">

      <h2 style="text-align: left">Licitação - Integração Compras Públicas</h2>
      <form name="form1" id="form1" method="post">
        <table>
          <tr>
            <td nowrap="nowrap" title="Licitação para processar" style="padding:5px;">
              <b><?php db_ancora('Licitação: ', "", 1, null, "linklicitacao"); ?></b>
            </td>
            <td align="left" nowrap="nowrap">
              <?php
              db_input("l20_codigo", 10, $Il20_codigo, true, "text", 3, "onchange='js_pesquisa_liclicita(false);'");
              ?>
            </td>

            <td>
              <button type="button" class="btn btn-light" id="btnConfigurarItem" title="Configuração de exclusividade do item" disabled="disabled">
                <i class="fas fa-cog"></i>
                <span>Configurar Itens</span>
              </button>
            </td>

            <td>
              <button type="button" class="btn btn-light" id="btnAdicionar" title="Documentos necessários para habilitação de forncedores na licitação" disabled="disabled">
                <i class="fa fa-plus"></i>
                <span>Configurar documentos</span>
              </button>
            </td>
            <td>
              <button type="button" id="btnEnviar" class="btn btn-light">
                <i class="far fa-save"></i>
                Enviar Dados
              </button>
            </td>
            <td>
              <button type="button" class="btn btn-light" id="btnConsultar">
                <i class="fas fa-search"></i>
                Consultar
              </button>
            </td>
            <td>
              <button type="button" class="btn btn-light" id="btnLimpar">
                <i class="fas fa-trash-alt"></i>
                Limpar
              </button>
            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
        </table>
      </form>

      <fieldset id="ctnTable" style="margin-top: 20px;">
        <legend>Consulta Licitação Enviada:</legend>
        <div style="width: 1000px;">
          <table id="data-table" style="width: 100%">
        </div>
        </table>
      </fieldset>
    </div>
  </div>

  <div id="ctnAbaRetorno" class="container">
    <div id="dadosRetorno">

    </div>
  </div>

  <div id="ctnAbas"></div>
  <?php db_menu() ?>

  <script type='text/javascript'>
    /**
     * Cria abas
     */
    var oDBAba = new DBAbas($('ctnAbas'));
    var oAbaEnvidoDados = oDBAba.adicionarAba("Enviar Dados", $('ctnAbaEnvio'));

    var oAbaRetornoDados = oDBAba.adicionarAba("Retorno Dados", $('ctnAbaRetorno'));
    oAbaRetornoDados.lBloqueada = true;
  </script>
  <script rel="script" type="text/javascript" src="scripts/session.js"></script>
  <script type="text/javascript" src="scripts/classes/bootstrapTable/detailFormaterTable.js"></script>
  <script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
  <script type="text/javascript" src="assets/bootstrap-table/popper.min.js"></script>
  <script type="text/javascript" src="assets/bootstrap-4.5.3/js/bootstrap.js"></script>
  <script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
  <script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
  <script type="text/javascript" src='extension/package/Desktop/assets/vendors/alertify/alertify.js'></script>
  <script type="text/javascript">

    String.prototype.FormatoBanco = function (decimais) {

      var valor = this;

      valor = valor.replace(/\D/g, '')

      switch (decimais) {
        case 3:
          valor = valor.replace(/(\d{1})(\d{1,3})$/, "$1.$2")
        break;
        case 4:
          valor = valor.replace(/(\d{1})(\d{1,4})$/, "$1.$2")
        break;
        default:
          valor = valor.replace(/(\d{1})(\d{1,2})$/, "$1.$2")
        break;
      }

      return valor;
    }

  </script>
  <script type="text/javascript">
    var tipoPregao = null
    var sRPC = 'lic4_integracaocompraspublicas.RPC.php'

    function js_pesquisa_liclicita(mostra) {

      const formData = new FormData();
      formData.append('acao', 'buscaModalidades');
      HttpClient.post(sRPC, {
          body: formData
        })
        .then(function(response) {

          if (response.erro) {

            alert(response.mensagem);
            return false;
          }

          var iModalidadeLicitacao = '';
          if (response.modalidade.length == 0) {
            alert("Não existe configuração para as modalidades de pregão");
            return false;
          }

          iModalidadeLicitacao = response.modalidade.join(',');
          if (mostra) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_liclicita', 'func_liclicita.php?iModalidadeLicitacao=' + iModalidadeLicitacao + '&situacao=0&funcao_js=parent.js_mostraliclicita1|l20_codigo|l44_sigla', 'Pesquisa', true);
          } else {

            if (document.form1.l20_codigo.value != '') {
              js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_liclicita', 'func_liclicita.php?iModalidadeLicitacao=' + iModalidadeLicitacao + '&situacao=0&pesquisa_chave=' + document.form1.l20_codigo.value + '&funcao_js=parent.js_mostraliclicita', 'Pesquisa', false);

            } else {
              document.form1.l20_codigo.value = '';
            }
          }
        });
    }


    function js_mostraliclicita1(chave1, chave2) {

      tipoPregao = chave2;
      document.form1.l20_codigo.value = chave1;
      let pregoes = ['PRE', 'PCE', 'PRP', 'PCP', 'CCE'];
      let pregao = pregoes.findIndex(pregao => pregao === tipoPregao);
      if(pregao >= 0) {
        document.getElementById("btnAdicionar").removeAttribute("disabled")
      }

      db_iframe_liclicita.hide();
      const formData = new FormData();
      formData.append('acao', 'verificaConfiguracao');
      formData.append('codigolicitacao', chave1);
      HttpClient.post(sRPC, {
          body: formData,
          reportMessage: 'Verificando configuração da licitação ...'
        })
        .then(function(response) {

          if (response.erro) {

            alert(response.mensagem);
            document.getElementById("l20_codigo").value = ""
            document.getElementById("btnAdicionar").setAttribute("disabled", '')
            return false;
          }

          if(response.habilitaConfiguracao) {

            document.getElementById("btnConfigurarItem").removeAttribute("disabled")
          }
        });
    }

    $.noConflict();
    jQuery(document).ready(function($) {

      const buscaProcessos = function(row, criaBotao=true) {

          const formData = new FormData();
          formData.append('acao', 'buscarDadosProcesso');
          formData.append('processo', row.idLicitacao);
          HttpClient.post(sRPC, {
              body: formData
            })
            .then(function(response) {

              if (response.erro) {

                alert(response.mensagem);
                return false;
              }
              let html ='<fieldset id="ctnTableRetorno" style="margin-top: 20px;">   '
                  html +=' <legend class="mainLegend"></legend>'
                  html +='   <fieldset>'
                  html +='     <legend>Participantes</legend>'
                  html +='     <div style="width: 1000px;">'
                  html +='        <table id = "data-table-fornecedor" style = "width: 100%" ></table>'
                  html +='     </div>'
                  html +='   </fieldset>'
                  html +='   <fieldset>'
                  html +='     <legend>Itens</legend>'
                  html +='     <div style="width: 1000px;">'
                  html +='       <table id = "data-table-response" style = "width: 100%" ></table>'
                  html +='     </div>'
                  html +='   </fieldset>'
                  html +=' </fieldset>'
                  html +='<input type="hidden" id="codlicitacao" name="codlicitacao" value=""/> '
                  if(criaBotao) {

                    html +='<button type="button" id="btnImportar" class="btn btn-light" >'
                    html +='<i class="far fa-save"></i>'
                    html +='  Importar Dados'
                    html +='</button>'
                  }
              jQuery("#dadosRetorno").html(html)

              const dadosPropostas = (dadosLinha) => {

               let dadosPropostas = []
               dadosLinha.Propostas.map((propostas, indiceProposta) => {

      dadosPropostas.push([{
      label: "Fornecedor:",
      valor: propostas.IdFornecedor
    },
    {
      label: "Valor Total:",
      valor: propostas.ValorTotal
    },
    {
      label: "Valor Unitário:",
      valor: propostas.ValorUnitario
    },
    {
      label: "Desconto(%):",
        valor: propostas.ValorDesconto!=undefined?propostas.ValorDesconto:0
    },
    {
      label: "Data:",
      valor: propostas.Data
    },
    {
      label: "Hora:",
      valor: propostas.Hora
    }
  ])
})

return dadosPropostas
}

const dadosVencedor = (dadosLinha) => {

let dadosVencedor = []
if (dadosLinha.Vencedores != undefined) {

  dadosLinha.Vencedores.map((vencedor, indiceVencedor) => {

    dadosVencedor.push([{
        label: "Fornecedor:",
        valor: vencedor.IdFornecedor
      },
      {
        label: "Valor Total:",
        valor: vencedor.ValorTotal
      },
      {
        label: "Valor Unitário:",
        valor: vencedor.ValorUnitario
      },
      {
        label: "Desconto(%):",
        valor: vencedor.ValorDesconto!=undefined?vencedor.ValorDesconto:0
      },
      {
        label: "Cancelado:",
        valor: vencedor.Cancelado ? 'Sim' : 'Não'
      }
    ])
  })
}

return dadosVencedor
}

const dadosLances = (dadosLinha) => {

let dadosLances = []
if (dadosLinha.Lances != undefined) {

  dadosLinha.Lances.map((lance, indiceLance) => {
    dadosLances.push([{
        label: "Fornecedor:",
        valor: lance.IdFornecedor
      },
      {
        label: "Valor Total:",
        valor: lance.ValorTotal
      },
      {
        label: "Valor Unitário:",
        valor: lance.ValorUnitario
      },
      {
        label: "Desconto(%):",
        valor: lance.ValorDesconto!=undefined?lance.ValorDesconto:0
      },
      {
        label: "Data:",
        valor: lance.Data
      },
      {
        label: "Hora:",
        valor: lance.Hora
      },
      {
        label: "Válido:",
        valor: lance.Valido ? 'Sim' : 'Não'
      },
      {
        label: "Cancelado:",
        valor: lance.Cancelado ? 'Sim' : 'Não'
      }
    ])
  })
}

dadosLances.sort(function(a, b) {

  if (a.Data > b.Data) {
    return 1;
  }

  if (a.Data < b.Data) {
    return -1;
  }

  if (a.Data == b.Data) {

    if (a.Hora > b.Hora) {
      return 1;
    }

    if (a.Hora < b.Hora) {
      return -1;
    }
}

return 0;
})

return dadosLances
}

const dadosPropostaReadequada = (dadosLinha) => {

let dadosPropostaReadequada = []
if (dadosLinha.PropostasReadequadas != undefined) {

  dadosLinha.PropostasReadequadas.map((propostaReadequada, indicePropostaReadequada) => {
    dadosPropostaReadequada.push([{
        label: "Fornecedor:",
        valor: propostaReadequada.IdFornecedor
      },
      {
        label: "Valor Total:",
        valor: propostaReadequada.ValorTotal
      },
      {
        label: "Valor Unitário:",
        valor: propostaReadequada.ValorUnitario
      },
      {
        label: "Desconto(%):",
        valor: propostaReadequada.ValorDesconto!=undefined?propostaReadequada.ValorDesconto:0
      },
      {
        label: "Data:",
        valor: propostaReadequada.Data
      },
      {
        label: "Hora:",
        valor: propostaReadequada.Hora
      }
    ])
  })
}
return dadosPropostaReadequada
}

const detailFormatter = (index, row) => {

let propostas          = dadosPropostas(row)
let vencedor           = dadosVencedor(row)
let lances             = dadosLances(row)
let propostaReadequada = dadosPropostaReadequada(row)
html = ''
if (propostas.length > 0) {
  html += detailFormaterTable.createDetail(propostas, 'Propostas');
}
if (lances.length > 0) {
  html += detailFormaterTable.createDetail(lances, 'Lances');
}
if (propostaReadequada.length > 0) {
  html += detailFormaterTable.createDetail(propostaReadequada, 'Proposta Readequada');
}
if (vencedor.length > 0) {
  html += detailFormaterTable.createDetail(vencedor, 'Vencedor');
}

return html
}
     /**
       * Inicio - Lista participantes da licitação
       */
      var tableRetornoFornecedor = jQuery('#data-table-fornecedor');
      var colunasFornecedor = [{
          title: 'Tipo',
          field: 'Tipo',
          align: 'left',
          valign: 'middle',
          sortable: false
        },
        {
          title: 'Razão Social',
          field: 'RazaoSocial',
          align: 'left',
          valign: 'middle',
          sortable: false
        },
        {
          title: 'CPF',
          field: 'CPF',
          align: 'left',
          valign: 'middle',
          sortable: false
        },
        {
          title: 'CNPJ',
          field: 'CNPJ',
          align: 'left',
          valign: 'middle',
          sortable: false
        },
        {
          title: 'Cidade',
          field: 'Cidade',
          align: 'left',
          valign: 'middle',
          sortable: false
        },
        {
          title: 'Endereço',
          field: 'Endereco',
          align: 'left',
          valign: 'middle',
          sortable: false
        },
        {
          title: 'Número',
          field: 'Numero',
          align: 'left',
          valign: 'middle',
          sortable: false
        },
        {
          title: 'Complemento',
          field: 'Complemento',
          align: 'left',
          valign: 'middle',
          sortable: false
        },
        {
          title: 'CEP',
          field: 'CEP',
          align: 'left',
          valign: 'middle',
          sortable: false
        }
      ]

      tableRetornoFornecedor.bootstrapTable({
                columns: colunasFornecedor,
                locale: 'pt-BR',
                height: 250,
                pagination: true,
                pageSize: 5,
                pageList: [5, 10, 15, 20, 25, 'All'],
                showButtonText: true,
                class: "table table-sm"
              })


      /**
       * Fim - Lista participantes da licitação
       */

              /**
      * Inicio - Retorno do julgamento dos itens da licitação
      */
        var tableRetorno = jQuery('#data-table-response');

        var colunasRetorno = [{
            title: 'Lote',
            field: 'NR_LOTE',
            align: 'right',
            valign: 'middle',
            sortable: true
          },
          {
            title: 'Item PCP',
            field: 'NR_ITEM',
            align: 'right',
            valign: 'middle',
            sortable: true
          },
          {
            title: 'Item',
            field: '_id',
            align: 'right',
            valign: 'middle',
            sortable: true
          },
          {
            title: 'Descrição',
            field: 'DS_ITEM',
            align: 'left',
            valign: 'middle',
            sortable: true
          },
          {
            title: 'Quantidade',
            field: 'QT_ITENS',
            align: 'left',
            valign: 'middle',
            sortable: true
          },
          {
            title: 'Valor de Referência',
            field: 'VL_UNITARIO_ESTIMADO',
            align: 'left',
            valign: 'middle',
            sortable: true
          },
          {
            title: 'Unidade de Medida',
            field: 'SG_UNIDADE_MEDIDA',
            align: 'left',
            valign: 'middle',
            sortable: true
          },
          {
            title: 'Resultado',
            field: 'TP_RESULTADO_ITEM',
            align: 'left',
            valign: 'middle',
            sortable: true
          }
        ]
      /**
      * Fim - Retorno do julgamento dos itens da licitação
      */

              tableRetorno.bootstrapTable({
                columns: colunasRetorno,
                detailFormatter: detailFormatter,
                locale: 'pt-BR',
                height: 350,
                pagination: true,
                detailView: true,
                pageSize: 5,
                pageList: [5, 10, 15, 20, 25, 'All'],
                showButtonText: true,
                class: "table table-sm"
              })

              jQuery("#ctnTableRetorno .mainLegend").html(`Retorno da Licitação ${response.dados._id}:`)
              jQuery("#codlicitacao").val(response.dados._id);
              oAbaRetornoDados.lBloqueada = false;
              oAbaEnvidoDados.setVisibilidade(false);
              oAbaRetornoDados.setVisibilidade(true);
              tableRetornoFornecedor.bootstrapTable('load', response.dados.Participantes)

              let itens = []
              response.dados.lotes.map((itenslote, indicelote) => {

                itenslote.itens.map((itensdados, indiceitem) => {

                  if (itenslote.Vencedores != undefined) {

                    itensdados.Vencedores = []
                    let Vencedor   = {}
                    let menorValor = new Number(0)
                    itensdados.Lances = itensdados.Lances == undefined? itensdados.PropostasReadequadas != undefined? itensdados.PropostasReadequadas : [] : itensdados.Lances
                    itensdados.Lances.map((lancesFornecedor, indLance) => {

                      if(itenslote.Vencedores.length > 0) {
                        if(lancesFornecedor.IdFornecedor == itenslote.Vencedores[0].IdFornecedor) {

                          if(menorValor == 0 || (lancesFornecedor.ValorTotal < menorValor && lancesFornecedor.Valido == true)) {

                            menorValor = lancesFornecedor.ValorTotal
                            Vencedor = {
                                        "Cancelado"    : lancesFornecedor.Cancelado,
                                        "IdFornecedor" : lancesFornecedor.IdFornecedor,
                                        "IdItem"       : lancesFornecedor.IdItem,
                                        "RazaoSocial"  : lancesFornecedor.RazaoSocial,
                                        "Tipo"         : lancesFornecedor.Tipo,
                                        "ValorTotal"   : lancesFornecedor.ValorTotal,
                                        "ValorUnitario": lancesFornecedor.ValorUnitario,
                                        "ValorDesconto": lancesFornecedor.ValorDesconto!=undefined?lancesFornecedor.ValorDesconto:0
                                       }
                          }
                        }
                      }
                    })
                    if(Vencedor != null) {

                      itensdados.Vencedores.push(Vencedor)
                    }

                  }

                  itens.push(itensdados)
                })
              });

              itens.sort(function(a, b) {

                if (a._id > b._id) {
                  return 1;
                }

                if (a._id < b._id) {
                  return -1;
                }

                if (a._id == b._id) {

                  if (a.IdItem > b.IdItem) {
                    return 1;
                  }

                  if (a.IdItem < b.IdItem) {
                    return -1;
                  }
                }

                return 0;
              })

              tableRetorno.bootstrapTable('load', itens)

              if(criaBotao) {

                const btnImportar           = jQuery('#btnImportar')
                btnImportar.one('click', () => {

                  const dados           = JSON.stringify(tableRetorno.bootstrapTable('getData'))
                  const dadosFornecedor = JSON.stringify(tableRetornoFornecedor.bootstrapTable('getData'))
                  const codlicitacao    = jQuery('#codlicitacao').val()
                  const formDados       = new FormData();
                  formDados.append('acao', 'importarDados');
                  formDados.append('dados', dados);
                  formDados.append('dadosFornecedor', dadosFornecedor);
                  formDados.append('codlicitacao', codlicitacao);
                  HttpClient.post(sRPC, {
                      body: formDados,
                      reportMessage: 'Importando dados ...'
                    })
                    .then(function(oResponse) {
                      if (oResponse.erro) {
                        alert(oResponse.mensagem)
                        return
                      }
                      alert(oResponse.mensagem);
                    })
               })
            }
            })

      }

      window.operateEvents = {

        'click .editar': function(e, value, row, index) {

           if(jQuery('#data-table-editar').find("input").hasClass("calculaValor")) {
             jQuery('.calculaValor').focus()
             return
           }


            const calculaLimiteValor = function (limitValue, valoreditado, codigo, grouper) {

             let valorLimite = new Number(limitValue)
             let agrupar = grouper
             let valorSoma = new Number(valoreditado)
             jQuery('#data-table-editar')
                 .bootstrapTable('getData').map((dados, indice) => {
                     let valor = new Number(dados.valorTotal)
                     if (dados.lote == grouper && dados.codigo != codigo) {
                         valorSoma = valorSoma + valor
                     }
                 })
             if (valorSoma > valorLimite) {
                 alert(`A soma dos itens (${valorSoma}) não pode ser maior que o valor do lote (${valorLimite})`)
                 this.value = 0
                 return false
             }

             return true
           }

           jQuery('#data-table-editar')
           .bootstrapTable('updateCell',
                            {index: index,
                             field: 'valorUnitario',
                             value: `<input type="text" class="calculaValor" size="8" title="Os valores devem ter ${row.casasdecimais} casas decimais" value="${row.valorUnitario}">`
                            }
                          )
           jQuery('.calculaValor').focus()
           let valor = new Number(jQuery('.calculaValor').val())
           jQuery('.calculaValor').keyup(function() {

            jQuery('.calculaValor').val(jQuery('.calculaValor').val().FormatoBanco(row.casasdecimais))
           }).change(function () {

             let valorOriginal = valor
                         valor = new Number(this.value.replace(',', '.'))

             let quantidade    = new Number(row.quantidade)
             let valorTotal    = valor>0?valor*quantidade:0
             let valorLimite   = row.valorTotalJulgado

             if (!calculaLimiteValor(valorLimite, valorTotal, row.codigo, row.lote)) {

               valor = valorOriginal
               jQuery('#data-table-editar')
              .bootstrapTable('updateCell',
                            {index: index,
                             field: 'valorUnitario',
                             value: valor==''?0:valor
                            }
                          )
               return false
             }

             jQuery('#data-table-editar')
             .bootstrapTable('updateRow',
                              {index: index,
                                 row: {valorTotal: valorTotal,
                                       valorUnitario: valor==''?0:valor
                                      }
                              }
                            )
             })
             .blur(
             function () {

              jQuery('#data-table-editar')
              .bootstrapTable('updateCell',
                            {index: index,
                             field: 'valorUnitario',
                             value: valor==''?0:valor
                            }
                          )
           }
           )
        },

        'click .configurar': function(e, value, row, index) {

          tableItensLicitacao.bootstrapTable('getData').map((dados, indice) => {
             if(dados.l04_descricao == row.l04_descricao) {

               tableItensLicitacao.bootstrapTable('updateCell', {index: indice, field: 'exclusivo', value: !value})
             }
          })
        },

        'click .dadosLicitacao': function(e, value, row, index) {
          var sURLLicitacao = "lic3_licitacao002.php?l20_codigo=" + value
          js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_licitacao' + value, sURLLicitacao, 'Consulta de Licitação', true);
        },
        'click .cancelar': function(e, value, row, index) {
          if(confirm(`Deseja cancelar a importação da licitação ${row._id}`)) {

            const formData = new FormData();
            formData.append('acao', 'cancelaImportacao');
            formData.append('codigoLicitacao',row._id);
            HttpClient.post(sRPC, {
              body: formData,
              reportMessage: 'Cancelando Importação ...'
            })
            .then(function(response) {

              alert(response.mensagem)
              limparTela()
            })
          }
        },
        'click .visualizar': function(e, value, row, index) {
          buscaProcessos(row, false);
        },
        'click .situacao': function(e, value, row, index) {
          const formData = new FormData();
          formData.append('acao', 'deparaSituacao');
          formData.append('codigoSituacao', value);
          HttpClient.post(sRPC, {
              body: formData,
              reportMessage: 'Verificando Situação ...'
            })
            .then(function(response) {

              if (response.erro) {

                alert(response.mensagem);
                return false;
              }

              selectSituacao.children().remove().end();
              let optionsSituacao = response.situacao
              optionsSituacao.map((optionSituacao, indexSituacao) => {

                selectSituacao.append(`<option value="${optionSituacao.codigo}">${optionSituacao.descricao}</option>`)
              })

              btnLancarSituacao.one('click', (event) => {

                event.preventDefault();
                btnLancarSituacao.prop("disabled", true)
                const formDados = new FormData();
                formDados.append('acao', 'atualizaSituacao');
                formDados.append('codigoSituacao', selectSituacao.val());
                formDados.append('licitacao', row._id);
                HttpClient.post(sRPC, {
                    body: formDados,
                    reportMessage: 'Alterando Situação ...'
                  })
                  .then(function(response) {
                    alert(response.mensagem)
                  })
                  .finally(() => {
                    btnLancarSituacao.prop("disabled", false)
                    windowSituacao.destroy()
                  })
              })


              windowSituacao.show(0, 0, true)
            })
        },

        'click .editaImportacao': function(e, value, row, index) {

          const formData = new FormData();
          formData.append('acao', 'buscarDadosProcessoEditar');
          formData.append('processo', row.idLicitacao);
          HttpClient.post(sRPC, {
              body: formData
            })
            .then(function(response) {

              if (response.erro) {

                alert(response.mensagem);
                return false;
              }

              oAbaRetornoDados.lBloqueada = false;
              oAbaEnvidoDados.setVisibilidade(false);
              oAbaRetornoDados.setVisibilidade(true);

              let html ='<fieldset id="ctnTableRetorno" style="margin-top: 20px;">   '
                  html +=' <legend class="mainLegend"></legend>'
                  html +='   <fieldset>'
                  html +='     <legend>Participantes</legend>'
                  html +='     <div style="width: 1000px;">'
                  html +='        <table id = "data-table-fornecedor" style = "width: 100%" ></table>'
                  html +='     </div>'
                  html +='   </fieldset>'
                  html +='   <fieldset>'
                  html +='     <legend>Itens para readequação de valores</legend>'
                  html +='     <div style="width: 1000px;">'
                  html +='       <table id = "data-table-editar" style = "width: 100%" ></table>'
                  html +='     </div>'
                  html +='   </fieldset>'
                  html +=' </fieldset>'
                  html +='<input type="hidden" id="codlicitacao" name="codlicitacao" value=""/> '
                  html +='<button type="button" id="btnImportarEditavel" class="btn btn-light" >'
                  html +='<i class="far fa-save"></i>'
                  html +='  Importar Dados'
                  html +='</button>'
                  jQuery("#dadosRetorno").html(html)

                  jQuery("#codlicitacao").val(response.dados.codlicitacao);
                  var tableRetornoFornecedor = jQuery('#data-table-fornecedor');
                  var colunasFornecedor = [
                  {
                   title: 'Tipo',
                   field: 'Tipo',
                   align: 'left',
                   valign: 'middle',
                   sortable: false
                  },
                  {
                    title: 'Razão Social',
                    field: 'RazaoSocial',
                    align: 'left',
                    valign: 'middle',
                    sortable: false
                  },
                  {
                    title: 'CPF',
                    field: 'CPF',
                    align: 'left',
                    valign: 'middle',
                    sortable: false
                  },
                  {
                    title: 'CNPJ',
                    field: 'CNPJ',
                    align: 'left',
                    valign: 'middle',
                    sortable: false
                  },
                  {
                    title: 'Cidade',
                    field: 'Cidade',
                    align: 'left',
                    valign: 'middle',
                    sortable: false
                  },
                  {
                    title: 'Endereço',
                    field: 'Endereco',
                    align: 'left',
                    valign: 'middle',
                    sortable: false
                  },
                  {
                    title: 'Número',
                    field: 'Numero',
                    align: 'left',
                    valign: 'middle',
                    sortable: false
                  },
                  {
                    title: 'Complemento',
                    field: 'Complemento',
                    align: 'left',
                    valign: 'middle',
                    sortable: false
                  },
                  {
                    title: 'CEP',
                    field: 'CEP',
                    align: 'left',
                    valign: 'middle',
                    sortable: false
                  }
                ]

                tableRetornoFornecedor.bootstrapTable({
                          columns: colunasFornecedor,
                          locale: 'pt-BR',
                          height: 250,
                          pagination: true,
                          pageSize: 5,
                          pageList: [5, 10, 15, 20, 25, 'All'],
                          showButtonText: true,
                          class: "table table-sm"
                        })

                tableRetornoFornecedor.bootstrapTable('load', response.dados.fornecedores)

                const formatterEditor = (value, row, index) => {
                   if(row.readequarvalor) {
                   return [
                       '<span class="editar" style="color: #2c5676"title="Readequar valores">',
                       '  <i class="fa fa-edit"></i>',
                       '</span>'
                   ].join('')
                   }

                   return value;
                 }

                const formatterNumeric = (value, row, index) => {

                  return js_formatar(value, 'f', row.casasdecimais)

                }

                var tableRetorno = jQuery('#data-table-editar');
                var colunasRetorno = [
                    {
                      title: 'Lote',
                      field: 'lote',
                      align: 'right',
                      valign: 'middle',
                      sortable: true
                    },
                    {
                      title: 'Valor Julgado',
                      field: 'valorTotalJulgado',
                      align: 'right',
                      valign: 'middle',
                      sortable: true,
                      formatter: formatterNumeric
                    },
                    {
                      title: 'Código',
                      field: 'codigo',
                      align: 'right',
                      valign: 'middle',
                      sortable: true
                    },
                    {
                      title: 'Descricao',
                      field: 'descricao',
                      align: 'left',
                      valign: 'middle',
                      sortable: true,
                    },
                    {
                      title: 'Fornecedor',
                      field: 'fornecedor',
                      align: 'right',
                      valign: 'middle',
                      sortable: true
                    },
                    {
                      title: 'Valor Unitário',
                      field: 'valorUnitario',
                      align: 'right',
                      valign: 'middle',
                      sortable: true,
                      formatter: formatterNumeric
                    },
                    {
                      title: 'Quantidade',
                      field: 'quantidade',
                      align: 'left',
                      valign: 'middle',
                      sortable: true
                    },
                    {
                      title: 'Valor Total',
                      field: 'valorTotal',
                      align: 'left',
                      valign: 'middle',
                      sortable: true,
                      formatter: formatterNumeric
                    },
                    {
                      title: 'Resultado',
                      field: 'resultado',
                      align: 'left',
                      valign: 'middle',
                      sortable: true
                    },
                    {
                      title: 'Configuração',
                      field: 'casasdecimais',
                      align: 'left',
                      valign: 'middle',
                      sortable: true,
                      visible: false
                    },
                    {
                      title: 'Readequar',
                      field: 'acoes',
                      align: 'center',
                      valign: 'center',
                      width: 70,
                      events: window.operateEvents,
                      formatter: formatterEditor,
                    }
                ]


                tableRetorno.bootstrapTable({
                  columns: colunasRetorno,
                  locale: 'pt-BR',
                  height: 350,
                  pagination: true,
                  pageSize: 5,
                  pageList: [5, 10, 15, 20, 25, 'All'],
                  showButtonText: true,
                  class: "table table-sm"
                })


              tableRetorno.bootstrapTable('load', response.dados.itens)

                const btnImportarEditavel  = jQuery('#btnImportarEditavel')
                btnImportarEditavel.on('click', () => {

                  const dados           = JSON.stringify(tableRetorno.bootstrapTable('getData'))
                  const dadosFornecedor = JSON.stringify(tableRetornoFornecedor.bootstrapTable('getData'))
                  const codlicitacao    = jQuery('#codlicitacao').val()
                  const formDados       = new FormData();
                  formDados.append('acao', 'importarDadosEditado');
                  formDados.append('dados', dados);
                  formDados.append('dadosFornecedor', dadosFornecedor);
                  formDados.append('codlicitacao', codlicitacao);
                  HttpClient.post(sRPC, {
                    body: formDados,
                    reportMessage: 'Importando dados ...'
                  })
                  .then(function(oResponse) {
                    if (oResponse.erro) {

                      alert(oResponse.mensagem)
                      return
                    }

                    alert(oResponse.mensagem);
                  })
              })
          })


        },
        'click .buscar': function(e, value, row, index) {

          buscaProcessos(row);
        }
      }

      const btnEnviar             = jQuery('#btnEnviar');
      const btnConsultar          = jQuery('#btnConsultar');
      const viewDadosRetorno      = jQuery('#ctnAbaRetorno');
      const btnAdicionar          = jQuery('#btnAdicionar');
      const btnLancarSituacao     = jQuery('#btnLancarSituacao');
      const linkPesquisaLicitacao = jQuery('#linklicitacao');
      const btnConfigurarItem     = jQuery('#btnConfigurarItem')
      var windowSituacao          = new windowAux('windowSituacao', 'Atualizar Situação da Licitação', 500, 300);
      var listaDocumentos         = []
      const modalDocumentos       = document.getElementById("modalDocumentos")
      var windowDocumentos        = new windowAux('windowDocumentos', 'Selecione os documentos necessários para habilitação de fornecedores', 1000, 500);
      conteudo = '<div style="width: 800px;">'
      conteudo += '        <table id = "data-table-documento" style = "width: 100%" ></table>'
      conteudo += '</div>'
      jQuery('#modalDocumentos').html(conteudo)
      windowDocumentos.setContent(modalDocumentos)
      windowDocumentos.allowCloseWithEsc(true)
      windowDocumentos.setShutDownFunction(function() {
        if (!!windowDocumentos.oDBMask) {
          windowDocumentos.oDBMask.destroy();
        }

        jQuery("#btnAdicionar span").html('Tipo de documento(s) configurado(s)')
        jQuery("#btnAdicionar i").removeClass("fa fa-plus").addClass("far fa-check-circle")
        let documentosSelecionados = tableDocumentos.bootstrapTable('getSelections')
        if (documentosSelecionados.length == 0) {

          jQuery("#btnAdicionar span").html('Configurar documentos')
          jQuery("#btnAdicionar i").removeClass("far fa-check-circle").addClass("fa fa-plus")
        }

        listaDocumentos = []
        documentosSelecionados.map((documento, index) => {

          listaDocumentos.push(documento.tipoDocumento)
        })

        windowDocumentos.hide()
      })

      const modalItens = document.getElementById("modalItens")
      var windowItens = new windowAux('windowItens', 'Configure a exclusividade dos itens', 1000, 600);
      windowItens.setContent(modalItens)
      windowItens.allowCloseWithEsc(true)
      windowItens.setShutDownFunction(function() {

        if (!!windowItens.oDBMask) {
          windowItens.oDBMask.destroy();
        }

        windowItens.hide()
      })

      // const modalEditar = document.getElementById("modalEditar")
      // let htmlModalEditar = '<div class="alert alert-primary text-left" role="alert">'
      //     htmlModalEditar += '   Readequação de valores dos itens'
      //     htmlModalEditar += ' </div>'
      //     htmlModalEditar += ' <fieldset style="margin-top: 20px;">'
      //     htmlModalEditar += '   <legend>Configuração dos itens:</legend>'
      //     htmlModalEditar += '   <div style="width: 900px;">'
      //     htmlModalEditar += '     <table id="data-table-edita-item" style="width: 100%">'
      //     htmlModalEditar += '   </div>'
      //     htmlModalEditar += '   </table>'
      //     htmlModalEditar += ' </fieldset> '
      //  jQuery('#modalEditar').html(htmlModalEditar)
      //  var windowEditar = new windowAux('windowEditar', 'Readequação dos itens', 1000, 600);
      //  windowEditar.setContent(modalEditar)
      //  windowEditar.allowCloseWithEsc(true)
      //  windowEditar.setShutDownFunction(function()
      // {
      //   if (!!windowEditar.oDBMask) {

      //     windowEditar.oDBMask.destroy();
      //   }

      //   windowEditar.hide()
      // })
      /**
       *
       * Inicio - Declaração das tables utilizadas
       *
       */

      /**
       * Inicio - Formatter das tables
       */
      const formatterLink = (value, row, index) => {

        const linkLicitacao = `<a class='dadosLicitacao' title='Clique para consultar licitação' >${value}</a>`
        return linkLicitacao
      };

      const formatterImportacao = (value, row, index) => {

        if(row.cdSituacao == 6) {

          if(row.importado == true) {
            return [
                  '<a class="cancelar" href="javascript:void(0)" title="Cancelar importação dos dados">',
                  ' <i class="fas fa-undo-alt"></i>',
                  '</a>',
                  '<a class="visualizar" href="javascript:void(0)" title="Visualizar dados importados">',
                  ' <i class="far fa-eye"></i>',
                  '</a>'
              ].join('')
          }

          if(row.editaImportacao == true) {

            return [
                '<a class="editaImportacao" href="javascript:void(0)" title="Clique para readequar os itens retornados">',
                ' <i class="fas fa-upload"></i>',
                '</a>'
          ].join('')
          }

          return [
                '<a class="buscar" href="javascript:void(0)" title="Clique para validar/importar retorno dos dados">',
                ' <i class="fas fa-upload"></i>',
                '</a>'
          ].join('')
        }
      };

      const formatterSituacao = (value, row, index) => {

        let codigoSituacao = `<span>${value}</span>`
        let situacaovalida = [3, 4, 5]
        if (situacaovalida.includes(value)) {

          codigoSituacao = `<a class="situacao" title="Clique para atualizar a situação da licitação" >${value}</a>`;
        }

        return codigoSituacao
      };

      const formatterConfigurar = (value, row, index) => {

        let valor = value==true?'Sim':'Não'
        if(row.selecione == false) {

          return '<span>Reservado</span>'
        }

        return `<a class="configurar">${valor}</a>`
      }

      const formatterCheckBox = (value, row, index) => {

        if(row.selecione == false) {
          return {disabled: true}
        }

      }

      /**
       * Fim - Formatter das tables
       */

      /**
       * Inicio - Lista dos itens a serem configurados
       */
      var tableItensLicitacao = jQuery('#data-table-itens');
      var colunasItens = [{
            field: 'checkitem',
            checkbox: true,
            formatter: formatterCheckBox
          },
          {
            title: 'Código Item',
            field: 'l21_codigo',
            align: 'left',
            valign: 'middle',
            sortable: false
          },
        {
          title: 'Descrição',
          field: 'pc04_descr',
          align: 'left',
          valign: 'middle',
          sortable: false,
        },
        {
          title: 'Lote',
          field: 'l04_descricao',
          align: 'left',
          valign: 'middle',
          sortable: false,
        },
        {
          title: 'Exclusivo?',
          field: 'exclusivo',
          align: 'center',
          valign: 'middle',
          sortable: false,
          events: window.operateEvents,
          formatter: formatterConfigurar
        }
      ]

      tableItensLicitacao.createTable = function() {

        tableItensLicitacao.bootstrapTable({
          columns: colunasItens,
          locale: 'pt-BR',
          height: 350,
          pagination: true,
          pageSize: 5,
          pageList: [5, 10, 15, 20, 25, 'All'],
          showButtonText: true,
          class: "table table-sm"
        })
      }

      tableItensLicitacao.createTable()
      /**
       * Fim - Lista dos itens a serem configurados
       */




      /**
      * Inicio - Licitações enviadas para o julgamento dos itens no Portal
      */
      /**
       * Inicio -
       */


      /**
       * Fim -
       */

      var table   = jQuery('#data-table');
      var colunas = [{
          title: 'Código PCP',
          field: 'idLicitacao',
          align: 'center',
          valign: 'middle',
          sortable: true,
          visible:  false
        },
        {
          title: 'Licitação',
          field: '_id',
          align: 'center',
          valign: 'middle',
          sortable: true,
          events: window.operateEvents,
          formatter: formatterLink
        },
        {
          title: 'Nº Processo',
          field: 'NR_PROCESSO',
          align: 'center',
          valign: 'middle'
        },
        {
          title: 'Tipo de Licitação',
          field: 'tipoLicitacao',
          align: 'center',
          valign: 'middle',
          sortable: true
        },
        {
          title: 'Nº Licitação',
          field: 'NUMERO',
          align: 'center',
          valign: 'middle'
        },
        {
          title: 'Objeto Licitação',
          field: 'DS_OBJETO',
          align: 'center',
          valign: 'middle'
        },
        {
          title: 'Código Situação',
          field: 'cdSituacao',
          align: 'center',
          valign: 'middle',
          visible: true,
          sortable: true,
          events: window.operateEvents,
          formatter: formatterSituacao
        },
        {
          title: 'Situação da Licitação',
          field: 'situacao',
          align: 'center',
          valign: 'middle',
          sortable: true
        },
        {
          title: 'Processar?',
          field: 'habilitaConsulta',
          align: 'center',
          valign: 'middle',
          visible: false,
        },
        {
          title: 'Ação',
          field: 'acoes',
          align: 'center',
          valign: 'center',
          events: window.operateEvents,
          formatter: formatterImportacao
        }
      ]

      table.createTable = function() {

        table.bootstrapTable({
          columns: colunas,
          locale: 'pt-BR',
          height: 350,
          pagination: true,
          pageSize: 10,
          pageList: [10, 20, 30, 40, 50, 'All'],
          search: true,
          showButtonText: true,
          class: "table table-sm"
        });
      }

      table.createTable()
      /**
      * Fim - Licitações enviadas para o julgamento dos itens no Portal
      */

      /**
      * Inicio - Documentos necessários para habilitar fornecedores/participantes da licitação
      */
        var tableDocumentos = jQuery('#data-table-documento');
        var colunasDocumento = [{
            checkbox: true
          },
          {
            title: 'Tipo',
            field: 'tipoDocumento',
            align: 'left',
            valign: 'middle',
            sortable: false,
            visible: false
          },
          {
            title: 'Título do Documento',
            field: 'titulo',
            align: 'left',
            valign: 'middle',
            sortable: false
          }
        ]

        tableDocumentos.createTable = function() {

          tableDocumentos.bootstrapTable({
            columns: colunasDocumento,
            locale: 'pt-BR',
            height: 350,
            pagination: true,
            pageSize: 5,
            pageList: [5, 10, 15, 20, 25, 'All'],
            showButtonText: true,
            class: "table table-sm"
          })
        }

        tableDocumentos.createTable()

      /**
      * Fim - Documentos necessários para habilitar fornecedores/participantes da licitação
      */
      /**
       *
       * Fim - Declaração das tables utilizadas
       *
       */

      linkPesquisaLicitacao.on('click', () => {

        listaDocumentos = []
        jQuery("#btnAdicionar span").html('Configurar documentos')
        jQuery("#btnAdicionar i").removeClass("far fa-check-circle").addClass("fa fa-plus")
        oAbaRetornoDados.lBloqueada = true
        js_pesquisa_liclicita(true);
        btnAdicionar.prop("disabled", true)
      })

      btnEnviar.on('click', () => {

        listaConfiguracao = []
        tableItensLicitacao.bootstrapTable('getSelections').map((lista, index) => {
          let lote = new Object()
              lote.exclusivo = lista.exclusivo
              lote.descricao = lista.l04_descricao
          listaConfiguracao.push(lote)

        })

        const licitacao = jQuery('#l20_codigo').val();

        if (licitacao == "") {

          return alert('Pesquise uma licitação para enviar os dados ao Serviço Compras Públicas');
        }

        if (listaDocumentos.length == 0 && tipoPregao == "PRE") {

          return alert('Selecione os documentos necessários para habilitação dos fornecedores na licitação');
        }

        const confirmarAcao = confirm("Enviar os dados desta licitacão para o Serviço Compras Públicas?");
        if (!confirmarAcao) {

          return false;
        }

        const formData = new FormData();
        formData.append('acao', 'enviarDados');
        formData.append('licitacao', licitacao);
        formData.append('documentos', JSON.stringify(listaDocumentos));
        formData.append('configuracao', JSON.stringify(listaConfiguracao));

        HttpClient.post(sRPC, {
            body: formData,
            reportMessage: 'Enviando os dados ...'
          })
          .then(function(response) {

            if (response.erro) {

              alert(response.mensagem);
              return false;
            }

            alert(response.mensagem);
            limparTela()
          });
      });

      const modalSituacao  = document.getElementById("modalSituacao")
      const selectSituacao = jQuery("#selectSituacao")
      windowSituacao.setContent(modalSituacao)
      windowSituacao.allowCloseWithEsc(true)
      windowSituacao.setShutDownFunction(function() {
        if (!!windowSituacao.oDBMask) {
          windowSituacao.oDBMask.destroy();
        }

        windowSituacao.hide();
      })

      btnConsultar.on('click', () => {

        const licitacao = jQuery('#l20_codigo').val();
        const formData  = new FormData();
        formData.append('acao', 'buscaProcesso');
        formData.append('licitacao', licitacao);
        HttpClient.post(sRPC, {
            body: formData,
            reportMessage: 'Buscando dados ...'
          })
          .then(function(oResponse) {
            if (oResponse.erro) {
              table.bootstrapTable('destroy')
              table.createTable()
              alert(oResponse.mensagem);
              return;
            }

            table.bootstrapTable('load', oResponse.processos);
          });
      });

      function limparTela() {

        btnAdicionar.prop("disabled", true)
        jQuery('#l20_codigo').val("")
        table.bootstrapTable('destroy')
        table.createTable()
        listaDocumentos = []
        tableDocumentos.bootstrapTable('destroy')
        tableDocumentos.createTable()
        jQuery("#btnAdicionar span").html('Configurar documentos')
        jQuery("#btnAdicionar i").removeClass("far fa-check-circle").addClass("fa fa-plus")
        jQuery("#btnConfigurarItem").prop("disabled", true)
        tableItensLicitacao.bootstrapTable('destroy')
        tableItensLicitacao.createTable()
        oAbaRetornoDados.lBloqueada = true
      }

      btnLimpar.on('click', () => {

        limparTela()
      });

      btnAdicionar.on('click', () => {

        windowDocumentos.show(0, 0, true)
        if (tableDocumentos.bootstrapTable('getSelections').length == 0) {

          const formData = new FormData();
          formData.append('acao', 'buscaDocumentos');
          HttpClient.post(sRPC, {
            body: formData,
            reportMessage: 'Buscando lista de documentos'
          }).then(function(response) {

            if (response.erro) {

              alert(response.mensagem);
              return false;
            }

            tableDocumentos.bootstrapTable('load', response.documentos);
          });
        }
      });

      btnConfigurarItem.on('click', () => {

        windowItens.show(0, 0, true)
        const licitacao = jQuery('#l20_codigo').val();
        const formData = new FormData();
              formData.append('acao', 'buscaItens');
              formData.append('licitacao', licitacao);

        HttpClient.post(sRPC, {
            body: formData,
            reportMessage: 'Buscando itens da licitação'
        }).then(function(response) {

          if (response.erro) {
            alert(response.mensagem);
            return false;
          }

          tableItensLicitacao.bootstrapTable('load', response.dadositens);
        })
      })

    });

  </script>
</body>

</html>
