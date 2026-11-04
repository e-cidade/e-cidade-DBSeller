<?php
/**
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

use ECidade\Lib\Formula\Calculo;
use ECidade\Saude\Laboratorio\Atributo\Formula;
use ECidade\Saude\Laboratorio\Repository\RequisicaoExameRepository;
use ECidade\Saude\Laboratorio\Service\RequisicaoLaboratorialService;
use ECidade\Saude\Laboratorio\Repository\RequisicaoLaboratorialRepository;

/**
 * Resultado de Um exame Clinico
 * Class ResultadoExame
 * Representa a tabela lab_resultado
 */
class ResultadoExame
{
    const FONTE_MSG = 'saude.laboratorio.ResultadoExame.';

    /**
     * Requisicao do Exame
     * @var RequisicaoExame
     */
    private $oRequisicao;

    /**
     * Codigo do Resultado
     * @var integer
     */
    private $iResultado;

    /**
     * Diagnostico do Exame
     * @var string
     */
    private $sConsideracao = '';

    /**
     * @var ResultadoExameAtributo[]
     */
    private $aResultadoAtributos = array();

    /**
     * Data do resultado
     * @var DBDate
     */
    private $oData;

    /**
     * Resultados do exame anterior
     * @var ResultadoExameAtributo[]
     */
    private $aResultadoDoExameAnterior = array();

    /**
     * Requisição do exame anterior
     * @var RequisicaoExame
     */
    private $oRequisicaoAnterior;

    /**
     * @var AtributoExame $oAtributo
     */
    private $atributoBuscandoHistorico = null;

    /**
     * Array contendo os registros de resultados do exame.
     * @var stdClass[]|null $resultados
     */
    private static $resultados = null;

    /**
     * Array contendo os valores dos atributos para os resultados encontrados.
     * @var stdClass[]|array $resultadosAtributosExame
     */
    private static $resultadosAtributosExame = [];

    /**
     * Controla se precisa buscar resultados para a requisição(lab_requisicao) sendo buscada
     * @var bool
     */
    private static $atualizarResultados = true;

    /**
     * Instancia um novo Resultado
     * @param RequisicaoExame $oRequisicao
     */
    public function __construct(RequisicaoExame $oRequisicao)
    {
        $this->oRequisicao = $oRequisicao;
        $oDaoResultadoExame = new cl_lab_resultado();
        $sWhere = "la52_i_requiitem = {$this->oRequisicao->getCodigo()}";
        $sSqlResultado = $oDaoResultadoExame->sql_query_file(null, "*", null, $sWhere);
        $rsResultado = $oDaoResultadoExame->sql_record($sSqlResultado);

        if ($rsResultado && $oDaoResultadoExame->numrows > 0) {
            $oDados = db_utils::fieldsMemory($rsResultado, 0);
            $this->iResultado = $oDados->la52_i_codigo;
            $this->sConsideracao = $oDados->la52_diagnostico;
            $this->oData = DBDate::create($oDados->la52_d_data);
        }
    }

    /**
     * @param ResultadoExameAtributo $oResultadoAtributo
     * @param bool $atualizarAtributo
     * @return bool
     */
    public function adicionarResultadoParaAtributo(
        ResultadoExameAtributo $oResultadoAtributo,
        $atualizarAtributo = false
    ) {
        if (!$atualizarAtributo) {
            $oAtributoJaLancado = $this->getValorDoAtributo($oResultadoAtributo->getAtributo());
            if (!empty($oAtributoJaLancado)) {
                return false;
            }
        }

        $this->aResultadoAtributos[$oResultadoAtributo->getAtributo()->getCodigo()] = $oResultadoAtributo;

        return true;
    }

    /**
     * Retorna o resultado do Atributo Informado
     * @param AtributoExame $oAtributo
     * @return null|ResultadoExameAtributo
     * @throws Exception
     */
    public function getValorDoAtributo(AtributoExame $oAtributo)
    {
        $aAtributos = $this->getResultadoDosAtributos();
        $resultadoExameAtributo = isset($aAtributos[$oAtributo->getCodigo()]) ? $aAtributos[$oAtributo->getCodigo(
        )] : null;

        if ($oAtributo->getFormula() !== '') {
            $resultadoExameAtributo = $this->executarCalculoFormula($oAtributo);
        }

        if ($resultadoExameAtributo !== null) {
            return $resultadoExameAtributo;
        }

        return null;
    }

