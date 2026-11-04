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

/**
 * Classe para montar a estrutura do relatorio de histórico/certificado escolar
 *
 * @package    educacao
 * @subpackage relatorio
 * @author     andrio.costa <andrio.costa@dbseller.com.br>
 * @version    $Revision: 1.44 $
 */

class RelatorioHistoricoEscolar
{
    const MENSAGEM = "educacao.escola.RelatorioHistoricoEscolar.";
    const CAMINHO_BRASAO_REPUBLICA = "imagens/brasaohistoricoescolar.jpeg";

    const TIPO_BRASAO_REPUBLICA = 1;
    const TIPO_BRASAO_MUNICIPIO = 2;

    /**
     * Define se devemos incluir as etapas reprovadas na estrutura dos dados
     * A  => Etapas APROVADAS
     * AR => Etapas APROVADAS e REPROVADAS
     * U  => Listar Último Registro
     *
     * @var string
     */
    protected $sTipoRegistro = 'A';

    /**
     * Estrutura com os dados necessarios para impressao do histórico
     *
     * @var array
     */
    protected $aDadosOrganizados = array();

    /**
     * Indica que o aluno teve ao menos uma disciplina com aprovação parcial
     *
     * @var boolean
     */
    protected $lAlunoTeveAprovacaoComProgressao = false;

    /**
     * Parâmetros de configuração do relatório
     *
     * @var stdClass
     */
    protected $oParametros;

    /**
     * Armazena as observações do histórico do aluno, indexando o array pelo curso
     *
     * @var array
     */
    protected $aObservacaoHistorico = array();

    /**
     * Instancia de Aluno
     *
     * @var Aluno
     */
    protected $oAluno;

    /**
     * Instãncia de Escola
     *
     * @var Escola
     */
    protected $oEscola;

    /**
     * Valida se etapas reclassificadas devem ser exibidas no relatório
     *
     * @var boolean
     */
    protected $lExibirReclassificacao = true;

    /**
     * Controla se deve ser apresentados todos os cursos ou somente os concluídos
     *
     * @var boolean
     */
    protected $lExibirSomenteCursosConcluidos = false;

    /**
     * Curso que será apresentado no relatório.
     *
     * @var Curso
     */
    protected $oCurso = null;

    /**
     * Carrega informações sobre as etapas posteriores
     *
     * @var array
     */
    protected $aEtapasPosterior = array();

    /**
     * Carrega se houver informacoes sobre as etapas anteriores
     *
     * @var array
     */
    protected $aEtapasAnterior = array();
    /**
     * @var integer
     */
    protected $iAnoLimite;

    /**
     * @var string
     */
    protected $sDataEmissao;

    /*PLUGIN DIARIO PROGRESSAO - INICIALIZANDO ARRAY COM AS OBSERVAÇÕES - NÃO APAGAR*/

    /**
     * Construtor da classe
     *
     * @param Aluno   $oAluno
     * @param Escola  $oEscola
     * @param integer $iTipoRelatorio
     * @param boolean $lExibirReclassificacao
     */
    public function __construct(Aluno $oAluno, Escola $oEscola, $iTipoRelatorio, $lExibirReclassificacao)
    {
        $this->oAluno = $oAluno;
        $this->oEscola = $oEscola;
        $this->parametrosRelatorio($iTipoRelatorio);
        $this->lExibirReclassificacao = $lExibirReclassificacao;
        $this->lExibirSomenteCursosConcluidos = false;
    }

    /**
     * Define o tipo de registro
     *
     * @param string $sTipoRegistro
     */
    public function setTipoRegistro($sTipoRegistro)
    {
        $this->sTipoRegistro = $sTipoRegistro;
    }

    /**
     * @param integer $iAnoLimite
     */
    public function setAnoLimite($iAnoLimite)
    {
        $this->iAnoLimite = $iAnoLimite;
    }

    /**
     * @param string $sDataEmissao
     */
    public function setDataEmisao($sDataEmissao)
    {
        $this->sDataEmissao = $sDataEmissao;
    }

