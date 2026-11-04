<?php


namespace ECidade\Educacao\Escola\Repository;

use \cl_periodocalendario;

use Exception;

/**
 * Class PeriodoCalendarioRepository
 * @package ECidade\Educacao\Escola\Repository
 */
class PeriodoCalendarioRepository extends Repository
{


    public function savePeriodo($codigo, $oParam, $dtIncioAnterior, $dtFimAnterior)
    {

        $oDaoPeriodoCalendario  = new cl_periodocalendario;
        $sWhere                 = " ed53_i_calendario = {$codigo}";
        $sCampos                = " ed53_i_codigo, ed53_d_inicio, ed53_d_fim, ed53_i_diasletivos, ";
        $sCampos                .= "ed53_i_semletivas, ed53_i_calendario ";
        $orderPrimeiro          = " ed53_d_inicio asc limit 1";
        $orderUltimo            = " ed53_d_inicio desc limit 1";
        $sSqlPeriodosPrimeiro   = $oDaoPeriodoCalendario->sql_query(
            null,
            $sCampos,
            $orderPrimeiro,
            $sWhere
        );
        $sSqlPeriodosUltimo     = $oDaoPeriodoCalendario->sql_query(
            null,
            $sCampos,
            $orderUltimo,
            $sWhere
        );
        
        $rsPeriodosPrimeiro = $oDaoPeriodoCalendario->sql_record($sSqlPeriodosPrimeiro);
        $rsPeriodosUltimo   = $oDaoPeriodoCalendario->sql_record($sSqlPeriodosUltimo);

        if ($oDaoPeriodoCalendario->numrows > 0) {
            $aPeriodosPrimeiro  = \db_utils::fieldsMemory($rsPeriodosPrimeiro, 0);
            $aPeriodosUltimo    = \db_utils::fieldsMemory($rsPeriodosUltimo, 0);
            $dtInicio = str_replace("/", "-", $oParam->dDataInicio);
            $dtInicio = date('Y-m-d', strtotime($dtInicio));
            $dtFim    = str_replace("/", "-", $oParam->dDataFim);
            $dtFim    = date('Y-m-d', strtotime($dtFim));
      
            $retornoInicio = $this->calculoDiasLetivos(
                'N',
                $aPeriodosPrimeiro->ed53_i_calendario,
                $dtInicio,
                $aPeriodosPrimeiro->ed53_d_fim
            );
          
            $retornoFim   = $this->calculoDiasLetivos(
                'N',
                $aPeriodosUltimo->ed53_i_calendario,
                $aPeriodosUltimo->ed53_d_inicio,
                $dtFim
            );
            $alterar = false;
      
            if ($aPeriodosPrimeiro->ed53_d_inicio == $dtIncioAnterior) {
                $oDaoPeriodoCalendarioIncio                     = \db_utils::getDao("periodocalendario");
                $oDaoPeriodoCalendarioIncio->ed53_i_codigo      = $aPeriodosPrimeiro->ed53_i_codigo;
                $oDaoPeriodoCalendarioIncio->ed53_d_inicio      = new \DBDate($oParam->dDataInicio);
                $oDaoPeriodoCalendarioIncio->ed53_i_diasletivos = $retornoInicio[0];
                $oDaoPeriodoCalendarioIncio->ed53_i_semletivas  = $retornoInicio[1];
                $oDaoPeriodoCalendarioIncio->alterar($aPeriodosPrimeiro->ed53_i_codigo);
                $alterar = true;
            }
      
            if ($aPeriodosUltimo->ed53_d_fim == $dtFimAnterior) {
                $oDaoPeriodoCalendarioFim                     = \db_utils::getDao("periodocalendario");
                $oDaoPeriodoCalendarioFim->ed53_i_codigo      = $aPeriodosUltimo->ed53_i_codigo;
                $oDaoPeriodoCalendarioFim->ed53_d_fim         = new \DBDate($oParam->dDataFim);
                $oDaoPeriodoCalendarioFim->ed53_i_diasletivos = $retornoFim[0];
                $oDaoPeriodoCalendarioFim->ed53_i_semletivas  = $retornoFim[1];
                $oDaoPeriodoCalendarioFim->alterar($aPeriodosUltimo->ed53_i_codigo);
                $alterar = true;
            }
      
            if ($alterar) {
                $sCamposPeriodo1 = "sum(ed53_i_diasletivos) as dias, sum(ed53_i_semletivas) as semanas";
                $sWherePeriodo1  = " ed53_i_calendario = {$codigo} AND ed09_c_somach = 'S'";
                $clperiodocalendario  = new cl_periodocalendario;
                $sql1                 = $clperiodocalendario->sql_query("", $sCamposPeriodo1, "", $sWherePeriodo1);
                $result1              = $clperiodocalendario->sql_record($sql1);

                if ($clperiodocalendario->numrows > 0) {
                    $acalendario  = \db_utils::fieldsMemory($result1, 0);
            
                    $sql2   = "UPDATE calendario ";
                    $sql2  .= "   SET ed52_i_diasletivos = {$acalendario->dias}, ";
                    $sql2  .= "       ed52_i_semletivas = {$acalendario->semanas} ";
                    $sql2  .= " WHERE ed52_i_codigo = {$codigo}";
                    $query2 = db_query($sql2);
                }
            }
        }
    }

