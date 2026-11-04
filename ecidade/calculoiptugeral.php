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

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conn.php"));
include(modification("classes/db_iptubase_classe.php"));

function db_msg($_msg="") {
  echo $_msg . "\n";
  return;
}

function db_termo($pos, $total, $msg="") {
  $perc = round(($pos/$total)*100, 2);
  $msg = !empty($msg)?" [$msg]":"";
  echo "  . Processando ($pos/$total) $perc % concluido...$msg\r";
  return;
}  

function db_tempodecorrido($segundo_inicial, $segundo_final) {
  
  $tempo = number_format(($segundo_final - $segundo_inicial),0);
  
  /*inicializando as variáveis*/
  $horas       = 0;
  $sobra_horas = 0;
  $minutos     = 0;
  $segundos    = 0;
  
  /*Calculando horas, minutos e segundos*/
  if ($tempo >= 3600) {
    $horas = number_format(($tempo / 3600),0);
    $sobra_horas = $tempo - ($horas*3600);
    if ($sobra_horas > 60) {
      $minutos = number_format(($sobra_horas/60),0);
      $segundos = $sobra_horas - ($minutos*60);
    }
  } else if (($tempo < 3600) && ($tempo >=60)) {
    $minutos = number_format(($tempo/60),0);
    $segundos = $tempo - ($minutos*60);
  } else {
    $segundos = $tempo;
  }
  
  /* Configurando exibição conforme resultado*/
  if ($horas > 0) {
    $duracao = "{$horas} hora(s) {$minutos} minuto(s) {$segundos} segundo(s).";
  } else if ($minutos > 0) {
    $duracao = "{$minutos} minuto(s) {$segundos} segundo(s).";
  } else {
    $duracao = "{$segundos} segundo(s).";
  }
  
  return $duracao;
}

// Bage
$DB_BASE     = "bage_baixabanco";
$DB_SERVIDOR = "127.0.0.1";
$DB_PORTA    = "5432";

$DB_BASE     = "iptu2007_carazinho_1212";
$DB_SERVIDOR = "192.168.0.43";
$DB_PORTA    = "5432";

$DB_BASE     = "arroio_1212";
$DB_SERVIDOR = "192.168.0.88";
$DB_PORTA    = "5432";

$DB_BASE     = "auto_cha_1612";
$DB_SERVIDOR = "192.168.0.33";
$DB_PORTA    = "5432";

$DB_BASE     = "itaqui_iptu2007_1612";
$DB_SERVIDOR = "192.168.0.36";
$DB_PORTA    = "5432";

$DB_BASE     = "template1";
$DB_SERVIDOR = "192.168.0.20";
$DB_PORTA    = "5433";

$DB_USUARIO  = "postgres";
$DB_SENHA    = "";

db_msg("");
db_msg("CALCULO GERAL IPTU   Base: ".$DB_BASE." - 3 segundos... se quiser cancelar CTRL+C");
db_msg("");
//sleep(3);

if(!($conn = @pg_connect("host=$DB_SERVIDOR dbname=$DB_BASE port=$DB_PORTA user=$DB_USUARIO password=$DB_SENHA"))) {
  db_msg("ERRO: Estabelecendo conexao com banco de dados (host=$DB_SERVIDOR base=$DB_NAME porta=$DB_PORTA user=$DB_USUARIO senha=$DB_SENHA)");
  exit;
}

// Parametros do Calculo
$anousu     = 2007;
$financeiro = 1;
//--------------------


$erro = false;
set_time_limit(0);
$cliptubase = new cl_iptubase;
$result = db_query("begin;");

$ini = mktime();

$iniciocalculo = mktime(date("H"), date("i"), date("s"), 0, 0, 0);
db_msg("- Inicio do Calculo: ".date("H:i:s", $iniciocalculo));
db_msg("");
db_msg("inicio: " . date("H:i:s", $ini));

$sql = "select j01_matric from iptubase";
$result = $cliptubase->sql_record($sql);

if ($cliptubase->numrows==0) {
  db_msg("ERRO: Sem matrículas a calcular!");
} else {
  $total_reg = $cliptubase->numrows;
  $sqlnextval = "select nextval('iptucalclog_j27_codigo_seq') as j27_codigo";
  $resultnextval = db_query($sqlnextval);
  if ($resultnextval == false) {
    db_msg("ERRO: Nao consegui buscar proxima sequencia 'iptucalclog_j27_codigo_seq'");
  } else {
    db_fieldsmemory($resultnextval,0);
    $insert = "insert into iptucalclog values ($j27_codigo,$anousu,'".date('Y-m-d')."','".db_hora()."', 1,false," . $cliptubase->numrows . ")";
    $resultinsert = db_query($insert) or die($insert);
    if ($resultinsert == false) {
      db_msg("ERRO: Gerando registro na tabela iptucalclog!");
    } else {

      db_query("commit;");
     
      // For para calcular matriculas
      for ($ii=0; $ii<$total_reg; $ii++) {
        db_fieldsmemory($result,$ii);
        
        db_query("begin;");

				$resultcfiptu=db_query("select distinct j18_anousu, j18_permvenc from cfiptu order by j18_anousu desc");
				$j18_permvenc = 1;
				if(pg_numrows($resultcfiptu) > 0){
					db_fieldsmemory($resultcfiptu,0);
				}
				if ($j18_permvenc == 0) {
					$j18_permvenc = 1;
				}
				db_query("select fc_putsession('DB_Gerafinanc','0')");
				if ($j18_permvenc == 1) {
					$sql = "select fc_calculoiptu($j01_matric,$anousu,true,false,false,false,false,0,0,0)";
				} elseif ($j18_permvenc == 2) {
					$sql = "select fc_calculoiptu($j01_matric,$anousu,true,false,false,false,false,0,0)";
				}
				//die($sql);
        $resultcalc = $cliptubase->sql_record($sql);
        if ($resultcalc != false) {
          db_fieldsmemory($resultcalc,0);
          db_termo($ii+1, $total_reg, "Matricula: $j01_matric");
          $insert = "insert into iptucalclogmat values ($j27_codigo,$j01_matric,".substr($fc_calculoiptu,0,2).",'".trim(substr($fc_calculoiptu,2))."')";
          $resultinsert = db_query($insert) or die($insert);
          if ($resultinsert == false) {
            $erro=true;
          }
        } else {
          $erro=true;
        }
        //if ($erro == true) {
        //  db_msg("\nERRO: Ocorreu um erro durante o processamento, Calculo abortado ! Matricula: $j01_matric");
        //  exit;
        //}
        db_query("commit;");
      }

      $fimcalculo = mktime(date("H"), date("i"), date("s"), 0, 0, 0);
      $fim = mktime();

      $tempo = date("H:i:s", $fim - $ini);

      db_msg("\n");
      db_msg("-    Fim do Calculo: ".date("H:i:s", $fimcalculo));
      db_msg();
      db_msg("tempo: $tempo");

      if ($erro == true) {
        db_msg("ERRO: Ocorreram erros durante o processamento! Emite relatorio de erros!");
			}

			db_msg("\n>>> Cálculo finalizado! <<<");

      db_msg("- Inicio do Calculo: ".date("H:i:s", $iniciocalculo));
      db_msg("-    Fim do Calculo: ".date("H:i:s", $fimcalculo));
      $decorrido = db_tempodecorrido($iniciocalculo, $fimcalculo);
      db_msg("-   Tempo decorrido: $decorrido");
      db_msg(""); 
    }
  }
}
?>