    /**
     * Retorna um estrutura organizada com todo o histórico acadêmico do aluno
     *
     * @return stdClass[]:
     * @throws DBException|ParameterException|Exception
     */
    public function montarEstruturaDeDados()
    {
        /**
         * Evita o sobrecarregamento dos dados
         */
        if (count($this->aDadosOrganizados) > 0) {
            return $this->aDadosOrganizados;
        }

        $iUltimoAnoCursado = $this->getUltimoAnoCursado();

        $aHistoricosAluno = HistoricoAlunoRepository::getHistoricosPorAluno($this->oAluno);

        $aMatriculas = MatriculaRepository::getTodasMatriculasAluno($this->oAluno);

        $oUltimaEtapaHistoricoCursada = null;
        $aCursosConcluidosImpressao = $this->getCursosConcluidos();

        $oHistoricoCurso = $aHistoricosAluno[0];
        $oPrimeiraEtapaHistorico = isset($oHistoricoCurso->getEtapas()[0])
        ? $oHistoricoCurso->getEtapas()[0]
        : null;

        if (!is_null($oPrimeiraEtapaHistorico)) {
            $this->buscaEtapasAnteriores($oPrimeiraEtapaHistorico->getEtapa());
        }

        foreach ($aHistoricosAluno as $oHistoricoCurso) {
            if (!empty($this->oCurso) && !$this->validaCursos($oHistoricoCurso->getCodigoCurso())) {
                continue;
            }

            if ($this->lExibirSomenteCursosConcluidos && $oHistoricoCurso->getAnoConclusao() == ''
                && !in_array($oHistoricoCurso->getCodigoCurso(), $aCursosConcluidosImpressao)
            ) {
                continue;
            }

            /**
             * Armazena as observações do historicos do aluno
             */
            if ($oHistoricoCurso->getObservacoes() != "") {
                $this->aObservacaoHistorico[$oHistoricoCurso->getCodigoCurso()] = $oHistoricoCurso->getObservacoes();
            }

            foreach ($oHistoricoCurso->getEtapas() as $oEtapaCursada) {
                if (!$this->lExibirReclassificacao && $oEtapaCursada->getSituacaoEtapa() == "RECLASSIFICADO") {
                    continue;
                }

                if ($this->sTipoRegistro == "A" && $oEtapaCursada->getResultadoAno() == 'R') {
                    continue;
                }

                /**
                 * A Etapa do ultimo ano cursado sempre deve ser mostrada.
                 */
                if (($this->sTipoRegistro == "U") 
                    && ($oEtapaCursada->getAnoCurso() < $iUltimoAnoCursado) 
                    && ($oEtapaCursada->getResultadoAno() == 'R')
                ) {
                    continue;
                }

                if (!empty($this->iAnoLimite) && $oEtapaCursada->getAnoCurso() > $this->iAnoLimite) {
                    continue;
                }

                $oUltimaEtapaHistoricoCursada = $oEtapaCursada;

                $iEnsino = $oEtapaCursada->getEtapa()->getEnsino()->getCodigo();
                $ensinos[$iEnsino] = $oEtapaCursada->getEtapa()->getEnsino();

                $aTermos = DBEducacaoTermo::getTermoEncerramentoDoEnsino($iEnsino, $oEtapaCursada->getAnoCurso());

                $oDadoEtapa = new stdClass();
                $oDadoEtapa->ensino = $oEtapaCursada->getEtapa()->getEnsino();
                $oDadoEtapa->iEtapa = $oEtapaCursada->getEtapa()->getCodigo();
                $oDadoEtapa->sEtapa = $oEtapaCursada->getEtapa()->getNome();
                $oDadoEtapa->ordem = $oEtapaCursada->getEtapa()->getOrdem();
                $oDadoEtapa->iAno = $oEtapaCursada->getAnoCurso();
                $oDadoEtapa->iDiasLetivos = $oEtapaCursada->getDiasLetivos();
                $oDadoEtapa->iCargaHoraria = $oEtapaCursada->getCargaHoraria();
                $oDadoEtapa->resultadoEtapaAno = $oEtapaCursada->getResultadoAno();

                $oDadoEtapa->sResultado = $this->termoResultadoFinalEtapa(
                    $oEtapaCursada->getResultadoAno(),
                    $oEtapaCursada->getSituacaoEtapa(),
                    $oEtapaCursada->getTermoFinal(),
                    $aTermos,
                    $oEtapaCursada instanceof HistoricoEtapaForaRede
                );

                $oDadoEtapa->sTurma = $oEtapaCursada->getTurma();
                $oDadoEtapa->periodo = $oEtapaCursada->getPeriodoReferencia();
                $oDadoEtapa->iEscola = $oEtapaCursada->getEscola()->getCodigo();
                $oDadoEtapa->sEscola = $oEtapaCursada->getEscola()->getNome();
                $oDadoEtapa->sMunicipio = $oEtapaCursada->getEscola()->getMunicipio();
                if ($this->oParametros->exibirdistrito) {
                    $oDadoEtapa->sMunicipio .= $oEtapaCursada->getEscola()->getDistrito();
                }
                $oDadoEtapa->sUF = $oEtapaCursada->getEscola()->getUf();

                $oDadoEtapa->nPercentualFalta = "";
                $oDadoEtapa->nPercentualFalta = $oEtapaCursada->getPercentualFrequencia();

                /**
                 * Quando a etapa que está sendo percorrida é igual há alguma etapa das matrículas do aluno:
                 * Verifica qual é a forma de calculo da frequência (Por Disciplina/ Carga Horária Total) e se existe
                 * ao menos alguma disciplina do diário que possui Reclassifacação por Baixa Frequência e substitui
                 * o percentual de  frequência por '--'.
                 */
                if ($oEtapaCursada->isLancamentoAutomatico()) {
                    foreach ($aMatriculas as $oMatricula) {
                        if ($oMatricula->getEtapaDeOrigem()->getCodigo() == $oDadoEtapa->iEtapa
                            && $oMatricula->getTurma()->getCalendario()->getAnoExecucao() == $oDadoEtapa->iAno
                        ) {
                            db_inicio_transacao();
                            $oDiarioClasse = $oMatricula->getDiarioDeClasse();
                            db_fim_transacao();

                            $iFormaCalculo = $oDiarioClasse->getProcedimentoDeAvaliacao()->getFormaCalculoFrequencia();
                            $lReclassificadoBaixaFrequencia = $oDiarioClasse->reclassificadoPorBaixaFrequencia();

                            if ($iFormaCalculo == 2 && $lReclassificadoBaixaFrequencia) {
                                $oDadoEtapa->nPercentualFalta = '--';
                            }

                            /*PLUGIN DIARIO PROGRESSAO - OBSERVACAO HISTORICO ALUNO EVADIDO - NÃO APAGAR*/
                        }
                    }
                }
                $oDadoEtapa->mMinimoAprovacao = $oEtapaCursada->getMininoParaAprovacao();
                $oDadoEtapa->areasConhecimento = [];
                $oDadoEtapa->aDisciplinas = [];

                if (count($oEtapaCursada->getAreasConhecimento()) != 0) {
                    foreach ($oEtapaCursada->getAreasConhecimento() as $areaHistorico) {
                        $areaConhecimento = $areaHistorico->getAreaConhecimento();
                        $codigoArea = $areaConhecimento->getCodigo();
                        $oDadoEtapa->areasConhecimento[$codigoArea] = (object)[
                                "codigo" => $codigoArea,
                                "descricao" => $areaConhecimento->getDescricao(),
                                "resultadoObtido" => $areaHistorico->getResultadoObtido(),
                                "resultadoFinal" => $areaHistorico->getResultadoFinal(),
                                "disciplinasArea" => []
                            ];
                        if (empty($areaHistorico->getDisciplinas())) {
                            unset($oDadoEtapa->areasConhecimento[$codigoArea]);
                        }

                        // Percorre as disciplinas para pegar a Situação do aluno, se for amparado, muda no resultadoObtido
                        foreach ($areaHistorico->getDisciplinas() as $oDisciplinaCursada) {
                            if ($oDisciplinaCursada->getSituacaoDisciplina() == "AMPARADO") {
                                foreach ($oDadoEtapa->areasConhecimento as $areas) {
                                    $areas->resultadoObtido = $oDisciplinaCursada->getSituacaoDisciplina();
                                }
                            }

                            $oDisciplina = $this->criarDisciplinaVO($oDisciplinaCursada, $oEtapaCursada);
                            $oDadoEtapa->areasConhecimento[$codigoArea]
                                ->disciplinasArea[$oDisciplina->iCadDisciplina] = $oDisciplina;
                        }

                    }
                }

                foreach ($oEtapaCursada->getDisciplinas() as $oDisciplinaCursada) {
                    if (empty($oDisciplinaCursada->getTipoBase())) {
                        continue;
                    }
                    $oDisciplina = $this->criarDisciplinaVO($oDisciplinaCursada, $oEtapaCursada);
                    $oDadoEtapa->aDisciplinas[$oDisciplina->iCadDisciplina] = $oDisciplina;
                }
                $sIndex = "{$oEtapaCursada->getAnoCurso()}#{$oEtapaCursada->getEtapa()->getOrdem()}";
                $sIndex .= "#{$oEtapaCursada->getEtapa()->getCodigo()}";

                $this->aDadosOrganizados[$sIndex] = $oDadoEtapa;
            }
        }
        $this->buscaEtapasPosteriores($oUltimaEtapaHistoricoCursada);


        if (count($this->aDadosOrganizados) === 0) {
            throw new Exception('No ano de emissão informado, o aluno não possui etapas aprovada.');
        }
        ksort($this->aDadosOrganizados);
        return $this->aDadosOrganizados;
    }

