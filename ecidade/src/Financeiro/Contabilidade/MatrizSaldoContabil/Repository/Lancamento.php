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

namespace ECidade\Financeiro\Contabilidade\MatrizSaldoContabil\Repository;

use BaseClassRepository;
use BusinessException;
use cl_conlancam;
use cl_conlancaminfocomplementarvalor;
use cl_conplanoatributolancamentos;
use cl_conplanoinfocomplementar;
use cl_infocomplementarvalor;
use db_utils;
use DBDate;
use DBEstrutura;
use DBException;
use DBRegistry;
use DBString;
use ECidade\Financeiro\Contabilidade\MatrizSaldoContabil\Model;
use ECidade\Financeiro\Contabilidade\MatrizSaldoContabil\Model\InformacaoComplementar;
use Exception;
use ParameterException;
use stdClass;
use tableDataManager;

/**
 * Class Lancamento
 *
 * @package Ecidade\Financeiro\Contabilidade\MatrizSaldoContabil\Repository
 * @author  Alysson Zanette <alysson.zanette@dbseller.com.br>
 */
class Lancamento extends BaseClassRepository
{
    const TIPO_SALDO_MSC = 1;

    const TIPO_SALDO_ECIDADE = 2;
    /**
     * Sobrescreve o atributro da classe pai para manter referencia atual
     *
     * @var Lancamento
     */
    protected static $oInstance;

    /**
     * @var bool
     */
    protected $persisteSaldoFinal = false;

    protected $lancamentos = array();


    /**
     * lista de atributos
     *
     * @var array
     */
    private $atributos = array();

    /**
     * sistema para processamento
     *
     * @var integer
     */
    private $sistema;

    /**
     * @var boolean
     */
    private $encerramento = false;

    /**
     * Lista de atributos para o processamento
     *
     * @var array
     */
    private $atributosParaProcessamento = array();

    /**
     * @var int
     */
    private $tipoSaldo = 1;

    /**
     * De para do atributo PO
     *
     * @var array
     */
    private $deParaPo = [];

    /**
     *  De para do atributo FR
     *
     * @var array
     */
    private $deParaRecursos = [];

    /**
     * De para do id do recurso para a fonte de recurso
     * @var array
     */
    private $deParaFonteRecursos = [];

    /**
     * Ano do processamento
     *
     * @var integer
     */
    private $ano;

    private $saldoImportado = false;


    /**
     * @param Model\Lancamento $oLancamento
     * @param tableDataManager $atributoLancamentoTable
     * @param tableDataManager $InfocomplementarTable
     *
     * @throws Exception
     */
    public function persistWithCopy(
        Model\Lancamento $oLancamento,
        tableDataManager $atributoLancamentoTable,
        tableDataManager $InfocomplementarTable
    ) {

        $oDaoAtributoLancamento = new stdClass();

        $oDaoAtributoLancamento->c124_lancamento = $oLancamento->getCodigoLancamento();
        $oDaoAtributoLancamento->c124_natureza = $oLancamento->getNatureza();
        $oDaoAtributoLancamento->c124_tipo = $oLancamento->getTipoLancamento();
        $oDaoAtributoLancamento->c124_valor = $oLancamento->getValor();
        $oDaoAtributoLancamento->c124_data = $oLancamento->getData()->getDate();
        $oDaoAtributoLancamento->c124_conplanosistema = $oLancamento->getSistema();

        $atributoLancamentoTable->setByLineOfDBUtils($oDaoAtributoLancamento);
        $sequencial = $atributoLancamentoTable->insertValue();

        $infoComplementareNaoObrigatorias = array(
            InformacaoComplementar::INFO_COMP_CODIGO_AI,
            InformacaoComplementar::INFO_COMP_CODIGO_ES,
            InformacaoComplementar::INFO_COMP_CODIGO_CF,
        );
        foreach ($oLancamento->getInfoComplementares() as $oInfocomplementar) {
            if ($oInfocomplementar->getValor() == ''
                && !in_array($oInfocomplementar->getCodigoInformacaoComplementar(), $infoComplementareNaoObrigatorias)
            ) {
                $lancamento = ", para o lançamento {$oLancamento->getCodigoLancamento()} ";
                if ($oLancamento->getCodigoLancamento() == '') {
                    $lancamento = " no saldo inicial ";
                }
                $mensagem = "O atributo {$oInfocomplementar->getSigla()}  para a conta reduzida ";
                $mensagem .= "{$oInfocomplementar->getContaReduzida()} {$lancamento}";
                $mensagem .= " não foi informado. O atributo para essa conta é obrigatório.";
                throw new Exception($mensagem);
            }

            $oDaoInfocomplementarValor = new stdClass();
            $oDaoInfocomplementarValor->c123_conplanoatributolancamentos = $sequencial;
            $oDaoInfocomplementarValor->c123_valor = "{$oInfocomplementar->getValor()}";
            $oDaoInfocomplementarValor->c123_reduzido = $oInfocomplementar->getContaReduzida();
            $oDaoInfocomplementarValor->c123_infocomplementar = $oInfocomplementar->getCodigoInformacaoComplementar();
            $oDaoInfocomplementarValor->c123_conplanosistema = $oInfocomplementar->getCodigoSistema();
            $InfocomplementarTable->setByLineOfDBUtils($oDaoInfocomplementarValor);
            $InfocomplementarTable->insertValue();
        }
    }

    /**
     * Persiste um Lancamento no banco de dados.
     *
     * @param Model\Lancamento $oLancamento
     *
     * @throws Exception
     */
    public function persistAtributoLancamentos(Model\Lancamento $oLancamento)
    {
        $oDaoAtributoLancamento = new cl_conplanoatributolancamentos();
        $sequencialLancamento = $oLancamento->getSequencial();

        $oDaoAtributoLancamento->c124_sequencial = $sequencialLancamento;

        $oDaoAtributoLancamento->c124_lancamento = $oLancamento->getCodigoLancamento();
        $oDaoAtributoLancamento->c124_natureza = $oLancamento->getNatureza();
        $oDaoAtributoLancamento->c124_tipo = $oLancamento->getTipoLancamento();
        $oDaoAtributoLancamento->c124_valor = $oLancamento->getValor();
        $oDaoAtributoLancamento->c124_conplanosistema = $oLancamento->getSistema();
        $oDaoAtributoLancamento->c124_data = $oLancamento->getData()->getDate();

        if (empty($sequencialLancamento)) {
            $oDaoAtributoLancamento->incluir(null);
            $oLancamento->setCodigoLancamento($oDaoAtributoLancamento->c124_sequencial);
        } else {
            $oDaoAtributoLancamento->alterar($oDaoAtributoLancamento->c124_sequencial);
        }
        if ($oDaoAtributoLancamento->erro_status == 0) {
            $mensagem = "Erro ao ao persistir dados do lançamento para {$oLancamento->getCodigoLancamento()}\n";
            $mensagem .= $oDaoAtributoLancamento->erro_msg;
            throw new Exception($mensagem);
        }

        foreach ($oLancamento->getInfoComplementares() as $oInfocomplementar) {
            $oDaoInfocomplementarValor = new cl_infocomplementarvalor();
            $oDaoInfocomplementarValor->c123_conplanoatributolancamentos = $oDaoAtributoLancamento->c124_sequencial;
            $oDaoInfocomplementarValor->c123_valor = $oInfocomplementar->getValor();
            $oDaoInfocomplementarValor->c123_reduzido = $oInfocomplementar->getContaReduzida();
            $oDaoInfocomplementarValor->c123_infocomplementar = $oInfocomplementar->getCodigoInformacaoComplementar();
            $oDaoInfocomplementarValor->c123_conplanosistema = $oInfocomplementar->getCodigoSistema();

            $oDaoInfocomplementarValor->incluir(null);
            if ($oDaoInfocomplementarValor->erro_status == 0) {
                $mensagem = "Erro ao ao persistir atributos  para lançamento para ";
                $mensagem .= "{$oLancamento->getCodigoLancamento()}\n{$oDaoInfocomplementarValor->erro_msg}";
                $mensagem .= "\nAtributo {$oInfocomplementar->getSigla()}";
                throw new Exception($mensagem);
            }
        }
    }

