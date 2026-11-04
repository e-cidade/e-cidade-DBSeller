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

require modification("libs/db_stdlib.php");
require modification("libs/db_conecta.php");
include modification("libs/db_sessoes.php");
include modification("libs/db_usuariosonline.php");
include modification("dbforms/db_funcoes.php");
include modification("classes/db_rhpessoal_classe.php");
include modification("classes/db_rhpessoalmov_classe.php");
include modification("classes/db_padroes_classe.php");
include modification("classes/db_pesdiver_classe.php");
include modification("dbforms/db_classesgenericas.php");

use ECidade\RecursosHumanos\ESocial\Repository\ServidorAlteracao;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;

ini_set('error_reporting', E_ALL);

$daoSalarioEsocial = new \cl_rhreajustesalarialesocial;
$clpadroes         = new cl_padroes;
$clrhpessoal       = new cl_rhpessoal;
$clrhpessoalmov    = new cl_rhpessoalmov;
$clprogress        = new cl_progress;
$clpesdiver        = new cl_pesdiver;
$aux               = new cl_arquivo_auxiliar;
$clrotulo          = new rotulocampo;

db_postmemory($HTTP_POST_VARS);

if (isset($incluir)) {
    db_inicio_transacao();

    $sqlerro = false;
    $dbwhere = " r24_anousu = " . $anofolha . " and r24_mesusu = " . $mesfolha . " and r24_instit = " . db_getsession("DB_instit") . " ";

    $contador = 0;
    $result_progressao = $clprogress->sql_record($clprogress->sql_query_padrao(null, null, null, null, null, " distinct r24_padrao as pad, r24_regime as reg, r24_meses as ms, r24_valor", "", $dbwhere));
    $numrows_progressao = $clprogress->numrows;

    if ($numrows_progressao == 0) {
        $erro_msg = "Nenhum registro encontrado.";
        $sqlerro = true;
    } else {
        for ($i = 0; $i < $numrows_progressao; $i++) {
            db_fieldsmemory($result_progressao, $i);

            $r24_valor += ($r24_valor * ($rh02_salari / 100));
            $valorprogressao = "round($r24_valor, 2)";

            $clprogress->r24_anousu = $anofolha;
            $clprogress->r24_mesusu = $mesfolha;
            $clprogress->r24_regime = $reg;
            $clprogress->r24_padrao = $pad;
            $clprogress->r24_meses = $ms;
            $clprogress->r24_valor  = $valorprogressao;
            $clprogress->r24_instit = db_getsession('DB_instit');
            $clprogress->alterar($anofolha, $mesfolha, $reg, $pad, $ms, db_getsession('DB_instit'));

            $erro_msg = $clprogress->erro_msg;

            if ($clprogress->erro_status == "0") {
                $sqlerro = true;
            }

            $sqlListaMatriculas = $clpadroes->sqlReajustePadraoByCompetencia($pad, $anofolha, $mesfolha, db_getsession('DB_instit'));
            $resultadoListaMatriculas = $clpadroes->sql_record($sqlListaMatriculas);
            
            if ($clpadroes->numrows > 0) {
                for ($z = 0; $z < $clpadroes->numrows; $z++) {
                    db_fieldsmemory($resultadoListaMatriculas, $z);
                    /**
                     * Inclui dados do reajuste salarial padrão para o eSocial.
                     */
                    $camposReajuste = [
                        'eso39_sequencial',
                        'eso39_matricula',
                        'eso39_dataefeito',
                        'eso39_tipo',
                        'eso39_descricao'
                    ];
                    $sqlVerificaReajuste = $daoSalarioEsocial->sql_query_file(
                        null,
                        implode(', ', $camposReajuste),
                        null,
                        "eso39_matricula = {$rh02_regist} and eso39_dataefeito = '{$eso39_dataefeito}' and eso39_tipo = '{$eso39_tipo}' "
                    );
                    $rsVerificaReajuste = $daoSalarioEsocial->sql_record($sqlVerificaReajuste);

                    if ($daoSalarioEsocial->numrows == 0) {
                        $daoSalarioEsocial->eso39_matricula = $rh02_regist;
                        $daoSalarioEsocial->eso39_dataefeito = $eso39_dataefeito;
                        $daoSalarioEsocial->eso39_tipo = $eso39_tipo;
                        $daoSalarioEsocial->eso39_descricao = $eso39_descricao;
                        $daoSalarioEsocial->incluir(null);

                        $servidorAlteracao = ServidorAlteracao::findMatriculaByLayout($rh02_regist, Tipo::S2206);
                        $servidorAlteracao->setDataS2206(new DBDate($eso39_dataefeito));
                        $servidorAlteracao->save();
                    }
                }
              }
            }
  
        }
    }
    db_fim_transacao($sqlerro);
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

<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" bgcolor="#cccccc">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
        <tr>
            <td width="360" height="18">&nbsp;</td>
            <td width="263">&nbsp;</td>
            <td width="25">&nbsp;</td>
            <td width="140">&nbsp;</td>
        </tr>
    </table>
    <tr>
        <td>
            <?php
            include modification("forms/db_frmreajusteprog.php");
            ?>
        </td>
    </tr>
    </table>
    <?php
    db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit"));
    ?>
</body>

</html>
<script>
    js_setfocus(true);
</script>
<?php
if (isset($incluir)) {
    db_msgbox($erro_msg);
}
?>
