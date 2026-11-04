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

// Seta Nome do Script
$sNomeScript = basename(__FILE__);

// Desabilita tempo maximo de execucao
set_time_limit(0);

// Hora de Inicio do Script
$sHoraInicio = date( "H:i:s" );

// Bibliotecas
include(modification("libs/db_conn.php"));
include(modification("libs/db_libconsole.php"));
include(modification("libs/db_utils.php"));

// Timestamp para data/Hora
$sTimeStampInicio = date("Ymd_His");

// Seta nome do arquivo de Log, caso já não exista
if(!defined("DB_ARQUIVO_LOG")) {
  $sArquivoLog = "log/".$sNomeScript."_".$sTimeStampInicio.".log";
  define("DB_ARQUIVO_LOG", $sArquivoLog);
}

// Logs...
db_log("", $sArquivoLog);
db_log("*** INICIO Script ".$sNomeScript." ***", $sArquivoLog);
db_log("", $sArquivoLog);

db_log("Arquivo de Log: $sArquivoLog", $sArquivoLog);
db_log("    Script PHP: ".$sNomeScript, $sArquivoLog);
db_log("", $sArquivoLog);


// Conexao com base de dados
$sDataSource = "host={$DB_SERVIDOR} dbname={$DB_BASE} port={$DB_PORTA} user={$DB_USUARIO} password={$DB_SENHA}";


db_log("- BASE PARA PROCESSAMENTO: $sDataSource", $sArquivoLog);

if(!($pConexao = pg_connect($sDataSource))) {
  db_log("Erro ao conectar no DBPortal ($sDataSource)...", $sArquivoLog);
  die();
}

// Fim do Script
db_log("Inicio: $sHoraInicio", $sArquivoLog);
db_log("Final.: " . date( "H:i:s"), $sArquivoLog);

db_log("", $sArquivoLog);
db_log("*** FINAL Script ".$sNomeScript." ***", $sArquivoLog);
db_log("", $sArquivoLog);

db_log("\n\n");

?>
