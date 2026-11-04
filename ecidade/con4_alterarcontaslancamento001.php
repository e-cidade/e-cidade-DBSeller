<?PHP
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
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_app.utils.php"));
parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);

$oRotulo = new rotulocampo();
$oRotulo->label("e60_numemp");
$oRotulo->label("e60_codemp");

?>
<html>
<head>

    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link href="estilos/DBFormularios.css" rel="stylesheet" type="text/css">
    <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
    <script  type="text/javascript" src="scripts/scripts.js"></script>
    <script  type="text/javascript" src="scripts/strings.js"></script>
    <script  type="text/javascript" src="scripts/prototype.js"></script>
    <script  type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <script  type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script  type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script  type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>


    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>

<body>

<div class="container">

    <fieldset style="width:600px">
        <legend class="bold">Alteração das contas de lançamento</legend>
        <table>
            <tr>
                <td class="bold">
                    <?php
                    db_ancora('Código Lançamento:', "pesquisaLancamento(true)", 1);
                    ?>
                    </td>
                <td>
                    <?php db_input('c70_codlan', 10, 1, true, 'text', 1, "onchange='pesquisaLancamento(false)'"); ?>
                </td>
            </tr>
            <tr>
                <td class="bold" >
                    <a href="#" id="lblDocumento">Documento:</a>
                </td>
                <td>
                    <?php
                    db_input('c53_coddoc', 10, 1, true, 'text', 1);
                    db_input('c53_descr', 50, 4, true, 'text', 3);
                    ?>
                </td>
            </tr>
            <tr>
                <td nowrap title="<?php echo $Te60_numemp?>">
                <?php db_ancora($Le60_codemp, "js_buscarEmpenho(true);", 1, null, 'aEmpenho'); ?>
                </td>
                <td>
                <?php db_input('e60_codemp', 10, $Ie60_codemp, true, 'text', 4, " onchange='js_buscarEmpenho(false);'");?>
                <?php db_input('e60_numemp', 10, $Ie60_numemp, true, 'hidden', 3);?>
                <?php db_input('z01_nome', 30, $Iz01_nome, true, 'text', 3);?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php db_ancora("<b>Estrutural da Receita:</b>", 'js_receitas(true)', 1); ?>
                </td>
                <td>
                    <?php 
                        db_input('o50_estrutreceita', 30, $Io70_codrec, true, 'text', 1, "onchange='js_receitas(false)'"); 
                        db_input('o57_descr', 60, $Io57_descr, true, 'text', 3);
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php db_ancora("<b>Elemento da despesa:</b>", 'js_despesas(true)', 1); ?>
                </td>
                <td>
                    <?php 
                        db_input('o56_elemento', 15, $Io70_codrec, true, 'text', 1, "onchange='js_despesas(false)'"); 
                        db_input('o56_descr', 60, $Io57_descr, true, 'text', 3);
                    ?>
                </td>
            </tr>
            <tr>
                <td class="bold">Data Inicial:</td>
                <td>
                    <?php
                    db_inputdata('data_inicial', null, null, null, true, 'text', 1);
                    echo "&nbsp;&nbsp;<b>Data Final:</b> ";
                    db_inputdata('data_final', null, null, null, true, 'text', 1);
                    ?>
                </td>
            </tr>

            <tr>
                <td class="bold">
                    <?php
                    db_ancora('Conta Débito:', "pesquisaContaContabil(true, 1)", 1);
                    ?>
                    </td>
                <td>
                    <?php
                    db_input('conta_debito', 10, 1, true, 'hidden', 3);
                    db_input('estrutural_debito', 15, 1, true, 'text', 1, 'onchange="pesquisaContaContabil(false, 1)"');
                    db_input('descricao_debito', 45, 1, true, 'text', 3);
                    ?>
                </td>
            </tr>
            <tr>
                <td class="bold">
                    <?php
                    db_ancora('Conta Crédito:', "pesquisaContaContabil(true, 2)", 1);
                    ?>
                    </td>
                <td>
                    <?php
                    db_input('conta_credito', 10, 1, true, 'hidden', 3);
                    db_input('estrutural_credito', 15, 1, true, 'text', 1, 'onchange="pesquisaContaContabil(false, 2)"');
                    db_input('descricao_credito', 45, 1, true, 'text', 3);
                    ?>
                </td>
            </tr>
        </table>
    </fieldset>
    <p>
        <input type="button" id="btnPesquisa" value="Pesquisar" onclick="pesquisar()" />
    </p>
