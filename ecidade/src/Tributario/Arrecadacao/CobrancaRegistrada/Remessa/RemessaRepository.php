<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (c) 2017  DBSeller Servicos de Informatica
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

namespace ECidade\Tributario\Arrecadacao\CobrancaRegistrada\Remessa;

use \cl_remessacobrancaregistrada as RemessaCobrancaRegistradaDAO;
use \cl_conveniocobranca as ConvenioCobrancaDAO;
use DateTime;
use \Exception;
use \db_utils;

class RemessaRepository
{
    private $oRemessaCobrancaRegistradaDAO;

    private $oConvenioCobrancaDAO;

    public function __construct(
        RemessaCobrancaRegistradaDAO $oRemessaCobrancaRegistradaDAO,
        ConvenioCobrancaDAO $oConvenioCobrancaDAO
    ) {
        $this->oRemessaCobrancaRegistradaDAO = $oRemessaCobrancaRegistradaDAO;
        $this->oConvenioCobrancaDAO = $oConvenioCobrancaDAO;
    }

    public function getDaoRemessaCobrancaRegistrada()
    {
        return $this->oRemessaCobrancaRegistradaDAO;
    }

    public function getDaoConvenioCobranca()
    {
        return $this->oConvenioCobrancaDAO;
    }

    public function createTempRemessaConvenio(
        $sTempTable,
        $iConvenio,
        $aParcelas = null,
        $sTiposDebito = null,
        $sDataEmisssao = null,
        $sFiltrarDebitos = null
    ) {
        return $this->createTemp(
            $sTempTable,
            "reciboregistra",
            "reciboregistra.k146_numpre",
            "reciboregistra.k146_convenio = $iConvenio",
            $aParcelas,
            $sTiposDebito,
            $sDataEmisssao,
            $sFiltrarDebitos
        );
    }

    public function createTempRemessaGerada($sTempTable, $iRemessa)
    {
        return $this->createTemp(
            $sTempTable,
            "remessacobrancaregistradarecibo",
            "remessacobrancaregistradarecibo.k148_numpre",
            "remessacobrancaregistradarecibo.k148_remessacobrancaregistrada = $iRemessa"
        );
    }

    private function createTemp(
        $sTempTable,
        $sFrom,
        $sJoin,
        $sWhere,
        $aParcelas = null,
        $sTiposDebito = null,
        $sDataEmisssao = null,
        $lFiltrarDebitos = false
    ) {

        if ($lFiltrarDebitos) {
            return $this->createTempIptu(
                $sTempTable,
                $sFrom,
                $sJoin,
                $sWhere,
                $aParcelas,
                $sTiposDebito,
                $sDataEmisssao
            );
        }

        return  $this->createTempPadrao($sTempTable, $sFrom, $sJoin, $sWhere, $sDataEmisssao);
    }