    public function savePeriodoEscolas($codigoCalendario, $inicioAnterior, $fimAnterior, $dataInicio, $dataFim)
    {

        $oDaoPeriodoCalendario  = new cl_periodocalendario;
        $sWhere                 = " ed53_i_calendario = {$codigoCalendario}";
        $sCampos                = " *";
      
        $sSqlPeriodos           = $oDaoPeriodoCalendario->sql_query(
            null,
            $sCampos,
            "",
            $sWhere
        );
      
        $rsPeriodos = $oDaoPeriodoCalendario->sql_record($sSqlPeriodos);
        
        if ($oDaoPeriodoCalendario->numrows > 0) {
            $Periodos = \db_utils::getCollectionByRecord($rsPeriodos, false, false, true);
      
            foreach ($Periodos as $periodo) {
                if ($periodo->ed53_d_inicio == $inicioAnterior && $periodo->ed53_d_fim == $fimAnterior) {
                    $oDaoPeriodoCalendario                          = \db_utils::getDao("periodocalendario");
                    $oDaoPeriodoCalendario->ed53_i_codigo           = $periodo->ed53_i_codigo;
                    $oDaoPeriodoCalendario->ed53_i_calendario       = $periodo->ed53_i_calendario;
                    $oDaoPeriodoCalendario->ed53_i_periodoavaliacao = $periodo->ed53_i_periodoavaliacao;
                    $oDaoPeriodoCalendario->ed53_i_diasletivos      = $periodo->ed53_i_diasletivos;
                    $oDaoPeriodoCalendario->ed53_d_inicio           = $dataInicio;
                    $oDaoPeriodoCalendario->ed53_d_fim              = $dataFim;
                    $oDaoPeriodoCalendario->alterar($periodo->ed53_i_codigo);
                }
            }
        }
    }