    /**
     * Cria um objeto do tipo Model\Lancamento
     *
     * @param stdClass $dados
     * @param array $infoComplementares
     * @param bool $atualizarValor
     *
     * @return Model\Lancamento|null
     * @throws ParameterException
     * @throws Exception
     */
    protected function build(stdClass $dados, array $infoComplementares = null)
    {
        if (empty($dados)) {
            return null;
        }

        $sequencialLancamento = null;

        if (isset($dados->sequencial)) {
            $sequencialLancamento = $dados->sequencial;
        }

        $oLancamento = new Model\Lancamento($sequencialLancamento);
        $oLancamento->setCodigoLancamento($dados->codigo_lancamento);
        $oLancamento->setNatureza($dados->natureza);
        $oLancamento->setTipoLancamento($dados->tipo);
        $oLancamento->setValor($dados->valor);
        $oLancamento->setSistema($dados->conta_corrente);

        if (isset($dados->data_lancamento)) {
            $oLancamento->setData(new DBDate($dados->data_lancamento));
        }

        if (!empty($infoComplementares)) {
            foreach ($infoComplementares as $infoComplementar) {
                $oInfoComplementar = new Model\InformacaoComplementar();
                $oInfoComplementar->setSigla($infoComplementar->sigla);
                $oInfoComplementar->setDescricao($infoComplementar->descricao);
                $oInfoComplementar->setConta($infoComplementar->conta);
                $oInfoComplementar->setAnousu($infoComplementar->anousu);
                $oInfoComplementar->setContaEstrutura($infoComplementar->estrutura);
                $oInfoComplementar->setCodigoLancamento($infoComplementar->codigoLancamento);
                $oInfoComplementar->setContaReduzida($infoComplementar->contaReduzida);
                $oInfoComplementar->setCodigoInstituicao($infoComplementar->codigoInstituicao);
                $oInfoComplementar->setCodigoInformacaoComplementar($infoComplementar->codigoInformacaoComplementar);
                $oInfoComplementar->setCodigoSistema($infoComplementar->codigoSistema);

                if (!empty($infoComplementar->codigoConplanoAtributos)) {
                    $oInfoComplementar->setCodigoConplanoAtributos($infoComplementar->codigoConplanoAtributos);
                }
                $oInfoComplementar->setValor($infoComplementar->valorLancamento);
                $oLancamento->addInfoComplementares($oInfoComplementar);
            }
        }

        return $oLancamento;
    }

    /**
     * Retorna um array de lancamentos
     *
     * @param $rsResult
     *
     * @return array
     * @throws DBException
     * @throws ParameterException
     * @throws Exception
     */
    public function makeCollection($rsResult)
    {
        $aCollection = array();

        if (is_object($rsResult)) {
            $aLancamentos[] = $rsResult;
        } else {
            $aLancamentos = db_utils::makeCollectionFromRecord($rsResult, function ($oLancamento) {
                return $oLancamento;
            });
        }

        foreach ($aLancamentos as $oLancamento) {
            if (!empty($oLancamento->infos_complementares)) {
                $aIdInfosComplementares = explode(',', $oLancamento->infos_complementares);
            }

            if (!empty($oLancamento->sequencial_conplanoatributos)) {
                $aSequencialConPlanoAtributos = explode(',', $oLancamento->sequencial_conplanoatributos);
            }

            $aInfoComplementar = array();

            if (!empty($aIdInfosComplementares) && !empty($aSequencialConPlanoAtributos)) {
                foreach ($aIdInfosComplementares as $index => $idInfocomplementar) {
                    $oDaoInfoComplementar = new cl_conplanoinfocomplementar();
                    $oStdInfoComplementar = DBRegistry::get("informacao_complementar_{$idInfocomplementar}");

                    if (empty($oStdInfoComplementar)) {
                        $oStdInfoComplementar = $oDaoInfoComplementar->findBydId($idInfocomplementar);
                        DBRegistry::add("informacao_complementar_{$idInfocomplementar}", $oStdInfoComplementar);
                    }

                    $oInfoComplementar = new stdClass();
                    $oInfoComplementar->sigla = $oStdInfoComplementar->c121_sigla;
                    $oInfoComplementar->descricao = $oStdInfoComplementar->c121_descricao;
                    $oInfoComplementar->conta = $oLancamento->conta;
                    $oInfoComplementar->anousu = $oLancamento->anousu;
                    $oInfoComplementar->estrutura = $oLancamento->estrutura;
                    $oInfoComplementar->codigoLancamento = $oLancamento->codigo_lancamento;
                    $oInfoComplementar->contaReduzida = $oLancamento->conta_reduzida;
                    $oInfoComplementar->codigoInstituicao = $oLancamento->instituicao;
                    $oInfoComplementar->codigoConplanoAtributos = $aSequencialConPlanoAtributos[$index];
                    $oInfoComplementar->codigoInformacaoComplementar = $idInfocomplementar;
                    $oInfoComplementar->codigoSistema = 1;
                    $oInfoComplementar->valorLancamento = $this->getValorAtributo($oLancamento, $oInfoComplementar);


                    $aInfoComplementar[] = $oInfoComplementar;
                }
            }

            $aCollection[] = $this->build((object)$oLancamento, $aInfoComplementar);
        }
        return $aCollection;
    }

    /**
     * @param $lancamento
     * @param $atributo
     *
     * @return null
     * @throws Exception
     */
    protected function getValorAtributo($lancamento, $atributo)
    {
        $valor = null;
        switch ($atributo->sigla) {
            case InformacaoComplementar::INFO_COMP_TIPO_PO:
                $valor = $lancamento->atributo_po;
                break;
            case InformacaoComplementar::INFO_COMP_TIPO_FP:
                $valor = $lancamento->atributo_fp;
                break;
            case InformacaoComplementar::INFO_COMP_TIPO_DC:
                $valor = $lancamento->atributo_dc;
                break;
            case InformacaoComplementar::INFO_COMP_TIPO_CF:
            case InformacaoComplementar::INFO_COMP_TIPO_CO:
                $valor = empty($lancamento->atributo_cf) ? '0000' : $lancamento->atributo_cf;
                $valor = str_pad($valor, 4, '0', STR_PAD_LEFT);
                break;
            case InformacaoComplementar::INFO_COMP_TIPO_FR:
                $valor = empty($lancamento->atributo_fr) ? '1' : $lancamento->atributo_fr;
                break;
            case InformacaoComplementar::INFO_COMP_TIPO_NR:
                $valor = empty($lancamento->atributo_nr) ? '19999921' : $lancamento->atributo_nr;
                break;
            case InformacaoComplementar::INFO_COMP_TIPO_ND:
                $valor = empty($lancamento->atributo_nd) ? '33909900' : $lancamento->atributo_nd;
                break;
            case InformacaoComplementar::INFO_COMP_TIPO_FS:
                $valorAtibuto = str_pad($lancamento->atributo_fs, 5, '0', STR_PAD_LEFT);
                $valor = empty($lancamento->atributo_fs) ? '04122' : $valorAtibuto;
                break;
            case InformacaoComplementar::INFO_COMP_TIPO_ES:
                $valor = empty($lancamento->atributo_es) ? '0' : $lancamento->atributo_es;
                break;
            case InformacaoComplementar::INFO_COMP_TIPO_AI:
                $valor = empty($lancamento->atributo_ai) ? $this->ano - 1 : $lancamento->atributo_ai;
                $lancamento->atributo_ai;
                break;
            default:
                throw new Exception('Informação complementar inválida.');
        }
        return $valor;
    }

