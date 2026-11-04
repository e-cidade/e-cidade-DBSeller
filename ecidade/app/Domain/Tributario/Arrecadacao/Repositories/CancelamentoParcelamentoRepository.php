<?php

namespace App\Domain\Tributario\Arrecadacao\Repositories;

use DBString;
use Exception;
use Illuminate\Support\Facades\DB;
use stdClass;
use Throwable;
use App\Domain\Tributario\Arrecadacao\Repositories\TermoRepository;

class CancelamentoParcelamentoRepository
{

    public function cancela(
        $parcel,
        $motivo,
        $processo,
        $DB_anousu,
        $DB_instit,
        $DB_datausu,
        $DB_id_usuario
    ) {
        try {
            DB::beginTransaction();
            DB::select("select fc_putsession('DB_anousu','$DB_anousu');");
            DB::select("select fc_putsession('DB_instit','$DB_instit');");
            DB::select("select fc_putsession('DB_datausu','$DB_datausu');");
            DB::select("select fc_putsession('DB_id_usuario','$DB_id_usuario');");
            $motivo = DBString::utf8_decode_all($motivo);
            $aSimulacao = array();

            $sSqlTermo = "select v07_parcel,                                                            ";
            $sSqlTermo .= "       v07_numpre,                                                           ";
            $sSqlTermo .= "       v07_dtlanc,                                                           ";
            $sSqlTermo .= "       v07_totpar,                                                           ";
            $sSqlTermo .= "       (select count(distinct k00_numpar)                                    ";
            $sSqlTermo .= "          from arrepaga                                                      ";
            $sSqlTermo .= "         where k00_numpre = v07_numpre ) as qtd_parcelas_pagas,              ";
            $sSqlTermo .= "       k40_tipoanulacao,                                                     ";
            $sSqlTermo .= "       k40_codigo                                                            ";
            $sSqlTermo .= "  from termo                                                                 ";
            $sSqlTermo .= "       inner join cadtipoparc on cadtipoparc.k40_codigo = termo.v07_desconto ";
            $sSqlTermo .= " where termo.v07_parcel = {$parcel}";
            $oTermo = DB::select($sSqlTermo);

            if (count($oTermo) == 0) {
                return;
            }

            $oSimulacaoAnula = DB::select("select fc_parc_gera_simulacao_anulacao($parcel) as simulaanula");

            if ($oSimulacaoAnula[0]->simulaanula !== '1 - OK') {
                return;
            }

            $sFormulaCalculoSaldo = " 0 ";
            $sFormulaCalculoValHistRet = " 0 ";
            $dDataCorrecaoOrigens = $oTermo[0]->v07_dtlanc;

            if (isset($oTermo[0]->k40_tipoanulacao)
                && ($oTermo[0]->k40_tipoanulacao == 3
                    || $oTermo[0]->k40_tipoanulacao == 2)
            ) {
                $sFormulaCalculoSaldo = " v23_valor - 
                ( ( v23_valor * ( (v23_vlrabatido * 100) / ( v23_vlrcor+v23_vlrjur+v23_vlrmul ) ) ) / 100 )";

                if ($oTermo[0]->k40_tipoanulacao == 2) {
                    $dDataCorrecaoOrigens = $DB_datausu;
                }


                $sFormulaCalculoValHistRet = " round((v23_valor - (
                    (v23_valor * ((v23_vlrabatido*100)/(v23_vlrcor+v23_vlrjur+v23_vlrmul)))/100)),2) ";
            } else {
                $sFormulaCalculoSaldo = " ( v23_valor - v23_vlrabatido ) ";
            }

            $sSql = "select *,                                                                       ";
            $sSql .= "   (select sum(k00_valor)                                                      ";
            $sSql .= "   from arrecad                                                                ";
            $sSql .= "where k00_numpre = " . $oTermo[0]->v07_numpre . " ) as valor_parcelas_abertas,      ";

            /*
             *
             * Informacoes utilizadas para montar o array de objetos oTotal
             *
             */
            $sSql .= "round( x.topo_valor_corrigido_origem * 
            (select fc_juros(k02_codigo::integer,               ";
            $sSql .= "                                 v23_dtvenc::date,                               ";
            $sSql .= "                                 '" . $oTermo[0]->v07_dtlanc . "'::date,             ";
            $sSql .= "                                 v23_dtoper::date,                                ";
            $sSql .= "                                 false,                                           ";
            $sSql .= "extract( year from '" . $oTermo[0]->v07_dtlanc . "'::date)::integer ) ),2) 
            as topo_valor_juros_origem,";
            $sSql .= "round( x.topo_valor_corrigido_origem * (select fc_multa(k02_codigo::integer,     ";
            $sSql .= "                                 v23_dtvenc::date,                               ";
            $sSql .= "                                              '" . $oTermo[0]->v07_dtlanc . "'::date,";
            $sSql .= "                                 v23_dtoper::date,                                ";
            $sSql .= "extract( year from '" . $oTermo[0]->v07_dtlanc . "'::date)::integer ) ) ,2) 
            as topo_valor_multa_origem,";

            /*
             *
             * Informacoes utilizadas para montar o array de objetos
             * aSimulacao que ira montar a grid das informacoes das origens
             *
             */
            $sSql .= "round( x.grid_valor_corrigido_origem * (select fc_juros(k02_codigo::integer,                ";
            $sSql .= "                                                      v23_dtvenc::date,                     ";
            $sSql .= "                                                      '{$dDataCorrecaoOrigens}'::date,      ";
            $sSql .= "                                                      v23_dtoper::date,                     ";
            $sSql .= "                                                      false,                                ";
            $sSql .= "extract( year from '{$dDataCorrecaoOrigens}'::date)::integer ) ),2) as grid_valor_juros_origem,";
            $sSql .= "round( x.grid_valor_corrigido_origem * (select fc_multa(k02_codigo::integer,                ";
            $sSql .= "                                                       v23_dtvenc::date,                    ";
            $sSql .= "                                                       '{$dDataCorrecaoOrigens}'::date,     ";
            $sSql .= "                                                       v23_dtoper::date,                    ";
            $sSql .= "extract( year from '{$dDataCorrecaoOrigens}'::date)::integer ) ) ,2) as grid_valor_multa_origem,";
            $sSql .= "round( x.grid_valor_corrigido_retorno * (select fc_juros(k02_codigo::integer,               ";
            $sSql .= "                                  v23_dtvenc::date,                                         ";
            $sSql .= "                             '" . $DB_datausu . "'::date,    ";
            $sSql .= "                                                       v23_dtoper::date,                    ";
            $sSql .= "                                                       false,                               ";
            $sSql .= "            " . $DB_anousu . "::integer ) ),2) as grid_valor_juros_retorno, ";
            $sSql .= "       round( x.grid_valor_corrigido_retorno * (select fc_multa(k02_codigo::integer,        ";
            $sSql .= "                                  v23_dtvenc::date,                                         ";
            $sSql .= "                             '" . $DB_datausu . "'::date,    ";
            $sSql .= "                                  v23_dtoper::date,                                         ";
            $sSql .= "           " . $DB_anousu . "::integer ) ) ,2) as grid_valor_multa_retorno  ";
            $sSql .= "  from ( select v23_numpre,                                                                 ";
            $sSql .= "                v23_numpar,                                                                 ";
            $sSql .= "                v21_sequencial,                                                             ";
            $sSql .= "                v23_sequencial,                                                             ";
            $sSql .= "                v21_percretorno,                                                            ";
            $sSql .= "                v21_valordevido,                                                            ";
            $sSql .= "                coalesce(v21_valorpago,0) as v21_valorpago,                                 ";
            $sSql .= "                v21_formaanulacao,                                                          ";
            $sSql .= "                k02_codigo,                                                                 ";
            $sSql .= "                k02_descr as v23_receit,                                                    ";
            $sSql .= "	               v23_dtoper,                                                                ";
            $sSql .= " v23_dtvenc,                                                                                ";
            $sSql .= " v23_valor,                                                                                 ";
            $sSql .= " v23_vlrcor,                                                                                ";
            $sSql .= " v23_vlrjur,                                                                                ";
            $sSql .= " v23_vlrmul,                                                                                ";
            $sSql .= " v23_vlrabatido,                                                                            ";
            $sSql .= " {$sFormulaCalculoSaldo} as saldo_pagar,                                                    ";
            $sSql .= " {$sFormulaCalculoValHistRet} as valor_historico_retorno,                                   ";
            $sSql .= " arreold.k00_valor as grid_valor_historico_origem,                                          ";

            /*
             *
             * Informacoes utilizadas para montar o array de objetos oTotal
             *
             */
            $sSql .= " round( ( select fc_corre(k02_codigo::integer,                                              ";
            $sSql .= "             arreold.k00_dtoper::date,                                                      ";
            $sSql .= "               arreold.k00_valor::float8,                                                   ";
            $sSql .= "               '" . $oTermo[0]->v07_dtlanc . "'::date,                                          ";
            $sSql .= "                extract( year from '" . $oTermo[0]->v07_dtlanc . "'::date)::integer,            ";
            $sSql .= "                 arreold.k00_dtvenc::date ) ), 2) as topo_valor_corrigido_origem,           ";

            /*
             *
             * Informacoes utilizadas para montar o array de objetos
             * aSimulacao que ira montar a grid das informacoes das origens
             *
             */
            $sSql .= "  round( ( select fc_corre(k02_codigo::integer,                                             ";
            $sSql .= "                            arreold.k00_dtoper::date,                                       ";
            $sSql .= "                      arreold.k00_valor::float8,                                            ";
            $sSql .= "                      '{$dDataCorrecaoOrigens}'::date,                                      ";
            $sSql .= "                        extract( year from '{$dDataCorrecaoOrigens}'::date)::integer,       ";
            $sSql .= "                        arreold.k00_dtvenc::date ) ), 2) as grid_valor_corrigido_origem,    ";
            $sSql .= "  round( ( select fc_corre(k02_codigo::integer,                                             ";
            $sSql .= "                                             arreold.k00_dtoper::date,                      ";
            $sSql .= "                         {$sFormulaCalculoSaldo}::float8,                                   ";
            $sSql .= "                            '" . $DB_datausu . "'::date,     ";
            $sSql .= "                           " . $DB_anousu . "::integer,                     ";
            $sSql .= "                           arreold.k00_dtvenc::date ) ), 2) as grid_valor_corrigido_retorno ";
            $sSql .= "from termosimulareg                                                                         ";
            $sSql .= "    inner join termosimula  on termosimulareg.v23_termosimula = termosimula.v21_sequencial  ";
            $sSql .= "     inner join arreold      on arreold.k00_numpre             = termosimulareg.v23_numpre  ";
            $sSql .= "                                       and arreold.k00_numpar = termosimulareg.v23_numpar   ";
            $sSql .= "                            and arreold.k00_receit             = termosimulareg.v23_receit  ";

            /**
             * Ajuste para consultar na arreold apenas tipo para inicial, caso for
             */

            $tipoParcelamento = DB::select("select fc_parc_gettipoparcelamento($parcel) as tipo;")[0]->tipo;

            if (trim($tipoParcelamento) == "termoini") {
                $sSql .= "and arreold.k00_tipo IN (SELECT a.k00_tipo
                                                     FROM arretipo a 
                                                    INNER JOIN cadtipo b  
                                                       ON a.k03_tipo = b.k03_tipo 
                                                    WHERE b.k03_tipo = 18)";
            }

            $sSql .= "                inner join tabrec       on tabrec.k02_codigo       = termosimulareg.v23_receit ";
            $sSql .= "                 left join tabrecjm     on tabrec.k02_codjm        = tabrecjm.k02_codjm        ";
            $sSql .= "          where v21_parcel = $parcel                                                           ";
            $sSql .= "            and v21_ativo = 'true'                                                             ";
            $sSql .= "          order by v23_sequencial ) as x                                                       ";

            $rsSimulacao = DB::select($sSql);

            if (count($rsSimulacao) == 0) {
                return;
            }

            $aSimulacao = $rsSimulacao;

            foreach ($aSimulacao as $stdLinha) {
                $updateSimulacao = DB::select("
                   update termosimulareg
                      set v23_vlrjur = {$stdLinha->grid_valor_juros_origem},
                          v23_vlrmul = {$stdLinha->grid_valor_multa_origem},
                          v23_vlrcor = {$stdLinha->grid_valor_corrigido_origem}
                    where v23_sequencial = $stdLinha->v23_sequencial;
                ");

                if (!$updateSimulacao) {
                    return;
                }
            }

            if (empty($processo)) {
                $processo = "null";
            }


            $termoRepository = TermoRepository::getInstance();

            $parcelamento = $termoRepository->getByCode($parcel);
            $persistiuObservacao = $termoRepository->adicionarObservacaoOrigem($parcelamento);

            if ($persistiuObservacao === false) {
                return;
            }
            $v21_sequencial = $aSimulacao[0]->v21_sequencial;

            $updateSimulacao = DB::select(
                "SELECT fc_excluiparcelamento($v21_sequencial, $DB_id_usuario, '$motivo', $processo)"
            );

            DB::commit();
            return true;
        } catch (Throwable $th) {
            DB::rollback();
            return;
        }
    }
}