    public function calculoDiasLetivosPeriodos($calendario, $sabado)
    {

        $clperiodocalendario = new cl_periodocalendario;

        $sql = $clperiodocalendario->sql_query("", "*", "", " ed09_c_somach = 'S' and ed53_i_calendario = $calendario");
        $result = $clperiodocalendario->sql_record($sql);

        if ($clperiodocalendario->numrows>0) {
            for ($xx=0; $xx<$clperiodocalendario->numrows; $xx++) {
                $periodo = \db_utils::fieldsMemory($result, $xx);
                $data_inicio = $periodo->ed53_d_inicio;
                $data_fim = $periodo->ed53_d_fim;

                $letivos = $this->calculoDiasLetivos($sabado, $calendario, $data_inicio, $data_fim);
                
                $diasletivos = $letivos[0];
                $semletivas = $letivos[1];

                $sql1 = "UPDATE periodocalendario 
                            SET
                            ed53_i_diasletivos = $diasletivos,
                            ed53_i_semletivas = $semletivas
                            WHERE ed53_i_codigo = $periodo->ed53_i_codigo
                        ";
                $query1 = db_query($sql1);
            }

            $sql2 = $clperiodocalendario->sql_query("", "sum(ed53_i_diasletivos) as dias1, sum(ed53_i_semletivas) as 
                                                    semanas1", "", " ed53_i_calendario = $calendario 
                                                    AND ed09_c_somach = 'S'");
            $result2 = $clperiodocalendario->sql_record($sql2);
            $periodoCalendario  = \db_utils::fieldsMemory($result2, 0);

            if ($periodoCalendario->dias1=="") {
                $dias1 = 0;
                $semanas1 = 0;
            } else {
                $dias1 = $periodoCalendario->dias1;
                $semanas1 = $periodoCalendario->semanas1;
            }

            $sql3 = "UPDATE calendario 
                        SET
                        ed52_i_diasletivos = $dias1,
                        ed52_i_semletivas = $semanas1
                        WHERE ed52_i_codigo = $calendario
                    ";
            db_query($sql3);
        }
    }

    public function calculoDiasLetivos($sabado, $calendario, $data_inicio, $data_fim)
    {
        $data_in = mktime(0, 0, 0, substr($data_inicio, 5, 2), substr($data_inicio, 8, 2), substr($data_inicio, 0, 4));
        $data_out = mktime(0, 0, 0, substr($data_fim, 5, 2), substr($data_fim, 8, 2), substr($data_fim, 0, 4));
        #pega a data de saida em UNIX_TIMESTAMP e diminui da data de entrada UNIX_TIMESTAMP
        $data_entre = $data_out - $data_in;
        #divide a diferenca das datas pelo numero de segundos de um dia e arredonda, para saber o numero de dias
        #inteiro que tem
        $dias = ceil($data_entre/86400);
        $dias2 = $dias;
        $day = 0;
        $nao_util = 0;
        #pega dia, mes e ano da data de entrada
        $d = date('d', $data_in);
        $m = date('m', $data_in);
        $y = date('Y', $data_in);
        #pega mes e ano da data de saida
        $m2 = date('m', $data_out);
        $y2 = date('Y', $data_out);
        #conta o numero de dias do mes de entrada
        $days_month = date("t", $data_in);
        $mi = date('m', $data_in);
        $semanas = 0;
        $primeiro_dia = date("w", mktime(
            0,
            0,
            0,
            substr($data_inicio, 5, 2),
            substr($data_inicio, 8, 2),
            substr($data_inicio, 0, 4)
        ));
        #se o dia da entrada + total de dias for menor que total de dias do mes, ou seja, se não passar do mesmo mês.
        if ($dias+$d <= $days_month) {
            for ($i = 0; $i < $dias+1; $i++) {
                if (date("w", mktime(0, 0, 0, $m, $d+$i, $y))==1) {
                    $semanas++;
                }
                $day++;
                #checa o dia da semana para cada dia do mês, se for igual a 0 (domingo) ou 6 (sabado) ele adiciona 1 no
                #dia não útil
                if ($sabado=="N") {
                    if (date("w", mktime(0, 0, 0, $m, $d+$i, $y)) == 0 ||
                    date("w", mktime(0, 0, 0, $m, $d+$i, $y)) == 6) {
                    #pesquisa no banco os feriados cadastrados se retornar aquele dia ele adiciona 1 no dia não útil
                        $res = db_query("SELECT * FROM feriado WHERE extract(month from ed54_d_data)=$m AND 
                                        extract(day from ed54_d_data)=$d+$i AND ed54_i_calendario=$calendario");
                        if (pg_num_rows($res)==0) {
                            $nao_util++;
                        } else {
                            if (pg_result($res, 0, 'ed54_c_dialetivo')=="N") {
                                $nao_util++;
                            }
                        }
                    } else {
                    #pesquisa no banco os feriados cadastrados se retornar aquele dia ele adiciona 1 no dia não útil
                        $res = db_query("SELECT * FROM feriado WHERE extract(month from ed54_d_data)=$m 
                                            AND extract(day from ed54_d_data)=$d+$i AND ed54_i_calendario=$calendario 
                                            AND ed54_c_dialetivo = 'N' ");
                        if ($row = pg_fetch_assoc($res)) {
                            $nao_util++;
                        }
                    }
                } else {
                    if (date("w", mktime(0, 0, 0, $m, $d+$i, $y)) == 0) {
                        #pesquisa no banco os feriados cadastrados se retornar aquele dia ele adiciona 1 no dia não útil
                        $res = db_query("SELECT * FROM feriado WHERE extract(month from ed54_d_data)=$m 
                                            AND extract(day from ed54_d_data)=$d+$i AND ed54_i_calendario=$calendario");
                        if (pg_num_rows($res)==0) {
                            $nao_util++;
                        } else {
                            if (pg_result($res, 0, 'ed54_c_dialetivo')=="N") {
                                $nao_util++;
                            }
                        }
                    } else {
                        #pesquisa no banco os feriados cadastrados se retornar aquele dia ele adiciona 1 no dia não útil
                        $res = db_query("SELECT * FROM feriado WHERE extract(month from ed54_d_data)=$m 
                                            AND extract(day from ed54_d_data)=$d+$i AND ed54_i_calendario=$calendario 
                                            AND ed54_c_dialetivo = 'N' ");
                        if ($row = pg_fetch_assoc($res)) {
                            $nao_util++;
                        }
                    }
                }
            }
        #se o dia da entrada + total de dias for maior que total de dias do mes, ou seja, se passar do mesmo mês.
        } else {
            #enquanto o mês de entrada for diferente do mês de saida ou ano de entrada for diferente do ano de saida.
            while ($m != $m2 || $y != $y2) {
                #pega total de dias do mes de entrada
                if ($m==$mi) {
                    $days_month = date("t", mktime(0, 0, 0, $m, $d, $y))-$d+1;
                } else {
                    $days_month = date("t", mktime(0, 0, 0, $m, $d, $y));
                }
                for ($i = 0; $i < $days_month; $i++) {
                    $day++;
                    if (date("w", mktime(0, 0, 0, $m, $d+$i, $y))==1) {
                        $semanas++;
                    }
                    #checa o dia da semana para cada dia do mês, se for igual a 0 (domingo) ou 6 (sabado) ele adiciona
                    #1 no dia não útil
                    if ($sabado=="N") {
                        if (date("w", mktime(0, 0, 0, $m, $d+$i, $y)) == 0 ||
                            date("w", mktime(0, 0, 0, $m, $d+$i, $y)) == 6) {
                            #pesquisa no banco os feriados cadastrados se retornar aquele dia ele adiciona
                            #1 no dia não útil
                            $res = db_query("SELECT * FROM feriado WHERE extract(month from ed54_d_data)=$m 
                                                AND extract(day from ed54_d_data)=$d+$i 
                                                AND ed54_i_calendario=$calendario");
                            if (pg_num_rows($res)==0) {
                                $nao_util++;
                            } else {
                                if (pg_result($res, 0, 'ed54_c_dialetivo')=="N") {
                                    $nao_util++;
                                }
                            }
                        } else {
                            #pesquisa no banco os feriados cadastrados se retornar aquele dia ele adiciona
                            #1 no dia não útil
                            $res = db_query("SELECT * FROM feriado WHERE extract(month from ed54_d_data)=$m 
                                                AND extract(day from ed54_d_data)=$d+$i 
                                                AND ed54_i_calendario=$calendario AND ed54_c_dialetivo = 'N' ");
                            if ($row = pg_fetch_assoc($res)) {
                                $nao_util++;
                            }
                        }
                    } else {
                        if (date("w", mktime(0, 0, 0, $m, $d+$i, $y)) == 0) {
                            #pesquisa no banco os feriados cadastrados se retornar aquele dia ele adiciona
                            #1 no dia não útil
                            $res = db_query("SELECT * FROM feriado WHERE extract(month from ed54_d_data)=$m 
                                                AND extract(day from ed54_d_data)=$d+$i 
                                                AND ed54_i_calendario=$calendario");
                            if (pg_num_rows($res)==0) {
                                $nao_util++;
                            } else {
                                if (pg_result($res, 0, 'ed54_c_dialetivo')=="N") {
                                    $nao_util++;
                                }
                            }
                        } else {
                            #pesquisa no banco os feriados cadastrados se retornar aquele dia ele adiciona
                            #1 no dia não útil
                            $res = db_query("SELECT * FROM feriado WHERE extract(month from ed54_d_data)=$m 
                                                AND extract(day from ed54_d_data)=$d+$i 
                                                AND ed54_i_calendario=$calendario AND ed54_c_dialetivo = 'N' ");
                            if ($row = pg_fetch_assoc($res)) {
                                $nao_util++;
                            }
                        }
                    }
                }
                #se o mes for igual a 12 (dezembro), mes recebe 1 (janeiro) e ano recebe +1 (próximo ano)
                if ($m == 12) {
                    $m = 1;
                    $y++;
                    #mês recebe mais 1 para fazer o mesmo processo do próximo mês
                } else {
                    $m++;
                }
                $d = 1;
                //$dias2 = $dias2 - $day;
                if ($m==$m2) {
                    $d3 = date('d', $data_out);
                    $m3 = date('m', $data_out);
                    $y3 = date('Y', $data_out);
                    for ($i = 0; $i < $d3; $i++) {
                        $day++;
                        if (date("w", mktime(0, 0, 0, $m3, $d+$i, $y3))==1) {
                            $semanas++;
                        }
                        #checa o dia da semana para cada dia do mês, se for igual a 0 (domingo) ou 6 (sabado) ele
                        #adiciona 1 no dia não útil
                        if ($sabado=="N") {
                            if (date("w", mktime(0, 0, 0, $m3, $d+$i, $y3)) == 0 ||
                                date("w", mktime(0, 0, 0, $m3, $d+$i, $y3)) == 6) {
                                #pesquisa no banco os feriados cadastrados se retornar aquele dia ele
                                #adiciona 1 no dia não útil
                                $res = db_query("SELECT * FROM feriado WHERE extract(month from ed54_d_data)=$m3 
                                                    AND extract(day from ed54_d_data)=$d+$i 
                                                    AND ed54_i_calendario=$calendario");
                                if (pg_num_rows($res)==0) {
                                    $nao_util++;
                                } else {
                                    if (pg_result($res, 0, 'ed54_c_dialetivo')=="N") {
                                        $nao_util++;
                                    }
                                }
                            } else {
                                #pesquisa no banco os feriados cadastrados se retornar aquele dia ele
                                #adiciona 1 no dia não útil
                                $res = db_query("SELECT * FROM feriado WHERE extract(month from ed54_d_data)=$m3 
                                                    AND extract(day from ed54_d_data)=$d+$i 
                                                    AND ed54_i_calendario=$calendario AND ed54_c_dialetivo = 'N' ");
                                if ($row = pg_fetch_assoc($res)) {
                                    $nao_util++;
                                }
                            }
                        } else {
                            if (date("w", mktime(0, 0, 0, $m3, $d+$i, $y3)) == 0) {
                                #pesquisa no banco os feriados cadastrados se retornar aquele dia ele
                                #adiciona 1 no dia não útil
                                $res = db_query("SELECT * FROM feriado WHERE extract(month from ed54_d_data)=$m3 
                                                    AND extract(day from ed54_d_data)=$d+$i 
                                                    AND ed54_i_calendario=$calendario");
                                if (pg_num_rows($res)==0) {
                                    $nao_util++;
                                } else {
                                    if (pg_result($res, 0, 'ed54_c_dialetivo')=="N") {
                                        $nao_util++;
                                    }
                                }
                            } else {
                                #pesquisa no banco os feriados cadastrados se retornar aquele dia ele
                                #adiciona 1 no dia não útil
                                $res = db_query("SELECT * FROM feriado WHERE extract(month from ed54_d_data)=$m3 
                                                    AND extract(day from ed54_d_data)=$d+$i 
                                                    AND ed54_i_calendario=$calendario AND ed54_c_dialetivo = 'N' ");
                                if ($row = pg_fetch_assoc($res)) {
                                    $nao_util++;
                                }
                            }
                        }
                    }
                }
            }
        }

        $diasletivos = $day-$nao_util;

        if ($primeiro_dia>1 && $primeiro_dia<6) {
            $semanas++;
        }

        $semletivas = $semanas;

        return [$diasletivos, $semletivas];
    }
}
