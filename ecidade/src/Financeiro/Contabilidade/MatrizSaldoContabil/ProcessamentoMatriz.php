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

namespace ECidade\Financeiro\Contabilidade\MatrizSaldoContabil;

use BusinessException;
use db_utils;
use DBException;
use ECidade\Financeiro\Contabilidade\Exportacao\Siconfi\Siconfi;
use ECidade\Financeiro\Contabilidade\MatrizSaldoContabil;
use ECidade\Financeiro\Contabilidade\MatrizSaldoContabil\Model\MatrizSaldoContabilLancamento;
use ECidade\Financeiro\Contabilidade\MatrizSaldoContabil\Repository\Lancamento;
use ECidade\Financeiro\Contabilidade\MatrizSaldoContabil\Repository\Lancamento as LancamentoRepository;
use ECidade\Financeiro\Contabilidade\MatrizSaldoContabil\Repository\MatrizSaldoContabilLancamentoRepositorio;
use ECidade\Financeiro\Contabilidade\MatrizSaldoContabil\Repository\MatrizSaldoContabilRepositorio;
use Exception;
use Instituicao;
use InstituicaoRepository;
use ParameterException;
use stdClass;
use tableDataManager;
use ZipArchive;

class ProcessamentoMatriz
{
    /**
     * @var int $mes
     */
    private $mes;

    /**
     * @var int $ano
     */
    private $ano;

    /**
     * @var Instituicao[] $instituicoes
     */
    private $instituicoes = array();

    /**
     * @var Lancamento
     */
    private $repository;

    private $persistirSaldoFinal = false;
    /**
     * @var int
     */
    private $tipoProcessamento;


    private $emissaoArquivo = false;
    /**
     * @var bool
     */
    private $temImportacao = false;

    /**
     * @var int
     */
    private $totalLinhasEcidade = 0;

    private $encerramento = false;

    /**
     * @param int $mes
     * @param int $ano
     * @param int $tipoProcessamento
     * @param null $encerramento
     * @throws ParameterException
     * @throws Exception
     */
    public function __construct($mes, $ano, $tipoProcessamento = 1, $encerramento = null)
    {
        if (empty($mes) || empty($ano)) {
            throw new ParameterException("Por favor, informe a competência a ser processada.");
        }

        $this->mes = $mes;
        $this->ano = $ano;

        if ($encerramento) {
            $this->encerramento = $encerramento;
            $this->validarEncerramento();
        }

        /**
         * adicionar setter para verifucar se existem algum saldo importado .
         * caso exista, n?o sera utilizado o saldo inicial do conplanoexe.
         */
        $this->repository = LancamentoRepository::getInstance();
        $this->repository->setSistema($tipoProcessamento);
        $this->repository->setEncerramento($encerramento);
        $this->repository->setAno($ano);
        $this->tipoProcessamento = $tipoProcessamento;
    }

    /**
     * @param Instituicao $instituicao
     */
    public function addInstituicao(Instituicao $instituicao)
    {
        $this->instituicoes[] = $instituicao;
    }

    /**
     * @return void
     * @throws ParameterException
     * @throws DBException
     *
     *
     * @throws Exception
     */
    public function processar()
    {
        if (empty($this->instituicoes)) {
            throw new ParameterException("Por favor, informe uma instituição para ter os lançamentos processados.");
        }

        $this->repository->setDeParaRecursos($this->getDeParaRecurso($this->ano));
        $this->repository->setDeParaFonteRecursos($this->getDeParaFonteRecurso($this->ano));

        $this->repository->setDeParaPo($this->getDeParaPO());
        $this->repository->setSaldoImportado($this->temSaldoImportado($this->ano));

        $this->processarLancamentos();
        $this->processarConplanoExe();
        $this->repository->setPersisteSaldoFinal(true);
        $this->repository->setSistema($this->tipoProcessamento);
        $lancamentos = $this->repository->getLancamentosMatrizByCompetenciaInstituicao(
            $this->mes,
            $this->ano,
            $this->instituicoes
        );

        $this->persistirMatrizSaldoContabilLancamentos($lancamentos);
    }

