<?php
/**
 * Created by PhpStorm.
 * User: andri
 * Date: 03/05/2019
 * Time: 16:05
 */

namespace ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Builder;

use App\Domain\Educacao\Secretaria\Models\Base;
use ECidade\Educacao\Escola\Censo\Helpers\Pessoa;
use ECidade\Educacao\Escola\Censo\Helpers\Turma;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro60;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\MatriculaCensoVo;
use ECidade\Educacao\Escola\Model\Aluno;
use ECidade\Educacao\Escola\Model\BaseCurricular;
use ECidade\Enum\Educacao\Secretaria\EstruturaCurricularEnum;
use ECidade\Enum\Educacao\Secretaria\TipoItinerarioFormativoEnum;
use Exception;

class Registro60Builder
{
    /**
     * @var MatriculaCensoVo
     */
    protected $matricula;

    /**
     * @var array
     */
    protected $tipoTransporte = array();

    /**
     * @var Registro60
     */
    protected $registro;

    protected $multiEtapasCenso = array(3, 22, 23, 72, 56, 64);

    protected $deParaEscolarizacaoOutroEspaco = array(
        Aluno::ESCOLARIZACAO_ESPECIAL_HOSPITAL => 2,
        Aluno::ESCOLARIZACAO_ESPECIAL_DOMICILIO => 3,
        Aluno::ESCOLARIZACAO_ESPECIAL_NAO => 1
    );

    public function addMatricula(MatriculaCensoVo $matricula)
    {
        $this->matricula = $matricula;
    }

    /**
     * Código do censo para os tipos de transporte
     * @param array $tipoTransporte
     */
    public function addVeiculoUtilizado(array $tipoTransporte)
    {
        $this->tipoTransporte = $tipoTransporte;
    }

    /**
     * @return Registro60
     */
    public function build()
    {
        $this->create();
        $this->buildMatricula();

        return $this->registro;
    }

    protected function create()
    {
        $this->registro = new Registro60();
    }

    protected function buildMatricula()
    {
        $aluno = $this->matricula->getAluno();
        $this->registro->setCodigoInepEscola($this->matricula->getTurma()->getEscola()->getCodigoInep());
        $this->registro->setCodigoPessoa(Pessoa::buildCodigoAluno($aluno->getCodigo()));
        $this->registro->setCodigoInep(trim($aluno->getCodigoInep()));

        $this->registro->setCodigoTurma(Turma::buildCodigoTurmaRegular($this->matricula->getTurma()->getCodigoTurma()));
        if (!$this->matricula->getTurma()->isEscolarizacao()) {
            $this->registro->setCodigoTurma(Turma::buildCodigoTurmaAC($this->matricula->getTurma()->getCodigoTurma()));
        }
        if ($this->matricula->getTurma()->isTurmaUnificada()) {
            $this->registro->setCodigoTurma(
                Turma::buildCodigoTurmaUnificada($this->matricula->getTurma()->getCodigoTurmaUnificada())
            );
        }

        $this->buildEtapa();
        $this->buildAtendimentoEspecializado();

        if ($this->matricula->getTurma()->isEscolarizacao()) {
            $escolaricacao = $aluno->getRecebeEscolarizacaoEspecial();
            $this->registro->setRecebeEscolarizacaoEspecial($this->deParaEscolarizacaoOutroEspaco[$escolaricacao]);

            $this->buildTransporteEscolar();
        }
        $this->buildAreaItinerarioFromativo();
    }

    public function buildAreaItinerarioFromativo()
    {
        $estruturaCurricular =  $this->matricula->getTurma()->getTipoEstruturaCurricular();
        if (in_array(EstruturaCurricularEnum::ITINERARIO_FORMATIVO, $estruturaCurricular)) {
            $codigoCurso = Base::find($this->matricula->getTurma()->getBase())->curso->ed29_censocursoprofiss;
            $this->registro->setCodigoCursoTecnico($codigoCurso);
            $disciplinas = $this->matricula->getTurma()->getDisciplinas();
            foreach ($disciplinas as $disciplina) {
                $tipoBase = $disciplina->getTipoBase();
                if (EstruturaCurricularEnum::ITINERARIO_FORMATIVO == $tipoBase['ed182_estrutura_curricular']) {
                    $areasItinerario = json_decode($tipoBase['ed182_tipo_itinerario_informativo']);
                    foreach ($areasItinerario as $area) {
                        $this->registro->setAreasItinerarioFormativo($area);
                        if (intval($area) == TipoItinerarioFormativoEnum::ITINERARIO_FORMATIVO_INTEGRADO) {
                            $this->registro->setComposicaoItinerarioIntegrado($tipoBase);
                        }
                    }
                }
            }
        }
    }

    protected function buildEtapa()
    {
        if (in_array($this->matricula->getTurma()->getEtapaCenso(), $this->multiEtapasCenso)) {
            $this->registro->setEtapaTurmaMultietapa($this->matricula->getEtapaCenso());
        }
    }

