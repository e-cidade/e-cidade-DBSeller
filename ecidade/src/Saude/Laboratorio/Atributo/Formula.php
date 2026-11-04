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

namespace ECidade\Saude\Laboratorio\Atributo;

use AtributoExame;
use ECidade\Lib\Formula\FormulaCompiler;
use ECidade\Lib\Formula\FormulaInterface;
use RequisicaoExame;

/**
 * Class Formula
 * @package ECidade\Saude\Laboratorio\Atributo
 */
class Formula extends FormulaCompiler implements FormulaInterface
{
    /**
     * @var RequisicaoExame
     */
    private $requisicaoExame;

    /**
     * @var bool
     */
    private $temAtributoSemResultado = false;

    /**
     * @var AtributoExame
     */
    private $atributoExame;

    /**
     * @var bool
     */
    private $buscarValoresVariaveis = false;

    private $resultadosAtributos = null;

    /**
     * Formula constructor.
     * @param $formula
     * @param RequisicaoExame $requisicaoExame
     * @param AtributoExame $atributoExame
     * @param bool $buscarValoresVariaveis
     */
    public function __construct(
        $formula,
        RequisicaoExame $requisicaoExame,
        AtributoExame $atributoExame,
        $buscarValoresVariaveis = false,
        $resultadosAtributos = null
    ) {
        $formula = str_replace('[', '', str_replace(']', '', $formula));
        parent::__construct($formula);

        $this->requisicaoExame = $requisicaoExame;
        $this->atributoExame = $atributoExame;
        $this->buscarValoresVariaveis = $buscarValoresVariaveis;
        $this->resultadosAtributos = $resultadosAtributos;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function executarCalculo($exibirHistorico = false)
    {
        if ($this->buscarValoresVariaveis === true) {
            if ($exibirHistorico) {
                $this->getValoresVariaveisOtimizado();
            } else {
                $this->getValoresVariaveis();
            }
        }

        if ($this->temAtributoSemResultado === true) {
            return '';
        }

        return $this->validaDecimais($this->execute());
    }

    /**
     * @throws \Exception
     */
    private function getValoresVariaveis()
    {
        $atributosFormula = $this->atributoExame->getSiglasFormula();
        $resultadosAtributos = $this->requisicaoExame->getResultado()->getResultadoDosAtributos();

        if (empty($resultadosAtributos)) {
            $this->temAtributoSemResultado = true;
        }

        foreach ($resultadosAtributos as $resultadoAtributo) {
            if (!in_array($resultadoAtributo->getAtributo()->getSigla(), $atributosFormula)) {
                continue;
            }

            $atributoExame = $resultadoAtributo->getAtributo();
            $atributoValorReferencia = $atributoExame->getValoresDeReferenciaParaExame($this->requisicaoExame);

            $valor = $resultadoAtributo->getValorAbsoluto();

            if ($atributoValorReferencia instanceof \AtributoValorReferenciaNumerico
                && $atributoValorReferencia->getTipoCalculo() == 2) {
                $valor = $resultadoAtributo->getValorPercentual();
            }

            if (strlen($valor) == 0) {
                $this->temAtributoSemResultado = true;
            }

            $this->addVariavelValor($resultadoAtributo->getAtributo()->getSigla(), $valor);
        }

        if ($this->totalVariaveis() < count($atributosFormula)) {
            $this->temAtributoSemResultado = true;
        }
    }

    /**
     * @param $resultado
     * @return string
     */
    public function validaDecimais($resultado)
    {
        $resultadoPartes = explode('.', $resultado);

        if (isset($resultadoPartes[1])) {
            $resultado = number_format($resultado, 2);
        }

        return str_replace(',', '', $resultado);
    }

    /**
     * @return bool
     */
    public function temAtributoSemResultado()
    {
        return $this->temAtributoSemResultado;
    }

    /**
     * @param $chave
     * @param $valor
     */
    public function setVariavelValor($chave, $valor)
    {
        $this->addVariavelValor($chave, $valor);
    }

    /**
     * @throws \Exception
     */
    private function getValoresVariaveisOtimizado()
    {
        $atributosFormula = $this->atributoExame->getSiglasFormula();

        $resultadosAtributos = $this->resultadosAtributos;

        if (empty($resultadosAtributos)) {
            $this->temAtributoSemResultado = true;
            return;
        }

        foreach ($resultadosAtributos as $resultadoAtributo) {
            if (!in_array($resultadoAtributo->getAtributo()->getSigla(), $atributosFormula)) {
                continue;
            }

            $atributoExame = $resultadoAtributo->getAtributo();
            //$atributoValorReferencia = $atributoExame->getValoresDeReferenciaParaExame($this->requisicaoExame);

            $valor = $resultadoAtributo->getValorAbsoluto();

//            if ($atributoValorReferencia instanceof \AtributoValorReferenciaNumerico
//                && $atributoValorReferencia->getTipoCalculo() == 2) {
//                $valor = $resultadoAtributo->getValorPercentual();
//            }

            if (strlen($valor) == 0) {
                $this->temAtributoSemResultado = true;
            }

            $this->addVariavelValor($resultadoAtributo->getAtributo()->getSigla(), $valor);
        }

        if ($this->totalVariaveis() < count($atributosFormula)) {
            $this->temAtributoSemResultado = true;
        }
    }
}
