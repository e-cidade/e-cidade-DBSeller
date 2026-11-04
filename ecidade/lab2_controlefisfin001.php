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

require_once(modification('libs/db_stdlib.php'));
require_once(modification('libs/db_conecta.php'));
require_once(modification('libs/db_sessoes.php'));
require_once(modification('libs/db_usuariosonline.php'));
require_once(modification('libs/db_utils.php'));
require_once(modification('dbforms/db_funcoes.php'));

$queryString = [];
parse_str($_SERVER['QUERY_STRING'], $queryString);
db_postmemory($queryString);
db_postmemory($_POST);

$oDaoLabControleFisicoFinanceiro = new cl_lab_controlefisicofinanceiro;
$oDaoLabControleFisicoFinanceiro->rotulo->label();
$oRotulo = new rotulocampo;
$oRotulo->label("la02_i_codigo");
$oRotulo->label("sd62_c_nome");
$oRotulo->label("descrdepto");
$oRotulo->label("la08_i_codigo");
$oRotulo->label("sd60_c_nome");
$oRotulo->label("sd61_c_nome");
$oRotulo->label('la08_c_descr');
$oRotulo->label('sd60_c_grupo');
$oRotulo->label('sd60_c_nome');
$oRotulo->label('sd61_c_subgrupo');
$oRotulo->label('sd61_c_nome');
$oRotulo->label('sd62_c_formaorganizacao');
$oRotulo->label('sd62_c_nome');

$db_opcao = 1;
$db_botao = false;
?>
<!DOCTYPE html>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link type="text/css" href="estilos.css" rel="stylesheet">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>