    /**
     * Busca as etapas posteriores do aluno a partir do último registro no histórico ou da matrícula caso o aluno
     * possua matrícula válida (Ativa e Não Concluída) de acordo com a base do curso.
     *
     * @param  HistoricoEtapa $oUltimaEtapaHistoricoCursada Última etapa cursada de acordo com o histórico
     * @throws DBException
     */
    private function buscaEtapasPosteriores($oUltimaEtapaHistoricoCursada)
    {
        $lMatriculaValida = false;
        $oUltimaEtapa = null;

        if (!empty($oUltimaEtapaHistoricoCursada)) {
            $oUltimaEtapa = $oUltimaEtapaHistoricoCursada->getEtapa();
        }

        $oUltimaMatricula = MatriculaRepository::getUltimaMatriculaAluno($this->oAluno);
        if (empty($oUltimaEtapa) && !empty($oUltimaMatricula) && $oUltimaMatricula->isAtiva() && !$oUltimaMatricula->isConcluida() 
            && $oUltimaMatricula->getSituacao() == 'MATRICULADO'
        ) {
            $oUltimaEtapa = $oUltimaMatricula->getEtapaDeOrigem();
        }
        if (empty($oUltimaEtapa)) {
            return;
        }

        $aEtapasEnsino = EtapaRepository::getEtapasEnsino($oUltimaEtapa->getEnsino());
        foreach ($aEtapasEnsino as $oEtapaEnsino) {
            $oDadoEtapa = $this->criarObjetoEtapa($oEtapaEnsino);
            $this->aEtapasPosterior[$oDadoEtapa->iEtapa] = $oDadoEtapa;
        }
    }


