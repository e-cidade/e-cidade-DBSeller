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

use ECidade\Tributario\Arrecadacao\Custas\Enum\Historico;
use ECidade\Tributario\Arrecadacao\Custas\Enum\TipoLancamento;
use ECidade\Tributario\Arrecadacao\Custas\Interfaces;
use ECidade\Tributario\Juridico\Interfaces\Partilha as PartilhaInterface;
use ECidade\Tributario\Juridico\InicialPartilha\InicialPartilha;
use ECidade\Tributario\Juridico\InicialPartilha\InicialPartilhaCustas;
use \Recibo as ReciboModel;
use \ECidade\Tributario\Arrecadacao\Custas\Partilha\PartilhaAdministrativaParcelamentoSimulacao as Simulacao;
use \Taxa;
use ECidade\Tributario\Arrecadacao\Custas\Calculo\Valor;

use ECidade\Tributario\Arrecadacao\Repository\CalculaParcelamentoHonorarioRepository;

final class PartilhaAdministrativaParcelamentoRecibo extends Simulacao implements Interfaces\PartilhaRecibo
{
    /**
     * @var ReciboModel
     */
    private $recibo;

    /**
     * @param ReciboModel $recibo
     */
    public function setRecibo(ReciboModel $recibo)
    {
        $this->recibo = $recibo;
    }

    /**
     * @param ReciboModel $recibo
     */
    public function processar()
    {
        $inicialPartilha = parent::processar();

        $inicialPartilhaCustas = $inicialPartilha->getCustas();

        $inicialPartilha->resetCustas();

        $calculoParcelamentoHonorario = new CalculaParcelamentoHonorarioRepository(
            $this->inicial,
            $this->inicialPartilhaRepository,
            $this->recibo
        );

        foreach ($inicialPartilhaCustas as $inicialPartilhaCusta) {
            if (!$this->reciboTemParcelaTaxa($inicialPartilhaCusta->getTaxa()->getCodigoTaxa())) {
                continue;
            }

            $inicialPartilhaCusta->setNumnov($this->recibo->getNumpreRecibo());
            $valorCusta = $calculoParcelamentoHonorario->calculaValorHonorario(
                $inicialPartilhaCusta,
                $inicialPartilha->getCodigoInicial(),
                false
            );

            if (empty($valorCusta)) {
                continue;
            }

            $inicialPartilhaCusta->setValor($valorCusta);

            $inicialPartilha->addCustas($inicialPartilhaCusta);
        }

        $inicialPartilhasIsentas = $this->getPartilhasIsentas();

        foreach ($inicialPartilhasIsentas as $inicialPartilhaIsenta) {
            $valorTotalPartilha = 0;

            $inicialPartilhaCustasIsenta = $inicialPartilhaIsenta->getCustas();
            $inicialPartilhaIsenta->resetCustas();

            foreach ($inicialPartilhaCustasIsenta as $inicialPartilhaCustaIsenta) {
                $valorCusta = $this->getValorTaxa($inicialPartilhaCustaIsenta->getTaxa());

                if ($valorCusta === null) {
                    continue;
                }

                $inicialPartilhaCustaIsenta->setNumnov($this->recibo->getNumpreRecibo());
                $inicialPartilhaCustaIsenta->setValor($valorCusta);

                $inicialPartilhaIsenta->addCustas($inicialPartilhaCustaIsenta);

                $valorTotalPartilha += $valorCusta;
            }

            $inicialPartilhaIsenta->setValorPartilha($valorTotalPartilha);

            $this->inicialPartilhaRepository->persist($inicialPartilhaIsenta);
        }

        $this->inicialPartilhaRepository->persist($inicialPartilha);

        $this->processarIsencaoPartilha($inicialPartilhasIsentas);
        $this->processarReciboPartilha($inicialPartilha);

        return $this->recibo;
    }

