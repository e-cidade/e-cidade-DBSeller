<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2014  DBSeller Servicos de Informatica
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
require_once(modification('std/db_stdClass.php'));
require_once(modification("dbforms/db_funcoes.php"));

require_once(modification("classes/db_extrato_classe.php"));
include(modification("classes/db_extratosaldo_classe.php"));
include(modification("classes/db_concilia_classe.php"));
include(modification("classes/db_conciliapendextrato_classe.php"));
include(modification("classes/db_conciliaextrato_classe.php"));
include(modification("classes/db_extratolinha_classe.php"));


$clextrato = new cl_extrato;
$clextratosaldo    = new cl_extratosaldo;
$clconcilia        = new cl_concilia;
$clconciliaextrato = new cl_conciliaextrato;
$clconciliapendextrato = new cl_conciliapendextrato;
$clextratolinha = new cl_extratolinha;

parse_str($_SERVER["QUERY_STRING"]);
db_postmemory($_POST);

$clrotulo = new rotulocampo;
$clrotulo->label("k86_contabancaria");
$clrotulo->label("k86_data");
$clrotulo->label("db83_descricao");

$sqlerro  = false;
$erro_msg = "Processamento concluído com sucesso!";
if (isset($processar) or isset($incluir)) {
    if (!isset($incluir)) {
        db_inicio_transacao();
    }

    try {
        if ($conta == "") {
            $sqlerro  = true;
            throw new Exception("Nenhuma conta informada.");
        }

        $oDadosPrefeitura = db_stdClass::getDadosInstit(db_getsession('DB_instit'));

        $sDtSaldoFinal = $sDtSaldoFinal_ano."-".$sDtSaldoFinal_mes."-".$sDtSaldoFinal_dia;

        $sqllPendCorrente  = " select count(*) as quantos ";
        $sqllPendCorrente .= "     from concilia ";
        $sqllPendCorrente .= "    where k68_data = (select k68_data 
                                               from concilia 
                                                where k68_data < '".$data."' 								                                               and k68_contabancaria = $conta 
						order by k68_data desc limit 1)";
        $sqllPendCorrente .= "      and k68_contabancaria = ".$conta." and '$sDtSaldoFinal' > k68_data" ;

        $rslCorrente = $clconcilia->sql_record($sqllPendCorrente);
        $oDadosl = db_utils::fieldsMemory($rslCorrente, 0);
        if ($oDadosl->count > 1) {
            $sqlerro  = true;
            throw new Exception("Data não poderá ser menor que a data da última conciliação!");
        }

        if (isset($processar) && $sDtSaldoFinal != $sDtSaldoFinal_valida_ano."-".$sDtSaldoFinal_valida_mes."-".$sDtSaldoFinal_valida_dia) {
            $sqlerro  = true;
            throw new Exception("Saldo somente pode ser lançado para a data da conciliação atual!");
        }


        $sSqlBusca = "select *
		  from extrato
		  where k85_codbco = ( select db89_db_bancos::integer from contabancaria inner join bancoagencia on db89_sequencial = db83_bancoagencia where db83_sequencial = $conta )
		  and k85_dtarq = '{$sDtSaldoFinal}'";
        $rsBusca = db_query($sSqlBusca);
        if (!$rsBusca) {
            $sqlerro  = true;
            throw new Exception("Erro ao buscar registro de extrato!");
        }

        $sSqlBanco = "select db89_db_bancos::integer from contabancaria inner join bancoagencia on db89_sequencial = db83_bancoagencia where db83_sequencial = $conta";
        $rsBanco = db_query($sSqlBanco);
        if (!$rsBanco) {
            $sqlerro  = true;
            throw new Exception("Erro ao buscar registro do banco!");
        }

        $oDadosBanco = db_utils::fieldsMemory($rsBanco, 0);

        if (pg_num_rows($rsBusca) == 0) {
            $clextrato->k85_codbco       = $oDadosBanco->db89_db_bancos;
            $clextrato->k85_dtproc       = date('Y-m-d', db_getsession('DB_datausu'));
            $clextrato->k85_dtarq        = $sDtSaldoFinal;
            $clextrato->k85_convenio     = "0";
            $clextrato->k85_seqarq       = 1;
            $clextrato->k85_nomearq      = "INCLUSAO MANUAL";
            $clextrato->k85_tipoinclusao = 2;
            $clextrato->k85_conteudo     = "INCLUSAO MANUAL";
            $clextrato->k85_cnpj         = $oDadosPrefeitura->cgc;
            $clextrato->incluir();
            $erro_msg = $clextrato->erro_msg;
            if ($clextrato->erro_status==0) {
                $sqlerro=true;
            }
        }

        $sSqlBuscaExtrato = "select *
		  from extrato
		  where k85_codbco = $oDadosBanco->db89_db_bancos
		  and k85_dtarq = '{$sDtSaldoFinal}'";
        $rsBuscaExtrato = db_query($sSqlBuscaExtrato);
        if (!$rsBuscaExtrato) {
            $sqlerro  = true;
            throw new Exception("Erro ao buscar registro de extrato!");
        }
        $oDadosExtrato = db_utils::fieldsMemory($rsBuscaExtrato, 0);

        $sSqlBuscaExtratoSaldo = "select * from extratosaldo where k97_extrato = $oDadosExtrato->k85_sequencial and k97_contabancaria = $conta and k97_dtsaldofinal = '{$sDtSaldoFinal}'";
        $rsBuscaExtratoSaldo = db_query($sSqlBuscaExtratoSaldo);
        if (!$rsBuscaExtratoSaldo) {
            $sqlerro  = true;
            throw new Exception("Erro ao buscar registro do saldo do extrato!");
        }


        if (isset($processar)) {
            if (pg_num_rows($rsBuscaExtratoSaldo) == 0) {
                $clextratosaldo->k97_contabancaria  = $conta;
                $clextratosaldo->k97_dtsaldofinal   = $sDtSaldoFinal;
                $clextratosaldo->k97_extrato        = $oDadosExtrato->k85_sequencial;
                $clextratosaldo->k97_valorcredito   = 0;
                $clextratosaldo->k97_valordebito    = 0;
                $clextratosaldo->k97_qtdregistros   = 1;
                $clextratosaldo->k97_posicao        = 'F';
                $clextratosaldo->k97_situacao       = $tiposaldo;
                $clextratosaldo->k97_saldobloqueado = 0;
                if (round($iSaldo, 2) == 0) {
                        $clextratosaldo->k97_saldofinal     = '0';
                } else {
                    $clextratosaldo->k97_saldofinal     = round($iSaldo, 2);
                }
                $clextratosaldo->k97_limite         = 0;
                $clextratosaldo->incluir(null);
                if ($clextratosaldo->erro_status == 0) {
                    $sqlerro = true;
                    $erromsg = "extrato saldo - " . $clextratosaldo->erro_msg;
                }
            } else {
                $oDadosExtratoSaldo = db_utils::fieldsMemory($rsBuscaExtratoSaldo, 0);

                $clextratosaldo->k97_sequencial     = $oDadosExtratoSaldo->k97_sequencial;
                $clextratosaldo->k97_situacao       = $tiposaldo;
                $clextratosaldo->k97_valorcredito   = '0';
                $clextratosaldo->k97_valordebito    = '0';
                if (round($iSaldo, 2) == 0) {
                    $clextratosaldo->k97_saldofinal     = '0';
                } else {
                    $clextratosaldo->k97_saldofinal     = round($iSaldo, 2);
                }
                $clextratosaldo->alterar($oDadosExtratoSaldo->k97_sequencial);
                if ($clextratosaldo->erro_status == 0) {
                    $sqlerro = true;
                    $erromsg = "extrato saldo - " . $clextratosaldo->erro_msg;
                }
            }

            db_fim_transacao($sqlerro);
        }
    } catch (Exception $eErro) {
        db_fim_transacao(true);
        $sqlerro  = true;
        $erro_msg = $eErro->getMessage();
    }

    db_fim_transacao($sqlerro);