    private function createTempIptu(
        $sTempTable,
        $sFrom,
        $sJoin,
        $sWhere,
        $aParcelas = null,
        $sTiposDebito = null,
        $sDataEmisssao = null
    ) {

        $sSql  = "select k00_numnov,                                                                                  ";
        $sSql .= "       k00_numpre,                                                                                  ";
        $sSql .= "       Sum(valor_total) as valor_total,                                                             ";
        $sSql .= "       k00_dtpaga,                                                                                  ";
        $sSql .= "       k138_data,                                                                                   ";
        $sSql .= "       nosso_numero,                                                                                ";
        $sSql .= "       regra,                                                                                       ";
        $sSql .= "       matricula,                                                                                   ";
        $sSql .= "       inscricao,                                                                                   ";
        $sSql .= "       cgm,                                                                                         ";
        $sSql .= "       sacado,                                                                                      ";
        $sSql .= "       k00_tipo,                                                                                    ";
        $sSql .= "       parcelas                                                                                     ";
        $sSql .= " from (                                                                                             ";
        $sSql .= " select recibopaga.k00_numnov,                                                                      ";
        $sSql .= "        (SELECT Array_agg(DISTINCT(rp.k00_numpre))                                                  ";
        $sSql .= "           FROM recibopaga rp                                                                       ";
        $sSql .= "          WHERE rp.k00_numnov = recibopaga.k00_numnov ) AS k00_numpre,                              ";
        $sSql .= "        sum(recibopaga.k00_valor) as valor_total,                                                   ";
        $sSql .= "        recibopaga.k00_dtpaga,                                                                      ";
        $sSql .= "        recibopagaboleto.k138_data,                                                                 ";
        $sSql .= "        arrebanco.k00_numbco as nosso_numero,                                                       ";
        $sSql .= "        ' '::char as regra,                                                                         ";
        $sSql .= "        (select array_agg(arrematric.k00_matric)                                                    ";
        $sSql .= "           from arrematric                                                                          ";
        $sSql .= "          where arrematric.k00_numpre = any(array_agg(recibopaga.k00_numpre))) as matricula,        ";
        $sSql .= "        (select array_agg(arreinscr.k00_inscr)                                                      ";
        $sSql .= "           from arreinscr                                                                           ";
        $sSql .= "          where arreinscr.k00_numpre = any(array_agg(recibopaga.k00_numpre))) as inscricao,         ";
        $sSql .= "        (select array_agg(arrenumcgm.k00_numcgm)                                                    ";
        $sSql .= "           from arrenumcgm                                                                          ";
        $sSql .= "          where arrenumcgm.k00_numpre = any(array_agg(recibopaga.k00_numpre))) as cgm,              ";
        $sSql .= "        array[1] as sacado,                                                                         ";
        $sSql .= "    (SELECT arrecad.k00_tipo FROM arrecad where arrecad.k00_numpre = recibopaga.k00_numpre limit 1),";
        $sSql .= "        ( select array_to_string(array_accum(distinct k00_numpar), ',')                             ";
        $sSql .= "            from recibopaga where k00_numnov = k146_numpre) as parcelas                             ";
        $sSql .= "   from $sFrom                                                                                      ";
        $sSql .= "        inner join recibopaga ON recibopaga.k00_numnov = $sJoin                                     ";
        $sSql .= "        inner join recibopagaboleto ON recibopaga.k00_numnov = recibopagaboleto.k138_numnov         ";
        $sSql .= "                                   AND recibopagaboleto.k138_data > '2016-01-01'                    ";
        $sSql .= "        left join arrebanco ON arrebanco.k00_numpre = recibopaga.k00_numnov                         ";
        $sSql .= "  where $sWhere                                                                                     ";
        $sSql .= $this->getSqlFiltroArrecad()                                                                          ;
        $sSql .= $this->getSqlTiposDebito($sTiposDebito)                                                               ;
        $sSql .= "  group by recibopaga.k00_numcgm,                                                                   ";
        $sSql .= "           recibopaga.k00_dtpaga,                                                                   ";
        $sSql .= "           recibopaga.k00_numnov,                                                                   ";
        $sSql .= "           recibopagaboleto.k138_data,                                                              ";
        $sSql .= "           arrebanco.k00_numbco,                                                                    ";
        $sSql .= "           recibopaga.k00_numpre,                                                                   ";
        $sSql .= "           parcelas                                                                                 ";

        $sSql .= "  union all                                                                                         ";

        $sSql .= " select recibo.k00_numpre as k00_numnov,                                                            ";
        $sSql .= "        (SELECT array_agg(DISTINCT(rp.k00_numpre))                                                  ";
        $sSql .= "           FROM   recibopaga rp                                                                     ";
        $sSql .= "          WHERE  rp.k00_numpre = recibo.k00_numpre ) AS k00_numpre,                                 ";
        $sSql .= "        sum(recibo.k00_valor) as valor_total,                                                       ";
        $sSql .= "        recibo.k00_dtvenc as k00_dtpaga,                                                            ";
        $sSql .= "        recibo.k00_dtoper as k138_data,                                                             ";
        $sSql .= "        arrebanco.k00_numbco as nosso_numero,                                                       ";
        $sSql .= "        ' '::char as regra,                                                                         ";
        $sSql .= "        (select array_agg(arrematric.k00_matric)                                                    ";
        $sSql .= "           from arrematric                                                                          ";
        $sSql .= "          where arrematric.k00_numpre = any(array_agg(recibo.k00_numpre))) as matricula,            ";
        $sSql .= "        (select array_agg(arreinscr.k00_inscr)                                                      ";
        $sSql .= "           from arreinscr                                                                           ";
        $sSql .= "          where arreinscr.k00_numpre = any(array_agg(recibo.k00_numpre))) as inscricao,             ";
        $sSql .= "        array_agg(recibo.k00_numcgm) as cgm,                                                        ";
        $sSql .= "        array[1] as sacado,                                                                         ";
        $sSql .= "        recibo.k00_tipo,                                                                            ";
        $sSql .= "        ( select array_to_string(array_accum(distinct k00_numpar), ',')                             ";
        $sSql .= "            from recibopaga where k00_numnov = k146_numpre) as parcelas                             ";
        $sSql .= "   from $sFrom                                                                                      ";
        $sSql .= "        inner join recibo on recibo.k00_numpre = $sJoin                                             ";
        $sSql .= "        inner join arrebanco on arrebanco.k00_numpre = $sJoin                                       ";
        $sSql .= "  where $sWhere                                                                                     ";
        $sSql .= $this->getSqlTiposDebitoRecibo($sTiposDebito)                                                         ;
        $sSql .= "  group by recibo.k00_numpre,                                                                       ";
        $sSql .= "           recibo.k00_dtvenc,                                                                       ";
        $sSql .= "           recibo.k00_dtoper,                                                                       ";
        $sSql .= "           arrebanco.k00_numbco,                                                                    ";
        $sSql .= "           recibo.k00_numpre,                                                                       ";
        $sSql .= "           recibo.k00_tipo,                                                                         ";
        $sSql .= "           parcelas                                                                                 ";
        $sSql .= ") as par                                                                                            ";
        $sSql .= $this->getSqlFiltros($aParcelas, $sDataEmisssao);
        $sSql .= " GROUP BY k00_numnov,                                                                               ";
        $sSql .= "         k00_numpre,                                                                                ";
        $sSql .= "         k00_dtpaga,                                                                                ";
        $sSql .= "         k138_data,                                                                                 ";
        $sSql .= "         nosso_numero,                                                                              ";
        $sSql .= "         regra,                                                                                     ";
        $sSql .= "         matricula,                                                                                 ";
        $sSql .= "         inscricao,                                                                                 ";
        $sSql .= "         cgm,                                                                                       ";
        $sSql .= "         sacado,                                                                                    ";
        $sSql .= "         k00_tipo,                                                                                  ";
        $sSql .= "         parcelas                                                                                   ";

        $sSql = "create temp table $sTempTable as $sSql";

        $rsRemessaCobrancaRegistradaDAO = $this->oRemessaCobrancaRegistradaDAO->sql_record($sSql);

        if ($this->oRemessaCobrancaRegistradaDAO->erro_banco) {
            throw new Exception($this->oRemessaCobrancaRegistradaDAO->erro_msg);
        }

        return $rsRemessaCobrancaRegistradaDAO;
    }

