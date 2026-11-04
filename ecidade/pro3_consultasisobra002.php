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

require_once(modification("classes/db_obrasresp_classe.php"));
require_once(modification("classes/db_obraspropri_classe.php"));
require_once(modification("classes/db_obraslote_classe.php"));
require_once(modification("classes/db_obraslotei_classe.php"));
require_once(modification("classes/db_obras_classe.php"));
require_once(modification("classes/db_obrasiptubase_classe.php"));

require_once(modification("dbforms/verticalTab.widget.php"));

use ECidade\Tributario\Projetos\Obras\Sisobras\Webservice\Arquivo\ConsultarDocumento;
use ECidade\Tributario\Projetos\Obras\Sisobras\Webservice\Manutencao;

$clobrasresp     = new cl_obrasresp;
$clobraspropri   = new cl_obraspropri;
$clobraslote     = new cl_obraslote;
$clobraslotei    = new cl_obraslotei;
$clobras         = new cl_obras;
$clobrasiptubase = new cl_obrasiptubase;

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);

if (empty($tipoDocumento) || empty($numeroDocumento) || empty($anoDocumento)) {
    db_msgbox("Preencha os campos para efetuar a pesquisa.");
    echo "<script>parent.db_iframe_consultasisobra.hide();</script>";
} else if (isset($tipoDocumento) && isset($numeroDocumento) && isset($anoDocumento)) {
  // Busca dados da tabela parprojetos
  $anousu = db_getsession('DB_anousu');
  $sqlBuscaParProjetos = "SELECT * FROM parprojetos WHERE ob21_anousu = $anousu;";
  $resultBuscaParProjetos = db_query($sqlBuscaParProjetos);
  $dadosBuscaParProjetos = db_utils::fieldsMemory($resultBuscaParProjetos, 0);

  // Atribui local e senha do certificado A1 cadastrados nos parametros dos projetos.
  $localA1 = $dadosBuscaParProjetos->ob21_localcertificadoa1;
  $senhaA1 = $dadosBuscaParProjetos->ob21_senhacertificadoa1;

  // Efetua busca do documento com número e ano, via webservice
  $consultaDocumento = '';
  $tipoDocumento == 1 ? $consultaDocumento = 'alvara' : $consultaDocumento = 'habitese';
  $oConsultarDocumento = new ConsultarDocumento($consultaDocumento, $numeroDocumento, $anoDocumento);
  $oConsultarDocumentoXml = $oConsultarDocumento->gerar()->saveXML();
  $client = new Manutencao($oConsultarDocumentoXml, $oConsultarDocumento->getOperacao(), $localA1, $senhaA1);
  $client->processarRequisicao();
  $getRespostaConsultarDocumento = $client->getRespostaConsultarDocumento();

  if (substr($getRespostaConsultarDocumento['codRetorno'], 0, 2) == 'ER') {
    db_msgbox($getRespostaConsultarDocumento['codRetorno'].' - '.utf8_decode($getRespostaConsultarDocumento['descricao']));
    echo "<script>parent.db_iframe_consultasisobra.hide();</script>";
  } else {
    $xml = html_entity_decode($getRespostaConsultarDocumento['xmlRetornoConsulta'], ENT_NOQUOTES, 'UTF-8');

    $dom = dom_import_simplexml($getRespostaConsultarDocumento['dadosRetornoConsulta'])->ownerDocument;
    $doc->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $xmlString = $dom->saveXML();

    // if (isset($xmlEnviadoDetalhamento) && $xmlEnviadoDetalhamento) {
    //   // (string)$getRespostaConsultarDocumento['xmlRetornoConsulta']
    //   echo $xml;
    // }
?>
<html>
<head>
  <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <link href="estilos.css" rel="stylesheet" type="text/css">
  <link href="estilos/tab.style.css" rel="stylesheet" type="text/css">
  <style>
    #elemento_principal {
      margin: 5px;
      width: 100%;
    } 
    #elemento_principal tr td:first-child {
      width: 150px;
    }
  </style>