    /**
     * Retorna em um array os lançamentos de uma dada competência e instituição.
     *
     * @param integer $mes
     * @param integer $ano
     * @param string $instituicoes
     *
     * @return array $aLancamentos
     * @throws DBException
     * @throws ParameterException
     */
    public function getLancamentosByCompetencia($mes, $ano, $instituicoes)
    {
        $sSqlLancamentos = $this->getQueryLancamentosPorCompetencia($mes, $ano, $instituicoes);

        $rsLancamentos = db_query($sSqlLancamentos);
        $aLancamentos = $this->makeCollection($rsLancamentos);
        return $aLancamentos;
    }


    /**
     * Retorna valores iniciais das contas.
     *
     * @param integer $mes
     * @param integer $ano
     * @param string $instituicoes
     *
     * @return array
     * @throws DBException
     * @throws ParameterException
     */
    public function getValoresConplanoExe($mes, $ano, $instituicoes)
    {
        $aCampos = array();
        $aWhere = array();
        $aWhere2 = array();

        $anoAnterior = $ano - 1;
        $mesAnterior = 12;
        $aCampos[0] = " c60_estrut estrutura,
                        c60_codcon as conta,
                        c60_anousu as anousu,
                         null as codigo_lancamento,
                        'D' as natureza,
                        c62_vlrdeb as valor,
                        '{$ano}-01-01' as data_lancamento,
                        c61_reduz as conta_reduzida,
                        c61_instit as instituicao,
                        1 as tipo,
                        1000 as tipodoc,
                        (select o15_codigo from orctiporec where o15_codigo = c61_codigo) as recurso,
                        (select o15_complemento
                           from orctiporec
                           join complementofonterecurso on o200_sequencial = o15_complemento
                          where o15_codigo = c61_codigo
                            and o200_msc is true
                        ) as complemento,
                        array_to_string(array_accum(c120_sequencial), ',') as sequencial_conplanoatributos,
                        array_to_string(array_accum(c120_infocomplementar), ',') as infos_complementares,
                        c120_conplanosistema as conta_corrente
                      ";
        $aCampos[1] = " c60_estrut estrutura,
                        c60_codcon as conta,
                        c60_anousu as anousu,
                         null as codigo_lancamento,
                        'C' as natureza,
                        c62_vlrcre as valor,
                        '{$ano}-01-01' as data_lancamento,
                        c61_reduz as conta_reduzida,
                        c61_instit as instituicao,
                        1 as tipo,
                        1000 as tipodoc,
                        (select o15_codigo from orctiporec where o15_codigo = c61_codigo) as recurso,
                        (select o15_complemento
                           from orctiporec
                           join complementofonterecurso on o200_sequencial = o15_complemento
                          where o15_codigo = c61_codigo
                            and o200_msc is true
                        ) as complemento,
                        array_to_string(array_accum(c120_sequencial), ',') as sequencial_conplanoatributos,
                        array_to_string(array_accum(c120_infocomplementar), ',') as infos_complementares,
                        c120_conplanosistema as conta_corrente
                       ";

        $aWhere[] = " c62_vlrdeb > 0 ";
        $aWhere[] = " c62_anousu = {$ano} ";


        $whereVerificacaoSaldo = "not exists(select 1 ";
        $whereVerificacaoSaldo .= "            from conplanoatributosaldo ";
        $whereVerificacaoSaldo .= "           where c125_hashcontaatributos ilike substring(c60_estrut, 1, 9)||'%'";
        $whereVerificacaoSaldo .= "             and c125_anousu = {$anoAnterior} ";
        $whereVerificacaoSaldo .= "             and c125_mesusu = {$mesAnterior} ";
        $whereVerificacaoSaldo .= "and c125_tiposaldo = 2 and c125_conplanosistema = 1 and (c60_estrut like '82111%'))";
        $aWhere[] = $whereVerificacaoSaldo;

        if (!empty($this->sistema)) {
            $aWhere[] = " c120_conplanosistema = {$this->sistema} ";
        }
        $aWhere[] = " c61_instit in ({$instituicoes}) group by 1,2,3,4,5,6,7,8,9,10,11,12,13, c120_conplanosistema";

        $aWhere2[] = " c62_vlrcre > 0 ";
        $aWhere2[] = " c62_anousu = {$ano} ";
        $aWhere2[] = $whereVerificacaoSaldo;
        if (!empty($this->sistema)) {
            $aWhere2[] = " c120_conplanosistema = {$this->sistema} ";
        }
        $aWhere2[] = " c61_instit in ({$instituicoes}) group by 1,2,3,4,5,6,7,8,9,10,11, 12,13, c120_conplanosistema ";

        $oDaoAtributoLancamento = new cl_conplanoatributolancamentos();
        $sSqlValroesConplanoEXe = $oDaoAtributoLancamento->sql_query_valores_conplanoexe(
            $aCampos[0],
            $aCampos[1],
            implode(' AND ', $aWhere),
            implode(' AND ', $aWhere2)
        );

//        die($sSqlValroesConplanoEXe);

        $sqlValoresConplanoExe = "select *,
                        (select (CASE WHEN cp.c60_codsis = 9 THEN 0 ELSE 1 END) AS infocomplementar_valor
                           from conplano cp
                          where cp.c60_codcon =  conta
                           and  c60_anousu = anousu
                        ) as atributo_dc,

                        recurso::varchar as atributo_fr,
                        complemento as atributo_cf,

                        '' as atributo_nd,
                        (SELECT codtrib::varchar AS infocomplementar_valor
                           FROM db_config WHERE codigo = instituicao) as atributo_po,
                        (SELECT case when c60_identificadorfinanceiro = 'N' then ''
                                     when c60_identificadorfinanceiro = 'F' then '1'
                                     when c60_identificadorfinanceiro = 'P' then '2'
                                    end as infocomplementar_valor
                          FROM conplanoreduz
                               INNER JOIN conplano ON c61_codcon = c60_codcon
                                                  AND c61_anousu = c60_anousu
                         WHERE c61_reduz = conta_reduzida
                           AND c61_anousu = anousu
                           AND c61_instit = instituicao limit 1) as atributo_fp,
                        null as atributo_fs,
                        null as atributo_nr,
                        '0' as atributo_es,
                        '' as atributo_ai
                        from ({$sSqlValroesConplanoEXe}) as x";
        $rsValroesConplanoExe = \db_query($sqlValoresConplanoExe);

        $aConplanoExe = $this->makeCollection($rsValroesConplanoExe);

        return $aConplanoExe;
    }

