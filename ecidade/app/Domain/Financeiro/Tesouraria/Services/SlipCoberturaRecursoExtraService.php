<?php

namespace App\Domain\Financeiro\Tesouraria\Services;

use App\Domain\Financeiro\Contabilidade\Models\ConplanoReduzido;
use cl_transferencia_cobertura_extra_placaixarec;
use Exception;
use Illuminate\Support\Facades\DB;
use slip;

class SlipCoberturaRecursoExtraService
{

    /**
     * @throws Exception
     */
    public function gerarSlips($dados, $dataInicial, $dataFinal = null)
    {
        if (empty($dataFinal)) {
            $dataFinal = date('d/m/Y');
        }
        $exercicio = date('Y');
        $observacao = "Transferência bancária para fins de cobertura financeira dos recursos extra orçamentários ";
        $observacao .= "para o pagamento de depósitos e consignações, apropriados entre {$dataInicial} e {$dataFinal}.";

        $slipsGerados = [];
        foreach ($dados as $dado) {
            /**
             * @todo buscar o cgm da conta $dado->creditar
             */

            $reduz = ConplanoReduzido::query()
                ->where('c61_reduz', $dado['creditar'])
                ->where('c61_anousu', $exercicio)
                ->with('instituicao')
                ->first();

            $composicaoObs = [];
            $origemPlanilha = [];
            $origemEmpenho = [];

            foreach ($dado['composisao'] as $composicao) {
                if ($composicao['origem'] === 'Empenho') {
                    $obs = "OP: {$composicao['identificador']}";
                    $origemEmpenho[] = $composicao['id'];
                }
                if ($composicao['origem'] === 'Planilha') {
                    $origemPlanilha[] = $composicao['id'];
                    $obs = "Planilha: {$composicao['identificador']}";
                }

                $composicaoObs[] = $obs;
            }
            $this->validaGeracaoSlipPlanilha($origemPlanilha);
            $this->validaGeracaoSlipRetencao($origemEmpenho);

            $observacaoSlip = sprintf(
                '%s Valores Composto de: %s',
                $observacao,
                implode(', ', $composicaoObs)
            );

            $slip = new Slip();
            $slip->setContaCredito($dado['creditar']);
            $slip->setContaDebito($dado['debitar']);
            $slip->setCaracteristicaPeculiarCredito("000");
            $slip->setCaracteristicaPeculiarDebito("000");
            $slip->setValor($dado['valor']);
            $slip->setTipoPagamento(3);
            $slip->setSituacao(1);
            $slip->setNumCgm($reduz->instituicao->numcgm);
            $slip->setHistorico(9140);
            $slip->setObservacoes($observacaoSlip);
            $slip->save();
            Slip::vincularTipoOperacaoSlip($slip->getSlip(), 17);

            $dao = new \cl_transferencia_cobertura_extra();
            $dao->slip_id = $slip->getSlip();
            $dao->incluir(null);

            if ($dao->erro_status == 0) {
                throw new Exception('Erro ao salvar vínculo do slip de Cobertura dos Recursos extra-orçamentários.');
            }

            foreach ($dado['composisao'] as $composicao) {
                $this->atualizaContasEmpagemovslips($composicao['id'], $dado['creditar'], $dado['debitar']);

                if ($composicao['origem'] === 'Empenho') {
                    $this->vincularApropriacaoRetencao($dao->id, $composicao['id']);
                }

                if ($composicao['origem'] === 'Planilha') {
                    $this->vincularApropriacaoPlanilha($dao->id, $composicao['id'], $slip->getSlip());
                }
            }

            $slipsGerados[] = $slip->getSlip();
        }
        return $slipsGerados;
    }

