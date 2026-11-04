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

use ECidade\Tributario\Arrecadacao\Custas\Calculo\CalculoAdministrativaParcelamentoSimulacao;
use ECidade\Tributario\Arrecadacao\Custas\Calculo\CalculoJuridicaParcelamentoSimulacao;
use ECidade\Tributario\Arrecadacao\Custas\Interfaces\Service;
use ECidade\Tributario\Arrecadacao\Custas\Partilha\PartilhaAdministrativaParcelamentoSimulacao;
use ECidade\Tributario\Arrecadacao\Custas\Partilha\PartilhaJuridicaParcelamentoSimulacao;
use ECidade\Tributario\Arrecadacao\Custas\Validador\FixaJuridicaParcelamento;
use ECidade\Tributario\Divida\Termo\Termo;
use ECidade\Tributario\Juridico\ProcessoForo\Repository\ProcessoForo;

final class Simulacao implements Service
{
    /**
     * @var Termo
     */
    private $termo;

    /**
     * @var ProcessoForo
     */
    private $processoForoRepository;

    public function __construct(Termo $termo)
    {
        $this->termo = $termo;
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
            $calculoAdministrativaParcelamentoSimulacao = new CalculoAdministrativaParcelamentoSimulacao(
                $this->termo,
                $inicial
            );

            $partilhas[] = new PartilhaAdministrativaParcelamentoSimulacao(
                $calculoAdministrativaParcelamentoSimulacao,
                $inicial,
                $this->termo
            );
        }

        foreach ($processos as $processo) {
            $calculoJuridicaParcelamentoSimulacao = new CalculoJuridicaParcelamentoSimulacao($this->termo, $processo);

            $fixaJuridicaParcelamento = new FixaJuridicaParcelamento($this->termo, $processo);

            $partilhas[] = new PartilhaJuridicaParcelamentoSimulacao(
                $calculoJuridicaParcelamentoSimulacao,
                $fixaJuridicaParcelamento,
                $processo,
                $this->termo
            );
        }

        $partilhasTermo = array();

        foreach ($partilhas as $partilha) {
            $partilhasTermo[] = $partilha->processar();
        }

        return $partilhasTermo;
    }
}