    /**
     * Retorna os lançamentos da matriz filtrando por competência e instituição.
     *
     * @param       $mes
     * @param       $ano
     * @param array $instituicoes
     * @param bool $emitirArquivo
     *
     * @return array|mixed
     * @throws Exception
     */
    public function getLancamentosMatrizByCompetenciaInstituicao(
        $mes,
        $ano,
        array $instituicoes,
        $emitirArquivo = false
    ) {
        $daoConplanoAtributoLancamentos = new cl_conplanoatributolancamentos();

        $codigoInstituicoes = array();
        foreach ($instituicoes as $instituicao) {
            $codigoInstituicoes[] = $instituicao->getCodigo();
        }

        $sqlLancamentos = $daoConplanoAtributoLancamentos->sql_query_lancamentos(
            $mes,
            $ano,
            implode(", ", $codigoInstituicoes),
            $this->encerramento
        );

        $rsLancamentos = db_query($sqlLancamentos);

        if (empty($rsLancamentos)) {
            throw new DBException("Erro ao consultar lançamentos processados.");
        }

        $sqlRestoPagar = "SELECT c75_codlan as codigo_lancamento
                            FROM contabilidade.conlancamemp
                          INNER JOIN empenho.empresto ON c75_numemp = e91_numemp
                          WHERE extract(MONTH FROM c75_data) = {$mes}
                            AND e91_anousu = {$ano};";

        $rsRestosPagar = db_query($sqlRestoPagar);

        $aLancamentosComRestoPagar = db_utils::makeCollectionFromRecord($rsRestosPagar, function ($objRestoPagar) {
            return $objRestoPagar->codigo_lancamento;
        });

        $instancia = $this;
        /**
         * Processamos os dois tipos
         */
        $lancamntosParaMatriz = array();
        for ($i = 1; $i <= 2; $i++) {
            $this->tipoSaldo = $i;
            pg_result_seek($rsLancamentos, 0);
            db_utils::makeCollectionFromRecord(
                $rsLancamentos,
                function ($dado) use (
                    $aLancamentosComRestoPagar,
                    $codigoInstituicoes,
                    $instancia,
                    $mes,
                    $ano,
                    $emitirArquivo
                ) {
                    $instancia->processarMovimentacaoContabil(
                        $dado,
                        $codigoInstituicoes,
                        $aLancamentosComRestoPagar,
                        $mes,
                        $ano,
                        $emitirArquivo
                    );
                }
            );

            $aLancamentos = array();
            foreach ($this->lancamentos as $lancamento) {
                $indice = implode('', array(
                    $lancamento->estrutura . "|",
                    implode('|', $lancamento->informacoesComplementares)
                ));

                $this->adicionarLancamento(
                    $aLancamentos[$indice],
                    $lancamento,
                    $lancamento->estrutura,
                    $lancamento->informacoesComplementares
                );
                unset($this->lancamentos[$lancamento->sequencial]);
            }

            $aLancamentos = $this->buscarValorInicial($aLancamentos, $mes, $ano);
            $aLancamentos = $this->calcularBalancoFinal($aLancamentos, $mes, $ano);

            ksort($aLancamentos);
            if ($i == self::TIPO_SALDO_MSC) {
                $lancamntosParaMatriz = $aLancamentos;
            }
        }

//        die('x');

        return $lancamntosParaMatriz;
    }

    /**
     * Calcula o saldo final das contas agrupando por sistema, e persiste e mesmo
     *
     * @param       $dado
     * @param array $codigoInstituicoes
     * @param array $aLancamentosComRestoPagar
     * @param       $mes
     * @param       $ano
     * @param bool $emissaoArquivo
     *
     * @throws BusinessException
     */
    protected function processarMovimentacaoContabil(
        $dado,
        array $codigoInstituicoes,
        array $aLancamentosComRestoPagar,
        $mes,
        $ano,
        $emissaoArquivo = false
    ) {
        /**
         * Cria um De/Para da fonte de recurso por ano.
         */
        $deParaFR = $this->deParaRecursos;
        $deParaPO = $this->deParaPo;

        if (empty($this->lancamentos[$dado->sequencial])) {
            $lancamento = clone $dado;
            $estrutural = DBEstrutura::mascararString('000000000', $lancamento->estrutura);
            if ($this->tipoSaldo == self::TIPO_SALDO_MSC) {
                $estrutural = DBEstrutura::mascararString('000000000', $lancamento->estrutura_pcasp);
            }
            $lancamento->estrutura = $estrutural;
            $lancamento->informacoesComplementares = [];
            $this->lancamentos[$dado->sequencial] = $lancamento;
        }

        $lancamento = $this->lancamentos[$dado->sequencial];
        $valor = $dado->valor;
        if ($dado->sigla == Model\InformacaoComplementar::INFO_COMP_TIPO_FR && !empty($deParaFR[$valor])
            && $this->tipoSaldo == self::TIPO_SALDO_MSC) {
            $valor = $deParaFR[$valor];

            if (in_array($dado->codigo_lancamento, $aLancamentosComRestoPagar)) {
                $valor = substr_replace($valor, '2', 0, 1);
            }
        }

        if ($dado->sigla == Model\InformacaoComplementar::INFO_COMP_TIPO_FR
            && !empty($this->deParaFonteRecursos[$valor])
            && $this->tipoSaldo == self::TIPO_SALDO_ECIDADE) {
            $valor = $this->deParaFonteRecursos[$valor];
        }

        $tipos = [Model\InformacaoComplementar::INFO_COMP_TIPO_ND, Model\InformacaoComplementar::INFO_COMP_TIPO_NR];
        if (in_array($dado->sigla, $tipos) && $this->tipoSaldo == self::TIPO_SALDO_MSC) {
            /**
             * Quando a receita for de dedução, devemos inverter o valor para deduzir do grupo.
             */
            if ($dado->sigla == Model\InformacaoComplementar::INFO_COMP_TIPO_NR && $valor{1} == 9) {
                $lancamento->valor = $lancamento->valor * -1;
            }
            // removido substr pois deve ser realizado o mapeamento com o plano do governo
            $valor = DBEstrutura::mascararString('00000000', $valor);
        }

        /* os atributos abaixo não são enviados para MSC caso o valor encontrado esteja vazio */
        $atributosParaIgnorar = array(
            Model\InformacaoComplementar::INFO_COMP_TIPO_ES,
            Model\InformacaoComplementar::INFO_COMP_TIPO_CF,
            Model\InformacaoComplementar::INFO_COMP_TIPO_CO,
        );
        if (in_array($dado->sigla, $atributosParaIgnorar) && $this->tipoSaldo == self::TIPO_SALDO_MSC
            && (empty($valor) or $valor == '0000')) {
            return;
        }

        // quando o atributo DC for 0 (zero) não deve ser enviado na matriz
        if ($this->tipoSaldo == self::TIPO_SALDO_MSC
            && $dado->sigla === Model\InformacaoComplementar::INFO_COMP_TIPO_DC
            && $valor === '0') {
            return;
        }
        if ((string)$dado->sigla === Model\InformacaoComplementar::INFO_COMP_TIPO_PO
            && $this->tipoSaldo == self::TIPO_SALDO_MSC) {
            if (!empty($deParaPO[$valor])) {
                $valor = $deParaPO[$valor];
            }
        }

        $lancamento->informacoesComplementares[] = "{$valor}#{$dado->sigla}";
    }

    /**
     * Remove os lançamentos
     *
     * @param       $mes
     * @param       $ano
     * @param array $instituicoes
     * @param array $tipoDocumentos
     *
     * @return bool
     * @throws Exception
     */
    public function removerLancamentosCompetencia(
        $mes,
        $ano,
        array $instituicoes,
        $sistemaContaCorrente = 1,
        $tipoDocumentos = array()
    ) {
        $dao = new cl_conplanoatributolancamentos();
        return $dao->removerLancametosCompetencia(
            $mes,
            $ano,
            $instituicoes,
            $sistemaContaCorrente,
            null,
            $tipoDocumentos
        );
    }

    /**
     * Insere dados na conplanoatributosaldo
     *
     * @param $mes
     * @param $ano
     * @param $hashContaAtributos
     * @param $valor
     * @param $natureza
     * @param $tipo
     *
     * @return bool
     * @throws Exception
     */
    public function inserirSaldoConplanoAtributo(
        $mes,
        $ano,
        $hashContaAtributos,
        $valor,
        $natureza,
        $tipo,
        $sistema = 1,
        $tipoSaldo = 1
    ) {

        if ($mes == 12 && $this->encerramento) {
            $mes = 13;
        }

        $dao = new cl_conplanoatributolancamentos();
        return $dao->inserirSaldoContaAtributo(
            $mes,
            $ano,
            $hashContaAtributos,
            $valor,
            $natureza,
            $tipo,
            $sistema,
            $tipoSaldo
        );
    }

    /**
     * Retorna quantidade de contas inconsistentes.
     *
     * @param integer $ano
     *
     * @return integer
     */
    public function getQuantidadeContasInconsistentes($ano)
    {
        $daoConplanoAtributoLancamentos = new cl_conplanoatributolancamentos();

        $sSql = $daoConplanoAtributoLancamentos->sql_query_contas_inconsistentes($ano);

        $result = db_query($sSql);

        return db_utils::makeFromRecord($result, function ($item) {
            return $item->quantidade;
        });
    }