</div>
<div class="container" style="width: 90%">

    <fieldset>
        <legend class="bold">Resultado da pesquisa</legend>
        <table>
            <tr>
                <td class="bold">
                    <?php
                    db_ancora('Débito Destino:', "pesquisaContaContabil(true, 3)", 1);
                    ?>
                </td>
                <td>
                    <?php
                    db_input('conta_debito_destino', 10, 1, true, 'hidden', 3);
                    db_input('estrutural_debito_destino', 15, 1, true, 'text', 1, 'onchange="pesquisaContaContabil(false, 3)"');
                    db_input('descricao_debito_destino', 45, 1, true, 'text', 3);
                    ?>
                </td>
                <td class="bold">
                    <?php
                    db_ancora('Crédito Destino:', "pesquisaContaContabil(true, 4)", 1);
                    ?>
                </td>
                <td>
                    <?php
                    db_input('conta_credito_destino', 10, 1, true, 'hidden', 3);
                    db_input('estrutural_credito_destino', 15, 1, true, 'text', 1, 'onchange="pesquisaContaContabil(false, 4)"');
                    db_input('descricao_credito_destino', 45, 1, true, 'text', 3);
                    ?>
                </td>
            </tr>
        </table>
        <div id="ctnResultadoPesquisa"></div>
    </fieldset>
    <p><input type="button" value="Alterar contas dos registros selecionados" onclick="salvarRegistros()" /></p>
</div>

</body>
</html>
<?php db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit")); ?>



