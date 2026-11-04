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

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

parse_str($HTTP_SERVER_VARS['QUERY_STRING']);

require(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("classes/db_fis_tipoandam_classe.php"));
require_once(modification('classes/db_fis_parametrosandamento_classe.php'));
require_once(modification('classes/db_fis_usuariosparametrosandamento_classe.php'));
require_once(modification('classes/db_fis_deptosparametrosandamento_classe.php'));
require_once(modification("classes/db_fis_grupotipoandamento_tipoandam_classe.php"));
require_once(modification("classes/db_fis_tipoandam_datafim_classe.php"));
include(modification("dbforms/db_funcoes.php"));

if(!isset($abas)){
    echo "<script>location.href='fis1_fis_tipoandam004.php?db_opcao=2'</script>";
    exit;
}

db_postmemory($HTTP_POST_VARS);
$cltipoandam = new cl_fis_tipoandam;
$clparametrosandamento = new cl_fis_parametrosandamento;
$clgrupotipoandamento_tipoandam = new cl_fis_grupotipoandamento_tipoandam;
$cltipoandam_datafim            = new cl_fis_tipoandam_datafim;
$db_opcao = 22;
$db_botao = false;
$sqlErro = false;
if(isset($alterar)){
    db_inicio_transacao();

    $db_opcao = 2;
    $cltipoandam->y41_permcalc= $y41_permcalc;
    $cltipoandam->alterar($y41_codtipo);
    $erro_msg = $cltipoandam->erro_msg;

    if ($cltipoandam->erro_status == '0') {
        $sqlErro  = true;
        $erro_msg = $cltipoandam->erro_msg;
    }

    if(!$sqlErro){
        if (isset($sequencial) and $sequencial != '') {
            $clparametrosandamento->tem_data_ciencia = $tem_data_ciencia;
            $clparametrosandamento->tipoandam = $y41_codtipo;
	    $clparametrosandamento->tem_data_recurso = $tem_data_recurso;
            $clparametrosandamento->alterar($sequencial);
        } else {
            $clparametrosandamento->tem_data_ciencia = $tem_data_ciencia;
            $clparametrosandamento->tipoandam = $y41_codtipo;
	    $clparametrosandamento->tem_data_recurso = $tem_data_recurso;
            $clparametrosandamento->incluir();
        }
        $erro_msg = $clparametrosandamento->erro_msg;

        if ($clparametrosandamento->erro_status == '0') {
            $sqlErro  = true;
            $erro_msg = $clparametrosandamento->erro_msg;
        }
    }
    
    if(!$sqlErro){
        $sqlGrupo = 'select fi30_sequencial from fiscalizacao.fis_grupotipoandamento_tipoandam where fi30_tipoandam = '.$y41_codtipo;
        $rsGrupo  = db_query($sqlGrupo);
        if(pg_num_rows($rsGrupo) > 0){
            db_fieldsmemory($rsGrupo,0);
            $clgrupotipoandamento_tipoandam->fi30_sequencial = $fi30_sequencial;
            $clgrupotipoandamento_tipoandam->fi30_grupo      = $grupo;
            $clgrupotipoandamento_tipoandam->fi30_tipoandam  = $y41_codtipo;
            $clgrupotipoandamento_tipoandam->alterar();
        }else{
            $clgrupotipoandamento_tipoandam->fi30_grupo     = $grupo;
            $clgrupotipoandamento_tipoandam->fi30_tipoandam = $y41_codtipo;
            $clgrupotipoandamento_tipoandam->incluir();
        }
        $erro_msg = $clgrupotipoandamento_tipoandam->erro_msg;

        if ($clgrupotipoandamento_tipoandam->erro_status == '0') {
            $sqlErro = true;  
            $erro_msg = $clgrupotipoandamento_tipoandam->erro_msg;
        }
    }

    if(!$sqlErro){
        $sqlDatafim = 'select fi31_sequencial from fiscalizacao.fis_tipoandam_datafim where fi31_tipoandam = '.$y41_codtipo;
        $rsDatafim  = db_query($sqlDatafim);

        if(pg_num_rows($rsDatafim) > 0){
            db_fieldsmemory($rsDatafim,0);
            $cltipoandam_datafim->fi31_sequencial = $fi31_sequencial;
            $cltipoandam_datafim->fi31_data       = $fi31_data;
            $cltipoandam_datafim->fi31_tipoandam  = $y41_codtipo;
            if($fi31_data != ""){
                $cltipoandam_datafim->alterar();
                $erro_msg = $cltipoandam_datafim->erro_msg;
            }else{
                $cltipoandam_datafim->excluir();
            }
            
        }else if($fi31_data != ""){
            $cltipoandam_datafim->fi31_data      = $fi31_data;
            $cltipoandam_datafim->fi31_tipoandam = $y41_codtipo;
            $cltipoandam_datafim->incluir();
            $erro_msg = $cltipoandam_datafim->erro_msg;
        }

        if ($cltipoandam_datafim->erro_status == '0') {
            $sqlErro = true;  
            $erro_msg = $cltipoandam_datafim->erro_msg;
        }
    }

    db_fim_transacao($sqlErro);
}else if(isset($chavepesquisa)){
    $db_opcao = 2;
    $result = $cltipoandam->sql_record($cltipoandam->sql_query($chavepesquisa)); 
    $result2 = $clparametrosandamento->sql_record($clparametrosandamento->sql_query(null, "*", null, "fis_parametrosandamento.tipoandam = $chavepesquisa"));
    $sqlPlugin = "select fi30_grupo, 
                      fi31_data, 
                      descricao, 
                    case manual_automatico 
                      when 1 then 'MANUAL' 
                      when 2 then 'AUTOMÁTICO' 
                    end as manual_automatico
                    ,case tipo_peca
                      when 1 then 'LEVANTAMENTO'
                      when 2 then 'NOTIFICAÇÃO'
                      when 3 then 'AUTO'
                      when 4 then 'INTIMAÇÃO'
                      when 5 then 'VISTORIA'
                    end as peca 
                    from fiscalizacao.fis_grupotipoandamento_tipoandam 
                    inner join fiscalizacao.fis_grupotipoandamento on sequencial = fi30_grupo 
                    left  join fiscalizacao.fis_tipoandam_datafim on fi31_tipoandam = fi30_tipoandam 
                    where fi30_tipoandam = ".$chavepesquisa;
    
    $result3 = db_query($sqlPlugin);

    db_fieldsmemory($result, 0);
    db_fieldsmemory($result2, 0);
    db_fieldsmemory($result3, 0);
    
    if($fi30_grupo != ''){
        $grupo_desc = $descricao.' / '.$manual_automatico.' / '.$peca;
        $grupo = $fi30_grupo;
    }

    $db_botao = true;
    echo "<script>
    parent.iframe_deptos.location.href='fis1_fis_tipoandamdeptos001.php?parametrosandamento=$sequencial&db_opcao=".$dbopcao."';
    parent.iframe_fiscais.location.href='fis1_fis_tipoandamusu001.php?parametrosandamento=$sequencial&db_opcao=".$dbopcao."';
    </script>";
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
<body bgcolor=#CCCCCC onLoad="a=1" >
    <?php 
        require_once(modification('forms/db_frm_fis_tipoandam.php'));      
    ?>
</body>
</html>
<?php 
if(isset($alterar)){
    if($sqlerro){
        // $cltipoandam->erro(true,false);
        db_msgbox($erro_msg);
        $db_botao=true;
        echo "<script> document.form1.db_opcao.disabled=false;</script>";
    // if($cltipoandam->erro_campo!=""){
    //     echo "<script> document.form1.".$cltipoandam->erro_campo.".style.backgroundColor='#99A9AE';</script>";
    //     echo "<script> document.form1.".$cltipoandam->erro_campo.".focus();</script>";
    // }
    }else{
        // $cltipoandam->erro(true,false);
        echo "<script>alert(\"".$erro_msg."\");</script>";
        db_redireciona("fis1_fis_tipoandam002.php?abas=1&parametrosandamento=$clparametrosandamento->sequencial");
    }
}
if($db_opcao==22){
    echo "<script>document.form1.pesquisar.click();</script>";
}
?>
