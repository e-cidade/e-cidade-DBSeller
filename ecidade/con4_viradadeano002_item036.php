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
 * Programa responsável pela virada anual das etapas do censo, e os vínculos das etapas do e-cidade
 */

$daoBnccensinofundamental = new cl_bnccensinofundamental();
$daoBnccEducacaoInfantil = new cl_bncceducacaoinfantil();
$daoBnccReferencial = new cl_bnccreferencial();

if ($sqlerro == false) {
    db_atutermometro(0, 2, 'termometroitem', 1, $sMensagemTermometroItem);
    try {
        /**
         * Virar bnccensinofundamental
         */
        $where = " ed148_ano = {$iAnoOrigem} ";

        $sqlBnccensinofundamental = $daoBnccensinofundamental->sql_query_file(null, "*", null, $where);
        $rsBnccensinofundamental = db_query($sqlBnccensinofundamental);

        if (!$rsBnccensinofundamental || pg_num_rows($rsBnccensinofundamental) == 0) {
            throw new Exception("Não existe Habilidades da Bncc para o ano de: {$iAnoOrigem} ", 2);
        }

        $iLinhasBnccensinofundamental = pg_num_rows($rsBnccensinofundamental);
        for ($i = 0; $i < $iLinhasBnccensinofundamental; $i++) {
            $oDadosBnccensinofundamental = db_utils::fieldsMemory($rsBnccensinofundamental, $i);
            /*
             * Valida se para o ano de destino, já não existe registros
             */
            $whereValidar         = " ed148_ano = {$iAnoDestino} ";
            $whereValidar        .= " and ed148_codigo = '{$oDadosBnccensinofundamental->ed148_codigo}' ";
            $sqlValidarBnccensinofundamental = $daoBnccensinofundamental->sql_query_file(null, "*", null, $whereValidar);

            $rsValidarBnccensinofundamental = db_query($sqlValidarBnccensinofundamental);
            if (!$rsValidarBnccensinofundamental) {
                throw new Exception("Erro ao validar bncc ensino fundamental.\n" . pg_last_error(), 1);
            }
            if (pg_num_rows($rsValidarBnccensinofundamental) > 0) {
                continue;
            }
            $daoBnccensinofundamental->ed148_sequencial = null;
            $daoBnccensinofundamental->ed148_disciplina = $oDadosBnccensinofundamental->ed148_disciplina;
            $daoBnccensinofundamental->ed148_etapa = $oDadosBnccensinofundamental->ed148_etapa;
            $daoBnccensinofundamental->ed148_codigo = $oDadosBnccensinofundamental->ed148_codigo;
            $daoBnccensinofundamental->ed148_unidade_tematica = $oDadosBnccensinofundamental->ed148_unidade_tematica;
            $daoBnccensinofundamental->ed148_objeto_conhecimento = pg_escape_string(
                $oDadosBnccensinofundamental->ed148_objeto_conhecimento
            );
            $daoBnccensinofundamental->ed148_habilidade = pg_escape_string(
                $oDadosBnccensinofundamental->ed148_habilidade
            );
            $daoBnccensinofundamental->ed148_ano = $iAnoDestino;

            $daoBnccensinofundamental->incluir(null);

            if ($daoBnccensinofundamental->erro_status == 0) {
                throw new Exception($daoBnccensinofundamental->erro_msg, 1);
            }
        }

        /** Virar bncceducacaoinfantil */
        $where = "ed147_ano = {$iAnoOrigem}";

        $sqlEducacaoInfantil = $daoBnccEducacaoInfantil->sql_query_file(null, '*', null, $where);
        $rsBnccEducacaoInfantil = db_query($sqlEducacaoInfantil);

        if (!$rsBnccEducacaoInfantil || pg_num_rows($rsBnccEducacaoInfantil) == 0) {
            throw new Exception("Não existem Habilidades da BNCC Educação Infantil para o ano de: {$iAnoOrigem} ", 2);
        }

        $iLinhasBnccEducacaoInfantil = pg_num_rows($rsBnccEducacaoInfantil);
        for ($i = 0; $i < $iLinhasBnccEducacaoInfantil; $i++) {
            $oDadosBnccEducacaoInfantil = db_utils::fieldsMemory($rsBnccEducacaoInfantil, $i);
            /*
             * Valida se para o ano de destino, já não existe registros
             */
            $whereValidar = " ed147_ano = {$iAnoDestino} ";
            $whereValidar .= " and ed147_codigo = '{$oDadosBnccEducacaoInfantil->ed147_codigo}' ";
            $sqlValidarBncceducacaoinfantil = $daoBnccEducacaoInfantil->sql_query_file(null, "*", null, $whereValidar);

            $rsValidarBncceducacaoinfantil = db_query($sqlValidarBncceducacaoinfantil);
            if (!$rsValidarBncceducacaoinfantil) {
                throw new Exception("Erro ao validar BNCC educação infantil.\n" . pg_last_error(), 1);
            }
            if (pg_num_rows($rsValidarBncceducacaoinfantil) > 0) {
                continue;
            }

            $daoBnccEducacaoInfantil->ed147_sequencial = null;
            $daoBnccEducacaoInfantil->ed147_disciplina = $oDadosBnccEducacaoInfantil->ed147_disciplina;
            $daoBnccEducacaoInfantil->ed147_faixa_etaria = $oDadosBnccEducacaoInfantil->ed147_faixa_etaria;
            $daoBnccEducacaoInfantil->ed147_codigo = $oDadosBnccEducacaoInfantil->ed147_codigo;
            $daoBnccEducacaoInfantil->ed147_habilidade = pg_escape_string(
                $oDadosBnccEducacaoInfantil->ed147_habilidade
            );
            $daoBnccEducacaoInfantil->ed147_ano = $iAnoDestino;

            $daoBnccEducacaoInfantil->incluir(null);
            if ($daoBnccEducacaoInfantil->erro_status == 0) {
                throw new Exception($daoBnccEducacaoInfantil->erro_msg, 1);
            }
        }

        /**
         *  Virar bnccreferencial
         */
        $where = "ed168_ano = {$iAnoOrigem}";
        $sqlBnccReferencial = $daoBnccReferencial->sql_query_file(null, '*', null, $where);
        $rsBnccReferencial = db_query($sqlBnccReferencial);

        if (!$rsBnccReferencial) {
            throw new Exception("Não existem Habilidades da BNCC para o ano de: {$iAnoOrigem}", 2);
        }

        $linhasBnccReferencial = pg_num_rows($rsBnccReferencial);

        for ($i = 0; $i < $linhasBnccReferencial; $i++) {
            $dadosBnccReferencial = db_utils::fieldsMemory($rsBnccReferencial, $i);
           /*
           * Valida se para o ano de destino, já não existe registros
           */
            $where = "ed168_ano = {$iAnoDestino}";
            $where .= " AND ed168_codigoreferencial = '{$dadosBnccReferencial->ed168_codigoreferencial}'";
            $sqlValidarBnccReferencial = $daoBnccReferencial->sql_query_file(null, '1', null, $where);
            $rsValidarBnccReferencial = db_query($sqlValidarBnccReferencial);

            if (pg_num_rows($rsValidarBnccReferencial) > 0) {
                continue;
            }

            $daoBnccReferencial->ed168_codigo = null;
            $daoBnccReferencial->ed168_ensino = $dadosBnccReferencial->ed168_ensino;
            $daoBnccReferencial->ed168_etapa = $dadosBnccReferencial->ed168_etapa;
            $daoBnccReferencial->ed168_codigohabilidade = $dadosBnccReferencial->ed168_codigohabilidade;
            $daoBnccReferencial->ed168_codigoreferencial = $dadosBnccReferencial->ed168_codigoreferencial;
            $daoBnccReferencial->ed168_habilidade = pg_escape_string($dadosBnccReferencial->ed168_habilidade);
            $daoBnccReferencial->ed168_objeto_conhecimento = pg_escape_string($dadosBnccReferencial->ed168_objeto_conhecimento);
            $daoBnccReferencial->ed168_ano = $iAnoDestino;

            $daoBnccReferencial->incluir(null);

            if ($daoBnccReferencial->erro_status == 0) {
                throw new Exception($daoBnccReferencial->erro_msg);
            }
        }
    } catch (Exception $oErro) {
        $sqlerro  = true;
        $erro_msg = $oErro->getMessage();

        if ($oErro->getCode() == 2) {
            $cldb_viradaitemlog->c35_log           = $oErro->getMessage();
            $cldb_viradaitemlog->c35_codarq        = 1010503;
            $cldb_viradaitemlog->c35_db_viradaitem = $cldb_viradaitem->c31_sequencial;
            $cldb_viradaitemlog->c35_data          = date("Y-m-d");
            $cldb_viradaitemlog->c35_hora          = date("H:i");
            $cldb_viradaitemlog->incluir(null);
            if ($cldb_viradaitemlog->erro_status == 0) {
                $erro_msg .= $cldb_viradaitemlog->erro_msg;
            }
        }
    }
    db_atutermometro(1, 2, 'termometroitem', 1, $sMensagemTermometroItem);
}

