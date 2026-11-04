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
require_once(modification('dbforms/db_funcoes.php'));
require_once(modification('libs/db_sessoes.php'));
require_once(modification('libs/db_usuariosonline.php'));
require_once(modification('libs/db_utils.php'));
require_once(modification('std/db_stdClass.php'));
require_once(modification('libs/db_libsys.php'));
require_once(modification('dbagata/classes/core/AgataAPI.class'));
require_once(modification('model/documentoTemplate.model.php'));
require_once(modification("classes/db_obrasalvara_classe.php"));
use App\Domain\Configuracao\DocumentosTemplate\Reports\Projetos\CartaAlvara;

$oGet = db_utils::postMemory($_GET);
$codigo_obra   = $oGet->codigo;
$clobrasalvara = new cl_obrasalvara;

try {

    $sCampos       = "obrasalvara.ob04_ativo";
    $rsObrasAlvara = $clobrasalvara->sql_record($clobrasalvara->sql_query_cartaAlvara($sCampos, $oGet->codigo));
	db_fieldsmemory($rsObrasAlvara, 0, true);

    if ($ob04_ativo == 'f') {
        $oParms = new stdClass();
        $oParms->iCodigo = $oGet->codigo;
        $sMsg = _M('tributario.projetos.pro2_execobra002.alvara_cancelado', $oParms);
        db_redireciona("db_erros.php?fechar=true&db_erro={$sMsg}");
        exit;
    }
    
    $oDocumentoTemplateCartaAlvara = new CartaAlvara($codigo_obra);
    $oDocumentoTemplateCartaAlvara->configuraDadosVariaveis();
    $pathDocumento                 = $oDocumentoTemplateCartaAlvara->processaTemplate();
    db_redireciona($pathDocumento);
} catch (Exception $eException) {
    $sErroMsg  = $eException->getMessage();
    db_redireciona("db_erros.php?fechar=true&db_erro={$sErroMsg}");
}
