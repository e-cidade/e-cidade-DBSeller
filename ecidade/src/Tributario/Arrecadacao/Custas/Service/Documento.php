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

namespace ECidade\Tributario\Arrecadacao\Custas\Service;

use ECidade\Tributario\Arrecadacao\Custas\Calculo\CalculoAdministrativaParcelamentoDocumento;
use ECidade\Tributario\Arrecadacao\Custas\Calculo\CalculoJuridicaParcelamentoDocumento;
use ECidade\Tributario\Arrecadacao\Custas\Interfaces\Service;
use ECidade\Tributario\Arrecadacao\Custas\Partilha\PartilhaAdministrativaParcelamentoDocumento;
use ECidade\Tributario\Arrecadacao\Custas\Partilha\PartilhaJuridicaParcelamentoDocumento;
use ECidade\Tributario\Arrecadacao\Custas\Validador\FixaJuridicaParcelamento;
use ECidade\Tributario\Arrecadacao\Repository\TermoTaxaParcela;
use ECidade\Tributario\Divida\Termo\Repository\Termo;
use ECidade\Tributario\Juridico\ProcessoForo\Repository\ProcessoForo;

final class Documento implements Service
{
    /**
     * @var Termo
     */
    private $termo;

    public function __construct($termo)
    {
        $this->termo = $termo;

        $this->termoTaxaParcelaRepository = TermoTaxaParcela::getInstance();
        $this->processoForoRepository = ProcessoForo::getInstance();
    }

    public function processar()
    {
        $termoIniciais = $this->termo->getTermoIniciais();

        $iniciais = array();
        $processos = array();

        foreach ($termoIniciais as $termoInicial) {
            $inicial = $termoInicial->getInicial();

            $processo = $this->processoForoRepository->getByInicial($inicial->getCodigo());

            if (empty($processo)) {
                $iniciais[] = $inicial;
            } else {
                $processos[] = $processo;
            }
        }

        $processos = array_unique($processos);

        $partilhas = array();

        foreach ($iniciais as $inicial) {
            $calculoAdministrativaParcelamentoSimulacao = new CalculoAdministrativaParcelamentoDocumento(
                $this->termo,
                $inicial
            );

            $partilhas[] = new PartilhaAdministrativaParcelamentoDocumento(
                $calculoAdministrativaParcelamentoSimulacao,
                $inicial,
                $this->termo
            );
        }

        foreach ($processos as $processo) {
            $calculoJuridicaParcelamentoSimulacao = new CalculoJuridicaParcelamentoDocumento($this->termo, $processo);

            $fixaJuridicaParcelamento = new FixaJuridicaParcelamento($this->termo, $processo);

            $partilhas[] = new PartilhaJuridicaParcelamentoDocumento(
                $calculoJuridicaParcelamentoSimulacao,
                $fixaJuridicaParcelamento,
                $processo,
                $this->termo
            );
        }

        $custas = array();

        foreach ($partilhas as $partilha) {
            $custas[] = $partilha->processar();
        }

        return $custas;
    }
}
