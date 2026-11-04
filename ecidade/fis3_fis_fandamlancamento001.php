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

require_once Modification("libs/db_stdlib.php");
require_once Modification("libs/db_conecta.php");
require_once Modification("libs/db_sessoes.php");
require_once Modification("libs/db_usuariosonline.php");
require_once Modification("classes/db_fis_lancamento_classe.php");
require_once Modification("classes/db_fis_lanctipo_classe.php");
require_once Modification("classes/db_fis_lancandam_classe.php");
require_once Modification("classes/db_fis_lancultandam_classe.php");
require_once Modification("classes/db_fis_fandam_classe.php");
require_once Modification("classes/db_fis_fandamusu_classe.php");
require_once Modification("classes/db_fis_lancusu_classe.php");
require_once Modification("classes/db_fis_lanclocal_classe.php");
require_once Modification("classes/db_fis_lancexec_classe.php");
require_once Modification("classes/db_fis_fiscalparametros_classe.php");
require_once Modification("classes/db_fis_processoandam_classe.php");
require_once Modification('classes/db_fis_datacienciaandamento_classe.php');
require_once Modification('classes/db_fis_lancnumpre_classe.php');
require_once Modification('classes/db_fis_procfiscallanc_classe.php');
require_once Modification("dbforms/db_funcoes.php");
require_once Modification("libs/db_utils.php");

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
db_postmemory($HTTP_POST_VARS);

if( !isset( $abas ) ){
    echo "<script>location.href='fis3_fis_fandamlancamento005.php'</script>";
    exit;
}

$clrotulo               = new rotulocampo;
$cllancamento           = new cl_fis_lancamento;
$cllanctipo             = new cl_fis_lanctipo;
$cllancamentoandam      = new cl_fis_lancandam;
$cllancamentoultandam   = new cl_fis_lancultandam;
$clfandam               = new cl_fis_fandam;
$clfandamusu            = new cl_fis_fandamusu;
$cllancusu              = new cl_fis_lancusu;
$cllanclocal            = new cl_fis_lanclocal;
$cllancexec             = new cl_fis_lancexec;
$clfiscalparametros     = new cl_fis_fiscalparametros;
$clprocessoandam        = new cl_fis_processoandam;
$cldatacienciaandamento = new cl_fis_datacienciaandamento;
$clprocfiscallanc       = new cl_fis_procfiscallanc;

// Classes para calculo
$oDaoLancamento = db_utils::getDao("fis_lancamento");
$oDaoArrecad    = db_utils::getDao('arrecad');
$oDaoArrecant   = db_utils::getDao('arrecant');
$oDaoLancnumpre = db_utils::getDao("fis_lancnumpre");

$clrotulo->label("y39_codandam");
$clrotulo->label("nl01_codlanc");

$db_opcao = 1;
$db_botao = true;
$auto     = 1;
$bloqueia = $db_opcao;

if(isset($nl01_codlanc) && !isset($HTTP_POST_VARS["db_opcao"])){

    // verificar se tem processo fiscal para aquele auto, se houver ele bloqueia a alteração do processo administrativo
    $sProcFiscal  = $clprocfiscallanc->sql_query(null,"*",null,"nl09_lanc = $nl01_codlanc");
    $rsProcFiscal = db_query($sProcFiscal);
    if( pg_num_rows( $rsProcFiscal ) > 0 ){
       $bloqueia = 3;
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

    $db_opcao = 3;

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
        $db_opcao = 1;
        db_msgbox("Não existem fiscais cadastrados para está Notificação de Lançamento de infração!");
        require_once Modification("fis3_fis_fandamlancamento004.php");
        exit;
    }

    if (isset($y39_codtipo) && $y39_codtipo != ''){

       $sSqlTipoAndam = " select * from fiscalizacao.fis_tipoandam where y41_codtipo = $y39_codtipo ";

       $rsTipoAndam = db_query($sSqlTipoAndam);
       if (pg_num_rows($rsTipoAndam) > 0) {
           db_fieldsmemory($rsTipoAndam, 0);
       }
    }
    $db_botao = false;

}

