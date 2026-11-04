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
 * classe para controle dos valores dos anexos legais da RGF/LRF
 * @package    contabilidade
 * @subpackage relatorios
 * @author Iuri Guncthnigg
 *
 */

use ECidade\Configuracao\RelatorioLegal\Enum\OrigemDadosEnum;

require_once modification('dbforms/db_funcoes.php');
require_once modification('libs/ReceitaSaldo.php');
require_once modification('libs/db_liborcamento.php');
require_once modification('libs/db_libcontabilidade.php');

class RelatoriosLegaisBase
{

    /**
     * Linhas que serão processadas os balancetes de receita
     * @var array
     */
    protected $aLinhasProcessamentoManual = array();


    /**
     * Linhas que serão processadas os balancetes de receita
     * @var array
     */
    protected $aLinhasProcessarReceita = array();

    /**
     * Linhas que serão processadas os balancetes de despesa
     * @var array
     */
    protected $aLinhasProcessarDespesa = array();

    /**
     * Linhas que serão processadas os balancetes de despesa por desdobramento (lancamento contabil)
     * @var array
     */
    protected $aLinhasProcessarDespesaDesdobramento = array();

    /**
     * Linhas que serão processadas os balancetes de verificação
     * @var array
     */
    protected $aLinhasProcessarVerificacao = array();

    /**
     * Linhas que serão processadas com as movimentações dos restos a pagar
     * @var array
     */
    protected $aLinhasProcessarRestosPagar = array();

    /**
     * Linhas para processar na consistência
     * @var array
     */
    protected $aLinhasConsistencia = array();

    /**
     * instacia da classe RelatorioContabil
     *
     * @var relatorioContabil
     */
    protected $oRelatorioLegal;

    /**
     * Exericio do relatorio
     *
     * @var integer
     */
    protected $iAnoUsu;

    /**
     * Codigo do relatorio
     *
     * @var integer
     */
    protected $iCodigoRelatorio;

    /**
     * Linhas do Relatório
     *
     * @var integer
     *
     */
    protected $aDados = array();

    /**
     * lista de Instituições
     *
     * @var string
     */
    protected $sListaInstit;
    /**
     * Codigo do periodo de emissao
     *
     * @var integer
     *
     */
    protected $iCodigoPeriodo;

    /**
     * Data inicial do período selecionado
     * Pega o primeiro dia com base no período selecionado
     *
     * @var DBDate
     */
    protected $oDataInicialPeriodo;

    /**
     * Data inicial do relatório
     * Pega por padrão o primeiro dia do ano do período do relatório
     *
     * @var DBDate
     */
    protected $oDataInicial;

    /**
     * Data final do relatório
     * Pega o ultimo dia com base no período selecionado do relatório
     *
     * @var DBDate
     */
    protected $oDataFinal;

    /**
     * Campos para cálculo do Balancete de Receita
     * @var array
     */
    public static $aCamposReceita = array(
        'saldo_inicial',
        'saldo_prevadic_acum',
        'saldo_inicial_prevadic',
        'saldo_anterior',
        'saldo_arrecadado',
        'saldo_a_arrecadar',
        'saldo_arrecadado_acumulado',
        'saldo_prev_anterior'
    );

    /**
     * Campos para cálculo do Balancete de Despesa
     * @var array
     */
    public static $aCamposDespesa = array(
        'dot_ini',
        'saldo_anterior',
        'empenhado',
        'anulado',
        'liquidado',
        'pago',
        'suplementado',
        'reduzido',
        'atual',
        'reservado',
        'atual_menos_reservado',
        'atual_a_pagar_liquidado',
        'empenhado_acumulado',
        'anulado_acumulado',
        'atual_a_pagar',
        'liquidado_acumulado',
        'pago_acumulado',
        'suplementado_acumulado',
        'reduzido_acumulado',
        'proj',
        'ativ',
        'oper',
        'ordinario',
        'vinculado',
        'suplemen',
        'suplemen_acumulado',
        'especial',
        'especial_acumulado'
    );

    /**
     * Campos para cálculo o Balancete despesa detalhado por desdobramento
     * @var array
     */

    public static $aCamposDespesaDesdobramento = array(
        'dot_ini',
        'saldo_anterior',
        'empenhado',
        'anulado',
        'liquidado',
        'pago',
        'suplementado',
        'reduzido',
        'atual',
        'reservado',
        'atual_menos_reservado',
        'atual_a_pagar_liquidado',
        'empenhado_acumulado',
        'anulado_acumulado',
        'atual_a_pagar',
        'liquidado_acumulado',
        'pago_acumulado',
        'suplementado_acumulado',
        'reduzido_acumulado',
        'proj',
        'ativ',
        'oper',
        'ordinario',
        'vinculado',
        'suplemen',
        'suplemen_acumulado',
        'especial',
        'especial_acumulado'
    );

    public static $aCamposRestoPagar = array(
        'e91_vlremp',
        'e91_vlranu',
        'e91_vlrliq',
        'e91_vlrpag',
        'vlranu',
        'vlrliq',
        'vlrpag',
        'vlrpagnproc',
        'vlranuliq',
        'vlranuliqnaoproc'
    );
    /**
     * Campos para consulta do Balancete de Verificação
     * @var array
     */
    public static $aCamposVerificacao = array(
        'saldo_anterior',
        'saldo_anterior_debito',
        'saldo_anterior_credito',
        'saldo_final'
    );

    /**
     * Marcadores que serão substituidos nas linhas do relatório
     * @var array
     */
    protected $aMarcadoresLinhasRelatorio = array(
        '#exercicio_anterior' => '',
        '#exercicio' => ''
    );

    /**
     * Tipo de cálculo do Balancete de Receita
     */
    const TIPO_CALCULO_RECEITA = 1;

    /**
     * Tipo de cálculo do Balancete de Despesa
     */
    const TIPO_CALCULO_DESPESA = 2;

    /**
     * Tipo de cálculo do Balancete de Verificação
     */
    const TIPO_CALCULO_VERIFICACAO = 3;

    /**
     * Calculos de Restos a pagar
     */

    const TIPO_CALCULO_RESTO = 4;
    /**
     * Calculos de Balancete da Despesa por lancamento contabil (desdobramento)
     */
    const TIPO_CALCULO_DESPESA_DESDOBRAMENTO = 6;

    /**
     * @var _db_fields|stdClass
     */
    protected $oPeriodo;

    /**
     *
     * @param integer $iAnoUsu ano de emissao do relatorio
     * @param integer $iCodigoRelatorio codigo do relatorio
     * @param integer $iCodigoPeriodo Codigo do periodo de emissao do relatorio
     */
    public function __construct($iAnoUsu, $iCodigoRelatorio, $iCodigoPeriodo)
    {
        $this->iCodigoRelatorio = $iCodigoRelatorio;
        $this->iAnoUsu = $iAnoUsu;
        $this->iCodigoPeriodo = $iCodigoPeriodo;
        $this->oRelatorioLegal = new relatorioContabil($iCodigoRelatorio, false);

        $this->oPeriodo = new Periodo($iCodigoPeriodo);

        if (Check::between($this->oPeriodo->getCodigo(), 1, 17)) {
            $aPeriodo = data_periodo($this->iAnoUsu, $this->oPeriodo->getSigla());
            $sDataExercicio = $aPeriodo[1];

            $this->setDataInicialPeriodo(new DBDate($aPeriodo[0]));
        } else {
            $iUltimoDiaMes = cal_days_in_month(CAL_GREGORIAN, $this->oPeriodo->getMesFinal(), $this->iAnoUsu);
            $sDataExercicio = "{$this->iAnoUsu}-{$this->oPeriodo->getMesFinal()}-{$iUltimoDiaMes}";
        }

        $this->aMarcadoresLinhasRelatorio['#exercicio'] = $this->iAnoUsu;
        $this->aMarcadoresLinhasRelatorio['#exercicio_anterior'] = $this->iAnoUsu - 1;

        $this->setDataInicial(new DBDate("{$iAnoUsu}-01-01"));
        $this->setDataFinal(new DBDate($sDataExercicio));
    }

