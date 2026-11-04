<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBselller Servicos de Informatica
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

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("libs/db_utils.php"));

include(modification("dbforms/db_funcoes.php"));

include(modification("classes/db_itbi_classe.php"));
include(modification("classes/db_itbidadosimovel_classe.php"));
include(modification("classes/db_itbiavalia_classe.php"));
include(modification("classes/db_itbiavaliaformapagamentovalor_classe.php"));
include(modification("classes/db_paritbi_classe.php"));

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
db_postmemory($_POST);
db_postmemory($_GET);

$db_botao=1;
$clrotulo = new rotulocampo;
$cliptubase = new cl_iptubase;
$clrotulo->label("j01_matric");
$clrotulo->label("z01_nome");
$clrotulo->label("it22_setor");
$clrotulo->label("it22_descrlograd");
$clrotulo->label("it22_numero");
$clrotulo->label("it22_compl");
$clrotulo->label("it22_quadra");
$clrotulo->label("it22_lote");
$clrotulo->label("it01_areaterreno");
$clrotulo->label("it01_percentualareatransmitida");
$clrotulo->label("it01_areatrans");
$clrotulo->label("it05_frente");
$clrotulo->label("it05_fundos");
$clrotulo->label("it05_direito");
$clrotulo->label("it05_esquerdo");
$clrotulo->label("it01_tipotransacao");
$clrotulo->label("it04_descr");
$clrotulo->label("it01_valorterreno");
$clrotulo->label("it01_valorconstr");
$clrotulo->label("it01_valortransacao");
$clrotulo->label("imposto_avalia");
$clrotulo->label("taxas_avalia");
$clrotulo->label("it21_numcgm");
$clrotulo->label("total_avalia");
$clrotulo->label("z01_nome_transmitente");
$clrotulo->label("it03_cpfcnpj");
$clrotulo->label("it03_cep");
$clrotulo->label("it03_numero");
$clrotulo->label("it03_endereco");
$clrotulo->label("it03_compl");
$clrotulo->label("it03_bairro");
$clrotulo->label("it03_uf");
$clrotulo->label("it03_munic");




$cl_iptucalc = new cl_iptucalc();
$cl_iptucale = new cl_iptucale();

$db_opcao = 3;
$db_opcao_adqtransm= 3;
$iAnoUsu  = db_getsession('DB_anousu');

// Busca parâmetro de Matrícula Imovel Rural na tabela paritbi
$sql = "SELECT it24_matricrural FROM paritbi WHERE it24_anousu = $iAnoUsu";
$result = db_query($sql);
db_fieldsmemory($result, 0);

// dd( 'ma oi' ,
//     $result,
//     $iAnoUsu,
//     $it24_matricrural
// );

if (isset($matric)) {


    $db_opcao = 1;


    $rsConsultaDadosMatric = $cliptubase->sql_record($cliptubase->sql_query_regmovel($_GET['matric']));
    $dados =  \db_utils::fieldsMemory($rsConsultaDadosMatric, 0);

  if ($cliptubase->numrows > 0) {

    if ($dados->j01_tipoimovel === '1') {
        $msgErro = "Matrícula não permitida por ser do tipo urbano.";
        echo ("
        <script>
            alert('$msgErro')
            window.location.href = 'itbi_simulacaorural001.php'
        </script>"
        );

    }

    db_postmemory(pg_fetch_assoc($rsConsultaDadosMatric));

    $it01_areaterreno = $j34_area;


    if ($db21_codcli == 19985 || $db21_codcli == 100 ) {

      $it22_setor 	= $j05_codigoproprio;
      $it22_quadra	= $j06_quadraloc;
      $it22_lote 		= $j06_lote;
    } else {

      $it22_setor 	= $j34_setor;
      $it22_quadra	= $j34_quadra;
      $it22_lote 		= $j34_lote;
    }

    $it22_descrlograd = $j14_nome;
    $it22_compl 	    = $j39_compl;
    $it22_numero 	    = $j39_numero;
    $it05_frente 	    = $j36_testad;
    $it05_fundos 	    = $j36_testad;
    $it01_areatrans   = $j34_area;

    $it29_setorloc    = $j04_setorregimovel;
    $j05_descr        = $j69_descr;

    $it22_matricri    = $j04_matricregimo;
    $it22_quadrari    = $j04_quadraregimo;
    $it22_loteri      = $j04_loteregimo;

    $nLados           = ($j36_testad) ? ($j34_area / $j36_testad) : 0;

    $it05_direito     = round($nLados, 2);
    $it05_esquerdo    = round($nLados, 2);

    }

    $anousu = db_getsession("DB_anousu");

    $rIptucalc = db_query($cl_iptucalc->sql_query_file($anousu, $matric, "j23_vlrter AS it01_valorterreno"));

    if (!$rIptucalc) {
      throw new Exception("Erro ao buscar o os dados da tabela iptucalc");
    }

    db_postmemory(pg_fetch_assoc($rIptucalc));

    $rIptucale = db_query($cl_iptucale->sql_query(null, null, null, "SUM(j22_valor) AS it01_valorconstr", null, " j22_anousu = {$anousu} AND j22_matric = {$matric} AND j39_dtdemo IS NULL "));

    if (!$rIptucale) {
      throw new Exception("Erro ao buscar o os dados da tabela iptucale");
    }

    db_postmemory(pg_fetch_assoc($rIptucale));
}

if (Isset($it21_numcgm)) {
    $rCgm = $cl_cgm->sql_record($cl_cgm->sql_query_file($it21_numcgm));

    if (!$rCgm) {
        throw new Exception("Erro ao buscar os dados da tabela cgm");
    }

    $oDadosPropri = \db_utils::fieldsMemory($rCgm, 0);

    $it03_guia     = $clitbi->it01_guia;
    $it03_tipo     = 'T';
    $it03_princ    = 'true';
    $it03_nome     = addslashes($oDadosPropri->z01_nome);
    $it03_sexo     = 'm';
    $it03_cpfcnpj  = $oDadosPropri->z01_cgccpf;
    $it03_endereco = addslashes($oDadosPropri->z01_ender);
    $it03_numero   = $oDadosPropri->z01_numero;
    $it03_compl    = $oDadosPropri->z01_compl;
    $it03_cxpostal = $oDadosPropri->z01_cxpostal;
    $it03_bairro   = addslashes($oDadosPropri->z01_bairro);
    $it03_munic    = $oDadosPropri->z01_munic;
    $it03_uf       = $oDadosPropri->z01_uf;
    $it03_cep      = $oDadosPropri->z01_cep;
    $it03_mail     = $oDadosPropri->z01_email;
}


