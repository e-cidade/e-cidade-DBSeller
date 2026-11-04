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

namespace ECidade\Educacao\Escola\Relatorios\DiarioClasse\Model;

use Curso;
use Etapa;

/**
 * Class TurmaDiarioClasse
 * @package ECidade\Educacao\Escola\Relatorios\DiarioClasse\Model
 */
class TurmaDiarioClasse
{
    /**
     * @var integer
     */
    protected $codigo;

    /**
     * @var string
     */
    protected $nome;

    /**
     * @var Curso
     */
    protected $curso;

    protected $aulasDadas;


    /**
     * Etapas selecionadas para impressão
     * @var Etapa[]
     */
    protected $etapas = [];

    /**
     * @return integer
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * @param integer $codigo
     * @return TurmaDiarioClasse
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
        return $this;
    }

    /**
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param string $nome
     * @return TurmaDiarioClasse
     */
    public function setNome($nome)
    {
        $this->nome = $nome;
        return $this;
    }

    /**
     * @return Curso
     */
    public function getCurso()
    {
        return $this->curso;
    }

    /**
     * @param Curso $curso
     * @return TurmaDiarioClasse
     */
    public function setCurso($curso)
    {
        $this->curso = $curso;
        return $this;
    }

    /**
     * @return Etapa[]
     */
    public function getEtapas()
    {
        return $this->etapas;
    }

    /**
     * @param Etapa[] $etapas
     * @return TurmaDiarioClasse
     */
    public function setEtapas(array $etapas)
    {
        $this->etapas = $etapas;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAulasDadas()
    {
        return $this->aulasDadas;
    }

    /**
     * @param mixed $aulasDadas
     * @return TurmaDiarioClasse
     */
    public function setAulasDadas($aulasDadas)
    {
        $this->aulasDadas = $aulasDadas;
        return $this;
    }
}