    /**
     * @param InicialPartilha[] $inicialPartilhasIsentas
     * @throws \DBException
     */
    public function processarIsencaoPartilha(array $inicialPartilhasIsentas)
    {
        foreach ($inicialPartilhasIsentas as $inicialPartilhaIsenta) {
            $inicialPartilhaIsentaCustas = $inicialPartilhaIsenta->getCustas();
            $inicialPartilhaIsenta->resetCustas();
            $inicialPartilhaIsenta->setCodigo(null);

            $valorTotalPartilha = 0;

            foreach ($inicialPartilhaIsentaCustas as $inicialPartilhaCustaIsenta) {
                $inicialPartilhaCusta = new InicialPartilhaCustas();
                $inicialPartilhaCusta->setCodigoTaxa($inicialPartilhaCustaIsenta->getCodigoTaxa());
                $inicialPartilhaCusta->setValor($inicialPartilhaCustaIsenta->getValor());
                $inicialPartilhaCusta->setNumnov(null);
                $inicialPartilhaCusta->setDispensaLancamentoRecibo(true);

                $inicialPartilhaIsenta->addCustas($inicialPartilhaCusta);

                $valorTotalPartilha += $inicialPartilhaCustaIsenta->getValor();
            }

            $inicialPartilhaIsenta->setValorPartilha($valorTotalPartilha);

            $this->inicialPartilhaRepository->persist($inicialPartilhaIsenta);
        }
    }

    /**
     * @param PartilhaInterface $inicialPartilha
     */
    public function processarReciboPartilha(PartilhaInterface $inicialPartilha)
    {
        foreach ($inicialPartilha->getCustas() as $inicialPartilhaCusta) {
            if ($inicialPartilhaCusta->isDispensaLancamentoRecibo()) {
                continue;
            }

            $numpar = $this->getParcelaTaxa($inicialPartilhaCusta->getTaxa()->getCodigoTaxa());

            if ($inicialPartilhaCusta->getTaxa()->isAplicaHonorario() && $this->inicial->getParcelasHonorarios() > 1) {
                $numpar = array();
                $debitos = $this->recibo->getDebitosRecibo();

                foreach ($debitos as $debito) {
                    if ($debito->k00_numpre == $this->termo->getNumpre()
                        && $debito->k00_numpar <= $this->inicial->getParcelasHonorarios()) {
                        $numpar[] = $debito->k00_numpar;
                    }
                }
            } else {
                $numpar = array($numpar);
            }

            $calculoParcelamentoHonorario = new CalculaParcelamentoHonorarioRepository(
                $this->inicial,
                $this->inicialPartilhaRepository,
                $this->recibo
            );

            foreach ($numpar as $parcela) {
                if ($inicialPartilhaCusta->getTaxa()->isAplicaHonorario()
                    && $this->inicial->getParcelasHonorarios() > 1) {
                    $valorCusta = $this->getValorTaxa($inicialPartilhaCusta->getTaxa());
                    $valorTaxa = $calculoParcelamentoHonorario->calculaValorTaxaParcela(
                        $valorCusta,
                        $parcela,
                        $inicialPartilha->getCodigoInicial(),
                        false,
                        $inicialPartilhaCusta->getTaxa()
                    );
                } else {
                    $valorTaxa = $inicialPartilhaCusta->getValor();
                }

                $this->recibo->adicionarReceitaCustaParcelamento(
                    $this->termo->getNumpre(),
                    $parcela,
                    $inicialPartilhaCusta->getTaxa(),
                    $valorTaxa,
                    Historico::CODIGO
                );
            }
        }
    }

    /**
     * @param $codigoTaxa
     * @return bool
     * @throws \DBException
     */
    private function reciboTemParcelaTaxa($codigoTaxa)
    {
        $numpar = $this->getParcelaTaxa($codigoTaxa);

        $debitos = $this->recibo->getDebitosRecibo();
        $taxa = $this->taxaRepository->getByCodigo($codigoTaxa);
        $parcelasHonorarios = $this->inicial->getParcelasHonorarios();

        foreach ($debitos as $debito) {
            if ($debito->k00_numpre == $this->termo->getNumpre()
                && ($debito->k00_numpar == $numpar
                    || ($taxa->isAplicaHonorario()
                        && !empty($parcelasHonorarios)
                        && $debito->k00_numpar <= $parcelasHonorarios))) {
                return true;
            }
        }

        return false;
    }
}
