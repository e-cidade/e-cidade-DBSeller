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

use ECidade\Saude\Laboratorio\Repository\MaterialColetaRepository;

define("MENSAGENS_EXAME_MODEL", "saude.laboratorio.Exame.");

class Exame
{
    protected $iCodigo;

    /**
     * Nome do Exame
     * @var string
     */
    protected $sNome = '';

    /**
     * Observação do Exame
     * @var string
     */
    protected $sObservacao = '';

    /**
     * Atributos do Exame
     * @var AtributoExame[]
     */
    protected $aAtributos = array();

    /**
     * Lista com os atributos dispensados do exame
     * @var array
     */
    private $aAtributosDispensados = array();

    /**
     * Instância de ProcedimentoSaude, do procedimento vinculado ao exame
     * @var ProcedimentoSaude
     */
    private $oProcedimento = null;

    /**
     * @var string
     */
    private $sigla;

    /**
     * @var integer
     */
    private $ativo;

    /**
     * @var integer
     */
    private $unidadeInicial;

    /**
     * @var integer
     */
    private $unidadeFinal;

    /**
     * @var integer
     */
    private $gerar;

    /**
     * @var string
     */
    private $interferencia;

    /**
     * @var integer
     */
    private $dias;

    /**
     * @var DateTime
     */
    private $dataInicio;

    /**
     * @var DateTime
     */
    private $dataFim;

    /**
     * @var integer
     */
    private $sexo;

    /**
     * @var integer
     */
    private $idadeMinima;

    /**
     * @var integer
     */
    private $idadeMaxima;

    /**
     * @var string
     */
    private $descricao;

    /**
     * @var MateriaColeta[]
     */
    private $materiaisColeta;

    /**
     * @var bool
     */
    private $exibeHistoricoResultados;

    /**
     * Instancia o novo Exame
     *
     * @param $iCodigoExame
     * @throws BusinessException
     */
    public function __construct($iCodigoExame = null)
    {
        if (empty($iCodigoExame)) {
            return $this;
        }

        $oDaoExame = new cl_lab_exame();
        $oDadosExame = db_utils::getRowFromDao($oDaoExame, array($iCodigoExame));

        if ($oDadosExame == null) {
            throw new BusinessException("Exame não cadastrado");
        }

        $this->iCodigo = $oDadosExame->la08_i_codigo;
        $this->sNome = $oDadosExame->la08_c_descr;
        $this->sObservacao = $oDadosExame->la08_observacao;
        $this->sigla = $oDadosExame->la08_c_sigla;
        $this->exibeHistoricoResultados = !($oDadosExame->la08_exibehistoricoresultados === 'f');
    }

    /**
     * @return int
     */
    public function getUnidadeInicial()
    {
        return $this->unidadeInicial;
    }

    /**
     * @param int $unidadeInicial
     */
    public function setUnidadeInicial($unidadeInicial)
    {
        $this->unidadeInicial = $unidadeInicial;
    }

    /**
     * @return int
     */
    public function getAtivo()
    {
        return $this->ativo;
    }

    /**
     * @param int $ativo
     */
    public function setAtivo($ativo)
    {
        $this->ativo = $ativo;
    }

    /**
     * @return int
     */
    public function getUnidadeFinal()
    {
        return $this->unidadeFinal;
    }

    /**
     * @param int $unidadeFinal
     */
    public function setUnidadeFinal($unidadeFinal)
    {
        $this->unidadeFinal = $unidadeFinal;
    }

    /**
     * @return int
     */
    public function getGerar()
    {
        return $this->gerar;
    }

    /**
     * @param int $gerar
     */
    public function setGerar($gerar)
    {
        $this->gerar = $gerar;
    }

    /**
     * @return string
     */
    public function getInterferencia()
    {
        return $this->interferencia;
    }

    /**
     * @param string $interferencia
     */
    public function setInterferencia($interferencia)
    {
        $this->interferencia = $interferencia;
    }

    /**
     * @return int
     */
    public function getDias()
    {
        return $this->dias;
    }

    /**
     * @param int $dias
     */
    public function setDias($dias)
    {
        $this->dias = $dias;
    }

    /**
     * @return DateTime
     */
    public function getDataInicio()
    {
        return $this->dataInicio;
    }

    /**
     * @param DateTime $dataInicio
     */
    public function setDataInicio($dataInicio)
    {
        $this->dataInicio = $dataInicio;
    }

    /**
     * @return DateTime
     */
    public function getDataFim()
    {
        return $this->dataFim;
    }

