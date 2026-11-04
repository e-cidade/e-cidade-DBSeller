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

use ECidade\Financeiro\Orcamento\Recurso\Origem;
use Exception;

/**
 * Class Recurso
 *
 * @package ECidade\Financeiro\Contabilidade\LancamentoContabil
 */
class ComplementoRecurso
{
    /**
     * Verifica o documento do
     *
     * @param integer $codigoLancamento
     * @param integer $ano ano da sessao
     * @return bool
     * @throws Exception
     */
    public function processar($codigoLancamento, $ano)
    {
        $complemento = $this->getComplementoPorLancamento($codigoLancamento, $ano);
        /**
         * Adicionamos esse if pois receitas extras-orçamentária não salva conlancamrec
         * portanto não temos como obter o recurso da receita
         */
        if (empty($complemento) || $complemento->o15_codigo === '') {
            throw new Exception('Erro ao buscar o complemento do recurso para o lançamento.');
        }

        return $this->salvarComplementoRecurso($codigoLancamento, $complemento);
    }

    /**
     * @param $codigoLancamento
     * @param $ano
     * @return \stdClass|false
     * @throws Exception
     */
    public function getComplementoPorLancamento($codigoLancamento, $ano)
    {
        /**
         * Foi identificado que quando a receita possui desdobramento, devesse salvar o recurso da receita.
         */
        if ($this->isLancamentoReceitaComDesdobramento($codigoLancamento)) {
            return $this->retornaRecursoReceita($codigoLancamento);
        }

        $sql = "
        select orctiporec.*, complementofonterecurso.*
          from conlancamdoc
          join conlancamrec on c74_codlan = c71_codlan
          join orcreceita on (o70_anousu, o70_codrec) = (c74_anousu, c74_codrec)
          join orctiporec on o15_codigo = o70_codigo
          join complementofonterecurso on o200_sequencial = o15_complemento
         where c71_codlan = $codigoLancamento
           and c71_coddoc in (6000,  6001,  6006, 6007)
        ";
        $rs = db_query($sql);
        if (pg_num_rows($rs) > 0) {
            return \db_utils::fieldsMemory($rs, 0);
        }

        $daoEmpenho = new \cl_conlancamemp();
        $sqlBuscaEmpenho = $daoEmpenho->sql_query_file($codigoLancamento);
        $resBuscaEmpenho = db_query($sqlBuscaEmpenho);

        if (pg_num_rows($resBuscaEmpenho) > 0) {
            $numeroEmpenho = \db_utils::fieldsMemory($resBuscaEmpenho, 0)->c75_numemp;
            return Origem::getEmpenho($numeroEmpenho, $ano);
        }

        /**
         * Lançamento com origem de suplementação tem que pegar o recurso da receita/despesa.
         */
        $sql = "
        with origem_suplementacao as (
            select c70_codlan
              from conlancam
              join contabilidade.conlancamsup on c79_codlan = c70_codlan
            where c70_codlan = {$codigoLancamento}
        ), origem_recurso as (
            select o70_codigo as id_recurso
              from origem_suplementacao
              join conlancamrec on  c74_codlan = c70_codlan
              join orcreceita on orcreceita.o70_codrec = conlancamrec.c74_codrec
                   and orcreceita.o70_anousu = conlancamrec.c74_anousu

            union

            select o58_codigo as id_recurso
              from origem_suplementacao
              join conlancamdot on c73_codlan = c70_codlan
              join orcdotacao on (o58_anousu, o58_coddot) = (c73_anousu, c73_coddot)
        ) select orctiporec.*, complementofonterecurso.*
            from origem_recurso
            join orctiporec on o15_codigo = id_recurso
            join complementofonterecurso on o200_sequencial = o15_complemento
        ";

        $rs = db_query($sql);
        if (!$rs || pg_num_rows($rs) > 0) {
            return \db_utils::fieldsMemory($rs, 0);
        }

        /**
         * Alterado busca do recurso quando se trata de um lançamento de receita Orçamentária/Extra
         */
        $sql = "
        with origem_recurso as (
            select o70_codigo as recurso_receita
              from conlancamrec
              join orcreceita on orcreceita.o70_codrec = conlancamrec.c74_codrec
                   and orcreceita.o70_anousu = conlancamrec.c74_anousu
              where c74_codlan = {$codigoLancamento}
             union
            select c61_codigo as recurso_receita
              from conlancam
              join conlancamval on conlancamval.c69_codlan = conlancam.c70_codlan
                   and conlancamval.c69_ordem = 1
              join conplanoreduz on conplanoreduz.c61_reduz = conlancamval.c69_credito
                   and conplanoreduz.c61_anousu = conlancamval.c69_anousu
             where c70_codlan = {$codigoLancamento}
               and not exists( select 1 from conlancamrec where conlancamrec.c74_codlan = conlancam.c70_codlan)
        ) select orctiporec.*, complementofonterecurso.*
            from origem_recurso
            join orctiporec on o15_codigo = recurso_receita
            join complementofonterecurso on o200_sequencial = o15_complemento
        ";

        $rs = db_query($sql);
        if (!$rs || pg_num_rows($rs) === 0) {
            throw new Exception('Erro ao executar lançamento. Entre em contato com o suporte.');
        }

        return \db_utils::fieldsMemory($rs, 0);
    }


