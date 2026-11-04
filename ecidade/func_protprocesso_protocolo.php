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

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_utils.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("dbforms/db_funcoes.php");

db_postmemory($_POST);
parse_str($_SERVER["QUERY_STRING"], $queryString);

$oPost = db_utils::postMemory($_POST);
$oGet = db_utils::postMemory($_GET);

$clprotprocesso = new cl_protprocesso;
$clprotprocesso->rotulo->label("p58_codproc");
$clprotprocesso->rotulo->label("p58_requer");
$clprotprocesso->rotulo->label("p58_numero");
$clprotprocesso->rotulo->label("p58_ano");
$daoOvidoriaAtendimento = new cl_ouvidoriaatendimento();
$daoOvidoriaAtendimento->rotulo->label('ov01_numero');

$cl_acordoparam = new cl_acordoparam;
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
</head>
<body class="body-default">
<?php if (!empty($oGet->alerta_filtro_ano)) : ?>
    <div class="alert alert-primary text-left" role="alert">
        Atenção! O sistema filtra automaticamente processos do ano logado, para buscar todos os processos cadastrados
        clique no botão pesquisar (sem informar nenhum filtro)
    </div>
<?php endif; ?>

<table height="100%" border="0" align="center" cellspacing="0" bgcolor="#CCCCCC">
    <tr>
        <td height="63" align="center" valign="top">
            <table width="35%" border="0" align="center" cellspacing="0">
                <form name="form2" method="post" action="">
                    <tr>
                        <td width="4%" align="left" nowrap title="<?= $Tp58_codproc ?>">
                            <?= $Lp58_codproc ?>
                        </td>
                        <td width="96%" align="left" nowrap>
                            <?php
                            db_input("p58_codproc", 10, $Ip58_codproc, true, "text", 4, "", "chave_p58_codproc");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="4%" align="left" nowrap title="<?= $Tp58_numero ?>">
                            <?= $Lp58_numero ?>
                        </td>
                        <td width="96%" align="left" nowrap>
                            <?php
                            db_input("p58_numero", 10, "$Ip58_numero", true, "text", 4, "placeholder='Número/Ano'", "chave_p58_numero", "", "text-transform: capitalize;");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="4%" align="left" nowrap title="<?= $Tov01_numero ?>">
                            <?= $Lov01_numero ?>
                        </td>
                        <td width="96%" align="left" nowrap>
                            <?php
                            db_input("ov01_numero", 10, "", true, "text", 4, "placeholder='Número/Ano'", "chave_ov01_numero");
                            ?>
                        </td>
                    </tr>
                    <!-- PLUGIN procjudicial -->
                    <?php
                    $departamentosLiberados = array(125, 236, 2481, 2479, 2483, 2489, 2488, 570, 2476, 2214);
                    if (in_array(db_getsession("DB_coddepto"), $departamentosLiberados)) { ?>
                        <tr>
                            <td width="4%" align="left" nowrap title="">
                                <b>Numero Judicial</b>
                            </td>
                            <td width="96%" align="left" nowrap>
                                <?php
                                db_input(
                                    "procjudi_cod",
                                    50,
                                    $Iprocjudi_cod,
                                    true,
                                    "text",
                                    4,
                                    "",
                                    "chave_procjudi_cod",
                                    "",
                                    "",
                                    20
                                );
                                ?>
                            </td>
                        </tr>
                        <?php
                        } ?>
                    <!-- PLUGIN procjudicial -->
                    <tr>
                        <td width="4%" align="left" nowrap title="<?= $Tp58_requer ?>">
                            <?= $Lp58_requer ?>
                        </td>
                        <td width="96%" align="left" nowrap>
                            <?php
                            db_input("p58_requer", 50, $Ip58_requer, true, "text", 4, "", "chave_p58_requer", "", "width:359px;");
                            ?>
                        </td>
                    </tr>
                    <?php
                    if (ProcessoProtocoloNumeracao::getTipoConfiguracao() == ProcessoProtocoloNumeracao::TIPOORGAO) { ?>
                        <tr>
                            <td nowrap title="Órgão">
                                <strong>Órgão</strong>
                            </td>
                            <td>
                                <?php
                                $clorcunidade = new cl_orcunidade();
                                $sWhere = " o40_anousu = " . db_getSession("DB_anousu");

                                $rsAcordoparam = $cl_acordoparam->sql_record($cl_acordoparam->sql_query_file(null, "ac59_permiteporinstit", null, "ac59_instituicao = " . db_getsession('DB_instit')));
                                db_fieldsmemory($rsAcordoparam, 0);

                                if ($ac59_permiteporinstit === 'f') {
                                    $todas_instituicoes = 1;
                                }

                                if (!isset($todas_instituicoes)) {
                                    $sWhere .= " and o41_instit = " . db_getsession('DB_instit');
                                }
                                $result = $clorcunidade->sql_record(
                                    $clorcunidade->sql_query(
                                        null,
                                        null,
                                        null,
                                        "distinct o40_orgao,o40_descr",
                                        "o40_descr",
                                        $sWhere
                                    )
                                );

                                $selectOrgao = '<select name="p07_orgao" style="width:359px;"><option value="0">Todos</option>';
                                while ($row = pg_fetch_assoc($result)) {
                                    $selectOrgao .= "<option value='{$row['o40_orgao']}'>{$row['o40_orgao']} - {$row['o40_descr']}</option>";
                                }
                                $selectOrgao .= '</select>';

                                echo $selectOrgao;
                                ?>
                            </td>
                        </tr>
                        <?php
                    } ?>


                    <tr>
                        <td width="4%" align="left" nowrap title="Descrição Tipo de Processo">
                            <strong>Descrição:</strong>
                        </td>
                        <td width="96%" align="left" nowrap>
                            <?php
                            db_input("p51_descr", 50, $Ip58_requer, true, "text", 4, "", "chave_p51_descr", "", "width:359px;");
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td width="4%" align="left" nowrap title="Ano de Processo">
                            <strong>Ano:</strong>
                        </td>
                        <td width="96%" align="left" nowrap>
                            <?php
                            db_input("p58_ano", 50, $Ip58_ano, true, "text", 4, "", "chave_p58_ano", "", "width:359px;");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <input name="pesquisar" type="submit" id="pesquisar2" value="Pesquisar">
                            <input name="limpar" type="reset" id="limpar" value="Limpar">
                            <input name="Fechar" type="button" id="fechar" value="Fechar"
                                   onClick="parent.db_iframe_proc.hide();">
                        </td>
                    </tr>
                </form>
            </table>
        </td>
    </tr>
    <tr>
        <td align="center" valign="top">
            <?php

        /**
         * Refatorado código para melhor legibilidade e adequação ao redmine M18678
         */

            $campos = [];
            $ordem = "";
            $where = [];

        //alteramos para pegar a instituicao do departamento da protprocesso, para trazer a instit que o criou
            $sCampoInstituicao  = "a.nomeinst as  instit";

            $campos[] = "p58_codproc as dl_codigo_do_processo, cast(p58_numero||'/'||p58_ano as varchar) as p58_numero,
                        z01_numcgm as DB_p58_numcgm,p109_nome as dl_tipo_de_processo,
                        z01_nome as dl_titular,p58_requer,p58_dtproc,p51_descr,p58_obs, {$sCampoInstituicao},
                        (ov01_numero ||'/'||ov01_anousu)::varchar as ov01_numero";

            /**
             * caso a requisição venha da rotina de anexar documentos ao processo, adiciona um where para que não retorne
             * processos arquivados
             */
            if (isset($anexar_documento)) {
                $where[] = "not exists (
              select 1 from arqandam
              where p69_codandam = p58_codandam
                and p69_arquivado is true)";
            }

            $rsAcordoparam = $cl_acordoparam->sql_record($cl_acordoparam->sql_query_file(null, "ac59_permiteporinstit", null, "ac59_instituicao = " . db_getsession('DB_instit')));
            db_fieldsmemory($rsAcordoparam, 0);

            if ($ac59_permiteporinstit === 'f') {
                $todas_instituicoes = 1;
            }

            if (!isset($todas_instituicoes)) {
                $where[] = "p58_instit = " . db_getsession("DB_instit");
            }

            if (isset($apenas_processopai)) {
                $where[] = 'p58_processopai = 0';
            } else {
                if (isset($apenas_volume)) {
                    $where[] = 'p58_processopai != 0';
                }
            }

            if (isset($grupo) && trim($grupo) != '') {
                $where[] = "tipoproc.p51_tipoprocgrupo in ({$grupo})";
            }

            if (isset($tipo) && trim($tipo) != '') {
                $where[] = "p58_codigo = {$tipo} ";
            }

            /**
             * Se a variável "$lAnoAtual" for true, filtra os processos do ano setado na sessão
             */
            if (!empty($lAnoAtual) && $lAnoAtual) {
                $where[] = "p58_ano = " . db_getsession("DB_anousu");
            }

            if (isset($apensado) && trim($apensado) != '') {
                $where[] = "not exists ( select *
                                        from processosapensados
                                        where p30_procapensado  = p58_codproc
                                          or p30_procprincipal = p58_codproc limit 1)
                      and p58_codproc != {$apensado} ";
            }

            //PLUGIN procjudicial
            if (isset($p07_orgao) && !empty($p07_orgao)) {
                if (ProcessoProtocoloNumeracao::getTipoConfiguracao() == ProcessoProtocoloNumeracao::TIPOORGAO) {
                    $where[] = "p58_orgao = {$p07_orgao}";
                } else {
                    $where[] = "db01_orgao = {$p07_orgao}";
                }
            }

        if (!isset($pesquisa_chave)) {

          if(!isset($chave_p58_ano)) {

            $chave_p58_ano = db_getsession("DB_anousu");
               $where[] = "p58_ano = $chave_p58_ano";
          }


          /**
           * Campo de pesquisa
           * Informou variavel pelo $_GET 'p58_codproc'
           */
          if ( !empty($oGet->sCampoPesquisa) && $oGet->sCampoPesquisa == 'p58_codproc' ) {

            $campos = [];
            $campos[]  = "p58_codproc, cast(p58_numero||'/'||p58_ano as varchar) as p58_numero,
                        z01_numcgm as DB_p58_numcgm,
                        z01_nome, p58_dtproc,p51_descr,p58_obs,p58_requer as DB_p58_requer,
                        {$sCampoInstituicao},(ov01_numero ||'/'||ov01_anousu)::varchar as ov01_numero";
                }
                $repassa = [];

                $ordem = "p58_codproc desc";

                if (isset($chave_p58_numcgm) && (trim($chave_p58_numcgm) != "")) {
                    $ordem = "p58_codproc desc";
                    $where[] = "p58_numcgm = $chave_p58_numcgm";
                } else {
                    if (isset($chave_p58_codproc) && (trim($chave_p58_codproc) != "")) {
                        $ordem = "p58_codproc desc";
                        $where[] = "p58_codproc = " . $chave_p58_codproc;
                    } else {
                        if (isset($chave_p58_requer) && (trim($chave_p58_requer) != "")) {
                            $ordem = "p58_codproc desc";
                            $where[] = "p58_requer ilike '$chave_p58_requer%'";
                        } else {
                            if (isset($chave_p51_descr) && (trim($chave_p51_descr) != "")) {
                                $ordem = "p51_descr desc";
                                $where[] = "p51_descr ilike '$chave_p51_descr%'";
                            } else {
                                if (isset($chave_p58_numero) && (trim($chave_p58_numero) != "")) {
                                    $aPartesNumero = explode("/", $chave_p58_numero);
                                    $iAno = db_getsession("DB_anousu");
                                    if (count($aPartesNumero) > 1) {
                                        $iAno = $aPartesNumero[1];
                                    }
                                    $iNumero = $aPartesNumero[0];
                                    $where[] = "p58_ano = {$iAno} and p58_numero = '{$iNumero}'";
                                } else {
                                    if (isset($chave_ov01_numero) && (trim($chave_ov01_numero) != "")) {
                                        $ordem = "p58_codproc desc";
                                        $aPartesNumero = explode("/", $chave_ov01_numero);
                                        $iAno = db_getsession("DB_anousu");
                                        if (count($aPartesNumero) > 1) {
                                            $iAno = $aPartesNumero[1];
                                        }
                                        $iNumero = $aPartesNumero[0];
                                        $where[] = "ov01_anousu = {$iAno} and ov01_numero = '{$iNumero}'";
                                    } else {
                                        if (isset($chave_unica) && ($chave_unica != '')) {
                                            $where[] = "p58_codproc = {$chave_unica}";
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if (isset($chave_p58_codproc)) {
                    $ordem = "p58_codproc desc";
                    $repassa = array("chave_p58_codproc" => $chave_p58_codproc);
                }

                $where = implode(' AND ', $where);
                $campos = implode(' ,', $campos);

                $sql = $clprotprocesso->sql_query("", $campos, $ordem, $where);

                db_lovrot($sql, 15, "()", "", $funcao_js, "", "NoMe", $repassa, false);
            } else {
                if ($pesquisa_chave != null && $pesquisa_chave != "") {
                    $aPesquisa = explode("/", $pesquisa_chave);
                    $iAno = db_getsession("DB_anousu");

                    if (count($aPesquisa) > 1) {
                        $iAno = $aPesquisa[1];
                    }

                    $sCampoPesquisa = 'p58_numero';

                    /**
                     * Campo de pesquisa
                     * Informou variavel pelo $_GET 'p58_codproc'
                     */
                    if (!empty($oGet->sCampoPesquisa)) {
                        $sCampoPesquisa = $oGet->sCampoPesquisa;
                    }
                    $where[] = "{$sCampoPesquisa} = '{$aPesquisa[0]}' and p58_ano = {$iAno}";
                    $where = implode(' AND ', $where);

                    $sSql = $clprotprocesso->sql_query("", "*", "", $where);
                    $result = $clprotprocesso->sql_record($sSql);

                    if ($clprotprocesso->numrows != 0) {
                        db_fieldsmemory($result, 0);

                        if (isset($retobs)) {
                            echo "<script>" . $funcao_js . "('$p58_numcgm','$p58_obs',false);</script>";
                        } else {
                            $sCampoRetorno = $p58_numero . '/' . $p58_ano;

                            if (!empty($oGet->sCampoRetorno)) {
                                $sCampoRetorno = $oGet->sCampoRetorno;
                                $sCampoRetorno = $$sCampoRetorno;
                            }

                            if (!empty($oGet->anexardocumento)) {
                                echo "<script>" . $funcao_js . "('{$p58_codproc}', '{$p58_numero}/{$p58_ano}', '{$z01_nome}'); </script> ";
                            } else {
                                echo "<script>" . $funcao_js . "('$sCampoRetorno', '$z01_nome', false, '$p58_codproc'); </script> ";
                            }
                        }
                    } else {
                        echo "<script>" . $funcao_js . "('','Chave(" . $pesquisa_chave . ") não Encontrado',true);</script>";
                    }
                } else {
                    echo "<script>" . $funcao_js . "('','',false);</script>";
                }
            }
            ?>
        </td>
    </tr>
</table>
</body>
</html>
<script type="text/javascript">
    (function () {
        var query = frameElement.getAttribute('name').replace('IF', ''),
            input = document.querySelector('input[value="Fechar"]');
        input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
    })();
</script>
