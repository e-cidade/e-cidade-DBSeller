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

$clrotulo = new rotulocampo;
$clrotulo->label('DBtxt21');
$clrotulo->label('DBtxt22');

$display = "display: none";
if (FONTE_RECURSO_UNIAO) {
    $display = "";
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

        variavel = 1;

        function js_emite() {
            // pega dados da func_selorcdotacao_aba.php

            if (filtroRecursos !== false) {
                $('recursos_selecionados').value = filtroRecursos.getListaRecursos();
            }

            document.form1.filtra_despesa.value = parent.iframe_filtro.js_atualiza_variavel_retorno();
            jan = window.open('', 'safo' + variavel, 'width=' + (screen.availWidth - 5) + ',height=' + (screen.availHeight - 40) + ',scrollbars=1,location=0 ');
            document.form1.target = 'safo' + variavel++;
            setTimeout("document.form1.submit()", 1000);
            return true;
        }
    </script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="subcontainer">
    <form name="form1" method="post" action="orc2_reldespesas002.php">
        <fieldset>
            <legend>Saldo de Verbas da Despesa</legend>
            <table class="form-container">
                <tr>
                    <td><strong>Nível :</strong></td>
                    <td>
                        <?php
                        $xy = [
                            '1A' => 'Órgão Até o Nível',
                            '1B' => 'Órgão só o Nível',
                            '2A' => 'Unidade Até o Nível',
                            '2B' => 'Unidade só o Nível',
                            '3A' => 'Função Até o Nível',
                            '3B' => 'Função só o Nível',
                            '4A' => 'Subfunção Até o Nível',
                            '4B' => 'Subfunção só o Nível',
                            '5A' => 'Programa Até o Nível',
                            '5B' => 'Programa só o Nível',
                            '6A' => 'Proj/Ativ Até o Nível',
                            '6B' => 'Proj/Ativ só o Nível',
                            '7A' => 'Elemento Até o Nível',
                            '7B' => 'Elemento só o Nível',
                            '8A' => 'Recurso Até o Nível',
                            '9A' => 'Recurso Até o Nível - Completo',
                            '8B' => 'Recurso só o Nível'
                        ];
                        db_select('nivel', $xy, true, 2, "");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" align="center">
                        <fieldset style="width: 98%; border-left: none; border-bottom: none; border-right: none;">
                            <legend><b>Instituições</b></legend>
                            <?php
                                db_selinstit('', 500, 130);
                            ?>
                        </fieldset>
                    </td>
                </tr>
                <tr id="linhaCodigoSiconfi" style="display: none">
                    <td>Apresentar :</td>
                    <td>
                        <select id="apresentarRecurso" name="apresentarRecurso">
                            <option selected value="fonteRecurso">Fonte Recurso</option>
                            <option value="depara">Depara Subrecurso</option>
                            <option value="Siconfi">Código Siconfi</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><strong>Troca de Página por Órgão:</strong>
                    </td>
                    <td>
                        <?php
                        $x = array('N' => 'NÃO', 'S' => 'SIM');
                        db_select('quebra_orgao', $x, true, 2, "");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><strong>Troca de Página por Unidade:</strong>
                    </td>
                    <td>
                        <?php
                        $xx = array('N' => 'NÃO', 'S' => 'SIM');
                        db_select('quebra_unidade', $xx, true, 2, "");
                        ?>
                    </td>
                </tr>
                <tr>
                    <?php
                    $sql = "select o50_subelem from orcparametro where o50_anousu = " . db_getsession("DB_anousu");
                    $result1 = db_query($sql);
                    $o50_subelem = pg_result($result1, 0, 0);
                    if ($o50_subelem == 'f') {
                        ?>

                        <td><strong>Listar Sub-elementos:</strong>
                        </td>
                        <td>
                            <?php
                            $xx = array('N' => 'NÃO', 'S' => 'SIM');
                            db_select('lista_subeleme', $xx, true, 2, "");
                            ?>
                        </td>
                        <?php
                    } else {
                        ?>
                        <td>
                        </td>
                        <td>
                            <?php
                            global $lista_subeleme;
                            $lista_subeleme = 'N';
                            db_input("lista_subeleme", 15, 0, true, 'hidden', 3);
                            ?>
                        </td>
                        <?php
                    }

                    /*
                     *  configura as datas default
                     */
                    $anousu = db_getsession("DB_anousu");
                    $dataini = date("m-d", db_getsession("DB_datausu"));
                    $datafin = date("m-d", db_getsession("DB_datausu"));
                    $dataini = $anousu . "-" . $dataini;
                    $datafin = $anousu . "-" . $datafin;

                    $dt = explode('-', $dataini);
                    $data_ini_dia = $dt[2];
                    $data_ini_mes = $dt[1];
                    $data_ini_ano = $dt[0];
                    $dt = explode('-', $datafin);
                    $data_fin_dia = $dt[2];
                    $data_fin_mes = $dt[1];
                    $data_fin_ano = $dt[0];

                    ?>
                </tr>
                <tr>
                    <td nowrap><b> Período inicial: </b></td>
                    <td colspan="2">
                        <?php db_inputdata('data_ini', @$data_ini_dia, @$data_ini_mes, @$data_ini_ano, true, 'text', 1); ?>
                    </td>
                </tr>

                <tr>
                    <td nowrap><b> Período final: </b></td>
                    <td colspan="2">
                        <?php db_inputdata('data_fin', @$data_fin_dia, @$data_fin_mes, @$data_fin_ano, true, 'text', 1); ?>
                    </td>
                </tr>
            </table>
        </fieldset>
        <input name="emite2" id="emite2" type="button" value="Processar" onclick="js_emite();">
        <input name="orgaos" id="orgaos" type="hidden" value="">
        <input name="vernivel" id="vernivel" type="hidden" value="">
        <input name="recursos_selecionados" id="recursos_selecionados" type="hidden" value="">
        <input name="filtra_despesa" id="filtra_despesa" type="hidden" value="">
    </form>
</div>
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

    const linhaCodigoSiconfi = document.getElementById('linhaCodigoSiconfi');
    document.getElementById('nivel').addEventListener('change', (e) => {
        linhaCodigoSiconfi.style.display = 'none';

        if (['8A', '9A', '8B'].includes(e.target.value)) {
            linhaCodigoSiconfi.style.display = 'table-row';
        }
    })
</script>
</html>
