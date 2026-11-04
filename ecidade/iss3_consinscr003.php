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

use App\Domain\Tributario\Fiscalizacao\Services\CheckUseModuloFiscalizacaoService;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_issbase_classe.php"));
require_once(modification("classes/db_varfixval_classe.php"));
require_once(modification("classes/db_issnotaavulsa_classe.php"));
require_once(modification("classes/db_isstipoalvara_classe.php"));
require_once(modification("classes/db_numpref_classe.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("dbforms/verticalTab.widget.php"));
require_once(modification("classes/db_cgm_classe.php"));

$clnumpref       = new cl_numpref;
$clissbase       = new cl_issbase;
$clisstipoalvara = new cl_isstipoalvara;
$clcgm           = new cl_cgm;

$clcgm->rotulo->label("z01_nome");
$clrotulo = new rotulocampo;
$clrotulo->label("q177_setorfiscal");
$clrotulo->label("q02_protocolojuntacomercial");

parse_str($_SERVER["QUERY_STRING"]);

if(isset($cpfCnpj)){
    $cpfCnpj = trim(stripslashes($cpfCnpj));
}

if(isset($numeroDaInscricao)){
    $iCodigoInscricao = stripslashes($numeroDaInscricao);
}

$whereIssVeiculo = " and q172_sequencial is null ";

if(isset($todasInscricoes)) {
  $whereIssVeiculo = "";
}

$sDisplayNone    = "";


/**
 * Retorna se o Alvara pode ser Impresso
 */
if(!empty($iCodigoInscricao)) {
    $sSqlTipoAlvara = " select q98_permiteimpressao                                                                  ";
    $sSqlTipoAlvara .= "  from isstipoalvara                                                                          ";
    $sSqlTipoAlvara .= "       inner join issalvara    on issalvara.q123_isstipoalvara = isstipoalvara.q98_sequencial ";
    $sSqlTipoAlvara .= "       inner join issmovalvara on issmovalvara.q120_issalvara  = issalvara.q123_sequencial    ";
    $sSqlTipoAlvara .= "        left join issveiculo   on issveiculo.q172_issbase = issalvara.q123_inscr    ";
    $sSqlTipoAlvara .= " where q123_inscr = {$iCodigoInscricao} {$whereIssVeiculo} and q123_situacao = 1;";
    $rsTipoAlvara = $clisstipoalvara->sql_record($sSqlTipoAlvara);
    $iLinhasMovimentacaoAlvara = $clisstipoalvara->numrows;
    if ($iLinhasMovimentacaoAlvara > 0) {
        $oTipoAlvara = db_utils::fieldsMemory($rsTipoAlvara, 0);

        if ($oTipoAlvara->q98_permiteimpressao == "f") {
            $sDisplayNone = "display:none;";
        }
    } else {
        $sDisplayNone = "display:none;";
    }
}

/**
 * Sql que retorna os dados básicos da inscrição
 */
$sSqlDadosInscricao  = " select issbase.*,                                                                                            \n";
$sSqlDadosInscricao .= "        case when (cgm_inscr.z01_nomecomple is null or trim(cgm_inscr.z01_nomecomple) = '')                   \n";
$sSqlDadosInscricao .= "          then cgm_inscr.z01_nome                                                                             \n";
$sSqlDadosInscricao .= "          else cgm_inscr.z01_nomecomple                                                                       \n";
$sSqlDadosInscricao .= "        end as z01_nome,                                                                                      \n";
$sSqlDadosInscricao .= "        cgm_inscr.z01_ender,                                                                                  \n";
$sSqlDadosInscricao .= "        cgm_inscr.z01_cgccpf,                                                                                 \n";
$sSqlDadosInscricao .= "        cgm_inscr.z01_nomefanta,                                                                              \n";
$sSqlDadosInscricao .= "        cgm_inscr.z01_telef,                                                                                  \n";
$sSqlDadosInscricao .= "        cgm_inscr.z01_incest,                                                                                 \n";
$sSqlDadosInscricao .= "        cgm_inscr.z01_email,                                                                                  \n";
$sSqlDadosInscricao .= "        cgm_inscr.z01_numero,                                                                                 \n";
$sSqlDadosInscricao .= "        cgm_inscr.z01_compl,                                                                                  \n";
$sSqlDadosInscricao .= "        cgm_inscr.z01_cep,                                                                                    \n";
$sSqlDadosInscricao .= "        cgm_inscr.z01_bairro,                                                                                 \n";
$sSqlDadosInscricao .= "        cgm_inscr.z01_munic,                                                                                  \n";
$sSqlDadosInscricao .= "        cgm_inscr.z01_uf,                                                                                     \n";
$sSqlDadosInscricao .= "        cgm_escr.z01_nome as escritorio,                                                                      \n";
$sSqlDadosInscricao .= "        j14_nome,                                                                                             \n";
$sSqlDadosInscricao .= "        j13_descr,                                                                                            \n";
$sSqlDadosInscricao .= "        q02_numero,                                                                                           \n";
$sSqlDadosInscricao .= "        q02_compl,                                                                                            \n";
$sSqlDadosInscricao .= "        issruas.z01_cep as issruas_cep,                                                                           \n";
$sSqlDadosInscricao .= "        q05_matric,                                                                                           \n";
$sSqlDadosInscricao .= "        q98_descricao,                                                                                        \n";
$sSqlDadosInscricao .= "        q14_proces,                                                                                           \n";
$sSqlDadosInscricao .= "        p58_dtproc,                                                                                           \n";
$sSqlDadosInscricao .= "        extract (year from p58_dtproc)::integer as p58_ano,                                                   \n";
$sSqlDadosInscricao .= "        q45_codporte,                                                                                         \n";
$sSqlDadosInscricao .= "        q40_descr,                                                                                            \n";
$sSqlDadosInscricao .= "        j88_sigla,                                                                                            \n";
$sSqlDadosInscricao .= "        p58_numero,                                                                                           \n";
$sSqlDadosInscricao .= "        q167_descricao,                                                                                       \n";
$sSqlDadosInscricao .= "        j90_descr,                                                                                            \n";
$sSqlDadosInscricao .= "        case                                                                                                  \n";
$sSqlDadosInscricao .= "          when meicgm.q115_numcgm is not null                                                                 \n";
$sSqlDadosInscricao .= "            then 'MEI'                                                                                        \n";
$sSqlDadosInscricao .= "            else ''                                                                                           \n";
$sSqlDadosInscricao .= "        end as tipo,                                                                                          \n";
$sSqlDadosInscricao .= "        case                                                                                                  \n";
$sSqlDadosInscricao .= "          when q30_graurisco = 'A' then 'ALTO'                                                                \n";
$sSqlDadosInscricao .= "          when q30_graurisco = 'M' then 'MÉDIO'                                                               \n";
$sSqlDadosInscricao .= "          when q30_graurisco = 'B' then 'BAIXO'                                                               \n";
$sSqlDadosInscricao .= "          else ''                                                                                             \n";
$sSqlDadosInscricao .= "        end as q30_graurisco                                                                                  \n";
$sSqlDadosInscricao .= "   from issbase                                                                                               \n";
$sSqlDadosInscricao .= "        inner      join cgm cgm_inscr      on cgm_inscr.z01_numcgm         = q02_numcgm                       \n";
$sSqlDadosInscricao .= "        left outer join issruas            on issbase.q02_inscr            = issruas.q02_inscr                \n";
$sSqlDadosInscricao .= "        left outer join ruas               on ruas.j14_codigo              = issruas.j14_codigo               \n";
$sSqlDadosInscricao .= "        left outer join ruastipo           on ruas.j14_tipo                = ruastipo.j88_codigo              \n";
$sSqlDadosInscricao .= "        left outer join issbairro          on issbase.q02_inscr            = q13_inscr                        \n";
$sSqlDadosInscricao .= "        left outer join bairro             on j13_codi                     = q13_bairro                       \n";
$sSqlDadosInscricao .= "        left outer join escrito            on issbase.q02_inscr            = q10_inscr                        \n";
$sSqlDadosInscricao .= "        left outer join cgm cgm_escr       on cgm_escr.z01_numcgm          = q10_numcgm                       \n";
$sSqlDadosInscricao .= "        left outer join issmatric          on issbase.q02_inscr            = q05_inscr                        \n";
$sSqlDadosInscricao .= "        left outer join issprocesso        on issbase.q02_inscr            = q14_inscr                        \n";
$sSqlDadosInscricao .= "              left join protprocesso       on issprocesso.q14_proces       = protprocesso.p58_codproc         \n";
$sSqlDadosInscricao .= "              left join meicgm             on meicgm.q115_numcgm           = issbase.q02_numcgm               \n";
$sSqlDadosInscricao .= "              left join issalvara          on issalvara.q123_inscr         = issbase.q02_inscr                \n";
$sSqlDadosInscricao .= "              left join isstipoalvara      on isstipoalvara.q98_sequencial = issalvara.q123_isstipoalvara     \n";
$sSqlDadosInscricao .= "              left join issqn.issbaseporte on q45_inscr                    = issbase.q02_inscr                \n";
$sSqlDadosInscricao .= "              left join issqn.issporte     on q40_codporte                 = q45_codporte                     \n";
$sSqlDadosInscricao .= "              left join issqn.formalocalvara on issbase.q02_formalocalvara    = formalocalvara.q167_sequencial\n";
$sSqlDadosInscricao .= "              left join issveiculo on issveiculo.q172_issbase = issbase.q02_inscr                             \n";
$sSqlDadosInscricao .= "              left join isssetorfiscal on isssetorfiscal.q177_issbase = issbase.q02_inscr                     \n";
$sSqlDadosInscricao .= "              left join setorfiscal on setorfiscal.j90_codigo = isssetorfiscal.q177_setorfiscal               \n";
$sSqlDadosInscricao .= "left join issquant on q30_inscr = issbase.q02_inscr  and q30_anousu = ".db_getsession('DB_anousu')."          \n";
if ( isset($numeroDaInscricao) ) {
    $sSqlDadosInscricao .= " where issbase.q02_inscr = $numeroDaInscricao {$whereIssVeiculo}";
} elseif ( isset($referenciaanterior) ) {
    $sSqlDadosInscricao .= " where issbase.q02_inscmu ilike '$referenciaanterior' {$whereIssVeiculo}";
} elseif ( !empty($cpfCnpj) ) {
    $sSqlDadosInscricao .= " where cgm_inscr.z01_cgccpf = '$cpfCnpj' {$whereIssVeiculo}";
}

$rsSqlDadosInscricao = db_query($sSqlDadosInscricao);
$iNumRowsIncricao    = pg_numrows($rsSqlDadosInscricao);

if ( isset($referenciaanterior) || !empty($cpfCnpj) ) {
    $numeroDaInscricao = null;

    if ( $iNumRowsIncricao == 1 ) {
        $numeroDaInscricao = pg_result($rsSqlDadosInscricao,0,"q02_inscr");
    }
    $iCodigoInscricao = $numeroDaInscricao;
}

$sSqlNumpref = $clnumpref->sql_query_file(
    db_getsession("DB_anousu"),
    db_getsession("DB_instit"),
  "k03_certissvar,
          k03_regracnd,
          k03_tipocertidao"
    );

$rsSqlNumpref    = $clnumpref->sql_record($sSqlNumpref);
$iNumRowsNumpref = $clnumpref->numrows;

if ($iNumRowsNumpref !== 0) {
    $oParmNumpref = db_utils::fieldsMemory($rsSqlNumpref,0);
}

/**
 * Define o tipo de certidao
 *   regular
 *   positiva
 *   negativa
 */

if(!empty($iCodigoInscricao)) {
    $sWhereIssvar = ($oParmNumpref->k03_certissvar == 't' ? " k00_valor <> 0 " : "");
    $dtBase = date('Y-m-d', db_getsession('DB_datausu'));
    $sSqlCertid = "select *                                              ";
    $sSqlCertid .= "  from fc_tipocertidao($iCodigoInscricao,              ";
    $sSqlCertid .= "                       'i',                           ";
    $sSqlCertid .= "                       '{$dtBase}',                   ";
    $sSqlCertid .= "                       '{$sWhereIssvar}',             ";
    $sSqlCertid .= "                        {$oParmNumpref->k03_regracnd} ";
    $sSqlCertid .= "                      )                               ";
    $rsSqlCertid = $clnumpref->sql_record($sSqlCertid);

    if ($rsSqlCertid !== false) {
        $sCertidao = db_utils::fieldsMemory($rsSqlCertid, 0)->fc_tipocertidao;
    }
}

/**
 * Retorna parametro da tabela parissqn
 */
$sSqlParIssqn  = "select q60_bloqemiscertbaixa from parissqn ";
$rsSqlParIssqn = $clnumpref->sql_record($sSqlParIssqn);
if ($rsSqlParIssqn !== false) {
    $iBloqueioEmissaoCertidaoBaixa  = db_utils::fieldsMemory($rsSqlParIssqn,0)->q60_bloqemiscertbaixa;
}

/**
 * Valida se é regular, negativa ou positiva
 */

if(!empty($sCertidao)) {
    $iAvisoCertidao = 1;
    if ($sCertidao != "negativa" && $iBloqueioEmissaoCertidaoBaixa == 2) {
        $iAvisoCertidao = 2;
    } else if ($sCertidao != "negativa" && $iBloqueioEmissaoCertidaoBaixa == 3) {
        $iAvisoCertidao = 3;
    }
}

/**
 * Retorna percentual dos socios
 */
if(!empty($iCodigoInscricao)) {
    $sSqlSocios = $clissbase->sqlinscricoes_socios($iCodigoInscricao, 0, "q95_perc");
    $rsSqlSocios = $clissbase->sql_record($sSqlSocios);
    $iNumRowsSocios = $clissbase->numrows;
    $nPercentual = 0;
    if ($iNumRowsSocios > 0) {
        $aPercentual = db_utils::getCollectionByRecord($rsSqlSocios);
        foreach ($aPercentual as $oSocios) {
            $nPercentual += $oSocios->q95_perc;
        }
    }
}

/**
 * Verifica se deve mostrar link de calculo de vistoria
 */
$showLinkVistoriaCalculo = false;
if(pg_num_rows($rsSqlDadosInscricao) > 0) {
    $cgmInscricao = db_utils::fieldsMemory($rsSqlDadosInscricao,0,1)->q02_numcgm;
    $daoVistorias = CheckUseModuloFiscalizacaoService::verify() ? new cl_fis_vistorias : new cl_vistorias;
    $sqlVistoria = $daoVistorias->sql_verifica_cgm_possui_calculo($cgmInscricao);
    $rsVistoria = $daoVistorias->sql_record($sqlVistoria);
    $numRowsVistoria = $daoVistorias->numrows;
    if ($numRowsVistoria > 0) {
        $showLinkVistoriaCalculo = true;
    }
}
?>
<html>
<head>
  <title>Dados da Inscri&ccedil;&atilde;o - BCI</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="estilos.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="assets/fontawesome/css/all.min.css">
  <script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/classes/tributario/issqn/EmitirBICView.js"></script>

  <script type="text/javascript">
    var iBloqueioCertidao = <?=$iAvisoCertidao; ?>

      function js_Impressao() {
        window.open('iss3_consinscr003_imprimealvara.php?inscricao=<?=$iCodigoInscricao?>','','location=0,HEIGHT=600,WIDTH=600');
      }
    function js_Imprime_iss() {
      const inscricao = "<?=$iCodigoInscricao?>";
      const windowBic = new windowAux('windowBic', 'Emitir BIC', 500, 300);
      const emitirBICView = new EmitirBICView(windowBic, inscricao);
      emitirBICView.show();
    }
    function js_Imprimebaix() {

      if (iBloqueioCertidao == 2){
        alert(_M("tributario.issqn.iss3_consinscr003.certidaoavisodebito"));
      } else if (iBloqueioCertidao == 3) {
        alert(_M("tributario.issqn.iss3_consinscr003.bloqueiocertidao"));
        return false;
      }
      window.open('iss2_certibaixa002.php?inscr=<?=$iCodigoInscricao?>','','location=0,HEIGHT=600,WIDTH=600');
    }

    function js_Imprimetermo() {
      window.open('iss2_imprimetermo001.php?inscr=<?=$iCodigoInscricao?>','','location=0,HEIGHT=600,WIDTH=600');
    }

    function js_selecionaInscricao(iCodigoInscricao) {
          window.location.href = `iss3_consinscr003.php?numeroDaInscricao=${iCodigoInscricao}`;
    }

    function js_demostrativoVistoria(numcgm) {
        const url = `web/tributario/fiscalizacao/vistoria/demostrivo-calculo/${numcgm}`;
        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_vistoria', url, 'Demostrativo do Calculo de Vistoria', true);
    }
  </script>
    <style>
        #tableLista > thead > tr > th {
            border: solid 1px #808080;
            height: 21px;
            background-color: #e1e1e1;
        }

        #tableLista > tbody > tr > td {
            border: solid 1px #808080;
        }

        #tableLista > tbody > tr {
            cursor: pointer;
        }

        #tableLista > tbody > tr:hover {
            background-color: black !important;
            color: white;
        }

        #tableLista {
            text-align: center;
        }
    </style>
