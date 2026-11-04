<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2016  DBSeller Servicos de Informatica
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
 *  junto com este programa; se nao, escreva para a Free Softwareb
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

namespace ECidade\Tributario\Cadastro\Repository;

use db_utils;

class HistoricoCalculoAnualRepository extends \BaseClassRepository
{
    private $matricula;

    private $exercicio;

    public function setMatricula($matricula)
    {
        $this->matricula = $matricula;
        return $this;
    }

    public function setExercicio($exercicio)
    {
        $this->exercicio = $exercicio;
        return $this;
    }

    public function organizaCalculos()
    {
        $oCalculos = $this->getTaxasCalculo();
        $oCalculosIptucalc = $this->getDadosCalculo();
        $oCalculosIptucale = $this->getDadosIptucale();

        $aIptucalclog = array();

        foreach ($oCalculos as $oCalculo) {
            if (!in_array($oCalculo->iptucalclog, $aIptucalclog)) {
                $aIptucalclog[] = $oCalculo->iptucalclog;
            }
        }

        $oCalculosAgrupados = array();

        foreach ($aIptucalclog as $iIptucalclog) {
            foreach ($oCalculosIptucalc as $oCalculoIptucalc) {
                if ($oCalculoIptucalc->iptucalclog == $iIptucalclog) {
                    $oCalculosAgrupados[$iIptucalclog]["iptucalc"] = $oCalculoIptucalc;
                }
            }
        }

        foreach ($aIptucalclog as $iIptucalclog) {
            foreach ($oCalculosIptucale as $oCalculoIptucale) {
                if ($oCalculoIptucale->j162_iptucalclog == $iIptucalclog) {
                    $oCalculosAgrupados[$iIptucalclog]["iptucale"][] = $oCalculoIptucale;
                }
            }
        }

        foreach ($aIptucalclog as $iIptucalclog) {
            $oIptucalclogAnterior = $this->getDadosIptucalclog($iIptucalclog);
            foreach ($oCalculos as $oCalculo) {
                if ($oCalculo->iptucalclog == $iIptucalclog) {
                    $oCalculosAgrupados[$iIptucalclog]["iptucalclog"] = $oIptucalclogAnterior;
                }
            }
        }

        foreach ($aIptucalclog as $iIptucalclog) {
            foreach ($oCalculos as $oCalculo) {
                if ($oCalculo->iptucalclog == $iIptucalclog) {
                    $oCalculosAgrupados[$iIptucalclog][] = $oCalculo;
                }
            }
        }

        krsort($oCalculosAgrupados);
        //dd($oCalculosAgrupados);
        return $oCalculosAgrupados;
    }

    private function getTaxasCalculo()
    {
        $sql = "SELECT DISTINCT
                       k02_codigo,
                       k02_descr,
                       j130_numpre as numpre,
                       j17_codhis,
                       j17_descr,
                       j157_valor as valor,
                       j223_iptucalclog AS iptucalclog,
                       CASE WHEN iptucalhconf.j89_codhis IS NOT NULL
                            THEN (SELECT coalesce(sum(x.j157_valor), 0)
                                    FROM iptucalvold x
                                   WHERE x.j157_anousu = iptucalvold.j157_anousu
                                     AND x.j157_matric = iptucalvold.j157_matric
                                     AND x.j157_receit = iptucalvold.j157_receit
                                     AND x.j157_iptucalclog = iptucalvold.j157_iptucalclog
                                     AND x.j157_codhis = iptucalhconf.j89_codhis)
                            ELSE 0
                        END AS valorisen,
                        CASE
                            WHEN j223_gerafinanc THEN 'Sim'
                            ELSE 'Não'
                        end as gerafinanc
                  FROM iptucalcold  
                 INNER JOIN iptucalvold ON j223_iptucalclog = j157_iptucalclog
                 INNER JOIN iptucalh ON iptucalh.j17_codhis = j157_codhis
                  LEFT JOIN iptucalhconf ON iptucalhconf.j89_codhispai = j157_codhis
                 INNER JOIN tabrec ON tabrec.k02_codigo = j157_receit
                  LEFT JOIN iptucadtaxaexe ON iptucadtaxaexe.j08_tabrec = j157_receit
                  LEFT JOIN iptunumpold on j157_iptucalclog = j130_iptucalclog

                 WHERE j223_matric = {$this->matricula}
                   AND j223_anousu = {$this->exercicio}
                   AND j17_codhis NOT IN (SELECT j89_codhis
                                            FROM iptucalhconf)
                                            
                UNION

                SELECT DISTINCT
                       k02_codigo,
                       k02_descr,
                       j159_numpre as numpre,
                       j17_codhis,
                       j17_descr,
                       j158_valor as valor,
                       j223_iptucalclog AS iptucalclog,
                       CASE WHEN iptucalhconf.j89_codhis IS NOT NULL
                            THEN (SELECT coalesce(sum(x.j158_valor), 0)
                                    FROM iptutaxacalvold x
                                   WHERE x.j158_iptutaxanumpold = iptutaxacalvold.j158_iptutaxanumpold
                                     AND x.j158_iptucalclog = iptutaxacalvold.j158_iptucalclog
                                     AND x.j158_codhis = iptucalhconf.j89_codhis)
                            ELSE 0
                        END AS valorisen,
                        CASE
                            WHEN j223_gerafinanc THEN 'Sim'
                            ELSE 'Não'
                        end as gerafinanc
                  FROM iptucalcold  
                 INNER JOIN iptutaxacalvold ON j223_iptucalclog = j158_iptucalclog
                 INNER JOIN iptutaxanumpold ON j158_iptucalclog = j159_iptucalclog
                 INNER JOIN iptucalh ON iptucalh.j17_codhis = j158_codhis
                 INNER JOIN tabrec ON tabrec.k02_codigo = j158_receit
                  LEFT JOIN iptucalhconf ON iptucalhconf.j89_codhispai = j158_codhis
                  LEFT JOIN iptucadtaxaexe ON iptucadtaxaexe.j08_tabrec = j158_receit

                 WHERE j223_matric = {$this->matricula}
                   AND j223_anousu = {$this->exercicio}
                   AND j17_codhis NOT IN (SELECT j89_codhis
                                            FROM iptucalhconf)                                            
                ;";

        $result = db_query($sql);

        if (!$result) {
            throw new \DBException('Erro ao buscar taxas.\\n\\n Erro: '.pg_last_error());
        }

        return db_utils::getColectionByRecord($result);
    }

