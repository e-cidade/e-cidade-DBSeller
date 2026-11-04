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

use ECidade\Patrimonial\Material\Helpers\Material as MaterialHelper;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/materialestoque.model.php"));
require_once(modification("dbforms/verticalTab.widget.php"));
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
$oDaoMatMater   = new cl_matmater();
$oDaoMatEstoque = new cl_matestoque();
$oDaoMatParam   = new cl_matparam();
$oDaoDepartOrg  = new cl_db_departorg();
$oDaoAlmoxDepto = new cl_db_almoxdepto();
$oDaoMatMater->rotulo->label();

$oGet              = db_utils::postMemory($_GET);
$sSqlBuscaMatMater = $oDaoMatMater->sql_query($oGet->iCodigoMaterial);
$rsBuscaMatMater   = $oDaoMatMater->sql_record($sSqlBuscaMatMater);
$oDadoMaterial     = db_utils::fieldsMemory($rsBuscaMatMater, 0);
$oMaterialEstoque  = new materialEstoque($oGet->iCodigoMaterial);
$nPrecoMedio       = $oMaterialEstoque->getPrecoMedioMaterial();
$iInstituicao      = db_getsession('DB_instit');
$iAnousu           = db_getsession('DB_anousu');

$sSqlMatEstoqueValores = "
  with consulta_material as (
    SELECT m91_codigo,
           descrdepto,
           (select m85_precomedio
            from matmaterprecomedio
            where m85_precomedio > 0
              and to_timestamp(m85_data || ' ' || m85_hora, 'YYYY-MM-DD HH24:MI:SS') < current_timestamp
              and m85_matmater = m70_codmatmater
              and m85_coddepto = m70_coddepto
            order by to_timestamp(m85_data || ' ' || m85_hora, 'YYYY-MM-DD HH24:MI:SS') desc
            limit 1)                                                                    as m85_precomedio,
           (Coalesce(Sum(CASE WHEN matestoquetipo.m81_tipo = 1 THEN matestoqueinimei.m82_quant end), 0) -
            Coalesce(Sum(CASE WHEN matestoquetipo.m81_tipo = 2 THEN m82_quant end), 0)) as m70_quant
    FROM matestoqueini
             INNER JOIN matestoquetipo ON m80_codtipo = m81_codtipo
             INNER JOIN matestoqueinimei ON m82_matestoqueini = m80_codigo
             left JOIN matestoqueinimeipm ON m82_codigo = m89_matestoqueinimei
             INNER JOIN matestoqueitem ON m82_matestoqueitem = m71_codlanc
             INNER JOIN matestoque ON m71_codmatestoque = m70_codigo
             INNER JOIN db_depart ON coddepto = m70_coddepto
             INNER JOIN db_departorg
                        on db_departorg.db01_coddepto = db_depart.coddepto and db_departorg.db01_anousu = {$iAnousu}
             INNER JOIN orcunidade ON orcunidade.o41_orgao = db_departorg.db01_orgao and
                                      orcunidade.o41_unidade = db_departorg.db01_unidade and
                                      orcunidade.o41_anousu = db_departorg.db01_anousu and orcunidade.o41_instit = {$iInstituicao}
             INNER JOIN orcorgao
                        on orcorgao.o40_orgao = orcunidade.o41_orgao and orcorgao.o40_anousu = orcunidade.o41_anousu
             INNER JOIN material.db_almox ON db_almox.m91_depto = db_depart.coddepto
    WHERE m70_codmatmater = {$oGet->iCodigoMaterial}
    GROUP BY m91_codigo, descrdepto, m70_codigo
), totais as (
    select m70_quant, (m70_quant * m85_precomedio) as m70_valor from consulta_material
) select sum(m70_quant) as quantidade_total, sum(m70_valor) as valor_total from totais;
";

$rsBuscaValorMatEstoque   = $oDaoMatEstoque->sql_record($sSqlMatEstoqueValores);
$oValorMatEstoque = db_utils::fieldsMemory($rsBuscaValorMatEstoque, 0);
$iQuantidadeTransferencia = MaterialHelper::arredondarQuantidade($oMaterialEstoque->getSaldoTransferencia(false));
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
  <link href="estilos.css" rel="stylesheet" type="text/css">
  <link href="estilos/tab.style.css" rel="stylesheet" type="text/css">

  <style>
  .tdValores {
    background-color: #FFF;
  }
  </style>
</head>
<body style="background-color: #cccccc;">
<fieldset>
  <legend><b>Dados do material</b></legend>
  <table width="600" border='0'>
    <tr>
      <td width="100"><b>Material:<b/></td>
      <td class='tdValores' colspan="3">
        <?php
          echo "{$oDadoMaterial->m60_codmater} - {$oDadoMaterial->m60_descr}";
        ?>
      </td>
    </tr>
    <tr>
      <td width="150"><b>Quantidade total em estoque:</b></td>
      <td class='tdValores' align="right" width="120" >
        <?php
        $iQuantidadeTotal = $oValorMatEstoque->quantidade_total;
        $iQuantidadeTotal = MaterialHelper::arredondarQuantidade($iQuantidadeTotal);
          echo "{$iQuantidadeTotal}";
        ?>
      </td>
        <td width="130"><b>Valor total em estoque:<b/></td>
        <td class='tdValores' align="right" width="120">
            <?php
            echo db_formatar($oValorMatEstoque->valor_total, "f");
            ?>
        </td>
    </tr>
      <tr>
          <td width="170"><b>Quantidade total reservada:</b></td>
          <td class='tdValores' align="right" width="120">
              <?php
              echo "{$iQuantidadeTransferencia}";
              ?>
          </td>
  </table>
</fieldset>
<fieldset>
    <legend><b>Movimentações</b></legend>
    <?php
    $oVerticalTab = new verticalTab('detalhesMaterial', 400);
    $sQueryString = "codmater={$oGet->iCodigoMaterial}&lNovaConsulta=true";

    $oVerticalTab->add('detalhesMaterial', 'Estoque', "mat1_matconsultaiframe001.php?{$sQueryString}");
    $oVerticalTab->add('detalhesMaterial', 'Lançamentos', "mat3_matconsultaiframe002.php?{$sQueryString}");
    $oVerticalTab->add('detalhesMaterial', 'Requisições', "mat3_matconsultaiframe004.php?{$sQueryString}");
    $oVerticalTab->add('detalhesMaterial', 'Atendimentos', "mat3_matconsultaiframe005.php?{$sQueryString}");
    $oVerticalTab->add('detalhesMaterial', 'Devoluções', "mat3_matconsultaiframe006.php?{$sQueryString}");
    $oVerticalTab->add('detalhesMaterial', 'Ponto de Pedido', "mat3_matconsultaiframe008.php?{$sQueryString}");
    $oVerticalTab->add('detalhesMaterial', 'Lotes', "mat3_matconsultalotes.php?{$sQueryString}");
    $oVerticalTab->add('detalhesMaterial', 'Nota Fiscal', "mat3_matconsultanota.php?{$sQueryString}");
    $oVerticalTab->add('detalhesMaterial', 'Imprimir', "mat3_consultamaterialimprimir001.php?{$sQueryString}");
    $oVerticalTab->show();

  ?>
</fieldset>
</body>
</html>
