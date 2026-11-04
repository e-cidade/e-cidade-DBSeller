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
 * Classe para controle de dotacoes
 * Controla a dotacao e seus saldos durante o exercicio
 * @author Iuri Guntchnigg iuri@dbseller.com.br
 * @package orcamento
 * @version $Revision: 1.26 $
 */
class Dotacao
{

    /**
     * @var int
     */
    protected $iCodigo;
    /**
     * @var int
     */
    protected $iAnoUsu;
    /**
     * @var int
     */
    protected $iOrgao;
    /**
     * @var int
     */
    protected $iUnidade;
    /**
     * @var int
     */
    protected $iFuncao;
    /**
     * @var int
     */
    protected $iSubFuncao;
    /**
     * @var int
     */
    protected $iPrograma;
    /**
     * @var int
     */
    protected $iProjAtiv;
    /**
     * @var int
     */
    protected $iElemento;
    /**
     * @var int
     */
    protected $iRecurso;
    /**
     * @var int
     */
    protected $iLocalizador;
    /**
     * @var float
     */
    protected $nSaldoFinal;
    /**
     * @var float
     */
    protected $nSaldoAtual;
    /**
     * @var float
     */
    protected $nSaldoReservado;
    /**
     * @var float
     */
    protected $nSaldoEmpenhado;
    /**
     * @var float
     */
    protected $nSaldoAtualMenosReservado;
    /**
     * @var float
     */
    protected $nValor;
    /**
     * @var int
     */
    protected $iAutorizacao;
    /**
     * @var Recurso
     */
    protected $oDadosRecurso;
    /**
     * @var string
     */
    protected $sCaracteristicaPeculiar;
    /**
     * @type int
     */
    protected $iCodigoInstituicao;
    /**
     * @type Instituicao
     */
    protected $oInstituicao;
    /**
     * @var string
     */
    protected $sDescricaoOrgao;
    /**
     * @var string
     */
    protected $sDescricaoUnidade;
    /**
     * @var string
     */
    protected $sDescricaoFuncao;
    /**
     * @var string
     */
    protected $sDescricaoSubFuncao;
    /**
     * @var string
     */
    protected $sDescricaoPrograma;
    /**
     * @var string
     */
    protected $sDescricaoProjAtiv;
    /**
     * @var string
     */
    protected $sDescricaoRecurso;
    /**
     * @type string
     */
    protected $sDescricaoElemento;
    /**
     * @var boolean
     */
    private $lSaldoCalculado = false;
    /**
     * @type integer
     */
    private $iCodigoContaOrcamento;
    /**
     * @type ContaOrcamento
     */
    private $oContaOrcamento;
    /**
     * @type date
     */
    private $dataCriacao;
    /**
     * @type integer
     */
    private $esferaOrcamentaria;