    /**
     * @param Model\Lancamento $lancamento
     *
     * @throws DBException
     */
    public function remove(Model\Lancamento $lancamento)
    {
        $daoInfoComplementarValor = new cl_infocomplementarvalor();
        $where = "c123_conplanoatributolancamentos = " . $lancamento->getSequencial();
        $resultado = $daoInfoComplementarValor->excluir(null, $where);

        if (!$resultado) {
            $mensagem = "Erro ao deletar informações complementares da matriz referente ao lançamento ";
            $mensagem .= $lancamento->getCodigoLancamento();
            throw new DBException($mensagem);
        }

        $daoConplanoAtributoLancamentos = new cl_conplanoatributolancamentos();
        $resultado = $daoConplanoAtributoLancamentos->excluir($lancamento->getSequencial());

        if (!$resultado) {
            $mensagem = "Erro ao deletar registros da matriz referente ao lancamento ";
            $mensagem .= $lancamento->getCodigoLancamento();
            throw new DBException($mensagem);
        }
    }

    /**
     * Monta a estrutura (begin_balance, period_change e ending_balance) dos lançamentos de acordo com sua
     * conta e informações complementares
     *
     * @param $aContasAtributos
     * @param $dadosAnterior
     * @param $estrutura
     * @param $hashInformacoesComplementares
     */
    private function adicionarLancamento(&$aContasAtributos, $dadosAnterior, $estrutura, $hashInformacoesComplementares)
    {
        if (empty($aContasAtributos['end'])) {
            $aContasAtributos['end'] = (object)array(
                'sequencial' => $dadosAnterior->sequencial,
                'estrutura' => $estrutura,
                'natureza' => 'D',
                'tipo' => 3,
                'conta' => $dadosAnterior->conta,
                'valor' => 0,
                'informacoesComplementares' => $hashInformacoesComplementares,
                'anousu' => $dadosAnterior->anousu
            );
        }

        if (empty($aContasAtributos['begin'])) {
            $aContasAtributos['begin'] = (object)array(
                'sequencial' => $dadosAnterior->sequencial,
                'estrutura' => $estrutura,
                'natureza' => $dadosAnterior->natureza,
                'tipo' => 1,
                'conta' => $dadosAnterior->conta,
                'valor' => 0,
                'informacoesComplementares' => $hashInformacoesComplementares,
                'anousu' => $dadosAnterior->anousu
            );
        }

        switch ($dadosAnterior->tipo) {
            case 1:
                $saldoInicial = $dadosAnterior->natureza == 'C' ? ($dadosAnterior->valor_lancamento * -1)
                    : $dadosAnterior->valor_lancamento;
                /**
                 * Quando existir saldo importado, devemos ignorar o valor do saldo inicial  (dados da conplanoexe)
                 */
                if ($this->isSaldoImportado() && $this->tipoSaldo == 1) {
                    $saldoInicial = 0;
                }

                $aContasAtributos['begin']->valor += round($saldoInicial, 2);
                $natureza = "D";
                if ($aContasAtributos['begin']->valor < 0) {
                    $natureza = "C";
                }
                $aContasAtributos['begin']->natureza = $natureza;
                break;
            case 2:
                if (empty($aContasAtributos['periods'][$dadosAnterior->natureza])) {
                    $aContasAtributos['periods'][$dadosAnterior->natureza] = (object)array(
                        'sequencial' => $dadosAnterior->sequencial,
                        'estrutura' => $estrutura,
                        'natureza' => $dadosAnterior->natureza,
                        'tipo' => $dadosAnterior->tipo,
                        'conta' => $dadosAnterior->conta,
                        'valor' => 0,
                        'informacoesComplementares' => $hashInformacoesComplementares,
                        'anousu' => $dadosAnterior->anousu,
                    );
                }

                $aContasAtributos['periods'][$dadosAnterior->natureza]->valor += round(
                    $dadosAnterior->valor_lancamento,
                    2
                );

                break;
        }
    }

    /**
     * Calcula o valor do saldo final da conta para o ending_balance e salva-o na conplanoatributosaldo
     *
     * @param $aContasAtributos
     * @param $mes
     * @param $ano
     * @param int $sistema
     * @return mixed
     * @throws Exception
     */
    private function calcularBalancoFinal($aContasAtributos, $mes, $ano, $sistema = 1)
    {
        foreach ($aContasAtributos as $index => $itens) {
            $aContasAtributos[$index]['end']->valor = $itens['begin']->valor;
            $aContasAtributos[$index]['end']->natureza = 'D';

            if (!empty($itens['periods']['D'])) {
                $aContasAtributos[$index]['end']->valor += $itens['periods']['D']->valor;
            }

            if (!empty($itens['periods']['C'])) {
                $aContasAtributos[$index]['end']->valor -= $itens['periods']['C']->valor;
            }

            if ($aContasAtributos[$index]['end']->valor < 0) {
                $aContasAtributos[$index]['end']->natureza = 'C';
                $aContasAtributos[$index]['end']->valor *= -1;
            }

            $aContasAtributos[$index]['end']->valor = round($aContasAtributos[$index]['end']->valor, 2);

            if ($this->persisteSaldoFinal()) {
                $this->inserirSaldoConplanoAtributo(
                    $mes,
                    $ano,
                    $index,
                    $aContasAtributos[$index]['end']->valor,
                    $aContasAtributos[$index]['end']->natureza,
                    3,
                    $sistema,
                    $this->tipoSaldo
                );
            }
        }
        return $aContasAtributos;
    }

