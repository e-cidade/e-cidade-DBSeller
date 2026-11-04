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

namespace ECidade\Tributario\Juridico\Repository;

/**
 * Repository para açoes com partilhas.
 *
 * @author Leonardo Oliveira <leonardo.malia@dbseller.com.br>
 */
class Partilha
{
    /**
     * Retorna as custas de um parcelamento do foro.
     *
     * @param integer $numnov
     *
     * @return \stdClass[]
     */
    public function getCustasParcelamentoForo($numnov)
    {
        $oDaoArrecad = new \cl_arrecad();
        $sWhere = "k00_numpre in (select k00_numpre from recibopaga where k00_numnov = {$numnov}) and k00_tipo = 30";
        $sqlDebitoInicialForo = $oDaoArrecad->sql_query_file(null, "*", null, $sWhere);
        $result = db_query($sqlDebitoInicialForo);

        if (!$result || pg_num_rows($result) == 0) {
            return array();
        }

        $sql = "select taxa.ar36_sequencial as taxa,
                          false as dispensalancamentorecibo,
                          taxa.ar36_receita as receita,
                          taxa.ar36_descricao as descricao,
                          (select v76_tipolancamento
                             from processoforopartilha
                            where v76_sequencial = (select v77_processoforopartilha
                                                      from processoforopartilhacusta
                                                     where v77_numnov = {$numnov}
                                                       and v77_taxa = taxa.ar36_sequencial limit 1)
                          union all
                          select v35_tipolancamento
                            from inicialpartilha
                           where v35_sequencial = (select v36_inicialpartilha
                                                     from inicialpartilhacustas
                                                    where v36_numnov = {$numnov}
                                                      and v36_taxa = taxa.ar36_sequencial limit 1)

                         ) as tipolancamento,
                          sum(recibopaga.k00_valor) as valor
                     from taxa inner join recibopaga on ar36_receita = k00_receit
                    where k00_numnov = {$numnov}
                      and k00_hist in (11403, 11404)
                 group by taxa.ar36_sequencial,
                          taxa.ar36_receita,
                          taxa.ar36_descricao";

        $result = db_query($sql);

        return \db_utils::getCollectionByRecord($result);
    }
}