    /**
     * Método construttor, Recebe com parametro o reduzido e o ano da dotacao
     * Define os dados básicos da dotacao
     * @param integer $iCodDot Código reduzido da dotacao (orcdotacao.o58_coddot)
     * @param integer $iAnoUsu Ano da dotacao (orcdotacao.o58_anousu)
     */
    function __construct($iCodDot = null, $iAnoUsu = null)
    {
        if (!empty($iCodDot) && !empty($iAnoUsu)) {
            $oDaoOrcdotacao = new cl_orcdotacao();
            $sCampos = "o58_coddot, o58_anousu, o58_orgao, o58_unidade, o58_funcao, o58_subfuncao, o58_programa, ";
            $sCampos .= "o58_projativ, o56_elemento, o56_codele, o58_codigo, o58_localizadorgastos, o58_instit, ";
            $sCampos .= "o58_concarpeculiar, o40_descr, o41_descr, o52_descr, o53_descr, o54_descr, o55_descr, ";
            $sCampos .= "o15_descr, o56_descr, o58_valor";
            $sSqlDotacao = $oDaoOrcdotacao->sql_query(
                null,
                null,
                "*",
                null,
                "o58_coddot = {$iCodDot} and o58_anousu = {$iAnoUsu}"
            );
            $rsDotacao = $oDaoOrcdotacao->sql_record($sSqlDotacao);

            if ($oDaoOrcdotacao->numrows > 0) {
                $oDotacao = db_utils::fieldsMemory($rsDotacao, 0);

                $this->iCodigo = $oDotacao->o58_coddot;
                $this->iAnoUsu = $oDotacao->o58_anousu;
                $this->iOrgao = $oDotacao->o58_orgao;
                $this->iUnidade = $oDotacao->o58_unidade;
                $this->iFuncao = $oDotacao->o58_funcao;
                $this->iSubFuncao = $oDotacao->o58_subfuncao;
                $this->iPrograma = $oDotacao->o58_programa;
                $this->iProjAtiv = $oDotacao->o58_projativ;
                $this->iElemento = $oDotacao->o56_elemento;
                $this->iCodigoContaOrcamento = $oDotacao->o56_codele;
                $this->iRecurso = $oDotacao->o58_codigo;
                $this->iLocalizador = $oDotacao->o58_localizadorgastos;
                $this->iCodigoInstituicao = $oDotacao->o58_instit;
                $this->sCaracteristicaPeculiar = $oDotacao->o58_concarpeculiar;

                $this->sDescricaoOrgao = $oDotacao->o40_descr;
                $this->sDescricaoUnidade = $oDotacao->o41_descr;
                $this->sDescricaoFuncao = $oDotacao->o52_descr;
                $this->sDescricaoSubFuncao = $oDotacao->o53_descr;
                $this->sDescricaoPrograma = $oDotacao->o54_descr;
                $this->sDescricaoProjAtiv = $oDotacao->o55_descr;
                $this->sDescricaoRecurso = $oDotacao->o15_descr;
                $this->sDescricaoElemento = $oDotacao->o56_descr;

                $this->nSaldoFinal = null;
                $this->nSaldoAtual = null;
                $this->nSaldoReservado = null;
                $this->nSaldoEmpenhado = null;
                $this->nValor = $oDotacao->o58_valor;
                $this->nSaldoAtualMenosReservado = null;
            }
        }
    }

    /**
     * Roda a função db_dotacaosaldo
     */
    private function carregarDadosSaldo()
    {

        if ($this->lSaldoCalculado) {
            return false;
        }

        require_once modification("libs/db_liborcamento.php");

        $rsDotacaoSaldo = db_dotacaosaldo(8, 2, 2, "true", "o58_coddot = {$this->iCodigo}", $this->iAnoUsu);
        $oDadosDotacao = db_utils::fieldsMemory($rsDotacaoSaldo, 0);

        $this->nSaldoFinal = $oDadosDotacao->atual_menos_reservado;
        $this->nSaldoAtual = $oDadosDotacao->atual;
        $this->nSaldoReservado = $oDadosDotacao->reservado;
        $this->nSaldoEmpenhado = $oDadosDotacao->empenhado_acumulado;
        $this->nSaldoAtualMenosReservado = ($oDadosDotacao->atual_menos_reservado);

        $this->lSaldoCalculado = true;
    }

    /**
     * Retorna o saldo reservado até data informada
     * @param DBDate $oDataFinal
     *
     * @return DotacaoSaldo
     * @throws Exception
     */
    public function getValorReservadoAteData(DBDate $oDataFinal)
    {

        if (empty($this->iCodigo) || empty($this->iAnoUsu)) {
            throw new Exception("O objeto não foi carregado.");
        }

        $nAutomatico = 0;
        $nManual = 0;
        $sWhere = "o80_anousu = {$this->iAnoUsu} and o80_coddot = {$this->iCodigo} and ";
        $sWhere .= "(o80_dtini <= '{$oDataFinal->getDate()}' and o80_dtfim >= '{$oDataFinal->getDate()}')";

        $oDaoReserva = new cl_orcreserva;
        $sCampos = 'orcreserva.o80_valor as valor, o80_dtini as data_inicial, o80_dtfim as data_final,';
        $sCampos .= 'case when orcreservager.o84_codres is null then false else true end as automatico';
        $sSqlReservas = $oDaoReserva->sql_query_simplificado_reservas($sCampos, null, $sWhere);
        $rsReservas = $oDaoReserva->sql_record($sSqlReservas);

        if ($oDaoReserva->numrows > 0) {
            for ($iIndice = 0; $iIndice < $oDaoReserva->numrows; $iIndice++) {
                $oStdReserva = db_utils::fieldsMemory($rsReservas, $iIndice);

                if ($oStdReserva->automatico == 't') {
                    $nAutomatico += $oStdReserva->valor;
                } else {
                    $nManual += $oStdReserva->valor;
                }
            }
        }

        $oSaldoReservado = new DotacaoSaldo;
        $oSaldoReservado->setValorReservadoAutomatico($nAutomatico);
        $oSaldoReservado->setValorReservadoManual($nManual);

        return $oSaldoReservado;
    }