</head>
<body>

<?php if (!(!empty($iCodigoInscricao) && $iCodigoInscricao > 0) && pg_num_rows($rsSqlDadosInscricao) > 0) :
    $aInscricoes = \db_utils::getCollectionByRecord($rsSqlDadosInscricao);
?>
    <table width="1000" border="0" align="center" cellpadding="0" cellspacing="0" id="tableLista">
        <thead>
            <tr>
                <th>Inscrição Municipal</th>
                <th>Nome/Razão Social</th>
                <th>CPF/CNPJ</th>
                <th>Endereço</th>
                <th>Número</th>
                <th>Complemento</th>
                <th>Dt. Inicio</th>
                <th>Dt. Baixa</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($aInscricoes as $key => $oInscriao) : ?>
                <tr style="background-color: <?= (($key % 2) == 0 ? "#FFFFFF" : "") ?>" onclick="js_selecionaInscricao(<?= $oInscriao->q02_inscr ?>)">
                    <td><?= $oInscriao->q02_inscr ?></td>
                    <td><?= $oInscriao->z01_nome ?></td>
                    <td><?= $oInscriao->z01_cgccpf ?></td>
                    <td><?= $oInscriao->j14_nome ?></td>
                    <td><?= $oInscriao->z01_numero ?></td>
                    <td><?= $oInscriao->z01_compl ?></td>
                    <td><?= date("d/m/Y", strtotime($oInscriao->q02_dtinic)) ?></td>
                    <td><?= !empty($oInscriao->q02_dtbaix) ? date("d/m/Y", strtotime($oInscriao->q02_dtbaix)) : ""?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php
    exit;
    endif;