if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Incluir"){

    db_inicio_transacao();
    $sqlerro = false;
    $erro_msg = 'Andamento incluído com sucesso.';

    $sSqlTipoAndam = " select * from fiscalizacao.fis_tipoandam where y41_codtipo = $y39_codtipo ";
    $rsTipoAndam = db_query($sSqlTipoAndam);
    if (pg_num_rows($rsTipoAndam) > 0) {
       db_fieldsmemory($rsTipoAndam, 0);
    }

        $importar = false;
        if($y41_permcalc == 't'){

            $cllancamento->nl01_dtvenc   = $y50_dtvenc_ano."-".$y50_dtvenc_mes."-".$y50_dtvenc_dia;
            $cllancamento->nl01_prazorec = $y50_prazorec_ano."-".$y50_prazorec_mes."-".$y50_prazorec_dia;
            $cllancamento->nl01_data 	 = $data_ciencia;
            $cllancamento->alterar($nl01_codlanc);
            if ($cllancamento->erro_status==0){
                $erro_msg = $cllancamento->erro_msg;
                $sqlerro  = true;
            }

            $result = $oDaoLancnumpre->sql_record($oDaoLancnumpre->sql_query_precalculo(null,"*",null,"nl17_codlanc = {$nl01_codlanc}"));
            $rsAutoLEVT = db_query(" select * from fiscalizacao.fis_lanclevanta where nl15_lancamento = {$nl01_codlanc} ");
            if($oDaoLancnumpre->numrows == 0 && pg_num_rows( $rsAutoLEVT ) > 0 ){
              $erro_msg = "Notificação de Lançamento sem pré cálculo, não pode ser implantado!";
              $sqlerro  = true;
            }

            if($sqlerro == false){

                $sDataCalc = explode('/', $data_ciencia);
                $sDataCalc = $sDataCalc[2].'-'.$sDataCalc[1].'-'.$sDataCalc[0];
                $rsCalculo = db_query("select fc_fis_lancamento_andam($nl01_codlanc, '$sDataCalc')");

                if (!$rsCalculo){
                    $erro_msg = "Occorreu um erro ao calcular a notificação de lançamento {$nl01_codlanc}.";
                    $sqlerro  = true;
                 }

                $sInfo = db_utils::fieldsmemory($rsCalculo, 0)->fc_fis_lancamento_andam;

                if(substr(trim($sInfo), 0, 3) == '001'){
                    $importar = true;
                    $erro_msg .= '\n' . $sInfo;
                }else{
                    $erro_msg = $sInfo;
                    $sqlerro  = true;
                }

            }
        }


        // //* Se andamento permitir calculo efetua a rotina de exportação dos levantamentos *\\

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
                    //--------------------------------------------------

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

                    // if($sqlerro == false){

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
                    // }
                    // echo db_msgbox($sql; exit;

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
    }

    if ($sqlerro==false) {
        $clfandam->incluir($y39_codandam);
        if($clfandam->erro_status == 0){
            $erro_msg = $clfandam->erro_msg;
            $sqlerro = true;
        }

        $cllancamentoultandam->y16_codnoti = $nl01_codlanc;
        $cllancamentoultandam->excluir($nl01_codlanc);
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

        $sProcFiscal  = $clprocfiscallanc->sql_query(null,"*",null,"nl09_lanc = $nl01_codlanc");
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
                        $aDataCiencia = explode('/', $data_ciencia);
                        $sDataCiencia = $aDataCiencia[2].'-'.$aDataCiencia[1].'-'.$aDataCiencia[0];
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
                        // die($sql);
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

    db_fim_transacao($sqlerro);

}

if(!isset($pri)){
    require_once Modification("fis3_fis_fandamlancamento004.php");
    exit;
}

?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" bgcolor="#cccccc" marginheight="0" onLoad="a=1" >
    <table width="790" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td height="40" align="center" valign="top" bgcolor="#CCCCCC">
                <fieldset>
                    <legend align="center">NOTIFICAÇÃO DE LANÇAMENTO</legend>
                    <center>
                        <?php
                        db_ancora("<b>Notificação de Lançamento:</b>","js_auto(true);",3);
                        db_input('nl01_codlanc',20,$Inl01_codlanc,true,'text',3,"");

                        ?>
                    </center>
                </fieldset>
            </td>
        </tr>
        <tr>
            <td height="100%" align="center" width="100%" valign="top" bgcolor="#CCCCCC">
                <fieldset>
                    <legend align="center">ANDAMENTO</legend>
                    <center>
                        <?php
                        $db_opcao=1;
                        $db_botao = true;

                        require_once(modification(Modification::getFile("forms/db_frm_fis_fandam.php")));
                        ?>
                    </center>
                </fieldset>
            </td>
        </tr>
    </table>
</body>
</html>
<?php
if((isset($HTTP_POST_VARS["db_opcao"]) && $HTTP_POST_VARS["db_opcao"])=="Incluir"){
;
    if($clfandam->erro_status=="0"){
        $clfandam->erro(true,false);
        $db_botao=true;
        if($clfandam->erro_campo!=""){
            echo "<script> document.form1.".$clfandam->erro_campo.".style.backgroundColor='#99A9AE';</script>";
            echo "<script> document.form1.".$clfandam->erro_campo.".focus();</script>";
        }
    }else{
        if($sqlerro == false){;
           db_msgbox($erro_msg);
	       echo "<script>parent.parent.corpo.location.href='fis3_fis_fandamlancamento001.php';</script>";
        }else{
            db_msgbox($erro_msg);
            echo "<script>document.getElementById('y39_codtipo').value = '';</script>";
            echo "<script>document.getElementById('y41_descr').value = '';</script>";

            echo "<script>document.getElementById('nl01_dtvenc_tmp').value = '';</script>";
            echo "<script>document.getElementById('nl01_dtvenc_tmp_dia').value = '';</script>";
            echo "<script>document.getElementById('nl01_dtvenc_tmp_mes').value = '';</script>";
            echo "<script>document.getElementById('nl01_dtvenc_tmp_ano').value = '';</script>";

            echo "<script>document.getElementById('nl01_dtvenc').value = '';</script>";
            echo "<script>document.getElementById('nl01_dtvenc_dia').value = '';</script>";
            echo "<script>document.getElementById('nl01_dtvenc_mes').value = '';</script>";
            echo "<script>document.getElementById('nl01_dtvenc_ano').value = '';</script>";

            echo "<script>document.getElementById('nl01_prazorec').value = '';</script>";
            echo "<script>document.getElementById('nl01_prazorec_dia').value = '';</script>";
            echo "<script>document.getElementById('nl01_prazorec_mes').value = '';</script>";
            echo "<script>document.getElementById('nl01_prazorec_ano').value = '';</script>";
        }
    }
}
?>
<script>
    function js_auto(mostra){
        var auto=document.form1.nl01_codlanc.value;
        js_OpenJanelaIframe('','db_iframe','fis3_fis_lancamento006.php?nl01_codlanc='+auto,'Consulta',true,0);
  }
</script>