    /**
     * Normaliza o valor inicial caso haja valores finais na competência anterior
     *
     * @param $aContasAtributos
     * @param $mes
     * @param $ano
     *
     * @return mixed
     * @throws DBException
     * @throws ParameterException
     */
    public function buscarValorInicial($aContasAtributos, $mes, $ano, array $saldosAnteriores = null)
    {
        // tratamento para a conta 82111 apena no inicio do exercício de 2022 no mês de Janeiro 1
        $condicaoEspecial = false;
        if ($this->tipoSaldo == self::TIPO_SALDO_MSC && $ano == 2022 && $mes == 1) {
            $condicaoEspecial = true;
        }

        $listaContasEncerramento = ['1', '2', '3', '4', '5', '6', '7', '8'];
        $listaContasEncerramentoExececao = ['82111'];
        $aSaldos = $saldosAnteriores;
        if (empty($aSaldos)) {
            $aSaldos = $this->getSaldosDoMesAnterior($mes, $ano, $this->tipoSaldo);
        }

        $saldosAgrupados = [];
        foreach ($aSaldos as $index => &$saldo) {
            $partesAtributos = explode('|', $saldo->c125_hashcontaatributos);
            $conta = $partesAtributos[0];
            $temEncerramento = DBString::string_contains_any_value($conta, $listaContasEncerramento);
            $temExececao = DBString::string_contains_any_value($conta, $listaContasEncerramentoExececao);

            // condição válida apenas em janeiro de 2022
            if ($temExececao && $condicaoEspecial) {
                continue;
            }

            if (($temEncerramento && !$temExececao) && $mes == 1 && !$this->isSaldoImportado()) {
                $saldo->c125_valor = 0;
                unset($aSaldos[$index]);
                continue;
            }

            /**
             * aqui deve ser feito os de/para do siconfi
             */
            if ($this->tipoSaldo == self::TIPO_SALDO_MSC) {
                unset($partesAtributos[0]);
                foreach ($partesAtributos as $atributo) {
                    $dadosAtributo = explode('#', $atributo);
                    $dadosAtributo[0] = $this->transformarAtributosSiconfi($dadosAtributo[1], $dadosAtributo[0]);
                    $atributoAjustado = implode('#', $dadosAtributo);
                    $saldo->c125_hashcontaatributos = str_replace(
                        $atributo,
                        $atributoAjustado,
                        $saldo->c125_hashcontaatributos
                    );
                }
            }

            if (!empty($saldosAgrupados[$saldo->c125_hashcontaatributos])) {
                $saldoExistente = clone $saldosAgrupados[$saldo->c125_hashcontaatributos];
                $saldoExistente->c125_valor += $saldo->c125_valor;
                $saldosAgrupados[$saldo->c125_hashcontaatributos]->c125_valor = ($saldoExistente->c125_valor);
                $saldosAgrupados[$saldo->c125_hashcontaatributos]->c125_natureza = 'D';
                if ($saldoExistente->c125_valor < 0) {
                    $saldosAgrupados[$saldo->c125_hashcontaatributos]->c125_natureza = 'C';
                }
            } else {
                $saldosAgrupados[$saldo->c125_hashcontaatributos] = $saldo;
            }
            unset($aSaldos[$index]);
        }

        /**
         * dessa demarcação, até a próxima, todo código deve ser apagado após processar 2022
         * Mas, observar que em 2023, não deve mais apagar 2022
         */
        if ($condicaoEspecial) {
            $saldos = $this->getSaldoInicial82111();

            foreach ($saldos as $index => $saldo) {
                if ($saldo->c125_valor == 0 || !$this->validaInstituicao($saldo->c125_hashcontaatributos)) {
                    continue;
                }
                $partesAtributos = explode('|', $saldo->c125_hashcontaatributos);
                unset($partesAtributos[0]);
                foreach ($partesAtributos as $atributo) {
                    list($valor, $atributo) = explode('#', $atributo);
                    if ($atributo == Model\InformacaoComplementar::INFO_COMP_TIPO_FR) {
                        $siconfi = $this->buscaSiconfi2022($valor);
                        if (is_null($siconfi)) {
                            throw new Exception(
                                "Erro ao buscar saldo inicial da conta 82111. Hash: " . $saldo->c125_hashcontaatributos
                            );
                        }
                        $saldo->c125_hashcontaatributos = str_replace(
                            "$valor#$atributo",
                            "$siconfi#$atributo",
                            $saldo->c125_hashcontaatributos
                        );
                    }
                }

                if ($this->tipoSaldo == self::TIPO_SALDO_MSC) {
                    unset($partesAtributos[0]);
                    foreach ($partesAtributos as $atributo) {
                        $dadosAtributo = explode('#', $atributo);
                        $dadosAtributo[0] = $this->transformarAtributosSiconfi($dadosAtributo[1], $dadosAtributo[0]);
                        $atributoAjustado = implode('#', $dadosAtributo);
                        $saldo->c125_hashcontaatributos = str_replace(
                            $atributo,
                            $atributoAjustado,
                            $saldo->c125_hashcontaatributos
                        );
                    }
                }

                if (!empty($saldosAgrupados[$saldo->c125_hashcontaatributos])) {
                    $saldoExistente = clone $saldosAgrupados[$saldo->c125_hashcontaatributos];
                    $saldoExistente->c125_valor += $saldo->c125_valor;
                    $saldosAgrupados[$saldo->c125_hashcontaatributos]->c125_valor = ($saldoExistente->c125_valor);
                    $saldosAgrupados[$saldo->c125_hashcontaatributos]->c125_natureza = 'D';
                    if ($saldoExistente->c125_valor < 0) {
                        $saldosAgrupados[$saldo->c125_hashcontaatributos]->c125_natureza = 'C';
                    }
                } else {
                    $saldosAgrupados[$saldo->c125_hashcontaatributos] = $saldo;
                }
                unset($saldos[$index]);
            }
        }

        /**
         * apagar até aqui
         */


        foreach ($aContasAtributos as $hash => $item) {
            if (array_key_exists($hash, $saldosAgrupados)) {
                $item['begin']->valor = round($saldosAgrupados[$hash]->c125_valor, 2);
                $item['begin']->natureza = $saldosAgrupados[$hash]->c125_natureza;
                unset($saldosAgrupados[$hash]);
            }
        }

        if (!empty($saldosAgrupados)) {
            $aContasAtributos = $this->adicionarContasSemMovimentacao($saldosAgrupados, $aContasAtributos);
        }


        return $aContasAtributos;
    }

    /**
     * Retorna objeto contendo última competência processada.
     * Caso nenhuma competência tenha sido processada, retorna null.
     *
     * @return stdClass $oUltimaCompetenciaProcessada
     * @throws Exception
     */
    public static function getUltimaCompetenciaProcessada()
    {
        $sqlUltimaCompetenciaProcessada = "SELECT c125_anousu,
                                                  c125_mesusu
                                             FROM conplanoatributosaldo
                                            GROUP BY c125_anousu,
                                                     c125_mesusu
                                            ORDER BY c125_anousu DESC, c125_mesusu DESC LIMIT 1";
        $rsQueryResult = db_query($sqlUltimaCompetenciaProcessada);

        if (!$rsQueryResult) {
            throw new Exception("Erro ao pesquisar última competência processada");
        }

        if (pg_num_rows($rsQueryResult) < 1) {
            return null;
        }

        $oUltimaCompetenciaProcessada = new stdClass();
        $oUltimaCompetenciaProcessada->ano = db_utils::fieldsMemory($rsQueryResult, 0)->c125_anousu;
        $oUltimaCompetenciaProcessada->mes = db_utils::fieldsMemory($rsQueryResult, 0)->c125_mesusu;

        $oUltimaCompetenciaProcessada->stringCompetencia = "";
        $oUltimaCompetenciaProcessada->stringCompetencia .= str_pad(
            $oUltimaCompetenciaProcessada->mes,
            2,
            "0",
            STR_PAD_LEFT
        );
        $oUltimaCompetenciaProcessada->stringCompetencia .= "/";
        $oUltimaCompetenciaProcessada->stringCompetencia .= $oUltimaCompetenciaProcessada->ano;

        return $oUltimaCompetenciaProcessada;
    }

    /**
     * @param $aContasSaldo
     *
     * @return array $aContasAtributos
     */
    protected function adicionarContasSemMovimentacao($aSaldos, $aContasAtributos)
    {
        foreach ($aSaldos as $hash => $saldo) {
            $aAtributos = explode("|", $hash);

            $estrutura = $aAtributos[0];
            unset($aAtributos[0]);
            $aAtributos = array_values($aAtributos);

            $aContasAtributos[$hash]['begin'] = new stdClass();
            $aContasAtributos[$hash]['end'] = new stdClass();

            $aContasAtributos[$hash]['begin']->estrutura = $estrutura;
            $aContasAtributos[$hash]['begin']->valor = $saldo->c125_valor;
            $aContasAtributos[$hash]['begin']->natureza = $saldo->c125_natureza;
            $aContasAtributos[$hash]['begin']->informacoesComplementares = $aAtributos;
        }

        return $aContasAtributos;
    }

    /**
     * Ajusta os valores das informações complementares dos lançamentos caso haja configuração para eles
     *
     * @param array $aCodigosLancamentos
     *
     * @throws Exception
     */
    public function ajustarValorInformacaoComplementar($aCodigosLancamentos)
    {
        $daoLancamInfoComplementarValor = new cl_conlancaminfocomplementarvalor();
        $daoLancamInfoComplementarValor->ajustarValorInformacaoComplementar($aCodigosLancamentos);
    }

