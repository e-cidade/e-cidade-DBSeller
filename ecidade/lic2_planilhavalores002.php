<?
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

require_once  modification("fpdf151/scpdf.php");
require_once  modification("fpdf151/impcarne.php");
require_once  modification("libs/db_sql.php");
require_once  modification("libs/db_utils.php");
require_once  modification("libs/db_libdocumento.php");
require_once  modification("classes/db_pcparam_classe.php");
require_once  modification("classes/db_cgm_classe.php");
require_once  modification("classes/db_cgm_classe.php");
require_once  modification("classes/db_liclicita_classe.php");
require_once  modification("classes/db_liclicitem_classe.php");

$clcgm             = new cl_cgm;
$clpcparam         = new cl_pcparam;
$clliclicita       = new cl_liclicita;
$clliclicitem      = new cl_liclicitem;

$sqlpref           = "select * from db_config where codigo = ".db_getsession("DB_instit");
$resultpref        = db_query($sqlpref);
db_fieldsmemory($resultpref,0);
$rsParam           = $clpcparam->sql_record($clpcparam->sql_query(db_getsession("DB_instit"),"*"));
$oParam            = db_utils::fieldsMemory($rsParam,0);
parse_str($HTTP_SERVER_VARS['QUERY_STRING']);

$sCampos    = "distinct db_config.*,l20_numero, l20_anousu,l20_dataaber,l20_horaaber,pc11_numero,pc80_codproc,(CASE WHEN l20_procadmin = ' ' THEN p58_numero||'/'||p58_ano WHEN l20_procadmin IS NULL THEN p58_numero||'/'||p58_ano ELSE l20_procadmin END) AS l20_procadmin,l20_prazoentrega,l20_localentrega";
$sSqlLicita = sql_query_julgamento_licitacao($l20_codigo,$sCampos);
$rsLicita   = db_query($sSqlLicita);
db_fieldsmemory($rsLicita,0);
$pdf = new scpdf();
$pdf->Open();
$pdf1 = new db_impcarne($pdf, 1333);
$pdf1->objpdf->SetTextColor(0,0,0);

for ($iProcs=0; $iProcs < pg_num_rows($rsLicita); $iProcs++) { 

  db_fieldsmemory($rsLicita,$iProcs);
  $sSqlItens     = $clliclicitem->sql_query_sol(null,"*","pc11_seq"," l21_codliclicita = {$l20_codigo} and pc80_codproc = {$pc80_codproc}", "pc03_codgrupo");
  $result_itens  = db_query($sSqlItens);
  $numrows_itens = pg_num_rows($result_itens); 

  $pdf1->labdados    = "PROCESSO DE COMPRAS N";
  $pdf1->labtitulo   = "Proc. compras";
  $pdf1->prefeitura  = @$nomeinst;
  $pdf1->enderpref   = trim(@$ender).",".@$numero;
  $pdf1->municpref   = @$munic;
  $pdf1->telefpref   = @$telef;
  $pdf1->logo        = @$logo;
  $pdf1->emailpref   = @$email;
  $pdf1->cgcpref     = @$cgc;
  $pdf1->faxpref     = @$fax;
  $pdf1->orccodigo   = @$pc20_codorc;
  $pdf1->orcdtlim    = db_formatar(@$pc20_dtate,"d");
  $pdf1->orchrlim    = @$pc20_hrate;
  $pdf1->orcobs      = @$pc20_obs;
  $pdf1->logo        = $logo;
  $pdf1->pregao      = $l20_numero."/".$l20_anousu;
  $pdf1->data        = $l20_dataaber;
  $pdf1->hora        = $l20_horaaber;
  $pdf1->solicita    = $pc11_numero;
  $pdf1->pcproc      = $pc80_codproc;
  $pdf1->processo    = $l20_procadmin;
  $pdf1->prazo_entrega    = $l20_prazoentrega;
  $pdf1->local_entrega = $l20_localentrega;


  $pdf1->orcprazo    = $pc20_prazoentrega." dias";
  if (empty($pc20_prazoentrega) || $pc20_prazoentrega == 0) {
    $pdf1->orcprazo    = "";
  }

  $pdf1->orcvalidade = $pc20_validadeorcamento." dias";
  if (empty($pc20_validadeorcamento) || $pc20_validadeorcamento == 0) {
    $pdf1->orcvalidade    = "";
  }
  $pdf1->coddepto    = $coddepto;
  $pdf1->validademinima = $pc01_validademinima;

  $cotacaoprevia     = "Não";
  if ($pc20_cotacaoprevia == 1) {
    $cotacaoprevia = "Sim";
  }

  $pdf1->orccotacao  = $cotacaoprevia;

  if(isset($z01_cep) && $z01_cep!=""){
    $ah = substr(@$z01_cep,0,5);
    $dh = substr(@$z01_cep,5,3);
    $z01_cep = $ah.'-'.$dh;
  }

  $pdf1->fonedepto = @$fonedepto;
  $pdf1->ramaldepto = @$ramaldepto;
  $pdf1->faxdepto = @$faxdepto;
  $pdf1->emaildepto = @$emaildepto;

  if(isset($imprimirbranco)){
  	$numrows_pcorcamforne = 1;
    $z01_nome = "";
    $z01_numcgm = "";
    $z01_cgccpf = "";
    $z01_ender = "";
    $z01_compl = "";
    $z01_munic = "";
    $z01_uf = "";
    $z01_fax = "";
    $z01_contato = "";
    $z01_cep = "";
    $z01_telef = "";
  }

  $pdf1->nome       = @$z01_nome;
  $pdf1->numcgm     = @$z01_numcgm;
  $pdf1->cnpj       = @$z01_cgccpf;
  $pdf1->ender      = @$z01_ender;
  $pdf1->compl      = @$z01_compl;
  $pdf1->munic      = @$z01_munic;
  $pdf1->uf         = @$z01_uf;
  $pdf1->fax        = @$z01_fax;
  $pdf1->contato    = @$z01_contato;
  $pdf1->cep        = @$z01_cep;
  $pdf1->telefone   = @$z01_telef;

  $pdf1->Scoddepto   = "coddepto";
  $pdf1->Sdescrdepto = "descrdepto";
  $pdf1->Snumdepart  = "numdepart";
  $pdf1->recorddosdepart = @$result_departs;
  $pdf1->linhasdosdepart = @$numrows_departs;

  $pdf1->Snumero= @$pc80_codproc;
  $pdf1->Sdepart= @$descrdepto;
  $pdf1->Sdata  = @$pc80_data;

  $pdf1->Sresumo= @$pc80_resumo;
  $pdf1->telefpref  = @$telef;
  $pdf1->emailpref  = @$email;
  $pdf1->cgcpref    = @$cgc;
  $pdf1->faxpref    = @$fax;

  $pdf1->recorddositens = @$result_itens;
  $pdf1->linhasdositens = @$numrows_itens;
  $pdf1->item	          = 'pc81_codprocitem';
  $pdf1->sitem          = 'pc01_codmater';
  $pdf1->quantitem      = 'pc11_quant';
  $pdf1->descricaoitem  = 'pc01_descrmater';
  $pdf1->sresum         = 'pc11_resum';
  $pdf1->sprazo         = 'pc11_prazo';
  $pdf1->spgto          = 'pc11_pgto';
  $pdf1->sunidade       = 'm61_descr';
  $pdf1->scodunid       = 'pc17_codigo';
  $pdf1->sservico       = 'pc01_servico';
  $pdf1->squantunid     = 'pc17_quant';
  $pdf1->susaquant      = 'm61_usaquant';
  $pdf1->vlrmedia       = 'pc11_vlrun';

  $pdf1->anexo2         = true ;  

  $pdf1->imprime();
}
if(isset($argv[1])){
  $pdf1->objpdf->Output("/tmp/teste.pdf");
}else{
  $pdf1->objpdf->Output();
}
?>


