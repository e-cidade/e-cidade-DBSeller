<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009 DBSeller Servicos de Informatica
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
require_once  modification("classes/db_fis_levusu_classe.php");
require_once  modification("classes/db_fis_levantlotearq_classe.php");
require_once  modification("classes/db_fis_levantlotearqcont_classe.php");
require_once  modification("classes/db_fis_levantlotearqparagrafo_classe.php");
require_once  modification("classes/db_protprocesso_classe.php");
require_once  modification("classes/db_issbase_classe.php");

require_once  modification("classes/db_fis_procfiscal_estendida_classe.php");
require_once  modification("classes/db_fis_procfiscalinscr_classe.php");
require_once  modification("classes/db_fis_procfiscalprot_classe.php");
require_once  modification("classes/db_fis_dataprocfiscal_classe.php");
require_once  modification("classes/db_fis_procfiscalcgm_classe.php");
require_once  modification("classes/db_fis_procfiscalauto_classe.php");
require_once  modification("classes/db_fis_procfiscallanc_classe.php");
require_once  modification("classes/db_fis_procfiscalfiscais_classe.php");
require_once  modification("classes/db_fis_auto_classe.php");
require_once  modification("classes/db_fis_autoinscr_classe.php");
require_once  modification("classes/db_fis_autousu_classe.php");
require_once  modification("classes/db_fis_autolocal_classe.php");
require_once  modification("classes/db_fis_autoexec_classe.php");
require_once  modification("classes/db_fis_autorespons_classe.php");
require_once  modification("classes/db_fis_autolevanta_classe.php");
require_once  modification("classes/db_fis_fiscalusuario_classe.php");
require_once  modification('classes/db_fis_processofiscalativo_classe.php');
require_once  modification('classes/db_fis_fiscalparagrafoauto_classe.php');

require_once  modification("classes/db_fis_levanta_classe.php");
require_once  modification("classes/db_fis_levinscr_classe.php");
require_once  modification("classes/db_fis_procfiscallevanta_classe.php");
require_once  modification("classes/db_fis_levvalor_classe.php");
require_once  modification("classes/db_fis_levvalorpgtos_classe.php");

require_once  modification("classes/db_fis_autotipo_classe.php");
require_once  modification("classes/db_fis_autoandam_classe.php");
require_once  modification("classes/db_fis_autoultandam_classe.php");
require_once  modification("classes/db_fis_fandam_classe.php");
require_once  modification("classes/db_fis_autorec_classe.php");
require_once  modification("classes/db_fis_fiscalprocrec_classe.php");
require_once  modification("classes/db_issbase_classe.php");
require_once  modification("classes/db_cgm_classe.php");

require_once  modification("classes/db_fis_lancamento_classe.php");
require_once  modification("classes/db_fis_lanclevanta_classe.php");
require_once  modification("classes/db_fis_lancinscr_classe.php");
require_once  modification("classes/db_fis_lanclocal_classe.php");
require_once  modification("classes/db_fis_lancexec_classe.php");
require_once  modification("classes/db_fis_paragrafolanc_classe.php");
require_once  modification("classes/db_fis_lanctipo_classe.php");
require_once  modification("classes/db_fis_lancrec_classe.php");
require_once  modification("classes/db_fis_lancrespons_classe.php");
require_once  modification("classes/db_fis_lancusu_classe.php");
require_once  modification("classes/db_fis_lancandam_classe.php");
require_once  modification("classes/db_fis_lancultandam_classe.php");
require_once  modification("classes/db_fis_lancmulta_classe.php");

db_postmemory($_POST);

$erro   = false;
$instit = db_getsession("DB_instit");

$cllevantlotearq     = new cl_fis_levantlotearq;
$cllevantlotearqcont = new cl_fis_levantlotearqcont;
$cllevantlotearqpar  = new cl_fis_levantlotearqpar;
$cldb_config         = new cl_db_config;
$clprotprocesso      = new cl_protprocesso;
$cllevanta           = new cl_fis_levanta;
$cllevinscr          = new cl_fis_levinscr;
$clprocfiscallevanta = new cl_fis_procfiscallevanta;
$clissbase           = new cl_issbase;
$clprocfiscal        = new cl_fis_procfiscal_estendida;
$clprocfiscalinscr   = new cl_fis_procfiscalinscr;
$clprocfiscalcgm     = new cl_fis_procfiscalcgm;
$clprocfiscalprot    = new cl_fis_procfiscalprot;
$cldataprocfiscal    = new cl_fis_dataprocfiscal;
$clprocfiscalauto    = new cl_fis_procfiscalauto;
$clprocfiscallanc    = new cl_fis_procfiscallanc;
$clprocfiscalfiscais = new cl_fis_procfiscalfiscais;
$clautousu           = new cl_fis_autousu;
$cllevvalor          = new cl_fis_levvalor;
$cllevvalorpgtos     = new cl_fis_levvalorpgtos;
$clauto              = new cl_fis_auto;
$clautoinscr         = new cl_fis_autoinscr;
$clautolevanta       = new cl_fis_autolevanta;
$clautolocal         = new cl_fis_autolocal;
$clautoexec          = new cl_fis_autoexec;
$clautotipo          = new cl_fis_autotipo;
$clautoandam         = new cl_fis_autoandam;
$clfandam            = new cl_fis_fandam;
$clautoultandam      = new cl_fis_autoultandam;
$clfiscalprocrec     = new cl_fis_fiscalprocrec;
$clautorec           = new cl_fis_autorec;
$clcgm               = new cl_cgm;
$clautorespons       = new cl_fis_autorespons;
$cllevusu            = new cl_fis_levusu;
$clfiscalusuario        = new cl_fis_fiscalusuario;
$clprocessofiscalativo  = new cl_fis_processofiscalativo;
$clparagrafoauto        = new cl_fis_fiscalparagrafoauto;
$cllancamento        = new cl_fis_lancamento;
$cllanclevanta       = new cl_fis_lanclevanta;
$cllancinscr         = new cl_fis_lancinscr;
$cllanclocal         = new cl_fis_lanclocal;
$cllancexec          = new cl_fis_lancexec;
$clparagrafolanc     = new cl_fis_paragrafolanc;
$cllanctipo          = new cl_fis_lanctipo;
$cllancrec           = new cl_fis_lancrec;
$cllancrespons       = new cl_fis_lancrespons;
$cllancusu           = new cl_fis_lancusu;
$cllancandam         = new cl_fis_lancandam;
$cllancultandam      = new cl_fis_lancultandam;
$cllancmulta         = new cl_fis_lancmulta;

$db_opcao = 1;
$db_botao = true;
$situacao = 0;

$iInstitSessao = db_getsession("DB_instit");
$result = $cldb_config->sql_record($cldb_config->sql_query_file($iInstitSessao, "cgc"));
db_fieldsmemory($result, 0);

$erro = false;

if (isset($processar)) {

    $campos = "";
    $msgtipoarq = "";
    if($y122_pecafiscal == 0){
        $campos .= " peça fiscal";
        $erro = true;
    }
    if($y122_data == ""){
        if($erro == true){
            $campos .= ",";
        }
        $campos .= " data";
        $erro = true;
    }
    if($y122_hora == ""){
        if($erro == true){
            $campos .= ",";
        }
        $campos .= " hora";
        $erro = true;
    }
    if($y122_procedencia == ""){
        if($erro == true){
            $campos .= ",";
        }
        $campos .= " procedência";
        $erro = true;
    }

    /*$verificarPer = $clfiscalprocrec->sql_query_file($y122_procedencia);
    $rsVerificarPer = $clfiscalprocrec->sql_record($verificarPer);

    if($clfiscalprocrec->numrows > 0){
        if($y122_valor == ""){
            if($erro == true){
                $campos .= ",";
            }
            $campos .= " valor";
            $erro = true;
        }
    }*/

    if($y122_procedencia != '193'){
        if($y122_valor == ""){
            if($erro == true){
                $campos .= ",";
            }
            $campos .= " valor";
            $erro = true;
        }
    }

    db_postmemory($_FILES["arquivo"]);
    $arq_name    = basename($name);
    $arq_type    = $type;
    $arq_size    = $size;
    $arq_array   = file($tmp_name);
    $sMd5Arquivo = md5(file_get_contents($tmp_name));

    if(basename($error) > 0) {
        if($erro == true){
            $campos .= ",";
        }
        $campos .= " arquivo ";
        $erro = true;
    }

    if(strtolower(substr($arq_name,-3)) != "csv"){
        $msgtipoarq = "\nÉ necessário um arquivo do tipo CSV (*.csv)!";
        $erro = true;
    }
    if($erro == true){
        $msgerro = "Informe o(s) campo(s): $campos.";
        db_msgbox($msgerro.$msgtipoarq);
    } else {
        system("cp -f ".$tmp_name." ".$DOCUMENT_ROOT."/tmp/".$arq_name);
        global $arq_array;
        $arq_array = file($DOCUMENT_ROOT."/tmp/".$arq_name);
    }
}
$_debug = false;
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<?php
db_app::load("scripts.js, strings.js, prototype.js, estilos.css, EmissaoRelatorio.js");
?>
</head>
<body class="body-default" onLoad="a=1">
<?php
  include modification("forms/db_frm_fis_processalevlote.php");
  db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<?php
