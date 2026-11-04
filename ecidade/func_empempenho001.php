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

use App\Domain\Configuracao\Usuario\Models\Usuario;
use App\Domain\Patrimonial\Protocolo\Factories\DocumentoAndamentoFactory;
use App\Domain\Patrimonial\Protocolo\Model\AtividadeExecucao;
use App\Domain\Patrimonial\Protocolo\Model\DocumentoAndamento;
use App\Domain\Patrimonial\Protocolo\Repository\DocumentosMovimentacaoRepository;
use ECidade\Financeiro\Orcamento\Recurso\Origem;

require_once(modification("libs/db_stdlib.php"));
require_once(modification('libs/db_utils.php'));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("model/Dotacao.model.php"));
require_once(modification("model/CgmFactory.model.php"));
require_once(modification("classes/db_empempenho_classe.php"));
require_once(modification("classes/db_empelemento_classe.php"));
require_once(modification("classes/db_orcdotacao_classe.php"));
require_once(modification("classes/db_empempaut_classe.php"));
require_once(modification("classes/db_empemphist_classe.php"));
require_once(modification("classes/db_emphist_classe.php"));
require_once(modification("classes/db_orctiporec_classe.php"));
require_once(modification("classes/db_empagemov_classe.php"));
require_once(modification("classes/db_empautitem_classe.php"));
require_once(modification("classes/db_empempitem_classe.php"));
require_once(modification("classes/db_empempenhonl_classe.php"));
require_once(modification('classes/db_empresto_classe.php'));
require_once(modification("dbforms/verticalTab.widget.php"));

db_postmemory($_POST);
parse_str($_SERVER["QUERY_STRING"]);

$clempempenho = new cl_empempenho;
$clempempenhonl = new cl_empempenhonl;
$clempelemento = new cl_empelemento;
$clorcdotacao = new cl_orcdotacao;
$clempempaut = new cl_empempaut;
$clempemphist = new cl_empemphist;
$clemphist = new cl_emphist;
$clorctiporec = new cl_orctiporec;
$clempagemov = new cl_empagemov;
$clempautitem = new cl_empautitem;
$clempempitem = new cl_empempitem;
$clempresto = new cl_empresto;
$clcgmalt = new cl_cgmalt;

$clClassificacaoCredorEmpenho = new cl_classificacaocredoresempenho();
$clClassificacaoCredorEmpenho->rotulo->label("cc31_justificativa");
$clClassificacaoCredorEmpenho->rotulo->label("cc31_classificacaocredores");

$clempempenho->rotulo->label();
$clempempaut->rotulo->label();
$clempemphist->rotulo->label();
$clemphist->rotulo->label();
$clorctiporec->rotulo->label();
$clempagemov->rotulo->label();
$iAnousu = db_getsession("DB_anousu");
$lNotaLiquidacao = false;
$lPermissaoImpressao = db_permissaomenu(db_getsession("DB_anousu"), 398, 4754);
$e61_autori = '';
$lcgmalt = false;