    /**
     * metodo para retornar o objeto recurso
     * @return Recurso
     */
    public function getDadosRecurso()
    {

        if (empty($this->oDadosRecurso)) {
            $this->oDadosRecurso = RecursoRepository::getRecursoPorCodigo($this->getRecurso());
        }
        return $this->oDadosRecurso;
    }

    /**
     * Retorna o ano da Dotacao
     * Retorna o ano em que a dotacao foi definida
     * @return integer
     * @deprecated
     * @see getAno
     */
    public function getAnoUsu()
    {
        return $this->iAnoUsu;
    }

    /**
     * @param $codigo
     */
    public function setAno($ano)
    {
        $this->iAnoUsu = $ano;
    }

    /**
     * @return int
     */
    public function getOrgao()
    {
        return $this->iOrgao;
    }

    /**
     * @param $codigo
     */
    public function setOrgao($orgao)
    {
        $this->iOrgao = $orgao;
    }

    /**
     * @return int
     */
    public function getUnidade()
    {
        return $this->iUnidade;
    }

    /**
     * @param $codigo
     */
    public function setUnidade($unidade)
    {
        $this->iUnidadee = $unidade;
    }

    /**
     * @return int
     */
    public function getSubFuncao()
    {
        return $this->iSubFuncao;
    }

    /**
     * @param $subFuncao
     */
    public function setSubFuncao($subFuncao)
    {
        $this->iSubFuncao = $subFuncao;
    }

    /**
     * @return int
     */
    public function getProjAtiv()
    {
        return $this->iProjAtiv;
    }

    /**
     * @param $iProjAtiv
     */
    public function setProjAtiv($iProjAtiv)
    {
        $this->iProjAtiv = $iProjAtiv;
    }

    /**
     * @param int $iRecurso
     */
    public function setRecurso($iRecurso)
    {
        $this->iRecurso = $iRecurso;
    }

    /**
     * @return int
     */
    public function getFuncao()
    {
        return $this->iFuncao;
    }

    /**
     * @param codigo $iFuncao
     */
    public function setFuncao($iFuncao)
    {
        $this->iFuncao = $iFuncao;
    }

    /**
     * @return int
     */
    public function getPrograma()
    {
        return $this->iPrograma;
    }

    /**
     * @param int $iPrograma
     */
    public function setPrograma($iPrograma)
    {
        $this->iPrograma = $iPrograma;
    }

    /**
     * @return int
     */
    public function getElemento()
    {
        return $this->iElemento;
    }

    /**
     * @param int $iElemento
     */
    public function setElemento($iElemento)
    {
        $this->iElemento = $iElemento;
    }

    /**
     * Retorna o valor previsto para a dotacao
     * @return float
     */
    public function getValor()
    {
        return $this->nValor;
    }

    /**
     * Seta o Valor presvisto da dotacao
     * @param float $nValor
     */
    public function setValor($nValor)
    {
        $this->nValor = $nValor;
    }

    /**
     * @return int
     */
    public function getCodigoInstituicao()
    {
        return $this->iCodigoInstituicao;
    }

    /**
     * @param int $instituicao
     */
    public function setCodigoInstituicao($instituicao)
    {
        $this->iCodigoInstituicao = $instituicao;
    }

    /**
     * @return int
     */
    public function getLocalizador()
    {
        return $this->iLocalizador;
    }

    /**
     * @param int $iLocalizador
     */
    public function setLocalizador($iLocalizador)
    {
        $this->iLocalizador = $iLocalizador;
    }

    /**
     * @return string
     */
    public function getCaracteristicaPeculiar()
    {
        return $this->sCaracteristicaPeculiar;
    }

    /**
     * @param string $sCaracteristicaPeculiar
     */
    public function setCaracteristicaPeculiar($sCaracteristicaPeculiar)
    {
        $this->sCaracteristicaPeculiar = $sCaracteristicaPeculiar;
    }

    /**
     * @return mixed
     */
    public function getCodigo()
    {
        return $this->iCodigo;
    }

    /**
     * @param mixed $iCodigo
     */
    public function setCodigo($iCodigo)
    {
        $this->iCodigo = $iCodigo;
    }

