<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBseller Servicos de Informatica
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
require_once(modification("classes/db_propri_classe.php"));
require_once(modification("classes/db_certid_classe.php"));
require_once(modification("classes/db_cfiptu_classe.php"));

$clissbase     = new cl_issbase;
$clcgm         = new cl_cgm;
$clsocios      = new cl_socios;
$cliptubase    = new cl_iptubase;
$clpropri      = new cl_propri;
$clpromitente  = new cl_promitente;
$clpardiv      = new cl_pardiv;
$clparjuridico = new cl_parjuridico;
$oLibDocumento = new libdocumento(1203);
$clcfiptu      = new cl_cfiptu;
/*
echo "<pre>";
var_dump($_REQUEST);
die;*/
$numeropg = isset($numeropg)?$numeropg:0;

db_sel_instit(null, "db21_usasisagua");

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);

$exercicio = db_getsession("DB_anousu");
$borda        = 1;
$bordat       = 1;
$preenc       = 0;
$TPagina      = 57;
$dbwhere      = ' 1=1 ';
$tamanho      = isset($tamanho)?$tamanho:10;
$pula         = 2;

$sExpFalecido = "";

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
  $sqlini  = " select distinct                                            ";
  $sqlini .= "        inicial.v50_inicial as inicialproc,                 ";
  $sqlini .= "        v57_oab as oab_advogado,                            ";
  $sqlini .= "        z01_nome as nome_advogado,                          ";
  $sqlini .= "        inicial.v50_data                                    ";
  $sqlini .= "   from inicial                                             ";
  $sqlini .= "        inner join juridico.advog on v50_advog = v57_numcgm ";
  $sqlini .= "        inner join protocolo.cgm on v57_numcgm = z01_numcgm ";
  $sqlini .= "        left join inicialnomes on v58_inicial = v50_inicial ";
  $sqlini .= "  where $dbwhere                                            ";
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

class PDF_INICIAL_CDA extends pdf3 {
 
  var $B;
  var $I;
  var $U;
  var $HREF;
  function PDF2($orientation='P',$unit='mm',$format='A4')
{
  //Call parent constructor
  $this->FPDF($orientation,$unit,$format);
  //Initialization
  $this->B=0;
  $this->I=0;
  $this->U=0;
  $this->HREF='';
}

function WriteHTML($html)
{   //HTML parser
    $html=strip_tags($html,"<b><u><i><a><img><p><br><strong><em><font><tr><blockquote>"); //supprime tous les tags sauf ceux reconnus
    $html=str_replace("\n",' ',$html); //remplace retour à la ligne par un espace
    $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE); //éclate la chaîne avec les balises
    foreach($a as $i=>$e)
    {
        if($i%2==0)
        {
            //Text
            if($this->HREF)
                $this->PutLink($this->HREF,$e);
            else
                $this->Write(5,stripslashes($this->txtentities($e)));
        }
        else
        {
            //Tag
            if($e[0]=='/')
                $this->CloseTag(strtoupper(substr($e,1)));
            else
            {
                //Extract attributes
                $a2=explode(' ',$e);
                $tag=strtoupper(array_shift($a2));
                $attr=array();
                foreach($a2 as $v)
                {
                    if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                        $attr[strtoupper($a3[1])]=$a3[2];
                }
                $this->OpenTag($tag,$attr);
            }
        }
    }
  }

function txtentities($html){
    $trans = get_html_translation_table(HTML_ENTITIES);
    $trans = array_flip($trans);
    return strtr($html, $trans);
}

function OpenTag($tag,$prop)
{
  //Opening tag
  if($tag=='B' or $tag=='I' or $tag=='U')
    $this->SetStyle($tag,true);
  if($tag=='A')
    $this->HREF=$prop['HREF'];
  if($tag=='BR')
    $this->Ln(5);
}

