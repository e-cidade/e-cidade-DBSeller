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
require_once(modification("libs/db_libdicionario.php"));
require_once(modification("dbforms/db_funcoes.php"));

db_postmemory($_POST);

$sNomeArquivo  = "config/apropriacao_automatica_retencao.txt";
$sLogin  = db_getsession('DB_login');
$sData   = date('d/m/Y H:i:s',db_getsession('DB_datausu'));
$sIP     = db_getsession('DB_ip');
$disable = "";
$sTextoAtivado = "";

if (file_exists($sNomeArquivo)){
    $sTextoAtivado = file_get_contents($sNomeArquivo);
    $disable = "disabled";
}

if ( isset($_POST["ativar"]) ) {

    $sTextoAtivado = "Ativado pelo usuário <b>{$sLogin}</b> dia <b>{$sData}</b>. Endereço de IP: <b>{$sIP}</b>";
    file_put_contents($sNomeArquivo, $sTextoAtivado);
    $disable = "disabled";
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
    <body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
    <table width="790" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
        <tr>
            <td width="360" height="18">&nbsp;</td>
            <td width="263">&nbsp;</td>
            <td width="25">&nbsp;</td>
            <td width="140">&nbsp;</td>
        </tr>
    </table>
    <br><br><br><br>
    <center>
        <form name="form1" method="post" action="">
            <fieldset style="width: 30%">
                <legend><b>Ativação de apropriação automática de retenções<b></legend>
                <table border="0">
                    <tr>
                        <td>
                            <p><?php echo $sTextoAtivado; ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="submit" id="ativar" name="ativar" value="Ativar" onclick="return js_ativar()" <?php echo $disable?>>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
    </center>
    <?php
    db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
    ?>
    </body>
    <script>
        function js_ativar(){

          var sConfirmacao = "Você realmente deseja ativar a apropriação automática de retenções? Uma vez ativado, este processo não pode ser revertido. Clique em OK para ativar ou cancelar caso não queira ativar agora.";
          if ( confirm(sConfirmacao) ) {
            return true;
          }
          return false;
        }
    </script>

    </html>
