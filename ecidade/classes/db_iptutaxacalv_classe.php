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

/*
 * isscalc
 * issvar
 * vistorianumpre
 */

class cl_iptutaxacalv extends \DAOBasica
{
    public function __construct()
    {
        parent::__construct("cadastro.iptutaxacalv");
    }

    function sql_queryValoresCalculoIptu($iAnoCalculo)
    {
        $sSql  = " select                                                                                                                   ";
        $sSql .= "   ano_calculo,                                                                                                           ";
        $sSql .= "   codigo_receita,                                                                                                        ";
        $sSql .= "   descricao_receita,                                                                                                     ";
        $sSql .= "   coalesce(round(sum(valor_calculado), 2), 0.00) as valor_calculado,                                                     ";
        $sSql .= "   coalesce(round(sum(valor_isento), 2), 0.00) as valor_isento,                                                           ";
        $sSql .= "   coalesce(round(sum(valor_cancelado), 2), 0.00) as valor_cancelado,                                                     ";
        $sSql .= "   coalesce(round(sum(valor_compensado), 2), 0.00) as valor_compensado,                                                   ";
        $sSql .= "   coalesce(round(sum(valor_pago), 2), 0.00) as valor_pago,                                                               ";
        $sSql .= "   coalesce(round(sum(valor_a_pagar), 2), 0.00) as valor_a_pagar,                                                         ";
        $sSql .= "   coalesce(                                                                                                              ";
        $sSql .= "       round(                                                                                                             ";
        $sSql .= "           sum(                                                                                                           ";
        $sSql .= "               case                                                                                                       ";
        $sSql .= "                   when valor_importado_arreold <> null then valor_importado_arreold                                      ";
        $sSql .= "                   else valor_importado_outros                                                                            ";
        $sSql .= "               end                                                                                                        ";
        $sSql .= "           ),                                                                                                             ";
        $sSql .= "           2                                                                                                              ";
        $sSql .= "       ),                                                                                                                 ";
        $sSql .= "       0.00                                                                                                               ";
        $sSql .= "   ) AS valor_importado,                                                                                                  ";
        $sSql .= "   (                                                                                                                      ";
        $sSql .= "       SELECT                                                                                                             ";
        $sSql .= "           Count(j151_matric)                                                                                             ";
        $sSql .= "       FROM                                                                                                               ";
        $sSql .= "           iptutaxacalv                                                                                                   ";
        $sSql .= "           INNER JOIN iptutaxanump ON j152_iptutaxanump = j151_codigo                                                     ";
        $sSql .= "           INNER JOIN iptucadtaxaexe ON j151_iptucadtaxaexe = j08_iptucadtaxaexe                                          ";
        $sSql .= "       WHERE                                                                                                              ";
        $sSql .= "           j08_anousu = ano_calculo                                                                                       ";
        $sSql .= "           AND j08_tabrec = codigo_receita                                                                                ";
        $sSql .= "           AND j152_valor > 0                                                                                             ";
        $sSql .= "   ) as quantidade                                                                                                        ";
        $sSql .= " from                                                                                                                     ";
        $sSql .= " (                                                                                                                        ";
        $sSql .= "     select                                                                                                               ";
        $sSql .= "         y.k02_codigo as codigo_receita,                                                                                  ";
        $sSql .= "         y.j08_anousu as ano_calculo,                                                                                     ";
        $sSql .= "         y.k02_descr as descricao_receita,                                                                                ";
        $sSql .= "         sum(                                                                                                             ";
        $sSql .= "             case                                                                                                         ";
        $sSql .= "                 when j152_valor > 0 then j152_valor                                                                      ";
        $sSql .= "                 else 0                                                                                                   ";
        $sSql .= "             end                                                                                                          ";
        $sSql .= "         ) as valor_calculado,                                                                                            ";
        $sSql .= "         sum(                                                                                                             ";
        $sSql .= "             CASE                                                                                                         ";
        $sSql .= "                 WHEN j152_valor < 0                                                                                      ";
        $sSql .= "                 and (                                                                                                    ";
        $sSql .= "                     1 = (                                                                                                ";
        $sSql .= "                         select                                                                                           ";
        $sSql .= "                             1                                                                                            ";
        $sSql .= "                         from                                                                                             ";
        $sSql .= "                             iptucalhconf                                                                                 ";
        $sSql .= "                             inner join iptuisen on y.j151_matric = iptuisen.j46_matric                                   ";
        $sSql .= "                             and y.j08_anousu = {$iAnoCalculo}                                                            ";
        $sSql .= "                             inner join isenexe on isenexe.j47_codigo = iptuisen.j46_codigo                               ";
        $sSql .= "                             inner join tipoisen on tipoisen.j45_tipo = iptuisen.j46_tipo                                 ";
        $sSql .= "                         where                                                                                            ";
        $sSql .= "                             iptucalhconf.j89_codhis = y.j152_codhis                                                      ";
        $sSql .= "                             and isenexe.j47_anousu = y.j08_anousu                                                        ";
        $sSql .= "                         limit                                                                                            ";
        $sSql .= "                             1                                                                                            ";
        $sSql .= "                     )                                                                                                    ";
        $sSql .= "                 ) THEN j152_valor * -1                                                                                   ";
        $sSql .= "                 ELSE 0                                                                                                   ";
        $sSql .= "             END                                                                                                          ";
        $sSql .= "         ) AS valor_isento,                                                                                               ";
        $sSql .= "         (                                                                                                                ";
        $sSql .= "             select                                                                                                       ";
        $sSql .= "                 sum(arrecant.k00_valor)                                                                                  ";
        $sSql .= "             from                                                                                                         ";
        $sSql .= "                 arrecant                                                                                                 ";
        $sSql .= "                 inner join cancdebitosreg on k21_numpre = k00_numpre                                                     ";
        $sSql .= "                 and k21_numpar = k00_numpar                                                                              ";
        $sSql .= "                 and k21_receit = k00_receit                                                                              ";
        $sSql .= "                 inner join cancdebitosprocreg on k24_cancdebitosreg = k21_sequencia                                      ";
        $sSql .= "             where                                                                                                        ";
        $sSql .= "                 arrecant.k00_numpre = y.j151_numpre                                                                      ";
        $sSql .= "                 and arrecant.k00_receit = y.k02_codigo                                                                   ";
        $sSql .= "         ) as valor_cancelado,                                                                                            ";
        $sSql .= "         (                                                                                                                ";
        $sSql .= "             select                                                                                                       ";
        $sSql .= "                 sum(valor)                                                                                               ";
        $sSql .= "             from                                                                                                         ";
        $sSql .= "                 (                                                                                                        ";
        $sSql .= "                     select                                                                                               ";
        $sSql .= "                         sum(arrecant.k00_valor) as valor                                                                 ";
        $sSql .= "                     from                                                                                                 ";
        $sSql .= "                         arrecant                                                                                         ";
        $sSql .= "                     where                                                                                                ";
        $sSql .= "                         exists(                                                                                          ";
        $sSql .= "                             select                                                                                       ";
        $sSql .= "                                 1                                                                                        ";
        $sSql .= "                             FROM                                                                                         ";
        $sSql .= "                                 abatimentoutilizacaodestino                                                              ";
        $sSql .= "                                 inner join abatimentoutilizacao on k157_sequencial = k170_utilizacao                     ";
        $sSql .= "                                 inner join abatimento on k125_sequencial = k157_abatimento                               ";
        $sSql .= "                             where                                                                                        ";
        $sSql .= "                                 arrecant.k00_numpre = k170_numpre                                                        ";
        $sSql .= "                                 and arrecant.k00_numpar = k170_numpar                                                    ";
        $sSql .= "                                 and arrecant.k00_receit = k170_receit                                                    ";
        $sSql .= "                                 and k125_tipoabatimento = " . Abatimento::TIPO_CREDITO . "                               ";
        $sSql .= "                             limit                                                                                        ";
        $sSql .= "                                 1                                                                                        ";
        $sSql .= "                         )                                                                                                ";
        $sSql .= "                         and arrecant.k00_numpre = y.j151_numpre                                                          ";
        $sSql .= "                         and arrecant.k00_receit = y.k02_codigo                                                           ";
        $sSql .= "                     union                                                                                                ";
        $sSql .= "                     all                                                                                                  ";
        $sSql .= "                     select                                                                                               ";
        $sSql .= "                         sum(abatimentoarreckey.k128_valorabatido) AS valor                                               ";
        $sSql .= "                     from                                                                                                 ";
        $sSql .= "                         arrecad                                                                                          ";
        $sSql .= "                         inner join arreckey on arrecad.k00_numpre = arreckey.k00_numpre                                  ";
        $sSql .= "                         and arrecad.k00_numpar = arreckey.k00_numpar                                                     ";
        $sSql .= "                         and arrecad.k00_receit = arreckey.k00_receit                                                     ";
        $sSql .= "                         inner join abatimentoarreckey on abatimentoarreckey.k128_arreckey = arreckey.k00_sequencial      ";
        $sSql .= "                         inner join abatimento on abatimentoarreckey.k128_abatimento = abatimento.k125_sequencial         ";
        $sSql .= "                     where                                                                                                ";
        $sSql .= "                         arrecad.k00_numpre = y.j151_numpre                                                               ";
        $sSql .= "                         and arrecad.k00_receit = y.k02_codigo                                                            ";
        $sSql .= "                         and abatimento.k125_tipoabatimento = " . Abatimento::TIPO_COMPENSACAO  . "                       ";
        $sSql .= "                 ) as valor                                                                                               ";
        $sSql .= "         ) as valor_compensado,                                                                                           ";
        $sSql .= "         (                                                                                                                ";
        $sSql .= "             select                                                                                                       ";
        $sSql .= "                 sum(valor)                                                                                               ";
        $sSql .= "             from                                                                                                         ";
        $sSql .= "                 (                                                                                                        ";
        $sSql .= "                     select                                                                                               ";
        $sSql .= "                         (arrecant.k00_valor) as valor                                                                    ";
        $sSql .= "                     from                                                                                                 ";
        $sSql .= "                         arrecant                                                                                         ";
        $sSql .= "                     where                                                                                                ";
        $sSql .= "                         exists(                                                                                          ";
        $sSql .= "                             select                                                                                       ";
        $sSql .= "                                 1                                                                                        ";
        $sSql .= "                             from                                                                                         ";
        $sSql .= "                                 arrepaga                                                                                 ";
        $sSql .= "                             where                                                                                        ";
        $sSql .= "                                 k00_numpre = arrecant.k00_numpre                                                         ";
        $sSql .= "                                 and k00_numpar = arrecant.k00_numpar                                                     ";
        $sSql .= "                                 and k00_receit = arrecant.k00_receit                                                     ";
        $sSql .= "                         )                                                                                                ";
        $sSql .= "                         and arrecant.k00_numpre = y.j151_numpre                                                          ";
        $sSql .= "                         and arrecant.k00_receit = y.k02_codigo                                                           ";
        $sSql .= "                         and not exists(                                                                                  ";
        $sSql .= "                             select                                                                                       ";
        $sSql .= "                                 1                                                                                        ";
        $sSql .= "                             from                                                                                         ";
        $sSql .= "                                 abatimentoutilizacaodestino                                                              ";
        $sSql .= "                                 inner join abatimentoutilizacao on k157_sequencial = k170_utilizacao                     ";
        $sSql .= "                                 inner join abatimento on k125_sequencial = k157_abatimento                               ";
        $sSql .= "                             where                                                                                        ";
        $sSql .= "                                 k170_numpre = arrecant.k00_numpre                                                        ";
        $sSql .= "                                 and k170_numpar = arrecant.k00_numpar                                                    ";
        $sSql .= "                                 and k170_receit = arrecant.k00_receit                                                    ";
        $sSql .= "                                 and k125_tipoabatimento = " . Abatimento::TIPO_CREDITO . "                               ";
        $sSql .= "                             limit                                                                                        ";
        $sSql .= "                                 1                                                                                        ";
        $sSql .= "                         )                                                                                                ";
        $sSql .= "                     union                                                                                                ";
        $sSql .= "                     all                                                                                                  ";
        $sSql .= "                     select                                                                                               ";
        $sSql .= "                         sum(abatimentoarreckey.k128_valorabatido) as valor                                               ";
        $sSql .= "                     from                                                                                                 ";
        $sSql .= "                         arrecad                                                                                          ";
        $sSql .= "                         inner join arreckey on arrecad.k00_numpre = arreckey.k00_numpre                                  ";
        $sSql .= "                         and arrecad.k00_numpar = arreckey.k00_numpar                                                     ";
        $sSql .= "                         and arrecad.k00_receit = arreckey.k00_receit                                                     ";
        $sSql .= "                         inner join abatimentoarreckey on abatimentoarreckey.k128_arreckey = arreckey.k00_sequencial      ";
        $sSql .= "                         inner join abatimento on abatimentoarreckey.k128_abatimento = abatimento.k125_sequencial         ";
        $sSql .= "                     where                                                                                                ";
        $sSql .= "                         arrecad.k00_numpre = y.j151_numpre                                                               ";
        $sSql .= "                         and arrecad.k00_receit = y.k02_codigo                                                            ";
        $sSql .= "                         and abatimento.k125_tipoabatimento = " . Abatimento::TIPO_PAGAMENTO_PARCIAL . "                  ";
        $sSql .= "                     union                                                                                                ";
        $sSql .= "                     all                                                                                                  ";
        $sSql .= "                     select                                                                                               ";
        $sSql .= "                         sum(abatimentoarreckey.k128_valorabatido) as valor                                               ";
        $sSql .= "                     from                                                                                                 ";
        $sSql .= "                         arrecant                                                                                         ";
        $sSql .= "                         inner join arreckey on arrecant.k00_numpre = arreckey.k00_numpre                                 ";
        $sSql .= "                         and arrecant.k00_numpar = arreckey.k00_numpar                                                    ";
        $sSql .= "                         and arrecant.k00_receit = arreckey.k00_receit                                                    ";
        $sSql .= "                         inner join abatimentoarreckey on abatimentoarreckey.k128_arreckey = arreckey.k00_sequencial      ";
        $sSql .= "                         inner join abatimento on abatimentoarreckey.k128_abatimento = abatimento.k125_sequencial         ";
        $sSql .= "                     where                                                                                                ";
        $sSql .= "                         arrecant.k00_numpre = y.j151_numpre                                                              ";
        $sSql .= "                         and arrecant.k00_receit = y.k02_codigo                                                           ";
        $sSql .= "                         and abatimento.k125_tipoabatimento = " . Abatimento::TIPO_PAGAMENTO_PARCIAL  . "                 ";
        $sSql .= "                 ) as y                                                                                                   ";
        $sSql .= "         ) AS valor_pago,                                                                                                 ";
        $sSql .= "         (                                                                                                                ";
        $sSql .= "             select                                                                                                       ";
        $sSql .= "                 sum(arrecad.k00_valor)                                                                                   ";
        $sSql .= "             from                                                                                                         ";
        $sSql .= "                 arrecad                                                                                                  ";
        $sSql .= "             where                                                                                                        ";
        $sSql .= "                 arrecad.k00_numpre = y.j151_numpre                                                                       ";
        $sSql .= "                 and arrecad.k00_receit = y.k02_codigo                                                                    ";
        $sSql .= "         ) as valor_a_pagar,                                                                                              ";
        $sSql .= "         (                                                                                                                ";
        $sSql .= "             select                                                                                                       ";
        $sSql .= "                 sum(k00_valor) as valor                                                                                  ";
        $sSql .= "             from                                                                                                         ";
        $sSql .= "                 arreold                                                                                                  ";
        $sSql .= "             where                                                                                                        ";
        $sSql .= "                 k00_numpre in (                                                                                          ";
        $sSql .= "                     select                                                                                               ";
        $sSql .= "                         distinct k10_numpre                                                                              ";
        $sSql .= "                     from                                                                                                 ";
        $sSql .= "                         divold                                                                                           ";
        $sSql .= "                     where                                                                                                ";
        $sSql .= "                         (k10_numpre = y.j151_numpre)                                                                     ";
        $sSql .= "                 )                                                                                                        ";
        $sSql .= "                 and k00_receit = y.k02_codigo                                                                            ";
        $sSql .= "             group by                                                                                                     ";
        $sSql .= "                 k00_receit                                                                                               ";
        $sSql .= "         ) as valor_importado_arreold,                                                                                    ";
        $sSql .= "         (                                                                                                                ";
        $sSql .= "             SELECT                                                                                                       ";
        $sSql .= "                 sum(x.valor) as valor                                                                                    ";
        $sSql .= "             FROM                                                                                                         ";
        $sSql .= "                 (                                                                                                        ";
        $sSql .= "                     SELECT                                                                                               ";
        $sSql .= "                         DISTINCT dv13_numpre as numpre,                                                                  ";
        $sSql .= "                         dv05_vlrhis as valor                                                                             ";
        $sSql .= "                     FROM                                                                                                 ";
        $sSql .= "                         diverimportaold                                                                                  ";
        $sSql .= "                         INNER JOIN diversos ON dv05_coddiver = dv13_diversos                                             ";
        $sSql .= "                     WHERE                                                                                                ";
        $sSql .= "                         dv13_numpre = y.j151_numpre                                                                      ";
        $sSql .= "                         AND dv13_receita = y.k02_codigo                                                                  ";
        $sSql .= "                     GROUP BY                                                                                             ";
        $sSql .= "                         dv13_numpre,                                                                                     ";
        $sSql .= "                         dv13_diversos,                                                                                   ";
        $sSql .= "                         dv05_vlrhis                                                                                      ";
        $sSql .= "                     UNION                                                                                                ";
        $sSql .= "                     ALL                                                                                                  ";
        $sSql .= "                     SELECT                                                                                               ";
        $sSql .= "                         DISTINCT k10_numpre as numpre,                                                                   ";
        $sSql .= "                         v01_vlrhis as valor                                                                              ";
        $sSql .= "                     FROM                                                                                                 ";
        $sSql .= "                         divold                                                                                           ";
        $sSql .= "                         INNER JOIN divida ON k10_coddiv = v01_coddiv                                                     ";
        $sSql .= "                     WHERE                                                                                                ";
        $sSql .= "                         k10_numpre = y.j151_numpre                                                                       ";
        $sSql .= "                         AND k10_receita = y.k02_codigo                                                                   ";
        $sSql .= "                     GROUP BY                                                                                             ";
        $sSql .= "                         k10_numpre,                                                                                      ";
        $sSql .= "                         k10_coddiv,                                                                                      ";
        $sSql .= "                         v01_vlrhis                                                                                       ";
        $sSql .= "                 ) AS x                                                                                                   ";
        $sSql .= "             UNION                                                                                                        ";
        $sSql .= "             ALL                                                                                                          ";
        $sSql .= "             select                                                                                                       ";
        $sSql .= "                 sum(k00_valor) as valor                                                                                  ";
        $sSql .= "             from                                                                                                         ";
        $sSql .= "                 arreold                                                                                                  ";
        $sSql .= "             where                                                                                                        ";
        $sSql .= "                 k00_numpre in (                                                                                          ";
        $sSql .= "                     select                                                                                               ";
        $sSql .= "                         distinct q05_numpre                                                                              ";
        $sSql .= "                     from                                                                                                 ";
        $sSql .= "                         issvar                                                                                           ";
        $sSql .= "                     where                                                                                                ";
        $sSql .= "                         q05_numpre = y.j151_numpre                                                                       ";
        $sSql .= "                 )                                                                                                        ";
        $sSql .= "                 and k00_receit = y.k02_codigo                                                                            ";
        $sSql .= "             group by                                                                                                     ";
        $sSql .= "                 k00_receit                                                                                               ";
        $sSql .= "         ) as valor_importado_outros                                                                                      ";
        $sSql .= "     from                                                                                                                 ";
        $sSql .= "         (                                                                                                                ";
        $sSql .= "             select                                                                                                       ";
        $sSql .= "                 k02_codigo,                                                                                              ";
        $sSql .= "                 j151_numpre,                                                                                             ";
        $sSql .= "                 j08_anousu,                                                                                              ";
        $sSql .= "                 k02_descr,                                                                                               ";
        $sSql .= "                 j152_valor,                                                                                              ";
        $sSql .= "                 j151_matric,                                                                                             ";
        $sSql .= "                 j152_codhis                                                                                              ";
        $sSql .= "             from                                                                                                         ";
        $sSql .= "                 tabrec                                                                                                   ";
        $sSql .= "                 inner join iptucadtaxaexe on iptucadtaxaexe.j08_tabrec = tabrec.k02_codigo                               ";
        $sSql .= "                 inner join iptutaxanump on iptutaxanump.j151_iptucadtaxaexe = iptucadtaxaexe.j08_iptucadtaxaexe          ";
        $sSql .= "                 inner join iptutaxacalv on iptutaxacalv.j152_iptutaxanump = j151_codigo                                  ";
        $sSql .= "             where                                                                                                        ";
        $sSql .= "                 iptucadtaxaexe.j08_anousu = {$iAnoCalculo}                                                               ";
        $sSql .= "             union                                                                                                        ";
        $sSql .= "             select                                                                                                       ";
        $sSql .= "                 k02_codigo,                                                                                              ";
        $sSql .= "                 j20_numpre,                                                                                              ";
        $sSql .= "                 j20_anousu,                                                                                              ";
        $sSql .= "                 k02_descr,                                                                                               ";
        $sSql .= "                 j21_valor,                                                                                               ";
        $sSql .= "                 j20_matric,                                                                                              ";
        $sSql .= "                 j21_codhis                                                                                               ";
        $sSql .= "             from                                                                                                         ";
        $sSql .= "                 tabrec                                                                                                   ";
        $sSql .= "                 inner join iptucalv on k02_codigo = j21_receit                                                           ";
        $sSql .= "                 inner join iptunump on j21_anousu = j20_anousu                                                           ";
        $sSql .= "                 and j21_matric = j20_matric                                                                              ";
        $sSql .= "             where                                                                                                        ";
        $sSql .= "                 j20_anousu = {$iAnoCalculo}                                                                              ";
        $sSql .= "         ) as y                                                                                                           ";
        $sSql .= "     where                                                                                                                ";
        $sSql .= "         y.j08_anousu = {$iAnoCalculo}                                                                                    ";
        $sSql .= "     group by                                                                                                             ";
        $sSql .= "         y.k02_codigo,                                                                                                    ";
        $sSql .= "         y.j08_anousu,                                                                                                    ";
        $sSql .= "         y.k02_descr,                                                                                                     ";
        $sSql .= "         y.j151_numpre                                                                                                    ";
        $sSql .= " ) as x                                                                                                                   ";
        $sSql .= " group by                                                                                                                 ";
        $sSql .= " ano_calculo,                                                                                                             ";
        $sSql .= " codigo_receita,                                                                                                          ";
        $sSql .= " descricao_receita                                                                                                        ";
        $sSql .= " order by                                                                                                                 ";
        $sSql .= " codigo_receita                                                                                                           ";

        return $sSql;
    }
}