function CloseTag($tag)
{
  //Closing tag
  if($tag=='B' or $tag=='I' or $tag=='U')
    $this->SetStyle($tag,false);
  if($tag=='A')
    $this->HREF='';
}

function SetStyle($tag,$enable)
{
  //Modify style and select corresponding font
  $this->$tag+=($enable ? 1 : -1);
  $style='';
  foreach(array('B','I','U') as $s)
    if($this->$s>0)
      $style.=$s;
  $this->SetFont('',$style);
}

function PutLink($URL,$txt)
{
  //Put a hyperlink
  $this->SetTextColor(0,0,255);
  $this->SetStyle('U',true);
  $this->Write(5,$txt,$URL);
  $this->SetStyle('U',false);
  $this->SetTextColor(0);
}

  function Footer() {
    $S = $this->lMargin;
    $this->SetLeftMargin(10);
    global $url;
    //Position at 1.5 cm from bottom

    $this->SetFont('Arial','',5);
    $this->text(10,289,'Base: '.@$GLOBALS["DB_NBASE"]);
    $this->SetFont('Arial','I',6);
    $this->SetY(-10);
    $nome = @$GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"];
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
    
     $this->SetXY(200,-10);
        // Select Arial italic 8
    $this->SetFont('Arial','',6);
        // Print centered page number

        $this->Cell(0,10,'Pág '.$this->PageNo().'/{nb}',0,0,'C');
  }
  function Header() {

    $sql = "select nomeinst,
                   bairro,
                   cgc,
                   trim(ender)||','||trim(cast(numero as text)) as ender,
                   upper(munic) as munic,
                   uf,
                   telef,
                   email,
                   url,
                   logo,
                   db12_extenso
              from db_config
                   inner join db_uf on db12_uf = uf
             where codigo = ".db_getsession("DB_instit");

    $result = db_query($sql);

    global $nomeinst;
    global $ender;
    global $munic;
    global $cgc;
    global $bairro;
    global $uf;
    global $db12_extenso;
    global $logo;
    global $lImpFolha;

    db_fieldsmemory($result,0);
    $db12_extenso = pg_result($result,0,"db12_extenso");

    $S      = $this->lMargin;
    $this->SetLeftMargin(10);
    $posini = 20;
    $Letra  = 'Times';

    $this->Image('imagens/files/'.$logo,$posini,8,24);
    $this->Ln(5);
    $this->SetFont($Letra,'',10);
    $this->MultiCell(0,4,$db12_extenso,0,"C",0);
    $this->SetFont($Letra,'B',13);
    $this->MultiCell(0,6,$nomeinst,0,"C",0);
    $this->SetFont($Letra,'B',12);
    $this->MultiCell(0,4,@$GLOBALS["head1"],0,"C",0);
    $this->SetLeftMargin($S);
    $this->Ln(1);

    $comprim = ($this->w - $this->rMargin - $this->lMargin);
    $Espaco  = $this->w - 80 ;

    if ($lImpFolha){

      $this->SetFont('Arial','',7);
      $this->SetFillColor(240);
      $this->RoundedRect(160,10, 40,20,2,"FD",'1234');
      $this->setfont('arial','',8);
      $this->Text(163,15,"Livro");
      $this->Text(172,15,":".@$GLOBALS["head2"]);
      $this->Text(163,18,"Folha" );
      $this->Text(172,18,":". @$GLOBALS["head3"]);
      $this->Text(163,21,"Data do Livro");
      $this->Text(180,21,":".db_formatar(@$GLOBALS["head4"],"d"));
    }

    if (!empty($GLOBALS["head5"])) {
      $this->SetY(35);
      $this->setfont('arial','b',11);
      $this->multicell(0,4,"CERTIDÃO DE DÍVIDA ATIVA N".CHR(176)." ".$GLOBALS["head5"],0,"C",0,0);
      $this->setfont('arial','',11);
      $this->ln(3);
    }
  }
}

$diretorio = "tmp/inicial_cda" . Date("dmYHis") . "/";
if (!file_exists($diretorio)) {
  mkdir($diretorio, 0777);
}

