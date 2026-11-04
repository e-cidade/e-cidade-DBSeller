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

use PhpParser\Node\Stmt\Break_;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("classes/db_empprestaitem_classe.php"));
require_once(modification("classes/db_emppresta_classe.php"));
require_once(modification("classes/db_empempenho_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_empprestaitemempagemov_classe.php"));
require_once(modification("libs/db_utils.php"));

parse_str($_SERVER["QUERY_STRING"]);
db_postmemory($_POST);

$oGet = db_utils::postMemory($_GET);

$clempprestaitem          = new \cl_empprestaitem;
$clemppresta              = new \cl_emppresta;
$clempempenho             = new \cl_empempenho;
$clempprestaitemempagemov = new \cl_empprestaitemempagemov;
$clempprestaitemdiaria    = new \cl_empprestaitemdiaria;

$db_opcao = 22;
$db_botao = false;

if (isset($alterar) || isset($incluir)) {
    $sqlerro = false;

    if (validaObrigatoriedadeCampos($e46_numemp, $erro_msg)) {
        if (empty($e46_nome) && empty($erro_msg)) {
            $erro_msg = "Nome não informado.";
        }

        if (empty($e46_nota) && empty($erro_msg)) {
            $erro_msg = "Nota fiscal não informada.";
        }

        if (empty($e46_descr) && empty($erro_msg)) {
            $erro_msg = "Descrição não informada.";
        }

        $sqlerro = !empty($erro_msg);
    }
}

if (isset($excluir)) {
    $sqlerro = false;
}

if (isset($incluir)) {
    if (empty($e45_codmov) && $sqlerro == false) {
        $erro_msg = _M("financeiro.empenho.emp1_empprestaitem001.movimento_nao_selecionado");
    } elseif ($sqlerro == false) {
        $sSqlVerificaTotalItem = $clempprestaitem->sql_query_file(null, "coalesce(sum(e46_valor), 0) as valor ", null, "e46_numemp = {$e46_numemp}");

        $rsTotalItens        = $clempprestaitem->sql_record($sSqlVerificaTotalItem);
        $nValorTotalItens    = db_utils::fieldsMemory($rsTotalItens, 0)->valor;
        $oEmpenhooFinanceiro = EmpenhoFinanceiroRepository::getEmpenhoFinanceiroPorNumero($e46_numemp);
        if (($nValorTotalItens + $e46_valor) > $oEmpenhooFinanceiro->getValorEmpenho()) {
            $oDados                 = new stdClass();
            $oDados->nItensLancados = ($nValorTotalItens + $e46_valor);
            $oDados->nValorEmpenho  = $oEmpenhooFinanceiro->getValorEmpenho();

            $sqlerro  = true;
            $erro_msg = _M("financeiro.empenho.emp1_empprestaitem001.valor_prestacao_maior_empenho", $oDados);
        }
        if (!$sqlerro) {
            db_inicio_transacao();

            $clempprestaitem->e46_id_usuario = db_getsession("DB_id_usuario");
            $clempprestaitem->e46_emppresta  = $oGet->e45_sequencial;
            $clempprestaitem->incluir(null);

            $erro_msg = $clempprestaitem->erro_msg;
            if (isset($e446_regist) && !empty($clempprestaitem->e46_codigo) && ($clempprestaitem->erro_status != 0)) {
                switch ($e446_tipodiaria) {
                    case "dentroestado":
                    case "foraestado":
                        $tipodiaria = 1;
                        break;

                    case "forapais":
                        $tipodiaria = 0;
                        break;
                }


                $clempprestaitemdiaria->e446_empprestaitem = $clempprestaitem->e46_codigo;
                $clempprestaitemdiaria->e446_datainicio = $e446_datainicio;
                $clempprestaitemdiaria->e446_datafim = $e446_datafim;
                $clempprestaitemdiaria->e446_motivo = $e446_motivo;
                $clempprestaitemdiaria->e446_quantidade = 1;
                $clempprestaitemdiaria->e446_destino = $e446_destino;
                $clempprestaitemdiaria->e446_movimento = $e45_codmov;
                $clempprestaitemdiaria->e446_regist = $e446_regist;
                $clempprestaitemdiaria->e446_tipodiaria = $tipodiaria;
                $clempprestaitemdiaria->incluir(null);
                $e446_sequencial = null;
                if ($clempprestaitemdiaria->erro_status == 0) {
                    $sqlerro = true;
                    $erro_msg = $clempprestaitemdiaria->erro_msg;
                }
            }
            if ($clempprestaitem->erro_status == 0) {
                $sqlerro = true;
            }
            db_fim_transacao($sqlerro);
        }
    }
} elseif (isset($alterar) && $sqlerro == false) {
    if (empty($e45_codmov)) {
        $erro_msg = _M("financeiro.empenho.emp1_empprestaitem001.movimento_nao_selecionado");
    } elseif ($sqlerro == false) {
        $sSqlVerificaTotalItem = $clempprestaitem->sql_query_file(
            null,
            "coalesce(sum(e46_valor), 0) as valor ",
            null,
            "e46_numemp = {$e46_numemp} and e46_codigo <> {$e46_codigo}"
        );

        $rsTotalItens        = $clempprestaitem->sql_record($sSqlVerificaTotalItem);
        $nValorTotalItens    = db_utils::fieldsMemory($rsTotalItens, 0)->valor;
        $oEmpenhooFinanceiro = EmpenhoFinanceiroRepository::getEmpenhoFinanceiroPorNumero($e46_numemp);
        if (($nValorTotalItens + $e46_valor) > $oEmpenhooFinanceiro->getValorEmpenho()) {
            $sqlerro  = true;
            $erro_msg = _M("financeiro.empenho.emp1_empprestaitem001.valor_prestacao_maior_empenho");
        }
        if (!$sqlerro) {
            db_inicio_transacao();
            $clempprestaitem->e46_emppresta = $oGet->e45_sequencial;
            $clempprestaitem->alterar($e46_codigo);
            $erro_msg = $clempprestaitem->erro_msg;

            if ($clempprestaitem->erro_status == 0) {
                $sqlerro = true;
            }

            if (isset($e446_regist) && !empty($e446_regist) && !empty($clempprestaitem->e46_codigo) && ($clempprestaitem->erro_status != 0)) {
                switch ($e446_tipodiaria) {
                    case "dentroestado":
                    case "foraestado":
                        $tipodiaria = 1;
                        break;

                    case "forapais":
                        $tipodiaria = 0;
                        break;
                }

                $clempprestaitemdiaria->e446_empprestaitem = $clempprestaitem->e46_codigo;
                $clempprestaitemdiaria->e446_datainicio = $e446_datainicio;
                $clempprestaitemdiaria->e446_datafim = $e446_datafim;
                $clempprestaitemdiaria->e446_motivo = $e446_motivo;
                $clempprestaitemdiaria->e446_quantidade = 1;
                $clempprestaitemdiaria->e446_destino = $e446_destino;
                $clempprestaitemdiaria->e446_movimento = $e45_codmov;
                $clempprestaitemdiaria->e446_tipodiaria = $tipodiaria;
                $clempprestaitemdiaria->e446_regist = $e446_regist;
                if (isset($e446_sequencial) && !empty($e446_sequencial)) {
                    $clempprestaitemdiaria->e446_sequencial = $e446_sequencial;
                    $clempprestaitemdiaria->alterar($e446_sequencial);
                    $e446_sequencial = null;
                } else {
                    $clempprestaitemdiaria->incluir(null);
                }

                if ($clempprestaitemdiaria->erro_status == 0) {
                    $sqlerro = true;
                    $erro_msg = $clempprestaitemdiaria->erro_msg;
                }
            }

            db_fim_transacao($sqlerro);
        }
    }
} elseif (isset($excluir)) {
    if ($sqlerro == false) {
        db_inicio_transacao();

        if (isset($e446_sequencial) && !empty($e446_sequencial)) {
            $sql = "delete from empprestaitemdiaria where e446_sequencial = {$e446_sequencial}";
            $rs = db_query($sql);
            if (!$rs) {
                throw new DBException("Erro ao excluir dados de diárias.");
            }
        }

        $clempprestaitem->excluir($e46_codigo);
        $erro_msg = $clempprestaitem->erro_msg;

        if ($clempprestaitem->erro_status == 0) {
            $sqlerro=true;
        }

        db_fim_transacao($sqlerro);
    }
} elseif (isset($opcao)) {
    $result = $clempprestaitem->sql_record($clempprestaitem->sql_query_emp($e46_numemp, $e46_codigo));
    if ($result!=false && $clempprestaitem->numrows>0) {
        db_fieldsmemory($result, 0);
    }
}

