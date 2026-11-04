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

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_usuariosonline.php');
require_once modification('libs/db_libpessoal.php');
require_once modification('dbforms/db_funcoes.php');
require_once modification('classes/db_pessoal_classe.php');
require_once modification('classes/db_rhpescargo_classe.php');
require_once modification('classes/db_rhcargo_classe.php');
require_once modification('classes/db_rhpessoal_classe.php');
require_once modification('classes/db_rhpessoalmov_classe.php');
require_once modification('classes/db_rhinstrucao_classe.php');
require_once modification('classes/db_rhestcivil_classe.php');
require_once modification('classes/db_rhpesfgts_classe.php');
require_once modification('classes/db_rhpesbanco_classe.php');
require_once modification('classes/db_cadferia_classe.php');
require_once modification('classes/db_rhdepend_classe.php');
require_once modification('classes/db_rhtipoapos_classe.php');
require_once modification('classes/db_vtfdias_classe.php');
require_once modification('classes/db_afasta_classe.php');
require_once modification('classes/db_rhpeslocaltrab_classe.php');
require_once modification('classes/db_rhpesorigem_classe.php');

$clpessoal = new cl_pessoal;
$clrhpessoal = new cl_rhpessoal;
$clrhpessoalmov = new cl_rhpessoalmov;
$clrhpescargo = new cl_rhpescargo();
$clrhcargo = new cl_rhcargo();
$clrhtipoapos = new cl_rhtipoapos;
$clrhinstrucao = new cl_rhinstrucao;
$clrhestcivil = new cl_rhestcivil;
$clrhpesfgts = new cl_rhpesfgts;
$clrhpesprop = new cl_rhpesprop;
$clrhpesbanco = new cl_rhpesbanco;
$clcadferia = new cl_cadferia;
$clrhdepend = new cl_rhdepend;
$clvtfdias = new cl_vtfdias;
$clafasta = new cl_afasta;
$clrhpeslocaltrab = new cl_rhpeslocaltrab;
$clrhpesorigem = new cl_rhpesorigem;

$clpessoal->rotulo->label();
$clrhpessoal->rotulo->label();
$clrhpessoalmov->rotulo->label();
$clrhpescargo->rotulo->label();
$clrhcargo->rotulo->label();
$clrhpesfgts->rotulo->label();
$clrhpesprop->rotulo->label();
$clrhpesbanco->rotulo->label();
$clrhtipoapos->rotulo->label();
$clrhpesorigem->rotulo->label();

$clrotulo = new rotulocampo;

$z01_nomeorigem = "";
$codigo = "";

$clrotulo->label('z01_nome');
$clrotulo->label('z01_nomeorigem');
$clrotulo->label('z01_ender');
$clrotulo->label('z01_munic');
$clrotulo->label('z01_numcgm');
$clrotulo->label('z01_cgccpf');

$clrotulo->label('h13_tpcont');
$clrotulo->label('h13_descr');

$clrotulo->label('r13_descr');
$clrotulo->label('r37_descr');

$clrotulo->label('rh03_padrao');
$clrotulo->label('rh03_padraoprev');
$clrotulo->label('rh37_cbo');
$clrotulo->label('rh37_descr');
$clrotulo->label('r02_descr');
$clrotulo->label('r70_descr');
$clrotulo->label('r70_estrut');
$clrotulo->label('rh30_regime');
$clrotulo->label('rh30_descr');
$clrotulo->label('rh30_vinculo');
$clrotulo->label('r33_nome');
$clrotulo->label('rh05_recis');
$clrotulo->label('db90_descr');
$clrotulo->label("rh19_propi");
$clrotulo->label("rh01_clas2");
$clrotulo->label("rh21_regpri");

$clrotulo->label('rh44_codban');
$clrotulo->label('rh44_agencia');
$clrotulo->label('rh44_dvagencia');
$clrotulo->label('rh44_conta');
$clrotulo->label('rh44_dvconta');

parse_str($HTTP_SERVER_VARS['QUERY_STRING'], $queryString);

foreach ($queryString as $key => $value) {
    ${$key} = $value;
}