    /**
     * retorna os dados do relatorio.
     *
     */
    public function getDados($trazerConfiguracaoPadrao = true)
    {
        if (empty($this->aLinhasConsistencia)) {
            $this->aLinhasConsistencia = $this->getLinhasRelatorio($trazerConfiguracaoPadrao);
            $this->executarBalancetesNecessarios();
            $this->processarValoresManuais();
            $this->processaTotalizadores($this->aLinhasConsistencia);
        }
        return $this->aLinhasConsistencia;
    }

    /**
     * retorna os dados necessários para o relatorio simplidicado
     *
     */
    public function getDadosSimplificado()
    {
    }

    /**
     * define as instituicoes que serao usadas no relatorio
     *
     * @param string $sInstituicoes lista das instituicoes, seperadas por virgula
     */
    public function setInstituicoes($sInstituicoes)
    {
        $this->sListaInstit = $sInstituicoes;
    }

    /**
     * Retorna as instituições setadas para o relatório. Quando o parâmetro $lObjeto for true, retorna uma coleção
     * de instituções
     * @param bool $lObjeto
     * @return Instituicao[]|string
     */
    public function getInstituicoes($lObjeto = false)
    {
        if ($lObjeto) {
            $aInstituicoes = explode(',', str_replace("-", ",", $this->sListaInstit));
            $aInstituicoesRetorno = array();
            foreach ($aInstituicoes as $iCodigoInstituicao) {
                $aInstituicoesRetorno[$iCodigoInstituicao] = InstituicaoRepository::getInstituicaoByCodigo($iCodigoInstituicao);
            }
            return $aInstituicoesRetorno;
        }
        return $this->sListaInstit;
    }


    /**
     * Processa as formulas do relatorio
     * @param $aLinhas
     * @throws Exception
     */
    public function processaTotalizadores($aLinhas)
    {

        $aLinhasResolverManual = $this->aLinhasProcessamentoManual;

        foreach ($aLinhas as $iLinha => $oLinha) {
            /*
              ajuste para cancelar o processamento de algumas linhas e deixar de forma fixa:
              acontece que uma linha que tenha 4 colunas utilizar de uma linha que tenha 2 colunas
              sistema se perde e busca na linha que tem 2 colunas uma terceira e quarta coluna devido a linha que a
              utiliza possuir mais colunas que a da origem dos dados
            */
            if (in_array($aLinhas[$iLinha]->ordem, $aLinhasResolverManual)) {
                continue;
            }

            if ($oLinha->totalizar) {
                foreach ($oLinha->colunas as $iColuna => $oColuna) {
                    if (trim($oColuna->o116_formula) != "") {
                        $this->parseFormula($aLinhas, $iLinha, $iColuna);
                    }
                }
            }
        }
    }

    /**
     * Reprocessa as formulas da linha passada
     * @param array $aLinhas
     * @param integer $iLinha
     * @throws Exception
     * @deprecated
     * @see processarFormulaDaLinha
     */
    public function processaFormulasLinha($aLinhas, $iLinha)
    {
        $this->processarFormulaDaLinha($iLinha);
    }

    /**
     * Reprocessa as formulas da linha passada
     * @param integer $iLinha
     * @throws Exception
     */
    public function processarFormulaDaLinha($iLinha)
    {
        if (empty($this->aLinhasConsistencia)) {
            $this->getDados();
        }

        foreach ($this->aLinhasConsistencia[$iLinha]->colunas as $iColuna => $oColuna) {
            if (trim($oColuna->o116_formula) != '') {
                $this->parseFormula($this->aLinhasConsistencia, $iLinha, $iColuna);
            }
        }
    }

    /**
     * @param $linha
     * @param $coluna
     * @throws Exception
     */
    public function processarFormulaDaLinhaEColuna($linha, $coluna)
    {
        if (empty($this->aLinhasConsistencia)) {
            $this->getDados();
        }

        $dadosColuna = $this->aLinhasConsistencia[$linha]->colunas[$coluna];
        if (trim($dadosColuna->o116_formula) !== '') {
            $this->parseFormula($this->aLinhasConsistencia, $linha, $coluna);
        }
    }

    /**
     * Processa as formas das linhas informadas no parâmetro
     *
     * @param array $aCodigoLinhas EX: array(1,5,10);
     * @return bool
     * @throws Exception
     */
    public function processarFormasDasLinhas(array $aCodigoLinhas)
    {
        if (count($aCodigoLinhas) == 0) {
            throw new Exception("Informe ao menos uma linha para ser processada suas fórmulas.");
        }
        foreach ($aCodigoLinhas as $iCodigoLinha) {
            $this->processarFormulaDaLinha($iCodigoLinha);
        }
        return true;
    }

    /**
     * Faz o parse da formula da linha e coluna passados
     * @param array &$aLinhas - Array das linhas do relatório
     * @param integer $iLinha - Linha
     * @param integer $iColuna - Coluna
     * @throws Exception
     */
    protected function parseFormula($aLinhas, $iLinha, $iColuna)
    {
        $sFormula = $this->oRelatorioLegal->parseFormula(
            'aLinhas',
            $aLinhas[$iLinha]->colunas[$iColuna]->o116_formula,
            $iColuna,
            $aLinhas
        );
        $evaluate = "\$aLinhas[{$iLinha}]->{$aLinhas[$iLinha]->colunas[$iColuna]->o115_nomecoluna} = {$sFormula};";

        ob_start();
        eval( str_replace("#", "$", $evaluate ) );
        $sRetorno = ob_get_contents();
        ob_clean();

        if (strpos(strtolower($sRetorno), "parse error") !== false) {
            $sMsg = "Linha {$iLinha}, Coluna {$aLinhas[$iLinha]->colunas[$iColuna]->o115_nomecoluna} com erro no cadastro da formula\n{$aLinhas[$iLinha]->colunas[$iColuna]->o116_formula}\n{$sRetorno}";
            throw new Exception($sMsg);
        }
    }

    /**
     * Retorna os periodos cadastras para o relatorio
     *
     * @return array();
     */
    public function getPeriodos()
    {
        return $this->oRelatorioLegal->getPeriodos();
    }

    /**
     * Verifica se há espaço na página e escreve a nota explicativa.
     *
     * Exemplo:
     * $this->notaExplicativa($oPdf, array($this, 'adicionarPagina'), 20);
     *
     * @param PDFDocument $oPdf Instância da PDFDocument
     * @param array $callback Callback utilizado para quebrar a página (se necessário)
     * @param integer $iMargem Margem a ser considerada no cálculo
     * @throws ParameterException
     */
    public function notaExplicativa(PDFDocument $oPdf, array $callback, $iMargem = 0)
    {
        /**
         * Verifica se o índice zero é objeto
         */
        if (!is_object($callback[0])) {
            throw new ParameterException('Não foi informada uma instância de objeto válida.');
        }

        /**
         * Verifica se o método existe no objeto informado
         */
        if (!method_exists($callback[0], $callback[1])) {
            throw new ParameterException('O método não existe no objeto informado.');
        }

        /**
         * Verifica a visiblidade do método
         */
        $oReflection = new ReflectionMethod($callback[0], $callback[1]);
        if (!$oReflection->isPublic()) {
            throw new ParameterException('O método informado deve ser público.');
        }

        /**
         * Calcula o tamanho da nota explicativa e caso não haja espaço
         * suficiente para escrever chama o método passado por parâmetro
         */
        $iAltura = $this->oRelatorioLegal->notaExplicativa($oPdf, $this->iCodigoPeriodo, $oPdf->getAvailWidth(), false);
        if ($oPdf->getAvailHeight() < ($iAltura + $iMargem)) {
            call_user_func($callback);
        }

        /**
         * Escreve a nota explicativa
         */
        $this->oRelatorioLegal->notaExplicativa($oPdf, $this->iCodigoPeriodo, $oPdf->getAvailWidth());
    }