    /**
     * @throws Exception
     */
    public function excluirProcessamento()
    {
        if (empty($this->instituicoes)) {
            throw new ParameterException("Por favor, informe uma instituição para ter os lançamentos processados.");
        }

        $tipoDocumentos = array();
        if ($this->mes == 12 && $this->encerramento) {
            $tipoDocumentos = array(1000);
        }

        $this->repository->removerLancamentosCompetencia(
            $this->mes,
            $this->ano,
            $this->instituicoes,
            $this->tipoProcessamento,
            $tipoDocumentos
        );

        $matrizSaldoContabilRepositorio = new MatrizSaldoContabilRepositorio();
        $mes = empty($tipoDocumentos) ? $this->mes : 13;
        $matrizSaldoContabil = $matrizSaldoContabilRepositorio->scopeAno($this->ano)->scopeMes($mes)->first();

        if ($matrizSaldoContabil instanceof Model\MatrizSaldoContabil) {
            $matrizSaldoContabilLancamentoRepositorio = new MatrizSaldoContabilLancamentoRepositorio();
            $matrizSaldoContabilLancamentoRepositorio->scopeMatrizSaldoContabil($matrizSaldoContabil)->delete();
        }
    }

    /**
     * @param bool $emitirArquivo
     * @return string
     * @throws Exception
     */
    public function emitirMatriz($emitirArquivo = false)
    {
        if (empty($this->instituicoes)) {
            throw new ParameterException("Por favor, informe uma instituição para ter os lançamentos processados.");
        }

        $this->repository->setDeParaRecursos($this->getDeParaRecurso($this->ano));
        $this->repository->setDeParaPo($this->getDeParaPO());
        $this->repository->setPersisteSaldoFinal($this->persistirSaldoFinal());
        $aLancamentos = $this->repository->getLancamentosMatrizByCompetenciaInstituicao(
            $this->mes,
            $this->ano,
            $this->instituicoes,
            $emitirArquivo
        );

        $this->persistirMatrizSaldoContabilLancamentos($aLancamentos);

        if (empty($aLancamentos)) {
            throw new BusinessException("Nenhum lançamento encontrado para a competência informada.");
        }
        if (!$emitirArquivo) {
            return '';
        }


        $zipFilePath = 'tmp/SICONFI_saldos_contabeis_' . $this->ano . '_' . $this->mes . '.zip';

        $zipArchiveParameter = ZipArchive::CREATE;
        if (file_exists($zipFilePath)) {
            $zipArchiveParameter = ZipArchive::OVERWRITE;
        }

        $zipArchive = new ZipArchive();
        $zipArchive->open($zipFilePath, $zipArchiveParameter);


        $prefeitura = InstituicaoRepository::getInstituicaoPrefeitura();
        $codigoSiconfi = $prefeitura->getDadosPrefeitura()->getCodigoIbge() . "EX";

        $siConfi = new Siconfi();
        $siConfi->setCompetencia($this->ano . '-' . $this->mes);
        $siConfi->setCodigoSiconfi($codigoSiconfi);
        $siConfi->setEncerramento($this->encerramento);

        $dadosMatriz = array();
        foreach ($aLancamentos as $lancamento) {
            $matriz_modelo = $siConfi->getColunas();
            $matriz_modelo['CONTA'] = $lancamento['begin']->estrutura;
            foreach ($lancamento['begin']->informacoesComplementares as $key => $informacaoComplementar) {
                $key++;
                list($valor, $info) = explode('#', $informacaoComplementar);
                $matriz_modelo['IC' . $key] = $valor;
                $matriz_modelo['TIPO' . $key] = $info;
            }

            $matriz_begin = $matriz_modelo;
            $matriz_begin['Tipo_valor'] = 'beginning_balance';

            $lancamento['begin']->natureza = $lancamento['begin']->valor < 0 ? 'C' : 'D';
            $matriz_begin['Natureza_valor'] = $lancamento['begin']->natureza;
            $matriz_begin['Valor'] = round(abs($lancamento['begin']->valor), 2);

            $dadosMatriz[] = $matriz_begin;

            if (!empty($lancamento['periods']['C'])) {
                $matriz_period_change_credito = $matriz_modelo;
                $matriz_period_change_credito['Natureza_valor'] = 'C';
                $matriz_period_change_credito['Tipo_valor'] = 'period_change';
                $matriz_period_change_credito['Valor'] = round($lancamento['periods']['C']->valor, 2);
                $dadosMatriz[] = $matriz_period_change_credito;
            }

            if (!empty($lancamento['periods']['D'])) {
                $matriz_period_change_debito = $matriz_modelo;
                $matriz_period_change_debito['Natureza_valor'] = 'D';
                $matriz_period_change_debito['Tipo_valor'] = 'period_change';
                $matriz_period_change_debito['Valor'] = round($lancamento['periods']['D']->valor, 2);
                $dadosMatriz[] = $matriz_period_change_debito;
            }

            $matriz_end = $matriz_modelo;
            $matriz_end['Tipo_valor'] = 'ending_balance';
            $matriz_end['Natureza_valor'] = $lancamento['end']->natureza;
            $matriz_end['Valor'] = $lancamento['end']->valor;
            $dadosMatriz[] = $matriz_end;
        }

        $siConfi->setMatriz($dadosMatriz);

        $file = $siConfi->gerarArquivo('tmp/SICONFI Matriz de saldos Contabeis.csv');


        $mes = $this->mes;
        if ($mes == 12 && $this->encerramento) {
            $mes = 13;
        }
        $arquivos = MatrizSaldoContabil\ArquivoExterno\Importacao::getArquivosPorCompetencia($this->ano, $mes);
        foreach ($arquivos as $linha) {
            $conteudo_arquivo = $linha->conteudo_arquivo;
            if (!empty($conteudo_arquivo)) {
                file_put_contents($file, $conteudo_arquivo . "\n", FILE_APPEND);
                $this->temImportacao = true;
                $this->totalLinhasEcidade = count($dadosMatriz) + 2;
            }
        }

        $zipArchive->addFile($file);
        $zipArchive->close();

        return $zipFilePath;
    }