//  db_msgbox($erro_msg);
}

$db_opcao = 22;
$db_botao = false;

if ($sqlerro == false && (isset($incluir) or isset($alterar) or isset($excluir))) {
    $k86_contabancaria = $conta;
    $dData = $sDtSaldoFinal;

    $dData = $sDtSaldoFinal_ano."-".$sDtSaldoFinal_mes."-".$sDtSaldoFinal_dia;

    $sqlerro = false;
    if (isset($incluir) or isset($alterar)) {
        $k86_contabancaria =  $conta;

        global $k86_historico;

        $clextratolinha->k86_extrato       = $oDadosExtrato->k85_sequencial;
        $clextratolinha->k86_bancohistmov  = $k86_bancohistmov;
        $clextratolinha->k86_contabancaria = $k86_contabancaria;
        $clextratolinha->k86_data          = $dData;
        $clextratolinha->k86_valor         = $k86_valor;
        $clextratolinha->k86_tipo          = $k86_tipo;
        $clextratolinha->k86_historico     = "historico";
        $clextratolinha->k86_documento     = $k86_documento;
    
        $clextratolinha->k86_observacao    = $k86_observacao;
    
        $clextratolinha->k86_lote          = 1;
        $clextratolinha->k86_loteseq       = 1;
    }
}

