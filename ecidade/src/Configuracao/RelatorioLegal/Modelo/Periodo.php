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

namespace ECidade\Configuracao\RelatorioLegal\Modelo;

use ECidade\Configuracao\RelatorioLegal\Registry\PeriodoRegistry;

class Periodo
{
    /**
     * @var int
     */
    private $sequencial;
    /**
     * @var string
     */
    private $descricao;
    /**
     * @var int
     */
    private $quantidadePorAno;
    /**
     * @var int
     */
    private $diaInicial;
    /**
     * @var int
     */
    private $mesInicial;
    /**
     * @var int
     */
    private $diaFinal;
    /**
     * @var int
     */
    private $mesFinal;
    /**
     * @var string
     */
    private $sigla;
    /**
     * @var int
     */
    private $ordem;

    public static function fromState(array $state)
    {
        $self = new self();

        if (array_key_exists('o114_sequencial', $state)) {
            $self->setSequencial($state['o114_sequencial']);
        }

        if (array_key_exists('o114_descricao', $state)) {
            $self->setDescricao($state['o114_descricao']);
        }

        if (array_key_exists('o114_qdtporano', $state)) {
            $self->setQuantidadePorAno($state['o114_qdtporano']);
        }

        if (array_key_exists('o114_diainicial', $state)) {
            $self->setDiaInicial($state['o114_diainicial']);
        }

        if (array_key_exists('o114_mesinicial', $state)) {
            $self->setMesInicial($state['o114_mesinicial']);
        }

        if (array_key_exists('o114_diafinal', $state)) {
            $self->setDiaFinal($state['o114_diafinal']);
        }

        if (array_key_exists('o114_mesfinal', $state)) {
            $self->setMesFinal($state['o114_mesfinal']);
        }

        if (array_key_exists('o114_sigla', $state)) {
            $self->setSigla($state['o114_sigla']);
        }

        if (array_key_exists('o114_ordem', $state)) {
            $self->setOrdem($state['o114_ordem']);
        }

        PeriodoRegistry::set($self);

        return $self;
    }

    /**
     * @return int
     */
    public function getDiaInicial()
    {
        return (int)$this->diaInicial;
    }

    /**
     * @param int $diaInicial
     * @return Periodo
     */
    public function setDiaInicial($diaInicial)
    {
        $this->diaInicial = (int)$diaInicial;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'sequencial' => $this->getSequencial(),
            'descricao' => $this->getDescricao(),
            'quantidadePorAno' => $this->getQuantidadePorAno(),
            'diaInicial' => $this->getDiaInicial(),
            'mesInicial' => $this->getMesInicial(),
            'diaFinal' => $this->getDiaFinal(),
            'mesFinal' => $this->getMesFinal(),
            'sigla' => $this->getSigla(),
            'ordem' => $this->getOrdem(),
        );
    }

    /**
     * @return int
     */
    public function getSequencial()
    {
        return (int)$this->sequencial;
    }

    /**
     * @param int $sequencial
     * @return Periodo
     */
    public function setSequencial($sequencial)
    {
        $this->sequencial = (int)$sequencial;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescricao()
    {
        return (string)$this->descricao;
    }

    /**
     * @param string $descricao
     * @return Periodo
     */
    public function setDescricao($descricao)
    {
        $this->descricao = (string)$descricao;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantidadePorAno()
    {
        return (int)$this->quantidadePorAno;
    }

    /**
     * @param int $quantidadePorAno
     * @return Periodo
     */
    public function setQuantidadePorAno($quantidadePorAno)
    {
        $this->quantidadePorAno = (int)$quantidadePorAno;
        return $this;
    }

    /**
     * @return int
     */
    public function getDiaFinal()
    {
        return (int)$this->diaFinal;
    }

    /**
     * @param int $diaFinal
     * @return Periodo
     */
    public function setDiaFinal($diaFinal)
    {
        $this->diaFinal = (int)$diaFinal;
        return $this;
    }

    /**
     * @return int
     */
    public function getMesInicial()
    {
        return (int)$this->mesInicial;
    }

    /**
     * @param int $mesInicial
     * @return Periodo
     */
    public function setMesInicial($mesInicial)
    {
        $this->mesInicial = (int)$mesInicial;
        return $this;
    }

    /**
     * @return int
     */
    public function getMesFinal()
    {
        return (int)$this->mesFinal;
    }

    /**
     * @param int $mesFinal
     * @return Periodo
     */
    public function setMesFinal($mesFinal)
    {
        $this->mesFinal = (int)$mesFinal;
        return $this;
    }

    /**
     * @return string
     */
    public function getSigla()
    {
        return (string)$this->sigla;
    }

    /**
     * @param string $sigla
     * @return Periodo
     */
    public function setSigla($sigla)
    {
        $this->sigla = (string)$sigla;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrdem()
    {
        return (int)$this->ordem;
    }

    /**
     * @param int $ordem
     * @return Periodo
     */
    public function setOrdem($ordem)
    {
        $this->ordem = (int)$ordem;
        return $this;
    }
}
