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

use ECidade\Financeiro\Orcamento\Service\RecursoService;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_liborcamento.php"));
require_once(modification("dbforms/db_funcoes.php"));

$clempautitem = new cl_empautitem;
$e56_orctiporec = "null";
parse_str($_SERVER['QUERY_STRING']);
db_postmemory($_POST);
$clorcdotacao = new cl_orcdotacao;
$clempautidot = new cl_empautidot;
$clorcsuplemval = new cl_orcsuplemval;
$clorcreserva = new cl_orcreserva;
$clorcreservaaut = new cl_orcreservaaut;
if (empty($db_opcao)) {
    $db_opcao = 1;
    $db_botao = true;
} else {
    $db_opcao = 33;
    $db_botao = false;
}

$mostrarLinhaPacto = FONTE_RECURSO_UNIAO ? '' : "display: none";
if (isset($confirmar)) {
    try {
        db_inicio_transacao();
        $sql = "update empparametro set e39_anousu = e39_anousu where e39_anousu = " . db_getsession("DB_anousu");
        $res = db_query($sql);

        $sqlerro = false;

        //rotina para verificar o saldo
        $result = $clempautitem->sql_record($clempautitem->sql_query_file($e56_autori, null, "sum(e55_vltot) as e54_valor"));
        db_fieldsmemory($result, 0);

        /* [Extensão] Programação Financeira */

        //===================================================>>
        //*******rotina que verifica sem ainda existe saldo disponivel******************//
        //rotina para calcular o saldo final
        $result = db_dotacaosaldo(8, 2, 2, "true", "o58_coddot=$o47_coddot", db_getsession("DB_anousu"));
        db_fieldsmemory($result, 0);

        $tot = ((0 + $atual_menos_reservado) - (0 + $e54_valor));
        if ($tot < 0) {
            $sqlerro = true;
            $clempautidot->erro_status = 0;
            $clempautidot->erro_msg = "Dotação sem saldo disponível. Verifique!";
        }
        //fim
        //====================================================================================================>>

        if ($sqlerro == false) {
            $result = $clempautidot->sql_record($clempautidot->sql_query_file($e56_autori));
            $clempautidot->e56_coddot = $o47_coddot;
            $numrows_verifica = $clempautidot->numrows;
            if ($numrows_verifica > 0) {
                if ($e56_orctiporec == 0) {
                    $clempautidot->e56_orctiporec = "null";
                }
                $clempautidot->alterar($e56_autori);
                if ($clempautidot->erro_status == 0) {
                    $sqlerro = true;
                }
            } else {
                if ($e56_orctiporec == 0) {
                    $clempautidot->e56_orctiporec = "null";
                }
                $clempautidot->incluir($e56_autori);
                if ($clempautidot->erro_status == 0) {
                    $sqlerro = true;
                }
            }
        }

        $msg_liberacao = false;
        $fazerReservaSaldo = true;
        $daoAndamentoEmppreAutorizacao  = new cl_andamentoemppreautorizacao;
        if ($sqlerro == false) {
            $daoEmppreAutUnidade = new cl_emppreautorizacaounidade;
            if ($daoEmppreAutUnidade->habilitadoParaLiberacaoAutorizacao($e56_autori)) {
                $fazerReservaSaldo = false;

                if (!$daoAndamentoEmppreAutorizacao->existeAndamentoAutorizacao($e56_autori)) {
                    $msg_liberacao = true;
                    $daoAndEmppreAut = new cl_andamentoemppreautorizacao;
                    $daoAndEmppreAut->empautoriza_id  = $e56_autori;
                    $daoAndEmppreAut->status_id  = '1';
                    $daoAndEmppreAut->observacao  = '';
                    $daoAndEmppreAut->id_usuario  = db_getsession('DB_id_usuario');
                    $daoAndEmppreAut->data = date("Y/m/d");
                    $daoAndEmppreAut->incluir();

                    if ($daoAndEmppreAut->erro_status == '0') {
                        throw new Exception($daoAndEmppreAut->erro_msg);
                    }
                }
            } else {
                $ultimoStatus = $daoAndamentoEmppreAutorizacao->ultimoStatusAndamentoAutorizacao($e56_autori);

                if (!empty($ultimoStatus)) {
                    $observacao = "Fechada por motivos de troca de dotacao ";
                    $observacao .= "que não está liberada para autorização de empenho";

                    //Status autorizado
                    $daoAndamentoEmppreAutorizacao->empautoriza_id  = $e56_autori;
                    $daoAndamentoEmppreAutorizacao->status_id  = '3';
                    $daoAndamentoEmppreAutorizacao->observacao  = $observacao;
                    $daoAndamentoEmppreAutorizacao->id_usuario  = db_getsession('DB_id_usuario');
                    $daoAndamentoEmppreAutorizacao->data = date("Y/m/d");

                    $daoAndamentoEmppreAutorizacao->incluir();
                }

                $daoEmpAutAutorizada = new cl_empautorizacaoautorizada;

                $sqlVerifica = $daoEmpAutAutorizada->sql_query_file(null,1,null,"empautoriza_id = {$e56_autori}");
                $rsVerifica = $daoEmpAutAutorizada->sql_record( $sqlVerifica);
                if ($daoEmpAutAutorizada->numrows <= 0) {

                    $daoEmpAutAutorizada->empautoriza_id = $e56_autori;
                    $daoEmpAutAutorizada->incluir();
                    if ($daoEmpAutAutorizada->erro_status == '0') {
                        throw new Exception($daoEmpAutAutorizada->erro_msg);
                    }
                }
            }
        }

        //rotina que inclui na tabela orcreserva
        if ($sqlerro == false && $fazerReservaSaldo == true) {
            $result = $clorcreservaaut->sql_record($clorcreservaaut->sql_query_file(
                null,
                "o83_codres as o80_codres",
                "",
                "o83_autori=$e56_autori"
            ));
            $numrows_verifica = $clorcreservaaut->numrows;
            if ($numrows_verifica > 0) {
                db_fieldsmemory($result, 0);
            }

            $clorcreserva->o80_anousu = db_getsession("DB_anousu");
            $clorcreserva->o80_coddot = $o47_coddot;
            $clorcreserva->o80_dtfim = date('Y', db_getsession('DB_datausu')) . "-12-31";
            $clorcreserva->o80_dtini = date('Y-m-d', db_getsession('DB_datausu'));
            $clorcreserva->o80_dtlanc = date('Y-m-d', db_getsession('DB_datausu'));
            $clorcreserva->o80_valor = $e54_valor;
            $clorcreserva->o80_descr = "Reserva da autorização $e56_autori";

            if ($numrows_verifica > 0) {
                $clorcreserva->o80_codres = $o80_codres;
                $clorcreserva->alterar($o80_codres);
            } else {
                $o80_codres = '';
                $clorcreserva->incluir($o80_codres);
                $o80_codres = $clorcreserva->o80_codres;
            }
            if ($clorcreserva->erro_status == 0) {
                $sqlerro = true;
            }
        }
        //fim
        //rotina da tabela orcreservaaut
        if ($sqlerro == false && $fazerReservaSaldo == true) {
            if ($numrows_verifica == 0) {
                $clorcreservaaut->o83_codres = $o80_codres;
                $clorcreservaaut->o83_autori = $e56_autori;
                $clorcreservaaut->incluir($o80_codres);
                if ($clorcreservaaut->erro_status == 0) {
                    $sqlerro = true;
                }
            }
        }
        //fim
        if ($complemento != '') {
            $dotacao = DotacaoRepository::getDotacaoPorCodigoAno($o47_coddot, db_getsession("DB_anousu"));
            $lancar = new \ECidade\Financeiro\Orcamento\Service\LancarComlementoService();
            $lancar->complementoAutorizacao($dotacao, $e56_autori, $complemento);
        }

        if ($msg_liberacao) {
            $msg = "A autorização já encontra- se em análise para liberação pelo setor responsável.";
            $msg .= " Acompanhe o status da sua autorizacao na rotina de Consultar Autorizações";
            db_msgbox($msg);
        }

        db_fim_transacao($sqlerro);
    } catch (Exception $e) {
        $clempautidot->erro_status = "0";
        $clempautidot->erro_msg = $e->getMessage();
        $sqlerro = false;
        db_fim_transacao(true);
    }

    $db_botao_c = true;
} elseif (isset($cancelar)) {
    $sqlerro = false;
    db_inicio_transacao();

    $result = $clorcreservaaut->sql_record($clorcreservaaut->sql_query_file(null, "o83_codres", "", "o83_autori=$e56_autori"));
    if ($clorcreservaaut->numrows > 0) {
        db_fieldsmemory($result, 0);
        //rotina exclusao da tabela
        $clorcreservaaut->o83_codres = $o83_codres;
        $clorcreservaaut->excluir($o83_codres);
        if ($clorcreservaaut->erro_status == 0) {
            $sqlerro = true;
        }
        //fim
        //rotina pra excusão da tabela orcreserva
        $clorcreserva->o80_codres = $o83_codres;
        $clorcreserva->excluir($o83_codres);
        if ($clorcreserva->erro_status == 0) {
            $sqlerro = true;
        }
    }
    $clempautidot->e56_autori = $e56_autori;
    $clempautidot->excluir($e56_autori);

    if ($clempautidot->erro_status == 0) {
        $sqlerro = true;
    }

    $db_botao_c = false;
    db_fim_transacao($sqlerro);
} else {
    $result = $clempautidot->sql_record($clempautidot->sql_query_linhapacto($e56_autori));

    if ($clempautidot->numrows > 0) {
        //    só passará se nao tiver sido clicado em processar
        if (empty($pesquisa_dot)) {
            db_fieldsmemory($result, 0);
            $o47_coddot = $e56_coddot;
        }
        if (empty($anulacao)) {
            $result_orcreservaaut = $clorcreservaaut->sql_record($clorcreservaaut->sql_query_file(null, "*", "", "o83_autori=$e56_autori"));
            if ($clorcreservaaut->numrows > 0) {
                echo "
           <script>
		(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_empautitem.location.href='emp1_empautitem001.php?db_opcaoal=3&e55_autori=$e56_autori';\n
		parent.document.formaba.empautret.disabled=false;
	   </script>
          ";
            }
        }
        if (isset($anulacao)) {//quando tiver sido anulado
            $db_botao_c = false;
        } else {
            $db_botao_c = true;
        }
    } else {
        $db_botao_c = false;
    }
}

if (!empty($o156_orcdotacaoplanoorcamentario)) {
    $planoorcamento = $o156_orcdotacaoplanoorcamentario;
}

?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1">
<div class="container">
    <?php
    include(modification("forms/db_frmempautidot.php"));
    ?>
</div>
</body>
</html>
<?php
if (isset($e54_valor)) {
    $e54_valor = str_replace(".", "", $e54_valor);
    $e54_valor = str_replace(",", ".", $e54_valor);
}
if (isset($confirmar) || isset($cancelar)) {
    if ($clempautidot->erro_status == "0") {
        $clempautidot->erro(true, false);
        $db_botao = true;
        if ($clempautidot->erro_campo != "") {
            echo "<script> document.form1." . $clempautidot->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
            echo "<script> document.form1." . $clempautidot->erro_campo . ".focus();</script>";
        };
    } else {
        if (isset($confirmar)) {
            echo "
           <script>
		(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_empautitem.location.href='emp1_empautitem001.php?db_opcaoal=3&e55_autori=$e56_autori';\n
		parent.document.formaba.empautret.disabled=false;
                (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_empautret.setValorNota($e54_valor);
	   </script>
          ";
        } else {
            echo "
           <script>
	        document.form1.atual.value='';
	        document.form1.reservado.value='';
	        document.form1.atudo.value='';
	        document.form1.atudo.value='';
	        document.form1.e54_valor.value='';
	        document.form1.atudo.value='';
		(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_empautitem.location.href='emp1_empautitem001.php?e55_autori=$e56_autori';\n
		parent.document.formaba.empautret.disabled=true;
                (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_empautret.setValorNota($e54_valor);
	   </script>
          ";
        }
        //$clempautidot->erro(true,false);
        //  db_redireciona("emp1_empautidot001.php?e56_autori=$e56_autori");
    };
} else {
    if (isset($e54_valor)) {
        echo "<script>(window.CurrentWindow || parent.CurrentWindow).corpo.iframe_empautret.setValorNota($e54_valor);</script>";
    }
}
if (!isset($o47_coddot) || (isset($o47_coddot) && trim($o47_coddot) == "")) {
    echo "<script>
          js_pesquisao47_coddot(true);
        </script>";
}
echo "<script>
          var time;
          function js_seleciona_campo_confirma(){
            document.form1.confirmar.focus();
            clearInterval(time);
          }
          // time = setTimeout(js_seleciona_campo_confirma,10);
         time = setInterval(js_seleciona_campo_confirma,10);
	    </script>";
?>

