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

namespace ECidade\Tributario\Juridico\Repository;

final class Desmembramento
{
    private $instituicao;
    private $data;
    private $ano;
    private $filtro = array();

    public function __construct($instituicao, $data, $ano)
    {
        $this->instituicao = $instituicao;
        $this->data = $data;
        $this->ano = $ano;
    }

    public function getByCgm($cgm)
    {
        return $this->get(
            $cgm,
            "arrenumcgm.k00_numcgm",
            "inner join arrenumcgm on arrenumcgm.k00_numpre = arrecad.k00_numpre"
        );
    }

    public function getByMatricula($matricula)
    {
        return $this->get(
            $matricula,
            "arrematric.k00_matric",
            "inner join arrematric on arrematric.k00_numpre = arrecad.k00_numpre"
        );
    }

    public function getByInscricao($inscricao)
    {
        return $this->get(
            $inscricao,
            "arreinscr.k00_inscr",
            "inner join arreinscr on arreinscr.k00_numpre = arrecad.k00_numpre"
        );
    }

    public function getByProcessoForo($processoForo)
    {
        return $this->get($processoForo, 'processoforo.v70_sequencial');
    }

    /**
     * Verifica se a CDA foi parcialmente selecionada.
     *
     * @param integer $cda
     *
     * @param array $dividas
     *
     * @return bool
     */
    public function cdaParcialmenteSelecionada($cda, array $dividas)
    {
        $dao = new \cl_certdiv();

        $sql = $dao->sql_query_file(
            null,
            null,
            '*',
            null,
            'v14_coddiv NOT IN(' . implode(',', $dividas) . ') AND v14_certid = ' . $cda
        );

        $result = \db_query($sql);

        if (pg_num_rows($result) > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param integer $certidao
     * @param integer $divida
     *
     * @throws \Exception
     */
    public function removeCertDiv($certidao, $divida)
    {
        $sqlDelete = "DELETE FROM certdiv WHERE v14_certid = {$certidao} AND v14_coddiv = {$divida}";
        $result = \db_query($sqlDelete);

        if (!$result) {
            throw new \Exception('Não foi possível excluir registro da tabela certdiv.');
        }
    }

    /**
     * @param integer $inicial
     * @param integer $certidao
     *
     * @throws \Exception
     */
    public function removeInicialCert($inicial, $certidao)
    {
        $sSql = "DELETE FROM inicialcert WHERE v51_inicial = {$inicial} AND v51_certidao = {$certidao}";
        $result = \db_query($sSql);

        if (!$result) {
            throw new \Exception('Não foi possível excluir registro da tabela inicialcert.');
        }
    }

    /**
     * Valida se foi selecionado todos numpres iguais.
     *
     * @param array $dividas
     *
     * @return bool
     */
    public function validaNumpresSelecionados(array $dividas)
    {
        $dividas = implode(',', $dividas);

        $sql = "
            SELECT TRUE
            FROM (SELECT DISTINCT v01_coddiv
                  FROM divida
                  WHERE v01_numpre IN (SELECT v01_numpre
                                       FROM divida
                                       WHERE v01_coddiv IN ({$dividas})
                  )
                 ) AS divida_distinct
            WHERE divida_distinct.v01_coddiv NOT IN ({$dividas})
            LIMIT 1;
        ";

        $result = \db_query($sql);

        if (pg_num_rows($result) > 0) {
            return false;
        }

        return true;
    }

    /**
     * Retorna os códigos das certidões a partir das dividas.
     *
     * @param array $dividas
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getCertidoesPorDividas($dividas)
    {
        $dividas = implode(',', $dividas);

        $dao = new \cl_certdiv();
        $sql = $dao->sql_query(
            null,
            null,
            'DISTINCT v14_certid',
            null,
            "v14_coddiv IN({$dividas})"
        );

        $result = \db_query($sql);

        if (!pg_num_rows($result)) {
            throw new \Exception('Nenhuma certidão encontrada para divida informada.');
        }

        $data = array();
        foreach (pg_fetch_all($result) as $item) {
            $data[] = $item['v14_certid'];
        }

        return $data;
    }

    /**
     * @param array $dividas
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getIniciaisPorDividas($dividas)
    {
        $dividas = implode(',', $dividas);

        $sql = "
            SELECT DISTINCT
                inicialcert.v51_inicial,
                inicialcert.v51_certidao
            FROM inicialcert
                INNER JOIN certdiv ON certdiv.v14_certid = inicialcert.v51_certidao
            WHERE certdiv.v14_coddiv IN ({$dividas});
        ";

        $result = \db_query($sql);

        if (!pg_num_rows($result)) {
            throw new \Exception('Nenhuma inicial encontrada para as dívidas selecionadas');
        }

        $data = array();
        foreach (pg_fetch_all($result) as $item) {
            if (empty($data[$item['v51_inicial']])) {
                $data[$item['v51_inicial']] = array();
            }
            $data[$item['v51_inicial']][] = $item['v51_certidao'];
        }

        return $data;
    }

    /**
     * Retorna um array com a inicias com suas origens.
     * @param string valor do campo para pesquisa,
     * @param string $campo para pesquisa
     * @param string $tabela tabela pra realiza o join com a consulta princial
     *
     * @return array lista de inicial
     *
     * @throws \Exception
     */
    private function get($valor, $campo, $tabela = '')
    {
        $sql = "select distinct
                       processoforo.v70_codforo as codigo_processo,
                       processoforo.v70_sequencial as sequencial_processo,
                       inicial.v50_inicial as codigo_inicial,
                       certid.v13_certid as codigo_certidao,
                       divida.v01_coddiv as codigo_divida,
                       divida.v01_exerc as exercicio_divida,
                       proced.v03_codigo as codigo_procedencia,
                       proced.v03_descr as nome_procedencia,
                       arrecad.k00_numpre as numpre,
                       arrecad.k00_numpar as numpar,
                       arrecad.k00_numcgm as codigo_cgm,
                       arrecad.k00_dtoper as data_operacao,
                       arrecad.k00_receit as receita,
                       arrecad.k00_hist as codigo_historico,
                       arrecad.k00_dtvenc as data_vencimento,
                       arrecad.k00_numtot as total_parcelas,
                       arrecad.k00_tipo as tipo_debito,
                       tabrec.k02_descr as receita_descricao,
                       (select row(substr(fc_calcula, 2, 13)::float8,
                                   substr(fc_calcula, 15, 13)::float8,
                                   substr(fc_calcula, 28, 13)::float8,
                                   substr(fc_calcula, 41, 13)::float8,
                                   substr(fc_calcula, 54, 13)::float8,
                                   (substr(fc_calcula, 15, 13)::float8 +
                                    substr(fc_calcula, 28, 13)::float8 +
                                    substr(fc_calcula, 41, 13)::float8 -
                                    substr(fc_calcula, 54, 13)::float8))
                          from (select fc_calcula(arrecad.k00_numpre,
                                                  arrecad.k00_numpar,
                                                  arrecad.k00_receit,
                                                  '" . date('Y-m-d', $this->data) . "',
                                                  '" . date("Y-m-d", $this->data) . "'," . $this->ano . ")
                                                 ) as fc_calcula
                       ) as valores
                  from arrecad
                       inner join arreinstit on arreinstit.k00_numpre = arrecad.k00_numpre
                                            and arreinstit.k00_instit = " . $this->instituicao . "
                       {$tabela}
                       inner join arretipo on arretipo.k00_tipo = arrecad.k00_tipo
                       inner join divida on divida.v01_numpre = arrecad.k00_numpre
                                        and divida.v01_numpar = arrecad.k00_numpar
                       inner join proced on proced.v03_codigo = divida.v01_proced
                       inner join certdiv on certdiv.v14_coddiv = divida.v01_coddiv
                       inner join certid on certid.v13_certid = certdiv.v14_certid
                       inner join inicialcert on inicialcert.v51_certidao = certid.v13_certid
                       inner join inicial on inicial.v50_inicial = inicialcert.v51_inicial
                       inner join tabrec on tabrec.k02_codigo = arrecad.k00_receit
                       left  join processoforoinicial on processoforoinicial.v71_inicial = inicial.v50_inicial
                                                     and processoforoinicial.v71_anulado is false
                       left  join processoforo on processoforo.v70_sequencial = processoforoinicial.v71_processoforo
                                              and processoforo.v70_anulado is false
                 where {$campo} = {$valor}
                   and arretipo.k03_tipo = 18
                   and inicial.v50_situacao = 1
                   {$this->montarWhere()}
              order by processoforo.v70_sequencial asc,
                       divida.v01_exerc desc,
                       inicial.v50_inicial desc,
                       arrecad.k00_numpre desc,
                       arrecad.k00_numpar asc,
                       arrecad.k00_receit desc";

        $rs = db_query($sql);

        if (!$rs) {
            throw new \Exception("Não foi possível consultar os dados de inicial.");
        }

        return \db_utils::getCollectionByRecord($rs);
    }

    public function setFiltro(\stdClass $filtro)
    {
        foreach ((array)$filtro as $campo => $valor) {
            switch ($campo) {
                case 'inicial':
                    $this->filtro[] = "inicial.v50_inicial = {$valor}";
                    break;
                case 'exercicio':
                    $this->filtro[] = "divida.v01_exerc = {$valor}";
                    break;
                case 'cda':
                    $this->filtro[] = "certid.v13_certid = {$valor}";
                    break;
            }
        }
    }

    private function montarWhere()
    {
        if (count($this->filtro) > 0) {
            return ' AND ' . implode(' AND ', $this->filtro);
        }

        return '';
    }
}