if (!isset($ano)) {
    $ano = db_anofolha();
}
if (!isset($mes)) {
    $mes = db_mesfolha();
}
$dataDia = date("Y-m-d", db_getsession("DB_datausu"));
$getSession = db_getsession('DB_instit');
$campos = "
    *,
    case rh30_regime
        when 1 then 'Estatutário'
        when 2 then 'Celetista'
        when 3 then 'Extra-Quadro'
    end as descr_regime,
    case rh30_vinculo
        when 'A' then 'Ativo'
        when 'I' then 'Inativo'
        when 'P' then 'Pensionista'
    end as descr_vinculo,
    rh37_cbo as cbo,
    case rh02_vincrais
        when 10 then 'CLT'
        when 30 then 'Servidor Público'
        when 35 then 'Servidor Público Não Efetivo'
        when 40 then 'Trabalhador Avulso'
        when 90 then 'Contrato'
    end as descr_vinculorais,
    h13_descr as descr_contrato
";
$where = "
    rh02_anousu = $ano
    and rh02_mesusu = $mes
    and rh01_regist = $regist
    and rh02_instit = {$getSession}
";

$sql = $clrhpessoal->sql_query_pesquisa(null, $campos, "", $where, $ano, $mes);
$result = $clrhpessoal->sql_record($sql);

if ($clrhpessoal->numrows == 0) {
    echo "
        <script>
          alert('Matrícula Não Cadastrada ou sem Movimentação.');
          (window.CurrentWindow || parent.CurrentWindow).corpo.location.href='pes3_conspessoal001.php';
        </script>
       ";
}

db_fieldsmemory($result, 0);

$sSql_instPrev = "   
    select codigo 
    from db_config 
    inner join db_tipoinstit on db21_tipoinstit = db21_codtipo 
    where db21_codigosiconfi = '10132' 
";
$result_instPrev = pg_query($sSql_instPrev);
$numRowls_instPrev = pg_num_rows($result_instPrev);

if ($numRowls_instPrev > 0) {
    $codigo = db_utils::fieldsmemory($result_instPrev, 0)->codigo;
}

if ($codigo == db_getsession("DB_instit")) {
    $result_rhpesorigem = $clrhpesorigem->sql_record($clrhpesorigem->sql_query_file($rh02_regist));
    if ($clrhpesorigem->numrows > 0) {
        db_fieldsmemory($result_rhpesorigem, 0);
        $sSql_nomeorigem = "   
            select z01_nome as z01_nomeorigem  
            from rhpessoal       
            inner join rhpessoalmov   on  rhpessoalmov.rh02_regist = rhpessoal.rh01_regist 
                and  rh02_mesusu = 02 and  rh02_anousu = 2022
            inner join rhlota  on  rhlota.r70_codigo = rhpessoalmov.rh02_lota 
                and  rhlota.r70_instit = rhpessoalmov.rh02_instit       
            inner join cgm  on  cgm.z01_numcgm = rhpessoal.rh01_numcgm  
            where rh01_regist = {$rh21_regpri}
        ";
        $result_nomeorigem = pg_query($sSql_nomeorigem);
        $nRows_nomeorigem = pg_num_rows($result_nomeorigem);
        
        if ($nRows_nomeorigem  > 0) {
            $z01_nomeorigem = db_utils::fieldsmemory($result_nomeorigem, 0)->z01_nomeorigem;
        }
    }

    $result_rhpesprop = $clrhpesprop->sql_record($clrhpesprop->sql_query_file($rh02_regist));
    if ($clrhpesprop->numrows > 0) {
        db_fieldsmemory($result_rhpesprop, 0);
    }
}

$result_rhpesfgts = $clrhpesfgts->sql_record($clrhpesfgts->sql_query_banco($rh01_regist, "rh15_data,rh15_banco,rh15_agencia,rh15_agencia_d,rh15_contac,rh15_contac_d,db90_descr"));
$contaFGTS = new stdClass();

if ($clrhpesfgts->numrows > 0) {
    $contaFGTS = pg_fetch_object($result_rhpesfgts);
}


