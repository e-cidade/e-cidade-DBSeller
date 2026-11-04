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


/**
 *  Configuração do Programa de Conversão
 */

 $ConfigINI = parse_ini_file("db_config.ini");

/**
 *  Configuração base Prefeitura
 */
 $ConfigConexaoPrefeitura["host"]     = $ConfigINI["ConPrefeitura_host"];
 $ConfigConexaoPrefeitura["port"]     = $ConfigINI["ConPrefeitura_port"];
 $ConfigConexaoPrefeitura["dbname"]   = $ConfigINI["ConPrefeitura_dbname"];
 $ConfigConexaoPrefeitura["user"]     = $ConfigINI["ConPrefeitura_user"];
 $ConfigConexaoPrefeitura["password"] = $ConfigINI["ConPrefeitura_password"];

/**
 *  Configuração base Destino
 */ 
 $ConfigConexaoDestino["host"]     = $ConfigINI["ConDestino_host"];
 $ConfigConexaoDestino["port"]     = $ConfigINI["ConDestino_port"];
 $ConfigConexaoDestino["dbname"]   = $ConfigINI["ConDestino_dbname"];
 $ConfigConexaoDestino["user"]     = $ConfigINI["ConDestino_user"];
 $ConfigConexaoDestino["password"] = $ConfigINI["ConDestino_password"];


/**
 *  Conexão com base de destino
 */
 $sDataSourceDestino = "host={$ConfigConexaoDestino["host"]} 
 	                      dbname={$ConfigConexaoDestino["dbname"]} 
	                      port={$ConfigConexaoDestino["port"]} 
	                      user={$ConfigConexaoDestino["user"]} 
	                      password={$ConfigConexaoDestino["password"]}";

 $sDataSourceDestinoLog  = " host={$ConfigConexaoDestino["host"]} ";
 $sDataSourceDestinoLog .= " dbname={$ConfigConexaoDestino["dbname"]} ";
 $sDataSourceDestinoLog .= " port={$ConfigConexaoDestino["port"]} ";
                                             
 db_log("- BASE PARA IMPORTACAO  Destino: $sDataSourceDestinoLog", $sArquivoLog,$iParamLog);

 if (! ($connDestino = pg_connect($sDataSourceDestino))) {
 	
   db_log("Erro ao conectar no Destino... ($sDataSourceDestinoLog)", $sArquivoLog,$iParamLog);
   die();
 }

/**
 *  Conexão com base de dados da Prefeitura
 */
 $sDataSourcePrefeitura = "host={$ConfigConexaoPrefeitura["host"]} 
                           dbname={$ConfigConexaoPrefeitura["dbname"]} 
                           port={$ConfigConexaoPrefeitura["port"]} 
                           user={$ConfigConexaoPrefeitura["user"]} 
                           password={$ConfigConexaoPrefeitura["password"]}";

 $sDataSourcePrefeituraLog  = " host={$ConfigConexaoPrefeitura["host"]} ";
 $sDataSourcePrefeituraLog .= " dbname={$ConfigConexaoPrefeitura["dbname"]} ";
 $sDataSourcePrefeituraLog .= " port={$ConfigConexaoPrefeitura["port"]} ";
                           
 db_log("- BASE PARA IMPORTACAO Prefeitura: $sDataSourcePrefeituraLog", $sArquivoLog,$iParamLog);
 
 if (! ($connOrigem = @pg_connect($sDataSourcePrefeitura))) {
 	
   db_log("Erro ao conectar no DBPortal ($sDataSourcePrefeituraLog)...", $sArquivoLog,$iParamLog);
   die();
 }

 db_log("", $sArquivoLog,$iParamLog);
?>
