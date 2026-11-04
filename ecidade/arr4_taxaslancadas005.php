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
require_once(modification("libs/db_app.utils.php"));

$clrotulo = new rotulocampo;
$clrotulo->label("ar44_procedencia");

?>
<html>

<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>

<body>
    <div class="container">
        <form name="form1" method="post">
            <input type="hidden" name="geraDebito">
            <fieldset>
                <legend>Lançamento de Taxa</legend>
                <table class="form-container">
                    <tr>
                        <td style="width: 70px;">
                            <strong>Periodo:</strong>
                        </td>
                        <td>
                            <?php db_inputdata('dataInicio',"","","",true,'text',1) ?> 
                            <b>até</b> 
                            <?php db_inputdata('dataFim',"","","",true,'text',1) ?> 
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 70px;">
                            <strong>Situação:</strong>
                        </td>
                        <td>
                            <input type="checkbox" situacao="true" id="situacao_1" value="1" checked>
                            <label for="situacao_1" style="position: relative; top: -5px; margin-right: 31px; font-weight: normal;">Pago</label>

                            <input type="checkbox" situacao="true" id="situacao_2" value="2" checked>
                            <label for="situacao_2" style="position: relative; top: -5px; font-weight: normal;">Pendente</label>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 70px;"></td>
                        <td>
                            <input type="checkbox" situacao="true" id="situacao_3" value="3" checked>
                            <label for="situacao_3" style="position: relative; top: -5px; font-weight: normal;">Cancelado</label>

                            <input type="checkbox" situacao="true" id="situacao_4" value="4" checked>
                            <label for="situacao_4" style="position: relative; top: -5px; font-weight: normal;">Inscrito Cob. Adm</label>

                        </td>
                    </tr>
                    <tr>
                        <td style="width: 70px;"></td>
                        <td>
                            <input type="checkbox" situacao="true" id="situacao_5" value="5" checked>
                            <label for="situacao_5" style="position: relative; top: -5px; font-weight: normal;">Parcelado</label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Tipo Lançamento:</strong>
                        </td>
                        <td>
                            <select name="gerouDebito" id="gerouDebito" style="width: 240;">
                                <option value="0">Todos</option>
                                <option value="1">Gerou Débito</option>
                                <option value="2">Não Gerou Débito</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Modelo Relatório:</strong>
                        </td>
                        <td>
                            <select name="modeloRelatorio" id="modeloRelatorio" style="width: 240;">
                                <option value="1">Sintético</option>
                                <option value="2">Analítico</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php
                            db_ancora("<strong>Departamentos</strong>", "js_pesquisaDepartamento(true);", 4);
                            db_input("departamentos", 5, false, "departamentos", "hidden", 1);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <fieldset>
                                <legend>Taxa</legend>
                                <table>
                                    <tr>
                                        <td>
                                            <?php
                                            db_ancora("<strong>Taxa</strong>", "js_pesquisaTaxa(true);", 4);
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            db_input("ar44_sequencial", 5, @$Iar44_sequencial, true, "text", 1, "onchange='js_pesquisaTaxa(false);'", "", "white");
                                            db_input("ar44_descricao", 40, false, true, "text", 5, "", "", "", "width: 260px;");
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <div id="ctnGridTaxas"></div>
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <input name="salvar" id="salvar" type="button" onclick="js_geraRelatorio();" value="Processar">
        </form>
    </div>
    <?php db_menu(); ?>
</body>

