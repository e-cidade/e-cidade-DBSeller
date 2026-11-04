<?php
/**
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

use App\Domain\Tributario\Juridico\Services\InicialDoForo\ParcelamentoCustasProcessoService;
use ECidade\Tributario\Arrecadacao\Custas\Calculo\CalculoColecao;
use ECidade\Tributario\Arrecadacao\Custas\Enum\TipoLancamento;
use ECidade\Tributario\Arrecadacao\Custas\Interfaces\Validador;
use ECidade\Tributario\Divida\Termo\Termo;
use ECidade\Tributario\Juridico\ProcessoForo\ProcessoForo;
use ECidade\Tributario\Arrecadacao\Repository\TermoTaxaParcela;
use ECidade\Tributario\Juridico\ProcessoForoPartilha\ProcessoForoPartilha as ProcessoForoPartilhaEntity;
use ECidade\Tributario\Juridico\ProcessoForoPartilha\ProcessoForoPartilhaCusta as ProcessoForoPartilhaCustaEntity;
use \DateTime;
use db_utils;
use \Taxa;

abstract class PartilhaJuridicaParcelamento extends PartilhaJuridica
{
    /**
     * @var Termo
     */
    protected $termo;

    /**
     * @var TermoTaxaParcela
     */
    private $termoTaxaParcelaRepository;

    private $taxasParcelas;

    protected $valores;

    public function __construct(
        CalculoColecao $calculo,
        Validador $validador,
        ProcessoForo $processoForo,
        Termo $termo
    ) {
        parent::__construct($calculo, $validador, $processoForo);

        $this->termo = $termo;
        $this->termoTaxaParcelaRepository = TermoTaxaParcela::getInstance();
        $this->taxasParcelas = array();
    }

    protected function processarGeracaoPartilha(array $taxasEmissao)
    {
        $dataPartilha = new DateTime(date('Y-m-d', db_getsession("DB_datausu")));

        $processoForoPartilha = new ProcessoForoPartilhaEntity;
        $processoForoPartilha->setDataPartilha($dataPartilha);
        $processoForoPartilha->setCodigoProcessoForo($this->processoForo->getCodigo());
        $processoForoPartilha->setTipoLancamento(TipoLancamento::PAGAMENTO);

        $valorTotalPartilha = 0;

        foreach ($taxasEmissao as $taxaEmissao) {
            $valorCusta = $this->getValorTaxa($taxaEmissao);

            if ($valorCusta === null) {
                continue;
            }

            $processoForoPartilhaCusta = new ProcessoForoPartilhaCustaEntity();
            $processoForoPartilhaCusta->setTaxa($taxaEmissao);
            $processoForoPartilhaCusta->setCodigoTaxa($taxaEmissao->getCodigoTaxa());
            $processoForoPartilhaCusta->setValor($valorCusta);
            $processoForoPartilhaCusta->setDispensaLancamentoRecibo(false);

            $processoForoPartilha->addCustas($processoForoPartilhaCusta);

            $valorTotalPartilha += $valorCusta;
        }

        $processoForoPartilha->setValorPartilha($valorTotalPartilha);

        return $processoForoPartilha;
    }

    protected function processarTaxasParcelas()
    {
        $termoTaxasParcelas = $this->termoTaxaParcelaRepository->getByInstituicao();

        $taxasParcela = array();

        foreach ($termoTaxasParcelas as $termoTaxaParcela) {
            $parcela = $termoTaxaParcela->getNumpar();

            if ($parcela > $this->termo->getTotalParcelas()) {
                $parcela = $this->termo->getTotalParcelas();
            }

            $taxasParcela[$termoTaxaParcela->getTaxa()] = $parcela;
        }

        if (db_utils::ativaParcelamentoCustas()) {
            $custasLiberadasParaParcelamento = $this->getCustasParcelamentoProcesso();
            
            foreach ($custasLiberadasParaParcelamento as $custaParcelameno) {
                $sequencial = $custaParcelameno->ar36_sequencial;
                
                if (!array_key_exists($sequencial, $taxasParcela)) {
                    $taxasParcela[$sequencial] = '1';
                }
            }
        }

        $this->taxasParcelas = $taxasParcela;
    }

    private function getCustasParcelamentoProcesso()
    {
        $parcelamentoCustasProcesso = new ParcelamentoCustasProcessoService();

        $parcelamentoCustasProcesso->setCustasProcesso($this->processoForo);

        return $parcelamentoCustasProcesso->getCustasProcesso();
    }

    protected function getParcelaTaxa($codigoTaxa)
    {
        if (empty($this->taxasParcelas)) {
            $this->processarTaxasParcelas();
        }

        if (!isset($this->taxasParcelas[$codigoTaxa])) {
            return null;
        }

        return $this->taxasParcelas[$codigoTaxa];
    }

    protected function getValorTaxa(Taxa $taxa)
    {
        if (empty($this->valores)) {
            $this->valores = parent::processaValorCalculo();
        }

        $parcela = $this->getParcelaTaxa($taxa->getCodigoTaxa());

        if (empty($parcela) || !array_key_exists($parcela, $this->valores)) {
            return null;
        }

        $valor = $this->valores[$parcela];

        return $this->calculaValorTaxa($taxa, $valor);
    }
}
