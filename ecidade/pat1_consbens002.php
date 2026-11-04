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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("dbforms/verticalTab.widget.php"));

require_once(modification("model/patrimonio/Bem.model.php"));
require_once(modification("model/patrimonio/BemCedente.model.php"));
require_once(modification("model/patrimonio/BemClassificacao.model.php"));
require_once(modification("model/patrimonio/PlacaBem.model.php"));
require_once(modification("model/patrimonio/BemHistoricoMovimentacao.model.php"));
require_once(modification("model/patrimonio/BemDadosMaterial.model.php"));
require_once(modification("model/patrimonio/BemDadosImovel.model.php"));
require_once(modification("model/patrimonio/BemTipoAquisicao.php"));
require_once(modification("model/patrimonio/BemTipoDepreciacao.php"));
require_once(modification("model/CgmFactory.model.php"));

try {
  $oDaoBens = new cl_bens();

/**
 * Carregamos o bem e os objetos que ele retorna
 */
$oGet  = db_utils::postMemory($_GET, false);
$lErro = true;

if (isset($oGet->t52_ident) && trim($oGet->t52_ident != "")) {

  $oDaoBensPlaca           = new cl_bensplaca();
  $sCamposBuscaBemPorPlaca = " t52_bem ";
  $sWhereBuscaBemPorPlaca  = "     t52_ident  ilike '{$oGet->t52_ident}' ";
  $sWhereBuscaBemPorPlaca .= " and t52_instit = " . db_getsession('DB_instit');

  $sSqlBuscaBemPorPlaca = $oDaoBensPlaca->sql_query(null, $sCamposBuscaBemPorPlaca, null, $sWhereBuscaBemPorPlaca);
  $rsBuscaBemPorPlaca   = $oDaoBensPlaca->sql_record($sSqlBuscaBemPorPlaca);

  if ($oDaoBensPlaca->numrows > 0) {

    $oBemPorPlaca  = db_utils::fieldsMemory($rsBuscaBemPorPlaca, 0);
    $oGet->t52_bem = $oBemPorPlaca->t52_bem;
    $lErro = false;
  }
}

if (isset($oGet->t52_bem) && trim($oGet->t52_bem != "")) {

  $sCamposBuscaBem = " t52_bem ";
  $sMetodoQuery    = "sql_query";

  if ( isset($oGet->lObrigaConta) && $oGet->lObrigaConta == 'false'){
    $sMetodoQuery = "sql_query_func_pesquisa";
  }
  $sSqlBuscaBem = $oDaoBens->{$sMetodoQuery}($oGet->t52_bem, $sCamposBuscaBem);
  $rsBuscaBem   = $oDaoBens->sql_record($sSqlBuscaBem);

  $lErro = !$rsBuscaBem || $oDaoBens->numrows == 0;
}

if ($lErro) {
  db_redireciona('db_erros.php?fechar=true&db_erro=Bem%20%20n%E3o%20encontrado.');
}

$oBem           = new Bem($oGet->t52_bem);
$oClassificacao = $oBem->getClassificacao();
$oFornecedor    = $oBem->getFornecedor();
$oPlaca         = $oBem->getPlaca();
$oImovel        = $oBem->getDadosImovel();
$oTipoAquisicao = $oBem->getTipoDepreciacao();

/**
 * Carregamos a DAO e efetuamos a consulta necessária de Orgão e Unidade
 */
$oDaoDbDepartOrg          = new cl_db_departorg();
$sCamposBuscaOrgaoUnidade = " db01_orgao, o40_descr, db01_unidade, o41_descr ";
$sWhereBuscaOrgaoUnidade  = "     db01_anousu = ".db_getsession("DB_anousu");
$sWhereBuscaOrgaoUnidade .= " AND db01_coddepto = {$oBem->getDepartamento()} ";
$sSqlBuscaOrgaoUnidade    = $oDaoDbDepartOrg->sql_query_orgunid(null, null, $sCamposBuscaOrgaoUnidade, null, $sWhereBuscaOrgaoUnidade);
$rsBuscaOrgaoUnidade      = $oDaoDbDepartOrg->sql_record($sSqlBuscaOrgaoUnidade);

if ($rsBuscaOrgaoUnidade) {
  $oOrgaoUnidade = db_utils::fieldsMemory($rsBuscaOrgaoUnidade, 0);
}

/**
 * Carregamos a DAO e efetuamos a consulta necessária de Descricao de departamento
 */
$oDaoDbDepart             = new cl_db_depart();
$sCamposBuscaDepartamento = " descrdepto, t30_codigo, t30_descr ";
$sWhereBuscaDepartamento  = " coddepto = {$oBem->getDepartamento()} ";

if ($oBem->getDivisao() != '') {
  $sWhereBuscaDepartamento .= " AND t30_codigo = {$oBem->getDivisao()}";
}

$sSqlBuscaDepartamento    = $oDaoDbDepart->sql_query_div(null, $sCamposBuscaDepartamento, null, $sWhereBuscaDepartamento);

$rsBuscaDepartameto       = $oDaoDbDepart->sql_record($sSqlBuscaDepartamento);
if ($rsBuscaDepartameto) {
  $oDepartamento            = db_utils::fieldsMemory($rsBuscaDepartameto, 0);
}

/**
 * Carregamos a DAO e efetuamos a consulta necessária de Convênios
 */
if ($oBem->getCedente() != null) {

  $oDaoConvenio         = new cl_benscadcedente();
  $sCamposBuscaConvenio = " z01_nome ";
  $sWhereBuscaConvenio  = " t04_sequencial = {$oBem->getCedente()->getCodigo()} ";
  $sSqlBuscaConvenio    = $oDaoConvenio->sql_query(null, $sCamposBuscaConvenio, null, $sWhereBuscaConvenio);
  $rsBuscaConvenio      = $oDaoConvenio->sql_record($sSqlBuscaConvenio);
  $oConvenio            = db_utils::fieldsMemory($rsBuscaConvenio, 0);
}

$valorAtual = $oBem->getValorAtual() - $oBem->getValorResidual();
$valorDepreciavel = db_formatar((float)($oBem->getValorAquisicao()) - ((float)$oBem->getValorResidual()), "f");

if($oBem->getQuantidadeMesesDepreciados() == 0) {
    $valorAtual = $oBem->getValorAtual();
}

$valorUltimaReavaliacao = (float) $oBem->getValorUltimaReavaliacao();
$valorAux = $oBem->getValorAquisicao();

if($oBem->getDataUltimaAvaliacao() !== null) {

  if($valorUltimaReavaliacao == 0) {
    $valorDepreciavel = db_formatar($valorUltimaReavaliacao,"f");
    
  } else {
    $valorDepreciavel = db_formatar($valorUltimaReavaliacao - ((float) $oBem->getValorResidual()), "f");
  }    

  if ($valorUltimaReavaliacao == $oBem->getValorAtual()) {
      $valorAtual = $oBem->getValorAtual();
  }

  $valorAux = $valorUltimaReavaliacao > $oBem->getValorAquisicao() ? $valorUltimaReavaliacao : $oBem->getValorAquisicao();
}

/*
 * Necessário por causa de um bug ao desprocessar um bem reavalido
 */
if ($valorAtual > $valorAux) {
    $valorAtual = $valorAux;
}

$saldoDepreciavel = $valorAtual - $oBem->getValorResidual();

$sql = $oDaoBens->getSqlValoresAtuaisBem($oBem->getCodigoBem());

$rsAux = db_query($sql);
$aux = pg_fetch_object($rsAux, 0);

$valorAtual = $aux->t58_valoratual ? $aux->t58_valoratual : round($aux->t44_valoratual + $aux->t44_valorresidual, 2);
$saldoDepreciavel = $valorAtual - $aux->t44_valorresidual;

?>
<html>
<head>
  <title>Dados do Cadastro de Veículos</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
  <link href="estilos.css" rel="stylesheet" type="text/css">
  <link href="estilos/tab.style.css" rel="stylesheet" type="text/css">
  <style type='text/css'>
    .valores {background-color:#FFFFFF}

    .ctnFoto {

      float:          right;
      vertical-align: top;
      margin-right:   50px;
    }

    .ctnFoto img {

      width:  130;
      height: 145;
    }
  </style>
</head>

<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<fieldset>

  <legend>
    <strong>Dados do Bem:</strong>
  </legend>

  <table style="display:inline;">

    <tr>
      <td>
        <strong>Bem: </strong>
      </td>
      <td class="valores" width="30" align="right">
        <?php echo $oBem->getCodigoBem(); ?>
      </td>
      <td class="valores" width="250">
        <?php echo $oBem->getDescricao(); ?>
      </td>
      <td style="width: 140px">
        <strong>Classificação: </strong>
      </td>
      <td class="valores" width="30" align="right">
        <?php
        if ($oClassificacao != null) {
          echo $oClassificacao->getCodigo();
        }
        ?>
      </td>
      <td class="valores" width="250">
        <?php
        if ($oClassificacao != null) {
          echo $oClassificacao->getDescricao();
        }
        ?>
      </td>
    </tr>

    <tr>
      <td>
        <strong>Órgão: </strong>
      </td>
      <td class="valores" align="right">
        <?php echo !empty($oOrgaoUnidade->db01_orgao)?$oOrgaoUnidade->db01_orgao:''; ?>
      </td>
      <td class="valores">
        <?php echo !empty($oOrgaoUnidade->o40_descr)?$oOrgaoUnidade->o40_descr:''; ?>
      </td>
      <td>
        <strong>Unidade: </strong>
      </td>
      <td class="valores" align="right">
        <?php echo !empty($oOrgaoUnidade->db01_unidade)?$oOrgaoUnidade->db01_unidade:''; ?>
      </td>
      <td class="valores">
        <?php echo !empty($oOrgaoUnidade->o41_descr)?$oOrgaoUnidade->o41_descr:''; ?>
      </td>
    </tr>

    <tr>
      <td>
        <strong>Departamento: </strong>
      </td>
      <td class="valores" align="right">
        <?php echo $oBem->getDepartamento(); ?>
      </td>
      <td class="valores">
        <?php echo $oDepartamento->descrdepto; ?>
      </td>
      <td>
        <strong>Divisão Depart.: </strong>
      </td>
      <td class="valores" align="right">
        <?php echo $oBem->getDivisao(); ?>
      </td>
      <td class="valores">
        <?php echo $oBem->getDivisao() != '' ? $oDepartamento->t30_descr :  null; ?>
      </td>
    </tr>

    <tr>
      <td>
        <strong>Fornecedor: </strong>
      </td>
      <td class="valores" align="right">
        <?php
        if ($oFornecedor != null) {
          echo $oFornecedor->getCodigo();
        }
        ?>
      </td>
      <td class="valores">
        <?php
        if ($oFornecedor != null) {
          echo $oFornecedor->getNome();
        }
        ?>
      </td>
      <td>
        <strong>Convênio: </strong>
      </td>
      <td class="valores" align="right">
        <?php
        if ($oBem->getCedente() != null) {
          echo $oBem->getCedente()->getCodigo();
        }
        ?>
      </td>
      <td class="valores">
        <?php
        if ($oBem->getCedente() != null) {
          echo $oConvenio->z01_nome;
        }
        ?>
      </td>
    </tr>

    <tr>
      <td>
        <strong>Placa ident.: </strong>
      </td>

      <td class="valores" colspan="2" align="right">
        <?php echo $oBem->getIdentificacao(); ?>
      </td>
      <td>
        <strong>Código do lote: </strong>
      </td>
      <td class="valores" colspan="2" align="right">
        <?php
        if ($oImovel != null) {
          echo $oImovel->getIdBql();
        }
        ?>
      </td>
    </tr>

    <tr>
      <td>
        <strong>Aquisição: </strong>
      </td>

      <td class="valores" colspan="2">
        <?php echo db_formatar($oBem->getDataAquisicao(), "d"); ?>
      </td>
      <td>
        <strong>Tipo de Depreciação: </strong>
      </td>
      <td class="valores" colspan="2">
        <?php
        if ($oTipoAquisicao != null) {
          echo $oTipoAquisicao->getDescricao();
        }
        ?>
      </td>
    </tr>
    <tr>
      <td>
        <strong>Valor Aquisição: </strong>
      </td>
      <td class="valores" colspan="2" align="right">
        <?php echo db_formatar($oBem->getValorAquisicao(), "f"); ?>
      </td>
      <td>
        <strong>Valor Residual: </strong>
      </td>

      <td class="valores" colspan="2" align="right">
        <?php echo db_formatar($oBem->getValorResidual(), "f");?>
      </td>
    </tr>
    <tr>
      <td>
        <strong>Valor Depreciável:</strong>
      </td>
      <td class="valores" colspan="2" align="right">
        <?php echo $valorDepreciavel; ?>
      </td>
      <td>
        <strong>Depreciação: </strong>
      </td>
      <td class="valores" colspan="2">
        <?php
        if ($oTipoAquisicao != null) {
          echo $oTipoAquisicao->getDescricao();
        }
        ?>
      </td>
    </tr>
    <tr>
    <tr>
      <td>
        <strong>Valor Depreciado:</strong>
      </td>
      <td class="valores" colspan="2" align="right">
        <?php
        $valorDepreciado = $oBem->getValorCalculo() - $oBem->getValorCalculo(false);
        $valorDepreciado = $valorDepreciado < 0 ? 0 : $valorDepreciado;
        echo db_formatar($valorDepreciado, "f"); ?>
      </td>
      <td>
        <strong>Vida Útil: </strong>
      </td>
      <td class="valores" colspan="2">
        <?php
          echo $oBem->getVidaUtil() . " Ano(s)";
        ?>
      </td>
    </tr>
    <tr>
      <td>
        <b>Valor Atual:</b>
      </td>
      <td class="valores" colspan="2" align="right">
        <?php echo db_formatar($valorAtual, "f");?>
      </td>

      <td>
        <strong>Situação: </strong>
      </td>
      <td class="valores" colspan="2">
        <?php
        echo $oBem->isBaixado() ? "Baixado" : "Ativo";
        ?>
      </td>
    </tr>
    <tr>
      <td>
        <strong>Valor da Última Reavaliação: </strong>
      </td>
      <td class="valores" colspan="2" align="right">
        <?php
        echo $oBem->getTotalDeReavaliacoes() == 0 ? db_formatar("", "f") : db_formatar($oBem->getValorUltimaReavaliacao(), "f");
        ?>
      </td>

      <td>
        <strong>Saldo a Depreciar: </strong>
      </td>
        <td class="valores" colspan="2" align="right">
          <?php
            echo db_formatar($saldoDepreciavel, "f");
          ?>
        </td>
    </tr>
    <tr>
      <td>
        <strong>Observações: </strong>
      </td>
      <td class="valores" colspan="7">
        <?php echo $oBem->getObservacao(); ?>
      </td>
    </tr>
  </table>

  <div class="ctnFoto">
    <fieldset>
      <legend>Foto</legend>
      <img id="fotoBem" src="imagens/none1.jpeg" />
    </fieldset>
  </div>

</fieldset>

<?php
$oTabDetalhes = new verticalTab('detalhesBem', 300);

$sGetUrl = "?t52_bem={$oGet->t52_bem}";
$oTabDetalhes->add('dadosMaterial', 'Dados Material', "func_detalhesdadosmaterialbens.php{$sGetUrl}");
$oTabDetalhes->add('dadosImovel', 'Dados Imovel', "func_detalhesdadosimovelbens.php{$sGetUrl}");
$oTabDetalhes->add('historicomovimentacao', 'Histórico Movimentação', "func_detalheshistoricomovimentacaobens.php{$sGetUrl}");
$oTabDetalhes->add('historicofinanceiro', 'Histórico Financeiro', "func_detalhehistoricofinanceirobem.php{$sGetUrl}");
$oTabDetalhes->add('placa', 'Placa', "func_detalhesplacabens.php{$sGetUrl}");
$oTabDetalhes->add('impressao', 'Impressão', "func_impressaodetalhesbem.php{$sGetUrl}");
$oTabDetalhes->add('inventario', 'Inventário', "func_detalheinventariobem.php{$sGetUrl}");
$oTabDetalhes->add('baixa', 'Dados da Baixa', "func_dadosbaixabem.php{$sGetUrl}", $oBem->isBaixado());

if (UTILIZA_INCORPORACAO_BEM) {
    $oTabDetalhes->add('incorporacao', 'Incorporação de Bens', "materiais_incorporados.php?bem={$oGet->t52_bem}");
}

$oTabDetalhes->show();
} catch (Exception $erro) {
    db_redireciona('db_erros.php?db_erro='.$erro->getMessage());
}
?>

<?php
  if ($oBem->getCaminhoFoto()) { ?>
    <script type="text/javascript">
      $('fotoBem').src = '<?php echo $oBem->getCaminhoFoto() ?>';
    </script>
<?php
  } ?>

</body>
</html>
