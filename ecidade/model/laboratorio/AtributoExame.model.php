<?php
/**
 * E-cidade Software Publico para Gestão Municipal
 *   Copyright (C) 2009 DBSeller Serviços de Informática Ltda
 *                          www.dbseller.com.br
 *                          e-cidade@dbseller.com.br
 *   Este programa é software livre; você pode redistribuí-lo e/ou
 *   modificá-lo sob os termos da Licença Pública Geral GNU, conforme
 *   publicada pela Free Software Foundation; tanto a versão 2 da
 *   Licença como (a seu critério) qualquer versão mais nova.
 *   Este programa e distribuído na expectativa de ser útil, mas SEM
 *   QUALQUER GARANTIA; sem mesmo a garantia implícita de
 *   COMERCIALIZAÇÃO ou de ADEQUAÇÃO A QUALQUER PROPÓSITO EM
 *   PARTICULAR. Consulte a Licença Pública Geral GNU para obter mais
 *   detalhes.
 *   Você deve ter recebido uma cópia da Licença Pública Geral GNU
 *   junto com este programa; se não, escreva para a Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *   02111-1307, USA.
 *   Cópia da licença no diretório licenca/licenca_en.txt
 *                                 licenca/licenca_pt.txt
 */

const CAMINHO_MENSAGEM_ATRIBUTO_EXAME = "laboratorio.AtributoExame";

/**
 * Atributos de um exame laboratorial
 * Representa a tabela lab_atributo
 *
 * @package Laboratorio
 * @author Iuri Guntchnigg iuri@dbseller.com.br
 * @version $Revision: 1.2 $
 */
class AtributoExame
{
    const SINTETICO = 1;

    const ANALITICO = 2;

    const REFERENCIA_FIXA = 1;

    const REFERENCIA_SELECIONAVEL = 3;

    const REFERENCIA_NUMERICA = 2;

    /**
     * Codigo do Exame
     * @var integer
     */
    protected $iCodigo;

    /**
     * Estrutura do atributo
     * @var string
     */
    protected $sEstrutural;

    /**
     * Nome do Atributo
     * @var string
     */
    protected $sNome;

    /**
     * Tipo do Atributo
     * @var integer
     */
    protected $tipo;

    /**
     * Nivel do Atributo
     * @var integer
     */
    protected $iNivel;

    /**
     * Unidade de medida o Atributo
     * @var AtributoExameUnidade
     */
    protected $oUnidadeMedida;

    /**
     * Retorna os valores de Rerferencia do Atributo
     *
     * @var AtributoValorReferenciaNumerico|AtributoValorReferenciaAlfaNumerico
     */
    protected $aValoresDeReferencia = array();

    /**
     * Codigo da unidade de medida
     * @var integer
     */
    protected $iCodigoUnidadeMedida = null;

    /**
     * Codigo de Referência
     * @var integer
     */
    protected $iCodigoReferencia = null;

    /**
     * Tipo da Referencia do atributo
     * @var integer
     */
    private $iTipoReferencia = null;

    /**
     * Sigla do atributo do exame
     * @var string
     */
    protected $sigla;

    /**
     * Recebe uma formula para calculo da celula
     * @var string
     */
    protected $formula;

    /**
     * Recebe informação se o atributo é de preenchimento obrigatório
     * @var bool
     */
    protected $preenchimentoObrigatorio;

    /**
     * @var RequisicaoExame
     */
    protected $oExame;

    /**
     * Indica uma vinculação com um dos campos de 'outras informações' da tela de criação de requisição
     * @var string
     */
    protected $outrasInformacoes;

        /**
     * @var String Unidade de Medida do atributo
     */
    protected $unidadeMedida;