    /**
     * @return date
     */
    public function getDataCriacao()
    {
        return $this->dataCriacao;
    }

    /**
     * @param date $dataCriacao
     */
    public function setDataCriacao($dataCriacao)
    {
        $this->dataCriacao = $dataCriacao;
    }

    /**
     * @return int
     */
    public function getEsferaOrcamentaria()
    {
        return $this->esferaOrcamentaria;
    }

    /**
     * @param int $esferaOrcamentaria
     */
    public function setEsferaOrcamentaria($esferaOrcamentaria)
    {
        $this->esferaOrcamentaria = $esferaOrcamentaria;
    }

    /**
     * @return int
     */
    public function getAno()
    {
        return $this->iAnoUsu;
    }

    /**
     * Retorna o recurso da dotacao
     * O Recurso da dotacao indica a origem da verba para o gasto.
     * Os dados do recurso sao da tabea orctiporec
     *
     * @see Esse campo é a PK da orctiporec e não representa mais o recurso
     *
     * @return integer codigo do recurso
     */
    public function getRecurso()
    {
        return $this->iRecurso;
    }

    /**
     * Retorna o saldo atual da dotacao
     * Retorna o saldo acumulado ate o dia da dotacao.
     * @return float
     */
    public function getSaldoAtual()
    {

        $this->carregarDadosSaldo();
        return $this->nSaldoAtual;
    }

    /**
     * Retorna o total empenhado
     * O valor retornado é o valor de todas os valores empenhados durante o ano.
     * @return float
     */
    public function getSaldoEmpenhado()
    {

        $this->carregarDadosSaldo();
        return $this->nSaldoEmpenhado;
    }

    /**
     * Retorna o saldo final da dotacao
     * @return Float
     */
    public function getSaldoFinal()
    {

        $this->carregarDadosSaldo();
        return $this->nSaldoFinal;
    }

    /**
     * Retorna o saldo reservado da dotacao
     * Retorna o valor de todas as reservas de saldo ativas para dotacao
     * @return float
     */
    public function getSaldoReservado()
    {

        $this->carregarDadosSaldo();
        return $this->nSaldoReservado;
    }

    /**
     * Retorna o saldo atual da dotacao;
     * O saldo Retornado e o saldo atual da dotacoa, menos o total de reservas de saldo
     * existentes para a dotacao
     * @return float;
     */
    public function getSaldoAtualMenosReservado()
    {

        $this->carregarDadosSaldo();
        return $this->nSaldoAtualMenosReservado;
    }

    /**
     * Retorna a Autorizacao
     * @return integer
     */
    public function getAutorizacao()
    {
        return $this->iAutorizacao;
    }

    /**
     * Seta a Autorizacao
     * @param integer $iAutorizacao
     */
    public function setAutorizacao($iAutorizacao)
    {
        $this->iAutorizacao = $iAutorizacao;
    }

    /**
     * @return string
     */
    public function getDescricaoElemento()
    {
        return $this->sDescricaoElemento;
    }

    /**
     * Valor previsto do orcamento de despesa
     * Retorna o valor previsto de todas as  dotacoes no ano $iAno e para a Instituicao $iInstituicao.
     * o Valor retornado é o valors previsto no ppa no ano anterior.
     * @param integer $iAno Ano do
     * @param integer $iInstituicao
     * @return number
     * @example
     * <code>
     *  $nValorPrevisto =  Dotacao::getValorPrevistoNoAno(2012, 1);
     * </code>
     *
     */
    public static function getValorPrevistoNoAno($iAno, $iInstituicao)
    {

        $nValorPrevisto = 0;
        $oDaoOrcDotacao = db_utils::getDao("orcdotacao");
        $sWhere = "o58_anousu = {$iAno} and o58_instit = {$iInstituicao}";
        $sSqlDotacao = $oDaoOrcDotacao->sql_query_file(
            null,
            null,
            "coalesce(sum(o58_valor), 0) as valor_total",
            null,
            $sWhere
        );
        $rsDotacao = $oDaoOrcDotacao->sql_record($sSqlDotacao);

        if ($oDaoOrcDotacao->numrows == 1) {
            $nValorPrevisto = db_utils::fieldsMemory($rsDotacao, 0)->valor_total;
        }

        return $nValorPrevisto;
    }

