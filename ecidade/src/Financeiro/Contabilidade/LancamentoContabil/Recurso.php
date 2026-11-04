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

namespace ECidade\Financeiro\Contabilidade\LancamentoContabil;

use cl_conlancamrecurso;
use ILancamentoAuxiliar;

/**
 * Class Recurso
 *
 * @package ECidade\Financeiro\Contabilidade\LancamentoContabil
 */
class Recurso
{
    private $UTILIZA_DOMICILIO_BANCARIO;

    public function __construct()
    {
        $this->UTILIZA_DOMICILIO_BANCARIO = (
        (isset($_SESSION["DB_utiliza_domicilio_bancario"])
            &&
            $_SESSION["DB_utiliza_domicilio_bancario"] == "t")
            ?
            true
            :
            false
        );
    }

    /**
     * Verifica o documento do
     *
     * @param $codigoLancamnento
     * @param ILancamentoAuxiliar|null $lancamentoAuxiliar
     * @throws \Exception
     */
    public function processar($codigoLancamnento, ILancamentoAuxiliar $lancamentoAuxiliar = null)
    {
        $dao = new \cl_conlancamdoc();
        $rs = db_query($dao->sql_query_file($codigoLancamnento, 'c71_coddoc'));
        if ($rs && pg_num_rows($rs) > 0) {
            $documento = \db_utils::fieldsMemory($rs, 0)->c71_coddoc;

            if (Documento::isTransferenciaCoberturaFinanceiro($documento)) {
                $this->salvarRecursoDocumento142($codigoLancamnento);
                return true;
            }
        }


        $tipoRecurso = new RecursoContaPagadora();

        if ($this->UTILIZA_DOMICILIO_BANCARIO) {
            $tipoRecurso = new RecursoOrigem();
        }
        $tipoRecurso->processar($codigoLancamnento, $lancamentoAuxiliar);
        return;
    }


    /**
     * @deprecated toda lógica de recurso esta sendo migrada para a classe: LancamentoRecurso
     * @see src/Financeiro/Contabilidade/LancamentoContabil/LancamentoRecurso.php
     * @param $codigoLancamnento
     * @return void
     * @throws \Exception
     */
    private function salvarRecursoDocumento142($codigoLancamnento)
    {
        $campos = "c69_anousu, c69_codlan, c69_credito, c69_debito, reduzcredito.c61_instit,
        reduzcredito.c61_codigo as recurso_credito, reduzdebito.c61_codigo as recurso_debito
        ";
        $dao = new \cl_conlancamval();
        $aql = $dao->sql_query_lancamentos($campos, "c69_codlan = {$codigoLancamnento} and c69_ordem = 1");
        $rs = db_query($aql);

        $lancamento = \db_utils::fieldsMemory($rs, 0);

        // recurso do primeiro lancamento
        $this->salvaRecursoLancamento($lancamento, $lancamento->c69_debito, $lancamento->recurso_debito, 'D');
        $this->salvaRecursoLancamento($lancamento, $lancamento->c69_credito, $lancamento->recurso_credito, 'C');

        $conta72111 = $this->getReduzido($lancamento->c69_anousu, $lancamento->c61_instit, '72111');
        $conta8211101 = $this->getReduzido($lancamento->c69_anousu, $lancamento->c61_instit, '8211101');

        // recurso do segundo lancamento
        $this->salvaRecursoLancamento($lancamento, $conta72111, $lancamento->recurso_debito, 'D');
        $this->salvaRecursoLancamento($lancamento, $conta8211101, $lancamento->recurso_debito, 'C');

        // recurso do terceiro lancamento
        $this->salvaRecursoLancamento($lancamento, $conta8211101, $lancamento->recurso_credito, 'D');
        $this->salvaRecursoLancamento($lancamento, $conta72111, $lancamento->recurso_credito, 'C');
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

    private function salvaRecursoLancamento($lancamento, $conta, $recurso, $natureza)
    {
        $dao = new cl_conlancamrecurso();
        $dao->c130_conlancam = $lancamento->c69_codlan;
        $dao->c130_anousu = $lancamento->c69_anousu;
        $dao->c130_conta = $conta;
        $dao->c130_orctiporec = $recurso;
        $dao->c130_natureza = "$natureza";
        $dao->incluir(null);

        if ($dao->erro_status == 0) {
            $msg = "Erro ao salvar dados do recurso do lançamento\n{$dao->erro_status}";
            throw new \Exception($msg);
        }
    }
}
