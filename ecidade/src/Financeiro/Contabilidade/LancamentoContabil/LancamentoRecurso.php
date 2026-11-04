<?php

namespace ECidade\Financeiro\Contabilidade\LancamentoContabil;

use db_utils;
use Exception;
use LancamentoAuxiliarAberturaExercicioOrcamento;
use LancamentoAuxiliarBase;
use LancamentoAuxiliarBem;
use LancamentoAuxiliarEncerramentoExercicio;
use LancamentoAuxiliarLancamentoManual;
use LancamentoAuxiliarMovimentacaoEstoque;
use LancamentoAuxiliarRecursosExercicioAnteriorControles;

class LancamentoRecurso
{
    /**
     * Código do documento
     * @var integer
     */
    private $documento;
    /**
     * Código do lançamento
     * @var integer
     */
    private $lancamento;
    /**
     * Código do documento
     * @var integer
     */
    private $exercicio;

    public function __construct($documento, $lancamento, $exercicio)
    {
        $this->documento = $documento;
        $this->lancamento = $lancamento;
        $this->exercicio = $exercicio;
    }

    /**
     * @throws Exception
     */
    public function processar(LancamentoAuxiliarBase $lancamentoAuxiliar)
    {
        if ($lancamentoAuxiliar instanceof LancamentoAuxiliarEncerramentoExercicio ||
            $lancamentoAuxiliar instanceof LancamentoAuxiliarAberturaExercicioOrcamento ||
            $lancamentoAuxiliar instanceof LancamentoAuxiliarRecursosExercicioAnteriorControles) {
            $this->salvarRecursoLancamentoEncerramento($lancamentoAuxiliar);
            return true;
        }

        if ($lancamentoAuxiliar instanceof LancamentoAuxiliarLancamentoManual &&
            in_array($this->documento, [3000, 3001])) {
            $this->salvarRecursoLancamentoManual($lancamentoAuxiliar);
            return true;
        }

        // Transferência de cobertura finaceira - Doc. 142, 143
        if (Documento::isTransferenciaCoberturaFinanceiro($this->documento)) {
            $this->salvarRecursoDocumento142();
            return true;
        }

        // 400, 401, 402, 403, 404
        if ($lancamentoAuxiliar instanceof LancamentoAuxiliarMovimentacaoEstoque) {
            $this->salvarRecursoMaterial();
            return true;
        }

       // documentos envolvendo Bens/Materiais
        $documentos = [600, 601, 602, 603, 604, 605, 700, 701, 702, 703, 704, 705, 706, 708, 707, 709, 710];
        if (in_array($this->documento, $documentos)) {
            $this->salvaRecursoDaConta();
            return true;
        }

        $documentosPagamento = [
            5, 35, 37, 6002, 6004, 6008, 6010, 6, 36, 38, 6003, 6005, 6009, 6011, 100, 107, 109, 111, 113, 115, 117,
            165, 416, 419, 6000, 6006, 101, 108, 110, 112, 114, 116, 118, 166, 417, 6001, 6013, 418, 140, 160, 161,
            141, 162, 163, 120, 130, 150, 151, 121, 131, 152, 153, 6012, 6007
        ];

        if (in_array($this->documento, $documentosPagamento)) {
            $this->salvarRecursoContaPagadora($lancamentoAuxiliar);
            return true;
        }

        /**
         * LANÇAMENTOS DE ALMOX ENVOLVENDO EMPENHOS e LANÇAMENTOS DE PATRIMÔNIO ENVOLVENDO EMPENHOS
         */
        $documentos = [210, 212, 208, 214, 211, 213, 209, 215];
        if (in_array($this->documento, $documentos)) {
            $this->salvarRecursoAlmoxOuPatrimonioEmpenho();
            return true;
        }

        /**
         * Documentos de Aproprições de Décimo e Férias e Reversões
         */
        $documentos = [
            350, 352, 354, 356, 358, 360, 362, 364, 366, 368, 370, 372, 420, 422, 424, 426, 428, 430, 432, 434, 436,
            438, 440, 442, 351, 353, 355, 357, 359, 361, 363, 365, 367, 369, 371, 373, 421, 423, 425, 427, 429, 431,
            433, 435, 437, 439, 441, 443
        ];

        if (in_array($this->documento, $documentos)) {
            $this->salvarRecursoApropriacaoDecimoFerias();
            return true;
        }

        /**
         * @todo, a lógica abaixo é como era realizado anteriormente os laçamentos de recurso
         */
        $complementoRecurso = new ComplementoRecurso();
        $complementoRecurso->processar($this->lancamento, $this->exercicio);

        $dadosRecurso = new \ECidade\Financeiro\Contabilidade\LancamentoContabil\Recurso();
        $dadosRecurso->processar($this->lancamento, $lancamentoAuxiliar);
        return true;
    }

