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
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("classes/db_procandam_classe.php"));
require_once(modification("classes/db_proctransfer_classe.php"));
require_once(modification("classes/db_proctransferproc_classe.php"));
require_once(modification("classes/db_protprocesso_classe.php"));
require_once(modification("classes/db_proctransand_classe.php"));
require_once(modification("classes/db_procarquiv_classe.php"));
require_once(modification("classes/db_arqproc_classe.php"));
require_once(modification("classes/db_arqandam_classe.php"));
require_once(modification("classes/db_ouvidoriaatendimento_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));

db_postmemory($HTTP_SERVER_VARS);
require_once(modification("classes/db_processosapensados_classe.php"));
$clprocessosapensados   = new cl_processosapensados;
db_postmemory($_POST);
db_postmemory($_GET);


$db_opcao = 1;
$db_botao = true;
$sqlerro  = false;

$clprocarquiv = new cl_procarquiv;
$clprotprocesso = new cl_protprocesso;
/**
 * @throws Exception
 */
function arquivamento($p58_codproc, $p67_dtarq, $p67_historico, $grupo)
{
    $sqlerro = false;
    $clprocarquiv           = new cl_procarquiv;
    $clprocandam            = new cl_procandam;
    $clarqproc              = new cl_arqproc;
    $clarqandam             = new cl_arqandam;
    $clproctransfer         = new cl_proctransfer;
    $clproctransand         = new cl_proctransand;
    $clOuvidoriaAtendimento = new cl_ouvidoriaatendimento();
    $clprocessosapensados   = new cl_processosapensados;

    db_inicio_transacao();

    $sSqlProcessosApensados = $clprocessosapensados->sql_query_file(null, "*", null, "p30_procprincipal = $p58_codproc");
    $rsProcessosApensados   = $clprocessosapensados->sql_record($sSqlProcessosApensados);

    if (existeApensadoComDeptoDiferente($p58_codproc)) {
        throw new Exception('Nem todos os apensados a este processo principal encontram-se no mesmo departamento, verifique.');
    }

    $p67_codproc = $p58_codproc;
    $p67_dtarq             = implode("-", array_reverse(explode("/", $p67_dtarq)));

    $oDaoProcAndam         = new cl_protprocesso;
    $campos = "p58_codandam, p61_dtandam, p61_hora, p58_numero";
    $sSqlBuscaProcAndam = $oDaoProcAndam->sql_query_andam($p58_codproc, $campos);
    $rsBuscaProcAndam      = $oDaoProcAndam->sql_record($sSqlBuscaProcAndam);
    $oProcAndam            = db_utils::fieldsMemory($rsBuscaProcAndam, 0);


    $dDataProcAndam        = $oProcAndam->p61_dtandam;
    $dHoraProcAndam        = $oProcAndam->p61_hora;

    $oDaoProcAndamInt      = db_utils::getDao('procandamint');
    $sSqlBuscaProcAndamInt = "select p78_data, p78_hora
                                from procandamint
                               where p78_codandam = {$oProcAndam->p58_codandam}
                               order by p78_sequencial desc limit 1";

    $rsBuscaProcAndamInt   = $oDaoProcAndamInt->sql_record($sSqlBuscaProcAndamInt);

    if ($oDaoProcAndamInt->numrows > 0) {
        $oProcAndamInt       = db_utils::fieldsMemory($rsBuscaProcAndamInt, 0);
        $dDataProcAndam      = $oProcAndamInt->p78_data;
        $dHoraProcAndam      = $oProcAndamInt->p78_hora;
    }

    if ($dDataProcAndam > $p67_dtarq ||
        ( $dDataProcAndam == $p67_dtarq
        && $dHoraProcAndam > db_hora() )) {
        $sqlerro             = true;
        $erro_msg            = "A data para o arquivamento é maior que a data da última movimentação. Verifique a data da sessão!";
        throw new Exception($erro_msg);
    }

    $clprocarquiv->p67_id_usuario = db_getsession("DB_id_usuario");
    $clprocarquiv->p67_coddepto   = db_getsession("DB_coddepto");
    $clprocarquiv->p67_codproc    = $p67_codproc;
    $clprocarquiv->p67_historico  = $p67_historico ?: '';
    $clprocarquiv->incluir(null);

    if ($clprocarquiv->erro_status == 0) {
        $sqlerro  = true;
        throw new Exception($clprocarquiv->erro_msg);
    }

    $clarqproc->incluir($clprocarquiv->p67_codarquiv, $p67_codproc);

    if ($clarqproc->erro_status == 0) {
        $sqlerro  = true;
        throw new Exception($clarqproc->erro_msg);
    }

    if ($clarqproc->erro_status == 0) {
        $sqlerro  = true;
        $erro_msg = $clarqproc->erro_msg;
        throw new Exception($erro_msg);
    } else {
        $clproctransfer->p62_coddepto    = db_getsession("DB_coddepto");
        $clproctransfer->p62_dttran      = $p67_dtarq;
        $clproctransfer->p62_coddeptorec = db_getsession("DB_coddepto");
        $clproctransfer->p62_id_usorec   = db_getsession("DB_id_usuario");
        $clproctransfer->p62_id_usuario  = db_getsession("DB_id_usuario");
        $clproctransfer->p62_hora        = db_hora();

        $clproctransfer->incluir(null);

        if ($clproctransfer->erro_status == 0) {
            $sqlerro  = true;
            $erro_msg = $clproctransfer->erro_msg;
            throw new Exception($erro_msg);
        } else {
            $cod  = $clproctransfer->p62_codtran;

            $sqli = "insert into proctransferproc values($cod,$p67_codproc)";
            $rsi  =  db_query($sqli) or die($sqli);

            if ($clproctransfer->erro_status == "1" or !$rsi) {
                $erro = 0;
            } else {
                $clproctransfer->erro(true, false);
                $sqlerro = true;
                throw new Exception($erro_msg);
            }

            //inclusão do andamento
            $clprocandam->p61_despacho   = $clprocarquiv->p67_historico;
            $clprocandam->p61_dtandam    = $p67_dtarq;
            $clprocandam->p61_hora       = db_hora();
            $clprocandam->p61_codproc    = $p67_codproc;
            $clprocandam->p61_id_usuario = db_getsession("DB_id_usuario");
            $clprocandam->p61_coddepto   = db_getsession("DB_coddepto");
            $clprocandam->p61_publico    = 'true';
            $clprocandam->incluir(null);

            $erro_msg = $clprocandam->erro_msg;
            if ($clprocandam->erro_status==0) {
                $sqlerro=true;
                throw new Exception($erro_msg);
            }
            //  $clprocandam->erro(true,false);

            //inclui  a transferencia. e o andamento do processo na tabela proctransand;
            $clproctransand->p64_codtran  = $clproctransfer->p62_codtran;
            $clproctransand->p64_codandam = $clprocandam->p61_codandam;
            $clproctransand->incluir();

            if ($clproctransand->erro_status == "1") {
                $erro = 0;
            } else {
                $clproctransand->erro(true, false);
                $sqlerro = true;
            }

            if ($sqlerro == false) {
                $clarqandam->p69_codarquiv = $clprocarquiv->p67_codarquiv;
                $clarqandam->p69_codandam  = $clprocandam->p61_codandam;
                $clarqandam->p69_arquivado = 'true';
                $clarqandam->incluir();
                $erro_msg = $clarqandam->erro_msg;

                if ($clarqandam->erro_status==0) {
                    $sqlerro=true;
                    throw new Exception($erro_msg);
                } else {
                    $oDaoProcAndam->p58_codandam = $clarqandam->p69_codandam;
                    $oDaoProcAndam->p58_numero = $oProcAndam->p58_numero;
                    $oDaoProcAndam->alterar($p58_codproc);
                    if ($grupo == 2) {
                        $sWhereAtendimento = " ov09_protprocesso = {$p67_codproc} ";
                        $sSqlAtendimento   = $clOuvidoriaAtendimento->sql_query_proc(null, "distinct ov01_sequencial", null, $sWhereAtendimento);
                        $rsAtendimento     = $clOuvidoriaAtendimento->sql_record($sSqlAtendimento);
                        $iNroAtendimento   = $clOuvidoriaAtendimento->numrows;

                        if ($iNroAtendimento > 0) {
                            for ($iInd=0; $iInd < $iNroAtendimento; $iInd++) {
                                $oAtendimento = db_utils::fieldsMemory($rsAtendimento, $iInd);
                                $clOuvidoriaAtendimento->ov01_sequencial = $oAtendimento->ov01_sequencial;
                                $clOuvidoriaAtendimento->ov01_situacaoouvidoriaatendimento = 3;
                                $clOuvidoriaAtendimento->alterar($oAtendimento->ov01_sequencial);

                                if ($clOuvidoriaAtendimento->erro_status == 0) {
                                    $sqlerro = true;
                                    throw new Exception($erro_msg);
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    $processo = new processoProtocolo($p58_codproc);
    if ($processo->isEletronico()) {
        try {
            notificar($processo);
        } catch (\Exception $ex) {
        }
    }

    $aArquivarProcessos = [];
    for ($iProcessoApensado = 0; $iProcessoApensado < $clprocessosapensados->numrows; $iProcessoApensado++) {
        $oProcessoApensado   = db_utils::fieldsmemory($rsProcessosApensados, $iProcessoApensado);
        $aArquivarProcessos[] = $oProcessoApensado->p30_procapensado;
    }

    foreach ($aArquivarProcessos as $iProcessoApensado) {
        $clprocarquiv->p67_codproc = $iProcessoApensado;
        $clprocarquiv->p67_id_usuario = db_getsession('DB_id_usuario');
        $clprocarquiv->p67_coddepto = db_getsession('DB_coddepto');
        $clprocarquiv->incluir(null);
        if ($clprocarquiv->erro_status == 0) {
            $sqlerro  = true;
            throw new Exception($clprocarquiv->erro_msg);
        }

        $clarqproc->incluir($clprocarquiv->p67_codarquiv, $iProcessoApensado);
        if ($clarqproc->erro_status == 0) {
            $sqlerro  = true;
            throw new Exception($clarqproc->erro_msg);
        }

        $sqli = "insert into proctransferproc values($clproctransfer->p62_codtran , $iProcessoApensado)";
        $rsi  =  db_query($sqli);

        if (!$rsi) {
            $sqlerro = true;
            throw new Exception('Falha ao arquivar processo.');
        }

        $clprocandam->p61_codproc = $iProcessoApensado;
        $clprocandam->p61_dtandam = date('y-m-d');
        $clprocandam->p61_hora = date('h:i');
        $clprocandam->p61_id_usuario = db_getsession('DB_id_usuario');
        $clprocandam->p61_coddepto = db_getsession('DB_coddepto');
        $clprocandam->incluir(null);

        if ($clprocandam->erro_status==0) {
            $sqlerro=true;
            throw new Exception($clprocandam->erro_msg);
        }

        $clproctransand->p64_codandam = $clprocandam->p61_codandam;
        $clproctransand->p64_codtran = $clproctransfer->p62_codtran;
        $clproctransand->incluir();
        if ($clproctransand->erro_status == 0) {
            $sqlerro = true;
            throw new Exception($clproctransand->erro_msg);
        }

        $clarqandam->p69_codarquiv = $clprocarquiv->p67_codarquiv;
        $clarqandam->p69_codandam  = $clprocandam->p61_codandam;
        $clarqandam->p69_arquivado = 'true';
        $clarqandam->incluir();
        if ($clarqandam->erro_status==0) {
            $sqlerro=true;
            throw new Exception($clarqandam->erro_msg);
        }


        if ($grupo == 2) {
            $sWhereAtendimento = " ov09_protprocesso = {$iProcessoApensado} ";
            $sSqlAtendimento   = $clOuvidoriaAtendimento->sql_query_proc(null, "distinct ov01_sequencial", null, $sWhereAtendimento);
            $rsAtendimento     = $clOuvidoriaAtendimento->sql_record($sSqlAtendimento);
            $iNroAtendimento   = $clOuvidoriaAtendimento->numrows;

            if ($iNroAtendimento > 0) {
                for ($iInd=0; $iInd < $iNroAtendimento; $iInd++) {
                    $oAtendimento = db_utils::fieldsMemory($rsAtendimento, $iInd);
                    $clOuvidoriaAtendimento->ov01_sequencial = $oAtendimento->ov01_sequencial;
                    $clOuvidoriaAtendimento->ov01_situacaoouvidoriaatendimento = 3;
                    $clOuvidoriaAtendimento->alterar($oAtendimento->ov01_sequencial);
                    if ($clOuvidoriaAtendimento->erro_status == 0) {
                        $sqlerro = true;
                        break;
                    }
                }
            }
        }
    }

    db_fim_transacao($sqlerro);
}

/**
 * @param  processoProtocolo  $processo
 *
 * @return void
 * @throws Exception
 */
function notificar(processoProtocolo $processo)
{
    $titular = \CgmFactory::getInstanceByCgm($processo->getCgm());

    if (empty($titular->getNome())) {
        throw new \Exception("Titular não encontrado!");
    }

    $atendimentoProcesso = $processo->getProcessoAtendimento();
    if (empty($atendimentoProcesso)) {
        throw new \Exception("Atendimento do processo não encontrado!");
    }

    $atendimentoCidadao = $atendimentoProcesso->getAtendimentoCidadao();
    if (empty($atendimentoCidadao)) {
        throw new \Exception("Atendimento ao Cidadão não encontrado!");
    }

    $cidadao = $atendimentoCidadao->getCidadao();
    if (empty($cidadao)) {
        throw new \Exception("Cidadão não encontrado!");
    }

    $atendimentoProcessoEletronico
        = $atendimentoProcesso->getAtendimentoProcessoEletronico();
    if (empty($atendimentoProcessoEletronico)) {
        throw new \Exception("Informações do processo eletronico não encontrado!");
    }

    $message = "O processo {$processo->getNumeroProcesso()}/{$processo->getAnoProcesso()} ";
    $message .= "do titular {$titular->getNome()}, foi arquivado.";

    $intergracaoProcessoEletronico =  new ECidade\Lib\Request\ProcessoEletronico\ProcessoEletronico();
    $intergracaoProcessoEletronico->notificar($message, $cidadao->getCnpjCpf());
}


$hoje = date('d/m/y');
$aArquivarProcessos = array();
if ((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Incluir") {
    try {
        arquivamento($p58_codproc, $p67_dtarq, $p67_historico, $grupo);
        if (isset($volume)) {
            foreach ($volume as $v) {
                arquivamento($v, $p67_dtarq, $p67_historico, $grupo);
            }
        }

        $mensagemSucesso = 'Processo arquivado com sucesso.';
    } catch (Exception $e) {
        $mensagemErro = $e->getMessage();
    }
}

function existeApensadoComDeptoDiferente($codigoProcesso)
{
    if (empty($codigoProcesso)) {
        return false;
    }

    $condicoes = "
        p30_procprincipal = $codigoProcesso
        AND andamento_pai.p61_coddepto <> andamento_filho.p61_coddepto
    ";

    $cl_processosapensados = new cl_processosapensados();
    $sqlApensados = $cl_processosapensados->sql_query_informacoes_processos(null, "p30_sequencial", null, $condicoes);
    $existeApensadoComDeptoDiferente = pg_num_rows($cl_processosapensados->sql_record($sqlApensados));

    if ($existeApensadoComDeptoDiferente) {
        return true;
    }
    return false;
}

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
<script type="text/javascript" src="scripts/datagrid.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" style="margin-top:25px;">
<?php
include(modification("forms/db_frmprocarquiv.php"));
?>
<?php
db_menu(
    db_getsession("DB_id_usuario"),
    db_getsession("DB_modulo"),
    db_getsession("DB_anousu"),
    db_getsession("DB_instit")
);
?>
</body>
</html>
<?php
if (isset($mensagemErro)) {
    echo '<script>alert(\''. $mensagemErro .'\')</script>';
}

if (isset($mensagemSucesso)) {
    echo '<script>alert(\''. $mensagemSucesso .'\')</script>';
}
?>

<?php
if ($clprocarquiv->erro_status=="0") {
    $clprocarquiv->erro(true, false);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>  ";
    if ($clprocarquiv->erro_campo!="") {
        echo "<script> document.form1.".$clprocarquiv->erro_campo.".style.backgroundColor='#99A9AE';</script>";
        echo "<script> document.form1.".$clprocarquiv->erro_campo.".focus();</script>";
    }
} else {
    $clprocarquiv->pagina_retorno = "pro4_procarquiv001.php?grupo=$grupo";
    $clprocarquiv->erro(true, true);
}
?>
