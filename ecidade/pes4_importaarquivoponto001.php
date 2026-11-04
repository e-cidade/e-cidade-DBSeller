<?php
/*
 * E-cidade Software Publico para Gestao Municipal
 * Copyright (C) 2009 DBSeller Servicos de Informatica
 * www.dbseller.com.br
 * e-cidade@dbseller.com.br
 *
 * Este programa e software livre; voce pode redistribui-lo e/ou
 * modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 * publicada pela Free Software Foundation; tanto a versao 2 da
 * Licenca como (a seu criterio) qualquer versao mais nova.
 *
 * Este programa e distribuido na expectativa de ser util, mas SEM
 * QUALQUER GARANTIA; sem mesmo a garantia implicita de
 * COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 * PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 * detalhes.
 *
 * Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 * junto com este programa; se nao, escreva para a Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 * 02111-1307, USA.
 *
 * Copia da licenca no diretorio licenca/licenca_en.txt
 * licenca/licenca_pt.txt
 */
require_once (modification ( "libs/db_stdlib.php" ));
require_once (modification ( "libs/db_conecta.php" ));
require_once (modification ( "libs/db_sessoes.php" ));
require_once (modification ( "libs/db_utils.php" ));
require_once (modification ( "libs/db_app.utils.php" ));
require_once (modification ( "dbforms/db_funcoes.php" ));

$oPost = db_utils::postMemory($_POST);

$ano = db_anofolha();
$mes = db_mesfolha();
$iInstituicao = db_getsession("DB_instit");

if (isset ( $oPost->processar )) {

    try {
        
        db_inicio_transacao ();
        
        $oFile = db_utils::postMemory ( $_FILES ["arquivo"] );
        $iLinha = 0;
        
        $aArquivo = file($oFile->tmp_name);
        foreach ( $aArquivo as $sDadosTxt ) {
            
            $iLinha++;
            $aDadosLinha = explode(";", $sDadosTxt);
            
            $iMatricula = trim($aDadosLinha[0]);
            $sRubrica   = trim($aDadosLinha[1]);
            if (count($aDadosLinha) == 3) {
               $iQtd = 1;
               $nValor = trim($aDadosLinha[2]);
            } else {
               $iQtd = trim($aDadosLinha[2]);
               $nValor = trim($aDadosLinha[3]);
            }
            
            if (count($aDadosLinha) != 3 && count($aDadosLinha) != 4 ) {
                $msg = "Qtd de colunas da linha do arquivo inválida.\n\n"; 
                $msg .= "Quando o arquivo possuir a informacao de quantidade, deve possuir 4 colunas, sendo:\n";
                $msg .= "Matricula;Rubrica;Quantidade;Valor\n\n";
                $msg .= "Sem a informação da quantidade, deve possuir 3 colunas, sendo:\n";
                $msg .= "Matricula;Rubrica;Valor\n\n";
                $msg .= "Linha com o erro: {$iLinha}";
                throw new Exception($msg);
            }
            
            if (empty($iMatricula)) {
                continue;
            }
            
            if (empty($sRubrica)) {
                throw new Exception("Matricula {$iMatricula} não possui rubrica informada no arquivo!\nLinha: {$iLinha}");
            }
            
            if (empty($nValor)) {
                throw new Exception("Matricula {$iMatricula} não possui valor informado no arquivo!\nLinha: {$iLinha}");
            }
            
            if (!is_numeric($nValor)) {
                throw new Exception("Matricula {$iMatricula} não possui um valor válido! Informação: {$nValor}\nLinha: {$iLinha}");
            }
            
            $sSqlVerificaRubrica = "select rh27_rubric
                                      from rhrubricas
                                     where rh27_rubric = '{$sRubrica}'
                                       and rh27_instit = {$iInstituicao}";
            $rsVerificaRubrica = db_query($sSqlVerificaRubrica);
            if (pg_num_rows($rsVerificaRubrica) == 0) {
                throw new Exception("Rubrica {$sRubrica} não encontrada para a instituição!\nLinha: {$iLinha}");
            }
            
            $sSqlDadosMatricula = "select *
                                     from rhpessoalmov
                                    where rh02_anousu = {$ano}
                                      and rh02_mesusu = {$mes}
                                      and rh02_regist = {$iMatricula}
                                      and rh02_instit = {$iInstituicao}";
            $rsDadosMatricula = db_query($sSqlDadosMatricula);
            if (pg_num_rows($rsDadosMatricula) == 0) {
                throw new Exception("Matricula {$iMatricula} não encontrada\nLinha: {$iLinha}");
            }
            
            $oDadosMatricula = db_utils::fieldsMemory($rsDadosMatricula, 0);
            
            //estanciamos a classe da tabela
            $oClasse = db_utils::getDao($oPost->tabela);
            
            //verificamos se ja existe registro para o ano/mes/matricula/rubrica
            $rsVerificaInclusao = $oClasse->sql_record($oClasse->sql_query_file($ano, $mes, $iMatricula, $sRubrica));
            if ($oClasse->numrows > 0) {
                throw new Exception("Encontrado registro lançado para matricula {$iMatricula} com a rubrica {$sRubrica} para a competência {$mes}/{$ano}\nLinha: {$iLinha}");
            }
            
            //alimentamos os campos da tabela
            switch ($oPost->tabela) {
                
                case "pontofx":
                    
                    $sSigla = "r90";
                    
                    $oClasse->r90_anousu = $ano;
                    $oClasse->r90_mesusu = $mes;
                    $oClasse->r90_regist = $iMatricula;
                    $oClasse->r90_rubric = $sRubrica;
                    $oClasse->r90_valor  = $nValor;
                    $oClasse->r90_quant  = $iQtd;
                    $oClasse->r90_lotac  = $oDadosMatricula->rh02_lota;
                    $oClasse->r90_datlim = null;
                    $oClasse->r90_instit = $iInstituicao;
                    $oClasse->incluir($ano, $mes, $iMatricula, $sRubrica);
                    
                    break;
                case "pontofs":
                    
                    $sSigla = "r10";
                    
                    $oClasse->r10_anousu = $ano;
                    $oClasse->r10_mesusu = $mes;
                    $oClasse->r10_regist = $iMatricula;
                    $oClasse->r10_rubric = $sRubrica;
                    $oClasse->r10_valor  = $nValor;
                    $oClasse->r10_quant  = $iQtd;
                    $oClasse->r10_lotac  = $oDadosMatricula->rh02_lota;
                    $oClasse->r10_datlim = null;
                    $oClasse->r10_instit = $iInstituicao;
                    $oClasse->incluir($ano, $mes, $iMatricula, $sRubrica);
                    
                    break;
                case "pontofr":
                    
                    $sSigla = "r19";
                    
                    $oClasse->r19_anousu = $ano;
                    $oClasse->r19_mesusu = $mes;
                    $oClasse->r19_regist = $iMatricula;
                    $oClasse->r19_rubric = $sRubrica;
                    $oClasse->r19_valor  = $nValor;
                    $oClasse->r19_quant  = $iQtd;
                    $oClasse->r19_lotac  = $oDadosMatricula->rh02_lota;
                    $oClasse->r19_tpp    = null;
                    $oClasse->r19_instit = $iInstituicao;
                    $oClasse->incluir($ano, $mes, $iMatricula, $sRubrica);
                    
                    break;
                case "pontofa":
                    
                    $sSigla = "r21";
                    
                    $oClasse->r21_anousu = $ano;
                    $oClasse->r21_mesusu = $mes;
                    $oClasse->r21_regist = $iMatricula;
                    $oClasse->r21_rubric = $sRubrica;
                    $oClasse->r21_valor  = $nValor;
                    $oClasse->r21_quant  = $iQtd;
                    $oClasse->r21_lotac  = $oDadosMatricula->rh02_lota;
                    $oClasse->r21_instit = $iInstituicao;
                    $oClasse->incluir($ano, $mes, $iMatricula, $sRubrica);
                    
                    break;
                case "pontocom":
                    
                    $sSigla = "r47";
                    
                    $oClasse->r47_anousu = $ano;
                    $oClasse->r47_mesusu = $mes;
                    $oClasse->r47_regist = $iMatricula;
                    $oClasse->r47_rubric = $sRubrica;
                    $oClasse->r47_valor  = $nValor;
                    $oClasse->r47_quant  = $iQtd;
                    $oClasse->r47_lotac  = $oDadosMatricula->rh02_lota;
                    $oClasse->r47_instit = $iInstituicao;
                    $oClasse->incluir($ano, $mes, $iMatricula, $sRubrica);
                    
                    break;
            }
            
            if ($oClasse->erro_status == 0) {
                throw new Exception("Erro realizando inclusão dos dados na tabela {$sTabela}!\n$oClasse->erro_msg\n\n".pg_last_error());
            }
            
        }
        
	db_msgbox ( "Processamento realizado com sucesso!" );
        db_fim_transacao ( false );
        
    } catch ( Exception $oException ) {
        
        db_fim_transacao ( true );
        db_msgbox ( "{$oException->getMessage()}" );
    }
}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<?
db_app::load ( "scripts.js, strings.js, prototype.js, estilos.css" );
?>
</head>