if (isset($incluir) && $sqlerro == false) {
    db_inicio_transacao();

    if ($sqlerro == false) {
        $clextratolinha->incluir(null);
        $erro_msg = $clextratolinha->erro_msg;
        if ($clextratolinha->erro_status==0) {
            $sqlerro = true;
        }
    }
  
  /*
   * Recalculamos o saldo do extrato
   */
    if ($sqlerro == false && $recalcula == "t") {
       /*
      * Verificamos o extrato saldo da data para efetuarmos a manutenção nos valores.
      *
      */
        $sWhere = " k97_dtsaldofinal = '{$dData}' and k97_contabancaria = $k86_contabancaria";
    
        $rsExtratoSaldo = $clextratosaldo->sql_record($clextratosaldo->sql_query(null, "*", null, $sWhere));
        if ($clextratosaldo->numrows > 0) {
            db_fieldsmemory($rsExtratoSaldo, 0);
      
            $clextratosaldo->recriarSaldoGeral($k86_contabancaria, $dData);
        } else {
            $sWhere = " k97_dtsaldofinal < '{$dData}' and k97_contabancaria = $k86_contabancaria ";
            $rsSaldoAnterior = $clextratosaldo->sql_record($clextratosaldo->sql_query_file(
                null,
                "coalesce(k97_saldofinal,0) as k97_saldofinal",
                "k97_dtsaldofinal desc limit 1",
                $sWhere
            ));
            $oSaldoAnterior = @db_utils::fieldsMemory($rsSaldoAnterior, 0, "k97_saldofinal");
        
            $clextratosaldo->k97_contabancaria  = $clextratolinha->k86_contabancaria;
            $clextratosaldo->k97_dtsaldofinal   = $clextratolinha->k86_data;
            $clextratosaldo->k97_extrato        = $clextratolinha->k86_extrato;
    
            if ($clextratolinha->k86_tipo == "C") {
                $clextratosaldo->k97_valorcredito   = $clextratolinha->k86_valor;
                $clextratosaldo->k97_valordebito    = 0;
            } else {
                $clextratosaldo->k97_valorcredito   = 0;
                $clextratosaldo->k97_valordebito    = $clextratolinha->k86_valor;
            }
    
            $clextratosaldo->k97_qtdregistros   = 1;
            $clextratosaldo->k97_posicao        = 'F';
            $clextratosaldo->k97_situacao       = 'D';
            $clextratosaldo->k97_saldobloqueado = 0;
            $clextratosaldo->k97_saldofinal     = round($clextratolinha->k86_valor + @$oSaldoAnterior->k97_saldofinal, 2);
            $clextratosaldo->k97_limite         = 0;
            $clextratosaldo->incluir(null);
            if ($clextratosaldo->erro_status == 0) {
                $sqlerro = true;
                $erromsg = "extrato saldo - " . $clextratosaldo->erro_msg;
            }
        }
    }
    
  /*
   * Geramos as pendencias
   */
    if ($sqlerro == false) {
      /*
       * Verifica se tem conciliacoes fechada com data superior para inclusao das pendencias
      *
      */
        $rsPendencias = $clconcilia->sql_record($clconcilia->sql_query_file(
            null,
            " k68_sequencial ",
            null,
            "    k68_data >= '{$dData}'
                                                                        and k68_contabancaria = $k86_contabancaria"
        ));
        $intNumrowsConcilia = $clconcilia->numrows;
     
        for ($i = 0; $i < $intNumrowsConcilia; $i ++) {
            db_fieldsmemory($rsPendencias, $i);
    
            $clconciliapendextrato->k88_conciliaorigem = "3";
            $clconciliapendextrato->k88_concilia       = $k68_sequencial;
            $clconciliapendextrato->k88_extratolinha   = $clextratolinha->k86_sequencial;
            $clconciliapendextrato->k88_justificativa  = $k86_observacao;
            $clconciliapendextrato->incluir(null);
            if ($clconciliapendextrato->erro_status == 0) {
                $erro_msg = "conciliapendextrato - ".$clconciliapendextrato->erro_msg;
                $sqlerro  = true;
                break;
            }
        }
    }
       
    db_fim_transacao($sqlerro);

    if ($sDtSaldoFinal != $sDtSaldoFinal_valida_ano."-".$sDtSaldoFinal_valida_mes."-".$sDtSaldoFinal_valida_dia) {
        $sDtSaldoFinal = $sDtSaldoFinal_valida_ano."-".$sDtSaldoFinal_valida_mes."-".$sDtSaldoFinal_valida_dia;
        $dData = $sDtSaldoFinal;
        $data = $sDtSaldoFinal;
    }
} elseif (isset($alterar) && $sqlerro == false) {
   /*
      * Verificamos se o movimento já está conciliado
      */
     $clconciliaextrato->sql_record($clconciliaextrato->sql_query(null, "*", null, "k87_extratolinha = $k86_sequencial"));
    if ($clconciliaextrato->numrows > 0) {
        $erro_msg = "Aviso:\\n\\nLinha do Extrato Manual já conciliada.\\nAlteração não permitida!";
        $sqlerro = true;
    }
     
    if ($sqlerro == false) {
        db_inicio_transacao();
    
        if ($sqlerro==false) {
            $clextratolinha->alterar($k86_sequencial);
            $erro_msg = $clextratolinha->erro_msg;
            if ($clextratolinha->erro_status==0) {
                $sqlerro=true;
            }
        }
    
        if ($sqlerro==false) {
            $clconciliapendextrato->excluir(null, "k88_extratolinha = {$k86_sequencial}");
            if ($clconciliapendextrato->erro_status == 0) {
                $erro_msg = "conciliapendextrato - ".$clconciliapendextrato->erro_msg;
                $sqlerro  = true;
            }
        }
    
    
        if ($sqlerro == false && $recalcula == "t") {
          /*
           * Verificamos o extrato saldo da data para efetuarmos a manutenção nos valores.
           *
           */
            $sWhere = " k97_dtsaldofinal = '{$dData}' and k97_contabancaria = {$k86_contabancaria}";
            $rsExtratoSaldo = $clextratosaldo->sql_record($clextratosaldo->sql_query(null, "*", null, $sWhere));
            if ($clextratosaldo->numrows > 0) {
                db_fieldsmemory($rsExtratoSaldo, 0);
         
                $clextratosaldo->recriarSaldoGeral($k86_contabancaria, $dData);
            } else {
                $sWhere = " k97_dtsaldofinal < '{$dData}' and k97_contabancaria = $k86_contabancaria ";
                $rsSaldoAnterior = $clextratosaldo->sql_record($clextratosaldo->sql_query_file(
                    null,
                    "coalesce(k97_saldofinal,0) as k97_saldofinal",
                    "k97_dtsaldofinal desc limit 1",
                    $sWhere
                ));
                $oSaldoAnterior = @db_utils::fieldsMemory($rsSaldoAnterior, 0, "k97_saldofinal");
        
                $clextratosaldo->k97_contabancaria  = $clextratolinha->k86_contabancaria;
                $clextratosaldo->k97_dtsaldofinal   = $clextratolinha->k86_data;
                $clextratosaldo->k97_extrato        = $clextratolinha->k86_extrato;
       
                if ($clextratolinha->k86_tipo == "C") {
                    $clextratosaldo->k97_valorcredito   = $clextratolinha->k86_valor;
                    $clextratosaldo->k97_valordebito    = 0;
                } else {
                    $clextratosaldo->k97_valorcredito   = 0;
                    $clextratosaldo->k97_valordebito    = $clextratolinha->k86_valor;
                }
       
                $clextratosaldo->k97_qtdregistros   = 1;
                $clextratosaldo->k97_posicao        = 'F';
                $clextratosaldo->k97_situacao       = 'D';
                $clextratosaldo->k97_saldobloqueado = 0;
                $clextratosaldo->k97_saldofinal     = round($clextratolinha->k86_valor + @$oSaldoAnterior->k97_saldofinal, 2);
                $clextratosaldo->k97_limite         = 0;
                $clextratosaldo->incluir(null);
                if ($clextratosaldo->erro_status == 0) {
                    $sqlerro = true;
                    $erromsg = "extrato saldo - " . $clextratosaldo->erro_msg;
                }
            }
        }
      
    /*
     * Geramos as pendencias
    */
        if ($sqlerro == false) {
            /*
             * Verifica se tem conciliacoes fechada com data superior para inclusao das pendencias
            *
            */
            $rsPendencias = $clconcilia->sql_record($clconcilia->sql_query_file(
                null,
                " k68_sequencial ",
                null,
                "    k68_data >= '{$dData}'
    			                                                                and k68_contabancaria = $k86_contabancaria"
            ));
            $intNumrowsConcilia = $clconcilia->numrows;
         
            for ($i = 0; $i < $intNumrowsConcilia; $i ++) {
                db_fieldsmemory($rsPendencias, $i);
    
                $clconciliapendextrato->k88_conciliaorigem = "3";
                $clconciliapendextrato->k88_concilia       = $k68_sequencial;
                $clconciliapendextrato->k88_extratolinha   = $clextratolinha->k86_sequencial;
                    $clconciliapendextrato->k88_justificativa  = $k86_observacao;
                $clconciliapendextrato->incluir(null);
                if ($clconciliapendextrato->erro_status == 0) {
                    $erro_msg = "conciliapendextrato - ".$clconciliapendextrato->erro_msg;
                    $sqlerro  = true;
                    break;
                }
            }
        }
    }
      
    db_fim_transacao($sqlerro);
} elseif (isset($excluir) && $sqlerro == false) {
    if ($sqlerro==false) {
        db_inicio_transacao();
      /*
       * Verificamos se o movimento possui pendencia para esta data ou posterior
       * Caso o movimento não seja de pendencia, atualiza a extratosaldo
       */
        $rsPendenciassql = $clconciliapendextrato->sql_query(
            null,
            "*",
            null,
            "    conciliapendextrato.k88_extratolinha = $k86_sequencial 
											  and concilia.k68_data >= '".$dData."'"
        );

        $rsPendencias = $clconciliapendextrato->sql_record($rsPendenciassql);
        $iNumRows = $clconciliapendextrato->numrows;
        if ($iNumRows > 0) {
            for ($ax = 0; $ax < $iNumRows; $ax++) {
                db_fieldsmemory($rsPendencias, $ax);
         
                $clconciliapendextrato->excluir($k88_sequencial);
                if ($clconciliapendextrato->erro_status == 0) {
                     $erro_msg = "Erro ao excluir pendencia posterior (conciliapendextrato) Sequencial: - $k88_sequencial ".$clconciliapendextrato->erro_msg;
                     $sqlerro  = true;
                     break;
                }
            }
        }
        $clextratolinha->excluir($k86_sequencial);
        $erro_msg = $clextratolinha->erro_msg;
        if ($clextratolinha->erro_status==0) {
            $sqlerro=true;
        }
    
    
     /*
      * Verificamos o extrato saldo da data para efetuarmos a manutenção nos valores caso o movimento não esteja pendente.
      *
      if ($iNumRows > 0 && $k88_conciliaorigem != 3 || $iNumRows == 0) {

      $sWhere = " k97_dtsaldofinal = '{$dData}' and k97_contabancaria = {$k86_contabancaria}";
      $rsExtratoSaldo = $clextratosaldo->sql_record($clextratosaldo->sql_query(null, "*", null, $sWhere));
      if ($clextratosaldo->numrows > 0) {
        db_fieldsmemory($rsExtratoSaldo,0);
        $clextratosaldo->recriarSaldoGeral($k86_contabancaria,$dData);
      }

      }
      */

        db_fim_transacao($sqlerro);
    }
} else {
    if ($sqlerro == false) {
        if (isset($opcao)) {
            $result = $clextratolinha->sql_record($clextratolinha->sql_query(null, "*", "", " k86_sequencial = $k86_sequencial "));
            if ($result!=false && $clextratolinha->numrows>0) {
                db_fieldsmemory($result, 0);
            }
        }

        if (!isset($data) or $data == "") {
            $sSqlBuscaSaldo = "	select k97_saldofinal, k97_situacao
				from extratosaldo 
				where k97_extrato = ( select k85_sequencial from extrato where k85_codbco = ( select db89_db_bancos::integer from contabancaria inner join bancoagencia on db89_sequencial = db83_bancoagencia where db83_sequencial = $conta ) order by k85_dtarq desc limit 1 )
				and k97_contabancaria = $conta 
				and k97_dtsaldofinal in ( select k85_dtarq from extrato where k85_codbco = ( select db89_db_bancos::integer from contabancaria inner join bancoagencia on db89_sequencial = db83_bancoagencia where db83_sequencial = $conta ) order by k85_dtarq desc limit 1 )";
            $rsBuscaSaldo = db_query($sSqlBuscaSaldo);
            if (!$rsBuscaSaldo) {
                throw new Exception("Erro ao buscar saldo do extrato!");
            }
            $oSaldo = db_utils::fieldsMemory($rsBuscaSaldo, 0);
            $iSaldo     = $oSaldo->k97_saldofinal;
            $tiposaldo  = $oSaldo->k97_situacao;
        } else {
            $sSqlBuscaSaldo = "	select k97_saldofinal, k97_situacao
				from extratosaldo 
				where k97_extrato = ( select k85_sequencial from extrato where k85_codbco = ( select db89_db_bancos::integer from contabancaria inner join bancoagencia on db89_sequencial = db83_bancoagencia where db83_sequencial = $conta ) and k85_dtarq = '{$data}' )
				and k97_contabancaria = $conta 
				and k97_dtsaldofinal = '{$data}'";
            $rsBuscaSaldo = db_query($sSqlBuscaSaldo);
            if (!$rsBuscaSaldo) {
                throw new Exception("Erro ao buscar saldo do extrato!");
            }
            $oSaldo = db_utils::fieldsMemory($rsBuscaSaldo, 0);
            $iSaldo     = $oSaldo->k97_saldofinal;
            $tiposaldo  = $oSaldo->k97_situacao;
        }

        $sDtSaldoFinal = $data;
    }
}
?>
<html>