?>

<html>

<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/datagrid.widget.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1">
    <center>
        <form name="form1">
            <table style="padding-top:25px;" align="center" border="0" cellspacing="0" cellpadding="0">
            <tr align="center">
                <td>
                <strong>I.T.B.I. RURAL</strong>
                </td>
            </tr>
            <?php

            if ($it24_matricrural !== 'f'):?>
                <tr>
                    <td>
                        <fieldset>
                            <table>
                                <tr>
                                    <td>
                                        <?php
                                        db_ancora("<b>Matrícula :</b>", ' js_matri(true); ', 1);
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        db_input('j01_matric', 10, $Ij01_matric, true, 'text', 1, "onchange='js_matri(false)'");
                                        db_input('z01_nome', 40, 0, true, 'text', 3, "", "z01_nome");
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </td>
                </tr>
            <?php endif;  ?>

                <tr>
                    <td>
                        <fieldset>
                            <legend>
                                <strong>Localização</strong>
                            </legend>
                            <table>
                                <tr>
                                    <td colspan="1">
                                        <strong>Setor/Bairro:</strong>
                                    </td>
                                    <td colspan="3">
                                        <?php
                                        db_input('it22_setor', 20, $Iit22_setor, true, 'text', $db_opcao);
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="1">
                                        <strong>Logradouro:</strong>
                                    </td>
                                    <td colspan="3">
                                        <?php
                                        db_input('it22_descrlograd', 114, $Iit22_descrlograd, true, 'text', $db_opcao, "");
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="1">
                                        <strong>Número:</strong>
                                    </td>
                                    <td colspan="1" width="165px">
                                        <?php
                                        db_input('it22_numero', 20, $Iit22_numero, true, 'text', $db_opcao, "");
                                        ?>
                                    </td>
                                    <td colspan="1">
                                        <strong>Complemento:</strong>
                                    </td>
                                    <td colspan="1">
                                        <?php
                                        db_input('it22_compl', 77, $Iit22_compl, true, 'text', $db_opcao, "");
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="1">
                                        <strong>Quadra:</strong>
                                    </td>
                                    <td colspan="1">
                                        <?php
                                        db_input('it22_quadra', 20, $Iit22_quadra, true, 'text', $db_opcao, "");
                                        ?>
                                    </td>
                                    <td colspan="1">
                                        <strong>Lote:</strong>
                                    </td>
                                    <td colspan="1">
                                        <?php
                                        db_input('it22_lote', 20, $Iit22_lote, true, 'text', $db_opcao, "");
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
                            <legend>
                                <strong>Medidas</strong>
                            </legend>
                            <table width="100%">
                                <tr>
                                    <td colspan="1">
                                        <strong>Área Total:</strong>
                                    </td>
                                    <td colspan="1" width="185px">
                                        <?php
                                        db_input('it01_areaterreno', 20, $Iit01_areaterreno, true, 'text', $db_opcao, " onblur=\"js_limpaCalculo()\"");
                                        ?>
                                        <strong>m²</strong>
                                    </td>

                                    <td colspan="1" width="170px">
                                        <strong>Percentual Área Transmitida:<strong>
                                    </td>
                                    <td colspan="1" width="180px">
                                        <?php
                                        db_input("it01_percentualareatransmitida", 20, $Iit01_percentualareatransmitida, true, "text", $db_opcao, " onkeyup=\"js_calculaPorcentagem(this, $('it01_areatrans'), false)\"");
                                        ?>
                                    </td>

                                    <td colspan="1" width="105px">
                                        <strong>Área Transmitida:</strong>
                                    </td>
                                    <td colspan="1">
                                        <?php
                                        db_input('it01_areatrans', 20, $Iit01_areatrans, true, 'text', $db_opcao, " onkeyup=\"js_calculaPorcentagem(this, $('it01_percentualareatransmitida'), true)\"");
                                        ?>
                                        <strong>m²</strong>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="1">
                                        <strong>Frente:</strong>
                                    </td>
                                    <td colspan="1">
                                        <?php
                                        db_input('it05_frente', 20, $Iit05_frente, true, 'text', $db_opcao, "");
                                        ?>
                                        <strong>m</strong>
                                    </td>
                                    <td colspan="1">
                                        <strong>Fundos:</strong>
                                    </td>
                                    <td colspan="3">
                                        <?php
                                        db_input('it05_fundos', 20, $Iit05_fundos, true, 'text', $db_opcao, "");
                                        ?>
                                        <strong>m</strong>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="1" width="75px">
                                        <strong>Lado Direito:</strong>
                                    </td>
                                    <td colspan="1">
                                        <?php
                                        db_input('it05_direito', 20, $Iit05_direito, true, 'text', $db_opcao, "");
                                        ?>
                                        <strong>m</strong>
                                    </td>

                                    <td colspan="1">
                                        <strong>Lado Esquerdo:</strong>
                                    </td>
                                    <td colspan="3">
                                        <?php
                                        db_input('it05_esquerdo', 20, $Iit05_esquerdo, true, 'text', $db_opcao, "");
                                        ?>
                                        <strong>m</strong>
                                    </td>
                                </tr>

                            </table>
                        </fieldset>
                    </td>
                </tr>

                <tr>
                    <td>
                        <fieldset>
                            <legend>
                                <strong>Dados da Transação</strong>
                            </legend>
                            <table width="100%">
                                <tr>
                                    <td title="<?php echo $Tit01_tipotransacao; ?>" width="108px" colspan="1">
                                        <?php
                                        db_ancora("Tipo De Transação", "js_pesquisait01_tipotransacao(true);", $db_opcao);
                                        ?>
                                    </td>
                                    <td colspan="5">
                                        <?php
                                        db_input('it01_tipotransacao', 20, $Iit01_tipotransacao, true, 'text', $db_opcao, " onBlur='js_pesquisait01_tipotransacao(false);'");
                                        ?>
                                        <?php
                                        db_input('it04_descr', 87, $Iit04_descr, true, 'text', 3, '');
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="1">
                                        <strong>Valor <?= $sPrefix . $sTerraLabel ?>:</strong>
                                    </td>
                                    <td colspan="1" width="165px">
                                        <?php
                                        db_input('it01_valorterreno', 20, $Iit01_valorterreno, true, 'text', $db_opcao, "onkeyup='js_validaValores(this)'");
                                        ?>
                                    </td>
                                    <td colspan="1" width="130px">
                                        <strong>Valor das Benfeitorias:</strong>
                                    </td>
                                    <td colspan="1" width="165px">
                                        <?php
                                        db_input('it01_valorconstr', 20, $Iit01_valorconstr, true, 'text', $db_opcao, "onkeyup='js_validaValores(this)'");
                                        ?>
                                    </td>
                                    <td colspan="1" width="63px">
                                        <strong>Valor Total:</strong>
                                    </td>
                                    <td colspan="1">
                                        <?php
                                            db_input('it01_valortransacao',20,$Iit01_valortransacao,true,'text',$db_opcao,"onkeyup='js_validaValores(this)'");
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
                            <legend>
                                <strong>Dados de Pagamento</strong>
                            </legend>
                            <div id="listaFormasPgto" width="700px"></div>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <td>
                        <fieldset>
                            <table width="100%">
                                <legend>
                                    <strong>Taxas</strong>
                                </legend>
                                <tr>
                                    <td style="width: 40px;">
                                        <strong>Tipo:</strong>
                                    </td>
                                    <td>
                                        <select name="codigoTipoTaxa" id="tipoTaxa" onchange="js_buscarTaxaTipo(this.value)">
                                            <option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>
                                        </select>
                                    </td>
                                </tr>
                                <!-- <div id="ctnGridTaxas" width="700px"></div> -->
                                <tr>
                                    <td id="ctnGridTaxas" colspan="6">

                                    </td>

                                </tr>
                            </table>

                        </fieldset>
                    </td>
                </tr>

                <tr>
                    <td>
                        <table>
                            <tr>
                                <td colspan="6">

                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Valor do Imposto R$:</b>
                                </td>
                                <td>
                                    <?php
                                        db_input('imposto_avalia',15,"",true,'text',3,"");
                                        ?>
                                </td>
                                <td style="padding-left: 110px;">
                                    <b>Valor das Taxas R$:</b>
                                </td>
                                <td>
                                    <?php
                                        db_input('taxas_avalia',15,"",true,'text',3,"");
                                        ?>
                                </td>
                                <td style="padding-left: 130px;">
                                    <b>Valor Total R$:</b>
                                </td>
                                <td>
                                    <?php
                                        db_input('total_avalia',15,"",true,'text',3,"");
                                        ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <fieldset>
                            <legend>Transmitente</legend>


                            <table>


                                <tr>
                                    <td nowrap title="<?php echo $Tit21_numcgm; ?>">
                                        <?php
                                        $GLOBALS['Lit21_numcgm'] = 'CGM/Nome:';
                                        db_ancora($Lit21_numcgm, "js_pesquisait21_numcgm(true);", $db_opcao);
                                        ?>
                                    </td>
                                    <td nowrap colspan="3">
                                        <?php
                                        db_input('it21_numcgm', 14, $Iit21_numcgm, true, 'text', $db_opcao, " onchange='js_pesquisait21_numcgm(false);'");
                                        db_input('z01_nome_transmitente', 52, $Iz01_nome, true, 'text', 3, '');
                                        ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td nowrap title="<?php echo $Tit03_cpfcnpj; ?>"><strong>CPF/CNPJ:</strong></td>
                                    <td>
                                        <?php
                                        db_input('it03_cpfcnpj', 14, $Iit03_cpfcnpj, true, 'text', $db_opcao_adqtransm, "  onBlur='js_verificaCGCCPF(this)' onKeyDown='return js_controla_tecla_enter(this,event);' onKeyUp='js_limpa(this)' ");
                                        ?>
                                        <script type="text/javascript">
                                            function js_limpa(obj) {
                                                x = obj.value;
                                                y = x.replace('.', '');
                                                y = y.replace('/', '');
                                                y = y.replace('-', '');
                                                document.form1.it03_cpfcnpj.value = y;
                                            }
                                        </script>

                                    </td>
                                    <td width="87px" title="<?php echo $Tit03_cep; ?>"><strong>CEP:</strong></td>
                                    <td>
                                        <?php
                                        db_input('it03_cep', 39, $Iit03_endereco, true, 'text', $db_opcao_adqtransm, "");
                                        ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td nowrap title="<?php echo $Tit03_numero; ?>"><strong>Número:</strong></td>
                                    <td>
                                        <?php
                                        db_input('it03_numero', 14, $Iit03_numero, true, 'text', $db_opcao_adqtransm, "");
                                        ?>
                                    </td>
                                    <td width="87px"><strong>Endereco:</strong></td>
                                    <td>
                                        <?php
                                        db_input('it03_endereco', 39, $Iit03_endereco, true, 'text', $db_opcao_adqtransm, "");
                                        ?>
                                    </td>
                                </tr>


                                <tr>
                                    <td nowrap><strong>Complemento:</strong></td>
                                    <td>
                                        <?php
                                        db_input('it03_compl', 14, $Iit03_compl, true, 'text', $db_opcao_adqtransm, "");
                                        ?>
                                    </td>
                                    <td><strong>Bairro:</strong></td>
                                    <td>
                                        <?php
                                        db_input('it03_bairro', 39, $Iit03_bairro, true, 'text', $db_opcao_adqtransm, "");
                                        ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td nowrap title="<?php echo $Tit03_uf; ?>"><strong>UF:</strong></td>
                                    <td nowrap>
                                        <?php
                                        db_input('it03_uf', 14, $Iit03_uf, true, 'text', $db_opcao_adqtransm, "");
                                        ?>
                                    </td>
                                    <td nowrap title="<?php echo $Tit03_munic; ?>"><strong>Município:</strong></td>
                                    <td colspan="3">
                                        <?php
                                        db_input('it03_munic', 39, $Iit03_munic, true, 'text', $db_opcao_adqtransm, "");
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
                            <legend>Adquirentes</legend>
                            <table>
                                <tr>
                                    <td nowrap title="<?php echo $Tit21_numcgm; ?>">
                                        <?php
                                        $GLOBALS['Lit21_numcgm'] = 'CGM/Nome:';
                                        db_ancora($Lit21_numcgm, "js_pesquisait21_numcgm_adquirente(true);", $db_opcao);
                                        ?>
                                    </td>
                                    <td nowrap colspan="3">
                                        <?php
                                        db_input('it21_numcgm_adquirentes', 14, $Iit21_numcgm, true, 'text', $db_opcao, " onchange='js_pesquisait21_numcgm_adquirente(false);'", 'it21_numcgm_adquirentes');
                                        db_input('z01_nome_adquirentes', 53, $Iz01_nome, true, 'text', 3, '', 'z01_nome_adquirentes');
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td nowrap title="<?php echo $Tit03_cpfcnpj; ?>"><strong>CPF/CNPJ:</strong></td>
                                    <td>
                                        <?php
                                        db_input('it03_cpfcnpj_adquirentes', 14, $Iit03_cpfcnpj, true, 'text', $db_opcao_adqtransm, "  onBlur='js_verificaCGCCPF(this)' onKeyDown='return js_controla_tecla_enter(this,event);' onKeyUp='js_limpa(this)' ", 'it03_cpfcnpj_adquirentes');
                                        ?>
                                        <script type="text/javascript">
                                            function js_limpa(obj) {
                                                x = obj.value;
                                                y = x.replace('.', '');
                                                y = y.replace('/', '');
                                                y = y.replace('-', '');
                                                document.form1.it03_cpfcnpj.value = y;
                                            }
                                        </script>

                                    </td>
                                    <td width="87px" title="<?php echo $Tit03_cep; ?>"><strong>CEP:</strong></td>
                                    <td>
                                        <?php
                                        db_input('it03_cep_adquirentes', 39, $Iit03_endereco, true, 'text', $db_opcao_adqtransm, "", 'it03_cep_adquirentes');
                                        ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td nowrap title="<?php echo $Tit03_numero; ?>"><strong>Número:</strong></td>
                                    <td>
                                        <?php
                                        db_input('it03_numero_adquirentes', 14, $Iit03_numero, true, 'text', $db_opcao_adqtransm, "", 'it03_numero_adquirentes');
                                        ?>
                                    </td>
                                    <td width="87px"><strong>Endereco:</strong></td>
                                    <td>
                                        <?php
                                        db_input('it03_endereco_adquirentes', 39, $Iit03_endereco, true, 'text', $db_opcao_adqtransm, "", 'it03_endereco_adquirentes');
                                        ?>
                                    </td>
                                </tr>


                                <tr>
                                    <td nowrap><strong>Complemento:</strong></td>
                                    <td>
                                        <?php
                                        db_input('it03_compl_adquirentes', 14, $Iit03_compl, true, 'text', $db_opcao_adqtransm, "", 'it03_compl_adquirentes');
                                        ?>
                                    </td>
                                    <td><strong>Bairro:</strong></td>
                                    <td>
                                        <?php
                                        db_input('it03_bairro_adquirentes', 39, $Iit03_bairro, true, 'text', $db_opcao_adqtransm, "", 'it03_bairro_adquirentes');
                                        ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td nowrap title="<?php echo $Tit03_uf; ?>"><strong>UF:</strong></td>
                                    <td nowrap>
                                        <?php
                                        db_input('it03_uf_adquirentes', 14, $Iit03_uf, true, 'text', $db_opcao_adqtransm, "", 'it03_uf_adquirentes');
                                        ?>
                                    </td>
                                    <td nowrap title="<?php echo $Tit03_munic; ?>"><strong>Município:</strong></td>
                                    <td colspan="3">
                                        <?php
                                        db_input('it03_munic_adquirentes', 39, $Iit03_munic, true, 'text', $db_opcao_adqtransm, "", 'it03_munic_adquirentes');
                                        ?>
                                    </td>
                                </tr>
                        </fieldset>
                    </td>
                </tr>

            </table>

            </fieldset>
            <br>
        <center>
            <input type="button" onclick="gerar_simulacao()" value="Simular" />
            <input type="button" onclick="js_novaSimulacao()" value="Nova Simulação" />
        </center>

        </form>


    </center>



