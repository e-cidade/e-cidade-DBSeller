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

/*
 *  modelo[analitico|sintetico]
 *  sintetico - somente codlan+sequencia, documento e valor
 *  analitico - imprimiri historico da tabela conlancamcompl
 *  imprime contrapartida - opcional
 * default - analitico
 *
 */

use ECidade\Financeiro\Contabilidade\Relatorio\Razao\PorConta\RelatorioRazaoPorConta;

require_once modification("dbforms/db_funcoes.php");
require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");

ini_set('memory_limit', '2048M');

$estruturais = [];

if (!empty($_GET["estrut_inicial"])) {
    $estruturais = explode("," , $_GET["estrut_inicial"]);
}
$relatorio = new RelatorioRazaoPorConta($_GET["tiporelatorio"]);

$relatorio->getRelatorio()->setAnoUsu(db_getsession("DB_anousu"));
$relatorio->getRelatorio()->setInstituicao(db_getsession("DB_instit"));
$relatorio->getRelatorio()->setDataInicial($_GET["data1"]);
$relatorio->getRelatorio()->setDataFinal($_GET["data2"]);
$relatorio->getRelatorio()->setRelatorio($_GET["relatorio"]);
$relatorio->getRelatorio()->setSaldoPorDia($_GET["saldopordia"] == 's');
$relatorio->getRelatorio()->setContasSemMovimento($_GET["contasemmov"] == 's');
$relatorio->getRelatorio()->setEstrutural($estruturais);
$relatorio->getRelatorio()->setQuebraPaginaPorConta(
    $_GET["quebrapaginaporconta"] == 's'
);
$relatorio->getRelatorio()->setDocumentos($_GET["sDocumentos"]);
$relatorio->getRelatorio()->setRecursos($_GET["recurso"]);
if (!empty($lista)) {
    $relatorio->getRelatorio()->setContas($lista);
}
$relatorio->getRelatorio()->run();
