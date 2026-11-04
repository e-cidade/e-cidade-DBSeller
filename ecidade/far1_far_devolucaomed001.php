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
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("far1_far_devolucaomed001_func.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification('libs/db_utils.php'));

db_postmemory($HTTP_POST_VARS);

$oDaoclFarDevolucaomed = new cl_far_devolucaomed();
$oDaoclFarDevolucao = new cl_far_devolucao();
$oDaoAtendrequiitem = new cl_atendrequiitem();
$db_opcao = 1;
$db_botao = true;
$fa22_i_login = db_getsession('DB_id_usuario');

if (isset($confirmar)) {
    //////////////////////////////////////////////////////////////////////////////////////////////////
    //  As informações como quantidade,atendrequiitem,codmater...                                   //
    //  do produto a ser devolvido chgan aqui atravez da string valores                             //
    //  os dados foran concatnados nessa string com um padrão separados por '_':                    //
    //                                                                                              //
    //  quant_[N° atendrequiitem]_[N° codmater]_[N° matrequiitem]_[Quant devolvida]_[iCodigoIniMei] //
    //                                                                                              //
    //  este é o padrão de um item eles podem vir em sequencia na mesma string assim:               //
    //                                                                                              //
    //  quant_..._quant_...quant_...  [3 Itens]                                                     //
    //                                                                                              //
    //////////////////////////////////////////////////////////////////////////////////////////////////

    //Quebrando string dos itens
    $aMedicamentos = explode('quant_', $valores);
    $aMotivos = explode('motivo_', $motivos);

    /* Iniciar um for para passar todos */
    $lErro = false;
    db_inicio_transacao();
    for ($iCont = 1; $iCont < count($aMedicamentos); $iCont++) {
        $aInfo = explode('_', $aMedicamentos[$iCont]);
        $sMotiv = explode('_', $aMotivos[$iCont]);
        $sMotivMed = $sMotiv[1];
        $iAtendrequiitem = $aInfo[0];
        $iCodRetiradaItens = $aInfo[5];
        $iQuant = $aInfo[6];
        $iCancelamento = $aInfo[7];
        $iRequiMed = 0;

        if ($iAtendrequiitem != '') {
            //Descobrir qual o codigo de atendimento de requisição na qual o item pertençe
            $sSql = $oDaoAtendrequiitem->sql_query_file($iAtendrequiitem, 'm43_codatendrequi');
            $rs = $oDaoAtendrequiitem->sql_record($sSql);
            $oDadosAtend = db_utils::fieldsmemory($rs, 0);
            $iRequiMed = $oDadosAtend->m43_codatendrequi;
        }

        /* Realizar rotina de devolução do estoque */
        if ($iRequiMed != 0) {
            $aMedicamentoUnit = explode("_", $aMedicamentos[$iCont]);
            $aMedicamentoUnit[3] = $iQuant;
            $valores = "quant_" . implode("_", $aMedicamentoUnit);
            $lErro = devolveMaterial($valores, $iRequiMed, $sMotivMed);

            if ($lErro) {
                $oDaoclFarDevolucaomed->erro_status = '0';
                $oDaoclFarDevolucaomed->erro_msg = 'ERRO AO EFETUAR DEVOLUCAO DO MATERIAL';
                break;
            }
        }

        /* incluir registro da devolução na farmacia */

        $oDaoclFarDevolucao->fa22_i_cgsund = $fa22_i_cgsund;
        $oDaoclFarDevolucao->fa22_c_hora = db_hora();
        $oDaoclFarDevolucao->fa22_i_login = $fa22_i_login;
        $oDaoclFarDevolucao->fa22_d_data = date('Y-m-d', db_getsession("DB_datausu"));
        $oDaoclFarDevolucao->incluir(null);

        if ($oDaoclFarDevolucao->erro_status == '0') {
            $oDaoclFarDevolucaomed->erro_status = '0';
            $oDaoclFarDevolucaomed->erro_msg = $oDaoclFarDevolucao->erro_msg;
            $lErro = true;
            break;
        }

        $oDaoclFarDevolucaomed->fa23_i_retiradaitens = $iCodRetiradaItens;
        $oDaoclFarDevolucaomed->fa23_i_cancelamento = $iCancelamento;
        $oDaoclFarDevolucaomed->fa23_i_quantidade = $iQuant;
        $oDaoclFarDevolucaomed->fa23_c_motivo = $sMotivMed;
        $oDaoclFarDevolucaomed->fa23_i_devolucao = $oDaoclFarDevolucao->fa22_i_codigo;
        $oDaoclFarDevolucaomed->incluir(null);
        $fa22_i_codigo = $oDaoclFarDevolucao->fa22_i_codigo;

        if ($oDaoclFarDevolucaomed->erro_status == '0') {
            $lErro = true;
            break;
        }
    }
    db_fim_transacao($lErro);
}
?>
<html>
<head>
    <style>
        [disabled] {
            background-color: #DEB887;
            color: #696969;
        }

    </style>

    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body marginwidth="0" marginheight="0" onLoad="a=1">
<div class="container" style="width: 790px;">
    <fieldset style="width:90%">
        <legend><b>Devolução Medicamentos</b></legend>
        <?php
        require_once(modification("forms/db_frmfar_devolucaomed.php"));
        ?>
    </fieldset>
</div>
<?php
db_menu();
?>
</body>
</html>
<script>
    js_tabulacaoforms("form1", "fa23_i_matersaude", true, 1, "fa23_i_matersaude", true);
</script>
<?php
if (isset($confirmar)) {
    if ($oDaoclFarDevolucaomed->erro_status == '0') {
        $oDaoclFarDevolucaomed->erro(true, false);
        $db_botao = true;
    } else {
        $oDaoclFarDevolucaomed->erro(true, false);
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Redireciona a pagina caso não ocorra erro, procedimento necessario para corrigir erro do regarregamento da pagina//
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $sVariaveis = "fa22_i_cgsund=" . $fa22_i_cgsund;
        $sVariaveis .= "&fa23_i_matersaude=" . $fa23_i_matersaude;
        $sVariaveis .= "&z01_v_nome=" . $z01_v_nome;
        $sVariaveis .= "&m60_descr=" . $m60_descr;
        db_redireciona("far1_far_devolucaomed001.php?" . $sVariaveis);
    }
}
?>
