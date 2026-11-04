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

class cl_taxaslancadas extends DAOBasica
{
    public function __construct()
    {
        parent::__construct('arrecadacao.taxaslancadas');
    }

    public function getUgTaxasGrm($codigo)
    {
        $sqlUgTaxaSemDepart = "SELECT (CASE WHEN (SELECT COUNT(*)
                                                    FROM arrecadacao.taxaslancadas
                                                    LEFT JOIN arrecadacao.taxaslancadasdepart
                                                      ON ar45_taxaslancadas = ar44_sequencial
                                                   WHERE ar45_sequencial IS NULL
                                                   AND ar44_emissaoweb = 't'
                                                   AND ar44_receita IS NOT NULL) > 0
                                            THEN 'GERAL'
                                            ELSE ''
                                       END) AS ug,
                                       0 AS codigo";
        
        $anousu = db_getsession("DB_anousu");

        if (!empty($codigo)) {
            $codigo = " AND o40_orgao = {$codigo}";
        }

        $sqlUgTaxaComDepart = "SELECT o40_descr as ug,
                                      o40_orgao as codigo
                                 FROM arrecadacao.taxaslancadas
                                INNER JOIN arrecadacao.taxaslancadasdepart ON ar45_taxaslancadas = ar44_sequencial
                                INNER JOIN db_departorg ON db01_coddepto = ar45_departamento
                                INNER JOIN orcorgao ON o40_orgao = db01_orgao
                                WHERE ar44_emissaoweb = 't'
                                  AND ar44_receita IS NOT NULL
                                  AND o40_anousu = {$anousu}
                                  {$codigo}
                                GROUP BY ar45_departamento,
                                         o40_descr,
                                         o40_orgao";

        $sql = "SELECT x.ug,
                       x.codigo
                  FROM ({$sqlUgTaxaSemDepart} UNION ALL {$sqlUgTaxaComDepart}) AS x
                 GROUP BY x.ug,
                          x.codigo";

        if (!empty($codigo)) {
            $sql = $sqlUgTaxaComDepart;
        }

        $result = db_query($sql);

        if (!$result) {
            throw new Exception("Erro ao buscar a unidade gestora vinculada a taxa.");
        }

        return $result;
    }

    public function getTaxasUgGrm($codigoUg, $codigoTaxa, $isTaxa)
    {
        if (!empty($codigoTaxa)) {
            $codigoTaxa = " AND ar44_sequencial = {$codigoTaxa} ";
        }

        if ($codigoUg == 0) {
            $sql = "SELECT *
                      FROM arrecadacao.taxaslancadas
                      LEFT JOIN arrecadacao.taxaslancadasdepart
                        ON ar45_taxaslancadas = ar44_sequencial
                     WHERE ar45_sequencial IS NULL
                       AND ar44_emissaoweb = 't'
                       AND ar44_receita IS NOT NULL
                       {$codigoTaxa};";
        } else {
            $sql = "SELECT DISTINCT taxaslancadas.*
                      FROM arrecadacao.taxaslancadas
                INNER JOIN arrecadacao.taxaslancadasdepart ON ar45_taxaslancadas = ar44_sequencial
                INNER JOIN db_departorg ON db01_coddepto = ar45_departamento
                INNER JOIN orcorgao ON o40_orgao = db01_orgao
                     WHERE o40_orgao = {$codigoUg}
                       AND ar44_emissaoweb = 't'
                       AND ar44_receita IS NOT NULL
                       {$codigoTaxa}
                  GROUP BY ar45_departamento,
                           o40_descr,
                           o40_orgao,
                           ar44_sequencial,
                           ar44_descricao,
                           ar44_valorinflator,
                           ar44_inflator,
                           ar44_diasvencimento,
                           ar44_tipo,
                           ar44_receitaxaexpediente,
                           ar44_valortaxaexpediente,
                           ar44_datavigencia,
                           ar44_procedencia,
                           ar44_receita,
                           ar44_emissaoweb,
                           ar44_recursoadm";
        }

        $result = db_query($sql);

        if (!$result) {
            throw new Exception("Erro ao buscar a(s) taxa(s) vinculada(s) a unidade gestora.");
        }

        return $result;
    }
}