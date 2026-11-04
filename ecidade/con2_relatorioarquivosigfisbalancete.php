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

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_utils.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

$instituicao = new Instituicao(db_getsession("DB_instit"));

$periodo = array(
    "0"  => " 0 - Saldos de abertura",
    "1"  => " 1 - Janeiro          ",
    "2"  => " 2 - Fevereiro (1 Bim)",
    "3"  => " 3 - Março            ",
    "4"  => " 4 - Abril     (2 Bim)",
    "5"  => " 5 - Maio             ",
    "6"  => " 6 - Junho     (3 Bim)",
    "7"  => " 7 - Julho            ",
    "8"  => " 8 - Agosto    (4 Bim)",
    "9"  => " 9 - Setembro         ",
    "10" => "10 - Outubro   (5 Bim)",
    "11" => "11 - Novembro         ",
    "12" => "12 - Dezembro  (6 Bim)",
    "13" => "13 - Inscrição de RP  "
);

?>

<html>

<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBToogle.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<style>
    legend {
        font-weight: bold;
    }

    #sigfis {
        width: 850px;
    }

    #lista-gerados {
        width: 850px;
    }
</style>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="#cccccc">
    <div style="margin-top: 40px;" class="container">
        <fieldset id='sigfis'>
            <legend>Selecione as opções</legend>
            <div>
                <div style="display: block; height:68px;">
                    <table>
                        <tr>
                            <td><span style="font-weight: bold;">Arquivos do :</span></td>
                            <td>
                                <?php
                                db_select("periodosigfis", $periodo, true, 2);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td><span style="font-weight: bold;">Utilizar Plano de Contas :</span></td>
                            <td>
                            <?php
                                db_select(
                                    "utilizaPlanoUniao",
                                    ['f'=>'Estadual / Regional', 't'=>'União / Federação'],
                                    true,
                                    2
                                );
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td><span style="font-weight: bold;">Gerar consolidado por UG :</span></td>
                            <td>
                            <?php
                                db_select("consolidado", ['t'=>'SIM', 'f'=>'NÃO'], true, 2);
                            ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <fieldset id='field-informes' class="separator">
                    <legend>Informes mensais</legend>
                    <table>
                        <tr>
                            <td>
                                <input type="checkbox" id='Balancete' value='Balancete' name="Balancete" />
                            </td>
                            <td>
                                <label for="Balancete">Balancete</label>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </div>
        </fieldset>

        <div style="margin-top: 15px;margin-bottom:10px">
<!--            <input type="button" id='selecionar-todos' value='Selecionar Todos' name='Selecionar Todos' onclick="js_marcaTodos();" />-->
<!--            <input type="button" id='limpar-selecao' value='Limpar Seleção' name='Limpar Seleção' onclick="js_desmarcar();" />-->
            <input type="button" id='processar' value='Processar' name='Processar' onclick="js_processar();" />
        </div>

        <div>
            <fieldset id='lista-gerados'>
                <legend>Arquivos Gerados</legend>
                <div style='overflow:auto; text-align: left;' id='retorno'></div>
            </fieldset>
        </div>
    </div>
</body>

</html>
<?php db_menu(
    db_getsession("DB_id_usuario"),
    db_getsession("DB_modulo"),
    db_getsession("DB_anousu"),
    db_getsession("DB_instit")
); ?>

<script type="text/javascript">
    var sURL = "con4_processarsigfis.RPC.php";

    var informesMensais = new DBToogle($('field-informes'), true);
    var orcamento = new DBToogle($('field-orcamento'), true);
    var ppa = new DBToogle($('field-ppa'), true);

    function js_processar() {

        var oParam = new Object();
        oParam.exec = "processarNovoSigfis";
        oParam.iPeriodo = $F('periodosigfis');
        oParam.gerarConsolidado = ($F('consolidado')=='t'?true:false);
        oParam.utilizaPlanoUniao = $F('utilizaPlanoUniao');
        oParam.sCodigoTribunal = encodeURIComponent('<?php echo $instituicao->getCodigoTribunal() ?>');
        oParam.relatorio = true;

        oParam.aArquivos = new Array();
        var aArquivos = $$("input[type='checkbox']");
        aArquivos.each(function(oCheckbox, id) {

            with(oCheckbox) {

                if (checked) {
                    oParam.aArquivos.push(oCheckbox.name);
                }
            }

        });

        if (oParam.aArquivos.length == 0) {

            alert("Selecione ao menos uma Opção.");
            return false;
        }

        for (let i = 0; i < oParam.aArquivos.length; i++) {

            if ($F('periodosigfis') == '0' &&
                ( oParam.aArquivos[i] == 'RPInscricao' ||
                  oParam.aArquivos[i] == 'ContaBancaria' ||
                  oParam.aArquivos[i] == 'ConciliacaoBancaria'
                )) {
                continue;
            } else if ($F('periodosigfis') == '0') {
                let msg = "Para a competência 0, serão exigidos apenas três arquivos: ";
                msg += "inscrição de restos a pagar, Conta Bancária e Conciliação Bancária";
                alert(msg);
                return false;
            }

            if ($F('periodosigfis') == '13' && oParam.aArquivos[i] != 'RPInscricao') {
                let msg = "Para a competência 13, só será exigido o arquivo de ";
                msg += "Inscrição de restos a pagar.";
                alert(msg);
                return false;
            }
        }

        js_divCarregando('Aguarde, Processando Arquivos', 'msgBox');

        var oAjax = new Ajax.Request(sURL, {
            method: 'post',
            parameters: 'json=' + Object.toJSON(oParam),
            onComplete: js_retornoProcessaSigfis
        });
    }

    function js_retornoProcessaSigfis(oAjax) {

        js_removeObj('msgBox');

        var oRetorno = JSON.parse(oAjax.responseText);
        if (oRetorno.status == 1) {

            if (oRetorno.arquivosNaoProcessados.length) {
                const arquivos = oRetorno.arquivosNaoProcessados.join("\n");
                const msg = `Alguns arquivos não foram processados. Verificar o log:\n ${arquivos}`;
                alert(msg);
            }

            var sRetorno = "";
            for (var i = 0; i < oRetorno.lista.length; i++) {

                with(oRetorno.lista[i]) {

                    sRetorno += "<a  download href='db_download.php?arquivo=" + path + "'>" + name + "</a><br>";
                }
            }

            $('retorno').innerHTML = sRetorno;
        } else {

            $('retorno').innerHTML = '';
            alert(oRetorno.message.urlDecode());
            return false;
        }
    }

    function js_marcaTodos() {

        var aCheckboxes = $$('input[type=checkbox]');
        aCheckboxes.each(function(oCheckbox) {
            oCheckbox.checked = true;
        });
    }

    function js_desmarcar() {

        var aCheckboxes = $$('input[type=checkbox]');
        aCheckboxes.each(function(oCheckbox) {
            oCheckbox.checked = false;
        });
    }
</script>