    /**
     * Busca o termo a ser apresentado no Resultado final da etapa
     *
     * @param  string $sResultadoFinal
     * @param  string $sSituacao
     * @param  string $sTermoFinalEtapa
     * @return mixed|string|null
     */
    private function termoResultadoFinalEtapa($sResultadoFinal, $sSituacao = null, $sTermoFinalEtapa = null, $aTermos, $fora = false)
    {
        $sTermoFinal = $this->termoFinal($sResultadoFinal, false, $aTermos);
        /**
         * Caso tenha sido informado um termo final, este substituira o resultado final
         */
        if (!empty($sTermoFinalEtapa)) {
            $sTermoFinal = $sTermoFinalEtapa;
        }

        /**
         * Caso o histórico tenha sido lancado como transferencia, o resultado recebe TR
         */
        if (!empty($sSituacao) && trim($sSituacao) == 'TRANSFERIDO') {
            $sTermoFinal = $fora ? 'TF' : 'TR';
        }

        /**
         * Situações que devem ser apresentadas no Resultado
         */
        $aSituacoes = array(
            'AVANÇADO' => 'AVAN',
            'CANCELADO' => 'CANC',
            'EVADIDO' => 'EVAD',
            'ABANDONO ESCOLAR' => 'AE',
            'FALECIDO' => 'FALEC',
        );

        if (array_key_exists($sSituacao, $aSituacoes)) {
            $sTermoFinal = $aSituacoes[$sSituacao];
        }

        return $sTermoFinal;
    }

    /**
     * Busca o termo a ser apresentado no Resultado final da disciplina
     *
     * @param  string $sResultadoFinal
     * @param  string $sSituacao
     * @param  string $sTermoFinalDisciplina
     * @return string|null
     */
    private function termoResultadoFinalDisciplina($sResultadoFinal, $sSituacao = null, $sTermoFinalDisciplina = null)
    {
        $sTermoFinal = 'REP';
        if (!empty($sSituacao) && trim($sSituacao) != 'CONCLUÍDO') {
            $sTermoFinal = 'APR';
        }

        $sTermoFinal = $this->termoFinal($sResultadoFinal, true);

        // Caso a situacao seja 'NÃO OPTANTE' ou 'AMPARADO', e nao exista um termo final, nao apresentamos o
        // resultado final (RF)
        if (!empty($sSituacao) 
            && (trim($sSituacao) == 'NÃO OPTANTE' || trim($sSituacao) == 'AMPARADO')
        ) {
            $sTermoFinal = '';
        }

        if (!empty($sTermoFinalDisciplina)) {
            $sTermoFinal = $sTermoFinalDisciplina;
        }

        return $sTermoFinal;
    }

    /**
     * Retorna o termo final de acordo com o Resultado
     *
     * @param  string $sResultadoFinal
     * @return string
     */
    private function termoFinal($sResultadoFinal, $lDisciplina = false, $aTermos = null)
    {
        $sSituacaoFinal = '';
        switch (trim($sResultadoFinal)) {
        case 'A':
            $sSituacaoFinal = 'APR';
            if (!empty($aTermos)) {
                $sSituacaoFinal = $this->getTermoByReferencia($aTermos, $sResultadoFinal);
            }
            break;

        case 'D':
            $sSituacaoFinal = 'AP/DP';
            if ($lDisciplina) {
                $sSituacaoFinal = 'APR*';
                $this->lAlunoTeveAprovacaoComProgressao = true;
            }
            break;

        case 'R':
            $sSituacaoFinal = 'REP';
            if (!empty($aTermos)) {
                foreach ($aTermos as $oTermo) {
                    if ($oTermo->sReferencia == 'R') {
                        $sSituacaoFinal = $this->getTermoByReferencia($aTermos, $sResultadoFinal);
                        ;
                        break;
                    }
                }
            }
            break;
        }

        return $sSituacaoFinal;
    }

    private function getTermoByReferencia($aTermos, $sReferencia)
    {
        foreach ($aTermos as $oTermo) {
            if ($oTermo->sReferencia === $sReferencia) {
                return $oTermo->sAbreviatura;
            }
        }
    }

