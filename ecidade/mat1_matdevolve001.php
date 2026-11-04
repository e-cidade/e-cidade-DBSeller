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
use App\Domain\Saude\Farmacia\Services\EstoqueMovimentacaoBnafarService;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("classes/db_atendrequi_classe.php"));
require_once(modification("classes/db_atendrequiitem_classe.php"));
require_once(modification("classes/db_atendrequiitemmei_classe.php"));
require_once(modification("classes/db_matrequi_classe.php"));
require_once(modification("classes/db_matrequiitem_classe.php"));
require_once(modification("classes/db_matestoque_classe.php"));
require_once(modification("classes/db_matestoqueini_classe.php"));
require_once(modification("classes/db_matestoqueinimei_classe.php"));
require_once(modification("classes/db_matestoqueinimeimdi_classe.php"));
require_once(modification("classes/db_matestoqueitem_classe.php"));
require_once(modification("classes/db_matestoquedev_classe.php"));
require_once(modification("classes/db_matestoquedevitem_classe.php"));
require_once(modification("classes/db_matestoquedevitemmei_classe.php"));
require_once(modification("classes/requisicaoMaterial.model.php"));
require_once(modification("classes/materialestoque.model.php"));
require_once(modification("dbforms/db_funcoes.php"));

require_once modification("libs/db_app.utils.php");
db_app::import("contabilidade.contacorrente.ContaCorrenteFactory");
db_app::import("Acordo");
db_app::import("AcordoComissao");
db_app::import("CgmFactory");
db_app::import("financeiro.*");
db_app::import("contabilidade.*");
db_app::import("contabilidade.lancamento.*");
db_app::import("Dotacao");
db_app::import("contabilidade.planoconta.*");
db_app::import("contabilidade.contacorrente.*");

