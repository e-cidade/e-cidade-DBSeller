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

use ECidade\Educacao\Escola\Relatorios\GuiaTransferencia\GuiaTransferencia;
use ECidade\Educacao\Escola\Relatorios\GuiaTransferencia\Services\GuiaTransferenciaService;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once modification("libs/db_utils.php");
require_once modification("libs/db_app.utils.php");
require_once modification("libs/db_stdlibwebseller.php");
require_once modification("libs/db_libdocumento.php");
require_once modification("dbforms/db_funcoes.php");

$isDeclaracao = isset($_GET['isDeclaracao']);
$alunos = $_GET['alunos'];
$tipo = $_GET['tipo'];
$diretor = $_GET['diretor'];
$bolsafamilia = $_GET['bolsafamilia'];
$obs = $_GET['obs'];
$modelo = $_GET['modelo'];
$notificar = $_GET['notificar'];
$formato = $_GET['formato'];

$guiaTransferenciaService = new GuiaTransferenciaService($tipo, $alunos, $obs, $bolsafamilia, $isDeclaracao);
$dados = $guiaTransferenciaService->buscaDadosImpressao();
$dados = $guiaTransferenciaService->montaDadosImpressao($dados);

$modelo = isset($modelo) ? $modelo : null;
$escola = db_getsession("DB_coddepto");
$notificar = isset($notificar) && $notificar == "sim" ? true : false;
$formato = isset($formato) ? $formato : "P";

$relatorio = new GuiaTransferencia($formato, $tipo, $modelo, $notificar, $escola);
$relatorio->setDados($dados);
$relatorio->setAssinatura(utf8_decode($diretor));
$emissao = $relatorio->emitir();

if (!empty($emissao->status_notificacao)) {
    ?>
        <script>alert(`<?=$emissao->status_notificacao?>`);</script>
    <?php
}
echo "<script>window.location.href = '{$emissao->guia_pdf}'</script>";