    /**
     * Retorna a quantidade de contas inconsistentes.
     *
     * @return int
     */
    public function getQuantidadeContasInconsistentes()
    {
        return $this->repository->getQuantidadeContasInconsistentes($this->ano);
    }

    /**
     * Processa os lançamentos da estrutura do e-cidade e persiste na conplanoatributolancamentos.
     * @param array|null $codigoLancamentos
     * @return bool
     * @throws Exception
     */
    protected function processarLancamentos(array $codigoLancamentos = null)
    {
        global $conn;

        // @todo - desabilitando auditoria via trigger
        $rsDesabilitarAuditoria = db_query("SELECT fc_putsession('__disable_audit__', 'on');");
        if (!$rsDesabilitarAuditoria) {
            throw new Exception("Erro ao desabilitar auditoria".pg_last_error());
        }

        $mes = $this->mes;
        $ano = $this->ano;
        $instituicoes = implode(', ', $this->getCodigoInstituicoes());

        $tableManagerLancamentoAtributo = new tableDataManager(
            $conn,
            "contabilidade.conplanoatributolancamentos",
            "c124_sequencial",
            false,
            100000000,
            "contabilidade.conplanoatributolancamentos_c124_sequencial_seq"
        );

        $tableManagerInfoComplementar = new tableDataManager(
            $conn,
            "contabilidade.infocomplementarvalor",
            "c123_sequencial",
            false,
            10000000,
            'contabilidade.infocomplementarvalor_c123_sequencial_seq'
        );

        $this->repository->setAtributosParaProcessamento(MatrizSaldoContabil::getAtributos($ano));

        $tipoDocumentos = [];
        if ($this->mes == 12 && $this->encerramento) {
            $tipoDocumentos = [1000];
        }
        $sSqlLancamentos = $this->repository->getQueryLancamentosPorCompetencia(
            $mes,
            $ano,
            $instituicoes,
            $codigoLancamentos,
            $tipoDocumentos
        );

        $rsLancamentos = db_query($sSqlLancamentos);

        if (!$rsLancamentos) {
            $msg = "Não foi possível consultar os lançamentos contábeis para processar a matriz.\n";
            $msg .= "Possíveis causas:\n";
            $msg .= "\t-Transações contábeis inconsistentes;\n";
            $msg .= "\t-Lançamentos em duplicidade.\n";
            throw new Exception($msg);
        }

        $instancia = $this;
        $i = 0;
        $codigosParaUpdate = array();

        db_utils::makeCollectionFromRecord(
            $rsLancamentos,
            function ($dados) use (
                $tableManagerInfoComplementar,
                $tableManagerLancamentoAtributo,
                $instancia,
                &$i,
                &$codigosParaUpdate
            ) {

                $lancamento = $instancia->getRepository()->makeCollection($dados);
                $instancia->getRepository()->persistWithCopy(
                    $lancamento[0],
                    $tableManagerLancamentoAtributo,
                    $tableManagerInfoComplementar
                );

                if ($i % 100000 == 0) {
                    $tableManagerLancamentoAtributo->persist();
                    $tableManagerInfoComplementar->persist();
                }

                $codigosParaUpdate[] = $lancamento[0]->getCodigoLancamento();
                $i++;
            }
        );

        $tableManagerLancamentoAtributo->persist();
        $tableManagerInfoComplementar->persist();
        $tableManagerLancamentoAtributo->persistSequenceValue();
        $tableManagerInfoComplementar->persistSequenceValue();

        $instancia->repository->ajustarValorInformacaoComplementar($codigosParaUpdate);
        return true;
    }