    /**
     * Monta a nota explicativa
     *
     * @param FPDF $oPdf instancia do PDf
     * @param integer $iPeriodo Codigo do periodo
     * @param integer $iTam Tamanho da celula
     * @return void
     * @deprecated Utilize o método notaExplicativa
     */
    public function getNotaExplicativa($oPdf, $iPeriodo, $iTam = 190)
    {
        $this->oRelatorioLegal->getNotaExplicativa($oPdf, $iPeriodo, $iTam);
    }

    /**
     * Seta a data inicial do relatório
     *
     * @param DBDate $oDataInicial instância da data inicial do relatório
     */
    public function setDataInicial(DBDate $oDataInicial)
    {
        $this->oDataInicial = $oDataInicial;
    }

    /**
     * Seta a data final do relatório
     *
     * @param DBDate $oDataFinal instância da data final do relatório
     */
    public function setDataFinal(DBDate $oDataFinal)
    {
        $this->oDataFinal = $oDataFinal;
    }

    /**
     * Data inicial de emissão do relatório
     * @return DBDate Data inicial da emissão do relatório
     */
    public function getDataInicial()
    {
        return $this->oDataInicial;
    }

    /**
     * Data final de emissão do relatório
     * @return DBDate Data final da emissão do relatório
     */
    public function getDataFinal()
    {
        return $this->oDataFinal;
    }


    public function ajustaValorManualPorColunaDaLinha( $iSequencialColuna, $iLinha){

        $oDao  = new \cl_orcparamseqorcparamseqcoluna();
        $sSql = $oDao->sql_queryValorManual ( null,
                                              "o115_nomecoluna,o117_valor ",
                                              null,
                                              "o116_sequencial = {$iSequencialColuna}");
        $rsManual = $oDao->sql_record($sSql);

        if ($oDao->numrows > 0) {

            $oDados = db_utils::fieldsMemory($rsManual, 0);
            $nomeColuna = $oDados->o115_nomecoluna;
            $nValorManual = db_utils::fieldsMemory($rsManual, 0)->o117_valor;
            $this->aLinhasConsistencia[$iLinha]->$nomeColuna += $nValorManual;

        }
    }

    /**
     * Retorna as linhas configuradas para o relatório
     */
    public function getLinhasRelatorio($trazerConfiguracaoPadrao = true)
    {
        $aLinhasRetorno = array();
        $aLinhasRelatorio = $this->oRelatorioLegal->getLinhasCompleto();

        foreach ($aLinhasRelatorio as $oLinha) {
            $oLinha->setPeriodo($this->iCodigoPeriodo);

            $oParametros = $oLinha->getParametros($this->iAnoUsu, $this->getInstituicoes(), $trazerConfiguracaoPadrao);
            $oColunas = $oLinha->getCols($this->iCodigoPeriodo);
            $oLinhaRetorno = new stdClass();
            $oLinhaRetorno->ordem = $oLinha->getOrdem();
            $oLinhaRetorno->codigo = $oLinha->getCodigo();
            $oLinhaRetorno->totalizar = $oLinha->isTotalizador();
            $oLinhaRetorno->descricao = $oLinha->getDescricaoLinha();
            $oLinhaRetorno->colunas = $oColunas;
            $oLinhaRetorno->contas = array();
            $oLinhaRetorno->desdobrar = false;
            $oLinhaRetorno->nivel = $oLinha->getNivel();
            $oLinhaRetorno->parametros = $oParametros;
            $oLinhaRetorno->oLinhaRelatorio = $oLinha;
            $oLinhaRetorno->origem = $oLinha->getOrigemDados();

            foreach ($this->aMarcadoresLinhasRelatorio as $sMarcador => $sValor) {
                $oLinhaRetorno->descricao = str_replace($sMarcador, $sValor, $oLinhaRetorno->descricao);
            }

            if ($oParametros->desdobrarlinha && $oLinha->desdobraLinha()) {
                $oLinhaRetorno->desdobrar = true;
            }

            /**
             * Criamos as colunas
             */
            foreach ($oLinhaRetorno->colunas as $oColuna) {
                $oLinhaRetorno->{$oColuna->o115_nomecoluna} = 0;
            }

            $aLinhasRetorno[$oLinha->getOrdem()] = $oLinhaRetorno;
        }

        return $aLinhasRetorno;
    }

    /**
     * Realiza o Calculo do valor para a linha informada
     *
     * @param resource $Recordset resource com os dados do balancete do tipo informado
     * @param stdClass $oLinha stdClass com os dados a ser Analisado
     * @param array $aColunasCalcular
     * @param integer $iTipoCalculo tipo do calculo que deve ser realizado
     * @return float
     */
    protected static function calcularValorDaLinha($Recordset, stdClass $oLinha, array $aColunasCalcular, $iTipoCalculo)
    {
        $aListaColunas = array();
        $sNomeColunaDescricao = '';
        switch ($iTipoCalculo) {
            case RelatoriosLegaisBase::TIPO_CALCULO_RECEITA:
                $sNomeColunaDescricao = "o57_descr";
                $aListaColunas = RelatoriosLegaisBase::$aCamposReceita;
                $sColunaEstrutural = 'estrutural';
                break;

            case RelatoriosLegaisBase::TIPO_CALCULO_DESPESA:
                $sNomeColunaDescricao = "o56_descr";
                $aListaColunas = RelatoriosLegaisBase::$aCamposDespesa;
                $sColunaEstrutural = 'o58_elemento';
                break;

            case RelatoriosLegaisBase::TIPO_CALCULO_VERIFICACAO:
                $sNomeColunaDescricao = "c60_descr";
                $aListaColunas = RelatoriosLegaisBase::$aCamposVerificacao;
                $sColunaEstrutural = 'estrutural';
                break;

            case RelatoriosLegaisBase::TIPO_CALCULO_RESTO:
                $sNomeColunaDescricao = "o56_descr";
                $aListaColunas = RelatoriosLegaisBase::$aCamposRestoPagar;
                $sColunaEstrutural = 'o56_elemento';
                break;

            case RelatoriosLegaisBase::TIPO_CALCULO_DESPESA_DESDOBRAMENTO:
                $sNomeColunaDescricao = "o56_descr";
                $aListaColunas = RelatoriosLegaisBase::$aCamposDespesaDesdobramento;
                $sColunaEstrutural = 'o58_elemento';
                break;
        }

        $nValorLinha = 0;
        $iTotalLinhas = pg_num_rows($Recordset);
        for ($iLinha = 0; $iLinha < $iTotalLinhas; $iLinha++) {
            $oDados = new stdClass();
            $oDadosResource = db_utils::fieldsMemory($Recordset, $iLinha);
            foreach ($oLinha->parametros->contas as $oConta) {
                $oVerificacao = $oLinha->oLinhaRelatorio->match(
                    $oConta,
                    $oLinha->parametros->orcamento,
                    $oDadosResource,
                    $iTipoCalculo
                );

                $oValoresParaCalculo = clone $oDadosResource;
                if ($oVerificacao->match) {
                    if ($oVerificacao->exclusao) {
                        foreach ($aListaColunas as $sColuna) {
                            $oValoresParaCalculo->{$sColuna} *= -1;
                        }
                    }

                    if ($oLinha->desdobrar) {
                        if (!isset($oLinha->contas[$oConta->estrutural])) {
                            $oContaDesdobrada = new stdClass();
                            $oContaDesdobrada->descricao = $oValoresParaCalculo->{$sNomeColunaDescricao};
                            $oLinha->contas[$oConta->estrutural] = $oContaDesdobrada;
                        }
                    }
                    $oLinhaCalculo = clone $oLinha;
                    $oDados->resource = $oValoresParaCalculo;
                    foreach ($aColunasCalcular as $oColuna) {
                        $oDados->coluna = $oColuna;
                        $nValorConta = RelatoriosLegaisBase::resolverFormula(
                            $oColuna->formula,
                            $oDados,
                            $oLinhaCalculo,
                            $oColuna
                        );

                        if ($oLinha->desdobrar) {
                            $oContaDesdobrada = $oLinha->contas[$oConta->estrutural];
                            if (!isset($oContaDesdobrada->{$oColuna->nome})) {
                                $oContaDesdobrada->{$oColuna->nome} = 0;
                            }
                            $oContaDesdobrada->{$oColuna->nome} += $nValorConta;
                        }

                        if (isset($oColuna->agrupar)) {
                            RelatoriosLegaisBase::agrupar($oLinha, $oColuna, $oValoresParaCalculo, $nValorConta);
                        }
                        $oLinha->{$oColuna->nome} += $nValorConta;
                    }
                }
            }
        }

        return $oLinha;
    }