$contaBancariaServidor  = new stdClass();
$result_rhpesbanco = $clrhpesbanco->sql_record($clrhpesbanco->sql_query($rh02_seqpes, "*"));
if ($clrhpesbanco->numrows > 0) {
    $contaBancariaServidor = pg_fetch_object($result_rhpesbanco);
}

if (!empty($rh02_rhtipoapos)) {
    $rsRhTipoApos = $clrhtipoapos->sql_record($clrhtipoapos->sql_query($rh02_rhtipoapos));
    if ($clrhtipoapos->numrows > 0) {
        db_fieldsmemory($rsRhTipoApos, 0);
    }
}

?>
<html>

<head>
    <title>Dados do Cadastro do Funcionário</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script>
        function js_Impressao() {

            sUrl  = "pes3_conspessoal_campos_impressao.php";
            sUrl += "?regist=<?=$rh01_regist?>";
            sUrl += "&ano=<?=$ano?>";
            sUrl += "&mes=<?=$mes?>";
            js_OpenJanelaIframe('CurrentWindow.corpo', 'func_campos_emissao_ficha_cadastral', sUrl, 'Outros Dados', true, '20');
            return false;
        }

        function js_Pesquisa(solicitacao) {
            if (solicitacao != 'assentamentos') {
                js_OpenJanelaIframe('CurrentWindow.corpo', 'func_pesquisa', 'pes3_conspessoal002_detalhes.php?solicitacao=' + solicitacao + '&parametro=<?=$rh01_regist?>&ano=<?=$ano?>&mes=<?=$mes?>', 'Outros Dados', true, '20');
            } else {
                js_OpenJanelaIframe('CurrentWindow.corpo',
                    'func_pesquisaassentamentos',
                    'rec3_consafastfunc002.php?codAssen=&codMatri=<?=$rh01_regist?>' +
                    '&ano=<?=$ano?>&mes=<?=$mes?>&dataIni=1900-01-01',
                    'CONSULTA DE ASSENTAMENTOS', true, '20');
            }
        }
    </script>
     <style>
        .input-view {
            background: #DEB887;
        }

        .conta-bancaria-section {
            display: flex;
            margin-top: 5px;

        }

        .conta-bancaria-section>div {
            width: 50%;
        }
    </style>
</head>