    /**
     * @param integer $codigoLancamento
     * @param \stdClass $complemento  exemplo {o200_sequencial: 0, o15_codigo: 1}
     * @return boolean
     * @throws Exception
     */
    public function salvarComplementoRecurso($codigoLancamento, $complemento)
    {
        if (!empty($complemento)) {
            $daoRecursoComplemento = new \cl_conlancamcomplementorecurso();
            $daoRecursoComplemento->o201_sequencial = null;
            $daoRecursoComplemento->o201_codlan = $codigoLancamento;
            $daoRecursoComplemento->o201_complemento = $complemento->o200_sequencial;
            $daoRecursoComplemento->o201_orctiporec = $complemento->o15_codigo;
            $daoRecursoComplemento->incluir(null);
            if ($daoRecursoComplemento->erro_status === '0') {
                $msg  = "Erro ao salvar dados do complemento do recurso ";
                $msg .= "do lançamento.\n\n{$daoRecursoComplemento->erro_status} ... Lanc: {$codigoLancamento} " ;
                throw new \Exception($msg);
            }

            $oDaoOrigem = new \cl_origemcomplementorecurso();
            $sWhere = "o206_numero = {$codigoLancamento} and o206_origem = 2";
            $sSqlVerificaVinculo = $oDaoOrigem->sql_query_file(null, "*", null, $sWhere);
            $rs = $oDaoOrigem->sql_record($sSqlVerificaVinculo);
            if ($oDaoOrigem->numrows <= 0) {
                $oDaoOrigem->o206_origem = 2;
                $oDaoOrigem->o206_numero = $codigoLancamento;
                $oDaoOrigem->o206_recurso = $complemento->o15_codigo;
                $oDaoOrigem->o206_complementorecurso = $complemento->o200_sequencial;
                $oDaoOrigem->incluir(null);
                if ($oDaoOrigem->erro_status === '0') {
                    $msg  = "Erro ao salvar dados da origem do complemento do recurso ";
                    $msg .= "do lançamento.\n\n{$oDaoOrigem->erro_status}";
                    throw new \Exception($msg);
                }
            }
            return true;
        }
        return false;
    }

    private function isLancamentoReceitaComDesdobramento($codigoLancamento)
    {
        $dao = new \cl_conlancamrec();
        $sql = "
        select 1
          from conlancamrec
          join orcreceita on (o70_anousu, o70_codrec) = (c74_anousu, c74_codrec)
          join orcfontesdes on  (o60_codfon, o60_anousu) = (o70_codfon, o70_anousu)
         where conlancamrec.c74_codlan = {$codigoLancamento}
        ";

        $rs = db_query($sql);
        if (pg_num_rows($rs) > 0) {
            return true;
        }
        return false;
    }

    /**
     * @param $codigoLancamento
     * @return \_db_fields|\stdClass
     * @throws Exception
     */
    private function retornaRecursoReceita($codigoLancamento)
    {
        $sql = "
            select o15_codigo, o200_sequencial, o15_recurso, o15_descr, o200_descricao
                from conlancamrec
                join orcreceita on (o70_anousu, o70_codrec) = (c74_anousu, c74_codrec)
                join orctiporec on orctiporec.o15_codigo = orcreceita.o70_codigo
                join complementofonterecurso on o200_sequencial = o15_complemento
             where c74_codlan = {$codigoLancamento}
            ";
        $rs = db_query($sql);
        if (!$rs) {
            throw new Exception('Erro ao buscar recurso da receita.');
        }

        return \db_utils::fieldsMemory($rs, 0);
    }
}