    /**
     * Realiza do agrupamentop dos valores atravez de um tipo
     * @param $oLinha
     * @param $oColuna
     * @param $oResource
     * @param $nValor
     */
    protected static function agrupar($oLinha, $oColuna, $oResource, $nValor)
    {
        if (!isset($oLinha->{$oColuna->agrupar->nome})) {
            $oLinha->{$oColuna->agrupar->nome} = array();
        }

        if (!isset($oLinha->{$oColuna->agrupar->nome}[$oResource->{$oColuna->agrupar->campo}])) {
            $oAgrupar = new stdClass();
            $oAgrupar->nome = $oResource->{$oColuna->agrupar->descricao};
            $oAgrupar->{$oColuna->nome} = 0;

            $oLinha->{$oColuna->agrupar->nome}[$oResource->{$oColuna->agrupar->campo}] = $oAgrupar;
        }

        $oAgrupar = $oLinha->{$oColuna->agrupar->nome}[$oResource->{$oColuna->agrupar->campo}];

        if (!isset($oAgrupar->{$oColuna->nome})) {
            $oAgrupar->{$oColuna->nome} = 0;
        }

        $oAgrupar->{$oColuna->nome} += $nValor;
    }

    /**
     * REalizar o parse da formula
     * @param string $sFormula Formula matematica
     * @param stdClass $oDados objeto com os valores
     * @param $oLinha
     * @param $oColuna
     * @return int
     */
    protected static function resolverFormula($sFormula, $oDados, $oLinha, $oColuna)
    {
        $nValor = 0;
        if (trim($sFormula) != '') {
            if (strpos($sFormula, 'L[') !== false || strpos($sFormula, 'F[') !== false) {
                return 0;
            }
            $sFormula = str_replace('#', '$oDados->resource->', $sFormula);
            eval("\$nValor = {$sFormula};");
        }


        return $nValor;
    }

    /**
     * Realiza o processamento das linhas com valores Digitados Manuais
     */
    protected function processarValoresManuais()
    {
        foreach ($this->aLinhasConsistencia as $oLinha) {
            $aValoresColunasLinhas = $oLinha->oLinhaRelatorio->getValoresColunas(
                null,
                null,
                $this->getInstituicoes(),
                $this->iAnoUsu
            );
            foreach ($aValoresColunasLinhas as $oValores) {
                foreach ($oValores->colunas as $oColuna) {
                    $oLinha->{$oColuna->o115_nomecoluna} += $oColuna->o117_valor;
                }
            }
        }
    }

    /**
     * @param $iOrdemLinha
     * @param $iColuna
     *
     * @throws ParameterException
     */
    protected function processaValorManualPorLinhaEColuna($iOrdemLinha, $iColuna)
    {
        if (empty($this->aLinhasConsistencia[$iOrdemLinha])) {
            throw new ParameterException("Linha de ordem {$iOrdemLinha} não encontrada.");
        }


        if (empty($this->aLinhasConsistencia[$iOrdemLinha]->colunas[$iColuna])) {

            throw new ParameterException("Coluna {$iColuna} não encontrada.");
        }
        $aValoresColunasLinhas = $this->aLinhasConsistencia[$iOrdemLinha]->oLinhaRelatorio->getValoresColunas(
            null,
            null,
            $this->getInstituicoes(),
            $this->iAnoUsu
        );

        foreach ($aValoresColunasLinhas as $oValores) {
            foreach ($oValores->colunas as $iIndiceColuna => $oColuna) {
                if ($iIndiceColuna == $iColuna) {
                    $this->aLinhasConsistencia[$iOrdemLinha]->{$oColuna->o115_nomecoluna} += $oColuna->o117_valor;
                }
            }
        }
    }

    /**
     * Retorna a instancia do relatorioContabil
     * @return relatorioContabil
     */
    public function getRelatorioContabil()
    {
        return $this->oRelatorioLegal;
    }

    /**
     * Verifica quais os tipos de calculos devem ser executados para a consistência
     */
    protected function processarTiposDeCalculo()
    {
        foreach ($this->aLinhasConsistencia as $iLinhas => $oLinha) {
            if ($oLinha->totalizar) {
                continue;
            }

            switch ($oLinha->origem) {
                case OrigemDadosEnum::BALANCETE_RECEITA:
                    $this->aLinhasProcessarReceita[] = $iLinhas;
                    break;
                case OrigemDadosEnum::BALANCETE_DESPESA:
                    $this->aLinhasProcessarDespesa[] = $iLinhas;
                    break;
                case OrigemDadosEnum::RESTOS_PAGAR:
                    $this->aLinhasProcessarRestosPagar[] = $iLinhas;
                    break;
                case OrigemDadosEnum::BALANCETE_VERIFICACAO:
                    $this->aLinhasProcessarVerificacao[] = $iLinhas;
                    break;
                case OrigemDadosEnum::BALANCETE_DESPESA_DESDOBRAMENTO:
                    $this->aLinhasProcessarDespesaDesdobramento[] = $iLinhas;
                    break;
            }
        }
    }

    /**
     * Alias para processarTiposDeCalculo
     */
    protected function organizaLinhasPorTipoDeCalculo()
    {
        $this->processarTiposDeCalculo();
    }


    /**
     * Executa oo calculo dos balancetes necessarios
     */
    protected function executarBalancetesNecessarios()
    {
        $this->processarTiposDeCalculo();

        if (count($this->aLinhasProcessarReceita) > 0) {
            $this->executarBalanceteDaReceita();
        }

        if (count($this->aLinhasProcessarDespesa) > 0) {
            $this->executarBalanceteDespesa();
        }

        if (count($this->aLinhasProcessarVerificacao) > 0) {
            $this->executarBalanceteVerificacao();
        }

        if (count($this->aLinhasProcessarRestosPagar) > 0) {
            $this->executarRestosPagar();
        }

        if (count($this->aLinhasProcessarDespesaDesdobramento) > 0) {
            $this->executarBalanceteDespesaDesdobramento();
        }
    }


