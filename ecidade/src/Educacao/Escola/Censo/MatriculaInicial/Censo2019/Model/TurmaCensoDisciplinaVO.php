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

namespace ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model;

use ECidade\Educacao\Escola\Model\CensoDisciplina;
use ECidade\Educacao\Escola\Registry\CensoDisciplinaRegistry;
use Exception;

class TurmaCensoDisciplinaVO
{

    /**
     * @var CensoDisciplina
     */
    protected $disciplina;

    protected $tipoBase;
    protected $unidadeCurricular;
    /**
     * @var bool
     */
    protected $oferece = true;

    /**
     * @return CensoDisciplina
     */
    public function getDisciplina()
    {
        return $this->disciplina;
    }

    /**
     * @param CensoDisciplina $disciplina
     * @return TurmaCensoDisciplinaVO
     */
    public function setDisciplina($disciplina)
    {
        $this->disciplina = $disciplina;
        return $this;
    }

    /**
     * @return bool
     */
    public function isOferece()
    {
        return $this->oferece;
    }

    /**
     * @param bool $oferece
     * @return TurmaCensoDisciplinaVO
     */
    public function setOferece($oferece)
    {
        $this->oferece = $oferece;
        return $this;
    }

    public function setTipoBase($tipoBase)
    {
        $sql = "select * from tipobase where ed182_id = {$tipoBase}";

        $this->tipoBase = pg_fetch_assoc(pg_query($sql));
    }

    public function getTipoBase()
    {
        return $this->tipoBase;
    }

    /**
     * @return mixed
     */
    public function getUnidadeCurricular()
    {
        return $this->unidadeCurricular;
    }

    /**
     * @param mixed $unidadeCurricular
     * @return TurmaCensoDisciplinaVO
     */
    public function setUnidadeCurricular($unidadeCurricular)
    {
        $this->unidadeCurricular = $unidadeCurricular;
        return $this;
    }

    /**
     * @param $state
     * @return TurmaCensoDisciplinaVO
     */
    public static function fromState($state)
    {
        $self = new self();

        if (array_key_exists('ed294_censodisciplina', $state)) {
            $self->setDisciplina(CensoDisciplinaRegistry::get($state['ed294_censodisciplina']));
        }

        if (array_key_exists('ed59_tipobase', $state)) {
            if (is_null($state['ed59_tipobase'])) {
                $mensagemErro = "Erro. Disciplina sem Tipo de Base definido.";
                $mensagemErro .= "\nAcesse DB:EDUCAÇÃO > Escola > Cadastros > Turmas > Alteração > ";
                $mensagemErro .= "Aba 'Disciplinas' > Verificar o tipo de base das disciplinas. Turma para ser ";
                $mensagemErro .= "verificada {$state['codigo_turma']}.";
                throw new Exception($mensagemErro);
            }
            $self->setTipoBase($state['ed59_tipobase']);
            $self->setUnidadeCurricular($state['ed59_unidade_curricular']);
        }

        if (array_key_exists('oferece', $state)) {
            $self->setOferece($state['oferece'] == 't');
        }

        return $self;
    }
}
