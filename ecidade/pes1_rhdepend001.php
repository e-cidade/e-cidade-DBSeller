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

use ECidade\RecursosHumanos\ESocial\Repository\ServidorAlteracao;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use ECidade\RecursosHumanos\ESocial\Transformer\S2405;
use ECidade\RecursosHumanos\ESocial\Transformer\S2205;

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_usuariosonline.php');
require_once modification('classes/db_rhdepend_classe.php');
require_once modification('classes/db_rhpessoal_classe.php');
require_once modification('classes/db_rhdependeplug_classe.php');
require_once modification('dbforms/db_funcoes.php');

db_postmemory($HTTP_GET_VARS);
db_postmemory($HTTP_POST_VARS);
$clrhdepend = new cl_rhdepend;
$clrhpessoal = new cl_rhpessoal;
$clrhdependeplug = new cl_rhdependeplug;
$db_opcao = 1;
$db_botao = true;

try {
    if (isset($incluir) || isset($alterar)) {
        $nome = empty($rh31_nome) ? '' : trim($rh31_nome);

        if (DBString::comprimento($nome) > 70) {
            throw new Exception('O "Nome do Dependente" não pode ser maior que 70 caracteres.');
        }

        $irf = empty($rh31_irf) ? '' : trim($rh31_irf);
        $cpf = empty($dp01_cpf) ? '' : trim($dp01_cpf);
        if ($rh31_irf && empty($cpf)) {
            throw new Exception('O "CPF" é obrigatório quando o campo "IRF" está informado.');
        }

        $clrhdepend->rh31_nome = $nome;
    }

    if (isset($incluir)) {
        db_inicio_transacao();

        $sqlerro = false;

        $clrhdepend->rh31_regist = $rh31_regist;
        $clrhdepend->rh31_nome = $rh31_nome;
        $clrhdepend->rh31_dtnasc = $rh31_dtnasc;
        $clrhdepend->rh31_gparen = $rh31_gparen;
        $clrhdepend->rh31_depend = $rh31_depend;
        $clrhdepend->rh31_irf = $rh31_irf;
        $clrhdepend->rh31_especi = $rh31_especi;
        $clrhdepend->rh31_tipoparentesco = $rh31_tipoparentesco;
        $clrhdepend->rh31_fins_previdenciarios = $rh31_fins_previdenciarios == 't'?'t':'f';
        $clrhdepend->rh31_descricaoparentesco = $rh31_descricaoparentesco;
        $clrhdepend->rh31_irfcomplementar = $rh31_irfcomplementar == 't'?'t':'f';
         

        (new S2405($rh31_regist))->salvarDataAlteracao();

        $clrhdepend->incluir(null);
        if ($clrhdepend->erro_status == "0") {
            $erro_msg = $clrhdepend->erro_msg;
            $sqlerro = true;
        }
        if ($sqlerro == false) {
            $clrhdependeplug->dp01_rhdepend = $clrhdepend->rh31_codigo;
            $clrhdependeplug->dp01_regist = $rh31_regist;
            $clrhdependeplug->dp01_instit = db_getsession('DB_instit');
            $clrhdependeplug->dp01_processo = 'null';
            $clrhdependeplug->dp01_cpf = $dp01_cpf;
            $clrhdependeplug->dp01_sexo = $dp01_sexo;
            $clrhdependeplug->incluir();
            if ($clrhdependeplug->erro_status == "0") {
                $erro_msg = $clrhdependeplug->erro_msg;
                $sqlerro = true;
            }
        }

        db_fim_transacao($sqlerro);

        /* INICIO VALIDAÇÃO PARA E-SOCIAL */
        alteracaoS2205($clrhdepend->rh31_regist, $clrhdepend->rh31_dtnasc, $clrhdepend->rh31_codigo, $clrhdepend, $sqlerro, $erro_msg);
        /* FINAL VALIDAÇÃO PARA E-SOCIAL */ 
               
    } else {
        if (isset($alterar)) {
            db_inicio_transacao();
            $sqlerro = false;

            /* INICIO VALIDAÇÃO PARA E-SOCIAL */
            alteracaoS2205($rh31_regist, $rh31_dtnasc, $rh31_codigo, $clrhdepend, $sqlerro, $erro_msg);
            /* FINAL VALIDAÇÃO PARA E-SOCIAL */

            $clrhdepend->rh31_regist = $rh31_regist;
            $clrhdepend->rh31_nome = $rh31_nome;
            $clrhdepend->rh31_dtnasc = $rh31_dtnasc;
            $clrhdepend->rh31_gparen = $rh31_gparen;
            $clrhdepend->rh31_depend = $rh31_depend;
            $clrhdepend->rh31_irf = $rh31_irf;
            $clrhdepend->rh31_especi = $rh31_especi;
            $clrhdepend->rh31_tipoparentesco = $rh31_tipoparentesco;
            $clrhdepend->rh31_fins_previdenciarios = $rh31_fins_previdenciarios == 't'? 't':'f';
            $clrhdepend->rh31_descricaoparentesco = $rh31_descricaoparentesco;
            $clrhdepend->rh31_irfcomplementar = $rh31_irfcomplementar == 't'? 't':'f';
            

            /**
             * Seta a dtAlteração para o S2405.
             */
            $where = "rh31_codigo = {$rh31_codigo}";
            (new S2405($rh31_regist))->validarS2405('rhdepend', $clrhdepend, $where);

            $clrhdepend->alterar($rh31_codigo);
            if ($clrhdepend->erro_status == "0") {
                $erro_msg = $clrhdepend->erro_msg;
                $sqlerro = true;
                $opcao = "alterar";
            }
            if ($sqlerro == false) {
                $clrhdependeplug->dp01_rhdepend = $rh31_codigo;
                $clrhdependeplug->dp01_regist = $rh31_regist;
                $clrhdependeplug->dp01_instit = db_getsession('DB_instit');
                $clrhdependeplug->dp01_processo = 'null';
                $clrhdependeplug->dp01_cpf = $dp01_cpf;
                $clrhdependeplug->dp01_sexo = $dp01_sexo;

                $sqlDependplug = "select * from rhdependeplug where dp01_regist = " . $rh31_regist . " and dp01_rhdepend =" . $rh31_codigo;
                $proc = db_query($sqlDependplug);
                $proclinhas = pg_num_rows($proc);
                
                // Valida a dtAlteração de acordo com os campos extra do dependente.
                (new S2405($rh31_regist))->validarS2405('rhdependplug', $clrhdependeplug, null, $sqlDependplug);

                if ($proclinhas == 0) {
                    $clrhdependeplug->incluir();
                } else {
                    $clrhdependeplug->alterar();
                }
                if ($clrhdependeplug->erro_status == "0") {
                    $erro_msg = $clrhdependeplug->erro_msg;
                    $sqlerro = true;
                    $opcao = "alterar";
                }
            }
            db_fim_transacao($sqlerro);
        } else {
            if (isset($excluir)) {
                db_inicio_transacao();
                $sqlerro = false;

                $where = "dp01_rhdepend = {$rh31_codigo} AND dp01_regist = {$rh31_regist}";

                (new S2405($rh31_regist))->salvarDataAlteracao();
                $clrhdependeplug->excluir(null, $where);
                if ($clrhdependeplug->erro_status == "0") {
                    $erro_msg = "Erro ao excluir as informações do dependente.";
                    $sqlerro = true;
                }

                if ($sqlerro == false) {
                    $clrhdepend->excluir($rh31_codigo);
                    if ($clrhdepend->erro_status == "0") {
                        $erro_msg = $clrhdepend->erro_msg;
                        $sqlerro = true;
                        $opcao = "excluir";
                    }
                }

                db_fim_transacao($sqlerro);
            }
        }
    }
} catch (Exception $exception) {
    $erro_msg = $exception->getMessage();
    $sqlerro = true;
    db_fim_transacao($sqlerro);
}