    private function createTempPadrao($sTempTable, $sFrom, $sJoin, $sWhere, $sDataEmissao = null)
    {
        $data = "> '2016-01-01'";
        if (!empty($sDataEmissao)) {
            $sTime        = date_format(date_create_from_format('d/m/Y', $sDataEmissao), 'Y/m/d');
            $sDataEmissao = (new DateTime($sTime))->format('Y-m-d');
            $data = "= '{$sDataEmissao}'";
        }

        $sSql = " select recibopaga.k00_numnov,                                                               ";
        $sSql .= "        sum(recibopaga.k00_valor) as valor_total,                                            ";
        $sSql .= "        recibopaga.k00_dtpaga,                                                               ";
        $sSql .= "        recibopagaboleto.k138_data,                                                          ";
        $sSql .= "        arrebanco.k00_numbco as nosso_numero,                                                ";
        $sSql .= "        ' '::char as regra,                                                                  ";
        $sSql .= "        (select array_agg(arrematric.k00_matric)                                             ";
        $sSql .= "           from arrematric                                                                   ";
        $sSql .= "          where arrematric.k00_numpre = any(array_agg(recibopaga.k00_numpre))) as matricula, ";
        $sSql .= "        (select array_agg(arreinscr.k00_inscr)                                               ";
        $sSql .= "           from arreinscr                                                                    ";
        $sSql .= "          where arreinscr.k00_numpre = any(array_agg(recibopaga.k00_numpre))) as inscricao,  ";
        $sSql .= "        (select array_agg(arrenumcgm.k00_numcgm)                                             ";
        $sSql .= "           from arrenumcgm                                                                   ";
        $sSql .= "          where arrenumcgm.k00_numpre = any(array_agg(recibopaga.k00_numpre))) as cgm,       ";
        $sSql .= "        array[1] as sacado                                                                   ";
        $sSql .= "   from $sFrom                                                                               ";
        $sSql .= "        inner join recibopaga ON recibopaga.k00_numnov = $sJoin                              ";
        $sSql .= "        inner join recibopagaboleto ON recibopaga.k00_numnov = recibopagaboleto.k138_numnov  ";
        $sSql .= "                                   AND recibopagaboleto.k138_data {$data}                    ";
        $sSql .= "        left join arrebanco ON arrebanco.k00_numpre = recibopaga.k00_numnov                  ";
        $sSql .= "  where $sWhere                                                                              ";
        //$sSql .= "  group by recibopaga.k00_numcgm,                                                          ";
        $sSql .= "  group by                                                                                   ";
        $sSql .= "           recibopaga.k00_dtpaga,                                                            ";
        $sSql .= "           recibopaga.k00_numnov,                                                            ";
        $sSql .= "           recibopagaboleto.k138_data,                                                       ";
        $sSql .= "           arrebanco.k00_numbco                                                              ";

        $sSql .= "  union all                                                                                  ";

        $sSql .= " select recibo.k00_numpre as k00_numnov,                                                     ";
        $sSql .= "        sum(recibo.k00_valor) as valor_total,                                                ";
        $sSql .= "        recibo.k00_dtvenc as k00_dtpaga,                                                     ";
        $sSql .= "        recibo.k00_dtoper as k138_data,                                                      ";
        $sSql .= "        arrebanco.k00_numbco as nosso_numero,                                                ";
        $sSql .= "        ' '::char as regra,                                                                  ";
        $sSql .= "        (select array_agg(arrematric.k00_matric)                                             ";
        $sSql .= "           from arrematric                                                                   ";
        $sSql .= "          where arrematric.k00_numpre = any(array_agg(recibo.k00_numpre))) as matricula,     ";
        $sSql .= "        (select array_agg(arreinscr.k00_inscr)                                               ";
        $sSql .= "           from arreinscr                                                                    ";
        $sSql .= "          where arreinscr.k00_numpre = any(array_agg(recibo.k00_numpre))) as inscricao,      ";
        $sSql .= "        array_agg(recibo.k00_numcgm) as cgm,                                                 ";
        $sSql .= "        array[1] as sacado                                                                   ";
        $sSql .= "   from $sFrom                                                                               ";
        $sSql .= "        inner join recibo on recibo.k00_numpre = $sJoin                                      ";
        $sSql .= "        inner join arrebanco on arrebanco.k00_numpre = $sJoin                                ";
        $sSql .= "  where $sWhere                                                                              ";
        $sSql .= "  group by recibo.k00_numpre,                                                                ";
        $sSql .= "           recibo.k00_dtvenc,                                                                ";
        $sSql .= "           recibo.k00_dtoper,                                                                ";
        $sSql .= "           arrebanco.k00_numbco                                                              ";

        $sSql = "create temp table $sTempTable as $sSql";

        $rsRemessaCobrancaRegistradaDAO = $this->oRemessaCobrancaRegistradaDAO->sql_record($sSql);

        if ($this->oRemessaCobrancaRegistradaDAO->erro_banco) {
            throw new Exception($this->oRemessaCobrancaRegistradaDAO->erro_msg);
        }

        return $rsRemessaCobrancaRegistradaDAO;
    }

