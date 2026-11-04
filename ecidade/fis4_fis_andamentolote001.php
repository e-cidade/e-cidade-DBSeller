<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *    Copyright (C) 2009 DBSeller Servicos de Informatica
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
require_once  modification("libs/db_stdlib.php");
require_once  modification("libs/db_conecta.php");
require_once  modification("libs/db_sessoes.php");
require_once  modification("libs/db_usuariosonline.php");
require_once  modification("libs/db_utils.php");
require_once  modification("classes/db_db_config_classe.php");
require_once  modification("dbforms/db_funcoes.php");

require_once  modification("classes/db_fis_levantlotearq_classe.php");
require_once  modification("classes/db_fis_levantlotearqcont_classe.php");
require_once  modification("classes/db_fis_levantlotearqparagrafo_classe.php");

//AUTO
include(modification("classes/db_fis_auto_classe.php"));
include(modification("classes/db_fis_autotipo_classe.php"));
include(modification("classes/db_fis_autoandam_classe.php"));
include(modification("classes/db_fis_autoultandam_classe.php"));
include(modification("classes/db_fis_fandam_classe.php"));
include(modification("classes/db_fis_fandamusu_classe.php"));
include(modification("classes/db_fis_autousu_classe.php"));
include(modification("classes/db_fis_autolocal_classe.php"));
include(modification("classes/db_fis_autoexec_classe.php"));
include(modification("classes/db_fis_fiscalparametros_classe.php"));
include(modification("classes/db_fis_processoandam_classe.php"));
require_once(modification('classes/db_fis_datacienciaandamento_classe.php'));

//NOTIFICAÇÃO DE LANÇAMENTO
require_once Modification("classes/db_fis_lancamento_classe.php");
require_once Modification("classes/db_fis_lanctipo_classe.php");
require_once Modification("classes/db_fis_lancandam_classe.php");
require_once Modification("classes/db_fis_lancultandam_classe.php");
require_once Modification("classes/db_fis_lancusu_classe.php");
require_once Modification("classes/db_fis_lanclocal_classe.php");
require_once Modification("classes/db_fis_lancexec_classe.php");
require_once Modification("classes/db_fis_lancnumpre_classe.php");
require_once Modification("classes/db_fis_procfiscallanc_classe.php");

db_postmemory($_POST);

$erro   = false;
$instit = db_getsession("DB_instit");
$clrotulo           = new rotulocampo;

$cllevantlotearq     = new cl_fis_levantlotearq;
$cllevantlotearqcont = new cl_fis_levantlotearqcont;
$cllevantlotearqpar  = new cl_fis_levantlotearqpar;
$cldb_config         = new cl_db_config;

//AUTO
$clauto             = new cl_fis_auto;
$clautotipo         = new cl_fis_autotipo;
$clautoandam        = new cl_fis_autoandam;
$clautoultandam     = new cl_fis_autoultandam;
$clfandam           = new cl_fis_fandam;
$clfandamusu        = new cl_fis_fandamusu;
$clautousu          = new cl_fis_autousu;
$clautolocal        = new cl_fis_autolocal;
$clautoexec         = new cl_fis_autoexec;
$clfiscalparametros = new cl_fis_fiscalparametros;
$clprocessoandam    = new cl_fis_processoandam;
$cldatacienciaandamento = new cl_fis_datacienciaandamento;

//NOTIFICAÇÃO DE LANÇAMENTO
$cllancamento           = new cl_fis_lancamento;
$cllanctipo             = new cl_fis_lanctipo;
$cllancamentoandam      = new cl_fis_lancandam;
$cllancamentoultandam   = new cl_fis_lancultandam;
$cllancusu              = new cl_fis_lancusu;
$cllanclocal            = new cl_fis_lanclocal;
$cllancexec             = new cl_fis_lancexec;
$clprocfiscallanc       = new cl_fis_procfiscallanc;

$db_opcao = 1;
$db_botao = true;
$situacao = 0;

$iInstitSessao = db_getsession("DB_instit");
$result = $cldb_config->sql_record($cldb_config->sql_query_file($iInstitSessao, "cgc"));
db_fieldsmemory($result, 0);

$_debug = false;
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<?php
db_app::load("scripts.js, strings.js, prototype.js, estilos.css");
?>
</head>
<body class="body-default" onLoad="a=1">
<?php
  include modification("forms/db_frm_fis_procandamentolote.php");
  db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit"));