    /**
     * @param LancamentoAuxiliarBase $lancamentoAuxiliar
     * @return void
     * @throws Exception
     */
    private function salvarRecursoLancamentoEncerramento(LancamentoAuxiliarBase $lancamentoAuxiliar)
    {
        $recurso = $lancamentoAuxiliar->getRecurso();
        $this->gerarConlancamRecursoEncerramento($recurso);

        // docs que não são de receita e despesa não devem salvar conlancamcomplementorecurso
        $docs = [1009, 1022, 1023];
        if (!in_array($this->documento, $docs)) {
            $this->criarConlancamComplementoRecurso($recurso->getCodigo(), $recurso->getComplemento());
        }
    }

    /**
     * Persiste o recurso na conlancamrecurso
     * @param Recurso $recurso
     * @return void
     * @throws Exception
     */
    private function gerarConlancamRecursoEncerramento(\Recurso $recurso)
    {
        $sql = "select * from conlancamval where c69_codlan = {$this->lancamento} order by c69_sequen";
        $rsLancamentos = db_query($sql);
        $lancamentos = db_utils::getCollectionByRecord($rsLancamentos);

        foreach ($lancamentos as $lancamento) {
            $contas = ["D" => $lancamento->c69_debito, "C" => $lancamento->c69_credito];

            foreach ($contas as $sinal => $conta) {
                $this->criarConlancamRecurso($recurso->getCodigo(), $conta, $sinal);
            }
        }
    }

    /**
     * @param $idRecurso
     * @param $conta
     * @param $natureza
     * @return void
     * @throws Exception
     */
    private function criarConlancamRecurso($idRecurso, $conta, $natureza)
    {
        $dao = new \cl_conlancamrecurso();
        $dao->c130_orctiporec = $idRecurso;
        $dao->c130_anousu = $this->exercicio;
        $dao->c130_conlancam = $this->lancamento;
        $dao->c130_conta = $conta;
        $dao->c130_natureza = $natureza;
        $dao->c130_sequencial = null;
        $dao->incluir(null);
        if ($dao->erro_status == 0) {
            $msg = "Erro ao salvar dados do recurso do lançamento\n{$dao->erro_status}";
            throw new \Exception($msg);
        }
    }


    /**
     * Persiste o recurso na conlancamcomplementorecurso
     * @param integer $recurso
     * @param integer $complemento
     * @return void
     * @throws Exception
     */
    private function criarConlancamComplementoRecurso($recurso, $complemento)
    {
        $dadosRecurso = (object)['o200_sequencial' => $complemento, 'o15_codigo' => $recurso];

        $complementoRecurso = new ComplementoRecurso();
        $complementoRecurso->salvarComplementoRecurso($this->lancamento, $dadosRecurso);
    }

    private function salvarRecursoLancamentoManual($lancamentoAuxiliar)
    {
        $recursoCredito = $lancamentoAuxiliar->getRecursoCredito();
        if ($lancamentoAuxiliar->isDespesaReceita()) {
            $this->criarConlancamComplementoRecurso($recursoCredito->getCodigo(), $recursoCredito->getComplemento());
        }

        $this->criarConlancamRecurso(
            $lancamentoAuxiliar->getRecursoDebito()->getCodigo(),
            $lancamentoAuxiliar->getContaDebito(),
            'D'
        );

        $this->criarConlancamRecurso(
            $recursoCredito->getCodigo(),
            $lancamentoAuxiliar->getContaCredito(),
            'C'
        );
    }