<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<form name="form1" method="post">
    <table width="700" align="center" border="0" cellpadding="0" cellspacing="2">
        <tr>
            <td>
                <fieldset>
                    <legend><strong>DADOS PESSOAIS</strong></legend>
                    <table>
                        <tr>
                            <td valign="top" rowspan="5">
                                <?php
                                db_foto($rh01_numcgm, 1, "js_JanelaAutomatica('cgm','$rh01_numcgm')")
                                ?>
                            </td>
                            <td align="right" nowrap title="<?= $Trh01_regist ?>">
                                <?php
                                db_ancora("<b>" . $RLrh01_regist . ":</b>", "js_JanelaAutomatica('cgm','$rh01_numcgm')", 1);
                                ?>
                            </td>
                            <td align="left" nowrap colspan="5">
                                <?php
                                db_input('rh01_regist', 8, $Irh01_regist, true, 'text', 3)
                                ?>
                                <?php
                                db_input('z01_numcgm', 8, $Iz01_numcgm, true, 'text', 3, '')
                                ?>
                                <?php
                                db_input('z01_nome', 66, $Iz01_nome, true, 'text', 3, '')
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" nowrap title="<?= $Trh01_nasc ?>">
                                <?= @$Lrh01_nasc ?>
                            </td>
                            <td align="left" nowrap>
                                <?php
                                db_inputdata('rh01_nasc', $rh01_nasc_dia, $rh01_nasc_mes, $rh01_nasc_ano, true, 'text', 3)
                                ?>
                            </td>
                            <td align="right" nowrap title="<?= @$Trh01_instru ?>">
                                <?php
                                db_ancora(@$Lrh01_instru, "js_pesquisarh01_instru(true);", 3);
                                ?>
                            </td>
                            <td align="left" nowrap colspan="3">
                                <?php
                                $result_instru = $clrhinstrucao->sql_record($clrhinstrucao->sql_query_file());
                                db_selectrecord("rh01_instru", $result_instru, "", 3);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" nowrap title="<?= @$Trh01_sexo ?>">
                                <?= @$Lrh01_sexo ?>
                            </td>
                            <td align="left" nowrap>
                                <?php
                                $arr_sexo = array('M' => 'Masculino', 'F' => 'Feminino');
                                db_select("rh01_sexo", $arr_sexo, true, 3, "");
                                ?>
                            </td>
                            <td align="right" nowrap title="<?= @$Trh01_estciv ?>">
                                <?php
                                db_ancora(@$Lrh01_estciv, "js_pesquisarh01_estciv(true);", 3);
                                ?>
                            </td>
                            <td align="left" nowrap>
                                <?php
                                $result_estciv = $clrhestcivil->sql_record($clrhestcivil->sql_query_file());
                                db_selectrecord("rh01_estciv", $result_estciv, "", 3);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" nowrap title="<?= $Tz01_ender ?>">
                                <?= @$Lz01_ender ?>
                            </td>
                            <td align="left" nowrap colspan="5">
                                <?php
                                $z01_ender .= ', ' . $z01_numero . ' ' . $z01_compl;
                                db_input('z01_ender', 84, $Iz01_ender, true, 'text', 3)
                                ?>
                        </tr>
                        <tr>
                            <td align="right" nowrap title="<?= $Tz01_munic ?>">
                                <?= @$Lz01_munic ?>
                            </td>
                            <td align="left" nowrap colspan="5">
                                <?php
                                $z01_munic .= ' / ' . $z01_uf;
                                db_input('z01_munic', 84, $Iz01_munic, true, 'text', 3)
                                ?>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </td>
        </tr>
        <tr>
            <td>
                <fieldset>
                    <legend><strong>DADOS ADMISSIONAIS</strong></legend>
                    <table>
                        <tr>
                            <td align="right" nowrap title="<?= $Trh01_admiss ?>">
                                <?= @$Lrh01_admiss ?>
                            </td>
                            <td align="left" nowrap colspan="3">
                                <?php
                                db_inputdata('rh01_admiss', $rh01_admiss_dia, $rh01_admiss_mes, $rh01_admiss_ano, true, 'text', 3)
                                ?>
                            </td>
                            <td align="right" nowrap title="<?= $Trh05_recis ?>">
                                <?= @$Lrh05_recis ?>
                            </td>
                            <td align="left" nowrap colspan="3">
                                <?php
                                db_inputdata('rh05_recis', $rh05_recis_dia, $rh05_recis_mes, $rh05_recis_ano, true, 'text', 3)
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" nowrap title="<?= $Trh02_hrssem ?>">
                                <?= @$Lrh02_hrssem ?>
                            </td>
                            <td align="left" nowrap>
                                <?php
                                db_input('rh02_hrssem', 8, $Irh02_hrssem, true, 'text', 3)
                                ?>
                            </td>
                            <td align="right" nowrap title="<?= $Trh02_hrsmen ?>">
                                <?= @$Lrh02_hrsmen ?>
                            </td>
                            <td align="right" nowrap>
                                <?php
                                db_input('rh02_hrsmen', 8, $Irh02_hrsmen, true, 'text', 3)
                                ?>
                            </td>
                            <td align="right" nowrap title="<?= $Trh37_cbo ?>">
                                <?= @$Lrh37_cbo ?>
                            </td>
                            <td align="left" nowrap>
                                <?php
                                db_input('rh37_cbo', 8, $Irh37_cbo, true, 'text', 3)
                                ?>
                            </td>
                            <td align="right" nowrap title="<?= $Trh02_salari ?>">
                                <?= @$Lrh02_salari ?>
                            </td>
                            <td align="left" nowrap colspan="3">
                                <?php
                                $rh02_salari = number_format($rh02_salari, 2);
                                db_input('rh02_salari', 15, $Irh02_salari, true, 'text', 3)
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" nowrap title="<?= $Th13_tpcont ?>">
                                <?= @$Lh13_tpcont ?>
                            </td>
                            <td align="left" nowrap colspan="3">
                                <?php
                                db_input('h13_tpcont', 8, $Ih13_tpcont, true, 'text', 3, "", "h13_tpcont")
                                ?>
                                <?php
                                db_input('h13_descr', 30, $Ih13_descr, true, 'text', 3, "", "descr_contrato")
                                ?>
                            </td>
                            <td align="right" nowrap title="<?= $Trh03_padrao ?>">
                                <?= @$Lrh03_padrao ?>
                            </td>
                            <td align="left" nowrap>
                                <?php
                                db_input('rh03_padrao', 8, $Irh03_padrao, true, 'text', 3);
                                db_input('r02_descr', 30, $Ir02_descr, true, 'text', 3);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" nowrap title="<?= $Trh02_funcao ?>">
                                <?= @$Lrh02_funcao ?>
                            </td>
                            <td align="left" nowrap colspan="3">
                                <?php
                                db_input('rh02_funcao', 8, $Irh02_funcao, true, 'text', 3)
                                ?>
                                <?php
                                db_input('rh37_descr', 30, $Irh37_descr, true, 'text', 3)
                                ?>
                            </td>
                            <td align="right">
                                <strong>Padrão Previdên:</strong>
                            </td>
                            <td align="left" nowrap colspan="3">
                                <?php

                                $clrhpespadrao = new cl_rhpespadrao();
                                $result_pespadraoprev = $clrhpespadrao->sql_record(
                                    $clrhpespadrao->sql_query_padrao_previdencia($rh02_seqpes, 'r02_descr as r02_descrprev')
                                );

                                if ($result_pespadraoprev) {
                                    db_fieldsmemory($result_pespadraoprev, 0);
                                }

                                db_input('rh03_padraoprev', 8, $Irh03_padraoprev, true, 'text', 3);
                                db_input('r02_descrprev', 30, @$r02_descrprev, true, 'text', 3);
                                ?>
                            </td>
                        </tr>
                        <?php if (!empty($rh20_cargo)) : ?>
                            <tr>
                                <td align="right" nowrap title="<?= $Trh20_cargo ?>">
                                    <?= @$Lrh20_cargo ?>
                                </td>
                                <td align="left" nowrap colspan="3">
                                    <?php
                                    db_input('rh20_cargo', 8, $Irh20_cargo, true, 'text', 3)
                                    ?>
                                    <?php
                                    db_input('rh04_descr', 30, $Irh04_descr, true, 'text', 3)
                                    ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td align="right" nowrap title="<?= $Trh30_regime ?>">
                                <?= @$Lrh30_regime ?>
                            </td>
                            <td align="left" nowrap colspan="3">
                                <?php
                                db_input('rh30_regime', 8, $Irh30_regime, true, 'text', 3)
                                ?>
                                <?php
                                db_input('rh30_descr', 30, $Irh30_descr, true, 'text', 3, "", "descr_regime")
                                ?>
                            </td>
                            <td align="right" nowrap title="<?= $Trh02_lota ?>">
                                <?= @$Lrh02_lota ?>
                            </td>
                            <td align="left" nowrap colspan="3">
                                <?php
                                db_input('rh02_lota', 8, $Irh02_lota, true, 'text', 3)
                                ?>
                                <?php
                                db_input('r70_estrut', 13, $Ir70_estrut, true, 'text', 3)
                                ?>
                                <?php
                                db_input('r70_descr', 30, $Irh37_descr, true, 'text', 3)
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" nowrap title="<?= $Trh02_tbprev ?>">
                                <?= @$Lrh02_tbprev ?>
                            </td>
                            <td align="left" nowrap colspan="3">
                                <?php
                                db_input('rh02_tbprev', 8, $Irh02_tbprev, true, 'text', 3)
                                ?>
                                <?php
                                db_input('r33_nome', 30, $Ir33_nome, true, 'text', 3)
                                ?>
                            </td>
                            <td align="right" nowrap title="<?= $Trh30_vinculo ?>">
                                <?= @$Lrh30_vinculo ?>
                            </td>
                            <td align="left" nowrap colspan="3">
                                <?php
                                db_input('rh30_vinculo', 8, $Irh30_vinculo, true, 'text', 3)
                                ?>
                                <?php
                                db_input('rh30_descr', 46, $Irh30_descr, true, 'text', 3, "", "descr_vinculo")
                                ?>
                            </td>
                        </tr>
                        <?php if (!empty($rh02_rhtipoapos)) { ?>
                            <tr>
                                <td align="right" nowrap title="<?= $Trh02_rhtipoapos ?>">
                                    <?= @$Lrh02_rhtipoapos ?>
                                </td>
                                <td align="left" nowrap colspan="3">
                                    <?php
                                    db_input('rh02_rhtipoapos', 8, $Irh02_rhtipoapos, true, 'text', 3)
                                    ?>
                                    <?php
                                    db_input('rh88_descricao', 30, $Irh88_descricao, true, 'text', 3)
                                    ?>
                                </td>
                                <?php if (!empty($rh02_validadepensao)) { ?>
                                    <td align="right" nowrap title="<?= $Trh02_validadepensao ?>">
                                        <?= @$Lrh02_validadepensao ?>
                                    </td>
                                    <td align="left" nowrap colspan="3">
                                        <?php db_inputdata('rh02_validadepensao', $rh02_validadepensao_dia, $rh02_validadepensao_mes, $rh02_validadepensao_ano, true, 'text', 3); ?>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td></td>
                            <td colspan="3"></td>
                            <td align="right" nowrap title="<?= $Trh02_vincrais ?>">
                                <?= @$Lrh02_vincrais ?>
                            </td>
                            <td align="left" nowrap colspan="3">
                                <?php
                                db_input('rh02_vincrais', 8, $Irh02_vincrais, true, 'text', 3)
                                ?>
                                <?php
                                db_input('rh30_descr', 46, $Irh30_descr, true, 'text', 3, "", "descr_vinculorais")
                                ?>
                            </td>
                        </tr>
                        <?php
                        if ($codigo == db_getsession("DB_instit")) {
                            ?>
                            <tr>
                                <td align="right" nowrap title="<?= $Trh01_numcgm ?>">
                                    <?= @$Lrh21_regpri ?>
                                </td>
                                <td align="left" nowrap colspan="3">
                                    <?php
                                    db_input('rh21_regpri', 6, $Irh21_regpri, true, 'text', 3, '');
                                    db_input('z01_nomeorigem', 33, 2, true, 'text', 3, '');
                                    ?>
                                </td>
                            </tr>  
                             <tr>
                                <td align="right" nowrap title="<?= $Trh19_propi ?>">
                                    <?= @$Lrh19_propi ?>
                                </td>
                                <td align="left" nowrap colspan="3">
                                    <?php
                                    db_input('rh19_propi', 8, $Irh19_propi, true, 'text', 3)
                                    ?>
                                </td>
                                <td align="right" nowrap title="<?= $Trh01_clas2 ?>">
                                    <?= @$Lrh01_clas2 ?>
                                </td>
                                <td align="left" nowrap colspan="3">
                                    <?php
                                    db_inputdata('rh01_clas2', @$rh01_clas2_dia, @$rh01_clas2_mes, @$rh01_clas2_ano, true, 'text', 3, "")
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </fieldset>
            </td>
        </tr>
        <tr>
            <td>
                <fieldset>
                    <legend><strong>FGTS / Conta Bancária</strong></legend>
                    
                    <div class="conta-bancaria-section">
                            <div id="contafgts">
                        <table>
                            <tr>
                               <td align="right"><?= @$Lrh15_data ?></td>
                                        <td>
                                            <input type="text" name="conta_fgts_banco_data_opcao" size="10" value="<?= !empty($contaFGTS->rh15_data) ?  DBDate::format($contaFGTS->rh15_data) : "" ?>" class="input-view" readonly />
                                        </td>
                                
                            </tr>
                            <tr>
                                <td align="right" nowrap title="<?= @$Trh15_banco ?>">
                                    <?= @$Lrh15_banco ?>
                                </td>
                                <td colspan="3" nowrap>
                                    <input type="text" name="conta_fgts_banco_codigo" size="5" value="<?= $contaFGTS->rh15_banco; ?>" class="input-view" readonly />
                                    <input type="text" name="conta_fgts_banco_nome" size="40" value="<?= $contaFGTS->db90_descr; ?>" class="input-view" readonly />                                        
                                </td>
                                </tr>
                                    <tr>
                                        <td align="right" nowrap title="<?= @$Trh15_agencia ?>">
                                            <?= @$Lrh15_agencia ?>
                                        </td>
                                <td nowrap>
                                    <input type="text" name="conta_fgts_banco_agencia" size="5" value="<?= $contaFGTS->rh15_agencia; ?>" class="input-view" readonly />                                        
                                </td>
                                <td align="right" nowrap title="<?= @$Trh15_agencia_d ?>">
                                    <?= @$Lrh15_agencia_d ?>
                                </td>
                                <td nowrap>
                                    <input type="text" name="conta_fgts_banco_agencia_digito" size="1" value="<?= $contaFGTS->rh15_agencia_d; ?>" class="input-view" readonly />
                                </td>
                            </tr>
                            <tr>
                            <td align="right" nowrap title="<?= @$Trh15_contac ?>">
                                            <?= @$Lrh15_contac ?>
                                </td>
                                <td nowrap>
                                    <input type="text" name="conta_fgts_banco_agencia_conta" size="15" value="<?= $contaFGTS->rh15_contac; ?>" class="input-view" readonly />                                        
                                </td>
                                    <td align="right" nowrap title="<?= @$Trh15_contac_d ?>">
                                    <?= @$Lrh15_contac_d ?>
                                </td>
                                <td nowrap>
                                  
                                <input type="text" name="conta_fgts_banco_agencia_conta_digito" size="1" value="<?= $contaFGTS->rh15_contac_d; ?>" class="input-view" readonly />
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div id="contaservidor">
                                <table>
                                        <tr>
                                            <td><br></td>
                                        </tr>
                                        <tr>
                                            <td align="right" nowrap title="<?= @$Trh44_codban ?>">
                                                <?= @$Lrh44_codban; ?>
                                            </td>
                                            <td colspan="3" nowrap>
                                                <input type="text" name="conta_servidor_banco_codigo" size="3" value="<?= $contaBancariaServidor->rh44_codban; ?>" class="input-view" readonly />
                                                <input type="text" name="conta_servidor_banco_descricao" size="40" value="<?= $contaBancariaServidor->db90_descr; ?>" class="input-view" readonly />
                                            </td>
                                            </tr>
                                        <tr>
                                            <td align="right" nowrap title="<?= @$Trh44_agencia ?>">
                                                <?= @$Lrh44_agencia ?>
                                            </td>
                                            <td nowrap>
                                                <input type="text" name="conta_servidor_banco_agencia" size="5" value="<?= $contaBancariaServidor->rh44_agencia; ?>" class="input-view" readonly />
                                            </td>
                                            <td align="right" nowrap title="<?= @$Trh44_dvagencia ?>">
                                                <?= @$Lrh44_dvagencia ?>
                                            </td>
                                            <td align="right" nowrap>
                                                <input type="text" name="conta_servidor_banco_agencia_digito" size="1" value="<?= @$contaBancariaServidor->rh44_dvagencia; ?>" class="input-view" readonly />
                                            </td>
                                        </tr>
                                    <tr>
                                        <td align="right" nowrap title="<?= @$Trh44_conta ?>">
                                            <?= @$Lrh44_conta ?>
                                        </td>
                                        <td nowrap>
                                            <input type="text" name="conta_servidor_banco_conta" size="15" value="<?= @$contaBancariaServidor->rh44_conta; ?>" class="input-view" readonly />
                                        </td>
                                        <td align="right" nowrap title="<?= @$Trh44_dvconta ?>">
                                            <?= @$Lrh44_dvconta ?>
                                        </td>
                                        <td align="right" nowrap>
                                            <input type="text" name="conta_servidor_banco_dvconta" size="1" value="<?= @$contaBancariaServidor->rh44_dvconta; ?>" class="input-view" readonly />
                                        </td>
                            </tr>
                        </table>
                    </div>

                </div>
                </fieldset>
            </td>
        </tr>
        <?php
        $ferias = " disabled ";
        $depend = " disabled ";
        $vtfdia = " disabled ";
        $afasta = " disabled ";
        $efetiv = " disabled ";
        $result_feria = $clcadferia->sql_record($clcadferia->sql_query_file(null, "*", "", "r30_anousu = " . $ano . " and r30_mesusu = " . $mes . " and r30_regist = " . $rh01_regist));
        if ($clcadferia->numrows > 0) {
            $ferias = "";
        }
        $result_depend = $clrhdepend->sql_record($clrhdepend->sql_query_file(null, "*", "", "rh31_regist = " . $rh01_regist));
        if ($clrhdepend->numrows > 0) {
            $depend = "";
        }
        $result_vtfdias = $clvtfdias->sql_record($clvtfdias->sql_query_file($ano, $mes, $rh01_regist));
        if ($clvtfdias->numrows > 0) {
            $vtfdia = "";
        }
        $result_afasta = $clafasta->sql_record($clafasta->sql_query_file(null, "*", "", "r45_anousu = " . $ano . " and r45_mesusu = " . $mes . " and r45_regist = " . $rh01_regist));
        if ($clafasta->numrows > 0) {
            $afasta = "";
        }
        $result_efetiv = $clrhpeslocaltrab->sql_record($clrhpeslocaltrab->sql_query_rhpessoalmov(null, "*", "", "rh02_anousu = " . $ano . " and rh02_mesusu = " . $mes . " and rh02_regist = " . $rh01_regist));
        if ($clrhpeslocaltrab->numrows > 0) {
            $efetiv = "";
        }
        ?>
        <tr>
            <td>
                <fieldset>
                    <legend><strong>OUTRAS OPÇÕES DE PESQUISA</strong></legend>
                    <center>
                        <table>
                            <tr>
                                <td align="right" nowrap>
                                    <input type="button" name="outs" style="width:130px" value="Outros dados"
                                           onclick="js_Pesquisa('Outros');">
                                    <input type="button" name="docs" style="width:130px" value="Documentos"
                                           onclick="js_Pesquisa('Documentos');">
                                    <input type="button" name="vars" style="width:130px" value="Variáveis"
                                           onclick="js_Pesquisa('Variaveis');">
                                    <input type="button" name="fers" style="width:130px" value="Férias"
                                           onclick="js_Pesquisa('Ferias');" <?= $ferias ?>>
                                    <input type="button" name="deps" style="width:130px" value="Dependentes"
                                           onclick="js_Pesquisa('Dependentes');" <?= $depend ?>>
                                    <input type="button" name="assenta" style="width:130px"
                                           value="Assentamentos" onclick="js_Pesquisa('assentamentos');">
                                </td>
                            </tr>
                            <tr>
                                <td align="left" nowrap>
                                    <input type="button" name="vtfs" style="width:130px" value="Vale transporte"
                                           onclick="js_Pesquisa('Vale');" <?= $vtfdia ?>>
                                    <input type="button" name="afas" style="width:130px" value="Afastamento"
                                           onclick="js_Pesquisa('Afastamentos');" <?= $afasta ?>>
                                    <input type="button" name="efes" style="width:130px" value="Local de Trabalho"
                                           onclick="js_Pesquisa('Efetividade');" <?= $efetiv ?>>
                                    <input type="button" name="temposervico" style="width:130px"
                                           value="Tempo Anterior" onclick="js_Pesquisa('temposervico');">
                                    <input type="button" name="impr" style="width:130px" value="Imprimir"
                                           onclick="js_Impressao();">
                                    <input type="button" name="fech" style="width:130px" value="Fechar"
                                           onclick="parent.func_nome.hide();">

                                </td>
                            </tr>
                        </table>
                    </center>
                </fieldset>
            </td>
        </tr>
    </table>
</form>
<script type="text/javascript">
</script>

</body>

</html>
