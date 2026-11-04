<?php
namespace ECidade\Educacao\Escola\Censo\SituacaoAluno;

use db_layouttxt;
use ECidade\Educacao\Escola\Censo\Censo;
use ECidade\Educacao\Escola\Censo\SituacaoAluno\Dados\DadosInterface;
use ECidade\Educacao\Escola\Censo\SituacaoAluno\Layout\Layout2016;
use Escola;
use Exception;

/**
 * Classe responsável pela geração do arquivo do censo de Situação do Aluno
 *
 * @author Andrio Costa <andrio.costa@dbseller.com.br>
 * @author André Mello  <andre.mello@dbseller.com.br>
 * @version $Revision: 1.9 $
 */
class Exportacao
{
    /**
     * @var db_layouttxt
     */
    private $oLayout = null;

    /**
     * @var Escola
     */
    private $oEscola = null;

    /**
     * @var DadosInterface
     */
    public $oDadosEscola = null;
    /**
     * @var DadosInterface[]
     */
    public $aDadosAlunoAntes = array();

    /**
     * @var DadosInterface[]
     */
    public $aDadosAlunoDepois = array();

    /**
     * @var Censo
     */
    private $oCenso = null;

    public function __construct(Censo $oCenso, \Escola $oEscola)
    {
        $this->oCenso = $oCenso;
        $this->oEscola = $oEscola;
    }

    /**
     * Busca o layout do censo de acordo com o ano que estamos gerando o aquivo
     * @return Layout2016
     * @throws Exception
     */
    protected function buscarLayout()
    {
        switch ($this->oCenso->getAno()) {
            case 2016:
            case 2017:
            case 2018:
            case 2019:
            case 2020:
            case 2021:
            case 2022:
                $this->oLayout = new Layout2016();
                break;
            default:
                throw new \BusinessException("Não há layout cadastrado para o ano de {$this->oCenso->getAno()}.");
                break;
        }

        return $this->oLayout;
    }

    /**
     * Busca os dados do censo de acordo com o ano
     * @throws Exception
     */
    protected function buscarDados()
    {
        $oDados = new BuscarDados($this->oCenso, $this->oEscola);

        $this->oDadosEscola = $oDados->registro89();
        $this->aDadosAlunoAntes = $oDados->registro90();
        $this->aDadosAlunoDepois = $oDados->registro91();
    }

    /**
     * Carrega os dados e gera o arquivo
     * @return boolean false se teve inconsistencias
     * @throws Exception
     */
    public function gerarArquivo()
    {
        $this->buscarDados();
        $this->buscarLayout();

        if (!$this->validar()) {
            $this->escreverRegistros();
            return false;
        }

        $this->escreverRegistros();
        return true;
    }

    /**
     * Escreve os dados no arquivo
     */
    private function escreverRegistros()
    {
        if (empty($this->oDadosEscola->getErros())) {
            $this->oLayout->escreverLinha($this->oDadosEscola->transformarStdClass(), 3, 89);
        }

        foreach ($this->aDadosAlunoAntes as $oDadosAluno) {
            if (empty($oDadosAluno->getErros())) {
                $this->oLayout->escreverLinha($oDadosAluno->transformarStdClass(), 3, 90);
            }
        }

        foreach ($this->aDadosAlunoDepois as $oDadosAluno) {
            if (empty($oDadosAluno->getErros())) {
                $this->oLayout->escreverLinha($oDadosAluno->transformarStdClass(), 3, 91);
            }
        }
    }

    /**
     * Realiza as validações de todos os registros e havendo inconsistencias registra as mesmas em uma arquivo de log
     * @return bool true se validou sem inconsistencias
     */
    private function validar()
    {
        $aValidacoes = array();
        $aValidacoes[] = $this->oDadosEscola->validar();

        $this->registrarErros($this->oDadosEscola->getErros(), 89);

        foreach ($this->aDadosAlunoAntes as $oDadosAluno) {
            $aValidacoes[] = $oDadosAluno->validar($this->oDadosEscola->getCodigoINEP());
            $this->registrarErros($oDadosAluno->getErros(), 90);
        }
        foreach ($this->aDadosAlunoDepois as $oDadosAluno) {
            $aValidacoes[] = $oDadosAluno->validar($this->oDadosEscola->getCodigoINEP());
            $this->registrarErros($oDadosAluno->getErros(), 91);
        }

        return !in_array(false, $aValidacoes);
    }

    /**
     * Registra os erros/inconsistencias no arquivo de log
     * @param  array   $aErros
     * @param  integer $iRegistro
     */
    private function registrarErros($aErros, $iRegistro)
    {
        foreach ($aErros as $sMsg) {
            LogErro::log($sMsg, $iRegistro);
        }
    }

    /**
     * Retorna o nome (com o path) do arquivo de log
     * @return string
     */
    public function getNomeAquivoLog()
    {
        return LogErro::fileName();
    }

    /**
     * Retorna o arquivo do censo
     * @return string
     */
    public function getNomeArquivo()
    {
        return $this->oLayout->getNomeArquivo();
    }
}
