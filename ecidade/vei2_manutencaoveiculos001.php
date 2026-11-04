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

require_once (modification("libs/db_stdlib.php"));
require_once (modification("libs/db_conecta.php"));
require_once (modification("libs/db_sessoes.php"));
require_once (modification("libs/db_usuariosonline.php"));
require_once (modification("dbforms/db_funcoes.php"));

$clrotulo = new rotulocampo;
$clrotulo->label('DBtxt21');
$clrotulo->label('DBtxt22');
?>
<html>

<head>

    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">

    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLancador.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/Input/DBInputDate.widget.js"></script>

    <link href="estilos.css" rel="stylesheet" type="text/css">

</head>

<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="#cccccc">

    <div class="container">

        <form name="frmManutencao" method="post" action="">
            <table>
                <tr>
                    <td align="center" colspan="3">
                        <fieldset style="width: 600px;">

                            <legend>Manutenção de Veículos</legend>

                            <table style="margin-top: 10px; width: 100%;">

                                <tr>
                                    <td>
                                        <label class="bold" id="lbl_data_inicial" for="DBtxt21">Data Inicial:</label>
                                    </td>
                                    <td>
                                        <?php
                                        $DBtxt21_ano = db_getsession("DB_anousu");
                                        $DBtxt21_mes = '01';
                                        $DBtxt21_dia = '01';
                                        db_inputdata('DBtxt21', $DBtxt21_dia, $DBtxt21_mes, $DBtxt21_ano, true, 'text', 4);
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="bold" id="lbl_data_final" for="DBtxt22">Data Final:</label>
                                    </td>
                                    <td>
                                        <?php
                                        $DBtxt22_ano = db_getsession("DB_anousu");
                                        $DBtxt22_mes = date("m", db_getsession("DB_datausu"));
                                        $DBtxt22_dia = date("d", db_getsession("DB_datausu"));
                                        db_inputdata('DBtxt22', $DBtxt22_dia, $DBtxt22_mes, $DBtxt22_ano, true, 'text', 4);
                                        ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <label class="bold" id="lbl_situacao" for="situacao">Situação:</label>
                                    </td>
                                    <td colspan="3">
                                        <?php
                                        $aOpcoesSituacao = array(
                                            '' => 'Todos',
                                            VeiculoManutencao::SITUACAO_PENDENTE => 'Pendente',
                                            VeiculoManutencao::SITUACAO_REALIZADO => 'Realizado',
                                        );
                                        db_select("situacao", $aOpcoesSituacao, true, 2, 'style="width: 20%;"');
                                        ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <label for="coddepto" id="ancoraDepartamento">
                                            Departamento:
                                        </label>
                                    </td>
                                    <td>
                                        <?php db_input('coddepto', 2, 'text', '', '', '3') ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="ve32_codigo" id="ancoraCategoria">
                                            Categoria:
                                        </label>
                                    </td>
                                    <td>
                                        <?php db_input('ve32_codigo', 2, 'text', '', '', '3') ?>
                                        <?php db_input('ve32_descr', 2, 'text', '', '', '3') ?>
                                    </td>
                                </tr>
                                <tr></tr>

                            </table>

                            <!-- Lancador combustíveis -->
                            <div id="lancadorCombustiveis"></div>

                            <!-- Lancador veículos-->
                            <div id="lancadorVeiculos"></div>

                        </fieldset>

                        <input style="margin-top: 20px;" name="emite" id="emite" type="button" value="Emitir"
                            onclick="js_emite();">

                    </td>
                </tr>

            </table>
        </form>

    </div> <!-- container -->

    <script>
        const URL_RELATORIO = "vei2_manutencaoveiculos002.php";
        var oLancadorVeiculos;
        var oLancadorCombustiveis;

        document.observe('dom:loaded', function () {

            oLancadorCombustiveis = new DBLancador('lancadorCombustiveis');
            oLancadorCombustiveis.setNomeInstancia('oLancadorCombustiveis');
            oLancadorCombustiveis.setLabelAncora('Combustível:');
            oLancadorCombustiveis.setTextoFieldset('Combustíveis');
            oLancadorCombustiveis.setParametrosPesquisa('func_veiccadcomb.php', ['0', '1']);
            oLancadorCombustiveis.setTituloJanela("Pesquisa de Combustíveis");
            oLancadorCombustiveis.setGridHeight(100);
            // oLancadorCombustiveis.show($('lancadorCombustiveis'));

            oLancadorVeiculos = new DBLancador('lancadorVeiculos');
            oLancadorVeiculos.setNomeInstancia('oLancadorVeiculos');
            oLancadorVeiculos.setLabelAncora('Veículo:');
            oLancadorVeiculos.setTextoFieldset('Veículos');
            oLancadorVeiculos.setParametrosPesquisa('func_veiculos.php', ['0', '1', '&instit=true']);
            oLancadorVeiculos.setTituloJanela("Pesquisa de Veículos");
            oLancadorVeiculos.setGridHeight(100);
            oLancadorVeiculos.show($('lancadorVeiculos'));

        });

        function js_emite() {

            var oForm = document.frmManutencao;

            var oDataInicial = new Date(oForm.DBtxt21_ano.value, oForm.DBtxt21_mes.value, oForm.DBtxt21_dia.value, 0, 0, 0);
            var oDataFinal = new Date(oForm.DBtxt22_ano.value, oForm.DBtxt22_mes.value, oForm.DBtxt22_dia.value, 0, 0, 0);

            var sPeriodoInicial = oForm.DBtxt21_ano.value + '-' + oForm.DBtxt21_mes.value + '-' + oForm.DBtxt21_dia.value;
            var sPeriodoFinal = oForm.DBtxt22_ano.value + '-' + oForm.DBtxt22_mes.value + '-' + oForm.DBtxt22_dia.value;

            var aVeiculos = oLancadorVeiculos.getRegistros(false);
            var aCombustiveis = oLancadorCombustiveis.getRegistros(false);

            var aCodigosVeiculos = [];
            var aCodigosCombustiveis = [];

            /**
             * Validações
             */
            if (empty(oForm.DBtxt21.value)) {

                alert('O campo Data Inicial deve ser informado.');
                return;
            }
            if (empty(oForm.DBtxt22.value)) {

                alert('O campo Data Final deve ser informado.');
                return;
            }
            if (oDataInicial.valueOf() > oDataFinal.valueOf()) {

                alert('Data Final deve ser maior ou igual a Data Inicial.');
                return false;
            }

            // if (aCombustiveis.length === 0) {

            //     alert('Deve ser informado ao menos um combustível.');
            //     return false;
            // }

            for (var iVeiculo = 0; iVeiculo < aVeiculos.length; iVeiculo++) {

                aCodigosVeiculos.push(aVeiculos[iVeiculo].sCodigo);
            }

            // for (var iCombustivel = 0; iCombustivel < aCombustiveis.length; iCombustivel++) {

            //     aCodigosCombustiveis.push(aCombustiveis[iCombustivel].sCodigo);
            // }

            var sQuery = '?';
            sQuery += 'situacao=' + oForm.situacao.value;
            sQuery += '&periodo_inicial=' + sPeriodoInicial;
            sQuery += '&periodo_final=' + sPeriodoFinal;
            sQuery += '&veiculos=' + aCodigosVeiculos.join(',');
            // sQuery += '&combustiveis=' + aCodigosCombustiveis.join(',');

            if (oForm.ve32_codigo.value) {
                sQuery += '&categoria=' + oForm.ve32_codigo.value;
            }

            if (oForm.coddepto.value) {
                sQuery += '&departamento=' + oForm.coddepto.value;
            }

            var iHeight = (screen.availHeight - 40);
            var iWidth = (screen.availWidth - 5);
            var sOpcoes = 'width=' + iWidth + ',height=' + iHeight + ',scrollbars=1,location=0';
            var oJanela = window.open(URL_RELATORIO + sQuery, '', sOpcoes);

            oJanela.moveTo(0, 0);
        }

        const ancoraDepartamento = document.getElementById('ancoraDepartamento');
        const inputDepartamentoId = document.getElementById('coddepto');
        const departamentoLookup = new DBLookUp(ancoraDepartamento, inputDepartamentoId, inputDepartamentoId, {
            'arquivo': 'func_db_depart.php',
            'label': 'Pesquisa de Departamentos',
            'objetoLookUp': 'db_iframe_depart',
            // 'camposAdicionais': ['pn03_numero', 'pn03_ano', 'pn03_liclicita']
        });

        const ancoraCategoria = document.getElementById('ancoraCategoria');
        const inputCategoriaId = document.getElementById('ve32_codigo');
        const inputCategoriaDescricao = document.getElementById('ve32_descr');
        const categoriaLookup = new DBLookUp(ancoraCategoria, inputCategoriaId, inputCategoriaDescricao, {
            'arquivo': 'func_veiccadcateg.php',
            'label': 'Pesquisa de Categorias',
            'objetoLookUp': 'db_iframe_categoria',
            'camposAdicionais': ['ve32_descr'],
        });
    </script>

    <?php db_menu() ?>
</body>

</html>
