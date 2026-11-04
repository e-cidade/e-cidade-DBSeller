<?php
/**
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

namespace ECidade\Tributario\Cadastro\Iptu;

use App\Domain\Tributario\Arrecadacao\Models\Recibobarpix;
use App\Domain\Tributario\Arrecadacao\Services\ArrecadacaoPixService;
use \ECidade\Tributario\Arrecadacao\CobrancaRegistrada\CobrancaRegistrada;
use \ECidade\Tributario\Arrecadacao\EmissaoGeral\EmissaoGeral;
use \ECidade\Tributario\Arrecadacao\EmissaoGeral\Registro\Repository as RegistroRepository;
use \ECidade\Tributario\Arrecadacao\EmissaoGeral\ParcelaUnicaRepository;
use \ECidade\Tributario\Arrecadacao\Convenio;
use ECidade\Tributario\Arrecadacao\Model\TaxaEspecifica as TaxaEspecificaModel;
use ECidade\Tributario\Caixa\Repository\RecibopagaRepository;
use ECidade\Tributario\Library\DataBase;
use Exception;

class EmissaoTXT
{

    public static function prepareQuery()
    {

        /**
         * $sqlnaogera = $sSqlNaoGera
         * @param $1 = $j34_setor   text
         * @param $2 = $j34_quadra  text
         */
        $sSqlNaoGera = ' select *                                                                        ';
        $sSqlNaoGera .= '   from iptunaogeracarne                                                         ';
        $sSqlNaoGera .= '        inner join iptunaogeracarnesetqua on j66_sequencial = j67_naogeracarne   ';
        $sSqlNaoGera .= '  where j67_setor  = $1                                                          ';
        $sSqlNaoGera .= '    and j67_quadra = $2                                                          ';
        \db_query("PREPARE sSqlNaoGera (text, text) AS {$sSqlNaoGera}");

        /**
         * $sqlnaogeracgm = $sSqlNaoGeraCgm
         * @param $1 = $z01_cgmpri integer
         */
        $sSqlNaoGeraCgm = ' select *                                                                     ';
        $sSqlNaoGeraCgm .= '   from iptunaogeracarne                                                      ';
        $sSqlNaoGeraCgm .= '        inner join iptunaogeracarnecgm on j66_sequencial = j68_naogeracarne   ';
        $sSqlNaoGeraCgm .= '  where j68_numcgm = $1                                                       ';
        \db_query("PREPARE sSqlNaoGeraCgm (integer) AS {$sSqlNaoGeraCgm}");

        /**
         * $sqlnaogeramatric = $sSqlNaoGeraMatric
         * @param $1 = $j23_matric  integer
         */
        $sSqlNaoGeraMatric = 'select *                                                                         ';
        $sSqlNaoGeraMatric .= '  from iptunaogeracarne                                                          ';
        $sSqlNaoGeraMatric .= '       inner join iptunaogeracarnematric on j66_sequencial = j131_naogeracarne   ';
        $sSqlNaoGeraMatric .= ' where j131_matric = $1                                                          ';
        \db_query("PREPARE sSqlNaoGeraMatric (integer) AS {$sSqlNaoGeraMatric}");


        /**
         * $sqlareaconstr = $sSqlAreaConstr
         * @param $1 = $j23_matric  integer
         */
        $sSqlAreaConstr = ' select sum(j39_area) as j39_area   ';
        $sSqlAreaConstr .= '   from iptuconstr                  ';
        $sSqlAreaConstr .= '  where j39_dtdemo is null          ';
        $sSqlAreaConstr .= '    and j39_matric = $1             ';
        \db_query("PREPARE sSqlAreaConstr (integer) AS {$sSqlAreaConstr}");

        /**
         * $resultcalc = $sSqlCalcIptuCale
         * @param $1 = $anousu      integer
         * @param $2 = $j23_matric  integer
         */
        $sSqlCalcIptuCale = 'select sum(j22_valor) as j22_valor   ';
        $sSqlCalcIptuCale .= '  from iptucale                      ';
        $sSqlCalcIptuCale .= ' where j22_anousu = $1               ';
        $sSqlCalcIptuCale .= '   and j22_matric = $2               ';
        \db_query("PREPARE sSqlCalcIptuCale (integer, integer) AS {$sSqlCalcIptuCale}");


        /**
         * $sqlsetfisc = $sSqlSetFisc
         * @param $1 = $j23_matric  integer
         */
        $sSqlSetFisc = ' select *                                              ';
        $sSqlSetFisc .= '   from lotesetorfiscal                                ';
        $sSqlSetFisc .= '        inner join iptubase on j01_idbql = j91_idbql   ';
        $sSqlSetFisc .= '  where j01_matric = $1                                ';
        \db_query("PREPARE sSqlSetFisc (integer) AS {$sSqlSetFisc}");

        /**
         * $sqljuros = $sSqlJuros
         * @param $1 = $anousu integer
         */
        $sSqlJuros = ' select distinct                                                                      ';
        $sSqlJuros .= '        tabrecjm.k02_codjm,                                                           ';
        $sSqlJuros .= '        k02_juros,                                                                    ';
        $sSqlJuros .= '        k140_faixa,                                                                   ';
        $sSqlJuros .= '        k140_multa                                                                    ';
        $sSqlJuros .= '   from cfiptu                                                                        ';
        $sSqlJuros .= '        inner join tabrec        on cfiptu.j18_rpredi           = tabrec.k02_codigo   ';
        $sSqlJuros .= '        inner join tabrecjm      on tabrecjm.k02_codjm          = tabrec.k02_codjm    ';
        $sSqlJuros .= '        inner join tabrecjmmulta on tabrecjmmulta.k140_tabrecjm = tabrec.k02_codjm    ';
        $sSqlJuros .= '  where j18_anousu = $1                                                               ';
        $sSqlJuros .= '  order by k140_multa                                                                 ';
        $sSqlJuros .= '  limit 1                                                                             ';
        \db_query("PREPARE sSqlJuros (integer) AS {$sSqlJuros}");

        /**
         * $sqlisen = $sSqlIsen
         * @param $1 = $anousu      integer
         * @param $2 = $j23_matric  integer
         */
        $sSqlIsen = ' select j46_codigo, j45_descr,                           ';
        $sSqlIsen .= '        j46_dtinc , j46_tipo                             ';
        $sSqlIsen .= '   from iptuisen                                         ';
        $sSqlIsen .= '        inner join isenexe  on j46_codigo = j47_codigo   ';
        $sSqlIsen .= '                           and j47_anousu = $1           ';
        $sSqlIsen .= '        inner join tipoisen on j46_tipo   = j45_tipo     ';
        $sSqlIsen .= '  where j46_matric = $2                                  ';
        \db_query("PREPARE sSqlIsen (integer, integer) AS {$sSqlIsen}");


        /**
         * $sql = $sSqlArretipo
         * @param $1 = $k00_tipo  integer
         */
        $sSqlArreTipoTxBanco = ' select k00_tipo, k00_codbco, k00_codage, k00_descr,   ';
        $sSqlArreTipoTxBanco .= '        k00_hist1, k00_hist2, k00_hist3,  k00_hist4,   ';
        $sSqlArreTipoTxBanco .= '        k00_hist5, k00_hist6, k00_hist7, k00_hist8,    ';
        $sSqlArreTipoTxBanco .= '        k03_tipo, k00_txban as tx_banc                 ';
        $sSqlArreTipoTxBanco .= '   from arretipo                                       ';
        $sSqlArreTipoTxBanco .= '  where k00_tipo = $1                                  ';
        \db_query("PREPARE sSqlArreTipoTxBanco (integer) AS {$sSqlArreTipoTxBanco}");

        /**
         * $sqlcalc = $sSqlCalcQntImpTaxas
         * @param $1 = $anousu      integer
         * @param $2 = $j23_matric  integer
         */
        $sSqlCalcQntImpTaxas = ' select sum(j21_valor) as total_j21_valor,      ';
        $sSqlCalcQntImpTaxas .= '        count(*)       as quant_imposto_taxas   ';
        $sSqlCalcQntImpTaxas .= '   from iptucalv                                ';
        $sSqlCalcQntImpTaxas .= '  where j21_anousu = $1                         ';
        $sSqlCalcQntImpTaxas .= '    and j21_matric = $2                         ';
        \db_query("PREPARE sSqlCalcQntImpTaxas (integer, integer) AS {$sSqlCalcQntImpTaxas}");

        /**
         * $sqlcalc = $sSqlCalcQntTaxas
         * @param $1 = $anousu      integer
         * @param $2 = $j23_matric  integer
         */
        $sSqlCalcQntTaxas = ' select sum(j21_valor) as total_j21_valor,   ';
        $sSqlCalcQntTaxas .= '        count(*)       as quant_taxas        ';
        $sSqlCalcQntTaxas .= '   from iptucalv                             ';
        $sSqlCalcQntTaxas .= '  where j21_anousu = $1                      ';
        $sSqlCalcQntTaxas .= '    and j21_matric = $2                      ';
        \db_query("PREPARE sSqlCalcQntTaxas (integer, integer) AS {$sSqlCalcQntTaxas}");

        /**
         * $sqlcalc = $sSqlCalcQntTaxasReceita
         * @param $1 = $anousu      integer
         * @param $2 = $j23_matric  integer
         */
        $sSqlCalcQntTaxasReceita = ' select sum(j21_valor) as total_j21_valor,       ';
        $sSqlCalcQntTaxasReceita .= '        count(*)       as quant_taxas            ';
        $sSqlCalcQntTaxasReceita .= '   from iptucalv                                 ';
        $sSqlCalcQntTaxasReceita .= '  where j21_anousu = $1                          ';
        $sSqlCalcQntTaxasReceita .= '    and j21_matric = $2                          ';
        $sSqlCalcQntTaxasReceita .= '    and j21_receit in (select j19_receit         ';
        $sSqlCalcQntTaxasReceita .= '                         from iptutaxa           ';
        $sSqlCalcQntTaxasReceita .= '                        where j19_anousu = $1)   ';
        \db_query("PREPARE sSqlCalcQntTaxasReceita (integer, integer) AS {$sSqlCalcQntTaxasReceita}");

        $sSqlCalcValorIptu = ' select sum(j21_valor) as total_valor_iptu        ';
        $sSqlCalcValorIptu .= '   from iptucalv                                 ';
        $sSqlCalcValorIptu .= '  where j21_anousu = $1                          ';
        $sSqlCalcValorIptu .= '    and j21_matric = $2                          ';
        \db_query("PREPARE sSqlCalcValorIptu (integer, integer) AS {$sSqlCalcValorIptu}");

        /**
         * $sqliptu = $sSqlIptu
         * @param $1 = $anousu     integer
         * @param $2 = $anoant     integer
         * @param $3 = $j23_matric integer
         */
        $sSqlIptu = ' select fc_calcula(k00_numpre, k00_numpar, 0, current_date, current_date, $1)           ';
        $sSqlIptu .= '   from ( select distinct                                                               ';
        $sSqlIptu .= '                 arrecad.k00_numpre, arrecad.k00_numpar                                 ';
        $sSqlIptu .= '            from iptunump                                                               ';
        $sSqlIptu .= '                 inner join arrematric on iptunump.j20_numpre = arrematric.k00_numpre   ';
        $sSqlIptu .= '                 inner join arrecad    on iptunump.j20_numpre = arrecad.k00_numpre      ';
        $sSqlIptu .= '           where j20_anousu = $2                                                        ';
        $sSqlIptu .= '             and k00_matric = $3 ) as x                                                 ';
        \db_query("PREPARE sSqlIptu (integer, integer, integer) AS {$sSqlIptu}");

        /**
         * $sqlvalorm2 = $sSqlValorM2
         * @param $1 = $j01_idbql   integer
         * @param $2 = $anousu      integer
         * @param $3 = $j23_matric  integer
         */
        $sSqlValorM2 = ' select j37_face, j81_valorterreno as j37_valor, j37_outros,   ';
        $sSqlValorM2 .= '        case when j36_testle = 0                               ';
        $sSqlValorM2 .= '          then j36_testad                                      ';
        $sSqlValorM2 .= '          else j36_testle                                      ';
        $sSqlValorM2 .= '        end as j36_testle,                                     ';
        $sSqlValorM2 .= '        j81_valorconstr as j37_vlcons                          ';
        $sSqlValorM2 .= '   from iptuconstr                                             ';
        $sSqlValorM2 .= '        inner join testada   on j36_face   = j39_codigo        ';
        $sSqlValorM2 .= '                            and j36_idbql  = $1                ';
        $sSqlValorM2 .= '        inner join face      on j37_face   = j36_face          ';
        $sSqlValorM2 .= '        left  join facevalor on j81_face   = j37_face          ';
        $sSqlValorM2 .= '                            and j81_anousu = $2                ';
        $sSqlValorM2 .= '        inner join iptubase  on j01_matric = j39_matric        ';
        $sSqlValorM2 .= '  where j39_matric = $3                                        ';
        $sSqlValorM2 .= '    and j39_dtdemo is null                                     ';
        $sSqlValorM2 .= '    and j01_baixa  is null                                     ';
        $sSqlValorM2 .= '  limit 1                                                      ';
        \db_query("PREPARE sSqlValorM2 (integer, integer, integer) AS {$sSqlValorM2}");

        /**
         * $sqlpagas = $sSqlPagas
         * @param $1 = $j20_numpre  integer
         * @param $2 = $parcpaga    integer
         */
        $sSqlPagas = ' select max(dtpago)    as dtpago,                                                               ';
        $sSqlPagas .= '        sum(k00_valor) as valorpago                                                            ';
        $sSqlPagas .= '   from ( select distinct                                                                      ';
        $sSqlPagas .= '                 j20_numpre, j20_matric, arrepaga.k00_numpar,                                  ';
        $sSqlPagas .= '                 arrepaga.k00_receit   , arrepaga.k00_valor ,                                  ';
        $sSqlPagas .= '                 case when disbanco2.dtpago is null                                            ';
        $sSqlPagas .= '                   then case when disbanco1.dtpago is null                                     ';
        $sSqlPagas .= '                          then arrepaga.k00_dtpaga                                             ';
        $sSqlPagas .= '                          else disbanco1.dtpago                                                ';
        $sSqlPagas .= '                        end                                                                    ';
        $sSqlPagas .= '                   else disbanco2.dtpago                                                       ';
        $sSqlPagas .= '                 end as dtpago                                                                 ';
        $sSqlPagas .= '            from iptunump                                                                      ';
        $sSqlPagas .= '                 inner join arrepaga           on arrepaga.k00_numpre   = j20_numpre           ';
        $sSqlPagas .= '                 left  join disbanco disbanco1 on disbanco1.k00_numpre  = arrepaga.k00_numpre  ';
        $sSqlPagas .= '                                              and disbanco1.k00_numpar  = arrepaga.k00_numpar  ';
        $sSqlPagas .= '                 left  join recibopaga         on recibopaga.k00_numpre = arrepaga.k00_numpre  ';
        $sSqlPagas .= '                                              and recibopaga.k00_numpar = recibopaga.k00_numpar';
        $sSqlPagas .= '                                              and recibopaga.k00_receit = arrepaga.k00_receit  ';
        $sSqlPagas .= '                 left  join disbanco disbanco2 on disbanco2.k00_numpre  = recibopaga.k00_numnov';
        $sSqlPagas .= '           where j20_numpre = $1                                                               ';
        $sSqlPagas .= '             and arrepaga.k00_numpar = $2                                                      ';
        $sSqlPagas .= '           order by j20_matric                                                                 ';
        $sSqlPagas .= '        ) as x                                                                                 ';
        \db_query("PREPARE sSqlPagas (integer, integer) AS {$sSqlPagas}");

        /**
         * $sqlfin2 = $sSqlFin02
         * @param $1 = $j20_numpre  integer
         */
        $sSqlFin02 = 'select k00_tipo, k00_dtvenc,         ';
        $sSqlFin02 .= '       k00_numpre, k00_numpar,       ';
        $sSqlFin02 .= '       sum(k00_valor) as k00_valor   ';
        $sSqlFin02 .= '  from arrecad                       ';
        $sSqlFin02 .= ' where k00_numpre = $1               ';
        $sSqlFin02 .= ' group by k00_dtvenc, k00_numpre,    ';
        $sSqlFin02 .= '          k00_numpar, k00_tipo       ';
        $sSqlFin02 .= ' order by k00_numpre, k00_numpar     ';
        \db_query("PREPARE sSqlFin02 (integer) AS {$sSqlFin02}");

        /**
         * $sqliptuant = $sSqlIptuAnt
         * @param $1 = $j23_matric  integer
         */
        $sSqlIptuAnt = ' select *                 ';
        $sSqlIptuAnt .= '   from iptuant           ';
        $sSqlIptuAnt .= '  where j40_matric = $1   ';
        \db_query("PREPARE sSqlIptuAnt (integer) AS {$sSqlIptuAnt}");

        /**
         * $result_iptuhist = $sSqlValorIptuHist
         * @param $1 = $anousu      integer
         * @param $2 = $j23_matric  integer
         */
        $sSqlValorIptuHist = ' select sum(j21_valor) as j21_valor   ';
        $sSqlValorIptuHist .= '   from iptucalv                      ';
        $sSqlValorIptuHist .= '  where j21_anousu = $1               ';
        $sSqlValorIptuHist .= '    and j21_matric = $2               ';
        $sSqlValorIptuHist .= '    and j21_codhis in (1,7)           ';
        \db_query("PREPARE sSqlValorIptuHist (integer, integer) AS {$sSqlValorIptuHist}");

        /**
         * $result_iptuhist = $sSqlValorTaxaHist
         * @param $1 = $anousu      integer
         * @param $2 = $j23_matric  integer
         */
        $sSqlValorTaxaHist = ' select sum(j21_valor) as j21_valor    ';
        $sSqlValorTaxaHist .= '   from iptucalv                       ';
        $sSqlValorTaxaHist .= '  where j21_anousu = $1                ';
        $sSqlValorTaxaHist .= '    and j21_matric = $2                ';
        $sSqlValorTaxaHist .= '    and j21_codhis in (2,8)            ';
        \db_query("PREPARE sSqlValorTaxaHist (integer, integer) AS {$sSqlValorTaxaHist}");

        /**
         * $result_iptuhist = $sSqlValorIptuHistReceita
         * @param $1 = $k00_numpre  integer
         * @param $2 = $k00_numpar  integer
         */
        $sSqlValorIptuHistReceita = ' select sum(k00_valor) as k00_valor    ';
        $sSqlValorIptuHistReceita .= '   from arrecad                        ';
        $sSqlValorIptuHistReceita .= '  where k00_numpre = $1                ';
        $sSqlValorIptuHistReceita .= '    and k00_numpar = $2                ';
        $sSqlValorIptuHistReceita .= '    and k00_receit in (1,2)            ';
        \db_query("PREPARE sSqlValorIptuHistReceita (integer, integer) AS {$sSqlValorIptuHistReceita}");

        /**
         * $result_iptuhist = $sSqlValorTaxaHistReceita
         * @param $1 = $k00_numpre  integer
         * @param $2 = $k00_numpar  integer
         */
        $sSqlValorTaxaHistReceita = ' select sum(k00_valor) as k00_valor    ';
        $sSqlValorTaxaHistReceita .= '  from arrecad                         ';
        $sSqlValorTaxaHistReceita .= ' where k00_numpre = $1                 ';
        $sSqlValorTaxaHistReceita .= '   and k00_numpar = $2                 ';
        $sSqlValorTaxaHistReceita .= '   and k00_receit in (10)              ';
        \db_query("PREPARE sSqlValorTaxaHistReceita (integer, integer) AS {$sSqlValorTaxaHistReceita}");

        /**
         * $sql = $sSqlArretipo
         * @param $1 = $k00_tipo  integer
         */
        $sSqlArretipo = ' select k00_tipo, k00_codbco, k00_codage, k00_descr,  ';
        $sSqlArretipo .= '        k00_hist1, k00_hist2, k00_hist3, k00_hist4,   ';
        $sSqlArretipo .= '        k00_hist5, k00_hist6, k00_hist7, k00_hist8,   ';
        $sSqlArretipo .= '        k03_tipo                                      ';
        $sSqlArretipo .= ' from arretipo                                        ';
        $sSqlArretipo .= ' where k00_tipo = $1                                  ';
        \db_query("PREPARE sSqlArretipo (integer) AS {$sSqlArretipo}");

        /**
         * $sqlpagas = $sSqlParcPagas
         * @param $1 = $j20_numpre  integer
         */
        $sSqlParcPagas = ' select sum(k00_valor) as valorpago                                         ';
        $sSqlParcPagas .= '   from ( select distinct                                                   ';
        $sSqlParcPagas .= '                 j20_numpre,                                                ';
        $sSqlParcPagas .= '                 j20_matric,                                                ';
        $sSqlParcPagas .= '                 arrepaga.k00_numpar,                                       ';
        $sSqlParcPagas .= '                 arrepaga.k00_receit,                                       ';
        $sSqlParcPagas .= '                 arrepaga.k00_valor                                         ';
        $sSqlParcPagas .= '            from iptunump                                                   ';
        $sSqlParcPagas .= '                 inner join arrepaga on arrepaga.k00_numpre = j20_numpre    ';
        $sSqlParcPagas .= '           where j20_numpre = $1) as x                                      ';
        \db_query("PREPARE sSqlParcPagas (integer) AS {$sSqlParcPagas}");

        /**
         * $sqltestada = $sSqlTestada
         * @param $1 = $j23_matric  integer
         */
        $sSqlTestada = ' select j36_testad                                              ';
        $sSqlTestada .= '   from iptubase                                                ';
        $sSqlTestada .= '        inner join testada on j36_idbql = j01_idbql             ';
        $sSqlTestada .= '        inner join testpri on j49_idbql = testada.j36_idbql     ';
        $sSqlTestada .= '  where j01_matric = $1                                         ';
        \db_query("PREPARE sSqlTestada (integer) AS {$sSqlTestada}");


        $sqlprinc = "select * from ( ";
        $sqlprinc .= "select distinct ";
        $sqlprinc .= "       j23_matric, ";
        $sqlprinc .= "       j23_vlrter, ";
        $sqlprinc .= "       j23_aliq, ";
        $sqlprinc .= "       j23_areafr, ";
        $sqlprinc .= "       j86_iptucadzonaentrega, ";
        $sqlprinc .= "       z01_nome, ";
        $sqlprinc .= "       z01_numcgm, ";
        $sqlprinc .= "       j20_numpre, ";
        $sqlprinc .= "       j01_idbql,";
        $sqlprinc .= "       j23_arealo,";
        $sqlprinc .= "       j23_m2terr,";
        $sqlprinc .= "       j40_refant,";
        $sqlprinc .= "       j34_setor,";
        $sqlprinc .= "       j34_quadra,";
        $sqlprinc .= "       j34_lote,";
        $sqlprinc .= "       substr(fc_iptuender,001,40) as j23_ender, ";
        $sqlprinc .= "       substr(fc_iptuender,042,10) as j23_numero, ";
        $sqlprinc .= "       substr(substr(fc_iptuender,053,20), 0, 21) as j23_compl, ";
        $sqlprinc .= "       substr(fc_iptuender,074,40) as j23_bairro, ";
        $sqlprinc .= "       substr(fc_iptuender,115,40) as j23_munic, ";
        $sqlprinc .= "       substr(fc_iptuender,156,02) as j23_uf, ";
        $sqlprinc .= "       substr(fc_iptuender,159,08) as j23_cep, ";
        $sqlprinc .= "       substr(fc_iptuender,168,20) as j23_cxpostal, ";
        $sqlprinc .= "       substr(fc_iptuender,189,40) as j23_dest from  ( ";
        $sqlprinc .= "       select j23_matric, ";
        $sqlprinc .= "              j23_vlrter, ";
        $sqlprinc .= "              j23_aliq, ";
        $sqlprinc .= "              j23_areafr, ";
        $sqlprinc .= "              z01_numcgm, ";
        $sqlprinc .= "              j20_numpre, ";
        $sqlprinc .= "              j86_iptucadzonaentrega, ";
        $sqlprinc .= "              (select z01_nome";
        $sqlprinc .= "                 from proprietario_nome where j01_matric = j23_matric limit 1) as z01_nome, ";
        $sqlprinc .= "              j01_idbql, ";
        $sqlprinc .= "              j23_m2terr,";
        $sqlprinc .= "              j23_arealo, ";
        $sqlprinc .= "              j40_refant,";
        $sqlprinc .= "              j34_setor,";
        $sqlprinc .= "              j34_quadra,";
        $sqlprinc .= "              j34_lote,";
        $sqlprinc .= "              fc_iptuender(j23_matric) ";
        $sqlprinc .= "         from iptucalc  ";
        $sqlprinc .= "              inner join iptubase       on iptubase.j01_matric = iptucalc.j23_matric ";
        $sqlprinc .= "              inner join iptunump       on iptunump.j20_matric = iptubase.j01_matric ";
        $sqlprinc .= "                                       and iptunump.j20_anousu = $1 ";
        $sqlprinc .= "              inner join lote           on lote.j34_idbql = iptubase.j01_idbql  ";
        $sqlprinc .= "              inner join cgm            on cgm.z01_numcgm = iptubase.j01_numcgm  ";
        $sqlprinc .= "              left  join iptumatzonaentrega a on a.j86_matric = iptubase.j01_matric ";
        $sqlprinc .= "              left  join imobil             on imobil.j44_matric = iptubase.j01_matric ";
        $sqlprinc .= "              left  join loteloteam         on loteloteam.j34_idbql = lote.j34_idbql ";
        $sqlprinc .= "              left  join iptuant            on iptuant.j40_matric   = iptubase.j01_matric ";
        $sqlprinc .= "        where iptucalc.j23_anousu = $2 and j01_matric = $3 ) as x ";
        $sqlprinc .= "        ) as y ";
        \db_query("PREPARE sSqlDadosMatricula(integer, integer, integer) AS {$sqlprinc}");

        $sSqlDadosRecibo = "select recibopaga.k00_numnov,
                               recibopaga.k00_dtpaga,
                               sum(recibopaga.k00_valor) as valor,
                               array_agg(recibopaga.k00_numpre) as numpres,
                               recibocodbar.k00_codbar,
                               recibocodbar.k00_linhadigitavel,
                               recibocodbar.k00_nossonumero
                          from recibopaga
                               inner join recibocodbar on recibopaga.k00_numnov = recibocodbar.k00_numpre
                         where recibopaga.k00_numnov = $1
                         group by recibopaga.k00_numnov,
                                  recibopaga.k00_dtpaga,
                                  recibocodbar.k00_codbar,
                                  recibocodbar.k00_linhadigitavel,
                                  recibocodbar.k00_nossonumero";
        \db_query("PREPARE sSqlDadosRecibo(integer) AS {$sSqlDadosRecibo}");
    }

    const TIPO_EMISSAO_TXT = "txt";
    const TIPO_EMISSAO_TXTBSJ = "txtbsj";

    /**
     * @var \SplFileObject
     */
    private $oArquivoDados;

    /**
     * @var \SplFileObject
     */
    private $oArquivoLayout;

    /**
     * @var array
     */
    private $aParcelasUnicas;

    /**
     * @var integer
     */
    private $iAno;

    /**
     * @var string
     */
    private $sTipoGeracao;

    /**
     * @var float
     */
    private $nTaxaBancaria;

    /**
     * @var boolean
     */
    private $lConvenioCobranca;

    /**
     * @var integer
     */
    private $iParcelaMaxima;

    /**
     * @var string
     */
    private $sQuebraLinha;

    /**
     * @var string
     */
    private $sTabelaTemporariaTaxas;

    /**
     * @var string
     */
    private $sTabelaTemporariaTaxas2;

    /**
     * @var string
     */
    private $sListaReceitas;

    /**
     * @var integer
     */
    private $iReceitaMin;

    /**
     * @var float
     */
    private $nValorJuros;

    /**
     * @var float
     */
    private $nValorJurosFaixa;

    /**
     * @var boolean
     */
    private $lMensagemAnosAnteriores;

    /**
     * @var resource
     */
    private $rsReceitasArrecadGeral;

    /**
     * @var integer
     */
    private $iTotalLinhas20;

    /**
     * @var float
     */
    private $nTotalParcelas20;

    /**
     * @var boolean
     */
    private $lOpcaoVencimento;

    /**
     * @var Convenio
     */
    private $oConvenio;

    /**
     * @var \Instituicao
     */
    private $oPrefeitura;

    /**
     * @var EmissaoGeral
     */
    private $oEmissao;

    /**
     * @var bool
     */
    private $buscarReceitaTaxaExpediente = true;

    /**
     * @var int|null
     */
    private $receitaTaxaExpediente;

    /**
     * @var boolean
     */
    private $gerarPix;

    /**
     * @var integer
     */
    private $maximoUnicasPorLinha;

    /**
     * Imprime as informações da matrícula no arquivo
     *
     * @param integer $iMatricula
     * @param array $aRecibos
     * @param integer $iQuantidadeRegistros
     * @param boolean $lGerarLayout
     * @throws \Exception
     */
    private function gerarLinhaRegistro($iMatricula, $aRecibos, $iQuantidadeRegistros, $lGerarLayout)
    {
        $cliptubase = new \cl_iptubase;

        /**
         * @todo lógica para não gerar o registro caso seja TXTBSJ e não tenha parcela única
         */
        if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXTBSJ && empty($aRecibos['unicas'])) {
            return;
        }

        $tipoDebito = $this->getTipoDebito($this->iAno);

        /**
         * @todo verificar isso aqui
         */
        global $contadorgeral;
        global $contador;

        $iTamCodArrecadao = 11;
        $iTamNossoNumeroVersao2 = 17;

        $nTotalBomPagador = 0;

        $aListaNossoNumeroUnica = array();
        $aListaNossoNumero = array();

        $oRegraEmissao = new \regraEmissao(
            $tipoDebito,
            10,
            \db_getsession('DB_instit'),
            date("Y-m-d", \db_getsession("DB_datausu")),
            \db_getsession('DB_ip')
        );

        $aQrCodesPix = [];

        /**
         * Busca os dados da matrícula
         */
        $sSqlDadosMatricula = "EXECUTE sSqlDadosMatricula({$this->iAno}, {$this->iAno}, {$iMatricula})";
        $rsDadosMatricula = \db_query($sSqlDadosMatricula);

        if (empty($rsDadosMatricula) || pg_num_rows($rsDadosMatricula) == 0) {
            throw new \Exception("Erro ao buscar os dados da Matrícula: {$iMatricula}");
        }

        $oDadosMatricula = \db_utils::fieldsMemory($rsDadosMatricula, 0);
        extract((array)$oDadosMatricula);

        /**
         * Busca os dados do proprietário da matrícula
         */
        $resultmat = $cliptubase->proprietario_record($cliptubase->proprietario_query($oDadosMatricula->j23_matric));
        if (pg_num_rows($resultmat) == 0) {
            return;
        }

        extract((array)\db_utils::fieldsMemory($resultmat, 0));

        $sSqlAreaConstrExec = "EXECUTE sSqlAreaConstr({$oDadosMatricula->j23_matric})";
        $resultsqlareaconstr = \db_query($sSqlAreaConstrExec) or die($sSqlAreaConstrExec);

        $j39_area = 0;
        if (!empty($resultsqlareaconstr) && pg_num_rows($resultsqlareaconstr) > 0) {
            $j39_area = \db_utils::fieldsMemory($resultsqlareaconstr, 0)->j39_area;
        }

        $sSqlCalcIptuCaleExec = "EXECUTE sSqlCalcIptuCale($this->iAno, {$oDadosMatricula->j23_matric})";
        $resultcalc = \db_query($sSqlCalcIptuCaleExec) or die($sSqlCalcIptuCaleExec);

        $j22_valor = 0;
        if (!empty($resultcalc) && pg_num_rows($resultcalc) > 0) {
            $j22_valor = \db_utils::fieldsMemory($resultcalc, 0)->j22_valor;
        }

        $sSqlSetFiscExec = "EXECUTE sSqlSetFisc({$oDadosMatricula->j23_matric})";
        $resultsetfisc = \db_query($sSqlSetFiscExec) or die($sSqlSetFiscExec);

        $j91_codigo = 0;
        if (!empty($resultsetfisc) && pg_num_rows($resultsetfisc) > 0) {
            $j91_codigo = \db_utils::fieldsMemory($resultsetfisc, 0)->j91_codigo;
        }

        $sSqlIsenExec = "EXECUTE sSqlIsen({$this->iAno}, {$oDadosMatricula->j23_matric})";
        $resultisen = \db_query($sSqlIsenExec) or die($sSqlIsenExec);

        if (pg_num_rows($resultisen) == 0) {
            $j46_tipo = 0;
            $j46_codigo = 0;
            $j45_descr = "";
            $j46_dtinc = "";
        } else {
            extract((array)\db_utils::fieldsMemory($resultisen, 0));
        }

        $sSqlCalcQntImpTaxasExec = "EXECUTE sSqlCalcQntImpTaxas($this->iAno, {$oDadosMatricula->j23_matric})";
        $resultcalc = \db_query($sSqlCalcQntImpTaxasExec) or die($sSqlCalcQntImpTaxasExec);

        if (pg_num_rows($resultcalc) == 0) {
            $total_j21_valor = 0;
            $quant_imposto_taxas = 0;
        } else {
            extract((array)\db_utils::fieldsMemory($resultcalc, 0, true));
        }

        if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
            $this->oArquivoDados->fwrite(str_pad($iQuantidadeRegistros, 10));
            $this->oArquivoDados->fwrite(($j01_tipoimp == "Predial" ? "2" : "1"));
            $this->oArquivoDados->fwrite(str_pad($j01_tipoimp, 11));
            $this->oArquivoDados->fwrite(str_pad($oDadosMatricula->j23_matric, 10));
            $this->oArquivoDados->fwrite(str_pad($this->iAno, 4));
            $this->oArquivoDados->fwrite(str_pad(0, 10));
            $this->oArquivoDados->fwrite(str_pad($oDadosMatricula->j86_iptucadzonaentrega, 5));
            $this->oArquivoDados->fwrite(str_pad($j34_zona, 5));
            $this->oArquivoDados->fwrite(str_pad($j91_codigo, 5));
            $this->oArquivoDados->fwrite(str_pad($oDadosMatricula->j34_setor, 4));
            $this->oArquivoDados->fwrite(str_pad($oDadosMatricula->j34_quadra, 4));
            $this->oArquivoDados->fwrite(str_pad($oDadosMatricula->j34_lote, 4));
        }

        if ($lGerarLayout) {
            $this->oArquivoLayout->fwrite(static::dbContador("CONTADOR", "CONTADOR", $contador, 10));
            $this->oArquivoLayout->fwrite(static::dbContador(
                "ESPECIE",
                "CODIGO DO TIPO DO IMOVEL - 1 = TERRITORIAL E 2 = PREDIAL",
                $contador,
                1
            ));
            $this->oArquivoLayout->fwrite(static::dbContador(
                "TIPOIMOVEL",
                "EXPRESSAO DO TIPO DO IMOVEL - TERRITORIAL OU PREDIAL",
                $contador,
                11
            ));
            $this->oArquivoLayout->fwrite(static::dbContador("MATRICULA", "MATRICULA", $contador, 10));
            $this->oArquivoLayout->fwrite(static::dbContador("EXERCICIO", "EXERCÍCIO DO CALCULO", $contador, 4));
            $this->oArquivoLayout->fwrite(static::dbContador("NOTIFICACAO", "NOTIFICACAO", $contador, 10));
            $this->oArquivoLayout->fwrite(static::dbContador("ZONAENTREGA", "ZONA DE ENTREGA", $contador, 5));
            $this->oArquivoLayout->fwrite(static::dbContador(
                "ZONAFISCALLOTE",
                "ZONA FISCAL DA TABELA LOTE",
                $contador,
                5
            ));
            $this->oArquivoLayout->fwrite(static::dbContador("SETORFISCAL", "SETOR FISCAL", $contador, 5));
            $this->oArquivoLayout->fwrite(static::dbContador(
                "SETORCARTO",
                "SETOR CARTOGRAFICO (DO SETOR/QUADRA/LOTE)",
                $contador,
                4
            ));
            $this->oArquivoLayout->fwrite(static::dbContador("QUADRACARTO", "QUADRA CARTOGRAFICA", $contador, 4));
            $this->oArquivoLayout->fwrite(static::dbContador("LOTECARTO", "LOTE CARTOGRAFICA", $contador, 4));
        }

        if ($j40_refant == "") {
            $j40_refant = "....";
        }

        $sqlsub = split('\.', $j40_refant);

        if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
            if (isset($sqlsub)) {
                if (sizeof($sqlsub) >= 5) {
                    $this->oArquivoDados->fwrite(substr(str_pad($sqlsub[4], 4), 0, 4));
                } else {
                    $this->oArquivoDados->fwrite("    ");
                }
            } else {
                $this->oArquivoDados->fwrite("    ");
            }
        }

        if ($lGerarLayout) {
            $this->oArquivoLayout->fwrite(static::dbContador("SUBLOTELOC", "SUBLOTE", $contador, 4));
        }

        if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXTBSJ) {
            $linha10 = "BSJR10";
        }

        if ($z01_cgmpri <> $z01_numcgm) {
            if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                $this->oArquivoDados->fwrite(substr(str_pad($z01_nome, 40), 0, 40));
                $this->oArquivoDados->fwrite(substr(str_pad($z01_nome, 40), 0, 40));
            } elseif ($this->sTipoGeracao == static::TIPO_EMISSAO_TXTBSJ) {
                $linha10 .= substr(str_pad($z01_nome, 40, " ", STR_PAD_RIGHT), 0, 40);
            }

            if ($lGerarLayout) {
                if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                    $this->oArquivoLayout->fwrite(static::dbContador(
                        "NOME",
                        "NOME A SER IMPRESSO NO CARNE",
                        $contador,
                        40
                    ));
                    $this->oArquivoLayout->fwrite(static::dbContador(
                        "PROMITENTE",
                        "PROMITENTE COMPRADOR POR CONTRATO",
                        $contador,
                        40
                    ));
                }
            }
        } else {
            if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                $this->oArquivoDados->fwrite(substr(str_pad($proprietario, 40), 0, 40));
                $this->oArquivoDados->fwrite(str_pad(' ', 40));
            } elseif ($this->sTipoGeracao == static::TIPO_EMISSAO_TXTBSJ) {
                $linha10 .= substr(str_pad($proprietario, 40, " ", STR_PAD_RIGHT), 0, 40);
            }

            if ($lGerarLayout) {
                if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                    $this->oArquivoLayout->fwrite(static::dbContador(
                        "NOME",
                        "NOME A SER IMPRESSO NO CARNE",
                        $contador,
                        40
                    ));
                    $this->oArquivoLayout->fwrite(static::dbContador(
                        "PROMITENTE",
                        "PROMITENTE COMPRADOR POR CONTRATO",
                        $contador,
                        40
                    ));
                }
            }
        }

        if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
            $this->oArquivoDados->fwrite(substr(str_pad($proprietario, 40), 0, 40));

            $this->oArquivoDados->fwrite(substr(str_pad($z01_ender, 40), 0, 40));
            $this->oArquivoDados->fwrite(str_pad($z01_numero, 10));
            $this->oArquivoDados->fwrite(substr(str_pad($z01_compl, 20), 0, 20));
            $this->oArquivoDados->fwrite(substr(str_pad($z01_munic, 20), 0, 20));
            $this->oArquivoDados->fwrite(str_pad($z01_cep, 8));
            $this->oArquivoDados->fwrite(str_pad($z01_uf, 2));
            $this->oArquivoDados->fwrite(str_pad($z01_cgccpf, 20));

            $this->oArquivoDados->fwrite(substr(str_pad($codpri, 6, "0", STR_PAD_LEFT), 0, 6));
            $this->oArquivoDados->fwrite(str_pad($tipopri, 20));
            $this->oArquivoDados->fwrite(str_pad($nomepri, 50));
            $this->oArquivoDados->fwrite(str_pad($j39_numero, 10));

            $j39_compl = substr($j39_compl, 0, 20);
            $this->oArquivoDados->fwrite(str_pad($j39_compl, 20));
            $this->oArquivoDados->fwrite(str_pad($j13_descr, 40));

            if (trim($j23_cxpostal) != "" && $j23_cxpostal > 0) {
                $j23_ender = "CAIXA POSTAL: $j23_cxpostal";
            }

            $this->oArquivoDados->fwrite(str_pad((trim($j23_ender) == "" ? $nomepri : $j23_ender), 50));
            $this->oArquivoDados->fwrite(str_pad((trim($j23_ender) == "" ? $j39_numero : $j23_numero), 10));
            $this->oArquivoDados->fwrite(str_pad((trim($j23_ender) == "" ? $j39_compl : $j23_compl), 20));
            $this->oArquivoDados->fwrite(str_pad($j23_bairro, 40));
            $this->oArquivoDados->fwrite(str_pad($j23_munic, 40));
            $this->oArquivoDados->fwrite(str_pad($j23_uf, 2));
            $this->oArquivoDados->fwrite(str_pad($j23_cep, 10));
            $this->oArquivoDados->fwrite(str_pad($j23_cxpostal, 10));
            $this->oArquivoDados->fwrite(str_pad($j23_dest, 40));

            $this->oArquivoDados->fwrite(str_repeat(" ", 3));
            $this->oArquivoDados->fwrite(str_repeat(" ", 5));
            if ($j45_descr == "") {
                $this->oArquivoDados->fwrite(str_repeat(" ", 40));
                $this->oArquivoDados->fwrite(str_repeat(" ", 10));
            } else {
                $this->oArquivoDados->fwrite(str_pad($j45_descr, 40));
                $this->oArquivoDados->fwrite(\db_formatar($j46_dtinc, 'd'));
            }
        } elseif ($this->sTipoGeracao == static::TIPO_EMISSAO_TXTBSJ) {
            if (trim($j23_cxpostal) != "" and $j23_cxpostal > 0) {
                $linha10 .= str_pad("CAIXA POSTAL: $j23_cxpostal", 40, " ", STR_PAD_RIGHT);
            } else {
                if (strlen(trim($j23_ender)) >= 40) {
                    $j23_ender = substr($j23_ender, 0, 34);
                }

                $numero = (strlen(trim($j23_numero)) > 0 ? ", " : "") . trim($j23_numero);
                $complemento = (strlen(trim($j23_compl)) > 0 ? "/" : "") . trim($j23_compl);
                $enderecoCompleto = substr(trim($j23_ender) . $numero . $complemento . "-" . $j23_bairro, 0, 40);
                $strpad = str_pad($enderecoCompleto, 40, " ", STR_PAD_RIGHT);

                $linha10 .= substr($strpad, 0, 40);
            }
            $linha10 .= substr(str_pad($j23_munic, 20, " ", STR_PAD_RIGHT), 0, 20);
            $linha10 .= str_pad(substr($j23_cep, 0, 5), 5);
            $linha10 .= str_pad($j23_uf, 2, " ", STR_PAD_RIGHT);
            $linha10 .= str_repeat(" ", 17);
            $linha10 .= str_repeat(" ", 80);
            $linha10 .= str_pad($this->iAno, 4, "0", STR_PAD_LEFT) . " ";

            $linha10 .= str_pad($parcelamaxima + $quantunica_linha10, 2, "0", STR_PAD_LEFT);
            $linha10 .= str_repeat("0", 15);
            $linha10 .= str_repeat("0", 5);
            $linha10 .= str_repeat("0", 2);
            $linha10 .= ($mensagemdebitosanosanteriores == "" ? "N" : "S");
            $linha10 .= ($quantunica_linha10 == 0 ? "N" : "S");
            $linha10 .= str_pad(substr($j23_cep, 0, 8), 8, "0", STR_PAD_LEFT);
            $linha10 .= str_pad($parcelamaxima, 2, "0", STR_PAD_LEFT);
            $linha10 .= str_repeat(" ", 37);
            $this->oArquivoDados->fwrite(static::dbContadorBsj($linha10, "", $contador, 288));

            // parte 1
            $linha31 = "BSJR30";

            $imp_linha31 = " ";
            $imp_linha31 .= "MATRIC: $j23_matric - S/Q/L: " . $j34_setor . "/" . $j34_quadra . "/" . $j34_lote;
            $imp_linha31 .= " - BAIRRO: " . $j13_descr;
            $linha31 .= str_pad($imp_linha31, 86, " ", STR_PAD_RIGHT);

            $imp_linha31 = " ";
            $imp_linha31 .= $tipopri . " " . $nomepri . ", " . $j39_numero . "/" . $j39_compl;
            if (trim($j23_cxpostal) != "" and $j23_cxpostal > 0) {
                $imp_linha31 .= " - CX POSTAL: $j23_cxpostal";
            }
            $imp_linha31 .= " - ALIQ: $j23_aliq - TIPO: $j01_tipoimp";
            $linha31 .= str_pad($imp_linha31, 86, " ", STR_PAD_RIGHT);

            $imp_linha31 = " ";
            $imp_linha31 .= "AREA CONSTR: $j39_area";
            $imp_linha31 .= " - EXERC: $this->iAno - TOTAL (IMPOSTO+TAXAS): " . trim(\db_formatar(
                $total_j21_valor,
                'f',
                ' ',
                15
            ));
            $linha31 .= str_pad($imp_linha31, 86, " ", STR_PAD_RIGHT);

            $linha31 .= str_repeat(" ", 24);

            $this->oArquivoDados->fwrite(static::dbContadorBsj($linha31, "", $contador, 288));

            // parte 2
            $linha31 = "BSJR30";

            $imp_linha31 = " ";
            $imp_linha31 .= "AREA TOTAL LOTE: $j34_area - AREA LOTE P/CALCULO: $j23_arealo";
            $imp_linha31 .= " - VLR M2 TERRENO: " . trim(\db_formatar($j23_m2terr, 'f', ' ', 10));
            $linha31 .= str_pad($imp_linha31, 86, " ", STR_PAD_RIGHT);

            $imp_linha31 = " ";
            $imp_linha31 .= "VALOR VENAL DO TERRENO: " . trim(\db_formatar(
                $j23_vlrter,
                'f',
                ' ',
                15
            )) . " - VALOR VENAL EDIFICACOES: " . trim(\db_formatar($j22_valor, 'f', ' ', 15));
            $linha31 .= str_pad($imp_linha31, 86, " ", STR_PAD_RIGHT);

            $imp_linha31 = " ";
            $imp_linha31 .= "VALOR VENAL TOTAL: " . trim(\db_formatar($j23_vlrter + $j22_valor, 'f', ' ', 15));
            $linha31 .= str_pad($imp_linha31, 86, " ", STR_PAD_RIGHT);

            $linha31 .= str_repeat(" ", 24);

            $this->oArquivoDados->fwrite(static::dbContadorBsj($linha31, "", $contador, 288));

            // parte 3
            $linha31 = "BSJR30";

            $imp_linha31 = " ";
            $imp_linha31 .= "DESPESAS EXTRAS: " . trim(\db_formatar($this->nTaxaBancaria, 'f', ' ', 15));
            $linha31 .= str_pad($imp_linha31, 86, " ", STR_PAD_RIGHT);

            $imp_linha31 = " ";
            $imp_linha31 .= $mensagemdebitosanosanteriores;
            $linha31 .= str_pad($imp_linha31, 86, " ", STR_PAD_RIGHT);

            $imp_linha31 = " ";
            $imp_linha31 .= "";
            $linha31 .= str_pad($imp_linha31, 86, " ", STR_PAD_RIGHT);

            $linha31 .= str_repeat(" ", 24);

            $this->oArquivoDados->fwrite(static::dbContadorBsj($linha31, "", $contador, 288));
        }

        if ($lGerarLayout) {
            if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "PROPRIETARIOESCRITURA",
                    "PROPRIETARIO DA ESCRITURA",
                    $contador,
                    40
                ));

                $this->oArquivoLayout->fwrite(static::dbContador(
                    "ENDNOME",
                    "ENDERECO DO CGM DO PROPRIETARIO",
                    $contador,
                    40
                ));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "NUMIMONOME",
                    "NUMERO DO IMOVEL DO CGM DO PROPRIETARIO",
                    $contador,
                    10
                ));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "COMPLIMONOME",
                    "COMPLEMENTO DO CGM DO PROPRIETARIO",
                    $contador,
                    20
                ));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "MUNICNOME",
                    "MUNICIPIO DO CGM DO PROPRIETARIO",
                    $contador,
                    20
                ));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "CEPNOME",
                    "CEP DO CGM DO PROPRIETARIO",
                    $contador,
                    8
                ));
                $this->oArquivoLayout->fwrite(static::dbContador("UFNOME", "UF DO CGM DO PROPRIETARIO", $contador, 2));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "CNPJCPFNOME",
                    "CNPJ/CPF DO CGM DO PROPRIETARIO",
                    $contador,
                    20
                ));

                $this->oArquivoLayout->fwrite(static::dbContador(
                    "CODLOGIMO",
                    "CODIGO DO LOGRADOURO DO IMOVEL",
                    $contador,
                    6
                ));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "TIPOLOGIMO",
                    "TIPO DO LOGRADOURO DO IMOVEL",
                    $contador,
                    20
                ));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "DESCRLOGIMO",
                    "NOME DO LOGRADOURO PRINCIPAL DO IMOVEL",
                    $contador,
                    50
                ));
                $this->oArquivoLayout->fwrite(static::dbContador("NUMIMOIMO", "NUMERO DO IMOVEL", $contador, 10));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "COMPLIMOIMO",
                    "COMPLEMENTO DO IMOVEL",
                    $contador,
                    20
                ));
                $this->oArquivoLayout->fwrite(static::dbContador("BAIIMO", "BAIRRO DO IMOVEL", $contador, 40));

                $this->oArquivoLayout->fwrite(static::dbContador(
                    "LOGRADENDENT",
                    "DESCRICAO DO LOGRADOURO DO ENDERECO DE ENTREGA",
                    $contador,
                    50
                ));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "NUMIMOENDENT",
                    "NUMERO DO ENDERECO DE ENTREGA",
                    $contador,
                    10
                ));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "COMPLENDENT",
                    "COMPLEMENTO DO ENDERECO DE ENTREGA",
                    $contador,
                    20
                ));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "BAIENDENT",
                    "BAIRRO DO ENDERECO DE ENTREGA",
                    $contador,
                    40
                ));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "CIDENDENT",
                    "CIDADE DO ENDERECO DE ENTREGA",
                    $contador,
                    40
                ));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "UFENDENT",
                    "UF DO ENDERECO DE ENTREGA",
                    $contador,
                    2
                ));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "CEPENDENT",
                    "CEP DO ENDERECO DE ENTREGA",
                    $contador,
                    10
                ));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "CXPENDENT",
                    "CAIXA POSTAL DO ENDERECO DE ENTREGA",
                    $contador,
                    10
                ));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "DESTENDENT",
                    "DESTINATARIO DO ENDERECO DE ENTREGA",
                    $contador,
                    40
                ));

                $this->oArquivoLayout->fwrite(static::dbContador("BRANCOS", "BRANCOS", $contador, 3));
                $this->oArquivoLayout->fwrite(static::dbContador("BRANCOS", "BRANCOS", $contador, 5));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "DESCRISEN",
                    "DESCRICAO DO TIPO DE ISENCAO",
                    $contador,
                    40
                ));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "LANCISEN",
                    "DATA DE LANCAMENTO DA ISENCAO",
                    $contador,
                    10
                ));
            }
        }

        $sSqlCalcQntTaxasExec = "EXECUTE sSqlCalcQntTaxas({$this->iAno}, {$oDadosMatricula->j23_matric})";
        $resultcalc = \db_query($sSqlCalcQntTaxasExec) or die($sSqlCalcQntTaxasExec);

        if (pg_num_rows($resultcalc) == 0) {
            $total_j21_valor = 0;
            $quant_taxas = 0;
        } else {
            extract((array)\db_utils::fieldsMemory($resultcalc, 0, true));
        }

        if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
            $this->oArquivoDados->fwrite(\db_formatar($total_j21_valor, 'f', ' ', 15));
            $this->oArquivoDados->fwrite(str_pad($quant_taxas, 3));
        }

        if ($lGerarLayout) {
            if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "TOTREGLANC",
                    "TOTAL DOS VALORES LANCADOS (IMPOSTO + TAXAS)",
                    $contador,
                    15
                ));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "QUANTREGLANC",
                    "QUANTIDADE DE LANCAMENTOS (IMPOSTO + TAXAS)",
                    $contador,
                    3
                ));
            }
        }

        $sSqlCalcQntTaxasReceitaExec = "EXECUTE sSqlCalcQntTaxasReceita({$this->iAno}, {$oDadosMatricula->j23_matric})";
        $resultcalc = \db_query($sSqlCalcQntTaxasReceitaExec) or die($sSqlCalcQntTaxasReceitaExec);


        if (pg_num_rows($resultcalc) == 0) {
            $total_j21_valor = 0;
            $quant_taxas = 0;
        } else {
            extract((array)\db_utils::fieldsMemory($resultcalc, 0, true));
        }

        if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
            $this->oArquivoDados->fwrite(\db_formatar($total_j21_valor, 'f', ' ', 15));
            $this->oArquivoDados->fwrite(str_pad($quant_taxas, 3));
        }

        if ($lGerarLayout) {
            if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "TOTREGLANCTAXAS",
                    "TOTAL DOS VALORES LANCADOS (TAXAS)",
                    $contador,
                    15
                ));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "QUANTREGLANCTAXAS",
                    "QUANTIDADE DE LANCAMENTOS (TAXAS)",
                    $contador,
                    3
                ));
            }
        }

        $anoant = $this->iAno - 1;

        $sSqlIptuExec = "EXECUTE sSqlIptu({$this->iAno}, {$anoant}, {$oDadosMatricula->j23_matric})";
        $resultiptu = \db_query($sSqlIptuExec) or die($sSqlIptuExec);

        $iptucor = 0;
        $iptujuros = 0;
        $iptumulta = 0;
        $iptudesconto = 0;
        $iptutotal = 0;

        if (pg_num_rows($resultiptu) > 0) {
            for ($iptu = 0; $iptu < pg_num_rows($resultiptu); $iptu++) {
                extract((array)\db_utils::fieldsMemory($resultiptu, $iptu));
                $iptucor += (float)substr($fc_calcula, 14, 13);
                $iptujuros += (float)substr($fc_calcula, 27, 13);
                $iptumulta += (float)substr($fc_calcula, 40, 13);
                $iptudesconto += (float)substr($fc_calcula, 53, 13);
                $iptutotal += (float)substr($fc_calcula, 14, 13) + (float)substr(
                    $fc_calcula,
                    27,
                    13
                ) + (float)substr($fc_calcula, 40, 13) - (float)substr($fc_calcula, 53, 13);
            }
        }

        if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
            $this->oArquivoDados->fwrite(\db_formatar($iptucor, 'f', ' ', 15));
            $this->oArquivoDados->fwrite(\db_formatar($iptujuros, 'f', ' ', 15));
            $this->oArquivoDados->fwrite(\db_formatar($iptumulta, 'f', ' ', 15));
            $this->oArquivoDados->fwrite(\db_formatar($iptudesconto, 'f', ' ', 15));
            $this->oArquivoDados->fwrite(\db_formatar($iptutotal, 'f', ' ', 15));
        }

        if ($lGerarLayout) {
            if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "VALORCORRIGIDOIPTU$anoant",
                    "VALOR CORRIGIDO DA IPTU DESTA MATRICULA NO ANO $anoant",
                    $contador,
                    15
                ));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "VALORJUROSIPTU$anoant",
                    "VALOR DOS JUROS DA IPTU DESTA MATRICULA NO ANO $anoant",
                    $contador,
                    15
                ));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "VALORMULTAIPTU$anoant",
                    "VALOR DA MULTA DA IPTU DESTA MATRICULA NO ANO $anoant",
                    $contador,
                    15
                ));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "VALORDESCONTOIPTU$anoant",
                    "VALOR DO DESCONTO DA IPTU DESTA MATRICULA NO ANO $anoant",
                    $contador,
                    15
                ));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "VALORTOTALIPTU$anoant",
                    "VALOR TOTAL DA IPTU DESTA MATRICULA NO ANO $anoant",
                    $contador,
                    15
                ));
                $this->oArquivoLayout->fwrite(static::dbContador("CODIGOFACE", "CODIGO DA FACE", $contador, 10));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "VALORM2TERRENOFACE",
                    "VALOR DO M2 DO TERRENO BASEADO NA FACE",
                    $contador,
                    20
                ));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "VALORM2CONSTRFACE",
                    "VALOR DO M2 DAS EDIFICACOES BASEADO NA FACE",
                    $contador,
                    20
                ));
            }
        }

        $sSqlValorM2Exec = "EXECUTE sSqlValorM2({$j01_idbql}, {$this->iAno}, {$oDadosMatricula->j23_matric})";
        $resultvalorm2 = \db_query($sSqlValorM2Exec) or die($sSqlValorM2Exec);

        if (pg_num_rows($resultvalorm2) == 0) {
            $sqlvalorm2 = " select  j49_face as j37_face,
        j81_valorterreno as j37_valor, j37_outros,
                         case when j36_testle = 0 then j36_testad else j36_testle end as j36_testle,
                         j81_valorconstr as j37_vlcons
                           from testpri
                           inner join face on j49_face = j37_face
                           left  join facevalor on j81_face = j37_face and j81_anousu = $this->iAno
                           inner join testada on j49_face = j36_face and j49_idbql = j36_idbql
                           where j49_idbql = $j01_idbql";
            $resultvalorm2 = \db_query($sqlvalorm2);
            if (pg_numrows($resultvalorm2) == 0) {
                if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                    $this->oArquivoDados->fwrite(str_pad($j37_face, 10));
                    $this->oArquivoDados->fwrite(\db_formatar(0, 'f', ' ', 20));
                    $this->oArquivoDados->fwrite(\db_formatar(0, 'f', ' ', 20));
                }
            } else {
                extract((array)\db_utils::fieldsMemory($resultvalorm2, 0));
                if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                    $this->oArquivoDados->fwrite(str_pad($j37_face, 10));
                    $this->oArquivoDados->fwrite(\db_formatar($j37_valor, 'f', ' ', 20));
                    $this->oArquivoDados->fwrite(\db_formatar($j37_vlcons, 'f', ' ', 20));
                }
            }
        } else {
            extract((array)\db_utils::fieldsMemory($resultvalorm2, 0));
            if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                $this->oArquivoDados->fwrite(str_pad($j37_face, 10));
                $this->oArquivoDados->fwrite(\db_formatar($j37_valor, 'f', ' ', 20));
                $this->oArquivoDados->fwrite(\db_formatar($j37_vlcons, 'f', ' ', 20));
            }
        }

        $sSqlCalcIptuCaleExec = "EXECUTE sSqlCalcIptuCale({$this->iAno}, {$oDadosMatricula->j23_matric})";
        $resultcalc = \db_query($sSqlCalcIptuCaleExec) or die($sSqlCalcIptuCaleExec);


        if (pg_num_rows($resultcalc) > 0) {
            extract((array)\db_utils::fieldsMemory($resultcalc, 0));
        } else {
            $j22_valor = 0;
        }

        if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
            $this->oArquivoDados->fwrite(\db_formatar($j23_vlrter, 'f', ' ', 15));
            $this->oArquivoDados->fwrite(\db_formatar($j22_valor, 'f', ' ', 15));
            $this->oArquivoDados->fwrite(\db_formatar($j23_vlrter + $j22_valor, 'f', ' ', 15));
            $this->oArquivoDados->fwrite(str_pad($j23_aliq, 6));
        }

        if ($lGerarLayout) {
            if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                $this->oArquivoLayout->fwrite(static::dbContador("VLRVENALTER", "VALOR VENAL TERRENO", $contador, 15));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "VLRVENALEDI",
                    "VALOR VENAL EDIFICACOES",
                    $contador,
                    15
                ));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "VLRVENALTOTAL",
                    "VALOR VENAL TOTAL (TERRENO + EDIFICACOES)",
                    $contador,
                    15
                ));
                $this->oArquivoLayout->fwrite(static::dbContador("ALIQ", "ALIQUOTA", $contador, 6));
            }
        }

        $mensagemdebitosanosanteriores = "";

        if ($this->lMensagemAnosAnteriores) {
            $sql_debitos = "select fc_tipocertidao({$oDadosMatricula->j23_matric}, 'm', current_date, '')";
            $result_debitos = \db_query($sql_debitos) or die($sql_debitos);

            if (pg_num_rows($result_debitos) > 0) {
                $oDadosCertidao = \db_utils::fieldsMemory($result_debitos, 0);
                if ($oDadosCertidao->fc_tipocertidao == "positiva") {
                    $mensagemdebitosanosanteriores = "EXISTEM DÉBITOS EM ABERTO PARA ESTA MATRÍCULA ATÉ A DATA ";
                    $mensagemdebitosanosanteriores .= \db_formatar($this->oEmissao->getData()->getDate(), 'd');
                }
            }
        }

        /**
         * Parcelas Unicas
         */
        $unicasTaxaExpediente = array();

        if (!empty($this->aParcelasUnicas)) {
            if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                $this->oArquivoDados->fwrite(str_pad(count($this->aParcelasUnicas), 3));
            }

            if ($lGerarLayout) {
                if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                    $this->oArquivoLayout->fwrite(static::dbContador(
                        "TOTUNICAS",
                        "TOTAL DE PARCELA UNICA",
                        $contador,
                        3
                    ));
                }
            }

            $linha20 = "BSJR20";

            $aUnicasSelecionadasOrdenadas = [];

            foreach ($this->aParcelasUnicas as $oUnica) {
                $aReciboUnica = array_filter($aRecibos["unicas"], function ($oUnicaFiltro) use ($oUnica) {
                    return $oUnicaFiltro->getParcelaUnica() == $oUnica->getCodigo();
                });

                if (count($aReciboUnica) > 0) {
                    $aUnicasSelecionadasOrdenadas[] = $oUnica;
                }
            }

            foreach ($this->aParcelasUnicas as $oUnica) {
                $aReciboUnica = array_filter($aRecibos["unicas"], function ($oUnicaFiltro) use ($oUnica) {
                    return $oUnicaFiltro->getParcelaUnica() == $oUnica->getCodigo();
                });

                if (count($aReciboUnica) == 0) {
                    $aUnicasSelecionadasOrdenadas[] = $oUnica;
                }
            }

            $contadorRecibosGeradosUnicas = count($aRecibos["unicas"]);
            $diferenca = $this->maximoUnicasPorLinha - $contadorRecibosGeradosUnicas;

            foreach ($aUnicasSelecionadasOrdenadas as $iIndiceUnica => $oUnica) {
                $oReciboUnica = null;

                $aReciboUnica = array_filter($aRecibos["unicas"], function ($oUnicaFiltro) use ($oUnica) {
                    return $oUnicaFiltro->getParcelaUnica() == $oUnica->getCodigo();
                });

                if (count($aReciboUnica) > 0) {
                    $oReciboUnica = array_values($aReciboUnica)[0];
                }

                if (empty($oReciboUnica)) {
                    if (!$lGerarLayout) {
                        $indice = ($iIndiceUnica + 1);

                        if ($indice <= ($contadorRecibosGeradosUnicas + $diferenca)) {
                            if ($this->lConvenioCobranca) {
                                $maxcols = 101;
                            } else {
                                $maxcols = 96;
                            }

                            if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                                $this->oArquivoDados->fwrite(str_repeat(" ", (151 + $maxcols)));
                            }

                            $aQrCodesPix[] = (object) [
                                "isUnica" => true,
                                "qrCode" => "",
                                "percentual" => $k00_percdes
                            ];

                            $unicasTaxaExpediente[] = null;
                        }
                    }

                    continue;
                }

                if (!empty($oReciboUnica)) {
                    $sSqlDadosRecibo = "EXECUTE sSqlDadosRecibo({$oReciboUnica->getNumpre()})";
                    $rsDadosRecibo = \db_query($sSqlDadosRecibo);

                    $oDadosRecibo = \db_utils::fieldsMemory($rsDadosRecibo, 0);

                    $this->getReceitaTaxaExpediente($oReciboUnica->getNumpre());
                }

                $sqlfin = "select r.k00_numpre,
                             r.k00_dtvenc,
                             r.k00_dtoper,
                             r.k00_percdes,
                             fc_calcula(r.k00_numpre,0,0,r.k00_dtvenc,r.k00_dtvenc, {$this->iAno})
                        from recibounica r
                       where r.k00_numpre = {$j20_numpre}
                         and r.k00_dtvenc = '{$oUnica->getDataVencimento()->getDate()}'
                         and r.k00_dtoper = '{$oUnica->getDataOperacao()->getDate()}'
                         and k00_percdes  = {$oUnica->getPercentual()}";
                $resultfin = \db_query($sqlfin) or die($sqlfin);

                if (pg_num_rows($resultfin) > 0) {
                    extract((array)\db_utils::fieldsMemory($resultfin, 0));

                    if (empty($valorTaxaExpediente) && !empty($oReciboUnica)) {
                        $valorTaxaExpediente = self::getValorTaxaExpediente($oReciboUnica->getNumpre());
                        $unicasTaxaExpediente[$iIndiceUnica] = $valorTaxaExpediente;
                    } else {
                        $valorTaxaExpediente = 0;
                    }

                    $uvlrhis = substr($fc_calcula, 1, 13);
                    $uvlrcor = substr($fc_calcula, 14, 13);
                    $uvlrjuros = substr($fc_calcula, 27, 13);
                    $uvlrmulta = substr($fc_calcula, 40, 13);
                    $uvlrdesconto = substr($fc_calcula, 53, 13);
                    $utotal = $uvlrcor + $uvlrjuros + $uvlrmulta - $uvlrdesconto + $this->nTaxaBancaria;
                    $utotal += $valorTaxaExpediente;

                    if (!empty($oReciboUnica)) {
                        if ($this->gerarPix) {
                            $qrCodePix = "";

                            if (ArrecadacaoPixService::validaEmissaoPix(
                                $tipoDebito,
                                $oRegraEmissao,
                                $oReciboUnica->getNumpre()
                            )) {
                                try {
                                    //M26664
                                    //$emissaoPix = new ArrecadacaoPixService();
                                    //$emissaoPix->setTipoDebito($tipoDebito);
                                    //$emissaoPix->setVencimento($k00_dtvenc);
                                    //$emissaoPix->geraPixCarne(
                                    //    null,
                                    //    $oReciboUnica->getNumpre(),
                                    //    0,
                                    //    $z01_numcgm,
                                    //    $utotal,
                                    //    $oDadosRecibo->k00_codbar
                                    //);

                                    $recibobarpix = Recibobarpix::query()
                                                                ->where("k00_numpre", $oReciboUnica->getNumpre())
                                                                ->where("k00_numpar", 0)->first();

                                    if ($recibobarpix) {
                                        $qrCodePix = $recibobarpix->k00_qrcode;
                                    }
                                } catch (Exception $th) {
//                                dd($th->getMessage());
                                }
                            }

                            $aQrCodesPix[] = (object) [
                                "isUnica" => true,
                                "qrCode" => $qrCodePix,
                                "percentual" => $k00_percdes
                            ];
                        }
                    }

                    $k00_numpar = 0;

                    if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                        $this->oArquivoDados->fwrite(\db_formatar($k00_dtoper, 'd'));
                        $this->oArquivoDados->fwrite(\db_formatar($k00_dtvenc, 'd'));
                        $this->oArquivoDados->fwrite(\db_formatar($k00_percdes, 'f', ' ', 15));
                        $this->oArquivoDados->fwrite(\db_formatar($uvlrhis, 'f', ' ', 15));
                        $this->oArquivoDados->fwrite(\db_formatar($uvlrcor, 'f', ' ', 15));
                        $this->oArquivoDados->fwrite(\db_formatar($uvlrjuros, 'f', ' ', 15));
                        $this->oArquivoDados->fwrite(\db_formatar($uvlrmulta, 'f', ' ', 15));
                        $this->oArquivoDados->fwrite(\db_formatar($uvlrdesconto, 'f', ' ', 15));
                        $this->oArquivoDados->fwrite(\db_formatar($uvlrcor, 'f', ' ', 15));
                        $this->oArquivoDados->fwrite(\db_formatar($utotal, 'f', ' ', 15));
                    }

                    if ($lGerarLayout) {
                        $expressao = "OPERACAO/LANCAMENTO DA UNICA DE $k00_percdes% DE DESCONTO COM VENCIMENTO EM ";
                        $expressao .= \db_formatar($k00_dtvenc, 'd');

                        $this->oArquivoLayout->fwrite(static::dbContador(
                            "OPERUNICA" . $k00_percdes,
                            $expressao,
                            $contador,
                            10
                        ));
                        $this->oArquivoLayout->fwrite(static::dbContador(
                            "VENCUNICA" . $k00_percdes,
                            "VENCIMENTO",
                            $contador,
                            10
                        ));
                        $this->oArquivoLayout->fwrite(static::dbContador(
                            "PERCDESCUNICA" . $k00_percdes,
                            "PERCENTUAL DE DESCONTO",
                            $contador,
                            15
                        ));
                        $this->oArquivoLayout->fwrite(static::dbContador(
                            "VLRHISTUNICA" . $k00_percdes,
                            "VALOR HISTORICO",
                            $contador,
                            15
                        ));
                        $this->oArquivoLayout->fwrite(static::dbContador(
                            "VLRCORUNICA" . $k00_percdes,
                            "VALOR CORRIGIDO",
                            $contador,
                            15
                        ));
                        $this->oArquivoLayout->fwrite(static::dbContador(
                            "JURUNICA" . $k00_percdes,
                            "JUROS",
                            $contador,
                            15
                        ));
                        $this->oArquivoLayout->fwrite(static::dbContador(
                            "MULUNICA" . $k00_percdes,
                            "MULTA",
                            $contador,
                            15
                        ));
                        $this->oArquivoLayout->fwrite(static::dbContador(
                            "DESCUNICA" . $k00_percdes,
                            "DESCONTO",
                            $contador,
                            15
                        ));
                        $this->oArquivoLayout->fwrite(static::dbContador(
                            "TOTALUNICA" . $k00_percdes,
                            "TOTAL (VALOR CORRIGIDO + JUROS + MULTA)",
                            $contador,
                            15
                        ));
                        $this->oArquivoLayout->fwrite(static::dbContador(
                            "TOTALLIQUNICA" . $k00_percdes,
                            "TOTAL - DESCONTO DE " . $k00_percdes,
                            $contador,
                            15
                        ));
                        $this->oArquivoLayout->fwrite(static::dbContador(
                            "CODARREC" . $k00_percdes,
                            "NUMERO DE ARRECADACAO",
                            $contador,
                            $iTamCodArrecadao
                        ));
                    }

                    if (!empty($oReciboUnica)) {
                        if ($this->oConvenio->getTipoConvenio() == Convenio::TIPO_CONVENIO_COMPENSACAO_SICOB
                          || $this->oConvenio->getTipoConvenio() == Convenio::TIPO_CONVENIO_COMPENSACAO_SIGCB) {
                            $aNossoNumero = explode("-", $oDadosRecibo->k00_nossonumero);
                            $sNossoNumero = $aNossoNumero[0];
                            $sDigNossoNumero = $aNossoNumero[1];
                        } else {
                            $sNossoNumero = $oDadosRecibo->k00_nossonumero;
                            $sDigNossoNumero = '';
                        }

                        $oNossoNumero = new \stdClass();
                        $oNossoNumero->sNumero = $sNossoNumero;
                        $oNossoNumero->sDigito = $sDigNossoNumero;

                        $aListaNossoNumeroUnica[($iIndiceUnica + 1)] = $oNossoNumero;
                    }


                    if ($this->lConvenioCobranca) {
                        $fc_febraban = $oDadosRecibo->k00_linhadigitavel . "," . $oDadosRecibo->k00_codbar;
                        $numpreunica = \db_numpre($oReciboUnica->getNumpre()) . str_pad(null, 3, "0", STR_PAD_LEFT);

                        if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                            $this->oArquivoDados->fwrite($numpreunica);
                        } elseif ($this->sTipoGeracao == static::TIPO_EMISSAO_TXTBSJ) {
                            $linha20 .= str_pad($numpreunica, 25, " ", STR_PAD_RIGHT);
                        }

                        $maxcols = 101;
                    } else {
                        if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                            $this->oArquivoDados->fwrite(str_pad(
                                $oReciboUnica->getNumpre(),
                                8,
                                "0",
                                STR_PAD_LEFT
                            ) . "000");
                        }

                        $fc_febraban = $oDadosRecibo->k00_linhadigitavel . "," . $oDadosRecibo->k00_codbar;

                        $maxcols = 96;
                    }

                    if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                        $this->oArquivoDados->fwrite($fc_febraban);
                    } elseif ($this->sTipoGeracao == static::TIPO_EMISSAO_TXTBSJ) {
                        $sSqlValorIptuHistExec = "EXECUTE SqlValorIptuHist($this->iAno, $j23_matric)";
                        $valor_iptu_his = pg_result($sSqlValorIptuHistExec, 0, 0);

                        $sSqlValorTaxaHist = "EXECUTE sSqlValorTaxaHist($this->iAno, $j23_matric)";
                        $valor_taxa_his = pg_result($sSqlValorTaxaHist, 0, 0);

                        $linha20 .= str_pad($oConvenio->getNossoNumero(), 13, " ", STR_PAD_LEFT);
                        $linha20 .= "00";
                        $linha20 .= substr($vencunica, 8, 2) . substr($vencunica, 5, 2) . substr($vencunica, 2, 2);
                        $linha20 .= str_replace(".", "", \db_formatar($utotal, 'p', '0', 16, "e"));
                        $linha20 .= str_repeat("0", 11);
                        $linha20 .= str_replace(".", "", \db_formatar($uvlrdesconto, 'p', '0', 12, "e"));

                        $linha20 .= ($valor_iptu_his > 0 ? ($j01_tipoimp == "Predial" ? "01" : "02") : "00");

                        $linha20 .= str_replace(".", "", \db_formatar($valor_iptu_his, 'p', '0', 16, "e"));

                        $linha20 .= ($valor_taxa_his > 0 ? "10" : "00");
                        $linha20 .= str_replace(".", "", \db_formatar($valor_taxa_his, 'p', '0', 16, "e"));

                        $linha20 .= "18";
                        $linha20 .= str_replace(".", "", \db_formatar($this->nTaxaBancaria, 'p', '0', 16, "e"));

                        $linha20 .= str_repeat("0", 148);

                        $this->oArquivoDados->fwrite(static::dbContadorBsj($linha20, "", $contador, 288));
                        $this->iTotalLinhas20++;
                        $this->nTotalParcelas20 += $utotal;
                    }

                    if ($lGerarLayout) {
                        $this->oArquivoLayout->fwrite(static::dbContador(
                            "BARRASUNICA" . $k00_percdes,
                            "CODIGO DE BARRAS",
                            $contador,
                            $maxcols
                        ));
                    }
                } else {
                    if ($this->lConvenioCobranca) {
                        $maxcols = 101;
                    } else {
                        $maxcols = 96;
                    }

                    if ($lGerarLayout) {
                        $k00_percdes = $oUnica->getPercentual();
                        $k00_dtvenc = \db_formatar($oUnica->getDataVencimento()->getDate(), 'd');

                        if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                            $expressao = "OPERACAO/LANCAMENTO DA UNICA DE $k00_percdes% DE DESCONTO COM VENCIMENTO EM ";
                            $expressao .= \db_formatar($k00_dtvenc, 'd');

                            $this->oArquivoLayout->fwrite(static::dbContador(
                                "OPERUNICA" . $k00_percdes,
                                $expressao,
                                $contador,
                                10
                            ));
                            $this->oArquivoLayout->fwrite(static::dbContador(
                                "VENCUNICA" . $k00_percdes,
                                "VENCIMENTO",
                                $contador,
                                10
                            ));
                            $this->oArquivoLayout->fwrite(static::dbContador(
                                "PERCDESCUNICA" . $k00_percdes,
                                "PERCENTUAL DE DESCONTO",
                                $contador,
                                15
                            ));
                            $this->oArquivoLayout->fwrite(static::dbContador(
                                "VLRHISTUNICA" . $k00_percdes,
                                "VALOR HISTORICO",
                                $contador,
                                15
                            ));
                            $this->oArquivoLayout->fwrite(static::dbContador(
                                "VLRCORUNICA" . $k00_percdes,
                                "VALOR CORRIGIDO",
                                $contador,
                                15
                            ));
                            $this->oArquivoLayout->fwrite(static::dbContador(
                                "JURUNICA" . $k00_percdes,
                                "JUROS",
                                $contador,
                                15
                            ));
                            $this->oArquivoLayout->fwrite(static::dbContador(
                                "MULUNICA" . $k00_percdes,
                                "MULTA",
                                $contador,
                                15
                            ));
                            $this->oArquivoLayout->fwrite(static::dbContador(
                                "DESCUNICA" . $k00_percdes,
                                "DESCONTO",
                                $contador,
                                15
                            ));
                            $this->oArquivoLayout->fwrite(static::dbContador(
                                "TOTALUNICA" . $k00_percdes,
                                "TOTAL (VALOR CORRIGIDO + JUROS + MULTA)",
                                $contador,
                                15
                            ));
                            $this->oArquivoLayout->fwrite(static::dbContador(
                                "TOTALLIQUNICA" . $k00_percdes,
                                "TOTAL - DESCONTO DE " . $k00_percdes,
                                $contador,
                                15
                            ));
                            $this->oArquivoLayout->fwrite(static::dbContador(
                                "CODARREC" . $k00_percdes,
                                "NUMERO DE ARRECADACAO",
                                $contador,
                                $iTamCodArrecadao
                            ));
                            $this->oArquivoLayout->fwrite(static::dbContador(
                                "BARRASUNICA" . $k00_percdes,
                                "CODIGO DE BARRAS",
                                $contador,
                                $maxcols
                            ));
                        }
                    }

                    if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                        $this->oArquivoDados->fwrite(str_repeat(" ", (151 + $maxcols)));
                    }
                }

                if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXTBSJ) {
                    // pode ter somente uma linha de unica

                    $linha50 = "BSJR50";

                    $dados = "MATRIC: $j23_matric - S/Q/L: " . $j34_setor . "/" . $j34_quadra . "/" . $j34_lote;
                    $dados .= " - EXERC: $this->iAno";

                    $imp_linha50 = "";
                    $imp_linha50 .= str_pad($dados, 55, " ", STR_PAD_RIGHT);

                    $imp_linha50 .= trim(strtoupper(str_pad("IPTU", 37))) . ": " . trim(\db_formatar(
                        $valor_iptu_his,
                        'f',
                        ' ',
                        12
                    ));
                    $imp_linha50 .= " - " . trim(strtoupper(str_pad(
                        "COLETA DE LIXO",
                        37
                    ))) . ": " . trim(\db_formatar($valor_taxa_his, 'f', ' ', 12));

                    $linha50 .= str_pad($imp_linha50, 220, " ", STR_PAD_RIGHT);
                    $linha50 .= str_repeat(" ", 62);

                    $this->oArquivoDados->fwrite(static::dbContadorBsj($linha50, "", $contador, 288));

                    $result_mesg = \db_query("select k00_msguni from arretipo where k00_tipo = {$k00_tipo}");
                    $imp_linha50 = str_pad("BSJR50" . pg_result($result_mesg, 0, 0), 288, " ", STR_PAD_RIGHT);

                    $this->oArquivoDados->fwrite(static::dbContadorBsj($imp_linha50, "", $contador, 288));
                }

                if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXTBSJ) {
                    break;
                }
            } // fim das unicas

            if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                $this->oArquivoDados->fwrite("# FIM DAS UNICAS", 16);
            }

            if ($lGerarLayout) {
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "FIMUNICAS",
                    "EXPRESSAO # FIM DAS UNICAS",
                    $contador,
                    16
                ));
            }
        }

        // inicio parceladas

        if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
            $this->oArquivoDados->fwrite(str_pad(count($aRecibos['parcelas']), 3, "0", STR_PAD_LEFT));
            $this->oArquivoDados->fwrite("PARCELADOS");
            $this->oArquivoDados->fwrite(\db_formatar($this->nValorJuros, 'f', ' ', 15));
            $this->oArquivoDados->fwrite(\db_formatar($this->nValorJurosFaixa, 'f', ' ', 15));
        }

        if ($lGerarLayout) {
            $this->oArquivoLayout->fwrite(static::dbContador("TOTPARC", "QUANTIDADE TOTAL DE PARCELAS", $contador, 3));
            $this->oArquivoLayout->fwrite(static::dbContador("EXP_PARCELADOS", "EXPRESSAO PARCELADOS", $contador, 10));
            $this->oArquivoLayout->fwrite(static::dbContador(
                "PERCMESJURATRASO",
                "PERCENTUAL POR MES DE JUROS POR ATRASO",
                $contador,
                15
            ));
            $this->oArquivoLayout->fwrite(static::dbContador(
                "PERCGERMULATRASO",
                "PERCENTUAL GERAL DE MULTA POR ATRASO",
                $contador,
                15
            ));
        }

        /**
         * @todo ver como fica no caso do BSJ
         */
        if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
            $parcelamaxima = 12;
        }

        /**
         * @todo verificar depois
         */
        $gerarparcelado = true;

        $aDadosParcelas = array();
        $valorTaxaExpediente = 0;
        $parcelasTaxaExpediente = array();
        $valorParcela = array();

        for ($iParcela = 1; $iParcela <= $parcelamaxima; $iParcela++) {
            $linha21 = "BSJR20";

            $fc_febraban = "";

            $oReciboParcela = null;
            $oDadosRecibo = null;

            if (!empty($aRecibos["parcelas"][$iParcela])) {
                $oReciboParcela = $aRecibos["parcelas"][$iParcela];

                $sSqlDadosRecibo = "EXECUTE sSqlDadosRecibo({$oReciboParcela->getNumpre()})";
                $rsDadosRecibo = \db_query($sSqlDadosRecibo);

                $oDadosRecibo = \db_utils::fieldsMemory($rsDadosRecibo, 0);
                $aDadosParcelas[$iParcela] = $oDadosRecibo;

                $this->getReceitaTaxaExpediente($oReciboParcela->getNumpre());
            }

            $k00_numpar = $iParcela;
            $k00_numpre = "";
            $k00_dtvenc = "";
            $k00_valor = 0;

            if (!empty($oReciboParcela)) {
                $aNumpre = explode(',', trim($oDadosRecibo->numpres, '{}'));

                $data_calc = $this->oEmissao->getData()->getDate();
                $sql_calcula = "select fc_calcula({$aNumpre[0]}";
                $sql_calcula .= ", {$k00_numpar}, 0, '{$data_calc}', '{$data_calc}', {$this->iAno})";
                $rsCalcula = \db_query($sql_calcula);

                if ($rsCalcula && pg_num_rows($rsCalcula) > 0) {
                    $oDadosCalculo = \db_utils::fieldsMemory($rsCalcula, 0);
                    $k00_valor = (float)substr($oDadosCalculo->fc_calcula, 14, 13) +
                      (float)substr($oDadosCalculo->fc_calcula, 27, 13) +
                      (float)substr($oDadosCalculo->fc_calcula, 40, 13) -
                      (float)substr($oDadosCalculo->fc_calcula, 53, 13);
                }

                $k00_numpre = $oReciboParcela->getNumpre();
                $k00_dtvenc = $oDadosRecibo->k00_dtpaga;
            }

            $k00_valor += $this->nTaxaBancaria;

            if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                if ($gerarparcelado == true && !empty($oReciboParcela)) {
                    if (empty($valorTaxaExpediente)) {
                        $valorTaxaExpediente = self::getValorTaxaExpediente($oReciboParcela->getNumpre());
                    } else {
                        $valorTaxaExpediente = 0;
                    }

                    $k00_valor += $valorTaxaExpediente;

                    $this->oArquivoDados->fwrite(\db_formatar($k00_dtvenc, 'd'));
                    $this->oArquivoDados->fwrite(\db_formatar($k00_valor, 'f', ' ', 15));
                    $this->oArquivoDados->fwrite(\db_formatar($k00_valor * $this->nValorJuros / 100, 'f', ' ', 15));
                    $this->oArquivoDados->fwrite(\db_formatar(
                        $k00_valor * $this->nValorJurosFaixa / 100,
                        'f',
                        ' ',
                        15
                    ));
                } else {
                    $this->oArquivoDados->fwrite(str_repeat(" ", 10));
                    $this->oArquivoDados->fwrite(\db_formatar(0, 'f', ' ', 15));
                    $this->oArquivoDados->fwrite(\db_formatar(0, 'f', ' ', 15));
                    $this->oArquivoDados->fwrite(\db_formatar(0, 'f', ' ', 15));
                }
            }

            if (!empty($oReciboParcela)) {
                if ($this->gerarPix) {
                    $qrCodePix = "";

                    if (ArrecadacaoPixService::validaEmissaoPix(
                        $tipoDebito,
                        $oRegraEmissao,
                        $oReciboParcela->getNumpre()
                    )) {
                        try {
                            //M26664
                            //$emissaoPix = new ArrecadacaoPixService();
                            //$emissaoPix->setTipoDebito($tipoDebito);
                            //$emissaoPix->setVencimento($k00_dtvenc);
                            //$emissaoPix->geraPixCarne(
                            //    null,
                            //    $oReciboParcela->getNumpre(),
                            //    $iParcela,
                            //    $z01_numcgm,
                            //    $k00_valor,
                            //    $oDadosRecibo->k00_codbar
                            //);

                            $recibobarpix = Recibobarpix::query()
                                                        ->where("k00_numpre", $oReciboParcela->getNumpre())
                                                        ->where("k00_numpar", $iParcela)->first();

                            if ($recibobarpix) {
                                $qrCodePix = $recibobarpix->k00_qrcode;
                            }
                        } catch (Exception $th) {
    //                      dd($th->getMessage());
                        }
                    }

                    $aQrCodesPix[] = (object) [
                        "isUnica" => false,
                        "qrCode" => $qrCodePix,
                        "parcela" => $iParcela
                    ];
                }
            }

            $parcelasTaxaExpediente[$iParcela] = $valorTaxaExpediente;
            $valorParcela[$iParcela] = $k00_valor;
            if ($lGerarLayout) {
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "VENCPARC" . str_pad($iParcela, 3, "0", STR_PAD_LEFT),
                    "VENCIMENTO DA PARCELA $iParcela",
                    $contador,
                    10
                ));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "VALPARC" . str_pad($iParcela, 3, "0", STR_PAD_LEFT),
                    "VALOR DA PARCELA $iParcela",
                    $contador,
                    15
                ));
                $this->oArquivoLayout->fwrite(static::dbContador("VALJURPARC" . str_pad(
                    $iParcela,
                    3,
                    "0",
                    STR_PAD_LEFT
                ), "JUROS POR ATRASO DE 1 MES JA CALCULADOS DA PARCELA $iParcela", $contador, 15));
                $this->oArquivoLayout->fwrite(static::dbContador("VALMULPARC" . str_pad(
                    $iParcela,
                    3,
                    "0",
                    STR_PAD_LEFT
                ), "MULTA POR ATRASO DE 1 MES JA CALCULADOS DA PARCELA $iParcela", $contador, 15));
                $this->oArquivoLayout->fwrite(static::dbContador("NUMPREPARC" . str_pad(
                    $iParcela,
                    3,
                    "0",
                    STR_PAD_LEFT
                ), "CODIGO DE ARRECADACAO DA PARCELA $iParcela", $contador, $iTamCodArrecadao));
            }

            if (!empty($oReciboParcela)) {
                if ($this->oConvenio->getTipoConvenio() == Convenio::TIPO_CONVENIO_COMPENSACAO_SICOB
                  || $this->oConvenio->getTipoConvenio() == Convenio::TIPO_CONVENIO_COMPENSACAO_SIGCB) {
                    $aNossoNumero = explode("-", $oDadosRecibo->k00_nossonumero);
                    $sNossoNumero = $aNossoNumero[0];
                    $sDigNossoNumero = $aNossoNumero[1];
                } else {
                    $sNossoNumero = $oDadosRecibo->k00_nossonumero;
                    $sDigNossoNumero = '';
                }

                $oNossoNumero = new \stdClass();
                $oNossoNumero->sNumero = $sNossoNumero;
                $oNossoNumero->sDigito = $sDigNossoNumero;

                $aListaNossoNumero[$k00_numpar] = $oNossoNumero;
            }

            if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXTBSJ) {
                $sSqlValorIptuHistReceitaExec = "EXECUTE sSqlValorIptuHistReceita($k00_numpre, $k00_numpar)";
                $valor_iptu_his = pg_result($sSqlValorIptuHistReceitaExec, 0, 0);


                $sSqlValorTaxaHistReceitaExec = "EXECUTE sSqlValorTaxaHistReceita($k00_numpre, $k00_numpar)";
                $valor_taxa_his = pg_result($sSqlValorTaxaHistReceitaExec, 0, 0);
            }

            if ($this->lConvenioCobranca) {
                if ($gerarparcelado == true && !empty($oReciboParcela)) {
                    $fc_febraban = $oDadosRecibo->k00_linhadigitavel . "," . $oDadosRecibo->k00_codbar;
                    $numprepar = \db_numpre($oReciboParcela->getNumpre()) . str_pad(0, 3, "0", STR_PAD_LEFT);

                    if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                        $this->oArquivoDados->fwrite($numprepar);
                    } elseif ($this->sTipoGeracao == static::TIPO_EMISSAO_TXTBSJ) {
                        $linha21 .= str_pad($numprepar, 25, " ", STR_PAD_RIGHT);
                    }
                } else {
                    if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                        $this->oArquivoDados->fwrite(str_repeat(" ", $iTamCodArrecadao));
                    } elseif ($this->sTipoGeracao == static::TIPO_EMISSAO_TXTBSJ) {
                        $linha21 .= str_repeat("0", 25);
                    }

                    $fc_febraban = '';
                }

                if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXTBSJ && !empty($oReciboParcela)) {
                    $linha21 .= str_pad($oConvenio->getNossoNumero(), 13, " ", STR_PAD_LEFT);
                    $linha21 .= str_pad($k00_numpar, 2, "0", STR_PAD_LEFT);
                    $linha21 .= substr($k00_dtvenc, 8, 2) . substr($k00_dtvenc, 5, 2) . substr($k00_dtvenc, 2, 2);
                    $linha21 .= str_replace(".", "", \db_formatar($k00_valor, 'p', '0', 16, "e"));
                    $linha21 .= str_repeat("0", 11);
                    $linha21 .= str_repeat("0", 11);

                    $linha21 .= ($valor_iptu_his > 0 ? ($j01_tipoimp == "Predial" ? "01" : "02") : "00");

                    $linha21 .= str_replace(".", "", \db_formatar($valor_iptu_his, 'p', '0', 16, "e"));

                    $linha21 .= ($valor_taxa_his > 0 ? "10" : "00");
                    $linha21 .= str_replace(".", "", \db_formatar($valor_taxa_his, 'p', '0', 16, "e"));

                    $linha21 .= "18";
                    $linha21 .= str_replace(".", "", \db_formatar($taxa_bancaria, 'p', '0', 16, "e"));

                    $linha21 .= str_repeat("0", 148);

                    $this->oArquivoDados->fwrite(static::dbContadorBsj($linha21, "", $contador, 288));
                    $this->iTotalLinhas20++;
                    $this->nTotalParcelas20 += $k00_valor;
                }

                $maxcols = 101;
            } else {
                if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                    if ($gerarparcelado == true && !empty($oReciboParcela)) {
                        $this->oArquivoDados->fwrite(\db_numpre($oReciboParcela->getNumpre()) . str_pad(
                            null,
                            3,
                            "0",
                            STR_PAD_LEFT
                        ));
                    } else {
                        $this->oArquivoDados->fwrite(str_pad(" ", $iTamCodArrecadao, " ", STR_PAD_LEFT));
                    }
                }

                if (!empty($oReciboParcela)) {
                    $fc_febraban = $oDadosRecibo->k00_linhadigitavel . "," . $oDadosRecibo->k00_codbar;
                }

                $maxcols = 96;
            }

            if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                if ($gerarparcelado == true && !empty($oReciboParcela)) {
                    $this->oArquivoDados->fwrite($fc_febraban);
                    $this->oArquivoDados->fwrite(str_pad($k00_numpar, 3, "0", STR_PAD_LEFT));
                } else {
                    $this->oArquivoDados->fwrite(str_repeat(" ", $maxcols));
                    $this->oArquivoDados->fwrite(str_repeat(" ", 3));
                }
            }

            if ($lGerarLayout) {
                $this->oArquivoLayout->fwrite(static::dbContador("BARRASPARC" . str_pad(
                    $k00_numpar,
                    3,
                    "0",
                    STR_PAD_LEFT
                ), "CODIGO DE BARRAS DA PARCELA $k00_numpar", $contador, $maxcols));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "PARC" . str_pad($k00_numpar, 3, "0", STR_PAD_LEFT),
                    "PARCELA " . str_pad($k00_numpar, 2),
                    $contador,
                    3
                ));
            }

            // imprime linha 50 para parcela
            if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXTBSJ) {
                $linha50 = "BSJR50";

                $dados = "MATRIC: $j23_matric - S/Q/L: " . $j34_setor . "/" . $j34_quadra . "/" . $j34_lote;
                $dados .= " - EXERC: $anousu";

                $imp_linha50 = "";
                $imp_linha50 .= str_pad($dados, 55, " ", STR_PAD_RIGHT);

                $imp_linha50 .= trim(strtoupper(str_pad("IPTU", 37))) . ": " . trim(\db_formatar(
                    $valor_iptu_his,
                    'f',
                    ' ',
                    12
                ));
                $imp_linha50 .= " - " . trim(strtoupper(str_pad(
                    "COLETA DE LIXO",
                    37
                ))) . ": " . trim(\db_formatar($valor_taxa_his, 'f', ' ', 12));

                $linha50 .= str_pad($imp_linha50, 220, " ", STR_PAD_RIGHT);
                $linha50 .= str_repeat(" ", 62);

                $this->oArquivoDados->fwrite(static::dbContadorBsj($linha50, "", $contador, 288));

                $result_mesg = \db_query("select k00_msgparc from arretipo where k00_tipo = {$k00_tipo}");

                $imp_linha50 = str_pad("BSJR50" . pg_result($result_mesg, 0, 0), 288, " ", STR_PAD_RIGHT);

                $this->oArquivoDados->fwrite(static::dbContadorBsj($imp_linha50, "", $contador, 288));
            }

            if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXTBSJ) {
                break;
            }
        } // final parcelas

        /**
         * Imprime as parcelas que ja foram pagas
         */
        for ($iParcela = 1; $iParcela <= $this->iParcelaMaxima; $iParcela++) {
            $sSqlPagasExec = "EXECUTE sSqlPagas({$oDadosMatricula->j20_numpre}, $iParcela)";
            $resultpagas = \db_query($sSqlPagasExec) or die($sSqlPagasExec);

            if (pg_num_rows($resultpagas) == 0) {
                $dtpago = "          ";
                $k00_valor = 0;
            } else {
                extract((array)\db_utils::fieldsMemory($resultpagas, 0));
                if (strlen($dtpago) == 0) {
                    $dtpago = "          ";
                    $valorpago = 0;
                } else {
                    $dtpago = \db_formatar($dtpago, 'd');
                }
            }

            if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                $this->oArquivoDados->fwrite($dtpago);
                $this->oArquivoDados->fwrite(\db_formatar($valorpago + 0, 'f', ' ', 15));
            }

            if ($lGerarLayout) {
                $this->oArquivoLayout->fwrite(static::dbContador("DTPGTOPARC" . str_pad(
                    $iParcela,
                    3,
                    "0",
                    STR_PAD_LEFT
                ), "DATA DO PAGAMENTO DA PARCELA $iParcela", $contador, 10));
                $this->oArquivoLayout->fwrite(static::dbContador("VALORPGTOPARC" . str_pad(
                    $iParcela,
                    3,
                    "0",
                    STR_PAD_LEFT
                ), "VALOR DO PAGAMENTO DA PARCELA $iParcela", $contador, 15));
            }
        }

        $sSqlParcPagasExec = "EXECUTE sSqlParcPagas({$oDadosMatricula->j20_numpre})";
        $resultpagas = \db_query($sSqlParcPagasExec) or die($sSqlParcPagasExec);

        if (pg_num_rows($resultpagas) == 0) {
            $valorpago = 0;
        } else {
            extract((array)\db_utils::fieldsMemory($resultpagas, 0));
        }

        if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
            $this->oArquivoDados->fwrite(str_pad(\db_formatar($valorpago, 'f', ' ', 18), 18, ' ', STR_PAD_LEFT));
        }

        if ($lGerarLayout) {
            $this->oArquivoLayout->fwrite(static::dbContador("TOTALPAGO", "TOTAL PAGO DESTE REGISTRO", $contador, 18));
        }


        /**
         * Aqui começa a bagaça
         */

        /**
         * Buscar as informações das taxas
         */
        if ($lGerarLayout) {
            $sqlcalc = "  select j17_codhis,                                                                      ";
            $sqlcalc .= "         j17_descr                                                                        ";
            $sqlcalc .= "    from (select k02_codigo,                                                              ";
            $sqlcalc .= "                 k02_descr,                                                               ";
            $sqlcalc .= "                 j17_codhis,                                                              ";
            $sqlcalc .= "                 j17_descr,                                                               ";
            $sqlcalc .= "                 sum(j21_valor) as j21_valor,                                             ";
            $sqlcalc .= "                 sum(coalesce(j21_quant,0)) as j21_quant,                                 ";
            $sqlcalc .= "                 sum(case                                                                 ";
            $sqlcalc .= "                   when iptucalhconf.j89_codhis is not null then                          ";
            $sqlcalc .= "                     (select sum(x.j21_valor)                                             ";
            $sqlcalc .= "                       from iptucalv x                                                    ";
            $sqlcalc .= "                      where x.j21_anousu = iptucalv.j21_anousu                            ";
            $sqlcalc .= "                        and x.j21_matric = iptucalv.j21_matric                            ";
            $sqlcalc .= "                        and x.j21_receit = iptucalv.j21_receit                            ";
            $sqlcalc .= "                        and x.j21_codhis = iptucalhconf.j89_codhis)                       ";
            $sqlcalc .= "                   else 0                                                                 ";
            $sqlcalc .= "                 end) as j21_valorisen                                                    ";
            $sqlcalc .= "            from iptucalv                                                                 ";
            $sqlcalc .= "                 inner join iptucalh        on iptucalh.j17_codhis        = j21_codhis    ";
            $sqlcalc .= "                 left  join iptucalhconf    on iptucalhconf.j89_codhispai = j21_codhis    ";
            $sqlcalc .= "                 inner join tabrec          on tabrec.k02_codigo          = j21_receit    ";
            $sqlcalc .= "                 left  join iptucadtaxaexe  on iptucadtaxaexe.j08_tabrec  = j21_receit    ";
            $sqlcalc .= "                                           and iptucadtaxaexe.j08_anousu  = {$this->iAno} ";
            $sqlcalc .= "           where j21_anousu = {$this->iAno}                                               ";
            $sqlcalc .= "             and j17_codhis not in (select j89_codhis from iptucalhconf)                  ";
            $sqlcalc .= "           group by k02_codigo, ";
            $sqlcalc .= "                    k02_descr,  ";
            $sqlcalc .= "                    j17_codhis, ";
            $sqlcalc .= "                    j17_descr  ";
            $sqlcalc .= "           order by iptucalh.j17_codhis ";
            $sqlcalc .= "        ) as x ";
            $sqlcalc .= " group by j17_codhis, ";
            $sqlcalc .= "          j17_descr  ";
            $sqlcalc .= " order by j17_codhis ";

            $cria_tab_sqlcalc = "create temp table {$this->sTabelaTemporariaTaxas} as {$sqlcalc}";
            $resultcriacalc = \db_query($cria_tab_sqlcalc) or die($cria_tab_sqlcalc);

            $resultcalc = \db_query("select * from {$this->sTabelaTemporariaTaxas}");

            if (pg_num_rows($resultcalc) > 0) {
                for ($iIndice = 0; $iIndice < pg_num_rows($resultcalc); $iIndice++) {
                    $oDadosReceitas = \db_utils::fieldsMemory($resultcalc, $iIndice);

                    $this->oArquivoLayout->fwrite(static::dbContador("DESCRTAXA" . str_pad(
                        $oDadosReceitas->j17_codhis,
                        3,
                        "0",
                        STR_PAD_LEFT
                    ), "DESCRICAO DA TAXA {$oDadosReceitas->j17_descr}", $contador, 40));
                    $this->oArquivoLayout->fwrite(static::dbContador("QUANTTAXA" . str_pad(
                        $oDadosReceitas->j17_codhis,
                        3,
                        "0",
                        STR_PAD_LEFT
                    ), "QUANTIDADE DA TAXA {$oDadosReceitas->j17_descr}", $contador, 10));
                    $this->oArquivoLayout->fwrite(static::dbContador("VALTAXA" . str_pad(
                        $oDadosReceitas->j17_codhis,
                        3,
                        "0",
                        STR_PAD_LEFT
                    ), "VALOR DA TAXA {$oDadosReceitas->j17_descr}", $contador, 18));
                    $this->oArquivoLayout->fwrite(static::dbContador(
                        "VALTAXAPARC" . str_pad(
                            $oDadosReceitas->j17_codhis,
                            3,
                            "0",
                            STR_PAD_LEFT
                        ),
                        "VALOR DA TAXA {$oDadosReceitas->j17_descr} PARA CADA PARCELA",
                        $contador,
                        18
                    ));
                }

                for ($iIndiceTaxa = $iIndice; $iIndiceTaxa < 10; $iIndiceTaxa++) {
                    $this->oArquivoLayout->fwrite(static::dbContador(
                        "BRANCOS",
                        "TAXA SEM USO {$iIndiceTaxa}",
                        $contador,
                        86
                    ));
                }
            } else {
                $this->oArquivoLayout->fwrite(static::dbContador("BRANCOS", "ESPACOS EM BRANCO", $contador, 63));
            }
        }

        $sSqlFin02Exec = "EXECUTE sSqlFin02({$oDadosMatricula->j20_numpre})";
        $resultfin2 = \db_query($sSqlFin02Exec) or die($sSqlFin02Exec);

        $colunaTotal = " j21_quant, sum ( coalesce(j21_valor,0) - abs( coalesce(j21_valorisen,0 ) ) ) as j21_valor, ";
        $groupby = " group by j17_codhis, j21_quant, j17_descr ";
        $whereMatric = " and j21_matric = {$oDadosMatricula->j23_matric} ";

        $sqlcalc = "  select j17_codhis,                                                                      ";
        $sqlcalc .= "         {$colunaTotal}                                                                   ";
        $sqlcalc .= "         j17_descr                                                                        ";
        $sqlcalc .= "    from (select k02_codigo,                                                              ";
        $sqlcalc .= "                 k02_descr,                                                               ";
        $sqlcalc .= "                 j17_codhis,                                                              ";
        $sqlcalc .= "                 j17_descr,                                                               ";
        $sqlcalc .= "                 sum(j21_valor) as j21_valor,                                             ";
        $sqlcalc .= "                 sum(coalesce(j21_quant,0)) as j21_quant,                                 ";
        $sqlcalc .= "                 sum(case                                                                 ";
        $sqlcalc .= "                   when iptucalhconf.j89_codhis is not null then                          ";
        $sqlcalc .= "                     (select sum(x.j21_valor)                                             ";
        $sqlcalc .= "                       from iptucalv x                                                    ";
        $sqlcalc .= "                      where x.j21_anousu = iptucalv.j21_anousu                            ";
        $sqlcalc .= "                        and x.j21_matric = iptucalv.j21_matric                            ";
        $sqlcalc .= "                        and x.j21_receit = iptucalv.j21_receit                            ";
        $sqlcalc .= "                        and x.j21_codhis = iptucalhconf.j89_codhis)                       ";
        $sqlcalc .= "                   else 0                                                                 ";
        $sqlcalc .= "                 end) as j21_valorisen                                                    ";
        $sqlcalc .= "            from iptucalv                                                                 ";
        $sqlcalc .= "                 inner join iptucalh        on iptucalh.j17_codhis        = j21_codhis    ";
        $sqlcalc .= "                 left  join iptucalhconf    on iptucalhconf.j89_codhispai = j21_codhis    ";
        $sqlcalc .= "                 inner join tabrec          on tabrec.k02_codigo          = j21_receit    ";
        $sqlcalc .= "                 left  join iptucadtaxaexe  on iptucadtaxaexe.j08_tabrec  = j21_receit    ";
        $sqlcalc .= "                                           and iptucadtaxaexe.j08_anousu  = {$this->iAno} ";
        $sqlcalc .= "           where j21_anousu = {$this->iAno}                                               ";
        $sqlcalc .= "                 {$whereMatric}                                                           ";
        $sqlcalc .= "             and j17_codhis not in (select j89_codhis from iptucalhconf)                  ";
        $sqlcalc .= "           group by k02_codigo, ";
        $sqlcalc .= "                    k02_descr,  ";
        $sqlcalc .= "                    j17_codhis, ";
        $sqlcalc .= "                    j17_descr  ";
        $sqlcalc .= "           order by iptucalh.j17_codhis ";
        $sqlcalc .= "        ) as x ";
        $sqlcalc .= $groupby;
        $sqlcalc .= " order by j17_codhis ";

        $w_iptucalv2 = "w_iptucalv2_{$this->iAno}";

        $cria_tab_sqlcalc = "create temp table $w_iptucalv2 as $sqlcalc";
        $resultcriacalc = \db_query($cria_tab_sqlcalc) or die($cria_tab_sqlcalc);

        $sqlcalc = "select {$this->sTabelaTemporariaTaxas}.j17_codhis";
        $sqlcalc .= ", {$this->sTabelaTemporariaTaxas}.j17_descr";
        $sqlcalc .= ", coalesce( (select j21_valor ";
        $sqlcalc .= "               from $w_iptucalv2";
        $sqlcalc .= "              where $w_iptucalv2.j17_codhis = {$this->sTabelaTemporariaTaxas}.j17_codhis )";
        $sqlcalc .= "          , 0) as j21_valor";
        $sqlcalc .= ", coalesce( (select j21_quant ";
        $sqlcalc .= "               from $w_iptucalv2";
        $sqlcalc .= "              where $w_iptucalv2.j17_codhis = {$this->sTabelaTemporariaTaxas}.j17_codhis )";
        $sqlcalc .= "          ,0) as j21_quant";
        $sqlcalc .= " from {$this->sTabelaTemporariaTaxas} ";
        $sqlcalc .= "order by {$this->sTabelaTemporariaTaxas}.j17_codhis";

        $resultcalc = \db_query($sqlcalc) or die("erro: " . $sqlcalc);
        if (pg_num_rows($resultcalc) > 0) {
            for ($iIndice = 0; $iIndice < pg_num_rows($resultcalc); $iIndice++) {
                $oDadosReceitas = \db_utils::fieldsMemory($resultcalc, $iIndice);

                if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                    if ($oDadosReceitas->j21_valor == 0) {
                        $this->oArquivoDados->fwrite(str_pad(null, 40));
                        $this->oArquivoDados->fwrite(str_pad(\db_formatar(0, 'f', ' ', 10), "0", STR_PAD_LEFT));
                        $this->oArquivoDados->fwrite(str_pad("", 18, ' ', STR_PAD_LEFT));
                        $this->oArquivoDados->fwrite(str_pad("", 18, ' ', STR_PAD_LEFT));
                    } else {
                        $this->oArquivoDados->fwrite(str_pad($oDadosReceitas->j17_descr, 40));
                        $this->oArquivoDados->fwrite(str_pad(
                            \db_formatar($oDadosReceitas->j21_quant, 'f', ' ', 10),
                            "0",
                            STR_PAD_LEFT
                        ));
                        $this->oArquivoDados->fwrite(str_pad(
                            \db_formatar($oDadosReceitas->j21_valor, 'f', ' ', 18),
                            18,
                            ' ',
                            STR_PAD_LEFT
                        ));
                        $this->oArquivoDados->fwrite(str_pad(\db_formatar(
                            $oDadosReceitas->j21_valor / pg_num_rows($resultfin2),
                            'f',
                            ' ',
                            18
                        ), 18, ' ', STR_PAD_LEFT));
                    }
                }
            }

            for ($iIndiceTaxa = $iIndice; $iIndiceTaxa < 10; $iIndiceTaxa++) {
                if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                    $this->oArquivoDados->fwrite(str_pad(' ', 86));
                }
            }
        } else {
            if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                $this->oArquivoDados->fwrite(str_repeat(" ", 63));
            }
        }

        $cria_tab_sqlcalc = "drop table $w_iptucalv2";
        $resultcriacalc = \db_query($cria_tab_sqlcalc) or die($cria_tab_sqlcalc);


        $sSqlTestadaExec = "EXECUTE sSqlTestada({$oDadosMatricula->j23_matric})";
        $resulttestada = \db_query($sSqlTestadaExec) or die($sSqlTestadaExec);

        if (pg_num_rows($resulttestada) > 0) {
            extract((array)\db_utils::fieldsMemory($resulttestada, 0));
        } else {
            $j36_testad = 0;
        }

        $sSqlIptuAntExec = "EXECUTE sSqlIptuAnt({$oDadosMatricula->j23_matric})";
        $resultiptuant = \db_query($sSqlIptuAntExec) or die($sSqlIptuAntExec);

        if (pg_num_rows($resultiptuant) > 0) {
            extract((array)\db_utils::fieldsMemory($resultiptuant, 0));
        } else {
            $j40_refant = "";
        }

        if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
            $this->oArquivoDados->fwrite(str_pad($j36_testad, 20));
            $this->oArquivoDados->fwrite(str_pad($j34_area, 20));
            $this->oArquivoDados->fwrite(str_pad($j39_area, 20));
            $this->oArquivoDados->fwrite(str_pad($j40_refant, 20));
            $this->oArquivoDados->fwrite(str_pad(\db_formatar($j23_arealo, 'f', ' ', 18), 18, ' ', STR_PAD_LEFT));
            $this->oArquivoDados->fwrite(str_pad(\db_formatar($j23_m2terr, 'f', ' ', 18), 18, ' ', STR_PAD_LEFT));
        }

        if ($lGerarLayout) {
            $this->oArquivoLayout->fwrite(static::dbContador(
                "TESTADALOTE",
                "TESTADA PRINCIPAL DO LOTE",
                $contador,
                20
            ));
            $this->oArquivoLayout->fwrite(static::dbContador("AREALOTE", "AREA DO LOTE", $contador, 20));
            $this->oArquivoLayout->fwrite(static::dbContador("AREATOTCONSTR", "AREA TOTAL CONSTRUIDA", $contador, 20));
            $this->oArquivoLayout->fwrite(static::dbContador(
                "REFERENCIAANTERIOR",
                "REFERENCIA ANTERIOR",
                $contador,
                20
            ));
            $this->oArquivoLayout->fwrite(static::dbContador(
                "AREADOLOTE",
                "AREA DO LOTE CONSIDERADA NO CALCULO",
                $contador,
                18
            ));
            $this->oArquivoLayout->fwrite(static::dbContador(
                "VALORM2CALCULO",
                "VALOR DO METRO QUADRADO DO TERRENO DO CALCULO",
                $contador,
                18
            ));
        }

        /**
         * Aqui que deveria ir a lógica do recibo gerado para as parcelas vencidas
         */
        if ($lGerarLayout) {
            if (pg_num_rows($this->rsReceitasArrecadGeral) > 0) {
                for ($unicont = 1; $unicont <= $this->iParcelaMaxima; $unicont++) {
                    for ($rec = 0; $rec < pg_num_rows($this->rsReceitasArrecadGeral); $rec++) {
                        extract((array)\db_utils::fieldsMemory($this->rsReceitasArrecadGeral, $rec));

                        $this->oArquivoLayout->fwrite(static::dbContador(
                            "PARC" . str_pad(
                                $unicont,
                                3,
                                "0",
                                STR_PAD_LEFT
                            ) . str_pad($k00_receit, 3, "0", STR_PAD_LEFT),
                            "PARCELA $unicont - RECEITA $k00_receit",
                            $contador,
                            3
                        ));
                        $this->oArquivoLayout->fwrite(static::dbContador(
                            "REC" . str_pad(
                                $unicont,
                                3,
                                "0",
                                STR_PAD_LEFT
                            ) . str_pad($k00_receit, 3, "0", STR_PAD_LEFT),
                            "RECEITA $k00_receit - PARCELA $unicont",
                            $contador,
                            3
                        ));
                        $this->oArquivoLayout->fwrite(static::dbContador(
                            "VALPARCREC" . str_pad(
                                $unicont,
                                3,
                                "0",
                                STR_PAD_LEFT
                            ) . str_pad($k00_receit, 3, "0", STR_PAD_LEFT),
                            "VALOR DA PARCELA $unicont - RECEITA $k00_receit",
                            $contador,
                            15
                        ));
                    }
                }
            }
        }

        // inicio parcelas com receitas
        for ($unicont = 1; $unicont <= $this->iParcelaMaxima; $unicont++) {
            for ($rec = 0; $rec < pg_num_rows($this->rsReceitasArrecadGeral); $rec++) {
                extract((array)\db_utils::fieldsMemory($this->rsReceitasArrecadGeral, $rec));

                $sqlarrecadrec = "select sum(k00_valor) as k00_valor ";
                $sqlarrecadrec .= " from iptunump ";
                $sqlarrecadrec .= "      inner join arrematric on iptunump.j20_numpre = arrematric.k00_numpre ";
                $sqlarrecadrec .= "      inner join arrecad on arrematric.k00_numpre = arrecad.k00_numpre ";
                $sqlarrecadrec .= "where iptunump.j20_anousu = {$this->iAno}";
                $sqlarrecadrec .= "  and iptunump.j20_matric = {$oDadosMatricula->j23_matric}";
                $sqlarrecadrec .= "  and k00_numpar = $unicont ";
                $sqlarrecadrec .= "  and case when $k00_receit in ({$this->sListaReceitas}) ";
                $sqlarrecadrec .= "           then k00_receit in ({$this->sListaReceitas})";
                $sqlarrecadrec .= "           else k00_receit = $k00_receit end";
                $resultfinarrecadrec = \db_query($sqlarrecadrec) or die($sqlarrecadrec);

                if (pg_num_rows($resultfinarrecadrec) == 0) {
                    $k00_valor = 0;
                } else {
                    extract((array)\db_utils::fieldsMemory($resultfinarrecadrec, 0));
                }

                if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                    $this->oArquivoDados->fwrite(substr(str_pad($unicont, 3, "0", STR_PAD_LEFT), 0, 3));
                    $this->oArquivoDados->fwrite(substr(str_pad($k00_receit, 3, "0", STR_PAD_LEFT), 0, 3));
                }

                $sqlimposto = "select $k00_receit in ({$this->sListaReceitas}) as imposto";
                $resultimposto = \db_query($sqlimposto) or die($sqlimposto);
                extract((array)\db_utils::fieldsMemory($resultimposto, 0));

                if ($imposto == 't') {
                    $sqlisencao = "select * from iptuisen ";
                    $sqlisencao .= "inner join isenexe on  isenexe.j47_codigo = iptuisen.j46_codigo ";
                    $sqlisencao .= "where j46_matric = {$oDadosMatricula->j23_matric} and ";
                    $sqlisencao .= "j47_anousu = {$this->iAno} and j46_perc > 0";
                } else {
                    $sqlisencao = "select * ";
                    $sqlisencao .= " from iptuisen ";
                    $sqlisencao .= "      inner join isenexe on  isenexe.j47_codigo = iptuisen.j46_codigo ";
                    $sqlisencao .= "      inner join isentaxa on isentaxa.j56_codigo = iptuisen.j46_codigo ";
                    $sqlisencao .= "where j46_matric = {$oDadosMatricula->j23_matric} ";
                    $sqlisencao .= "  and j47_anousu = {$this->iAno} ";
                    $sqlisencao .= "  and case when $k00_receit in ({$this->sListaReceitas}) ";
                    $sqlisencao .= "           then j56_receit in ({$this->sListaReceitas}) ";
                    $sqlisencao .= "           else j56_receit = $k00_receit end";
                }

                $resultisencao = \db_query($sqlisencao) or die($sqlisencao);

                if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                    if (pg_num_rows($resultisencao) == 0 || $k00_valor > 0) {
                        $this->oArquivoDados->fwrite(\db_formatar($k00_valor, 'f', ' ', 15));
                    } else {
                        $this->oArquivoDados->fwrite(str_pad("", 15, ' ', STR_PAD_LEFT));
                    }
                }
            }
        }

        if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
            $this->oArquivoDados->fwrite(str_pad($j37_outros, 40, ' ', STR_PAD_LEFT));
            $this->oArquivoDados->fwrite(str_pad($z01_cgmpri, 10));
            $this->oArquivoDados->fwrite(substr(str_pad($j23_areafr, 10), 0, 10));
            $this->oArquivoDados->fwrite(str_pad($this->oPrefeitura->getCep(), 8));
            $this->oArquivoDados->fwrite(str_pad(
                strtoupper($this->oPrefeitura->getMunicipio()),
                40,
                ' ',
                STR_PAD_RIGHT
            ));
            $this->oArquivoDados->fwrite(str_pad($this->oPrefeitura->getUf(), 2));

            $this->oArquivoDados->fwrite(str_pad($mensagemdebitosanosanteriores, 100, ' ', STR_PAD_RIGHT));
            $this->oArquivoDados->fwrite(substr(str_pad($z01_bairro, 40), 0, 40));
            $this->oArquivoDados->fwrite(substr(str_pad($j46_codigo, 10, "0", STR_PAD_LEFT), 0, 10));
            $this->oArquivoDados->fwrite(substr(str_pad($j46_tipo, 5, "0", STR_PAD_LEFT), 0, 5));
        }

        if ($lGerarLayout) {
            $this->oArquivoLayout->fwrite(static::dbContador(
                "FACEOUTROS",
                "OUTRAS INFORMACOES DA FACE",
                $contador,
                40
            ));
            $this->oArquivoLayout->fwrite(static::dbContador(
                "NUMCGMNOME",
                "CODIGO DO CGM DO NOME A SER IMPRESSO NO CARNE",
                $contador,
                10
            ));
            $this->oArquivoLayout->fwrite(static::dbContador(
                "FRACAODOLOTE",
                "FRACAO DO LOTE UTILIZADA NO CALCULO",
                $contador,
                10
            ));
            $this->oArquivoLayout->fwrite(static::dbContador("CEPDOIMOVEL", "CEP DO IMOVEL", $contador, 8));
            $this->oArquivoLayout->fwrite(static::dbContador("MUNICDOIMOVEL", "MUNICIPIO DO IMOVEL", $contador, 40));
            $this->oArquivoLayout->fwrite(static::dbContador("UFDOIMOVEL", "UF DO IMOVEL", $contador, 2));
            $this->oArquivoLayout->fwrite(static::dbContador(
                "MSGDEBANOSANT",
                "MENSAGEM CASO A MATRICULA TENHA DEBITOS EM ANOS ANTERIORES",
                $contador,
                100
            ));
            $this->oArquivoLayout->fwrite(static::dbContador(
                "BAIRRONOME",
                "BAIRRO DO CGM DO PROPRIETARIO",
                $contador,
                40
            ));
            $this->oArquivoLayout->fwrite(static::dbContador("CODISEN", "CODIGO DA ISENCAO", $contador, 10));
            $this->oArquivoLayout->fwrite(static::dbContador("TIPOISEN", "CODIGO DO TIPO DE ISENCAO", $contador, 5));
        }


        if ($lGerarLayout) {
            $sqlcalc = "  select j17_codhis,                                                                      ";
            $sqlcalc .= "         j17_descr                                                                        ";
            $sqlcalc .= "    from (select k02_codigo,                                                              ";
            $sqlcalc .= "                 k02_descr,                                                               ";
            $sqlcalc .= "                 j17_codhis,                                                              ";
            $sqlcalc .= "                 j17_descr,                                                               ";
            $sqlcalc .= "                 sum(j21_valor) as j21_valor,                                             ";
            $sqlcalc .= "                 sum(coalesce(j21_quant,0)) as j21_quant,                                 ";
            $sqlcalc .= "                 sum(case                                                                 ";
            $sqlcalc .= "                   when iptucalhconf.j89_codhis is not null then                          ";
            $sqlcalc .= "                     (select sum(x.j21_valor)                                             ";
            $sqlcalc .= "                       from iptucalv x                                                    ";
            $sqlcalc .= "                      where x.j21_anousu = iptucalv.j21_anousu                            ";
            $sqlcalc .= "                        and x.j21_matric = iptucalv.j21_matric                            ";
            $sqlcalc .= "                        and x.j21_receit = iptucalv.j21_receit                            ";
            $sqlcalc .= "                        and x.j21_codhis = iptucalhconf.j89_codhis)                       ";
            $sqlcalc .= "                   else 0                                                                 ";
            $sqlcalc .= "                 end) as j21_valorisen                                                    ";
            $sqlcalc .= "            from iptucalv                                                                 ";
            $sqlcalc .= "                 inner join iptucalh        on iptucalh.j17_codhis        = j21_codhis    ";
            $sqlcalc .= "                 left  join iptucalhconf    on iptucalhconf.j89_codhispai = j21_codhis    ";
            $sqlcalc .= "                 inner join tabrec          on tabrec.k02_codigo          = j21_receit    ";
            $sqlcalc .= "                 left  join iptucadtaxaexe  on iptucadtaxaexe.j08_tabrec  = j21_receit    ";
            $sqlcalc .= "                                           and iptucadtaxaexe.j08_anousu  = {$this->iAno} ";
            $sqlcalc .= "           where j21_anousu = {$this->iAno}                                               ";
            $sqlcalc .= "           group by k02_codigo, ";
            $sqlcalc .= "                    k02_descr,  ";
            $sqlcalc .= "                    j17_codhis, ";
            $sqlcalc .= "                    j17_descr  ";
            $sqlcalc .= "           order by iptucalh.j17_codhis ";
            $sqlcalc .= "        ) as x ";
            $sqlcalc .= " group by j17_codhis, ";
            $sqlcalc .= "          j17_descr  ";
            $sqlcalc .= " order by j17_codhis ";

            $cria_tab_sqlcalc = "create temp table {$this->sTabelaTemporariaTaxas2} as $sqlcalc";
            $resultcriacalc = \db_query($cria_tab_sqlcalc) or die($cria_tab_sqlcalc);

            $resultcalc = \db_query("select * from {$this->sTabelaTemporariaTaxas2}");

            if (pg_num_rows($resultcalc) > 0) {
                for ($iIndice = 0; $iIndice < pg_num_rows($resultcalc); $iIndice++) {
                    $oDadosTaxas = \db_utils::fieldsMemory($resultcalc, $iIndice);

                    $this->oArquivoLayout->fwrite(static::dbContador("DESCRTAXA" . str_pad(
                        $oDadosTaxas->j17_codhis,
                        3,
                        "0",
                        STR_PAD_LEFT
                    ), "DESCRICAO DA TAXA {$oDadosTaxas->j17_descr}", $contador, 40));
                    $this->oArquivoLayout->fwrite(static::dbContador("QUANTTAXA" . str_pad(
                        $oDadosTaxas->j17_codhis,
                        3,
                        "0",
                        STR_PAD_LEFT
                    ), "QUANTIDADE DA TAXA {$oDadosTaxas->j17_descr}", $contador, 10));
                    $this->oArquivoLayout->fwrite(static::dbContador("VALTAXA" . str_pad(
                        $oDadosTaxas->j17_codhis,
                        3,
                        "0",
                        STR_PAD_LEFT
                    ), "VALOR DA TAXA {$oDadosTaxas->j17_descr}", $contador, 18));
                    $this->oArquivoLayout->fwrite(static::dbContador(
                        "VALTAXAPARC" . str_pad(
                            $oDadosTaxas->j17_codhis,
                            3,
                            "0",
                            STR_PAD_LEFT
                        ),
                        "VALOR DA TAXA {$oDadosTaxas->j17_descr} PARA CADA PARCELA",
                        $contador,
                        18
                    ));
                }

                for ($iIndiceTaxa = $iIndice; $iIndiceTaxa < 10; $iIndiceTaxa++) {
                    $this->oArquivoLayout->fwrite(static::dbContador(
                        "BRANCOS",
                        "TAXA SEM USO {$iIndiceTaxa}",
                        $contador,
                        86
                    ));
                }
            } else {
                $this->oArquivoLayout->fwrite(static::dbContador("BRANCOS", "ESPACOS EM BRANCO", $contador, 63));
            }
        }


        $colunaTotal = " j21_quant, sum ( coalesce(j21_valor,0) ) as j21_valor";
        $colunaTotal .= ", sum ( abs( coalesce(j21_valorisen,0 ) ) ) as j21_valorisen, ";
        $groupby = " group by j17_codhis, j21_quant, j17_descr ";
        $whereMatric = " and j21_matric = {$oDadosMatricula->j23_matric} ";

        $sqlcalc = "  select j17_codhis,                                                                      ";
        $sqlcalc .= "         {$colunaTotal} ";
        $sqlcalc .= "         j17_descr                                                                        ";
        $sqlcalc .= "    from (select k02_codigo,                                                              ";
        $sqlcalc .= "                 k02_descr,                                                               ";
        $sqlcalc .= "                 j17_codhis,                                                              ";
        $sqlcalc .= "                 j17_descr,                                                               ";
        $sqlcalc .= "                 sum(j21_valor) as j21_valor,                                             ";
        $sqlcalc .= "                 sum(coalesce(j21_quant,0)) as j21_quant,                                 ";
        $sqlcalc .= "                 sum(case                                                                 ";
        $sqlcalc .= "                   when iptucalhconf.j89_codhis is not null then                          ";
        $sqlcalc .= "                     (select sum(x.j21_valor)                                             ";
        $sqlcalc .= "                       from iptucalv x                                                    ";
        $sqlcalc .= "                      where x.j21_anousu = iptucalv.j21_anousu                            ";
        $sqlcalc .= "                        and x.j21_matric = iptucalv.j21_matric                            ";
        $sqlcalc .= "                        and x.j21_receit = iptucalv.j21_receit                            ";
        $sqlcalc .= "                        and x.j21_codhis = iptucalhconf.j89_codhis)                       ";
        $sqlcalc .= "                   else 0                                                                 ";
        $sqlcalc .= "                 end) as j21_valorisen                                                    ";
        $sqlcalc .= "            from iptucalv                                                                 ";
        $sqlcalc .= "                 inner join iptucalh        on iptucalh.j17_codhis        = j21_codhis    ";
        $sqlcalc .= "                 left  join iptucalhconf    on iptucalhconf.j89_codhispai = j21_codhis    ";
        $sqlcalc .= "                 inner join tabrec          on tabrec.k02_codigo          = j21_receit    ";
        $sqlcalc .= "                 left  join iptucadtaxaexe  on iptucadtaxaexe.j08_tabrec  = j21_receit    ";
        $sqlcalc .= "                                           and iptucadtaxaexe.j08_anousu  = {$this->iAno} ";
        $sqlcalc .= "           where j21_anousu = {$this->iAno}                                               ";
        $sqlcalc .= "                 {$whereMatric}                                                           ";
        $sqlcalc .= "           group by k02_codigo, ";
        $sqlcalc .= "                    k02_descr,  ";
        $sqlcalc .= "                    j17_codhis, ";
        $sqlcalc .= "                    j17_descr  ";
        $sqlcalc .= "           order by iptucalh.j17_codhis ";
        $sqlcalc .= "        ) as x ";
        $sqlcalc .= $groupby;
        $sqlcalc .= " order by j17_codhis ";

        $w_iptucalv2 = "ww_iptucalv2_{$this->iAno}";

        $cria_tab_sqlcalc = "create temp table {$w_iptucalv2} as {$sqlcalc}";
        $resultcriacalc = \db_query($cria_tab_sqlcalc) or die($cria_tab_sqlcalc);

        $sqlcalc = "select {$this->sTabelaTemporariaTaxas2}.j17_codhis";
        $sqlcalc .= ", {$this->sTabelaTemporariaTaxas2}.j17_descr";
        $sqlcalc .= ", coalesce( (select j21_valor";
        $sqlcalc .= "               from $w_iptucalv2";
        $sqlcalc .= "              where $w_iptucalv2.j17_codhis = {$this->sTabelaTemporariaTaxas2}.j17_codhis )";
        $sqlcalc .= "          , 0) as j21_valor";
        $sqlcalc .= ", coalesce( (select j21_valorisen ";
        $sqlcalc .= "               from $w_iptucalv2 ";
        $sqlcalc .= "              where $w_iptucalv2.j17_codhis = {$this->sTabelaTemporariaTaxas2}.j17_codhis )";
        $sqlcalc .= "          , 0) as j21_valorisen ";
        $sqlcalc .= ", coalesce( (select j21_quant ";
        $sqlcalc .= "               from $w_iptucalv2 ";
        $sqlcalc .= "              where $w_iptucalv2.j17_codhis = {$this->sTabelaTemporariaTaxas2}.j17_codhis )";
        $sqlcalc .= "          ,0) as j21_quant ";
        $sqlcalc .= " from {$this->sTabelaTemporariaTaxas2} ";
        $sqlcalc .= "order by {$this->sTabelaTemporariaTaxas2}.j17_codhis";

        $resultcalc = \db_query($sqlcalc) or die("erro: " . $sqlcalc);

        $valorTaxaBomPgd = array(11 => 0, 12 => 0);
        $valorTotalDesconto = array();

        if (pg_num_rows($resultcalc) > 0) {
            for ($iIndice = 0; $iIndice < pg_num_rows($resultcalc); $iIndice++) {
                $oDadosTaxas = \db_utils::fieldsMemory($resultcalc, $iIndice);

                if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                    if ($oDadosTaxas->j21_valor == 0) {
                        $this->oArquivoDados->fwrite(str_pad(null, 40));
                        $this->oArquivoDados->fwrite(str_pad(\db_formatar(0, 'f', ' ', 10), "0", STR_PAD_LEFT));
                        $this->oArquivoDados->fwrite(str_pad("", 18, ' ', STR_PAD_LEFT));
                        $this->oArquivoDados->fwrite(str_pad("", 18, ' ', STR_PAD_LEFT));
                    } else {
                        $this->oArquivoDados->fwrite(str_pad($oDadosTaxas->j17_descr, 40));
                        $this->oArquivoDados->fwrite(str_pad(
                            \db_formatar($oDadosTaxas->j21_quant, 'f', ' ', 10),
                            "0",
                            STR_PAD_LEFT
                        ));
                        $this->oArquivoDados->fwrite(str_pad(
                            \db_formatar($oDadosTaxas->j21_valor, 'f', ' ', 18),
                            18,
                            ' ',
                            STR_PAD_LEFT
                        ));
                        $this->oArquivoDados->fwrite(str_pad(\db_formatar(
                            $oDadosTaxas->j21_valor / pg_num_rows($resultfin2),
                            'f',
                            ' ',
                            18
                        ), 18, ' ', STR_PAD_LEFT));

                        $valorTaxa = round($oDadosTaxas->j21_valor / pg_numrows($resultfin2), 2);
                        $numeroTotalParcelas = pg_numrows($resultfin2);

                        if ($oDadosTaxas->j17_codhis == 11 || $oDadosTaxas->j17_codhis == 12) {
                            if ($oDadosTaxas->j17_codhis == 11) {
                                $valorTaxaBomPgd[$oDadosTaxas->j17_codhis] += floatval($valorTaxa);
                                $valorTotalDesconto[] = $oDadosTaxas->j21_valor;
                            }

                            if ($oDadosTaxas->j17_codhis == 12) {
                                $valorTaxaBomPgd[$oDadosTaxas->j17_codhis] += floatval($valorTaxa);
                                $valorTotalDesconto[] = $oDadosTaxas->j21_valor;
                            }
                            //Analisar como implementar
                            $nTotalBomPagador += $oDadosTaxas->j21_valor;
                        }
                    }
                }
            }

            for ($iIndiceTaxa = $iIndice; $iIndiceTaxa < 10; $iIndiceTaxa++) {
                if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                    $this->oArquivoDados->fwrite(str_pad(' ', 86));
                }
            }
        } else {
            if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                $this->oArquivoDados->fwrite(str_repeat(" ", 63));
            }
        }

        // Convênio SICOB
        if ($this->oConvenio->getTipoConvenio() == Convenio::TIPO_CONVENIO_COMPENSACAO_SICOB) {
            $iTamCarteira = 2;
            $iTamCedente = 8;
            $iTamConvenio = 4;
            $iTamNossoNumero = 10;

            $iConvenio = ($this->oConvenio->getConvenioCobranca() == 0 ? ' ' : $this->oConvenio->getConvenioCobranca());

            $sAgencia = str_pad($this->oConvenio->getCodAgencia(), 5, "0", STR_PAD_LEFT);
            $sDigAgencia = str_pad($this->oConvenio->getDigAgencia(), 1, " ", STR_PAD_LEFT);
            $sOperacao = str_pad($this->oConvenio->getOperacao(), 3, "0", STR_PAD_LEFT);
            $sCedente = str_pad($this->oConvenio->getCedente(), $iTamCedente, "0", STR_PAD_LEFT);
            $sDigCedente = str_pad($this->oConvenio->getDigitoCedente(), 1, " ", STR_PAD_LEFT);
            $sCarteira = str_pad($this->oConvenio->getCarteira(), $iTamCarteira, " ", STR_PAD_LEFT);
            $sConvenio = str_pad($iConvenio, $iTamConvenio, " ", STR_PAD_LEFT);

            // Convênio BSJ,BDL
        } elseif (in_array(
            $this->oConvenio->getTipoConvenio(),
            array(Convenio::TIPO_CONVENIO_COMPENSACAO_BDL, Convenio::TIPO_CONVENIO_COMPENSACAO_BSJ)
        )) {
            $iTamCarteira = 6;

            $sCodigoCedente = $this->oConvenio->getCedente();

            /**
             * Caso seja Compensação DBL e BANRISUL
             */
            if ($this->oConvenio->getTipoConvenio() == Convenio::TIPO_CONVENIO_COMPENSACAO_BDL
              && $this->oConvenio->getCodBanco() == 41) {
                $sCodigoCedente = $this->oConvenio->getCodigoAgencia() . $sCodigoCedente;
            }

            if ($sCodigoCedente) {
                $iTamCedente = strlen($sCodigoCedente);
            } else {
                $iTamCedente = 7;
            }

            if ($this->oConvenio->getTipoConvenio() == Convenio::TIPO_CONVENIO_COMPENSACAO_BDL) {
                $iTamConvenio = 7;
                $iTamNossoNumero = 10;
            } else {
                $iTamConvenio = 4;
                $iTamNossoNumero = 13;
            }

            $iConvenio = ($this->oConvenio->getConvenioCobranca() == 0 ? ' ' : $this->oConvenio->getConvenioCobranca());
            $aCarteira = explode("-", $this->oConvenio->getCarteira());
            $aAgencia = explode("-", $this->oConvenio->getCodAgencia());

            $sAgencia = str_pad($aAgencia[0], 5, "0", STR_PAD_LEFT);
            $sDigAgencia = str_pad($aAgencia[1], 1, " ", STR_PAD_LEFT);
            $sOperacao = str_pad($this->oConvenio->getOperacao(), 3, "0", STR_PAD_LEFT);
            $sCedente = str_pad($sCodigoCedente, $iTamCedente, " ", STR_PAD_LEFT);
            $sDigCedente = str_pad($this->oConvenio->getDigitoCedente(), 1, " ", STR_PAD_LEFT);
            $sCarteira = str_pad($aCarteira[0], $iTamCarteira, " ", STR_PAD_LEFT);
            $sConvenio = str_pad($iConvenio, $iTamConvenio, " ", STR_PAD_LEFT);

            // Demais Convênios ARRECADAÇÃO, CAIXA PADRÃO etc.
        } else {
            $iTamCarteira           = 6;
            $iTamCedente            = 6;
            $iTamConvenio           = 4;
            $iTamNossoNumero        = 10;
            $iTamNossoNumeroVersao2 = 17;

            $aAgencia               = explode("-", $this->oConvenio->getCodAgencia());

            $sAgencia       = str_pad($aAgencia[0], 5, "0", STR_PAD_LEFT);
            $sDigAgencia    = str_pad(@$aAgencia[1], 1, " ", STR_PAD_LEFT);
            $sOperacao      = str_pad("", 3, " ", STR_PAD_LEFT);

            $sCodigoCedente = $this->oConvenio->getCedente();
            $sCedente       = str_pad($sCodigoCedente, $iTamCedente, " ", STR_PAD_LEFT);
            $sDigCedente    = str_pad($this->oConvenio->getDigitoCedente(), 1, " ", STR_PAD_LEFT);

            $sCarteira      = str_pad("", $iTamCarteira, " ", STR_PAD_LEFT);
            $sConvenio      = str_pad($this->oConvenio->getConvenioArrecadacao(), $iTamConvenio, " ", STR_PAD_LEFT);
        }

        if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {

            /**
             * Nosso numero para unica
             */
            for ($iUnica = 1; $iUnica <= count($this->aParcelasUnicas); $iUnica++) {
                if (isset($aListaNossoNumeroUnica[$iUnica])) {
                    $oNossoNumero = $aListaNossoNumeroUnica[$iUnica];
                    $this->oArquivoDados->fwrite(
                        str_pad($oNossoNumero->sNumero, $iTamNossoNumero, " ", STR_PAD_LEFT),
                        $iTamNossoNumero
                    );
                    $this->oArquivoDados->fwrite(str_pad($oNossoNumero->sDigito, 1, " ", STR_PAD_LEFT), 1);
                } else {
                    $this->oArquivoDados->fwrite(str_pad("", $iTamNossoNumero, " ", STR_PAD_LEFT), $iTamNossoNumero);
                    $this->oArquivoDados->fwrite(str_pad("", 1, " ", STR_PAD_LEFT), 1);
                }
            }

            for ($iParcela = 1; $iParcela <= $this->iParcelaMaxima; $iParcela++) {
                if (isset($aListaNossoNumero[$iParcela])) {
                    $oNossoNumero = $aListaNossoNumero[$iParcela];
                    $this->oArquivoDados->fwrite(
                        str_pad($oNossoNumero->sNumero, $iTamNossoNumero, " ", STR_PAD_LEFT),
                        $iTamNossoNumero
                    );
                    $this->oArquivoDados->fwrite(str_pad($oNossoNumero->sDigito, 1, " ", STR_PAD_LEFT), 1);
                } else {
                    $this->oArquivoDados->fwrite(str_pad("", $iTamNossoNumero, " ", STR_PAD_LEFT), $iTamNossoNumero);
                    $this->oArquivoDados->fwrite(str_pad("", 1, " ", STR_PAD_LEFT), 1);
                }
            }

            $this->oArquivoDados->fwrite(str_pad(\db_formatar($nTotalBomPagador, 'f', ' ', 18), 18, ' ', STR_PAD_LEFT));
            $this->oArquivoDados->fwrite($sAgencia, 5);
            $this->oArquivoDados->fwrite($sDigAgencia, 1);
            $this->oArquivoDados->fwrite($sOperacao, 3);
            $this->oArquivoDados->fwrite($sCedente, $iTamCedente);
            $this->oArquivoDados->fwrite($sDigCedente, 1);
            $this->oArquivoDados->fwrite($sCarteira, $iTamCarteira);
            $this->oArquivoDados->fwrite($sConvenio, $iTamConvenio);
            $this->oArquivoDados->fwrite($this->oEmissao->getData()->getDate(\DBDate::DATA_PTBR), 10);
            $this->oArquivoDados->fwrite(str_pad($this->oConvenio->getNomeConvenio(), 50, " ", STR_PAD_RIGHT), 50);

            for ($iUnica = 1; $iUnica <= count($this->aParcelasUnicas); $iUnica++) {
                if (isset($aListaNossoNumeroUnica[$iUnica])) {
                    $oNossoNumero = $aListaNossoNumeroUnica[$iUnica];
                    $this->oArquivoDados->fwrite(str_pad(
                        str_replace("/", "", $oNossoNumero->sNumero),
                        $iTamNossoNumeroVersao2,
                        " ",
                        STR_PAD_LEFT
                    ), $iTamNossoNumeroVersao2);
                    $this->oArquivoDados->fwrite(str_pad($oNossoNumero->sDigito, 1, " ", STR_PAD_LEFT), 1);
                } else {
                    $this->oArquivoDados->fwrite(
                        str_pad("", $iTamNossoNumeroVersao2, " ", STR_PAD_LEFT),
                        $iTamNossoNumeroVersao2
                    );
                    $this->oArquivoDados->fwrite(str_pad("", 1, " ", STR_PAD_LEFT), 1);
                }
            }

            for ($iParcela = 1; $iParcela <= $this->iParcelaMaxima; $iParcela++) {
                if (isset($aListaNossoNumero[$iParcela])) {
                    $oNossoNumero = $aListaNossoNumero[$iParcela];
                    $this->oArquivoDados->fwrite(str_pad(
                        str_replace("/", "", $oNossoNumero->sNumero),
                        $iTamNossoNumeroVersao2,
                        " ",
                        STR_PAD_LEFT
                    ), $iTamNossoNumeroVersao2);
                    $this->oArquivoDados->fwrite(str_pad($oNossoNumero->sDigito, 1, " ", STR_PAD_LEFT), 1);
                } else {
                    $this->oArquivoDados->fwrite(
                        str_pad("", $iTamNossoNumeroVersao2, " ", STR_PAD_LEFT),
                        $iTamNossoNumeroVersao2
                    );
                    $this->oArquivoDados->fwrite(str_pad("", 1, " ", STR_PAD_LEFT), 1);
                }
            }
        }

        unset($aListaNossoNumero);
        unset($aListaNossoNumero, $aListaNossoNumeroUnica);

        if ($lGerarLayout) {
            for ($iUnica = 1; $iUnica <= count($this->aParcelasUnicas); $iUnica++) {
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "NOSSO_NUMERO_UNICA{$iUnica}",
                    "NOSSO NUMERO UNICA {$iUnica}",
                    $contador,
                    $iTamNossoNumero
                ));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "DG_NOSSO_NUMERO_UNICA{$iUnica}",
                    "DIGITO DO NOSSO NUMERO UNICA {$iUnica}",
                    $contador,
                    1
                ));
            }

            for ($iParcela = 1; $iParcela <= $this->iParcelaMaxima; $iParcela++) {
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "NOSSO_NUMERO_PARC{$iParcela}",
                    "NOSSO NUMERO PARCELA {$iParcela}",
                    $contador,
                    $iTamNossoNumero
                ));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "DG_NOSSO_NUMERO_PARC{$iParcela}",
                    "DIGITO DO NOSSO NUMERO PARCELA {$iParcela}",
                    $contador,
                    1
                ));
            }

            $this->oArquivoLayout->fwrite(static::dbContador(
                "VALTOTALBOMPAGADOR",
                "VALOR TOTAL DO BOM PAGADOR",
                $contador,
                18
            ));
            $this->oArquivoLayout->fwrite(static::dbContador("AGENCIA", "AGENCIA DO CONVENIO", $contador, 5));
            $this->oArquivoLayout->fwrite(static::dbContador("DG_AGENCIA", "DIGITO DA AGENCIA", $contador, 1));
            $this->oArquivoLayout->fwrite(static::dbContador("OPERACAO", "OPERACAO DO CONVENIO", $contador, 3));
            $this->oArquivoLayout->fwrite(static::dbContador(
                "CEDENTE",
                "CEDENTE DO CONVENIO",
                $contador,
                $iTamCedente
            ));
            $this->oArquivoLayout->fwrite(static::dbContador("DG_CEDENTE", "DIGITO DO CEDENTE", $contador, 1));
            $this->oArquivoLayout->fwrite(static::dbContador(
                "CARTEIRA",
                "CARTEIRA DO CONVENIO",
                $contador,
                $iTamCarteira
            ));
            $this->oArquivoLayout->fwrite(static::dbContador("CONVENIO", "CONVENIO", $contador, $iTamConvenio));
            $this->oArquivoLayout->fwrite(static::dbContador(
                "DATA_PROCESSAMENTO",
                "DATA DO PROCESSAMENTO",
                $contador,
                10
            ));
            $this->oArquivoLayout->fwrite(static::dbContador(
                "DESCRICAO_CONVENIO",
                "DESCRICAO DO CONVENIO",
                $contador,
                50
            ));

            for ($iUnica = 1; $iUnica <= count($this->aParcelasUnicas); $iUnica++) {
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "NOSSO_NUMERO_VERSAO2_UNICA{$iUnica}",
                    "NOSSO NUMERO VERSAO 2 UNICA {$iUnica}",
                    $contador,
                    $iTamNossoNumeroVersao2
                ));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "DG_NOSSO_NUMERO_VERSAO2_UNICA{$iUnica}",
                    "DIGITO DO NOSSO NUMERO VERSAO 2 UNICA {$iUnica}",
                    $contador,
                    1
                ));
            }

            for ($iParcela = 1; $iParcela <= $this->iParcelaMaxima; $iParcela++) {
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "NOSSO_NUMERO_VERSAO2_PARC{$iParcela}",
                    "NOSSO NUMERO VERSAO 2 PARCELA {$iParcela}",
                    $contador,
                    $iTamNossoNumeroVersao2
                ));
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "DG_NOSSO_NUMERO_VERSAO2_PARC{$iParcela}",
                    "DIGITO DO NOSSO NUMERO VERSAO 2 PARCELA {$iParcela}",
                    $contador,
                    1
                ));
            }
        }

        if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
            $this->oArquivoDados->fwrite(str_pad($j05_codigoproprio, 10));
            $this->oArquivoDados->fwrite(str_pad($j06_setorloc, 10));
            $this->oArquivoDados->fwrite(str_pad(substr($j05_descr, 0, 40), 40));
            $this->oArquivoDados->fwrite(str_pad($j06_quadraloc, 10));
            $this->oArquivoDados->fwrite(str_pad($j06_lote, 10));
        }

        if ($lGerarLayout) {
            $this->oArquivoLayout->fwrite(static::dbContador(
                "SEQUENCIALSETORLOCALIZACAO",
                "SEQUENCIAL DO SETOR DE LOCALIZACAO",
                $contador,
                10
            ));
            $this->oArquivoLayout->fwrite(static::dbContador(
                "CODPROPRIOSETORLOCALIZACAO",
                "CODIGO PROPRIO DO SETOR DE LOCALIZACAO",
                $contador,
                10
            ));
            $this->oArquivoLayout->fwrite(static::dbContador(
                "DESCRSETORLOCALIZACAO",
                "DESCRICAO DO SETOR DE LOCALIZACAO",
                $contador,
                40
            ));
            $this->oArquivoLayout->fwrite(static::dbContador(
                "QUADRALOCALIZACAO",
                "QUADRA DE LOCALIZACAO",
                $contador,
                10
            ));
            $this->oArquivoLayout->fwrite(static::dbContador("LOTELOCALIZACAO", "LOTE DE LOCALIZACAO", $contador, 10));
        }

        /**
         * Melhoria na emissao para gerar parcelas e unicas opcionais de vencimentos, aplicando correção
         */
        if ($this->lOpcaoVencimento) {
            if (count($this->aParcelasUnicas)) {
                $aUnicasComparativo = array();

                foreach ($this->aParcelasUnicas as $oParcelaUnica) {
                    $oUnicasComparativo = new \stdClass();
                    $oUnicasComparativo->vencimento = $oParcelaUnica->getDataVencimento()->getDate(\DBDate::DATA_PTBR);
                    $oUnicasComparativo->lancamento = $oParcelaUnica->getDataOperacao()->getDate(\DBDate::DATA_PTBR);
                    $oUnicasComparativo->percentual = $oParcelaUnica->getPercentual();
                    $iIndiceComparativo = explode("/", $oUnicasComparativo->vencimento);
                    $aUnicasComparativo[(int)$iIndiceComparativo[1]] = $oUnicasComparativo;
                }
                ///  totais de UNICAS

                foreach ($this->aParcelasUnicas as $iUnicas => $oParcelaUnica) {
                    $iIndicadorOpcao = 1;

                    /*
           * Montamos um objeto com dados das unicas
           * vencimento
           * lancamento
           * percentual
           */
                    $oDadosUnica = new \stdClass();
                    $oDadosUnica->vencimento = $oParcelaUnica->getDataVencimento()->getDate(\DBDate::DATA_PTBR);
                    $oDadosUnica->lancamento = $oParcelaUnica->getDataOperacao()->getDate(\DBDate::DATA_PTBR);
                    $oDadosUnica->percentual = $oParcelaUnica->getPercentual();

                    /*
           * Definimos o mes do vencimento,
           * para iniciar no for das unicas, para que na unica do mes 3
           * nao apareca a opcao do mes 2 e do 1
           */

                    $iMesVencimento = explode("/", $oDadosUnica->vencimento);
                    $aVencimento = $iMesVencimento;
                    $iProximoDiaUnica = $iMesVencimento[0];
                    $iMesVencimento = $iMesVencimento[1];
                    $iOpcaoUnicas = $iUnicas + 1;

                    $iProximoMesUnica = (int)$iMesVencimento;

                    $nPercentualUnica = 0;

                    if (isset($aUnicasComparativo[$iUnicas + 1])) {
                        $oDadosComparativo = $aUnicasComparativo[$iUnicas + 1];
                        $nPercentualUnica = $oDadosComparativo->percentual / 100;
                    }
                    for ($iMes = $iMesVencimento; $iMes <= 12; $iMes++) {
                        $iProximoAnoUnica = $aVencimento[2];
                        if ((int)$iProximoMesUnica > 12) {
                            $iProximoMesUnica = 1;
                            $iProximoAnoUnica = $iProximoAnoUnica + 1;
                        }

                        $iDiaVencUnica = static::getUltimoDiaMes($iProximoMesUnica, $iProximoAnoUnica, "friday");

                        $sDataVencimentoUnica = "{$iDiaVencUnica}/" . str_pad($iProximoMesUnica, 2, "0", STR_PAD_LEFT)
                          . "/{$iProximoAnoUnica}";

                        if (isset($aUnicasComparativo[(int)$iMes])) {
                            $oTeste = $aUnicasComparativo[(int)$iMes];
                            $sDataVencimentoUnica = $oTeste->vencimento;
                        }

                        $dtVencimentoUnicaCorrecao = implode("-", array_reverse(explode("/", $sDataVencimentoUnica)));

                        /*
                         * aplicamos debitos numpre para o valor corrigido da unica
                         */
                        $sSqlCorrecao = "select k00_numpre         as numpre,                                   \n";
                        $sSqlCorrecao .= "       sum(vlr_historico) as historico,                               \n";
                        $sSqlCorrecao .= "       sum(vlr_corrigido) as corrigido,                               \n";
                        $sSqlCorrecao .= "       sum(vlr_juros)     as juros,                                   \n";
                        $sSqlCorrecao .= "       sum(vlr_multa)     as multa,                                   \n";
                        $sSqlCorrecao .= "       sum(vlr_desconto)  as desconto,                                \n";
                        $sSqlCorrecao .= "       sum(vlr_total)     as total                                    \n";
                        $sSqlCorrecao .= "  from (select distinct                                               \n";
                        $sSqlCorrecao .= "               k00_numpre,                                            \n";
                        $sSqlCorrecao .= "               substr( fc_calcula, 2 , 13)::float8  as vlr_historico, \n";
                        $sSqlCorrecao .= "               substr( fc_calcula, 15, 13)::float8  as vlr_corrigido, \n";
                        $sSqlCorrecao .= "               substr( fc_calcula, 28, 13)::float8  as vlr_juros,     \n";
                        $sSqlCorrecao .= "               substr( fc_calcula, 41, 13)::float8  as vlr_multa,     \n";
                        $sSqlCorrecao .= "               substr( fc_calcula, 54, 13)::float8  as vlr_desconto,  \n";
                        $sSqlCorrecao .= "               (substr(fc_calcula, 15, 13)::float8+                   \n";
                        $sSqlCorrecao .= "               substr( fc_calcula, 28, 13)::float8+                   \n";
                        $sSqlCorrecao .= "               substr( fc_calcula, 41, 13)::float8-                   \n";
                        $sSqlCorrecao .= "               substr( fc_calcula, 54, 13)::float8) as vlr_total      \n";
                        $sSqlCorrecao .= "          from (select k00_numpre,                                    \n";
                        $sSqlCorrecao .= "                       fc_calcula(k00_numpre,                         \n";
                        $sSqlCorrecao .= "                                  0,                                  \n";
                        $sSqlCorrecao .= "                                  0,                                  \n";
                        $sSqlCorrecao .= "                                  '{$dtVencimentoUnicaCorrecao}',     \n";
                        $sSqlCorrecao .= "                                  '{$dtVencimentoUnicaCorrecao}',     \n";
                        $sSqlCorrecao .= "                                  " . $this->iAno . "                 \n";
                        $sSqlCorrecao .= "                       )                                              \n";
                        $sSqlCorrecao .= " from ( select distinct k00_numpre                                    \n";
                        $sSqlCorrecao .= "          from arrecad                                                \n";
                        $sSqlCorrecao .= "         where k00_numpre={$oDadosMatricula->j20_numpre} ) as x       \n";
                        $sSqlCorrecao .= "               ) as arrecad                                           \n";
                        $sSqlCorrecao .= "      ) as total_unica                                                \n";
                        $sSqlCorrecao .= "group by k00_numpre                                                   \n";
                        $rsDadosUnica = \db_query($sSqlCorrecao);
                        $oValUnica = \db_utils::fieldsMemory($rsDadosUnica, 0);
                        $nTotalUnica = $oValUnica->total + $this->nTaxaBancaria;

                        if (isset($aUnicasComparativo[(int)$iMes])) {
                            $oUnica = $aUnicasComparativo[(int)$iMes];
                            $sDataVencimentoUnica = $oUnica->vencimento;
                        }

                        if ($lGerarLayout) {
                            $this->oArquivoLayout->fwrite(static::dbContador(
                                "VCTO_OPCAO_{$iIndicadorOpcao}_QUOTA_{$iOpcaoUnicas}",
                                "OPCAO DE VENC. {$iIndicadorOpcao} DA QUOTA ÚNICA {$iOpcaoUnicas}",
                                $contador,
                                10
                            ));
                            $this->oArquivoLayout->fwrite(static::dbContador(
                                "VALOR_{$iIndicadorOpcao}_QUOTA_{$iOpcaoUnicas}",
                                "Valor Opçao {$iIndicadorOpcao} da Quota ÚNICA {$iOpcaoUnicas}",
                                $contador,
                                15
                            ));
                            $iIndicadorOpcao++;
                        }

                        $this->oArquivoDados->fwrite($sDataVencimentoUnica);
                        $this->oArquivoDados->fwrite(str_pad(
                            trim(\db_formatar($nTotalUnica, "f")),
                            15,
                            ' ',
                            STR_PAD_LEFT
                        ));

                        $iProximoMesUnica++;
                    }
                }
            }

            /// FOEREACH PARA TOTAL PARCELAS
            $sDataVencParcela = reset($aDadosParcelas)->k00_dtpaga;
            $iTotalParcelas = end($aRecibos['parcelas'])->getParcela();
            $iMesPrimeiraParcela = explode("-", $sDataVencParcela);
            $iMesPrimeiraParcela = $iMesPrimeiraParcela[1];
            $iContadorParcela = 0;
            for ($iMesParcela = 1; $iMesParcela <= $iTotalParcelas; $iMesParcela++) {
                if (!isset($aDadosParcelas[$iMesParcela])) {
                    continue;
                }

                $iIndice = $iMesParcela - 1;
                $oParcela = $aDadosParcelas[$iMesParcela];//dados das parcelas tipo, numpre, valor etc.
                $iProximoMesParcela = explode("-", $oParcela->k00_dtpaga); // MES VENCIMENTO DA PARCELA
                $iProximoDiaParcela = $iProximoMesParcela[2];
                $iProximoMesParcela = $iProximoMesParcela[1];

                $contador = 12 - $iIndice;
                for ($iMesParcelaOpcao = $iMesPrimeiraParcela; $iMesParcelaOpcao <= $contador; $iMesParcelaOpcao++) {
                    $iContadorParcela++;
                    $sSqlVencimento = "select extract (year from k00_dtvenc) as anoVencimento ";
                    $sSqlVencimento .= " from arrecad ";
                    $sSqlVencimento .= "where k00_numpre = {$oDadosMatricula->j20_numpre}";
                    $sSqlVencimento .= "  and k00_numpar = {$iMesParcela}";
                    $rsAnoVenc = \db_query($sSqlVencimento);
                    $aAnoVenc = \db_utils::fieldsMemory($rsAnoVenc, 0);
                    $iAnoVencimento = $aAnoVenc->anovencimento;
                    $iProximoAnoParcela = $iAnoVencimento;

                    if ($iProximoMesParcela > 12) {
                        $iProximoMesParcela = 1;
                        $iProximoAnoParcela = $iProximoAnoParcela + 1;
                    }

                    $iDiaVenc = static::getUltimoDiaMes($iProximoMesParcela, $iProximoAnoParcela, "friday");
                    if ($iMesParcelaOpcao == $iMesPrimeiraParcela) {
                        $iDiaVenc = $iProximoDiaParcela;
                    }

                    $sDataVencimentoParcela = "{$iDiaVenc}/" . str_pad(
                        $iProximoMesParcela,
                        2,
                        "0",
                        STR_PAD_LEFT
                    ) . "/{$iProximoAnoParcela}";
                    $dtVencimentoParcCorrecao = implode("-", array_reverse(explode("/", $sDataVencimentoParcela)));
                    $rsDadosParcela = \debitos_numpre(
                        $oDadosMatricula->j20_numpre,
                        0,
                        0,
                        strtotime($dtVencimentoParcCorrecao),
                        $this->iAno,
                        $iMesParcela,
                        'k00_numpre, k00_numpar'
                    );

                    $aValoresParcela = \db_utils::getCollectionByRecord($rsDadosParcela);

                    $nMulta = 0;
                    $nJuros = 0;
                    foreach ($aValoresParcela as $indTotalParcela => $oValorTotalParcela) {
                        $nTotalParcela = $oValorTotalParcela->total + $this->nTaxaBancaria;
                        $nMulta = $oValorTotalParcela->vlrmulta;
                        $nJuros = $oValorTotalParcela->vlrjuros;
                    }

                    if ($lGerarLayout) {
                        $this->oArquivoLayout->fwrite(static::dbContador(
                            "VCTO_OPCAO_{$iContadorParcela}_PARCELA_{$iMesParcela}",
                            "OPCAO DE VENC. {$iMesParcelaOpcao} DA COTA ÚNICA {$iMesParcela}",
                            $contador,
                            10
                        ));
                        $this->oArquivoLayout->fwrite(static::dbContador(
                            "VALOR_{$iContadorParcela}_PARCELA_{$iMesParcela}",
                            "VALOR OPCAO {$iMesParcelaOpcao} DA PARCELA  {$iMesParcela}",
                            $contador,
                            15
                        ));
                    }

                    $this->oArquivoDados->fwrite($sDataVencimentoParcela);
                    $this->oArquivoDados->fwrite(str_pad(
                        trim(\db_formatar($nTotalParcela, "f")),
                        15,
                        ' ',
                        STR_PAD_LEFT
                    ));

                    $iProximoMesParcela++;
                }
            }
        }

        if (!empty($unicasTaxaExpediente)) {
            foreach ($unicasTaxaExpediente as $unicaTaxaExpediente) {
                $tamanhoReceita = 7;
                $tamanhoValor = 10;

                if ($lGerarLayout) {
                    $this->oArquivoLayout->fwrite(
                        static::dbContador(
                            "RECUNICATAXAEXPED",
                            "RECEITA {$this->receitaTaxaExpediente} UNICA DA TAXA DE EXPEDIENTE",
                            $contador,
                            $tamanhoReceita
                        )
                    );

                    $this->oArquivoLayout->fwrite(
                        static::dbContador(
                            "VALRECUNICATAXAEXPED",
                            "VALOR RECEITA {$this->receitaTaxaExpediente} UNICA DA TAXA DE EXPEDIENTE",
                            $contador,
                            $tamanhoValor
                        )
                    );
                }

                $valor = trim(db_formatar($unicaTaxaExpediente, 'f'));

                $this->oArquivoDados->fwrite(str_pad($this->receitaTaxaExpediente, $tamanhoReceita, '0', STR_PAD_LEFT));
                $this->oArquivoDados->fwrite(str_pad($valor, $tamanhoValor, ' ', STR_PAD_LEFT));
            }
        }

        if (!empty($this->receitaTaxaExpediente)) {
            foreach ($parcelasTaxaExpediente as $parcela => $parcelaTaxa) {
                $tamanhoParcela = 2;
                $tamanhoReceita = 7;
                $tamanhoValor = 10;

                if ($lGerarLayout) {
                    $this->oArquivoLayout->fwrite(
                        static::dbContador(
                            "PARCTAXAEXPED" . str_pad($parcela, $tamanhoParcela, '0', STR_PAD_LEFT),
                            "PARCELA {$parcela} DA TAXA DE EXPEDIENTE",
                            $contador,
                            $tamanhoParcela
                        )
                    );

                    $this->oArquivoLayout->fwrite(
                        static::dbContador(
                            "RECPARCTAXAEXPED" . str_pad($parcela, $tamanhoParcela, '0', STR_PAD_LEFT),
                            "RECEITA {$this->receitaTaxaExpediente} - PARCELA {$parcela} DA TAXA DE EXPEDIENTE",
                            $contador,
                            $tamanhoReceita
                        )
                    );

                    $valorReceita = "VALOR DA RECEITA {$this->receitaTaxaExpediente}";
                    $valorReceita .= " - PARCELA {$parcela} DA TAXA DE EXPEDIENTE";

                    $this->oArquivoLayout->fwrite(
                        static::dbContador(
                            "VALRECPARCTAXAEXPED" . str_pad($parcela, $tamanhoParcela, '0', STR_PAD_LEFT),
                            $valorReceita,
                            $contador,
                            $tamanhoValor
                        )
                    );
                }

                $valor = trim(db_formatar($parcelaTaxa, 'f'));

                $this->oArquivoDados->fwrite(str_pad($parcela, $tamanhoParcela, '0', STR_PAD_LEFT));
                $this->oArquivoDados->fwrite(str_pad($this->receitaTaxaExpediente, $tamanhoReceita, '0', STR_PAD_LEFT));
                $this->oArquivoDados->fwrite(str_pad($valor, $tamanhoValor, ' ', STR_PAD_LEFT));
            }
        }

        $valorTotalTaxa = abs(array_sum($valorTaxaBomPgd));

        $diferencaDescontoBomPagador = round(abs(array_sum($valorTotalDesconto)) -
        ($valorTotalTaxa * $numeroTotalParcelas), 2);

        foreach ($valorParcela as $key => $parcela) {
                        /*
                            VALOR TOTAL PARCELA             = $parcela + $taxa;
                            VALOR DA PARCELA COM O DESCONTO = $parcela;
                        */

            if (!(floatval($parcela) > 0)) {
                $valorTotalTaxa = 0;
            }

            if ($key == $numeroTotalParcelas) {
                $valorTotalTaxa = $valorTotalTaxa + $diferencaDescontoBomPagador;
            }

            if ($lGerarLayout) {
                $this->oArquivoLayout->fwrite(
                    static::dbContador(
                        "VALORTOTPARC_{$key}",
                        "VALOR TOTAL DA PARCELA {$key}",
                        $contador,
                        15
                    )
                );

                $this->oArquivoLayout->fwrite(
                    static::dbContador(
                        "VALORTOTALDESCBOMPAGPARC_{$key}",
                        "VALOR TOTAL DO DESCONTO DE BOM PAGADOR NA PARCELA {$key}",
                        $contador,
                        15
                    )
                );

                $this->oArquivoLayout->fwrite(
                    static::dbContador(
                        "VALORBOMPAGDESCPARC_{$key}",
                        "VALOR DA PARCELA {$key} COM DESCONTO DE BOM PAGADOR",
                        $contador,
                        15
                    )
                );
            }

            if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                $this->oArquivoDados->fwrite(\db_formatar(($parcela + $valorTotalTaxa), 'f', ' ', 15));
                $this->oArquivoDados->fwrite(\db_formatar($valorTotalTaxa, 'f', ' ', 15));
                $this->oArquivoDados->fwrite(\db_formatar($parcela, 'f', ' ', 15));
            }
        }

        $sSqlCalcValorIptuExec = "EXECUTE sSqlCalcValorIptu({$this->iAno}, {$oDadosMatricula->j23_matric})";
        $resultcalciptu = \db_query($sSqlCalcValorIptuExec) or die($sSqlCalcValorIptuExec);

        if (pg_num_rows($resultcalciptu) == 0) {
            $total_valor_iptu = 0;
        } else {
            extract((array)\db_utils::fieldsMemory($resultcalciptu, 0, true));
        }

        if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
            $this->oArquivoDados->fwrite(\db_formatar($total_valor_iptu, 'f', ' ', 15));
        }

        if ($lGerarLayout) {
            if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
                $this->oArquivoLayout->fwrite(static::dbContador(
                    "TOTVALORIPTU",
                    "TOTAL DOS VALORES LANCADOS (IMPOSTO/TAXAS/DESCONTOS)",
                    $contador,
                    15
                ));
            }
        }

        if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT && $this->gerarPix) {
            foreach ($aQrCodesPix as $oQrCodePix) {
                $this->oArquivoDados->fwrite(str_pad($oQrCodePix->qrCode, 300));

                if ($oQrCodePix->isUnica) {
                    $apelido = "QRCODEPIXUNICA{$oQrCodePix->percentual}";
                    $expressao = "QRCODE DO PIX DA PARCELA UNICA DE {$oQrCodePix->percentual}%";
                } else {
                    $apelido = "QRCODEPIXPARCELA".str_pad($oQrCodePix->parcela, 2);
                    $expressao = "QRCODE DO PIX DA PARCELA ".str_pad($oQrCodePix->parcela, 2);
                }

                if ($lGerarLayout) {
                    $this->oArquivoLayout->fwrite(static::dbContador(
                        $apelido,
                        $expressao,
                        $contador,
                        300
                    ));
                }
            }
        }

        $cria_tab_sqlcalc = "drop table {$w_iptucalv2}";
        $resultcriacalc = \db_query($cria_tab_sqlcalc) or die($cria_tab_sqlcalc);

        if ($this->sTipoGeracao == static::TIPO_EMISSAO_TXT) {
            $this->oArquivoDados->fwrite("{$this->sQuebraLinha}");
        }
    }

    /**
     * @todo A emissão do TXTBSJ não foi corrigida, e não esta funcionando.
     */
    /**
     * @param EmissaoGeral $oEmissao
     * @param integer $iQuantidadeRegistrosArquivo - Quantidade de registros a imprimir no arquivo
     * @param boolean $lMensagemAnosAnteriores - Se deve exibir mensagem para débitos de anos anteriores
     * @param string $tipo - Tipo de emissão (TXT | TXTBSJ)
     * @param boolean $lOpcaoVencimento - Se deve gerar opções de vencimento adicionais para parcelas e únicas
     * @param callback $callbackAtualiza
     * @return array
     * @throws \DBException
     * @throws \Exception
     */
    public function gerar(
        EmissaoGeral $oEmissao,
        $iQuantidadeRegistrosArquivo,
        $lMensagemAnosAnteriores,
        $tipo,
        $lOpcaoVencimento,
        $callbackAtualiza,
        $gerarPix
    ) {

        if ($tipo == static::TIPO_EMISSAO_TXTBSJ) {
            throw new \Exception("Erro ao processar o arquivo.");
        }

        $iQuantidadeRegistrosArquivo = (int)$iQuantidadeRegistrosArquivo;

        static::prepareQuery();

        /**
         * Busca osregistros da emissão geral
         */
        $oRegistroRepository = new RegistroRepository();
        $oRegistroCollection = $oRegistroRepository->getCollection($oEmissao);

        /**
         * Busca as informações de parcela unica
         */
        $oParcelaUnicaRepository = new ParcelaUnicaRepository();
        $this->aParcelasUnicas = $oParcelaUnicaRepository->getParcelas($oEmissao);

        /**
         * Carrega os parametros selecionados no momento da geração
         */
        $oParametrosGeracao = $oEmissao->getParametros();

        $this->iAno = $oParametrosGeracao->ano;
        $this->sTipoGeracao = $tipo;
        $this->iParcelaMaxima = 12;
        $this->sQuebraLinha = "\r\n";
        $this->sTabelaTemporariaTaxas = "w_iptucalv_{$this->iAno}";
        $this->sTabelaTemporariaTaxas2 = "ww_iptucalv_{$this->iAno}";
        ;
        $this->nTaxaBancaria = $oParametrosGeracao->nTaxaBancaria;
        $this->lMensagemAnosAnteriores = $lMensagemAnosAnteriores;
        $this->lOpcaoVencimento = $lOpcaoVencimento;
        $this->oPrefeitura = new \Instituicao();
        $this->oEmissao = $oEmissao;

        $this->oPrefeitura->getDadosPrefeitura();

        $this->nValorJuros = 0;
        $this->nValorJurosFaixa = 0;

        $this->gerarPix = $gerarPix;

        /**
         * Busca as informações de juros para o ano
         */
        $sSqlJurosExec = "EXECUTE sSqlJuros({$this->iAno})";
        $resultjuros = \db_query($sSqlJurosExec) or die($sSqlJurosExec);

        if (pg_num_rows($resultjuros) != 0) {
            $oDadosJuros = \db_utils::fieldsMemory($resultjuros, 0);

            $this->nValorJuros = $oDadosJuros->k02_juros;
            $this->nValorJurosFaixa = $oDadosJuros->k140_faixa;
        }

        /**
         * Buscar as informações do convênio
         */
        $this->oConvenio = new Convenio($oEmissao->getConvenio());
        $this->lConvenioCobranca = ($this->oConvenio->getModalidadeConvenio() == Convenio::MODALIDADE_COBRANCA);

        /**
         * Instancia os arquivos a serem gerados
         */
        $maximoParcelas = empty($oParametrosGeracao->maximo_parcelas_gerar) ? "" : "_quantparc_";
        $padMaximoParcelas = str_pad($oParametrosGeracao->maximo_parcelas_gerar, 3, "0", STR_PAD_LEFT);

        $sNomeArquivo = "tmp/dados_iptu_";
        $sNomeArquivo .= $oParametrosGeracao->filtro_principal;
        $sNomeArquivo .= $maximoParcelas . $padMaximoParcelas . "_" . $this->iAno . "_";
        $sNomeArquivo .= $oEmissao->getData()->getDate() . '_' . time() . ".txt";

        $sNomeLayout = "tmp/layout_iptu_";
        $sNomeLayout .= $oParametrosGeracao->filtro_principal;
        $sNomeLayout .= $maximoParcelas . $padMaximoParcelas . "_" . $this->iAno . "_";
        $sNomeLayout .= $oEmissao->getData()->getDate() . '_' . time() . ".txt";

        $this->oArquivoDados = new \SplFileObject($sNomeArquivo, "w+");
        $this->oArquivoLayout = new \SplFileObject($sNomeLayout, "w+");

        $aArquivosRetorno = array(
          new \File($this->oArquivoLayout->getPathname()),
          new \File($this->oArquivoDados->getPathname())
        );

        /**
         * Busca as receitas
         */
        $sSqlReceitas = "select j18_rterri as j18_receit from cfiptu where j18_anousu = {$this->iAno} ";
        $sSqlReceitas .= "union ";
        $sSqlReceitas .= "select j18_rpredi as j18_receit from cfiptu where j18_anousu = {$this->iAno} ";
        $sSqlReceitas .= "union ";
        $sSqlReceitas .= "select distinct j23_recdst as j18_receit ";
        $sSqlReceitas .= "  from iptucalcconfrec where j23_anousu = {$this->iAno} and j23_tipo = 1";

        $rsReceitas = \db_query($sSqlReceitas);
        $iLinhasRec = pg_num_rows($rsReceitas);

        $this->iReceitaMin = "";
        $this->sListaReceitas = "";

        if ($iLinhasRec > 0) {
            $aRec = array();
            for ($indx = 0; $indx < $iLinhasRec; $indx++) {
                $aRec[] = pg_result($rsReceitas, $indx, "j18_receit");
            }

            $this->iReceitaMin = min($aRec);
            $this->sListaReceitas = implode(",", $aRec);
        } else {
            $this->sListaReceitas = "null";
        }

        $sqlarrecadrecgeral = "select * from ( ";
        $sqlarrecadrecgeral .= "    select distinct case when arrecad.k00_receit in ({$this->sListaReceitas}) ";
        $sqlarrecadrecgeral .= "                         then {$this->iReceitaMin} ";
        $sqlarrecadrecgeral .= "                         else arrecad.k00_receit end as k00_receit ";
        $sqlarrecadrecgeral .= "    from iptunump ";
        $sqlarrecadrecgeral .= "    inner join arrematric on j20_numpre = arrematric.k00_numpre ";
        $sqlarrecadrecgeral .= "    inner join arrecad on arrematric.k00_numpre = arrecad.k00_numpre ";
        $sqlarrecadrecgeral .= "    where j20_anousu = {$this->iAno}) as x ";
        $sqlarrecadrecgeral .= "order by k00_receit ";
        $this->rsReceitasArrecadGeral = \db_query($sqlarrecadrecgeral) or die($sqlarrecadrecgeral);

        global $contadorgeral;
        $contadorgeral = 1;

        global $contador;
        $contador = 0;

        if ($tipo == static::TIPO_EMISSAO_TXTBSJ) {
            $linha00 = "";
            $linha00 .= "BSJR00";
            $linha00 .= str_replace("/", "", date('d/m/y', \db_getsession('DB_datausu')));
            $linha00 .= str_replace(":", "", db_hora(0, "H:i:s"));
            $linha00 .= "1035";
            $linha00 .= "    ";
            $linha00 .= "N";
            $linha00 .= "IPTU" . substr($this->iAno, 2, 2);
            $linha00 .= str_repeat(" ", 255);

            $this->oArquivoDados->fwrite(static::dbContadorBsj($linha00, "", $contador, 288));
        }

        $this->iTotalLinhas20 = 0;
        $this->nTotalParcelas20 = 0;

        $lGerarLayout = ($tipo != static::TIPO_EMISSAO_TXTBSJ);
        $iQuantidadeRegistros = 0;

        $iMatriculaAtual = null;
        $aDadosMatricula = array();

        $this->maximoUnicasPorLinha = (integer) $this->getQuantidadeMaximaUnicas($oEmissao);

        foreach ($oRegistroCollection as $iIndice => $oRegistro) {
             $callbackAtualiza($iIndice, count($oRegistroCollection));

            if ($oRegistro->getMatricula() != $iMatriculaAtual || empty($iMatriculaAtual)) {

                /**
                 * Verifica se foi passado uma quantidade de registros para impressão no arquivo
                 */
                if (!empty($iMatriculaAtual) && !empty($iQuantidadeRegistrosArquivo)
                  && $iQuantidadeRegistros >= $iQuantidadeRegistrosArquivo - 1) {
                    $callbackAtualiza(count($oRegistroCollection), count($oRegistroCollection));
                    break;
                }

                if (!empty($iMatriculaAtual)) {
                    $iQuantidadeRegistros++;
                    $this->gerarLinhaRegistro($iMatriculaAtual, $aDadosMatricula, $iQuantidadeRegistros, $lGerarLayout);
                    $lGerarLayout = false;
                }

                $iMatriculaAtual = $oRegistro->getMatricula();

                $aDadosMatricula = array(
                  'unicas'   => array(),
                  'parcelas' => array()
                );
            }

            if ($oRegistro->getParcela() == 0) {
                $aDadosMatricula["unicas"][] = $oRegistro;
            } else {
                $aDadosMatricula["parcelas"][$oRegistro->getParcela()] = $oRegistro;
            }
        }

        $callbackAtualiza(count($oRegistroCollection), count($oRegistroCollection));

        /**
         * Imprime no arquivo a última matricula gerada
         */
        $iQuantidadeRegistros++;
        $this->gerarLinhaRegistro($iMatriculaAtual, $aDadosMatricula, $iQuantidadeRegistros, $lGerarLayout);

        if ($tipo == static::TIPO_EMISSAO_TXTBSJ) {
            $linha90 = "";
            $linha90 .= "BSJR90";
            $linha90 .= str_pad($this->iTotalLinhas20, 7, "0", STR_PAD_LEFT);
            $linha90 .= str_pad(
                str_replace(".", "", \db_formatar($this->nTotalParcelas20, 'p', '0', 15)),
                15,
                "0",
                STR_PAD_LEFT
            );
            $linha90 .= str_pad(0, 7, "0", STR_PAD_LEFT);
            $linha90 .= str_pad(0, 15, "0", STR_PAD_LEFT);
            $linha90 .= str_repeat(" ", 238);

            $this->oArquivoDados->fwrite(static::dbContadorBsj($linha90, "", $contador, 288));
        }

        /**
         * Escreve as informações sobre a geração no arquivo de layout
         */
        $ordem = "Endereco de entrega";

        if ($oParametrosGeracao->ordem != "endereco") {
            $ordem = "Alfabética";

            if ($oParametrosGeracao->ordem != "alfabetica") {
                $ordem = "Zona de entrega";
            }
        }

        $quantidadeRegistros = "QUANTIDADE DE REGISTROS A PROCESSAR: ";
        $quantidadeRegistros .= "{$oParametrosGeracao->quantidade}{$this->sQuebraLinha}";

        $imprimirValidos = "IMPRIMIR APENAS REGISTROS COM ENDERECO DE ENTREGA VALIDOS: SIM";

        if (!$oParametrosGeracao->somente_com_endereco_valido) {
            $imprimirValidos = "IMPRIMIR APENAS REGISTROS COM ENDERECO DE ENTREGA VALIDOS: NAO";
        }

        $this->oArquivoLayout->fwrite("{$this->sQuebraLinha}{$this->sQuebraLinha}{$this->sQuebraLinha}");
        $this->oArquivoLayout->fwrite("OPCOES ESCOLHIDAS NA GERACAO: {$this->sQuebraLinha}");
        $this->oArquivoLayout->fwrite("{$this->sQuebraLinha}");
        $this->oArquivoLayout->fwrite("ORDEM: {$ordem}{$this->sQuebraLinha}");
        $this->oArquivoLayout->fwrite("ESPÉCIE: $oParametrosGeracao->especie{$this->sQuebraLinha}");
        $this->oArquivoLayout->fwrite($quantidadeRegistros);
        $this->oArquivoLayout->fwrite($imprimirValidos);

        $sqlnaogeracgm = "select j68_numcgm, z01_nome ";
        $sqlnaogeracgm .= " from iptunaogeracarnecgm inner join cgm on z01_numcgm = j68_numcgm order by j68_numcgm";
        $resultnaogeracgm = \db_query($sqlnaogeracgm) or die($sqlnaogeracgm);

        if (pg_num_rows($resultnaogeracgm) > 0) {
            $this->oArquivoLayout->fwrite("{$this->sQuebraLinha}{$this->sQuebraLinha}{$this->sQuebraLinha}");
            $this->oArquivoLayout->fwrite("LISTA DE CGM NAO GERADOS: {$this->sQuebraLinha}");
            $this->oArquivoLayout->fwrite("{$this->sQuebraLinha}");
        }

        for ($naogeracgm = 0; $naogeracgm < pg_numrows($resultnaogeracgm); $naogeracgm++) {
            $oDadosCgmNaoGera = \db_utils::fieldsMemory($resultnaogeracgm, $naogeracgm);
            $this->oArquivoLayout->fwrite(str_pad(
                $oDadosCgmNaoGera->j68_numcgm,
                6,
                "0",
                STR_PAD_LEFT
            ) . " - $oDadosCgmNaoGera->z01_nome{$this->sQuebraLinha}");
        }

        $totalCgm = pg_num_rows($resultnaogeracgm);

        $this->oArquivoLayout->fwrite("{$this->sQuebraLinha}");
        $this->oArquivoLayout->fwrite("TOTAL DE CGM A NAO GERAR: {$totalCgm}{$this->sQuebraLinha}");
        $this->oArquivoLayout->fwrite("{$this->sQuebraLinha}");

        if ($oParametrosGeracao->filtro_principal == "normal") {
            $filtroprincipal = "NORMAL";
        } elseif ($oParametrosGeracao->filtro_principal == "compgto") {
            $filtroprincipal = "SOMENTE SEM PARCELAS EM ATRASO";
        } else {
            $filtroprincipal = "SOMENTE OS REGISTROS SEM PAGAMENTOS";
        }

        $this->oArquivoLayout->fwrite("FILTRO PRINCIPAL: $filtroprincipal{$this->sQuebraLinha}");
        $this->oArquivoLayout->fwrite("{$this->sQuebraLinha}");

        $imobiliariaTodos = "IMPRIMIR TODOS OS REGISTROS, INDEPENDENTE DO VINCULO COM IMOBILIARIA";
        $imobiliariaCom = "SOMENTE OS QUE TENHAM VINCULO COM IMOBILIARIA";
        $imobiliariaSem = "SOMENTE OS QUE NAO TENHAM VINCULO COM IMOOBILIARIA";

        if ($oParametrosGeracao->imobiliaria == "todos") {
            $this->oArquivoLayout->fwrite("{$imobiliariaTodos}{$this->sQuebraLinha}");
        } elseif ($oParametrosGeracao->imobiliaria == "com") {
            $this->oArquivoLayout->fwrite("{$imobiliariaCom}{$this->sQuebraLinha}");
        } elseif ($oParametrosGeracao->imobiliaria == "sem") {
            $this->oArquivoLayout->fwrite("{$imobiliariaSem}{$this->sQuebraLinha}");
        }

        $loteamentoTodos = "IMPRIMIR TODOS OS REGISTROS, INDEPENDENTE DO VINCULO COM LOTEAMENTO";
        $loteamentoCom = "SOMENTE OS QUE TENHAM VINCULO COM LOTEAMENTO";
        $loteamentoSem = "SOMENTE OS QUE NAO TENHAM VINCULO COM LOTEAMENTO";

        if ($oParametrosGeracao->loteamento == "todos") {
            $this->oArquivoLayout->fwrite("{$loteamentoTodos}{$this->sQuebraLinha}");
        } elseif ($oParametrosGeracao->loteamento == "com") {
            $this->oArquivoLayout->fwrite("{$loteamentoCom}{$this->sQuebraLinha}");
        } elseif ($oParametrosGeracao->loteamento == "sem") {
            $this->oArquivoLayout->fwrite("{$loteamentoSem}{$this->sQuebraLinha}");
        }

        if ($oParametrosGeracao->barras_unica == "seis") {
            $this->oArquivoLayout->fwrite("TERCEIRO DIGITO CODIGO DE BARRAS UNICA: 6{$this->sQuebraLinha}");
        } else {
            $this->oArquivoLayout->fwrite("TERCEIRO DIGITO CODIGO DE BARRAS UNICA: 7{$this->sQuebraLinha}");
        }

        if ($oParametrosGeracao->barras_parcela == "seis") {
            $this->oArquivoLayout->fwrite("TERCEIRO DIGITO CODIGO DE BARRAS PARCELADO: 6{$this->sQuebraLinha}");
            $this->oArquivoLayout->fwrite("{$this->sQuebraLinha}");
        } else {
            $this->oArquivoLayout->fwrite("TERCEIRO DIGITO CODIGO DE BARRAS PARCELADO: 7{$this->sQuebraLinha}");
            $this->oArquivoLayout->fwrite("{$this->sQuebraLinha}");
        }

        $massaFalida = "PROCESSAR MASSA FALIDA: SIM";

        if (!$oParametrosGeracao->processa_massa_falida) {
            $massaFalida = "PROCESSAR MASSA FALIDA: NAO";
        }

        $this->oArquivoLayout->fwrite("{$massaFalida}{$this->sQuebraLinha}");

        $expressaoIsento = 'COLOCAR EXPRESSAO "ISENTO" QUANDO TAXAS/IMPOSTO ZERADO: SIM';

        if (!$oParametrosGeracao->expressao_isento) {
            $expressaoIsento = 'COLOCAR EXPRESSAO "ISENTO" QUANDO TAXAS/IMPOSTO ZERADO: NAO';
        }

        $this->oArquivoLayout->fwrite("{$expressaoIsento}{$this->sQuebraLinha}");

        if (!empty($oParametrosGeracao->parcelas_obrigatorias)) {
            $this->oArquivoLayout->fwrite('PARCELA OBRIGATORIA EM ABERTO: ' . implode(
                ", ",
                $oParametrosGeracao->parcelas_obrigatorias
            ) . "{$this->sQuebraLinha}");
        } else {
            $this->oArquivoLayout->fwrite("SEM DEFINICAO DE PARCELA OBRIGATORIA EM ABERTO{$this->sQuebraLinha}");
        }

        $this->oArquivoLayout->fwrite('VALOR MINIMO : ' . \db_formatar(
            $oParametrosGeracao->valor_minimo,
            'f'
        ) . "{$this->sQuebraLinha}");
        $this->oArquivoLayout->fwrite('VALOR MAXIMO : ' . \db_formatar(
            $oParametrosGeracao->valor_maximo,
            'f'
        ) . "{$this->sQuebraLinha}");

        $this->oArquivoLayout->fwrite('VALOR MINIMO PARA PARCELADO: ' . \db_formatar(
            $oParametrosGeracao->valor_minimo_parcelado,
            'f'
        ) . "{$this->sQuebraLinha}");

        $this->oArquivoLayout->fwrite('VALOR MAXIMO PARA PARCELADO: ' . \db_formatar(
            $oParametrosGeracao->valor_maximo_parcelado,
            'f'
        ) . "{$this->sQuebraLinha}");

        if ($oParametrosGeracao->intervalo_parcelado == "desconsiderar") {
            $this->oArquivoLayout->fwrite("DESCONSIDERAR INTERVALO{$this->sQuebraLinha}");
        } elseif ($oParametrosGeracao->intervalo_parcelado == "gerar") {
            $this->oArquivoLayout->fwrite("GERAR PARA OS QUE ESTIVEREM NO INTERVALO{$this->sQuebraLinha}");
        } elseif ($oParametrosGeracao->intervalo_parcelado == "naogerar") {
            $this->oArquivoLayout->fwrite("NAO GERAR PARA OS QUE ESTIVEREM NO INTERVALO{$this->sQuebraLinha}");
        }

        $this->oArquivoLayout->fwrite("{$this->sQuebraLinha}");

        return $aArquivosRetorno;
    }

    public static function dbContador($apelido, $expressao, $contador, $valor)
    {

        global $contador, $contadorgeral;
        $sQuebraLinha = "\r\n";
        $contadorant = $contador + 1;
        $contador += $valor;

        $linha = str_pad($contadorgeral++, 5);
        $linha .= " | " . str_pad($apelido, 30);
        $linha .= " | " . str_pad($expressao, 80);
        $linha .= " | " . str_pad($valor, 4, "0", STR_PAD_LEFT);
        $linha .= " | " . str_pad($contadorant, 4, "0", STR_PAD_LEFT);
        $linha .= " | " . str_pad($contador, 4, "0", STR_PAD_LEFT) . "{$sQuebraLinha}";

        return $linha;
    }

    public static function dbContadorBsj($apelido, $expressao, $contador, $valor)
    {

        global $contador, $contadorgeral;
        $sQuebraLinha = "\r\n";
        $contadorant = $contador + 1;
        $contador += $valor;

        return str_pad($apelido, 30) . "{$sQuebraLinha}";
    }

    /*
   * função que retorna ultima sexta do mes
   * @param $iMes integer mes a ser validado
   * @param $iAno integer ano a ser validado
   */
    public static function getUltimoDiaMes($iMes, $iAno, $diaSemana = null)
    {

        $ultimo_dia_mes = strtotime("{$iAno}-{$iMes}-" . cal_days_in_month(CAL_GREGORIAN, $iMes, $iAno));

        if (!empty($diaSemana)) {
            if (date('w', $ultimo_dia_mes) != 5) {
                $ultima_sexta = strtotime("last {$diaSemana}", $ultimo_dia_mes);
            } else {
                $ultima_sexta = $ultimo_dia_mes;
            }

            return date("d", $ultima_sexta);
        }
    }

    /**
     * @param $numnov
     * @return mixed
     * @throws \Exception
     */
    private static function getValorTaxaExpediente($numnov)
    {
        $repository = new RecibopagaRepository(DataBase::getInstance(), new \cl_recibopaga());
        $where = array("k00_numnov = {$numnov}", "k00_hist = " . TaxaEspecificaModel::CODIGO_HISTORICO);
        $reciboPaga = $repository->findAll(implode(' AND ', $where))->get(0);

        return $reciboPaga->getValor();
    }


    /**
     * @param $numnov
     * @return int|null
     * @throws \Exception
     */
    private function getReceitaTaxaExpediente($numnov)
    {
        if ($this->buscarReceitaTaxaExpediente === false) {
            return null;
        }

        $this->buscarReceitaTaxaExpediente = true;

        $dao = new \cl_recibopaga();
        $campos = array('tabdesc.k07_codigo as receita');
        $where = array("recibopaga.k00_numnov = {$numnov}");

        $sql = $dao->sqlTaxaEspecifica($campos, $where);
        $rs = db_query($sql);

        if (!$rs) {
            throw new \Exception('Erro ao buscar a receita da taxa específica do tipo de débito.');
        }

        if (pg_num_rows($rs) === 0) {
            return null;
        }

        $this->receitaTaxaExpediente = (int) \db_utils::fieldsMemory($rs, 0)->receita;
    }

    /**
     * @param $ano
     * @return integer
     * @throws \Exception
     */
    private function getTipoDebito($ano)
    {
        $sqlTipo = "select k00_tipo ";
        $sqlTipo .= " from iptunump ";
        $sqlTipo .= "      inner join arrecad on j20_numpre = k00_numpre ";
        $sqlTipo .= "where j20_anousu = {$ano} limit 1";
        $rsTipo = \db_query($sqlTipo);

        if (pg_num_rows($rsTipo) == 0) {
            throw new \Exception("Erro ao buscar o tipo de débito.");
        }

        return \db_utils::fieldsMemory($rsTipo, 0)->k00_tipo;
    }

    private function getQuantidadeMaximaUnicas(EmissaoGeral $emissaoGeral)
    {
        $sql = "select coalesce(count(1), 0) as qtd
                  from emissaogeralregistro
                 inner join emissaogeralmatricula
                    on tr03_emissaogeralregistro = tr02_sequencial
                 where tr02_emissaogeral = {$emissaoGeral->getCodigo()}
                   and tr02_parcela = 0
                 group by tr03_matric
                 order by 1 desc
                 limit 1;";

        $record = \db_query($sql);

        if (pg_num_rows($record) == 0) {
            throw new \Exception("Erro ao buscar a quantidade maxima de unicas.");
        }

        return \db_utils::fieldsMemory($record, 0)->qtd;
    }
}