    /**
     * @param array|null $linhas
     * @param array|null $colunas
     * @param string $dataInicial
     * @param string $dataFinal
     */
    protected function executarBalanceteDaReceita(array $linhas = null, array $colunas = null, $dataInicial = null, $dataFinal = null)
    {

        $linhas = empty($linhas) ? $this->aLinhasProcessarReceita : $linhas;
        $colunas = empty($colunas) ? array() : $colunas;
        $sWhereReceita = "o70_instit in ({$this->getInstituicoes()})";

        $sDataInicial = $this->getDataInicial()->getDate();
        $sDataFinal = $this->getDataFinal()->getDate();

        if (!empty($dataInicial)) {
            $sDataInicial = $dataInicial;
        }

        if (!empty($dataFinal)) {
            $sDataFinal = $dataFinal;
        }

        if ($this->iAnoUsu >= 2018) {
            $rsBalanceteReceita = ReceitaSaldo(
                11,
                1,
                3,
                true,
                $sWhereReceita,
                $this->iAnoUsu,
                $sDataInicial,
                $sDataFinal
            );
        } else {
            $rsBalanceteReceita = db_receitasaldo(
                11,
                1,
                3,
                true,
                $sWhereReceita,
                $this->iAnoUsu,
                $this->getDataInicial()->getDate(),
                $this->getDataFinal()->getDate()
            );
        }

        foreach ($linhas as $iLinha) {
            $oLinha = $this->aLinhasConsistencia[$iLinha];
            $aColunasProcessar = $this->getColunasPorLinha($oLinha, $colunas);
            RelatoriosLegaisBase::calcularValorDaLinha(
                $rsBalanceteReceita,
                $oLinha,
                $aColunasProcessar,
                RelatoriosLegaisBase::TIPO_CALCULO_RECEITA
            );
        }
        $this->limparEstruturaBalanceteReceita();
    }

    /**
     * Executa o Balancete de Despesa
     */
    protected function executarBalanceteDespesa()
    {
        $sWhereDespesa = " o58_instit in({$this->getInstituicoes()})";
        $rsBalanceteDespesa = db_dotacaosaldo(
            8,
            2,
            2,
            true,
            $sWhereDespesa,
            $this->iAnoUsu,
            $this->getDataInicial()->getDate(),
            $this->getDataFinal()->getDate()
        );

        foreach ($this->aLinhasProcessarDespesa as $iLinha) {
            $oLinha = $this->aLinhasConsistencia[$iLinha];
            $aColunasProcessar = $this->processarColunasDaLinha($oLinha);
            RelatoriosLegaisBase::calcularValorDaLinha(
                $rsBalanceteDespesa,
                $oLinha,
                $aColunasProcessar,
                RelatoriosLegaisBase::TIPO_CALCULO_DESPESA
            );

            $this->limparEstruturaBalanceteDespesa();
        }
    }

    /**
     * Executa o Balancete de Despesa por desdobramento, fazendo leitura pelos lancamentos contabeis
     */
    protected function executarBalanceteDespesaDesdobramento()
    {
        $sWhereDespesaDesdobramento = " empempenho.e60_instit in ({$this->getInstituicoes()})";

        $daoConlancamEmp = new cl_conlancamemp();
        $sql = $daoConlancamEmp->sql_query_despesa_desdobramento(
            $this->getAno(),
            $this->getDataInicial()->convertTo('Y-m-d'),
            $this->getDataFinal()->convertTo('Y-m-d'),
            $sWhereDespesaDesdobramento
        );

        $rsBalanceteDespesaDesdobramento = db_query($sql);


        foreach ($this->aLinhasProcessarDespesaDesdobramento as $iLinha) {
            $oLinha = $this->aLinhasConsistencia[$iLinha];
            $aColunasProcessar = $this->processarColunasDaLinha($oLinha);
            RelatoriosLegaisBase::calcularValorDaLinha(
                $rsBalanceteDespesaDesdobramento,
                $oLinha,
                $aColunasProcessar,
                RelatoriosLegaisBase::TIPO_CALCULO_DESPESA_DESDOBRAMENTO
            );
        }
    }

    /**
     * @param array $linhas
     * @param array $colunas
     * @param null $where
     * @throws Exception
     */
    protected function executarBalanceteVerificacao(array $linhas = null, array $colunas = null, $where = null)
    {
        $linhas = empty($linhas) ? $this->aLinhasProcessarVerificacao : $linhas;
        $colunas = empty($colunas) ? array() : $colunas;

        $sWhereVerificacao = " c61_instit in({$this->getInstituicoes()})";
        $sWhereVerificacao .= !empty($where) ? " and {$where} " : '';

        $rsBalanceteVerificacao = db_planocontassaldo_matriz(
            $this->iAnoUsu,
            $this->getDataInicial()->getDate(),
            $this->getDataFinal()->getDate(),
            false,
            $sWhereVerificacao,
            '',
            'true',
            'false'
        );

        foreach ($linhas as $iLinha) {
            $oLinha = $this->aLinhasConsistencia[$iLinha];
            $aColunasProcessar = $this->getColunasPorLinha($oLinha, $colunas);
            RelatoriosLegaisBase::calcularValorDaLinha(
                $rsBalanceteVerificacao,
                $oLinha,
                $aColunasProcessar,
                RelatoriosLegaisBase::TIPO_CALCULO_VERIFICACAO
            );

            $this->limparEstruturaBalanceteVerificacao();
        }
    }

    /**
     * Executa a função de Restos a Pagar para as linhas e Colunas Desejadas
     * @param array $linhas
     * @param null $coluna
     */
    protected function executarRestosPagar(array $linhas = array(), $coluna = null)
    {
        if (empty($linhas)) {
            $linhas = $this->aLinhasProcessarRestosPagar;
        }

        $oDaoRestosAPagar = new cl_empresto();
        $sWhereRestoPagar = " e60_instit in({$this->getInstituicoes()})";
        $sSqlRestosaPagar = $oDaoRestosAPagar->sql_rp_novo(
            $this->iAnoUsu,
            $sWhereRestoPagar,
            $this->getDataInicial()->getDate(),
            $this->getDataFinal()->getDate()
        );

        $rsRestosPagar = db_query($sSqlRestosaPagar);
        foreach ($linhas as $iLinha) {
            if (empty($coluna) && $coluna !== "0") {
                $coluna = array();
            } elseif (!is_array($coluna)) {
                $coluna = array($coluna);
            }

            $oLinha = $this->aLinhasConsistencia[$iLinha];
            $aColunasProcessar = $this->getColunasPorLinha($oLinha, $coluna);
            RelatoriosLegaisBase::calcularValorDaLinha(
                $rsRestosPagar,
                $oLinha,
                $aColunasProcessar,
                RelatoriosLegaisBase::TIPO_CALCULO_RESTO
            );
        }
    }

    /**
     * Processas as colunas que serão usadas para o calculo
     *
     * @param stdClass $oLinha Instancia da linha
     * @param null $iColuna
     * @return array retorna um array com as linhas
     * @deprecated
     * @see getColunasPorLinha
     */
    protected function processarColunasDaLinha(stdClass $oLinha, $iColuna = null)
    {
        return $this->getColunasPorLinha($oLinha, !empty($iColuna) ? array($iColuna) : array());
    }