    private function getDadosCalculo()
    {
        $sql = "SELECT DISTINCT
                       iptucalcold.j223_anousu,
                       iptucalcold.j223_matric ,
                       iptucalcold.j223_testad ,
                       iptucalcold.j223_arealo ,
                       iptucalcold.j223_areafr ,
                       iptucalcold.j223_areaed ,
                       iptucalcold.j223_m2terr ,
                       iptucalcold.j223_vlrter ,
                       iptucalcold.j223_aliq   ,
                       iptucalcold.j223_vlrisen,
                       sum(j162_valor) AS j162_valor,
                       iptucalcold.j223_iptucalclog AS iptucalclog
                  FROM cadastro.iptucalcold
                  LEFT OUTER JOIN cadastro.iptucaleold ON j162_matric = j223_matric
                   AND j162_anousu = j223_anousu
                   AND j162_iptucalclog = j223_iptucalclog
                 WHERE j223_matric = {$this->matricula}
                   AND j223_anousu = {$this->exercicio}
                 GROUP BY iptucalcold.j223_anousu ,
                          iptucalcold.j223_matric ,
                          iptucalcold.j223_testad ,
                          iptucalcold.j223_arealo ,
                          iptucalcold.j223_areafr ,
                          iptucalcold.j223_areaed ,
                          iptucalcold.j223_m2terr ,
                          iptucalcold.j223_vlrter ,
                          iptucalcold.j223_aliq   ,
                          iptucalcold.j223_vlrisen,
                          iptucalcold.j223_iptucalclog
                    ORDER BY iptucalcold.j223_anousu DESC;";

        $result = db_query($sql);

        if (!$result) {
            throw new \DBException('Erro ao buscar os dados do cálculo.\\n\\n Erro: '.pg_last_error());
        }

        return db_utils::getColectionByRecord($result);
    }

    private function getDadosIptucalclog($iIptucalclog)
    {
        $sqlIptucalclog = "SELECT j27_codigo,
                                  j27_parcial,
                                  j27_data,
                                  j27_hora,
                                  j28_tipologcalc,
                                  j62_descr,
                                  j62_erro,
                                  j27_observacao,
                                  login,
                                  nome
                             FROM iptucalclog
                            INNER JOIN db_usuarios ON iptucalclog.j27_usuario = db_usuarios.id_usuario
                            INNER JOIN iptucalclogmat ON iptucalclogmat.j28_codigo = iptucalclog.j27_codigo
                            INNER JOIN iptucadlogcalc ON iptucadlogcalc.j62_codigo = iptucalclogmat.j28_tipologcalc
                            WHERE j27_codigo = {$iIptucalclog}
                              AND j28_matric = {$this->matricula}
                            ORDER BY j27_codigo DESC;";

        $result = db_query($sqlIptucalclog);

        if (!$result) {
            throw new \DBException('Erro ao buscar os dados do cálculo 
            anterior nas tabelas de log.\\n\\n Erro: '.pg_last_error());
        }

        return db_utils::fieldsMemory($result, 0);
    }

    private function getDadosIptucale()
    {
        $sql = "SELECT *
                  FROM iptucaleold
                 WHERE j162_matric = {$this->matricula}
                   AND j162_anousu = {$this->exercicio};";

        $result = db_query($sql);

        if (!$result) {
            throw new \DBException("Erro ao buscar os dados da 
            construção no cálculo anterior.\\n\\n Erro: ".pg_last_error());
        }

        return db_utils::getColectionByRecord($result);
    }
}
