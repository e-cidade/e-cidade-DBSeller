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

namespace ECidade\Educacao\Escola\Model;

use CgmFisico;
use CgmJuridico;
use CgmFactory;
use ECidade\Educacao\Escola\Registry\PaisRegistry;
use ECidade\Educacao\Escola\Registry\ProfissionalEscolaRegistry;
use ECidade\Educacao\Escola\Repository\AtividadeProfissionalEscolaRepository;
use ECidade\RecursosHumanos\Pessoal\Model\Vinculo;
use Escola;
use EscolaRepository;
use Exception;

class ProfissionalEscola
{
    private $isDocente;
    private $codigoInep;
    /**
     * @var integer
     */
    private $codigoRecursoHumano;

    /**
     * @var integer
     */
    private $codigoVinculoEscola;

    /**
     * @var Escola
     */
    private $escola;
    private $nacionalidade;
    private $censoUfNascimento;
    private $censoMunicipioNascimento;

    /**
     * @var Pais
     */
    private $censoPaisNascimento;
    private $raca;
    private $escolaridade;
    private $matricula;
    /**
     * @var CgmFisico|CgmJuridico
     */
    private $cgm;

    /**
     * @var Pais
     */
    private $paisResidencia;
    private $municipioResidencia;
    private $localizacaoDiferenciada;
    private $tipoEnsinoMedio;
    private $zonaResidencia;

    /**
     * @var bool
     */
    private $gestor = false;

    /**
     * @var string
     */
    private $gestorEmail = "";

    /**
     * @var ProfissionalFormacao[]
     */
    private $formacoes;

    /**
     * @var integer
     */
    private $regimeContratacao;

    /**
     * @var Vinculo
     */
    private $vinculoRegimeContratacao;
    /**
     * @var array
     */
    private $posGraduacoes;

    private $atividadesProfissionalEscola = [];

    /**
     * @param array $state
     *
     * @return ProfissionalEscola
     * @throws Exception
     */
    public static function fromState(array $state)
    {
        $self = new self();

        if (array_key_exists('ed20_i_codigo', $state)) {
            $self->setCodigoRecursoHumano($state['ed20_i_codigo']);
            $self->setDocente();
        }

        if (array_key_exists('ed20_i_codigoinep', $state)) {
            $self->setCodigoInep($state['ed20_i_codigoinep']);
        }

        if (array_key_exists('ed75_i_codigo', $state)) {
            $self->setCodigoVinculoEscola($state['ed75_i_codigo']);
        }

        if (array_key_exists('ed75_i_escola', $state)) {
            $self->setEscola(EscolaRepository::getEscolaByCodigo($state['ed75_i_escola']));
        }

        if (array_key_exists('ed20_i_nacionalidade', $state)) {
            $self->setNacionalidade($state['ed20_i_nacionalidade']);
        }

        if (array_key_exists('ed20_i_censoufnat', $state)) {
            $self->setCensoUfNascimento($state['ed20_i_censoufnat']);
        }

        if (array_key_exists('ed20_i_censomunicnat', $state)) {
            $self->setCensoMunicipioNascimento($state['ed20_i_censomunicnat']);
        }

        if (array_key_exists('ed20_i_raca', $state)) {
            $self->setRaca($state['ed20_i_raca']);
        }

        if (array_key_exists('ed20_i_escolaridade', $state)) {
            $self->setEscolaridade($state['ed20_i_escolaridade']);
        }

        if (array_key_exists('matricula', $state)) {
            $self->setMatricula($state['matricula']);
        }

        if (array_key_exists('ed20_i_pais', $state)) {
            $self->setCensoPaisNascimento(PaisRegistry::get($state['ed20_i_pais']));
        }

        if (array_key_exists('cgm', $state)) {
            $self->setCgm(CgmFactory::getInstanceByCgm($state['cgm']));
        }

        if (array_key_exists('ed20_paisresidencia', $state)) {
            $self->setPaisResidencia(PaisRegistry::get($state['ed20_paisresidencia']));
        }

        if (array_key_exists('ed20_localizacaodiferenciada', $state)) {
            $self->setLocalizacaoDiferenciada($state['ed20_localizacaodiferenciada']);
        }

        if (array_key_exists('ed20_tipoensinomedio', $state)) {
            $self->setTipoEnsinoMedio($state['ed20_tipoensinomedio']);
        }

        if (array_key_exists('ed20_i_censomunicender', $state)) {
            $self->setMunicipioResidencia($state['ed20_i_censomunicender']);
        }

        if (array_key_exists('ed20_i_zonaresidencia', $state)) {
            $self->setZonaResidencia($state['ed20_i_zonaresidencia']);
        }

        if (array_key_exists('gestor', $state)) {
            $self->setGestor($state['gestor'] == 't');
        }

        if (array_key_exists('gestor_email', $state)) {
            $self->setGestorEmail($state['gestor_email']);
        }

        if (array_key_exists('ed20_i_rhregime', $state)) {
            $self->setRegimeContratacao($state['ed20_i_rhregime']);
        }

        $atividadeProfissionalEscolaRepository = new AtividadeProfissionalEscolaRepository();
        $atividadeProfissionalEscola = $atividadeProfissionalEscolaRepository->scopeProfissional($self)->get();
        $self->setAtividades($atividadeProfissionalEscola);

        ProfissionalEscolaRegistry::set($self);

        return $self;
    }

