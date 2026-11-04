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

    $rotulo = new rotulocampo();
    $rotulo->label("z01_numcgm");
    $rotulo->label("j01_matric");
    $rotulo->label("q02_inscr");
?>
    <html>
    <head>
        <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta http-equiv="Expires" CONTENT="0">
        <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
        <script src="scripts/jquery-2.1.1.min.js" rel="script" type="text/javascript"></script>
        <link rel="stylesheet" href="assets/fontawesome/css/all.min.css">
        <link href="estilos.css" rel="stylesheet" type="text/css">
        <style>
            form > fieldset {
                margin-left: 0px;
                margin-right: 0px;
            }

            legend {
                width: auto;
                margin-left: auto;
                margin-right: auto;
            }

            #container-iniciais {
                margin-top: 30px;
                width: 50%;
                border: solid 2px #8a8a8a;
            }

            #container-iniciais > table {
                float:left;
                width: 100%;
            }

            #container-iniciais > table > thead > tr {
                background-color: #8a8a8a !important;
                height: 25px;
            }

            #container-iniciais > div {
                max-height: 375px;
                overflow-y: scroll;
                margin: auto;
                width: 100%;
                scrollbar-width: thin;
                scrollbar-color: #8a8a8a #aeaeaf;
            }

            #container-iniciais > div > table > tbody > tr > th {
                background-color: #bdbbbb !important;
                height: 25px;
            }

            #container-iniciais > div td {
                border: 1px solid #aaaaaa;
            }

            .linhaInicial {
                background-color: #aeaeaf;
            }

            .headerCda {
                background-color: #eeeff2;
            }

            .bodyCda {
                background-color: #ffffff;
            }

            .center {
                text-align: center;
            }

            .fas {
                font-size: 15px;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
    <div class="container">
        <form name="form1" method="post">
            <fieldset>
                <legend><strong>Unificação de Iniciais</strong></legend>
                <table class="form-container">
                    <tr>
                        <td>
                            <?php
                                db_ancora("CGM:", "", 1, "", "ancoraCgm");
                            ?>
                        </td>
                        <td>
                            <?php
                                db_input('z01_numcgm', 5, $Iz01_numcgm, true, 'text', 1);
                                db_input('z01_nome', 30, 0, true, 'text', 3, "", "z01_nomecgm");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php
                                db_ancora($Lj01_matric, "", 1, "", "ancoraMatricula");
                            ?>
                        </td>
                        <td>
                            <?php
                                db_input('j01_matric', 5, $Ij01_matric, true, 'text', 1);
                                db_input('z01_nome', 30, 0, true, 'text', 3, "", "z01_nomematri");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php
                                db_ancora($Lq02_inscr, "", 1, "", "ancoraInscricao");
                            ?>
                        </td>
                        <td>
                            <?php
                                db_input('q02_inscr', 5, $Iq02_inscr, true, 'text', 1);
                                db_input('z01_nome', 30, 0, true, 'text', 3, "", "z01_nomeinscr");
                            ?>
                        </td>
                    </tr>
                    <tr id="trAgrupamento" style="display: none">
                        <td>
                            <strong>Agrupamento:</strong>
                        </td>
                        <td>
                            <select id="agrupamento">
                                <option value="1">CGM</option>
                                <option value="2">Matricula</option>
                                <option value="3">Inscrição</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <input id="pesquisar" type="button" onclick="js_pesquisar();" value="Pesquisar" disabled>
        </form>
    </div>
    <div class="container" id="container-iniciais">
        <table cellspacing=0 cellpadding=0>
            <thead>
                <tr>
                    <th width="10%" colspan="1">Primária</th>
                    <th width="10%" colspan="1">Secundária(s)</th>
                    <th width="25%" colspan="1">Processo</th>
                    <th width="10%" colspan="1">Inicial</th>
                    <th width="25%" colspan="1">Tipo Débito</th>
                    <th width="10%" colspan="1">Data</th>
                    <th width="5.5%" colspan="1">Origem</th>
                    <th width="4.5%" colspan="1"></th>
                </tr>
            </thead>
        </table>
        <div>
            <table cellspacing=0 cellpadding=0 width="100%">
                <tbody id="dados"></tbody>
            </table>
        </div>
    </div>
    <div class="container">
        <input id="processar" type="button" onclick="js_processar();" value="Processar" disabled>
    </div>
    <?php db_menu(); ?>
    </body>
    </html>
    <script>
        document.getElementById("ancoraCgm").addEventListener("click", (oEvent) => {
            js_ajustaCampos(document.getElementById("z01_numcgm"));
            js_buscaCgm(true);
        });

        document.getElementById("z01_numcgm").addEventListener("change", (oEvent) => {
            js_desbloquearBotaoPesquisar();
            js_ajustaCampos(oEvent.target);
            js_buscaCgm();
        });

        document.getElementById("ancoraMatricula").addEventListener("click", (oEvent) => {
            js_ajustaCampos(document.getElementById("j01_matric"));
            js_buscaMatriula(true);
        });

        document.getElementById("j01_matric").addEventListener("change", (oEvent) => {
            js_desbloquearBotaoPesquisar();
            js_ajustaCampos(oEvent.target);
            js_buscaMatriula();
        });

        document.getElementById("ancoraInscricao").addEventListener("click", (oEvent) => {
            js_ajustaCampos(document.getElementById("q02_inscr"));
            js_buscaInscricao(true);
        });

        document.getElementById("q02_inscr").addEventListener("change", (oEvent) => {
            js_desbloquearBotaoPesquisar();
            js_ajustaCampos(oEvent.target);
            js_buscaInscricao();
        });

        function js_ajustaCampos(oCampo) {
            let bCgm = false;
            let bMatricula = false;
            let bInscricao = false;

            switch (oCampo.id) {
                case "z01_numcgm":
                    bMatricula = true;
                    bInscricao = true;
                    break;
                case "j01_matric":
                    bCgm = true;
                    bInscricao = true;
                    break;
                case "q02_inscr":
                    bCgm = true;
                    bMatricula = true;
                    break;
            }

            if (bCgm) {
                document.getElementById("z01_numcgm").value = "";
                document.getElementById("z01_nomecgm").value = "";
                js_ajustaAgrupamento(false);
            }

            if (bMatricula) {
                document.getElementById("j01_matric").value = "";
                document.getElementById("z01_nomematri").value = "";
            }

            if (bInscricao) {
                document.getElementById("q02_inscr").value = "";
                document.getElementById("z01_nomeinscr").value = "";
            }

        }

        function js_buscaCgm(bMostra = false) {
            let sFunc = 'func_nome.php?funcao_js=parent.js_mostraCgm|0|1';

            if (!bMostra) {
                const iCgm = document.getElementById("z01_numcgm").value;

                if (!iCgm) return;

                sFunc = 'func_nome.php?pesquisa_chave='+iCgm+'&funcao_js=parent.js_mostraCgm2';
            }

            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_cgm_iframe', sFunc, 'Pesquisa', bMostra);
        }

        function js_mostraCgm(chave1, chave2) {
            document.form1.z01_numcgm.value = chave1;
            document.form1.z01_nomecgm.value = chave2;

            js_ajustaAgrupamento(true);
            js_desbloquearBotaoPesquisar(false);

            db_cgm_iframe.hide();
        }

        function js_mostraCgm2(erro, chave) {
            document.form1.z01_nomecgm.value = chave;

            js_ajustaAgrupamento(true);
            js_desbloquearBotaoPesquisar(erro);

            if (erro == true){
                js_ajustaAgrupamento(false);
                document.form1.z01_numcgm.focus();
                document.form1.z01_numcgm.value = '';
            }
        }

        function js_buscaMatriula(bMostra = false) {
            let sFunc = 'func_iptubase.php?funcao_js=parent.js_mostraMatricula|j01_matric|z01_nome';

            if (!bMostra) {
                const iMatricula = document.getElementById("j01_matric").value;

                if (!iMatricula) return;

                sFunc = 'func_iptubase.php?pesquisa_chave='+iMatricula+'&funcao_js=parent.js_mostraMatricula2';
            }

            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_matricula_iframe', sFunc, 'Pesquisa', bMostra);
        }

        function js_mostraMatricula(chave1, chave2) {
            document.form1.j01_matric.value = chave1;
            document.form1.z01_nomematri.value = chave2;

            js_desbloquearBotaoPesquisar(false);

            db_matricula_iframe.hide();
        }

        function js_mostraMatricula2(chave, erro) {
            document.form1.z01_nomematri.value = chave;

            js_desbloquearBotaoPesquisar(erro);

            if (erro == true) {
                document.form1.j01_matric.focus();
                document.form1.j01_matric.value = '';
            }
        }

        function js_buscaInscricao(bMostra = false) {
            let sFunc = 'func_issbase.php?funcao_js=parent.js_mostraInscricao|q02_inscr|z01_nome';

            if (!bMostra) {
                const iInscricao = document.getElementById("q02_inscr").value;

                if (!iInscricao) return;

                sFunc = 'func_issbase.php?pesquisa_chave='+iInscricao+'&funcao_js=parent.js_mostraInscricao2';
            }

            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_inscricao_iframe', sFunc, 'Pesquisa', bMostra);
        }

        function js_mostraInscricao(chave1, chave2) {
            document.form1.q02_inscr.value = chave1;
            document.form1.z01_nomeinscr.value = chave2;

            js_desbloquearBotaoPesquisar(false);

            db_inscricao_iframe.hide();
        }

        function js_mostraInscricao2(chave,erro){
            document.form1.z01_nomeinscr.value = chave;

            js_desbloquearBotaoPesquisar(erro);

            if (erro == true) {
                document.form1.q02_inscr.focus();
                document.form1.q02_inscr.value = '';
            }
        }

        function js_ajustaAgrupamento(bMostra) {
            if (bMostra) {
                jQuery("#trAgrupamento").show();
            } else {
                jQuery("#trAgrupamento").hide();
                jQuery("#agrupamento").val("1");
            }
        }

        function js_pesquisar() {
            const cgm = document.getElementById("z01_numcgm").value;
            const matricula = document.getElementById("j01_matric").value;
            const inscricao = document.getElementById("q02_inscr").value;

            if (!cgm && !matricula && !inscricao) {
                alert("Selecione uma origem.");
                return;
            }

            const oParam = {executa: "getIniciais"};
            oParam.agrupamento = document.getElementById("agrupamento").value;

            if (cgm) {
                oParam.codigo = cgm;
                oParam.origem = 1;
            } else {
                if (matricula) {
                    oParam.codigo = matricula;
                    oParam.origem = 2;
                } else {
                    if (inscricao) {
                        oParam.codigo = inscricao;
                        oParam.origem = 3;
                    }
                }
            }

            new AjaxRequest("jur01_unificainicial.RPC.php", oParam, js_carregaIniciais).execute();
        }

        function js_carregaIniciais(oResponse) {
            const oDados = document.getElementById("dados");
            oDados.innerHTML = "";

            if (oResponse.erro) {
                alert(oResponse.mensagem);
                return;
            }

            Object.values(oResponse.iniciais).forEach((oInicial) => {

                oDados.appendChild(js_defaultInicialBody(oInicial));

                Object.values(oInicial.cdas).forEach((oCda, key) => {
                    if (key == 0) {
                        oDados.appendChild(js_defaultCdaHeader(oInicial.codigo_inicial));
                    }

                    oCda.identificador = oInicial.codigo_inicial;

                    oDados.appendChild(js_defaultCdaBody(oCda));
                });
            });

            jQuery('.fas').click(js_mostraCdas);
            jQuery('.pri').click(js_desmarcarRadio);
            jQuery('.sec').click(js_desbloquearBotaoProcessar);
        }

        function js_mostraCdas() {
            const identificador = jQuery(this).attr('identificador');
            const selector = 'tr[identificador="' + identificador + '"]';

            if (jQuery(this).hasClass('fa-arrow-alt-circle-left')) {
                jQuery(selector).show(200);
                jQuery(this).removeClass('fa-arrow-alt-circle-left');
                jQuery(this).addClass('fa-arrow-alt-circle-down');
                jQuery(this).attr('title', 'Fechar');
            } else {
                jQuery(selector).hide(200);
                jQuery(this).removeClass('fa-arrow-alt-circle-down');
                jQuery(this).addClass('fa-arrow-alt-circle-left');
                jQuery(this).attr('title', 'Expandir');
            }
        }

        function js_createTr(sIdentificador = null, sStyle = null, sClass = null) {
            const tr = document.createElement("tr");

            if (sIdentificador) {
                tr.setAttribute("identificador", sIdentificador);
            }

            if (sStyle) {
                tr.setAttribute("style", sStyle);
            }

            if (sClass) {
                tr.setAttribute("class", sClass);
            }

            return tr;
        }

        function js_createTd(sValor, sWidth, sClass = null, iColspan = null) {
            const td = document.createElement("td");
            td.setAttribute("width", `${sWidth}%`);

            if (sClass) {
                td.setAttribute("class", sClass);
            }

            if (iColspan) {
                td.setAttribute("colspan", iColspan);
            }

            if (typeof sValor == "object") {
                td.appendChild(sValor);
            } else {
                td.innerHTML = sValor;
            }

            return td;
        }

        function js_defaultInicialBody(oInicial) {
            const tr = js_createTr(null, null, "linhaInicial");

            const radioPri = document.createElement("input");
            radioPri.setAttribute("type", "radio");
            radioPri.setAttribute("class", "pri");
            radioPri.setAttribute("identificador", oInicial.codigo_inicial);
            radioPri.setAttribute("processo", oInicial.codigo_processo);

            tr.appendChild(js_createTd(radioPri, 10, "center"));

            const sCdas = Object.values(oInicial.cdas).map(oCda => oCda.codigo_certidao).join(",");

            const checkboxSec = document.createElement("input");
            checkboxSec.setAttribute("type", "checkbox");
            checkboxSec.setAttribute("class", "sec");
            checkboxSec.setAttribute("identificador", oInicial.codigo_inicial);
            checkboxSec.setAttribute("processo", oInicial.codigo_processo);
            checkboxSec.setAttribute("disabled", "true");
            checkboxSec.setAttribute("cdas", sCdas);

            tr.appendChild(js_createTd(checkboxSec, 10, "center"));

            tr.appendChild(js_createTd(oInicial.codigo_processo, 25, "center"));
            tr.appendChild(js_createTd(oInicial.codigo_inicial, 10, "center"));
            tr.appendChild(js_createTd(oInicial.tipo_debito, 25, "center"));
            tr.appendChild(js_createTd(oInicial.data_inicial, 10, "center"));

            const sOrigem = oInicial.origem.join(", ");
            tr.appendChild(js_createTd(sOrigem, 5.5, "center"));

            const icon = document.createElement("i");
            icon.setAttribute("class", "fas fa-arrow-alt-circle-left");
            icon.setAttribute("identificador", oInicial.codigo_inicial);
            icon.setAttribute("title", "Expandir");

            tr.appendChild(js_createTd(icon, 4.5, "center"));

            return tr;
        }

        function js_defaultCdaHeader(sIdentificador) {
            const tr = js_createTr(sIdentificador, "display: none; ", "headerCda");

            tr.appendChild(js_createTd("<strong>CDA</strong>", 10, "center"));
            tr.appendChild(js_createTd("<strong>Exercício</strong>", 10, "center"));
            tr.appendChild(js_createTd("<strong>Receitas</strong>", 50, "center", 2));
            tr.appendChild(js_createTd("<strong>Total</strong>", 30, "center", 4));

            return tr;
        }

        function js_defaultCdaBody(oCda) {
            const sReceitas =  oCda.nome_procedencia.join(", ");
            const sValor =  oCda.valor_total.toLocaleString('pt-BR', {maxiFractionDigits: 2});

            const tr = js_createTr(oCda.identificador, "display: none", "bodyCda");

            tr.appendChild(js_createTd(oCda.codigo_certidao, 10, "center"));
            tr.appendChild(js_createTd(oCda.exercicio_divida, 10, "center"));
            tr.appendChild(js_createTd(sReceitas, 50, null, 2));
            tr.appendChild(js_createTd(sValor, 30, "center", 4));

            return tr;
        }

        function js_desmarcarRadio(oCampo)
        {
            const aRadio = document.querySelectorAll("input[type='radio']");

            aRadio.forEach(function (oCampo1) {
                if (oCampo.target.getAttribute("identificador") != oCampo1.getAttribute("identificador")) {
                    oCampo1.checked = false;
                }
            });

            const aCheckbox = document.querySelectorAll("input[type='checkbox']");

            aCheckbox.forEach(function (oCampo1) {
                if (oCampo.target.getAttribute("identificador") != oCampo1.getAttribute("identificador")) {
                    if (
                        (oCampo.target.getAttribute("processo") && oCampo1.getAttribute("processo"))
                            &&
                        (oCampo.target.getAttribute("processo") == oCampo1.getAttribute("processo"))
                    ) {
                        oCampo1.removeAttribute("disabled");
                        jQuery(oCampo1).show();
                    } else {
                        if (!oCampo.target.getAttribute("processo") && !oCampo1.getAttribute("processo")) {
                            oCampo1.removeAttribute("disabled");
                            jQuery(oCampo1).show();
                        } else {
                            oCampo1.setAttribute("disabled", "true");
                            oCampo1.checked = false;
                            jQuery(oCampo1).hide();
                        }
                    }
                } else {
                    oCampo1.setAttribute("disabled", "true");
                    oCampo1.checked = false;
                    jQuery(oCampo1).hide();
                }
            });
        }

        function js_desbloquearBotaoPesquisar(bBloquear = true) {
            if (bBloquear) {
                $("#pesquisar").attr('disabled','disabled');
                $("#dados").html("");
                $("#processar").attr('disabled','disabled');
            } else {
                $("#pesquisar").removeAttr('disabled');
            }
        }

        function js_desbloquearBotaoProcessar() {
            const aCheckbox = document.querySelectorAll("input[type='checkbox']:checked");

            if (aCheckbox.length == 0) {
                $("#processar").attr('disabled','disabled');
            } else {
                $("#processar").removeAttr('disabled');
            }
        }

        function js_processar() {
            const oInicialPri = document.querySelector("input[type='radio']:checked");

            const aCheckbox = document.querySelectorAll("input[type='checkbox']:checked");
            const aIniciaisSec = [];
            let sCdas = "";

            aCheckbox.forEach(function (oCampo) {
                aIniciaisSec.push(oCampo.getAttribute("identificador"));
                sCdas += oCampo.getAttribute("cdas");
            });

            const oParam = {executa: "verificaOrigens"};
            oParam.inicialPrimaria = oInicialPri.getAttribute("identificador");
            oParam.iniciaisSecundarias = aIniciaisSec;
            oParam.cdas = sCdas.split(",");
            oParam.somenteOrigemFiltro = false;

            const cgm = document.getElementById("z01_numcgm").value;
            const matricula = document.getElementById("j01_matric").value;
            const inscricao = document.getElementById("q02_inscr").value;
            const agrupamento = document.getElementById("agrupamento").value;

            if (cgm) {
                oParam.origem = 1;
                if (agrupamento == "2") {
                    oParam.descricaoOrigem = "matricula";
                } else {
                    if (agrupamento == "3") {
                        oParam.descricaoOrigem = "inscrição";
                    }
                }
            } else {
                if (matricula) {
                    oParam.origem = 2;
                    oParam.descricaoOrigem = "matricula";
                } else {
                    if (inscricao) {
                        oParam.origem = 3;
                        oParam.descricaoOrigem = "inscrição";
                    }
                }
            }

            if (oParam.origem != 1 || agrupamento != "1") {
                new AjaxRequest("jur01_unificainicial.RPC.php", oParam, (oResponse) => {
                    if (oResponse.erro) {
                        alert(oResponse.mensagem);
                        return;
                    }

                    let iQtdMultiplasOrigens = 0;

                    Object.values(oResponse.origens).forEach((aOrigem) => {
                        iQtdMultiplasOrigens += (aOrigem.length > 1 ? 1 : 0);
                    });

                    if (iQtdMultiplasOrigens > 0) {
                        if (confirm(`Existem iniciais com mais de uma origem, deseja unificar somente as CDA's desta ${oParam.descricaoOrigem} ou de toda a inicial? \n\n OK para unificar somente as CDA's desta origem \n\n CANCELAR para unificar todas as CDA's'`)) {
                            oParam.somenteOrigemFiltro = true;
                        } else {
                            oParam.somenteOrigemFiltro = false;
                        }
                    }

                    js_processarUnificacao(oParam);
                }).execute();
            } else {
                js_processarUnificacao(oParam);
            }
        }

        function js_processarUnificacao(oParam) {
            oParam.executa = "unificaIniciais";

            new AjaxRequest("jur01_unificainicial.RPC.php", oParam, (oResponse) => {
                alert(oResponse.mensagem);

                if (oResponse.erro) {
                    return;
                }

                document.form1.reset();
                document.getElementById("dados").innerHTML = "";
            }).execute();
        }
    </script>
<?php