db_postmemory($HTTP_POST_VARS);
$daoDeposito = new cl_db_almox();
$clatendrequi = new cl_atendrequi;
$clatendrequiitem = new cl_atendrequiitem;
$clatendrequiitemmei = new cl_atendrequiitemmei;
$clmatrequi = new cl_matrequi;
$clmatrequiitem = new cl_matrequiitem;
$clmatestoque = new cl_matestoque;
$clmatestoqueini = new cl_matestoqueini;
$clmatestoqueinimei = new cl_matestoqueinimei;
$clmatestoqueinimeimdi = new cl_matestoqueinimeimdi;
$clmatestoqueitem = new cl_matestoqueitem;
$clmatestoquedev = new cl_matestoquedev;
$clmatestoquedevitem = new cl_matestoquedevitem;
$clmatestoquedevitemmei = new cl_matestoquedevitemmei;
$oDaoMatestoqueInimeiPm = new cl_matestoqueinimeipm();
$clmatrequi->rotulo->label();
$db_opcao = 1;
$db_botao = true;
$pesq = false;
$aItens = array();
if (isset($incluir)) {
    $hasMaterialFarmacia = false;
    $sqlerro = false;
    db_inicio_transacao();

    /*
     * busca data do registro de atendimento no banco
     */
    $sql = $clatendrequi->sql_query_file("", "m42_data, m42_hora", "", "m42_codigo=$m42_codigo");
    $result_data_registro = $clatendrequi->sql_record($sql);

    if ($clatendrequi->numrows > 0) {
        db_fieldsmemory($result_data_registro, 0);
        $clatendrequi->m42_data = $m42_data;
        $clatendrequi->m42_hora = $m42_hora;
    }
    /*
     * compara se a data do sistema é menor ou igual(se for igual testa hora) a data do registro,
     * se for menor cencela a devolução, gerar erro e mensagem
     */
    if (date("Y-m-d", db_getsession("DB_datausu")) < $clatendrequi->m42_data) {
        $erro_msg = 'Data atual é anterior a data do atendimento, Devolução abortada!';
        $sqlerro = true;
    } else {
        if (date("Y-m-d", db_getsession("DB_datausu")) == $clatendrequi->m42_data) {
            if (db_hora() <= $clatendrequi->m42_hora) {
                $erro_msg = 'Hora atual dever ser posterior a hora e data do antendimento, Devolução abortada!';
                $sqlerro = true;
            }
        }
    }

    if ($sqlerro == false) {
        if (trim($valores) != "") {
            $result_m40_codigo = $clatendrequiitem->sql_record($clatendrequiitem->sql_query(
                null,
                "m40_codigo",
                null,
                "m43_codatendrequi=$m42_codigo"
            ));
            if ($clatendrequiitem->numrows != 0) {
                db_fieldsmemory($result_m40_codigo, 0);
            }
            $clmatestoquedev->m45_depto = db_getsession("DB_coddepto");
            $clmatestoquedev->m45_login = db_getsession("DB_id_usuario");
            $clmatestoquedev->m45_hora = db_hora();
            $clmatestoquedev->m45_data = date('Y-m-d', db_getsession("DB_datausu"));
            $clmatestoquedev->m45_obs = $m45_obs;
            $clmatestoquedev->m45_codmatrequi = $m40_codigo;
            $clmatestoquedev->m45_codatendrequi = $m42_codigo;
            $clmatestoquedev->incluir(null);
            $erro_msg = $clmatestoquedev->erro_msg;
            if ($clmatestoquedev->erro_status == 0) {
                $sqlerro = true;
            }
            $codigo = $clmatestoquedev->m45_codigo;
        } else {
            $sqlerro = true;
            $erro_msg = "";
        }
        if ($sqlerro == false) {
            $clmatestoqueini->m80_login = db_getsession("DB_id_usuario");
            $clmatestoqueini->m80_data = date("Y-m-d", db_getsession("DB_datausu"));
            $clmatestoqueini->m80_hora = date('H:i:s');
            $clmatestoqueini->m80_obs = @$m45_obs;
            $clmatestoqueini->m80_codtipo = "18";
            $clmatestoqueini->m80_coddepto = db_getsession("DB_coddepto");
            $clmatestoqueini->incluir(@$m80_codigo);
            if ($clmatestoqueini->erro_status == 0) {
                $sqlerro = true;
                $erro_msg = $clmatestoqueini->erro_msg;
            }
            $m82_matestoqueini = $clmatestoqueini->m80_codigo;
        }
    }
    if ($sqlerro == false) {
        $dados = explode("quant_", "$valores");
        for ($y = 1; $y < count($dados); $y++) {
            if ($sqlerro == false) {
                $info = explode("_", $dados[$y]);
                $atendrequiitem = $info[0];
                $codmatmater = $info[1];
                $matrequiitem = $info[2];
                $iCodigoIniMei = $info[4];
                $iCodMatEstoqueItem = $info[5];
                $quant_devolvida = $info[6];

                $oItem = new stdClass;
                $oItem->iCodigoMaterial = $codmatmater;
                $oItem->nQuantidade = $quant_devolvida;
                $oItem->iCodigoAtendimento = $m82_matestoqueini;
                $aItens[] = $oItem;
                $result_atend = $clatendrequiitem->sql_record($clatendrequiitem->sql_query_file($atendrequiitem));
                if ($clatendrequiitem->numrows != 0) {
                    db_fieldsmemory($result_atend, 0);
                }

                /**
                 * Consultamos se existe uma apropriacao de custo para esse atendimento;
                 * caso exista, verificamos se é necessário atualizar os valores dessa apropriação
                 */
                $oDaoCustoAproria = db_utils::getDao("custoapropria");
                $sSqlCustoAproriado = $oDaoCustoAproria->sql_query_file(
                    null,
                    "*",
                    null,
                    "cc12_matestoqueinimei = {$iCodigoIniMei}"
                );

                $rsCustoApropriado = $oDaoCustoAproria->sql_record($sSqlCustoAproriado);
                if ($oDaoCustoAproria->numrows > 0) {
                    $aCustosApropriados = db_utils::getCollectionByRecord($rsCustoApropriado);
                    foreach ($aCustosApropriados as $oCustoApropriado) {
                        $nNovaQuantidade = $oCustoApropriado->cc12_qtd - $quant_devolvida;
                        if ($nNovaQuantidade > 0) {
                            $nNovoValor = round(
                                (($nNovaQuantidade * $oCustoApropriado->cc12_valor) / $oCustoApropriado->cc12_qtd),
                                2
                            );
                            $oDaoCustoAproria->cc12_sequencial = $oCustoApropriado->cc12_sequencial;
                            $oDaoCustoAproria->cc12_qtd = $nNovaQuantidade;
                            $oDaoCustoAproria->cc12_valor = $nNovoValor;
                            $oDaoCustoAproria->alterar($oCustoApropriado->cc12_sequencial);
                        } else {
                            $oDaoCustoAproria->excluir(null, "cc12_matestoqueinimei = {$iCodigoIniMei}");
                        }
                        if ($oDaoCustoAproria->erro_status == 0) {
                            $sqlerro = true;
                            $erro_msg = $oDaoCustoAproria->erro_msg;
                        }
                    }
                }
                if (!$sqlerro) {
                    $clmatestoquedevitem->m46_codmatestoquedev = $codigo;
                    $clmatestoquedevitem->m46_codmatrequiitem = $matrequiitem;
                    $clmatestoquedevitem->m46_codatendrequiitem = $atendrequiitem;
                    $clmatestoquedevitem->m46_codmatmater = $codmatmater;
                    $clmatestoquedevitem->m46_quantdev = $quant_devolvida;
                    $clmatestoquedevitem->m46_quantexistia = $m43_quantatend;
                    $clmatestoquedevitem->incluir(null);
                    if ($clmatestoquedevitem->erro_status == 0) {
                        $sqlerro = true;
                        $erro_msg = $clmatestoquedevitem->erro_msg;
                    }
                }
                $codigodevitem = $clmatestoquedevitem->m46_codigo;
                if ($sqlerro == false) {
                    if ($m43_quantatend == $quant_devolvida) {
                        $quantatend_alt = $quant_devolvida;
                    } else {
                        $quantatend_alt = $m43_quantatend - $quant_devolvida;
                    }

                    $clatendrequiitem->m43_quantatend = "$quantatend_alt";
                    $clatendrequiitem->m43_codigo = $m43_codigo;
                    $clatendrequiitem->alterar($m43_codigo);
                    if ($clatendrequiitem->erro_status == 0) {
                        $sqlerro = true;
                        $erro_msg = $clatendrequiitem->erro_msg;
                    }
                }
                if ($sqlerro == false) {
                    $acaba = false;
                    $where = "m44_codatendreqitem={$m43_codigo} and m44_codmatestoqueitem = {$iCodMatEstoqueItem}";
                    $sql = $clatendrequiitemmei->sql_query_file(null, "*", null, $where);
                    $result_mei = $clatendrequiitemmei->sql_record($sql);
                    $numrowsmei = $clatendrequiitemmei->numrows;

                    $utilizaBnafar = FarmaciaHelper::utilizaIntegracaoBnafar();
                    for ($w = 0; $w < $numrowsmei; $w++) {
                        db_fieldsmemory($result_mei, $w);

                        if ($sqlerro == false) {
                            $sql = $clmatestoqueitem->sql_query($m44_codmatestoqueitem);
                            $result_matestoqueitem = $clmatestoqueitem->sql_record($sql);
                            db_fieldsmemory($result_matestoqueitem, 0);

                            $isMaterialFarmacia = $utilizaBnafar && FarmaciaHelper::isMaterialFarmacia(
                                $m70_codmatmater
                            );
                            if (!$hasMaterialFarmacia && $isMaterialFarmacia) {
                                $hasMaterialFarmacia = true;
                            }

                            if ($quant_devolvida >= $m44_quant) {
                                if ($quant_devolvida == $m44_quant) {
                                    $quant_altera = abs($quant_devolvida - $m71_quantatend);
                                    $devolver = $quant_devolvida;
                                } else {
                                    $quant_altera = $m71_quantatend - $m44_quant; // 0
                                    $devolver = $m44_quant; // 20
                                    $quant_devolvida = $quant_devolvida - $m44_quant; // 20
                                }
                                $clmatestoqueitem->m71_quantatend = "$quant_altera";
                            } else {
                                $quant_altera = $m71_quantatend - $quant_devolvida; // 10

                                $clmatestoqueitem->m71_quantatend = "$quant_altera";
                                $devolver = $quant_devolvida; // 20
                                $acaba = true;
                            }

                            try {
                                /**
                                 * Valida se a quantidade informada não possui ponto flutuando, caso seja um material
                                 * da farmacia e esteja sendo devolvido uma quantidade parcial
                                 */
                                if ($isMaterialFarmacia && $devolver > 0) {
                                    FarmaciaHelper::validaQuantidade($quant_devolvida);
                                }
                            } catch (Exception $e) {
                                $erro_msg = $e->getMessage();
                                $sqlerro = true;
                            }

                            $sqlInimei = "select m89_valorunitario
                                from matestoqueinimei
                                join matestoqueitem on matestoqueitem.m71_codlanc = matestoqueinimei.m82_matestoqueitem
                                join matestoqueinimeipm ON matestoqueinimeipm.m89_matestoqueinimei = matestoqueinimei.m82_codigo
                                where matestoqueinimei.m82_codigo = {$iCodigoIniMei}";
                            $rsInimei = db_query($sqlInimei);

                            if (!$rsInimei) {
                                $sqlerro = true;
                                $erro_msg = "Erro ao buscar valor unitário do atendimento.";
                            }

                            db_fieldsmemory($rsInimei, 0);
                            $valor_uni = $m89_valorunitario;
                            $valordev = $valor_uni * $devolver;

                            $clmatestoqueitem->m71_codlanc = $m71_codlanc;
                            $clmatestoqueitem->alterar($m71_codlanc);
                            if ($clmatestoqueitem->erro_status == 0) {
                                $sqlerro = true;
                                $erro_msg = $clmatestoqueitem->erro_msg;
                            }
                        }

                        if ($sqlerro == false) {
                            $clatendrequiitemmei->m44_quant = "$quant_altera";
                            $clatendrequiitemmei->m44_codigo = $m44_codigo;
                            $clatendrequiitemmei->alterar($m44_codigo);
                            if ($clatendrequiitemmei->erro_status == 0) {
                                $sqlerro = true;
                                $erro_msg = $clatendrequiitemmei->erro_msg;
                            }
                        }

                        if ($sqlerro == false) {
                            $clmatestoquedevitemmei->m47_quantdev = $devolver;
                            $clmatestoquedevitemmei->m47_codmatestoqueitem = $m71_codlanc;
                            $clmatestoquedevitemmei->m47_codmatestoquedevitem = $codigodevitem;
                            $clmatestoquedevitemmei->incluir(null);
                            if ($clmatestoquedevitemmei->erro_status == 0) {
                                $sqlerro = true;
                                $erro_msg = $clmatestoquedevitemmei->erro_msg;
                            }
                        }
                        if ($sqlerro == false) {
                            $clmatestoqueinimei->m82_matestoqueitem = $m71_codlanc;
                            $clmatestoqueinimei->m82_matestoqueini = $m82_matestoqueini;
                            $clmatestoqueinimei->m82_quant = $devolver;
                            $clmatestoqueinimei->m82_matestoqueinimeiorigem = $iCodigoIniMei;
                            $clmatestoqueinimei->incluir(@$m82_codigo);
                            if ($clmatestoqueinimei->erro_status == 0) {
                                $erro_msg = $clmatestoqueinimei->erro_msg;
                                $sqlerro = true;
                                break;
                            }
                            $codigo_inimei = $clmatestoqueinimei->m82_codigo;
                        }
                        if ($sqlerro == false) {
                            $clmatestoqueinimeimdi->m50_codmatestoquedevitem = $codigodevitem;
                            $clmatestoqueinimeimdi->m50_codmatestoqueinimei = $codigo_inimei;
                            $clmatestoqueinimeimdi->incluir(null);
                            if ($clmatestoqueinimeimdi->erro_status == 0) {
                                $erro_msg = $clmatestoqueinimeimdi->erro_msg;
                                $sqlerro = true;
                                break;
                            }
                        }

                        if ($sqlerro == false) {
                            $v = $valordev + $m70_valor;
                            $q = $devolver + $m70_quant;

                            $clmatestoque->m70_quant = "$q";
                            $clmatestoque->m70_valor = "$v";
                            $clmatestoque->m70_codigo = $m70_codigo;
                            $clmatestoque->alterar($m70_codigo);
                            if ($clmatestoque->erro_status == 0) {
                                $sqlerro = true;
                                $erro_msg = $clmatestoque->erro_msg;
                            }
                        }

                        $oDataImplantacao = new DBDate(date("Y-m-d", db_getsession('DB_datausu')));
                        $oInstituicao = new Instituicao(db_getsession('DB_instit'));
                        $lIntegracaoContabilidade = ParametroIntegracaoPatrimonial::possuiIntegracaoMaterial(
                            $oDataImplantacao,
                            $oInstituicao
                        );

                        if (USE_PCASP && $lIntegracaoContabilidade) {
                            if (!$sqlerro) {
                                try {
                                    $oMaterial = new MaterialEstoque($oItem->iCodigoMaterial);
                                    $nValorLancamento = round($valor_uni * $devolver, 2);

                                    $oRequisicao = new RequisicaoMaterial($m40_codigo);
                                    $oRequisicao->estornarLancamento($oMaterial, $codigo_inimei, $nValorLancamento);
                                } catch (BusinessException $eException) {
                                    $erro_msg = str_replace("\n", "\\n", $eException->getMessage());
                                    $sqlerro = true;
                                } catch (Exception $eException) {
                                    $erro_msg = str_replace("\n", "\\n", $eException->getMessage());
                                    $sqlerro = true;
                                }
                            }
                        }

                        if (!$sqlerro && $hasMaterialFarmacia) {
                            try {
                                EstoqueMovimentacaoBnafarService::salvar((object)[
                                    'tipoMovimentacao' => 3, // Ajuste de estoque
                                    'unidade' => db_getsession('DB_coddepto'),
                                    'cgm' => null
                                ], $m82_matestoqueini);
                            } catch (Exception $e) {
                                $erro_msg = str_replace('\n', '\\n', $e->getMessage());
                                $sqlerro = true;
                            }
                        }

                        if ($acaba == true || $devolver == 0) {
                            break;
                        }
                    }
                }
            }
        }
    }

    /**
     * escrituramos a saida dos materiais
     */
    db_fim_transacao($sqlerro);
} else {
    if (isset($chavepesquisa)) {
        $sql = $clatendrequi->sql_query($chavepesquisa);
        $result_atendrequi = $clatendrequi->sql_record($sql);
        if ($clatendrequi->numrows != 0) {
            db_fieldsmemory($result_atendrequi, 0);
            $sqlDeposito = $daoDeposito->sql_query(null, '*', null, " m91_depto = {$m42_depto} ");
            $rsDeposito = $daoDeposito->sql_record($sqlDeposito);
            db_fieldsmemory($rsDeposito, 0);
        }
    } else {
        $pesq = true;
    }
}
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container">
    <?php
    include(modification("forms/db_frmmatdevolve.php"));
    ?>
</div>
<?php
db_menu();
?>
</body>
</html>
<?php
if (isset($incluir)) {
    if (trim($erro_msg) == "") {
        $sqlerro = false;
    } else {
        db_msgbox($erro_msg);
    }

    if ($sqlerro == true) {
        if ($clmatestoquedev->erro_campo != "") {
            echo "<script> document.form1." . $clmatestoquedev->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
            echo "<script> document.form1." . $clmatestoquedev->erro_campo . ".focus();</script>";
        }
    } else {
        echo "<script>location.href='mat1_matdevolve001.php'</script>";
    }
}
if ($pesq == true) {
    echo "<script>document.form1.pesquisar.click();</script>";
}
?>