    private function salvarRecursoDocumento142()
    {
        $where = "c69_codlan = {$this->lancamento} and c69_ordem = 1";
        $lancamentos = $this->getLancamentos($where);
        $lancamento = $lancamentos[0];

        // recurso do primeiro lancamento
        $this->criarConlancamRecurso($lancamento->recurso_debito, $lancamento->c69_debito, 'D');
        $this->criarConlancamRecurso($lancamento->recurso_credito, $lancamento->c69_credito, 'C');

        $conta72111 = $this->getReduzido($lancamento->c69_anousu, $lancamento->c61_instit, '72111');
        $conta8211101 = $this->getReduzido($lancamento->c69_anousu, $lancamento->c61_instit, '8211101');

        // recurso do segundo lancamento
        $this->criarConlancamRecurso($lancamento->recurso_debito, $conta72111, 'D');
        $this->criarConlancamRecurso($lancamento->recurso_debito, $conta8211101, 'C');

        // recurso do terceiro lancamento
        $this->criarConlancamRecurso($lancamento->recurso_credito, $conta8211101, 'D');
        $this->criarConlancamRecurso($lancamento->recurso_credito, $conta72111, 'C');
    }

    private function getReduzido($exercicio, $instituicao, $estrutural)
    {
        $sql = "
        select c60_estrut, c61_instit, c61_codigo, c61_reduz
          from contabilidade.conplano
          join contabilidade.conplanoreduz on (c60_codcon, c60_anousu) = (c61_codcon, c61_anousu)
         where c60_anousu = {$exercicio}
           and c61_instit = {$instituicao}
           and c60_estrut like '{$estrutural}%'";
        $rs = db_query($sql);
        if (!$rs || pg_num_rows($rs) === 0) {
            throw new \Exception('Erro ao buscar reduzido do estrutural: ' . $estrutural);
        }

        return \db_utils::fieldsMemory($rs, 0)->c61_reduz;
    }

