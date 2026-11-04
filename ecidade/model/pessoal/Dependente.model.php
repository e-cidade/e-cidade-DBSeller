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

// phpcs:disable
require_once modification('std/DBDate.php');
// phpcs:enable

/**
 * Class Dependente
 */
class Dependente
{
    const CONJUGE = 'C';
    const FILHO = 'F';
    const PAI = 'P';
    const MAE = 'M';
    const AVO = 'A';
    const OUTROS = 'O';

    const CALCULO = 'C';
    const SEMPRE = 'S';
    const NAO_DEPENDENTE = 'N';

    const IRF_NAO_DEPENDENTE = 0;
    const IRF_CONJUGE_COMPANHEIRO = 1;
    const IRF_FILHOS_ATE_21 = 2;
    const IRF_FILHO_ENTEADO_ATE_24_ENSINO_SUPERIOR = 3;
    const IRF_IRMAO_NETO_BISNETO_ATE_21 = 4;
    const IRF_IRMAO_NETO_BISNETO_21_A_24_ENSINO_SUPERIOR = 5;
    const IRF_PAIS_AVOS_BISAVOS = 6;
    const IRF_MENOR_POBRE_ATE_21_GUARDA_JUDICIAL = 7;
    const IRF_ABSOLUTAMENTE_INCAPAZ = 8;

    /**
     * @var int
     */
    private $iCodigo;

    /**
     * @var int
     */
    private $matricula;

    /**
     * Nome do dependente
     * @var string
     */
    private $sNome;

    /**
     * Data de nascimento do dependente
     * @var DBDate
     */
    private $oDataNascimento;

    /**
     * Grau de parentesco
     * @var string
     *
     * C - Conjuge
     * F - Filho(a)
     * P - Pai
     * M - Mãe
     * A - Avó(ô)
     * O - Outros
     *
     */
    private $sGrauParentesco;

    /**
     * Salário família
     * @var string
     *
     * C - Cálculo
     * S - Sempre Dependente
     * N - Não Dependente
     *
     */
    private $sSalarioFamilia;

    /**
     * Tipo de depentente
     * IRF:
     * @var int
     *
     * 0 - Não Dependente
     * 1 - Cônjuge
     * 2 - Filhos até 21 anos
     * 3 - Irmãos, netos até 21 anos
     * 4 - Pais e avós
     * 5 - Absolutamente incapaz
     * 6 - Filhos maiores de 21 anos em curso universitário,
     * 7 - Irmãos, netos maiores de 21 anos em curso  universitário
     *
     */
    private $iTipo;

    /**
     * Especial - condição de dependente especial
     * @var string
     *
     * C - Cálculo
     * S - Sempre Dependente
     * N - Não Dependente
     *
     */
    private $sCondicaoEspecial;

    /**
     * @var bool
     */
    private $finsPrevidenciarios;

    /**
     * CPF vinculado ao rhdependeplug
     * @var string
     */
    private $sCpf;

    /**
     *
     * @var String
     * M - Masculino
     * F- Feminino
     */
    private $sexo;

    /**
     * @var integer
     */
    private $instituicao;

    /**
     * @column  rh31_tipoparentesco
     * @var string
     */
    private $tipoParentesco;

    /**
     * @column rh31_descricaoparentesco
     * @var string
     */
    private $descricaoParentesco;

    /**
     * @column rh31_irfcomplementar
     * @var string
     */
    private $irfComplementar;

    /**
     * Dependente constructor.
     * @param int $iDependente
     * @throws DBException
     * @throws ParameterException
     */
    public function __construct($iDependente = 0)
    {
        if (empty($iDependente)) {
            return;
        }

        $oDaoRhdepend = db_utils::getDao('rhdepend');
        $sSqlDependente = $oDaoRhdepend->sql_query_file_dependeplug($iDependente);
        $rsDependente = $oDaoRhdepend->sql_record($sSqlDependente);

        if ($oDaoRhdepend->erro_status == "0") {
            throw new DBException($oDaoRhdepend->erro_msg);
        }

        $oDependente = db_utils::fieldsMemory($rsDependente, 0);

        $this->setCodigo($oDependente->rh31_codigo);
        $this->setNome($oDependente->rh31_nome);

        if (!empty($oDependente->rh31_dtnasc)) {
            $this->setDataNascimento(new DBDate($oDependente->rh31_dtnasc));
        }

        $this->setGrauParentesco($oDependente->rh31_gparen);
        $this->setTipo($oDependente->rh31_irf);
        $this->setSalarioFamilia($oDependente->rh31_depend);
        $this->setCondicaoEspecial($oDependente->rh31_especi);
        $this->setCpf($oDependente->dp01_cpf);
        $this->setSexo($oDependente->dp01_sexo);
    }

