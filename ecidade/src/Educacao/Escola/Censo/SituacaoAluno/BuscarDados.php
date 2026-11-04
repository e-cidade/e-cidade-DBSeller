<?php

namespace ECidade\Educacao\Escola\Censo\SituacaoAluno;

use ECidade\Educacao\Escola\Censo\Censo;
use ECidade\Educacao\Escola\Censo\SituacaoAluno\Dados\DadosInterface;
use ECidade\Educacao\Escola\Censo\SituacaoAluno\Dados\BuscaDadosEscola2016;
use ECidade\Educacao\Escola\Censo\SituacaoAluno\Dados\DadosEscola2016;
use Escola;
use Exception;

/**
 * Class BuscarDados
 * @package ECidade\Educacao\Escola\Censo\SituacaoAluno
 */
class BuscarDados
{
    /**
     * @var Censo
     */
    private $oCenso;
    /**
     * @var Escola
     */
    private $oEscola;
    private $aCodigoAlunoAntes = array();

    /**
     * BuscarDados constructor.
     * @param Censo $oCenso
     * @param Escola $oEscola
     */
    public function __construct(Censo $oCenso, Escola $oEscola)
    {
        $this->oCenso = $oCenso;
        $this->oEscola = $oEscola;
    }

    /**
     * @return DadosEscola2016
     * @throws Exception
     */
    public function registro89()
    {
        switch ($this->oCenso->getAno()) {
            case 2016:
            case 2017:
            case 2018:
            case 2019:
            case 2020:
            case 2021:
            case 2022:
                $oDados = new BuscaDadosEscola2016($this->oCenso, $this->oEscola);
                return $oDados->getDados();
                break;

            default:
                throw new \BusinessException("Não foi possível buscar os dados da Escola.");
                break;
        }
    }

    /**
     * @return DadosInterface[]
     * @throws Exception
     */
    public function registro90()
    {
        switch ($this->oCenso->getAno()) {
            case 2016:
            case 2017:
            case 2018:
            case 2019:
            case 2020:
            case 2021:
            case 2022:
                $oDados = new Dados\BuscaDadosAlunosAntes2016($this->oCenso, $this->oEscola);
                $aDados = $oDados->getDados();

                $aDadosAlunosAntes = array();
                foreach ($aDados as $oDadosAluno) {
                    $oValidacaoAluno = new Dados\DadosAlunoAntes2016();
                    $oValidacaoAluno->popular($oDadosAluno);
                    $aDadosAlunosAntes[] = $oValidacaoAluno;
                }

                if (empty($aDadosAlunosAntes)) {
                    throw new \BusinessException("Nenhum aluno encontrado para a escola informada.");
                }
                $this->aCodigoAlunoAntes = $oDados->getCodigoAlunos();
                return $aDadosAlunosAntes;
                break;

            default:
                throw new \BusinessException("Não foi possível buscar os dados do aluno.");
                break;
        }
    }

    /**
     * @return DadosInterface[]
     * @throws Exception
     */
    public function registro91()
    {
        switch ($this->oCenso->getAno()) {
            case 2016:
            case 2017:
            case 2018:
            case 2019:
            case 2020:
            case 2021:
            case 2022:
                $oDados = new Dados\BuscaDadosAlunosApos2016($this->oCenso, $this->oEscola, $this->aCodigoAlunoAntes);
                $aDados = $oDados->getDados();

                $aDadosAluno = array();
                foreach ($aDados as $oDadosAluno) {
                    $oValidacaoAluno = new Dados\DadosAlunosApos2016();
                    $oValidacaoAluno->popular($oDadosAluno);
                    $aDadosAluno[] = $oValidacaoAluno;
                }

                return $aDadosAluno;
                break;

            default:
                throw new \BusinessException("Não foi possível buscar os dados da Escola.");
                break;
        }
    }
}