for ($xyx = 0; $xyx < $iLinhasIniciais; $xyx++) {
  
  db_fieldsmemory($resultini, $xyx);
  $pdf = new PDF_INICIAL_CDA();
  $head5 = "";
  if (!defined('DB_BIBLIOT')) {
  
    $pdf->Open(); // abre o relatorio
    $pdf->AliasNbPages(); // gera alias para as paginas
  }

  $pdf->SetAutoPageBreak('on', 10);  
  
  $lTemInicial = false;
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

  $aOrigemInicial   = db_utils::getCollectionByRecord($rsOrigemInicial);
  $arrayInscr = array();
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

  foreach ($aOrigemInicial as $oOrigemInicial) {
    if ( trim($oOrigemInicial->certdiv) != "" ) {
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
  
      if ( pg_num_rows($rsDadosInicial) > 0 ) {
        db_fieldsmemory($rsDadosInicial, 0);
      }

    } else if ( trim($oOrigemInicial->certter) != "" ) {
      $sSqlDadosTermo = " select coalesce(k00_matric,0) as matric2,
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
                           where certter.v14_certid = {$oOrigemInicial->certter} ";
      $rsDadosInicial = db_query($sSqlDadosTermo);

      if ( pg_num_rows($rsDadosInicial) > 0 ) {
        db_fieldsmemory($rsDadosInicial,0);
      } else {
        $sSqlReparc = " select coalesce(k00_matric,0) as matric2,
                               coalesce(k00_inscr,0)  as inscr2,
                               coalesce(k00_contr,0)  as contr2,
                               proced.v03_dcomp,
                               divida.v01_exerc
                          from fc_origemparcelamento( ( select v07_numpre
                                                          from termo
                                                         where v07_parcel = {$oOrigemInicial->parcel} ) ) as x
                               inner join termoreparc on termoreparc.v08_parcel = x.riparcel
                               inner join termodiv    on termodiv.parcel        = termoreparc.v08_parcelorigem
                               inner join divida      on divida.v01_coddiv      = termodiv.coddiv
                               inner join proced      on proced.v03_codigo      = divida.v01_proced
                               left  join arrematric  on arrematric.k00_numpre  = divida.v01_numpre
                               left  join arreinscr   on arreinscr.k00_numpre   = divida.v01_numpre
                               left  join arrecontr   on arrecontr.k00_numpre   = divida.v01_numpre
                         order by riseq desc limit 1 ";
        $rsDadosInicial = db_query($sSqlReparc);
        if ( pg_num_rows($rsDadosInicial) > 0 ) {
          db_fieldsmemory($rsDadosInicial,0);
        }
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

  asort($aListaExercicio);

  $oOrigemInicial   = db_utils::fieldsMemory($rsOrigemInicial,0);
  db_fieldsmemory($rsOrigemInicial,0);

  $sNomeMatricula = "";

  if ( trim($oOrigemInicial->certdiv) != "" ) {

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

    if ( pg_num_rows($rsDadosInicial) > 0 ) {

      db_fieldsmemory($rsDadosInicial, 0);

      $matric2 = 0;
      $inscr2  = 0;
      $contr2  = 0;
      $lDivida = true;

    } else {
      continue;
    }


  } else if ( trim($oOrigemInicial->certter) != "" ) {

    $sNomeMatricula = "matric2";

    $sSqlDadosTermo = " select coalesce(k00_matric,0) as matric2,
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
                         where certter.v14_certid = {$oOrigemInicial->certter} ";

    $rsDadosInicial = db_query($sSqlDadosTermo);

    if ( pg_num_rows($rsDadosInicial) > 0 ) {

      db_fieldsmemory($rsDadosInicial,0);

    } else {

      $sSqlReparc = " select coalesce(k00_matric,0) as matric2,
                             coalesce(k00_inscr,0)  as inscr2,
                             coalesce(k00_contr,0)  as contr2,
                             proced.v03_dcomp,
                             divida.v01_exerc
                        from fc_origemparcelamento( ( select v07_numpre
                                                        from termo
                                                       where v07_parcel = {$oOrigemInicial->parcel} ) ) as x
                             inner join termoreparc on termoreparc.v08_parcel = x.riparcel
                             inner join termodiv    on termodiv.parcel        = termoreparc.v08_parcelorigem
                             inner join divida      on divida.v01_coddiv      = termodiv.coddiv
                             inner join proced      on proced.v03_codigo      = divida.v01_proced
                             left  join arrematric  on arrematric.k00_numpre  = divida.v01_numpre
                             left  join arreinscr   on arreinscr.k00_numpre   = divida.v01_numpre
                             left  join arrecontr   on arrecontr.k00_numpre   = divida.v01_numpre
                       order by riseq desc limit 1 ";

      $rsDadosInicial = db_query($sSqlReparc);

      if ( pg_num_rows($rsDadosInicial) > 0 ) {
        db_fieldsmemory($rsDadosInicial,0);
      } else {
        continue;
      }

    }

    $matric1       = 0;
    $inscr1        = 0;
    $contr1        = 0;
    $lParcelamento = true;

  } else {
    continue;
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

  $iLinhaDadosInicial = pg_num_rows($rsDadosInicial);

  $aListaProcedencia = array();

  for ($iInd=0; $iInd < $iLinhaDadosInicial; $iInd++ ) {

    $oDadosInicial = db_utils::fieldsMemory($rsDadosInicial,$iInd);

    if (!in_array($oDadosInicial->v01_exerc, $aListaExercicio)) {
      $aListaExercicio[]   = $oDadosInicial->v01_exerc;
    }
    if (!in_array($oDadosInicial->v03_dcomp, $aListaProcedencia)) {
      $aListaProcedencia[] = $oDadosInicial->v03_dcomp;
    }
  }

  $oLibDocumento->Exercicio    = "Exercício(s) de ".implode(",",$aListaExercicio);
  $oLibDocumento->Procedencias = "Procedência(s) ".ucfirst(strtolower(implode(",",$aListaProcedencia)));


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
                                 inner join divida   on certdiv.v14_coddiv = divida.v01_coddiv
                                 inner join arrecad  on arrecad.k00_numpre = divida.v01_numpre
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
        $ximovel .= $v.trim(pg_result($result, $jk, "j14_tipo")) . ' ' . trim(pg_result($result,$jk , "j14_nome")) . ', ' . trim(pg_result($result, $jk, "j39_numero")) . ' setor/quadra/lote ' . trim(pg_result($result, $jk, "j34_setor")) . "/" . trim(pg_result($result, $jk, "j34_quadra")) . "/" . trim(pg_result($result, $jk, "j34_lote")) . ( $oCfiptu->j18_utilizaloc == 't'? ' - dados de localização: ' . trim(pg_result($result, $jk, "j06_setorloc")) . '-' . trim(pg_result($result, $jk, "j05_descr")) . '/' . trim(pg_result($result, $jk, "j06_quadraloc")) . '/' . trim(pg_result($result, $jk, "j06_lote")) :"") . ', em ' . $munic . '/' . $uf . ' (matrícula municipal n' . chr(176) . ' ' . trim(pg_result($result,$jk,"j01_matric")) . ')';
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

        $rsDadosEnvol = $clcgm->sql_record($clcgm->sql_query_file($oEnvolvidos->rinumcgm));

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
            }else{
              $sNacionalidade = ", ESTRANGEIRA(O)";
            }
          }

          $xender .=  $sExpFalecido . trim($oDadosEnvol->z01_nome) . $sNacionalidade . ($oDadosEnvol->z01_cgccpf!=''?", {$sCgcCpf}":"");
          $xender .= " ENDEREÇO: ".$oDadosEnvol->z01_ender.', N°'.$oDadosEnvol->z01_numero.''.($oDadosEnvol->z01_bairro!=""?", BAIRRO: {$oDadosEnvol->z01_bairro}":"").', '.$oDadosEnvol->z01_munic.'-'.$oDadosEnvol->z01_uf.''.($oDadosEnvol->z01_cep !=""?", CEP: {$oDadosEnvol->z01_cep}":"").''.($oDadosEnvol->z01_cxpostal!=""?", CAIXA POSTAL: {$oDadosEnvol->z01_cxpostal}":"").". ";
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
          $sCgcCpf = "CNPJ: ".db_formatar($oDadosEnvol->z01_cgccpf, 'cnpj').",";
        } else {
          $sCgcCpf = "CPF: ".db_formatar($oDadosEnvol->z01_cgccpf, 'cpf').",";
        }

        if ($oEnvolvidos->ritipoenvol == "4") {
          $xender  = $sExpFalecido.trim($oDadosEnvol->z01_nome).",  " .$sCgcCpf.' sito o endereço: '.$oDadosEnvol->z01_ender.', N° '.$oDadosEnvol->z01_numero.''.($oDadosEnvol->z01_bairro != "" ? ", BAIRRO:{$oDadosEnvol->z01_bairro}" : "").', '.$oDadosEnvol->z01_munic.'-'.$oDadosEnvol->z01_uf.''.($oDadosEnvol->z01_cep != "" ? ", CEP:{$oDadosEnvol->z01_cep}" : "").''.($oDadosEnvol->z01_cxpostal != "" ? ", CAIXA POSTAL:{$oDadosEnvol->z01_cxpostal}" : "");
        } else {

          $xender .= $sTextoSocios;
          $xender .= "\n-  ".$sExpFalecido.trim($oDadosEnvol->z01_nome).", " .$sCgcCpf."\n";
          $xender .= "ENDEREÇO: ".$oDadosEnvol->z01_ender.', N°: '.$oDadosEnvol->z01_numero.''.($oDadosEnvol->z01_bairro != "" ? ", BAIRRO: {$oDadosEnvol->z01_bairro}" : "").', '.$oDadosEnvol->z01_munic.'-'.$oDadosEnvol->z01_uf.''.($oDadosEnvol->z01_cep != "" ? ", CEP:{$oDadosEnvol->z01_cep}" : "").''.($oDadosEnvol->z01_cxpostal != "" ? ", CAIXA POSTAL:{$oDadosEnvol->z01_cxpostal}" : "");
          $sTextoSocios = "";
        }
      }
    }

    $inscricao_matricula_cgm = "INSCRICAO: $xinscr";

  } else {

    $sql = "select munic, uf, numero as numeroinst from db_config where codigo = " . db_getsession("DB_instit");
    $result = db_query($sql);
    db_fieldsmemory($result, 0);

    if ($certdiv > 0) {
      $sSql  = " select distinct cgm.*                                     ";
      $sSql .= "   from certdiv                                            ";
      $sSql .= "        inner join divida     on v14_coddiv = v01_coddiv   ";
      $sSql .= "        inner join arrenumcgm   on v01_numpre = k00_numpre ";
      $sSql .= "        inner join cgm          on z01_numcgm = k00_numcgm ";
      $sSql .= "  where v14_certid = $certdiv                              ";
    } else {
      $sSql  = " select distinct cgm.*                                     ";
      $sSql .= "   from certter                                            ";
      $sSql .= "        inner join termo        on v14_parcel = v07_parcel ";
      $sSql .= "        inner join arrenumcgm   on v07_numpre = k00_numpre ";
      $sSql .= "        inner join cgm          on z01_numcgm = k00_numcgm ";
      $sSql .= "  where v14_certid = $certter                              ";
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
        $sCgcCpf = "CNPJ: ".db_formatar($z01_cgccpf, 'cnpj').",";
      } else {
        $sCgcCpf = "CPF: ".db_formatar($z01_cgccpf, 'cpf').",";
      }

      $xender .= " CGM: " . $z01_numcgm . " - " . $sExpFalecido . trim($z01_nome) . (strlen(trim($z01_cgccpf)) == 11 ? ($z01_nacion == 1 ? ", BRASILEIRA" : ", ESTRANGEIRA") : "") . ($z01_cgccpf != '' ? ", {$sCgcCpf}" : "");
      $xender .= " ENDEREÇO: " . $z01_ender . ($z01_numero > 0 ? ', N°' . $z01_numero : '') . '' . ($z01_bairro != "" ? ", BAIRRO: $z01_bairro" : "") . ', ' . $z01_munic . '-' . $z01_uf . '' . ($z01_cep != "" ? ", CEP: $z01_cep" : "") . '' . ($z01_cxpostal != "" ? ", CAIXA POSTAL: $z01_cxpostal" : "");
    }

    $inscricao_matricula_cgm = "CGM: $z01_numcgm";

  }

  $pdf->AddPage(); // adiciona uma pagina
  $pdf->SetTextColor(0, 0, 0);
  $pdf->SetFillColor(220);
  $yy = $pdf->h - 11;
  $pdf->SetFont('Arial', '', $tamanho);

  $cgc      = db_formatar($cgc, 'cnpj');
  $Valor    = db_formatar($ValorTotal, 'f');
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
  $oLibDocumento->munic         = $munic;
  $oLibDocumento->ender         = $ender;
  $oLibDocumento->xender        = $xender;
  $oLibDocumento->Valor         = trim($Valor);
  $oLibDocumento->Valorext      = trim($Valorext);
  $oLibDocumento->Certidao      = $Certidao;
  $oLibDocumento->xinscr        = $inscr;
  $oLibDocumento->ximovel       = $ximovel;
  $oLibDocumento->xmatric       = $xmatric;
  $oLibDocumento->processoforo  = $oOrigemInicial->v70_codforo;
  $oLibDocumento->numeroinicial = $oOrigemInicial->v50_inicial;
  $oLibDocumento->dataporextenso = date('d')." de ".db_mes(date('m'))." de ".date('Y');
  $oLibDocumento->oab_advogado   = $oab_advogado;
  $oLibDocumento->nome_advogado  = $nome_advogado;
  $oLibDocumento->vara_descricao = $vara_descricao;
  $oLibDocumento->local_descricao = $local_descricao;
  $oLibDocumento->inscricao_matricula_cgm = $inscricao_matricula_cgm;
  if( $xmatric > 0 ){
        $sSqlIptuEnder = "select * from iptuender where j43_matric =  ".$xmatric;
        $rsIptuEnder = db_query( $sSqlIptuEnder );
        db_fieldsmemory( $rsIptuEnder , 0);
        $oLibDocumento->j43_matric      = $j43_matric   ;
        $oLibDocumento->j43_dest        = $j43_dest     ;
        $oLibDocumento->j43_ender       = $j43_ender    ;
        $oLibDocumento->j43_numimo      = $j43_numimo   ;
        $oLibDocumento->j43_comple      = $j43_comple   ;
        $oLibDocumento->j43_bairro      = $j43_bairro   ;
        $oLibDocumento->j43_munic       = $j43_munic    ;
        $oLibDocumento->j43_uf          = $j43_uf       ;
        $oLibDocumento->j43_cep         = $j43_cep      ;
        $oLibDocumento->j43_cxpost      = $j43_cxpost   ;
        $oLibDocumento->j43_codbairro   = $j43_codbairro;
        $oLibDocumento->j43_codigo      = $j43_codigo   ;
        $oLibDocumento->z01_cgccpf      = $z01_cgccpf   ;
        $oLibDocumento->z01_numcgm      = $z01_numcgm   ;
  }
  $oLibDocumento->z01_cgccpf     = $oDadosEnvol->z01_cgccpf;
  $oLibDocumento->sNomeMatricula = $oDadosEnvol->z01_nome;
  $oLibDocumento->sNomeCgm     	 = $z01_nome; 
  $oLibDocumento->sNomeCpf       = $z01_cgccpf;
 
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
  
  $pdf->SetFont('Arial', '', 10);
  $pdf->Text(10, $yy, 'Controle Administrativo nº ' . $xinicial);

  $tipo = 2;
  $certid = $v14_certid;
  $certid1 = $v14_certid;
  $reemissao = $DBtxt14;
  $ordenarpor = $DBtxt16;
  $totexe = $totexe;
  $endamimp = $endaimp;
   
  /**
    * Rotina  em processo de refatoração
    */
    try{
        
      $sqlCertidoes = "
        SELECT
            DISTINCT certdiv.v14_certid,
            v13_dtemis,
            2 as tipo
        FROM
            certdiv
            INNER JOIN divida ON v01_coddiv = certdiv.v14_coddiv
            AND v01_instit = ".db_getsession("DB_instit")."
            INNER JOIN certid ON v13_certid = certdiv.v14_certid
            LEFT JOIN cgm ON v01_numcgm = z01_numcgm
        WHERE
          certdiv.v14_certid in(
              select
                  v51_certidao
              from
                  inicial
                  inner join inicialcert ON v51_inicial = v50_inicial
              where
                  v50_inicial = {$oOrigemInicial->v50_inicial}
          )
        UNION
        SELECT
            DISTINCT certter.v14_certid,
            v13_dtemis,
            1 as tipo
        FROM
            certter
            INNER JOIN certid ON v13_certid = certter.v14_certid
        WHERE
          certter.v14_certid in(
            select
                v51_certidao
            from
                inicial
                inner join inicialcert ON v51_inicial = v50_inicial
            where
                v50_inicial = {$oOrigemInicial->v50_inicial}
          )
      ";

        $resultCertidoes = db_query($sqlCertidoes);
// dd(pg_fetch_all($resultCertidoes));
        while ($certidoes = pg_fetch_object($resultCertidoes)) {
            $oGeradorCda = new GeradorCDA($pdf);
            $oGeradorCda->gerar($certidoes->tipo,$certidoes->v14_certid,$certidoes->v14_certid, $reemissao, $ordenarpor, $totexe, $endaimp);
        }            

    } catch (Exception $eErro) {
        db_redireciona("db_erros.php?fechar=true&db_erro={$eErro->getMessage()}");
    }
    
    $arquivo = "inicial_cda_{$oOrigemInicial->v50_inicial}.pdf";
    $pdf->Output($diretorio.$arquivo,false,true);
    unset($pdf);
}

try {
    makeZip($diretorio);
    downloadZip($diretorio);

} catch (Exception $eErro) {
    db_redireciona("db_erros.php?fechar=true&db_erro={$eErro->getMessage()}");
}



function downloadZip($diretorio)
{
  
    $arquivoZip = substr_replace($diretorio, "", -1) . ".zip";
    if (file_exists($arquivoZip)) {
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . basename($arquivoZip) . '"');
        header('Content-Length: ' . filesize($arquivoZip));

        flush();
        readfile($arquivoZip);
        unlink($diretorio);
    } else {
        throw new \Exception("Erro ao efetuar download");
    }

}

function makeZip($diretorio)
{
    $zip = new ZipArchive();
    $nomeZip = substr_replace($diretorio, "", -1);
    if ($zip->open("{$nomeZip}.zip", ZipArchive::CREATE) === TRUE) {
        if ($handle = opendir($diretorio)) {
            // Add all files inside the directory
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != ".." && !is_dir($diretorio . $entry)) {
                    $zip->addFile($diretorio . $entry);
                }
            }
            closedir($handle);
        }

        $zip->close();
    } else {
        throw new \Exception("Erro ao zipar arquivos");
    }
}