if ((isset($alterar) || isset($excluir) || isset($incluir)) && $sqlerro == false) {
    unset($opcao);
    $rh31_codigo = "";
    $rh31_nome = "";
    $rh31_dtnasc = "";
    $rh31_dtnasc_dia = "";
    $rh31_dtnasc_mes = "";
    $rh31_dtnasc_ano = "";
    $rh31_gparen = "";
    $rh31_depend = "";
    $rh31_irf = "";
    $rh31_especi = "";
    $p58_numero = "";
    $z01_nome1 = "";
    $p58_codproc = "";
    $processo = "";
    $dp01_cpf = "";
    $dp01_sexo = "";
    $rh31_tipoparentesco="";
}

if (isset($opcao)) {
    if ($opcao == "alterar") {
        $db_opcao = 2;
    } else {
        $db_opcao = 3;
    }
    // echo("<BR><BR>".$clrhdepend->sql_query_cgm($rh31_codigo,"rh31_codigo,rh31_regist,z01_nome,rh31_nome,rh31_dtnasc,rh31_gparen,rh31_depend,rh31_irf,rh31_especi"));
    $result_dados = $clrhdepend->sql_record($clrhdepend->sql_query_cgm($rh31_codigo, "rhdepend.*,cgm.z01_nome"));
    if ($clrhdepend->numrows > 0) {
        db_fieldsmemory($result_dados, 0);
    }
    $proc = db_query("select * from rhdependeplug where dp01_regist = " . $rh31_regist . " and dp01_rhdepend =" . $rh31_codigo);
    $proclinhas = pg_num_rows($proc);
    if ($proclinhas > 0) {
        db_fieldsmemory($proc, 0);
    } else {
        $p58_numero = "";
        $z01_nome1 = "";
        $p58_codproc = "";
        $processo = "";
    }

} else {
    if (isset($rh31_regist) && trim($rh31_regist) != "") {
        $result_nome = $clrhpessoal->sql_record($clrhpessoal->sql_query_cgm($rh31_regist, "z01_nome"));
        if ($clrhpessoal->numrows > 0) {
            db_fieldsmemory($result_nome, 0);
        }
    }
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
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1">
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#cccccc">
    <tr>
        <td width="360" height="18">&nbsp;</td>
        <td width="263">&nbsp;</td>
        <td width="25">&nbsp;</td>
        <td width="140">&nbsp;</td>
    </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td height="430" align="center" valign="top" bgcolor="#CCCCCC">
            <center>
                <?php
                require_once modification('forms/db_frmrhdepend.php');
                ?>
            </center>
        </td>
    </tr>
</table>
<?php
if (!$_GET["vmenu"]) {
    db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"),
        db_getsession("DB_anousu"), db_getsession("DB_instit"));
}
?>
</body>
</html>
<?php
if (isset($incluir) || isset($alterar) || isset($excluir)) {
    if ($sqlerro == true) {
        db_msgbox($erro_msg);
        echo "<script> document.form1." . $clrhdepend->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
        echo "<script> document.form1." . $clrhdepend->erro_campo . ".focus();</script>";
    }
}

