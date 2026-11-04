<?php
/**
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

require_once(modification("fpdf151/pdf3.php"));
require_once(modification("libs/db_sql.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_libdocumento.php"));
require_once(modification("classes/db_issbase_classe.php"));
require_once(modification("classes/db_cgm_classe.php"));
require_once(modification("classes/db_socios_classe.php"));
require_once(modification("classes/db_iptubase_classe.php"));
require_once(modification("classes/db_propri_classe.php"));
require_once(modification("classes/db_promitente_classe.php"));
require_once(modification("classes/db_pardiv_classe.php"));
require_once(modification("classes/db_parjuridico_classe.php"));
require_once(modification("classes/db_cfiptu_classe.php"));

$clissbase		 = new cl_issbase;
$clcgm				 = new cl_cgm;
$clsocios			 = new cl_socios;
$cliptubase		 = new cl_iptubase;
$clpropri			 = new cl_propri;
$clpromitente  = new cl_promitente;
$clpardiv			 = new cl_pardiv;
$clparjuridico = new cl_parjuridico;
$oLibDocumento = new libdocumento(1203);
$clcfiptu      = new cl_cfiptu;

$numeropg = isset($numeropg)?$numeropg:0;

db_sel_instit(null, "db21_usasisagua");

parse_str($_SERVER['QUERY_STRING']);

$exercicio = db_getsession("DB_anousu");
$borda        = 1;
$bordat		    = 1;
$preenc		    = 0;
$TPagina	    = 57;
$dbwhere	    = ' 1=1 ';
$tamanho	    = isset($tamanho)?$tamanho:10;
$pula			    = 2;

$sExpFalecido = "";
$sNaoPossui   = "NÃO POSSUI";

if (isset ($inicial) && $inicial != "") {
  $dbwhere .= " and v50_inicial = $inicial";
}
if (isset ($v50_inicial) && $v50_inicial != "") {
  $dbwhere .= " and v50_inicial >= $v50_inicial";
}
if (isset ($v50_inicial_fim) && $v50_inicial_fim != "") {
  $dbwhere .= " and v50_inicial <= $v50_inicial_fim";
}

if($db21_usasisagua == 't') {

  $sqlini  = "   select distinct                                               ";
  $sqlini .= "          v50_inicial as inicialproc,                            ";
  $sqlini .= "          v57_oab as oab_advogado,                               ";
  $sqlini .= "          z01_nome as nome_advogado,                             ";
  $sqlini .= "          v50_data,                                              ";
  $sqlini .= "          v51_certidao                                           ";
  $sqlini .= "     from inicial                                                ";
  $sqlini .= "          inner join juridico.advog on v50_advog = v57_numcgm    ";
  $sqlini .= "          inner join protocolo.cgm  on v57_numcgm = z01_numcgm   ";
  $sqlini .= "          left  join inicialnomes   on v58_inicial = v50_inicial ";
  $sqlini .= "          left  join inicialcert    on v51_inicial = v50_inicial ";
  $sqlini .= "    where $dbwhere                                               ";
  $sqlini .= " order by v51_certidao                                           ";

} else {

  $sqlini  = " select distinct																						";
  $sqlini .= "			  inicial.v50_inicial as inicialproc,									";
  $sqlini .= "			  v57_oab as oab_advogado,                            ";
  $sqlini .= "			  z01_nome as nome_advogado,                          ";
  $sqlini .= "			  inicial.v50_data																		";
  $sqlini .= "	 from inicial																							";
  $sqlini .= "	      inner join juridico.advog on v50_advog = v57_numcgm ";
  $sqlini .= "	      inner join protocolo.cgm on v57_numcgm = z01_numcgm ";
  $sqlini .= "			  left join inicialnomes on v58_inicial = v50_inicial ";
  $sqlini .= "  where $dbwhere																						";
  $sqlini .= " order by inicial.v50_inicial                               ";

}

$resultini       = db_query($sqlini);
$iLinhasIniciais = pg_num_rows($resultini);

if ( $iLinhasIniciais == 0) {

  $sMsg = _M('tributario.juridico.div2_inicial_002.iniciais_nao_encontradas');
  db_redireciona("db_erros.php?fechar=true&db_erro={$sMsg}");
  exit;
}

$rsCfiptu = $clcfiptu->sql_record($clcfiptu->sql_query_file("","j18_utilizaloc","","j18_anousu = ".db_getsession("DB_anousu")));
if ( $clcfiptu->numrows > 0 ) {
  $oCfiptu = db_utils::fieldsMemory($rsCfiptu,0);
} else {
  $oCfiptu->j18_utilizaloc = 'f';
}

$rsParJuridico = $clparjuridico->sql_record($clparjuridico->sql_query_file(db_getsession('DB_anousu'),db_getsession('DB_instit')));
$oParJuridico  = db_utils::fieldsMemory($rsParJuridico,0);

if ($oParJuridico->v19_envolprinciptu == "t") {
  $lPrincipal = "true";
}else{
  $lPrincipal = "false";
}

$resultpardiv = $clpardiv->sql_record($clpardiv->sql_query_file(null,"*",null,""));
db_fieldsmemory($resultpardiv, 0);

if ( isset($v04_confexpfalec) && $v04_confexpfalec != 1) {

	if ( !empty($v04_expfalecimentocda) ) {
		$sExpFalecido = trim($v04_expfalecimentocda)." ";
	}
}

class PDF_RODAPE extends pdf3 {

  function Footer() {
    $S = $this->lMargin;
    $this->SetLeftMargin(10);
    global $url;
    //Position at 1.5 cm from bottom

    $this->SetFont('Arial','',5);
    $this->text(10,289,'Base: '.@$GLOBALS["DB_NBASE"]);
    $this->SetFont('Arial','I',6);
    $this->SetY(-10);
    $nome = @$_SERVER["PHP_SELF"];
    $nome = substr($nome,strrpos($nome,"/")+1);
    $result_nomeusu = db_query("select nome as nomeusu from db_usuarios where id_usuario =".db_getsession("DB_id_usuario"));

    if (pg_numrows($result_nomeusu)>0){
      $nomeusu = pg_result($result_nomeusu,0,0);
    }
    if (isset($nomeusu)&&$nomeusu!=""){
      $emissor = $nomeusu;
    }else{
      $emissor = @$GLOBALS["DB_login"];
    }

    $this->Cell(0,10,$url.'   '.$nome.'   Emissor: '.substr(ucwords(strtolower($emissor)),0,30).'   Exercício: '.db_getsession("DB_anousu").'   Data: '.date("d-m-Y",db_getsession("DB_datausu"))." - ".date("H:i:s"),"T",0,'L');
    $this->Cell(0,10,' ',0,1,'R');
    $this->SetLeftMargin($S);
  }
}

if($numeropg == 's'){
  $pdf = new PDF3(); // abre a classe
}else{
  $pdf = new PDF_RODAPE();
}

if (!defined('DB_BIBLIOT')) {

  $pdf->Open(); // abre o relatorio
  $pdf->AliasNbPages(); // gera alias para as paginas
}

$pdf->SetAutoPageBreak(true, 30);

$lTemInicial = false;

for ($xyx = 0; $xyx < $iLinhasIniciais; $xyx++) {
  db_fieldsmemory($resultini, $xyx);

	$lParcelamento = false;
	$lDivida       = false;

  $sSqlOrigemInicial = " select distinct on (v13_certid, v01_exerc)
                                inicial.*,
                                certid.*,
                                certdiv.v14_certid as certdiv,
                                certter.v14_certid as certter,
                                certter.v14_parcel as parcel,
                                divida.v01_exerc,
                                v70_codforo,
                                v53_descr as vara_descricao,
                                v54_descr as local_descricao
                           from inicial
                                left  join localiza            on v50_codlocal                = v54_codlocal
                                inner join inicialcert         on inicial.v50_inicial         = inicialcert.v51_inicial
                                left  join processoforoinicial on inicial.v50_inicial         = processoforoinicial.v71_inicial
                                left  join processoforo        on processoforo.v70_sequencial = processoforoinicial.v71_processoforo
                                left  join vara                on v70_vara                    = v53_codvara
                                left outer join certid         on certid.v13_certid           = inicialcert.v51_certidao
                                left outer join certdiv        on certdiv.v14_certid          = certid.v13_certid
                                left outer join certter        on certter.v14_certid          = certid.v13_certid
                                left join  divida              on  divida.v01_coddiv          = certdiv.v14_coddiv
                          where inicial.v50_inicial = {$inicialproc} ";
  $rsOrigemInicial  = db_query($sSqlOrigemInicial);
  $iNumRows = pg_num_rows($rsOrigemInicial);

  $aListaExercicio = array();

  for ($iInd = 0; $iInd < $iNumRows; $iInd++) {

  	$iExercicio = db_utils::fieldsMemory($rsOrigemInicial,$iInd)->v01_exerc;

    if ( in_array($iExercicio, $aListaExercicio) ) {
      continue;
    }

  	if (trim($iExercicio) != "") {
  	 $aListaExercicio[] = db_utils::fieldsMemory($rsOrigemInicial,$iInd)->v01_exerc;
  	}
  }

  asort($aListaExercicio);

  $aOrigemInicial   = db_utils::getCollectionByRecord($rsOrigemInicial);
  db_fieldsmemory($rsOrigemInicial,0);

  $sNomeMatricula = "";
  $aListaProcedencia = array();
  $arrayInscr = array();
  foreach ($aOrigemInicial as $oOrigemInicial) {
      if (trim($oOrigemInicial->certdiv) != "") {

          $sNomeMatricula = "matric1";

          $sSqlDadosDivida = " select coalesce(k00_matric,0) as matric1,
										            coalesce(k00_inscr,0)  as inscr1,
										            coalesce(k00_contr,0)  as contr1,
  	                            divida.v01_exerc,
										            proced.v03_dcomp
  	                       from certdiv
									              inner join divida     on divida.v01_coddiv     = certdiv.v14_coddiv
									              inner join proced     on proced.v03_codigo     = divida.v01_proced
									              left  join arrematric on arrematric.k00_numpre = divida.v01_numpre
									              left  join arreinscr  on arreinscr.k00_numpre  = divida.v01_numpre
									              left  join arrecontr  on arrecontr.k00_numpre  = divida.v01_numpre
							            where certdiv.v14_certid = {$oOrigemInicial->certdiv}";

          $rsDadosInicial = db_query($sSqlDadosDivida);

          if (pg_num_rows($rsDadosInicial) > 0) {

              db_fieldsmemory($rsDadosInicial, 0);

              $matric2 = 0;
              $inscr2 = 0;
              $contr2 = 0;
              $lDivida = true;

          } else {
              continue;
          }


      } else {
          if (trim($oOrigemInicial->certter) != "") {

              $sNomeMatricula = "matric2";

              $sSqlDadosGerais = "select coalesce(k00_matric,0) as matric2,
                                           coalesce(k00_inscr,0)  as inscr2,
                                           coalesce(k00_contr,0)  as contr2,
                                           proced.v03_dcomp,
                                           divida.v01_exerc
                                    from certter
                                    inner join termo      on termo.v07_parcel      = certter.v14_parcel
                                    inner join termodiv   on termodiv.parcel       = termo.v07_parcel
                                    inner join divida     on divida.v01_coddiv     = termodiv.coddiv
                                    inner join proced     on proced.v03_codigo     = divida.v01_proced
                                    left  join arrematric on arrematric.k00_numpre = divida.v01_numpre
                                    left  join arreinscr  on arreinscr.k00_numpre  = divida.v01_numpre
                                    left  join arrecontr  on arrecontr.k00_numpre  = divida.v01_numpre
                                    where certter.v14_certid = {$oOrigemInicial->certter}
                                    UNION
                                    select coalesce(k00_matric,0) as matric2,
                                           coalesce(k00_inscr,0)  as inscr2,
                                           coalesce(k00_contr,0)  as contr2,
                                           proced.v03_dcomp,
                                           divida.v01_exerc
                                    from fc_parc_origem_completo( ( 
                                        select v07_numpre
                                        from termo
                                        where v07_parcel = {$oOrigemInicial->parcel} ) ) as x
                                    inner join termoreparc on termoreparc.v08_parcel = x.riparcel
                                    inner join termodiv    on termodiv.parcel        = termoreparc.v08_parcelorigem
                                    inner join divida      on divida.v01_coddiv      = termodiv.coddiv
                                    inner join proced      on proced.v03_codigo      = divida.v01_proced
                                    left  join arrematric  on arrematric.k00_numpre  = divida.v01_numpre
                                    left  join arreinscr   on arreinscr.k00_numpre   = divida.v01_numpre
                                    left  join arrecontr   on arrecontr.k00_numpre   = divida.v01_numpre
                                    order by v01_exerc asc";

              $rsDadosInicial = db_query($sSqlDadosGerais);

              if (pg_num_rows($rsDadosInicial) > 0) {
                  db_fieldsmemory($rsDadosInicial, 0);
              } else {
                  continue;
              }

              $matric1 = 0;
              $inscr1 = 0;
              $contr1 = 0;
              $lParcelamento = true;

          } else {
              continue;
          }
      }

      $iLinhaDadosInicial = pg_num_rows($rsDadosInicial);

      for ($iInd=0; $iInd < $iLinhaDadosInicial; $iInd++ ) {

          $oDadosInicial = db_utils::fieldsMemory($rsDadosInicial,$iInd);

          if (!in_array($oDadosInicial->v01_exerc, $aListaExercicio)) {
              $aListaExercicio[]   = $oDadosInicial->v01_exerc;
          }
          if (!in_array($oDadosInicial->v03_dcomp, $aListaProcedencia)) {
              $aListaProcedencia[] = $oDadosInicial->v03_dcomp;
          }
      }
      if($inscr1 != 0 && $inscr1 != '' ){
        if(!in_array($inscr1 , $arrayInscr)){
          array_push($arrayInscr, $inscr1);
        }
      }else if ($inscr2 != 0 && $inscr2 != ''){
        if(!in_array($inscr2 , $arrayInscr)){
          array_push($arrayInscr, $inscr2);
        }
      }
  }

  $sqlPagoCancelado  = " select a.k00_numpre as inicial_paga,                              ";
  $sqlPagoCancelado .= "        b.k00_numpre as parc_pago,                                 ";
  $sqlPagoCancelado .= "        r.k00_numpre,                                              ";
  $sqlPagoCancelado .= "        v59_numpre,                                                ";
  $sqlPagoCancelado .= "        v59_inicial                                                ";
  $sqlPagoCancelado .= "   from inicialnumpre                                              ";
  $sqlPagoCancelado .= "        left join arrecant a on a.k00_numpre = v59_numpre          ";
  $sqlPagoCancelado .= "        left join termoini   on inicial      = v59_inicial         ";
  $sqlPagoCancelado .= "                            and inicial      = $inicialproc        ";
  $sqlPagoCancelado .= "        left join arrecad r  on r.k00_numpre = v59_numpre          ";
  $sqlPagoCancelado .= "        left join termo o    on o.v07_numpre   = v59_numpre        ";
  $sqlPagoCancelado .= "        left join termo      on termo.v07_parcel   = parcel        ";
  $sqlPagoCancelado .= "        left join arrecant b on termo.v07_numpre   = b.k00_numpre  ";
  $sqlPagoCancelado .= "        left join arrecad  c on termo.v07_numpre   = c.k00_numpre  ";
  $sqlPagoCancelado .= "  where ( a.k00_numpre is not null or b.k00_numpre is not null )   ";
  $sqlPagoCancelado .= "    and r.k00_numpre is null                                       ";
  $sqlPagoCancelado .= "    and o.v07_numpre is null                                       ";
  $sqlPagoCancelado .= "    and c.k00_numpre is null                                       ";
  $sqlPagoCancelado .= "    and v59_inicial = $inicialproc                                 ";

  $rsPagoCancelado = db_query($sqlPagoCancelado);

  if (pg_numrows($rsPagoCancelado) > 0 ) {
    continue;
  }else{
    $lTemInicial = true;
  }

  $xinicial = $v50_inicial;
  $oLibDocumento->Exercicio    = "Exercício(s) de ".implode(", ",$aListaExercicio);
  $oLibDocumento->Procedencias = "Procedência(s) ".ucfirst(strtolower(implode(", ",$aListaProcedencia)));

  $sSqlCert = " select distinct v51_certidao
                  from inicialcert
                 where v51_inicial = {$inicialproc}
                 order by v51_certidao";

  $rsCertid     = db_query($sSqlCert);
  $iLinhasCert  = pg_num_rows($rsCertid);
  $aListaCertid = array();

  for ( $iInd=0; $iInd < $iLinhasCert; $iInd++ ) {
  	$aListaCertid[] = db_utils::fieldsMemory($rsCertid,$iInd)->v51_certidao;
  }

  $Certidao = implode(",",$aListaCertid);

  $ValorTotal = 0;

  if ( $lParcelamento || $lDivida ) {

    $sSqlParcelamentos   = " select v14_parcel,
                                    v07_numpre
							                 from certter
 		                                inner join termo on v07_parcel = v14_parcel
							                where v14_certid in ($Certidao) ";

    $rsParcelamentos     = db_query($sSqlParcelamentos);
    $iLinhasParcel       = pg_num_rows($rsParcelamentos);
    $aListaParcelamentos = array();

    for ( $iIndParcel=0; $iIndParcel < $iLinhasParcel; $iIndParcel++) {

      $oParcelamento         = db_utils::fieldsMemory($rsParcelamentos,$iIndParcel);
    	$aListaParcelamentos[] = $oParcelamento->v14_parcel;

      if ( isset($atualiza) && $atualiza == 's') {
        $rsDadosDebitoCorrigido = debitos_numpre($oParcelamento->v07_numpre, 0, 0, db_getsession("DB_datausu"), db_getsession("DB_anousu"), 0);
      } else {
        $rsDadosDebitoCorrigido = debitos_numpre($oParcelamento->v07_numpre, 0, 0, mktime(0, 0, 0, substr($v13_dtemis, 5, 2), substr($v13_dtemis, 8, 2), substr($v13_dtemis, 0, 4)), substr($v13_dtemis, 0, 4), 0);
      }

      if ( $rsDadosDebitoCorrigido != false ) {

      	$iLinhasDebito = pg_num_rows($rsDadosDebitoCorrigido);

        for ($iIndDebito = 0; $iIndDebito < $iLinhasDebito; $iIndDebito++) {
          $ValorTotal += db_utils::fieldsMemory($rsDadosDebitoCorrigido,$iIndDebito)->total;
        }
      }
    }

    $oLibDocumento->parcelamentos  = implode(",",$aListaParcelamentos);

    $corrigirPeloArreold = false;

    $sSqlDadosDebitos = " select distinct k00_numpre,
                                          k00_numpar
			                      from certdiv
											           inner join divida 	 on certdiv.v14_coddiv = divida.v01_coddiv
												         inner join arrecad	 on arrecad.k00_numpre = divida.v01_numpre
			                                              and arrecad.k00_numpar = divida.v01_numpar
			                     where v14_certid in ($Certidao)";

    $rsDadosDebitos      = db_query($sSqlDadosDebitos);
    $iLinhasDadosDebitos = pg_num_rows($rsDadosDebitos);

    if ( $iLinhasDadosDebitos == 0 ) {

      $sSqlDadosDebitos  = " select distinct ";
      $sSqlDadosDebitos .= "        inicial, ";
      $sSqlDadosDebitos .= "        arreold.k00_numpre, ";
      $sSqlDadosDebitos .= "        arreold.k00_numpar, ";
      $sSqlDadosDebitos .= "        v50_data ";
      $sSqlDadosDebitos .= "   from termoini ";
      $sSqlDadosDebitos .= "        inner join inicial       on inicial     = v50_inicial         ";
      $sSqlDadosDebitos .= "        inner join termo         on v07_parcel  = parcel              ";
      $sSqlDadosDebitos .= "        inner join arrecad       on k00_numpre  = v07_numpre          ";
      $sSqlDadosDebitos .= "        inner join inicialnumpre on v59_inicial = inicial             ";
      $sSqlDadosDebitos .= "        inner join arreold       on v59_numpre  = arreold.k00_numpre  ";
      $sSqlDadosDebitos .= "  where inicial = $inicialproc ";
      $rsDadosDebitos      = db_query($sSqlDadosDebitos);
      $iLinhasDadosDebitos = pg_num_rows($rsDadosDebitos);

      $corrigirPeloArreold = true;
    } else {
      $corrigirPeloArreold = false;
    }

    for ( $iIndDadosDebitos = 0; $iIndDadosDebitos < $iLinhasDadosDebitos; $iIndDadosDebitos++ ) {

      db_fieldsmemory($rsDadosDebitos, $iIndDadosDebitos);

      if($corrigirPeloArreold && ($lParcelamento or $lDivida)) {

        $dataemis = mktime(0,0,0,substr($v50_data,5,2),substr($v50_data,8,2),substr($v50_data,0,4));
        $rsDadosDebitoCorrigido  = debitos_numpre_old($k00_numpre, 0, 0, $dataemis, db_getsession('DB_anousu'), $k00_numpar);

      } else {

        if (isset ($atualiza) && $atualiza == 's') {
          $rsDadosDebitoCorrigido = debitos_numpre($k00_numpre, 0, 0, db_getsession("DB_datausu"), db_getsession("DB_anousu"), $k00_numpar);
        } else {
          $rsDadosDebitoCorrigido = debitos_numpre($k00_numpre, 0, 0, mktime(0, 0, 0, substr($v13_dtemis, 5, 2), substr($v13_dtemis, 8, 2), substr($v13_dtemis, 0, 4)), substr($v13_dtemis, 0, 4), $k00_numpar);
        }

      }

      if($rsDadosDebitoCorrigido) {
        for ($iIndDebito = 0; $iIndDebito < pg_numrows($rsDadosDebitoCorrigido); $iIndDebito++) {
          $ValorTotal += db_utils::fieldsMemory($rsDadosDebitoCorrigido, $iIndDebito)->total;
        }
      }

    }


  } else {

    $oParms           = new stdClass();
    $oParms->iInicial = $xinicial;
    $oParms->xChr     = chr(176);

    $sMsg = _M('tributario.juridico.div2_inicial_002.inicial_nao_iptu_issqn', $oParms);
    db_redireciona("db_erros.php?fechar=true&db_erro={$sMsg}");
    //db_redireciona('db_erros.php?fechar=true&db_erro=Inicial n' . chr(176) . ' ' . $xinicial . ' não é de IPTU nem de ISSQN!');
    exit;
  }

  /**
   * @todo rever essa logica
   * rotina nao esta pronta para buscar o envolvido quando existe matricula e inscricao na mesma divida, retorna apenas
   * um dos registros vinculados
   */
  $xmatric   = $matric1 + $matric2;
  $xcontr    = $contr1  + $contr2;
  $xinscr    = $inscr1  + $inscr2;
  $q02_inscr = $xinscr;
  $xender    = '';
  $ximovel   = '';

  $inscricao_matricula_cgm = "";

  if ( $xmatric > 0 ) {

    $sql    = "select munic, uf, numero as numeroinst from db_config where codigo = " . db_getsession("DB_instit");
    $result = db_query($sql);

    if (pg_numrows($result) > 0) {
      db_fieldsmemory($result, 0);
    }

    $sql = "select *
				      from proprietario
				      where j01_matric in ( select distinct k00_matric
													            from inicialnumpre
													                 inner join arrematric on k00_numpre = v59_numpre
										                 where v59_inicial  = $inicialproc )";

    $result = db_query($sql);

    if (pg_numrows($result) > 0) {

      $v = '';

      for ($jk = 0;$jk < pg_numrows($result);$jk++) {

        $tipo = trim(pg_result($result, $jk, "j14_tipo"));
        $nomepri = trim(pg_result($result,$jk , "nomepri")) != '' ? (' ' . trim(pg_result($result,$jk , "nomepri"))) : '';
        $numero = trim(pg_result($result, $jk, "j39_numero"));
        $setor = trim(pg_result($result, $jk, "j34_setor"));
        $quadra = trim(pg_result($result, $jk, "j34_quadra")) != '' ? ("/" . trim(pg_result($result, $jk, "j34_quadra"))) : '';
        $lote1 = trim(pg_result($result, $jk, "j34_lote")) != '' ? ("/" . trim(pg_result($result, $jk, "j34_lote"))) : '';
        $descricaoTexto = trim($setor . $quadra .$lote1) != '' ? ' setor/quadra/lote ' : '';
        $setorLocalizacao = trim(pg_result($result, $jk, "j06_setorloc"));
        $descricao = trim(pg_result($result, $jk, "j05_descr")) != '' ? '-' .trim(pg_result($result, $jk, "j05_descr")) : '';
        $quadraLoc = trim(pg_result($result, $jk, "j06_quadraloc")) != '' ? '/' . trim(pg_result($result, $jk, "j06_quadraloc")) : '';
        $lote2 = trim(pg_result($result, $jk, "j06_lote")) != '' ? '/' . trim(pg_result($result, $jk, "j06_lote")) : '';
        $descricaoTextoLocalizacao = trim($descricao . $quadraLoc . $lote2) != '' ? ' - dados de localização: ' : '';
        $matricula = trim(pg_result($result,$jk,"j01_matric")) != '' ? ' (matrícula municipal n' . chr(176) . ' ' . trim(pg_result($result,$jk,"j01_matric")) . ')' : '';
        $municipio = trim($munic) != '' ? ', em ' . $munic : '';
        $unidadefederal = trim($uf) ? '/' . $uf : '';

        $ximovel .= $v. $tipo . $nomepri . ', ' . $numero . $descricaoTexto . $setor . $quadra . $lote1 . ( $oCfiptu->j18_utilizaloc == 't'? $descricaoTextoLocalizacao . $setorLocalizacao . $descricao . $quadraLoc . $lote2 :"") . $municipio . $unidadefederal . $matricula;
        $v = ", ";
      }

      $ximovel .= ".";
      $cgmpri = pg_result($result, 0, "z01_numcgm");
    }

    $aDadosInicial  = db_utils::getCollectionByRecord($rsDadosInicial);
    $aCgmEnvolvidos = array();

    foreach ($aDadosInicial as $iIndice => $oDadosInicial) {

      if($oDadosInicial->$sNomeMatricula == '0'){
        continue;
      }
      $sSqlEnvolvidos = "select  * from fc_busca_envolvidos({$lPrincipal},{$oParJuridico->v19_envolinicialiptu},'M',{$oDadosInicial->$sNomeMatricula})";
      $rsEnvolvidos   = db_query($sSqlEnvolvidos) or die($sSqlEnvolvidos);
      $iLinhasEnvol   = pg_num_rows($rsEnvolvidos);

      if ($oParJuridico->v19_envolinicialiptu == 2 && $iLinhasEnvol == 0 ) {

        $sSqlEnvolvidos  = " select j01_numcgm as rinumcgm  ";
        $sSqlEnvolvidos .= "   from iptubase                ";
        $sSqlEnvolvidos .= "  where j01_matric = {$xmatric} ";

        $rsEnvolvidos = db_query($sSqlEnvolvidos) or die($sSqlEnvolvidos);
        $iLinhasEnvol = pg_num_rows($rsEnvolvidos);
      }

      for ($i=0; $i < $iLinhasEnvol; $i++) {

        $oEnvolvidos  = db_utils::fieldsMemory($rsEnvolvidos, $i);

        if ( in_array($oEnvolvidos->rinumcgm, $aCgmEnvolvidos) ) {
          continue;
        }
        $aCgmEnvolvidos[] = $oEnvolvidos->rinumcgm;

        $sSqlDadosEnvolvido =  " select                                                                                              ";
        $sSqlDadosEnvolvido .= "     distinct cgm.*,                                                                                 ";
        $sSqlDadosEnvolvido .= "     UPPER(coalesce(                                                                                 ";
        $sSqlDadosEnvolvido .= "         tipopromitente.j164_descricao,                                                              ";
        $sSqlDadosEnvolvido .= "         tipoproprietario.j163_descricao                                                             ";
        $sSqlDadosEnvolvido .= "     )) as tipo                                                                                      ";
        $sSqlDadosEnvolvido .= " from                                                                                                ";
        $sSqlDadosEnvolvido .= "     cgm                                                                                             ";
        $sSqlDadosEnvolvido .= "     left join promitente on promitente.j41_numcgm = cgm.z01_numcgm                                  ";
        $sSqlDadosEnvolvido .= "     left join tipopromitente ON tipopromitente.j164_tipopromitente = promitente.j41_tipopromitente  ";
        $sSqlDadosEnvolvido .= "     left join proprietario on proprietario.z01_numcgm = cgm.z01_numcgm                              ";
        $sSqlDadosEnvolvido .= "     left join tipoproprietario on tipoproprietario.j163_abreviatura = proprietario.j163_abreviatura ";
        $sSqlDadosEnvolvido .= " where cgm.z01_numcgm = {$oEnvolvidos->rinumcgm}                                                     ";

        $rsDadosEnvol = $clcgm->sql_record($sSqlDadosEnvolvido);

        if ($clcgm->numrows > 0) {

          $oDadosEnvol = db_utils::fieldsMemory($rsDadosEnvol,0);

          if ( isset($oDadosEnvol->z01_dtfalecimento) ) {
            if ( empty($oDadosEnvol->z01_dtfalecimento) ) {
              $sExpFalecido = "";
            }
          }

          if (strlen($oDadosEnvol->z01_cgccpf) > 11) {
            $sCgcCpf = "CNPJ: ".db_formatar($oDadosEnvol->z01_cgccpf, 'cnpj').",";
          } else {
            $sCgcCpf = "CPF: ".db_formatar($oDadosEnvol->z01_cgccpf, 'cpf').",";
          }

          $sNacionalidade = '';
          if ( strlen ( trim($oDadosEnvol->z01_cgccpf) ) == 11 ){

            if( $oDadosEnvol->z01_nacion == 1 ){
              $sNacionalidade = ", BRASILEIRA(O)";
            } else if ($oDadosEnvol->z01_nacion == 2) {
              $sNacionalidade = ", ESTRANGEIRA(O)";
            }else{
              $sNacionalidade = " ";
            }
          }


          $sqlNomeETipoEnvolvido = $cliptubase->sql_query_envolvidos_matricula($xmatric, $oEnvolvidos->rinumcgm);
          $rsNomeETipoEnvolvido  = db_query($sqlNomeETipoEnvolvido);
          $nomeETipoEnvolvido = db_utils::getCollectionByRecord($rsNomeETipoEnvolvido);

          $nomeEnvolvido = isset($nomeETipoEnvolvido) && count($nomeETipoEnvolvido) > 0 ? $nomeETipoEnvolvido[0]->nome : trim($oDadosEnvol->z01_nome);
          $cgccpfEnvolvido = $oDadosEnvol->z01_cgccpf && trim($oDadosEnvol->z01_cgccpf) != '' ? ", {$sCgcCpf}":"";
          $numeroEnvolvido = $oDadosEnvol->z01_numero && trim($oDadosEnvol->z01_numero) ? ', N°'.$oDadosEnvol->z01_numero : '';
          $bairroEnvolvido = $oDadosEnvol->z01_bairro && trim($oDadosEnvol->z01_bairro) != "" ? ", BAIRRO: {$oDadosEnvol->z01_bairro}" : "";
          $municipioEnvolvido = $oDadosEnvol->z01_munic && trim($oDadosEnvol->z01_munic) ? ', '.$oDadosEnvol->z01_munic : '';
          $ufEnvolvido = $oDadosEnvol->z01_uf && trim($oDadosEnvol->z01_uf) ? '-'.$oDadosEnvol->z01_uf : '';
          $cepEnvolvido = $oDadosEnvol->z01_cep && trim($oDadosEnvol->z01_cep) != "" ? ", CEP: {$oDadosEnvol->z01_cep}" : "";
          $caixaPostalEnvolvido = $oDadosEnvol->z01_cxpostal && trim($oDadosEnvol->z01_cxpostal) != "" ? ", CAIXA POSTAL: {$oDadosEnvol->z01_cxpostal}" : "";
          $telefoneEnvolvido = $oDadosEnvol->z01_telef && trim($oDadosEnvol->z01_telef) != '' ? ", TELEFONE: ". $oDadosEnvol->z01_telef : '';
          $celularEnvolvido = $oDadosEnvol->z01_telcel && trim($oDadosEnvol->z01_telcel) != '' ? ", CELULAR: ". $oDadosEnvol->z01_telcel : '';
          $emailEnvolvido = $oDadosEnvol->z01_email != '' ? ", EMAIL: ". $oDadosEnvol->z01_email : '';

          $xender .= $i > 0 ? ', ' : '';
          $xender	.=  $sExpFalecido . $nomeEnvolvido . $sNacionalidade . $cgccpfEnvolvido;
          $xender	.= " ENDEREÇO: ".$oDadosEnvol->z01_ender . $numeroEnvolvido . '' . $bairroEnvolvido . $municipioEnvolvido . $ufEnvolvido .''. $cepEnvolvido .''. $caixaPostalEnvolvido;
          $xender	.= $telefoneEnvolvido . $celularEnvolvido;
          $xender	.= $emailEnvolvido;
        }
      }
    }

    $inscricao_matricula_cgm = "MATRICULA: $xmatric";

  } else if ($xinscr > 0) {

    $sTextoSocios   = " e seus sócios:";

    $sSqlEnvolvidos = "select  * from fc_busca_envolvidos({$lPrincipal},{$oParJuridico->v19_envolinicialiss},'I',{$xinscr})";
    $rsEnvolvidos   = db_query($sSqlEnvolvidos) or die($sSqlEnvolvidos);
    $iLinhasEnvol   = pg_num_rows($rsEnvolvidos);

    $sCgcCpf = "";

    for ($i=0; $i < $iLinhasEnvol; $i++) {

      $oEnvolvidos  = db_utils::fieldsMemory($rsEnvolvidos,$i);
      $rsDadosEnvol = $clcgm->sql_record($clcgm->sql_query_file($oEnvolvidos->rinumcgm));


      if ($clcgm->numrows > 0) {

        $oDadosEnvol = db_utils::fieldsMemory($rsDadosEnvol,0);

        if ( isset($oDadosEnvol->z01_dtfalecimento) ) {
          if ( empty($oDadosEnvol->z01_dtfalecimento) ) {
            $sExpFalecido = "";
          }
        }

        if (strlen($oDadosEnvol->z01_cgccpf) > 11) {
          $sCgcCpf = ", CNPJ: ".db_formatar($oDadosEnvol->z01_cgccpf, 'cnpj');
        } else {
          $sCgcCpf = ", CPF: ".db_formatar($oDadosEnvol->z01_cgccpf, 'cpf');
        }

        $numeroEnvolvido = $oDadosEnvol->z01_numero && trim($oDadosEnvol->z01_numero) ? ', N°'.$oDadosEnvol->z01_numero : '';
        $bairroEnvolvido = $oDadosEnvol->z01_bairro && trim($oDadosEnvol->z01_bairro) != "" ? ", BAIRRO: {$oDadosEnvol->z01_bairro}" : "";
        $municipioEnvolvido = $oDadosEnvol->z01_munic && trim($oDadosEnvol->z01_munic) ? ', '.$oDadosEnvol->z01_munic : '';
        $ufEnvolvido = $oDadosEnvol->z01_uf && trim($oDadosEnvol->z01_uf) ? '-'.$oDadosEnvol->z01_uf : '';
        $cepEnvolvido = $oDadosEnvol->z01_cep && trim($oDadosEnvol->z01_cep) != "" ? ", CEP: {$oDadosEnvol->z01_cep}" : "";
        $caixaPostalEnvolvido = $oDadosEnvol->z01_cxpostal && trim($oDadosEnvol->z01_cxpostal) != "" ? ", CAIXA POSTAL: {$oDadosEnvol->z01_cxpostal}" : "";
        $telefoneEnvolvido = $oDadosEnvol->z01_telef && trim($oDadosEnvol->z01_telef) != '' ? ", TELEFONE: ". $oDadosEnvol->z01_telef : '';
        $celularEnvolvido = $oDadosEnvol->z01_telcel && trim($oDadosEnvol->z01_telcel) != '' ? ", CELULAR: ". $oDadosEnvol->z01_telcel : '';
        $emailEnvolvido = $oDadosEnvol->z01_email != '' ? ", EMAIL: ". $oDadosEnvol->z01_email : '';

        if ($oEnvolvidos->ritipoenvol == "4") {
          $xender  = $sExpFalecido.trim($oDadosEnvol->z01_nome).",  " .$sCgcCpf.' sito o endereço: '.$oDadosEnvol->z01_ender. $numeroEnvolvido .''. $bairroEnvolvido . $municipioEnvolvido . $ufEnvolvido .''. $cepEnvolvido .''. $caixaPostalEnvolvido;
        } else {

          $xender .= $sTextoSocios;
          $xender .= "\n-  ".$sExpFalecido.trim($oDadosEnvol->z01_nome).", " .$sCgcCpf;
          $xender .= ", ENDEREÇO: ".$oDadosEnvol->z01_ender. $numeroEnvolvido .''. $bairroEnvolvido . $municipioEnvolvido . $ufEnvolvido .''. $cepEnvolvido .''. $caixaPostalEnvolvido;
          $sTextoSocios = "";
        }
        $xender	.= $telefoneEnvolvido . $celularEnvolvido;
        $xender	.= $emailEnvolvido;
        $xender .= '. ';

      }
    }

    $inscricao_matricula_cgm = "INSCRICAO: $xinscr";

  } else {

    $sql = "select munic, uf, numero as numeroinst from db_config where codigo = " . db_getsession("DB_instit");
    $result = db_query($sql);
    db_fieldsmemory($result, 0);
    $arrematric = db_query("select distinct k00_matric
            from inicialnumpre
            inner join arrematric on k00_numpre = v59_numpre
            where v59_inicial  = $inicialproc");
            db_fieldsmemory($arrematric, 0);

    $where = '';

    if ($oParJuridico->v19_envolinicialiptu > 0 && !empty($k00_matric)) {
      $sSqlEnvolvidos = "select  * from fc_busca_envolvidos({$lPrincipal},{$oParJuridico->v19_envolinicialiptu},'M',{$k00_matric})";
      $rsEnvolvidos   = db_query($sSqlEnvolvidos) or die($sSqlEnvolvidos);

      $iLinhasEnvol   = pg_num_rows($rsEnvolvidos);
      if($iLinhasEnvol == 1){
          $where .= " and z01_numcgm = ".pg_fetch_all($rsEnvolvidos)[0]['rinumcgm'];
      }else if ($iLinhasEnvol > 1){
        $where .= ' and z01_numcgm in (';
        for ($i=0; $i < $iLinhasEnvol; $i++) { 
            if($i == $iLinhasEnvol-1){
              $where .= pg_fetch_all($rsEnvolvidos)[$i]['rinumcgm'].')';
            }else{
              $where .= pg_fetch_all($rsEnvolvidos)[$i]['rinumcgm'].', ';
            }
        }
      }
    }

    if ($certdiv > 0) {
      $sSql  = " select distinct cgm.*																	   ";
      $sSql .= "	 from certdiv																						 ";
      $sSql .= "			  inner join divida			on v14_coddiv = v01_coddiv	 ";
      $sSql .= "			  inner join arrenumcgm 	on v01_numpre = k00_numpre ";
      $sSql .= "			  inner join cgm					on z01_numcgm = k00_numcgm ";
      $sSql .= "	where v14_certid = $certdiv															 ";
    } else {
      $sSql  = " select distinct cgm.*																		 ";
      $sSql .= "	 from certter																						 ";
      $sSql .= "			  inner join termo				on v14_parcel = v07_parcel ";
      $sSql .= "			  inner join arrenumcgm 	on v07_numpre = k00_numpre ";
      $sSql .= "			  inner join cgm					on z01_numcgm = k00_numcgm ";
      $sSql .= "  where v14_certid = $certter															 ";
    }
    if($where != ''){
      $sSql .= $where;
    }

    $rsDadosEnvol = db_query($sSql);

    for ($xi = 0; $xi < pg_num_rows($rsDadosEnvol); $xi++) {
      db_fieldsmemory($rsDadosEnvol, $xi);

      if ( isset($z01_dtfalecimento) ) {
        if ( empty($z01_dtfalecimento) ) {
          $sExpFalecido = "";
        }
      }

      if (strlen($z01_cgccpf) > 11) {
        $sCgcCpf = "CNPJ: ".db_formatar($z01_cgccpf, 'cnpj');
      } else {
        $sCgcCpf = "CPF: ".db_formatar($z01_cgccpf, 'cpf');
      }

      if( $z01_nacion == 1 ){
        $sNacionalidade = ", BRASILEIRA(O)";
      } else if ($z01_nacion == 2) {
        $sNacionalidade = ", ESTRANGEIRA(O)";
      }else{
        $sNacionalidade = " ";
      }

      $cgccpfEnvolvido = (strlen(trim($z01_cgccpf)) == 11 ? $sNacionalidade : "") . ($z01_cgccpf != '' ? ", {$sCgcCpf}" : "");
      $bairroEnvolvido = $z01_bairro && trim($z01_bairro) != "" ? ", BAIRRO: $z01_bairro" : "";
      $municipioEnvolvido = $z01_munic && trim($z01_munic) != '' ? ', ' . $z01_munic : '';
      $ufEnvolvido =  $z01_uf && trim($z01_uf) != '' ? '-' . $z01_uf : '';
      $cepEnvolvido = $z01_cep && trim($z01_cep) != "" ? ", CEP: $z01_cep" : "";
      $caixaPostalEnvolvido = $z01_cxpostal && trim($z01_cxpostal) != "" ? ", CAIXA POSTAL: $z01_cxpostal" : "";
 
      $xender .= $sExpFalecido . trim($z01_nome) . $cgccpfEnvolvido;
      $xender .= ", ENDEREÇO: " . $z01_ender . ($z01_numero > 0 ? ', N°' . $z01_numero : '') . '' . $bairroEnvolvido . $municipioEnvolvido . $ufEnvolvido. '' . $cepEnvolvido . '' . $caixaPostalEnvolvido;
      $xender	.= ", TELEFONE: ".($oDadosEnvol->z01_telef!=''?$oDadosEnvol->z01_telef:$sNaoPossui) . ", CELULAR: ".($oDadosEnvol->z01_telcel!=''?$oDadosEnvol->z01_telcel:$sNaoPossui);
      $xender	.= ", EMAIL:    ".($oDadosEnvol->z01_email!=''?$oDadosEnvol->z01_email:$sNaoPossui) .". ";
    }

    $inscricao_matricula_cgm = "CGM: $z01_numcgm";
  }

  $pdf->AddPage(); // adiciona uma pagina
  $pdf->setVariavelControle('Controle Administrativo nº ' . $inicialproc);
  $pdf->SetTextColor(0, 0, 0);
  $pdf->SetFillColor(220);
  $yy = $pdf->h - 11;
  $pdf->SetFont('Arial', '', $tamanho);

  $cgc 		  = db_formatar($cgc, 'cnpj');
  $Valor 		= db_formatar($ValorTotal, 'f');
  $Valorext = db_extenso($ValorTotal);

  $inscr = '';

  foreach ($arrayInscr as $key => $value) {
    if($key == 0){
      $inscr.= $value;
    }else{
      $inscr.= ', '.$value;
    }
  }
  
  $oLibDocumento->cgc           = $cgc;
  $oLibDocumento->uf            = $uf;
  $oLibDocumento->munic 				= $munic;
  $oLibDocumento->ender 				= $ender;
  $oLibDocumento->xender 				= $xender;
  $oLibDocumento->Valor					= trim($Valor);
  $oLibDocumento->Valorext			= trim($Valorext);
  $oLibDocumento->Certidao			= $Certidao;
  $oLibDocumento->xinscr			  = $inscr;
  $oLibDocumento->ximovel			  = $ximovel;
  $oLibDocumento->xmatric       = $xmatric;
  $oLibDocumento->processoforo  = $oOrigemInicial->v70_codforo;
  $oLibDocumento->numeroinicial = $oOrigemInicial->v50_inicial;
  $oLibDocumento->dataporextenso = date('d')." de ".db_mes(date('m'))." de ".date('Y');
  $oLibDocumento->oab_advogado   = $oab_advogado;
  $oLibDocumento->nome_advogado  = $nome_advogado;
  $oLibDocumento->vara_descricao = $vara_descricao;
  $oLibDocumento->local_descricao = $local_descricao;
  $oLibDocumento->inscricao_matricula_cgm = $inscricao_matricula_cgm;

  $aParagrafos = $oLibDocumento->getDocParagrafos();

  foreach ($aParagrafos as $oParag) {

    if ($xmatric > 0) {
      if(strtolower($oParag->oParag->db02_descr) == "inicial_p4i" || strtolower($oParag->oParag->db02_descr) == "inicial_p4c" ){
        continue;
      }
    } else if ($xinscr > 0) {
      if(strtolower($oParag->oParag->db02_descr) == "inicial_p4m" || strtolower($oParag->oParag->db02_descr) == "inicial_p4c" ){
        continue;
      }
    } else {
      if(strtolower($oParag->oParag->db02_descr) == "inicial_p4m" || strtolower($oParag->oParag->db02_descr) == "inicial_p4i" ){
        continue;
      }
    }
//INICIAL_P4C
    if ($matric2 > 0 || $inscr2 > 0 || $certter > 0) {
      if(strtolower($oParag->oParag->db02_descr) == "inicial_p51"){
        continue;
      }
    } else if ($matric1 > 0 || $inscr1 > 0 || $certdiv > 0) {
      if(strtolower($oParag->oParag->db02_descr) == "inicial_p5m"){
        continue;
      }
    }

    if(strtolower($oParag->oParag->db02_descr) == "inicial_p8"){

      $pdf->Ln($pula);
      $pdf->SetFont('Arial', 'B', $tamanho);
      $pdf->Cell(35, 5, "", 0, 0, "L", 0);
      if ($v04_peticaoinicial <> 2) {
        $pdf->MultiCell(0, 5, "ANTE O EXPOSTO, REQUER:", 0, "J", 0,35);
      }
      $pdf->SetFont('Arial', '', $tamanho);
      $pdf->Ln($pula);

    }

    if ($v04_peticaoinicial == 2) {
      $pdf->SetX(25);
    } else {
      $pdf->SetX(35);
    }

    if($oParag->oParag->db02_descr == "inicial_p2"){
      if ($v04_peticaoinicial == 2) {
        $pdf->SetX(25);
      } else {
        $pdf->SetX(80);
      }
    }

    if($oParag->oParag->db02_descr == "ass_adv1" || $oParag->oParag->db02_descr == "ass_adv2" || $oParag->oParag->db02_descr == "ASSINATURAS_CODIGOPHP"){
      continue;
    }

    $oParag->writeText( $pdf );

  }

  foreach ($aParagrafos as $oParag) {

    if($oParag->oParag->db02_descr == "ass_adv1"){
      $pdf->SetX(30);
      $oParag->writeText( $pdf );
    }

    if($oParag->oParag->db02_descr == "ass_adv2"){
      $pdf->SetY(($pdf->getY()-10));
      $pdf->SetX(130);
      $oParag->writeText( $pdf );
    }

    if($oParag->oParag->db02_descr == "ASSINATURAS_CODIGOPHP"){
	    eval(trim($oParag->oParag->db02_texto));
    }

  }
}

if ($lTemInicial){
  if (!defined('DB_BIBLIOT')){
    $pdf->Output('', true);
  }
}else{

  $sMsg = _M('tributario.juridico.div2_inicial_002.inicial_nao_encontrada');
  db_redireciona("db_erros.php?fechar=true&db_erro={$sMsg}");
}