    /**
     * Retorna um array com os códigos das instituições
     * @return array
     */
    protected function getCodigoInstituicoes()
    {
        $aInstituicoes = array();

        if (!empty($this->instituicoes)) {
            foreach ($this->instituicoes as $oInstituicao) {
                $aInstituicoes[] = $oInstituicao->getCodigo();
            }
        }

        return $aInstituicoes;
    }

    /**
     * @return bool
     * @throws ParameterException
     * @throws DBException
     * @throws Exception
     */
    protected function processarConplanoExe()
    {
        $mes = $this->mes;
        $ano = $this->ano;
        $instituicoes = implode(', ', $this->getCodigoInstituicoes());

        $aConplanoExe = $this->repository->getValoresConplanoExe($mes, $ano, $instituicoes);

        foreach ($aConplanoExe as $oConplanoExe) {
            $this->repository->persistAtributoLancamentos($oConplanoExe);
        }

        return true;
    }

    /**
     * @throws Exception
     */
    private function validarEncerramento()
    {
        $sql = "
            select codigo,
                   nomeinst,
                   (select array_to_string(array_agg(distinct c42_encerramentotipo), ',') as tipos
                      from contabilidade.conencerramento
                    where c42_anousu = {$this->ano}
                      and c42_encerramentotipo IN (6, 7)
                      and c42_instit = c125_db_config)
            from contabilidade.configuracaoinstituicaosiconfi
            inner join configuracoes.db_config on codigo = c125_db_config
        ";

        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception('Não foi possível consultar o encerramento do exercício.');
        }

        $dados = pg_fetch_all($rs);

        $instituicoesErro = array();