    /**
     * @param $iCodigo
     * @return Dependente|void
     * @throws DBException
     * @throws ParameterException
     */
    public static function find($iCodigo)
    {
        if (empty($iCodigo)) {
            return;
        }

        $oDaoRhdepend = db_utils::getDao('rhdepend');
        $sSqlDependente = $oDaoRhdepend->sql_query_file_dependeplug($iCodigo);
        $rsDependente = $oDaoRhdepend->sql_record($sSqlDependente);

        if ($oDaoRhdepend->erro_status == "0") {
            throw new DBException($oDaoRhdepend->erro_msg);
        }
        $state = pg_fetch_assoc($rsDependente, 0);

        return self::fromState(empty($state) ? array() : $state);
    }

    public static function findByServidorMatriculaDependenteCPF($matriculaServidor, $cpfDependente)
    {
        $DaoDependente = new  cl_rhdepend();
        $dbwhere = "rh31_regist  = {$matriculaServidor} and  dp01_cpf = '{$cpfDependente}'";
        $sSqlDependente = $DaoDependente->sql_query_file_dependeplug(null, "*", null, $dbwhere);
        if ($DaoDependente->erro_status == "0") {
            throw new DBException($DaoDependente->erro_msg);
        }
        $rsDependente = $DaoDependente->sql_record($sSqlDependente);
        $state = pg_fetch_assoc($rsDependente, 0);

        return self::fromState(empty($state) ? array() : $state);
    }

    /**
     * Define o codigo do dependente
     * @param int $iCodigo
     */
    public function setCodigo($iCodigo)
    {
        $this->iCodigo = $iCodigo;
    }

    /**
     * Define o nome do depedente
     * @param string $sNome
     */
    public function setNome($sNome)
    {
        $this->sNome = $sNome;
    }

    /**
     * Defincao a data de nascimento do servidor
     * @param DBDate $oDataNascimento
     */
    public function setDataNascimento(DBDate $oDataNascimento)
    {
        $this->oDataNascimento = $oDataNascimento;
    }

    /**
     * Define o grau de parentesco do dependente com o servidor
     * @param string $sGrauParentesco
     */
    public function setGrauParentesco($sGrauParentesco)
    {
        $this->sGrauParentesco = $sGrauParentesco;
    }

    /**
     * Define o tipo de dependente
     * @param int $iTipo
     */
    public function setTipo($iTipo)
    {
        $this->iTipo = $iTipo;
    }

    /**
     * Define se é dependente de salario familia
     * @param string $sSalarioFamilia
     */
    public function setSalarioFamilia($sSalarioFamilia)
    {
        $this->sSalarioFamilia = $sSalarioFamilia;
    }

    /**
     * Define se dependente tem condição especial
     * @param string $sCondicaoEspecial
     */
    public function setCondicaoEspecial($sCondicaoEspecial)
    {
        $this->sCondicaoEspecial = $sCondicaoEspecial;
    }

    /**
     * Define o cpf do dependente
     * @param string $sCpf
     */
    public function setCpf($sCpf)
    {
        $this->sCpf = $sCpf;
    }

    /**
     * Define o sexo do dependente
     * @param string $sCpf
     */
    public function setSexo($sexo)
    {
        $this->sexo = $sexo;
    }

    /**
     * @return int
     */
    public function getInstituicao()
    {
        return $this->instituicao;
    }

    /**
     * @param int $instituicao
     */
    public function setInstituicao($instituicao)
    {
        $this->instituicao = $instituicao;
    }


