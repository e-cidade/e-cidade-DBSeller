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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("fpdf151/pdf.php"));

$oGet = db_utils::postMemory( $_GET );

/**
 * Cria a instância de Escola para preenchimento do cabeçalho
 */
$oEscola = EscolaRepository::getEscolaByCodigo( db_getsession( "DB_coddepto" ) );

$oJson = new Services_JSON();

/**
 * Lê o contéudo do arquivo de log gerado
 */
$sArquivoLog  = file_get_contents( $oGet->sCaminhoArquivo );
$oJsonArquivo = $oJson->decode( $sArquivoLog );

/**
 * Array para armazenar as mensagens de erro
 * @param array
 */
$aErros = array();

/**
 * Array para armazenar os alunos importados com sucesso
 * @param array
 */
$aSucessos = array();

/**
 * Define Largura e Altura padrões para a linha do arquivo PDF
 */
$iLargura = 192;
$iAltura  = 4;
 
/**
 * Caso o atributo aLogs não tenha sido setado ou não existam logs gerados, apresenta a mensagem e redireciona para 
 * o formulário de importação
 */
if ( !isset( $oJsonArquivo->aLogs ) || count( $oJsonArquivo->aLogs ) == 0 ) {
  
  db_msgbox( "Não foram encontrados dados com os filtros informados para geração do arquivo de log." );
  db_redireciona( "edu4_importacaosituacaoaluno001.php" );
}

/**
 * Percorre os logs gerados, validando o tipo de erro para armazenar no array
 */
foreach ( $oJsonArquivo->aLogs as $oLog ) {

  if ( trim( $oLog->tipo ) == "ERRO" ) {
    $aErros[] = utf8_decode( $oLog->sMensagem );
  }
  
  if ( trim( $oLog->tipo ) == "INFO" ) {
    $aSucessos[] = utf8_decode( $oLog->sMensagem );
  }
}

/**
 * Dados do cabeçalho
 */
$head1 = "IMPORTAÇÃO SITUAÇÃO DO ALUNO DO CENSO";
$head3 = "ESCOLA: {$oEscola->getCodigo()} - {$oEscola->getNome()}";
$head4 = "ANO: {$oGet->iAno}";

/**
 * Cria a instância de PDF e inicializa os métodos padrões
 */
$oPdf = new PDF();
$oPdf->Open();
$oPdf->AliasNbPages();

/**
 * Percorre primeiramente o array com as mensagens de erro
 */
$iTotalErros = count( $aErros );

if ( $iTotalErros > 0 ) {

  $head6 = "Registros com erros";
  $oPdf->AddPage();
  $oPdf->SetFont( "arial", "", 7 );
  $oPdf->SetFillColor( 225, 225, 225 );
  
  for ( $iContador = 0; $iContador < $iTotalErros; $iContador++ ) {
    
    $iPreenchimento = 0;
    
    if ($iContador % 2 != 0) {
      $iPreenchimento = 1;
    }
    
    $oPdf->MultiCell($iLargura, $iAltura, $aErros[$iContador], 0, 'L', $iPreenchimento);
  }
}

/**
 * Percorre os registros importados com sucesso. Inicia a partir de uma nova página
 */
$iTotalSucesso = count( $aSucessos );

if ( $iTotalSucesso > 0 ) {
  
  $head6 = "Alunos Importados com Sucesso";
  
  $oPdf->AddPage();
  $oPdf->SetFont( "arial", "", 7 );
  $oPdf->SetFillColor( 225, 225, 225 );
  
  for ( $iContador = 0; $iContador < $iTotalSucesso; $iContador++ ) {
    
    $iPreenchimento = 0;
    
    if ( $iContador % 2 != 0 ) {
      $iPreenchimento = 1;
    }
    
    $oPdf->MultiCell($iLargura, $iAltura, $aSucessos[$iContador], 0, 'L', $iPreenchimento);
  }
}

$oPdf->Output();
?>