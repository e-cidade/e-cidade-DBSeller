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

require_once(modification("fpdf151/impcarne.php"));
require_once(modification("std/db_stdClass.php"));
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_sql.php"));
require_once(modification("libs/db_libsys.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("dbagata/classes/core/AgataAPI.class"));
require_once(modification("model/documentoTemplate.model.php"));
require_once(modification("model/cadastro/CertidaoIsencaoPdf.php"));


db_postmemory($_SERVER);

$oGet           = db_utils::postMemory($_GET);

$iAno           = $oGet->ano;
$iTipoIsencao   = $oGet->tipoIsencao;
if (!empty($oGet->condominios)) {
  $sCondominios   = $oGet->condominios;
}
if (!empty($oGet->bairros)) {
  $sBairros       = $oGet->bairros;
}
if (!empty($oGet->tiposignorar)) {
  $sTiposIgnorar  = $oGet->tiposignorar;
}

if(!isset($iAno) || $iAno == "" || (integer)$iAno <= 1950 || (integer)$iAno >= 2050) {
  
  db_redireciona('db_erros.php?fechar=true&db_erro=Filtro de ano inválido!');
}

if(!isset($iTipoIsencao) || $iTipoIsencao == "") {
  
  db_redireciona('db_erros.php?fechar=true&db_erro=Filtro tipo de isenção !');
}

$cliptuisen = new cl_iptuisen;
$clcfiptu   = new cl_cfiptu;

$sqlparag = "select db02_texto
               from db_documento
                    inner join db_docparag  on db03_docum   = db04_docum
                    inner join db_tipodoc   on db08_codigo  = db03_tipodoc
                    inner join db_paragrafo on db04_idparag = db02_idparag
              where db03_tipodoc = 1017
                and db03_instit  = " . db_getsession("DB_instit")." order by db04_ordem ";
$resparag = db_query($sqlparag);


	$head1 = 'SECRETARIA DE FINANÇAS';
  if ( pg_numrows($resparag) > 0 ) {

	  db_fieldsmemory( $resparag, 0 );
 	  $head1 = $db02_texto;
  }


$pdf = new CertidaoInsencaoPdf('P', 'mm', 'A4');
$pdf->titulos["head1"] = $head1;
$oPdf= new db_impcarne($pdf, '29');
$oPdf->objpdf->AddPage();

$sqlMunic     = "select nomeinst, munic, logo from db_config where codigo = ". db_getsession("DB_instit");
$rsMunic      = db_query($sqlMunic);
$numrowsmunic = pg_numrows($rsMunic);
if ($numrowsmunic == 0){

  db_redireciona('db_erros.php?fechar=true&db_erro=Nome da instituição não encontrado!');
  exit;
}

db_fieldsmemory($rsMunic,0);

$oPdf->isenprefeitura = $nomeinst;
$oPdf->isenlogo       = $logo;
$oPdf->munic          = $munic; 

$sqlparag = "select *
	             from db_documento
	                  inner join db_docparag  on db03_docum   = db04_docum
	                  inner join db_tipodoc   on db08_codigo  = db03_tipodoc
	                  inner join db_paragrafo on db04_idparag = db02_idparag
	            where db03_tipodoc = 1019
                and db03_instit = " . db_getsession("DB_instit")." order by db04_ordem ";
$resparag = db_query($sqlparag);
$numrows  = pg_numrows($resparag);
if ( $numrows == 0 ) {

 db_redireciona('db_erros.php?fechar=true&db_erro=Configure o documento da certidão de isenção!');
 exit;
}

for($cont=0;$cont<$numrows;$cont++){
  
  db_fieldsmemory($resparag,$cont);
  if($db04_ordem == 1){
    $oPdf->isenmsg1 = db_geratexto($db02_texto);     		// Cabec CERTIDÃO
  }
  if($db04_ordem == 2){
    $oPdf->isenmsg4 = db_geratexto($db02_texto);			// texto CERTIDÃO
  }
  if($db04_ordem == 3){
    
    $oPdf->isenassinatura2 = db_geratexto($db02_texto);
    if($db02_descr == "ASSINATURAS_CODIGOPHP") {
      
      $oPdf->isenassinatura2 = $db02_texto;
    }
  }
  if($db04_ordem == 4){
    $oPdf->isenassinatura  = db_geratexto($db02_texto);		// Assinatura Secretario
  }
}



$rsCfiptu  = $clcfiptu->sql_record($clcfiptu->sql_query_file(db_getsession('DB_anousu'),"*",null,""));
$numrows   = $clcfiptu->numrows;
if ($numrows==0){

  db_redireciona('db_erros.php?fechar=true&db_erro=Configure os parametros do modulo ');
  exit;
}

db_fieldsmemory($rsCfiptu,0);

$sWhereTipoPromitente = '';
if(isset($j18_dadoscertisen) && $j18_dadoscertisen == 1){
  $sWhereTipoPromitente = " and (j41_tipopro is true or j41_tipopro is null) ";
}

$j46_dtini = null;
$j46_dtfim = null;

$where  = " j46_tipo = {$tipoIsencao} ";
$where .= " and exists(select 1 "; 
$where .= "    from isenexe ";
$where .= "   where j46_codigo = j47_codigo ";
$where .= "     and j47_anousu = $iAno) ";
if(!empty($sCondominios)) {

  $where .= " and j108_condominio in ($sCondominios)";
}

if(!empty($sBairros)) {

  $where .= " and proprietario.j13_codi in ($sBairros)";
}

if(!empty($sTiposIgnorar)) {

  $where .= " and j46_tipo not in ({$sTiposIgnorar})";
}

$where .= "   {$sWhereTipoPromitente}";

$sSqlIsencao = $cliptuisen->sql_query_isen( null,
                                            " proprietario.*,
                                              cgm_propri.z01_cgccpf as cpfpropri,
                                              isenproc.*,
                                              cgm.z01_nome as nomepromi,
                                              cgm.z01_cgccpf as cpfpromi,
                                              j45_obscertidao,
                                              j41_tipopro,
                                              j46_codigo,
                                              j46_matric,
                                              j46_dtini,
                                              extract(year from j46_dtini) as anoini,
                                              j46_dtfim,
                                              extract(year from j46_dtfim) as anofim,
                                              fc_dataextenso(current_date) as data_extenso",
                                            null,
                                            $where);
                  
$rsIptuisen  = $cliptuisen->sql_record($sSqlIsencao);
$numrowsisen = $cliptuisen->numrows;
if ($numrowsisen==0){

  db_redireciona('db_erros.php?fechar=true&db_erro=Não foi possível encontrar registros ');
  exit;
}

for ($iIndice = 0; $iIndice <  $numrowsisen; $iIndice ++) {

  db_fieldsmemory($rsIptuisen,$iIndice);


$oPdf->isenmatric = $j46_matric;
$oPdf->codisen    = $j46_codigo;
$oPdf->isennome   = $proprietario;
$oPdf->isencgc    = $cpfpropri;

/**
 * Valida regra do promitente
 */
if(isset($j18_dadoscertisen) && $j18_dadoscertisen == 0){

	$oPdf->isennome = $proprietario;
	$oPdf->isencgc  = $cpfpropri;

}else if(isset($j18_dadoscertisen) && $j18_dadoscertisen == 1){

  if(isset($nomepromi) && $nomepromi != ""){

		$oPdf->isennome = $nomepromi;
		$oPdf->isencgc  = $cpfpromi;
	}else{

		$oPdf->isennome = $proprietario;
		$oPdf->isencgc  = $cpfpropri;
	}
}

$oPdf->isenmsg3 = "";
if(isset($j45_obscertidao) && $j45_obscertidao != ""){
  $oPdf->isenmsg3 = $j45_obscertidao;
}

$oPdf->isenender     = (isset($nomepri)&&$nomepri!=""?$nomepri.",".$j39_numero."/".$j39_compl:"Sem endereço cadastrado");
$oPdf->isenbairro    = $j13_descr;
$oPdf->isendtini     = $j46_dtini;
$oPdf->isendtfim     = $j46_dtfim;
$oPdf->isenanoini    = $anoini;
$oPdf->isenanofim    = $anofim;
$oPdf->data_extenso  = $data_extenso;
$oPdf->isenproc      = $j61_codproc;
$oPdf->isensetor     = $j34_setor;
$oPdf->isenquadra    = $j34_quadra;
$oPdf->isenlote      = $j34_lote;

$oPdf->j05_setorloc  = $j06_setorloc;
$oPdf->j05_quadraloc = $j06_quadraloc;
$oPdf->j05_loteloc   = $j06_lote;

$oPdf->imprime();
if ( ($numrowsisen - $iIndice) > 1 ) {
  
  $oPdf->objpdf->AddPage();
  $oPdf->objpdf->SetTextColor(0, 0, 0);
}  
}
$oPdf->objpdf->Output();