if (isset($processar) and (isset($arq_name))) {

    if($erro == false){
        db_postmemory($_FILES["arquivo"]);
        $arq_array   = file($tmp_name);

        global $arq_array;
    }

    unset($processar);

    $totalproc = count($arq_array);
    $sqlerro   = false;

    db_criatermometro('termometro', 'Concluido...', 'blue', 1);
    flush();

    db_inicio_transacao();
    /**
     * Verifica se arquivo já foi importado
     */
    $sSqlArquivoImportado = $cllevantlotearq->sql_query_file(null, 'true', null, "y122_md5 = '$sMd5Arquivo'");
    $rsArquivoImportado   = $cllevantlotearq->sql_record($sSqlArquivoImportado);

    if ($cllevantlotearq->numrows > 0) {
        $erro_msg = "Arquivo já importado!";
        $sqlerro  = true;
    }

    if ($_debug) {
        echo "  >> Arquivo CSV - Passo 1 -  Inclusão do LevantLoteArq <br>";
        flush();
    }

    /* Inclusão na fis_levantlotearq */
    $cllevantlotearq->y122_pecafiscal    = $y122_pecafiscal;
    $cllevantlotearq->y122_data          = $y122_data_ano.'/'.$y122_data_mes.'/'.$y122_data_dia;
    $cllevantlotearq->y122_hora          = $y122_hora;
    $cllevantlotearq->y122_tipofiscal    = $y122_tipofiscal;
    $cllevantlotearq->y122_usuario       = db_getsession("DB_id_usuario");
    $cllevantlotearq->y122_observacao    = $y122_observacao;
    $cllevantlotearq->y122_procedencia   = $y122_procedencia;
    $cllevantlotearq->y122_valor         = $y122_valor;
    $cllevantlotearq->y122_instit        = $iInstitSessao;
    $cllevantlotearq->y122_nomearq       = trim($arq_name);
    $cllevantlotearq->y122_md5           = $sMd5Arquivo;
    $cllevantlotearq->incluir(null);
    if ($_debug) {
        var_dump($cllevantlotearq);echo "<br />";
        flush();
    }
    if ($cllevantlotearq->erro_status == "0") {
        db_msgbox("Ocorreu um erro ao registrar o Arquivo do Levantamento - cllevantlotearq ");
        $sqlerro  = true;
    } else {
        $y122_codigo = $cllevantlotearq->y122_codigo;
    }

    $errolinha = "";
    for ($abc=0; $abc < $totalproc; $abc++) {
        if($abc != 0) {

            $linha_arquivo = $arq_array[$abc];
            $dados_arquivo = explode(";",$linha_arquivo);

            if(count($dados_arquivo) != 24){
                $sqlerrolinha  = true;
                $errolinha .= $abc.", ";
                continue;
            } else {
                if ($_debug) {
                    echo "  >> Arquivo CSV - Passo 2 -  Inclusão no LevantLoteArqCont linha: $abc<br>";
                    flush();
                }
                if(!empty($dados_arquivo[12]) && empty($dados_arquivo[13])) {
                    db_msgbox("Informe a data de pagamento na linha $abc!");
                    $sqlerro  = true;
                }

                if($sqlerro==false) {
                    /* Inclusão na fis_levantlotearqcont */
                    $cllevantlotearqcont->y123_levantlotearq = $y122_codigo;
                    $cllevantlotearqcont->y123_inscr         = trim($dados_arquivo[0]);
                    $cllevantlotearqcont->y123_processo      = trim($dados_arquivo[1]);
                    $cllevantlotearqcont->y123_acaofiscal    = trim($dados_arquivo[2]);
                    $cllevantlotearqcont->y123_codfiscal     = trim($dados_arquivo[3]);
                    $cllevantlotearqcont->y123_levcontato    = substr(strtoupper(trim($dados_arquivo[4])),0,100);
                    $cllevantlotearqcont->y123_dtlevanta     = trim($dados_arquivo[5]);
                    $cllevantlotearqcont->y123_perinicial    = trim($dados_arquivo[6]);
                    $cllevantlotearqcont->y123_perfinal      = trim($dados_arquivo[7]);
                    $cllevantlotearqcont->y123_anocomp       = trim($dados_arquivo[8]);
                    $cllevantlotearqcont->y123_mescomp       = trim($dados_arquivo[9]);
                    $cllevantlotearqcont->y123_bruto         = trim(str_replace(",",".",str_replace(".","",$dados_arquivo[10])));
                    $cllevantlotearqcont->y123_aliquota      = trim(str_replace(",",".",$dados_arquivo[11]));
                    $cllevantlotearqcont->y123_pago          = (empty($dados_arquivo[12]) ? 'null' : trim(str_replace(",",".",str_replace(".","",$dados_arquivo[12]))));
                    $cllevantlotearqcont->y123_dtpago        = (empty($dados_arquivo[13]) ? null : trim($dados_arquivo[13]));
                    $cllevantlotearqcont->y123_descricao     = substr(trim($dados_arquivo[14]),0,500);
                    $cllevantlotearqcont->y123_tprespons     = (empty($dados_arquivo[15]) ? 'null' : trim($dados_arquivo[15]));
                    $cllevantlotearqcont->y123_nomecomple    = substr(strtoupper(trim($dados_arquivo[16])),0,100);
                    $cllevantlotearqcont->y123_cgccpf        = substr(trim($dados_arquivo[17]),0,14);
                    $cllevantlotearqcont->y123_ender         = substr(strtoupper(trim($dados_arquivo[18])),0,100);
                    $cllevantlotearqcont->y123_numero        = (empty($dados_arquivo[19]) ? 'null' : trim($dados_arquivo[19]));
                    $cllevantlotearqcont->y123_bairro        = substr(strtoupper(trim($dados_arquivo[20])),0,40);
                    $cllevantlotearqcont->y123_munic         = substr(strtoupper(trim($dados_arquivo[21])),0,40);
                    $cllevantlotearqcont->y123_uf            = substr(strtoupper(trim($dados_arquivo[22])),0,2);
                    $cllevantlotearqcont->y123_cep           = substr(trim($dados_arquivo[23]),0,8);
                    $cllevantlotearqcont->incluir(null);
                    if ($_debug) {
                        var_dump($cllevantlotearqcont);echo "<br />";
                        flush();
                    }
                    if ($cllevantlotearqcont->erro_status == "0") {
                        db_msgbox("Ocorreu um erro ao registrar o Conteúdo do Arquivo do Levantamento - cllevantlotearqcont linha: $abc");
                        $sqlerro  = true;
                    }
                }
            }
        }
    }

    if ($_debug) {
        echo "  >> Arquivo CSV - Passo 3 -  Inclusão no LevantLoteArqParagrafo <br>";
        flush();
    }
    /* Inclusão na fis_levantlotearqparagrafo */
    if($sqlerro==false) {
        $rowParagrafo = count($paragrafoTipo);
        for($bcd=0; $bcd < $rowParagrafo; $bcd++){
            $cllevantlotearqpar->y124_levantlotearq     = $y122_codigo;
            $cllevantlotearqpar->y124_paragrafo         = $paragrafoTipo[$bcd];
            $cllevantlotearqpar->y124_texto             = $paragrafoTexto[$bcd];
            $cllevantlotearqpar->incluir(null);
        }
        if ($_debug) {
            var_dump($cllevantlotearqpar);echo "<br />";
            flush();
        }
        if ($cllevantlotearqpar->erro_status == "0") {
            $erro_msg = "Ocorreu um erro ao registrar o(s) Parágrado(s) do Arquivo do Levantamento - cllevantlotearqpar ";
            $sqlerro  = true;
        }
    }

    if($sqlerro==false) {
        $camposLev = "distinct y123_inscr as inscr, y123_processo as processo, y123_acaofiscal as acaofiscal , y123_codfiscal as fiscal, y123_levcontato as contato, y123_dtlevanta as datalevanta, y123_perinicial as perinicial, y123_perfinal as perfinal, y123_tprespons, y123_nomecomple, y123_cgccpf, y123_ender, y123_numero, y123_bairro, y123_munic, y123_uf, y123_cep ";
        /* Buscar Levantamentos por Inscrição gravados no Banco */
        $sSqlLevantamentoLote = $cllevantlotearqcont->sql_query_file(null, $camposLev, "", "y123_levantlotearq = $y122_codigo ");
        //echo $sSqlLevantamentoLote."<br />";
        $rsLevantamentoLote   = $cllevantlotearqcont->sql_record($sSqlLevantamentoLote);

        if ($cllevantlotearqcont->numrows == 0) {
            $erro_msg = "Erro ao buscar os dados do Levantamento Lote!";
            $sqlerro  = true;
        }
    }

    $inscsemend = "";
    $inscbaixad = "";
    /* Processo de Inserção nas Tabelas devidas */
    for($cde = 0; $cde < pg_num_rows($rsLevantamentoLote); $cde++){

        db_atutermometro($cde, pg_num_rows($rsLevantamentoLote), 'termometro');

        $codproc = "";
        $numcgm  = "";

        db_fieldsmemory($rsLevantamentoLote, $cde);
        $eFiscal = explode(',', $fiscal);

        if ($_debug) {
            echo "  >>  passo 3  - Busca Dados Levantamento Lote - Inscrição $inscr <br>";
            flush();
        }
        $sBaixaInscr  = "SELECT * FROM issbase where q02_inscr = $inscr AND q02_dtbaix IS NOT NULL";
        $rsBaixaInscr = db_query($sBaixaInscr);

        if(pg_num_rows($rsBaixaInscr) > 0){
            $inscbaixad .= $inscr.", ";
            continue;
        }

        if($sqlerro==false) {
            if($processo != ""){
                $dadosProc = explode("/",$processo);
                $numProc   = $dadosProc[0];
                $anoProc   = $dadosProc[1];
                $sSqlLevantProcesso = $clprotprocesso->sql_query_file(null, "p58_codproc", "", "p58_numero = '$numProc' and p58_ano = $anoProc");
                $rsLevantProcesso   = $clprotprocesso->sql_record($sSqlLevantProcesso);
                if(pg_num_rows($rsLevantProcesso) > 0){
                    $codproc = pg_result($rsLevantProcesso,0);
                } else {
                    $erro_msg = "Erro ao buscar o Processo:".$processo;
                    $sqlerro  = true;
                }
            } else {
                $erro_msg = "Processo Administrativo não informado!";
                $sqlerro  = true;
            }

            /* REGISTRA PROCESSO FISCAL */
            $sSqlCgmInsc = $clissbase->sql_query_file(null, "q02_numcgm", "", "q02_inscr = $inscr");
            $rsCgmInsc   = $clissbase->sql_record($sSqlCgmInsc);

            if(pg_num_rows($rsCgmInsc) > 0){
                $numcgm = pg_result($rsCgmInsc,0);
            } else {
                $erro_msg = "Erro ao buscar o Cgm da Inscrição: $inscr .";
                $sqlerro  = true;
            }
        }

        if($sqlerro==false) {
            if ($_debug) {
                echo "  >>  01 - Processo Fiscal - passo 1  -  Registra Processo Fiscal <br>";
                flush();
            }

            $clprocfiscal->y100_coddepto = db_getsession("DB_coddepto");
            $clprocfiscal->y100_instit   = db_getsession("DB_instit");
            $clprocfiscal->y100_procfiscalcadtipo = $acaofiscal;
            $clprocfiscal->y100_dtinicial = $y123_dtlevanta;
            $clprocfiscal->incluir(null);
            if ($_debug) {
                var_dump($clprocfiscal);echo "<br />";
                flush();
            }
            if($clprocfiscal->erro_status==0){
                $erro_msg = "Ocorreu um erro ao registrar o Processo Fiscal - clprocfiscal ";
                $sqlerro  = true;
            } else {
                $y100_sequencial = $clprocfiscal->y100_sequencial;
            }
        }

        if($sqlerro==false) {
            if ($_debug) {
                echo "  >>  01 - Processo Fiscal - passo 2 - Registra Inscrição $inscr<br>";
                flush();
            }

            if($sqlerro==false) {
                $clprocfiscalinscr->y103_inscr = $inscr;
                $clprocfiscalinscr->y103_procfiscal = $y100_sequencial;
                $clprocfiscalinscr->incluir(null);
                if ($_debug) {
                    var_dump($clprocfiscalinscr);echo "<br />";
                    flush();
                }
                if($clprocfiscalinscr->erro_status==0){
                    $erro_msg = "Ocorreu um erro ao registrar a Inscrição no Processo Fiscal - clprocfiscalinscr ";
                    $sqlerro  = true;
                }
            }
        }

        if($sqlerro==false) {
            if ($_debug) {
                echo "  >>  01 - Processo Fiscal - passo 3 - Registra CGM $numcgm <br>";
                flush();
            }
            $clprocfiscalcgm->y101_numcgm     = $numcgm;
            $clprocfiscalcgm->y101_procfiscal = $y100_sequencial;
            $clprocfiscalcgm->incluir(null);
            if ($_debug) {
                var_dump($clprocfiscalcgm);echo "<br />";
                flush();
            }
            if($clprocfiscalcgm ->erro_status==0){
                $erro_msg = "Ocorreu um erro ao registrar o CGM da Inscrição no Processo Fiscal - clprocfiscalcgm ";
                $sqlerro  = true;
            }
        }

        if($sqlerro==false) {
            $where = " p58_instit = ".db_getsession("DB_instit") . " and coddepto = ".db_getsession("DB_coddepto");
            $sqlVerificaTipo = "select * from fiscalizacao.fis_tipoprocessoadministrativo";
            $resultVerificaTipo = db_query($sqlVerificaTipo);
            db_fieldsmemory($resultVerificaTipo, 0);
            if (isset($verificatipo) and $verificatipo == 't') {
                $where .= " and p58_codigo in (select protprocesso.p58_codigo from protprocesso ";
                $where .= " inner join tipoproc on protprocesso.p58_codigo = tipoproc.p51_codigo ";
                $where .= " inner join fiscalizacao.fis_parfiscal on tipoproc.p51_codigo = fis_parfiscal.y32_tipoprocpadrao) ";
            }
            $where .= " and p58_codproc not in (SELECT p67_codproc FROM procarquiv INNER JOIN arqandam ON p69_codarquiv = p67_codarquiv and p69_codandam = p58_codandam WHERE p69_arquivado = 't')";
            $where  .= " and p58_codproc =  '{$codproc}'";
            if($sqlerro == false){
                $sql     = $clprotprocesso->sqlGetProcessoAdministrativo("p58_codproc", $where,"p58_codproc desc");
                $rsProt = db_query($sql);
                if(pg_num_rows($rsProt) == 0){
                    $sqlerro = true;
                    $erro_msg = "Processo $processo fora do departamento ou arquivado!";
                }else{
                    db_fieldsmemory($rsProt,0);
                }
            }
            if($sqlerro==false) {
            // processo protocolo
                if($p58_codproc==""){
                    $sqlerro=true;
                    $erro_msg = "Campo processo não informado!";
                }else{

                    if ($_debug) {
                        echo "  >>  01 - Processo Fiscal - passo 4 - Registra Processo Administrativo $p58_codproc<br>";
                        flush();
                    }
                    $clprocfiscalprot->y105_procfiscal   = $y100_sequencial;
                    $clprocfiscalprot->y105_protprocesso = $p58_codproc;
                    $clprocfiscalprot->incluir(null);
                    if ($_debug) {
                        var_dump($clprocfiscalprot);echo "<br />";
                        flush();
                    }
                    if($clprocfiscalprot ->erro_status==0){
                        $erro_msg = "Ocorreu um erro ao registrar o Processo Administratico no Processo Fiscal - clprocfiscalprot ";
                        $sqlerro  = true;
                    }
                }
            }
        }

        if($sqlerro==false) {
            if ($_debug) {
                echo "  >>  01 - Processo Fiscal - passo 5 - Registra Data <br>";
                flush();
            }
            if( $sqlerro == false ){
                $dadosData = explode("/",$y123_dtlevanta);
                $cldataprocfiscal->ypl01_procfiscal = $y100_sequencial;
                $cldataprocfiscal->ypl01_dtlanc_dia = $dadosData[2];
                $cldataprocfiscal->ypl01_dtlanc_mes = $dadosData[1];
                $cldataprocfiscal->ypl01_dtlanc_ano = $dadosData[0];
                $cldataprocfiscal->incluir();
                if ($_debug) {
                    var_dump($cldataprocfiscal);echo "<br />";
                    flush();
                }
                if( $cldataprocfiscal->erro_status == 0 ){
                    $erro_msg = "Ocorreu um erro ao registrar a Data do Processo Fiscal - cldataprocfiscal ";
                    $sqlerro  = true;
                }
            }
        }

        if($sqlerro==false) {
            if ($_debug) {
                echo "  >>  01 - Processo Fiscal - passo 6 - Registra Fiscal $fiscal <br>";
                flush();
            }
            for ($fgh = 0; $fgh < count($eFiscal); ++$fgh) {
                $clprocfiscalfiscais->y106_procfiscal = $y100_sequencial;
                $clprocfiscalfiscais->y106_cadfiscais = trim($eFiscal[$fgh]);
                if (0 == $fgh) {
                    $clprocfiscalfiscais->y106_principal = 'true';
                } else {
                    $clprocfiscalfiscais->y106_principal = 'false';
                }
                if ('' != trim($eFiscal[$fgh])) {
                    $clprocfiscalfiscais->incluir(null);
                }
            }
            if ($_debug) {
                var_dump($clprocfiscalfiscais);echo "<br />";
                flush();
            }
            if($clprocfiscalfiscais->erro_status == '0'){
                $erro_msg = "Ocorreu um erro ao registrar o fiscal ao Processo Fiscal - clprocfiscalfiscais ";
                $sqlerro  = true;
            }
        }

        if($sqlerro==false) {
            if ($_debug) {
                echo "  >>  01 - Processo Fiscal - passo 7 - Registra Fiscal Ativo $fiscal <br>";
                flush();
            }

            for ($ghi = 0; $ghi < count($eFiscal); ++$ghi) {
                $clprocessofiscalativo->processo_fiscal = $y100_sequencial;
                $clprocessofiscalativo->fiscal = trim($eFiscal[$ghi]);
                $clprocessofiscalativo->ativo = 'true';
                if ('' != trim($eFiscal[$ghi])) {
                    $clprocessofiscalativo->incluir();
                }
            }
            if ($_debug) {
                var_dump($clprocessofiscalativo);echo "<br />";
                flush();
            }
            if ($clprocessofiscalativo->erro_status == '0') {
                $erro_msg = "Ocorreu um erro ao registrar o fiscal ativo ao Processo Fiscal - clprocessofiscalativo ";
                $sqlerro  = true;
            }
        }

        /* REGISTRA LEVANTAMENTO */
        if($sqlerro==false) {
            if ($_debug) {
                echo "  >>  02 - Levantamento - passo 1 - Registra Levantamento<br>";
                flush();
            }
            $rsCodLev   = db_query("select nextval('fis_fis_levanta_y60_codlev_seq')");
            $CodLevanta = pg_result($rsCodLev,0,0);
            $cllevanta->y60_data        = $datalevanta;
            $cllevanta->y60_contato     = $contato;
            $cllevanta->y60_dtini       = $perinicial;
            $cllevanta->y60_dtfim       = $perfinal;
            $cllevanta->y60_proces      = $codproc;
            $cllevanta->y60_obs         = strtoupper($y122_observacao);
            $cllevanta->y60_espontaneo  = "false";
            $cllevanta->incluir($CodLevanta);
            if ($_debug) {
                var_dump($cllevanta);echo "<br />";
                flush();
            }
            if($cllevanta->erro_status == 0){
                $erro_msg  = "Ocorreu um erro ao registrar o Levantamento - cllevanta ";
                $sqlerro   = true;
            }

            $y60_codlev = $cllevanta->y60_codlev;
        }

        /* REGISTRA INSCRIÇÃO NO LEVANTAMENTO */
        if($sqlerro==false) {
            if ($_debug) {
                echo "  >>  02 - Levantamento - passo 2 - Registra Inscrição $inscr no Levantamento $y60_codlev<br>";
                flush();
            }
            $cllevinscr->y62_inscr  =$inscr;
            $cllevinscr->y62_codlev =$y60_codlev;
            $cllevinscr->incluir($y60_codlev,$inscr);
            if ($_debug) {
                var_dump($cllevinscr);echo "<br />";
                flush();
            }
            if($cllevinscr->erro_status==0){
                $erro_msg  = "Ocorreu um erro ao registrar a Inscrição no Levantamento - cllevinscr ";
                $sqlerro   = true;
            }
        }

        /* REGISTRA INSCRIÇÃO NO LEVANTAMENTO */
        if($sqlerro==false) {
            if ($_debug) {
                echo "  >>  02 - Levantamento - passo 3 - Registra Usuário no Levantamento - $y60_codlev<br>";
                flush();
            }

            for ($hij = 0; $hij < count($eFiscal); ++$hij) {
                $cllevusu->incluir($y60_codlev, trim($eFiscal[$hij]));
            }
            if ($_debug) {
                var_dump($cllevusu);echo "<br />";
                flush();
            }
            if($cllevusu->erro_status==0){
                $erro_msg  = "Ocorreu um erro ao registrar o Usuário no Levantamento - cllevinscr ";
                $sqlerro   = true;
            }
        }

        /* REGISTRA PROCESSO FISCAL */
        if($sqlerro==false) {
            if ($_debug) {
                echo "  >>  02 - Levantamento - passo 4 - Registra Proc. Fiscal nº $y100_sequencial no Levantamento $y60_codlev<br>";
                flush();
            }
            $clprocfiscallevanta->y112_procfiscal = $y100_sequencial;
            $clprocfiscallevanta->y112_levanta    = $y60_codlev;
            $clprocfiscallevanta->incluir(null);
            if ($_debug) {
                var_dump($clprocfiscallevanta);echo "<br />";
                flush();
            }
            if($clprocfiscallevanta->erro_status == 0){
                $erro_msg  = "Ocorreu um erro ao registrar o Processo Fiscal no Levantamento - clprocfiscallevanta ";
                $sqlerro   = true;
            }
        }
        /* BUSCA ENDEREÇO DA INSCRIÇÃO */
        $sqlEndInscr    = "select j14_codigo as endrua, q02_numero as endnum, q02_compl as endcompl, q13_bairro as endbairro from issruas inner join issbairro on q13_inscr = q02_inscr and q02_inscr = ". $inscr;
        $rsEndInscr     = db_query($sqlEndInscr);
        $endrua         = pg_result($rsEndInscr,0);
        $endnum         = pg_result($rsEndInscr,1);
        $endcompl       = pg_result($rsEndInscr,2);
        $endbairro      = pg_result($rsEndInscr,3);

        if(pg_num_rows($rsEndInscr) == 0){
            $inscsemend .= $inscr.", ";
            continue;
        }

        if ($_debug) {
            echo "  >>  02 - Levantamento - passo 5 - Busca Valores Levantamento Lote - Inscrição $inscr <br>";
            flush();
        }
        $camposLevVal = "distinct  y123_dtlevanta as datalevanta, y123_perinicial as perinicial, y123_perfinal as perfinal, y123_anocomp as anocomp, y123_mescomp as mescomp, y123_bruto as bruto, y123_aliquota as aliquota, y123_pago as pago, y123_dtpago as dtpago, y123_descricao as descricao";
        /* Buscar Valores do Levantamentos por Inscrição gravados no Banco */
        $sSqlLevantamentoLoteVal = $cllevantlotearqcont->sql_query_file(null, $camposLevVal, "", "y123_levantlotearq = $y122_codigo and y123_inscr = $inscr" );
//      echo $sSqlLevantamentoLoteVal."<br />";
        $rsLevantamentoLoteVal   = $cllevantlotearqcont->sql_record($sSqlLevantamentoLoteVal);

        if (pg_num_rows($rsLevantamentoLoteVal) == 0) {
            $erro_msg = "Erro ao buscar os Valores do Levantamento Lote!";
            $sqlerro  = true;
        }

        if($sqlerro==false) {

            for($def = 0; $def < pg_num_rows($rsLevantamentoLoteVal); $def++){

                db_fieldsmemory($rsLevantamentoLoteVal, $def);

                if ($_debug) {
                    echo "  >>  02 - Levantamento - passo 6 - Registra Valor no Levantamento - Levantamento $y60_codlev <br>";
                    flush();
                }

                /* BUSCA DATA DE VENCIMENTO */
                $resultpar      = db_query("select * from parissqn");
                $q60_codvencvar = pg_result($resultpar,0,"q60_codvencvar");
                $sqlvenc    = "select q82_venc,q82_hist from cadvenc where q82_codigo = $q60_codvencvar and q82_parc = $mescomp";
                $resultvenc = db_query($sqlvenc);
                $vencimento = pg_result($resultvenc,0,"q82_venc");
                $res  = db_query("select * from confvencissqnvariavel where q144_ano = $anocomp");
                if(pg_num_rows($res) > 0){
                    $q144_codvenc = pg_result($res,0,"q144_codvenc");
                }else{
                    $erro = "Tabela confvencissqnvariavel vazia!";
                }
                $sqlvenc    = "select q82_venc,q82_hist from cadvenc where q82_codigo = $q144_codvenc and q82_parc = $mescomp";
                $resultvenc = db_query($sqlvenc);
                $vencimento = pg_result($resultvenc,0,"q82_venc");

                $apagar = ($bruto*$aliquota)/100;
                $saldo = ($apagar-$pago);
                $saldo = (trim($saldo) < 0  ? 0 : $saldo);
                $cllevvalor->y63_codlev         = $y60_codlev;
                $cllevvalor->y63_ano            = $anocomp;
                $cllevvalor->y63_mes            = $mescomp;
                $cllevvalor->y63_dtvenc         = $vencimento;
                $cllevvalor->y63_bruto          = $bruto;
                $cllevvalor->y63_aliquota       = $aliquota;
                $cllevvalor->y63_pago           = (($pago == '') ? '0' : $pago);
                $cllevvalor->y63_saldo          = (($saldo > 0) ? $saldo : '0');
                $cllevvalor->y63_histor         = $descricao;
                $cllevvalor->incluir(null);
                if ($_debug) {
                    var_dump($cllevvalor);echo "<br />";
                    flush();
                }
                if($cllevvalor->erro_status == 0){
                    $erro_msg  = "Ocorreu um erro ao registrar o Valor no Levantamento - cllevvalor ";
                    $sqlerro   = true;
                }
                $y63_sequencia = $cllevvalor->y63_sequencia;


                if($sqlerro==false) {
                    if( $pago > 0){
                        if ($_debug) {
                            echo "  >>  02 - Levantamento - passo 6.1 - Registra Pagamento do Valor no Levantamento - Levantamento $y60_codlev - Seq. Valor $y63_sequencia;<br>";
                            flush();
                        }
                        $result55 = $cllevvalorpgtos->sql_record($cllevvalorpgtos->sql_query_file($y63_sequencia,""," max(y68_seq) +1 as seq"));
                        db_fieldsmemory($result55,0);
                        $y68_seq = $seq == ""?"1":$seq;
                        $cllevvalorpgtos->y68_sequencia = $y63_sequencia;
                        $cllevvalorpgtos->y68_seq       = $y68_seq;
                        $cllevvalorpgtos->y68_valor     = $pago;
                        $cllevvalorpgtos->y68_pgto      = $dtpago;
                        $cllevvalorpgtos->incluir($y63_sequencia,$y68_seq);
                        if ($_debug) {
                            var_dump($cllevvalorpgtos);echo "<br />";
                            flush();
                        }
                        if($cllevvalorpgtos->erro_status==0){
                            $erro_msg  = "Ocorreu um erro ao registrar o Pagamento do Valor no Levantamento - cllevvalorpgtos ";
                            $sqlerro   = true;
                        }
                    }
                }
            }
        }


        if($sqlerro==false) {
            /* AUTO DE INFRAÇÃO */
            if($y122_pecafiscal == 1){
                if ($_debug) {
                    echo "  >>  03 - Auto de Infração - passo 1 - Registra o Auto de Infração para o Levantamento - $y60_codlev <br>";
                    flush();
                }

                $clauto->y50_data     = $y122_data_ano.'/'.$y122_data_mes.'/'.$y122_data_dia;
                $clauto->y50_hora     = $y122_hora;
                $clauto->y50_setor    = db_getsession("DB_coddepto");
                $clauto->y50_codtipo  = $y122_tipofiscal;
                $clauto->y50_instit   = db_getsession("DB_instit");
                $clauto->incluir(null);
                if ($_debug) {
                    var_dump($clauto);echo "<br />";
                    flush();
                }
                if ($clauto->erro_status==0){
                    $erro_msg  = "Ocorreu um erro ao registrar o Auto de Infração - clauto ";
                    $sqlerro=true;
                }
                $y50_codauto = $clauto->y50_codauto;

                if($sqlerro==false) {
                    if ($_debug) {
                        echo "  >>  03 - Auto de Infração - passo 2 - Registra o Auto de Infração $y50_codauto com a Inscrição $inscr <br>";
                        flush();
                    }

                    $clautoinscr->y52_inscr = $inscr;
                    $clautoinscr->incluir($y50_codauto);
                    if ($_debug) {
                        var_dump($clautoinscr);echo "<br />";
                        flush();
                    }
                    if($clautoinscr->erro_status==0){
                        $erro_msg = "Ocorreu um erro ao registrar o Auto de Infração com a Inscrição - clautoinscr ";
                        $sqlerro = true;
                    }
                }

                if($sqlerro==false) {
                    if ($_debug) {
                        echo "  >>  03 - Auto de Infração - passo 3 - Registra o Auto de Infração $y50_codauto com o Levantamento $y60_codlev <br>";
                        flush();
                    }

                    $clautolevanta->y117_auto       = $y50_codauto;
                    $clautolevanta->y117_levanta    = $y60_codlev;
                    $clautolevanta->incluir(null);
                    if ($_debug) {
                        var_dump($clautolevanta);echo "<br />";
                        flush();
                    }
                    if($clautolevanta->erro_status==0){
                        $erro_msg = "Ocorreu um erro ao registrar o Auto de Infração com o Levantamento - clautolevanta ";
                        $sqlerro  = true;
                    }
                }

                if($sqlerro==false) {
                    if ($_debug) {
                        echo "  >>  03 - Auto de Infração - passo 4 - Registra o Auto de Infração $y50_codauto com o Endereço da Localização <br>";
                        flush();
                    }

                    $clautolocal->y14_codauto   = $y50_codauto;
                    $clautolocal->y14_codigo    = $endrua;
                    $clautolocal->y14_codi      = $endbairro;
                    $clautolocal->y14_numero    = $endnum;
                    $clautolocal->y14_compl     = $endcompl;
                    $clautolocal->incluir($y50_codauto);
                    if ($_debug) {
                        var_dump($clautolocal);echo "<br />";
                        flush();
                    }
                    if ($clautolocal->erro_status == 0){
                        $erro_msg = "Ocorreu um erro ao registrar o Auto de Infração com o Local - clautolocal ";
                        $sqlerro  = true;
                    }
                }

                if ($sqlerro==false){
                    if ($_debug) {
                        echo "  >>  03 - Auto de Infração - passo 5 - Registra o Auto de Infração $y50_codauto com o Endereço da Execução <br>";
                        flush();
                    }
                    $clautoexec->y15_codauto    = $y50_codauto;
                    $clautoexec->y15_codigo     = $endrua;
                    $clautoexec->y15_codi       = $endbairro;
                    $clautoexec->y15_numero     = $endnum;
                    $clautoexec->y15_compl      = $endcompl;
                    $clautoexec->incluir($clauto->y50_codauto);
                    if ($_debug) {
                        var_dump($clautoexec);echo "<br />";
                        flush();
                    }
                    if ($clautoexec->erro_status==0){
                        $erro_msg = "Ocorreu um erro ao registrar o Auto de Infração com o Exec - clautoexec ";
                        $sqlerro=true;
                    }
                }

                if($sqlerro==false){
                    if ($_debug) {
                        echo "  >>  03 - Auto de Infração - passo 6 - Registra os Parágrafos para o Auto de Infração $y50_codauto <br>";
                        flush();
                    }
                    $rowParagrafo = count($paragrafoTipo);

                    for($efg=0; $efg < $rowParagrafo; $efg++){
                    //  $clparagrafoauto->pl10_codigo    = $paragrafoCod[$efg];
                        $clparagrafoauto->pl10_auto      = $y50_codauto;
                        $clparagrafoauto->pl10_paragrafo = $paragrafoTipo[$efg];
                        $clparagrafoauto->pl10_texto     = $paragrafoTexto[$efg];
                        $clparagrafoauto->pl10_usu       = trim($eFiscal[0]);
                        $clparagrafoauto->incluir();
                        if ($_debug) {
                            var_dump($clparagrafoauto);echo "<br />";
                            flush();
                        }
                        if($clparagrafoauto->erro_status==0){
                            $erro_msg = "Ocorreu um erro ao registrar os Parágrafos no Auto de Infração - clparagrafoauto ";
                            $sqlerro=true;
                        }
                    }
                }

                if($sqlerro==false){
                    if ($_debug) {
                        echo "  >>  03 - Auto de Infração - passo 7 - Registra a Procedencia do Auto de Infração $y50_codauto <br>";
                        flush();
                    }

                    $clautotipo->y59_valor   = $y122_valor;
                    $clautotipo->y59_codauto = $y50_codauto;
                    $clautotipo->y59_codtipo = $y122_procedencia;
                    $clautotipo->y59_fator   = '0';
                    $clautotipo->y59_tipo    = '0';
                    $clautotipo->incluir(null);
                    if ($_debug) {
                        var_dump($clautotipo);echo "<br />";
                        flush();
                    }
                    if($clautotipo->erro_status==0){
                        $erro_msg = "Ocorreu um erro ao registrar o Tipo do Auto de Infração - clparagrafoauto ";
                        $sqlerro=true;
                    }
                }

                if($sqlerro==false){
                    if ($_debug) {
                        echo "  >>  03 - Auto de Infração - passo 8 - Vincula a Receita ao Auto de Infração $y50_codauto <br>";
                        flush();
                    }
                    $result = $clautorec->sql_record($clautorec->sql_query_file($y50_codauto));
                    if($clautorec->numrows == 0){

                        $result = $clfiscalprocrec->sql_record($clfiscalprocrec->sql_query_autotipo("",""," *",""," y59_codauto = $y50_codauto"));
                        if($clfiscalprocrec->numrows > 0){
                            $numrows = $clfiscalprocrec->numrows;
                            for($i=0;$i<$numrows;$i++){
                                db_fieldsmemory($result,$i);
                                $clautorec->y57_valor = $y45_valor;
                                $clautorec->y57_descr = $y45_descr;
                                $clautorec->incluir($y59_codauto,$y45_receit);
                                if ($_debug) {
                                    var_dump($clautorec);echo "<br />";
                                    flush();
                                }
                            }
                        }
                    }
                }

                if($sqlerro==false){
                    if ($_debug) {
                        echo "  >>  03 - Auto de Infração - passo 9 - Vincula o Processo fiscal ao Auto de Infração $y50_codauto <br>";
                        flush();
                    }

                    $clprocfiscalauto->y111_procfiscal = $y100_sequencial;
                    $clprocfiscalauto->y111_auto       = $y50_codauto;
                    $clprocfiscalauto->incluir(null);
                    if ($_debug) {
                        var_dump($clprocfiscalauto);echo "<br />";
                        flush();
                    }
                    if($clprocfiscalauto->erro_status==0){
                        $erro_msg = "Ocorreu um erro ao Vincular o Processo Fiscal ao Auto - clprocfiscalauto ";
                        $sqlerro=true;
                    }
                }

                if($sqlerro==false){
                    if ($_debug) {
                        echo "  >>  03 - Auto de Infração - passo 10 - Vincula o Fiscal ao Auto de Infração $y50_codauto <br>";
                        flush();
                    }
                    for ($hij = 0; $hij < count($eFiscal); ++$hij) {
                        $clautousu->incluir($y50_codauto, trim($eFiscal[$hij]));
                    }

                    if ($_debug) {
                        var_dump($clautousu);echo "<br />";
                        flush();
                    }
                    if($clautousu->erro_status==0){
                        $erro_msg = "Ocorreu um erro ao Vincular o Fiscal - clautousu ";
                        $sqlerro=true;
                    }
                }

                /* CGM */
                if($y123_cgccpf!= ""){
                    if($sqlerro==false){
                        if ($_debug) {
                            echo "  >>  03 - Auto de Infração - passo 11 - Vincula o Responsável ao Auto de Infração $y50_codauto <br>";
                            flush();
                        }
                        $cgccpf = trim($y123_cgccpf);
                        $rsCgm = $clcgm->sql_record($clcgm->sql_query_file(null,"z01_numcgm",""," z01_cgccpf = '$cgccpf'"));
                        if(pg_num_rows($rsCgm) == 0){
                            $clcgm->z01_nome    = trim($y123_nomecomple);
                            $clcgm->z01_cgccpf  = trim($y123_cgccpf);
                            $clcgm->z01_ender   = trim($y123_ender);
                            $clcgm->z01_numero  = trim($y123_numero);
                            $clcgm->z01_bairro  = trim($y123_bairro);
                            $clcgm->z01_munic   = trim($y123_munic);
                            $clcgm->z01_uf      = trim($y123_uf);
                            $clcgm->z01_cep     = trim($y123_cep);
                            $clcgm->incluir(null);
                            if ($_debug) {
                                var_dump($clcgm);echo "<br />";
                                flush();
                            }
                            if($clcgm->erro_status==0){
                                $erro_msg = "Ocorreu um erro ao Cadastrar o CGM - clcgm ";
                                $sqlerro=true;
                            }

                            $cgmresp = $clcgm->z01_numcgm;
                        } else {
                            db_fieldsmemory($rsCgm,0);
                            $cgmresp = $z01_numcgm;
                        }

                        $clautorespons->incluir($y50_codauto,$cgmresp,$y123_tprespons);
                        if ($_debug) {
                            var_dump($clautorespons);echo "<br />";
                            flush();
                        }
                        if($clautorespons->erro_status==0){
                            $erro_msg = "Ocorreu um erro ao Vincular o Responsável  - clautorespons ";
                            $sqlerro=true;
                        }
                    }
                }

                if($sqlerro==false){
                    if ($_debug) {
                        echo "  >>  03 - Auto de Infração - passo 12 - Realiza o Pré Cálculo do Auto de Infração $y50_codauto <br>";
                        flush();
                    }
                    $rsprecalculo   = $clauto->sql_precalculo($y50_codauto);
                    $sInfo          = db_utils::fieldsmemory($rsprecalculo, 0)->fc_fis_calculoautodeinfracao;
                }

                if($sqlerro==false){
                    if ($_debug) {
                        echo "  >>  03 - Auto de Infração - passo 13 - Realiza o primeiro andamento do Auto de Infração $y50_codauto <br>";
                        flush();
                    }

                    $bTipoAnd = "select y29_tipoandam from fiscalizacao.fis_fiscalproc where y29_coddepto = ".db_getsession("DB_coddepto")." and y29_codtipo = $y122_procedencia";
                    $rstipoAnd = db_query($bTipoAnd);

                    $clfandam->y39_codtipo    = pg_result($rstipoAnd,0);
                    $clfandam->y39_obs        = "0";
                    $clfandam->y39_id_usuario = trim($eFiscal[0]);
                    $clfandam->y39_data       = $y122_data_ano.'/'.$y122_data_mes.'/'.$y122_data_dia;
                    $clfandam->y39_hora       = $y122_hora;
                    $clfandam->incluir( null );
                    if ($_debug) {
                        var_dump($clfandam);echo "<br />";
                        flush();
                    }
                    if($clfandam->erro_status==0){
                        $erro_msg = "Ocorreu um erro ao registrar o andamento do auto de infração  - clfandam ";
                        $sqlerro=true;
                    }
                    $y39_codandam = $clfandam->y39_codandam;

                    $clautoandam->incluir($y50_codauto,$y39_codandam);
                    if ($_debug) {
                        var_dump($clautoandam);echo "<br />";
                        flush();
                    }
                    if($clautoandam->erro_status==0){
                        $erro_msg = "Ocorreu um erro ao registrar o andamento do auto de infração  - clautoandam ";
                        $sqlerro=true;
                    }

                    $clautoultandam->incluir( $y50_codauto, $clfandam->y39_codandam );
                    if ($_debug) {
                        var_dump($clautoultandam);echo "<br />";
                        flush();
                    }
                    if($clautoultandam->erro_status==0){
                        $erro_msg = "Ocorreu um erro ao registrar o andamento do auto de infração  - clautoandam ";
                        $sqlerro=true;
                    }
                }


                if($sqlerro==false){
                    if ($_debug) {
                        echo "  >>  03 - Auto de Infração - passo 14 - Realiza o Vínculo do Auto com o Arquivo Lote <br>";
                        flush();
                    }
                    $rsInsLevantLoteArqAuto = db_query("INSERT INTO fiscalizacao.fis_levantlotearqauto (y125_levantlotearq, y125_codauto, y125_procfiscal, y125_levanta, y125_calculado) VALUES ($y122_codigo, $y50_codauto, $y100_sequencial, $y60_codlev, '$sInfo')");
                    if ($_debug) {
                        var_dump($rsInsLevantLoteArqAuto);echo "<br />";
                        flush();
                    }
                    if(!$rsInsLevantLoteArqAuto){
                        $erro_msg = "Ocorreu um erro ao Vincular o Lote com o Auto. ";
                        $sqlerro=true;
                    }
                }

            } else if($y122_pecafiscal == 2){ /* NOTIFICAÇÃO DE LANÇAMENTO */
                if ($_debug) {
                    echo "  >>  03 - Notificação de Lançamento - passo 1 - Registra a Notificação de Lançamento para o Levantamento - $y60_codlev <br>";
                    flush();
                }

                $cllancamento->nl01_data     = $y122_data_ano.'/'.$y122_data_mes.'/'.$y122_data_dia;
                $cllancamento->nl01_hora     = $y122_hora;
                $cllancamento->nl01_setor    = db_getsession("DB_coddepto");
                $cllancamento->nl01_codtipo  = $y122_tipofiscal;
                $cllancamento->nl01_instit   = db_getsession("DB_instit");
                $cllancamento->incluir(null);
                if ($_debug) {
                    var_dump($cllancamento);echo "<br />";
                    flush();
                }
                if ( $cllancamento->erro_status == 0 ){
                    $erro_msg = $cllancamento->erro_msg;
                    $sqlerro=true;
                }
                $nl01_codlanc = $cllancamento->nl01_codlanc;

                if($sqlerro==false) {
                    if ($_debug) {
                        echo "  >>  03 - Notificação de Lançamento - passo 2 - Registra o Lancamento $nl01_codlanc com a Inscrição $inscr <br>";
                        flush();
                    }

                    $cllancinscr->nl04_inscr=$inscr;
                    $cllancinscr->incluir($nl01_codlanc);
                    if ($_debug) {
                        var_dump($cllancinscr);echo "<br />";
                        flush();
                    }
                    if( $cllancinscr->erro_status == 0 ){
                        $erro_msg = $cllancinscr->erro_msg;
                        $sqlerro  = true;
                    }
                }

                if($sqlerro==false){
                    if ($_debug) {
                        echo "  >>  03 - Notificação de Lançamento - passo 3 - Registra o Levantamento para a Notificação $nl01_codlanc <br>";
                        flush();
                    }
                    $cllanclevanta->nl15_lancamento = $nl01_codlanc;
                    $cllanclevanta->nl15_levanta    = $y60_codlev;
                    $cllanclevanta->incluir();
                    if ($_debug) {
                        var_dump($cllanclevanta);echo "<br />";
                        flush();
                    }
                    if ( $cllanclevanta->erro_status == 0 ) {
                        $erro_msg = $cllanclevanta->erro_msg;
                        $sqlerro = true;
                    }
                }
                if($sqlerro==false) {
                    if ($_debug) {
                        echo "  >>   03 - Notificação de Lançamento - passo 4 - Registra a Notificação $nl01_codlanc com o Endereço da Localização <br>";
                        flush();
                    }
                    $cllanclocal->nl02_codlanc  = $nl01_codlanc;
                    $cllanclocal->nl02_codigo   = $endrua;
                    $cllanclocal->nl02_codi     = $endbairro;
                    $cllanclocal->nl02_numero   = $endnum;
                    $cllanclocal->nl02_compl    = $endcompl;
                    $cllanclocal->incluir($nl01_codlanc);
                    if ($_debug) {
                        var_dump($cllanclocal);echo "<br />";
                        flush();
                    }
                    if ($cllanclocal->erro_status == 0){
                        $erro_msg = "Ocorreu um erro ao registrar o Endereço da Notificação para a Inscricao $inscr - cllanclocal";
                        $sqlerro  = true;
                    }
                }

                if ($sqlerro==false){
                    if ($_debug) {
                        echo "  >>   03 - Notificação de Lançamento - passo 5 - Registra a Notificação $nl01_codlanc com o Endereço da Execução <br>";
                        flush();
                    }
                    $cllancexec->nl03_codlanc  = $nl01_codlanc;
                    $cllancexec->nl03_codigo   = $endrua;
                    $cllancexec->nl03_codi     = $endbairro;
                    $cllancexec->nl03_numero   = $endnum;
                    $cllancexec->nl03_compl    = $endcompl;
                    $cllancexec->incluir($nl01_codlanc);
                    if ($_debug) {
                        var_dump($cllancexec);echo "<br />";
                        flush();
                    }
                    if ($cllancexec->erro_status==0){
                        $erro_msg = "Ocorreu um erro ao registrar o Endereço da Notificação para a Inscricao $inscr - cllancexec";
                        $sqlerro=true;
                    }
                }

                if($sqlerro==false){
                    if ($_debug) {
                        echo "  >>   03 - Notificação de Lançamento - passo 6 - Registra os Parágrafos para a Notificação $nl01_codlanc <br>";
                        flush();
                    }
                    $rowParagrafo = count($paragrafoTipo);

                    for($efg=0; $efg < $rowParagrafo; $efg++){
                        $clparagrafolanc->pl30_codlanc   = $nl01_codlanc;
                        $clparagrafolanc->pl30_paragrafo = $paragrafoTipo[$efg];
                        $clparagrafolanc->pl30_texto     = $paragrafoTexto[$efg];
                        $clparagrafolanc->pl30_usu       = trim($eFiscal[0]);
                        $clparagrafolanc->incluir();
                        if ($_debug) {
                            var_dump($clparagrafolanc);echo "<br />";
                            flush();
                        }
                        if($clparagrafolanc->erro_status==0){
                            $erro_msg = "Ocorreu um erro ao registrar os Parágrafos na Notificação de Lançamento - clparagrafolanc ";
                            $sqlerro=true;
                        }
                    }
                }

                if($sqlerro==false){
                    if ($_debug) {
                        echo "  >>  03 - Notificação de Lançamento - passo 7.1 - Registra a Multa da Procedência da Notificação $nl01_codlanc <br>";
                        flush();
                    }

                    if( isset($y122_procedencia) && $y122_procedencia != "" ){

                        $cllancmulta->nl28_codlanc = $nl01_codlanc;
                        $cllancmulta->nl28_codtipo = $y122_procedencia;
                        $cllancmulta->nl28_valor   = $y122_valor;
                        $cllancmulta->incluir( null );
                        if ($_debug) {
                            var_dump($cllancmulta);echo "<br />";
                            flush();
                        }
                        if($cllancmulta->erro_status==0){
                            $erro_msg = "Ocorreu um erro ao registrar a Multa da Procedência da Notificação - cllanctipo ";
                            $sqlerro=true;
                        }
                    }
                }
                if($sqlerro==false){
                    if ($_debug) {
                        echo "  >>  03 - Notificação de Lançamento - passo 8 - Vincula a Receita a Notificação de Lançamento $nl01_codlanc <br>";
                        flush();
                    }
                    $result = $cllancrec->sql_record($cllancrec->sql_query_file($nl01_codlanc));
                    if($cllancrec->numrows == 0){

                        $oCampos = "y45_valor,y45_descr,y45_receit";
                        $sSql    = $clfiscalprocrec->sql_query_lanctipo  ("","", $oCampos ,""," nl18_codlanc = $nl01_codlanc");
                        $sSql   .= " union all ";
                        $sSql   .= $clfiscalprocrec->sql_query_lancmulta ("","", $oCampos ,""," nl28_codlanc = $nl01_codlanc");

                        $result  = $clfiscalprocrec->sql_record( "SELECT DISTINCT * FROM (".$sSql.") AS x" );

                        if($clfiscalprocrec->numrows > 0){
                            $numrows = $clfiscalprocrec->numrows;
                            for( $i = 0; $i < $numrows; $i++ ){
                                db_fieldsmemory( $result , $i );
                                $cllancrec->nl22_valor = $y45_valor;
                                $cllancrec->nl22_descr = $y45_descr;
                                $cllancrec->incluir( $nl01_codlanc , $y45_receit );
                                if ($_debug) {
                                    var_dump($cllancrec);echo "<br />";
                                    flush();
                                }
                            }
                        }
                    }
                }

                if($sqlerro==false){
                    if ($_debug) {
                        echo "  >>  03 - Auto de Infração - passo 9 - Vincula o Processo fiscal a Notificação de Lançamento $nl01_codlanc <br>";
                        flush();
                    }

                    $clprocfiscallanc->nl09_procfiscal = $y100_sequencial;
                    $clprocfiscallanc->nl09_lanc       = $nl01_codlanc;
                    $clprocfiscallanc->incluir(null);
                    if ($_debug) {
                        var_dump($clprocfiscallanc);echo "<br />";
                        flush();
                    }
                    if($clprocfiscallanc->erro_status==0){
                        $erro_msg = "Ocorreu um erro ao Vincular o Processo Fiscal a Notificação de Lançamento - clprocfiscallanc ";
                        $sqlerro=true;
                    }
                }

                if($sqlerro==false){
                    if ($_debug) {
                        echo "  >>  03 - Notificação de Lançamento - passo 10 - Vincula o Fiscal a Notificação de Lançamento $nl01_codlanc <br>";
                        flush();
                    }

                    for ($hij = 0; $hij < count($eFiscal); ++$hij) {
                        $cllancusu->incluir($nl01_codlanc, trim($eFiscal[$hij]));
                    }
                    if ($_debug) {
                        var_dump($cllancusu);echo "<br />";
                        flush();
                    }
                    if($cllancusu->erro_status==0){
                        $erro_msg = "Ocorreu um erro ao Vincular o Fiscal - cllancusu ";
                        $sqlerro=true;
                    }
                }

                /* CGM */
                if($y123_cgccpf!= ""){
                    if($sqlerro==false){
                        if ($_debug) {
                            echo "  >>  03 - Notificação de Lançamento - passo 11 - Vincula o Responsável a Notificação de Lançamento $nl01_codlanc <br>";
                            flush();
                        }
                        $cgccpf = trim($y123_cgccpf);
                        $rsCgm = $clcgm->sql_record($clcgm->sql_query_file(null,"z01_numcgm",""," z01_cgccpf = '$cgccpf'"));
                        if(pg_num_rows($rsCgm) == 0){
                            $clcgm->z01_nome    = trim($y123_nomecomple);
                            $clcgm->z01_cgccpf  = trim($y123_cgccpf);
                            $clcgm->z01_ender   = trim($y123_ender);
                            $clcgm->z01_numero  = trim($y123_numero);
                            $clcgm->z01_bairro  = trim($y123_bairro);
                            $clcgm->z01_munic   = trim($y123_munic);
                            $clcgm->z01_uf      = trim($y123_uf);
                            $clcgm->z01_cep     = trim($y123_cep);
                            $clcgm->incluir(null);
                            if ($_debug) {
                                var_dump($clcgm);echo "<br />";
                                flush();
                            }
                            if($clcgm->erro_status==0){
                                $erro_msg = "Ocorreu um erro ao Cadastrar o CGM - clcgm ";
                                $sqlerro=true;
                            }

                            $cgmresp = $clcgm->z01_numcgm;
                        } else {
                            db_fieldsmemory($rsCgm,0);
                            $cgmresp = $z01_numcgm;
                        }

                        $cllancrespons->nl12_codlanc = $nl01_codlanc;
                        $cllancrespons->nl12_numcgm  = $cgmresp;
                        $cllancrespons->nl12_tipo    = $y123_tprespons;
                        $cllancrespons->incluir($nl01_codlanc,$cgmresp,$y123_tprespons);
                        if ($_debug) {
                            var_dump($cllancrespons);echo "<br />";
                            flush();
                        }
                        if($cllancrespons->erro_status==0){
                            $erro_msg = "Ocorreu um erro ao Vincular o Responsável  - cllancrespons ";
                            $sqlerro=true;
                        }
                    }
                }

                if($sqlerro==false){
                    if ($_debug) {
                        echo "  >>  03 - Notificação de Lançamento - passo 12 - Realiza o Pré Cálculo da Notificação de Lançamento $nl01_codlanc <br>";
                        flush();
                    }

                    $rsprecalculo   = $cllancamento->sql_precalculo($nl01_codlanc);
                    $sInfo          = db_utils::fieldsmemory($rsprecalculo, 0)->fc_fis_calculolancamentodeinfracao;
                }

                if($sqlerro==false){
                    if ($_debug) {
                        echo "  >>  03 - Auto de Infração - passo 13 - Realiza o primeiro andamento da Notificação $nl01_codlanc <br>";
                        flush();
                    }

                    $bTipoAnd = "select y29_tipoandam from fiscalizacao.fis_fiscalproc where y29_coddepto = ".db_getsession("DB_coddepto")." and y29_codtipo = $y122_procedencia";
                    $rstipoAnd = db_query($bTipoAnd);

                    $clfandam->y39_codtipo    = pg_result($rstipoAnd,0);
                    $clfandam->y39_obs        = "0";
                    $clfandam->y39_id_usuario = trim($eFiscal[0]);
                    $clfandam->y39_data       = $y122_data_ano.'/'.$y122_data_mes.'/'.$y122_data_dia;
                    $clfandam->y39_hora       = $y122_hora;
                    $clfandam->incluir( null );
                    if ($_debug) {
                        var_dump($clfandam);echo "<br />";
                        flush();
                    }

                    if($clfandam->erro_status==0){
                        $erro_msg = "Ocorreu um erro ao registrar o andamento da notificação  - clfandam ";
                        $sqlerro=true;
                    }
                    $y39_codandam = $clfandam->y39_codandam;

                    $cllancandam->incluir($nl01_codlanc,$y39_codandam);
                    if ($_debug) {
                        var_dump($cllancandam);echo "<br />";
                        flush();
                    }
                    if($cllancandam->erro_status==0){
                        $erro_msg = "Ocorreu um erro ao registrar o andamento da notificação  - cllancandam ";
                        $sqlerro=true;
                    }

                    $cllancultandam->incluir( $nl01_codlanc, $clfandam->y39_codandam );
                    if ($_debug) {
                        var_dump($cllancultandam);echo "<br />";
                        flush();
                    }
                    if($cllancultandam->erro_status==0){
                        $erro_msg = "Ocorreu um erro ao registrar o andamento da notificação  - cllancultandam ";
                        $sqlerro=true;
                    }
                }

                if($sqlerro==false){
                    if ($_debug) {
                        echo "  >>  03 - Notificação de Lançamento - passo 14 - Realiza o Vínculo da Notificação de Lançamento com o Arquivo Lote <br>";
                        flush();
                    }
                    $rsInsLevantLoteArqLanc = db_query("INSERT INTO fiscalizacao.fis_levantlotearqlanc(y126_levantlotearq, y126_codlanc, y126_procfiscal, y126_levanta, y126_calculado) VALUES ($y122_codigo, $nl01_codlanc, $y100_sequencial, $y60_codlev, '$sInfo')");
                    if ($_debug) {
                        var_dump($rsInsLevantLoteArqLanc);echo "<br />";
                        flush();
                    }
                    if(!$rsInsLevantLoteArqLanc){
                        $erro_msg = "Ocorreu um erro ao Vincular o Lote com a Notificação. ";
                        $sqlerro=true;
                    }
                }
            }
        }
    }
    if($sqlerrolinha != ""){
        $pos = strpos($sqlerrolinha, ",");
        if ($pos === false) {
            $numerrolinha = $sqlerrolinha;
        } else {
            $numerrolinha = substr($sqlerrolinha,0,-2);
        }
        $erro_msg .= "Arquivo inconsistente! Verifique a(s) linha(s): ".$numerrolinha."! <br />";
        $sqlerro = true;
    }

    if($inscsemend != ""){
        $posend = strpos($inscsemend, ",");
        if ($posend === false) {
            $numinscsemend = $inscsemend;
        } else {
            $arrEnd = explode(", ",$inscsemend);
            $arrSEnd = array_unique($arrEnd);
            natcasesort($arrSEnd);
            foreach ($arrSEnd as $key => $val) {
                if(!empty($val)) {
                    $numinscsemend .= $val.", ";
                }
            }
            $numinscsemend = substr($numinscsemend,0,-2);
        }
        $erro_msg = "Endereço não encontrado para a(s) inscrição(ões): ".$numinscsemend."!<br />";
        $sqlerro = true;
    }

    if($inscbaixad != ""){
        $posbaix = strpos($inscbaixad, ",");
        if ($posbaix === false) {
            $numinscbaixad = $inscbaixad;
        } else {
            $arrBaix = explode(", ",$inscbaixad);
            $arrIndBaix = array_unique($arrBaix);
            natcasesort($arrIndBaix);
            foreach ($arrIndBaix as $key => $val) {
                if(!empty($val)) {
                    $numinscbaixad .= $val.", ";
                }
            }
            $numinscbaixad = substr($numinscbaixad,0,-2);
        }
        $erro_msg = "Baixa encontrada para a(s) inscrição(ões): ".$numinscbaixad."!<br />";
        $sqlerro = true;
    }

    db_atutermometro(pg_num_rows($rsLevantamentoLote), pg_num_rows($rsLevantamentoLote), 'termometro');
    db_fim_transacao($sqlerro);

    if($sqlerro == true) {
        if(!empty($erro_msg)) {
            db_msgbox($erro_msg);
        }
    } else {

        ?><script>
          var iCodLote     = <?php echo $y122_codigo;
          echo "
          var oParametros = {
            codlote       : iCodLote
          };
          alert('Arquivo importado com SUCESSO!!\\n$_msg');
                new EmissaoRelatorio('fis3_fis_rellevantlotearq003.php', oParametros).open();
          </script>";
    }
}
?>