</body>

<script>

    function js_novaSimulacao() {
        window.location.href = 'itbi_simulacaorural001.php';
    }

    function gerar_simulacao() {
        if (js_verificaCampoObrigatorio()) {
            return false;
        }

        const frmsPgto = document.querySelectorAll(".formasPgto");
        var x, dadosDePagamento = [];

        for (i = 0; i < frmsPgto.length; i++) {
            var dadoPagamento = {
                descricao: frmsPgto[i].getAttribute('descricao'),
                aliquota: frmsPgto[i].getAttribute('aliquota'),
                valor: frmsPgto[i].value
            }
            dadosDePagamento.push(dadoPagamento);
        }


        console.log(dadosDePagamento);

        const taxasElementos = document.querySelectorAll("[isTaxa='true']");


        var i, taxas = [];
        for (i = 0; i < taxasElementos.length; i++) {
            var taxa = {
                id: taxasElementos[i].id,
                nome: taxasElementos[i].getAttribute('nome'),
                percentual: taxasElementos[i].getAttribute('percentual'),
                valor: taxasElementos[i].innerHTML
            }

            taxas.push(taxa);

        }

        const obj = document.form1;

        var sUrl = 'itbi_simulacaoitbi002.php?';
        sUrl += 'it22_setor=' + obj.it22_setor.value + '&';
        sUrl += 'it22_descrlograd=' + obj.it22_descrlograd.value + '&';
        sUrl += 'it22_numero=' + obj.it22_numero.value + '&';
        sUrl += 'it22_compl=' + obj.it22_compl.value + '&';
        sUrl += 'it22_quadra=' + obj.it22_quadra.value + '&';
        sUrl += 'it22_lote=' + obj.it22_lote.value + '&';
        sUrl += 'it01_areaterreno=' + obj.it01_areaterreno.value + '&';
        sUrl += 'it01_percentualareatransmitida=' + obj.it01_percentualareatransmitida.value + '&';
        sUrl += 'it01_areatrans=' + obj.it01_areatrans.value + '&';
        sUrl += 'it05_frente=' + obj.it05_frente.value + '&';
        sUrl += 'it05_fundos=' + obj.it05_fundos.value + '&';
        sUrl += 'it05_direito=' + obj.it05_direito.value + '&';
        sUrl += 'it05_esquerdo=' + obj.it05_esquerdo.value + '&';
        sUrl += 'it01_tipotransacao=' + obj.it01_tipotransacao.value + '&';
        sUrl += 'it04_descr=' + obj.it04_descr.value + '&';
        sUrl += 'it01_valorterreno=' + obj.it01_valorterreno.value + '&';
        sUrl += 'it01_valorconstr=' + obj.it01_valorconstr.value + '&';
        sUrl += 'it01_valortransacao=' + obj.it01_valortransacao.value + '&';
        sUrl += 'codigoTipoTaxa=' + obj.codigoTipoTaxa.value + '&';
        sUrl += 'imposto_avalia=' + obj.imposto_avalia.value + '&';
        sUrl += 'taxas_avalia=' + obj.taxas_avalia.value + '&';
        sUrl += 'total_avalia=' + obj.total_avalia.value + '&';
        sUrl += 'it21_numcgm=' + obj.it21_numcgm.value + '&';
        sUrl += 'z01_nome_transmitente=' + obj.z01_nome_transmitente.value + '&';
        sUrl += 'it03_cpfcnpj=' + obj.it03_cpfcnpj.value + '&';
        sUrl += 'it03_endereco=' + obj.it03_endereco.value + '&';
        sUrl += 'it03_numero=' + obj.it03_numero.value + '&';
        sUrl += 'it03_bairro=' + obj.it03_bairro.value + '&';
        sUrl += 'it03_compl=' + obj.it03_compl.value + '&';
        sUrl += 'it03_uf=' + obj.it03_uf.value + '&';
        sUrl += 'it03_cep=' + obj.it03_cep.value + '&';
        sUrl += 'it03_munic=' + obj.it03_munic.value + '&';
        sUrl += 'it21_numcgm_adquirentes=' + obj.it21_numcgm_adquirentes.value + '&';
        sUrl += 'z01_nome_adquirentes=' + obj.z01_nome_adquirentes.value + '&';
        sUrl += 'it03_cpfcnpj_adquirentes=' + obj.it03_cpfcnpj_adquirentes.value + '&';
        sUrl += 'it03_endereco_adquirentes=' + obj.it03_endereco_adquirentes.value + '&';
        sUrl += 'it03_numero_adquirentes=' + obj.it03_numero_adquirentes.value + '&';
        sUrl += 'it03_bairro_adquirentes=' + obj.it03_bairro_adquirentes.value + '&';
        sUrl += 'it03_compl_adquirentes=' + obj.it03_compl_adquirentes.value + '&';
        sUrl += 'it03_uf_adquirentes=' + obj.it03_uf_adquirentes.value + '&';
        sUrl += 'it03_cep_adquirentes=' + obj.it03_cep_adquirentes.value + '&';
        sUrl += 'it03_munic_adquirentes=' + obj.it03_munic_adquirentes.value + '&';
        sUrl += 'taxa=' + JSON.stringify(taxas) + '&';
        sUrl += 'dadosPgto=' + JSON.stringify(dadosDePagamento) + '&';



        jan = window.open(sUrl, '', 'width=' + (screen.availWidth - 5) + ', height=' + (screen.availHeight - 40) + ', scrollbars=1, location=0');
        jan.moveTo(0, 0);

        obj.reset();
    }

        var oGridTaxas = new DBGrid('gridTaxas');
        var aHeaders = ["Código", "Descrição", "Tipo de Valor", "Calcula Sobre", "Aliquota %", "Valor"];
        var aCellWidth = ["10%", "40%", "15%", "15%", "10%", "10%"];
        var aCellAlign = ["center", "left", "center", "center", "center", "center"];

        oGridTaxas.nameInstance = 'oGridTaxas';
        oGridTaxas.setCellWidth(aCellWidth);
        oGridTaxas.setCellAlign(aCellAlign);
        oGridTaxas.setHeader(aHeaders);
        oGridTaxas.setHeight(100);
        oGridTaxas.show($('ctnGridTaxas'));

        const spanPersonalizado_gridTaxas = document.getElementById("spanPersonalizado_gridTaxas");
        spanPersonalizado_gridTaxas.setAttribute("style", "float: right; color: blue");


        (function() {

        if ($F('it01_areatrans') != '') {
            js_calculaPorcentagem($('it01_areatrans'), $('it01_percentualareatransmitida'), true);
        }
        })();



    js_buscaTipos();

    function js_buscaTipos() {
        const tipoTaxa = document.getElementById("tipoTaxa");

        var oParam = new Object();
        oParam.executa = "listarTipos";
        oParam.tipo = "urbano";

        new AjaxRequest("itbi_taxasitbi001.RPC.php", oParam, function(oRetorno) {
            const aTipos = oRetorno.aTipos;

            aTipos.forEach(function(oTipo) {
                const option = document.createElement("option");
                option.setAttribute("value", oTipo.it36_sequencial);
                option.innerHTML = oTipo.it36_descricao;
                if (aTipos.length == 1) {
                    option.setAttribute("selected", "selected");
                    js_buscarTaxaTipo(oTipo.it36_sequencial);
                }

                tipoTaxa.appendChild(option);
            });
        }).execute();
    }

    function js_mostracgm1(chave1, chave2) {

        document.form1.it21_numcgm.value = chave1;
        document.form1.z01_nome_transmitente.value = chave2;
        db_iframe_cgm.hide();
        const oParam = new Object();
                oParam.executa = "buscar";
                oParam.cgm = document.form1.it21_numcgm.value;

                new AjaxRequest("itbi_simulacaourbanoRPC.php", oParam, js_setaCamposTransmitente).execute();
    }

    function js_setaCamposTransmitente(oRetorno)
    {
        document.form1.z01_nome_transmitente.value = oRetorno.oDadosCgm.nome
        document.form1.it03_cpfcnpj.value =  oRetorno.oDadosCgm.cpfOuCnpj;
        document.form1.it03_endereco.value = oRetorno.oDadosCgm.endereco;
        document.form1.it03_numero.value = oRetorno.oDadosCgm.numero;
        document.form1.it03_bairro.value = oRetorno.oDadosCgm.bairro;
        document.form1.it03_compl.value = oRetorno.oDadosCgm.complemento;
        document.form1.it03_uf.value = oRetorno.oDadosCgm.uf;
        document.form1.it03_cep.value = oRetorno.oDadosCgm.cep;
        document.form1.it03_munic.value = oRetorno.oDadosCgm.municipio;
    }

    function js_setaCamposAdquirentes(oRetorno)
    {
        document.form1.z01_nome_adquirentes.value = oRetorno.oDadosCgm.nome
        document.form1.it03_cpfcnpj_adquirentes.value =  oRetorno.oDadosCgm.cpfOuCnpj;
        document.form1.it03_endereco_adquirentes.value = oRetorno.oDadosCgm.endereco;
        document.form1.it03_numero_adquirentes.value = oRetorno.oDadosCgm.numero;
        document.form1.it03_bairro_adquirentes.value = oRetorno.oDadosCgm.bairro;
        document.form1.it03_compl_adquirentes.value = oRetorno.oDadosCgm.complemento;
        document.form1.it03_uf_adquirentes.value = oRetorno.oDadosCgm.uf;
        document.form1.it03_cep_adquirentes.value = oRetorno.oDadosCgm.cep;
        document.form1.it03_munic_adquirentes.value = oRetorno.oDadosCgm.municipio;
    }

    function js_buscaDadosCgm(cgm, fCallback)
    {
        const oParam = new Object();
        oParam.executa = "buscar";
        oParam.cgm = cgm;

        new AjaxRequest("itbi_simulacaourbanoRPC.php", oParam, fCallback).execute();
        }


        function js_pesquisait21_numcgm(mostra) {

        if (mostra == true) {
            js_OpenJanelaIframe('', 'db_iframe_cgm', 'func_nome.php?funcao_js=parent.js_mostracgm1|z01_numcgm|z01_nome&testanome=1', 'Pesquisa', true);

        } else {

            if (document.form1.it21_numcgm.value != '') {
                js_OpenJanelaIframe('', 'db_iframe_cgm', 'func_nome.php?pesquisa_chave=' + document.form1.it21_numcgm.value + '&funcao_js=parent.js_mostracgm&testanome=1', 'Pesquisa', false);


                const cgm = document.form1.it21_numcgm.value;
                js_buscaDadosCgm(cgm, js_setaCamposTransmitente);

            } else {
                document.form1.z01_nome.value = '';
            }
        }
    }

    function js_mostracgm1_adquirente(chave1, chave2) {

        document.form1.it21_numcgm_adquirentes.value = chave1;
        document.form1.z01_nome_adquirentes.value = chave2;
        db_iframe_cgm.hide();

        const cgm = document.form1.it21_numcgm_adquirentes.value;

        js_buscaDadosCgm($cgm, js_setaCamposAdquirentes);


    }


        function js_pesquisait21_numcgm_adquirente(mostra) {

        if (mostra == true) {
            js_OpenJanelaIframe('', 'db_iframe_cgm', 'func_nome.php?funcao_js=parent.js_mostracgm1_adquirente|z01_numcgm|z01_nome&testanome=1', 'Pesquisa', true);
        } else {

            if (document.form1.it21_numcgm_adquirentes.value != '') {
                js_OpenJanelaIframe('', 'db_iframe_cgm', 'func_nome.php?pesquisa_chave=' + document.form1.it21_numcgm_adquirentes.value + '&funcao_js=parent.js_mostracgm&testanome=1', 'Pesquisa', false);

                const cgm = document.form1.it21_numcgm_adquirentes.value;
                js_buscaDadosCgm(cgm, js_setaCamposAdquirentes);
            } else {
                document.form1.z01_nome.value = '';
            }
        }
}



    function js_buscarTaxaTipo(codigo) {
        oGridTaxas.clearAll(true);

        if (codigo != "" && codigo != undefined) {
            var oParam = new Object();
            oParam.executa = "buscarTaxasTipo";
            oParam.it36_sequencial = codigo;

            new AjaxRequest("itbi_taxasitbi001.RPC.php", oParam, function(oRetorno) {
                const aTaxas = oRetorno.aTaxas;

                aTaxas.forEach(function(oTaxa) {
                    var aLinha = [];
                    aLinha.push(oTaxa.ar44_sequencial);
                    aLinha.push(oTaxa.ar44_descricao);

                    const span = document.createElement("span");

                    if (oTaxa.ar44_tipo == 2) {
                        aLinha.push("Percentual");

                        if (oTaxa.it37_calculasobre == 1) {
                            aLinha.push("Valor Venal do Terreno");
                        } else if (oTaxa.it37_calculasobre == 2) {
                            aLinha.push("Valor Venal da Construção");
                        } else if (oTaxa.it37_calculasobre == 3) {
                            aLinha.push("Ambos");
                        }

                        aLinha.push(oTaxa.i02_valor.toLocaleString('pt-BR', {
                            maximumFractionDigits: 2
                        }));


                        span.setAttribute("percentual", parseFloat(oTaxa.i02_valor).toFixed(2));
                        span.setAttribute("sobre", oTaxa.it37_calculasobre);
                        span.setAttribute("isPercentual", "true");
                        span.setAttribute("isTaxa", "true");
                        span.innerHTML = 0;
                    } else {
                        aLinha.push("Fixo");
                        aLinha.push("");
                        aLinha.push("");

                        span.setAttribute("isTaxa", "true");
                        span.innerHTML = oTaxa.i02_valor.toLocaleString('pt-BR', {
                            maximumFractionDigits: 2
                        });
                    }

                    span.setAttribute("id", oTaxa.ar44_sequencial);
                    span.setAttribute("nome", oTaxa.ar44_descricao);

                    aLinha.push(span.outerHTML);

                    oGridTaxas.addRow(aLinha);
                });

                oGridTaxas.renderRows();

                js_validaValores(document.getElementById("it01_valorterreno"));
                js_validaValores(document.getElementById("it01_valorconstr"));
            }).execute();
        }
    }

    function js_calculaPorcentagem(oElemento, oElementoCalculo, lReverse) {

        var oAreaTotal = $('it01_areaterreno');

        if (oAreaTotal.value == '') {

            alert('Campo Área Total deve ser preenchido.');
            return;
        }

        if (oElemento.value != '') {

            var iDecimal = 6;

            oElementoCalculo.value = (lReverse) ? ((+oElemento.value * 100) / +oAreaTotal.value) :
                (+oAreaTotal.value * (+oElemento.value / 100));

            if (lReverse) {
                iDecimal = 8;
            }

            if (isNaN(oElementoCalculo.value) || oElementoCalculo.value == Infinity || isNaN(oElementoCalculo.value)) {

                oElementoCalculo.value = '';
                oElemento.value = '';
            } else {
                oElementoCalculo.value = parseFloat((new Number(oElementoCalculo.value)).toFixed(iDecimal));
            }

        } else {
            oElementoCalculo.value = '';
        }
    }


    function js_pesquisait01_tipotransacao(mostra) {

        if (mostra == true) {
            js_OpenJanelaIframe('', 'db_iframe_itbitransacao', 'func_itbitransacao.php?validadata=true&funcao_js=parent.js_mostraitbitransacao1|it04_codigo|it04_descr', 'Pesquisa', true);
        } else {

            if (document.form1.it01_tipotransacao.value != '') {
                js_OpenJanelaIframe('', 'db_iframe_itbitransacao', 'func_itbitransacao.php?validadata=true&pesquisa_chave=' + document.form1.it01_tipotransacao.value + '&funcao_js=parent.js_mostraitbitransacao', 'Pesquisa', false);
            } else {
                document.form1.it04_descr.value = '';
            }
        }
    }

    function js_mostraitbitransacao(chave, erro) {

        document.form1.it04_descr.value = chave;

        if (erro == true) {

            document.form1.it01_tipotransacao.focus();
            document.form1.it01_tipotransacao.value = '';
        } else {
            js_consultaFormaPgto(document.form1.it01_tipotransacao.value);
        }
    }

    function js_mostraitbitransacao1(chave1, chave2) {

        document.form1.it01_tipotransacao.value = chave1;
        document.form1.it04_descr.value = chave2;
        db_iframe_itbitransacao.hide();
        js_consultaFormaPgto(chave1);
    }

    function js_consultaFormaPgto(iCodTransacao) {

        js_divCarregando('Aguarde...', 'msgBoxB');

        var url = "itb4_consultaformaPagamentoRPC.php";
        var sQuery = "codtransacao=" + iCodTransacao;
        sQuery += "&tipoPesquisa=formasDisponiveis";
        var oAjax = new Ajax.Request(url, {
            method: 'post',
            parameters: sQuery,
            onComplete: js_retornoFormaPgto
        });
    }



    function js_retornoFormaPgto(oAjax) {

        js_removeObj("msgBoxB");
        var objListaForma = JSON.parse(oAjax.responseText);
        var nValor = 0;


        gridFormasPgto.clearAll(true);

        if (objListaForma.iStatus && objListaForma.iStatus == 2) {
            alert(objListaForma.sMensagem.urlDecode());
            return false;
        }

        for (var iInd = 0; iInd < objListaForma.length; iInd++) {

            with(objListaForma[iInd]) {

                if (iInd == 0) {
                    nValor = document.form1.it01_valortransacao.value;
                    var sDisabled = "disabled";
                    var sNomeCampo = "name='primeiro'";
                } else {
                    nValor = 0;
                    var sDisabled = "";
                    var sNomeCampo = "";
                }

                var sInputValor = "<input type='text' aliquota='" + js_formatar(it27_aliquota.urlDecode(), 'f') + "' descricao='" + it27_descricao.urlDecode() + "'id='" + it25_sequencial.urlDecode() + "' class='formasPgto' value='" + nValor + "'";
                sInputValor += "style='width:100%;text-align:right;height:100%;border:1px inset' " + sDisabled + " " + sNomeCampo + "";
                sInputValor += " oninput='js_controlaValoresFormaPgto(this);'>";

                var aLinha = new Array();
                aLinha[0] = it27_descricao.urlDecode();
                aLinha[1] = js_formatar(it27_aliquota.urlDecode(), 'f');
                aLinha[2] = sInputValor;

                gridFormasPgto.addRow(aLinha);
                gridFormasPgto.aRows[iInd].isSelected = true;
                gridFormasPgto.renderRows();
            }
        }

        document.form1.it01_valortransacao.focus();
    }

    js_criaGrid();

    function js_criaGrid() {

        gridFormasPgto = new DBGrid("listaFormasPgto");
        gridFormasPgto.nameInstance = "gridFormasPgto";

        gridFormasPgto.setCellAlign(new Array("left", "center", "right"));
        gridFormasPgto.setHeader(new Array("Descrição", "Alíquota %", "Valor"));
        gridFormasPgto.setCellWidth(new Array("60%", "20%", "20%"));
        gridFormasPgto.setHeight(80);
        gridFormasPgto.show(document.getElementById('listaFormasPgto'));

        closeOnSave = false;
    }

    js_validaValores(document.getElementById("it01_valorterreno"));
    js_validaValores(document.getElementById("it01_valorconstr"));

    function js_validaValores(obj) {

        var sNomeCampo = obj.name;
        obj.value = new String(obj.value).replace(",", ".");
        obj.value = new Number(obj.value).toFixed(2);
        var doc = document.form1;
        var nValorTotal = new Number(doc.it01_valortransacao.value);
        var nValorTerreno = new Number(doc.it01_valorterreno.value);
        var nValorBenfeitoria = new Number(doc.it01_valorconstr.value);


        if (nValorTerreno != 0 || nValorBenfeitoria != 0) {
            doc.it01_valortransacao.disabled = true;
            doc.it01_valortransacao.value = new Number(nValorTerreno + nValorBenfeitoria).toFixed(2);
        } else if (nValorTerreno == 0 && nValorBenfeitoria == 0 && sNomeCampo == "it01_valortransacao" && nValorTotal != 0) {
            doc.it01_valorterreno.disabled = true;
            doc.it01_valorconstr.disabled = true;
        } else if (nValorTerreno == 0 && nValorBenfeitoria == 0 && sNomeCampo != "it01_valortransacao") {
            doc.it01_valortransacao.value = 0;
            doc.it01_valortransacao.disabled = false;
        } else {
            doc.it01_valorterreno.disabled = false;
            doc.it01_valorconstr.disabled = false;
            doc.it01_valortransacao.disabled = false;
        }

        js_calculaTaxas(obj);

        if (doc.primeiro_avalia != undefined) {
            js_limpaValorFormaPgto();
            doc.primeiro_avalia.value = new Number(doc.it01_valortransacao_avalia.value).toFixed(2);
        }

        js_somaValores();
    }

    function js_limpaValorFormaPgto() {
        var aObjFormasPgto = js_getElementbyClass(document.all, 'formasPgto');
        for (var iInd = 0; iInd < aObjFormasPgto.length; iInd++) {
            aObjFormasPgto[iInd].value = 0;
        }
    }

    function js_somaValores() {

        var aObjGrid = gridFormasPgto.getSelection("object");
        var nTotalImposto = 0;

        for (var iInd = 0; iInd < aObjGrid.length; iInd++) {

            var nValorAliquota = js_strToFloat(aObjGrid[iInd].aCells[1].getValue());
            var nValorForma = new Number(aObjGrid[iInd].aCells[2].getValue());
            var nValorImposto = nValorForma * (nValorAliquota / 100);
            // var nValorDescImposto = nValorImposto * (document.form1.desconto_avalia.value / 100);
            var nValorDescImposto = nValorImposto * (0 / 100);
            nValorImposto = nValorImposto - nValorDescImposto;
            nTotalImposto = nTotalImposto + nValorImposto;

        }

        document.form1.imposto_avalia.value = nTotalImposto.toLocaleString('pt-BR', {
            maximumFractionDigits: 2
        });

        const valorTotal = parseFloat(document.form1.imposto_avalia.value.replaceAll(".", "").replace(",", ".")) + parseFloat(document.form1.taxas_avalia.value.replaceAll(".", "").replace(",", "."));

        document.form1.total_avalia.value = valorTotal.toLocaleString('pt-BR', {
            maximumFractionDigits: 2
        });

        document.form1.primeiro.value = document.form1.it01_valortransacao.value
    }

    function js_calculaTaxas(oCampo) {




        const aSpans = document.querySelectorAll("[isPercentual='true']");
        const it01_valortransacao = document.getElementById("it01_valortransacao");
        var valor = 0;

        aSpans.forEach(function(aSpan) {
            if (aSpan.getAttribute("sobre") == 1) {
                if (oCampo.name == "it01_valorterreno") {
                    const valorFinal = ((aSpan.getAttribute("percentual") / 100) * oCampo.value);

                    aSpan.innerHTML = valorFinal.toLocaleString('pt-BR', {
                        maximumFractionDigits: 2
                    });
                }
            } else if (aSpan.getAttribute("sobre") == 2) {
                if (oCampo.name == "it01_valorconstr") {
                    const valorFinal = ((aSpan.getAttribute("percentual") / 100) * oCampo.value);

                    aSpan.innerHTML = valorFinal.toLocaleString('pt-BR', {
                        maximumFractionDigits: 2
                    });
                }
            } else if (aSpan.getAttribute("sobre") == 3) {
                const valorFinal = ((aSpan.getAttribute("percentual") / 100) * it01_valortransacao.value);

                aSpan.innerHTML = valorFinal.toLocaleString('pt-BR', {
                    maximumFractionDigits: 2
                });
            }
        });

        js_atualizaValorTotal();
    }

    function js_atualizaValorTotal() {
        const aSpans = document.querySelectorAll("[isTaxa='true']");
        const taxas_avalia = document.getElementById("taxas_avalia");
        var valor = 0;

        aSpans.forEach(function(aSpan) {
            const valorTaxa = aSpan.innerText.replaceAll(".", "").replace(",", ".");
            valor = valor + parseFloat(valorTaxa);
        });

        taxas_avalia.value = valor.toLocaleString('pt-BR', {
            maximumFractionDigits: 2
        });
    }


    function js_controlaValoresFormaPgto(obj) {

        var doc = document.form1;
        var aObjFormasPgto = js_getElementbyClass(document.all, 'formasPgto');
        var nValorTotal = new Number(doc.it01_valortransacao.value);
        obj.value = new String(obj.value).replace(",", ".");
        obj.value = new Number(obj.value).toFixed(2);
        var nValorAlterado = new Number(obj.value);
        var nValorResto = new Number();

        for (var iInd = 0; iInd < aObjFormasPgto.length; iInd++) {

            if (aObjFormasPgto[iInd].name != "primeiro") {

                var nValLinha = new Number(aObjFormasPgto[iInd].value);
                nValorResto += nValLinha;
            }
        }

        var nValorAvista = new Number(nValorTotal.toFixed(2) - nValorResto.toFixed(2));

        if (nValorAvista < 0) {

            nValorAvista = nValorTotal - (nValorResto - new Number(obj.value));
            alert("A soma dos valores das formas de pagamento nÃ£o conferem com o valor total do imÃ³vel!");
            obj.value = 0;

        }

        doc.primeiro.value = new Number(nValorAvista).toFixed(2);
    }

    function js_retornoFormaPgtoCadastrada(oAjax) {

        js_removeObj("msgBoxC");
        var objListaForma = JSON.parse(oAjax.responseText);
        var nValor = 0;


        gridFormasPgto.clearAll(true);

        if (objListaForma.iStatus && objListaForma.iStatus == 2) {

            alert(objListaForma.sMensagem.urlDecode());
            return false;
        }

        for (var iInd = 0; iInd < objListaForma.length; iInd++) {

            with(objListaForma[iInd]) {

                if (iInd == 0) {
                    var sDisabled = "disabled";
                    var sNomeCampo = "name='primeiro'";
                } else {
                    var sDisabled = "";
                    var sNomeCampo = "";
                }

                var sInputValor = "<input type='text' id='" + it25_sequencial.urlDecode() + "' class='formasPgto' value='" + it26_valor.urlDecode() + "'";
                sInputValor += "style='width:100%;text-align:right;height:100%;border:1px inset' " + sDisabled + " " + sNomeCampo + "";
                sInputValor += " onChange='js_controlaValoresFormaPgto(this);'>";

                var aLinha = new Array();
                aLinha[0] = it27_descricao.urlDecode();
                aLinha[1] = js_formatar(it27_aliquota.urlDecode(), 'f');
                aLinha[2] = sInputValor;


                gridFormasPgto.addRow(aLinha);
                gridFormasPgto.renderRows();
            }
        }

        console.log(aLinha)

        document.form1.it01_valortransacao.focus();
    }

    function js_matri(mostra){
        var matri=document.form1.j01_matric.value;

        if(mostra==true){
            js_OpenJanelaIframe('','db_iframe3','func_matricitbi.php?valida=true&funcao_js=parent.js_mostramatri|0|1','Pesquisa',true);
        }else{
            js_OpenJanelaIframe('','db_iframe3','func_matricitbi.php?pesquisa_chave='+matri+'&funcao_js=parent.js_mostramatri1','Pesquisa',false);
        }

        if (document.form1.j01_matric.value != "") {
            window.location="itbi_simulacaorural001.php?matric="+document.form1.j01_matric.value;
        }
    }

    function js_mostramatri(chave1,chave2){
        document.form1.j01_matric.value = chave1;
        document.form1.z01_nomematri.value = chave2;
        db_iframe3.hide();
    }

    function js_mostramatri1(chave,erro){

        if ( erro == true ) {

            document.form1.fiscal.disabled = true;
            document.form1.j01_matric.focus();
            document.form1.j01_matric.value = '';
        } else {

            document.form1.z01_nomematri.value = chave;
        }
    }

    onLoad = document.form1.j01_matric.focus();

function js_testacamp(){
    var matri = document.form1.j01_matric.value;

    if ( matri == "" ) {
      alert("Informe um campo para prosseguir!");
      return false;
    }
    document.form1.submit();
}
</script>