<?
function sql_query_julgamento_licitacao ( $l20_codigo=null,$campos="*",$ordem=null,$dbwhere=""){

  $sql = "select ";
  if($campos != "*" ){

    $campos_sql = split("#",$campos);
    $virgula = "";

    for($i=0;$i<sizeof($campos_sql);$i++){
      $sql .= $virgula.$campos_sql[$i];
      $virgula = ",";
    }
  }else{
    $sql .= $campos;
  }

  $sql .= " from liclicita ";
  $sql .= "      inner join liclicitem               on liclicitem.l21_codliclicita         = liclicita.l20_codigo              ";
  $sql .= "      inner join pcprocitem               on pcprocitem.pc81_codprocitem         = liclicitem.l21_codpcprocitem      ";
  $sql .= "      inner join pcproc                   on pcproc.pc80_codproc                 = pcprocitem.pc81_codproc           ";
  $sql .= "      inner join solicitem                on solicitem.pc11_codigo               = pcprocitem.pc81_solicitem         ";
  $sql .= "      inner join solicita                 on solicita.pc10_numero                = solicitem.pc11_numero             ";
  $sql .= "      inner join db_config                 on solicita.pc10_instit                = db_config.codigo             ";
  $sql .= "      inner join solicitempcmater         on solicitempcmater.pc16_solicitem     = solicitem.pc11_codigo             ";
  $sql .= "      inner join pcmater                  on pcmater.pc01_codmater               = solicitempcmater.pc16_codmater    ";
  $sql .= "      left  join liclicitaproc on liclicitaproc.l34_liclicita = liclicita.l20_codigo";
  $sql .= "      left  join protprocesso  on protprocesso.p58_codproc = liclicitaproc.l34_protprocesso";
  
  $sql2 = "";
  if ($dbwhere == "") {

    if ($l20_codigo != null ) {
      $sql2 .= " where liclicita.l20_codigo = $l20_codigo ";
    }
  } else if ($dbwhere != "") {
    $sql2 = " where $dbwhere";
  }
  $sql .= $sql2;
  if ($ordem != null ) {

    $sql .= " order by ";
    $campos_sql = split("#",$ordem);
    $virgula = "";
    for ($i = 0; $i < sizeof($campos_sql); $i++) {
      $sql .= $virgula.$campos_sql[$i];
      $virgula = ",";
    }
  }
  return $sql;

}


?>