    /**
     * Só para turmas de AEE
     */
    protected function buildAtendimentoEspecializado()
    {
        if (!$this->matricula->getTurma()->isAtendimentoAEE()) {
            return;
        }
        $tipoAtendimento = $this->matricula->getTiposAtendimento();

        $this->registro->setEnsinoSistemaBraille($tipoAtendimento[0]);
        $this->registro->setEnsinoRecursosOpticosNaoOpticos($tipoAtendimento[2]);
        $this->registro->setEnsinoTecnicasOrientacaoMobilidade($tipoAtendimento[4]);
        $this->registro->setEnsinoLibras($tipoAtendimento[5]);
        $this->registro->setEnsinoCAA($tipoAtendimento[6]);
        $this->registro->setEnriquecimentoCurricular($tipoAtendimento[7]);
        $this->registro->setEnsinoCalculoSoroban($tipoAtendimento[8]);
        $this->registro->setEnsinoInformaticaAcessivel($tipoAtendimento[9]);
        $this->registro->setDesenvolvimentoFuncoesCognitivas($tipoAtendimento[12]);
        $this->registro->setDesenvolvimentoVidaAutonoma($tipoAtendimento[13]);
        $this->registro->setEnsinoPortuguesaSegundaLingua($tipoAtendimento[14]);
    }

    private function buildTransporteEscolar()
    {
        $aluno = $this->matricula->getAluno();
        if (!is_null($aluno->getPaisResidencia())) {
            if ($aluno->getPaisResidencia()->getCodigoOnu() == 76) {
                $this->registro->setTransporteEscolarPublico($aluno->isUtilizaTransportePublico() ? 1 : 0);
                if ($aluno->isUtilizaTransportePublico()) {
                    $this->registro->setResponsavelTransporteEscolar($aluno->getTransporteResponsavel());
                    $this->registro->setBicicleta(0);
                    $this->registro->setMicroonibus(0);
                    $this->registro->setOnibus(0);
                    $this->registro->setTracaoAnimal(0);
                    $this->registro->setVansKombis(0);
                    $this->registro->setOutro(0);
                    $this->registro->setAquaviarioAte5(0);
                    $this->registro->setAquaviarioEntre5A15(0);
                    $this->registro->setAquaviarioEntre15A35(0);
                    $this->registro->setAquaviarioAcima35(0);
                    foreach ($this->tipoTransporte as $tipo) {
                        switch ($tipo) {
                            case 1:
                                $this->registro->setVansKombis(1);
                                break;
                            case 2:
                                $this->registro->setMicroonibus(1);
                                break;
                            case 3:
                                $this->registro->setOnibus(1);
                                break;
                            case 4:
                                $this->registro->setBicicleta(1);
                                break;
                            case 5:
                                $this->registro->setTracaoAnimal(1);
                                break;
                            case 6:
                                $this->registro->setOutro(1);
                                break;
                            case 7:
                                $this->registro->setAquaviarioAte5(1);
                                break;
                            case 8:
                                $this->registro->setAquaviarioEntre5A15(1);
                                break;
                            case 9:
                                $this->registro->setAquaviarioEntre15A35(1);
                                break;
                            case 10:
                                $this->registro->setAquaviarioAcima35(1);
                                break;
                        }
                    }
                }
            }
        }
    }

    /**
     * @param array $linha
     * @return Registro60
     * @throws Exception
     */
    public function buildFromFileLine(array $linha)
    {
        if ($linha[0] != 60) {
            throw new Exception("Linha não é do registro 60");
        }

        $this->create();
        $this->registro->setCodigoInepEscola($linha[1]);
        $this->registro->setCodigoPessoa($linha[2]);
        $this->registro->setCodigoInep($linha[3]);
        $this->registro->setCodigoTurma($linha[4]);
        $this->registro->setCodigoTurmaInep($linha[5]);
        $this->registro->setCodigoMatricula($linha[6]);
        $this->registro->setEtapaTurmaMultietapa($linha[7]);
        $this->registro->setDesenvolvimentoFuncoesCognitivas($linha[8]);
        $this->registro->setDesenvolvimentoVidaAutonoma($linha[9]);
        $this->registro->setEnriquecimentoCurricular($linha[10]);
        $this->registro->setEnsinoInformaticaAcessivel($linha[11]);
        $this->registro->setEnsinoLibras($linha[12]);
        $this->registro->setEnsinoPortuguesaSegundaLingua($linha[13]);
        $this->registro->setEnsinoCalculoSoroban($linha[14]);
        $this->registro->setEnsinoSistemaBraille($linha[15]);
        $this->registro->setEnsinoTecnicasOrientacaoMobilidade($linha[16]);
        $this->registro->setEnsinoCAA($linha[17]);
        $this->registro->setEnsinoRecursosOpticosNaoOpticos($linha[18]);
        $this->registro->setRecebeEscolarizacaoEspecial($linha[19]);
        $this->registro->setTransporteEscolarPublico($linha[20]);
        $this->registro->setResponsavelTransporteEscolar($linha[21]);
        $this->registro->setBicicleta($linha[22]);
        $this->registro->setMicroonibus($linha[23]);
        $this->registro->setOnibus($linha[24]);
        $this->registro->setTracaoAnimal($linha[25]);
        $this->registro->setVansKombis($linha[26]);
        $this->registro->setOutro($linha[27]);
        $this->registro->setAquaviarioAte5($linha[28]);
        $this->registro->setAquaviarioEntre5A15($linha[29]);
        $this->registro->setAquaviarioEntre15A35($linha[30]);
        $this->registro->setAquaviarioAcima35($linha[31]);

        return $this->registro;
    }
}
