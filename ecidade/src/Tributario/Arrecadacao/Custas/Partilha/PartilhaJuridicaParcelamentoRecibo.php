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

use db_utils;
use ECidade\Tributario\Arrecadacao\Custas\Enum\Historico;
use ECidade\Tributario\Arrecadacao\Custas\Interfaces;
use ECidade\Tributario\Juridico\Interfaces\Partilha as PartilhaInterface;
use ECidade\Tributario\Juridico\ProcessoForoPartilha\ProcessoForoPartilha;
use ECidade\Tributario\Juridico\ProcessoForoPartilha\ProcessoForoPartilhaCusta;
use \Recibo as ReciboModel;
use \ECidade\Tributario\Arrecadacao\Custas\Partilha\PartilhaJuridicaParcelamentoSimulacao as Simulacao;
use Taxa;

use ECidade\Tributario\Arrecadacao\Repository\CalculaParcelamentoHonorarioRepository;
use ECidade\Tributario\Juridico\ProcessoForoPartilha\Repository\ProcessoForoPartilhaCusta
as RepositoryProcessoForoPartilhaCusta;

final class PartilhaJuridicaParcelamentoRecibo extends Simulacao implements Interfaces\PartilhaRecibo
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
     * @param bool $comCalculoCustas
     * @return array|ProcessoForoPartilha|ReciboModel|Taxa[]|null
     * @throws \DBException
     */
    public function processar($comCalculoCustas = true)
    {
        if (db_utils::ativaParcelamentoCustas()) {
            $processoForoPartilha = (object) parent::processar();

            $processoForoPartilhaCustas = $processoForoPartilha->getCustas();

            if (!empty($processoForoPartilhaCustas)) {
                $processoForoPartilha->resetCustas();

                $calculoParcelamentoHonorario = new CalculaParcelamentoHonorarioRepository(
                    $this->processoForo,
                    $this->processoForoPartilhaRepository,
                    $this->recibo
                );

                foreach ($processoForoPartilhaCustas as $processoForoPartilhaCusta) {
                    if (!$this->reciboTemParcelaTaxa($processoForoPartilhaCusta->getTaxa()->getCodigoTaxa())) {
                        continue;
                    }

                    $processoForoPartilhaCusta->setNumnov($this->recibo->getNumpreRecibo());
                    $valorCusta = $calculoParcelamentoHonorario->calculaValorHonorario(
                        $processoForoPartilhaCusta,
                        $processoForoPartilha->getCodigoProcessoForo(),
                        true
                    );

                    if (empty($valorCusta)) {
                        continue;
                    }

                    $processoForoPartilhaCusta->setValor($valorCusta);
                    $processoForoPartilha->addCustas($processoForoPartilhaCusta);
                }

                $this->processoForoPartilhaRepository->persist($processoForoPartilha, $comCalculoCustas);
                $this->processarReciboPartilha($processoForoPartilha);
            }

            $processoForoPartilhasIsentas = $this->getPartilhasIsentas();

            foreach ($processoForoPartilhasIsentas as $processoForoPartilhaIsenta) {
                $valorTotalPartilha = 0;

                $processoForoPartilhaCustasIsenta = $processoForoPartilhaIsenta->getCustas();
                $processoForoPartilhaIsenta->resetCustas();

                foreach ($processoForoPartilhaCustasIsenta as $processoForoPartilhaCustaIsenta) {
                    $valorCusta = $this->getValorTaxa($processoForoPartilhaCustaIsenta->getTaxa());

                    if ($valorCusta === null) {
                        continue;
                    }

                    $processoForoPartilhaCustaIsenta->setNumnov($this->recibo->getNumpreRecibo());
                    $processoForoPartilhaCustaIsenta->setValor($valorCusta);

                    $processoForoPartilhaIsenta->addCustas($processoForoPartilhaCustaIsenta);

                    $valorTotalPartilha += $valorCusta;
                }

                $processoForoPartilhaIsenta->setValorPartilha($valorTotalPartilha);

                if (!$valorTotalPartilha || $valorTotalPartilha == 0) {
                    continue;
                }

                $this->processoForoPartilhaRepository->persist($processoForoPartilhaIsenta, $comCalculoCustas);
            }

            $this->processarIsencaoPartilha($processoForoPartilhasIsentas);
            /*
                Ajuste para casos que as custas foram lançadas diretamente no arrecad:
                Se existir custas lançadas no arrecad e ainda não existe o processoforopartilhacustas,
                devemos usar os dados lançados no arrecad para criar a processoforopartilhacustas.
            */
            /*  Monta lista Numpre/Taxa  */
            $sListaNumpreTaxa = "";
            $sOr = "";
            foreach ($this->recibo->getDebitosRecibo() as $oDebito) {
                $sListaNumpreTaxa .= $sOr . " (k00_numpre ="
                    . $oDebito->k00_numpre
                    . " and k00_numpar ="
                    . $oDebito->k00_numpar . ")";
                $sOr = " OR ";
            }
            if (!empty($sListaNumpreTaxa)) {
                $sSqlCustasArrecad = "select k00_numpre,
                                         k00_receit,
                                         sum(k00_valor) as valor_taxa,
                                         k00_hist,
                                         ar36_receita,
                                         ar36_sequencial as codigo_taxa,
                                         ar36_descricao
                                    from arrecad inner join taxa on k00_receit = ar36_receita
                                   where k00_hist in (11303, 11304) /* Fixo para debito no arrecad de custas */
                                     and ({$sListaNumpreTaxa})
                                group by k00_numpre, k00_receit, k00_hist, ar36_receita, ar36_sequencial, ar36_descricao
                                order by k00_numpre, ar36_sequencial;";
                $rsDebitosCustas = db_query($sSqlCustasArrecad);

                $totRegCustas = pg_num_rows($rsDebitosCustas);

                if ($totRegCustas > 0) {
                    for ($i = 0; $i < $totRegCustas; $i++) {
                        $oDebitoCustas = (object) pg_fetch_assoc($rsDebitosCustas, $i);

                        $oCustas = new ProcessoForoPartilhaCusta;
                        $oCustas->setCodigoTaxa($oDebitoCustas->codigo_taxa);
                        $oCustas->setCodigoProcessoForoPartilha($processoForoPartilha->getCodigo());
                        $oCustas->setValor($oDebitoCustas->valor_taxa);
                        $oCustas->setNumnov($this->recibo->getNumpreRecibo());
                        $oCustas->setDispensaLancamentoRecibo(false);

                        $processoForoPartilhaCustaRepository = RepositoryProcessoForoPartilhaCusta::getInstance();
                        $processoForoPartilhaCustaRepository->persist($oCustas);
                    }
                }
            }

            return $this->recibo;
        } else {
            $processoForoPartilha = (object)parent::processar();

            $processoForoPartilhaCustas = $processoForoPartilha->getCustas();

            if (!empty($processoForoPartilhaCustas)) {
                $processoForoPartilha->resetCustas();

                $calculoParcelamentoHonorario = new CalculaParcelamentoHonorarioRepository(
                    $this->processoForo,
                    $this->processoForoPartilhaRepository,
                    $this->recibo
                );

                foreach ($processoForoPartilhaCustas as $processoForoPartilhaCusta) {
                    if (!$this->reciboTemParcelaTaxa($processoForoPartilhaCusta->getTaxa()->getCodigoTaxa())) {
                        continue;
                    }

                    $processoForoPartilhaCusta->setNumnov($this->recibo->getNumpreRecibo());
                    $valorCusta = $calculoParcelamentoHonorario->calculaValorHonorario(
                        $processoForoPartilhaCusta,
                        $processoForoPartilha->getCodigoProcessoForo(),
                        true
                    );

                    if (empty($valorCusta)) {
                        continue;
                    }

                    $processoForoPartilhaCusta->setValor($valorCusta);
                    $processoForoPartilha->addCustas($processoForoPartilhaCusta);
                }

                $this->processoForoPartilhaRepository->persist($processoForoPartilha);
                $this->processarReciboPartilha($processoForoPartilha);
            }

            $processoForoPartilhasIsentas = $this->getPartilhasIsentas();

            foreach ($processoForoPartilhasIsentas as $processoForoPartilhaIsenta) {
                $valorTotalPartilha = 0;

                $processoForoPartilhaCustasIsenta = $processoForoPartilhaIsenta->getCustas();
                $processoForoPartilhaIsenta->resetCustas();

                foreach ($processoForoPartilhaCustasIsenta as $processoForoPartilhaCustaIsenta) {
                    $valorCusta = $this->getValorTaxa($processoForoPartilhaCustaIsenta->getTaxa());

                    if ($valorCusta === null) {
                        continue;
                    }

                    $processoForoPartilhaCustaIsenta->setNumnov($this->recibo->getNumpreRecibo());
                    $processoForoPartilhaCustaIsenta->setValor($valorCusta);

                    $processoForoPartilhaIsenta->addCustas($processoForoPartilhaCustaIsenta);

                    $valorTotalPartilha += $valorCusta;
                }

                $processoForoPartilhaIsenta->setValorPartilha($valorTotalPartilha);

                $this->processoForoPartilhaRepository->persist($processoForoPartilhaIsenta);
            }

            $this->processarIsencaoPartilha($processoForoPartilhasIsentas);

            return $this->recibo;
        }
    }
    /**
     * @param processoForoPartilha[] $processoForoPartilhasIsentas
     */
    public function processarIsencaoPartilha(array $processoForoPartilhasIsentas)
    {
        foreach ($processoForoPartilhasIsentas as $processoForoPartilhaIsenta) {
            $processoForoPartilhaIsentaCustas = $processoForoPartilhaIsenta->getCustas();
            $processoForoPartilhaIsenta->resetCustas();
            $processoForoPartilhaIsenta->setCodigo(null);

            $valorTotalPartilha = 0;

            foreach ($processoForoPartilhaIsentaCustas as $processoForoPartilhaCustaIsenta) {
                $processoForoPartilhaCusta = new ProcessoForoPartilhaCusta();
                $processoForoPartilhaCusta->setCodigoTaxa($processoForoPartilhaCustaIsenta->getCodigoTaxa());
                $processoForoPartilhaCusta->setValor($processoForoPartilhaCustaIsenta->getValor());
                $processoForoPartilhaCusta->setNumnov(0);
                $processoForoPartilhaCusta->setDispensaLancamentoRecibo(true);

                $processoForoPartilhaIsenta->addCustas($processoForoPartilhaCusta);

                $valorTotalPartilha += $processoForoPartilhaCustaIsenta->getValor();
            }

            if (!$valorTotalPartilha) {
                continue;
            }

            $processoForoPartilhaIsenta->setValorPartilha($valorTotalPartilha);

            $this->processoForoPartilhaRepository->persist($processoForoPartilhaIsenta);
        }
    }

    /**
     * @param PartilhaInterface $processoForoPartilha
     */
    public function processarReciboPartilha(PartilhaInterface $processoForoPartilha)
    {
        foreach ($processoForoPartilha->getCustas() as $processoForoPartilhaCusta) {
            if ($processoForoPartilhaCusta->isDispensaLancamentoRecibo()) {
                continue;
            }

            $numpar = $this->getParcelaTaxa($processoForoPartilhaCusta->getTaxa()->getCodigoTaxa());

            if ($processoForoPartilhaCusta->getTaxa()->isAplicaHonorario()
                && $this->processoForo->getParcelasHonorarios() > 1
            ) {
                $numpar = array();

                $debitos = $this->recibo->getDebitosRecibo();

                foreach ($debitos as $debito) {
                    if ($debito->k00_numpre == $this->termo->getNumpre()
                        && $debito->k00_numpar <= $this->processoForo->getParcelasHonorarios()
                    ) {
                        $numpar[] = $debito->k00_numpar;
                    }
                }
            } else {
                $numpar = array($numpar);
            }

            $calculoParcelamentoHonorario = new CalculaParcelamentoHonorarioRepository(
                $this->processoForo,
                $this->processoForoPartilhaRepository,
                $this->recibo
            );

            foreach ($numpar as $parcela) {
                if ($processoForoPartilhaCusta->getTaxa()->isAplicaHonorario()
                    && $this->processoForo->getParcelasHonorarios() > 1
                ) {
                    $valorCusta = $this->getValorTaxa($processoForoPartilhaCusta->getTaxa());
                    $valorTaxa = $calculoParcelamentoHonorario->calculaValorTaxaParcela(
                        $valorCusta,
                        $parcela,
                        $processoForoPartilha->getCodigoProcessoForo(),
                        true,
                        $processoForoPartilhaCusta->getTaxa()
                    );
                } else {
                    $valorTaxa = $processoForoPartilhaCusta->getValor();
                }

                $this->recibo->adicionarReceitaCustaParcelamento(
                    $this->termo->getNumpre(),
                    $parcela,
                    $processoForoPartilhaCusta->getTaxa(),
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
        $parcelasHonorarios = $this->processoForo->getParcelasHonorarios();

        foreach ($debitos as $debito) {
            if ($debito->k00_numpre == $this->termo->getNumpre()
                && ($debito->k00_numpar == $numpar
                    || ($taxa->isAplicaHonorario()
                        && !empty($parcelasHonorarios)
                        && $debito->k00_numpar <= $parcelasHonorarios))
            ) {
                return true;
            }
        }

        return false;
    }
}