<body class="body-default" onLoad="a=1">

<form name="form1" enctype="multipart/form-data" method="post" action="">
<fieldset style="margin: 40px auto 10px; width: 700px;">
  <legend>
      <strong>Processa arquivo arrecadações tributárias</strong>
  </legend>
  <table align="center">
      <tr>
        <td> <b>Competência:</b> </td>
        <td>
          <?php
            db_input("ano", 4, 0, true);
            db_input("mes", 2, 0, true);
          ?> 
        </td>
      </tr>
      <tr>
        <td> <b>Ponto: </b> </td>
        <td>
          <?php
          $aPontos = array("pontofx" =>"Ponto Fixo",
                           "pontofs" =>"Ponto de Salario",
                           "pontofr" =>"Ponto de Rescisao",
                           "pontocom"=>"Ponto complementar");
          db_select("tabela", $aPontos, true, 1);
              
          ?>
        </td>
      </tr>
      <tr>
          <td nowrap><b>Arquivo: </b></td>
          <td><input name="arquivo" id="arquivo" type="file"></td>
      </tr>
  </table>
</fieldset>
<center>
  <input name="processar" type="submit" id="processar" value="Processar" onclick="return verifica()">
</center>
</form>

<?db_menu();?>
</body>
</html>
<script type="text/javascript">
function verifica() {

  if( $F('arquivo') == "" ){

    alert( "Arquivo não informado!" );
    $("arquivo").focus();
    return false;
    
  }

  if (confirm("Confirma o processamento das informações?\n\nCompetência:"+$F("mes")+"/"+$F("ano")+"\nPonto: "+$F("tabela"))) {
	  return true;
  }	  
  
  return false;
}
</script>
