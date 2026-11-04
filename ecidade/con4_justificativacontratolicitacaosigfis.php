<?php
/**
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
include_once(modification("libs/db_sessoes.php"));
include_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));



$clempempenho = new cl_empempenho;
$clempempenho->rotulo->label();

?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link href="estilos/DBFormularios.css" rel="stylesheet" type="text/css">
    <script src="scripts/scripts.js" type="text/javascript"></script>
    <script src="scripts/prototype.js" type="text/javascript"></script>
    <script src="scripts/widgets/DBLookUp.widget.js" type="text/javascript"></script>
    <script src="scripts/widgets/DBLancador.widget.js" type="text/javascript"></script>
    <script src="scripts/AjaxRequest.js" type="text/javascript"></script>
</head>
<body>
<div class="container">
    <form>
        <fieldset>
            <legend>Pesquisa de Empenho</legend>

            <table class="form-container">
                <tr>
                    <td>
                        <label for="tipoFiltroEmpenho">Filtrar:</label>
                    </td>
                    <td width="650">
                        <select id="tipoFiltroEmpenho" onchange="validaExibicaoFiltroEmpenho()">
                            <option value="1">Individual</option>
                            <option value="2" selected>Lote</option>
                        </select>
                    </td>
                </tr>

                <tr style="display: none;" id="linhaEmpenhoIndividual">
                    <td>
                        <label for="e60_numemp"></label>
                        <a href="#" id="empenho">Empenho:</a>
                    </td>
                    <td>
                        <input id="e60_numemp" type="text" value="" class="field-size2"/>
                        <input id="z01_nome" type="text" value="" class="field-size11"/>
                    </td>
                </tr>

                <tr id="linhaEmpenhosLote">


                    <td colspan="2">


                        <table width="100%" >
                            <tr>
                                <td width="20%"><strong>Data Emissão:</strong></td>
                                <td>
                                    <?php
                                    db_inputdata('e60_emiss1',@$e60_emiss_dia,@$e60_emiss_mes,@$e60_emiss_ano,true,'text',1,"");
                                    echo "<strong> a </strong>";
                                    db_inputdata('e60_emiss2',@$e60_emiss_dia,@$e60_emiss_mes,@$e60_emiss_ano,true,'text',1,"");
                                    ?>

                                </td>
                            </tr>

                            <tr>
                                <td><strong>Faixa de Valor do Empenho:</strong></td>
                                <td>
                                    <?php
                                       db_input("e60_vlrempInicial", 9, $Ie60_vlremp,true,"text",4,"");
                                       echo "<strong> a </strong>";
                                       db_input("e60_vlrempFinal", 9, $Ie60_vlremp ,true,"text",4,"");
                                     ?>

                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <center style="margin-top: 5px;">
                                        <input type="button" value="Pesquisar" id="btnPesquisaEmpenhos" onclick="js_pesquisarEmpenhos();">
                                    </center>
                                </td>
                            </tr>


                            <tr>
                                <td colspan="2">
                                    <div id="ctnGridEmpenhos" style="margin-top: 5px;"></div>
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>
            </table>




        </fieldset>

        <fieldset>
            <legend>Justificativas de Empenho</legend>

            <table class="form-container">
                <fieldset class="separator">
                    <legend>Justificativa de não vinculação de contrato</legend>

                    <tr>
                        <td class="field-size1">
                            <label for="tipoJustificativaContrato">Tipo:</label>
                        </td>
                        <td>
                            <select id="tipoJustificativaContrato" onchange="validaOutrasJustificativasContrato(true)">
                                <option value="">Selecione</option>
                                <option value="1">Valor inferior ao previsto para Tomada de Preços</option>
                                <option value="2">Compra com entrega imediata e integral, não resultando obrigações futuras</option>
                                <option value="3">Concessionários de serviços públicos</option>
                                <option value="4">Tarifas e obrigações bancárias</option>
                                <option value="5">Taxas, custas, tributos ou emolumentos devidos a outros entes da federação</option>
                                <option value="6">Adiantamentos</option>
                                <option value="7">Outros casos que amparam a não celebração do contrato</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2">
                            <fieldset>
                                <legend>Justificativa</legend>
                                <textarea class="field-size7 readonly" id="descricaoJustificativaContrato" disabled="disabled"></textarea>
                            </fieldset>
                        </td>
                    </tr>
                </fieldset>
            </table>

            <table class="form-container">
                <fieldset class="separator">
                    <legend>Justificativa de não vinculação de licitação ou Dispensa/Inexigibilidade</legend>

                    <tr>
                        <td class="field-size1">
                            <label for="tipoJustificativaLicitacao">Tipo:</label>
                        </td>
                        <td>
                            <select id="tipoJustificativaLicitacao" onchange="validaOutrasJustificativasLicitacao(true)">
                                <option value="">Selecione</option>
                                <option value="1">Valor inferior ao previsto para Tomada de Preços</option>
                                <option value="2">Compra com entrega imediata e integral, não resultando obrigações futuras</option>
                                <option value="3">Concessionários de serviços públicos</option>
                                <option value="4">Tarifas e obrigações bancárias</option>
                                <option value="5">Taxas, custas, tributos ou emolumentos devidos a outros entes da federação</option>
                                <option value="6">Adiantamentos</option>
                                <option value="8">Outros casos que amparam a não realização do procedimento licitatório / dispensa ou inexigibilidade</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2">
                            <fieldset>
                                <legend>Justificativa</legend>
                                <textarea class="field-size7 readonly" id="descricaoJustificativaLicitacao" disabled="disabled"></textarea>
                            </fieldset>
                        </td>
                    </tr>
                </fieldset>
            </table>

        </fieldset>

        <input id="salvar" type="button" value="Salvar" onclick="salvarJustificativas()">
        <input id="excluir" type="button" value="Excluir" onclick="excluirJustificativas()">

    </form>
</div>
</body>

<?php db_menu(); ?>

<script type="text/javascript">


    var sUrl = "con4_justificativacontratolicitacaosigfis.RPC.php";

    function js_gridEmpenhos() {

	  oGridEmpenhos = new DBGrid('GridEmpenhos');
	  oGridEmpenhos.nameInstance = 'oGridEmpenhos';

      oGridEmpenhos.setCheckbox(0);
	  oGridEmpenhos.setCellWidth(new Array( '8%' ,
	                                        '10%' ,
	                                        '10%',
                                            '55%',
                                            '12%'
	                                           ));

      oGridEmpenhos.setCellAlign(new Array( 'right'  ,
	                                        'right',
	                                        'center',
                                            'left',
                                             'right'
	                                         ));

      oGridEmpenhos.setHeader(new Array( 'Seq.',
                                         'Número' ,
                                         'Ano',
                                         'Descrição',
                                         'Valor'
	                                      ));

      oGridEmpenhos.setHeight(200);
      oGridEmpenhos.show($('ctnGridEmpenhos'));
      oGridEmpenhos.clearAll(true);

	}

    function js_pesquisarEmpenhos(){

      var oObject                   = new Object();
          oObject.exec              = "getEmpenhos";
          oObject.dtInicial         = $F("e60_emiss1");
          oObject.dtFinal           = $F("e60_emiss2");
          oObject.e60_vlrempInicial = $F("e60_vlrempInicial");
          oObject.e60_vlrempFinal   = $F("e60_vlrempFinal");

          if (  ( $F("e60_emiss1") == "" ||   $F("e60_emiss2") == "" )  && ( $F("e60_vlrempInicial") == "" || $F("e60_vlrempFinal") == "" )  ) {

            alert("Selecione um Intervalo de Datas ou Uma Faixa de Valores.");
            return false;
          }

      js_divCarregando('Aguarde, buscando Registros...','msgBox');

      new Ajax.Request (sUrl,{
          method:'post',
          parameters:'json='+Object.toJSON(oObject),
          onComplete:js_retornoPesquisarEmpenhos
        }
      );
    }



    function js_retornoPesquisarEmpenhos(oJson) {

      js_removeObj("msgBox");
      var oRetorno = JSON.parse(oJson.responseText);

      if (oRetorno.iStatus == 2) {

        alert(oRetorno.sMessage.urlDecode());
        return false;
      }
      oGridEmpenhos.clearAll(true);
      oRetorno.aDados.each( function( oDados, iIndice  ){

        aRow    = [];
        aRow[0] = oDados.e60_numemp;
        aRow[1] = oDados.e60_codemp;
        aRow[2] = oDados.e60_anousu;
        aRow[3] = oDados.z01_nome.urlDecode();
        aRow[4] = oDados.e60_vlremp;
        oGridEmpenhos.addRow(aRow);

      });

      oGridEmpenhos.renderRows();
    }

    function getEmpenhosSelecionados(){

         var aListaCheckbox = oGridEmpenhos.getSelection();
         var aListaEmpenhos = new Array();

         aListaCheckbox.each(
           function ( aRow ) {
             aListaEmpenhos.push(aRow[0]);
          }
         );
         return aListaEmpenhos;
    }


js_gridEmpenhos();



























    var aEmpenhos = [];

    new DBLookUp(
        $('empenho'),
        $('e60_numemp'),
        $('z01_nome'),
        {
            'sArquivo' : 'func_empempenho.php',
            'sLabel'   : 'Pesquisa de Empenho'
        }
    );

    var oLancadorEmpenho = new DBLancador('oLancadorEmpenho');
    oLancadorEmpenho.setLabelAncora('Empenho:');
    oLancadorEmpenho.setNomeInstancia('oLancadorEmpenho');
    oLancadorEmpenho.setTituloJanela('Pesquisa de Empenho');
    oLancadorEmpenho.setParametrosPesquisa('func_empempenho.php', ['e60_numemp', "z01_nome"]);
    oLancadorEmpenho.setTextoFieldset('Empenhos selecionados');
    oLancadorEmpenho.setGridHeight(200);
    //oLancadorEmpenho.show($('empenhosLote'));

    limpaCampos();

    function salvarJustificativas() {

        if(!validaCampos()) {
            return false;
        }

        if($F('tipoFiltroEmpenho') == 1) {
            aEmpenhos.push($F('e60_numemp'));
        }

        /*
        if($F('tipoFiltroEmpenho') == 2) {
            oLancadorEmpenho.getRegistros().each(function(empenho) {
                aEmpenhos.push(empenho.sCodigo);
            });
        }
        */
        if($F('tipoFiltroEmpenho') == 2) {

            aEmpenhos = getEmpenhosSelecionados();

            if (aEmpenhos.length == 0) {

              alert( "Selecione um Empenho para Justificar." );
              return false;
            }
        }


        new AjaxRequest(
            'con4_justificativacontratolicitacaosigfis.RPC.php',
            {
                'exec'                            : 'salvar',
                'tipoJustificativaContrato'       : $F('tipoJustificativaContrato'),
                'descricaoJustificativaContrato'  : $F('descricaoJustificativaContrato'),
                'tipoJustificativaLicitacao'      : $F('tipoJustificativaLicitacao'),
                'descricaoJustificativaLicitacao' : $F('descricaoJustificativaLicitacao'),
                'aEmpenhos' : aEmpenhos
            },
            function (oRetorno, lErro) {

                if(lErro) {

                    alert(oRetorno.sMessage);
                    return false;
                }

                alert('Empenho(s) justificado(s) com sucesso.');
                oGridEmpenhos.clearAll(true);
                limpaCampos();
            }
        ).execute();
    }

    function excluirJustificativas() {

        if($F('tipoFiltroEmpenho') == 1) {

            if($F('e60_numemp') == '') {
                alert('É necessário informar o campo Empenho para ser excluída sua justificativa.');
                return false;
            }

            aEmpenhos[0] = $F('e60_numemp');
        }

        if($F('tipoFiltroEmpenho') == 2) {

            if(oLancadorEmpenho.getRegistros().length == 0) {
                alert('É necessário selecionar ao menos um empenho para ser excluída a justificativa.');
                return false;
            }

            oLancadorEmpenho.getRegistros().each(function(empenho) {
                aEmpenhos.push(empenho.sCodigo);
            });
        }

        new AjaxRequest(
            'con4_justificativacontratolicitacaosigfis.RPC.php',
            {
                'exec' : 'excluir',
                'aEmpenhos' : aEmpenhos
            },
            function (oRetorno, lErro) {

                if(lErro) {
                    alert(oRetorno.sMessage);
                    return false;
                }

                alert('Exclusão de justificativa(s) de empenho(s) realizada com sucesso.');
                limpaCampos();
            }
        ).execute();
    }

    function validaExibicaoFiltroEmpenho() {

        $('linhaEmpenhoIndividual').setStyle({'display': ''});
        $('linhaEmpenhosLote').setStyle({'display': 'none'});

        if($F('tipoFiltroEmpenho') == 1) {

            $('linhaEmpenhoIndividual').setStyle({'display' : ''});
            $('linhaEmpenhosLote').setStyle({'display' : 'none'});
            oLancadorEmpenho.clearAll();
        }

        if($F('tipoFiltroEmpenho') == 2) {

            $('linhaEmpenhoIndividual').setStyle({'display': 'none'});
            $('linhaEmpenhosLote').setStyle({'display' : ''});
            $('e60_numemp').value = '';
            $('z01_nome').value = '';
        }

    }

    function validaCampos() {

        if($F('tipoFiltroEmpenho') == 1) {

            if($F('e60_numemp') == '') {
                alert('Campo Empenho é de preenchimento obrigatório.');
                return false;
            }
        }

        if($('tipoFiltroEmpenho') == 2) {

            if(oLancadorEmpenho.getRegistros().length == 0) {
                alert('É necessário selecionar empenhos para serem justificados.');
                return false;
            }
        }

        if($F('tipoJustificativaContrato') == '' && $F('tipoJustificativaLicitacao') == '') {
            alert('É necessário selecionar um tipo de justificava para ser realizada.');
            return false;
        }

        if($F('tipoJustificativaContrato') == 7 && $F('descricaoJustificativaContrato') == '') {
            alert('Campo Justificativa da não vinculação de contrato é de preenchimento obrigatório.');
            return false;
        }

        if($F('tipoJustificativaLicitacao') == 8 && $F('descricaoJustificativaLicitacao') == '') {
            alert('Campo Justificativa da não vinculação de licitação é de preenchimento obrigatório.');
            return false;
        }

        return true;
    }

    function validaOutrasJustificativasContrato(limparCampo) {

        $('descricaoJustificativaContrato').setAttribute('disabled', 'disabled');
        $('descricaoJustificativaContrato').setAttribute('class', 'field-size7 readonly');

        if (limparCampo === true) {
          $('descricaoJustificativaContrato').value = '';
        }

        if($F('tipoJustificativaContrato') == 7) {
            $('descricaoJustificativaContrato').removeAttribute('disabled');
            $('descricaoJustificativaContrato').removeAttribute('class');
        }
    }

    function validaOutrasJustificativasLicitacao(limparCampo) {

        $('descricaoJustificativaLicitacao').setAttribute('disabled','disabled');
        $('descricaoJustificativaLicitacao').setAttribute('class', 'field-size7 readonly');

        if (limparCampo === true) {
          $('descricaoJustificativaLicitacao').value = '';
        }

        if($F('tipoJustificativaLicitacao') == 8) {
            $('descricaoJustificativaLicitacao').removeAttribute('disabled');
            $('descricaoJustificativaLicitacao').removeAttribute('class');
        }
    }

    function limpaCampos() {

        $('tipoJustificativaContrato').value = '';
        $('descricaoJustificativaContrato').value = '';
        $('tipoJustificativaLicitacao').value = '';
        $('descricaoJustificativaLicitacao').value = '';
        $('e60_numemp').value = '';
        $('z01_nome').value = '';
        //oLancadorEmpenho.clearAll();
        aEmpenhos.length = 0;
        validaOutrasJustificativasContrato(true);
        validaOutrasJustificativasLicitacao(true);
    }

    $('e60_numemp').observe('change', function() {

      if ($F('e60_numemp') === '') {

        limpaCampos();
        return;
      }

      AjaxRequest.create(
        'con4_justificativacontratolicitacaosigfis.RPC.php',
        {'exec' : 'buscarJustificativaEmpenho', 'codigoEmpenho' : $F('e60_numemp') },
        function (retorno, erro) {

          if (erro) {

            alert(retorno.mensagem);
            return false;
          }
          $('tipoJustificativaContrato').value       = retorno.dadosEmpenho.e08_tipojustificativacontrato;
          $('descricaoJustificativaContrato').value  = retorno.dadosEmpenho.e08_descricaojustificativacontrato;
          $('tipoJustificativaLicitacao').value      = retorno.dadosEmpenho.e08_tipojustificativalicitacao;
          $('descricaoJustificativaLicitacao').value = retorno.dadosEmpenho.e08_descricaojustificativalicitacao;
          validaOutrasJustificativasLicitacao(false);
          validaOutrasJustificativasContrato(false);
        }
      ).execute();
    });
</script>
</html>