    /**
     * Retorna todos Atributos com seus Resultados
     * @return ResultadoExameAtributo[]
     */
    public function getResultadoDosAtributos()
    {
        if (count($this->aResultadoAtributos) == 0 && !empty($this->iResultado)) {
            $this->aResultadoAtributos = $this->buscarResultadosAtributo($this->iResultado);
        }

        return $this->aResultadoAtributos;
    }

    /**
     * @return ResultadoExameAtributo[]
     * @throws Exception
     */
    public function getResultadoCompleto()
    {
        $resultados = [];
        /**
         * Retorna resultados vazio junto dos atributos
         */
        if (empty($this->iResultado)) {
            $atributos = $this->oRequisicao->getExame()->getAtributos();
            foreach ($atributos as $atributo) {
                $resultadoAtributo = new ResultadoExameAtributo();
                $resultadoAtributo->setAtributo($atributo);

                switch ($atributo->getOutrasInformacoes()) {
                    case 'la22_peso':
                        $resultadoAtributo->setValorAbsoluto($this->oRequisicao->getPeso());
                        break;

                    case 'la22_altura':
                        $resultadoAtributo->setValorAbsoluto($this->oRequisicao->getAltura());
                        break;

                    case 'la22_volumeamostra':
                        $resultadoAtributo->setValorAbsoluto($this->oRequisicao->getVolumeAmostra());
                        break;
                }

                $resultados[] = $resultadoAtributo;
            }
            return $resultados;
        }

        $campos = [];
        $campos[] = 'la25_i_codigo,la25_c_estrutural,la25_i_nivel,la25_c_descr,la25_c_tipo,la25_sigla,la25_formula,la25_preenchimentoobrigatorio, la25_outrasinformacoes';
        $campos[] = 'exists (select 1 from lab_tiporeferenciaalnumerico where la30_i_valorref =la27_i_codigo) as numerico';
        $campos[] = 'exists (select 1 from lab_tiporeferenciaalfa left join lab_valorrefselgrupo on la51_i_referencia = la29_i_codigo where la29_i_valorref = la27_i_codigo and la51_i_referencia is null) as fixo';
        $campos[] = 'exists (select 1 from lab_tiporeferenciaalfa left join lab_valorrefselgrupo on la51_i_referencia = la29_i_codigo where la29_i_valorref = la27_i_codigo and la51_i_referencia is not null) as selecionavel';
        $campos[] = 'la27_i_codigo';
        $campos[] = 'la27_i_unidade';
        $campos[] = 'la39_i_codigo';
        $campos[] = 'la39_titulacao';
        $campos[] = 'la41_valorpercentual AS valorpercentual';
        $campos[] = 'la41_faixaescolhida AS faixautilizada';
        $case = [];
        $case[] = 'WHEN la41_f_valor IS NOT NULL THEN CAST(la41_f_valor AS VARCHAR)';
        $case[] = 'WHEN la40_i_valorrefsel IS NOT NULL THEN CAST(la40_i_valorrefsel AS VARCHAR)';
        $case[] = "WHEN la40_c_valor != '' THEN la40_c_valor";
        $case = implode(' ', $case);

        $campos[] = "CASE {$case} ELSE '' END as valor";
        $campos = implode(', ', $campos);

        $dao = new cl_lab_resultado();
        $sql = $dao->sql_query_resultado_completo($this->iResultado, $campos, 'la25_c_estrutural');
        $rs = $dao->sql_record($sql);

        if (!$rs) {
            throw new \Exception('Erro ao buscar resultado completo do exame.');
        }

        $this->aResultadoAtributos = [];
        foreach (\db_utils::getCollectionByRecord($rs) as $resultado) {
            if (array_key_exists($resultado->la25_i_codigo, $this->aResultadoAtributos)) {
                continue;
            }
            $atributo = AtributoExame::fromState((array)$resultado);
            $atributo->setCodigoUnidadeMedida($resultado->la27_i_unidade);
            $atributo->setCodigoReferencia($resultado->la27_i_codigo);

            if ($resultado->numerico == 't') {
                $atributo->setTipoReferencia(AtributoExame::REFERENCIA_NUMERICA);
            }
            if ($resultado->selecionavel == 't') {
                $atributo->setTipoReferencia(AtributoExame::REFERENCIA_SELECIONAVEL);
            }
            if ($resultado->fixo == 't') {
                $atributo->setTipoReferencia(AtributoExame::REFERENCIA_FIXA);
            }

            $resultadoAtributo = new ResultadoExameAtributo($resultado->la39_i_codigo);
            $resultadoAtributo->setAtributo($atributo);
            if ($resultado->la39_i_codigo !== null) {
                $resultadoAtributo->setValorAbsoluto($resultado->valor);
                $resultadoAtributo->setValorPercentual($resultado->valorpercentual);
                $resultadoAtributo->setTitulacao($resultado->la39_titulacao);

                if (!empty($resultado->faixautilizada)) {
                    $resultadoAtributo->setFaixaUtilizada(
                        new AtributoValorReferenciaNumerico($resultado->faixautilizada)
                    );
                }

                $this->aResultadoAtributos[$atributo->getCodigo()] = $resultadoAtributo;

                if ($atributo->getFormula() !== '') {
                    $resultadoAtributo = $this->executarCalculoFormula($atributo);
                }
            }

            $resultados[] = $resultadoAtributo;
        }

        return $resultados;
    }

