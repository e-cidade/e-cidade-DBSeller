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

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("libs/db_utils.php");
require_once modification("libs/db_app.utils.php");
require_once modification("dbforms/db_funcoes.php");
require_once modification("classes/db_gerfcom_classe.php");

$rotulo = new rotulocampo;
$rotulo->label("r44_selec");
$rotulo->label("r44_descr");

$ano = DBPessoal::getAnoFolha();
$mes = DBPessoal::getMesFolha();

?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/classes/DBViewTipoFiltrosFolha.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/classes/DBViewFormularioFolha/CompetenciaFolha.js"></script>
    <link rel="stylesheet" href="estilos.css">
    <link rel="stylesheet" href="estilos/grid.style.css">
    <style type="text/css">
        td {
            padding: 5px;
        }
    </style>
</head>
<body>
<form name="form1" id="form1" class="container">
    <fieldset>
        <legend>Aviso de Férias</legend>
        <table class="form-container">
            <tr>
                <td>
                    <label>Competência:</label>
                </td>
                <td id="formularioCompetencia" width="500"></td>
            </tr>
            <tr>
                <td align="left" nowrap title="Digite o Ano / Mes de competência" >
                    <label for="periodovencido"><strong> Data de Pagamento:</strong></label>
                </td>
                <td>
                    <?php db_inputdata('dtPagamento',null, null, null, true, 'text', 1); ?>
                </td>
            </tr>
            <tr>
                <td align="left" nowrap title="Digite o Ano / Mes de competência" >
                    <label for="periodovencido"><strong> Data do Documento:</strong></label>
                </td>
                <td>
                    <?php db_inputdata('dtDocumento',null, null, null, true, 'text', 1); ?>
                </td>
            </tr>
            <tr>
                <td> <?php db_ancora("<b>Responsável:</b>", "js_pesquisarh01_regist(true);",0); ?></td>
                <td> <?php db_input('rh01_regist',7,0,true,'text',0, "onchange='js_pesquisarh01_regist(false);'");?> <?php db_input('nome',25,0,true,'text',3);?></td>
            </tr>
            <tr>
                <td>
                    <label for="tipoordem">
                        <strong>Tipo de Ordem: </strong>
                    </label>
                </td>
                <td align="left">
                    <?php
                    $tipo_ordem = array("numerica"=>"Numérica",
                        "alfabetica"=>"Alfabética");
                    db_select('ordem',$tipo_ordem ,true,1);
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="2" id="containnerTipoFiltrosFolha"></td>
            </tr>

        </table>
    </fieldset>
    <input type="button" id="emitir" onclick="js_emite();" value="Emitir">
</form>

<?php
db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"),
    db_getsession("DB_instit"));
?>
</body>
</html>
<script>
    var filtrosFolha = new DBViewFormularioFolha.DBViewTipoFiltrosFolha(<?php echo db_getsession("DB_instit")?>);
    filtrosFolha.aTipos = [0, 2, 3];
    filtrosFolha.sInstancia = 'filtrosFolha';
    filtrosFolha.show($('containnerTipoFiltrosFolha'));


    function js_pesquisarh01_regist(mostra){
        if (mostra==true) {
            js_OpenJanelaIframe('CurrentWindow.corpo',
                'db_iframe_rhpessoal',
                'func_rhpessoal.php?filtro_lotacao=true&funcao_js=parent.js_mostrapessoal1|rh01_regist|z01_nome&instit=<?=(db_getsession("DB_instit")) ?>','Pesquisa',true);
        } else {
            if (document.form1.rh01_regist.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_rhpessoal',
                    'func_rhpessoal.php?filtro_lotacao=true&pesquisa_chave='+document.form1.rh01_regist.value+'&funcao_js=parent.js_mostrapessoal&instit=<?=(db_getsession("DB_instit"))?>','Pesquisa',false);
            } else{
                document.form1.z01_nome.value = '';
            }
        }
    }

    function js_emite(){

        var tipoFiltro = $F('oCboTipoFiltro');
        var tipoRelatorio = $F('oCboTipoRelatorio');
        var query = {};
        query.ordem = document.form1.ordem.value;
        query.iTipoRelatorio = $F('oCboTipoRelatorio');
        query.iTipoFiltro = $F('oCboTipoFiltro');
        query.dtDocumento = document.form1.dtDocumento.value;
        query.dtPagamento = document.form1.dtPagamento.value;
        query.responsavel = document.form1.rh01_regist.value;
        query.ano = document.form1.ano.value;
        query.mes = document.form1.mes.value;

        /**
         * Verifica se o tipo escolhido foi intervalo
         */
        if (tipoFiltro == 1) {
            query.iIntervaloInicial = $F('InputIntervaloInicial');
            query.iIntervaloFinal = $F('InputIntervaloFinal');
        }

        /**
         * Verifica se o tipo escolhido foi seleção
         */
        if (tipoFiltro == 2) {
            var selecionados = [];
            var tipoFiltros = filtrosFolha.getLancadorAtivo().getRegistros();

            /**
             * Percorre os itens selecionados no lançador
             */
            tipoFiltros.each(function (oFiltro, iIndice) {
                selecionados[iIndice] = oFiltro.sCodigo;
            });

            query.iRegistros = selecionados;
        }

        if (tipoRelatorio != 0 && tipoFiltro == 2) {

            var lancadorSelecionado = filtrosFolha.getLancadorAtivo().getRegistros();
            if (lancadorSelecionado.length === 0) {
                alert('Por Favor, realize pelo menos o lançamento de 1 registro.');
                return false
            }
        }

        if (tipoRelatorio != 0 && tipoFiltro == 1) {

            if ($F('InputIntervaloInicial') == '' || $F('InputIntervaloFinal') == '') {
                alert('Por favor, informe o intervalo para geração do relatório.');
                return false;
            }
        }

        if (query.dtPagamento == "") {
            alert('Informe a data de pagamento.');
            return false;
        }

        if (query.ano == "") {
            alert('Informe o ano da Competência.');
            return false;
        }

        if (query.mes == "") {
            alert('Informe o mês da Competência.');
            return false;
        }

        jan = window.open('pes2_feriasaviso002.php?json='+ Object.toJSON(query) ,'','scrollbars=1,location=0 ');
        jan.moveTo(0,0);
    }

    function js_mostrapessoal(chave,erro){
        document.form1.nome.value = chave;
        if(erro==true){
            document.form1.rh01_regist.focus();
            document.form1.rh01_regist.value = '';
            document.form1.nome.value = '';
        }
    }
    function js_mostrapessoal1(chave1,chave2){
        document.form1.rh01_regist.value = chave1;
        document.form1.nome.value   = chave2;
        db_iframe_rhpessoal.hide();
    }

    var oCompetenciaFolha = new DBViewFormularioFolha.CompetenciaFolha(false);
    oCompetenciaFolha.renderizaFormulario($('formularioCompetencia'));

</script>