    /**
     * Valor previsto do orcamento de despesa
     * Retorna o valor previsto de todas as  dotacoes no ano $iAno e para a Instituicao $iInstituicao.
     * o Valor retornado é o valors previsto no ppa no ano anterior.
     *
     * @param integer $iAno Ano do
     * @param integer $iInstituicao
     * @return array
     */
    public static function getDotacoesNoAno($iAno, $iInstituicao)
    {

        $dotacoes = array();
        $oDaoOrcDotacao = db_utils::getDao("orcdotacao");
        $sWhere = "o58_anousu = {$iAno} and o58_instit = {$iInstituicao}";
        $sSqlDotacao = $oDaoOrcDotacao->sql_query_file(
            null,
            null,
            "o58_coddot, o58_anousu, o58_valor",
            null,
            $sWhere
        );
        $rsDotacao = $oDaoOrcDotacao->sql_record($sSqlDotacao);
        $dotacoes = db_utils::makeCollectionFromRecord($rsDotacao, function ($dados) {
            return $dados;
        });


        return $dotacoes;
    }

    /**
     * Retorna o estrutural da despesa
     * @return string
     * @example : 02.01.04.122.0004.2002.3319009000000.0001
     */
    public function getEstruturalDaDespesa()
    {
        /**
         * @todo testar
         */

        $aEstrutural = array();
        $aEstrutural[] = str_pad($this->iOrgao, 2, '0', STR_PAD_LEFT);
        $aEstrutural[] = str_pad($this->iUnidade, 2, '0', STR_PAD_LEFT);
        $aEstrutural[] = str_pad($this->iFuncao, 2, '0', STR_PAD_LEFT);
        $aEstrutural[] = str_pad($this->iSubFuncao, 3, '0', STR_PAD_LEFT);
        $aEstrutural[] = str_pad($this->iPrograma, 4, '0', STR_PAD_LEFT);
        $aEstrutural[] = str_pad($this->iProjAtiv, 4, '0', STR_PAD_LEFT);
        $aEstrutural[] = substr($this->iElemento, 0, 13);
        $aEstrutural[] = str_pad($this->getDadosRecurso()->getRecurso(), 4, '0', STR_PAD_LEFT);
        return implode('.', $aEstrutural);
    }


    /**
     * @return string
     */
    public function getDescricaoOrgao()
    {
        return $this->sDescricaoOrgao;
    }

    /**
     * @param string $sDescricaoOrgao
     */
    public function setDescricaoOrgao($sDescricaoOrgao)
    {
        $this->sDescricaoOrgao = $sDescricaoOrgao;
    }

    /**
     * @return string
     */
    public function getDescricaoUnidade()
    {
        return $this->sDescricaoUnidade;
    }

    /**
     * @param string $sDescricaoUnidade
     */
    public function setDescricaoUnidade($sDescricaoUnidade)
    {
        $this->sDescricaoUnidade = $sDescricaoUnidade;
    }

    /**
     * @return string
     */
    public function getDescricaoFuncao()
    {
        return $this->sDescricaoFuncao;
    }

    /**
     * @param string $sDescricaoFuncao
     */
    public function setDescricaoFuncao($sDescricaoFuncao)
    {
        $this->sDescricaoFuncao = $sDescricaoFuncao;
    }

    /**
     * @return string
     */
    public function getDescricaoSubFuncao()
    {
        return $this->sDescricaoSubFuncao;
    }

    /**
     * @param string $sDescricaoSubfuncao
     */
    public function setDescricaoSubFuncao($sDescricaoSubfuncao)
    {
        $this->sDescricaoSubFuncao = $sDescricaoSubfuncao;
    }

    /**
     * @return string
     */
    public function getDescricaoPrograma()
    {
        return $this->sDescricaoPrograma;
    }

    /**
     * @param string $sDescricaoPrograma
     */
    public function setDescricaoPrograma($sDescricaoPrograma)
    {
        $this->sDescricaoPrograma = $sDescricaoPrograma;
    }

    /**
     * @return string
     */
    public function getDescricaoProjAtiv()
    {
        return $this->sDescricaoProjAtiv;
    }

    /**
     * @param string $sDescricaoProjAtiv
     */
    public function setDescricaoProjAtiv($sDescricaoProjAtiv)
    {
        $this->sDescricaoProjAtiv = $sDescricaoProjAtiv;
    }