    /**
     * Retorna os resultados dos atributos
     * @param integer $iCodigoResultado código do resultado de um exame
     * @return ResultadoExameAtributo[]
     */
    private function buscarResultadosAtributo($iCodigoResultado)
    {
        $aResultados = array();

        $sCampos = "la39_i_atributo,";
        $sCampos .= "la39_i_codigo,";
        $sCampos .= "la39_titulacao,";
        $sCampos .= "case when la41_f_valor is not null then cast(la41_f_valor as varchar)";
        $sCampos .= "     when la40_i_valorrefsel is not null then cast(la40_i_valorrefsel as varchar)";
        $sCampos .= "     when la40_c_valor <> '' then la40_c_valor  else '' end as valor,";
        $sCampos .= "la41_valorpercentual as valorpercentual,";
        $sCampos .= "la41_faixaescolhida as faixautilizada";
        $sWhere = "la39_i_resultado = {$iCodigoResultado}";

        $oDaoResultaoItem = new cl_lab_resultadoitem();
        $sSqlResultadoAtributos = $oDaoResultaoItem->sql_query_resultado_valores(null, $sCampos, null, $sWhere);
        $rsResultadoAtributo = $oDaoResultaoItem->sql_record($sSqlResultadoAtributos);

        if ($rsResultadoAtributo && $oDaoResultaoItem->numrows > 0) {
            for ($iItem = 0; $iItem < $oDaoResultaoItem->numrows; $iItem++) {
                $oDadosResultado = db_utils::fieldsMemory($rsResultadoAtributo, $iItem);
                $oResultadoAtributo = new ResultadoExameAtributo($oDadosResultado->la39_i_codigo);
                $oResultadoAtributo->setAtributo(
                    AtributoExameRepository::getByCodigo($oDadosResultado->la39_i_atributo)
                );
                $oResultadoAtributo->setValorAbsoluto($oDadosResultado->valor);
                $oResultadoAtributo->setValorPercentual($oDadosResultado->valorpercentual);
                $oResultadoAtributo->setTitulacao($oDadosResultado->la39_titulacao);

                if (!empty($oDadosResultado->faixautilizada)) {
                    $oResultadoAtributo->setFaixaUtilizada(
                        new AtributoValorReferenciaNumerico($oDadosResultado->faixautilizada)
                    );
                }

                $aResultados[$oResultadoAtributo->getAtributo()->getCodigo()] = $oResultadoAtributo;
            }
        }

        return $aResultados;
    }

    /**
     * Define o Diagnostico do exame
     * @param string $sConsideracao
     */
    public function setConsideracao($sConsideracao)
    {
        $this->sConsideracao = $sConsideracao;
    }

