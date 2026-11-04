<?php
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_liclicita_classe.php"));
require_once(modification("dbforms/verticalTab.widget.php"));

$oDaoLicitacao = new cl_liclicita;
$oDaoRotulo = new rotulocampo;
$oDaoLicitacao->rotulo->label();
$oDaoRotulo->label("nome");
$oDaoRotulo->label("l03_descr");

$oGet = db_utils::postMemory($_GET);

$sSqlBuscaLicitacao = $oDaoLicitacao->sql_query($oGet->l20_codigo);
$rsLicitacao = $oDaoLicitacao->sql_record($sSqlBuscaLicitacao);
if ($oDaoLicitacao->numrows == 0) {
    db_redireciona('db_erros.php?fechar=true&db_erro=Este registro não possui licitação.');
}

$oLicitatacao = db_utils::fieldsMemory($rsLicitacao, 0);
$licitacao = new licitacao($oGet->l20_codigo);
$orcamentoLicitacao = new OrcamentoLicitacao($licitacao);

$comprasPncp = new \App\Domain\Patrimonial\PNCP\Models\ComprasPncp();
$existePncp = $comprasPncp->where(['pn03_liclicita' => $oGet->l20_codigo])->exists();

$dtSituacao = "";
$oDaoLicitacaoSituacao = db_utils::getDao("liclicitasituacao");
$sWhere = " l11_liclicita       = " . $oLicitatacao->l20_codigo;
$sWhere .= " and l11_licsituacao = " . $oLicitatacao->l08_sequencial;
$sOrder = " l11_sequencial desc limit 1 ";
$sSqlDataSituacao = $oDaoLicitacaoSituacao->sql_query(null, "l11_data", $sOrder, $sWhere);
$rsDataSituacao = $oDaoLicitacaoSituacao->sql_record($sSqlDataSituacao);
if ($oDaoLicitacaoSituacao->numrows > 0) {
    $dtSituacao = db_formatar(db_utils::fieldsMemory($rsDataSituacao, 0)->l11_data, 'd');
}

$oDadosLicitatacao = new licitacao($l20_codigo);
$oProcessoProtocolo = $oDadosLicitatacao->getProcessoProtocolo();
$sProcessoProtocolo = '';

if (!empty($oProcessoProtocolo)) {
    $sProcessoProtocolo = $oProcessoProtocolo->getNumeroProcesso() . "/" . $oProcessoProtocolo->getAnoProcesso();
}

$tramita = "Não";
if (isParaiba()) {
    $rs = db_query("select 1 from licitacaotramita where licitacao_id = {$l20_codigo}");
    $tramita = pg_num_rows($rs) > 0? 'Sim' : 'Não';
}

?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link href="estilos/tab.style.css" rel="stylesheet" type="text/css">
    <style type="text/css">

        .valor {
            background-color: #FFF;
        }
    </style>