    public static function fromState(array $state)
    {
        $self = new self();
        if (array_key_exists('la25_i_codigo', $state)) {
            $self->setCodigo($state['la25_i_codigo']);
        }
        if (array_key_exists('la25_c_estrutural', $state)) {
            $self->setEstrutural($state['la25_c_estrutural']);
        }
        if (array_key_exists('la25_i_nivel', $state)) {
            $self->setNivel($state['la25_i_nivel']);
        }
        if (array_key_exists('la25_c_descr', $state)) {
            $self->setNome($state['la25_c_descr']);
        }
        if (array_key_exists('la25_c_tipo', $state)) {
            $self->setTipo($state['la25_c_tipo']);
        }
        if (array_key_exists('la25_sigla', $state)) {
            $self->setSigla($state['la25_sigla']);
        }
        if (array_key_exists('la25_formula', $state)) {
            $self->setFormula($state['la25_formula']);
        }
        if (array_key_exists('la25_preenchimentoobrigatorio', $state)) {
            $self->setPreenchimentoObrigatorio($state['la25_preenchimentoobrigatorio'] === 't');
        }
        if (array_key_exists('la25_outrasinformacoes', $state)) {
            $self->setOutrasInformacoes($state['la25_outrasinformacoes']);
        }

        return $self;
    }

    /**
     * Instancia um  atributo cadastrado no sistema, ou uma instancia vazia
     *
     * @param integer $iCodigo codigo do atributo
     * @throws BusinessException
     * @throws Exception
     */
    public function __construct($iCodigo = null, $sigla = null)
    {
        $where = array();

        if (!empty ($iCodigo)) {
            $where[] = "la25_i_codigo = {$iCodigo}";
        }

        if (!empty ($sigla)) {
            $where[] = "la25_sigla = '{$sigla}'";
        }

        if (!empty ($where)) {
            $oDaoAtributo = new cl_lab_atributo();

            $sCampos = "lab_atributo.*,";
            $sCampos .= "lab_undmedida.*,";
            $sCampos .= "lab_valorreferencia.*,";
            $sCampos .= "exists (select 1 ";
            $sCampos .= "          from lab_tiporeferenciaalnumerico";
            $sCampos .= "         where la30_i_valorref =la27_i_codigo) as numerico, ";
            $sCampos .= "exists (select 1 ";
            $sCampos .= "          from lab_tiporeferenciaalfa";
            $sCampos .= "               left join lab_valorrefselgrupo on la51_i_referencia = la29_i_codigo";
            $sCampos .= "         where la29_i_valorref =la27_i_codigo and la51_i_referencia is null) as fixo,";
            $sCampos .= "exists (select 1 ";
            $sCampos .= "          from lab_tiporeferenciaalfa";
            $sCampos .= "               left join lab_valorrefselgrupo on la51_i_referencia = la29_i_codigo";
            $sCampos .= "         where la29_i_valorref =la27_i_codigo and la51_i_referencia is not null) as selecionavel";
            $where = implode(" and ", $where);
            $sSqlAtributo = $oDaoAtributo->sql_query_referencia(null, $sCampos, null, $where);
            $rsAtributo = db_query($sSqlAtributo);

            if (!$rsAtributo) {
                throw new Exception('Erro ao buscar o atributo do exame.');
            }

            if (pg_num_rows($rsAtributo) == 0) {
                throw new BusinessException(_M(CAMINHO_MENSAGEM_ATRIBUTO_EXAME . ".atributo_nao_cadastrado"));
            }

            $oDadosAtributo = db_utils::fieldsMemory($rsAtributo, 0);
            $this->setCodigo($oDadosAtributo->la25_i_codigo);
            $this->setEstrutural($oDadosAtributo->la25_c_estrutural);
            $this->setNivel($oDadosAtributo->la25_i_nivel);
            $this->setNome($oDadosAtributo->la25_c_descr);
            $this->setTipo($oDadosAtributo->la25_c_tipo);
            $this->setSigla($oDadosAtributo->la25_sigla);
            $this->setFormula($oDadosAtributo->la25_formula);
            $this->setPreenchimentoObrigatorio($oDadosAtributo->la25_preenchimentoobrigatorio === 't');
            $this->setOutrasInformacoes($oDadosAtributo->la25_outrasinformacoes);
            $this->setUnidadeMedidaAtributo($oDadosAtributo->la13_c_descr);

            $this->iCodigoUnidadeMedida = $oDadosAtributo->la27_i_unidade;
            $this->iCodigoReferencia = $oDadosAtributo->la27_i_codigo;
            if ($oDadosAtributo->numerico == 't') {
                $this->iTipoReferencia = AtributoExame::REFERENCIA_NUMERICA;
            }
            if ($oDadosAtributo->selecionavel == 't') {
                $this->iTipoReferencia = AtributoExame::REFERENCIA_SELECIONAVEL;
            }

            if ($oDadosAtributo->fixo == 't') {
                $this->iTipoReferencia = AtributoExame::REFERENCIA_FIXA;
            }
        }
    }