    /**
     * Retorna os dados da coluna para serem utilizados.
     * @param stdClass $oLinha
     * @param array $aColunas
     * @return array
     */
    protected function getColunasPorLinha(stdClass $oLinha, array $aColunas = array())
    {
        $aColunasLinha = $oLinha->colunas;
        $aColunasProcessar = array();
        foreach ($aColunasLinha as $iOrdemColuna => $oColunaRelatorio) {
            if (!empty($aColunas) && !in_array($iOrdemColuna, $aColunas)) {
                continue;
            }

            if (!isset($oLinha->{$oColunaRelatorio->o115_nomecoluna})) {
                $oLinha->{$oColunaRelatorio->o115_nomecoluna} = 0;
            }

            $oColuna = new stdClass();
            $oColuna->nome = $oColunaRelatorio->o115_nomecoluna;
            $oColuna->formula = $oColunaRelatorio->o116_formula;
            $oColuna->analisada = false;

            if (property_exists($oColunaRelatorio, 'agrupar')) {
                $oColuna->agrupar = $oColunaRelatorio->agrupar;
            }
            $aColunasProcessar[] = $oColuna;
        }
        return $aColunasProcessar;
    }


    /**
     * Procurar formulas de colunas
     * @param $sFormula
     * @param $oColuna
     * @param $oLinha
     * @return mixed
     */
    protected function procurarFormulaColuna($sFormula, $oColuna, $oLinha)
    {
        if ($oColuna->analisada) {
            return $sFormula;
        }

        $aPalavras = str_word_count($sFormula, 2, '1234567890');
        $sFormulaOriginal = $sFormula;
        foreach ($aPalavras as $iInicio => $sPalavra) {
            $sLetraAnterior = substr($sFormulaOriginal, $iInicio - 1, 1);
            if ($sLetraAnterior == '@') {
                foreach ($oLinha->colunas as $oColunaLinha) {
                    if (trim($sPalavra) == trim($oColunaLinha->o115_nomecoluna)) {
                        $sFormula = str_replace("@{$sPalavra} ", $oColunaLinha->o116_formula . " ", $sFormula);
                    }
                }
            }
        }

        $oColuna->formula = $sFormula;
        $oColuna->analisada = true;
        return $oColuna->formula;
    }

    /**
     * função para buscar os lançamentos de acordo com o documento informado
     * retorna o campo c70_valor somado
     * @param EventoContabil $oEventoContabil
     * @param DBDate $oDataInicial
     * @param DBDate $oDataFinal
     * @return float
     * @throws DBException
     */
    public static function getValorLancamentoPorDocumentoPeriodo(
        EventoContabil $oEventoContabil,
        DBDate $oDataInicial,
        DBDate $oDataFinal
    )
    {
        $iDocumento = $oEventoContabil->getCodigoDocumento();
        $iInstituicao = $oEventoContabil->getInstituicao();

        $dtInicial = $oDataInicial->getDate('Y-m-d');
        $dtFinal = $oDataFinal->getDate('Y-m-d');
        $oDaoConlancam = new cl_conlancam();
        $nValorTotal = 0;

        $sWhereValores = "     c71_coddoc = {$iDocumento}   ";
        $sWhereValores .= " and c02_instit = {$iInstituicao} ";
        $sWhereValores .= " and c70_data between '{$dtInicial}' and '{$dtFinal}' ";

        $sCampos = " coalesce(sum(c70_valor), 0) as valor_total ";

        $sSqlValores = $oDaoConlancam->sql_query_ValorLancamentoPorDocumentoPeriodo(
            null,
            $sCampos,
            null,
            $sWhereValores
        );
        $rsValorTotal = $oDaoConlancam->sql_record($sSqlValores);

        /**
         * Tratamos se nao deu erro na query
         */
        if ($oDaoConlancam->erro_status == "0") {
            throw new DBException("Erro ao buscar valor total: \n" . $oDaoConlancam->erro_msg);
        }

        /**
         * Se achou registro para os filtros reatribuimos o valor total
         */
        if ($oDaoConlancam->numrows > 0) {
            $nValorTotal = db_utils::fieldsMemory($rsValorTotal, 0)->valor_total;
        }

        return $nValorTotal;
    }

    /**
     * Retorna o Titulo do periodo de emissão do relatório
     * @return string
     */
    public function getTituloPeriodo()
    {
        $sNomeMesInicial = mb_strtoupper(db_mes($this->oPeriodo->getMesInicial()));
        $sNomeMesFinal = mb_strtoupper(db_mes($this->oPeriodo->getMesFinal()));

        $sNomePeriodo = str_replace(array(1, 2, 3, 4, 5, "º"), "", $this->oPeriodo->getDescricao());

        $sPeriodo = "JANEIRO À {$sNomeMesFinal}/{$this->iAnoUsu} {$sNomePeriodo}";
        $sPeriodo .= " {$sNomeMesInicial}-{$sNomeMesFinal}";
        return $sPeriodo;
    }

    /**
     * remove as tabelas utilizadas para processamento do balancete de verificacao
     */
    protected function limparEstruturaBalanceteVerificacao()
    {
        db_query("DROP TABLE IF EXISTS work_pl");
        db_query("DROP TABLE IF EXISTS work_pl_estrut");
        db_query("DROP TABLE IF EXISTS work_pl_estrut");
        db_query("DROP TABLE IF EXISTS work_pl_estrutmae");
    }


    /**
     * Remove as tabelas utilizadas para processamento do balancete de despesa
     */
    protected function limparEstruturaBalanceteDespesa()
    {
        db_query("DROP TABLE IF EXISTS work_dotacao");
    }

    /**
     * Remove as tabelas utilizadas para processamento do balancete de receita
     */
    protected function limparEstruturaBalanceteReceita()
    {
        db_query("DROP TABLE IF EXISTS work_receita");
    }

    /**
     * @return DBDate
     */
    public function getDataInicialPeriodo()
    {
        return $this->oDataInicialPeriodo;
    }

    /**
     * @param DBDate $oDataInicialPeriodo
     */
    public function setDataInicialPeriodo(DBDate $oDataInicialPeriodo)
    {
        $this->oDataInicialPeriodo = $oDataInicialPeriodo;
    }

    /**
     * @return int
     */
    public function getAno()
    {
        return $this->iAnoUsu;
    }


