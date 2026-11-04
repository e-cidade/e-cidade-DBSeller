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

namespace ECidade\Tributario\Juridico\ProcessoEletronico\Domain;

/**
 * Class Certidao
 * @package ECidade\Tributario\Juridico\ProcessoEletronico\Domain
 */
class Certidao
{
    /**
     * @var  $numero_certidao
     */
    private $numero_certidao;

    /**
     * @var $ano_exercicio
     */
    private $ano_exercicio;

    /**
     * @var $moeda_divida
     */
    private $moeda_divida;

    /**
     * @var $valor_divida
     */
    private $valor_divida;

    /**
     * @var $natureza_divida
     */
    private $natureza_divida;

    /**
     * @var $base_legal
     */
    private $base_legal;

    /**
     * @var MOEDA_DIVIDA_DEFAULT string
     */
    const MOEDA_DIVIDA_DEFAULT = 'R$';


    /**
     * @return mixed
     */
    public function getNumeroCertidao()
    {
        return $this->numero_certidao;
    }

    /**
     * @param $numero_certidao
     */
    public function setNumeroCertidao($numero_certidao)
    {
        $this->numero_certidao = $numero_certidao;
    }

    /**
     * @return mixed
     */
    public function getAnoExercicio()
    {
        return $this->ano_exercicio;
    }

    /**
     * @param $ano_exercicio
     */
    public function setAnoExercicio($ano_exercicio)
    {
        $this->ano_exercicio = $ano_exercicio;
    }

    /**
     * @return MOEDA_DIVIDA_DEFAULT
     */
    public function getMoedaDivida()
    {
        return (!empty($this->moeda_divida) ? $this->moeda_divida : self::MOEDA_DIVIDA_DEFAULT);
    }

    /**
     * @param $moeda_divida
     */
    public function setMoedaDivida($moeda_divida)
    {
        $this->moeda_divida = $moeda_divida;
    }

    /**
     * @return mixed
     */
    public function getValorDivida()
    {
        return $this->valor_divida;
    }

    /**
     * @param $valor_divida
     */
    public function setValorDivida($valor_divida)
    {
        $this->valor_divida = $valor_divida;
    }

    /**
     * @return mixed
     */
    public function getNaturezaDivida()
    {
        return $this->natureza_divida;
    }

    /**
     * @param $natureza_divida
     */
    public function setNaturezaDivida($natureza_divida)
    {
        $this->natureza_divida = $natureza_divida;
    }


    /**
     * @return mixed
     */
    public function getBaseLegal()
    {
        return $this->base_legal;
    }

    /**
     * @param $base_legal
     */
    public function setBaseLegal($base_legal)
    {
        $this->base_legal = $base_legal;
    }
}