    private function salvarRecursoContaPagadora(LancamentoAuxiliarBase $lancamentoAuxiliar)
    {
        switch ($this->documento) {
            case 5:    // PAGAMENTO
            case 35:   // PAGAMENTO RP
            case 37:   // PAGAMENTO RP NAO PROC
            case 6002: // RETENÇÃO DE VALORES CONSIGNADOS
            case 6004: // BAIXA DE RETENÇÃO APROPRIADA
            case 6008: // BAIXA DA RETENÇÃO APROPRIADA E CONSIGNADOS - RPP
            case 6010: // BAIXA DA RETENÇÃO APROPRIADA E CONSIGNADOS - RPNP
            case 6:    // ESTORNO DE PAGAMENTO
            case 36:   // ESTORNO DE PAGAMENTO RP
            case 38:   // ESTORNO PAGAMENTO RP NAO PROC
            case 6003: // ESTORNO DE RETENÇÃO DE VALORES CONSIGNADOS
            case 6005: // ESTORNO DA BAIXA DE RETENÇÃO APROPRIADA
            case 6009: // ESTORNO BAIXA DA RETENÇÃO APROPRIADA E CONSIGNADOS - RPP
            case 6011: // ESTORNO BAIXA DA RETENÇÃO APROPRIADA E CONSIGNADOS - RPNP
                $this->contaPagadoraDespesa();
                break;
            case 100:  // ARRECADAÇÃO DE RECEITA
            case 107:  // ARRECADAÇÃO DE RECEITA LANÇADA
            case 109:  // RECEITA OPERAÇÃO DE CRÉDITO
            case 111:  // RECEITA DE ALIENAÇÃO DE BENS
            case 113:  // RECEITA DE CONVÊNIOS
            case 117:  // RECEBIMENTO DE DÍVIDA ATIVA
            case 165:  // ARRECADAÇÃO DE RECEITA TEF
            case 416:  // DEVOLUÇÃO DE ADIANTAMENTO
            case 419:  // ESTORNO DE DESCONTO CONCEDIDO
            case 6000: // RETENÇÃO DE TRIBUTOS
            case 6006: // BAIXA POR INGRESSO DE TRIBUTO RETIDO
            case 101:  // ESTORNO DE ARRECADAÇÃO
            case 108:  // ESTORNO DE ARRECADAÇÃO DE RECEITA LANÇADA
            case 110:  // RECEITA OP. DE CRÉDITO ESTORNO
            case 112:  // REC. DE ALIENAÇÃO DE BENS ESTORNO
            case 114:  // RECEITA DE CONVÊNIOS  ESTORNO
            case 118:  // ESTORNO DO RECEBIMENTO DE DÍVIDA ATIVA
            case 166:  // ESTORNO DE ARRECADAÇÃO DE RECEITA TEF
            case 417:  // ESTORNO DE DEVOLUÇÃO DE ADIANTAMENTO
            case 6001: // ESTORNO DE RETENÇÃO DE TRIBUTOS
            case 6013: // ESTORNO DE RECONHECIMENTO DE TRIBUTOS RETIDOS OUTRA ENTIDADE
            case 418: // DESCONTO CONCEDIDO
                $this->contaPagadoraReceita();
                break;
            case 115:  // RECEITA DE TRANSFERÊNCIAS
            case 116:  // RECEITA DE TRANSFERÊNCIAS ESTORNO
                $this->contaReceitaTransferencia();
                break;
            case 140: // TRANSFERENCIA BANCARIA
            case 141: // ESTORNO DE TRANSFERENCIA BANCARIA
            case 150: // RECEBIMENTO DE OUTRAS MOVIMENTAÇÕES EXTRAS
            case 152: // ESTORNO DE RECEBIMENTO DE OUTRAS MOVIMENTAÇÕES EXTRAS
            case 151: // PAGAMENTO DE OUTRAS MOVIMENTAÇÕES EXTRAS
            case 153: // ESTORNO DE PAGAMENTO DE OUTRAS MOVIMENTAÇÕES EXTRAS
            case 6012: // RECONHECIMENTO DE TRIBUTOS RETIDOS OUTRA ENTIDADE
            case 6007: // ESTORNO DA BAIXA POR INGRESSO DE TRIBUTO RETIDO
                $this->contaPagadoraSlipsUmLancamento();
                break;
            case 160: // DEPOSITOS DIVERSOS - Recebimento
            case 161: // DEPOSITOS DIVERSOS - Pagamento
            case 162: // ESTORNO DEPOSITOS DIVERSOS - Recebimento
            case 163: // ESTORNO DEPOSITOS DIVERSOS - Pagamento
                $this->contaPagadoraSlipsDepositoDiversos();
                break;
            case 120: // CONCESSÃO DE TRANSFERÊNCIA FINANCEIRA
            case 121: // ESTORNO DE CONCESSÃO DE TRANSFERÊNCIA FINANCEIRA
            case 130: // RECEBIMENTO DE TRANSFERENCIA FINANCEIRA
            case 131: // ESTORNO DE RECEB DE TRANSFERENCIA FINANCEIRA
                $this->contaPagadoraTransferenciaFinanceira();
                break;
        }
    }

    /**
     * @return \stdClass
     * @throws Exception
     */
    private function recursoReceita()
    {
        $sql = "
         select o15_codigo as id_recurso, o15_complemento as complemento
           from conlancam
           join conlancamdoc on c71_codlan = c70_codlan
           join conlancamrec on c74_codlan = c70_codlan
           join orcreceita on (c74_anousu, c74_codrec) = (o70_anousu, o70_codrec)
           join orctiporec on o15_codigo = o70_codigo
          where c70_codlan = {$this->lancamento}
        ";
        $rs = db_query($sql);
        if (pg_num_rows($rs) === 0) {
            throw new Exception("Não foi possível encontrar o recurso da receita.");
        }
        return \db_utils::fieldsMemory($rs, 0);
    }

