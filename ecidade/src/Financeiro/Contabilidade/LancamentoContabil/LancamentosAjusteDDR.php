<?php

namespace ECidade\Financeiro\Contabilidade\LancamentoContabil;

use cl_conlancamdoc;
use cl_conlancamval;
use cl_conplanoreduz;
use db_utils;
use DBContaCorrenteAtributos;
use EventoContabil;
use Exception;
use InstituicaoRepository;
use stdClass;

/**
 * Classe criada para ajustar o saldo da disponibilidade por fonte de recurso
 * Quando diminuir a disponibilidade? A difenrença é < 0
 *
 * O que fazer?
 *
 * 1º Realizar um lançamento para o recurso com a diferença:
 * - Debitar  8211101 e
 * - creditar 7211101 ou 72111
 *
 * 2º Realizar outro lançamento no recurso da conplanoreduz da 8211101
 * - Debitar  7211101 ou 72111
 * - Creditar 8211101
 *
 *
 * Quando aumentar a disponibilidade? A difenrença é > 0
 *
 * O que fazer?
 * 1º Realizar um lançamento para o recurso com a diferença:
 * - Debitar  7211101 ou 72111
 * - creditar 8211101
 *
 * 2º Realizar outro lançamento no recurso da conplanoreduz da 8211101
 * - Debitar  8211101
 * - Creditar 7211101 ou 72111
 */
class LancamentosAjusteDDR
{
    /**
     * @var int
     */
    private $institituicao;
    /**
     * @var int
     */
    private $exercicio;
    /**
     * @var string
     */
    private $data;
    /**
     * @var stdClass
     */
    private $dados821101;
    /**
     * @var stdClass
     */
    private $dados72111;
    /**
     * @var integer
     */
    private $codigoPO;

    /**
     * @param integer $institituicao
     * @param integer $exercicio
     * @param string $data
     */
    public function __construct($institituicao, $exercicio, $data)
    {
        $this->institituicao = $institituicao;
        $this->exercicio = $exercicio;
        $this->data = $data;
        $this->codigoPO = InstituicaoRepository::getInstituicaoByCodigo($institituicao)->getCodigoTribunal();

        $this->dados821101 = $this->getDadosConta($institituicao, $exercicio, '8211101');
        $this->dados72111 = $this->getDadosConta($institituicao, $exercicio, '72111');
    }

    /**
     * @param int $instit
     * @param int $exercicio
     * @param string $estrutural
     * @return stdClass
     */
    private function getDadosConta($instit, $exercicio, $estrutural)
    {
        $where = "c61_instit = {$instit} and c60_anousu = {$exercicio} and c60_estrut like '{$estrutural}%' ";
        $order = " order by c60_estrut";
        $campos = "c61_reduz, c60_estrut, c61_codigo";
        $dao = new cl_conplanoreduz();
        $sql = $dao->sql_query_plano_reduzido($campos, $where . $order);
        $rs = db_query($sql);
        return db_utils::fieldsMemory($rs, 0);
    }

    /**
     * @param stdClass $dados
     * @return void
     * @throws Exception
     */
    public function processarLancamentos(stdClass $dados)
    {
        $valorLancamento = abs($dados->diferenca);
        $reduzido821101 = $this->dados821101->c61_reduz;
        $reduzido72111 = $this->dados72111->c61_reduz;
        if ($dados->diferenca < 0) {
            $this->gerarLancamento($dados->o15_codigo, $valorLancamento, $reduzido821101, $reduzido72111);
        } else {
            $this->gerarLancamento($dados->o15_codigo, $valorLancamento, $reduzido72111, $reduzido821101);
        }
    }

    /**
     * @param integer $idRecurso
     * @param float $valorLancamento
     * @param integer $contaDebito
     * @param integer $contaCredito
     * @return void
     * @throws Exception
     */
    private function gerarLancamento($idRecurso, $valorLancamento, $contaDebito, $contaCredito)
    {
        $dadosRecurso = $this->buscaDadosRecurso($idRecurso);
        $codigoLancamento = $this->criaLancamento($valorLancamento);
        $this->vincularInstituicao($codigoLancamento);
        $this->vincularDocumento($codigoLancamento);
        $this->vincularTextoComplementar($codigoLancamento);
        $this->vincularConlancamRecurso($codigoLancamento, $dadosRecurso);
        $this->lancarValores($codigoLancamento, $valorLancamento, $contaDebito, $contaCredito, $dadosRecurso);
        $this->vincularRecursoConta($codigoLancamento, $contaDebito, $idRecurso, 'D');
        $this->vincularRecursoConta($codigoLancamento, $contaCredito, $idRecurso, 'C');
    }