    /**
     * @param DateTime $dataFim
     */
    public function setDataFim($dataFim)
    {
        $this->dataFim = $dataFim;
    }

    /**
     * @return int
     */
    public function getSexo()
    {
        return $this->sexo;
    }

    /**
     * @param int $sexo
     */
    public function setSexo($sexo)
    {
        $this->sexo = $sexo;
    }

    /**
     * @return int
     */
    public function getIdadeMinima()
    {
        return $this->idadeMinima;
    }

    /**
     * @param int $idadeMinima
     */
    public function setIdadeMinima($idadeMinima)
    {
        $this->idadeMinima = $idadeMinima;
    }

    /**
     * @return int
     */
    public function getIdadeMaxima()
    {
        return $this->idadeMaxima;
    }

    /**
     * @param int $idadeMaxima
     */
    public function setIdadeMaxima($idadeMaxima)
    {
        $this->idadeMaxima = $idadeMaxima;
    }

    /**
     * @return string
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param string $descricao
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    /**
     * @return string
     */
    public function getSigla()
    {
        return $this->sigla;
    }

    /**
     * @param string $sigla
     */
    public function setSigla($sigla)
    {
        $this->sigla = $sigla;
    }

    /**
     * @param mixed $iCodigo
     */
    public function setCodigo($iCodigo)
    {
        $this->iCodigo = $iCodigo;
    }

    /**
     * @param string $sObservacao
     */
    public function setObservacao($sObservacao)
    {
        $this->sObservacao = $sObservacao;
    }

    /**
     * @return AtributoExame[]
     */
    public function getAtributos()
    {
        if (count($this->aAtributos) > 0) {
            return $this->aAtributos;
        }

        $aAtributosDispensadosNoExame = $this->getAtributosDispensados();
        $aAtributos = AtributoExameRepository::getByCodigoExame($this->iCodigo);

        foreach ($aAtributos as $key => $atributo) {
            if (in_array($atributo->getCodigo(), $aAtributosDispensadosNoExame)) {
                unset($aAtributos[$key]);
            }
        }

        $this->aAtributos = $aAtributos;

        return $this->aAtributos;
    }

    protected function getAtributosDispensados()
    {
        if (count($this->aAtributosDispensados) == 0) {
            $oDaoAtributosDispensados = new cl_lab_examedisp();
            $sSqlAtributos = $oDaoAtributosDispensados->sql_query(
                null,
                "la50_i_atributo as item",
                null,
                " la42_i_exame={$this->iCodigo} "
            );

            $rsAtributos = $oDaoAtributosDispensados->sql_record($sSqlAtributos);
            if ($rsAtributos && $oDaoAtributosDispensados->numrows > 0) {
                for ($iAtributo = 0; $iAtributo < $oDaoAtributosDispensados->numrows; $iAtributo++) {
                    $this->aAtributosDispensados[] = db_utils::fieldsMemory($rsAtributos, $iAtributo)->item;
                }
            }
        }

        return $this->aAtributosDispensados;
    }

    /**
     * Retorna a instância do procedimento vinculado ao exame
     * @return ProcedimentoSaude
     * @throws DBException
     */
    public function getProcedimento()
    {
        $oDaoExameProced = new cl_lab_exameproced();
        $sWhereExameProced = "la53_i_exame = {$this->iCodigo}";
        $sSqlExameProced = $oDaoExameProced->sql_query_file(null, "la53_i_procedimento", null, $sWhereExameProced);
        $rsExameProced = db_query($sSqlExameProced);

        if (!$rsExameProced) {
            $oMensagem = new stdClass();
            $oMensagem->sErro = pg_result_error($rsExameProced);
            throw new DBException(_M(MENSAGENS_EXAME_MODEL . "erro_buscar_procedimento"));
        }

        if (pg_num_rows($rsExameProced)) {
            $this->oProcedimento = new ProcedimentoSaude(
                db_utils::fieldsMemory(
                    $rsExameProced,
                    0
                )->la53_i_procedimento
            );
        }

        return $this->oProcedimento;
    }

    /**
     * Retorna o código do exame
     * @return integer
     */
    public function getCodigo()
    {
        return $this->iCodigo;
    }

    /**
     * Retorna o nome do exame
     * @return string
     */
    public function getNome()
    {
        return $this->sNome;
    }

    /**
     * Retorna a observação do exame
     * @return string
     */
    public function getObservacao()
    {
        return $this->sObservacao;
    }

