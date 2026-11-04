<?php

/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidadedbseller.com.br
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
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_rhteutri_classe.php"));
require_once(modification("classes/db_db_usuarios_classe.php"));

$clrhteutri   = new cl_rhteutri;
$cldbusuarios = new cl_db_usuarios;
$clrotulo     = new rotulocampo;

db_postmemory($_POST);

$rh67_rhtipovale = isset($_POST['rh67_rhtipovale']) ? $_POST['rh67_rhtipovale'] : '';
$rh68_descr      = $_POST['rh68_descr'];
$tipo_emissao    = $_POST['tipo_emissao'];
$ordem           = $_POST['ordem'];
$gera            = $_POST['gera'];

$iAno = DBPessoal::getAnoFolha();
$iMes = DBPessoal::getMesFolha();

if (isset($gera)) {

    $lEmitePorValor = ($tipo_emissao == "valor" ? true : false);

    if ($ordem == 'a') {
        $xordem = 'z01_nome';
    } else {
        $xordem = 'rh67_regist';
    }

    $where = " rh67_rhtipovale = $rh67_rhtipovale ";

    if ($tipo == 'a') {
        $where .= " and rh67_ativo = 't'";
    } elseif ($tipo == 'i') {
        $where .= " and rh67_ativo = 'f'";
    }

    $where .= " and rh67_anousu = $iAno ";
    $where .= " and rh67_mesusu = $iMes ";

    $usuario = db_getsession('DB_id_usuario');

    $sql = $cldbusuarios->sql_query_usuarios_cgm($usuario, "nome", null);

    $nomeUsuario = pg_query($conn, $sql);

    if (!$nomeUsuario) {
        throw new Exception("Erro na consulta: " . pg_last_error());
    }

    if (pg_num_rows($nomeUsuario) > 0) {
        $row = pg_fetch_assoc($nomeUsuario);
        $nome = DBString::removerAcentuacao($row['nome']);
    } else {
        echo "Nenhum registro encontrado.";
    }

    $constante = "valefunc";
    $nome      = $nome;

    $arq = "tmp/{$constante}.{$nome}.txt";

    $result = $clrhteutri->sql_record($clrhteutri->sql_query(null,
                                                            "rh67_regist,
                                                            z01_cgccpf,
                                                            z01_nome,
                                                            rh01_nasc,
                                                            rh01_regist,
                                                            z01_mae,
                                                            z01_uf,
                                                            z01_ender,
                                                            z01_numero,
                                                            z01_compl,
                                                            z01_bairro,
                                                            z01_munic,
                                                            z01_cep,
                                                            z01_ident ",
                                                            $xordem,
                                                            $where));
    $xxnum = pg_num_rows($result);

    $arquivo = fopen($arq, 'w');

    if ($rh67_rhtipovale == 2) {
        //Header
        $sHeader .= '0100' . PHP_EOL;

        fputs($arquivo, $sHeader);

        $iAno = DBPessoal::getAnoFolha();
        $iMes = DBPessoal::getMesFolha();

        for ($x = 0; $x < pg_num_rows($result); $x++) {
            db_fieldsmemory($result, $x);

            if ($lEmitePorValor) {

                $sSql  = "SELECT ROUND(SUM(valor), 2) AS valor ";
                $sSql .= "  FROM ( ";
                $sSql .= "       SELECT r16_valor * r63_quant AS valor ";
                $sSql .= "       FROM vtfempr ";
                $sSql .= "       INNER JOIN vtffunc ON vtffunc.r17_codigo = vtfempr.r16_codigo ";
                $sSql .= "                         AND vtffunc.r17_anousu = vtfempr.r16_anousu ";
                $sSql .= "                         AND vtffunc.r17_mesusu = vtfempr.r16_mesusu ";
                $sSql .= "       INNER JOIN vtfdias ON vtfdias.r63_vale   = vtfempr.r16_codigo ";
                $sSql .= "                         AND vtfdias.r63_anousu = vtfempr.r16_anousu ";
                $sSql .= "                         AND vtfdias.r63_mesusu = vtfempr.r16_mesusu "; 
                $sSql .= "       INNER JOIN rhteutri ON rhteutri.rh67_anousu = vtffunc.r17_anousu ";
                $sSql .= "                         AND rhteutri.rh67_mesusu = vtffunc.r17_mesusu ";
                $sSql .= "                         AND rhteutri.rh67_regist = vtffunc.r17_regist ";
                $sSql .= "       INNER JOIN rhpessoalmov ON rhpessoalmov.rh02_anousu = vtffunc.r17_anousu ";
                $sSql .= "                         AND rhpessoalmov.rh02_mesusu = vtffunc.r17_mesusu "; 
                $sSql .= "                         AND rhpessoalmov.rh02_regist = vtffunc.r17_regist ";
                $sSql .= "       LEFT JOIN rhpesrescisao ON rhpesrescisao.rh05_seqpes = rhpessoalmov.rh02_seqpes ";
                $sSql .= "       WHERE r16_anousu = $iAno ";
                $sSql .= "         AND r16_mesusu = $iMes ";
                $sSql .= "         AND r17_regist = {$rh67_regist} ";
                $sSql .= "         AND r63_regist = {$rh67_regist} ";
                $sSql .= "         AND r16_empres = '2' ";
                $sSql .= "         AND rh05_recis is null";
                $sSql .= "         AND r16_instit = " . db_getsession('DB_instit') . " ";
                $sSql .= "   GROUP BY r16_codigo, r16_valor, r63_quant ";               
                $sSql .= "  ) AS subquery ";

                $rsValorVtf = db_query($sSql);

                if (!$rsValorVtf || pg_num_rows($rsValorVtf) == 0) {
                    continue;
                }

                $nValor = str_replace(".", "", db_utils::fieldsMemory($rsValorVtf, 0)->valor);
                $nascimento = date('d/m/Y', strtotime($rh01_nasc));

                $sLinha  = db_formatar($z01_cgccpf, 's', ' ', 25, 'd', 0);
                $sLinha .= db_formatar($z01_nome, 's', ' ', 99, 'd', 0);
                $sLinha .= db_formatar($rh01_regist, 's', ' ', 29, 'd', 0);
                $sLinha .= db_formatar($nascimento, 's', ' ', 9, 'd', 0);
                $sLinha .= db_formatar($z01_mae, 's', ' ', 48, 'd', 0);
                $sLinha .= str_pad($nValor, 9, "0", STR_PAD_LEFT);
                $sLinha .= str_pad("", 12, " ", STR_PAD_LEFT);
                $sLinha .= db_formatar($z01_uf, 's', ' ', 2, 'd', 0);
                $sLinha .= db_formatar($z01_ender, 's', ' ', 199, 'd', 0);
                $sLinha .= db_formatar($z01_numero, 's', ' ', 14, 'd', 0);
                $sLinha .= db_formatar($z01_compl, 's', ' ', 49, 'd', 0);
                $sLinha .= db_formatar($z01_bairro, 's', ' ', 99, 'd', 0);
                $sLinha .= db_formatar($z01_munic, 's', ' ', 99, 'd', 0);
                $sLinha .= db_formatar($z01_ident, 's', ' ', 14, 'd', 0);
                $sLinha .= db_formatar($z01_cep, 's', 8, ' ', 'd', 0) . PHP_EOL;
                fputs($arquivo, $sLinha);
            }
        }
    }
    fclose($arquivo);
}

$oRetorno = (object)array( 'erro' => false,
                           'mensagem' => '',
                           'arquivo' => $arq);

echo JSON::create()->stringify($oRetorno);

?>