    /**
     * @param array $state
     * @return Dependente
     * @throws DBException
     * @throws ParameterException
     */
    public static function fromState(array $state)
    {
        $dependente = new self();

        if (array_key_exists('rh31_codigo', $state)) {
            $dependente->setCodigo((int)$state['rh31_codigo']);
        }

        if (array_key_exists('rh31_regist', $state)) {
            $dependente->setMatricula((int)$state['rh31_regist']);
        }

        if (array_key_exists('rh31_nome', $state)) {
            $dependente->setNome((string)$state['rh31_nome']);
        }

        if (array_key_exists('rh31_dtnasc', $state)) {
            if ($state['rh31_dtnasc'] !== null) {
                $dependente->setDataNascimento(DBDate::create($state['rh31_dtnasc']));
            }
        }

        if (array_key_exists('rh31_gparen', $state)) {
            $dependente->setGrauParentesco((string)$state['rh31_gparen']);
        }

        if (array_key_exists('rh31_depend', $state)) {
            $dependente->setSalarioFamilia((string)$state['rh31_depend']);
        }

        if (array_key_exists('rh31_irf', $state)) {
            $dependente->setTipo((string)$state['rh31_irf']);
        }

        if (array_key_exists('rh31_especi', $state)) {
            $dependente->setCondicaoEspecial((string)$state['rh31_especi']);
        }

        if (array_key_exists('rh31_fins_previdenciarios', $state)) {
            $dependente->setFinsPrevidenciarios($state['rh31_fins_previdenciarios'] === 't');
        }

        if (array_key_exists('dp01_cpf', $state)) {
            $dependente->setCpf((string)$state['dp01_cpf']);
        }

        if (array_key_exists('dp01_sexo', $state)) {
            $dependente->setSexo((string)$state['dp01_sexo']);
        }

        if (array_key_exists('dp01_instit', $state)) {
            $dependente->setInstituicao((int)$state['dp01_instit']);
        }
        if (array_key_exists('rh31_tipoparentesco', $state)) {
            $dependente->setTipoParentesco($state['rh31_tipoparentesco']);
        }

        if (array_key_exists('rh31_descricaoparentesco', $state)) {
            $dependente->setDescricaoParentesco($state['rh31_descricaoparentesco']);
        }

        if (array_key_exists('rh31_irfcomplementar', $state)) {
            $dependente->setIrfComplementar($state['rh31_irfcomplementar'] === 't');
        }

        return $dependente;
    }

    /**
     * @return string
     */
    public function getDescricaoParentesco()
    {
        return $this->descricaoParentesco;
    }

    /**
     * @param string $descricaooParentesco
     */
    public function setDescricaoParentesco($descricaoParentesco)
    {
        $this->descricaoParentesco = $descricaoParentesco;
    }

        /**
     * @return string
     */
    public function getTipoParentesco()
    {
        return $this->tipoParentesco;
    }

    /**
     * @param string $tipoParentesco
     */
    public function setTipoParentesco($tipoParentesco)
    {
        $this->tipoParentesco = $tipoParentesco;
    }

    /**
     * @return bool
     */
    public function isIrfComplementar()
    {
        return $this->irfComplementar;
    }

    /**
     * @param  bool  $irfComplementar
     */
    public function setIrfComplementar($irfComplementar)
    {
        $this->irfComplementar = $irfComplementar;
    }


    /**
     * @return array
     * @throws ParameterException
     */
    public function toArray()
    {
        return array(
            'codigo' => $this->getCodigo(),
            'matricula' => $this->getMatricula(),
            'nome' => $this->getNome(),
            'nascimento' => $this->getDataNascimento()->convertTo(DBDate::DATA_PTBR),
            'grauParentesco' => $this->getGrauParentesco(),
            'salarioFamilia' => $this->getSalarioFamilia(),
            'tipo' => $this->getTipo(),
            'condicaoEspecial' => $this->getCondicaoEspecial(),
            'cpf' => $this->getCpf(),
            'finsPrevidenciarios' => $this->isFinsPrevidenciarios(),
            'sexo' => $this->getSexo(),
            'instituicao' => $this->getInstituicao(),
            'irfComplementar' => $this->isIrfComplementar()
        );
    }

    /**
     * @return int
     */
    public function getCodigo()
    {
        return $this->iCodigo;
    }

    /**
     * @return int
     */
    public function getMatricula()
    {
        return $this->matricula;
    }

    /**
     * @param int $matricula
     */
    public function setMatricula($matricula)
    {
        $this->matricula = $matricula;
    }