    /**
     * Salva os dados do resultado para o exame
     *
     */
    public function salvar()
    {
        $oDaoResultadoExame = new cl_lab_resultado();
        $oDaoResultadoExame->la52_diagnostico = $this->getConsideracao();

        if (empty($this->iResultado)) {
            $oDaoResultadoExame->la52_c_hora = db_hora();
            $oDaoResultadoExame->la52_d_data = date("Y-m-d", db_getsession("DB_datausu"));
            $oDaoResultadoExame->la52_i_requiitem = $this->oRequisicao->getCodigo();
            $oDaoResultadoExame->la52_i_usuario = db_getsession("DB_id_usuario");
            $oDaoResultadoExame->la52_t_motivo = '';
            $oDaoResultadoExame->incluir(null);
            $this->iResultado = $oDaoResultadoExame->la52_i_codigo;
            $this->oRequisicao->setSituacao(RequisicaoExame::LANCADO);
            $this->oRequisicao->salvar();
        } else {
            $oDaoResultadoExame->la52_i_codigo = $this->iResultado;
            $oDaoResultadoExame->alterar($this->iResultado);
        }

        if ($oDaoResultadoExame->erro_status == 0) {
            throw new BusinessException("Erro ao salvar do exame");
        }

        foreach ($this->getResultadoDosAtributos() as $oResultadosAtributos) {
            $oResultadosAtributos->salvar($this->iResultado);
        }
    }

    /**
     * @return string
     */
    public function getConsideracao()
    {
        return $this->sConsideracao;
    }

    /**
     * Retorna o resultado do Atributo Informado
     * @param AtributoExame $oAtributo
     * @return null|ResultadoExameAtributo
     * @throws Exception
     */
    public function getValorDoAtributoResultadoAnterior(AtributoExame $oAtributo)
    {
        $aAtributos = $this->getResultadoAnterior();
        if (isset($aAtributos[$oAtributo->getCodigo()])) {
            return $aAtributos[$oAtributo->getCodigo()];
        }

        return null;
    }

    /**
     * Retorna os resultados anteriores
     * @return array|ResultadoExameAtributo[]
     * @throws Exception
     */
    public function getResultadoAnterior()
    {
        if (!empty($this->aResultadoDoExameAnterior)) {
            return $this->aResultadoDoExameAnterior;
        }

        if (empty($this->oData)) {
            return null;
        }

        $oDaoResultadoExame = new cl_lab_resultado();

        $sCampos = "la52_i_codigo, la21_i_codigo ";
        $sCampos .= ",(la52_d_data||' '||la52_c_hora)::timestamp as time";
        $sWhere = "     la08_i_codigo = " . $this->oRequisicao->getExame()->getCodigo();
        $sWhere .= " and la22_i_cgs = " . $this->oRequisicao->getSolicitante()->getCodigo();
        $sWhere .= " and la21_i_requisicao < " . $this->oRequisicao->getCodigoRequisicao();
        $sWhere .= " and la52_d_data <= '{$this->oData->getDate()}'";
        $sWhere .= " and trim(la21_c_situacao) in ('70 - Entregue', '60 - Conferido') ";
        $sOrdem = " time desc limit 1";
        $sSql = $oDaoResultadoExame->sql_query_exames(null, $sCampos, $sOrdem, $sWhere);
        $rs = db_query($sSql);

        if (!$rs) {
            throw new Exception(_M(self::FONTE_MSG . "erro_buscar_resultado_anterior"));
        }

        if (pg_num_rows($rs) == 0) {
            return array();
        }

        $oDados = db_utils::fieldsMemory($rs, 0);

        $this->oRequisicaoAnterior = new \RequisicaoExame($oDados->la21_i_codigo);
        $this->aResultadoDoExameAnterior = $this->buscarResultadosAtributo($oDados->la52_i_codigo);

        return $this->aResultadoDoExameAnterior;
    }

    /**
     * Retorna a data da requisição anterior
     * @return DBDate|null
     */
    public function getDataResultadoAnterior()
    {
        if (is_null($this->oRequisicaoAnterior)) {
            return null;
        }

        return $this->oRequisicaoAnterior->getData();
    }