</head>
<body>
<fieldset>
    <legend style="font-weight: bolder;"> Dados da Licitação</legend>

    <table border='0'>

        <tr>
            <td title="<?= $Tl20_codigo ?>">
                <?php echo $Ll20_codigo; ?>
            </td>
            <td nowrap="nowrap" class="valor" style="text-align: left;">
                <?php echo $oLicitatacao->l20_codigo; ?>
            </td>
            <td nowrap="nowrap" title="Numero do Processo">
                <?php
                if ($sProcessoProtocolo != '') {
                    db_ancora("Número do Processo:", "consultaProcesso('{$sProcessoProtocolo}')", 1);
                } else {
                    echo "<b> Número do Processo: </b>";
                }
                ?>
            </td>
            <td align='left' class="valor">
                <?php echo $sProcessoProtocolo ? $sProcessoProtocolo : $oLicitatacao->l20_procadmin ?>
            </td>
        </tr>
        <tr>
            <td nowrap title="<?= $Tl20_edital ?>" style="width: 100px;">
                <?= $Ll20_edital ?>
            </td>
            <td nowrap="nowrap" class="valor" style="width: 400px; text-align: left; ">
                <?php echo $oLicitatacao->l20_edital; ?>
            </td>
            <td nowrap="nowrap" style=" width: 100px;">
                <?= $Ll20_anousu ?>
            </td>
            <td nowrap="nowrap" class="valor" style="width:100px; text-align: left; ">
                <?php echo $oLicitatacao->l20_anousu; ?>
            </td>
        </tr>
        <tr>
            <td nowrap="nowrap" title="<?= @$Tl20_codtipocom ?>">
                <b>Modalidade:</b>
            </td>
            <td nowrap="nowrap" class="valor" style="text-align: left;">
                <?php echo $oLicitatacao->l20_codtipocom . " - " . $oLicitatacao->l03_descr; ?>
            </td>
            <td nowrap="nowrap" title="<?= $Tl20_numero ?>">
                <?= $Ll20_numero ?>
            </td>
            <td nowrap="nowrap" class="valor" style="text-align: left; ">
                <?php echo $oLicitatacao->l20_numero; ?>
            </td>
        </tr>
        <tr>
            <td nowrap="nowrap" title="<?= @$Tl20_datacria ?>">
                <b><?= @$Ll20_datacria ?></b>
            </td>
            <td nowrap="nowrap" class="valor" style="text-align: left;">
                <?php echo implode("/", array_reverse(explode("-", $oLicitatacao->l20_datacria))); ?>
            </td>
            <td nowrap="nowrap" title="<?= @$Tl20_horacria ?>">
                <b><?= @$Ll20_horacria ?></b>
            </td>
            <td nowrap="nowrap" class="valor" style="text-align: left;">
                <?php echo $oLicitatacao->l20_horacria; ?>
            </td>
        </tr>
        <tr>
            <td nowrap="nowrap" title="<?= @$Tl20_dataaber ?>">
                <b><?= @$Ll20_dataaber ?></b>
            </td>
            <td nowrap="nowrap" class="valor" style="text-align: left;">
                <?php echo implode("/", array_reverse(explode("-", $oLicitatacao->l20_dataaber))); ?>
            </td>
            <td nowrap="nowrap" title="<?= @$Tl20_horaaber ?>">
                <b><?= @$Ll20_horaaber ?></b>
            </td>
            <td nowrap="nowrap" class="valor" style="text-align: left;">
                <?php echo $oLicitatacao->l20_horaaber; ?>
            </td>
        </tr>
        <tr>
            <td nowrap="nowrap">
                <b>Data Situação:</b>
            </td>
            <td nowrap="nowrap" class="valor" style="text-align: left;">
                <?php echo $dtSituacao; ?>
            </td>
            <td nowrap="nowrap" style="text-align: left;" title="<?= @$Tl20_dtpublic ?>">
                <b><?= @$Ll20_dtpublic ?></b>
            </td>
            <td nowrap="nowrap" class="valor" style="text-align: left;">
                <?php echo implode("/", array_reverse(explode("-", $oLicitatacao->l20_dtpublic))); ?>
            </td>
        </tr>
        <tr>
            <td nowrap="nowrap" title="<?= @$Tl20_id_usucria ?>">
                <?= @$Ll20_id_usucria ?>
            </td>
            <td nowrap="nowrap" class="valor" style="text-align: left;">
                <?php echo $oLicitatacao->l20_id_usucria . " - " . $oLicitatacao->nome; ?>
            </td>

            <td nowrap="nowrap">
                <b>Situação:</b>
            </td>
            <td nowrap="nowrap" class="valor" style="text-align: left;">
                <?php echo $oLicitatacao->l08_descr; ?>
            </td>
        </tr>
        <tr>
            <td nowrap="nowrap" title="<?= @$Tl20_local ?>">
                <b><?= @$Ll20_local ?></b>
            </td>
            <td colspan='3' align='left' class="valor">
                <?php echo $oLicitatacao->l20_local ?>
            </td>
        </tr>
        <tr>
            <td nowrap="nowrap" title="<?= @$Tl20_objeto ?>">
                <b><?= @$Ll20_objeto ?></b>
            </td>
            <td colspan='3' align='left' class="valor">
                <?php echo $oLicitatacao->l20_objeto ?>
            </td>
        </tr>
        <tr>
            <td nowrap="nowrap" title="<?= @$Tl20_tipo ?>">
                <b><?= @$Ll20_tipo ?></b>
            </td>
            <td colspan='3' align='left' class="valor">
                <?php echo $oLicitatacao->l20_tipo == 1 ? 'Gera Despesa' : 'Não Gera Despesa'; ?>
            </td>
        </tr>
        <tr>
            <td nowrap="nowrap">
                <b><?= @$Ll20_tipojulg ?></b>
            </td>
            <td nowrap="nowrap" class="valor" style="text-align: left;">
                <?php
                $tipoJulgamento = 'Por item';
                $tipoJulgamento = $oLicitatacao->l20_tipojulg === '2' ? 'Global' : $tipoJulgamento;
                $tipoJulgamento = $oLicitatacao->l20_tipojulg === '3' ? 'Por lote' : $tipoJulgamento;

                echo $tipoJulgamento;
                ?>
            </td>
            <td nowrap="nowrap" title="<?= @$Tl20_id_usucria ?>">
                <b><?= @$Ll20_usaregistropreco ?></b>
            </td>
            <td nowrap="nowrap" class="valor" style="text-align: left;">
                <?= $licitacao->usaRegistroDePreco() ? 'Sim' : 'Não'?>
            </td>
        </tr>
        <tr>
            <td nowrap="nowrap">
                <b>PNCP: </b>
            </td>
            <td nowrap="nowrap" class="valor" style="text-align: left;">
                <?= $existePncp ? 'Sim' : 'Não'; ?>
            </td>

            <td nowrap="nowrap" title="<?= @$Tl20_id_usucria ?>">
                <b>Valor Total Estimado: </b>
            </td>
            <td nowrap="nowrap" class="valor" style="text-align: left;">
                <?= db_formatar($orcamentoLicitacao->getValorTotalEstimado(), 'f'); ?>
            </td>
        </tr>
        <?php if (isParaiba()) : ?>
        <tr>
            <td nowrap="nowrap" title="">
                <b>Esta no TCE (Tramita): </b>
            </td>
            <td colspan='3' align='left' class="valor">
                <?= $tramita?>
            </td>
        </tr>
        <?php endif; ?>
    </table>