    /**
     * Busca os parâmetros para relatório impresso
     *
     * @param integer $iTipoRelatorio
     */
    private function parametrosRelatorio($iTipoRelatorio)
    {
        $sCampos = " ed217_t_cabecalho        as cabecalho,                  ";
        $sCampos .= " ed217_t_rodape           as rodape,                     ";
        $sCampos .= " ed217_t_obs              as observacao,                 ";
        $sCampos .= " ed217_orientacao         as orientacao,                 ";
        $sCampos .= " ed217_exibeturma         as exibe_turma,                ";
        $sCampos .= " ed217_exibecargahoraria  as exibe_percentual_frequencia,";
        $sCampos .= " ed217_exibe_obs_diario  as exibe_obs_diario,";
        $sCampos .= " ed217_exibirmantenedora as exibirmantenedora,";
        $sCampos .= " ed217_exibirdistrito  as exibirdistrito,";
        $sCampos .= " ed217_exibirperiodo  as exibirperiodo,";
        $sCampos .= " ed217_exibir_etapa_obs  as exibir_etapa_obs,";
        $sCampos .= " ed217_exibircertidao as exibircertidao,";
        $sCampos .= " ed217_exibiridentidade as exibiridentidade,";
        $sCampos .= " CASE                                                    ";
        $sCampos .= "   WHEN ed217_gradenotas = 1 THEN '6'                    ";
        $sCampos .= "   WHEN ed217_gradenotas = 2 THEN '8'                    ";
        $sCampos .= "   WHEN ed217_gradenotas = 3 THEN '10'                   ";
        $sCampos .= "   WHEN ed217_gradenotas = 4 THEN '12'                   ";
        $sCampos .= " END AS fonte_grade_nota,                                ";
        $sCampos .= " CASE                                                    ";
        $sCampos .= "   WHEN ed217_gradeetapas = 1 THEN '6'                   ";
        $sCampos .= "   WHEN ed217_gradeetapas = 2 THEN '8'                   ";
        $sCampos .= "   WHEN ed217_gradeetapas = 3 THEN '10'                  ";
        $sCampos .= "   WHEN ed217_gradeetapas = 4 THEN '12'                  ";
        $sCampos .= " END AS fonte_grade_etapa,                               ";
        $sCampos .= " CASE                                                    ";
        $sCampos .= "   WHEN ed217_observacao = 1 THEN '6'                    ";
        $sCampos .= "   WHEN ed217_observacao = 2 THEN '8'                    ";
        $sCampos .= "   WHEN ed217_observacao = 3 THEN '10'                   ";
        $sCampos .= "   WHEN ed217_observacao = 4 THEN '12'                   ";
        $sCampos .= " END AS fonte_observacao,                                ";
        $sCampos .= " ed217_brasao as brasao                                  ";

        $oDaoRelatorio = new cl_edu_relatmodel();
        $sSqlRelatorio = $oDaoRelatorio->sql_query("", $sCampos, "", "ed217_i_codigo = $iTipoRelatorio");
        $rsRelatorio = $oDaoRelatorio->sql_record($sSqlRelatorio);

        if ($oDaoRelatorio->numrows == 0) {
            db_redireciona("db_erros.php?fechar=true&db_erro=" . _M(self::MENSAGEM . "parametros_nao_localizado"));
        }

        $this->oParametros = db_utils::fieldsMemory($rsRelatorio, 0);
        $this->oParametros->exibe_turma = $this->oParametros->exibe_turma == 't';
        $this->oParametros->exibe_percentual_frequencia = $this->oParametros->exibe_percentual_frequencia == 't';
        $this->oParametros->exibirmantenedora = $this->oParametros->exibirmantenedora == 't';
        $this->oParametros->exibirdistrito = $this->oParametros->exibirdistrito == 't';
        $this->oParametros->exibirperiodo = $this->oParametros->exibirperiodo == 't';
        $this->oParametros->exibir_etapa_obs = $this->oParametros->exibir_etapa_obs == 't';
        $this->oParametros->exibircertidao = $this->oParametros->exibircertidao == 't';
        $this->oParametros->exibiridentidade = $this->oParametros->exibiridentidade == 't';
    }

    /**
     * Retorna uma string contendo todas as observações lançadas para o aluno quando este foi aprovado por conselho de
     * classe.
     *
     * @return string
     * @throws Exception
     */
    protected function getObservacaoAprovadoPeloConselho()
    {
        $aHistoricosAluno = HistoricoAlunoRepository::getHistoricosPorAluno($this->oAluno);

        $aCursos = array();
        foreach ($aHistoricosAluno as $oHistoricoCurso) {
            $aCursos[] = $oHistoricoCurso->getCodigoCurso();
        }

        $sCursos = implode(", ", $aCursos);

        $sWhere = "     ed95_i_aluno = {$this->oAluno->getCodigoAluno()} ";
        $sWhere .= " and ed31_i_curso in ({$sCursos})";

        $sCampos =  "
            cgmrh.z01_nome,
            ed253_i_data,
            ed232_c_descrcompleta as disciplina,
            ed253_t_obs,
            ed47_v_nome,
            ed11_c_descr as serie_conselho,
            ed59_i_ordenacao,
            ed253_aprovconselhotipo,
            ed52_i_ano,
            ed253_alterarnotafinal, ed253_avaliacaoconselho";
        $oDaoConselho = new cl_aprovconselho();
        $sSqlAprovConselho = $oDaoConselho->sql_query("", $sCampos, "ed59_i_ordenacao", $sWhere);
        $rsAprovConselho = $oDaoConselho->sql_record($sSqlAprovConselho);
        $iLinhas = $oDaoConselho->numrows;

        if ($iLinhas == 0) {
            return "";
        }

        $aObservacao = array();
        $aAprovadoBaixaFrequencia = array();
        for ($i = 0; $i < $iLinhas; $i++) {
            $oDadosAprovConselho = db_utils::fieldsmemory($rsAprovConselho, $i);

            switch ($oDadosAprovConselho->ed253_aprovconselhotipo) {

                /**
                 * Valida se a aprovação foi por conselho
                 */
            case 1:
                $oDocumento = new libdocumento(5013);
                $oDocumento->disciplina = $oDadosAprovConselho->disciplina;
                $oDocumento->etapa = $oDadosAprovConselho->serie_conselho;
                $oDocumento->justificativa = $oDadosAprovConselho->ed253_t_obs;
                $oDocumento->nota = ArredondamentoNota::arredondar(
                    $oDadosAprovConselho->ed253_avaliacaoconselho,
                    $oDadosAprovConselho->ed52_i_ano
                );
                $oDocumento->anomatricula = $oDadosAprovConselho->ed52_i_ano;

                $oObservacao = new stdClass();
                $oObservacao->aParagrafos = $oDocumento->getDocParagrafos();

                if (trim($oObservacao->aParagrafos[1]->oParag->db02_texto)) {
                    $aObservacao[] = "- " . $oObservacao->aParagrafos[1]->oParag->db02_texto;
                }

                break;

                /**
                 * Valida se a aprovação não foi por baixa frequencia
                 */
            case 2:
                $sHashSerieAno = $oDadosAprovConselho->serie_conselho . $oDadosAprovConselho->ed52_i_ano;
                if (!isset($aAprovadoBaixaFrequencia[$sHashSerieAno])) {
                    $aAprovadoBaixaFrequencia[$sHashSerieAno] = $oDadosAprovConselho;
                }
                break;

                /**
                 * Valida se a aprovação foi por regimento escolar
                 */
            case 3:
                $sTipoAprovacao = "foi aprovado pelo regimento escolar.";
                $sObservacao = "- Disciplina {$oDadosAprovConselho->disciplina} na etapa";
                $sObservacao .= " {$oDadosAprovConselho->serie_conselho} {$sTipoAprovacao}";
                $sObservacao .= "Justificativa: {$oDadosAprovConselho->ed253_t_obs}";
                $aObservacao[] = $sObservacao;
                break;
            }
        }

        $oDocumento = new libdocumento(5005);
        foreach ($aAprovadoBaixaFrequencia as $oAprovadosBaixaFrequencia) {
            $oDocumento->nome_aluno = $this->oAluno->getNome();
            $oDocumento->ano = $oAprovadosBaixaFrequencia->ed52_i_ano;
            $oDocumento->nome_etapa = $oAprovadosBaixaFrequencia->serie_conselho;
            $aParagrafos = $oDocumento->getDocParagrafos();
            if (isset($aParagrafos[1])) {
                $aObservacao[] = "- {$aParagrafos[1]->oParag->db02_texto}";
            }
        }

        return implode("\n", $aObservacao);
    }

