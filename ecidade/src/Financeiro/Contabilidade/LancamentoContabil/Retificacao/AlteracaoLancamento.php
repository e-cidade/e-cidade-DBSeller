<?php

namespace ECidade\Financeiro\Contabilidade\LancamentoContabil\Retificacao;

use cl_conlancamrecurso;
use cl_conlancamval;
use cl_conplanoatributolancamentos;
use cl_infocomplementarvalor;
use db_utils;
use DBCompetencia;
use DBDate;
use ECidade\Financeiro\Contabilidade\ContaCorrente\Services\Processamento;
use ECidade\Financeiro\Contabilidade\LancamentoContabil\Recurso;
use EventoContabil;
use Exception;
use InstituicaoRepository;
use ReflectionException;
use stdClass;

class AlteracaoLancamento
{

    /**
     * Retorna os lancamentos para alteracao
     * @param $parametros
     * @return array
     * @throws \ParameterException
     */
    public function getLancamentos($parametros)
    {

        $daoConlancamval = new cl_conlancamval();
        $where = array(
            'c02_instit = ' . db_getsession("DB_instit"),
            'c69_anousu = ' . db_getsession("DB_anousu")
        );

        if (!empty($parametros->documento)) {
            $where[] = "c71_coddoc = {$parametros->documento}";
        }
        if (!empty($parametros->codigo_lancamento)) {
            $where[] = "c69_codlan = {$parametros->codigo_lancamento}";
        }
        if (!empty($parametros->data_final)) {
            $dataFinal = new DBDate($parametros->data_final);
            $where[] = "c69_data <= '{$dataFinal->getDate()}'";
        }

        if (!empty($parametros->data_inicial)) {
            $dataInicial = new DBDate($parametros->data_inicial);
            $where[] = "c69_data >= '{$dataInicial->getDate()}'";
        }

        if (!empty($parametros->conta_debito)) {
            $where[] = "c69_debito = {$parametros->conta_debito}";
        }

        if (!empty($parametros->conta_credito)) {
            $where[] = "c69_credito = {$parametros->conta_credito}";
        }

        if (!empty($parametros->codigo_empenho)) {
            $where[] = "c75_numemp = {$parametros->codigo_empenho}";
        }

        $campos = array(
            'c69_codlan as codigo_lancamento',
            'c69_sequen  as sequen',
            'c71_coddoc  as documento',
            'c69_data as data',
            'c70_valor as valor',
            'reduzcredito.c61_reduz as reduz_credito',
            'planocredito.c60_descr as descricao_credito',
            'planocredito.c60_estrut as estrutural_credito',
            'reduzdebito.c61_reduz as reduz_debito',
            'planodebito.c60_descr as descricao_debito',
            'planodebito.c60_estrut as estrutural_debito',
        );

        $sqlLancamentos = $daoConlancamval->sql_query_lancamentos(
            implode(", ", $campos),
            implode(" and ", $where),
            " c69_codlan, c69_sequen"
        );
        if (!empty($parametros->codigo_empenho) || !empty($parametros->estrutural_receita)
            || !empty($parametros->elemento_despesa)
        ) {
            $campos[] = 'fc_estruturalreceita(' . db_getsession("DB_anousu")
                . ', orcreceita.o70_codrec) as o50_estrutreceita';
            if (!empty($parametros->elemento_despesa)) {
                $where[] = "(conele.o56_elemento = '{$parametros->elemento_despesa}' 
                    or orcele.o56_elemento = '{$parametros->elemento_despesa}')";
            }
    
            $sqlLancamentos = $daoConlancamval->sql_query_lancamentos_novo(
                implode(", ", $campos),
                implode(" and ", $where),
                " c69_codlan, c69_sequen"
            );
            if (!empty($parametros->estrutural_receita)) {
                $sqlLancamentos = "select * from ({$sqlLancamentos}) as x where o50_estrutreceita = 
                    '{$parametros->estrutural_receita}'";
            }
        }

        $rsLancamentos = db_query($sqlLancamentos);
        if (pg_num_rows($rsLancamentos) == 0) {
            return array();
        }