    /**
     * Criado para atualizar as contas na empagemovslips
     * @param integer $id
     * @param integer $creditar
     * @param integer $debitar
     * @return void
     * @throws Exception
     */
    private function atualizaContasEmpagemovslips($id, $creditar, $debitar)
    {
        $sql = sprintf(
            'update empagemovslips set k107_ctadebito = %s, k107_ctacredito = %s where k107_sequencial = %s',
            $debitar,
            $creditar,
            $id
        );
        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception('Erro ao atualizar empagemovslips.');
        }
    }

    /**
     * Vincula o movimento do empenho.
     * @param $idTransferenciaCoberturaExtra
     * @param $idEmpagemovSlips
     * @return void
     * @throws Exception
     */
    private function vincularApropriacaoRetencao($idTransferenciaCoberturaExtra, $idEmpagemovSlips)
    {
        $dao = new \cl_transferencia_cobertura_extra_empagemovslips();
        $dao->transferencia_cobertura_extra_id = $idTransferenciaCoberturaExtra;
        $dao->empagemovslips_id = $idEmpagemovSlips;
        $dao->incluir(null);

        if ($dao->erro_status == 0) {
            throw new Exception('Erro ao salvar vínculo do movimento a Cobertura dos Recursos extra-orçamentários.');
        }
    }

    /**
     * Vincula o item da planilha ao slip de transferência ban
     * @param integer $idTransferenciaCoberturaExtra
     * @param integer $idPlacaixarec
     * @param integer $idSlip
     * @return void
     * @throws Exception
     */
    private function vincularApropriacaoPlanilha($idTransferenciaCoberturaExtra, $idPlacaixarec, $idSlip)
    {
        $dao = new cl_transferencia_cobertura_extra_placaixarec();
        $dao->transferencia_cobertura_extra_id = $idTransferenciaCoberturaExtra;
        $dao->placaixarec_id = $idPlacaixarec;
        $dao->incluir(null);

        if ($dao->erro_status == 0) {
            throw new Exception('Erro ao salvar vínculo da planilha a Cobertura dos Recursos extra-orçamentários.');
        }
        Slip::vincularSlipReceitaPlanilha($idSlip, $idPlacaixarec);
    }

    private function validaGeracaoSlipPlanilha(array $origemPlanilha)
    {
        if (!empty($origemPlanilha)) {
            $where = " placaixarec_id in (" . implode(', ', $origemPlanilha) . ")";
            $dao = new cl_transferencia_cobertura_extra_placaixarec();
            $rs = db_query($dao->sql_query(null, '1', null, $where));
            if ($rs && pg_num_rows($rs) > 0) {
                $msg = 'Não é possível gerar os slips pois um ou mais itens da planilhas já foram gerados.';
                throw new Exception($msg, 405);
            }
        }
        return true;
    }

    private function validaGeracaoSlipRetencao(array $origemEmpenho)
    {
        if (!empty($origemEmpenho)) {
            $where = " empagemovslips_id in (" . implode(', ', $origemEmpenho) . ")";
            $dao = new \cl_transferencia_cobertura_extra_empagemovslips();
            $rs = db_query($dao->sql_query(null, '1', null, $where));
            if ($rs && pg_num_rows($rs) > 0) {
                throw new Exception('Não é possível gerar os slips pois uma ou mais OPs já foram gerados.', 405);
            }
        }
        return true;
    }

    /**
     * @param integer $instituicao
     * @param integer $tipo 0 - Todos empenhos, 1 - Folha de Pagamento, 2 - Fornecedores
     * @param integer $situacao 0 - Ambas, 1 - Apenas apropriação da retenção, 2 - Líquido da OP paga ao credor
     * @param string $dataInicial
     * @param string $dataFinal
     * @return mixed
     * @throws Exception
     */
    public function getAproriacoesRetencoes($instituicao, $tipo, $situacao, $dataInicial, $dataFinal = null)
    {
        $where = [
            "(  not exists(
                 select 1 from transferencia_cobertura_extra_empagemovslips
                  where empagemovslips_id = k107_sequencial
                )
                or exists(
                  select 1
                    from transferencia_cobertura_extra_empagemovslips m
                    join transferencia_cobertura_extra t on t.id = m.transferencia_cobertura_extra_id
                    join slip on slip.k17_codigo = t.slip_id
                  where empagemovslips_id = k107_sequencial
                    and k17_dtanu is not null
               )
             )",
            "e60_instit = $instituicao",
            "k02_tipo = 'E'",
            "k107_data >= '{$dataInicial}'"
        ];
        if (!is_null($dataFinal)) {
            $where[] = "k107_data <= '{$dataFinal}'";
        }

        if ((int)$tipo === 1) {
            $where[] = " exists (select 1 from rhempenhofolhaempenho where rh76_numemp = e60_numemp) ";
        }
        if ((int)$tipo === 2) {
            $where[] = " not exists (select 1 from rhempenhofolhaempenho where rh76_numemp = e60_numemp) ";
        }

        $outrosFiltros = '';
        if ((int)$situacao === 1) {
            $outrosFiltros = "where exists (
             select 1
               from empord ord
               join empagemov mov on mov.e81_codmov = ord.e82_codmov
               join pagordemele on e53_codord = ord.e82_codord
               where ord.e82_codord = empord.e82_codord
                 and e53_valor != e53_vlrpag
             )";
        }
        if ((int)$situacao === 2) {
            $outrosFiltros = "where exists (
             select 1
               from empord ord
               join empagemov mov on mov.e81_codmov = ord.e82_codmov
               join pagordemele on e53_codord = ord.e82_codord
               where ord.e82_codord = empord.e82_codord
                 and e53_valor = e53_vlrpag
            ) ";
        }

        $where = implode(' and ', $where);

        $sql = "
        with retencoes as (
          select k107_sequencial as empagemovslips_id,
                 k107_empagemov as movimento,
                 k107_data as data_apropriacao,
                 k02_codigo as receita_tesouraria,
                 k02_descr as descricao_receita,
                 e21_descricao as tipo_retencao,
                 e23_valorretencao as valor,
                 extract(YEAR FROM k107_data) as exercicio,
                 e21_sequencial
            from empagemovslips
            join retencaoreceitas on e23_sequencial = k107_retencao
            join retencaotiporec on e21_sequencial = e23_retencaotiporec
            join empagemov on e81_codmov = k107_empagemov
            join empempenho on e60_numemp = e81_numemp
            join tabrec on k02_codigo = e21_receita
           where {$where}
         ),dados as (
            select retencoes.*,
                   e82_codord AS op,
                   e48_cgm AS cgm,
                   z01_nome AS nome_credor,
                   o58_codigo as recurso_op,
                   cc.k13_conta AS creditar,
                   cc.k13_descr AS creditar_descricao,
                   k109_contaextra AS debitar,
                   cd.k13_descr AS debitar_descricao,
                   c61_codigo AS recurso,
                   codigo_siconfi AS siconfi,
                   o15_recurso AS subrecurso,
                   o15_complemento AS complemento
            from retencoes
            join retencaotiporeccgm on e48_retencaotiporec = e21_sequencial
            join empord on e82_codmov = movimento
            join pagordem on e50_codord = e82_codord
            join empempenho on e60_numemp = e50_numemp
            join orcdotacao on (o58_anousu, o58_coddot) = (e60_anousu, e60_coddot)
            join orctiporec on o15_codigo = o58_codigo
            join fonterecurso on orctiporec_id = o15_codigo
                 and fonterecurso.exercicio = o58_anousu
            join cgm on z01_numcgm = e48_cgm
            left join empagepag on empagepag.e85_codmov = movimento
            left join empagetipo on empagetipo.e83_codtipo = empagepag.e85_codtipo
            left join conplanoreduz on c61_reduz = empagetipo.e83_conta
                      and c61_anousu = retencoes.exercicio
                      and c61_instit = o58_instit
            left join saltes cc on cc.k13_conta = c61_reduz
            left join saltesextra on saltesextra.k109_saltes = cc.k13_conta
            left join saltes cd on cd.k13_conta = saltesextra.k109_contaextra
            {$outrosFiltros}
        ) select * from dados;
        ";

        $dados = DB::select($sql);
        if (empty($dados)) {
            throw new Exception('Não foram encontrados registros de empenhos para gerar as transferências.', 206);
        }

        return $dados;
    }

    public function getapropriacaoReceitaExtra($instituicao, $dataInicial, $dataFinal = null)
    {
        $where = [
            "(  not exists(
                 select 1 from transferencia_cobertura_extra_placaixarec
                  where placaixarec_id = k81_seqpla
                )
                or exists(
                  select 1
                    from transferencia_cobertura_extra_placaixarec m
                    join transferencia_cobertura_extra t on t.id = m.transferencia_cobertura_extra_id
                    join slip on slip.k17_codigo = t.slip_id
                   where placaixarec_id = k81_seqpla
                     and k17_dtanu is not null
                )
            )",
            "k81_conta != k109_contaextra", // filtra para que não traga contas iguais a debito e credito
            "c61_instit = $instituicao",
            "tabrec.k02_tipo = 'E'",
            "k80_dtaut >= '{$dataInicial}'",
        ];
        if (!is_null($dataFinal)) {
            $where[] = "k80_dtaut <= '{$dataFinal}'";
        }
        $where = implode(' and ', $where);

        $sql = "
        select
               k81_codpla as planilha,
               k81_seqpla as lancamento,
               k81_numcgm as cgm,
               z01_nome as nome_credor,
               k80_dtaut as data_apropriacao,
               k81_conta as creditar, -- conta_bancaria
               cc.k13_descr as creditar_descricao,
               k109_contaextra as debitar,
               cd.k13_descr as debitar_descricao,
               c61_codigo as recurso,
               codigo_siconfi as siconfi,
               o15_recurso as subrecurso,
               o15_complemento as complemento,
               k81_valor as valor
          from planilha_gerar_slips_cobertura_extra
          join placaixarec on placaixarec.k81_seqpla = planilha_gerar_slips_cobertura_extra.placaixarec_id
          join placaixa on k80_codpla = placaixarec.k81_codpla
          join tabrec on tabrec.k02_codigo = k81_receita
          join conplanoreduz on c61_reduz = placaixarec.k81_conta
               and c61_anousu = extract(year from placaixa.k80_dtaut)
          join orctiporec on o15_codigo = c61_codigo
          join fonterecurso on orctiporec_id = o15_codigo and exercicio = c61_anousu
          join saltes cc on cc.k13_conta = placaixarec.k81_conta
          join saltesextra on saltesextra.k109_saltes = cc.k13_conta
          join saltes cd on cd.k13_conta = saltesextra.k109_contaextra
          join cgm on z01_numcgm = k81_numcgm
          where {$where}
        ";
        $dados = DB::select($sql);

        if (empty($dados)) {
            throw new Exception('Não foram encontrados registros de planilhas para gerar as transferências.', 206);
        }

        return $dados;
    }
}