    /**
     * @return \stdClass
     * @throws Exception
     */
    private function recursoEmpenho()
    {
        $sql = "
         select o15_codigo as id_recurso, o15_complemento as complemento
           from conlancam
           join conlancamdoc on c71_codlan = c70_codlan
           join conlancamemp on c75_codlan = c70_codlan
           join empempenho on e60_numemp = c75_numemp
           left join empenho.empresto on e91_numemp = e60_numemp
                and e91_anousu = c70_anousu
           join origemcomplementorecurso on o206_numero = e60_numemp
                and o206_origem = (case when e91_numemp is not null then 10 else 1 end)
           join orctiporec on o15_codigo = o206_recurso
          where c70_codlan = {$this->lancamento}
        ";

        $rs = db_query($sql);
        if (pg_num_rows($rs) === 0) {
            throw new Exception("Não foi possível encontrar o recurso do empenho.");
        }
        return \db_utils::fieldsMemory($rs, 0);
    }


    private function contaPagadoraReceita()
    {
        $recursoReceita = $this->recursoReceita();
        $where = "c69_codlan = {$this->lancamento}";
        $lancamentos = $this->getLancamentos($where, 'c69_ordem');

        $primeiroLancamento = $lancamentos[0];
        $recursoContaPagadora = $primeiroLancamento->recurso_debito;
        $reduzidoContaPagadora = $primeiroLancamento->c69_debito;
        $reduzido = $primeiroLancamento->c69_credito;
        $naturezaPagadora = 'D';
        $natureza = 'C';

        if (in_array($this->documento, [101, 108, 110, 112, 114, 116, 118, 166, 417, 6001, 6013, 418])) {
            $recursoContaPagadora = $primeiroLancamento->recurso_credito;
            $reduzidoContaPagadora = $primeiroLancamento->c69_credito;
            $reduzido = $primeiroLancamento->c69_debito;
            $naturezaPagadora = 'C';
            $natureza = 'D';
        }

        /**
         * Primeiro lançamento
         * Documentos 100, 107, 109, 111, 113, 115, 117, 165, 416, 419, 6000, 6006
         *  - salva a Débito o recurso da conta pagadora e a crédito recurso da receita
         * Documentos 101, 108, 110, 112, 114, 116, 118, 166, 417, 6001, 6013, 418
         *   - salva a Crédito o recurso da conta pagadora e a Débito recurso da receita
         */
        $this->criarConlancamRecurso($recursoContaPagadora, $reduzidoContaPagadora, $naturezaPagadora);
        $this->criarConlancamRecurso($recursoReceita->id_recurso, $reduzido, $natureza);

        // segundo lançamento recurso da receita
        if (array_key_exists(1, $lancamentos)) {
            $segundo = $lancamentos[1];
            $this->criarConlancamRecurso($recursoReceita->id_recurso, $segundo->c69_debito, 'D');
            $this->criarConlancamRecurso($recursoReceita->id_recurso, $segundo->c69_credito, 'C');
        }

        // terceiro lançamento recurso da conta pagadora
        if (array_key_exists(2, $lancamentos)) {
            $terceiro = $lancamentos[2];
            $this->criarConlancamRecurso($recursoContaPagadora, $terceiro->c69_debito, 'D');
            $this->criarConlancamRecurso($recursoContaPagadora, $terceiro->c69_credito, 'C');
        }

        $this->criarConlancamComplementoRecurso($recursoReceita->id_recurso, $recursoReceita->complemento);
    }