$outrosDados = null;
if (isset($e60_numemp) and $e60_numemp != "") {
    $sCampos = "
    *,
    (select rh76_rhempenhofolha from rhempenhofolhaempenho where rh76_numemp = {$e60_numemp}) as empenho_folha,
    (select e171_dados from  empempenhooutrosdados where e171_numemp = {$e60_numemp}) as outros_dados";
    $sSqlBuscaEmpenho = $clempempenho->sql_query($e60_numemp, $sCampos);
    $res = $clempempenho->sql_record($sSqlBuscaEmpenho);

    if ($clempempenho->numrows > 0) {
        db_fieldsmemory($res, 0, true);
        if (!empty($outros_dados)) {
            $outrosDados = json_decode($outros_dados);
        }
        $rsNotLiq = $clempempenhonl->sql_record($clempempenhonl->sql_query_file(null, "*", null, "e68_numemp={$e60_numemp}"));
        if ($clempempenhonl->numrows > 0) {
            $lNotaLiquidacao = true;
        }

        //-----
        $ra = $clempempaut->sql_record($clempempaut->sql_query_file($e60_numemp));
        if ($clempempaut->numrows > 0) {
            db_fieldsmemory($ra, 0, true);
        }

        /**
         * Busca o processo
         */
        $oDaoEmpAutorizaProcesso = db_utils::getDao("empautorizaprocesso");
        $sWhereBuscaProcessoAdmin = " e150_empautoriza = {$e61_autori}";
        $sSqlBuscaProcessoAdmin = $oDaoEmpAutorizaProcesso->sql_query_file(null, "e150_numeroprocesso", null, $sWhereBuscaProcessoAdmin);
        $rsBuscaProcessoAdmin = $oDaoEmpAutorizaProcesso->sql_record($sSqlBuscaProcessoAdmin);
        $sProcessoAdministrativo = "";

        if ($rsBuscaProcessoAdmin && $oDaoEmpAutorizaProcesso->numrows > 0) {
            $sProcessoAdministrativo = db_utils::fieldsMemory($rsBuscaProcessoAdmin, 0)->e150_numeroprocesso;
        }

        //------
        $rhist = $clempemphist->sql_record($clempemphist->sql_query($e60_numemp));
        if ($clempemphist->numrows > 0) {
            db_fieldsmemory($rhist, 0, true);
        }

        //--CGM Alterado
        $order = " abs(z05_data_alt - date '$e60_emiss') asc, z05_sequencia desc limit 1";
        $sWhere = "z05_numcgm = $z01_numcgm and z05_data_alt > '$e60_emiss' ";
        $sqlCgmAlt = $clcgmalt->sql_query_file(null, 'z05_nome as z01_nome, z05_sequencia', $order, $sWhere);
        $rsCgmAlt = $clcgmalt->sql_record($sqlCgmAlt);

        if ($clcgmalt->numrows > 0) {
            db_fieldsmemory($rsCgmAlt, 0);
            $lcgmalt = true;
        }
    } else {
        echo '<html>';
        echo '<head>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">';
        echo '<link href="estilos.css" rel="stylesheet" type="text/css">';
        echo '<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>';
        echo '</head>';
        echo '<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">';
        echo '<script>';
        echo 'alert("Empenho não encontrado.")';
        echo '</script>';
        echo '<body>';
        echo '</html>';
        exit;
    }
}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
    <link href="estilos/tab.style.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/widgets/dbmessageBoard.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
    <script type="text/javascript" src="scripts/classes/infoLancamentoContabil.classe.js"></script>
    <script type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script>
        function js_gerar_relatorio() {

            var permissao_gerar = <?=db_permissaomenu(db_getsession("DB_anousu"), 398, 4754)?>;

            if (permissao_gerar == true) {
                tabDetalhes.location.href = 'emp2_consultas001.php';
                //jan = window.open('emp2_consultas.php?e60_numemp=<?=@$e60_numemp?>&permissao='+permissao_gerar,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
            } else {
                alert('Você não tem permissão para imprimir!');
            }

        }

        function pesquisa_cgm(lcgmalt = null) {
            if (lcgmalt) {
                js_OpenJanelaIframe('CurrentWindow.corpo', 'db_janelaCgm', 'prot3_conscgm002.php?fechar=CurrentWindow.corpo.db_janelaCgm&numcgm=' + '<?=@$e60_numcgm ?>' + '&lcgmalt=true&seqalt=' + '<?=@$z05_sequencia?>', 'Dados Cadastrais');
            } else {
                js_JanelaAutomatica('cgm', '<?=@$e60_numcgm ?>');
            }
        }

        function pesquisa_dot() {
            js_JanelaAutomatica('orcdotacao', '<?=@$e60_coddot ?>', '<?=@$e60_anousu ?>');
        }

        function pesquisa_autori() {
            js_JanelaAutomatica('empautoriza', '<?=@$e61_autori ?>');
        }

    </script>
    <style>
        .valores {
            background-color: #FFFFFF
        }
        /**
         * @TODO Criar arquivo separado
         */
        .stepper li {
            list-style: none;
            float: left;
            padding: 0px 30px;
            position: relative;
            text-align: center;
            color: #b7b7b7;
        }
        .stepper li i {
            width: 42px;
            height: 42px;
            line-height: 42px;
            border: 2px solid #b7b7b7;
            border-radius: 30px;
            display: block;
            text-align: center;
            background-color: #e1dede;
            margin-bottom: 7px;
        }
        .stepper li:after {
            content: '';
            position: absolute;
            width: 100%;
            height: 2px;
            background-color: #b7b7b7;
            top: 21px;
            left: -50%;
            z-index: -1;
        }
        .stepper li:first-child:after {
            content: none;
        }
        .stepper li.active {
            color: #0f3d64;
        }
        .stepper li.active i {
            border: 2px solid #0f3d64;
        }
        .stepper li.active:after {
            background-color: #0f3d64;
        }
    </style>