    public function getCodigoConvenio($iRemessa)
    {
        $sSql = $this->oRemessaCobrancaRegistradaDAO->sql_query_file($iRemessa);
        $rsRemessaCobrancaRegistradaDAO = $this->oRemessaCobrancaRegistradaDAO->sql_record($sSql);

        if ($this->oRemessaCobrancaRegistradaDAO->erro_banco) {
            throw new Exception("Erro ao buscar os dados da remessa.");
        }

        $oRemessa = db_utils::fieldsMemory($rsRemessaCobrancaRegistradaDAO, 0);

        return $oRemessa->k147_convenio;
    }

    public function getCodigoBanco($iConvenio)
    {
        $sSql = $this->oConvenioCobrancaDAO->sql_query(null, "db90_codban", null, "ar13_cadconvenio = $iConvenio");
        $rsConvenioCobrancaDAO = $this->oConvenioCobrancaDAO->sql_record($sSql);

        if ($this->oConvenioCobrancaDAO->erro_banco) {
            throw new Exception("Erro ao buscar as informações do convênio.");
        }

        $oConvenioCobranca = db_utils::fieldsMemory($rsConvenioCobrancaDAO, 0);

        return $oConvenioCobranca->db90_codban;
    }

    public function getSqlTiposDebito($sTiposDebito)
    {
        $sql = "";
        if ($sTiposDebito) {
            $sql = " AND (SELECT true FROM arrecad where arrecad.k00_tipo in ($sTiposDebito) 
            and arrecad.k00_numpre = recibopaga.k00_numpre limit 1)";
        }

