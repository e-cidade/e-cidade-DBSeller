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
require_once(modification("libs/db_app.utils.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("std/DBDate.php"));
require_once(modification("fpdf151/pdfnovo.php"));
require_once(modification("model/issqn/GeracaoArquivoSimplesNacional.model.php"));
require_once(modification("classes/db_arquivosimplesimportacaoretorno_classe.php"));
use App\Domain\Configuracao\Helpers\StorageHelper;
use ECidade\Lib\File\FileEstorage;

define('MENSAGENS', 'tributario.issqn.iss1_processararquivosimplesnacional.');
$parametros = JSON::requestParameters();
$retorno = (object)['erro' => false, 'mensagem' => ''];
$retorno->aArquivo = [];
try {

  switch ($parametros->acao) {

    case 'getCnae' :

      $aCnaes                        = array();
      $aCnaesInvalidos               = array();
      $oDaoArquivoSimplesImportacao  = new GeracaoArquivoSimplesNacional();

      $aCnaes = $oDaoArquivoSimplesImportacao->getCnae( $parametros->iArquivo );

      if( empty($aCnaes) ){
        throw new BusinessException( _M( MENSAGENS . 'nenhum_cnae_encontrado' ) );
      }

      /**
       * Buscar CNAES não encontrados
       */
      $aCnaesInvalidos = $oDaoArquivoSimplesImportacao->getCnae( $parametros->iArquivo, false );
      if( !empty($aCnaesInvalidos) ) {

        $aInvalidos = array( 'q71_estrutural' => 'Y' , 'q71_descr' => urlencode('CNAES NÃO ENCONTRADOS') );
        array_push( $aCnaes, $aInvalidos );
      }

      if( empty($aCnaes) ){
        throw new BusinessException( _M( MENSAGENS . 'nenhum_cnae_encontrado' ) );
      }

      $retorno->aCnaes = $aCnaes;
    break;

    case "gerar":
      
      $aEmpresas                      = JSON::create()->parse($parametros->aEmpresas);
      $oDaoArquivoSimples             = new cl_arquivosimplesimportacao();
      $oGeracaoArquivoSimplesNacional = new GeracaoArquivoSimplesNacional();
      $oGeracaoArquivoSimplesNacional->setArquivo( $parametros->iArquivo );

      if (!$oGeracaoArquivoSimplesNacional->isValido()) {
        throw new BusinessException(_M(MENSAGENS."preenchimento_obrigatorio"));
      }

      $sSql                = $oDaoArquivoSimples->sql_query( $parametros->iArquivo,
                                                             'q64_datalimitevencimentos, q64_processado' );
      $rsDaoArquivoSimples = $oDaoArquivoSimples->sql_record( $sSql );
      if ($oDaoArquivoSimples->numrows <= 0) {

        throw new BusinessException( _M( MENSAGENS . 'arquivo_nao_encontrado' ) );
      }
        
      $oArquivoSimples = db_utils::fieldsMemory($rsDaoArquivoSimples, 0);
      
      db_inicio_transacao();
      if ($oArquivoSimples->q64_processado == 'f') {
       
        $oDaoArquivoSimples->q64_processado = 't';
        $oDaoArquivoSimples->q64_sequencial = $parametros->iArquivo;
        $oDaoArquivoSimples->alterar($parametros->iArquivo);
         // Atualiza os registros com as alterações manuais 
        foreach ( $aEmpresas as $oEmpresa ) {
   
          $oDaoArquivoSimplesImportacaoDetalhe = new cl_arquivosimplesimportacaodetalhe();
          $sSqlArquivoSimplesImportacaoDetalhe = $oDaoArquivoSimplesImportacaoDetalhe->sql_query_file($oEmpresa->q142_sequencial);
          $rsArquivoSimplesImportacaoDetalhe   = $oDaoArquivoSimplesImportacaoDetalhe->sql_record($sSqlArquivoSimplesImportacaoDetalhe);
          $oArquivoSimplesImportacaoDetalhe    = db_utils::fieldsMemory($rsArquivoSimplesImportacaoDetalhe, 0);
        
          if($oArquivoSimplesImportacaoDetalhe->q142_apto == $oEmpresa->q142_apto) {
            
            throw new Exception("Registro alterado durante processamento. Processo cancelado");
          } 
        
          $oDaoArquivoSimplesImportacaoDetalhe->q142_sequencial = $oEmpresa->q142_sequencial;
          $oDaoArquivoSimplesImportacaoDetalhe->q142_apto       = $oEmpresa->q142_apto; 
          if($oEmpresa->q142_apto == "f") {
           $oDaoArquivoSimplesImportacaoDetalhe->q142_observacao = $oEmpresa->q142_observacao; 
          }
        
          $oDaoArquivoSimplesImportacaoDetalhe->alterar($oEmpresa->q142_sequencial);           
        }

        $retorno->aArquivo[] = $oGeracaoArquivoSimplesNacional->gerarTxt();       
        
      }  
      
      if ($oArquivoSimples->q64_processado == 't' || $parametros->lReprocessamento) {
        
        
        
        $sArquivoEmpresaAdicionada  = $oGeracaoArquivoSimplesNacional->gerarTxtArquivosADC($aEmpresas);
        if($sArquivoEmpresaAdicionada) {
          
          $retorno->aArquivo[] = $sArquivoEmpresaAdicionada;
        }
        $sArquivoEmpresaExcluida = $oGeracaoArquivoSimplesNacional->gerarTxtArquivosEXC($aEmpresas);
        if($sArquivoEmpresaExcluida) {

          $retorno->aArquivo[] = $sArquivoEmpresaExcluida;
        }
        
        if(count($retorno->aArquivo) == 0 && count($aEmpresas) > 0) {

          throw new Exception("Não gerou arquivo(s) para as empresas modificadas");
           
        }
        // Atualiza os registros 
        foreach ( $aEmpresas as $oEmpresa ) {
          
          $oDaoArquivoSimplesImportacaoDetalhe = new cl_arquivosimplesimportacaodetalhe();
          $sSqlArquivoSimplesImportacaoDetalhe = $oDaoArquivoSimplesImportacaoDetalhe->sql_query_file($oEmpresa->q142_sequencial);
          $rsArquivoSimplesImportacaoDetalhe   = $oDaoArquivoSimplesImportacaoDetalhe->sql_record($sSqlArquivoSimplesImportacaoDetalhe);
          $oArquivoSimplesImportacaoDetalhe    = db_utils::fieldsMemory($rsArquivoSimplesImportacaoDetalhe, 0);
          
         if($oArquivoSimplesImportacaoDetalhe->q142_apto == $oEmpresa->q142_apto) {
           
           throw new Exception("Registro alterado durante processamento. Processo cancelado");
          } 
          
          $oDaoArquivoSimplesImportacaoDetalhe->q142_sequencial = $oEmpresa->q142_sequencial;
          $oDaoArquivoSimplesImportacaoDetalhe->q142_apto       = $oEmpresa->q142_apto=='t'; 
          if($oEmpresa->q142_apto == "f") {

            $oDaoArquivoSimplesImportacaoDetalhe->q142_observacao = $oEmpresa->q142_observacao;
          }
          
          $oDaoArquivoSimplesImportacaoDetalhe->alterar($oEmpresa->q142_sequencial);           
        }
      }
       
      foreach($retorno->aArquivo as $arquivo) {
        
        $idStorage   = StorageHelper::uploadArquivo($arquivo, null, true);
        $nomeArquivo = basename($arquivo);
        $daoArquivosSimplesImportacaoRetorno                   = new cl_arquivosimplesimportacaoretorno();
        $daoArquivosSimplesImportacaoRetorno->q182_id_usuario  = db_getsession('DB_id_usuario');
        $daoArquivosSimplesImportacaoRetorno->q182_nomearquivo = $nomeArquivo;
        $daoArquivosSimplesImportacaoRetorno->q182_id_storage  = $idStorage;
        $daoArquivosSimplesImportacaoRetorno->q182_arquivosimplesimportacao = $parametros->iArquivo;
        $daoArquivosSimplesImportacaoRetorno->incluir(null);
        if ($daoArquivosSimplesImportacaoRetorno->erro_status == 0) {

          throw new Exception("[Erro ao vincular arquivo armazenado". $daoArquivosSimplesImportacaoRetorno->erro_msg);
        }
      }

      db_fim_transacao();

    break;

    case "getArquivos":
      
      
      $retorno->aArquivos = array();

      $oDaoArquivoSimplesImportacao = new cl_arquivosimplesimportacao();
      $sWhere = ($parametros->lReprocessamento=="true"? 'q64_processado is true' : '');
      
      $sSqlArquivoSimplesImportacao  = $oDaoArquivoSimplesImportacao->sql_query_file( null,
                                                                                      'q64_sequencial, q64_nomearquivo',
                                                                                      'q64_sequencial desc',
                                                                                      $sWhere );
       
      $rsDAOArquivoSimplesImportacao = $oDaoArquivoSimplesImportacao->sql_record( $sSqlArquivoSimplesImportacao );

      if ( $oDaoArquivoSimplesImportacao->numrows <= 0 ) {
        throw new BusinessException( _M( MENSAGENS . 'nenhum_arquivo_encontrado' ) );
      }

      $aArquivoSimplesImportacao = db_utils::getCollectionByRecord( $rsDAOArquivoSimplesImportacao );

      foreach ($aArquivoSimplesImportacao as $aDados ) {
        $retorno->aArquivos[] = array('iSequencial' => $aDados->q64_sequencial, 'sLabel' => $aDados->q64_nomearquivo);
      }
    break;

    case "getDataVencimento":

      $oDaoArquivoSimplesImportacao  = new cl_arquivosimplesimportacao();

      $sSql                          = $oDaoArquivoSimplesImportacao->sql_query( $parametros->iArquivo,
                                                                                 'q64_data, q64_datalimitevencimentos, q64_processado' );
      $rsDAOArquivoSimplesImportacao = $oDaoArquivoSimplesImportacao->sql_record( $sSql );

      if ($oDaoArquivoSimplesImportacao->numrows <= 0) {
        throw new BusinessException( _M( MENSAGENS . 'arquivo_nao_encontrado' ) );
      }

      $oArquivoSimplesImportacao = db_utils::fieldsMemory($rsDAOArquivoSimplesImportacao, 0);

      $dtData = '';

      if (!empty($oArquivoSimplesImportacao->q64_datalimitevencimentos)) {
        $oData  = new DBDate($oArquivoSimplesImportacao->q64_datalimitevencimentos);
        $dtData = $oData->convertTo(DBDate::DATA_PTBR);
      }

      $retorno->lProcessado = ($oArquivoSimplesImportacao->q64_processado == 't');
      $retorno->dtData      = $dtData;
    break;

    /**
     * Faz o processamento do arquivo quando necessário
     * Condições para ser feito o processamento:
     *   -- Estar na rotina de reprocessamento
     *   -- Não ter sido processado ainda
     */
    case 'validacaoAutomatica':
      
      $retorno->aEmpresasModificadas = [];
      $oDaoArquivoSimples  = new cl_arquivosimplesimportacao();
      $sSql                = $oDaoArquivoSimples->sql_query( $parametros->iArquivo,
                                                             'q64_datalimitevencimentos, q64_processado' );
      $rsDaoArquivoSimples = $oDaoArquivoSimples->sql_record( $sSql );

      if ($oDaoArquivoSimples->numrows <= 0) {
        throw new BusinessException( _M( MENSAGENS . 'arquivo_nao_encontrado' ) );
      }

      $oArquivoSimples = db_utils::fieldsMemory($rsDaoArquivoSimples, 0);

      /**
       * Verifica se não foi processado ainda ou se veio da rotina de reprocessamento
       */
      if ($oArquivoSimples->q64_processado == 'f' || $parametros->lReprocessamento) {

        $oData  = new DBDate($parametros->dtLimite);
        $dtData = $oData->convertTo(DBDate::DATA_EN);

        db_inicio_transacao();

        $oGeracaoArquivoSimplesNacional = new GeracaoArquivoSimplesNacional();

        $oGeracaoArquivoSimplesNacional->setArquivo( $parametros->iArquivo );
        $oGeracaoArquivoSimplesNacional->setDataLimite( $dtData );
        $lReprocessamento              = $parametros->lReprocessamento==1?true:false;
        db_putsession("DB_desativar_account", true);
        $oGeracaoArquivoSimplesNacional->validacaoAutomatica($lReprocessamento);
        db_putsession("DB_desativar_account", false);
        // Somente retornar registros quando for rotina de reprocessamento
        $retorno->aEmpresasModificadas = $oGeracaoArquivoSimplesNacional->empresasModificadasReprocessamento();
        /**
         * Altera o arquivo que foi validado como processado e a data limite utilizada
         */
        $oDaoArquivoSimples->q64_datalimitevencimentos = $dtData;
        $oDaoArquivoSimples->q64_sequencial            = $parametros->iArquivo;
        $oDaoArquivoSimples->alterar( $parametros->iArquivo );
        db_fim_transacao();
      }
    break;

    case "getEmpresas":

      $oArquivosSimples   = new GeracaoArquivoSimplesNacional();
      $oArquivosSimples->setArquivo( $parametros->iArquivo );
      $aEmpresas          = $oArquivosSimples->getEmpresasByCnae($parametros->estrutural);
     
      $retorno->aEmpresas = $aEmpresas;
    break;

    case "setAptos":

      $oArquivoSimples = new GeracaoArquivoSimplesNacional();
      $oArquivoSimples->setAptos($parametros->oEmpresas, $parametros->lApto);

    break;

    case "getRegistro":

      $oDaoArquivoSimplesImportacaoDetalhe = new cl_arquivosimplesimportacaodetalhe();
      $sql = $oDaoArquivoSimplesImportacaoDetalhe->sql_query_file($parametros->sequencial);      
      $rsDAOArquivoSimplesImportacaoDetalhe = $oDaoArquivoSimplesImportacaoDetalhe->sql_record($sql);
      $retorno->registro = db_utils::fieldsMemory($rsDAOArquivoSimplesImportacaoDetalhe, 0);
    break;
    
    case "getRelatorio":
      $oGeracaoArquivoSimplesNacional = new GeracaoArquivoSimplesNacional();
      $oGeracaoArquivoSimplesNacional->setArquivo( $parametros->iArquivo);
      $retorno->sInconsistencias  = $oGeracaoArquivoSimplesNacional->relatorioInconsistencias();      
    break;

    case "buscarArquivos":

      $oGeracaoArquivoSimplesNacional = new GeracaoArquivoSimplesNacional();
      $oGeracaoArquivoSimplesNacional->setArquivo( $parametros->iArquivo);
      $retorno->listaArquivos  = $oGeracaoArquivoSimplesNacional->getArquivosGerados();     
    break;

    case "downloadArquivo":
      
      $daoArquivosSimplesImportacaoRetorno = new cl_arquivosimplesimportacaoretorno();
      $sSqlArquivo                         = $daoArquivosSimplesImportacaoRetorno->sql_query_file($parametros->id_arquivo);
      $rsArquivo                           = $daoArquivosSimplesImportacaoRetorno->sql_record($sSqlArquivo);
      $oArquivo                            = db_utils::fieldsMemory($rsArquivo, 0);
      $storage                             = StorageHelper::downloadArquivo($oArquivo->q182_id_storage);
      $pathArquivo                         = "tmp/{$oArquivo->q182_nomearquivo}";
      if(!rename($storage, $pathArquivo)) {

         throw new Exception("Não foi possível baixar o arquivo");
      }

      $retorno->urlArquivo = $pathArquivo;
    break;
  }

} catch (Exception $oErro) {

  db_fim_transacao(true);
  $retorno->erro   = true;
  $retorno->mensagem = $oErro->getMessage();
}

$retorno->mensagem = urlencode($retorno->mensagem);
echo JSON::create()->stringify($retorno);