?>
</body>
</html>
<?php
if (isset($processar)) {

    unset($processar);
    $sqlerro   = false;

    foreach ($_POST as $value){
        if(substr($value,0,4) == "chk-"){
            $arrItem[] = substr($value,4);
        }
    }
    if(count($arrItem) == 0){
        $sqlerro = true;
        $erro_msg = "Informe os códigos para processar o andamento";
    }

    db_criatermometro('termometro', 'Concluido...', 'blue', 1);
    flush();
    $data_ciencia = $data_ciencia_ano.'/'.$data_ciencia_mes.'/'.$data_ciencia_dia;
    if($y50_dtvenc != ""){
        $y50_dtvenc_ano."/".$y50_dtvenc_mes."/".$y50_dtvenc_dia;
    }
    if($y50_prazorec != ""){
        $y50_prazorec = $y50_prazorec_ano.'/'.$y50_prazorec_mes.'/'.$y50_prazorec_dia;
    }
    $dataAtual = date('Y-m-d');
    db_inicio_transacao();

    for($bcd = 0; $bcd < count($arrItem); $bcd++){
        db_atutermometro($bcd, count($arrItem), 'termometro');

        if($pecafiscal == 1){ // AUTO DE INFRAÇÃO

            $auto = 1;
            $oDaoAuto       = db_utils::getDao("fis_auto");
            $oDaoArrecad    = db_utils::getDao('arrecad');
            $oDaoArrecant   = db_utils::getDao('arrecant');
            $oDaoAutonumpre = db_utils::getDao("fis_autonumpre");

            $y50_codauto = $arrItem[$bcd];

            $sqlPuginParam = "select * from fiscalizacao.fis_fiscalparametros";
            $rsPluginParam = db_query($sqlPuginParam);
            db_fieldsmemory($rsPluginParam, 0);

            $sSqlGetProcessoAdministrativo  = " select p58_numero || '/' || p58_ano as p58_numero, p58_requer from fiscalizacao.fis_auto ";
            $sSqlGetProcessoAdministrativo .= " inner join fiscalizacao.fis_procfiscalauto on y111_auto = y50_codauto ";
            $sSqlGetProcessoAdministrativo .= " inner join fiscalizacao.fis_procfiscal on y111_procfiscal = y100_sequencial ";
            $sSqlGetProcessoAdministrativo .= " inner join fiscalizacao.fis_procfiscalprot on y105_procfiscal = y100_sequencial ";
            $sSqlGetProcessoAdministrativo .= " inner join protprocesso on y105_protprocesso = p58_codproc ";
            $sSqlGetProcessoAdministrativo .= " where y50_codauto = $y50_codauto ";

            $rsGetProcessoAdministrativo = db_query($sSqlGetProcessoAdministrativo);
            if (pg_numrows($rsGetProcessoAdministrativo) > 0){
                db_fieldsmemory($rsGetProcessoAdministrativo,0);
            }

            $db_opcao = 3;
            $result = db_query("select * from fiscalizacao.fis_procfiscalauto where y111_auto = ".$y50_codauto);
            if (pg_numrows($result) > 0) {
                db_fieldsmemory($result,0);
                $ProcFiscal = $y111_procfiscal;
            }


            $sGestor  = "select * from fiscalizacao.fis_cadgestorfiscal where id_usuario = ".db_getsession('DB_id_usuario');
            $rsGestor = pg_query($sGestor);
            $iGestor  = pg_num_rows($rsGestor);
            $where2 = "";
            if( $iGestor > 0 ){
                $where2 .= " AND CASE when y100_sequencial is not null then";
            } else {
                $where2 .= " and";
            }

            $where2 .= " CASE WHEN  fis_grupotipoandamento.sequencial <> 8 or fis_grupotipoandamento.sequencial is null THEN (   db_usuarios.id_usuario = ".db_getsession('DB_id_usuario');
            $where2 .= " and (fis_datalimitefiscal.data is null or fis_datalimitefiscal.data > '$dataAtual') ";
            $where2 .= " and fis_processofiscalativo.ativo = 't') or (y56_id_usuario = ".db_getsession('DB_id_usuario').")";

            if( $iGestor > 0 ){
                $where2 .= " else 1=1 end";
            }
            $where2  .= " else 1=1 end";
            $where = $where2;
            $andWhere = " and dl_Auto = ".$y50_codauto;

            $sql  = "select distinct dl_Auto, dl_identificacao, dl_codigo, z01_nome, tipo, y50_instit, y50_numbloco, y41_descr as dl_Andamento ";
            $sql .= "from (select y50_numbloco, y50_instit, y50_setor, y50_codauto  AS dl_Auto, y41_descr, ";
            $sql .= "case when q02_numcgm is not null then 'Inscrição' else (case when j01_numcgm is not null then 'Matrícula' ";
            $sql .= " else (case when y80_numcgm is not null then 'Sanitário' else (case when z01_numcgm is not null then 'Cgm' ";
            $sql .= " else (case when y30_codnoti is not null then 'Notificação' else 'Nenhum' end) end ) end) end) end as dl_identificacao, ";
            $sql .= "case when y52_inscr is not null then y52_inscr else (case when y53_matric is not null then y53_matric ";
            $sql .= " else (case when y55_codsani is not null then y55_codsani else (case when z01_numcgm is not null then z01_numcgm ";
            $sql .= " else (case when y51_codnoti is not null then y51_codnoti end) end) end) end) end as dl_codigo, ";
            $sql .= " case when q02_numcgm is not null then q02_numcgm else (case when j01_numcgm is not null then j01_numcgm  ";
            $sql .= " else (case when y80_numcgm is not null then y80_numcgm else (case when z01_numcgm is not null then z01_numcgm ";
            $sql .= " else q02_numcgm end) end) end) end as z01_numcgm, y27_descr as tipo from fiscalizacao.fis_auto ";
            $sql .= "left join fiscalizacao.fis_tipofiscaliza on y50_codtipo = y27_codtipo ";
            $sql .= "left join fiscalizacao.fis_autousu on y56_codauto = y50_codauto ";
            $sql .= "left join fiscalizacao.fis_autocgm on y54_codauto = y50_codauto ";
            $sql .= "left join fiscalizacao.fis_autoinscr on y52_codauto = y50_codauto ";
            $sql .= "left join fiscalizacao.fis_automatric on y53_codauto = y50_codauto ";
            $sql .= "left join fiscalizacao.fis_autosanitario on y55_codauto = y50_codauto ";
            $sql .= "left join iptubase on j01_matric = y53_matric ";
            $sql .= "left join issbase on y52_inscr = q02_inscr ";
            $sql .= "left join cgm on z01_numcgm = y54_numcgm ";
            $sql .= "left join fiscalizacao.fis_sanitario on y80_codsani = y55_codsani ";
            $sql .= "left join fiscalizacao.fis_autofiscal on y51_codauto = y50_codauto ";
            $sql .= "left join fiscalizacao.fis_procfiscalauto on y111_auto = y50_codauto ";
            $sql .= "left join fiscalizacao.fis_procfiscal on y111_procfiscal = y100_sequencial ";
            $sql .= "left join fiscalizacao.fis_procfiscalfiscais  on fis_procfiscalfiscais.y106_procfiscal = y100_sequencial ";
            $sql .= "left join fiscalizacao.fis_cadfiscais on fis_procfiscalfiscais.y106_cadfiscais = fis_cadfiscais.id_usuario ";
            $sql .= "left join db_usuarios  on db_usuarios.id_usuario = fis_cadfiscais.id_usuario ";
            $sql .= "left join fiscalizacao.fis_processofiscalativo on fis_processofiscalativo.processo_fiscal = y100_sequencial ";
            $sql .= "left join fiscalizacao.fis_processoprorrogacaofinalizacao on fis_processoprorrogacaofinalizacao.processo_fiscal = y100_sequencial ";
            $sql .= "left join fiscalizacao.fis_datalimitefiscal on fis_datalimitefiscal.fiscal = fis_cadfiscais.id_usuario ";
            $sql .= "left join fiscalizacao.fis_fiscal on y51_codnoti = y30_codnoti ";
            $sql .= "left join fiscalizacao.fis_fandam on y39_codandam = (select max(y58_codandam) from fiscalizacao.fis_autoandam where y58_codauto = y50_codauto) ";
            $sql .= "left join fiscalizacao.fis_tipoandam on y41_codtipo = y39_codtipo ";
            $sql .= "left join fiscalizacao.fis_grupotipoandamento_tipoandam on y41_codtipo = fi30_tipoandam ";
            $sql .= "left join fiscalizacao.fis_grupotipoandamento on fi30_grupo = fis_grupotipoandamento.sequencial ";
            $sql .= " where (y39_codandam is null or (fis_grupotipoandamento.sequencial=3 or fis_grupotipoandamento.sequencial=11 or fis_grupotipoandamento.sequencial=14)) ";
            $sql .= $where;
            $sql .= " ) as x inner join cgm on cgm.z01_numcgm = x.z01_numcgm ";
            $sql .= " where y50_instit = ".db_getsession('DB_instit')." and x.y50_setor=".db_getsession('DB_coddepto'). $andWhere;

            $result = $clauto->sql_record($sql);

            if($clauto->numrows > 0){
                db_fieldsmemory($result,0);

                $result = $clautolocal->sql_record($clautolocal->sql_query($y50_codauto,"*",null," y50_codauto = $y50_codauto and y50_instit = ".db_getsession('DB_instit') ));
                if($clautolocal->numrows > 0){
                    db_fieldsmemory($result,0);
                }

                $result = $clautoexec->sql_record($clautoexec->sql_query($y50_codauto,"*",null," y50_codauto = $y50_codauto and y50_instit = ".db_getsession('DB_instit') ));
                if($clautoexec->numrows > 0){
                    db_fieldsmemory($result,0);
                }

                $result = $clautousu->sql_record($clautousu->sql_query($y50_codauto,null,"*",null," y50_codauto = $y50_codauto and y50_instit = ".db_getsession('DB_instit')));

                if($clautousu->numrows == 0){
                    $sqlerro = true;
                    $erro_msg = "Não existem fiscais cadastrados para este auto de infração!";
                }
                $sSqlTipoAndam = " select * from fiscalizacao.fis_tipoandam where y41_codtipo = $y39_codtipo ";
                $rsTipoAndam = db_query($sSqlTipoAndam);
                if (pg_num_rows($rsTipoAndam) > 0) {
                    db_fieldsmemory($rsTipoAndam, 0);
                }
                $db_botao = false;
            }else{
                $sqlerro = true;
                $erro_msg = "Código do auto de infração inválido!";
            }

            $clauto->y50_dtvenc   = $y50_dtvenc;
            $clauto->y50_prazorec = $y50_prazorec;
            $clauto->y50_data     = $data_ciencia;
            $clauto->y50_codauto  = $y50_codauto;
            $clauto->alterar($y50_codauto);


            if ($clauto->erro_status==0){
                $erro_msg = $clauto->erro_msg;
                $sqlerro  = true;
            }

            $sSqlTipoAndam = " select * from fiscalizacao.fis_tipoandam where y41_codtipo = $y39_codtipo ";
            $rsTipoAndam = db_query($sSqlTipoAndam);
            if (pg_num_rows($rsTipoAndam) > 0) {
                db_fieldsmemory($rsTipoAndam, 0);
            }

            if($y41_permcalc == t){
                $result = $oDaoAutonumpre->sql_record($oDaoAutonumpre->sql_query_precalculo(null,"*",null,"y122_codauto = {$y50_codauto}"));
                $rsAutoLEV = db_query(" select * from fiscalizacao.fis_autolevanta where y117_auto = {$y50_codauto} ");
                if($oDaoAutonumpre->numrows == 0 && pg_num_rows( $rsAutoLEVT ) > 0 ){
                    $erro_msg = "Auto sem pré cálculo, não pode ser implantado!";
                    $importar = false;
                    $sqlerro  = true;
                }

                if($sqlerro == false){

                    $sDataCalc = $data_ciencia;
                    $rsCalculo = db_query("select fc_fis_autodeinfracao_andam($y50_codauto, '$sDataCalc')");;
                    $sInfo     = db_utils::fieldsmemory($rsCalculo, 0)->fc_fis_autodeinfracao_andam;

                    if(substr(trim($sInfo), 0, 37) == 'Cálculo do Auto de Infração Executado'){
                        $importar = true;
                        $erro_msg = $sInfo;
                    } else {
                        $erro_msg = $sInfo;
                    }

                    $importar = true;
                 }
             }

            /* Se andamento permitir calculo efetua a rotina de exportação dos levantamentos */

            if ($importar == true) {

                $cllevanta            = db_utils::getDao("fis_levanta");
                $clautolevanta        = db_utils::getDao("fis_autolevanta");
                $cllevvalor           = db_utils::getDao("fis_levvalor");
                $clissvarlevold       = db_utils::getDao("fis_issvarlevold");
                $cllevinscr           = db_utils::getDao("fis_levinscr");
                $cllevusu             = db_utils::getDao("fis_levusu");
                $clissvar             = db_utils::getDao('issvar');
                $clissvarlev          = db_utils::getDao('issvarlev');
                $clnumpref            = db_utils::getDao('numpref');
                $clcadvenc            = db_utils::getDao('cadvenc');
                $clparissqn           = db_utils::getDao('parissqn');
                $cldb_confplan        = db_utils::getDao('db_confplan');
                $clarrecad            = db_utils::getDao('arrecad');
                $clarretipo           = db_utils::getDao('arretipo');
                $clcadtipo            = db_utils::getDao('cadtipo');
                $clarreinscr          = db_utils::getDao('arreinscr');
                $clarrenumcgm         = db_utils::getDao('arrenumcgm');
                $clparfiscal          = db_utils::getDao("fis_parfiscal");
                $oDaoInformacaoDebito = db_utils::getDao('informacaodebito');

                $sqllev = "select y117_levanta as y60_codlev from fiscalizacao.fis_autolevanta where y117_auto = ".$y50_codauto;

                $result_lev = db_query($sqllev);
                if(pg_num_rows($result_lev) != 0) {
                    for ($il=0; $il < pg_num_rows($result_lev); $il++) {
                        db_fieldsmemory($result_lev, $il);

                        $result_fiscais = $cllevusu->sql_record($cllevusu->sql_query_file($y60_codlev));
                        if ( $cllevusu->numrows!=0 ) {
                            $passou = true;
                            $result = $cllevanta->sql_record($cllevanta->sql_query_file($y60_codlev));
                            db_fieldsmemory($result,0);

                            /**
                             * Verifica se já foi importado
                             */
                            if($y60_importado == 't'){

                                $erro_msg = "Levantamento já exportado!";
                                $sqlerro = true;

                            }else{

                                $cllevanta->y60_importado = 'true';
                                $cllevanta->y60_codlev = $y60_codlev;
                                $cllevanta->alterar($y60_codlev);
                                if($cllevanta->erro_status==0){
                                    $erro_msg= $cllevanta->erro_msg;
                                    $sqlerro=true;
                                }
                            }

                            //rotina que monta o array com o perido de exclusao...
                            if ($sqlerro == false){
                                $result = $cllevvalor->sql_record($cllevvalor->sql_query_file(null,"y63_ano as max_ano,y63_mes as max_mes","y63_ano desc,y63_mes desc ","y63_codlev=$y60_codlev"));
                                if ($cllevvalor->numrows > 0) {
                                    $result = $cllevvalor->sql_record($cllevvalor->sql_query_file(null,"y63_ano as max_ano,y63_mes as max_mes","y63_ano desc,y63_mes desc ","y63_codlev=$y60_codlev"));
                                    db_fieldsmemory($result,0);

                                    $result = $cllevvalor->sql_record($cllevvalor->sql_query_file(null,"y63_ano as min_ano,y63_mes as min_mes","y63_ano asc,y63_mes asc ","y63_codlev=$y60_codlev"));
                                    db_fieldsmemory($result,0);

                                    $arr = array();
                                    $cont = 0;
                                    while(1==1){
                                        $arr[$cont][0] =  $min_ano;
                                        $arr[$cont][1] =  $min_mes;
                                        $cont++;
                                        $min_mes++;
                                        if($min_mes>12){
                                            $min_mes =1;
                                            if($min_ano != $max_ano){
                                                $min_ano++;
                                            }
                                        }
                                        if($min_ano == $max_ano){
                                            if($min_mes == $max_mes){
                                                $arr[$cont][0] =  $min_ano;
                                                $arr[$cont][1] =  $min_mes;
                                                break;
                                            }
                                        }
                                    }
                                } else {
                                    db_msgbox('Levantamento sem valores. \\nVerifique.');
                                    $sqlerro=true;
                                    $passou = false;
                                }
                            }

                            //ROTINA QUE EXCLUI DOS ISSVAR E ARRECAD
                            if($sqlerro == false){
                                $result_levinfo=$cllevanta->sql_record($cllevanta->sql_query_inf($y60_codlev));
                                db_fieldsmemory($result_levinfo,0);
                                if (isset($y62_inscr) && $y62_inscr!=""){
                                    $tab   = "arreinscr";
                                    $where = "arreinscr.k00_inscr=$y62_inscr";
                                }else if (isset($y93_numcgm)&&$y93_numcgm!=""){

                                    $tab   = "arrenumcgm";
                                    $where = "arrenumcgm.k00_numcgm=$y93_numcgm";
                                }

                                $sql11="select arrecad.k00_numpre,k00_numpar,q05_codigo,q05_ano,q05_mes
                                    from issvar
                                    left  join issvarlev on q18_codigo              = q05_codigo
                                    left  join issarqsimplesregissvar on q68_issvar = q05_codigo
                                    inner join $tab      on $tab.k00_numpre         = q05_numpre
                                    inner join arrecad   on $tab.k00_numpre         = arrecad.k00_numpre
                                    and arrecad.k00_numpar      = q05_numpar
                                    left  join issplannumpre          on q32_numpre = q05_numpre
                                    where $where
                                    and q18_codigo is null
                                    and q32_numpre is null
                                    and q68_issvar is null";

                                $result11  = db_query($sql11);
                                $numrows11 = pg_numrows($result11);

                                for($x=0; $x<$numrows11; $x++){
                                    db_fieldsmemory($result11,$x,true);
                                    $exclui = false;
                                    for($q=0; $q<count($arr); $q++ ){
                                        if($q05_ano==$arr[$q][0] && $q05_mes==$arr[$q][1]  ){
                                            $exclui = true;
                                            break;
                                        }
                                    }
                                    if($exclui==false){
                                        continue;
                                    }
                                    if($sqlerro == false){
                                        $clarrecad->excluir_arrecad($k00_numpre,$k00_numpar);
                                        if($clarrecad->erro_status==0){
                                            $erro_msg = $clarrecad->erro_msg;
                                            $sqlerro  = true;
                                            break;
                                        }
                                    }

                                    if($sqlerro == false){
                                        $clissvarlevold->y85_codissvar = $q05_codigo;
                                        $clissvarlevold->y85_codlev = $y60_codlev;
                                        $clissvarlevold->incluir();
                                        if($clissvarlevold->erro_status==0){
                                            $erro_msg = $clissvarlevold->erro_msg;
                                            $sqlerro  = true;
                                            break;
                                        }
                                    }
                                    if($sqlerro == false){
                                        $clissvarlev->sql_record($clissvarlev->sql_query_file($q05_codigo,$y60_codlev));
                                        if($clissvarlev->numrows>0){
                                            $clissvarlev->q18_codigo = $q05_codigo;
                                            $clissvarlev->q18_codlev = $y60_codlev;
                                            $clissvarlev->excluir($q05_codigo,$y60_codlev);
                                            if($clissvarlev->erro_status==0){
                                                $erro_msg = $clissvarlev->erro_msg;
                                                $sqlerro  = true;
                                                break;
                                            }
                                        }
                                    }

                                    if($sqlerro == false){
                                        $clissvar->excluir_issvar($q05_codigo,$y60_codlev);
                                        if($clissvar->erro_status==0){
                                            $erro_msg = $clissvar->erro_msg;
                                            $sqlerro  = true;
                                            break;
                                        }
                                    }
                                }
                            }
                            //FINAL

                            $sql=$cllevvalor->sql_query_inf(null,"sum(y63_saldo) as y63_saldo,
                                    y62_inscr,
                                    y93_numcgm,
                                    y63_mes,
                                    y63_ano,
                                    y63_aliquota,
                                    y63_sequencia,
                                    x.z01_numcgm,
                                    y63_dtvenc",
                              "",
                              "y63_codlev = $y60_codlev and y63_saldo > 0
                                    group by y63_ano,y63_mes,y63_aliquota,y62_inscr,y93_numcgm,y63_sequencia,x.z01_numcgm,y63_dtvenc
                                    order by y63_dtvenc");
                            $resultado  = $cllevanta->sql_record($sql);
                            $numrows = $cllevanta->numrows;

                            if($numrows == 0){

                                $erro_msg = "Valores não disponíveis para este levantamento. Importação cancelada.";
                                $sqlerro= true;

                            }

                            $parc   = 0;
                            for($i=0; $i<$numrows; $i++){

                                if($sqlerro == true){
                                    break;
                                }

                                db_fieldsmemory($resultado,$i);

                                if ($anoAnterior != $y63_ano) {
                                    $anoAnterior = $y63_ano;
                                    $parc = 0;
                                    $numtot = 0;
                                    $numpre = $clnumpref->sql_numpre();
                                    $levantamentos = db_utils::getCollectionByRecord($resultado);

                                    foreach ($levantamentos as $levantamento){
                                        if ($levantamento->y63_ano == $y63_ano) {
                                            $numtot++;
                                        }
                                    }
                                }

                                $parc++;
                                if($sqlerro == false){

                                    $clissvar->q05_numpre=$numpre;
                                    $clissvar->q05_numpar=$parc;
                                    $clissvar->q05_valor=$y63_saldo;
                                    $clissvar->q05_ano=$y63_ano;
                                    $clissvar->q05_mes=$y63_mes;
                                    $clissvar->q05_histor="Levantamento fiscal...";
                                    $clissvar->q05_aliq=$y63_aliquota;

                                    $bruto = ($y63_saldo/$y63_aliquota)*100;
                                    $clissvar->q05_bruto="$bruto";
                                    $clissvar->q05_vlrinf="$bruto";
                                    $clissvar->incluir(null);
                                    if($clissvar->erro_status==0){
                                        $erro_msg = $clissvar->erro_msg;
                                        $sqlerro=true;

                                    }
                                    $codigo=$clissvar->q05_codigo;

                                }
                                //FINAL DA INCLUSÃO NO ARR*

                                //INCLUI NA TABELA ISSVARLEV
                                if($sqlerro==false){

                                    $clissvarlev->y60_codigo = $codigo;
                                    $clissvarlev->y60_codlev = $y60_codlev;
                                    $clissvarlev->incluir($codigo,$y60_codlev);
                                    if($clissvarlev->erro_status==0){
                                        $erro_msg = $clissvarlev->erro_msg;
                                        $sqlerro=true;

                                    }
                                }

                                if ($sqlerro==false) {

                                    /**
                                     * Rotina criada para guardar data do débito de issqn variável igual a data de realização do levantamento fiscal
                                     * Para essa função tambem existe uma trigger na tabela issvar, que a cada operação atualiza a data do débito com a data atual do sistema
                                     * Essa parte foi criada para atualizar os dados
                                     */
                                    $sWhere                = "k163_numpre = {$numpre} and k163_numpar = {$parc}";
                                    $sSqlInformacaoDebito  = $oDaoInformacaoDebito->sql_query_file(null, "*", null, $sWhere);
                                    $rsInformacaoDebito    = $oDaoInformacaoDebito->sql_record($sSqlInformacaoDebito);

                                    if ($oDaoInformacaoDebito->numrows > 0) {

                                        $oInformacaoDebito = db_utils::fieldsMemory($rsInformacaoDebito, 0);

                                        $oDaoInformacaoDebito->k163_sequencial = $oInformacaoDebito->k163_sequencial;
                                        $oDaoInformacaoDebito->k163_data       = $y60_data != '' ? $y60_data : date('Y-m-d', db_getsession('DB_datausu'));
                                        $oDaoInformacaoDebito->alterar($oInformacaoDebito->k163_sequencial);

                                    } else {

                                        $oDaoInformacaoDebito->k163_numpre = $numpre;
                                        $oDaoInformacaoDebito->k163_numpar = $parc;
                                        $oDaoInformacaoDebito->k163_data   = $y60_data != '' ? $y60_data : date('Y-m-d', db_getsession('DB_datausu'));
                                        $oDaoInformacaoDebito->incluir(null);
                                    }

                                    if ($oDaoInformacaoDebito->erro_status == '0') {
                                        $erro_msg = $oDaoInformacaoDebito->erro_msg;
                                        $sqlerro  = true;
                                    }

                                }

                                //inclui no arreinscr
                                if(!$sqlerro && isset($y62_inscr) && $y62_inscr!=""){

                                    $clarreinscr->sql_record($clarreinscr->sql_query_file($numpre,$y62_inscr));
                                    if($clarreinscr->numrows==0){

                                        $clarreinscr->k00_numpre=$numpre;
                                        $clarreinscr->k00_inscr=$y62_inscr;
                                        $clarreinscr->k00_perc=100;
                                        $clarreinscr->incluir($numpre,$y62_inscr);

                                        if($clarreinscr->erro_status==0){
                                            $sqlerro  = true;
                                            $erro_msg = $clarreinscr->erro_msg;
                                        }
                                    }
                                }

                                if(!$sqlerro && isset($y93_numcgm) && $y93_numcgm!=""){

                                    $clarrenumcgm->sql_record($clarrenumcgm->sql_query_file($y93_numcgm,$numpre));
                                    if($clarrenumcgm->numrows==0){

                                        $clarrenumcgm->k00_numpre=$numpre;
                                        $clarrenumcgm->k00_numcgm=$y93_numcgm;
                                        $clarrenumcgm->incluir($y93_numcgm,$numpre);
                                        if($clarrenumcgm->erro_status==0){
                                            $sqlerro  = true;
                                            $erro_msg = $clarrenumcgm->erro_msg;
                                        }
                                    }
                                }

                                $iTipoDebitoAlterado = 22;

                                //INCLUSÃO NO ARRECAD
                                if(!$sqlerro){

                                    $result66 = $clparissqn->sql_record($clparissqn->sql_query_file());
                                    db_fieldsmemory($result66,0);

                                    /**
                                     * Altera pelo tipo de debito selecionado
                                     */
                                    $clarrecad->k00_tipo = $q60_tipo;
                                    if( !empty($iTipoDebitoAlterado) ){
                                        $clarrecad->k00_tipo = $iTipoDebitoAlterado;
                                    }

                                    $result_parfiscal=$clparfiscal->sql_record($clparfiscal->sql_query_file());
                                    db_fieldsmemory($result_parfiscal,0);

                                    if ($y60_espontaneo=='f'){
                                        $clarrecad->k00_receit = $y32_receit;
                                    }else{
                                        $clarrecad->k00_receit = $y32_receitexp;
                                    }

                                    $result77 = $clcadvenc->sql_record($clcadvenc->sql_query_file($q60_codvencvar,$y63_mes,"q82_venc,q82_hist"));
                                    db_fieldsmemory($result77,0);
                                    $clarrecad->k00_hist = $q82_hist;
                                    if($y63_ano == db_getsession("DB_anousu")){
                                        $clarrecad->k00_dtvenc="$y63_dtvenc";
                                    }else{

                                        $res = $cldb_confplan->sql_record($cldb_confplan->sql_query());
                                        if($cldb_confplan->numrows > 0){
                                            db_fieldsmemory($res,0);
                                        }else{
                                            $erro_msg = "Tabela db_confplan vazia!";
                                            $sqlerro  = true;
                                        }
                                        $qmes = $y63_mes;
                                        $qano = $y63_ano;
                                        $qmes += 1;
                                        if($qmes > 12){
                                            $qmes = 1;
                                            $qano += 1;
                                        }
                                        $clarrecad->k00_dtvenc="$y63_dtvenc";
                                    }

                                    $arr = explode  ("-",$clarrecad->k00_dtvenc);

                                    $clarrecad->k00_numcgm=$z01_numcgm;
                                    $clarrecad->k00_dtoper= $arr[0]."-".$arr[1]."-01";
                                    $clarrecad->k00_valor=$y63_saldo;
                                    $clarrecad->k00_numpre=$numpre;
                                    $clarrecad->k00_numtot=1;
                                    $clarrecad->k00_numpar=$parc;
                                    $clarrecad->k00_numdig='0';
                                    $clarrecad->k00_tipojm='0';
                                    $clarrecad->incluir();

                                    if($clarrecad->erro_status==0){
                                        $erro_msg = $clarrecad->erro_msg;
                                        $sqlerro=true;
                                    }
                                }
                            }
                        } else {
                            $erro_msg = "Não existem fiscais cadastrados para o levantamento!!Exportação Cancelada entrou!!";
                            $sqlerro = true;
                            $passou = false;
                            break;
                        }
                    }
                }
            }

            if ($sqlerro==false) {
                $clfandam->y39_data  = $dataAtual;
                $clfandam->incluir($y39_codandam);

                if($clfandam->erro_status == 0){
                    $erro_msg = $clfandam->erro_msg;
                    $sqlerro = true;
                }

                $clautoultandam->y16_codnoti = $y50_codauto;
                $clautoultandam->excluir($y50_codauto);
                $clautoultandam->incluir($y50_codauto, $clfandam->y39_codandam);
                $clautoandam->incluir($y50_codauto, $clfandam->y39_codandam);

                if($clautoultandam->erro_status==0){
                    $erro_msg = $clautoultandam->erro_msg;
                    $sqlerro = true;
                }
                if (isset($sequencial) and $sequencial != '') {
                    $cldatacienciaandamento->codigo_peca = $y50_codauto;
                    $cldatacienciaandamento->data_ciencia = $data_ciencia;
                    $cldatacienciaandamento->fandam = $clfandam->y39_codandam;
                    $cldatacienciaandamento->alterar($sequencial);
                } else {
                    $cldatacienciaandamento->codigo_peca = $y50_codauto;
                    $cldatacienciaandamento->data_ciencia = $data_ciencia;
                    $cldatacienciaandamento->fandam = $clfandam->y39_codandam;
                    $cldatacienciaandamento->incluir();
                }

                $sProcFiscal  = "select fis_procfiscal.* from fiscalizacao.fis_auto inner join fiscalizacao.fis_procfiscalauto on y50_codauto = y111_auto ";
                $sProcFiscal .= "inner join fiscalizacao.fis_procfiscal on y111_procfiscal = y100_sequencial where y50_codauto = $y50_codauto ";
                $rsProcFiscal = db_query($sProcFiscal);

                db_fieldsmemory($rsProcFiscal,0);
                if (pg_num_rows($rsProcFiscal) > 0 && ($y100_dtinicial == '' or $y100_dtinicial == null)) {
                    $iUpdate = 0;
                    $sFandam  = "select sequencial as seq from fiscalizacao.fis_grupotipoandamento ";
                    $sFandam .= " inner join fiscalizacao.fis_grupotipoandamento_tipoandam on fi30_grupo = sequencial ";
                    $sFandam .= " inner join fiscalizacao.fis_tipoandam on fi30_tipoandam = y41_codtipo ";
                    $sFandam .= " where y41_codtipo = $y39_codtipo";
                    $rsFandam = db_query($sFandam);
                    if (pg_num_rows($rsFandam) > 0) {
                        for($i = 0; $i < pg_num_rows($rsFandam); $i++){
                            db_fieldsmemory($rsFandam, $i);
                            if ($seq == 8) {
                                $iUpdate = 1; break;
                            }
                        }
                    }
                    if ($iUpdate == 1){
                        $sDataCiencia = $data_ciencia;
                        $sDataProcFiscal = " update fiscalizacao.fis_procfiscal set y100_dtinicial = '$sDataCiencia' ";
                        $sDataProcFiscal .= " where y100_sequencial =  $y100_sequencial ";
                        db_query($sDataProcFiscal);

                        $sql = "INSERT INTO fiscalizacao.fis_processoprorrogacaofinalizacao (
                                                processo_fiscal,
                                                data_abertura,
                                                data_prorrogacao,
                                                data_fim,
                                                id_usuario,
                                                situacao,
                                                observacao
                                                ) VALUES (
                                                ".$y100_sequencial.",
                                                '".date("Y-m-d")."',
                                                null,
                                                '".date("Y-m-d",strtotime("+31 days",strtotime($sDataCiencia)))."',
                                                ".db_getsession('DB_id_usuario').",
                                                0,
                                                ''
                                                )";

                        $rsInsert = db_query($sql);
                        if($rsInsert == false){
                            $sqlerro = true;
                            $erro_msg = "Erro ao Incluir Data Final do Processo Fiscal.".pg_last_error();
                        }

                    }
                }
            }
            if($sqlerro == false){
                if ($y60_proces != '') {
                    $clprocessoandam->pa01_codandam = $clfandam->y39_codandam;
                    $clprocessoandam->pa01_processo = $y60_proces;

                    $clprocessoandam->incluir();
                    if($clprocessoandam->erro_status == 0){
                        $sqlerro = true;
                        $erro_msg = $clprocessoandam->erro_msg;
                    }
                }
            }
        } else if($pecafiscal == 2) { // NOTIFICAÇÃO DE LANCAMENTO

            $oDaoLancamento = db_utils::getDao("fis_lancamento");
            $oDaoArrecad    = db_utils::getDao('arrecad');
            $oDaoArrecant   = db_utils::getDao('arrecant');
            $oDaoLancnumpre = db_utils::getDao("fis_lancnumpre");

            $nl01_codlanc = $arrItem[$bcd];

            // verificar se tem processo fiscal para aquela notif, se houver ele bloqueia a alteração do processo administrativo
            $sProcfiscal  = $clprocfiscallanc->sql_query(null,"*",null,"nl09_lanc = $nl01_codlanc");
            $rsProcFiscal = db_query($sProcFiscal);
            if( pg_num_rows( $rsProcFiscal ) > 0 ){
                $ProcFiscal = $y111_procfiscal;
            }

            $sqlPuginParam = 'select * from fiscalizacao.fis_fiscalparametros';
            $rsPluginParam = db_query($sqlPuginParam);
            db_fieldsmemory($rsPluginParam, 0);

            $oSqlProcesso = $clprocfiscallanc->getTipoProcessoAdministrativo(null,"nl01_codlanc = $nl01_codlanc");
            $rsGetProcessoAdministrativo = db_query( $oSqlProcesso );

            if (pg_num_rows($rsGetProcessoAdministrativo) > 0){
                db_fieldsmemory($rsGetProcessoAdministrativo,0);
            }

            db_fieldsmemory($result,0);
            $result = $cllanclocal->sql_record($cllanclocal->sql_query($nl01_codlanc,"*",null," nl01_codlanc = $nl01_codlanc and nl01_instit = ".db_getsession('DB_instit') ));
            if( $cllanclocal->numrows > 0 ){
                db_fieldsmemory($result,0);
            }

            $result = $cllancexec->sql_record($cllancexec->sql_query($nl01_codlanc,"*",null," nl01_codlanc = $nl01_codlanc and nl01_instit = ".db_getsession('DB_instit') ));
            if( $cllancexec->numrows > 0 ){
                db_fieldsmemory($result,0);
            }

            $result = $cllancusu->sql_record($cllancusu->sql_query($nl01_codlanc,null,"*",null," nl01_codlanc = $nl01_codlanc and nl01_instit = ".db_getsession('DB_instit')));

            if( $cllancusu->numrows == 0 ){
                db_msgbox("Não existem fiscais cadastrados para esta Notificação de Lançamento de infração!");
                $sql_erro = true;
            }

            $sSqlTipoAndam = " select * from fiscalizacao.fis_tipoandam where y41_codtipo = $y39_codtipo ";

            $rsTipoAndam = db_query($sSqlTipoAndam);
            if (pg_num_rows($rsTipoAndam) > 0) {
                db_fieldsmemory($rsTipoAndam, 0);
            }

            $cllancamento->nl01_dtvenc   = $y50_dtvenc;
            $cllancamento->nl01_prazorec = $y50_prazorec;
            $cllancamento->nl01_data      = $data_ciencia;
            $cllancamento->nl01_codlanc  = $nl01_codlanc;
            $cllancamento->alterar($nl01_codlanc);
            if ($cllancamento->erro_status==0){
                $erro_msg = $cllancamento->erro_msg;
                $sqlerro  = true;
            }

            $sSqlTipoAndam = " select * from fiscalizacao.fis_tipoandam where y41_codtipo = $y39_codtipo ";
            $rsTipoAndam = db_query($sSqlTipoAndam);
            if (pg_num_rows($rsTipoAndam) > 0) {
                db_fieldsmemory($rsTipoAndam, 0);
            }

            if($y41_permcalc == t){
                $result = $oDaoLancnumpre->sql_record($oDaoLancnumpre->sql_query_precalculo(null,"*",null,"nl17_codlanc = {$nl01_codlanc}"));
                $rsAutoLEV = db_query(" select * from fiscalizacao.fis_lanclevanta where nl15_lancamento = {$nl01_codlanc} ");
                if($oDaoLancnumpre->numrows == 0 && pg_num_rows( $rsAutoLEVT ) > 0 ){
                    $erro_msg = "Notificação de Lançamento sem pré cálculo, não pode ser implantado!";
                    $importar = false;
                    $sqlerro  = true;
                }

                if($sqlerro == false){

                        $sDataCalc = $data_ciencia;
                        $rsCalculo = db_query("select fc_fis_lancamento_andam($nl01_codlanc, '$sDataCalc')");;
                        $sInfo     = db_utils::fieldsmemory($rsCalculo, 0)->fc_fis_lancamento_andam;

                    if(substr(trim($sInfo), 0, 37) == 'Cálculo do Auto de Infração Executado'){
                        $importar = true;
                        $erro_msg = $sInfo;
                    }else{
                        $erro_msg = $sInfo;
                    }

                    $importar = true;
                }
            }
            if ($importar == true) {

                $cllevanta            = db_utils::getDao("fis_levanta");
                $cllanclevanta        = db_utils::getDao("fis_lanclevanta");
                $cllevvalor           = db_utils::getDao("fis_levvalor");
                $clissvarlevold       = db_utils::getDao("fis_issvarlevold");
                $cllevinscr           = db_utils::getDao("fis_levinscr");
                $cllevusu             = db_utils::getDao("fis_levusu");
                $clissvar             = db_utils::getDao('issvar');
                $clissvarlev          = db_utils::getDao('issvarlev');
                $clnumpref            = db_utils::getDao('numpref');
                $clcadvenc            = db_utils::getDao('cadvenc');
                $clparissqn           = db_utils::getDao('parissqn');
                $cldb_confplan        = db_utils::getDao('db_confplan');
                $clarrecad            = db_utils::getDao('arrecad');
                $clarretipo           = db_utils::getDao('arretipo');
                $clcadtipo            = db_utils::getDao('cadtipo');
                $clarreinscr          = db_utils::getDao('arreinscr');
                $clarrenumcgm         = db_utils::getDao('arrenumcgm');
                $clparfiscal          = db_utils::getDao("fis_parfiscal");
                $oDaoInformacaoDebito = db_utils::getDao('informacaodebito');

                $sqllev = "select nl15_levanta as y60_codlev from fiscalizacao.fis_lanclevanta where nl15_lancamento = ".$nl01_codlanc;

                $result_lev = db_query($sqllev);
                if(pg_num_rows($result_lev) != 0) {
                    for ($il=0; $il < pg_num_rows($result_lev); $il++) {
                        db_fieldsmemory($result_lev, $il);

                        $result_fiscais = $cllevusu->sql_record($cllevusu->sql_query_file($y60_codlev));
                        if ( $cllevusu->numrows!=0 ) {
                            $passou = true;
                            $result = $cllevanta->sql_record($cllevanta->sql_query_file($y60_codlev));
                            db_fieldsmemory($result,0);

                            /**
                            * Verifica se já foi importado
                            */
                            if($y60_importado == 't'){

                                $erro_msg = "Levantamento já exportado!";
                                $sqlerro = true;

                            }else{

                                $cllevanta->y60_importado = 'true';
                                $cllevanta->y60_codlev = $y60_codlev;
                                $cllevanta->alterar($y60_codlev);
                                if($cllevanta->erro_status==0){
                                    $erro_msg= $cllevanta->erro_msg;
                                    $sqlerro=true;
                                }
                            }

                            //rotina que monta o array com o perido de exclusao...
                            if ($sqlerro == false){
                                $result = $cllevvalor->sql_record($cllevvalor->sql_query_file(null,"y63_ano as max_ano,y63_mes as max_mes","y63_ano desc,y63_mes desc ","y63_codlev=$y60_codlev"));
                                if ($cllevvalor->numrows > 0) {
                                    $result = $cllevvalor->sql_record($cllevvalor->sql_query_file(null,"y63_ano as max_ano,y63_mes as max_mes","y63_ano desc,y63_mes desc ","y63_codlev=$y60_codlev"));
                                    db_fieldsmemory($result,0);

                                    $result = $cllevvalor->sql_record($cllevvalor->sql_query_file(null,"y63_ano as min_ano,y63_mes as min_mes","y63_ano asc,y63_mes asc ","y63_codlev=$y60_codlev"));
                                    db_fieldsmemory($result,0);

                                    $arr = array();
                                    $cont = 0;
                                    while(1==1){
                                        $arr[$cont][0] =  $min_ano;
                                        $arr[$cont][1] =  $min_mes;
                                        $cont++;
                                        $min_mes++;
                                        if($min_mes>12){
                                            $min_mes =1;
                                            if($min_ano != $max_ano){
                                                $min_ano++;
                                            }
                                        }
                                        if($min_ano == $max_ano){
                                            if($min_mes == $max_mes){
                                                $arr[$cont][0] =  $min_ano;
                                                $arr[$cont][1] =  $min_mes;
                                                break;
                                            }
                                        }
                                    }

                                }else{

                                    db_msgbox('Levantamento sem valores. \\nVerifique.');
                                    $sqlerro=true;
                                    $passou = false;
                                }
                            }

                            //ROTINA QUE EXCLUI DOS ISSVAR E ARRECAD
                            if($sqlerro == false){

                                $result_levinfo=$cllevanta->sql_record($cllevanta->sql_query_inf($y60_codlev));
                                db_fieldsmemory($result_levinfo,0);
                                if (isset($y62_inscr) && $y62_inscr!=""){
                                    $tab   = "arreinscr";
                                    $where = "arreinscr.k00_inscr=$y62_inscr";
                                }else if (isset($y93_numcgm)&&$y93_numcgm!=""){
                                    $tab   = "arrenumcgm";
                                    $where = "arrenumcgm.k00_numcgm=$y93_numcgm";
                                }

                                $sql11="select arrecad.k00_numpre,k00_numpar,q05_codigo,q05_ano,q05_mes
                                    from issvar
                                    left  join issvarlev on q18_codigo              = q05_codigo
                                    left  join issarqsimplesregissvar on q68_issvar = q05_codigo
                                    inner join $tab      on $tab.k00_numpre         = q05_numpre
                                    inner join arrecad   on $tab.k00_numpre         = arrecad.k00_numpre
                                    and arrecad.k00_numpar      = q05_numpar
                                    left  join issplannumpre          on q32_numpre = q05_numpre
                                    where $where
                                    and q18_codigo is null
                                    and q32_numpre is null
                                    and q68_issvar is null";

                                $result11  = db_query($sql11);
                                $numrows11 = pg_numrows($result11);

                                for($x=0; $x<$numrows11; $x++){

                                    db_fieldsmemory($result11,$x,true);
                                    $exclui = false;
                                    for($q=0; $q<count($arr); $q++ ){
                                        if($q05_ano==$arr[$q][0] && $q05_mes==$arr[$q][1]  ){
                                            $exclui = true;
                                            break;
                                        }
                                    }
                                    if($exclui==false){
                                        continue;
                                    }
                                    if($sqlerro == false){

                                        $clarrecad->excluir_arrecad($k00_numpre,$k00_numpar);
                                        if($clarrecad->erro_status==0){

                                            $erro_msg = $clarrecad->erro_msg;
                                            $sqlerro  = true;

                                            break;
                                        }
                                    }

                                    if($sqlerro == false){

                                        $clissvarlevold->y85_codissvar = $q05_codigo;
                                        $clissvarlevold->y85_codlev = $y60_codlev;
                                        $clissvarlevold->incluir();
                                        if($clissvarlevold->erro_status==0){

                                            $erro_msg = $clissvarlevold->erro_msg;
                                            $sqlerro  = true;

                                            break;
                                        }
                                    }
                                    if($sqlerro == false){

                                        $clissvarlev->sql_record($clissvarlev->sql_query_file($q05_codigo,$y60_codlev));
                                        if($clissvarlev->numrows>0){

                                            $clissvarlev->q18_codigo = $q05_codigo;
                                            $clissvarlev->q18_codlev = $y60_codlev;
                                            $clissvarlev->excluir($q05_codigo,$y60_codlev);
                                            if($clissvarlev->erro_status==0){

                                                $erro_msg = $clissvarlev->erro_msg;
                                                $sqlerro  = true;

                                                break;
                                            }
                                        }
                                    }

                                    if($sqlerro == false){

                                        $clissvar->excluir_issvar($q05_codigo,$y60_codlev);
                                        if($clissvar->erro_status==0){

                                            $erro_msg = $clissvar->erro_msg;
                                            $sqlerro  = true;

                                            break;
                                        }
                                    }
                                }
                            }
                            //FINAL

                            $sql=$cllevvalor->sql_query_inf(null,"sum(y63_saldo) as y63_saldo,
                                    y62_inscr,
                                    y93_numcgm,
                                    y63_mes,
                                    y63_ano,
                                    y63_aliquota,
                                    y63_sequencia,
                                    x.z01_numcgm,
                                    y63_dtvenc",
                                    "",
                                    "y63_codlev = $y60_codlev and y63_saldo > 0
                                    group by y63_ano,y63_mes,y63_aliquota,y62_inscr,y93_numcgm,y63_sequencia,x.z01_numcgm,y63_dtvenc
                                    order by y63_dtvenc");
                            $resultado  = $cllevanta->sql_record($sql);
                            $numrows = $cllevanta->numrows;

                            if($numrows == 0){

                                $erro_msg = "Valores não disponíveis para este levantamento. Importação cancelada.";
                                $sqlerro= true;

                            }

                            $parc   = 0;
                            $numpre = $clnumpref->sql_numpre();
                            for($i=0; $i<$numrows; $i++){

                                if($sqlerro == true){
                                    break;
                                }

                                db_fieldsmemory($resultado,$i);
                                $parc++;
                                if($sqlerro == false){

                                    $clissvar->q05_numpre=$numpre;
                                    $clissvar->q05_numpar=$parc;
                                    $clissvar->q05_valor=$y63_saldo;
                                    $clissvar->q05_ano=$y63_ano;
                                    $clissvar->q05_mes=$y63_mes;
                                    $clissvar->q05_histor="Levantamento fiscal...";
                                    $clissvar->q05_aliq=$y63_aliquota;;

                                    $bruto = ($y63_saldo/$y63_aliquota)*100;
                                    $clissvar->q05_bruto="$bruto";
                                    $clissvar->q05_vlrinf="$bruto";
                                    $clissvar->incluir(null);
                                    if($clissvar->erro_status==0){
                                        $erro_msg = $clissvar->erro_msg;
                                        $sqlerro=true;

                                    }
                                    $codigo=$clissvar->q05_codigo;

                                }
                                //FINAL DA INCLUSÃO NO ARR*

                                //INCLUI NA TABELA ISSVARLEV
                                if($sqlerro==false){

                                    $clissvarlev->y60_codigo = $codigo;
                                    $clissvarlev->y60_codlev = $y60_codlev;
                                    $clissvarlev->incluir($codigo,$y60_codlev);
                                    if($clissvarlev->erro_status==0){
                                        $erro_msg = $clissvarlev->erro_msg;
                                        $sqlerro=true;

                                    }
                                }

                                if ($sqlerro==false) {

                                    /**
                                    * Rotina criada para guardar data do débito de issqn variável igual a data de realização do levantamento fiscal
                                    * Para essa função tambem existe uma trigger na tabela issvar, que a cada operação atualiza a data do débito com a data atual do sistema
                                    * Essa parte foi criada para atualizar os dados
                                    */
                                    $sWhere                = "k163_numpre = {$numpre} and k163_numpar = {$parc}";
                                    $sSqlInformacaoDebito  = $oDaoInformacaoDebito->sql_query_file(null, "*", null, $sWhere);
                                    $rsInformacaoDebito    = $oDaoInformacaoDebito->sql_record($sSqlInformacaoDebito);

                                    if ($oDaoInformacaoDebito->numrows > 0) {

                                        $oInformacaoDebito = db_utils::fieldsMemory($rsInformacaoDebito, 0);

                                        $oDaoInformacaoDebito->k163_sequencial = $oInformacaoDebito->k163_sequencial;
                                        $oDaoInformacaoDebito->k163_data       = $y60_data != '' ? $y60_data : date('Y-m-d', db_getsession('DB_datausu'));
                                        $oDaoInformacaoDebito->alterar($oInformacaoDebito->k163_sequencial);

                                    } else {

                                        $oDaoInformacaoDebito->k163_numpre = $numpre;
                                        $oDaoInformacaoDebito->k163_numpar = $parc;
                                        $oDaoInformacaoDebito->k163_data   = $y60_data != '' ? $y60_data : date('Y-m-d', db_getsession('DB_datausu'));
                                        $oDaoInformacaoDebito->incluir(null);
                                    }

                                    if ($oDaoInformacaoDebito->erro_status == '0') {
                                        $erro_msg = $oDaoInformacaoDebito->erro_msg;
                                        $sqlerro  = true;
                                    }

                                }

                                //inclui no arreinscr
                                if(!$sqlerro && isset($y62_inscr) && $y62_inscr!=""){

                                    $clarreinscr->sql_record($clarreinscr->sql_query_file($numpre,$y62_inscr));
                                    if($clarreinscr->numrows==0){

                                        $clarreinscr->k00_numpre=$numpre;
                                        $clarreinscr->k00_inscr=$y62_inscr;
                                        $clarreinscr->k00_perc=100;
                                        $clarreinscr->incluir($numpre,$y62_inscr);

                                        if($clarreinscr->erro_status==0){
                                            $sqlerro  = true;
                                            $erro_msg = $clarreinscr->erro_msg;
                                        }
                                    }
                                }

                                if(!$sqlerro && isset($y93_numcgm) && $y93_numcgm!=""){

                                    $clarrenumcgm->sql_record($clarrenumcgm->sql_query_file($y93_numcgm,$numpre));
                                    if($clarrenumcgm->numrows==0){

                                        $clarrenumcgm->k00_numpre=$numpre;
                                        $clarrenumcgm->k00_numcgm=$y93_numcgm;
                                        $clarrenumcgm->incluir($y93_numcgm,$numpre);
                                        if($clarrenumcgm->erro_status==0){
                                            $sqlerro  = true;
                                            $erro_msg = $clarrenumcgm->erro_msg;
                                        }
                                    }
                                }

                                $sSqlTipoDebi = "select nl27_tipo, nl27_historico
                                                        from fiscalizacao.fis_parnotificacaolancamento
                                                         where nl27_instit = ".db_getsession('DB_instit');
                                $rsSqlTipoDebi = db_query($sSqlTipoDebi);
                                db_fieldsmemory($rsSqlTipoDebi,0);

                                $iTipoDebitoAlterado = $nl27_tipo;

                                //INCLUSÃO NO ARRECAD
                                if(!$sqlerro){

                                    $result66 = $clparissqn->sql_record($clparissqn->sql_query_file());
                                    db_fieldsmemory($result66,0);

                                    /**
                                    * Altera pelo tipo de debito selecionado
                                    */
                                    $clarrecad->k00_tipo = $q60_tipo;
                                    if( !empty($iTipoDebitoAlterado) ){
                                        $clarrecad->k00_tipo = $iTipoDebitoAlterado;
                                    }

                                    $result_parfiscal=$clparfiscal->sql_record($clparfiscal->sql_query_file());
                                    db_fieldsmemory($result_parfiscal,0);

                                    if ($y60_espontaneo=='f'){
                                        $clarrecad->k00_receit = $y32_receit;
                                    }else{
                                        $clarrecad->k00_receit = $y32_receitexp;
                                    }

                                    $result77 = $clcadvenc->sql_record($clcadvenc->sql_query_file($q60_codvencvar,$y63_mes,"q82_venc,q82_hist"));
                                    db_fieldsmemory($result77,0);
                                    $clarrecad->k00_hist = $q82_hist;
                                    if($y63_ano == db_getsession("DB_anousu")){
                                        $clarrecad->k00_dtvenc="$y63_dtvenc";
                                    }else{

                                        $res = $cldb_confplan->sql_record($cldb_confplan->sql_query());
                                        if($cldb_confplan->numrows > 0){
                                            db_fieldsmemory($res,0);
                                        }else{
                                            $erro_msg = "Tabela db_confplan vazia!";
                                            $sqlerro  = true;
                                        }
                                        $qmes = $y63_mes;
                                        $qano = $y63_ano;
                                        $qmes += 1;
                                        if($qmes > 12){
                                            $qmes = 1;
                                            $qano += 1;
                                        }
                                        $clarrecad->k00_dtvenc="$y63_dtvenc";
                                    }

                                    $arr = explode  ("-",$clarrecad->k00_dtvenc);

                                    $clarrecad->k00_numcgm=$z01_numcgm;
                                    $clarrecad->k00_dtoper= $arr[0]."-".$arr[1]."-01";
                                    $clarrecad->k00_valor=$y63_saldo;
                                    $clarrecad->k00_numpre=$numpre;
                                    $clarrecad->k00_numtot=1;
                                    $clarrecad->k00_numpar=$parc;
                                    $clarrecad->k00_numdig='0';
                                    $clarrecad->k00_tipojm='0';
                                    $clarrecad->incluir();

                                    if($clarrecad->erro_status==0){
                                        $erro_msg = $clarrecad->erro_msg;
                                        $sqlerro=true;
                                    }
                                }
                            }

                        }else{
                            $erro_msg = "Não existem fiscais cadastrados para o levantamento!!Exportação Cancelada entrou!!";
                            $sqlerro = true;
                            $passou = false;
                            break;
                        }
                       }
                }

                if ($sqlerro==false) {
                    $clfandam->y39_data  = $dataAtual;
                    $clfandam->incluir($y39_codandam);

                    if($clfandam->erro_status == 0){
                        $erro_msg = $clfandam->erro_msg;
                        $sqlerro = true;
                    }

                    $cllancamentoultandam->y16_codnoti = $nl01_codlanc;
                    $cllancamentoultandam->excluir($nl01_codlanc);

                    $cllancamentoultandam->nl20_codandam = $clfandam->y39_codandam;
                    $cllancamentoultandam->nl20_codlanc  = $nl01_codlanc;
                    $cllancamentoultandam->incluir($nl01_codlanc, $clfandam->y39_codandam);

                    $cllancamentoandam->incluir($nl01_codlanc, $clfandam->y39_codandam);

                    if($cllancamentoultandam->erro_status==0){
                        $erro_msg = $cllancamentoultandam->erro_msg;
                        $sqlerro = true;
                    }

                    if (isset($sequencial) and $sequencial != '') {
                        $cldatacienciaandamento->codigo_peca = $nl01_codlanc;
                        $cldatacienciaandamento->data_ciencia = $data_ciencia;
                        $cldatacienciaandamento->fandam = $clfandam->y39_codandam;
                        $cldatacienciaandamento->alterar($sequencial);
                    } else {
                        $cldatacienciaandamento->codigo_peca = $nl01_codlanc;
                        $cldatacienciaandamento->data_ciencia = $data_ciencia;
                        $cldatacienciaandamento->fandam = $clfandam->y39_codandam;
                        $cldatacienciaandamento->incluir();
                    }

                    if($cldatacienciaandamento->erro_status == 0){
                        $erro_msg = $cldatacienciaandamento->erro_msg;
                        $sqlerro = true;
                    }

                    $rsProcFiscal  = $clprocfiscallanc->sql_record($clprocfiscallanc->sql_query(null,"*",null,"nl09_lanc = $nl01_codlanc"));

                    db_fieldsmemory($rsProcFiscal,0);

                    if ($clprocfiscallanc->numrows > 0 && ($y100_dtinicial == '' or $y100_dtinicial == null)) {
                        $iUpdate = 0;
                        $sFandam  = "select sequencial as seq from fiscalizacao.fis_grupotipoandamento ";
                        $sFandam .= " inner join fiscalizacao.fis_grupotipoandamento_tipoandam on fi30_grupo = sequencial ";
                        $sFandam .= " inner join fiscalizacao.fis_tipoandam on fi30_tipoandam = y41_codtipo ";
                        $sFandam .= " where y41_codtipo = $y39_codtipo";
                        $rsFandam = db_query($sFandam);
                        if (pg_num_rows($rsFandam) > 0) {
                            for($i = 0; $i < pg_num_rows($rsFandam); $i++){
                                db_fieldsmemory($rsFandam, $i);
                                if ($seq == 8 || $seq == 22) {// 8 - IMPLANTADO AUTO & 22 - IMPLANTADO NOTIFICAÇÃO DE LANÇAMENTO
                                    $iUpdate = 1;
                                    break;
                                }
                            }
                        }

                        if ($iUpdate == 1){
                            $sDataCiencia = $data_ciencia;
                            $sDataProcFiscal = " update fiscalizacao.fis_procfiscal set y100_dtinicial = '$sDataCiencia' ";
                            $sDataProcFiscal .= " where y100_sequencial =  $y100_sequencial ";
                            db_query($sDataProcFiscal);

                            $sql = "INSERT INTO fiscalizacao.fis_processoprorrogacaofinalizacao (
                                        processo_fiscal,
                                        data_abertura,
                                        data_prorrogacao,
                                        data_fim,
                                        id_usuario,
                                        situacao,
                                        observacao
                                        ) VALUES (
                                        ".$y100_sequencial.",
                                        '".date("Y-m-d")."',
                                        null,
                                        '".date("Y-m-d",strtotime("+31 days",strtotime($sDataCiencia)))."',
                                        ".db_getsession('DB_id_usuario').",
                                        0,
                                        ''
                                        )";

                            $rsInsert = db_query($sql);
                            if($rsInsert == false){
                                $sqlerro = true;
                                $erro_msg = "Erro ao Incluir Data Final do Processo Fiscal.".pg_last_error();
                            }
                        }
                    }
                }

                if($sqlerro == false){
                    if ($y60_proces != '') {
                        $clprocessoandam->pa01_codandam = $clfandam->y39_codandam;
                        $clprocessoandam->pa01_processo = $y60_proces;
                        $clprocessoandam->incluir();
                        if($clprocessoandam->erro_status == 0){
                            $sqlerro = true;
                            $erro_msg = $clprocessoandam->erro_msg;
                        }
                    }
                }
            }
        }
    }
    if($sqlerro == true){
        db_msgbox($erro_msg);
    } else {
        db_atutermometro($bcd, count($arrItem), 'termometro');
        db_msgbox("Andamento processado com Sucesso!");
    }

    db_fim_transacao($sqlerro);
}
?>