</head>
<body>
<div class="container" style="width: 500px; height: 430px;">
    <fieldset>
        <legend><b>Controle Físico / Financeiro</b></legend>
        <div class="subcontainer" style="width: 100%; text-align: left;">
            <table class="form-container">
                <tr>
                    <td>Tipo de Controle:</td>
                    <td>
                        <?php
                        $aX = [
                            '' => 'Selecione...',
                            '1' => 'DEPARTAMENTO SOLICITANTE',
                            '2' => 'LABORATÓRIO',
                            '3' => 'GRUPO DE EXAME',
                            '4' => 'EXAME'
                        ];
                        db_select('select-controle', $aX, true, 1, ' onchange="listenerSelectControle();" ');
                        ?>
                    </td>
                </tr>
            </table>
        </div>
        <div class="subcontainer" id="ctn-form" style="width: 100%; display: none;">
            <fieldset class="separator">
                <legend>Filtros</legend>
                <table class="form-container">
                    <tr>
                        <td>
                            <b>Periodo:</b>
                        </td>
                        <td nowrap>
                            <?php
                            db_inputdata('dDataIni', @$iDia1, @$iMes1, @$iAno1, true, 'text', 1, "");
                            ?>
                            A
                            <?php
                            db_inputdata('dDataFim', @$iDia2, @$iMes2, @$iAno2, true, 'text', 1, "");
                            ?>
                        </td>
                    </tr>
                    <tr id="tr-controle-departamento">
                        <td><b>Departamento:</b></td>
                        <td nowrap>
                            <?php
                            $oDaoDbDepart = new cl_db_depart;
                            $sSql = $oDaoDbDepart->sql_query_file(null, 'coddepto, descrdepto', 'coddepto');
                            $rs = $oDaoDbDepart->sql_record($sSql);
                            $aX = array();
                            $aX[-1] = 'TODOS';
                            for ($iCont = 0; $iCont < $oDaoDbDepart->numrows; $iCont++) {
                                $oDados = db_utils::fieldsmemory($rs, $iCont);
                                $aX[$oDados->coddepto] = $oDados->coddepto . ' - ' . $oDados->descrdepto;
                            }
                            db_select('select-departamento', $aX, true, 1);
                            ?>
                            <script>
                                $('select-departamento').selectedIndex = 0;
                            </script>
                        </td>
                    </tr>
                    <tr id="tr-controle-laboratorio">
                        <td><b>Laboratório:</b></td>
                        <td nowrap>
                            <?php
                            $oDaoLabLaboratorio = new cl_lab_laboratorio;
                            $sSql = $oDaoLabLaboratorio->sql_query_file(null, 'la02_i_codigo, la02_c_descr');
                            $rs = $oDaoLabLaboratorio->sql_record($sSql);
                            $aX = array();
                            for ($iCont = 0; $iCont < $oDaoLabLaboratorio->numrows; $iCont++) {
                                $oDados = db_utils::fieldsmemory($rs, $iCont);
                                $aX[$oDados->la02_i_codigo] = $oDados->la02_c_descr;
                            }
                            $aX[-1] = 'TODOS';
                            db_select('select-laboratorio', $aX, true, 1);
                            ?>
                            <script>
                                $('select-laboratorio').selectedIndex = 0;
                            </script>
                        </td>
                    </tr>
                    <tr id="tr-controle-grupo-1" name="tr-controle-grupo">
                        <td nowrap title="<?= @$Tla56_i_grupo ?>">
                            <?php
                            db_ancora(@$Lla56_i_grupo, "js_pesquisala56_i_grupo(true);", $db_opcao);
                            ?>
                        </td>
                        <td nowrap>
                            <?php
                            db_input('la56_i_grupo', 10, $Ila56_i_grupo, true, 'hidden', 3, '');
                            db_input(
                                'sd60_c_grupo',
                                2,
                                $Isd60_c_grupo,
                                true,
                                'text',
                                $db_opcao,
                                " onchange='js_pesquisala56_i_grupo(false);'"
                            );
                            db_input('sd60_c_nome', 50, $Isd60_c_nome, true, 'text', 3, '');
                            ?>
                        </td>
                    </tr>
                    <tr id="tr-controle-grupo-2" name="tr-controle-grupo">
                        <td nowrap title="<?= @$Tla56_i_subgrupo ?>">
                            <?php
                            db_ancora(@$Lla56_i_subgrupo, "js_pesquisala56_i_subgrupo(true);", $db_opcao);
                            ?>
                        </td>
                        <td nowrap>
                            <?php
                            db_input('la56_i_subgrupo', 10, $Ila56_i_subgrupo, true, 'hidden', 3, '');
                            db_input(
                                'sd61_c_subgrupo',
                                2,
                                $Isd61_c_subgrupo,
                                true,
                                'text',
                                $db_opcao,
                                " onchange='js_pesquisala56_i_subgrupo(false);'"
                            );
                            db_input('sd61_c_nome', 50, $Isd61_c_nome, true, 'text', 3, '');
                            ?>
                        </td>
                    </tr>
                    <tr id="tr-controle-grupo-3" name="tr-controle-grupo">
                        <td nowrap title="<?= @$Tla56_i_formaorganizacao ?>">
                            <?php
                            db_ancora(@$Lla56_i_formaorganizacao, "js_pesquisala56_i_formaorganizacao(true);", $db_opcao);
                            ?>
                        </td>
                        <td nowrap>
                            <?php
                            db_input('la56_i_formaorganizacao', 10, $Ila56_i_formaorganizacao, true, 'hidden', 3, '');
                            db_input(
                                'sd62_c_formaorganizacao',
                                2,
                                $Isd62_c_formaorganizacao,
                                true,
                                'text',
                                $db_opcao,
                                " onchange='js_pesquisala56_i_formaorganizacao(false);'"
                            );
                            db_input('sd62_c_nome', 50, $Isd62_c_nome, true, 'text', 3, '');
                            ?>
                        </td>
                    </tr>
                    <tr id="tr-controle-exame">
                        <td nowrap title="<?= @$Tla56_i_exame ?>">
                            <?php
                            db_ancora(@$Lla56_i_exame, "js_pesquisala56_i_exame(true);", $db_opcao);
                            ?>
                        </td>
                        <td nowrap>
                            <?php
                            db_input(
                                'id-exame',
                                10,
                                $Ila56_i_exame,
                                true,
                                'text',
                                $db_opcao,
                                " onchange='js_pesquisala56_i_exame(false);'"
                            );
                            db_input('la08_c_descr', 50, $Ila08_c_descr, true, 'text', 3, '');
                            ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <div class="subcontainer">
                <button onclick="js_gerar()">
                    <i class="fas fa-print"></i>
                    Imprimir
                </button>
            </div>
        </div>
    </fieldset>
