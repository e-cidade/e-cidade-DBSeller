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
use ECidade\Tributario\Juridico\Inicial\Inicial;

final class CalculoAdministrativa extends Calculo implements Interfaces\Calculo
{
    private $inicial;

    private $numnov;

    public function __construct(Inicial $inicial, $numnov)
    {
        $this->inicial = $inicial;
        $this->numnov = $numnov;
    }

    public function calcular()
    {
        $sql = " select sum(case when k00_hist <> 401 and k00_hist <> 400 and
                                      k00_hist <> 918 then k00_valor else 0 end) as valor_corrigido,
                        sum(case when k00_hist = 400 then k00_valor else 0 end) as valor_juros,
                        sum(case when k00_hist = 401 then k00_valor else 0 end) as valor_multa,
                        sum(case when k00_hist = 918 then k00_valor else 0 end) as valor_desconto
                   from recibopaga
                  where k00_numnov = $this->numnov
                    and k00_numpre in (select v59_numpre
                        	             from inicialnumpre
                    	                where v59_inicial = {$this->inicial->getCodigo()}) ";

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
