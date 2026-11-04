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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_utils.php"));

require_once(modification("dbforms/db_funcoes.php"));

$oGet  = db_utils::postMemory($_GET);
$oPost  = db_utils::postMemory($_POST);

try {
    
  $oServidor      = ServidorRepository::getInstanciaByCodigo($oGet->rh01_regist, $oGet->ano, $oGet->mes);
  $oContaBancaria      = $oServidor->getContaBancaria();
  if ($oContaBancaria) {
      $db83_sequencial     = $oContaBancaria->getSequencialContaBancaria();
      $db83_tipoconta      = $oContaBancaria->getTipoConta();
      $db83_codigooperacao = $oContaBancaria->getCodigoOperacao();
  }
  

  if (isset($oPost->salvar)) {
      
        db_inicio_transacao();
        
        $oContaBancaria = $oServidor->getContaBancaria();
        if ( $inputSequencialConta != "" ) {
            $oContaBancaria->setSequencialContaBancaria($inputSequencialConta);
        }
        $oContaBancaria->setCodigoBanco($inputCodigoBanco);
        $oContaBancaria->setNumeroAgencia($inputNumeroAgencia);
        $oContaBancaria->setDVAgencia($inputDvAgencia);
        $oContaBancaria->setNumeroConta($inputNumeroConta);
        $oContaBancaria->setDVConta($inputDvConta);
        $oContaBancaria->setIdentificador('0');
        $oContaBancaria->setCodigoOperacao($inputOperacao);
        $oContaBancaria->setTipoConta($cboTipoConta);
        $oContaBancaria->salvar();
                
        $oServidor->setContaBancaria($oContaBancaria);
        $oRetorno  = ServidorRepository::persistServidor($oServidor);

        db_msgbox("Dados bancários alterados com sucesso.");
        
        $clrhpesbanco    = new cl_rhpesbanco;
        $sWherePesBanco  = "     rh44_codban    = '{$oPost->inputCodigoBanco}'   ";
        $sWherePesBanco .= " and rh44_agencia   = '{$oPost->inputNumeroAgencia}' ";
        $sWherePesBanco .= " and rh44_dvagencia = '{$oPost->inputDvAgencia}'     ";
        $sWherePesBanco .= " and rh44_conta     = '{$oPost->inputNumeroConta}'   ";
        $sWherePesBanco .= " and rh44_dvconta   = '{$oPost->inputDvConta}'       ";
        $sWherePesBanco .= " and rh02_regist   <> '{$oGet->rh01_regist}'        ";
        $sWherePesBanco .= " and rh02_mesusu    = {$oGet->mes} ";
        $sWherePesBanco .= " and rh02_anousu    = {$oGet->ano} ";
        $sWherePesBanco .= " and rhpesrescisao.rh05_seqpes is null";
        $sSqlValidaRhPesBanco = "select distinct
                                        rh02_regist,
                                        z01_nome
                                   from rhpesbanco
                                        inner join rhpessoalmov  on rhpessoalmov.rh02_seqpes = rhpesbanco.rh44_seqpes
                                        inner join rhpessoal     on rhpessoal.rh01_regist    = rhpessoalmov.rh02_regist
                                        inner join cgm           on cgm.z01_numcgm           = rhpessoal.rh01_numcgm
    		                               left join rhpesrescisao on rhpessoalmov.rh02_seqpes = rhpesrescisao.rh05_seqpes
    		                        where {$sWherePesBanco}";
        $rsRhPesBanco = $clrhpesbanco->sql_record($sSqlValidaRhPesBanco);
        if ( $clrhpesbanco->numrows > 0 ) {
            $oDadosRhPesBanco    = db_utils::getCollectionByRecord($rsRhPesBanco);
            $sStrDadosServidores = "";
            foreach ($oDadosRhPesBanco as $oDados) {
                $sStrDadosServidores .= $oDados->rh02_regist." - ".$oDados->z01_nome."\\n";
            }
            db_msgbox("AVISO:\\nExistem servidores cadastrados com os mesmos dados de conta informados.\\n\\nServidor(es):\\n {$sStrDadosServidores}");
        }
        
        db_fim_transacao();
        echo "<script> parent.js_fechaJanelaManutencao() </script>";
        
  }
  
} catch ( Exception $oException ) {
      
   db_fim_transacao(true);
   db_msgbox("Erro ao Cadastrar dados bancários do Servidor\n".$oException->getMessage());
   
}


?>
<html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <link href='estilos.css' rel='stylesheet' type='text/css'/>
    <script language='JavaScript' type='text/javascript' src='scripts/scripts.js'></script>
    <script language='JavaScript' type='text/javascript' src='scripts/strings.js'></script>
    <script language='JavaScript' type='text/javascript' src='scripts/prototype.js'></script> 
    <script language='JavaScript' type='text/javascript' src='scripts/widgets/dbtextField.widget.js'></script>
    <script language='JavaScript' type='text/javascript' src='scripts/widgets/dbcomboBox.widget.js'></script>
    <script language='JavaScript' type='text/javascript' src='scripts/classes/DBViewContaBancariaServidor.js'></script> 
  </head>
  <body class="body-default">

    <form class="container" id="form1" name="form1" method="post">
      <?php 
         db_input('db83_sequencial',10,1,true,'hidden');
      ?>   
        <table>
         </tr>
          <tr>
            <td colspan="2">
                <div id="ctnContaBancariaServidor"></div>
            </td>
          </tr>
        </table>
      <input type="submit" value="Salvar" id="salvar" name="salvar" onclick="return salvarDados()" />
    </form>
  </body>
</html>

<script>
    var oContaBancariaServidor = new DBViewContaBancariaServidor($F('db83_sequencial'),'oContaBancariaServidor',false);
    oContaBancariaServidor.show('ctnContaBancariaServidor');
    oContaBancariaServidor.getDados($F('db83_sequencial'));

    /**
     * valida antes de colar no campo valor
     */

    $('inputCodigoBanco').onpaste = function(event) {
        return /^[0-9|.]+$/.test(event.clipboardData.getData('text/plain'));
    };

    $('inputDvConta').onpaste = function(event) {
        return /^[0-9|.xX]+$/.test(event.clipboardData.getData('text/plain'));
    };

    $('inputDvAgencia').onpaste = function(event) {
        return /^[0-9|.xX]+$/.test(event.clipboardData.getData('text/plain'));
    };
    $('inputNumeroAgencia').onpaste = function(event) {
        return /^[0-9|.]+$/.test(event.clipboardData.getData('text/plain'));
    };

    $('inputOperacao').onpaste = function(event) {
        return /^[0-9|.]+$/.test(event.clipboardData.getData('text/plain'));
    };

    $('inputNumeroConta').onpaste = function(event) {
        return /^[0-9|.]+$/.test(event.clipboardData.getData('text/plain'));
    };

    $('inputOperacao').onkeyup = function(event) {
        return js_ValidaCampos(this, 1, 'Código da Operação', false, false, event);
    };

function salvarDados() {
	return true;
}

</script>