    /**
     * @param $valorLancamento
     * @return int|mixed
     * @throws Exception
     */
    private function criaLancamento($valorLancamento)
    {
        $dao = new \cl_conlancam();
        $dao->c70_anousu = $this->exercicio;
        $dao->c70_data = $this->data;
        $dao->c70_valor = $valorLancamento;
        $dao->incluir(null);

        if ($dao->erro_status == 0) {
            throw new \Exception("Erro ao salvar o lançamento.");
        }
        return $dao->c70_codlan;
    }

    /**
     * @param $codigoLancamento
     * @return void
     * @throws Exception
     */
    private function vincularInstituicao($codigoLancamento)
    {
        if (!EventoContabil::vincularLancamentoNaInstituicao($codigoLancamento, $this->institituicao)) {
            throw new Exception('Não foi possível vincular o lançamento a instituição.');
        }
    }

    /**
     * @param $codigoLancamento
     * @return void
     * @throws Exception
     */
    private function vincularDocumento($codigoLancamento)
    {
        $dao = new cl_conlancamdoc();
        $dao->c71_codlan = $codigoLancamento;
        $dao->c71_data = $this->data;
        $dao->c71_coddoc = 3000;

        $dao->incluir($codigoLancamento);
        if ($dao->erro_status == 0) {
            $sErroMsg = "Não foi possível salvar documento contabeis\n";
            $sErroMsg .= "Erro Técnico: {$dao->erro_msg}\n{$dao->erro_campo}";
            throw new Exception($sErroMsg);
        }
    }

    /**
     * @param $codigoLancamento
     * @return void
     * @throws Exception
     */
    private function vincularTextoComplementar($codigoLancamento)
    {
        $dao = new \cl_conlancamcompl();
        $dao->c72_complem = "Ajuste da DDR por fonte de Recursos.";
        $dao->c72_codlan = $codigoLancamento;
        $dao->incluir($codigoLancamento);
        if ($dao->erro_status == "0") {
            throw new Exception("Erro ao incluir complemento do lançamento:" . $dao->erro_msg);
        }
    }

    /**
     * @param $id
     * @return stdClass
     * @throws Exception
     */
    private function buscaDadosRecurso($id)
    {
        $campos = 'o15_codigo, o200_sequencial, o15_recurso, o15_descr, o200_descricao, gestao';
        $where = "o15_codigo = {$id} and exercicio = {$this->exercicio}";
        $dao = new \cl_fonterecurso();
        $rs = db_query($dao->sqlFonteRecruso(null, $campos, null, $where));

        if (!$rs || pg_num_rows($rs) === 0) {
            throw new Exception("Erro ao buscaar o recurso de id: $id");
        }

        return db_utils::fieldsMemory($rs, 0);
    }

    /**
     * @param integer $codigoLancamento
     * @param stdClass $dadosRecurso
     * @return void
     * @throws Exception
     */
    private function vincularConlancamRecurso($codigoLancamento, $dadosRecurso)
    {
        $complemento = new ComplementoRecurso();
        $complemento->salvarComplementoRecurso($codigoLancamento, $dadosRecurso);
    }

    /**
     * @param integer $codigoLancamento
     * @param float $valorLancamento
     * @param integer $contaDebito reduzido
     * @param integer $contaCredito reduzido
     * @param stdClass $dadosRecurso
     * @throws Exception
     */
    private function lancarValores($codigoLancamento, $valorLancamento, $contaDebito, $contaCredito, $dadosRecurso)
    {
        $dao = new cl_conlancamval;
        $dao->c69_anousu = $this->exercicio;
        $dao->c69_codlan = $codigoLancamento;
        $dao->c69_codhist = 8000; // DISPONIBILIDADE POR DESTINAÇÃO DE RECURSOS
        $dao->c69_debito = $contaDebito;
        $dao->c69_credito = $contaCredito;
        $dao->c69_valor = $valorLancamento;
        $dao->c69_data = $this->data;
        $dao->incluir(null);
        if ($dao->erro_status == "0") {
            throw new Exception("Erro ao salvar valores.\n" . str_replace('\n', '', $dao->erro_msg));
        }

        $sistemaAtributosConta = $this->getSistemaAtributosContas([$contaDebito, $contaCredito]);
        $atributosContaDebito = $this->filtraAtributosConta($sistemaAtributosConta, $contaDebito);
        $atributosContaCredito = $this->filtraAtributosConta($sistemaAtributosConta, $contaCredito);

        $this->salvarAtributosConta($dao, $atributosContaDebito, $dadosRecurso, true);
        $this->salvarAtributosConta($dao, $atributosContaCredito, $dadosRecurso, false);
    }