?>

<?php
/**
 * valida se a inscrição passada é valida
 */
if (!empty($iCodigoInscricao) && $iCodigoInscricao > 0 && pg_num_rows($rsSqlDadosInscricao) > 0) {
    db_fieldsmemory($rsSqlDadosInscricao,0,1);
    $oDadosInscricao = db_utils::fieldsMemory($rsSqlDadosInscricao,0,true);
    ?>

  <table width="900" border="0" align="center" cellpadding="0" cellspacing="2">
    <tr bgcolor="#CCCCCC">
      <td colspan="4" align="center">
        <font color="#333333">
          <strong>
            &nbsp;DADOS DA INSCRI&Ccedil;&Atilde;O&nbsp;
          </strong>
        </font>
      </td>
    </tr>
    <tr>
      <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Inscri&ccedil;&atilde;o municipal:&nbsp;</td>
      <td width="356" align="left" nowrap bgcolor="#FFFFFF">
        <font color="#666666">
          <strong>&nbsp;<?=$iCodigoInscricao . " - CGM: " . $oDadosInscricao->q02_numcgm?>&nbsp;</strong>
          <?php $sqlparalisada = "select
                                q140_datainicio,
                                q141_descricao
                              from
                                issbaseparalisacao
                              inner join issmotivoparalisacao on
                                q141_sequencial = q140_issmotivoparalisacao
                              where q140_issbase = $iCodigoInscricao
                              and (q140_datafim > current_date OR q140_datafim is null)";
            $resultparalisada = db_query($sqlparalisada) or die($sqlparalisada);

            if (pg_numrows($resultparalisada) > 0) {
              echo "<b> <font color=red><blink> - INSCRIÇÃO PARALISADA EM "
                  . db_formatar(pg_result($resultparalisada, 0, "q140_datainicio"), "d")
                  . " - " . pg_result($resultparalisada, 0, "q141_descricao") . "</b>";
            }?>
        </font>
      </td>
      <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;CNPJ/CPF: </td>
      <td width="165" nowrap bgcolor="#FFFFFF">
        <font color="#666666"><strong>&nbsp;<?=$oDadosInscricao->z01_cgccpf?></strong></font>
      </td>
    </tr>

    <tr>
      <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;<?=db_ancora($Lz01_nome,"js_JanelaAutomatica('cgm','$oDadosInscricao->q02_numcgm')",2) . ":"?>&nbsp; </td>
      <td align="left" nowrap bgcolor="#FFFFFF"> <font color="#666666"><strong>&nbsp;
                  <?=$oDadosInscricao->z01_nome?>
            &nbsp; </strong></font></td>
      <td align="right" nowrap bgcolor="#CCCCCC">Inscrição Estadual:&nbsp;</td>
      <td align="left" nowrap bgcolor="#FFFFFF"> <font color="#666666"><strong>&nbsp;
                  <?=$oDadosInscricao->z01_incest?>
            &nbsp; </strong></font></td>
    </tr>

    <tr>
      <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Endereço:&nbsp; </td>
      <td align="left" nowrap bgcolor="#FFFFFF"> <font color="#666666"><strong>&nbsp;
          <?=$oDadosInscricao->z01_ender . ", " . $oDadosInscricao->z01_numero . " - " . $oDadosInscricao->z01_compl
            . " CEP: " . $oDadosInscricao->z01_cep?>
            &nbsp; </strong></font></td>
      <td align="right" nowrap bgcolor="#CCCCCC">Telefone:&nbsp;</td>
      <td align="left" nowrap bgcolor="#FFFFFF"> <font color="#666666"><strong>&nbsp;
                  <?=$oDadosInscricao->z01_telef?>
            &nbsp; </strong></font></td>
    </tr>

    <tr>
      <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Bairro/Municipio/UF:&nbsp;</td>
      <td align="left" nowrap bgcolor="#FFFFFF">&nbsp;<font color="#666666"><strong>
                  <?="$z01_bairro/$z01_munic/$z01_uf"?>
            &nbsp; </strong></font></td>
      <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Referência anterior:&nbsp;</td>
      <td nowrap bgcolor="#FFFFFF">&nbsp;<font color="#666666"><strong>
                  <?=$oDadosInscricao->q02_inscmu?>
            &nbsp; </strong></font></td>
    </tr>
    <tr>
      <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Email:&nbsp;</td>
      <td align="left" nowrap bgcolor="#FFFFFF">&nbsp;<font color="#666666"><strong>
                  <?=@$z01_email?>
            &nbsp; </strong></font></td>
      <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Data inicial:&nbsp;</td>
      <td nowrap bgcolor="#FFFFFF">&nbsp;<font color="#666666"><strong>
                  <?=$oDadosInscricao->q02_dtinic?>
            &nbsp; </strong></font></td>
    </tr>
    <tr>
      <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Nome fantasia:&nbsp;</td>
      <td align="left" nowrap bgcolor="#FFFFFF">&nbsp;<font color="#666666"><strong>
                  <?=$oDadosInscricao->z01_nomefanta?>
            &nbsp; </strong></font></td>
      <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Data do cadastro:&nbsp;</td>
      <td nowrap bgcolor="#FFFFFF">&nbsp;<font color="#666666"><strong>
                  <?=$oDadosInscricao->q02_dtcada?>
            &nbsp; </strong></font></td>
    </tr>

    <tr>
      <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Registro na junta:&nbsp; </td>
      <td align="left" nowrap bgcolor="#FFFFFF"> <font color="#666666"><strong>&nbsp;
                  <?=$oDadosInscricao->q02_regjuc?>
            &nbsp; </strong></font></td>
      <td align="right" nowrap bgcolor="#CCCCCC">Data da junta:&nbsp;</td>
      <td align="left" nowrap bgcolor="#FFFFFF"> <font color="#666666"><strong>&nbsp;
                  <?=$oDadosInscricao->q02_dtjunta?>
            &nbsp; </strong></font></td>
    </tr>

    <tr>

      <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;<?=$LSq02_protocolojuntacomercial?>:&nbsp;</td>
      <td align="left" nowrap bgcolor="#FFFFFF">&nbsp;
        <font color="#666666">
          <strong><?=$oDadosInscricao->q02_protocolojuntacomercial ?> &nbsp; </strong>
        </font>
      </td>
      <td align="right" nowrap bgcolor="#CCCCCC">Data da baixa: </td>
      <td nowrap bgcolor="#FFFFFF">&nbsp;<font color="#666666"><strong>
                  <?=$oDadosInscricao->q02_dtbaix?>
            &nbsp; </strong></font></td>
    </tr>

    <tr>
      <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;Capital social:&nbsp;</td>
      <td align="left" nowrap bgcolor="#FFFFFF">&nbsp;
        <font color="#666666">
          <strong><?=(trim($oDadosInscricao->q02_capit) == 0 ? db_formatar($nPercentual, 'f') : db_formatar($oDadosInscricao->q02_capit, 'f'));?> &nbsp; </strong>
        </font>
      </td>
      <td align="right" nowrap bgcolor="#CCCCCC">&nbsp;<?=$LSq177_setorfiscal?>:&nbsp;</td>
      <td align="left" nowrap bgcolor="#FFFFFF">&nbsp;
        <font color="#666666">
          <strong><?=$oDadosInscricao->j90_descr ?> &nbsp; </strong>
        </font>
      </td>
    </tr>

    <tr>
      <td height="15px;" colspan="4">&nbsp;</td>
    </tr>

    <tr>
      <td align="right" nowrap bgcolor="#CCCCCC">Matr&iacute;cula:&nbsp;</td>
      <td nowrap bgcolor="#FFFFFF"> <strong><font color="#666666"> &nbsp; <strong>
                      <?=$oDadosInscricao->q05_matric?>
            </strong></font></strong></td>
      <td align="right" nowrap bgcolor="#CCCCCC">Processo:</td>
      <td nowrap bgcolor="#FFFFFF">
        <font color="#666666">&nbsp;
          <strong>
            <a href="" onclick="consultaProcesso('<?=$oDadosInscricao->p58_numero?>', <?=$oDadosInscricao->p58_ano?>);">
                <?="$oDadosInscricao->p58_numero/$oDadosInscricao->p58_ano"?>
            </a>
              <?="- $oDadosInscricao->p58_dtproc"?>
          </strong>
        </font>
      </td>

    </tr>

    <tr>
      <td align="right" nowrap bgcolor="#CCCCCC">Rua:&nbsp; </td>
      <td align="left" nowrap bgcolor="#FFFFFF"> <font color="#666666"><strong>&nbsp;
                  <?=$oDadosInscricao->j88_sigla . " - " . $oDadosInscricao->j14_nome?>
          </strong></font></td>

      <td align="right" nowrap bgcolor="#CCCCCC">n&deg; : </td>
      <td align="left" nowrap bgcolor="#FFFFFF"> <font color="#666666"><strong>&nbsp;
        <?=$oDadosInscricao->q02_numero?>&nbsp; </strong></font></td>
    </tr>
      <td align="right" nowrap bgcolor="#CCCCCC">Complemento :&nbsp;</td>
      <td nowrap bgcolor="#FFFFFF"><font color="#666666"><strong>&nbsp;
                  <?=$oDadosInscricao->q02_compl?>
            &nbsp; </strong></font></td>
      <td align="right" nowrap bgcolor="#CCCCCC">CEP :&nbsp;</td>
      <td nowrap bgcolor="#FFFFFF"><font color="#666666"><strong>&nbsp;
                  <?=$oDadosInscricao->issruas_cep?>
            &nbsp; </strong></font></td>

    <tr>

    </tr>

    <tr>
      <td align="right" nowrap bgcolor="#CCCCCC">Bairro:&nbsp;</td>
      <td nowrap bgcolor="#FFFFFF"><font color="#666666"><strong>&nbsp;
                  <?=$oDadosInscricao->j13_descr?>
          </strong></font></td>

    </tr>

    <tr>
      <td align="right" nowrap bgcolor="#CCCCCC">Escrit&oacute;rio:&nbsp;</td>
      <td align="left" nowrap bgcolor="#FFFFFF"> <font color="#666666"><strong>&nbsp;
                  <?=$oDadosInscricao->escritorio?>
          </strong></font></td>
      <td align="right" nowrap bgcolor="#CCCCCC">Porte:</td>
      <td nowrap bgcolor="#FFFFFF"> <font color="#666666">&nbsp;<strong> <font color="#666666"><strong>
                          <?="$oDadosInscricao->q45_codporte - $oDadosInscricao->q40_descr"?>
              </strong></font></strong></font></td>
    </tr>

    <tr>
      <td align="right" nowrap bgcolor="#CCCCCC">Forma de Localização:&nbsp;</td>
      <td align="left" nowrap bgcolor="#FFFFFF"> <font color="#666666"><strong>&nbsp;
                  <?=$oDadosInscricao->q167_descricao?>
          </strong></font></td>
          <td align="right" nowrap bgcolor="#CCCCCC">Grau de Risco :</td>
      <td nowrap bgcolor="#FFFFFF"> <font color="#666666">&nbsp;<strong> <font color="#666666"><strong>
                          <?="$oDadosInscricao->q30_graurisco"?>
              </strong></font></strong></font></td>
    </tr>

    <tr>
      <td align="right" nowrap bgcolor="#CCCCCC">Ultima alteração:&nbsp;</td>
      <td nowrap bgcolor="#FFFFFF"> <strong><font color="#666666"> &nbsp; <strong>
                      <?=$oDadosInscricao->q02_dtalt?>
            </strong></font></strong></td>
      <td align="right" nowrap bgcolor="#CCCCCC">Tipo:</td>
      <td nowrap bgcolor="#FFFFFF"> <font color="#666666">&nbsp;<strong> <font color="#666666"><strong>
                          <?=$oDadosInscricao->tipo?>
              </strong></font></strong></font></td>
    </tr>

    <tr>
      <td align="right" nowrap bgcolor="#CCCCCC">Tipo de Alvará:&nbsp;</td>
      <td nowrap bgcolor="#FFFFFF">
        <strong>
          <font color="#666666"> &nbsp; <?=!empty($iLinhasMovimentacaoAlvara)?$oDadosInscricao->q98_descricao:""?></font>
        </strong>
      </td>
    </tr>

    <tr>
      <td height="15px;" colspan="4"> </td>
    </tr>

    <tr>
      <td colspan="4" align="left"><table width="100%" height="100%" border="0" align="left" cellpadding="0" cellspacing="0">
          <tr valign="bottom">
            <td width="12%">

              <table width="80%" border="0" cellspacing="2" cellpadding="0">
                  <?php if ($q02_dtbaix!=""){?>
                    <tr>
                      <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand" >
                        <a href="iss3_consinscr003_detalhes.php?solicitacao=Baixa&inscricao=<?=$iCodigoInscricao?>" target="iframeDetalhes">&nbsp;Dados da Baixa&nbsp;</a></td>
                    </tr>
                  <?php }?>
                <tr>
                  <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand" >
                    <a href="iss3_consinscr003_detalhes.php?solicitacao=Atividades&inscricao=<?=$iCodigoInscricao?>" target="iframeDetalhes">&nbsp;Atividades&nbsp;</a></td>
                </tr>
                <tr>
                  <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand">
                    <a href="iss3_consinscr003_detalhes.php?inscricao=<?=$iCodigoInscricao?>&solicitacao=Socios" target="iframeDetalhes">&nbsp;S&oacute;cios&nbsp;</a></td>
                </tr>
                  <?php if (1==2){?>
                    <tr>
                      <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand">
                        <a href="iss3_consinscr003_detalhes.php?inscricao=<?=$iCodigoInscricao?>&solicitacao=Calculo" target="iframeDetalhes">&nbsp;C&aacute;lculo&nbsp;</a></td>
                    </tr>
                  <?php }?>
                <tr>
                  <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand">
                    <a href="iss3_consinscr003_detalhes.php?inscricao=<?=$iCodigoInscricao?>&solicitacao=TiposDeCalculo" target="iframeDetalhes">&nbsp;Tipos
                      de c&aacute;lculo&nbsp;</a></td>
                </tr>
                <tr>
                  <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand">
                    <a href="iss3_consinscr003_detalhes.php?inscricao=<?=$iCodigoInscricao?>&solicitacao=Quantidades" target="iframeDetalhes">&nbsp;Quantidades&nbsp;</a></td>
                </tr>
                <tr>
                  <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand">
                    <a href="iss3_consinscr003_detalhes.php?inscricao=<?=$iCodigoInscricao?>&solicitacao=Fixado" target="iframeDetalhes">&nbsp;Fixado&nbsp;</a></td>
                </tr>
                <tr>
                  <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand">
                    <a href="iss3_consinscr003_detalhes.php?inscricao=<?=$iCodigoInscricao?>&solicitacao=HistoricoRisco" target="iframeDetalhes">&nbsp;Histórico de risco&nbsp;</a></td>
                </tr>
                <tr>
                  <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand" >
                    <a href="iss3_consinscr003_detalhes.php?solicitacao=Caracteristicas&inscricao=<?=$iCodigoInscricao?>" target="iframeDetalhes">&nbsp;Caracter&iacute;sticas&nbsp;</a></td>
                </tr>
                <tr>
                  <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand">
                    <a href="iss3_consinscr003_detalhes.php?inscricao=<?=$iCodigoInscricao?>&solicitacao=Observacoes" target="iframeDetalhes">&nbsp;Observa&ccedil;&otilde;es&nbsp;</a></td>
                </tr>
                <tr>
                  <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand">
                    <a href="iss3_consinscr003_detalhes.php?inscricao=<?=$iCodigoInscricao?>&solicitacao=TextoAlvara" target="iframeDetalhes">&nbsp;Texto
                      alvar&aacute;&nbsp;</a></td>
                </tr>
                <tr>
                  <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand">
                    <a href="iss3_consinscr003_detalhes.php?inscricao=<?=$iCodigoInscricao?>&solicitacao=Ocorrencias" target="iframeDetalhes">&nbsp;Ocorrências&nbsp;</a></td>
                </tr>
                <tr>
                  <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand">
                    <a href="iss3_consinscr003_detalhes.php?inscricao=<?=$iCodigoInscricao?>&solicitacao=SalaoParceiro" target="iframeDetalhes">&nbsp;Salão Parceiro&nbsp;</a></td>
                </tr>
                <tr>
                  <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand">
                    <a href="iss3_consinscr003_detalhes.php?inscricao=<?=$iCodigoInscricao?>&solicitacao=Manual" target="iframeDetalhes">&nbsp;Demonstrativo do calculo</a></td>
                </tr>

                <?php if ($showLinkVistoriaCalculo): ?>
                <!-- Calculo de Vistoria-->
                <tr>
                  <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand">
                    <a href="javascript:;" onclick="js_demostrativoVistoria('<?=$oDadosInscricao->q02_numcgm?>')">&nbsp;Demonstrativo do calculo de vistoria</a></td>
                </tr>
                <?php endif; ?>

                <tr>
                  <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand">
                    <a href="iss3_consinscr003_detalhes.php?inscricao=<?=$iCodigoInscricao?>&solicitacao=Paralisacoes" target="iframeDetalhes">&nbsp;Histórico de paralisações</a></td>
                </tr>
                <tr>
                  <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand">
                    <a href="iss3_consinscr003_detalhes.php?inscricao=<?=$iCodigoInscricao?>&solicitacao=Historico_MEI" target="iframeDetalhes">&nbsp;Histórico MEI</a></td>
                </tr>
                <tr>
                  <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand"><a href="forms/db_frmaidofant.php?inscr=<?=$iCodigoInscricao?>" target="iframeDetalhes">&nbsp;AIDOF&nbsp;</a></td>
                </tr>

                <tr>
                  <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand"><a href="iss3_consinscr003_detalhes.php?inscricao=<?=$iCodigoInscricao?>&solicitacao=OptanteSimples" target="iframeDetalhes">&nbsp;Optante Simples&nbsp;</a></td>
                </tr>

                  <?php
                  $clissnotaavulsa = new cl_issnotaavulsa();
                  $clissnotaavulsa->sql_record($clissnotaavulsa->sql_query(null,"*","q51_numnota","q51_inscr = $iCodigoInscricao"));
                  if ($clissnotaavulsa->numrows > 0){
                      ?>
                    <tr>
                      <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand"><a href="iss3_issnotaavulsa002.php?inscr=<?=$iCodigoInscricao?>" target="iframeDetalhes">&nbsp;Notas Avulsas&nbsp;</a></td>
                    </tr>
                      <?php
                  }
                  ?>
                <tr>
                  <td title="Imprime BIC" align="center" title="Imprime BIC" nowrap bgcolor="#CCCCCC" style="cursor:hand"><a href='javascript:;' onClick="js_Imprime_iss();" >Emitir BIC</a></td>
                </tr>
                <tr>
                  <td title="Imprime BIC" align="center" title="Imprime BIC" nowrap bgcolor="#CCCCCC" style="cursor:hand">
                    <a href="iss3_consinscr003_movimentacao.php?inscricao=<?=$iCodigoInscricao?>" target="iframeDetalhes">Movimentações</a></td>
                </tr>
                <tr>
                  <td title="isencoes" align="center" title="isencoes" nowrap bgcolor="#CCCCCC" style="cursor:hand">
                    <a href="iss3_consinscr003_isencao.php?inscricao=<?=$iCodigoInscricao?>&cgm=<?=$oDadosInscricao->q02_numcgm?>" target="iframeDetalhes">Isenções</a></td>
                </tr>
                  <?php
                  if ($q02_dtbaix!=""){
                      ?>
                    <tr>
                      <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand"><input name='imprime'  type='button'  onClick="js_Imprimebaix()" value='Imprime  Certid&atilde;o de Baixa'></td>
                    </tr>
                      <?php
                  } else {
                      /*
                       * Verifica se o suario possui permissão para imprimir o alvará
                       */
                      if ( db_permissaomenu(db_getsession("DB_anousu"), 40, 9740) == "true") {
                          ?>
                        <tr style=<?=$sDisplayNone?>>
                          <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand"><input name='imprime' type='button' onClick="js_Impressao()" value='Imprime Alvará'></td>
                        </tr>
                          <?php
                      }
                  }
                  ?>

                  <?php
                  $sql="select * from varfix inner join varfixval on q33_codigo=q34_codigo and q33_inscr=$iCodigoInscricao";
                  $varfix=db_query($sql);
                  $numrows=pg_numrows($varfix);
                  if ($numrows>0)
                  {
                      ?>
                    <tr>
                      <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand"><input name='imprime' type='button' onClick="js_Imprimetermo()" value='Imprime Termo'></td>
                    </tr>
                  <?php }?>
                <!-- <tr>
                        <td align="center" nowrap bgcolor="#CCCCCC" style="cursor:hand" onClick="js_impressao()"><strong>&nbsp;Imprimir</strong></td>
                      </tr>-->
              </table></td>
            <td width="88%" align="left"> <iframe align="middle" height="100%" frameborder="0" marginheight="0" marginwidth="0" name="iframeDetalhes" width="100%">
              </iframe> </td>
          </tr>
        </table></td>
    </tr>
  </table>

    <?php
} else {  // caso nao tenha retornado nenhum registro é mostrado uma tabela informando que a matricula nao foi localizada
    ?>
  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td align="center">&nbsp;</td>
    </tr>
    <tr>
        <?php if(!empty($iCodigoInscricao)) : ?>
            <td align="center"><strong>Pesquisa da inscricao n&deg;&nbsp;  <?=$iCodigoInscricao?>&nbsp;n&atilde;o retornou nenhum registro.</strong></td>
        <?php else : ?>
            <td align="center"><strong>Pesquisa pelo CPF/CNPJ n&deg;&nbsp;  <?=$cpfCnpj?>&nbsp;n&atilde;o retornou nenhum registro.</strong></td>
        <?php endif?>
    </tr>
  </table>
    <?php
} // fim da verificacao
?>

<script type="text/javascript">

  function consultaProcesso(numeroProcesso, ano){

    var processo = numeroProcesso + '/' + ano;
    var sUrl = 'pro3_consultaprocesso003.php?numeroprocesso=' + processo;

    js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe', sUrl, 'Pesquisa de Processos', true);
  }
</script>

</body>
</html>
