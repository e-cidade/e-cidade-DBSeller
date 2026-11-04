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

//
// Configuracoes para programas de Conversão
//

$ConfigINI = parse_ini_file("db_config.ini");

db_log("", $sArqLog);

// DBPORTAL Prefeitura
$ConfigConexaoPrefeitura ["host"]     = $ConfigINI ["ConPrefeitura_host"];
$ConfigConexaoPrefeitura ["port"]     = $ConfigINI ["ConPrefeitura_port"];
$ConfigConexaoPrefeitura ["dbname"]   = $ConfigINI ["ConPrefeitura_dbname"];
$ConfigConexaoPrefeitura ["user"]     = $ConfigINI ["ConPrefeitura_user"];
$ConfigConexaoPrefeitura ["password"] = $ConfigINI ["ConPrefeitura_password"];

// DBPORTAL Giss
$ConfigConexaoGiss ["host"]     = $ConfigINI ["ConGiss_host"];
$ConfigConexaoGiss ["port"]     = $ConfigINI ["ConGiss_port"];
$ConfigConexaoGiss ["dbname"]   = $ConfigINI ["ConGiss_dbname"];
$ConfigConexaoGiss ["user"]     = $ConfigINI ["ConGiss_user"];
$ConfigConexaoGiss ["password"] = $ConfigINI ["ConGiss_password"];

//
// Conexao com a base de dados do gissonline
//
$sDataSourceGiss = "host={$ConfigConexaoGiss["host"]} 
                    dbname={$ConfigConexaoGiss["dbname"]} 
                    port={$ConfigConexaoGiss["port"]} 
                    user={$ConfigConexaoGiss["user"]} 
                    password={$ConfigConexaoGiss["password"]}";

db_log("- BASE PARA IMPORTACAO       Giss: $sDataSourceGiss", $sArquivoLog);

if (! ($conn2 = pg_connect($sDataSourceGiss))) {
  db_log("Erro ao conectar no Giss... ($sDataSourceGiss)", $sArqLog);
  die();
}

//
// Conexao com a base de dados da prefeitura
//
$sDataSourcePrefeitura = "host={$ConfigConexaoPrefeitura["host"]} 
                          dbname={$ConfigConexaoPrefeitura["dbname"]} 
                          port={$ConfigConexaoPrefeitura["port"]} 
                          user={$ConfigConexaoPrefeitura["user"]} 
                          password={$ConfigConexaoPrefeitura["password"]}";

db_log("- BASE PARA IMPORTACAO Prefeitura: $sDataSourcePrefeitura", $sArquivoLog);

if (! ($conn = pg_connect($sDataSourcePrefeitura))) {
  db_log("Erro ao conectar no DBPortal ($sDataSourcePrefeitura)...", $sArquivoLog);
  die();
}

db_log("", $sArqLog);

?>