    /**
     * @param AtributoExame $atributoExame
     * @return ResultadoExameAtributo
     * @throws Exception
     */
    private function executarCalculoFormula(AtributoExame $atributoExame)
    {
        try {
            if (!array_key_exists($atributoExame->getCodigo(), $this->aResultadoAtributos)) {
                $resultadoExameAtributo = new ResultadoExameAtributo();
                $resultadoExameAtributo->setAtributo($atributoExame);
            } else {
                $resultadoExameAtributo = $this->aResultadoAtributos[$atributoExame->getCodigo()];
            }

            $formula = new Formula($atributoExame->getFormula(), $this->oRequisicao, $atributoExame, true);
            $calculo = new Calculo($formula);
            $resultado = $calculo->executar();
            
            if($resultado == 'NAN' || $resultado == 'INF' || $resultado == ''){
                $resultado = "0";
            }
            
            $atributoValorReferencia = $atributoExame->getValoresDeReferenciaParaExame($this->oRequisicao);

            $resultadoExameAtributo->setValorAbsoluto($resultado);

            if ($atributoValorReferencia instanceof \AtributoValorReferenciaNumerico
                && $atributoValorReferencia->getTipoCalculo() == 2) {
                $resultadoExameAtributo->setValorAbsoluto('');
                $resultadoExameAtributo->setValorPercentual($resultado);
            }

            $this->aResultadoAtributos[$atributoExame->getCodigo()] = $resultadoExameAtributo;

            return $resultadoExameAtributo;
        } catch (Exception $erro) {
            $mensagem = $erro->getMessage();

            if ($erro instanceof \FormulaInterpreter\Parser\ParserException) {
                $mensagem = "Erro ao calcular o resultado do atributo. Verifique a fórmula do atributo";
                $mensagem .= " '{$atributoExame->getNome()}'.";
            }

            if ($erro instanceof \FormulaInterpreter\Exception\UnknownVariableException) {
                $mensagem = "Erro ao calcular o resultado do atributo. Verifique a fórmula do atributo";
                $mensagem .= " '{$atributoExame->getNome()}'.";
            }

            throw new Exception($mensagem);
        }
    }

    /**
     * Retorna os resultados anteriores do Atributo Informado e a respectiva requisição laboratorial
     * @param AtributoExame $oAtributo
     * @param DBDate $dataRequisicaoAtual
     * @param Exame $exame
     * @param array $idsExames
     * @param int $numeroDeResultados
     * @return array|null
     * @throws Exception
     */
    public function getResultadosAnteriores(
        AtributoExame $oAtributo,
        $dataRequisicaoAtual,
        Exame $exame,
        $idsExames,
        $numeroDeResultados = 5
    ) {
        $this->setAtributoBuscandoHistorico($oAtributo);

        $historicosDeResultados = $this->getHistoricoDeResultados(
            $numeroDeResultados,
            $dataRequisicaoAtual,
            $idsExames
        );
        $resultadosAnteriores = [];

        if (!$historicosDeResultados) {
            return null;
        }

        $idsAtributosParaFormula = [];
        $idsAtributosParaFormula[] = $oAtributo->getCodigo();
        $resultadosParaFormulas = [];

        foreach ($historicosDeResultados as $arrayAtributos) {
            foreach (self::getResultadosAtributosExame() as $resultado) {
                if ($resultado->la39_i_resultado === $arrayAtributos['resultado']) {
                    $idsAtributosParaFormula[] = $resultado->la39_i_atributo;
                }
            }
        }

        $atributosParaFormula = AtributoExameRepository::getVariosAtributos($idsAtributosParaFormula);

        foreach ($historicosDeResultados as $arrayAtributos) {
            if ($oAtributo->getFormula() !== '') {
                foreach (self::getResultadosAtributosExame() as $resultado) {
                    if ($resultado->la39_i_resultado === $arrayAtributos['resultado']) {
                        $oResultadoAtributo = new ResultadoExameAtributo($resultado->la39_i_codigo);
                        $oResultadoAtributo->setAtributo($atributosParaFormula[$resultado->la39_i_atributo]);
                        $oResultadoAtributo->setValorAbsoluto($resultado->valor);
                        $oResultadoAtributo->setValorPercentual($resultado->valorpercentual);
                        $oResultadoAtributo->setTitulacao($resultado->la39_titulacao);

//            if (!empty($resultado->faixautilizada)) {
//                $oResultadoAtributo->setFaixaUtilizada(new AtributoValorReferenciaNumerico($resultado->faixautilizada));
//            }

                        $resultadosParaFormulas[$oResultadoAtributo->getAtributo()->getCodigo()] = $oResultadoAtributo;
                    }
                }

                $requiItem = $this->getRequiItem($arrayAtributos['requisicao']->getCodigo(), $exame->getCodigo());

                $resultadosAnteriores['requisicao'][] = $arrayAtributos['requisicao'];
                $resultadosAnteriores['atributo'][] = $this->resultadoAnteriorFormula(
                    $oAtributo,
                    $requiItem,
                    $resultadosParaFormulas
                );

                $resultadosParaFormulas = [];
            }

            if (!empty($arrayAtributos[$oAtributo->getCodigo()]) && $oAtributo->getFormula() === '') {
                $resultadosAnteriores['requisicao'][] = $arrayAtributos['requisicao'];
                $resultadosAnteriores['atributo'][] = $arrayAtributos[$oAtributo->getCodigo()];
            }
        }

        if (empty($resultadosAnteriores)) {
            return null;
        }

        return $resultadosAnteriores;
    }