    /**
     * @param integer $codigoLancamento
     * @param integer $contaDebito
     * @param integer $idRecurso
     * @param string $naturea
     * @return void
     * @throws Exception
     */
    private function vincularRecursoConta($codigoLancamento, $contaDebito, $idRecurso, $naturea)
    {
        $dao = new \cl_conlancamrecurso();
        $dao->c130_conlancam = $codigoLancamento;
        $dao->c130_orctiporec = $idRecurso;
        $dao->c130_conta = $contaDebito;
        $dao->c130_anousu = $this->exercicio;
        $dao->c130_natureza = $naturea;
        $dao->incluir();

        if ($dao->erro_status == "0") {
            throw new Exception("Erro ao salvar recurso.");
        }
    }

    /**
     * Busca os atributos da Matriz e da Conta Corrente
     * @param array $contas
     * @return stdClass[]
     */
    private function getSistemaAtributosContas(array $contas)
    {
        $reduzidos = implode(', ', $contas);
        $sql = "
        select distinct c61_reduz, c122_sequencial, c121_sigla as sigla
          from conplanoatributos
          join conplano on (c60_codcon, c60_anousu) = (c120_conplano, c120_anousu)
          join conplanoreduz on (c61_codcon, c61_anousu) = (c60_codcon, c60_anousu)
          join conplanoinfocomplementar on c121_sequencial = c120_infocomplementar
          join conplanosistema on c122_sequencial = c120_conplanosistema
         where c61_reduz in  ({$reduzidos}) and c120_anousu = {$this->exercicio} order by 1, 2
        ";
        $rs = db_query($sql);

        return db_utils::getCollectionByRecord($rs);
    }

    /**
     * Filtra os atributos da conta e indexa pelo sistema da conta (Matriz/CC/DDR)
     * @param array $sistemaAtributosConta
     * @param integer $conta (reduzido da conta
     * @return array
     */
    public function filtraAtributosConta(array $sistemaAtributosConta, $conta)
    {
        $atributosSistema = [];
        collect($sistemaAtributosConta)->filter(function ($sistemaAtributos) use ($conta) {
            return $sistemaAtributos->c61_reduz === $conta;
        })->map(function ($sistemaAtributos) use (&$atributosSistema) {
            if (!array_key_exists($sistemaAtributos->c122_sequencial, $atributosSistema)) {
                $atributosSistema[$sistemaAtributos->c122_sequencial] = (object)['atributos' => []];
            }
            $atributosSistema[$sistemaAtributos->c122_sequencial]->atributos[] = $sistemaAtributos->sigla;
        });
        return $atributosSistema;
    }

    /**
     * Salva os atributos da conta corrente.
     * @param cl_conlancamval $dao
     * @param array $atributosConta
     * @param stdClass $dadosRecurso
     * @param boolean $debito
     * @return void
     * @throws Exception
     */
    private function salvarAtributosConta(
        cl_conlancamval $dao,
        array $atributosConta,
        stdClass $dadosRecurso,
        $debito = true
    ) {
        foreach ($atributosConta as $idSistema => $atributos) {
            $atributosSalvar = [];
            foreach ($atributos->atributos as $sigla) {
                switch ($sigla) {
                    case 'RV':
                    case 'FR':
                        $atributosSalvar[$sigla] = $dadosRecurso->o15_recurso;
                        break;
                    case 'PO':
                        $atributosSalvar[$sigla] = $this->codigoPO;
                        break;
                    case 'CO':
                        $atributosSalvar[$sigla] = $dadosRecurso->o200_sequencial;
                        break;
                    case 'DDR':
                        $atributosSalvar[$sigla] = $dadosRecurso->o15_codigo;
                        break;
                }
            }
            DBContaCorrenteAtributos::salvarAtributos(
                $dao,
                $idSistema,
                $atributosSalvar,
                $debito
            );
        }
    }
}
