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

namespace ECidade\Tributario\Arrecadacao\Custas\Partilha;

use ECidade\Tributario\Arrecadacao\Custas\Interfaces;
use ECidade\Tributario\Divida\Termo\Repository\TermoInicial as TermoInicialRepository;
use ECidade\Tributario\Juridico\Inicial\Repository\HistoricoDesmembramentoRepository;
use ECidade\Tributario\Juridico\Inicial\Repository\Inicial as InicialRepository;
use ECidade\Tributario\Juridico\ProcessoForo\ProcessoForo;
use ECidade\Tributario\Juridico\ProcessoForo\Repository;
use ECidade\Tributario\Juridico\ProcessoForoPartilha\Repository\ProcessoForoPartilha as ProcessoForoPartilhaRepository;
use ECidade\Tributario\Arrecadacao\Custas\Calculo\Valor;
use \Taxa;

abstract class PartilhaJuridica extends Partilha
{
    protected $validador;

    /**
     * @var ProcessoForo
     */
    protected $processoForo;

    /**
     * @var Repository\ProcessoForo
     */
    protected $processoForoRepository;

    /** @var InicialRepository */
    protected $inicialRepository;

    /**
     * @var ProcessoForoPartilhaRepository
     */
    protected $processoForoPartilhaRepository;

    public function __construct(
        Interfaces\Calculo $calculo,
        Interfaces\Validador $validador,
        ProcessoForo $processoForo,
        $iniciais = []
    ) {
        parent::__construct($calculo);
        parent::setCodigoProcessoForo($processoForo);
        parent::setCodigoIniciais($iniciais);

        $this->validador = $validador;
        $this->processoForo = $processoForo;
        $this->processoForoRepository = Repository\ProcessoForo::getInstance();
        $this->processoForoPartilhaRepository = ProcessoForoPartilhaRepository::getInstance();
        $this->inicialRepository = InicialRepository::getInstance();

        // PLUGINTAXAJURIDICAADICIONALPORNOME1
    }

    /**
     * @return \ECidade\Tributario\Juridico\ProcessoForoPartilha\ProcessoForoPartilha[]
     */
    protected function getPartilhasIsentas()
    {
        return $this->processoForoPartilhaRepository->getIsencaoByProcessoForoCodigo($this->processoForo->getCodigo());
    }

    /**
     * @return \ECidade\Tributario\Juridico\ProcessoForoPartilha\ProcessoForoPartilha[]
     */
    protected function getPartilhasPagas()
    {
        return $this->processoForoPartilhaRepository->getPagoByProcessoForoCodigo($this->processoForo->getCodigo());
    }

    protected function getPartilhasPagasSemHonrarios()
    {
        return $this->processoForoPartilhaRepository->getPagoSemHonorariosByProcessoForo($this->processoForo);
    }

    /**
     * @return null|\Taxa[]
     */
    protected function getTaxasEmissao()
    {
        return $this->taxaRepository->getTaxasProcessuais();
    }

    /**
     * @return \ECidade\Tributario\Juridico\ProcessoForoPartilha\ProcessoForoPartilha[]
     */
    protected function getPartilhasPagasManual()
    {
        return $this->processoForoPartilhaRepository->getPagoManualByProcessoForoCodigo(
            $this->processoForo->getCodigo()
        );
    }

    /**
     * @return array|Taxa[]|null
     * @throws \Exception
     */
    public function processar()
    {
        $partilhasIsentas = $this->getPartilhasIsentas();

        $partilhasPagasManual = $this->getPartilhasPagasManual();

        $taxasEmissao = $this->getTaxasEmissao();

        $taxasEmissao = $this->processaRemocaoTaxa($taxasEmissao, $partilhasIsentas);
        $taxasEmissao = $this->processaRemocaoTaxa($taxasEmissao, $partilhasPagasManual);
        $taxasEmissao = $this->processaTaxaFixa($taxasEmissao);

        $iniciais = $this->inicialRepository->getIniciaisAtivasPorProcesso($this->processoForo->getCodigo());

        if (count($iniciais) == 1) {
            /* *
             * codigo com regra para niteroi removido
             */
            // $taxasEmissao = $this->processaRemocaoTaxa($taxasEmissao, $this->getPartilhasPagasSemHonrarios());
        } else {
            $desmembramentoRepository = new HistoricoDesmembramentoRepository();
            $termoInicialRepository = TermoInicialRepository::getInstance();

            foreach ($iniciais as $inicial) {
                $history = $desmembramentoRepository->getHistoryByInitial($inicial->getCodigo());

                if (!$history) {
                    continue;
                }

                $possuiAnulacaoParcelamento = $termoInicialRepository->inicialPossuiAnulacaoParcelamento(
                    $inicial->getCodigo()
                );

                if ($possuiAnulacaoParcelamento) {
                    // remove as taxa pagas
                    $taxasEmissao = $this->processaRemocaoTaxa($taxasEmissao, $this->getPartilhasPagas());
                    break;
                }
            }
        }

        return $taxasEmissao;
    }

    public function processaRemocaoTaxa(array $taxas, array $partilhas)
    {
        $taxasRemover = array();

        foreach ($partilhas as $inicialPartilha) {
            foreach ($inicialPartilha->getCustas() as $inicialPartilhaCusta) {
                $taxa = $inicialPartilhaCusta->getTaxa();
                $taxasRemover[] = $taxa->getCodigoTaxa();
            }
        }

        $taxasRemover = array_unique($taxasRemover);

        foreach ($taxas as $i => $taxa) {
            if (in_array($taxa->getCodigoTaxa(), $taxasRemover)) {
                unset($taxas[$i]);
            }
        }

        return $taxas;
    }

    protected function processaTaxaFixa(array $taxas)
    {
        foreach ($taxas as $i => $taxa) {
            $emitir = $taxa->isFixo();

            if ($emitir) {
                $emitir = $this->validador->processarValidacao();
            }

            if ($emitir) {
                unset($taxas[$i]);
            }
        }

        return $taxas;
    }

    /**
     * @inheritdoc
     */
    public function calculaValorTaxa(Taxa $taxa, Valor $valor)
    {
        $valor = parent::calculaValorTaxa($taxa, $valor);

        // PLUGINTAXAJURIDICAADICIONALPORNOME2

        return $valor;
    }
}