    public function isDocente()
    {
        return $this->isDocente;
    }

    public function setDocente()
    {
        $sql = "select  ed119_docente from rechumanoativ
                    join atividaderh on ed22_i_atividade = ed01_i_codigo
                    join funcaoatividade on ed01_funcaoatividade = ed119_sequencial
             where ed22_i_rechumanoescola = {$this->getCodigoRecursoHumano()}";

        if (pg_fetch_assoc(db_query($sql))) {
            $this->isDocente = pg_fetch_assoc(db_query($sql))['ed119_docente'] == 't';
        } else {
            $this->isDocente = pg_fetch_assoc(db_query($sql));
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigoInep()
    {
        return $this->codigoInep;
    }

    /**
     * @param mixed $codigoInep
     * @return ProfissionalEscola
     */
    public function setCodigoInep($codigoInep)
    {
        $this->codigoInep = $codigoInep;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCodigoRecursoHumano()
    {
        return $this->codigoRecursoHumano;
    }

    /**
     * @param interger $codigoRecursoHumano
     * @return ProfissionalEscola
     */
    public function setCodigoRecursoHumano($codigoRecursoHumano)
    {
        $this->codigoRecursoHumano = (int) $codigoRecursoHumano;
        return $this;
    }

    /**
     * @return integer
     */
    public function getCodigoVinculoEscola()
    {
        return $this->codigoVinculoEscola;
    }

    /**
     * @param integer $codigoVinculoEscola
     * @return ProfissionalEscola
     */
    public function setCodigoVinculoEscola($codigoVinculoEscola)
    {
        $this->codigoVinculoEscola = (int) $codigoVinculoEscola;
        return $this;
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
     * @return ProfissionalEscola
     */
    public function setEscola(Escola $escola)
    {
        $this->escola = $escola;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNacionalidade()
    {
        return $this->nacionalidade;
    }

    /**
     * @param mixed $nacionalidade
     * @return ProfissionalEscola
     */
    public function setNacionalidade($nacionalidade)
    {
        $this->nacionalidade = (int) $nacionalidade;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCensoUfNascimento()
    {
        return $this->censoUfNascimento;
    }

    /**
     * @param mixed $censoUfNascimento
     * @return ProfissionalEscola
     */
    public function setCensoUfNascimento($censoUfNascimento)
    {
        $this->censoUfNascimento = $censoUfNascimento;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCensoMunicipioNascimento()
    {
        return $this->censoMunicipioNascimento;
    }

    /**
     * @param mixed $censoMunicipioNascimento
     * @return ProfissionalEscola
     */
    public function setCensoMunicipioNascimento($censoMunicipioNascimento)
    {
        $this->censoMunicipioNascimento = $censoMunicipioNascimento;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCensoPaisNascimento()
    {
        return $this->censoPaisNascimento;
    }

    /**
     * @param Pais $censoPaisNascimento
     * @return ProfissionalEscola
     */
    public function setCensoPaisNascimento(Pais $censoPaisNascimento)
    {
        $this->censoPaisNascimento = $censoPaisNascimento;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRaca()
    {
        return $this->raca;
    }

    /**
     * @param mixed $raca
     * @return ProfissionalEscola
     */
    public function setRaca($raca)
    {
        $this->raca = $raca;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEscolaridade()
    {
        return $this->escolaridade;
    }

    /**
     * @param mixed $escolaridade
     * @return ProfissionalEscola
     */
    public function setEscolaridade($escolaridade)
    {
        $this->escolaridade = $escolaridade;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMatricula()
    {
        return $this->matricula;
    }

    /**
     * @param mixed $matricula
     * @return ProfissionalEscola
     */
    public function setMatricula($matricula)
    {
        $this->matricula = $matricula;
        return $this;
    }

    /**
     * @return CgmFisico|CgmJuridico
     */
    public function getCgm()
    {
        return $this->cgm;
    }

    /**
     * @param CgmFisico|CgmJuridico $cgm
     * @return ProfissionalEscola
     */
    public function setCgm($cgm)
    {
        $this->cgm = $cgm;
        return $this;
    }

    /**
     * @return Pais
     */
    public function getPaisResidencia()
    {
        return $this->paisResidencia;
    }

    /**
     * @param Pais $paisResidencia
     * @return ProfissionalEscola
     */
    public function setPaisResidencia($paisResidencia)
    {
        $this->paisResidencia = $paisResidencia;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocalizacaoDiferenciada()
    {
        return $this->localizacaoDiferenciada;
    }

    /**
     * @param mixed $localizacaoDiferenciada
     * @return ProfissionalEscola
     */
    public function setLocalizacaoDiferenciada($localizacaoDiferenciada)
    {
        $this->localizacaoDiferenciada = $localizacaoDiferenciada;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTipoEnsinoMedio()
    {
        return $this->tipoEnsinoMedio;
    }

    /**
     * @param mixed $tipoEnsinoMedio
     * @return ProfissionalEscola
     */
    public function setTipoEnsinoMedio($tipoEnsinoMedio)
    {
        $this->tipoEnsinoMedio = $tipoEnsinoMedio;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMunicipioResidencia()
    {
        return $this->municipioResidencia;
    }

    /**
     * @param mixed $municipioResidencia
     * @return ProfissionalEscola
     */
    public function setMunicipioResidencia($municipioResidencia)
    {
        $this->municipioResidencia = $municipioResidencia;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getZonaResidencia()
    {
        return $this->zonaResidencia;
    }

    /**
     * @param mixed $zonaResidencia
     * @return ProfissionalEscola
     */
    public function setZonaResidencia($zonaResidencia)
    {
        $this->zonaResidencia = $zonaResidencia;
        return $this;
    }

    /**
     * @param ProfissionalFormacao[] $formacoesProfissional
     */
    public function setFormacoes(array $formacoesProfissional)
    {
        $this->formacoes = $formacoesProfissional;
    }

    public function getFormacoes()
    {
        return $this->formacoes;
    }


    public function setPosgraduaceos(array $posGraduacoes)
    {
        $this->posGraduacoes = $posGraduacoes;
    }

    public function getPosgraduaceos()
    {
        return $this->posGraduacoes;
    }

    /**
     * @return bool
     */
    public function isGestor()
    {
        return $this->gestor;
    }

    /**
     * @param bool $gestor
     * @return ProfissionalEscola
     */
    public function setGestor($gestor)
    {
        $this->gestor = $gestor;
        return $this;
    }

    public function getGestorEmail()
    {
        return $this->gestorEmail;
    }

    /**
     * @param string $gestorEmail
     * @return ProfissionalEscola
     */
    public function setGestorEmail($gestorEmail)
    {
        $this->gestorEmail = $gestorEmail;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRegimeContratacao()
    {
        return $this->regimeContratacao;
    }

    /**
     * @param mixed $regimeContratacao
     * @return ProfissionalEscola
     */
    public function setRegimeContratacao($regimeContratacao)
    {
        $this->regimeContratacao = $regimeContratacao;
        return $this;
    }

    /**
     * @return Vinculo
     */
    public function getVinculoRegimeContratacao()
    {
        return $this->vinculoRegimeContratacao;
    }

    /**
     * @param Vinculo $vinculoRegimeContratacao
     * @return ProfissionalEscola
     */
    public function setVinculoRegimeContratacao($vinculoRegimeContratacao)
    {
        $this->vinculoRegimeContratacao = $vinculoRegimeContratacao;
        return $this;
    }

    /**
     * @param AtividadeProfissionalEscola[] $atividades
     * @return ProfissionalEscola
     */
    private function setAtividades(array $atividades)
    {
        $this->atividadesProfissionalEscola = $atividades;
        return $this;
    }

    /**
     * Atividades do profissional
     * @return AtividadeProfissionalEscola[]
     */
    public function getAtividades()
    {
        return $this->atividadesProfissionalEscola;
    }
}