function alteracaoS2205($rh31_regist, $rh31_dtnasc, $rh31_codigo, &$clrhdepend, &$sqlerro, &$erro_msg) {

    $nasc = explode('/', $rh31_dtnasc);
    $dtmin = strtotime('1890-01-01');
    $dtnasc = strtotime($nasc[2] . '-' . $nasc[1] . '-' . $nasc[0]);

    if ($dtnasc < $dtmin) {
        $sqlerro = true;
        $erro_msg = "Data de nascimento inválida";
    }

    $sqlDepend = $clrhdepend->sql_query(null, '*', null, "rh31_codigo = {$rh31_codigo}");

    $result = $clrhdepend->sql_record($sqlDepend);
    $dadosAtuais = pg_fetch_object($result,0);

    // Registra alteração para envio do formulário S2205
    foreach(S2205::getCamposControleAlteracao() as $campo){
        if(isset($$campo)){
            if(isset($dadosAtuais->$campo)){
                if( $dadosAtuais->$campo != $$campo){
                    $servidorAlteracao = ServidorAlteracao::findMatriculaByLayout($rh31_regist, Tipo::S2205);
                    $servidorAlteracao->setDataS2205(new DBDate(date('Y-m-d')));
                    $servidorAlteracao->save();

                    break;
                }
            }
        }
    }
}