</fieldset>

<div>
<?php
/**
 * Configuramos e exibimos as "abas verticais" (componente verticalTab)
 */
$oVerticalTab = new verticalTab('detalhesLicitacao', 530);
$sGetUrl = "l20_codigo={$oGet->l20_codigo}";

$oVerticalTab->add('dadosItensLicitacoes', 'Itens da Licitação',
    "db_consultaitenslicitacao001.php?{$sGetUrl}");

$oVerticalTab->add('dadosProcessosCompras', 'Processos de Compras',
    "lic3_infolic003.php?tipo=p&{$sGetUrl}");

$oVerticalTab->add('dadosSolicitacoesCompras', 'Solicitações de Compras',
    "lic3_infolic003.php?tipo=s&{$sGetUrl}");

$oVerticalTab->add('dadosSituacoesLicitacao', 'Situações da Licitação',
    "lic3_infolic003.php?tipo=m&{$sGetUrl}");

$oVerticalTab->add('dadosEditais', 'Editais',
    "lic3_infolicanexo002.php?{$sGetUrl}");

$oVerticalTab->add('dadosAtas', 'Atas',
    "lic3_infolicata002.php?{$sGetUrl}");

$oVerticalTab->add('dadosMinutas', 'Minutas',
    "lic3_infolicminuta002.php?{$sGetUrl}");

$oVerticalTab->add('dadosAcordo', 'Acordos',
    "com3_pesquisalicitacaocontrato.php?{$sGetUrl}");

$oVerticalTab->add('dadosEventos', 'Eventos',
    "lic4_licitacaoeventos001.php?{$sGetUrl}&consulta=true");

$oVerticalTab->add('dadosComissoes', 'Comissão',
    "lic4_detalhecomissao001.php?{$sGetUrl}");

$oVerticalTab->show();
?>
</div>
</body>

<script>
    function consultaProcesso(sProtProcesso) {
        js_OpenJanelaIframe(
            'CurrentWindow.corpo',
            'db_iframe_processo',
            "pro3_consultaprocesso002.php?numero=" + sProtProcesso,
            'Consulta de Processo',
            true
        );
    }

</script>