</head>
<body bgcolor=#CCCCCC>
  <center>
	<table width="800" border="0" cellspacing="0" cellpadding="0">
		<tr bgcolor="#CCCCCC">
      <td align="center" valign="top" bgcolor="#CCCCCC">
				<table width="790" border="0" >
				  <tr>
						<td>
              <?php
              // =========== Informações do Alvará =========== 
              if ($tipoDocumento == 1) {
              ?>
              <fieldset>
                <legend><b>Dados do Alvará:</b></legend>
                <table id="elemento_principal">
                  <tr> 
                    <td><strong>Número do Alvará</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->numeroAlvara?></td>
                  </tr>
                  <tr> 
                    <td nowrap><strong>Nome da Obra: </strong></td>
                    <td bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->nomeObra?></td>
                  </tr>
                  <tr> 
                    <td><strong>Data do Alvará</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->dataAlvara?></td>
                  </tr>
                  <tr> 
                    <td><strong>Data Início da Obra</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->dataInicioObra?></td>
                  </tr>
                  <tr> 
                    <td><strong>Data Final da Obra</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->dataFinalObra?></td>
                  </tr>
                  <tr> 
                    <td><strong>Tipo de Alvará</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->tipoAlvara?></td>
                  </tr>
                  <tr> 
                    <td nowrap ><strong>Responsável Execução Obra:</strong> </td>
                    <td nowrap bgcolor="#FFFFFF"> 
                      <?php echo $oObras->ob01_tiporesp ." - ". $oObras->ob02_descr; ?>
                    </td>
                  </tr>
                </table>
              </fieldset>
                  <!-- Dados de Endereço da Obra -->
              <fieldset>
                <legend><b>Endereço da Obra:</b></legend>
                <table id="elemento_principal">
                  <tr> 
                    <td><strong>CEP</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->enderecoObra->cep?></td>
                  </tr>
                  <tr> 
                    <td><strong>Tipo de Logradouro</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->enderecoObra->tipoLogradouro?></td>
                  </tr>
                  <tr> 
                    <td><strong>Logradouro</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->enderecoObra->logradouro?></td>
                  </tr>
                  <tr> 
                    <td><strong>Número</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->enderecoObra->numero?></td>
                  </tr>
                  <tr> 
                    <td><strong>Bairro</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->enderecoObra->bairro?></td>
                  </tr>
                </table>
              </fieldset>

              <!-- Dados da Área -->
              <fieldset>
                <legend><b>Área Principal:</b></legend>
                <table id="elemento_principal">
                  <tr> 
                    <td><strong>Categoria</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->area->areaPrincipal->categoria?></td>
                  </tr>
                  <tr> 
                    <td><strong>Destinação</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->area->areaPrincipal->destinacao?></td>
                  </tr>
                  <tr> 
                    <td><strong>Tipo de Obra</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->area->areaPrincipal->tipoObra?></td>
                  </tr>
                  <tr> 
                    <td><strong>Área (m²)</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->area->areaPrincipal->area?></td>
                  </tr>
                </table>
              </fieldset>

              <!-- Dados do Proprietário da Obra -->
              <fieldset>
                <legend><b>Proprietário da Obra:</b></legend>
                <table id="elemento_principal">
                  <tr> 
                    <td><strong>CPF</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->proprietarioObra->cpf?></td>
                  </tr>
                </table>
              </fieldset>

              <!-- Informações Adicionais do Alvará -->
              <fieldset>
                <legend><b>Informações Adicionais:</b></legend>
                <table id="elemento_principal">
                  <tr> 
                    <td><strong>Número do Processo</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->infoAdicionais->numeroProcesso?></td>
                  </tr>
                </table>
              </fieldset>

              <!-- Dados Responsável Técnico -->
              <fieldset>
                <legend><b>Responsável Técnico:</b></legend>
                <table id="elemento_principal">
                  <tr> 
                    <td><strong>Nome do(a) Engenheiro(a)</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->infoAdicionais->responsavelTecnico->engenheiro->nome?></td>
                  </tr>
                  <tr> 
                    <td><strong>CREA</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->infoAdicionais->responsavelTecnico->engenheiro->crea?></td>
                  </tr>
                  <tr> 
                    <td><strong>ART</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->infoAdicionais->responsavelTecnico->engenheiro->art?></td>
                  </tr>
                </table>
              </fieldset>

              <!-- Dados Responsável Projeto -->
              <fieldset>
                <legend><b>Responsável Projeto:</b></legend>
                <table id="elemento_principal">
                  <tr> 
                    <td><strong>Nome do(a) Engenheiro(a)</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->infoAdicionais->responsavelProjeto->engenheiro->nome?></td>
                  </tr>
                  <tr> 
                    <td><strong>CREA</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->infoAdicionais->responsavelProjeto->engenheiro->crea?></td>
                  </tr>
                  <tr> 
                    <td><strong>ART</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->infoAdicionais->responsavelProjeto->engenheiro->art?></td>
                  </tr>
                </table>
              </fieldset>
              <?php
              }
              ?>

              <?php
              // =========== Informações do Habite-se =========== 
              if ($tipoDocumento == 2) {
              ?>
              <fieldset>
                <legend><b>Dados do Habite-se:</b></legend>
                <table id="elemento_principal">
                  <tr> 
                    <td><strong>Número do Habite-se</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->numeroHabitese?></td>
                  </tr>
                  <tr> 
                    <td><strong>Data do Habite-se</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->dataHabitese?></td>
                  </tr>
                  <tr> 
                    <td><strong>Data Final da Obra</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->dataFinalObra?></td>
                  </tr>
                  <tr> 
                    <td><strong>Tipo de Habite-se</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->tipoHabitese?></td>
                  </tr>
                </table>
              </fieldset>

              <!-- Dados da Área -->
              <fieldset>
                <legend><b>Área Principal:</b></legend>
                <table id="elemento_principal">
                  <tr> 
                    <td><strong>Categoria</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->area->areaPrincipal->categoria?></td>
                  </tr>
                  <tr> 
                    <td><strong>Destinação</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->area->areaPrincipal->destinacao?></td>
                  </tr>
                  <tr> 
                    <td><strong>Tipo da Obra</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->area->areaPrincipal->tipoObra?></td>
                  </tr>
                  <tr> 
                    <td><strong>Área (m²)</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->area->areaPrincipal->area?></td>
                  </tr>
                  </table>
              </fieldset>

              <!-- Informações Adicionais -->
              <fieldset>
                <legend><b>Informações Adicionais:</b></legend>
                <table id="elemento_principal">
                  <tr>
                    <td><strong>Número do Alvará</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->numeroAlvara?></td>
                  </tr>
                  <tr> 
                    <td><strong>Data do Alvará</strong></td>
                    <td nowrap bgcolor="#FFFFFF"><?=(string)$getRespostaConsultarDocumento['dadosRetornoConsulta']->dataAlvara?></td>
                  </tr>
                  </table>
              </fieldset>
              <?php
              }
              ?>
						</td>
					</tr>          
				  <tr>
					  <td colspan="4" align="left">
              <!-- 
              <fieldset>
                <legend><b>XML Enviado:</b></legend>
                <table>
                  <tr>
                    <td style="max-width:100px">
                      <?php 
                        echo '<pre>', 
                        // htmlentities($xml),
                        htmlspecialchars($xmlString),
                        '</pre>';

                        // $escaped = htmlentities($xmlString);
                        // $formatted = str_replace('&lt;', '<span style="color:blue">&lt;', $escaped);
                        // $formatted = str_replace('&gt;', '&gt;</span>', $formatted);
                        // echo "<pre>$formatted</pre>\n";
                      ?>
                    </td>
                  </tr>
                </table>
	            </fieldset>-->
              <center>
                <input type="button" onClick="parent.db_iframe_consultasisobra.hide();" value="Fechar" />
              </center>
            </td>
				  </tr>
			  </table>
			</td>
	  </tr>
  </table>
  </center>
</body>
</html>
<?php 
  }
}
?>