<head>
  <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <link href="estilos.css" rel="stylesheet" type="text/css">
</head>

<body >

<form name="form1" method="post" action="">
  <div class="container">
    <fieldset>
      <legend class="bold">Processar Saldo do Extrato</legend>

      <table border="0">
        <tr>
          <td nowrap title="<?=@$Tk86_contabancaria?>">
            <?php echo $Lk86_contabancaria; // db_ancora(@$Lk86_contabancaria,"js_pesquisak86_contabancaria(true);",1); ?>
          </td>
          <td nowrap>
            <?php db_input('conta', 10, $Ik86_contabancaria, true, 'text', 3, " onchange='js_pesquisak86_contabancaria(false);'") ?>
            <?php db_input('db83_descricao', 50, $Idb83_descricao, true, 'hidden', 3, '') ?>
          </td>
        </tr>
        <tr>
          <td nowrap title="<?=@$Tk86_data?>">
            <?=@$Lk86_data?>
          </td>
          <td>
            <?php
            $k86_data_dia = substr($data, 8, 2);
            $k86_data_mes = substr($data, 5, 2);
            $k86_data_ano=  substr($data, 0, 4);
            db_inputdata('sDtSaldoFinal', @$k86_data_dia, @$k86_data_mes, @$k86_data_ano, true, 'text', 1, "");
            db_inputdata('sDtSaldoFinal_valida', @$k86_data_dia, @$k86_data_mes, @$k86_data_ano, true, 'hidden', 1, "");
            ?>
          </td>
        </tr>
        <tr>
          <td nowrap>
            <strong>Saldo Bancário na Data: </strong>
          </td>
          <td>
            <?php
            db_input('iSaldo', 10, 4, true, 'text', 1);

            $x = array("C"=>"C","D"=>"D");
            db_select('tiposaldo', $x, true, 2, "");

            ?>
          </td>



        </tr>
      </table>
      <p><input name="processar"  type="submit" value="Processar" />
      <input type='button' value='Fechar' onclick='parent.db_iframe_digitasaldoextratomanual.hide()'>
      </p>
    </fieldset>


     <fieldset>
     <?php
        include(modification("cai2_digitasaldoextratomanuallinha.php"));
        ?>
     </fieldset>

  </div>