    /**
     * Define o codigo do Atributo
     * @param int $iCodigo Codigo do Atributo
     */
    private function setCodigo($iCodigo)
    {
        $this->iCodigo = $iCodigo;
    }

    /**
     *  Retorna o código do atributo
     * @return int
     */
    public function getCodigo()
    {
        return $this->iCodigo;
    }

    /**
     * Retorna o codigo da referencia
     * @return integer
     */
    public function getCodigoReferencia()
    {
        return $this->iCodigoReferencia;
    }

    /**
     * define o Nivel do atributo na Estrutura do exame
     * @param int $iNivel
     */
    public function setNivel($iNivel)
    {
        $this->iNivel = $iNivel;
    }

    /**
     * Retorna o nivel do atributo dentro do exame
     * @return int
     */
    public function getNivel()
    {
        return $this->iNivel;
    }

    /**
     * Define o codigo estrutural do exame
     * @param string $sEstrutural
     */
    public function setEstrutural($sEstrutural)
    {
        $this->sEstrutural = $sEstrutural;
    }

    /**
     * Define o codigo Estrutural do exame
     * @return string
     */
    public function getEstrutural()
    {
        return $this->sEstrutural;
    }

    /**
     * Define o nome do atributo
     * @param string $sNome nome do atributo
     */
    public function setNome($sNome)
    {
        $this->sNome = $sNome;
    }

    /**
     * Retorna o nome do atributo
     * @return string
     */
    public function getNome()
    {
        return $this->sNome;
    }

    /**
     * @param int $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    /**
     * @return int
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * @return string
     */
    public function getDescricaoTipo()
    {
        $tipos = [
            self::SINTETICO => 'Sintético',
            self::ANALITICO => 'Analítico'
        ];

        return $tipos[$this->tipo];
    }

    /**
     * @param int $iCodigoReferencia
     * @return void
     */
    public function setCodigoReferencia($iCodigoReferencia)
    {
        $this->iCodigoReferencia = $iCodigoReferencia;
    }

    /**
     * @param int $iTipoReferencia
     * @return void
     */
    public function setTipoReferencia($iTipoReferencia)
    {
        $this->iTipoReferencia = $iTipoReferencia;
    }

    /**
     * Retorna o tipo de Referencia do atributo
     * @return int
     */
    public function getTipoReferencia()
    {
        return $this->iTipoReferencia;
    }

    /**
     * @param int $iCodigoUnidadeMedida
     * @return void
     */
    public function setCodigoUnidadeMedida($iCodigoUnidadeMedida)
    {
        $this->iCodigoUnidadeMedida = $iCodigoUnidadeMedida;
    }

    /**
     * Retorna a Unidade de medida do Atributo
     * @return AtributoExameUnidade
     * @throws BusinessException
     */
    public function getUnidadeMedida()
    {
        if (empty($this->oUnidadeMedida) && !empty($this->iCodigoUnidadeMedida)) {
            $this->oUnidadeMedida = new AtributoExameUnidade($this->iCodigoUnidadeMedida);
        }

        return $this->oUnidadeMedida;
    }