    public function getMaterialColeta()
    {
        $aMateriaisColeta = array();

        if (empty($this->iCodigo)) {
            return $aMateriaisColeta;
        }

        $oDaoMaterialColeta = new cl_lab_examematerial();

        $sWhere = " la19_i_exame = {$this->iCodigo} ";
        $sCampos = " la11_c_descr as metodo_coleta, la15_c_descr as material_coleta ";
        $sCampos .= ", la19_i_materialcoleta as codigo_material_coleta";

        $sSqlMaterialColeta = $oDaoMaterialColeta->sql_query(null, $sCampos, null, $sWhere);
        $rsMaterialColeta = db_query($sSqlMaterialColeta);

        if (!$rsMaterialColeta) {
            throw new DBException('Falhar ao buscar os materiais de coleta.');
        }

        $oDadosMaterialColeta = db_utils::getColectionByRecord($rsMaterialColeta);

        return $oDadosMaterialColeta;
    }

    /**
     * @return MateriaColeta[]
     */
    public function getMateriaisColeta()
    {
        return $this->materiaisColeta;
    }

    /**
     * @param MateriaColeta[] $materiaisColeta
     */
    public function setMateriaisColeta($materiaisColeta)
    {
        $this->materiaisColeta = $materiaisColeta;
    }


    /**
     * @return array
     */
    public function toArray()
    {
        $materiaisColeta = array();
        foreach ((array)$this->getMateriaisColeta() as $materialColeta) {
            $materiaisColeta[] = $materialColeta->toArray();
        }

        return array(
            'la08_i_codigo' => $this->getCodigo(),
            'la08_c_sigla' => $this->getSigla(),
            'la08_c_descr' => $this->getDescricao(),
            'la08_observacao' => $this->getObservacao(),
            'materiaisColeta' => $materiaisColeta
        );
    }

    /**
     * @param array $state
     * @return Exame
     * @throws BusinessException
     */
    public static function fromState(array $state)
    {
        $exame = new self;
        if (array_key_exists('la08_i_codigo', $state)) {
            $exame->setCodigo((int)$state['la08_i_codigo']);

            $repositoryMaterialColeta = new MaterialColetaRepository(new \cl_lab_materialcoleta);
            $exame->setMateriaisColeta(
                $repositoryMaterialColeta->getMateriaisExame(
                    $exame->getCodigo(),
                    new cl_lab_examematerial()
                )
            );
        }

        if (array_key_exists('la08_c_sigla', $state)) {
            $exame->setSigla($state['la08_c_sigla']);
        }

        if (array_key_exists('la08_c_descr', $state)) {
            $exame->setDescricao($state['la08_c_descr']);
        }

        if (array_key_exists('la08_i_idademax', $state)) {
            $exame->setIdadeMaxima((int)$state['la08_i_idademax']);
        }

        if (array_key_exists('la08_i_idademin', $state)) {
            $exame->setIdadeMinima((int)$state['la08_i_idademin']);
        }

        if (array_key_exists('la08_i_sexo', $state)) {
            $exame->setSexo((int)$state['la08_i_sexo']);
        }

        if (array_key_exists('la08_d_inicio', $state)) {
            $exame->setDataInicio(new DateTime($state['la08_d_inicio']));
        }

        if (array_key_exists('la08_d_fim', $state)) {
            $exame->setDataFim(new DateTime($state['la08_d_fim']));
        }

        if (array_key_exists('la08_i_dias', $state)) {
            $exame->setDias((int)$state['la08_i_dias']);
        }

        if (array_key_exists('la08_t_interferencia', $state)) {
            $exame->setInterferencia($state['la08_t_interferencia']);
        }

        if (array_key_exists('la08_i_gerar', $state)) {
            $exame->setGerar((int)$state['la08_i_gerar']);
        }

        if (array_key_exists('la08_i_undidadeini', $state)) {
            $exame->setUnidadeInicial((int)$state['la08_i_undidadeini']);
        }

        if (array_key_exists('la08_i_undidadefim', $state)) {
            $exame->setUnidadeFinal((int)$state['la08_i_undidadefim']);
        }

        if (array_key_exists('la08_i_ativo', $state)) {
            $exame->setAtivo((int)$state['la08_i_ativo']);
        }

        if (array_key_exists('la08_observacao', $state)) {
            $exame->setObservacao($state['la08_observacao']);
        }

        return $exame;
    }

    public function getExibeHistoricoResultados()
    {
        return $this->exibeHistoricoResultados;
    }
}