    /**
     * Retorna a consulta dos lançamentos no mês
     *
     * @param            $mes
     * @param            $ano
     * @param            $instituicoes
     *
     * @param array|null $codigoLancamentos
     * @param array $tipoDocumentos
     * @return string
     */
    public function getQueryLancamentosPorCompetencia(
        $mes,
        $ano,
        $instituicoes,
        array $codigoLancamentos = null,
        $tipoDocumentos = []
    ) {
        $aCampos = array();
        $aWhere = array();

        //$aCampos[0] = " conta as estrutura,
        $aCampos[0] = " c60_estrut as estrutura,
                        c60_codcon as conta,
                        c69_anousu as anousu,
                        c69_codlan as codigo_lancamento,
                        'D' as natureza,
                        c69_valor as valor,
                        c69_data as data_lancamento,
                        c69_debito as conta_reduzida,
                        c02_instit as instituicao,
                        2 as tipo,
                        c53_tipo as tipodoc,
                        array_to_string(array_accum(distinct c120_sequencial), ',') as sequencial_conplanoatributos,
                        array_to_string(array_accum(distinct c121_nomepropriedade), ',') as nome_infos_complementares,
                        array_to_string(array_accum(distinct c121_sigla), ',') as siglas_atributos,
                        array_to_string(array_accum(distinct c121_sequencial), ',') as infos_complementares,
                        c120_conplanosistema as conta_corrente,
                        c129_ordem as ordem
                      ";

        //        $aCampos[1] = " conta as estrutura,
        $aCampos[1] = " c60_estrut as estrutura,

                        c60_codcon as conta,
                        c69_anousu as anousu,
                        c69_codlan as codigo_lancamento,
                        'C' as natureza,
                        c69_valor as valor,
                        c69_data as data_lancamento,
                        c69_credito as conta_reduzida,
                        c02_instit as instituicao,
                        2 as tipo,
                        c53_tipo as tipodoc,
                        array_to_string(array_accum(distinct c120_sequencial), ',') as sequencial_conplanoatributos,
                        array_to_string(array_accum(distinct c121_nomepropriedade), ',') as nome_infos_complementares,
                        array_to_string(array_accum(distinct c121_sigla), ',') as siglas_atributos,
                        array_to_string(array_accum(distinct c121_sequencial), ',') as infos_complementares,
                        c120_conplanosistema as conta_corrente,
                        c129_ordem as ordem
                       ";

        if (is_array($codigoLancamentos) && count($codigoLancamentos) > 0) {
            $aWhere[] = " c69_codlan in(" . implode(", ", $codigoLancamentos) . ") ";
        }
        $aWhere[] = " EXTRACT(MONTH FROM C69_data) = {$mes} ";
        $aWhere[] = " c69_anousu = {$ano} ";
        if (!empty($this->sistema)) {
            $aWhere[] = " c120_conplanosistema in( {$this->sistema} )";
        }

        if (!empty($tipoDocumentos)) {
            $tipos = implode(", ", $tipoDocumentos);
            $aWhere[] = " c53_tipo in( {$tipos} )";
        }

        $aWhere[] = " c02_instit in ({$instituicoes}) group by 1,2,3,4,5,6,7,8,9,10,11,c120_conplanosistema,c129_ordem";

        $ordem = "data_lancamento, estrutura, ordem";
        $oDaoAtributoLancamento = new cl_conlancam();
        $sSqlLancamentos = $oDaoAtributoLancamento->sql_query_lancamentos_by_competencia(
            $aCampos[0],
            $aCampos[1],
            implode(' AND ', $aWhere),
            $ordem
        );
        $camposAtributos = $this->getConsultasDosAtributos();

        $sSqlLancamentos = " select *, {$camposAtributos} from ($sSqlLancamentos) as x";

        return $sSqlLancamentos;
    }

    /**
     * @return bool
     */
    public function persisteSaldoFinal()
    {
        return $this->persisteSaldoFinal;
    }

    /**
     * @param bool $persisteSaldoFinal
     */
    public function setPersisteSaldoFinal($persisteSaldoFinal)
    {
        $this->persisteSaldoFinal = $persisteSaldoFinal;
    }

    /**
     * @return int
     */
    public function getSistema()
    {
        return $this->sistema;
    }

    /**
     * @param  $sistema
     */
    public function setSistema($sistema)
    {
        $this->sistema = $sistema;
    }


    /**
     * retorna os atributos configurados para calculo
     *
     * @return array
     */
    public function getAtributos()
    {
        if (count($this->atributos) > 0) {
            return $this->atributos;
        }
        $oDaoAtributos = new cl_conplanoinfocomplementar();
        $campos = "c121_sequencial as codigo, c121_sigla as sigla, c121_sql as sql, ";
        $campos .= "c121_nomepropriedade as nome_propriedade, c121_valorpadrao as valor_padrao, ";
        $campos .= "c121_descricao as descricao";
        $where = "c121_sql <> ''";
        if (count($this->getAtributosParaProcessamento()) > 0) {
            $where .= " and c121_sequencial in(" . implode(", ", $this->getAtributosParaProcessamento()) . ")";
        }
        $sqlAtributos = $oDaoAtributos->sql_query_file(null, $campos, "c121_sequencial", $where);
        $rsAtributos = db_query($sqlAtributos);
        $totalAtributos = pg_num_rows($rsAtributos);

        for ($i = 0; $i < $totalAtributos; $i++) {
            $dadosAtributos = db_utils::fieldsMemory($rsAtributos, $i);
            $this->atributos[$dadosAtributos->codigo] = $dadosAtributos;
        }

        return $this->atributos;
    }

    /**
     * Retorna a query com os campos dos atributos
     *
     * @return string
     */
    protected function getConsultasDosAtributos()
    {
        $atributos = $this->getAtributos();
        $atributosRetorno = array();
        foreach ($atributos as $atributo) {
            $atributosRetorno[] = " ({$atributo->sql}) as {$atributo->nome_propriedade} ";
        }
        return implode(", ", $atributosRetorno);
    }

    /**
     * Retorna a consulta dos lancamentos no mês
     *
     * @param            $mes
     * @param            $ano
     * @param            $instituicoes
     *
     * @param array|null $codigoLancamentos
     *
     * @return string
     */
    public function getQueryLancamentosPorCompetenciaPorContaCorrente(
        $mes,
        $ano,
        $instituicoes,
        array $codigoLancamentos = null,
        array $contas = null
    ) {
        $aCampos = array();
        $aWhere = array();

        $tipo = '2';
        if ($this->encerramento) {
            $tipo = 1000;
        }

        $aCampos[0] = " distinct c60_estrut as estrutura,
                        c60_codcon as conta,
                        c69_anousu as anousu,
                        c69_codlan as codigo_lancamento,
                        'D' as natureza,
                        c69_valor as valor,
                        c69_data as data_lancamento,
                        c69_debito as conta_reduzida,
                        c02_instit as instituicao,
                        {$tipo} as tipo,
                        c53_tipo as tipodoc,
                        (select array_to_string(array_accum(c129_conplanoinfocomplementar), ',') from
                           (select c129_conplanoinfocomplementar
                              from conplanosistemaatributos
                              where c129_conplanosistema = c120_conplanosistema
                              order by c129_ordem
                             ) as x) as infos_complementares,
                        c120_conplanosistema as conta_corrente

                      ";
        $aCampos[1] = " distinct c60_estrut estrutura,
                        c60_codcon as conta,
                        c69_anousu as anousu,
                        c69_codlan as codigo_lancamento,
                        'C' as natureza,
                        c69_valor as valor,
                        c69_data as data_lancamento,
                        c69_credito as conta_reduzida,
                        c02_instit as instituicao,
                        {$tipo} as tipo,
                        c53_tipo as tipodoc,
                         (select array_to_string(array_accum(c129_conplanoinfocomplementar), ',') from
                           (select c129_conplanoinfocomplementar
                              from conplanosistemaatributos
                              where c129_conplanosistema = c120_conplanosistema
                              order by c129_ordem
                             ) as x) as infos_complementares,
                        c120_conplanosistema as conta_corrente
                       ";

        if (is_array($codigoLancamentos) && count($codigoLancamentos) > 0) {
            $aWhere[] = " c69_codlan in(" . implode(", ", $codigoLancamentos) . ") ";
        }
        if (is_array($contas) && count($contas) > 0) {
            $listaContas = implode(", ", $contas);
            $aWhere[] = " (c69_credito in({$listaContas}) or c69_debito in($listaContas)) ";
        }
        $aWhere[] = " EXTRACT(MONTH FROM C69_data) = {$mes} ";
        $aWhere[] = " c69_anousu = {$ano} ";
        if (!empty($this->sistema)) {
            $aWhere[] = " c120_conplanosistema in( {$this->sistema} )";
        }

        if ($this->encerramento) {
            $aWhere[] = ' c53_tipo = 1000 ';
        }

        $group = "group by c60_estrut, c60_codcon, c69_anousu, c69_codlan, natureza, c69_valor, c69_data, ";
        $group .= "c02_instit, tipo, conta_reduzida, c53_tipo, c120_conplanosistema";
        $aWhere[] = " c02_instit in ({$instituicoes}) {$group}";
        $ordem = "data_lancamento, estrutura";
        $oDaoAtributoLancamento = new cl_conlancam();
        $sSqlLancamentos = $oDaoAtributoLancamento->sql_query_lancamentos_by_competencia(
            $aCampos[0],
            $aCampos[1],
            implode(' AND ', $aWhere),
            $ordem
        );
        $camposAtributos = $this->getConsultasDosAtributos();

        $sSqlLancamentos = " select *, {$camposAtributos}
                               from ($sSqlLancamentos) as x";
        return $sSqlLancamentos;
    }