    /**
     * Retorna os valores de Referencia do Atributo
     * @return AtributoValorReferenciaAlfaNumerico|AtributoValorReferenciaNumerico
     */
    public function getValoresReferencia()
    {
        if (count($this->aValoresDeReferencia) == 0) {
            $oDaoAtributoReferencia = new cl_lab_valorreferencia();

            $sWhere = "la27_i_atributo = {$this->iCodigo}";
            $sCampos = "la30_i_codigo, la29_i_codigo, case when la30_i_codigo is not null then 2 else 1 end as tipo";

            $sSqlValoreReferencia = $oDaoAtributoReferencia->sql_query_tipos(null, $sCampos, null, $sWhere);
            $rsValoreReferencia = $oDaoAtributoReferencia->sql_record($sSqlValoreReferencia);

            if ($oDaoAtributoReferencia->numrows > 0) {
                for ($iValor = 0; $iValor < $oDaoAtributoReferencia->numrows; $iValor++) {
                    $oDadosValor = db_utils::fieldsMemory($rsValoreReferencia, $iValor);

                    switch ($oDadosValor->tipo) {
                        case 2:
                            $this->aValoresDeReferencia[] = new AtributoValorReferenciaNumerico(
                                $oDadosValor->la30_i_codigo
                            );
                            break;
                    }
                }
            }
        }

        return $this->aValoresDeReferencia;
    }

    /**
     * Adiciona uma referencia ao atributo
     * @param $oReferencia
     */
    public function adicionarReferencia($oReferencia)
    {
        $this->aValoresDeReferencia[] = $oReferencia;
    }

    /**
     * Retorna os valores de Referencia do Atributo
     *
     * @param RequisicaoExame $oExame
     * @return AtributoValorReferenciaNumerico|AtributoValorReferenciaAlfaNumerico
     */
    public function getValoresDeReferenciaParaExame(RequisicaoExame $oExame)
    {
        $this->oExame = $oExame;

        switch ($this->iTipoReferencia) {
            case AtributoExame::REFERENCIA_NUMERICA:
                return $this->getAtributoNumerico();
                break;

            case AtributoExame::REFERENCIA_FIXA:
                return $this->getAtributoAlfaNumerico();
                break;

            case AtributoExame::REFERENCIA_SELECIONAVEL:
                return $this->getAtributoAlfaNumerico(true);
                break;
        }

        return null;
    }

    /**
     * Retorna os dados de Referencia Numerico
     *
     * @return AtributoValorReferenciaNumerico|null
     */
    protected function getAtributoNumerico()
    {
        $oDaoAtributoReferencia = new cl_lab_valorreferencia();
        $oSolicitante = $this->oExame->getSolicitante();
        $oDtNascimento = $oSolicitante->getDataNascimento();
        $sNascimento = $oDtNascimento->getDate();

        $sWhere = " and age(current_date, '{$sNascimento}') between la59_periodoinicial and la59_periodofinal ";
        $sWhere .= " and (la60_sexo = '{$oSolicitante->getSexo()}' or la60_sexo is null)";

        $sSqlValoreReferencia = $oDaoAtributoReferencia->sql_query_referencia_numerica($this->getCodigo(), $sWhere);
        $rsValoreReferencia = $oDaoAtributoReferencia->sql_record($sSqlValoreReferencia);

        if ($oDaoAtributoReferencia->numrows > 0) {
            $oDadosValor = db_utils::fieldsMemory($rsValoreReferencia, 0);

            return new AtributoValorReferenciaNumerico($oDadosValor->la30_i_codigo);
        }

        return null;
    }

