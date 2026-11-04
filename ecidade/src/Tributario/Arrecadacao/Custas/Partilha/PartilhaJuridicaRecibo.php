<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2018  DBSeller Servicos de Informatica
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

use ECidade\Tributario\Arrecadacao\Custas\Enum;
use ECidade\Tributario\Arrecadacao\Custas\Interfaces;
use ECidade\Tributario\Juridico;
use \Recibo as ReciboModel;
use \DateTime;
use db_utils;

final class PartilhaJuridicaRecibo extends PartilhaJuridica implements Interfaces\PartilhaRecibo
{
    /** @var ReciboModel */
    private $recibo;

    /**
     * @param ReciboModel $recibo
     */
    public function setRecibo(ReciboModel $recibo)
    {
        $this->recibo = $recibo;
    }

    /**
     * @return ReciboModel
     */
    public function processar()
    {
        $taxasEmissao = parent::processar();

        $partilhasPagas = $this->getPartilhasPagas();

        /**
         * desativado remocao de custas pagas para valenca
         */
        // $taxasEmissao = $this->processaRemocaoTaxaPaga($taxasEmissao, $partilhasPagas);

        $aIniciais = $this->verificaIniciaisVinculadasProcesso();

        /**
         * Se somente uma inicial foi selecionada para emitir recibo
         * e existe mais de uma inicial vinculada ao processo
        */
        if (count($this->iniciais) == 1 and count($aIniciais) > 1) {
            $existeReciboValido = $this->verificaReciboValido();
            
            $arrayTaxasPercentual = array();
            $arrayTaxasFixas = array();

            foreach ($taxasEmissao as $key => $taxa) {
                if ($taxa->isPorcentagem()) {
                    $arrayTaxasPercentual[] = $key;
                }
            }

            foreach ($taxasEmissao as $key => $taxa) {
                if ($taxa->isFixo()) {
                    $arrayTaxasFixas[] = $key;
                }
            }

            if (count($arrayTaxasFixas) > 0) {
                foreach ($arrayTaxasPercentual as $taxaPercentual) {
                    unset($taxasEmissao[$taxaPercentual]);
                }
            } else {
                if ($existeReciboValido) {
                    unset($taxasEmissao);
                }
            }
        }

        $valor = $this->processaValorCalculo();

        if (!empty($taxasEmissao)) {
            $dataPartilha = new DateTime(date('Y-m-d', db_getsession("DB_datausu")));

            $processoForoPartilha = new Juridico\ProcessoForoPartilha\ProcessoForoPartilha();
            $processoForoPartilha->setDataPartilha($dataPartilha);
            $processoForoPartilha->setCodigoProcessoForo($this->processoForo->getCodigo());
            $processoForoPartilha->setTipoLancamento(Enum\TipoLancamento::PAGAMENTO);

            $valorTotalPartilha = 0;

            foreach ($taxasEmissao as $taxaEmissao) {
                $valorCusta = $this->calculaValorTaxa($taxaEmissao, $valor);

                if ($valorCusta === null) {
                    continue;
                }

                /**
                * desativado diminuicao do preco das custas quando ja pago
                */
                // if (db_utils::ativaParcelamentoCustas()){}
                // $valorPago = $this->processoForoPartilhaRepository->getValorPago($taxaEmissao, $this->processoForo);

                // $valorCusta -= $valorPago;

                if ($valorCusta <= 0) {
                    continue;
                }

                $processoForoPartilhaCusta = new Juridico\ProcessoForoPartilha\ProcessoForoPartilhaCusta();
                $processoForoPartilhaCusta->setTaxa($taxaEmissao);
                $processoForoPartilhaCusta->setCodigoTaxa($taxaEmissao->getCodigoTaxa());
                $processoForoPartilhaCusta->setDispensaLancamentoRecibo(false);
                $processoForoPartilhaCusta->setNumnov($this->recibo->getNumpreRecibo());
                $processoForoPartilhaCusta->setValor($valorCusta);

                $valorTotalPartilha += $valorCusta;

                $processoForoPartilha->addCustas($processoForoPartilhaCusta);
            }

            $custas = $processoForoPartilha->getCustas();

            if (!empty($custas)) {
                $processoForoPartilha->setValorPartilha($valorTotalPartilha);

                $this->processoForoPartilhaRepository->persist($processoForoPartilha);

                $this->processarReciboPartilha($processoForoPartilha);
            }
        }

        $processoForoPartilhasIsentas = $this->getPartilhasIsentas();

        foreach ($processoForoPartilhasIsentas as $processoForoPartilhaIsenta) {
            $valorTotalPartilha = 0;

            foreach ($processoForoPartilhaIsenta->getCustas() as $processoForoPartilhaCusta) {
                $valorCusta = $this->calculaValorTaxa($processoForoPartilhaCusta->getTaxa(), $valor);

                if ($valorCusta === null) {
                    continue;
                }

                $processoForoPartilhaCusta->setValor($valorCusta);
                $processoForoPartilhaCusta->setNumnov($this->recibo->getNumpreRecibo());

                $valorTotalPartilha += $valorCusta;
            }

            $processoForoPartilhaIsenta->setValorPartilha($valorTotalPartilha);

            $this->processoForoPartilhaRepository->persist($processoForoPartilhaIsenta);
        }

        $this->processarIsencaoPartilha($processoForoPartilhasIsentas);

        return $this->recibo;
    }

    /**
     * @param Juridico\ProcessoForoPartilha\ProcessoForoPartilha[] $processoForoPartilhaIsentas
     */
    public function processarIsencaoPartilha(array $processoForoPartilhaIsentas)
    {
        foreach ($processoForoPartilhaIsentas as $processoForoPartilhaIsenta) {
            $custas = $processoForoPartilhaIsenta->getCustas();
            $processoForoPartilhaIsenta->resetCustas();
            $processoForoPartilhaIsenta->setCodigo(null);

            $valorTotalPartilha = 0;

            foreach ($custas as $custa) {
                $processoForoPartilhaCusta = new Juridico\ProcessoForoPartilha\ProcessoForoPartilhaCusta();
                $processoForoPartilhaCusta->setCodigoTaxa($custa->getCodigoTaxa());
                $processoForoPartilhaCusta->setValor($custa->getValor());
                $processoForoPartilhaCusta->setNumnov(0);
                $processoForoPartilhaCusta->setDispensaLancamentoRecibo(true);

                $processoForoPartilhaIsenta->addCustas($processoForoPartilhaCusta);

                $valorTotalPartilha += $custa->getValor();
            }

            $processoForoPartilhaIsenta->setValorPartilha($valorTotalPartilha);

            $this->processoForoPartilhaRepository->persist($processoForoPartilhaIsenta);
        }
    }

    /**
     * @param Juridico\Interfaces\Partilha $partilha
     *
     * @return ReciboModel
     */
    public function processarReciboPartilha(Juridico\Interfaces\Partilha $partilha)
    {
        foreach ($partilha->getCustas() as $processoForoPartilhaCusta) {
            if ($processoForoPartilhaCusta->isDispensaLancamentoRecibo()) {
                continue;
            }

            $this->recibo->adicionarReceitaCusta(
                $processoForoPartilhaCusta->getTaxa()->getReceita(),
                $processoForoPartilhaCusta->getValor(),
                Enum\Historico::CODIGO,
                $processoForoPartilhaCusta->getTaxa()
            );
        }
    }
}