    public function contaReceitaTransferencia()
    {
        $recursoReceita = $this->recursoReceita();
        $where = "c69_codlan = {$this->lancamento}";
        $lancamentos = $this->getLancamentos($where, 'c69_ordem');

        $primeiroLancamento = $lancamentos[0];
        $recursoContaPagadora = $primeiroLancamento->recurso_debito;
        $reduzidoContaPagadora = $primeiroLancamento->c69_debito;
        $reduzido = $primeiroLancamento->c69_credito;
        $naturezaPagadora = 'D';
        $natureza = 'C';

        if (in_array($this->documento, [116])) {
            $recursoContaPagadora = $primeiroLancamento->recurso_credito;
            $reduzidoContaPagadora = $primeiroLancamento->c69_credito;
            $reduzido = $primeiroLancamento->c69_debito;
            $naturezaPagadora = 'C';
            $natureza = 'D';
        }

        $this->criarConlancamRecurso($recursoContaPagadora, $reduzidoContaPagadora, $naturezaPagadora);
        $this->criarConlancamRecurso($recursoReceita->id_recurso, $reduzido, $natureza);

        // segundo lançamento recurso da receita
        if (array_key_exists(1, $lancamentos)) {
            $segundo = $lancamentos[1];
            $this->criarConlancamRecurso($recursoReceita->id_recurso, $segundo->c69_debito, 'D');
            $this->criarConlancamRecurso($recursoReceita->id_recurso, $segundo->c69_credito, 'C');
        }

        // terceiro lançamento recurso da receita
        if (array_key_exists(2, $lancamentos)) {
            $terceiro = $lancamentos[2];
            $this->criarConlancamRecurso($recursoReceita->id_recurso, $terceiro->c69_debito, 'D');
            $this->criarConlancamRecurso($recursoReceita->id_recurso, $terceiro->c69_credito, 'C');
        }

        // quarto lançamento recurso da conta pagadora
        if (array_key_exists(3, $lancamentos)) {
            $terceiro = $lancamentos[2];
            $this->criarConlancamRecurso($recursoContaPagadora, $terceiro->c69_debito, 'D');
            $this->criarConlancamRecurso($recursoContaPagadora, $terceiro->c69_credito, 'C');
        }

        $this->criarConlancamComplementoRecurso($recursoReceita->id_recurso, $recursoReceita->complemento);
    }

    /**
     * @return \stdClass[]
     * @throws Exception
     */
    public function getLancamentos($where, $order = null)
    {
        $campos = "c69_anousu, c69_codlan, c69_credito, c69_debito, reduzcredito.c61_instit,
        reduzcredito.c61_codigo as recurso_credito, reduzdebito.c61_codigo as recurso_debito, c69_ordem
        ";
        $dao = new \cl_conlancamval();
        $sql = $dao->sql_query_lancamentos($campos, $where, $order);
        $rs = db_query($sql);

        if (pg_num_rows($rs) === 0) {
            throw new Exception('Não foi possível encontrar os lançamentos.');
        }

        return \db_utils::getCollectionByRecord($rs);
    }

    private function contaPagadoraDespesa()
    {
        $recursoEmpenho = $this->recursoEmpenho();
        $where = "c69_codlan = {$this->lancamento}";
        $lancamentos = $this->getLancamentos($where, 'c69_ordem');

        $primeiroLancamento = $lancamentos[0];
        $recursoContaPagadora = $primeiroLancamento->recurso_credito;
        $reduzidoContaPagadora = $primeiroLancamento->c69_credito;
        $reduzido = $primeiroLancamento->c69_debito;
        $naturezaPagadora = 'C';
        $natureza = 'D';

        if (in_array($this->documento, [6, 36, 38, 6003, 6005, 6009, 6011])) {
            $recursoContaPagadora = $primeiroLancamento->recurso_debito;
            $reduzidoContaPagadora = $primeiroLancamento->c69_debito;
            $reduzido = $primeiroLancamento->c69_credito;
            $naturezaPagadora = 'D';
            $natureza = 'C';
        }

        $this->criarConlancamRecurso($recursoContaPagadora, $reduzidoContaPagadora, $naturezaPagadora);
        $this->criarConlancamRecurso($recursoEmpenho->id_recurso, $reduzido, $natureza);

        array_shift($lancamentos);
        foreach ($lancamentos as $lancamento) {
            $this->criarConlancamRecurso($recursoEmpenho->id_recurso, $lancamento->c69_debito, 'D');
            $this->criarConlancamRecurso($recursoEmpenho->id_recurso, $lancamento->c69_credito, 'C');
        }

        $this->criarConlancamComplementoRecurso($recursoEmpenho->id_recurso, $recursoEmpenho->complemento);
    }