</html>
<script>
    const oTaxas = [];

    function js_pesquisaDepartamento(mostra)
    {
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_departamento','arr4_taxaslancadas002.php?departamentos='+document.form1.departamentos.value,'Pesquisa',true);
    }

    function js_ocultaDepartamento ()
    {
        db_iframe_departamento.hide();
    }

    function js_pesquisaTaxa(mostra)
    {
        const departamentos = document.form1.departamentos.value;

        if(mostra==true){
            js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_taxaslancadas','func_taxaslancadas.php?funcao_js=parent.js_mostraTaxaLancada|ar44_sequencial|ar44_descricao&departamentos='+departamentos,'Pesquisa',true);
        }else{
            if (document.form1.ar44_sequencial.value != "") {
                js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_taxaslancadas','func_taxaslancadas.php?pesquisa_chave='+document.form1.ar44_sequencial.value+'&funcao_js=parent.js_mostraTaxaLancada1&departamentos='+departamentos,'Pesquisa',false);
            } else {
                document.form1.ar44_sequencial.value = '';            
            }
        }
    }

    function js_mostraTaxaLancada(chave1,chave2)
    {
        document.form1.ar44_sequencial.value = chave1;
        document.form1.ar44_descricao.value = chave2;
        db_iframe_taxaslancadas.hide();

        if (js_verificaDadosArray(oTaxas, chave1)) {
            document.form1.ar44_sequencial.value = "";
            document.form1.ar44_descricao.value = "";
            document.form1.ar44_sequencial.focus();
            return false;
        }

        oTaxas.push({"codigo" : chave1, "descricao" : chave2});

        js_listaTaxas();
    }

    function js_mostraTaxaLancada1(chave,erro)
    {
        document.form1.ar44_descricao.value = chave;

        if(erro==true){ 
            document.form1.ar44_sequencial.focus(); 
            document.form1.ar44_sequencial.value = ''; 
            document.form1.ar44_descricao.value = ''; 
        } else {

            if (js_verificaDadosArray(oTaxas, document.form1.ar44_sequencial.value)) {
                document.form1.ar44_sequencial.value = "";
                document.form1.ar44_descricao.value = "";
                document.form1.ar44_sequencial.focus();
                return false;
            }

            oTaxas.push({"codigo" : document.form1.ar44_sequencial.value, "descricao" : chave});

            setTimeout(() => {
                js_listaTaxas();
            }, 500);
        }
    }

    // GRID DAS TAXAS >>>

    var oGridTaxas = new DBGrid('gridTaxas');
    var aHeaders   = ["Código", "Descrição", "Ação"];
    var aCellWidth = ["20%", "60%", "20%"];
    var aCellAlign = ["center", "left", "center"];

    oGridTaxas.nameInstance = 'oGridTaxas';
    oGridTaxas.setCellWidth(aCellWidth);
    oGridTaxas.setCellAlign(aCellAlign);
    oGridTaxas.setHeader(aHeaders);
    oGridTaxas.setHeight(100);
    oGridTaxas.show($('ctnGridTaxas'));

    function js_listaTaxas()
    {
        oGridTaxas.clearAll(true);

        oTaxas.forEach(function (oTaxa){
            var aLinha = [];
            aLinha.push(oTaxa.codigo);
            aLinha.push(oTaxa.descricao);

            var oBtnAlterar = document.createElement('input');
            oBtnAlterar.setAttribute("value", "R");
            oBtnAlterar.setAttribute("type", "button");
            oBtnAlterar.setAttribute("id", "btnRemover_" + oTaxa.codigo);
            oBtnAlterar.setAttribute("onclick", "js_removerTaxa("+oTaxa.codigo+")");

            aLinha.push(oBtnAlterar.outerHTML);

            oGridTaxas.addRow(aLinha);
        });

        oGridTaxas.renderRows();

        document.form1.ar44_sequencial.value = ''; 
        document.form1.ar44_descricao.value = '';

        document.form1.ar44_sequencial.focus();
    }

    function js_removerTaxa(codigo)
    {
        for (var i = 0; i < oTaxas.length; i++) {
            if (oTaxas[i].codigo == codigo) {
                oTaxas.splice(i);
                break;
            }
        }

        js_listaTaxas();
    }

    // <<< GRID DAS TAXAS

    function js_verificaDadosArray(objeto, codigo)
    {
        var erro = false;

        objeto.forEach(function (elemento) {
            if (elemento.codigo == codigo && !erro) {
                alert("Item: "+elemento.codigo+" - "+elemento.descricao+" já incluso na lista.");
                erro = true;
            }
        });

        return erro;
    }

    function js_ajustaArrayFiltro()
    {
        const aTaxa = [];
        const aSituacao = [];

        oTaxas.forEach(function(oTaxa){
            aTaxa.push(oTaxa.codigo);
        });

        const oSituacoes = document.querySelectorAll('[situacao="true"]');

        oSituacoes.forEach(function(oSituacao) {
            if (oSituacao.checked == true) {
                aSituacao.push(oSituacao.value);
            }
        });

        return {"aTaxa" : aTaxa, "aSituacao" : aSituacao};
    }

    function js_validaCampos(oDados)
    {
        const obj = document.form1;

        if (obj.dataInicio.value == "") {
            alert("Data inicial deve ser preenchida.");
            return false;
        }

        if (obj.dataFim.value == "") {
            alert("Data final deve ser preenchida");
            return false;
        }

        const dataInicio = new Date(obj.dataInicio.value);
        const dataFim = new Date(obj.dataFim.value);

        if (dataInicio > dataFim) {
            alert("A data inicial não pode ser maior que a final.");
            return false;
        }

        if (oDados.aSituacao.join() == "") {
            alert("Marque uma situação.");
            return false;
        }

        if (obj.modeloRelatorio.value == "") {
            alert("Campo Modelo Relatório não preenchido.");
            return false;
        }

        return true;
    }

    function js_geraRelatorio()
    {
        const oDados = js_ajustaArrayFiltro();

        if (!js_validaCampos(oDados)) {
            return false;
        }

        var sTaxas = "";
        if (oDados.aTaxa.join() != "") {
            sTaxas = oDados.aTaxa.join();
        }

        var dataInicio = "";
        var dataFim = "";

        const obj = document.form1;

        if (obj.dataInicio.value != "") {
            const aDataInicio = obj.dataInicio.value.split("/");
            dataInicio = aDataInicio[2]+"-"+aDataInicio[1]+"-"+aDataInicio[0];   
        }

        if (obj.dataFim.value != "") {
            const aDataFim = obj.dataFim.value.split("/");
            dataFim = aDataFim[2]+"-"+aDataFim[1]+"-"+aDataFim[0];
        }

        var sUrl = 'arr4_taxaslancadas006.php?';
            sUrl += 'dataInicio='+dataInicio+'&';
            sUrl += 'dataFim='+dataFim+'&';
            sUrl += 'situacao='+oDados.aSituacao.join()+'&';
            sUrl += 'departamentos='+obj.departamentos.value+'&';
            sUrl += 'gerouDebito='+obj.gerouDebito.value+'&';
            sUrl += 'modeloRelatorio='+obj.modeloRelatorio.value+'&';
            sUrl += 'taxa='+sTaxas;

        jan = window.open(sUrl, '', 'width='+(screen.availWidth-5)+', height='+(screen.availHeight-40)+', scrollbars=1, location=0');
        jan.moveTo(0,0);
    }
</script>