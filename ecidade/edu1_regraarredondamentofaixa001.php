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

  require_once(modification("libs/db_stdlib.php"));
  require_once(modification("libs/db_conecta.php"));
  require_once(modification("libs/db_sessoes.php"));
  require_once(modification("libs/db_usuariosonline.php"));
  require_once(modification("libs/db_utils.php"));
  require_once(modification("dbforms/db_funcoes.php"));
  
  parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
  db_postmemory($HTTP_POST_VARS);
  
  $oDaoRegraArredondamentoFaixa     = new cl_regraarredondamentofaixa();
  $oDaoNovaRegraArredondamentoFaixa = new cl_regraarredondamentofaixa();
  $oDaoRegraArredondamento          = new cl_regraarredondamento();
  
  $db_opcao = 22;
  $db_botao = false;
  $lErro    = false;
  $sUrl     = "edu1_regraarredondamentofaixa001.php?ed317_regraarredondamento={$ed317_regraarredondamento}";
  
  if (isset($alterar) || isset($excluir) || isset($incluir)) {
    
    $sqlerro = false;
    $oDaoRegraArredondamentoFaixa->ed317_sequencial          = $ed317_sequencial;
    $oDaoRegraArredondamentoFaixa->ed317_regraarredondamento = $ed317_regraarredondamento;
    $oDaoRegraArredondamentoFaixa->ed317_inicial             = $ed317_inicial;
    $oDaoRegraArredondamentoFaixa->ed317_final               = $ed317_final;
    $oDaoRegraArredondamentoFaixa->ed317_arredondar          = $ed317_arredondar;
  }
  
  if (isset($incluir)) {
    
    if ($sqlerro == false) {
      
        db_inicio_transacao();
        $sWhere                         = " ed317_regraarredondamento = {$ed317_regraarredondamento}";
        $sSqlRegraArredondamentoFaixa   = $oDaoNovaRegraArredondamentoFaixa->sql_query(null, "ed317_inicial, ed317_final, ed317_arredondar", null, $sWhere);
        $rsRegraArredondamentoFaixa     = $oDaoNovaRegraArredondamentoFaixa->sql_record($sSqlRegraArredondamentoFaixa);
        $iLinhaRegraArredondamentoFaixa = $oDaoNovaRegraArredondamentoFaixa->numrows;
        
        try {
          
          for ($iContador = 0; $iContador < $iLinhaRegraArredondamentoFaixa; $iContador++) {
            
            $oDadosRegraArredondamentoFaixa = db_utils::fieldsMemory($rsRegraArredondamentoFaixa, $iContador);
              
              if(empty($ed317_inicial)) {
              
                $erro_msg = "Deve ser informada a faixa inicial da regra";
                throw new Exception ($erro_msg);
              }
              if(empty($ed317_final)) {
              
                $erro_msg = "Deve ser informada a faixa final da regra";
                throw new Exception ($erro_msg);
              }
              
              if ($ed317_inicial > $ed317_final) {
              
                $erro_msg = "Faixa inicial não pode ser maior que a final";
                throw new Exception ($erro_msg);
              }
              
              if ($ed317_inicial >= $oDadosRegraArredondamentoFaixa->ed317_inicial && 
                  $ed317_inicial <= $oDadosRegraArredondamentoFaixa->ed317_final) {
                
                $erro_msg  = "Faixa inicial ({$ed317_inicial}) não permitida. ";
                $erro_msg .= "Faixa está entre os valores de uma regra existente";
                throw new Exception ($erro_msg);
              }
              if ($ed317_final >= $oDadosRegraArredondamentoFaixa->ed317_inicial &&
                  $ed317_final <= $oDadosRegraArredondamentoFaixa->ed317_final) {
              
                $erro_msg  = "Faixa final ({$ed317_final}) não permitida. ";
                $erro_msg .= "Faixa está entre os valores de uma regra existente";
                throw new Exception ($erro_msg);
              }
              if ($ed317_arredondar == $oDadosRegraArredondamentoFaixa->ed317_arredondar) {
                
                $erro_msg  = "Tipo de regra de arredondamento já cadastrado.";
                throw new Exception ($erro_msg);
              }
            
          }
        } catch (Exception $eErro) {
          
          db_msgbox($eErro->getMessage());
          
          $lErro  = true;
          $sUrl  .= "&ed317_inicial={$ed317_inicial}&ed317_final={$ed317_final}&ed317_arredondar={$ed317_arredondar}";
          $sUrl  .= "&iCasasDecimais={$iCasasDecimais}&sDisabled={$sDisabled}";
          
          if ( isset( $chavepesquisa ) ) {
            $sUrl .= "&chavepesquisa={$chavepesquisa}";
          }
          
          db_redireciona( $sUrl );
        }
        
        if ($lErro == false) {
          
          $oDaoRegraArredondamentoFaixa->incluir($ed317_sequencial);
          $erro_msg = $oDaoRegraArredondamentoFaixa->erro_msg;
          if ($oDaoRegraArredondamentoFaixa->erro_status == 0) {
            
            $sqlerro = true;
            db_msgbox($erro_msg);
          }
          db_fim_transacao($sqlerro);
        }
    }
  }
  
  if (isset($alterar)) {
    
    if ($sqlerro == false) {
      
      db_inicio_transacao();
      $sWhere                         = "     ed317_regraarredondamento = {$ed317_regraarredondamento}";
      $sWhere                        .= " AND ed317_sequencial         != {$ed317_sequencial}";
      $sSqlRegraArredondamentoFaixa   = $oDaoNovaRegraArredondamentoFaixa->sql_query(null, "ed317_inicial, ed317_final, ed317_arredondar", null, $sWhere);
      $rsRegraArredondamentoFaixa     = $oDaoNovaRegraArredondamentoFaixa->sql_record($sSqlRegraArredondamentoFaixa);
      $iLinhaRegraArredondamentoFaixa = $oDaoNovaRegraArredondamentoFaixa->numrows;
      
      try {
        
        for ($iContador = 0; $iContador < $iLinhaRegraArredondamentoFaixa; $iContador++) {
      
          $oDadosRegraArredondamentoFaixa = db_utils::fieldsMemory($rsRegraArredondamentoFaixa, $iContador);
      
          if(empty($ed317_inicial)) {
      
            $erro_msg = "Deve ser informada a faixa inicial da regra";
            throw new Exception ($erro_msg);
          }
          if(empty($ed317_final)) {
      
            $erro_msg = "Deve ser informada a faixa final da regra";
            throw new Exception ($erro_msg);
          }
      
          if ($ed317_inicial > $ed317_final) {
      
            $erro_msg = "Faixa inicial não pode ser maior que a final";
            throw new Exception ($erro_msg);
          }
      
          if ($ed317_inicial >= $oDadosRegraArredondamentoFaixa->ed317_inicial &&
              $ed317_inicial <= $oDadosRegraArredondamentoFaixa->ed317_final) {
      
            $erro_msg  = "Faixa inicial ({$ed317_inicial}) não permitida. ";
            $erro_msg .= "Faixa está entre os valores de uma regra existente";
            throw new Exception ($erro_msg);
          }
          if ($ed317_final >= $oDadosRegraArredondamentoFaixa->ed317_inicial &&
              $ed317_final <= $oDadosRegraArredondamentoFaixa->ed317_final) {
      
            $erro_msg  = "Faixa final ({$ed317_final}) não permitida. ";
            $erro_msg .= "Faixa está entre os valores de uma regra existente";
            throw new Exception ($erro_msg);
          }
          if ($ed317_arredondar == $oDadosRegraArredondamentoFaixa->ed317_arredondar) {
      
            $erro_msg  = "Tipo de regra de arredondamento já cadastrado.";
            throw new Exception ($erro_msg);
          }
      
        }
      } catch (Exception $eErro) {
        
        db_msgbox($eErro->getMessage());
        
        $lErro = true;
        $sUrl .= "&ed317_sequencial={$ed317_sequencial}&ed317_inicial={$ed317_inicial}&ed317_final={$ed317_final}";
        $sUrl .= "&ed317_arredondar={$ed317_arredondar}&chavepesquisa={$chavepesquisa}&opcao=alterar";
        $sUrl .= "&sqlerro=false&iCasasDecimais={$iCasasDecimais}&sDisabled={$sDisabled}";
        
        db_redireciona( $sUrl );
      }
      
      if ($lErro == false) {
      
        $oDaoRegraArredondamentoFaixa->alterar($ed317_sequencial);
        $erro_msg = $oDaoRegraArredondamentoFaixa->erro_msg;
        if ($oDaoRegraArredondamentoFaixa->erro_status == 0) {
          
          $sqlerro = true;
          db_msgbox($erro_msg);
        }
        
        db_fim_transacao($sqlerro);
        
        $sUrl .= "&iCasasDecimais={$iCasasDecimais}&sDisabled={$sDisabled}";
        
        db_redireciona( $sUrl );
      }
    }
  }
  if (isset($excluir)) {
    
    if ($sqlerro == false) {
      
      db_inicio_transacao();
      $oDaoRegraArredondamentoFaixa->excluir($ed317_sequencial);
      $erro_msg = $oDaoRegraArredondamentoFaixa->erro_msg;
      if ($oDaoRegraArredondamentoFaixa->erro_status == 0) {
        $sqlerro = true;
      }
      db_fim_transacao($sqlerro);
    }
  }
  if (isset($opcao)) {
     
    $result = $oDaoRegraArredondamentoFaixa->sql_record($oDaoRegraArredondamentoFaixa->sql_query($ed317_sequencial));
    if ($result != false && $oDaoRegraArredondamentoFaixa->numrows > 0) {
      db_fieldsmemory($result,0);
    }
  }
?>
<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
  <link href="estilos.css" rel="stylesheet" type="text/css">
  </head>
  <body>
    <div class="container">
      <?php
    	  require_once(modification("forms/db_frmregraarredondamentofaixa.php"));
      ?>
    </div>
  </body>
</html>
<?php
  if (isset($alterar) || isset($excluir) || isset($incluir)) {
    
    if ($oDaoRegraArredondamentoFaixa->erro_campo != "") {
      echo "<script> document.form1.".$oDaoRegraArredondamentoFaixa->erro_campo.".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1.".$oDaoRegraArredondamentoFaixa->erro_campo.".focus();</script>";
    }
  }
?>