    /**
     * Nos documentos relacionado: 140,  141,  150,  152,  151,  153, 6012, 6007
     * Os recursos salvos devem vir da conta salva no lançamento
     *
     * @return void
     * @throws Exception
     */
    private function contaPagadoraSlipsUmLancamento()
    {
        $where = "c69_codlan = {$this->lancamento}";
        $lancamentos = $this->getLancamentos($where, 'c69_ordem');

        $lancamento = $lancamentos[0];
        $this->criarConlancamRecurso($lancamento->recurso_credito, $lancamento->c69_credito, 'C');
        $this->criarConlancamRecurso($lancamento->recurso_debito, $lancamento->c69_debito, 'D');
    }

    private function contaPagadoraSlipsDepositoDiversos()
    {
        $where = "c69_codlan = {$this->lancamento}";
        $lancamentos = $this->getLancamentos($where, 'c69_ordem');

        $primeiroLancamento = $lancamentos[0];
        $recursoContaExtra = $primeiroLancamento->recurso_credito;
        $reduzidoContaExtra = $primeiroLancamento->c69_credito;
        $reduzidoContaPagadora = $primeiroLancamento->c69_debito;
        $recursoContaPagadora = $primeiroLancamento->recurso_debito;
        $naturezaPagadora = 'D';
        $naturezaExtra = 'C';

        if (in_array($this->documento, [162, 161])) {
            $recursoContaExtra = $primeiroLancamento->recurso_debito;
            $reduzidoContaExtra = $primeiroLancamento->c69_debito;
            $recursoContaPagadora = $primeiroLancamento->recurso_credito;
            $reduzidoContaPagadora = $primeiroLancamento->c69_credito;
            $naturezaPagadora = 'C';
            $naturezaExtra = 'D';
        }

        $this->criarConlancamRecurso($recursoContaPagadora, $reduzidoContaPagadora, $naturezaPagadora);
        $this->criarConlancamRecurso($recursoContaExtra, $reduzidoContaExtra, $naturezaExtra);

        array_shift($lancamentos);
        foreach ($lancamentos as $lancamento) {
            $this->criarConlancamRecurso($recursoContaExtra, $lancamento->c69_debito, 'D');
            $this->criarConlancamRecurso($recursoContaExtra, $lancamento->c69_credito, 'C');
        }
    }

    private function contaPagadoraTransferenciaFinanceira()
    {
        $where = "c69_codlan = {$this->lancamento}";
        $lancamentos = $this->getLancamentos($where, 'c69_ordem');

        $primeiroLancamento = $lancamentos[0];
        $recursoConta = $primeiroLancamento->recurso_debito;
        $reduzidoConta = $primeiroLancamento->c69_debito;
        $recursoContaPagadora = $primeiroLancamento->recurso_credito;
        $reduzidoContaPagadora = $primeiroLancamento->c69_credito;
        $naturezaPagadora = 'C';
        $naturezaConta = 'D';

        if (in_array($this->documento, [121, 130])) {
            $recursoConta = $primeiroLancamento->recurso_credito;
            $reduzidoConta = $primeiroLancamento->c69_credito;
            $recursoContaPagadora = $primeiroLancamento->recurso_debito;
            $reduzidoContaPagadora = $primeiroLancamento->c69_debito;
            $naturezaPagadora = 'D';
            $naturezaConta = 'C';
        }

        $this->criarConlancamRecurso($recursoContaPagadora, $reduzidoContaPagadora, $naturezaPagadora);
        $this->criarConlancamRecurso($recursoConta, $reduzidoConta, $naturezaConta);

        $segundoLancamento = $lancamentos[1];
        $this->criarConlancamRecurso($recursoContaPagadora, $segundoLancamento->c69_credito, 'C');
        $this->criarConlancamRecurso($recursoContaPagadora, $segundoLancamento->c69_debito, 'D');
    }

