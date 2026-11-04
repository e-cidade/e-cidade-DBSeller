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

use App\Domain\Saude\Farmacia\Helpers\FarmaciaHelper;
use ECidade\Patrimonial\Material\Services\EntradaManualService;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("classes/db_matestoque_classe.php"));
require_once(modification("classes/db_matestoqueitem_classe.php"));
require_once(modification("classes/db_matestoqueini_classe.php"));
require_once(modification("classes/db_matestoqueinil_classe.php"));
require_once(modification("classes/db_matestoqueinill_classe.php"));
require_once(modification("classes/db_matestoqueinimei_classe.php"));
require_once(modification("classes/materialestoque.model.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_app.utils.php"));

require_once(modification("model/contabilidade/contacorrente/ContaCorrenteFactory.model.php"));
require_once(modification("model/contabilidade/contacorrente/ContaCorrenteBase.model.php"));
require_once(modification("model/financeiro/ContaBancaria.model.php"));
require_once(modification("model/contabilidade/planoconta/ContaPlano.model.php"));
require_once(modification("model/contabilidade/planoconta/ClassificacaoConta.model.php"));
require_once(modification("model/contabilidade/planoconta/ContaCorrente.model.php"));
require_once(modification("model/contabilidade/planoconta/ContaOrcamento.model.php"));
require_once(modification("model/contabilidade/planoconta/ContaPlanoPCASP.model.php"));


db_app::import("exceptions.*");
db_app::import("contabilidade.*");
db_app::import("contabilidade.lancamento.*");
db_app::import("estoque.*");
db_app::import("Acordo");
db_app::import("AcordoComissao");
db_app::import("CgmFactory");
db_app::import("financeiro.*");
db_app::import("contabilidade.*");
db_app::import("contabilidade.lancamento.*");
db_app::import("Dotacao");

db_app::import("contabilidade.contacorrente.*");

db_postmemory($_GET);
db_postmemory($_POST);

$oPost = db_utils::postMemory($_POST);
$oGet = db_utils::postMemory($_GET);

$clmatestoque = new cl_matestoque;
$clmatestoqueitem = new cl_matestoqueitem;
$clmatestoqueini = new cl_matestoqueini;
$clmatestoqueinil = new cl_matestoqueinil;
$clmatestoqueinill = new cl_matestoqueinill;
$clmatestoqueinimei = new cl_matestoqueinimei;
$estoquemovimentacaoBnafar = new cl_estoquemovimentacaobnafar;
$db_botao = false;
$db_opcao = 33;

$iCodidoMovimentacaoEstoque = '';

if (isset($excluir)) {
    db_inicio_transacao();
    $sqlerro = false;
    try {
        $entradaManualService = new EntradaManualService();
        $entradaManualService->cancelarEntradaManual($oPost->m80_codigo, $oPost->m80_obs);
    } catch (Exception $exception) {
        $msgalert = $exception->getMessage();
        $sqlerro = true;
        unset($excluir);
    }
    $erro_msg = "Cancelamento da Entrada Manual efetuado com sucesso!";
    db_fim_transacao($sqlerro);
} elseif (isset($chavepesquisa)) {
    $db_opcao = 3;
    $result = $clmatestoqueini->sql_record($clmatestoqueini->sql_query_mater(
        null,
        "matestoqueini.m80_codigo,
                                          m70_codigo,
                                          m71_codlanc,
                                          m71_quantatend,
                                          m70_quant,
                                          m60_codmater,
                                          m60_descr,
                                          coddepto,
                                          descrdepto,
                                          m71_quant,
                                          m77_lote,
                                          m77_dtvalidade,
                                          m78_matfabricante,
                                          m76_nome,
                                          m71_valor,
                                          m79_sequencial,
                                          m79_notafiscal,
                                          m79_data,
                                          (m71_valor/m71_quant) as m71_valorunit,
                                          matestoqueini.m80_obs",
        "",
        "matestoqueini.m80_codigo={$chavepesquisa} and m71_quantatend=0"
    ));
    if ($clmatestoqueini->numrows > 0) {
        db_fieldsmemory($result, 0);
        if ($m77_dtvalidade != "") {
            list($m77_dtvalidade_ano, $m77_dtvalidade_mes, $m77_dtvalidade_dia) = explode("-", $m77_dtvalidade);
        }

        if (FarmaciaHelper::utilizaIntegracaoBnafar()) {
            $campos = "fa69_tipomovimentacao as cod_tipo, fa69_cgm as cgm, ";
            $campos .= " z01_nome as descr, fa69_unidade as unidade, descrdepto as unidadedescr";
            $where = "fa69_matestoqueini = {$chavepesquisa}";
            $sql = $estoquemovimentacaoBnafar->sql_query_estoquemovimentacoesbnafar(null, $campos, null, $where);
            $rs = $estoquemovimentacaoBnafar->sql_record($sql);
            if ($rs) {
                db_fieldsmemory($rs, 0);
            }
        }
        $db_botao = true;
    } else {
        $msgalert = "Usuário:\\n\\nLançamento não encontrado ou já atendido.\\nExclusão cancelada.\\n\\nAdministrador:";
        $db_opcao = 33;
    }
}
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1">
<table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
    <tr>
        <td width="360" height="18">&nbsp;</td>
        <td width="263">&nbsp;</td>
        <td width="25">&nbsp;</td>
        <td width="140">&nbsp;</td>
    </tr>
</table>
<center>
    <?php
    include(modification("forms/db_frmmatestoqueini.php"));
    ?>
</center>
<?php
db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit"));
?>
</body>
</html>
<?php
if (isset($excluir)) {
    db_msgbox($erro_msg);
}
if (isset($msgalert)) {
    db_msgbox($msgalert);
    $db_opcao = 3;
}
if ($db_opcao == 33) {
    echo "<script>document.form1.pesquisar.click();</script>";
}
?>
