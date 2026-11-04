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

namespace ECidade\Tributario\Arrecadacao\Custas\Calculo;

use db_utils;
use ECidade\Tributario\Arrecadacao\Custas\Interfaces;
use ECidade\Tributario\Divida\Termo\Termo;
use ECidade\Tributario\Juridico\ProcessoForo\ProcessoForo;
use Exception;

final class CalculoJuridicaParcelamentoSimulacao extends CalculoColecao implements Interfaces\Calculo
{
    private $termo;

    private $processo;

    public function __construct(Termo $termo, ProcessoForo $processo)
    {
        $this->termo = $termo;
        $this->processo = $processo;
    }

    public function calcular()
    {
        if (db_utils::ativaParcelamentoCustas()) {
            $sql = "select x.k00_numpar as numpar,
            sum(fc_corre(x.receit, x.dtoper, x.valor::float8, x.arrecad_dtvenc ,
            extract(year from x.arrecad_dtvenc)::int4, x.arreold_dtvenc)) +
            sum(round((fc_corre(x.receit, x.dtoper, x.valor::float8, x.arrecad_dtvenc,
            extract(year from x.arrecad_dtvenc)::int4, x.arreold_dtvenc)) *
            fc_juros(x.receit, x.arreold_dtvenc, x.arrecad_dtvenc, x.dtoper, false,
            extract(year from x.arrecad_dtvenc)::integer)::numeric(20, 10), 2)) +
            sum(round((fc_corre(x.receit, x.dtoper, x.valor::float8, x.arrecad_dtvenc ,
            extract(year from x.arrecad_dtvenc)::int4, x.arreold_dtvenc)) *
            fc_multa(x.receit, x.arreold_dtvenc, x.arrecad_dtvenc, x.dtoper,
            extract(year from x.arrecad_dtvenc)::integer)::numeric(20, 10), 2)) as valor
            from (select distinct
            arrecad.k00_numpre,
            arrecad.k00_numpar,
            arreold.k00_receit as receit,
            arreold.k00_dtoper as dtoper,
            arreold.k00_valor as valor,
            arreold.k00_dtvenc as arreold_dtvenc,
            arrecad.k00_dtvenc as arrecad_dtvenc
            from arrecad
            inner join termo on termo.v07_numpre = arrecad.k00_numpre
            inner join termoini on termoini.parcel = termo.v07_parcel
            inner join processoforoinicial on processoforoinicial.v71_inicial = termoini.inicial
            inner join inicialcert on inicialcert.v51_inicial = termoini.inicial
            inner join certdiv on certdiv.v14_certid = inicialcert.v51_certidao
            inner join divida on divida.v01_coddiv = certdiv.v14_coddiv
            inner join arreold on arreold.k00_numpre = divida.v01_numpre
            and arreold.k00_numpar = divida.v01_numpar
            inner join arreoldcalc on arreoldcalc.k00_numpre = arreold.k00_numpre
            and arreoldcalc.k00_numpar = arreold.k00_numpar
            and arreoldcalc.k00_receit = arreold.k00_receit
            where termo.v07_parcel = {$this->termo->getCodigo()}
            and processoforoinicial.v71_processoforo = {$this->processo->getCodigo()}
            ) as x
            group by k00_numpar
            order by k00_numpar asc ";

            $rs = db_query($sql);

            if (!$rs) {
                throw new Exception("Não foi possivel consultar o valor base de calculo das custas.");
            }

            $colecao = $this->colecao($rs);

            if (count($colecao) == 0) {
                $sql = "select x.k00_numpar as numpar,
                sum(fc_corre(x.receit, x.dtoper, x.valor::float8, x.arrecad_dtvenc ,
                extract(year from x.arrecad_dtvenc)::int4, x.arreold_dtvenc)) +
                sum(round((fc_corre(x.receit, x.dtoper, x.valor::float8, x.arrecad_dtvenc,
                extract(year from x.arrecad_dtvenc)::int4, x.arreold_dtvenc)) *
                fc_juros(x.receit, x.arreold_dtvenc, x.arrecad_dtvenc, x.dtoper, false,
                extract(year from x.arrecad_dtvenc)::integer)::numeric(20, 10), 2)) +
                sum(round((fc_corre(x.receit, x.dtoper, x.valor::float8, x.arrecad_dtvenc ,
                extract(year from x.arrecad_dtvenc)::int4, x.arreold_dtvenc)) *
                fc_multa(x.receit, x.arreold_dtvenc, x.arrecad_dtvenc, x.dtoper,
                extract(year from x.arrecad_dtvenc)::integer)::numeric(20, 10), 2)) as valor
                from (select distinct
                arrecad.k00_numpre,
                arrecad.k00_numpar,
                arrecad.k00_receit as receit,
                arrecad.k00_dtoper as dtoper,
                arrecad.k00_valor as valor,
                arrecad.k00_dtvenc as arreold_dtvenc,
                arrecad.k00_dtvenc as arrecad_dtvenc
                from
                arrecad
                inner join termo on termo.v07_numpre = arrecad.k00_numpre
                inner join termoini on termoini.parcel = termo.v07_parcel
                inner join processoforoinicial on processoforoinicial.v71_inicial = termoini.inicial
                where
                termo.v07_parcel = {$this->termo->getCodigo()}
                and processoforoinicial.v71_processoforo = {$this->processo->getCodigo()}
                ) as x
                group by k00_numpar
                order by k00_numpar asc ";

                $rs = db_query($sql);

                if (!$rs) {
                    throw new Exception("Não foi possivel consultar o valor base de calculo das custas.");
                }

                $colecao = $this->colecao($rs);
            }

            return $colecao;
        } else {
            $sql = "select x.k00_numpar as numpar,
            sum(fc_corre(x.receit, x.dtoper, x.valor::float8, x.arrecad_dtvenc ,
                         extract(year from x.arrecad_dtvenc)::int4, x.arreold_dtvenc)) +
            sum(round((fc_corre(x.receit, x.dtoper, x.valor::float8, x.arrecad_dtvenc,
                       extract(year from x.arrecad_dtvenc)::int4, x.arreold_dtvenc)) *
                fc_juros(x.receit, x.arreold_dtvenc, x.arrecad_dtvenc, x.dtoper, false,
                         extract(year from x.arrecad_dtvenc)::integer)::numeric(20, 10), 2)) +
            sum(round((fc_corre(x.receit, x.dtoper, x.valor::float8, x.arrecad_dtvenc ,
                                extract(year from x.arrecad_dtvenc)::int4, x.arreold_dtvenc)) *
                fc_multa(x.receit, x.arreold_dtvenc, x.arrecad_dtvenc, x.dtoper,
                         extract(year from x.arrecad_dtvenc)::integer)::numeric(20, 10), 2)) as valor
       from (select distinct
                    arrecad.k00_numpre,
                    arrecad.k00_numpar,
                    arreold.k00_receit as receit,
                    arreold.k00_dtoper as dtoper,
                    arreold.k00_valor as valor,
                    arreold.k00_dtvenc as arreold_dtvenc,
                    arrecad.k00_dtvenc as arrecad_dtvenc
               from arrecad
                    inner join termo on termo.v07_numpre = arrecad.k00_numpre
                    inner join termoini on termoini.parcel = termo.v07_parcel
                    inner join processoforoinicial on processoforoinicial.v71_inicial = termoini.inicial
                    inner join inicialcert on inicialcert.v51_inicial = termoini.inicial
                    inner join certdiv on certdiv.v14_certid = inicialcert.v51_certidao
                    inner join divida on divida.v01_coddiv = certdiv.v14_coddiv
                    inner join arreold on arreold.k00_numpre = divida.v01_numpre
                                      and arreold.k00_numpar = divida.v01_numpar
                    inner join arreoldcalc on arreoldcalc.k00_numpre = arreold.k00_numpre
                                          and arreoldcalc.k00_numpar = arreold.k00_numpar
                                          and arreoldcalc.k00_receit = arreold.k00_receit
              where termo.v07_parcel = {$this->termo->getCodigo()}
                and processoforoinicial.v71_processoforo = {$this->processo->getCodigo()}
            ) as x
        group by k00_numpar
        order by k00_numpar asc ";

            $rs = db_query($sql);

            if (!$rs) {
                throw new Exception("Não foi possivel consultar o valor base de calculo das custas.");
            }

            return $this->colecao($rs);
        }
    }

    protected function colecao($rs)
    {
        $rows = pg_fetch_all($rs);

        $valores = array();

        if ($rows && count($rows) > 0) {
            foreach ($rows as $row) {
                $valores[$row["numpar"]] = $this->factory($row["valor"], 0, 0, 0);
            }
        }

        return $valores;
    }
}
