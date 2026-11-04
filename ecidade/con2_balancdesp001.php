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
require_once(modification("libs/db_liborcamento.php"));
require_once(modification("classes/db_orctiporec_classe.php"));
$clrotulo = new rotulocampo;
$clrotulo->label('DBtxt21');
$clrotulo->label('DBtxt22');

$display = "display: none";
$displayRecursoAntigo = "";

if (FONTE_RECURSO_UNIAO) {
    $display = "";
    $displayRecursoAntigo = "display: none";
}
?>

<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <script type="text/javascript" src="scripts/classes/DBViewFiltroRecursos.classe.js"></script>

    <script>

        function js_abre(opcao) {
            sel_instit = new Number(document.form1.db_selinstit.value);
            if (sel_instit == 0) {
                alert('Você não escolheu nenhuma Instituição. Verifique!');
                return false;
            }

            if (document.form1.vernivel.value != '' && document.form1.vernivel.value != document.form1.nivel.value) {
                if (confirm('Você já escolheu anteriormente dados do nível ' + document.form1.vernivel.value + ' , deseja altera-los?') == false)
                    return false
                else
                    js_OpenJanelaIframe('', 'db_iframe_orgao', 'func_selorcdotacao.php?nivel=' + document.form1.nivel.value + '&db_selinstit=' + document.form1.db_selinstit.value, 'pesquisa', true);
            } else if ((window.CurrentWindow || parent.CurrentWindow).corpo.db_iframe_orgao != undefined) {
//   alert('entrou');

                if (document.form1.nivel.value == document.form1.vernivel.value) {
                    db_iframe_orgao.show();
                } else {
                    js_OpenJanelaIframe('', 'db_iframe_orgao', 'func_selorcdotacao.php?&nivel=' + document.form1.nivel.value + '&db_selinstit=' + document.form1.db_selinstit.value, 'pesquisa', true);
                }
            } else {
                js_OpenJanelaIframe('', 'db_iframe_orgao', 'func_selorcdotacao.php?&nivel=' + document.form1.nivel.value + '&db_selinstit=' + document.form1.db_selinstit.value, 'pesquisa', true);
            }
        }

        variavel = 1;

        function js_emite(opcao, origem) {
            sel_instit = new Number(document.form1.db_selinstit.value);
            if (sel_instit == 0) {
                alert('Você não escolheu nenhuma Instituição. Verifique!');
                return false;
            }
            if (opcao == 3) {
                var data1 = new Date(document.form1.DBtxt21_ano.value, document.form1.DBtxt21_mes.value, document.form1.DBtxt21_dia.value, 0, 0, 0);
                var data2 = new Date(document.form1.DBtxt22_ano.value, document.form1.DBtxt22_mes.value, document.form1.DBtxt22_dia.value, 0, 0, 0);
                if (data1.valueOf() > data2.valueOf()) {
                    alert('Data inicial maior que data final. Verifique!');
                    return false;
                }
                perini = document.form1.DBtxt21_ano.value + '-' + document.form1.DBtxt21_mes.value + '-' + document.form1.DBtxt21_dia.value;
                perfin = document.form1.DBtxt22_ano.value + '-' + document.form1.DBtxt22_mes.value + '-' + document.form1.DBtxt22_dia.value;
                ;
            } else if (opcao == 2) {
                if (document.form1.mesfin.value == 0) {
                    mesfinal = 12;
                } else if (document.form1.mesfin.value < 10) {
                    mesfinal = '0' + document.form1.mesfin.value;
                } else if (document.form1.mesfin.value == 'mes') {
                    alert('Mês final do intervalo invalido.Verifique!');
                    return false
                } else {
                    mesfinal = document.form1.mesfin.value;
                }

                if (document.form1.mesini.value == 0) {
                    mesinicial = 12;
                } else if (document.form1.mesini.value < 10) {
                    mesinicial = '0' + document.form1.mesini.value;
                } else {
                    mesinicial = document.form1.mesini.value;
                }

                perini = <?=db_getsession("DB_anousu")?> + '-' + mesinicial + '-01';
                perfin = <?=db_getsession("DB_anousu")?> + '-' + mesfinal + '-01';
                opcao = 4;
            } else {
                perini = <?=db_getsession("DB_anousu")?> + '-01-01';
                perfin = <?=db_getsession("DB_anousu")?> + '-01-01';
            }

            var recursos_selecionados = "";
            if (filtroRecursos !== false) {
                recursos_selecionados = filtroRecursos.getListaRecursos();
            }


            let apresentacaoRecurso = document.getElementById('apresentarRecurso').value;
            let url = `con2_balancdesp002.php?recursos_selecionados=${recursos_selecionados}`
            url += `&vernivel=${document.form1.vernivel.value}&orgaos=${document.form1.orgaos.value}`;
            url +=`&totaliza=${document.form1.totaliza.value}&opcao=${opcao}&recurso=${document.form1.recurso.value}`;
            url +=`&origem=${origem}&db_selinstit=${document.form1.db_selinstit.value}&perfin=${perfin}&perini=${perini}`;
            url +=`&recursodescr=${document.form1.recursodescr.value}&totaliza_atividade=${document.form1.totaliza_atividade.value}`;
            url +=`&apresentacaoRecurso=${apresentacaoRecurso}`;
            jan = window.open(url, '', 'scrollbars=1,location=0');
            jan.moveTo(0, 0);
        }

    </script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body>