    /**
     * Verifica se houve troca de série para o aluno
     * Se sim monta uma string, com os dados da troca
     *
     * @return string
     * @throws DBException
     */
    protected function getObservacaoTrocaSerie()
    {
        $aObservacao = [];
        $matriculas = MatriculaRepository::getTodasMatriculasAluno(new Aluno($this->oAluno->getCodigoAluno()), false);
        foreach ($matriculas as $matricula) {
            if ($matricula->getSituacao() == "CLASSIFICADO" || $matricula->getSituacao() == "AVANÇADO") {
                $aObservacao[] = $matricula->getObservacao();
            }
        }

        return implode("\n", $aObservacao);
    }

    /**
     * Busca os atos legais que aparecem no histórico do aluno
     *
     * @return array
     */
    protected function getAtosLegais()
    {
        $aAtosLegaisEscola = array();
        $aAtosLegaisCurso = array();

        /**
         * Primeiramente separamos os atos legais da escola e os que estão vinculados a algum curso da escola
         */
        foreach ($this->oEscola->getAtosLegais() as $oAtoLegal) {
            if ($oAtoLegal->existeCursoVinculado()) {
                $aAtosLegaisCurso[] = $oAtoLegal;
            } else {
                $aAtosLegaisEscola[] = $oAtoLegal;
            }
        }

        $aAtosLegais = array();
        foreach ($aAtosLegaisEscola as $oAtoLegal) {
            if (!$oAtoLegal->apareceHistorico()) {
                continue;
            }
            $sAtoLegal = "{$oAtoLegal->getFinalidade()}  Nº {$oAtoLegal->getNumero()} ";
            $sAtoLegal .= "Data {$oAtoLegal->getDataVigor()->convertTo(DBDate::DATA_PTBR)} ";
            $sAtoLegal .= "D.O.: {$oAtoLegal->getDataDePublicacao()->convertTo(DBDate::DATA_PTBR)} ";
            $sAtoLegal .= "\n" . $oAtoLegal->getTexto();

            $aAtosLegais[$oAtoLegal->getCodigoAtoLegal()] = $sAtoLegal;
        }

        $aHistoricoAluno = HistoricoAlunoRepository::getHistoricosPorAluno($this->oAluno);
        $aCodigoCursos = array();
        /**
         * Adicionamos em um array, os códigos dos cursos que o aluno cursou
         */
        foreach ($aHistoricoAluno as $oHistoricoAluno) {
            $aCodigoCursos[] = $oHistoricoAluno->getCodigoCurso();
        }


        /**
         * Filtra os atos legais dos cursos que o aluno estudou
         */
        foreach ($aAtosLegaisCurso as $oAtoLegal) {
            if (!$oAtoLegal->apareceHistorico()) {
                continue;
            }

            foreach ($oAtoLegal->getCursosVinculado() as $oCurso) {
                if (!in_array($oCurso->getCodigo(), $aCodigoCursos)) {
                    continue;
                }
            }

            $sAtoLegal = "{$oAtoLegal->getFinalidade()}  Nº {$oAtoLegal->getNumero()} ";
            $sAtoLegal .= "Data {$oAtoLegal->getDataVigor()->convertTo(DBDate::DATA_PTBR)} ";
            $sAtoLegal .= "D.O.: {$oAtoLegal->getDataDePublicacao()->convertTo(DBDate::DATA_PTBR)} ";
            $sAtoLegal .= "\n" . $oAtoLegal->getTexto();
            $aAtosLegais[$oAtoLegal->getCodigoAtoLegal()] = $sAtoLegal;
        }

        return $aAtosLegais;
    }