    /**
     * Retorna um array contendo resultados anteriores do atributo informado e a respectiva requisição laboratorial.
     * @param int $quantidadeResultados
     * @param DBDate $dataRequisicaoAtual
     * @param array $idsExames
     * @return array|null
     * @throws Exception
     */
    public function getHistoricoDeResultados($quantidaDeResultados, $dataRequisicaoAtual, $idsExames)
    {
        $resultadoAnterior = [];

        $this->getListaHistoricoResultados($quantidaDeResultados, $dataRequisicaoAtual, $idsExames);

        if (is_null(self::getResultados())) {
            return null;
        }

        $serviceRequisicao = new RequisicaoLaboratorialService(
            new RequisicaoLaboratorialRepository(new \cl_lab_requisicao())
        );

        foreach (self::getResultados() as $resultado) {
            if ($resultado->la08_i_codigo === $this->oRequisicao->getExame()->getCodigo()) {
                $requisicao = $serviceRequisicao->getRequisicao($resultado->la21_i_requisicao);

                $resultadoAnterior[$resultado->la21_i_requisicao] = $this->buscarResultadosAnterioresAtributo(
                    $resultado->la52_i_codigo
                );

                $resultadoAnterior[$resultado->la21_i_requisicao]['requisicao'] = $requisicao;
                $resultadoAnterior[$resultado->la21_i_requisicao]['resultado'] = $resultado->la52_i_codigo;
            }
        }

        return $resultadoAnterior;
    }

    /**
     * Retorna os resultados anteriores do atributo
     * @param int $iCodigoResultado
     * @param int $atributoId
     * @return ResultadoExameAtributo[]|array
     */
    private function buscarResultadosAnterioresAtributo($iCodigoResultado)
    {
        $aResultados = array();

        $this->getListaResultadosAtributos();

        /**
         * Caso tenhamos valores irá devolver para o respectivo resultado e atributo que queremos.
         */
        if (self::getResultadosAtributosExame()) {
            foreach (self::getResultadosAtributosExame() as $resultado) {
                if ($resultado->la39_i_resultado === $iCodigoResultado &&
                    $this->getAtributoBuscandoHistorico()->getCodigo() === $resultado->la39_i_atributo) {
                    $oResultadoAtributo = new ResultadoExameAtributo($resultado->la39_i_codigo);
                    $oResultadoAtributo->setAtributo($this->getAtributoBuscandoHistorico());
                    $oResultadoAtributo->setValorAbsoluto($resultado->valor);
                    $oResultadoAtributo->setValorPercentual($resultado->valorpercentual);
                    $oResultadoAtributo->setTitulacao($resultado->la39_titulacao);

//            if (!empty($resultado->faixautilizada)) {
//                $oResultadoAtributo->setFaixaUtilizada(new AtributoValorReferenciaNumerico($resultado->faixautilizada));
//            }

                    $aResultados[$oResultadoAtributo->getAtributo()->getCodigo()] = $oResultadoAtributo;
                }
            }
        }

        return $aResultados;
    }

