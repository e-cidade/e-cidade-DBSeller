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

use App\Domain\Configuracao\DocumentosTemplate\Reports\Cadastro\CertidaoForoELaudemio;

$matricula = $_GET['matricula'];

$oDaoCertForVal = new cl_certforlaud();

$sSqlUltimaCertidaoGerada = $oDaoCertForVal->sql_ultimaCertidao($matricula);
$rsUltimaCertidaoGerada   = db_query($sSqlUltimaCertidaoGerada);
$aUltimaCertidaoGerada    = (array) db_utils::fieldsMemory($rsUltimaCertidaoGerada, 0);

try {
    $oTemplateCertidaoForoELaudemio = new CertidaoForoELaudemio(
        $aUltimaCertidaoGerada['j178_sequencial']
    );

    $oTemplateCertidaoForoELaudemio->configuraDadosVariaveis();
    $pathDocumento = $oTemplateCertidaoForoELaudemio->processaTemplate(false);

    echo "<script> window.open('{$pathDocumento}','_blank'); </script>";

} catch (Exception $error) {
    echo "<script>alert('" . $error->getMessage() . "');</script>";
}

echo "<script>location.href = 'cad3_conscertidoes001.php?abaSelecionada=cad3_conscertforlaud001.php&matricula=" . $matricula . "';</script>";