/**
 * Valida se deve ou não obrigar informar os campos nome, nota fiscal e descrição,
 * conforme o tipo de evento.
 *
 * @param int $e46_numemp
 * @param string &$erro_msg
 */
function validaObrigatoriedadeCampos($e46_numemp, &$erro_msg)
{
    $obrigarCampos = true;
    if (!empty($e46_numemp)) {
        $empenho = new cl_emppresta();
        $where = "e45_numemp = {$e46_numemp}";
        $sql = $empenho->sql_query_emp(null, 'e44_obriga', null, $where);
        $rs = db_query($sql);

        if (!$rs || pg_num_rows($rs) == 0) {
            $erro_msg = "Erro ao buscar obrigatoriedade do tipo de evento.";
        }
        $obrigarCampos = db_utils::fieldsMemory($rs, 0)->e44_obriga != 2;
    }

    return $obrigarCampos;
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
<body bgcolor=#CCCCCC leftmargin="0" style="margin-top: 30px;" marginwidth="0" marginheight="0" onLoad="a=1" >
<center>
    <?php
    include(modification("forms/db_frmempprestaitem.php"));
    ?>
</center>
</body>
</html>
<?php
if (isset($alterar) || isset($excluir) || isset($incluir)) {
    db_msgbox($erro_msg);

    if ($clempprestaitem->erro_campo != "") {
        echo "<script> document.form1.".$clempprestaitem->erro_campo.".style.backgroundColor='#99A9AE';</script>";
        echo "<script> document.form1.".$clempprestaitem->erro_campo.".focus();</script>";
    }
}
?>
