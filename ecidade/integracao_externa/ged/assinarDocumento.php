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

$sResposta = '';
echo "<PRE>";
if ( isset($_GET['processo_compra']) && !empty($_GET['processo_compra']) ) {

  $oParametrosSoap           = new stdClass();
  $oParametrosSoap->uri      = "http://swp10:8080";
  $oParametrosSoap->location = "http://swp10:8080/wsged/services/GED?wsdl";
  $oSoapClient               = new SoapClient(null, (array)$oParametrosSoap);
  $sNomeParametro            = "processo_compra";
  $sTipoParametro            = "NUMERO";
  $sValorParametro           = $_GET['processo_compra'];
  $sTipoComparacao           = '0';
  $sAcervo                   = null;
  $sResposta                 = $oSoapClient->buscarListaDocumento( $sNomeParametro, $sTipoParametro, $sValorParametro, $sTipoComparacao, $sAcervo);

}
echo "</PRE>";
?>

<html>
  <head>
    <style>
      span {
       padding: 3px;
       border-left  : 2px groove #ccc;
       border-bottom: 2px groove #ccc;
      }
      a{
        text-decoration:none;
      }
      div > div {
       padding      : 4px;
       margin       : 5px;
       width        : 200px;
      }
      textarea  {
       width: 100%;
       height:100px;
      }
    </style>

      <applet id="appletassinador" name="appletassinador" archive="http://172.16.144.200:8080/ged/assinadors.jar" code="signer/SignerApplet.class" width="1" height="1">
       <param name="servidor" value="http://172.16.144.200:8080/wsged/faces/download?param=" />
       <param name="upload" value="http://172.16.144.200:8080/wsged/faces/upload" />
       <param name="arquivo.0" value="/acervo001/emp2_emiteautori002.php.pdf" />
       <param name="arquivo.1" value="/acervo001/com2_emiteprocessocompra002.php.pdf" />
       <param name="arquivo.2" value="/acervo001/com2_mapaorc002.php.pdf" />
       <param name="arquivo.3" value="/acervo001/com2_emitesolicita002.php.pdf" />
       <param name="arquivo.4" value="/acervo001/teste_assinatura.pdf "/>
       Seu navegador não suporta applet.
      </applet>
  </head>
  <body>
    <fieldset>
      <legend><strong> Busca: </strong></legend>
      <B>Processo de Compra:</B> <input id="filtro" /><input type="BUTTON" value="Vai" onclick="window.location.href='?processo_compra='+$('filtro').value"/><BR />

      <fieldset>
        <legend><strong> Resposta: </strong></legend>
        <textarea rows="5" cols="50" id="resposta"><?php echo $sResposta; ?></textarea><br />
      </fieldset>
      <fieldset id="resultado">
        <legend><strong> Resultado: </strong></legend>
      </fieldset>
    </fieldset>
  </body>
</html>
<script>



(function(){

  if ( $('resposta').value.trim() == "" ) {
    return;
  }  
  var oTeste = eval("(" + $('resposta').value + ")");
  console.log(oTeste);

  criarListaArquivos(oTeste);

})();

function $( sNomeElemento ) {
  return document.getElementById(sNomeElemento);
}

/**
 * criarListaArquivos
 *
 * @param oRetornoCOnsulta  $oRetornoCOnsulta 
 * @access public
 * @return void
 */
function criarListaArquivos( oRetornoCOnsulta ) {

  var criar             = function (sNomeElemento) { return document.createElement(sNomeElemento); };
  var oDestino          = $('resultado');
  var oDivPrincipal     = criar("div");

  for ( var iLinha in oRetornoCOnsulta ) {
    if (!oRetornoCOnsulta[iLinha]) {
      continue;
    }
    var oResultado              = oRetornoCOnsulta[iLinha];
    var oLinha                  = criar("div");
    var oColunaArquivo          = criar("span"); 
    var oColunaAssinatura       = criar("span");
    var oLink                   = criar("a");
    oLink.innerHTML             = "(" + oResultado.endereco +  ")";
    oLink.href                  = 'arquivos' + oResultado.endereco;
    oLink.target                = '_blank';
    var oAssinatura             = criar("a");
    oAssinatura.innerHTML       = 'Assinar';
    oAssinatura.href            = '#';
    oAssinatura.setAttribute( "onClick", "assinar("+ iLinha +");");//"oResultado.endereco );

    oLinha.appendChild(oColunaArquivo);
    oLinha.appendChild(oColunaAssinatura);
    oColunaArquivo.appendChild(oLink);
    oColunaAssinatura.appendChild(oAssinatura);
    oDivPrincipal.appendChild(oLinha);
  }

oDestino.appendChild(oDivPrincipal);
}
function assinar(index) {

  try {

    appt = document.applets['appletassinador'];
    var str = ""+index;
    appt.assinarPDF(str);
  } catch (e) {
    alert(e.message + " Verifique seu plugin applet.");
    return false;
  }
}
</script>