    /**
     * @return string
     */
    public function getDescricaoRecurso()
    {
        return $this->sDescricaoRecurso;
    }

    /**
     * @param string $sDescricaoRecurso
     */
    public function setDescricaoRecurso($sDescricaoRecurso)
    {
        $this->sDescricaoRecurso = $sDescricaoRecurso;
    }

    /**
     * @return ContaOrcamento
     */
    public function getContaOrcamentaria()
    {

        if (!empty($this->iCodigoContaOrcamento) && empty($this->oContaOrcamento)) {
            $this->oContaOrcamento = ContaOrcamentoRepository::getContaByCodigo($this->iCodigoContaOrcamento, $this->iAnoUsu);
        }
        return $this->oContaOrcamento;
    }

    /**
     * @return Instituicao
     */
    public function getInstituicao()
    {

        if (empty($this->oInstituicao) && !empty($this->iCodigoInstituicao)) {
            $this->oInstituicao = InstituicaoRepository::getInstituicaoByCodigo($this->iCodigoInstituicao);
        }
        return $this->oInstituicao;
    }

    /**
     * @param array $state
     * @return Dotacao
     */
    public static function fromState(array $state)
    {
        $dotacao = new self();

        if (array_key_exists('o58_anousu', $state)) {
            $dotacao->setAno((int)$state['o58_anousu']);
        }

        if (array_key_exists('o58_coddot', $state)) {
            $dotacao->setCodigo((int)$state['o58_coddot']);
        }

        if (array_key_exists('o58_orgao', $state)) {
            $dotacao->setOrgao((int)$state['o58_orgao']);
        }

        if (array_key_exists('o58_unidade', $state)) {
            $dotacao->setUnidade((int)$state['o58_unidade']);
        }

        if (array_key_exists('o58_subfuncao', $state)) {
            $dotacao->setSubFuncao((int)$state['o58_subfuncao']);
        }

        if (array_key_exists('o58_projativ', $state)) {
            $dotacao->setProjAtiv((int)$state['o58_projativ']);
        }

        if (array_key_exists('o58_funcao', $state)) {
            $dotacao->setFuncao((int)$state['o58_funcao']);
        }

        if (array_key_exists('o58_programa', $state)) {
            $dotacao->setPrograma((int)$state['o58_programa']);
        }

        if (array_key_exists('o58_codele', $state)) {
            $dotacao->setElemento((int)$state['o58_codele']);
        }

        if (array_key_exists('o58_valor', $state)) {
            $dotacao->setValor($state['o58_valor']);
        }

        if (array_key_exists('o58_instit', $state)) {
            $dotacao->setCodigoInstituicao((int)$state['o58_instit']);
        }

        if (array_key_exists('o58_localizadorgastos', $state)) {
            $dotacao->setLocalizador((int)$state['o58_localizadorgastos']);
        }

        if (array_key_exists('o58_datacriacao', $state)) {
            $dotacao->setDataCriacao($state['o58_datacriacao']);
        }

        if (array_key_exists('o58_concarpeculiar', $state)) {
            $dotacao->setCaracteristicaPeculiar($state['o58_concarpeculiar']);
        }

        if (array_key_exists('o58_esferaorcamentaria', $state)) {
            $dotacao->setEsferaOrcamentaria((int)$state['o58_esferaorcamentaria']);
        }

        return $dotacao;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $retorno = array(
            'o58_anousu' => $this->getAnoUsu(),
            'o58_coddot' => $this->getCodigo(),
            'o58_orgao' => $this->getOrgao(),
            'o58_unidade' => $this->getUnidade(),
            'o58_subfuncao' => $this->getSubFuncao(),
            'o58_projativ' => $this->getProjAtiv(),
            'o58_codigo' => $this->getRecurso(),
            'o58_funcao' => $this->getFuncao(),
            'o58_programa' => $this->getPrograma(),
            'o58_codele' => $this->getElemento(),
            'o58_valor' => $this->getValor(),
            'o58_instit' => $this->getCodigoInstituicao(),
            'o58_localizadorgastos' => $this->getLocalizador(),
            'o58_datacriacao' => $this->getDataCriacao(),
            'o58_concarpeculiar' => $this->getCaracteristicaPeculiar(),
            'o58_esferaorcamentaria' => $this->getEsferaOrcamentaria()
        );

        return $retorno;
    }
}