        foreach ($dados as $dado) {
            $tipos = explode(',', $dado['tipos']);
            if (count($tipos) < 2) {
                $instituicoesErro[] = $dado['nomeinst'];
            }
        }

        if ($instituicoesErro) {
            $instituicoesErro = implode("\n", $instituicoesErro);
            $mensagem = "É necessário encerrar o exercício para emitir a matriz de encerramento.";
            $mensagem .= "\n\nInstituições não encerradas:\n{$instituicoesErro}\n\n";
            $mensagem .= "Acesse: DB:FINANCEIRO > Contabilidade > Procedimentos > Escrituração Contábil > ";
            $mensagem .= "Encerramento do Exercício > Encerramento de Exercício Contábil";
            throw new Exception($mensagem);
        }
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
        return LancamentoRepository::getUltimaCompetenciaProcessada();
    }

    /**
     * @return bool
     */
    public function persistirSaldoFinal()
    {
        return $this->persistirSaldoFinal;
    }

    /**
     * @param bool $persistirSaldoFinal
     */
    public function setPersistirSaldoFinal($persistirSaldoFinal)
    {
        $this->persistirSaldoFinal = $persistirSaldoFinal;
    }

    /**
     * @return LancamentoRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return bool
     */
    public function isEmissaoArquivo()
    {
        return $this->emissaoArquivo;
    }

    /**
     * @param bool $emissaoArquivo
     */
    public function setEmissaoArquivo($emissaoArquivo)
    {
        $this->emissaoArquivo = $emissaoArquivo;
    }

    /**
     * @param array $matrizSaldoContabilLancamentos
     * @throws Exception
     */
    private function persistirMatrizSaldoContabilLancamentos(array $matrizSaldoContabilLancamentos)
    {
        $matrizSaldoContabilLancamentos = array_map(function (array $lancamentoContabil) {
            $atributos = implode('|', $lancamentoContabil['begin']->informacoesComplementares);

            $matrizSaldoContabilLancamento = new MatrizSaldoContabilLancamento();
            $matrizSaldoContabilLancamento->setEstrutural($lancamentoContabil['begin']->estrutura);
            $matrizSaldoContabilLancamento->setBeginningBalance(abs($lancamentoContabil['begin']->valor));
            $matrizSaldoContabilLancamento->setEndingBalance(abs($lancamentoContabil['end']->valor));
            $matrizSaldoContabilLancamento->setPeriodChangeCredit(0);
            $matrizSaldoContabilLancamento->setPeriodChangeDebit(0);
            $matrizSaldoContabilLancamento->setAtributos($atributos);
            $matrizSaldoContabilLancamento->setNaturezaInicial($lancamentoContabil['begin']->natureza);
            $matrizSaldoContabilLancamento->setNaturezaFinal($lancamentoContabil['end']->natureza);

            if (isset($lancamentoContabil['periods']['D'])) {
                $periodChangeDebit = $lancamentoContabil['periods']['D']->valor;
                $matrizSaldoContabilLancamento->setPeriodChangeDebit($periodChangeDebit);
            }

            if (isset($lancamentoContabil['periods']['C'])) {
                $periodChangeCredit = $lancamentoContabil['periods']['C']->valor;
                $matrizSaldoContabilLancamento->setPeriodChangeCredit($periodChangeCredit);
            }

            return $matrizSaldoContabilLancamento;
        }, $matrizSaldoContabilLancamentos);

        $mes = ($this->encerramento && $this->mes == 12) ? 13 : $this->mes;

        $matrizSaldoContabilRepositorio = new MatrizSaldoContabilRepositorio();
        $matrizSaldoContabil = $matrizSaldoContabilRepositorio->scopeAno($this->ano)->scopeMes($mes)->first();

        if ($matrizSaldoContabil instanceof Model\MatrizSaldoContabil) {
            $matrizSaldoContabilLancamentoRepositorio = new MatrizSaldoContabilLancamentoRepositorio();
            $matrizSaldoContabilLancamentoRepositorio->scopeMatrizSaldoContabil($matrizSaldoContabil)->delete();
        } else {
            $matrizSaldoContabil = new Model\MatrizSaldoContabil();
            $matrizSaldoContabil->setAno($this->ano);
            $matrizSaldoContabil->setMes($mes);
            $matrizSaldoContabil = MatrizSaldoContabilRepositorio::save($matrizSaldoContabil);
        }

        /** @var MatrizSaldoContabilLancamento[] $matrizSaldoContabilLancamentos */
        foreach ($matrizSaldoContabilLancamentos as $matrizSaldoContabilLancamento) {
            $matrizSaldoContabilLancamento->setMatrizSaldoContabil($matrizSaldoContabil);
            MatrizSaldoContabilLancamentoRepositorio::save($matrizSaldoContabilLancamento);
        }
    }

    /**
     * Retorna os dados do De/Para do Sicongi
     * @param $ano
     * @return array
     */
    protected function getDeParaRecurso($ano)
    {
        $deParaFR = [];

        $sql = "
            select orctiporec_id, codigo_siconfi
              from orctiporec
              join orcamento.fonterecurso on orctiporec_id = o15_codigo
             where exercicio = {$this->ano}
               and o15_codigo > 0
        ";


        $rs = db_query($sql);
        while ($dado = pg_fetch_object($rs)) {
            $deParaFR[$dado->orctiporec_id] = $dado->codigo_siconfi;
        }
        return $deParaFR;
    }

    protected function getDeParaFonteRecurso()
    {
        $deParaFR = [];
        $sql = "
            select orctiporec_id, gestao
              from orctiporec
              join orcamento.fonterecurso on orctiporec_id = o15_codigo
             where exercicio = {$this->ano}
               and o15_codigo > 0
        ";
        $rs = db_query($sql);
        while ($dado = pg_fetch_object($rs)) {
            $deParaFR[$dado->orctiporec_id] = $dado->gestao;
        }
        return $deParaFR;
    }

    /**
     * Retorna os dados do De/Para o Do PO
     * @return array
     */
    protected function getDeParaPO()
    {
        $sSql = "
            SELECT db_config.codtrib,
                   db_tipoinstit.db21_codigosiconfi
              FROM configuracoes.db_tipoinstit
             INNER JOIN configuracoes.db_config ON db_config.db21_tipoinstit = db_tipoinstit.db21_codtipo
             WHERE configuracoes.db_config.codigo IN(" . implode(',', $this->getCodigoInstituicoes()) . ");";

        $result = db_query($sSql);

        $deParaPO = array();
        db_utils::makeCollectionFromRecord($result, function ($item) use (&$deParaPO) {
            $deParaPO[$item->codtrib] = $item->db21_codigosiconfi;
        });
        return $deParaPO;
    }

    /**
     * @return bool
     */
    public function temImportacao()
    {
        return $this->temImportacao;
    }

    /**
     * @return int
     */
    public function getTotalLinhasEcidade()
    {
        return $this->totalLinhasEcidade;
    }

    /**
     * Verifica se existe saldo inicial importado para o exericio
     * @param $ano
     * @return bool
     */
    private function temSaldoImportado($ano)
    {
        $daoConplanosaldo = new \cl_conplanoatributosaldo();
        $where = ['c125_conplanosistema = 1',
            "c125_anousu = {$ano}",
            "c125_mesusu = 0",
            "c125_tipo = 4",
            "c125_tiposaldo = 1",
        ];
         $sql = $daoConplanosaldo->sql_query_file(null, 'count(*) as total', null, implode(" and ", $where));
         $rsTotalRegitros = db_query($sql);
         $linha = \db_utils::fieldsMemory($rsTotalRegitros, 0)->total;
         return $linha > 0;
    }
}