        if (pg_num_rows($rsLancamentos) >= 6000) {
            $msg = "Mais de 6000 registros foram encontrados para a consulta. Por favor, refine a sua busca.";
            throw new Exception($msg);
        }
        $ultimoLancamento = null;
        $ordem = 1;
        $lancamentos = db_utils::makeCollectionFromRecord(
            $rsLancamentos,
            function ($dados) use (&$ultimoLancamento, &$ordem) {

                if ($ultimoLancamento != $dados->codigo_lancamento) {
                    $ordem = 1;
                }
                $contaCredito = array(
                    "reduzido" => $dados->reduz_credito,
                    "estrutural" => $dados->estrutural_credito,
                    "descricao" => $dados->descricao_credito,
                );

                $contaDebito = array(
                    "reduzido" => $dados->reduz_debito,
                    "estrutural" => $dados->estrutural_debito,
                    "descricao" => $dados->descricao_debito,
                );
                $lancamento = new stdClass();
                $lancamento->codigo_lancamento = $dados->codigo_lancamento;
                $lancamento->sequen = $dados->sequen;
                $lancamento->documento = $dados->documento;
                $lancamento->data = db_formatar($dados->data, 'd');
                $lancamento->valor = trim(db_formatar($dados->valor, 'f'));
                $lancamento->conta_credito = (object)$contaCredito;
                $lancamento->conta_debito = (object)$contaDebito;
                $lancamento->ordem = $ordem;
                $ordem++;

                $ultimoLancamento = $dados->codigo_lancamento;
                return $lancamento;
            }
        );
        return $lancamentos;
    }

    /**
     * Realiza a alteracaa das contas de Lancamento.
     * @param $parametros
     * @return int
     * @throws Exception
     */
    public function alterarLancamento($parametros, $fileLog)
    {

        $codigosLancamentos = implode(',', $parametros->lancamentos);
        $daoContaCorrenteAntigo = new \cl_contacorrentedetalheconlancamval();
        $daoContaCorrenteAntigo->excluir(null, "c28_conlancamval in ({$codigosLancamentos})");
        if ($daoContaCorrenteAntigo->erro_status === '0') {
            throw new Exception("Não foi possível excluir os conta correntes antigos.");
        }

        $daoConlancamval = new cl_conlancamval();
        $where = array('c69_sequen in(' . $codigosLancamentos . ')');

        $sqlLancamentos = $daoConlancamval->sql_query_file(null, "*", null, implode('and ', $where));
        $rsLancamentos = db_query($sqlLancamentos);
        if (!$rsLancamentos) {
            throw new Exception("Erro ao verificar lancamentos para processamento.");
        }

        $totalLinhas = pg_num_rows($rsLancamentos);
        if ($totalLinhas == 0) {
            $sMensagem = "Nenhum lançamento encontrado com os filtros informados. ";
            $sMensagem .= "Revise os filtros e execute novamente a consulta.";
            throw new Exception($sMensagem);
        }

        $logs = array();
        $totalLancamentosProcessados = 0;
        try {
            for ($i = 0; $i < $totalLinhas; $i++) {
                db_inicio_transacao();
                $lancamento = db_utils::fieldsMemory($rsLancamentos, $i);

                if (!empty($parametros->conta_credito_origem)
                    && $parametros->conta_credito_origem != $lancamento->c69_credito) {
                    $textoLog = "Lançamento {$lancamento->c69_codlan} ";
                    $textoLog .= "(sequencia: $lancamento->c69_sequen) nao foi processado:\n";
                    $textoLog .= "\t\t\t Conta crédito diferente da conta informada.";
                    $logs[] = $textoLog;
                    continue;
                }

                if (!empty($parametros->conta_debito_origem)
                    && $parametros->conta_debito_origem != $lancamento->c69_debito) {
                    $textoLog = "Lançamento {$lancamento->c69_codlan} ";
                    $textoLog .= "(sequencia: $lancamento->c69_sequen) nao foi processado:\n";
                    $textoLog .= "\t\t\t Conta débito diferente da conta informada.";
                    $logs[] = $textoLog;
                    continue;
                }

                self::removerLancamentoContaCorrente($lancamento);
                $this->ajustarLancamento(
                    $lancamento,
                    $parametros->conta_credito_destino,
                    $parametros->conta_debito_destino
                );
                self::ajustaRecursoLancamento($lancamento);
                self::processarContaCorrente($lancamento);

                $totalLancamentosProcessados++;
                $textoLog = "Lançamento {$lancamento->c69_codlan} (sequencia: $lancamento->c69_sequen) processado";
                $logs[] = $textoLog;
                db_fim_transacao(false);
            }
        } catch (Exception $exception) {
            db_fim_transacao(true);
            $textoLog = "Lançamento {$lancamento->c69_codlan}";
            $textoLog .= "(sequencia: $lancamento->c69_sequen) não foi processado:\n";
            $textoLog .= "\t\t\t" . $exception->getMessage();
            $logs[] = $textoLog;
        }
        file_put_contents($fileLog, implode(" ========\n ", $logs));
        return $totalLancamentosProcessados;
    }

    /**
     * @param $lancamento
     * @throws Exception
     */
    public static function removerLancamentoContaCorrente($lancamento)
    {

        $daoConplanoAtributo = new cl_conplanoatributolancamentos();
        $daoInfoComplementarValor = new cl_infocomplementarvalor();
        $campos = "array_to_string(array_accum(c124_sequencial), ',') as atributos";

        $sqlAtributos = $daoConplanoAtributo->sql_query_file(
            null,
            $campos,
            null,
            'c124_lancamento =' . $lancamento->c69_codlan
        );
        $rsAtributos = db_query($sqlAtributos);
        if (pg_num_rows($rsAtributos) > 0) {
            $dadosLancamentos = db_utils::fieldsMemory($rsAtributos, 0)->atributos;
            if ($dadosLancamentos == '') {
                return;
            }
            $daoInfoComplementarValor->excluir(null, "c123_conplanoatributolancamentos in({$dadosLancamentos})");
            if ($daoInfoComplementarValor->erro_status == 0) {
                throw new Exception('Erro ao excluir atributos do Conta Corrente');
            }
            $daoConplanoAtributo->excluir(null, "c124_sequencial in({$dadosLancamentos})");
        }
    }

    /**
     * Ajusta as contas do lancamento
     * @param $lancamento
     * @param $contaCredito
     * @param $contaDebito
     * @throws Exception
     */
    private function ajustarLancamento($lancamento, $contaCredito, $contaDebito)
    {
        $daoConlancamval = new cl_conlancamval();
        $daoConlancamval->excluir($lancamento->c69_sequen);
        if ($daoConlancamval->erro_status == 0) {
            throw new Exception('Erro ao ajustar lançamento' . $daoConlancamval->erro_msg);
        }

        $daoConlancamval->c69_sequen = $lancamento->c69_sequen;
        $daoConlancamval->c69_anousu = $lancamento->c69_anousu;
        $daoConlancamval->c69_codlan = $lancamento->c69_codlan;
        $daoConlancamval->c69_codhist = $lancamento->c69_codhist;
        $daoConlancamval->c69_credito = empty($contaCredito) ? $lancamento->c69_credito : $contaCredito;
        $daoConlancamval->c69_debito = empty($contaDebito) ? $lancamento->c69_debito : $contaDebito;
        $daoConlancamval->c69_valor = $lancamento->c69_valor;
        $daoConlancamval->c69_data = $lancamento->c69_data;
        $daoConlancamval->c69_ordem = $lancamento->c69_ordem;
        $daoConlancamval->incluir($lancamento->c69_sequen);
        if ($daoConlancamval->erro_status == 0) {
            throw  new Exception('Erro ao incluir dados do lançamento: '.$daoConlancamval->erro_msg);
        }
        EventoContabil::salvarLancamento($lancamento->c69_codlan);
    }

    /**
     * @param $lancamento
     * @throws ReflectionException
     * @throws Exception
     */
    public static function ajustaRecursoLancamento($lancamento)
    {
        $daoConlancamRecurso = new cl_conlancamrecurso();
        $daoConlancamRecurso->excluir(null, 'c130_conlancam =' . $lancamento->c69_codlan);
        if ($daoConlancamRecurso->erro_status == 0) {
            throw new Exception("Erro ao ajustar recursos do lançamento.\n" . $daoConlancamRecurso->erro_sql);
        }
        $dadosRecurso = new Recurso();
        $dadosRecurso->processar($lancamento->c69_codlan);
    }

    /**
     * @param $lancamento
     * @throws \DBException
     * @throws \ParameterException
     * @throws Exception
     */
    public static function processarContaCorrente($lancamento)
    {
        $data = new DBDate($lancamento->c69_data);
        $competencia = new DBCompetencia($data->getAno(), $data->getMes());
        $oProcessamento = new Processamento(InstituicaoRepository::getInstituicaoSessao(), $competencia);
        $oProcessamento->processar(array($lancamento->c69_codlan));
    }
}