    protected function getUltimoAnoCursado()
    {
        $anoLimiteRede = "";
        $anoLimiteFora = "";
        if (!empty($this->iAnoLimite)) {
            $anoLimiteRede = " and ed62_i_anoref <= $this->iAnoLimite ";
            $anoLimiteFora = " and ed99_i_anoref <= $this->iAnoLimite ";
        }
        $sSql = "
          select max(ano) as ano
           from ( select max(ed62_i_anoref) as ano
                    from historico
                         inner join historicomps     on ed62_i_historico  = ed61_i_codigo
                   where ed61_i_aluno = {$this->oAluno->getCodigoAluno()} {$anoLimiteRede}
                   union
                  select max(ed99_i_anoref) as ano
                    from historico
                         inner join historicompsfora on ed99_i_historico  = ed61_i_codigo
                   where ed61_i_aluno = {$this->oAluno->getCodigoAluno()} {$anoLimiteFora}
                ) as x
          where ano is not null
        ";

        $rsSql = db_query($sSql);

        if (!$rsSql) {
            throw new DBException(_M(self::MENSAGEM . "erro_query_ultimo_ano_cursado"));
        }

        if (pg_num_rows($rsSql) > 0) {
            return db_utils::fieldsMemory($rsSql, 0)->ano;
        }

        return db_getsession("DB_anousu");
    }

    /**
     * Retorna o caminho do brasão, de acordo com o tipo enviado
     *
     * @param  integer     $iTipoBrasao
     * @param  Instituicao $oInstituicao
     * @return string Caminho do brasão
     */
    public static function getBrasao($iTipoBrasao, Instituicao $oInstituicao)
    {
        switch ($iTipoBrasao) {
        case RelatorioHistoricoEscolar::TIPO_BRASAO_REPUBLICA:
            return RelatorioHistoricoEscolar::CAMINHO_BRASAO_REPUBLICA;
                break;

        case RelatorioHistoricoEscolar::TIPO_BRASAO_MUNICIPIO:
            return "imagens/files/" . $oInstituicao->getImagemLogo();
                break;
        }
    }

    /**
     * Retorna as obervações lançadas para as etapas do histórico do aluno
     *
     * @return array
     */
    protected function getObservacaoHistoricoEtapa()
    {
        $aHistoricosAluno = HistoricoAlunoRepository::getHistoricosPorAluno($this->oAluno);

        $aObservacao = array();

        foreach ($aHistoricosAluno as $oHistoricoAluno) {
            foreach ($oHistoricoAluno->getEtapas() as $oEtapaHistorico) {
                if (!empty($this->iAnoLimite) && $oEtapaHistorico->getAnoCurso() > $this->iAnoLimite) {
                    continue;
                }
                $justificativa = '';
                if ($oEtapaHistorico->getSituacaoEtapa() === 'AMPARADO'
                    && !empty($oEtapaHistorico->getJustificativa())
                ) {
                    $oJustificativa = new Justificativa($oEtapaHistorico->getJustificativa());
                    $justificativa = "Amparado com Justificativa: " . $oJustificativa->getDescricao() . "\n";
                }
                if ($this->oParametros->exibir_etapa_obs) {
                    if ($this->temEtapaHistorico($oEtapaHistorico) && (!empty($oEtapaHistorico->getObservacao()) || !empty($justificativa))) {
                        $aObservacao[] = sprintf(
                            "%s - %s %s",
                            '<b>' . $oEtapaHistorico->getEtapa()->getNomeAbreviado() . "</b>",
                            $justificativa,
                            $oEtapaHistorico->getObservacao()
                        );
                    }
                } else {
                    if ($this->temEtapaHistorico($oEtapaHistorico) && (!empty($oEtapaHistorico->getObservacao()) || !empty($justificativa))) {
                        $aObservacao[] = sprintf(
                            "%s %s",
                            $justificativa,
                            $oEtapaHistorico->getObservacao()
                        );
                    }
                }

            }
        }

        return $aObservacao;
    }

    /**
     * Define se deve ser exibido somente cursos concluídos
     *
     * @param boolean $lExibirSomenteCursosConcluidos
     */
    protected function setExibirSomenteCursosEncerrados($lExibirSomenteCursosConcluidos)
    {
        $this->lExibirSomenteCursosConcluidos = $lExibirSomenteCursosConcluidos;
    }

    /**
     * Define um curso para ser apresentado na impressão do relatório
     *
     * @param Curso $oCurso
     */
    public function setCurso(Curso $oCurso)
    {
        $this->oCurso = $oCurso;
    }

