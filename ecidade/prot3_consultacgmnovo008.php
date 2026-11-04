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
require_once(modification("model/CgmFactory.model.php"));

$oGet = db_utils::postMemory($_GET, false);
?>
<html>
<head>
    <title>Dados do Cadastro de Veículos</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <style type='text/css'>
        .valores {
            background-color: #FFFFFF;
            width: 35%;
        }
    </style>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<?php
try {
    if (empty($oGet->cgm)) {
        throw new \Exception("Erro não foi encontrado codigo cgm");
    }

    $oCgm = CgmFactory::getInstance('', $oGet->cgm);

    $eauth = new \ECidade\Lib\Request\EAuth\EAuth();

    if ($oCgm instanceof CgmFisico) {
        $result = $eauth->consultaUserCpf($oCgm->getCpf());
    }

    if ($oCgm instanceof CgmJuridico) {
        $result = $eauth->consultaUserCpf($oCgm->getCnpj());
    }

    if (!$result->success) {
        echo "<h1 style='text-align: center;margin-top: 30px'>Não possui dados registrados no Eauth</h1>";
        return;
    }

} catch (\Exception $ex) {
    echo "<h1 style='color:red;text-align: center;margin-top: 30px'>{$ex->getMessage()}</h1>";
    return;
}
?>
<table>
    <tr>
        <td>
            <strong>Nome: </strong>
        </td>
        <td class="valores">
            <?= $result->data['name']; ?>
        </td>
        <td>
            <strong>Email: </strong>
        </td>
        <td class="valores">
            <?= $result->data['email']; ?>
        </td>
    </tr>
    <tr>
        <td>
            <strong>Registrado em: </strong>
        </td>
        <td class="valores">
            <?=
            $result->data['createdAt'] ?
                \Carbon\Carbon::parse($result->data['createdAt'])->format("d/m/Y H:i:s")
                : "-";
            ?>
        </td>
        <td>
            <strong>Última Alteração: </strong>
        </td>
        <td class="valores">
            <?=
            $result->data['updatedAt'] ?
                \Carbon\Carbon::parse($result->data['updatedAt'])->format("d/m/Y H:i:s")
                :
                "-"
            ?>
        </td>
    </tr>
</table>
</body>
</html>
