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

namespace ECidade\Educacao\Escola\Censo\Identificacao\Service;

use ECidade\Educacao\Escola\Censo\Censo;
use ECidade\Educacao\Escola\Censo\Identificacao\Builder\PessoaBuilder;
use ECidade\Educacao\Escola\Censo\Identificacao\Model\Pessoa;
use ECidade\Educacao\Escola\Model\CensoMunicipio;
use ECidade\Educacao\Escola\Repository\AlunoRepository;
use ECidade\Educacao\Escola\Repository\CensoMunicipioRepository;
use ECidade\Educacao\Escola\Repository\ProfissionalEscolaRepository;
use Escola;
use Exception;
use DBDate;

/**
 * Class ExportacaoService
 * @package ECidade\Educacao\Escola\Censo\Identificacao\Service
 */
class ExportacaoService
{
    /**
     * @var Censo
     */
    private $censo;

    /**
     * @var Escola[]
     */
    private $escolas = [];

    /**
     * @var Pessoa[]
     */
    private $pessoas = [];

    /**
     * @var CensoMunicipio
     */
    private $municipioInstituicao;

    /**
     * @var DBDate
     */
    private $dataCensoPersonalizada = null;

    /**
     * @param DBDate $dataCensoPersonalizada
     */
    public function setDataCensoPersonalizada(DBDate $dataCensoPersonalizada)
    {
        $this->dataCensoPersonalizada = $dataCensoPersonalizada;
    }

    /**
     * @return DBDate
     */
    public function getDataCensoPersonalizada()
    {
        return $this->dataCensoPersonalizada;
    }

    /**
     * @param Censo $censo
     */
    public function setCenso(Censo $censo)
    {
        $this->censo = $censo;
    }

    /**
     * @param Escola[] $escolas
     */
    public function setEscolas(array $escolas)
    {
        $this->escolas = $escolas;
    }

    /**
     * @param Pessoa $pessoa
     */
    public function addPessoa(Pessoa $pessoa)
    {
        $this->pessoas[] = $pessoa;
    }

    /**
     * @throws Exception
     */
    public function buscarDados()
    {
        $this->municipioIBGEInstituicao();
        $this->buscarProfissionais();
        $this->buscarAlunos();
    }

    /**
     * @return Pessoa[]
     */
    public function getPessoas()
    {
        return $this->pessoas;
    }

    /**
     * @throws Exception
     */
    private function buscarProfissionais()
    {
        $dataFiltro = !is_null($this->dataCensoPersonalizada)
            ? $this->dataCensoPersonalizada
            : $this->censo->getDataCenso();

        $profissionalEscolaRepository = new ProfissionalEscolaRepository();
        $profissionalEscolaRepository->scopeDocentePresente($dataFiltro);
        $profissionalEscolaRepository->scopeSemCodigoInep();
        
        $profissionalEscolaRepository->scopeDiretorProfessorMonitor($dataFiltro);

        foreach ($this->escolas as $escola) {
            $profissionaisEscola = $profissionalEscolaRepository->getProfissionaisAtivos(
                $escola
            );

            $profissionaisEscolaMatriculaUnificada = array();
            foreach ($profissionaisEscola as $profissionalEscola) {
                $profissionaisEscolaMatriculaUnificada[$profissionalEscola->getCgm()->getCpf()] = $profissionalEscola;
            }
            foreach ($profissionaisEscolaMatriculaUnificada as $profissionalEscola) {
                $builder = new PessoaBuilder();
                $builder->setDadosProfissional($profissionalEscola);
                $builder->setMunicipio($this->municipioInstituicao);
                $this->addPessoa($builder->build());
            }
        }
    }

    /**
     * @throws Exception
     */
    private function buscarAlunos()
    {
        $alunoRepository = new AlunoRepository();
        foreach ($this->escolas as $escola) {
            $alunoRepository->scopeSemCodigoInep();
            $dataFiltro = !is_null($this->dataCensoPersonalizada)
                ? $this->dataCensoPersonalizada
                : $this->censo->getDataCenso();
            $alunosMatriculados = $alunoRepository->getAlunosCenso($escola, $dataFiltro);
            $alunoRepository->resetScopes();
            foreach ($alunosMatriculados as $aluno) {
                $builder = new PessoaBuilder();
                $builder->setDadosAluno($aluno);
                $builder->setMunicipio($this->municipioInstituicao);
                $this->addPessoa($builder->build());
            }
        }
    }

    private function municipioIBGEInstituicao()
    {
        $instituicao = \InstituicaoRepository::getInstituicaoSessao();
        $repository = new CensoMunicipioRepository();

        $municipio = $instituicao->getMunicipio();
        $municipios = $repository->scopeNome($municipio)->get();

        if (count($municipios) == 0) {
            throw new Exception("Não foi localizado o município {$municipio} na tabela do censo.");
        }
        $this->municipioInstituicao = array_shift($municipios);
    }
}
