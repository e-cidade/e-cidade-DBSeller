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

use ECidade\Tributario\Arrecadacao\Custas\Calculo\CalculoColecao;
use ECidade\Tributario\Arrecadacao\Custas\Enum\TipoLancamento;
use ECidade\Tributario\Arrecadacao\Repository\TermoTaxaParcela;
use ECidade\Tributario\Divida\Termo\Termo;
use ECidade\Tributario\Juridico\Inicial\Inicial;
use ECidade\Tributario\Juridico\InicialPartilha\InicialPartilha;
use ECidade\Tributario\Juridico\InicialPartilha\InicialPartilhaCustas;
use ECidade\Tributario\Arrecadacao\Custas\Calculo\Valor;
use \DateTime;
use \Taxa;

abstract class PartilhaAdministrativaParcelamento extends PartilhaAdministrativa
{
    protected $termo;

    private $termoTaxaParcelaRepository;

    private $taxasParcelas;

    protected $valores;

    public function __construct(CalculoColecao $calculoColesao, Inicial $inicial, Termo $termo)
    {
        parent::__construct($calculoColesao, $inicial);

        $this->termo = $termo;
        $this->termoTaxaParcelaRepository = TermoTaxaParcela::getInstance();
        $this->taxasParcelas = array();
    }

    protected function processarGeracaoPartilha(array $taxasEmissao)
    {
        $dataPartilha = new DateTime(date('Y-m-d', db_getsession("DB_datausu")));

        $inicialPartilha = new InicialPartilha();
        $inicialPartilha->setCodigoInicial($this->inicial->getCodigo());
        $inicialPartilha->setTipoLancamento(TipoLancamento::PAGAMENTO);
        $inicialPartilha->setDataPartilha($dataPartilha);

        $valorTotalPartilha = 0;

        foreach ($taxasEmissao as $taxaEmissao) {
            $valorCusta = $this->getValorTaxa($taxaEmissao);

            if ($valorCusta === null) {
                continue;
            }

            $inicialPartilhaCustas = new InicialPartilhaCustas();
            $inicialPartilhaCustas->setTaxa($taxaEmissao);
            $inicialPartilhaCustas->setCodigoTaxa($taxaEmissao->getCodigoTaxa());
            $inicialPartilhaCustas->setValor($valorCusta);
            $inicialPartilhaCustas->setDispensaLancamentoRecibo(false);

            $inicialPartilha->addCustas($inicialPartilhaCustas);

            $valorTotalPartilha += $valorCusta;
        }

        $inicialPartilha->setValorPartilha($valorTotalPartilha);

        return $inicialPartilha;
    }

    protected function processarTaxasParcelas()
    {
        $termoTaxasParcelas = $this->termoTaxaParcelaRepository->getByInstituicao();

        $taxasParcelas = array();

        foreach ($termoTaxasParcelas as $termoTaxaParcela) {
            $parcela = $termoTaxaParcela->getNumpar();

            if ($parcela > $this->termo->getTotalParcelas()) {
                $parcela = $this->termo->getTotalParcelas();
            }

            $taxasParcela[$termoTaxaParcela->getTaxa()] = $parcela;
        }

        $this->taxasParcelas = $taxasParcela;
    }

    protected function getParcelaTaxa($codigoTaxa)
    {
        if (empty($this->taxasParcelas)) {
            $this->processarTaxasParcelas();
        }

        return $this->taxasParcelas[$codigoTaxa];
    }

    protected function getValorTaxa(Taxa $taxa)
    {
        if (empty($this->valores)) {
            $this->valores = parent::processaValorCalculo();
        }

        $parcela = $this->getParcelaTaxa($taxa->getCodigoTaxa());

        if (empty($parcela)) {
            return null;
        }

        $valor = $this->valores[$parcela];

        if (!isset($valor) || !($valor instanceof Valor)) {
            return 0;
        }

        return $this->calculaValorTaxa($taxa, $valor);
    }
}