    /**
     * @return string
     */
    public function getNome()
    {
        return $this->sNome;
    }

    /**
     * @return DBDate
     */
    public function getDataNascimento()
    {
        return $this->oDataNascimento;
    }

    /**
     * @return string
     */
    public function getGrauParentesco()
    {
        return $this->sGrauParentesco;
    }

    /**
     * @return string
     */
    public function getSalarioFamilia()
    {
        return $this->sSalarioFamilia;
    }

    /**
     * @return int
     */
    public function getTipo()
    {
        return $this->iTipo;
    }

    /**
     * @return string
     */
    public function getCondicaoEspecial()
    {
        return $this->sCondicaoEspecial;
    }

    /**
     * @return string
     */
    public function getCpf()
    {
        return $this->sCpf;
    }

    /**
     * @return  bool
     */
    public function isFinsPrevidenciarios()
    {
        return $this->finsPrevidenciarios;
    }

    /**
     * @param bool $finsPrevidenciarios
     */
    public function setFinsPrevidenciarios($finsPrevidenciarios)
    {
        $this->finsPrevidenciarios = $finsPrevidenciarios;
    }

    /**
     * @return String
     */
    public function getSexo()
    {
        return $this->sexo;
    }

    public function isConjuge()
    {
        return $this->getGrauParentesco() == "C";
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function save()
    {


        $sMsgErro = 'Falha ao salvar Dependente';

        /**
         * Verifica se existe alguma transação ativa
         */
        if (!db_utils::inTransaction()) {
            throw new Exception("{$sMsgErro}, nenhuma transação encontrada para dependentede CPF {$this->getCpf()}!");
        }

        $rhdepend = new cl_rhdepend();
        $rhdepend->rh31_nome = pg_escape_string($this->getNome());
        $rhdepend->rh31_regist = $this->getMatricula();
        $rhdepend->rh31_irf = $this->getTipo();
        $rhdepend->rh31_fins_previdenciarios = ($this->isFinsPrevidenciarios() === true ? "t" :  "f");
        $rhdepend->rh31_especi = $this->getCondicaoEspecial();
        $rhdepend->rh31_dtnasc = $this->getDataNascimento();
        $rhdepend->rh31_gparen = $this->getGrauParentesco();
        $rhdepend->rh31_depend = $this->getSalarioFamilia();
        $rhdepend->rh31_tipoparentesco = $this->getTipoParentesco();
        $rhdepend->rh31_irfcomplementar = ($this->isIrfComplementar() === true ? "t" :  "f");

        $rhdependeplug = new cl_rhdependeplug();
        $rhdependeplug->dp01_regist = $this->getMatricula();
        $rhdependeplug->dp01_cpf = $this->getCpf();
        $rhdependeplug->dp01_sexo = $this->getSexo();
        $rhdependeplug->dp01_instit = $this->getInstituicao();

        if (empty($this->getCodigo())) {
            $rhdepend->incluir(null);
            if ($rhdepend->erro_status != "1") {
                throw new \Exception($rhdepend->erro_msg);
            }
            $this->setCodigo($rhdepend->rh31_codigo);
            $rhdependeplug->dp01_rhdepend = $this->getCodigo();
            $rhdependeplug->incluir();
            if ($rhdependeplug->erro_status != "1") {
                throw new \Exception($rhdependeplug->erro_msg);
            }
        } else {
            $rhdepend->rh31_codigo = $this->getCodigo();
            $rhdepend->alterar($this->getCodigo());
            if ($rhdepend->erro_status != "1") {
                throw new \Exception($rhdepend->erro_msg);
            }
            $rhdependeplug->dp01_rhdepend = $this->getCodigo();
            $rhdependeplug->alterar();
            if ($rhdepend->erro_status != "1") {
                throw new \Exception($rhdependeplug->erro_msg);
            }
        }

        return true;
    }

    public function delete()
    {

        $rhdependplug = new cl_rhdependeplug();
        $rhdependplug->excluir(null, "dp01_rhdepend = {$this->getCodigo()}");

        $rhdepend = new cl_rhdepend();
        $rhdepend->excluir($this->getCodigo());
        if ($rhdepend->erro_status == 0) {
            throw new \Exception($rhdepend->erro_msg);
        }
    }
}