    /**
     * Verifica se o curso percorrido do histórico é igual ao curso informado pelo filtro ou suas equivalências.
     *
     * @param  integer $iCurso Código do curso do histórico
     * @return boolean
     * @throws DBException
     */
    protected function validaCursos($iCurso)
    {
        $aCursosValidos = array($this->oCurso->getCodigo());

        foreach ($this->oCurso->getCursosEquivalentes() as $oCursoEquivalente) {
            $aCursosValidos[] = $oCursoEquivalente->getCodigo();
        }

        if (!in_array($iCurso, $aCursosValidos)) {
            return false;
        }

        return true;
    }

    /**
     * Retorna todos os códigos de cursos concluídos e suas equivalências de um aluno
     *
     * @return array
     * @throws DBException
     */
    private function getCursosConcluidos()
    {
        $aCursosConcluidosImpressao = array();

        if ($this->lExibirSomenteCursosConcluidos) {
            $aCursosConcluidos = CursoRepository::getCursosConcluidosPorAluno($this->oAluno);

            foreach ($aCursosConcluidos as $oCurso) {
                $aCursosConcluidosImpressao[] = $oCurso->getCodigo();

                foreach ($oCurso->getCursosEquivalentes() as $oCursoEquivalente) {
                    $aCursosConcluidosImpressao[] = $oCursoEquivalente->getCodigo();
                }
            }
        }
        return $aCursosConcluidosImpressao;
    }

    private function criarDisciplinaVO($oDisciplinaCursada, $oEtapaCursada)
    {
        $oDisciplina = new stdClass();
        $oDisciplina->iCadDisciplina = $oDisciplinaCursada->getDisciplina()->getCodigoDisciplinaGeral();
        $oDisciplina->sDisciplina = $oDisciplinaCursada->getDisciplina()->getNomeDisciplina();
        $oDisciplina->sAbrevDisciplina = $oDisciplinaCursada->getDisciplina()->getAbreviatura();
        $oDisciplina->sNomeCompleto = $oDisciplinaCursada->getDisciplina()->getNomeCompleto();
        $oDisciplina->sEtapa = $oEtapaCursada->getEtapa()->getNomeAbreviado();
        $oDisciplina->mAvaliacao = $oDisciplinaCursada->getResultadoObtido();
        $oDisciplina->iCargaHoraria = $oDisciplinaCursada->getCargaHoraria();
        $oDisciplina->sResultado = $this->termoResultadoFinalDisciplina(
            $oDisciplinaCursada->getResultadoFinal(),
            $oDisciplinaCursada->getSituacaoDisciplina(),
            $oDisciplinaCursada->getTermoFinal()
        );
        if ($oDisciplina->sResultado == 'APR*') {
            $oDisciplina->mAvaliacao .= "*";
        }
        $oDisciplina->iAno = $oEtapaCursada->getAnoCurso();
        $oDisciplina->iEscola = $oEtapaCursada->getEscola()->getCodigo();
        $oDisciplina->lBaseComum = $oDisciplinaCursada->isBaseComum();
        $oDisciplina->lTipoBase = $oDisciplinaCursada->getTipoBase();
        $oDisciplina->sSituacao = $oDisciplinaCursada->getSituacaoDisciplina();

        return $oDisciplina;
    }

    /**
     * Busca as etapas anteriores do aluno a partir do primeiro registro no histórico.
     *
     * @param  Etapa $oPrimeiraEtapa Última etapa cursada de acordo com o histórico
     * @throws DBException
     */
    protected function buscaEtapasAnteriores($oPrimeiraEtapa)
    {
        $ordemPrimeiraEtapa = $oPrimeiraEtapa->getOrdem();
        $aEtapasEnsino = EtapaRepository::getEtapasEnsino($oPrimeiraEtapa->getEnsino());

        foreach ($aEtapasEnsino as $etapa) {
            $oDadoEtapa = $this->criarObjetoEtapa($etapa);
            if ($etapa->getOrdem() < $ordemPrimeiraEtapa) {
                $this->aEtapasAnterior[$oDadoEtapa->iEtapa] = $oDadoEtapa;
            }
        }
    }

    protected function criarObjetoEtapa($oEtapaEnsino)
    {
        $oDadoEtapa = new stdClass();
        $oDadoEtapa->iEtapa = $oEtapaEnsino->getCodigo();
        $oDadoEtapa->ensino = $oEtapaEnsino->getEnsino();
        $oDadoEtapa->ordem = $oEtapaEnsino->getOrdem();
        $oDadoEtapa->sEtapa = $oEtapaEnsino->getNome();
        $oDadoEtapa->sTurma = '-';
        $oDadoEtapa->periodo = '-';
        $oDadoEtapa->sEscola = '-';
        $oDadoEtapa->sMunicipio = '-';
        $oDadoEtapa->iAno = '-';
        $oDadoEtapa->iDiasLetivos = '-';
        $oDadoEtapa->iCargaHoraria = '-';
        $oDadoEtapa->nPercentualFalta = '-';
        $oDadoEtapa->sResultado = '-';
        $oDadoEtapa->sUF = '-';

        return $oDadoEtapa;
    }

    /**
     * @param  HistoricoEtapa $etapa
     * @return boolean
     */
    private function temEtapaHistorico(HistoricoEtapa $etapa)
    {
        foreach ($this->aDadosOrganizados as $etapaHistorico) {
            if ($etapaHistorico->iEtapa === $etapa->getEtapa()->getCodigo()) {
                return true;
            }
        }
        return false;
    }
}