    /**
     * @return void
     * @throws Exception
     */
    private function salvarRecursoMaterial()
    {
        $this->salvaRecursoDaConta();
    }

    /**
     * Esse método salva busca todas contas (conlancamval) do lançamento contábil e salva o recurso da própria conta
     *
     * @return void
     * @throws Exception
     */
    private function salvaRecursoDaConta()
    {
        $where = "c69_codlan = {$this->lancamento}";
        $lancamentos = $this->getLancamentos($where, 'c69_ordem');

        foreach ($lancamentos as $lancamento) {
            $this->criarConlancamRecurso($lancamento->recurso_credito, $lancamento->c69_credito, 'C');
            $this->criarConlancamRecurso($lancamento->recurso_debito, $lancamento->c69_debito, 'D');
        }
    }

    /**
     * Regra
     * 1º lançamento, para os docs 208, 210, 212, 214
     *  - salvar o recurso da conta a débito e o recurso do empenho a crédito
     * demais lançamentos: salvar o recurso do empenho
     *
     * 1º lançamento, para os docs 209, 211, 213, 215 (EXTORNO)
     *  - salvar o recurso da conta a crédito e o recurso do empenho a crédito
     *  demais lançamentos: salvar o recurso do empenho
     * @return void
     * @throws Exception
     */
    private function salvarRecursoAlmoxOuPatrimonioEmpenho()
    {
        $this->recursoPrimeiroLancamentosDemaisDoEmpenho([209, 211, 213, 215]);
    }

    /**
     * Regra
     * 1º lançamento (padrão)
     *  - salvar o recurso da conta a débito e o recurso do empenho a crédito
     * demais lançamentos: salvar o recurso do empenho
     *
     * 1º lançamento (EXTORNO)
     *  - salvar o recurso da conta a crédito e o recurso do empenho a crédito
     *  demais lançamentos: salvar o recurso do empenho
     * @return void
     * @throws Exception
     */
    private function recursoPrimeiroLancamentosDemaisDoEmpenho(
        $documentoExtorno = [],
        $criaConlancamComplementoRecurso = true
    ) {
        $recursoEmpenho = $this->recursoEmpenho();
        $where = "c69_codlan = {$this->lancamento}";
        $lancamentos = $this->getLancamentos($where, 'c69_ordem');

        $primeiroLancamento = $lancamentos[0];
        $recursoConta = $primeiroLancamento->recurso_debito;
        $reduzidoConta = $primeiroLancamento->c69_debito;
        $naturezaConta = 'D';

        $reduzidoEmpenho = $primeiroLancamento->c69_credito;
        $naturezaEmpenho = 'C';

        if (in_array($this->documento, $documentoExtorno)) {
            $recursoConta = $primeiroLancamento->recurso_credito;
            $reduzidoConta = $primeiroLancamento->c69_credito;
            $reduzidoEmpenho = $primeiroLancamento->c69_debito;
            $naturezaConta = 'C';
            $naturezaEmpenho = 'D';
        }

        $this->criarConlancamRecurso($recursoConta, $reduzidoConta, $naturezaConta);
        $this->criarConlancamRecurso($recursoEmpenho->id_recurso, $reduzidoEmpenho, $naturezaEmpenho);

        array_shift($lancamentos);
        foreach ($lancamentos as $lancamento) {
            $this->criarConlancamRecurso($recursoEmpenho->id_recurso, $lancamento->c69_debito, 'D');
            $this->criarConlancamRecurso($recursoEmpenho->id_recurso, $lancamento->c69_credito, 'C');
        }

        if ($criaConlancamComplementoRecurso) {
            $this->criarConlancamComplementoRecurso($recursoEmpenho->id_recurso, $recursoEmpenho->complemento);
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private function salvarRecursoApropriacaoDecimoFerias()
    {
        $this->salvaRecursoDaConta();
    }
}
