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
use \DateTime;

final class PartilhaAdministrativaRecibo extends PartilhaAdministrativa implements Interfaces\PartilhaRecibo
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
     * @param ReciboModel $recibo
     */
    public function processar()
    {
        $taxasEmissao = parent::processar();

        $partilhasPagas = $this->getPartilhasPagas();
        $taxasEmissao = $this->processaRemocaoTaxaPaga($taxasEmissao, $partilhasPagas);

        $valor = $this->processaValorCalculo();

        if (!empty($taxasEmissao)) {
            $dataPartilha = new DateTime(date('Y-m-d', db_getsession("DB_datausu")));

            $inicialPartilha = new InicialPartilha();
            $inicialPartilha->setCodigoInicial($this->inicial->getCodigo());
            $inicialPartilha->setTipoLancamento(TipoLancamento::PAGAMENTO);
            $inicialPartilha->setDataPartilha($dataPartilha);

            $valorTotalPartilha = 0;
            $inicialPartilha->setValorPartilha($valorTotalPartilha);

            foreach ($taxasEmissao as $taxaEmissao) {
                $valorCusta = $this->calculaValorTaxa($taxaEmissao, $valor);

                $valorCusta -= $this->inicialPartilhaRepository->getValorPago($taxaEmissao, $this->inicial);

                if ($valorCusta <= 0) {
                    continue;
                }

                $inicialPartilhaCustas = new InicialPartilhaCustas();
                $inicialPartilhaCustas->setTaxa($taxaEmissao);
                $inicialPartilhaCustas->setCodigoTaxa($taxaEmissao->getCodigoTaxa());
                $inicialPartilhaCustas->setValor($valorCusta);
                $inicialPartilhaCustas->setDispensaLancamentoRecibo(false);
                $inicialPartilhaCustas->setNumnov($this->recibo->getNumpreRecibo());

                $inicialPartilha->addCustas($inicialPartilhaCustas);

                $valorTotalPartilha += $valorCusta;
            }

            $inicialPartilha->setValorPartilha($valorTotalPartilha);

            $this->inicialPartilhaRepository->persist($inicialPartilha);

            $this->processarReciboPartilha($inicialPartilha);
        }

        $inicialPartilhasIsentas = $this->getPartilhasIsentas();

        foreach ($inicialPartilhasIsentas as $inicialPartilhaIsenta) {
            foreach ($inicialPartilhaIsenta->getCustas() as $inicialPartilhaCustaIsenta) {
                $valorCusta = $this->calculaValorTaxa($inicialPartilhaCustaIsenta->getTaxa(), $valor);

                $inicialPartilhaCustaIsenta->setValor($valorCusta);
                $inicialPartilhaCustaIsenta->setNumnov($this->recibo->getNumpreRecibo());
            }

            $this->inicialPartilhaRepository->persist($inicialPartilhaIsenta);
        }

        $this->processarIsencaoPartilha($inicialPartilhasIsentas);

        return $this->recibo;
    }

    /**
     * @param InicialPartilha[] $inicialPartilhasIsentas
     */
    public function processarIsencaoPartilha(array $inicialPartilhasIsentas)
    {
        foreach ($inicialPartilhasIsentas as $inicialPartilhaIsenta) {
            $inicialPartilhaIsentaCustas = $inicialPartilhaIsenta->getCustas();
            $inicialPartilhaIsenta->resetCustas();
            $inicialPartilhaIsenta->setCodigo(null);

            foreach ($inicialPartilhaIsentaCustas as $inicialPartilhaCustaIsenta) {
                $inicialPartilhaCusta = new InicialPartilhaCustas();
                $inicialPartilhaCusta->setCodigoTaxa($inicialPartilhaCustaIsenta->getCodigoTaxa());
                $inicialPartilhaCusta->setInicialPartilha($inicialPartilhaCustaIsenta->getInicialPartilha());
                $inicialPartilhaCusta->setValor(0);
                $inicialPartilhaCusta->setNumnov(null);
                $inicialPartilhaCusta->setDispensaLancamentoRecibo(true);

                $inicialPartilhaIsenta->addCustas($inicialPartilhaCusta);
            }

            $inicialPartilhaIsenta->setValorPartilha(0);

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

            $this->recibo->adicionarReceitaCusta(
                $inicialPartilhaCusta->getTaxa()->getReceita(),
                $inicialPartilhaCusta->getValor(),
                Historico::CODIGO,
                $inicialPartilhaCusta->getTaxa()
            );
        }
    }
}