    /**
     * Retorna os dados o Atributo Fixo
     *
     * @return AtributoValorReferenciaAlfaNumerico|null
     */
    protected function getAtributoAlfaNumerico()
    {
        $oDaoReferenciaFixa = new cl_lab_tiporeferenciaalfa();
        $sSqlReferenciaFixa = $oDaoReferenciaFixa->sql_query_file(
            null,
            "*",
            null,
            "la29_i_valorref = {$this->iCodigoReferencia}"
        );
        $rsReferenciaFixa = $oDaoReferenciaFixa->sql_record($sSqlReferenciaFixa);
        if (!$rsReferenciaFixa || $oDaoReferenciaFixa->numrows == 0) {
            return null;
        }

        $oDadosReferenciaFixa = db_utils::fieldsMemory($rsReferenciaFixa, 0);
        $oReferenciaFixa = new AtributoValorReferenciaAlfaNumerico($oDadosReferenciaFixa->la29_i_codigo);
        $oReferenciaFixa->setTamanho($oDadosReferenciaFixa->la29_v_fixo);

        return $oReferenciaFixa;
    }

    /**
     * @return string
     */
    public function getSigla()
    {
        return $this->sigla;
    }

    /**
     * @param $sigla
     */
    public function setSigla($sigla)
    {
        $this->sigla = $sigla;
    }

    /**
     * Retorna a formula
     * @return string
     */
    public function getFormula()
    {
        return $this->formula;
    }

    /**
     * Define a formula
     * @param string $formula
     * @return string $formula
     */
    public function setFormula($formula)
    {
        return $this->formula = $formula;
    }

    /**
     * @param Exame $exame
     * @return array
     * @throws Exception
     */
    public function getAtributosFormula(Exame $exame)
    {
        $abreviaturas = $this->getSiglasFormula();
        $atributosFormula = array();

        foreach ($abreviaturas as $abreviatura) {
            try {
                $atributoExame = AtributoExameRepository::getBySiglaExame($abreviatura, $exame);

                if (!$atributoExame instanceof AtributoExame) {
                    throw new Exception('');
                }

                $sigla = new stdClass();
                $sigla->codigo = $atributoExame->getCodigo();
                $sigla->sigla = $abreviatura;
                $sigla->valor = "";
                $atributosFormula[] = $sigla;
            } catch (Exception $erro) {
                $dadosMensagem = new stdClass();
                $dadosMensagem->sigla = $abreviatura;
                $dadosMensagem->atributo = $this->getNome();
                $dadosMensagem->exame = $exame->getNome();

                throw new Exception(_M(CAMINHO_MENSAGEM_ATRIBUTO_EXAME . ".sigla_nao_encontrada", $dadosMensagem));
            }
        }

        return $atributosFormula;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getSiglasFormula()
    {
        $expressao = "/\[(\w+)\]/";
        preg_match_all($expressao, $this->formula, $siglas);

        if ($siglas === false) {
            $mensagem = 'Não foi possível encontrar os atributos que fazem parte do resultado.';
            $mensagem .= ' Verifique se a fórmula está correta.';

            throw new Exception($mensagem);
        }

        return $siglas[1];
    }

    /**
     * @return bool
     */
    public function preenchimentoObrigatorio()
    {
        return $this->preenchimentoObrigatorio;
    }

    /**
     * @param bool $preenchimentoObrigatorio
     */
    public function setPreenchimentoObrigatorio($preenchimentoObrigatorio)
    {
        $this->preenchimentoObrigatorio = $preenchimentoObrigatorio;
    }

    /**
     * @return string
     */
    public function getOutrasInformacoes()
    {
        return $this->outrasInformacoes;
    }

        /**
     * @return string
     */
    public function getUnidadeMedidaAtributo()
    {
        return $this->unidadeMedida;
    }

    /**
     * @param string $outrasInformacoes
     */
    public function setOutrasInformacoes($outrasInformacoes)
    {
        $this->outrasInformacoes = $outrasInformacoes;
    }

       /**
     * @param string $unidadeMedida
     */
    public function setUnidadeMedidaAtributo($unidadeMedida){
        $this->unidadeMedida = $unidadeMedida;
    }
}