    /**
     * @param AtributoExame|null $atributoBuscandoHistorico
     */
    private function setAtributoBuscandoHistorico($atributoBuscandoHistorico)
    {
        $this->atributoBuscandoHistorico = $atributoBuscandoHistorico;
    }

    /**
     * @return AtributoExame|null
     */
    private function getAtributoBuscandoHistorico()
    {
        return $this->atributoBuscandoHistorico;
    }

    /**
     * @return stdClass[]|null
     */
    public static function getResultados()
    {
        return self::$resultados;
    }

    /**
     * @param stdClass[]|null $resultados
     */
    public static function setResultados($resultados)
    {
        self::$resultados = $resultados;
    }

    /**
     * @return stdClass[]|array
     */
    public static function getResultadosAtributosExame()
    {
        return self::$resultadosAtributosExame;
    }

    /**
     * @param stdClass[] $resultadosAtributosExame
     */
    public static function setResultadosAtributosExame($resultadosAtributosExame)
    {
        self::$resultadosAtributosExame = $resultadosAtributosExame;
    }

    /**
     * @param AtributoExame $atributoExame
     * @param int $codRequiItem
     * @return ResultadoExameAtributo
     * @throws Exception
     */
    private function resultadoAnteriorFormula(AtributoExame $atributoExame, $codRequiItem, $resultadosAtributos)
    {
        try {
            $resultadoExameAtributo = new ResultadoExameAtributo();
            $resultadoExameAtributo->setAtributo($atributoExame);

            $formula = new Formula(
                $atributoExame->getFormula(),
                $codRequiItem,
                $atributoExame,
                true,
                $resultadosAtributos
            );
            $calculo = new Calculo($formula);
            $resultado = $calculo->executar(true);

            if($resultado == 'NAN' || $resultado == 'INF' || $resultado == ''){
                $resultado = "0";
            }

            //$atributoValorReferencia = $atributoExame->getValoresDeReferenciaParaExame($codRequiItem);

            $resultadoExameAtributo->setValorAbsoluto($resultado);

//            if ($atributoValorReferencia instanceof \AtributoValorReferenciaNumerico
//                && $atributoValorReferencia->getTipoCalculo() == 2) {
//                $resultadoExameAtributo->setValorAbsoluto('');
//                $resultadoExameAtributo->setValorPercentual($resultado);
//            }

            return $resultadoExameAtributo;
        } catch (Exception $erro) {
            $mensagem = $erro->getMessage();

            if ($erro instanceof \FormulaInterpreter\Parser\ParserException) {
                $mensagem = "Erro ao calcular o resultado anterior do atributo.";
                $mensagem .= " '{$atributoExame->getNome()}'.";
            }

            if ($erro instanceof \FormulaInterpreter\Exception\UnknownVariableException) {
                $mensagem = "Erro ao calcular o resultado anterior do atributo.";
                $mensagem .= " '{$atributoExame->getNome()}'.";
            }

            throw new Exception($mensagem);
        }
    }

    /**
     * @param int $codRequisicao
     * @param int $codExame
     * @return false|RequisicaoExame
     * @throws DBException
     */
    private function getRequiItem($codRequisicao, $codExame)
    {
        $requiItemRepository = new RequisicaoExameRepository(new cl_lab_requiitem());

        $campos = '
                    la21_i_codigo, la21_i_requisicao, la21_d_entrega, la21_d_data, la21_c_hora,
                    la21_i_setorexame, la21_i_emergencia, la21_c_situacao, la21_i_quantidade,
                    la21_observacao, la22_i_cgs
                    ';

        $sql = "select {$campos} from lab_requiitem
                        inner join lab_setorexame on la21_i_setorexame = la09_i_codigo
                        inner join lab_requisicao on la22_i_codigo = la21_i_requisicao
                        where la21_i_requisicao = {$codRequisicao}
                        and la09_i_exame = {$codExame};";

        return $requiItemRepository->getRequisicaoExame($sql);
    }