<form name="form1" method="post" action="orc2_balancdesp002.php">
    <div class="container">
        <?php
        db_selinstit('parent.js_limpa', 300, 100);
        ?>
        <table class="form-container">


            <tr>
                <td align="right"><strong>Órgão/Unidade :</strong></td>
                <td align="left">
                    <?php
                    $xy = array('1A' => 'Órgão', '2A' => 'Unidade');
                    db_select('nivel', $xy, true, 2, "");
                    ?>
                    <input name="seleciona" id="seleciona" type="button" value="Seleciona"
                           onclick="js_abre();">
                </td>
                </td>
            </tr>
            <tr>
                <td align="right"><strong>Totalização : </strong></td>
                <td align="left">
                    <?php
                    $x = array('A' => 'ANALÍTICO', 'S' => 'SINTÉTICO');
                    db_select('totaliza', $x, true, 2, "");
                    ?>
                </td>
            </tr>
            <tr>
                <td align="right"><strong>Totaliza Atividade : </strong></td>
                <td align="left">
                    <?php
                    $x = array('N' => 'Não', 'S' => 'SIM');
                    db_select('totaliza_atividade', $x, true, 2, "");
                    ?>
                </td>
            </tr>
            <tr style="<?php echo $displayRecursoAntigo; ?>">
                <td align="right"><strong>Recurso:</strong></td>
                <td>
                    <?php
                    $dbwhere = " o15_datalimite is null or o15_datalimite > '" . date('Y-m-d', db_getsession('DB_datausu')) . "'";
                    $clorctiporec = new cl_orctiporec;
                    $res = $clorctiporec->sql_record($clorctiporec->sql_query(null, "*", "o15_codigo", $dbwhere));
                    db_selectrecord("recurso", $res, true, 2, "", "", "", "0");
                    ?>
                </td>
            </tr>

            <tr style="<?php echo $display; ?>">
                <td colspan="1" align="right"><strong>Fonte de Recursos:&nbsp;</strong></td>
                <td colspan=2>
                    <input type="button" name="gerar" id="gerar" value="Selecionar Recursos" onclick="abrirJanela()"/>
                </td>
            </tr>
            <tr>
                <td>Apresentar :</td>
                <td>
                    <select id="apresentarRecurso" name="apresentarRecurso">
                        <option selected value="fonteRecurso">Fonte Recurso</option>
                        <option value="depara">Depara Subrecurso</option>
                        <option value="siconfi">Código Siconfi</option>
                    </select>
                </td>
            </tr>

        </table>
    </div>


    <div class="container">
        <table
        <?php
        db_selorcbalanco();
        ?>
        </div>
        <input name="orgaos" id="orgaos" type="hidden" value="">
        <input name="vernivel" id="vernivel" type="hidden" value="">

    </div>
</form>

<?php
db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit"));
?>
</body>
<script>
    var filtroRecursos = false;

    function abrirJanela() {
        if (!filtroRecursos) {
            filtroRecursos = new DBViewFiltroRecursos();
            filtroRecursos.construirJanela();
        }
        filtroRecursos.show();
    }
</script>
</html>
