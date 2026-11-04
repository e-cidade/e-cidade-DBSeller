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

namespace ECidade\Tributario\Arrecadacao\ModeloImpressao\Repository;

class CobrancaRegistrada
{
    public static function getDebitosRecibo($iNumnov, $iInstit)
    {
        $sSql  = " select exerc,                                                                                      ";
        $sSql .= "        sum(vlrhist)    as historico,                                                               ";
        $sSql .= "        sum(principal)  as corrigido,                                                               ";
        $sSql .= "        sum(juro)       as juro,                                                                    ";
        $sSql .= "        sum(multa)      as multa,                                                                   ";
        $sSql .= "        sum(desconto)   as desconto,                                                                ";
        $sSql .= "        sum(principal) + sum(juro) + sum(multa) + sum(desconto) as total                            ";

        $sSql .= "   from ( select case                                                                               ";
        $sSql .= "                   when divida.v01_exerc is null                                                    ";
        $sSql .= "                     then termo.exerc                                                               ";
        $sSql .= "                     else divida.v01_exerc                                                          ";
        $sSql .= "                 end as exerc,                                                                      ";
        $sSql .= "                 case                                                                               ";
        $sSql .= "                   when termo.perc is not null                                                      ";
        $sSql .= "                     then recibopaga.k00_valor_historico * perc                                     ";
        $sSql .= "                     else recibopaga.k00_valor_historico                                            ";
        $sSql .= "                 end as vlrhist,                                                                    ";
        $sSql .= "                 case                                                                               ";
        $sSql .= "                   when k02_tabrectipo = 1                                                          ";
        $sSql .= "                     then                                                                           ";
        $sSql .= "                       case                                                                         ";
        $sSql .= "                         when termo.perc is not null                                                ";
        $sSql .= "                         then recibopaga.k00_valor*perc                                             ";
        $sSql .= "                         else case                                                                  ";
        $sSql .= "                                when ( select ac.k00_numpre                                         ";
        $sSql .= "                                         from arrecad ac                                            ";
        $sSql .= "                                      where ac.k00_numpre = recibopaga.k00_numpre                   ";
        $sSql .= "                                        and ac.k00_numpar = recibopaga.k00_numpar                   ";
        $sSql .= "                                        and ac.k00_receit = recibopaga.k00_receit) is null          ";
        $sSql .= "                                then 0                                                              ";
        $sSql .= "                                else recibopaga.k00_valor                                           ";
        $sSql .= "                              end                                                                   ";
        $sSql .= "                       end                                                                          ";
        $sSql .= "                     else 0                                                                         ";
        $sSql .= "                   end as principal,                                                                ";
        $sSql .= "                 case                                                                               ";
        $sSql .= "                   when k02_tabrectipo = 2 or k02_tabrectipo = 5                                    ";
        $sSql .= "                     then                                                                           ";
        $sSql .= "                       case                                                                         ";
        $sSql .= "                         when termo.perc is not null                                                ";
        $sSql .= "                           then recibopaga.k00_valor * perc                                         ";
        $sSql .= "                           else recibopaga.k00_valor                                                ";
        $sSql .= "                       end                                                                          ";
        $sSql .= "                     else 0                                                                         ";
        $sSql .= "                 end as juro,                                                                       ";
        $sSql .= "                 case                                                                               ";
        $sSql .= "                   when k02_tabrectipo = 3                                                          ";
        $sSql .= "                     then                                                                           ";
        $sSql .= "                       case                                                                         ";
        $sSql .= "                         when termo.perc is not null                                                ";
        $sSql .= "                           then recibopaga.k00_valor * perc                                         ";
        $sSql .= "                           else recibopaga.k00_valor                                                ";
        $sSql .= "                       end                                                                          ";
        $sSql .= "                     else 0                                                                         ";
        $sSql .= "                 end as multa,                                                                      ";
        $sSql .= "                 case                                                                               ";
        $sSql .= "                   when k02_tabrectipo = 4                                                          ";
        $sSql .= "                     then                                                                           ";
        $sSql .= "                       case                                                                         ";
        $sSql .= "                         when termo.perc is not null                                                ";
        $sSql .= "                           then recibopaga.k00_valor * perc                                         ";
        $sSql .= "                           else recibopaga.k00_valor                                                ";
        $sSql .= "                       end                                                                          ";
        $sSql .= "                     else 0                                                                         ";
        $sSql .= "                 end as desconto,                                                                   ";
        $sSql .= "                 recibopaga.k00_valor as valor                                                      ";
        $sSql .= "            from ( select sum(recibopaga.k00_valor) as k00_valor,                                   ";
        $sSql .= "                          ( select sum(arrecad.k00_valor)                                           ";
        $sSql .= "                              from arrecad                                                          ";
        $sSql .= "                             where arrecad.k00_numpre = recibopaga.k00_numpre                       ";
        $sSql .= "                               and arrecad.k00_numpar = recibopaga.k00_numpar                       ";
        $sSql .= "                               and arrecad.k00_receit = recibopaga.k00_receit                       ";
        $sSql .= "                               and k00_hist not in (11403, 11404)) as k00_valor_historico,          ";
        $sSql .= "                          recibopaga.k00_receit,                                                    ";
        $sSql .= "                          recibopaga.k00_numpar,                                                    ";
        $sSql .= "                          recibopaga.k00_numpre,                                                    ";
        $sSql .= "                          recibopaga.k00_numnov                                                     ";
        $sSql .= "                     from recibopaga                                                                ";
        $sSql .= "                    where k00_numnov = {$iNumnov} and k00_hist not in (11403, 11404)                ";
        $sSql .= "                    group by recibopaga.k00_receit,                                                 ";
        $sSql .= "                             recibopaga.k00_numpar,                                                 ";
        $sSql .= "                             recibopaga.k00_numpre,                                                 ";
        $sSql .= "                             recibopaga.k00_numnov ) as recibopaga                                  ";
        $sSql .= "                 inner join tabrec on tabrec.k02_codigo = recibopaga.k00_receit                     ";
        $sSql .= "                  left join divida on divida.v01_numpre = recibopaga.k00_numpre                     ";
        $sSql .= "                                  and divida.v01_numpar = recibopaga.k00_numpar                     ";
        $sSql .= "                  left join ( select (select v07_numpre                                             ";
        $sSql .= "                                        from termo                                                  ";
        $sSql .= "                                  inner join recibopaga on recibopaga.k00_numpre = termo.v07_numpre ";
        $sSql .= "                                       where recibopaga.k00_numnov = {$iNumnov} limit 1 ),          ";
        $sSql .= "                                             v01_exerc as exerc,                                    ";
        $sSql .= "                                     (((sum(k00_valor) * 100) / (select sum(k00_valor)              ";
        $sSql .= "             from fc_parc_origem_completo((select k00_numpre                                        ";
        $sSql .= "                                             from recibopaga                                        ";
        $sSql .= "                                            where k00_numnov = {$iNumnov}                           ";
        $sSql .= "                                              and exists (select 1                                  ";
        $sSql .= "                                                            from termo                              ";
        $sSql .= "                                  where termo.v07_numpre = recibopaga.k00_numpre limit 1) limit 1)) ";
        $sSql .= "                  inner join termo on termo.v07_parcel = riparcel                                   ";
        $sSql .= "                  inner join termodiv on termodiv.parcel = riparcel                                 ";
        $sSql .= "                  inner join divida on divida.v01_coddiv = termodiv.coddiv                          ";
        $sSql .= "                                   and divida.v01_instit = {$iInstit}                               ";
        $sSql .= "                  inner join proced on proced.v03_codigo = v01_proced                               ";
        $sSql .= "                  inner join arreold on arreold.k00_numpre = divida.v01_numpre                      ";
        $sSql .= "                                    and arreold.k00_numpar = divida.v01_numpar                      ";
        $sSql .= "                                    and arreold.k00_receit = proced.v03_receit)) / 100) as perc     ";
        $sSql .= "                                from fc_parc_origem_completo((select k00_numpre                     ";
        $sSql .= "                                                                from recibopaga                     ";
        $sSql .= "                                                               where k00_numnov = {$iNumnov}        ";
        $sSql .= "                                                                 and exists(select 1                ";
        $sSql .= "                                                                              from termo            ";
        $sSql .= "            where termo.v07_numpre = recibopaga.k00_numpre limit 1) limit 1)) as origemparcelamento ";
        $sSql .= "                                     inner join termo on termo.v07_parcel = riparcel                ";
        $sSql .= "                                     inner join termodiv on termodiv.parcel = riparcel              ";
        $sSql .= "                                     inner join divida on divida.v01_coddiv = termodiv.coddiv       ";
        $sSql .= "                                                      and divida.v01_instit = {$iInstit}            ";
        $sSql .= "                                     inner join proced on proced.v03_codigo = v01_proced            ";
        $sSql .= "                                     inner join arreold on arreold.k00_numpre = divida.v01_numpre   ";
        $sSql .= "                                                       and arreold.k00_numpar = divida.v01_numpar   ";
        $sSql .= "                                                       and arreold.k00_receit = proced.v03_receit   ";
        $sSql .= "                               group by v07_numpre,                                                 ";
        $sSql .= "                                        v01_exerc,                                                  ";
        $sSql .= "                                        v07_vlrhis                                                  ";
        $sSql .= "                              union                                                                 ";
        $sSql .= "                              select (select v07_numpre                                             ";
        $sSql .= "                                        from termo                                                  ";
        $sSql .= "                                  inner join recibopaga on recibopaga.k00_numpre = termo.v07_numpre ";
        $sSql .= "                                       where recibopaga.k00_numnov = {$iNumnov} limit 1),           ";
        $sSql .= "                                     v01_exerc as exerc,                                            ";
        $sSql .= "                                     (((sum(k00_valor) * 100) / (select sum(k00_valor)              ";
        $sSql .= "                                                    from fc_parc_origem_completo((select k00_numpre ";
        $sSql .= "                                                                                    from recibopaga ";
        $sSql .= "                                                                      where k00_numnov = {$iNumnov} ";
        $sSql .= "                                                                               and exists (select 1 ";
        $sSql .= "                                                                                         from termo ";
        $sSql .= "                                  where termo.v07_numpre = recibopaga.k00_numpre limit 1) limit 1)) ";
        $sSql .= "                                                    inner join termo on termo.v07_parcel = riparcel ";
        $sSql .= "                                                  inner join termoini on termoini.parcel = riparcel ";
        $sSql .= "                                                    inner join inicialcert on inicial = v51_inicial ";
        $sSql .= "                                                    inner join certdiv on v14_certid = v51_certidao ";
        $sSql .= "                                                inner join divida on divida.v01_coddiv = v14_coddiv ";
        $sSql .= "                                                                 and divida.v01_instit = {$iInstit} ";
        $sSql .= "                                                inner join proced on proced.v03_codigo = v01_proced ";
        $sSql .= "                                       inner join arreold on arreold.k00_numpre = divida.v01_numpre ";
        $sSql .= "                                                         and arreold.k00_numpar = divida.v01_numpar ";
        $sSql .= "                                        and arreold.k00_receit = proced.v03_receit)) / 100) as perc ";
        $sSql .= "                                from fc_parc_origem_completo((select k00_numpre                     ";
        $sSql .= "                                                                from recibopaga                     ";
        $sSql .= "                                                               where k00_numnov = {$iNumnov}        ";
        $sSql .= "                                                                 and exists (select 1               ";
        $sSql .= "                                                                               from termo           ";
        $sSql .= "            where termo.v07_numpre = recibopaga.k00_numpre limit 1) limit 1)) as origemparcelamento ";
        $sSql .= "                                     inner join termo on termo.v07_parcel = riparcel                ";
        $sSql .= "                                     inner join termoini on termoini.parcel = riparcel              ";
        $sSql .= "                                     inner join inicialcert on inicial = v51_inicial                ";
        $sSql .= "                                     inner join certdiv on v14_certid = v51_certidao                ";
        $sSql .= "                                     inner join divida on divida.v01_coddiv = v14_coddiv            ";
        $sSql .= "                                                      and divida.v01_instit = {$iInstit}            ";
        $sSql .= "                                     inner join proced on proced.v03_codigo = v01_proced            ";
        $sSql .= "                                     inner join arreold on arreold.k00_numpre = divida.v01_numpre   ";
        $sSql .= "                                                       and arreold.k00_numpar = divida.v01_numpar   ";
        $sSql .= "                                                       and arreold.k00_receit = proced.v03_receit   ";
        $sSql .= "                               group by v07_numpre,                                                 ";
        $sSql .= "                                        v01_exerc,                                                  ";
        $sSql .= "                                   v07_vlrhis) as termo on termo.v07_numpre = recibopaga.k00_numpre ";
        $sSql .= "  where recibopaga.k00_numnov = {$iNumnov}) as x                                                    ";
        $sSql .= "  group by exerc                                                                                    ";
        $sSql .= "  order by exerc                                                                                    ";

        $rsSql = db_query($sSql);

        return \db_utils::getCollectionByRecord($rsSql);
    }
}