    /**
     * @param RelatoriosLegaisBase $oRelatorio
     * @param array $aLinhasValidar
     * @param bool $lValidarExercicioAnterior
     *
     * @return Recurso[]
     */
    public static function getRecursosPendentesConfiguracao(
        RelatoriosLegaisBase $oRelatorio,
        array $aLinhasValidar,
        $lValidarExercicioAnterior = false
    )
    {
        $aRecursosNaoConfiguradosRetorno = array();
        $iAnoAnterior = ($oRelatorio->getDataInicial()->getAno() - 1);
        $oDataInicialAnterior = new DBDate("01/01/{$iAnoAnterior}");
        
        /**
         * tratamento da data final para exercicio anterior
         * ano atual é bissexto , o ultimo dia de fevereiro do ano anterior (que nao é bissexto) é 28
         */
        $diaDataFinal = $oRelatorio->getDataFinal()->getDia();
        if ( !date('L', mktime(0, 0, 0, 1, 1, $iAnoAnterior)) ) {
        
            if ($oRelatorio->getDataFinal()->getMes() == 02 && $diaDataFinal == 29) {
              $diaDataFinal = 28;
            }
        }
        $oDataFinalAnterior = new DBDate("{$diaDataFinal}/{$oRelatorio->getDataFinal()->getMes()}/{$iAnoAnterior}");

        $sWhereReceita = "o70_instit in ({$oRelatorio->getInstituicoes()})";
        $rsBalanceteReceita = db_receitasaldo(
            11,
            1,
            3,
            true,
            $sWhereReceita,
            $oRelatorio->getAno(),
            $oRelatorio->getDataInicial()->getDate(),
            $oRelatorio->getDataFinal()->getDate()
        );

        $sWhereDespesa = " o58_instit in({$oRelatorio->getInstituicoes()})";
        $rsBalanceteDespesa = db_dotacaosaldo(
            8,
            2,
            2,
            true,
            $sWhereDespesa,
            $oRelatorio->getAno(),
            $oRelatorio->getDataInicial()->getDate(),
            $oRelatorio->getDataFinal()->getDate()
        );

        $rsBalanceteReceitaAnoAnterior = null;
        $rsBalanceteDespesaAnoAnterior = null;
        if ($lValidarExercicioAnterior) {
            db_query("DROP TABLE IF EXISTS work_dotacao");
            db_query("DROP TABLE IF EXISTS work_receita");

            $rsBalanceteReceitaAnoAnterior = db_receitasaldo(
                11,
                1,
                3,
                true,
                $sWhereReceita,
                $oRelatorio->getAno(),
                $oDataInicialAnterior->getDate(),
                $oDataFinalAnterior->getDate()
            );

            $rsBalanceteDespesaAnoAnterior = db_dotacaosaldo(
                8,
                2,
                2,
                true,
                $sWhereDespesa,
                $oRelatorio->getAno(),
                $oDataInicialAnterior->getDate(),
                $oDataFinalAnterior->getDate()
            );
        }

        $aRecursos = array();

        /**
         * Pega os recursos das movimentações do exercício atual
         */
        for ($iRowCalculo = 0; $iRowCalculo < pg_num_rows($rsBalanceteReceita); $iRowCalculo++) {
            $aRecursos[] = pg_fetch_result($rsBalanceteReceita, $iRowCalculo, 'o70_codigo');
        }

        for ($iRowCalculo = 0; $iRowCalculo < pg_num_rows($rsBalanceteDespesa); $iRowCalculo++) {
            $aRecursos[] = pg_fetch_result($rsBalanceteDespesa, $iRowCalculo, 'o58_codigo');
        }

        /**
         * Pega os recursos das movimentações do exercício anterior
         */
        if ($lValidarExercicioAnterior) {
            for ($iRowCalculo = 0; $iRowCalculo < pg_num_rows($rsBalanceteReceitaAnoAnterior); $iRowCalculo++) {
                $aRecursos[] = pg_fetch_result($rsBalanceteReceitaAnoAnterior, $iRowCalculo, 'o70_codigo');
            }

            for ($iRowCalculo = 0; $iRowCalculo < pg_num_rows($rsBalanceteDespesaAnoAnterior); $iRowCalculo++) {
                $aRecursos[] = pg_fetch_result($rsBalanceteDespesaAnoAnterior, $iRowCalculo, 'o58_codigo');
            }
        }

        $aRecursos = array_unique($aRecursos);
        sort($aRecursos);

        /**
         * Pega os recursos da configuração
         */
        $aRecursosConfiguradosIn = array();
        $aRecursosConfiguradosNotIn = array();

        $aLinhasRelatorio = $oRelatorio->getLinhasRelatorio();

        foreach ($aLinhasValidar as $iLinhaRelatorio) {
            $pArrayToMerge =& $aRecursosConfiguradosIn;
            if (strtolower(trim($aLinhasRelatorio[$iLinhaRelatorio]->parametros->orcamento->recurso->operador)) != 'in') {
                $pArrayToMerge =& $aRecursosConfiguradosNotIn;
            }
            $pArrayToMerge = array_merge(
                $pArrayToMerge,
                $aLinhasRelatorio[$iLinhaRelatorio]->parametros->orcamento->recurso->valor
            );
        }

        $oDaoTipoRec = new cl_orctiporec();

        /**
         * Pega os recursos do tipo vinculado quando for "não contendo" na configuração
         */
        if (!empty($aRecursosConfiguradosNotIn)) {
            $sSqlTiporec = $oDaoTipoRec->sql_query_file(
                null,
                "o15_codigo",
                null,
                "o15_codigo > 0 and o15_codigo not in (" . implode(', ', $aRecursosConfiguradosNotIn) . ") and o15_tipo = 2"
            );
            $rsTiporec = $oDaoTipoRec->sql_record($sSqlTiporec);

            if ($rsTiporec && pg_num_rows($rsTiporec) > 0) {
                for ($iRowTiporec = 0; $iRowTiporec < pg_num_rows($rsTiporec); $iRowTiporec++) {
                    $aRecursosConfiguradosIn[] = db_utils::fieldsMemory($rsTiporec, $iRowTiporec)->o15_codigo;
                }
            }
        }

        $aRecursosNaoConfigurados = array_diff($aRecursos, array_unique($aRecursosConfiguradosIn));

        $recursosNaoConfigurados = \App\Domain\Financeiro\Orcamento\Models\FonteRecurso::query()
            ->whereIn('orctiporec_id', $aRecursosNaoConfigurados)
            ->where('exercicio', $oRelatorio->getAno())
            ->get();

        db_query("DROP TABLE IF EXISTS work_dotacao");
        db_query("DROP TABLE IF EXISTS work_receita");
        return $recursosNaoConfigurados;
    }

    /**
     * @return Periodo
     */
    public function getPeriodo()
    {
        if (empty($this->oPeriodo) && !empty($this->iCodigoPeriodo)) {
            $this->oPeriodo = new Periodo($this->iCodigoPeriodo);
        }
        return $this->oPeriodo;
    }

    /**
     * Deixa os valores arredondados as casas decimais desejadas, prontos para impressão
     * @param integer $iCasasDecimais
     */
    protected function arredondarValores($iCasasDecimais = 2)
    {
        foreach ($this->aLinhasConsistencia as $oStdLinha) {
            foreach ($oStdLinha->colunas as $oStdColuna) {
                $oStdLinha->{$oStdColuna->o115_nomecoluna} = round(
                    $oStdLinha->{$oStdColuna->o115_nomecoluna},
                    $iCasasDecimais
                );
            }
        }
    }

    /**
     * Método responsável por calcular os valores por coluna para exercicios diferentes da emissão do relatório.
     * @param integer $iColuna
     * @param DBDate $oDataInicial
     * @param DBDate $oDataFinal
     */
    protected function processarBalanceteVerificacaoParaColunaPorData(
        $iColuna,
        DBDate $oDataInicial,
        DBDate $oDataFinal
    )
    {
        $rsBalanceteVerificacao = db_planocontassaldo_matriz(
            $oDataInicial->getAno(),
            $oDataInicial->getDate(),
            $oDataFinal->getDate(),
            false,
            "c61_instit in ({$this->getInstituicoes()})",
            '',
            'true',
            'false'
        );

        foreach ($this->aLinhasProcessarVerificacao as $iLinha) {
            $oLinha = $this->aLinhasConsistencia[$iLinha];
            $aColunasProcessar = $this->getColunasPorLinha($oLinha, array($iColuna));
            $sNomeColunaLimpar = $aColunasProcessar[0]->nome;
            $oLinha->{$sNomeColunaLimpar} = 0;

            RelatoriosLegaisBase::calcularValorDaLinha(
                $rsBalanceteVerificacao,
                $oLinha,
                $aColunasProcessar,
                RelatoriosLegaisBase::TIPO_CALCULO_VERIFICACAO
            );
            $this->limparEstruturaBalanceteVerificacao();
            $this->processaValorManualPorLinhaEColuna($oLinha->ordem, $iColuna);
        }
    }

    /**
     * @param integer $linha
     * @param array $ordemColunas
     */
    protected function zerarValorLinhaColuna($linha, $ordemColunas)
    {
        foreach ($this->aLinhasConsistencia[$linha]->colunas as $coluna) {
            if (in_array($coluna->o116_ordem, $ordemColunas)) {
                $this->aLinhasConsistencia[$linha]->{$coluna->o115_nomecoluna} = 0;
            }
        }
    }