</form>

</body>

</html>

<?php
db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit"));
?>

<script>

  function js_pesquisak86_contabancaria(mostra){
    if(mostra==true){
      js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_contabancaria','func_contabancaria.php?funcao_js=parent.js_mostracontabancaria1|db83_sequencial|db83_descricao','Pesquisa',true,'20');
    }else{
      if(document.form1.Conta.value != ''){
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_contabancaria','func_contabancaria.php?lImplantacao=1&tp=1&pesquisa_chave='+document.form1.Conta.value+'&funcao_js=parent.js_mostracontabancaria','Pesquisa',false);
      }else{
        document.form1.db83_descricao.value = '';
      }
    }
  }
  function js_mostracontabancaria(chave,erro){

    document.form1.db83_descricao.value = chave;
    if(erro==true){
      document.form1.Conta.focus();
      document.form1.Conta.value = '';
    }
  }
  function js_mostracontabancaria1(chave1,chave2){
    document.form1.Conta.value = chave1;
    document.form1.db83_descricao.value = chave2;
    db_iframe_contabancaria.hide();
  }

<?php

if (isset($alterar) || isset($excluir) || isset($incluir) || isset($processar)) {
/* variaveis de configuração do formulario  */
    $borda = 0;
    $iInstit = db_getsession("DB_instit");

    $sqlDadosConta  = " select distinct ";
    $sqlDadosConta .= "        db83_sequencial as reduzido,";
    $sqlDadosConta .= "        db83_descricao  as descricao,";
    $sqlDadosConta .= "        db90_codban as banco,";
    $sqlDadosConta .= "        db83_conta||'-'||db83_dvconta as contabancaria,";
    $sqlDadosConta .= "        db89_codagencia||'-'||db89_digito as agencia,";
    $sqlDadosConta .= "        db83_contaunica as conta_unica";
    $sqlDadosConta .= "   from contabancaria ";
    $sqlDadosConta .= "        inner join bancoagencia on bancoagencia.db89_sequencial = contabancaria.db83_bancoagencia ";
    $sqlDadosConta .= "        inner join db_bancos    on db_bancos.db90_codban        = bancoagencia.db89_db_bancos ";
    $sqlDadosConta .= "  where contabancaria.db83_sequencial = {$conta} ";
    $rsDadosConta   = db_query($sqlDadosConta);
    if ($rsDadosConta && pg_num_rows($rsDadosConta) > 0) {
        db_fieldsmemory($rsDadosConta, 0);
    }

/* variaveis dos saldos  */
    $saldo_tesouraria = 0;
    $saldo_extrato = 0;

    $data = $sDtSaldoFinal;

    $sSqlReduz  = " select c61_reduz, c61_instit , (select case when k97_situacao = 'D' then k97_saldofinal * -1 else k97_saldofinal end as k97_saldofinal from extratosaldo where k97_contabancaria = $conta and k97_dtsaldofinal = '$data') as saldo_extrato";
    $sSqlReduz .= "   from contabancaria ";
    $sSqlReduz .= "        inner join conplanocontabancaria on conplanocontabancaria.c56_contabancaria = contabancaria.db83_sequencial and conplanocontabancaria.c56_anousu = " . db_getsession('DB_anousu');
    $sSqlReduz .= "        inner join conplanoreduz         on conplanoreduz.c61_codcon = conplanocontabancaria.c56_codcon ";
    $sSqlReduz .= "                                        and conplanoreduz.c61_anousu = conplanocontabancaria.c56_anousu ";
    $sSqlReduz .= "                                        and conplanoreduz.c61_anousu = ".db_getsession('DB_anousu');
//se for conta unica a instituicao nao deve ser considerada
    if ($conta_unica == "f") {
        $sSqlReduz .= " and conplanoreduz.c61_instit = ".db_getsession('DB_instit');
    }
    $sSqlReduz .= "  where contabancaria.db83_sequencial = {$conta} ";
    $rsReduz    = db_query($sSqlReduz);

    if ($rsReduz && pg_num_rows($rsReduz) > 0) {
        for ($i = 0; $i <  pg_num_rows($rsReduz); $i++) {
            db_fieldsmemory($rsReduz, $i);

            $sqlSaldoContaCaixa = "select substr(fc_saltessaldo(".$c61_reduz.",'".$data."','".$data."',null, {$c61_instit}),41,13)::float as saldocontacaixa";
            $rsSaldoContaCaixa  = db_query($sqlSaldoContaCaixa);

            if (pg_num_rows($rsSaldoContaCaixa) > 0) {
                db_fieldsmemory($rsSaldoContaCaixa, 0);
                $saldo_tesouraria += $saldocontacaixa;
            }
        }
    }
    $sql = " select round(sum(k86_valor),2) as saldo_pendencias from (
           select case when k86_tipo = 'D' then k86_valor else k86_valor * -1 end as k86_valor
        from conciliapendextrato 
             inner join concilia on k88_concilia = k68_sequencial
             inner join extratolinha on k86_sequencial = k88_extratolinha 
             inner join extrato on k85_sequencial = k86_extrato 
	where k86_contabancaria = $conta and k68_data = '$data' and k86_bancohistmov = 1
       ) as x
       ";

    $rsSaldoContaCaixa  = db_query($sql);

    if (pg_num_rows($rsSaldoContaCaixa) > 0) {
        db_fieldsmemory($rsSaldoContaCaixa, 0);
    }
 
    $saldo_conciliacao = $saldo_extrato + ( $saldo_pendencias ) ;

    $sql = "select round(sum(k86_valor),2) as saldo_pendencias from (
        select case when k86_tipo = 'C' then k86_valor * -1 else k86_valor end as k86_valor
	from conciliapendextrato 
             inner join concilia on k88_concilia = k68_sequencial
             inner join extratolinha on k86_sequencial = k88_extratolinha 
             inner join extrato on k85_sequencial = k86_extrato 
	where k86_contabancaria = $conta and k68_data = '$data' and k86_bancohistmov = 2

	union all 

        select round(sum(case when tipo = 'cheque' or tipo = 'credito' then valor else valor * -1 end),2) as valor
          from (
         select 
max( case when richeque is not null and richeque <> 0 and rivalorcredito <> 0 then 'cheque' 
          when rnvalordebito is not null and rnvalordebito <> 0 or richeque is not null and richeque <> 0 and rnvalordebito <> 0 then 'debito' 
	  when rivalorcredito is not null and rivalorcredito <> 0 then 'credito' 
     end) as tipo, 
ricaixa, 
riautent, 
ridata, 
(select e60_codemp||'/'||e60_anousu from empempenho where e60_numemp = riempenho ) as riempenho,
 riordem,
 riplanilha,
 rislip,
 richeque as cheque,
 max(case when rnvalordebito is not null and rnvalordebito <> 0 then 'D' else 'C' end) as tipomov, 
sum(case when rnvalordebito is not null and rnvalordebito <> 0 then rnvalordebito else rivalorcredito end) as valor, 
k89_justificativa 
from conciliapendcorrente 
     inner join concilia on k89_concilia = k68_sequencial
     inner join fc_extratocaixa(".db_getsession('DB_instit').",$conta,null,null,false ) on ricaixa = k89_id 
                      and riautent = k89_autent and ridata = k89_data 
     where k68_contabancaria = $conta  and k68_data = '$data'
       and not exists (select 1 from corgrupocorrente where k105_autent = k89_autent and k105_id = k89_id and k105_data = k89_data 
and ( ( k105_corgrupotipo in (2,3,5,6) and extract(year from k105_data) <= 2012 ) 

						or ( k105_corgrupotipo in (2,3) ) )
) 
group by ricaixa, 
riautent, 
ridata, 
riempenho, 
riordem, 
riplanilha, 
rislip, 
richeque,
k89_justificativa
) as x
	) as x";

    $rsSaldoContaCaixa  = db_query($sql);

    if (pg_num_rows($rsSaldoContaCaixa) > 0) {
        db_fieldsmemory($rsSaldoContaCaixa, 0);
    }
    $saldo_conciliacao -=  ( $saldo_pendencias ) ;

    echo "
  var iAtualiza = parent.document.getElementById('iframeExtrato').src;
  parent.document.getElementById('iframeExtrato').src = iAtualiza+'&';
  ";
    if (isset($processar) && !$sqlerro) {
        echo " parent.document.getElementById('id_saldo_extrato').value = '".db_formatar($clextratosaldo->k97_saldofinal, 'f')."'; \n";
    }

    if (!$sqlerro) {
        echo " parent.document.getElementById('id_saldo_tesouraria').value = '".db_formatar($saldo_tesouraria, 'f')."'; \n";
        echo " parent.document.getElementById('id_saldo_conciliacao').value = '".db_formatar($saldo_conciliacao, 'f')."'; \n";
        echo " parent.document.getElementById('id_saldo_diferenca').value = '".db_formatar(($saldo_tesouraria - $saldo_conciliacao), 'f')."'; \n";
        echo " parent.document.getElementById('saldo_diferenca').value = '".round($saldo_tesouraria - $saldo_conciliacao, 2)."'; \n";
    }

    echo "</script>";

    if ($sqlerro==true) {
        db_msgbox($erro_msg);
        if ($clextratolinha->erro_campo!="") {
            echo "<script> document.form1.".$clextratolinha->erro_campo.".style.backgroundColor='#99A9AE'; </script>";
            echo "<script> document.form1.".$clextratolinha->erro_campo.".focus(); </script>";
        }
    }
}

?>