</div>
<?php
db_menu();
?>
</body>
</html>
<script>
    let campoValor = '';
    let input = null;
    let quebraLabel = '';

    function listenerSelectControle() {
        const ctnForm = $('ctn-form');

        switch ($F('select-controle')) {
            case '1':
                campoValor = 'select-departamento';
                input = $('select-departamento');
                quebraLabel = 'Departamento';
                break;
            case '2':
                campoValor = 'select-laboratorio';
                input = $('select-laboratorio');
                quebraLabel = 'Laboratório';
                break;
            case '3':
                campoValor = '';
                input = null;
                quebraLabel = 'Grupo de Exame';
                break;
            case '4':
                campoValor = 'id-exame';
                input = $('id-exame');
                quebraLabel = 'Exame';
                break;
            default:
                input = null;
                quebraLabel = '';
                ctnForm.style.display = 'none';
                return;
        }

        ctnForm.style.display = null;

        $('tr-controle-departamento').hidden = $F('select-controle') !== '1';
        $('tr-controle-laboratorio').hidden = $F('select-controle') !== '2';

        for (const tr of document.querySelectorAll('tr[name="tr-controle-grupo"]')) {
            tr.hidden = $F('select-controle') !== '3'
        }

        $('tr-controle-exame').hidden = $F('select-controle') !== '4';
    }

    function js_gerar() {
        if (!js_validaDados()) {
            return false;
        }
        sQuery = 'dDataIni=' + $F('dDataIni');
        sQuery += '&dDataFim=' + $F('dDataFim');
        sQuery += `&sQuebraLabel=${quebraLabel}`;
        sQuery += `&iTpcontrole=${$F('select-controle')}`;
        if ($F('select-controle') !== '3') {
            let valor = input.value !== '' ? input.value : '-1';
            sQuery += `&iValor1=${valor}`;
        } else {
            if ($F('la56_i_grupo') === '') {
                sQuery += '&iValor1=-1';
            } else {
                sQuery += '&iValor1=' + $F('sd60_c_grupo');
                sQuery += '&iValor2=' + $F('sd61_c_subgrupo');
                sQuery += '&iValor3=' + $F('sd62_c_formaorganizacao');
            }
        }
        const jan = window.open(
            'lab2_controlefisfin002.php?' + sQuery,
            '',
            'width=' + (screen.availWidth - 5) + ',height=' + (screen.availHeight - 40) + ',scrollbars=1,location=0 '
        );
        jan.moveTo(0, 0);

    }

    function js_validaDados() {
        if ($F('select-controle') === '') {
            alert('Informe o tipo de controle.');
            return false;
        }
        if ($F('dDataFim') == '' || $F('dDataFim') == '') {
            alert('Entre com o periodo!');
            return false;
        }
        sData = $F('dDataIni');
        aData = sData.split('/');
        sDataini = aData.reverse().join('');
        sData = $F('dDataFim');
        aData = sData.split('/');
        sDatafim = aData.reverse().join('');
        if (parseInt(sDatafim, 10) < parseInt(sDataini, 10)) {
            alert('Final não pode ser menos que a inicial!');
            return false;
        }
        return true;
    }

    function js_pesquisala56_i_grupo(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_iframe_sau_grupo',
                'func_sau_grupo.php?funcao_js=' +
                'parent.js_mostrasau_grupo|sd60_i_codigo|sd60_c_nome|sd60_c_grupo',
                'Pesquisa',
                true
            );
        } else {
            if ($('sd60_c_grupo').value != '') {
                js_OpenJanelaIframe(
                    'CurrentWindow.corpo',
                    'db_iframe_sau_grupo',
                    'func_sau_grupo.php?chave_sd60_c_grupo=' +
                    $('sd60_c_grupo').value + '&funcao_js=parent.js_mostrasau_grupo|' +
                    'sd60_i_codigo|sd60_c_nome|sd60_c_grupo&nao_mostra=true',
                    'Pesquisa',
                    false
                );
            } else {
                js_limpaGrupo();
                js_limpaSubGrupo();
                js_limpaFormaOrg();
            }
        }
    }

    function js_mostrasau_grupo(chave1, chave2, chave3) {
        js_limpaSubGrupo();
        js_limpaFormaOrg();

        if (chave1 == '') {
            chave3 = '';
        }
        $('la56_i_grupo').value = chave1;
        $('sd60_c_nome').value = chave2;
        $('sd60_c_grupo').value = chave3;
        db_iframe_sau_grupo.hide();
    }

    function js_pesquisala56_i_subgrupo(mostra) {
        if ($F('sd60_c_grupo') == '' || $F('la56_i_grupo') == '') {
            alert('Selecione um grupo primeiro.');
            $('sd61_c_subgrupo').value = '';
            return false;
        }

        var sGet = '&chave_grupo=' + $F('sd60_c_grupo');

        if (mostra == true) {
            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_iframe_sau_subgrupo',
                'func_sau_subgrupo.php?' +
                'funcao_js=parent.js_mostrasau_subgrupo|sd61_i_codigo|sd61_c_nome|sd61_c_subgrupo' + sGet,
                'Pesquisa',
                true
            );
        } else {
            if ($('sd61_c_subgrupo').value != '') {
                js_OpenJanelaIframe(
                    'CurrentWindow.corpo',
                    'db_iframe_sau_subgrupo',
                    'func_sau_subgrupo.php?chave_sd61_c_subgrupo=' +
                    $('sd61_c_subgrupo').value + '&funcao_js=parent.js_mostrasau_subgrupo|' +
                    'sd61_i_codigo|sd61_c_nome|sd61_c_subgrupo&nao_mostra=true' + sGet,
                    'Pesquisa',
                    false
                );
            } else {
                js_limpaSubGrupo();
                js_limpaFormaOrg();
            }
        }
    }

    function js_mostrasau_subgrupo(chave1, chave2, chave3) {
        js_limpaFormaOrg();

        if (chave1 == '') {
            chave3 = '';
        }

        $('la56_i_subgrupo').value = chave1;
        $('sd61_c_nome').value = chave2;
        $('sd61_c_subgrupo').value = chave3;
        db_iframe_sau_subgrupo.hide();
    }

    function js_pesquisala56_i_formaorganizacao(mostra) {
        if ($F('sd60_c_grupo') == '' || $F('la56_i_grupo') == '') {
            alert('Selecione um grupo primeiro.');
            $('sd62_c_formaorganizacao').value = '';
            return false;
        }

        if ($F('sd61_c_subgrupo') == '' || $F('la56_i_subgrupo') == '') {
            alert('Selecione um subgrupo primeiro.');
            $('sd62_c_formaorganizacao').value = '';
            return false;
        }

        var sGet = '&chave_grupo=' + $F('sd60_c_grupo') + '&chave_subgrupo=' + $F('sd61_c_subgrupo');

        if (mostra == true) {
            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_iframe_sau_formaorganizacao',
                'func_sau_formaorganizacao.php?' +
                'funcao_js=parent.js_mostrasau_formaorganizacao|sd62_i_codigo|sd62_c_nome|' +
                'sd62_c_formaorganizacao' + sGet,
                'Pesquisa',
                true
            );

        } else {
            if ($('sd62_c_formaorganizacao').value != '') {
                js_OpenJanelaIframe(
                    'CurrentWindow.corpo',
                    'db_iframe_sau_formaorganizacao',
                    'func_sau_formaorganizacao.php?' +
                    'chave_sd62_c_formaorganizacao=' + $('sd62_c_formaorganizacao').value +
                    '&funcao_js=parent.js_mostrasau_formaorganizacao|sd62_i_codigo|sd62_c_nome|' +
                    'sd62_c_formaorganizacao&nao_mostra=true' + sGet,
                    'Pesquisa',
                    false
                );
            } else {
                js_limpaFormaOrg();
            }
        }
    }

    function js_mostrasau_formaorganizacao(chave1, chave2, chave3) {
        if (chave1 == '') {
            chave3 = '';
        }

        $('la56_i_formaorganizacao').value = chave1;
        $('sd62_c_nome').value = chave2;
        $('sd62_c_formaorganizacao').value = chave3;
        db_iframe_sau_formaorganizacao.hide();
    }

    function js_limpaGrupo() {
        $('la56_i_grupo').value = '';
        $('sd60_c_grupo').value = '';
        $('sd60_c_nome').value = '';
    }

    function js_limpaSubGrupo() {
        $('la56_i_subgrupo').value = '';
        $('sd61_c_subgrupo').value = '';
        $('sd61_c_nome').value = '';
    }

    function js_limpaFormaOrg() {
        $('la56_i_formaorganizacao').value = '';
        $('sd62_c_formaorganizacao').value = '';
        $('sd62_c_nome').value = '';
    }

    function js_pesquisala56_i_exame(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe(
                'CurrentWindow.corpo',
                'db_iframe_lab_exame',
                'func_lab_exame.php?funcao_js=parent.js_mostralab_exame1|la08_i_codigo|la08_c_descr',
                'Pesquisa',
                true
            );
        } else {
            if ($(campoValor).value != '') {
                js_OpenJanelaIframe(
                    'CurrentWindow.corpo',
                    'db_iframe_lab_exame',
                    'func_lab_exame.php?pesquisa_chave=' +
                    $(campoValor).value + '&funcao_js=parent.js_mostralab_exame',
                    'Pesquisa',
                    false
                );
            } else {
                $('la08_c_descr').value = '';
            }
        }
    }

    function js_mostralab_exame(chave, erro) {
        $('la08_c_descr').value = chave;
        if (erro == true) {
            $(campoValor).focus();
            $(campoValor).value = '';
        }
    }

    function js_mostralab_exame1(chave1, chave2) {
        $(campoValor).value = chave1;
        $('la08_c_descr').value = chave2;
        db_iframe_lab_exame.hide();
    }
</script>