    /**
     * @param $quantidaDeResultados
     * @param $dataRequisicaoAtual
     * @param $idsExames
     * @return void|null
     */
    private function getListaHistoricoResultados($quantidaDeResultados, $dataRequisicaoAtual, $idsExames)
    {
        /**
         * irá buscar todos os resultados anteriores uma única vez por requisição, levando em conta todos os exames
         * presentes em tal requisição
         */
        if (self::$atualizarResultados) {
            $codigosExames = implode(',', $idsExames);

            if (empty($this->oData)) {
                return null;
            }

            $dataInicial = date('Y-m-d', strtotime('-24 month', $dataRequisicaoAtual->getTimeStamp()));

            $sql = "
                    SELECT la52_i_codigo, la21_i_requisicao, TIME, la08_i_codigo
                    FROM (
                        SELECT la52_i_codigo, la21_i_requisicao, (la52_d_data||' '||la52_c_hora)::timestamp AS TIME, la08_i_codigo,
                               ROW_NUMBER() OVER (PARTITION BY la08_i_codigo ORDER BY (la52_d_data||' '||la52_c_hora)::timestamp DESC) AS rn
                        FROM lab_resultado
                        JOIN lab_requiitem ON lab_requiitem.la21_i_codigo = lab_resultado.la52_i_requiitem
                        JOIN lab_requisicao ON lab_requisicao.la22_i_codigo = lab_requiitem.la21_i_requisicao
                        JOIN lab_setorexame ON lab_setorexame.la09_i_codigo = lab_requiitem.la21_i_setorexame
                        JOIN lab_exame ON lab_exame.la08_i_codigo = lab_setorexame.la09_i_exame
                        WHERE la08_i_codigo in ({$codigosExames})
                          AND la22_i_cgs = {$this->oRequisicao->getSolicitante()->getCodigo()}
                          AND la21_i_requisicao < {$this->oRequisicao->getCodigoRequisicao()}
                          AND la52_d_data <= '{$this->oData->getDate()}'
                          AND la52_d_data >= '{$dataInicial}'
                          AND trim(la21_c_situacao) in ('70 - Entregue', '60 - Conferido')
                        ORDER BY TIME DESC
                    ) AS t
                    WHERE rn <= {$quantidaDeResultados} order by la08_i_codigo
        ";

            $resultadoSql = db_query($sql);

            if (pg_num_rows($resultadoSql) == 0) {
                self::setResultados(null);
                return null;
            }

            $dados = db_utils::getCollectionByRecord($resultadoSql);

            self::setResultados($dados);
        }
    }

    /**
     * @return void
     */
    private function getListaResultadosAtributos()
    {
        /**
         * irá buscar todos os valores para os resultados anteriores encontrados uma única vez por requisição
         */
        if (self::$atualizarResultados) {
            $codigosResultados = [];

            foreach (self::getResultados() as $resultado) {
                $codigosResultados[] = $resultado->la52_i_codigo;
            }

            $codigosResultadosTxt = implode(',', $codigosResultados);

            $sCampos = "la39_i_atributo,";
            $sCampos .= "la39_i_codigo,";
            $sCampos .= "la39_titulacao,";
            $sCampos .= "la39_i_resultado,";
            $sCampos .= "case when la41_f_valor is not null then cast(la41_f_valor as varchar)";
            $sCampos .= "     when la40_i_valorrefsel is not null then cast(la28_c_descr as varchar)";
            $sCampos .= "     when la40_c_valor <> '' then la40_c_valor  else '' end as valor,";
            $sCampos .= "la41_valorpercentual as valorpercentual,";
            $sCampos .= "la41_faixaescolhida as faixautilizada";
            $sWhere = "la39_i_resultado in ({$codigosResultadosTxt})";

            $sWhere .= " and (la41_f_valor is not null";
            $sWhere .= " or la40_i_valorrefsel is not null";
            $sWhere .= " or la40_c_valor <> '')";
            $order = "la39_i_atributo ASC,";
            $order .= " la39_i_codigo DESC";

            $dao = new cl_lab_resultadoitem();
            $sql = $dao->sql_query_resultado_valores(null, $sCampos, $order, $sWhere);
            $resultadoSql = db_query($sql);

            if ($resultadoSql && pg_num_rows($resultadoSql) > 0) {
                $resultado = db_utils::getCollectionByRecord($resultadoSql);
                self::setResultadosAtributosExame($resultado);
            }

            self::$atualizarResultados = false;
        }
    }
}