        return $sql;
    }

    public function getSqlTiposDebitoRecibo($sTiposDebito, $sTable = '')
    {
        $sql = "";
        if ($sTiposDebito) {
            $sql = " AND recibo.k00_tipo in ($sTiposDebito)";
        }

        return $sql;
    }

    public function getSqlFiltros($aParcelas, $sDataEmisssao)
    {
        $sSql = '';
        $sSql = $this->getSqlParcelas($aParcelas, $sSql);
        $sSql = $this->getSqlDataEmissao($sDataEmisssao, $sSql);
        return $sSql;
    }

    public function getSqlParcelas($aParcelas, $sSql)
    {

        if (isset($aParcelas['unicas'])) {
            $sSql .= ($sSql == "") ? " WHERE" : " AND";
            $sSql .= " (exists (select 1 from recibounica inner join db_reciboweb on k99_numpre_n = par.k00_numnov   ";
            $sSql .= " where recibounica.k00_numpre = ANY(par.k00_numpre) and recibounica.k00_dtvenc = par.k00_dtpaga";
            $sSql .= " and k99_numpar = 0                                                                            ";

            $count = 0;
            foreach ($aParcelas['unicas'] as $key => $value) {
                $sSql .= ($count > 0) ? " or" : " and";
                $sSql .= " (par.k00_tipo = $key";
                $aux = explode(',', $value);
                $strAux = "'".implode("','", $aux)."'";
                $sSql .= " and recibounica.k00_dtvenc in ($strAux)))";
                $count++;
            }

            $sSql .= ")";
        }
        if (isset($aParcelas['parcelas'])) {
            if ($sSql == "") {
                $sSql .= " where";
            } else {
                $sSql .= " or";
            }

            $count = 0;
            foreach ($aParcelas['parcelas'] as $key => $value) {
                if ($count > 0) {
                    $sSql .= " or (par.k00_tipo = $key and )";
                } else {
                    $sSql .= " (par.k00_tipo = $key and (";
                }

                $aux = explode(',', $value);
                $count2 = 0;
                foreach ($aux as $dado) {
                    $arrData = explode('.', $dado);
                    $sSql .= ($count2 > 0) ? " or" : "";
                    $sSql .= " (par.k00_dtpaga = '$arrData[0]' AND par.parcelas = '$arrData[1]')";
                    $count2++;
                }
                $sSql .= "))";
                $count++;
            }
        }

        return $sSql;
    }

    public function getSqlDataEmissao($sDataEmisssao, $sSql)
    {
        if ($sDataEmisssao) {
            $arrAux = explode('/', $sDataEmisssao);
            $sSql .= ($sSql == "") ? " WHERE":" AND";
            $sSql .= " par.k138_data = '$arrAux[2]-$arrAux[1]-$arrAux[0]'" ;
        }

        return $sSql;
    }

    public function getSqlFiltroArrecad()
    {
        $sSql  = "AND                                                           ";
        $sSql .= "(                                                             ";
        $sSql .= "       SELECT    count(*)                                     ";
        $sSql .= "        FROM      recibopaga                                  ";
        $sSql .= "        INNER JOIN arrecad                                     ";
        $sSql .= "          ON        recibopaga.k00_numpre = arrecad.k00_numpre";
        $sSql .= "         AND       recibopaga.k00_numpar = arrecad.k00_numpar ";
        $sSql .= "         AND       recibopaga.k00_receit = arrecad.k00_receit ";
        $sSql .= "       WHERE     k00_numnov = k146_numpre                     ";
        $sSql .= "         AND       recibopaga.k00_hist NOT IN (400,           ";
        $sSql .= "                                               401)) > 0      ";

        return $sSql;
    }
}