    /**
     * @param $encerramento
     */
    public function setEncerramento($encerramento)
    {
        $this->encerramento = $encerramento;
    }

    /**
     * @return bool
     */
    public function getEncerramento()
    {
        return $this->encerramento;
    }

    /**
     * @return array
     */
    public function getAtributosParaProcessamento()
    {
        return $this->atributosParaProcessamento;
    }

    /**
     * @param array $atributosParaProcessamento
     */
    public function setAtributosParaProcessamento($atributosParaProcessamento)
    {
        $this->atributosParaProcessamento = $atributosParaProcessamento;
    }


    /**
     * Realiza o De/Para do atributo para o Siconfi
     *
     * @param $atributo
     * @param $valor
     *
     * @return mixed
     */
    public function transformarAtributosSiconfi($atributo, $valor)
    {
        switch ($atributo) {
            case Model\InformacaoComplementar::INFO_COMP_TIPO_PO:
                if (!empty($this->deParaPo[$valor])) {
                    $valor = $this->deParaPo[$valor];
                }
                return $valor;

            case Model\InformacaoComplementar::INFO_COMP_TIPO_FR:
                // a FR já vem no padrão do siconfi ao olhar o saldo anterior
                return $valor;
        }
        return $valor;
    }


    /**
     * Define os dados de transformacao do Atributo P
     *
     * @param array $deParaPo
     */
    public function setDeParaPo(array $deParaPo)
    {
        $this->deParaPo = $deParaPo;
    }

    /**
     * Define os dados de transformacao do Atributo PO
     *
     * @param array $deParaPo
     */
    public function setDeParaRecursos(array $deParaRecursos)
    {
        $this->deParaRecursos = $deParaRecursos;
    }

    public function setDeParaFonteRecursos(array $dePara)
    {
        $this->deParaFonteRecursos = $dePara;
    }

    /**
     * Retorna os saldos da MSC no mes anterior
     *
     * @param $mes
     * @param $ano
     * @param $tipoSaldo
     *
     * @return array
     * @throws DBException
     * @throws ParameterException
     */
    protected function getSaldosDoMesAnterior($mes, $ano, $tipoSaldo)
    {
        $data = new DBDate("{$ano}-{$mes}-01");
        $data->modificarIntervalo("-1 month");

        $mesCalculo = $data->getMes();
        $anoCalculo = $data->getAno();
        /**
         * caso a matriz possua saldo inicial importado, devemos processar os saldo da importacao.
         */
        if ($this->tipoSaldo == 1 && $this->isSaldoImportado() && $mes == 1) {
            $mesCalculo = 0;
            $anoCalculo = $ano;
        }

        /**
         * Caso exista mês 13 processado (MSC de Encerramento) ele deve ler a tabela utilizando o mês 13.
         */
        if (!$this->isSaldoImportado() && $mes == 1) {
            $sqlVerificaSaldo = db_query("select c125_hashcontaatributos
                                   from conplanoatributosaldo
                                  where c125_mesusu = 13
                                    and c125_anousu = {$anoCalculo}
                                    and c125_tiposaldo = {$tipoSaldo}
                                    and c125_conplanosistema = 1 limit 1");
            if (pg_num_rows($sqlVerificaSaldo) > 0) {
                $mesCalculo = 13;
            }
        }

        if ($this->encerramento) {
            $mesCalculo = 12;
        }

        $sqlSaldo = " select c125_hashcontaatributos,
                             case when c125_natureza = 'C'
                                  then c125_valor * -1 else c125_valor
                              end as c125_valor, c125_natureza
                        from contabilidade.conplanoatributosaldo
                       where c125_mesusu = {$mesCalculo}
                         and c125_anousu = {$anoCalculo}
                         and c125_tiposaldo = {$tipoSaldo}
                         and c125_conplanosistema = 1 ";

        $rsSaldos = db_query($sqlSaldo);
        if (!$rsSaldos) {
            throw new DBException("Erro ao buscar os valores finais da competência anterior.");
        }

        $aSaldos = db_utils::makeCollectionFromRecord($rsSaldos, function ($saldoAnterior) {
            return $saldoAnterior;
        });
        return $aSaldos;
    }

    /**
     * Essa função é para usar apenas em janeiro de 2022
     */
    public function getSaldoInicial82111()
    {
        $sql = "
         SELECT c125_hashcontaatributos,
                CASE
                    WHEN c125_natureza = 'C' THEN c125_valor * -1
                    ELSE c125_valor
                END AS c125_valor,
                c125_natureza
         FROM contabilidade.conplanoatributosaldo
         WHERE c125_mesusu = 13
           AND c125_anousu = 2021
           AND c125_tiposaldo = 2
           AND c125_conplanosistema = 1
           and c125_hashcontaatributos like '82111%';
         ";
        $rsSaldos = db_query($sql);
        if (!$rsSaldos) {
            throw new DBException("Erro ao buscar os valores finais da competência anterior.");
        }

        return db_utils::makeCollectionFromRecord($rsSaldos, function ($saldoAnterior) {
            return $saldoAnterior;
        });
    }

    /**
     * @return int
     */
    public function getAno()
    {
        return $this->ano;
    }

    /**
     * @param int $ano
     */
    public function setAno($ano)
    {
        $this->ano = $ano;
    }

    /**
     * @return bool
     */
    public function isSaldoImportado()
    {
        return $this->saldoImportado;
    }

    /**
     * @param bool $saldoImportado
     */
    public function setSaldoImportado($saldoImportado)
    {
        $this->saldoImportado = $saldoImportado;
    }

    /**
     * @param $valor
     * @return string|null
     */
    private function buscaSiconfi2022($valor)
    {
        $where = ["o15_recurso = '$valor'"];
        // ajuste para quando o código do recurso esta salvo na tabela ao invés da fonte de recurso...
        // (isso é uma gambiarra para corrigir um erro de sistema)
        if (strlen($valor) < 4 || strlen($valor) > 4) {
            $where = ["(o15_recurso = '$valor' or o15_codigo = $valor) "];
        }

        $where[] = "exercicio = 2022";
        $where = implode(' and ', $where);
        $sql = "
        select codigo_siconfi from orcamento.fonterecurso
          join orcamento.orctiporec on orctiporec_id = o15_codigo
         where {$where} limit 1
        ";
        $rs = db_query($sql);
        if ($rs && pg_num_rows($rs) === 1) {
            return db_utils::fieldsMemory($rs, 0)->codigo_siconfi;
        }
        return null;
    }

    /**
     * Pelo hash dos atributos, valida se o atributo PO que seria a instituição esta presente no array de deParaPo
     * O array $this->deParaPo só tem os dados das instituições que fazem a matriz
     *
     * @param $hashContaAtributos
     * @return bool
     */
    private function validaInstituicao($hashContaAtributos)
    {
        $partesAtributos = explode('|', $hashContaAtributos);
        unset($partesAtributos[0]);
        foreach ($partesAtributos as $atributo) {
            list($valor, $atributo) = explode('#', $atributo);

            if ($atributo === Model\InformacaoComplementar::INFO_COMP_TIPO_PO
                && array_key_exists($valor, $this->deParaPo)) {
                return true;
            }
        }

        return false;
    }
}
