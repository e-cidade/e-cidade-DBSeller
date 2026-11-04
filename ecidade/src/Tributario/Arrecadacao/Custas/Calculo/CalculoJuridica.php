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

use ECidade\Tributario\Arrecadacao\Custas\Interfaces;
use ECidade\Tributario\Juridico\ProcessoForo\ProcessoForo;

final class CalculoJuridica extends Calculo implements Interfaces\Calculo
{
    private $processoForo;

    private $numnov;

    private $dataVencimento;

    public function __construct(ProcessoForo $processoForo, $numnov, $dataVencimento)
    {
        $this->processoForo = $processoForo;
        $this->numnov = $numnov;
        $this->dataVencimento = $dataVencimento;
    }

    public function calcular()
    {
        $anoVencimento = substr($this->dataVencimento, 0, 4);

        $sql = " select sum(substr(fc_calcula, 15, 13)::numeric(10, 2)) as valor_corrigido,
                        sum(substr(fc_calcula, 28, 13)::numeric(10, 2)) as valor_juros,
                        sum(substr(fc_calcula, 41, 13)::numeric(10, 2)) as valor_multa,
                        sum(substr(fc_calcula, 54, 13)::numeric(10, 2)) as valor_desconto
                   from (select k00_numpre,
                                fc_calcula(k00_numpre, k00_numpar, k00_receit, '$this->dataVencimento',
                                           '$this->dataVencimento', $anoVencimento)
                           from arrecad
                          where k00_numpre in
                                (select v59_numpre
                                 from inicialnumpre
                                      join recibopaga on recibopaga.k00_numpre = inicialnumpre.v59_numpre
                                                     and recibopaga.k00_numnov = $this->numnov
                                 where v59_inicial in (select v71_inicial
                                                       from processoforoinicial
                                                       where v71_processoforo = {$this->processoForo->getCodigo()}))
                          group by k00_numpre,
                                   k00_numpar,
                                   k00_receit,
                                   k00_dtvenc
                        ) as calculo ";

        $rs = db_query($sql);

        if (!$rs) {
            throw new \Exception("Não foi possivel consultar o valor base de calculo das custas.");
        }

        $objeto = pg_fetch_object($rs, 0);

        return $this->factory(
            $objeto->valor_corrigido,
            $objeto->valor_juros,
            $objeto->valor_multa,
            $objeto->valor_desconto
        );
    }
}
