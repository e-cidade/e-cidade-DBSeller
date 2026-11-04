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

use ECidade\Financeiro\Contabilidade\Relatorio\RREO\Factory\AnexoXIV as RelatorioFactory;
use ECidade\Financeiro\Contabilidade\Relatorio\RREO\Factory\Layout\AnexoXIV as LayoutFactory;

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_usuariosonline.php');
require_once modification('fpdf151/assinatura.php');
require_once modification('libs/db_sql.php');
require_once modification('libs/db_utils.php');
require_once modification('libs/db_app.utils.php');
require_once modification('libs/db_libtxt.php');
require_once modification('dbforms/db_funcoes.php');
require_once modification('libs/db_libpostgres.php');
require_once modification('libs/db_libcontabilidade.php');
require_once modification('libs/db_liborcamento.php');
require_once modification('fpdf151/PDFDocument.php');
require_once modification('fpdf151/pdf.php');

$get = (object)filter_input_array(INPUT_GET);

$carregarLaravel = [
    $get->emite_balorc,
    $get->emite_rec_desp,
    $get->emite_mde,
    $get->emite_rcl,
    $get->emite_resultado,
    $get->emite_saude
];

if (in_array(1, $carregarLaravel)) {
    $app = require_once ECIDADE_PATH . 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
}


$ano = db_getsession('DB_anousu');
$instituicoes = str_replace('-', ',', $get->db_selinstit);
$quadros = array(
    AnexoXVIIIResumido::EMITIR_BALANCO_ORCAMENTARIO => $get->emite_balorc,
    AnexoXVIIIResumido::EMITIR_DESPESA_FUNCAO_SUBFUNCAO => $get->emite_desp_funcsub,
    AnexoXVIIIResumido::EMITIR_RECEITA_CORRENTE_LIQUIDA => $get->emite_rcl,
    AnexoXVIIIResumido::EMITIR_DESPESAS_RECEITAS_RPPS => $get->emite_rec_desp,
    AnexoXVIIIResumido::EMITIR_RESULTADO_NOMINAL_PRIMARIO => $get->emite_resultado,
    AnexoXVIIIResumido::EMITIR_RESTOS_A_PAGAR => $get->emite_rp,
    AnexoXVIIIResumido::EMITIR_DESPESAS_MDE => $get->emite_mde,
    AnexoXVIIIResumido::EMITIR_DESPESAS_SAUDE => $get->emite_saude,
    AnexoXVIIIResumido::EMITIR_OPERACAO_DE_CREDITO => $get->emite_oper,
    AnexoXVIIIResumido::EMITIR_PROJECAO_ATUARIAL_RPPS => $get->emite_proj,
    AnexoXVIIIResumido::EMITIR_ALIENACAO_ATIVOS => $get->emite_alienacao,
    AnexoXVIIIResumido::EMITIR_PPP => $get->emite_ppp,
);

$relatorio = RelatorioFactory::getInstance($ano, $get->bimestre);
$relatorio->setInstituicoes($instituicoes);
$relatorio->setExibirRelatorios($quadros);

$layout = LayoutFactory::getInstance($ano);
$layout->definirRelatorio($relatorio);
$layout->definirQuadros($quadros);
$layout->imprimir();