<script>


    var gridResultado = new DBGrid('gridResultado');
    gridResultado.nameInstance = 'gridResultado';
    gridResultado.setCheckbox(0);
    gridResultado.setHeader(['Lançamento', 'Documento', 'Conta Débito', 'Conta Crédito', 'Valor', 'sequen']);
    gridResultado.setCellWidth(['10%', '8%', '35%', '35%', '10%']);
    gridResultado.setCellAlign(['center', 'center', 'left', 'left', 'right', 'center']);
    gridResultado.aHeaders[6].lDisplayed = false;
    gridResultado.show($('ctnResultadoPesquisa'));

    function salvarRegistros () {

        var campos = {
            'conta_debito_origem' : document.querySelector('#conta_debito').value,
            'conta_debito_destino' : document.querySelector('#conta_debito_destino').value,
            'conta_credito_origem' : document.querySelector('#conta_credito').value,
            'conta_credito_destino' : document.querySelector('#conta_credito_destino').value,
        };

        if (Number(campos.conta_debito_destino) === Number(campos.conta_credito_destino)) {
            return alert("As contas Débito e Crédito de destino estão iguais.");
        }

        var registrosGrid = gridResultado.getSelection();
        if (registrosGrid.length === 0) {
            return alert("Nenhum lançamento selecionado no resultado da pesquisa.");
        }

        var registrosSelecionados = [];
        registrosGrid.each(
            function(linha) {
                registrosSelecionados.push(linha[6]);
            }
        );

        var parametros = {
            "exec" : 'processar',
            "conta_debito_origem" : document.querySelector('#conta_debito').value,
            "conta_debito_destino" : document.querySelector('#conta_debito_destino').value,
            "conta_credito_origem" : document.querySelector('#conta_credito').value,
            "conta_credito_destino" : document.querySelector('#conta_credito_destino').value,
            "lancamentos" : registrosSelecionados,
        };


        AjaxRequest.create(
            'con4_ajustemanualancamentos.RPC.php',
            parametros,
            function (response, erro) {

                alert(response.message);
                if (!erro) {

                    document.querySelector('#conta_debito_destino').value = '';
                    document.querySelector('#estrutural_debito_destino').value = '';
                    document.querySelector('#descricao_debito_destino').value = '';

                    document.querySelector('#conta_credito_destino').value = '';
                    document.querySelector('#estrutural_credito_destino').value = '';
                    document.querySelector('#descricao_credito_destino').value = '';

                    gridResultado.clearAll();
                    var oDownload = new DBDownload();
                    oDownload.addFile(response.arquivo, "Log de Processamento");
                    oDownload.show();

                    pesquisar();
                }
            }
        ).execute();


    }


    function pesquisar() {

        var parametros = {
            'exec' : 'consulta',
            'codigo_lancamento' : document.querySelector('#c70_codlan').value,
            'documento' : document.querySelector('#c53_coddoc').value,
            'data_inicial' : document.querySelector('#data_inicial').value,
            'data_final' : document.querySelector('#data_final').value,
            'conta_debito' : document.querySelector('#conta_debito').value,
            'conta_credito' : document.querySelector('#conta_credito').value,
            'codigo_empenho' : document.querySelector('#e60_numemp').value,
            'estrutural_receita' : document.querySelector('#o50_estrutreceita').value,
            'elemento_despesa' : document.querySelector('#o56_elemento').value,
        };

        AjaxRequest.create(
            'con4_ajustemanualancamentos.RPC.php',
            parametros,
            carregarRegistros
        ).setMessage('Aguarde, pesquisando informações...').execute();
    }

    var oLookUpContratos = new DBLookUp($('lblDocumento'), $('c53_coddoc'), $('c53_descr'), {
        "sArquivo" : "func_conhistdoc.php",
        "sObjetoLookUp" : "db_iframe_conhistdoc",
        "sLabel" : "Pesquisar Documentos"
    });

    function carregarRegistros(response, erro) {

        if (erro) {
            return alert(response.message);
        }

        gridResultado.clearAll(true);
        response.lancamentos.each(
            function (lancamento) {

                var descricaoDebito = lancamento.conta_debito.reduzido + " - " + lancamento.conta_debito.estrutural + " - " + lancamento.conta_debito.descricao;
                var descricaoCredito = lancamento.conta_credito.reduzido + " - " + lancamento.conta_credito.estrutural + " - " + lancamento.conta_credito.descricao;

                gridResultado.addRow([
                    lancamento.codigo_lancamento,
                    lancamento.documento,
                    descricaoDebito,
                    descricaoCredito,
                    lancamento.valor,
                    lancamento.sequen,
                ]);

            }
        );
        gridResultado.renderRows();
    }

    function pesquisaLancamento(mostrar) {

        var url = "func_conlancamlan.php?funcao_js=parent.preencheCampo|c70_codlan";
        if (!mostrar) {
            url = "func_conlancamlan.php?funcao_js=parent.confirmaCampo&pesquisa_chave="+document.querySelector('#c70_codlan').value;
        }

        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_conlancamlan', url, 'Pesquisar Lançamento Contábil', mostrar);
    }

    function preencheCampo(codigoLancamento) {
        document.querySelector('#c70_codlan').value = codigoLancamento;
        db_iframe_conlancamlan.hide();
    }

    function confirmaCampo(codigo, erro) {

        if (erro) {
            alert('Código '+document.querySelector('#c70_codlan').value+' do lançamento não encontrado.');
            document.querySelector('#c70_codlan').value = '';
            return false;
        }
    }

    function pesquisaContaContabil(mostrar, tipoPesquisa) {

        if (document.querySelector('#estrutural_debito').value === '' && !mostrar && tipoPesquisa === 1) {
            document.querySelector('#descricao_debito').value = '';
            document.querySelector('#conta_debito').value = '';
            return false;
        }

        if (document.querySelector('#estrutural_credito').value === '' && !mostrar && tipoPesquisa === 2) {
            document.querySelector('#descricao_credito').value = '';
            document.querySelector('#conta_credito').value = '';
            return false;
        }

        if (document.querySelector('#estrutural_debito_destino').value === '' && !mostrar && tipoPesquisa === 3) {
            document.querySelector('#descricao_debito_destino').value = '';
            document.querySelector('#conta_debito_destino').value = '';
            return false;
        }

        if (document.querySelector('#estrutural_credito_destino').value === '' && !mostrar && tipoPesquisa === 4) {
            document.querySelector('#descricao_credito_destino').value = '';
            document.querySelector('#conta_credito_destino').value = '';
            return false;
        }

        var funcaoPreenche = 'preencheContaContabilDebito';
        var funcaoCompleta = 'completaContaContabilDebito';
        var valorPesquisaEstrutural = document.querySelector('#estrutural_debito').value;

        if (tipoPesquisa === 2) {
            funcaoPreenche = 'preencheContaContabilCredito';
            funcaoCompleta = 'completaContaContabilCredito';
            valorPesquisaEstrutural = document.querySelector('#estrutural_credito').value;
        }

        if (tipoPesquisa === 3) {
            funcaoPreenche = 'preencheContaContabilDebitoDestino';
            funcaoCompleta = 'completaContaContabilDebitoDestino';
            valorPesquisaEstrutural = document.querySelector('#estrutural_debito_destino').value;
        }

        if (tipoPesquisa === 4) {
            funcaoPreenche = 'preencheContaContabilCreditoDestino';
            funcaoCompleta = 'completaContaContabilCreditoDestino';
            valorPesquisaEstrutural = document.querySelector('#estrutural_credito_destino').value;
        }

        var caminhoLookup = 'func_conplanoreduz.php?funcao_js=parent.'+funcaoPreenche+'|c61_reduz|c60_descr|c60_estrut';
        if (!mostrar) {
            caminhoLookup = 'func_conplanoreduz.php?pesquisa_chave='+valorPesquisaEstrutural+'&funcao_js=parent.'+funcaoCompleta+'&pesquisaEstrutural=true';
        }
        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_conplano', caminhoLookup, 'Pesquisar Conta Contábil', mostrar);
    }

    /** funcoes paraa debito */
    function preencheContaContabilDebito(codigoConta, descricaoConta, estrutural) {

        document.querySelector('#conta_debito').value = codigoConta;
        document.querySelector('#descricao_debito').value = descricaoConta + " (" + codigoConta + ")";
        document.querySelector('#estrutural_debito').value = estrutural;
        db_iframe_conplano.hide();
    }

    function completaContaContabilDebito(descricaoConta, erro, estrutural, codigoConta) {

        document.querySelector('#conta_debito').value = codigoConta;
        document.querySelector('#descricao_debito').value = descricaoConta + " (" + codigoConta + ")";
        document.querySelector('#estrutural_debito').value = estrutural;
        if (erro) {
            document.querySelector('#conta_debito').value = '';
            document.querySelector('#estrutural_debito').value = '';
        }
    }

    /** funcoes paraa credito */
    function preencheContaContabilCredito(codigoConta, descricaoConta, estrutural) {

        document.querySelector('#conta_credito').value = codigoConta;
        document.querySelector('#descricao_credito').value = descricaoConta + " (" + codigoConta + ")";
        document.querySelector('#estrutural_credito').value = estrutural;
        db_iframe_conplano.hide();
    }

    function completaContaContabilCredito(descricaoConta, erro, estrutural, codigoConta) {

        document.querySelector('#conta_credito').value = codigoConta;
        document.querySelector('#descricao_credito').value = descricaoConta + " (" + codigoConta + ")";
        document.querySelector('#estrutural_credito').value = estrutural;
        if (erro) {
            document.querySelector('#conta_credito').value = '';
            document.querySelector('#estrutural_credito').value = '';
        }
    }

    /** funcoes paraa debito destino */
    function preencheContaContabilDebitoDestino(codigoConta, descricaoConta, estrutural) {

        document.querySelector('#conta_debito_destino').value = codigoConta;
        document.querySelector('#descricao_debito_destino').value = descricaoConta + " (" + codigoConta + ")";
        document.querySelector('#estrutural_debito_destino').value = estrutural;
        db_iframe_conplano.hide();
    }

    function completaContaContabilDebitoDestino(descricaoConta, erro, estrutural, codigoConta) {

        document.querySelector('#conta_debito_destino').value = codigoConta;
        document.querySelector('#descricao_debito_destino').value = descricaoConta + " (" + codigoConta + ")";
        document.querySelector('#estrutural_debito_destino').value = estrutural;
        if (erro) {
            document.querySelector('#conta_debito_destino').value = '';
            document.querySelector('#estrutural_debito_destino').value = '';
        }
    }

    /** funcoes paraa credito destino */
    function preencheContaContabilCreditoDestino(codigoConta, descricaoConta, estrutural) {

        document.querySelector('#conta_credito_destino').value = codigoConta;
        document.querySelector('#descricao_credito_destino').value = descricaoConta + " (" + codigoConta + ")";
        document.querySelector('#estrutural_credito_destino').value = estrutural;
        db_iframe_conplano.hide();
    }

    function completaContaContabilCreditoDestino(descricaoConta, erro, estrutural, codigoConta) {

        document.querySelector('#conta_credito_destino').value = codigoConta;
        document.querySelector('#descricao_credito_destino').value = descricaoConta + " (" + codigoConta + ")";
        document.querySelector('#estrutural_credito_destino').value = estrutural;
        if (erro) {
            document.querySelector('#conta_credito_destino').value = '';
            document.querySelector('#estrutural_credito_destino').value = '';
        }
    }


    /**
     * Busca empenho
     *
     * @param boolean $lMostra - mostra ou nao janela de pesquisa
     * @access public
     * @return boolean
     */
    function js_buscarEmpenho(lMostra) {
        if (lMostra) {
            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_iframe_empempenho',
                'func_empempenho.php?funcao_js=parent.js_retornoBuscaEmpenhoAncora|e60_numemp|e60_codemp|z01_nome',
                'Pesquisa',
                true
            );
            return true;
        }

        if ($F('e60_codemp') != '') {
            var aCodigoEmpenho = $F('e60_codemp').split('/');
            var iCodigoEmpenho = aCodigoEmpenho[0];
            var sParametroAnoEmpenho = null;

            if (aCodigoEmpenho.length > 1) {
                sParametroAnoEmpenho = '&iAnoEmpenho=' + aCodigoEmpenho[1];
            }

            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_iframe_empempenho',
                'func_empempenho.php?lPesquisaPorCodigoEmpenho=true' + sParametroAnoEmpenho +
                '&pesquisa_chave=' + iCodigoEmpenho +
                '&funcao_js=parent.js_retornoBuscaEmpenhoInput',
                'Pesquisa',
                false
            );
        } else {
            $('z01_nome').value = '';
            $('e60_codemp').value = '';
            $('e60_numemp').value = '';
        }
    }

    /**
    * Retorno da pesquisa de empenho pelo campo input(onchange)
    *
    * @param string $sNome
    * @param boolean $lErro
    * @access public
    * @return void
    */
    function js_retornoBuscaEmpenhoInput(iNumeroEmpenho, sNome, lErro) {
        if (lErro == 'false' || lErro == false ) {
            $('e60_numemp').value = iNumeroEmpenho;
            $('z01_nome').value = sNome;
        } else {
            $('z01_nome').value = 'Empenho ' + $('e60_codemp').value + ' Não Encontrado';
            $('e60_codemp').focus();
            $('e60_codemp').value = '';
            $('e60_numemp').value = '';
        }
    }

    /**
    * Retorno da pesquisa de empenho pelo ancora(onclick)
    *
    * @param integer $iNumeroEmpenho
    * @param string $sNome
    * @access public
    * @return void
    */
    function js_retornoBuscaEmpenhoAncora(iNumeroEmpenho, sCodigoEmpenho, sNome) {
        $('e60_codemp').value = sCodigoEmpenho;
        $('e60_numemp').value = iNumeroEmpenho;
        $('z01_nome').value = sNome;
        db_iframe_empempenho.hide();
    }

    /**
     * funcao de pesquisa para receitas
     */
    function js_receitas(lMostra){
        if (lMostra == true) {
            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_iframe_orcreceita',
                'func_orcreceita.php?funcao_js=parent.js_mostraReceita|o50_estrutreceita|o57_descr',
                'Pesquisa',
                true
            );
        } else {
            if ($F('o50_estrutreceita') != '') {
                js_OpenJanelaIframe(
                    'CurrentWindow.corpo',
                    'db_iframe_orcreceita',
                    'func_orcreceita.php?pesquisa_estrutural=true&o50_estrutreceita=' + $F('o50_estrutreceita') + '&funcao_js=parent.js_mostraReceita1',
                    'Pesquisa',
                    false
                );
            } else {
                $('o50_estrutreceita').value = '';
                $('o57_descr').value = '';
            }
        }
    }

    function js_mostraReceita(iCodigoReceita, sDescricao)
    {
        $('o50_estrutreceita').value = iCodigoReceita;
        $('o57_descr').value  = sDescricao;
        db_iframe_orcreceita.hide();
    }
 
    function js_mostraReceita1(iCodigoReceita, lErro)
    {
        console.log(iCodigoReceita);
        console.log(lErro);
        if (lErro == 'false' || lErro == false) {
            $('o57_descr').value = iCodigoReceita;
        } else {
            $('o50_estrutreceita').value = "";
            $('o57_descr').value  = iCodigoReceita;
        }
    }

    /**
     * funcao de pesquisa para receitas
     */
    function js_despesas(lMostra){
        if (lMostra == true) {
            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_iframe_orcdespesa',
                'func_orcdotacao.php?funcao_js=parent.js_mostraDespesa|o56_elemento|o56_descr',
                'Pesquisa',
                true
            );
        } else {
            if ($('o56_elemento') != '') {
                js_OpenJanelaIframe(
                    'CurrentWindow.corpo',
                    'db_iframe_orcdespesa',
                    'func_orcdotacao.php?pesquisa_elemento=true&o56_elemento=' + $F('o56_elemento') + '&funcao_js=parent.js_mostraDespesa1',
                    'Pesquisa',
                    false
                );
            } else {
                $('o56_elemento').value = '';
                $('o56_descr').value = '';
            }
        }
    }

    function js_mostraDespesa(iCodigoDespesa, sDescricao)
    {
        $('o56_elemento').value = iCodigoDespesa;
        $('o56_descr').value  = sDescricao;
        db_iframe_orcdespesa.hide();
    }
 
    function js_mostraDespesa1(sDescricao, lErro)
    {
        console.log(sDescricao);
        console.log(lErro);
        if (lErro == 'false' || lErro == false) {
            $('o56_descr').value = sDescricao;
        } else {
            $('o56_elemento').value = "";
            $('o56_descr').value  = sDescricao;
        }
    }
</script>
