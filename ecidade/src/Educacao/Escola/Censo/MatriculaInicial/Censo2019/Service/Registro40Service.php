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

namespace ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Service;

use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Builder\Registro40Builder;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro40;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Repository\Registro40Repository;
use ECidade\Educacao\Escola\Model\ProfissionalEscola;
use ECidade\Educacao\Escola\Registry\ProfissionalEscolaRegistry;
use ECidade\RecursosHumanos\Pessoal\Registry\VinculoRegistry;
use Escola;
use Exception;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Validators\Registro40Validator;

class Registro40Service
{
    /**
     * @var Escola
     */
    private $escola;

    /**
     * @var Registro40[]
     */
    private $registros = array();

    /**
     * @var Registro00Service
     */
    private $registro00Service;

    /**
     * @var Registro30Service
     */
    private $registro30Service;

    /**
     * @return Registro40[]
     */
    public function getRegistros()
    {
        return $this->registros;
    }

    /**
     * @return Escola
     */
    public function getEscola()
    {
        return $this->escola;
    }

    /**
     * @param Escola $escola
     * @return Registro40Service
     */
    public function setEscola($escola)
    {
        $this->escola = $escola;
        return $this;
    }

    public function setRegistro00Service(Registro00Service $registro00Service)
    {
        $this->registro00Service = $registro00Service;
    }

    public function setRegistro30Service(Registro30Service $registro30Service)
    {
        $this->registro30Service = $registro30Service;
    }

    /**
     * @throws Exception
     */
    public function buscarDados()
    {
        $registro40Repository = new Registro40Repository();
        $gestores = $this->identificarGestores();

        $contador = 1;
        foreach ($gestores as $gestor) {
            if ($contador > 3) {
                return;
            }

            $gestor->setVinculoRegimeContratacao(VinculoRegistry::get($gestor->getRegimeContratacao()));
            $diretor = $registro40Repository->getDadosDiretor($gestor);

            $builder = new Registro40Builder();
            $builder->addProfissional($gestor);
            if (!is_null($diretor)) {
                $builder->addDadosDiretor($diretor);
            }

            $this->addRegistro($builder->build());

            $contador++;
        }
    }

    /**
     * @param Registro40 $registro
     */
    private function addRegistro(Registro40 $registro)
    {
        $this->registros[] = $registro;
    }

    public function validar()
    {
        if (count($this->registros) == 0) {
            $validator = new Registro40Validator();
            $validator->semGestor();
        }
        foreach ($this->registros as $registro) {
            $validator = new Registro40Validator();
            $registro30 = $this->getRegistro30($registro);
            $validator->setRegistro($registro);
            $validator->setRegistro00($this->registro00Service->getRegistro());
            $validator->setRegistro30($registro30);
            $validator->validar();
        }
    }

    private function getRegistro30(Registro40 $gestor)
    {
        foreach ($this->registro30Service->getRegistros() as $reg50) {
            if ($gestor->getCodigoPessoa() == $reg50->getCodigoPessoa()) {
                return $reg50;
            }
        }
    }

    /**
     * @return ProfissionalEscola[]
     */
    private function identificarGestores()
    {
        $gestores = [];
        $sql = "select ed325_rechumano from escolagestorcenso where ed325_escola = {$this->escola->getCodigo()}";
        $rs = db_query($sql);
        while ($gestor = pg_fetch_assoc($rs)) {
            $dao = new \cl_rechumano();
            $where = ["ed75_i_rechumano = {$gestor['ed325_rechumano']}"];
            $sql = $dao->sqlProfissionaisEscola(null, $this->escola->getCodigo(), $where);
            $prof = pg_fetch_assoc(db_query($sql));
            $profissional = ProfissionalEscola::fromState($prof);
            $gestores[$profissional->getCgm()->getCpf()] = $profissional;
        }
        if (empty($gestores)) {
            return array();
        }
        return $gestores;
    }
}
