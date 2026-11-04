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

namespace ECidade\RecursosHumanos\Pessoal\Model;

use DBException;

class MenorAprendiz
{
    /**
     * @var int
     */
    private $codigo;
    /**
     * @var int
     */
    private $matricula;
    /**
     * @var int
     */
    private $codigoModalidade;
    /**
     * @var string
     */
    private $codigoInstituicao;
    /**
     * @var string
     */
    private $cnpjDireta;
    /**
     * @var string
     */
    private $cnpjIndireta;
    /**
     * @var string
     */
    private $cnpjPratica;

    /**
     * @var int $codigo
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }

    /**
     * @return int
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * @var int $matricula
     */
    public function setMatricula($matricula)
    {
        $this->matricula = $matricula;
    }

    /**
     * @var int
     */
    public function getMatricula()
    {
        return $this->matricula;
    }

    /**
     * @var int $codigoModalidade
     */
    public function setModalidade($codigoModalidade)
    {
        $this->codigoModalidade = $codigoModalidade;
    }

    /**
     * @return int
     */
    public function getModalidade()
    {
        return $this->codigoModalidade;
    }

    /**
     * @var int $codigoInstituicaoo
     */
    public function setCodigoInstituicao($codigoInstituicao)
    {
        $this->codigoInstituicao = $codigoInstituicao;
    }

    /**
     * @return int
     */
    public function getCodigoInstituicao()
    {
        return $this->codigoInstituicao;
    }

    /**
     * @var string $cnpjDireta
     */
    public function setCnpjDireta($cnpjDireta)
    {
        $this->cnpjDireta = $cnpjDireta;
    }

    /**
     * @return string
     */
    public function getCnpjDireta()
    {
        return $this->cnpjDireta;
    }

    /**
     * @var string $cnpjIndireta
     */
    public function setCnpjIndireta($cnpjIndireta)
    {
        $this->cnpjIndireta = $cnpjIndireta;
    }

    /**
     * @return string
     */
    public function getCnpjIndireta()
    {
        return $this->cnpjIndireta;
    }

    /**
     * @var string $cnpjPratica
     */
    public function setCnpjPratica($cnpjPratica)
    {
        $this->cnpjPratica = $cnpjPratica;
    }

    /**
     * @return string
     */
    public function getCnpjPratica()
    {
        return $this->cnpjPratica;
    }

    public function __construct($matricula = null)
    {
        if (!empty($matricula)) {
            $sql = "select * from pessoal.rhvinculoaprendiz where rh312_matricula = {$matricula}";
            $rs = db_query($sql);
            if (!$rs) {
                throw new DBException("Erro ao buscar dados de Menor Aprendiz da Matrícula {$matricula}.");
            }
        }
    }

    public static function fromState(array $state)
    {
        $self = new self();

        if (array_key_exists('rh312_sequencial', $state)) {
            $self->setCodigo((int)$state['rh312_sequencial']);
        }

        if (array_key_exists('rh312_matricula', $state)) {
            $self->setMatricula((int)$state['rh312_matricula']);
        }

        if (array_key_exists('rh312_instit', $state)) {
            $self->setCodigoInstituicao((string)$state['rh312_instit']);
        }

        if (array_key_exists('rh312_modalidade', $state)) {
            $self->setModalidade((string)$state['rh312_modalidade']);
        }

        if (array_key_exists('rh312_cnpjqualificadora', $state)) {
            $self->setCnpjDireta((string)$state['rh312_cnpjqualificadora']);
        }

        if (array_key_exists('rh312_cnpjefetivada', $state)) {
            $self->setCnpjIndireta((string)$state['rh312_cnpjefetivada']);
        }

        if (array_key_exists('rh312_cnpjpratica', $state)) {
            $self->setCnpjPratica((string)$state['rh312_cnpjpratica']);
        }

        return $self;
    }
}