    /**
     * Retorna as linhas do relatorio
     */
    public function dumpLinhas()
    {

        foreach ($this->aLinhasConsistencia as $linha) {

            echo "(" . $linha->ordem . ") " . $linha->descricao . " Colunas (";
            foreach ($linha->colunas as $colunas) {

                echo "{$colunas->o115_nomecoluna}: {$linha->{$colunas->o115_nomecoluna}} ";
            }
            echo ")\n";
        }
    }

    /**
     * Retorna a fonte e nota explicatica do periodo
     * @return string
     * @throws Exception
     */
    public function getTextoNotaExplicativa()
    {

        $texto = '';
        $sSqlNotaPadrao = "select o42_notapadrao ";
        $sSqlNotaPadrao .= "  from orcparamrel ";
        $sSqlNotaPadrao .= " where o42_codparrel = {$this->iCodigoRelatorio}";

        $rsNotaPadrao = db_query($sSqlNotaPadrao);
        $oNotaPadrao = db_utils::fieldsMemory($rsNotaPadrao, 0);
        $iDepartamento = db_getsession("DB_coddepto");
        $oDepartamento = new DBDepartamento($iDepartamento);
        /*
         * nas notas explicativas, fonte, sera possivel colocar variaveis de seção se necessario
         * inicial teremos 3
         * [nome_departamento]
         * [data_emissao]
         * [hora_emissao]
        */
        $sDepartamento = $oDepartamento->getNomeDepartamento();
        $dtEmissao = date("d/m/Y", db_getsession("DB_datausu"));
        $hEmissao = date("H:i:s");
        $aParseVariaveis = array('[nome_departamento]' => $sDepartamento,
            '[data_emissao]' => $dtEmissao,
            '[hora_emissao]' => $hEmissao
        );

        if (isset($oNotaPadrao->o42_notapadrao) && trim($oNotaPadrao->o42_notapadrao) != "") {

            $sNotaPadrao = $oNotaPadrao->o42_notapadrao;
            foreach ($aParseVariaveis as $sIndiceValores => $oParseVariaveis) {

                if (str_replace($sIndiceValores, $oParseVariaveis, $sNotaPadrao)) {
                    $sNotaPadrao = str_replace($sIndiceValores, $oParseVariaveis, $sNotaPadrao);
                }
            }
            $texto .= $sNotaPadrao;
        }

        $sSqlNota = "select orcparamrelnota.*";
        $sSqlNota .= "  from orcparamrelnota  ";
        $sSqlNota .= "       inner join  orcparamrelnotaperiodo on o42_sequencial = o118_orcparamrelnota";
        $sSqlNota .= " where o42_codparrel = {$this->iCodigoRelatorio}";
        $sSqlNota .= "   and o42_anousu = " . db_getsession("DB_anousu");
        $sSqlNota .= "   and o42_instit = " . db_getsession("DB_instit");
        $sSqlNota .= "   and o118_periodo = {$this->getPeriodo()->getCodigo()}";
        $rsNota = db_query($sSqlNota);
        $oNotas = db_utils::fieldsMemory($rsNota, 0);
        /**
         * Seta os tamanhos das fontes setada na tabela orcparamrelnota se ela for maior que zero,
         * Para as Notas Explicativas
         */
        if (isset($oNotas->o42_fonte) && trim($oNotas->o42_fonte) != "") {

            $sFonte = "Fonte: " . $oNotas->o42_fonte;

            /*
             * aqui criamos o array com as variaveis que estarao disponiveis
             * percorremos ele, fazendo um parse pelos valores correto
             */

            $sDepartamento = $oDepartamento->getNomeDepartamento();
            $dtEmissao = date("d/m/Y", db_getsession("DB_datausu"));
            $hEmissao = date("H:i:s");
            $aParseVariaveis = array('[nome_departamento]' => $sDepartamento,
                '[data_emissao]' => $dtEmissao,
                '[hora_emissao]' => $hEmissao
            );
            foreach ($aParseVariaveis as $sIndiceValores => $oParseVariaveis) {
                if (str_replace($sIndiceValores, $oParseVariaveis, $sFonte)) {
                    $sFonte = str_replace($sIndiceValores, $oParseVariaveis, $sFonte);
                }
            }
            $texto .= "\n$sFonte";
        }

        if (isset($oNotas->o42_nota) && trim($oNotas->o42_nota) != "") {
            $sNotaExplicativa = "Nota Explicativa: " . $oNotas->o42_nota;
            if (!empty($texto)) {
                $sNotaExplicativa = "\n{$sNotaExplicativa}";
            }
            $texto .= $sNotaExplicativa;
        }
        return $texto;
    }


    /**
     *Retorna o template do relatorio
     */
    public function getTemplateRelatorio()
    {
        db_inicio_transacao();
        $daoTemplate = new \cl_orcparamreltemplate();
        $sql = $daoTemplate->sql_query_file(null, '*', "o163_orcparamrel = {$this->iCodigoRelatorio}");
        $rsRelatorio = db_query($sql);
        if (pg_num_rows($rsRelatorio) == 0) {
            return;
        }
        $codigoOid = \db_utils::fieldsMemory($rsRelatorio, 0)->o163_template;
        $nomeTemplate = 'tmp/template_relatorio_' . $this->iCodigoRelatorio . '.xlsx';
        DBLargeObject::leitura($codigoOid, $nomeTemplate);
        db_fim_transacao(false);
        return $nomeTemplate;
    }

    /**
     * retorna os dados da assinatura
     * @param string $tipo
     * @return array
     */
    public function getTextoAssinaturas($tipo = 'LRF')
    {
        $classinatura = new \cl_assinatura();
        $controle = "______________________________" . "\n" . "Controle Interno";
        $sec = "______________________________" . "\n" . "Secretaria da Fazenda";
        $cont = "______________________________" . "\n" . "Contadoria";
        $pref = "______________________________" . "\n" . "Prefeito";

        $assinaturaPrefeito = $classinatura->assinatura(1000, $pref);
        $assinaturaSecretario = $classinatura->assinatura(1002, $sec);
        $assinaturaContador = $classinatura->assinatura(1005, $cont);
        $assinaturaControle = $classinatura->assinatura(1009, $controle);
        return ["prefeito" => $assinaturaPrefeito,
            "secretario" => $assinaturaSecretario,
            "contador" => $assinaturaContador,
            "controle" => $assinaturaControle,
        ];
    }

    /**
     * Especializado metodo para buscar o valor do complemento
     * @param $complemento
     * @param $dataInicio
     * @param $dataFinal
     * @return int
     */
    protected function getValorComplemento($complemento, $dataInicio, $dataFinal)
    {
        $sql = "
        select coalesce (sum(valor_lancamento), 0)::float as valor
          from (select (select o201_complemento
                          from conlancamcomplementorecurso
                         where o201_codlan = c70_codlan
                       ) as complemento_fonte,
                       case when c53_tipo = 101 then round(c70_valor,2)*-1 else round(c70_valor,2) end
                       as valor_lancamento
                from conlancam
                         inner join conlancamrec on c70_codlan = c74_codlan
                         inner join conlancamdoc on c70_codlan = c71_codlan
                         inner join conhistdoc on c71_coddoc = c53_coddoc
                         inner join orcreceita on c74_codrec = o70_codrec and c74_anousu = o70_anousu
                         inner join orcfontes on o70_codfon = o57_codfon and o70_anousu = o57_anousu
                         inner join orctiporec on o15_codigo = o70_codigo
                where c70_data between '{$dataInicio}' and '{$dataFinal}'
                  and o70_instit in ($this->sListaInstit)
                  and c53_tipo in (100,101)
               ) as x
         where complemento_fonte = {$complemento};
        ";

        $rs = db_query($sql);
        if (pg_num_rows($rs) > 0) {
            return \db_utils::fieldsMemory($rs, 0)->valor;
        }
        return 0;
    }
}