</head>
<body bgcolor="#CCCCCC">
<form name='form1'>
    <fieldset>
        <legend><B>Dados do Empenho</b></legend>
        <table border="0" cellspacing="1">
            <tr>
                <td nowrap="nowrap" align="left" title="<?= $Te60_numemp ?>">
                    <?= $Le60_numemp ?>
                </td>
                <td nowrap="nowrap" align="left" class='valores' style="width:80px">
                    <?= $e60_numemp; ?>
                </td>
                <td nowrap="nowrap" align="left" title="<?= $Te60_codemp ?>" width="45px">
                    <?= $Le60_codemp ?>
                </td>
                <?php
                /*
                 * Consulta na tabela empresto, para ver se possui registro do numero de empenho no ano corrente
                 * se tiver, exibe a iformação de restos a pagar
                 */
                $sSqlEmpresto = $clempresto->sql_query(
                    "",
                    "",
                    "*",
                    "",
                    "e91_numemp = {$e60_numemp} AND e91_anousu = {$iAnousu}"
                );
                $rsEmpresto = $clempresto->sql_record($sSqlEmpresto);
                if ($clempresto->numrows > 0) {
                    db_fieldsmemory($rsEmpresto, 0);
                    $sRestos = " - <font color='red'><b>RESTOS À PAGAR</b></font>";
                    $sRestosPagar = $e91_codtipo . " - " . $e90_descr;
                } else {
                    $sRestos = "";
                    $sRestosPagar = "";
                }
                ?>

                <td nowrap="nowrap" class='valores'>
                    <?php
                    echo "{$e60_codemp}/{$e60_anousu}";
                    if ($e60_anousu != db_getsession("DB_anousu")) {
                        echo $sRestos;
                    }
                    ?>
                </td>

                <?php //-----------  dotacão
                if (isset($e60_coddot) and ($e60_coddot != "")) {
                    $sql = $clorcdotacao->sql_query(
                        $e60_anousu,
                        $e60_coddot,
                        "o56_elemento,o56_descr,
                                           fc_estruturaldotacao(o58_anousu,o58_coddot) as o58_estrutdespesa, o15_descr"
                    );
                    $res = $clorcdotacao->sql_record($sql);
                    if ($clorcdotacao->numrows > 0) {
                        db_fieldsmemory($res, 0, true);
                    }
                }
                ?>
                <td nowrap="nowrap"  title="<?= $Te60_coddot ?>">
                    <?php
                    db_ancora($Le60_coddot, "pesquisa_dot();", 1); ?>
                </td>
                <td nowrap="nowrap" style="width: 90px; text-align: right;" class='valores'>
                    <?php
                    echo $e60_coddot;
                    ?>
                </td>
                <td class='valores' colspan='2' nowrap>
                    <?= $o40_descr ?>
                </td>
            </tr>
            <tr>
                <td nowrap="nowrap" width="160" align="left" title="Tipo de Restos a Pagar">
                    <strong>Tipo de Restos a Pagar:</strong>
                </td>
                <td nowrap="nowrap" class='valores' colspan="3">
                    <?php echo $sRestosPagar; ?>
                </td>
                <td nowrap="nowrap" align="left" title="<?= $To15_descr ?>">
                    <b>Recurso:</b>
                </td>
                <td nowrap="nowrap"  style="width: 90px; text-align: right;" class='valores'>
                    <?php
                    $origemRecurso = Origem::getEmpenho($e60_numemp, db_getsession('DB_anousu'));

                    $fonteRecurso = \App\Domain\Financeiro\Orcamento\Models\FonteRecurso::with('recurso')
                        ->where('exercicio', db_getsession('DB_anousu'))
                        ->where('orctiporec_id', $origemRecurso->o15_codigo)
                        ->first();

                    echo sprintf(
                        '%s - %s',
                        $fonteRecurso->gestao,
                        str_pad($fonteRecurso->recurso->o15_complemento, 4, '0', STR_PAD_LEFT)
                    );
                    ?>
                <td nowrap="nowrap" colspan=2 class='valores' align="left">
                    <?php
                    echo $fonteRecurso->descricao;
                    ?>
                </td>
            </tr>

            <tr>
                <td nowrap="nowrap" width="160" align="left" title="Número do processo administrativo (P.A.)">
                    <strong>Proc. Adminstravivo (P.A):</strong>
                </td>
                <td nowrap="nowrap" class='valores' colspan="3">
                    <?php echo $sProcessoAdministrativo; ?>
                </td>
                <td class="bold">Subrecurso:</td>
                <td nowrap="nowrap"  style="width: 90px; text-align: right;" class='valores'>
                    <?php

                    echo sprintf(
                        '%s - %s',
                        $fonteRecurso->recurso->o15_recurso,
                        str_pad($fonteRecurso->recurso->o15_complemento, 4, '0', STR_PAD_LEFT)
                    );

                    ?>
                </td>
                <td nowrap="nowrap" colspan=2 class='valores' align="left">
                    <?php echo $fonteRecurso->recurso->o15_descr; ?>
                </td>

                <?php
                $oDaoEmpautidot = db_utils::getDao("empautidot");
                $rsEmpautidot = $oDaoEmpautidot->sql_record($oDaoEmpautidot->sql_query_dotacao($e61_autori));
                if ($oDaoEmpautidot->numrows > 0) {
                    $oContrapartida = db_utils::fieldsMemory($rsEmpautidot, 0);
                    if ($oContrapartida->e56_orctiporec != '') {
                        echo "<tr> ";
                        echo "<td colspan='2'>&nbsp;</td>";
                        echo " <td  nowrap ";
                        echo "<b>Contrapartida:</b></td>";
                        echo "<td colspan='1' align='left' width='15' class='valores'>";
                        echo $oContrapartida->e56_orctiporec;
                        echo "</td>";
                        echo "<td class='valores' colspan='2' nowrap>";
                        echo $oContrapartida->o15_descr;
                        echo "</td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tr>
            <tr>
                <td align="left" nowrap title="<?= $Te60_emiss ?>"><?= $Le60_emiss ?></td>
                <td align="left" nowrap class='valores' colspan="3">
                    <?php
                    echo "{$e60_emiss}";
                    ?>
                </td>
                <td>
                    <b>Desdobramento:</b>
                </td>
                <td class='valores' style='text-align:right'>
                    <?php
                    $e64_codele = ' ';
                    $o56_descr = ' ';
                    $result = $clempelemento->sql_record($clempelemento->sql_query($e60_numemp, null, "e64_codele, o56_elemento,
                                                                           o56_descr", "e64_codele"));
                    // die($clempelemento->sql_query($e60_numemp, null, "e64_codele, o56_elemento,o56_descr","e64_codele"));
                    if ($clempelemento->numrows > 0) {
                        db_fieldsmemory($result, 0);
                    }
                    echo $e64_codele;
                    ?>
                </td>
                <td class='valores'> <?= $o56_elemento ?></td>
                <td class='valores'>
                    <?php
                    echo $o56_descr;
                    ?>
                </td>
            </tr>
            <tr>
                <td align="left" nowrap title="<?= $Te60_vencim ?>">
                    <?= $Le60_vencim ?>
                </td>
                <td class='valores' colspan="3">
                    <?= $e60_vencim; ?>
                </td>
                <td align="left" nowrap>
                    <?php db_ancora("<b>Credor:</b>", "pesquisa_cgm($lcgmalt);", 1); ?></b></td>
                <td colspan=3 class='valores' align="left" nowrap title="<?= $z01_nome ?>">
                    <?php
                    echo $z01_nome;
                    ?>
                </td>
            </tr>
            <tr>
                <td align="left" nowrap title="<?= $Te60_destin ?>">
                    <?= $Le60_destin ?>
                </td>
                <td class='valores' colspan="3">
                    <?= $e60_destin == '' ? '&nbsp;' : $e60_destin; ?>
                </td>
                <td>
                    <?= @$Le63_codhist ?>
                </td>
                <td colspan='3' class='valores'>
                    <?=@$e40_descr;?>
                </td>
            </tr>
            <tr>
                <td align="left" nowrap title="<?= $Te61_autori ?>">
                    <?php db_ancora($Le61_autori, "pesquisa_autori();", 1); ?></td>
                <td align="left" nowrap class='valores' colspan="3">
                    <?=$e61_autori;?>
                </td>
                <td align="left" nowrap title="<?= $Te60_codtipo ?>">
                    <?=$Le60_codtipo;?>
                </td>
                <td class='valores' colspan='3'>
                    <?=$e41_descr;?>
                </td>
            </tr>
            <tr>
                <?php

                if ($e60_numerol == "null") {
                    $e60_numerol = null;
                }

                $sql1 = $clempautitem->sql_query_lic(
                    null,
                    null,
                    "distinct l20_codigo,l20_dtpublic,l20_numero,l03_codcom,l03_descr, l20_anousu",
                    null,
                    "e55_autori = " . @$e61_autori
                );
                $result_licita = $clempautitem->sql_record($sql1);
                // por enquanto desativamos e vamos buscar sempre o e60_numerol, pois esse campo
                // não é uma FK e pode ter licitacao de outros orgãos.
                if ($clempautitem->numrows > 0 && 1 == 2) {
                    db_fieldsmemory($result_licita, 0);
                    $numerolic = $l20_numero . "/" . $l20_anousu;
                    db_input("l20_codigo", 10, "", true, "hidden", 3);
                    ?>
                <td align="left" nowrap><b>
                    <?= @$Le60_codcom ?></b>
                </td>
                <td align="left" class='valores' colspan="3">
                    <?=$l03_descr; ?>
                </td>
                <td align="left" nowrap>
                    <b>
                        <?php
                        db_ancora($Le60_numerol, "pesquisa_lic($l20_codigo);", 1);
                        ?>
                    </b>
                </td>
                <td colspan='3' class='valores'>
                    <?=$numerolic;?>
                </td>
            </tr>
                    <?php
                } else {
                    ?>
                <td align="left" nowrap>
                    <b><?= $Le60_codcom ?> </b>
                </td>
                <td align="left" nowrap class='valores' colspan="3">
                    <?= $pc50_descr; ?>
                </td>
                <td align="left" nowrap>
                    <b><?= $Le60_numerol ?>
                </td>
                <td colspan='3' class='valores'>
                    <?=$e60_numerol;?>
                </td>
                </tr>
                    <?php
                }

                $sListaCredor = " Empenho não classificado";
                $sJustificativa = null;

                $sCamposListaCredor = " cc30_codigo, cc30_descricao, cc31_justificativa ";
                $sWhereListaCredor = " cc31_empempenho = {$e60_numemp} ";

                $oDaoListaCredor = new cl_classificacaocredoresempenho();
                $sSqlListaCredor = $oDaoListaCredor->sql_query(null, $sCamposListaCredor, null, $sWhereListaCredor);
                $rsListaCredor = $oDaoListaCredor->sql_record($sSqlListaCredor);

                if ($rsListaCredor != false && $oDaoListaCredor->numrows > 0) {
                    $oListaCredor = db_utils::fieldsMemory($rsListaCredor, 0);
                    $sListaCredor = "{$oListaCredor->cc30_codigo} - {$oListaCredor->cc30_descricao}";
                    $sJustificativa = $oListaCredor->cc31_justificativa;
                }

                ?>
            <tr>
                <td align="left" nowrap title="<?= $Tcc31_classificacaocredores ?>">
                    <b><?= $Lcc31_classificacaocredores ?></b>
                </td>
                <td align="left" nowrap class='valores' colspan="3">
                    <?= $sListaCredor ?>
                </td>
                <td align="left" nowrap>
                    <b>Base Legal de contratação:</b>
                </td>
                <td colspan='3' class='valores' >
                    <?php
                    $baseLegal = '';

                    $sql = "
                    SELECT descricao
                      FROM plugins.padrsacordovinculo
                      JOIN plugins.padrsbaselegalcontratacaoacordo
                            ON padrsbaselegalcontratacaoacordo.sequencial = padrsacordovinculo.baselegalcontratacao
                    WHERE acordo = (
                        SELECT coalesce(e100_acordo, ac54_acordo)
                          FROM empempenho
                          LEFT JOIN empempenhocontrato ON empempenhocontrato.e100_numemp = empempenho.e60_numemp
                          LEFT JOIN acordoempempenho ON acordoempempenho.ac54_empempenho = empempenho.e60_numemp
                          WHERE e60_numemp = {$e60_numemp}
                          limit 1
                        )
                    LIMIT 1
                    ";
                    $rs = db_query($sql);
                    if ($rs && pg_num_rows($rs) > 0) {
                        $baseLegal = pg_fetch_result($rs, 0, 'descricao');
                    }
                    if (in_array($pc50_pctipocompratribunal, [63,64,65,66])) {
                        $baseLegal = 'Lei 14.133/21';
                    }
                    echo $baseLegal;
                    ?>
                </td>
            </tr>

            <?php
            if (!empty($sJustificativa)) {
                ?>
                <tr>
                    <td align="left" nowrap title="<?= $Tcc31_justificativa ?>">
                        <b><?= $Lcc31_justificativa ?></b>
                    </td>
                    <td colspan='8' width='100%' class='valores'>
                        <?= nl2br($sJustificativa) ?>
                </tr>
                <?php
            }
            ?>

            <tr>
                <td align="left" nowrap title="<?= $Te60_resumo ?>">
                    <?= $Le60_resumo ?>
                </td>
                <td colspan='8' width='100%' class='valores'>
                    <?= nl2br($e60_resumo); ?>
            </tr>

            <?php

            if (!empty($empenho_folha)) {
                ?>
                <tr>
                    <td align="left" nowrap>
                        <b>Origem:</b>
                    </td>
                    <td colspan='8' width='100%' class='valores'>
                        Folha de Pagamento
                </tr>
                <?php
            }
            ?>

            <?php

            if (isParaiba() && isset($outrosDados->geo_obra)) : ?>
            <tr>
                <td class="bold">Geo Obras:</td>
                <td colspan='8' class='valores'>
                    <?= $outrosDados->geo_obra?>
                </td>
            </tr>
            <?php endif; ?>
        </table>
    </fieldset>
    <fieldset style='padding-left:0px'>
        <legend><b>Detalhamento</b></legend>
        <?php
        $oTabDetalhes = new verticalTab("detalhesemp", 300);
        $oTabDetalhes->add("resmovim", "Resumo da Movimentação", "func_empempenho002.php?e60_numemp={$e60_numemp}");
        $oTabDetalhes->add(
            "itensempNovo",
            "Itens do Empenho",
            "emp2_consultaitensempenho001.php?e60_numemp={$e60_numemp}&e55_autori={$e61_autori}"
        );
        $oTabDetalhes->add("lancamemp", "Lançamentos Contábeis", "func_conlancam002.php?chavepesquisa={$e60_numemp}");
        $oTabDetalhes->add("notasemp", "Notas de Liquidação", "func_empnota001.php?e60_numemp={$e60_numemp}");
        $oTabDetalhes->add("opsemp", "Pagamentos", "func_pagordem002.php?e60_numemp={$e60_numemp}");
        $oTabDetalhes->add(
            "ordensemp",
            "Ordens de Compra",
            "func_consultamatordememp001.php?m52_numemp={$e60_numemp}&funcao_js=js_mostraordem|m51_codordem"
        );
        $lDetalhesAut = true;
        if ($e61_autori == '') {
            $lDetalhesAut = false;
        }
        $oTabDetalhes->add("solicitaemp", "Solicitações de Compra", "func_solicita001.php?e55_autori={$e61_autori}", $lDetalhesAut);
        $oTabDetalhes->add("pcemp", "Processo de Compras", "func_pcproc001.php?e55_autori={$e61_autori}", $lDetalhesAut);
        $oTabDetalhes->add("agendaemp", "Agenda de Pagamentos", "func_empempage001.php?e60_numemp={$e60_numemp}");
        //$oTabDetalhes->add("prestaconta", "Prestação de Contas","", false);
        $oTabDetalhes->add("contratos", "Contratos", "emp2_listacontratosempenho001.php?e60_numemp={$e60_numemp}");
        $oTabDetalhes->add("impressao", "Imprimir Consulta", "emp2_consultas001.php?e60_numemp={$e60_numemp}", $lPermissaoImpressao);
        $oTabDetalhes->add("reemissão", "Reemitir PDF", "emp2_reemissao.php?e60_numemp={$e60_numemp}", $lPermissaoImpressao);
        $oTabDetalhes->show();
        ?>
    </fieldset>
</form>
<div class="container">
    <?php
    $sqlStatus = "select p118_codigo,
                   p118_protprocesso,
                   p114_status,
                   p114_codigo,
                   p116_codigo as codigo_documento,
                   p118_ordem,
                   p118_ordem <= ordem as active
            from (select processo_atividadesexecucao.*,
                         documentos_andamento.p116_codigo,
                         processo_atividadesexecucao2.p118_ordem as ordem
                  from documentos_andamento
                           join processo_atividadesexecucao on p118_protprocesso = p116_protprocesso
                           join processo_atividadesexecucao processo_atividadesexecucao2
                                ON processo_atividadesexecucao2.p118_codigo = documentos_andamento.p116_atividade_atual
                  where p116_codigo_origem = {$e60_numemp}) as x
                    join atividadesexecucao on p114_codigo = p118_atividadesexecucao order by p118_ordem";
    $rsStatus = db_query($sqlStatus);
    if (!$rsStatus) {
        echo "Erro ao buscar Status do Empenho";
    }
    if (pg_num_rows($rsStatus) > 0) {
        $primeiraLinha = db_utils::fieldsMemory($rsStatus, 0);
        $documentoAndamento = DocumentoAndamento::find($primeiraLinha->codigo_documento);
        $documentoAndamentoService = DocumentoAndamentoFactory::getService($documentoAndamento);
        $processo = $documentoAndamentoService->getProcesso();
        $icons = [
            "Gerado" => '<i class="fas fa-file-alt fa-2x"></i>',
            "Conferido" => '<i class="fas fa-check fa-2x"></i>',
            "Assinado" => '<i class="fas fa-file-signature fa-2x"></i>',
            "Arquivado" => '<i class="fas fa-archive fa-2x"></i>',
        ];
        ?>
        <ul class="stepper">
            <?php
            while ($linha = pg_fetch_assoc($rsStatus)) {
                $title = '';
                $documentosMovimentacaoRepository = new DocumentosMovimentacaoRepository();
                switch ($linha['p114_codigo']) {
                    case AtividadeExecucao::GERAR:
                        $title = "Processo: {$processo->getNumero()}/{$processo->getAno()}";
                        break;
                    case AtividadeExecucao::CONFERIR:
                    case AtividadeExecucao::ASSINAR:
                    case AtividadeExecucao::ARQUIVAR:
                        if ($linha['active'] == 't') {
                            $movimentacao = $documentosMovimentacaoRepository
                                ->scopeDocumento($documentoAndamento)
                                ->scopeDevolucao('false')
                                ->scopeInvalida('false')
                                ->scopeAtividade($linha['p118_codigo'])
                                ->first();
                            $title = $movimentacao->usuario->nome;
                        } else {
                            $daoProcessoUsuario = new \cl_processo_usuarios();
                            $sql = $daoProcessoUsuario->sql_query_file(
                                null,
                                '*',
                                null,
                                "p119_protprocesso = {$processo->p58_codproc} AND
                                p119_atividadeexecucao = {$linha['p114_codigo']}"
                            );

                            $rs = db_query($sql);
                            $usuarios = [];
                            while ($usuarioResult = pg_fetch_array($rs)) {
                                $usuario = Usuario::find($usuarioResult['p119_id_usuario']);
                                $movimentacao = $documentosMovimentacaoRepository
                                    ->resetScopes()
                                    ->scopeUsuario($usuario)
                                    ->scopeDocumento($documentoAndamento)
                                    ->scopeDevolucao('false')
                                    ->scopeInvalida('false')
                                    ->first();
                                $atividadeExecutada = $movimentacao->processoAtividadeExecucao->atividade;
                                if ($atividadeExecutada->p114_codigo == $linha['p114_codigo']) {
                                    continue;
                                }
                                $usuarios[] = $usuario->nome;
                            }
                            $title = implode(PHP_EOL, $usuarios);
                        }
                        break;
                }
                $active = $linha['active'] == 't' ? ' class="active"' : '';
                $status = $linha['p114_status'];
                ?>
                <li <?=$active?> title="<?=$title?>" onclick="js_consultaProcesso()">
                    <?=$icons[$status]?><?=$status?>
                </li>

                <?php
            }
            ?>
        </ul>
        <?php
    }
    ?>
</div>
</body>
</html>
<script>

  function pesquisa_lic(licitacao){

    js_OpenJanelaIframe('CurrentWindow.corpo',
                       'db_iframe_licitacao',
                       'lic3_licitacao002.php?l20_codigo=' + licitacao,
                       'Consulta Licitação', true);
  }

  function js_infoLancamento(iLancamento) {
    var oLancamentoInfo = new infoLancamentoContabil(iLancamento);
  }

  function js_exibeContrato(iNumero) {

    var sQuery = "";
    var ac16_sequencial = iNumero;
    sQuery = "ac16_sequencial=" + ac16_sequencial;
    var iLargura = document.width - 10;
    var iAltura = getDocHeight() - 50;
    js_OpenJanelaIframe('', 'db_iframe_consultaabertura',
        'con4_consacordos003.php?' + sQuery,
        'Detalhes', true, 0, 0, iLargura);
  }
</script>
<script type="text/javascript">
    (function () {
        var query = frameElement.getAttribute('name').replace('IF', ''),
            input = document.querySelector('input[value="Fechar"]');
        input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
    })();

    function js_consultaProcesso() {
        var iCodProc = <?=$processo->p58_codproc?>;
        var sUrl = 'pro3_consultaprocesso003.php?p58_codproc='+ iCodProc;

        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe', sUrl, 'Pesquisa de Processos', true);
    }
</script>
