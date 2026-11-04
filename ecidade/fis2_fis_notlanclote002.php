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

require_once modification("libs/db_sql.php");
require_once modification("fpdf151/pdf1.php");
require_once modification("libs/db_libsys.php");
require_once modification("dbagata/classes/core/AgataAPI.class");
require_once modification("model/documentoTemplate.model.php");

$cldb_docparag = db_utils::getDao('db_docparag');
$cllancamento  = db_utils::getDao("fis_lancamento");
$cllancfiscal  = db_utils::getDao("fis_lancfiscal");
$cllancusu     = db_utils::getDao("fis_lancusu");
$cllanctipo    = db_utils::getDao("fis_lanctipo");
$cllancnumpre  = db_utils::getDao("fis_lancnumpre");
$clfiscal      = db_utils::getDao("fis_fiscal");
$oDaoParfiscal = db_utils::getDao("fis_parfiscal");
$clrotulo      = new rotulocampo;
$clrotulo->label('nl01_codlanc');
$clrotulo->label('nl01_nome');

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);

$iCodigoProcessoFiscal = $procfiscalini;
$procFiscal            = explode(",",$iCodigoProcessoFiscal);
$iCodigoLote           = $codlote;
$sRelatorios           = "";

for($abc = 0; $abc < count($procFiscal); $abc++){

  pg_close();
  include(modification('libs/db_conecta.php'));
  $iCodigoProc = $procFiscal[$abc];

  /**
   * Verifica se existe documento template setado para utilizar o agata
   */
  $sSqlParfiscal         = $oDaoParfiscal->sql_query_file(db_getsession("DB_instit"), "y32_templateautoinfracao");
  $rsParfiscal           = $oDaoParfiscal->sql_record($sSqlParfiscal);

  $sSqlFis   =  "select distinct nl01_codlanc,fd01_doc, nl04_inscr from fiscalizacao.fis_lancamento ";
  $sSqlFis  .=  " inner join fiscalizacao.fis_tipofiscaliza on fis_tipofiscaliza.y27_codtipo = fis_lancamento.nl01_codtipo ";
  $sSqlFis  .=  " inner join fiscalizacao.fis_fisdoc on fd01_codtipo = fis_tipofiscaliza.y27_codtipo";

  $andWhere = '';
  if (isset($iCodigoProcessoFiscal) and $iCodigoProcessoFiscal != '') {
    $sSqlFis .= " inner join fiscalizacao.fis_procfiscallanc on nl01_codlanc = nl09_lanc ";
    $sSqlFis .= " inner join fiscalizacao.fis_procfiscal on nl09_procfiscal = y100_sequencial ";
    $sSqlFis .= " inner JOIN fiscalizacao.fis_lancinscr ON nl04_codlanc = nl01_codlanc ";
    $sSqlFis .= " where fd01_instit = ".db_getsession("DB_instit")." and y100_sequencial IN ( {$iCodigoProc} ) ORDER BY nl01_codlanc limit 1";
  }

  $ResulFis  =  db_query($sSqlFis);

  if(pg_num_rows($ResulFis) > 0){
    db_fieldsmemory($ResulFis,0);
    $iTemplateNotiLancamento = $fd01_doc;
  }else{
    $iTemplateNotiLancamento = db_utils::fieldsMemory($rsParfiscal, 0)->y32_templateautoinfracao;
  }

  if ($iTemplateNotiLancamento != "") {

    $sDescrDoc        = "NL_{$iCodigoLote}_{$iCodigoProc}";
    $sImprFinal       = "tmp/NL_{$iCodigoLote}_".date("Ymd").".pdf";
    $sDescrFinal      = ECIDADE_PATH.$sImprFinal;
    $sNomeRelatorio   = "tmp/{$sDescrDoc}.pdf";
    $sCaminhoSalvoSxw = "tmp/{$sDescrDoc}.sxw";
    $sRelatorios     .= ECIDADE_PATH.$sNomeRelatorio." ";
    $sArquivoAgt      = "fiscal/fis_notificacao_de_lancamento_lote.agt";
    if( file_exists($sCaminhoSalvoSxw) ){
        unlink($sCaminhoSalvoSxw);
    }
    if( file_exists($sNomeRelatorio) ){
        unlink($sNomeRelatorio);
    }
    if( file_exists($sImprFinal) ){
        unlink($sImprFinal);
    }

    $oAgata           = new cl_dbagata($sArquivoAgt);
    $oApiAgata        = $oAgata->api;
    $oApiAgata->setOutputPath($sCaminhoSalvoSxw);
    $oApiAgata->setParameter('$sParametro', $iCodigoProc);

    $oDocumentoTemplate = new documentoTemplate(6000, $iTemplateNotiLancamento );

    if ( $oApiAgata->parseOpenOffice( $oDocumentoTemplate->getArquivoTemplate() ) ) {
      $lConversao       = db_stdClass::ex_oo2pdf($sCaminhoSalvoSxw, $sNomeRelatorio);
    }
  }
    unset($lConversao);
    unset($oDocumentoTemplate);
    unset($oApiAgata);
    unset($oAgata);
}

/* Se não tiver a página, verificar com o comando which pdfunite se está instalado no servidor */
$cmd = "pdfunite ".$sRelatorios." ".$sDescrFinal;
passthru($cmd);
db_redireciona($sImprFinal);
?>