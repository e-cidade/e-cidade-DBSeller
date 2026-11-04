<?php

namespace App\Domain\Financeiro\Empenho\Services;

use App\Domain\Financeiro\Empenho\Models\RetencaoReceitasSubcontratacao;
use Illuminate\Database\Capsule\Manager as DB;
use Exception;

class RetencaoReceitasSubcontratacaoService
{
    /**
     * Update subcontratacao
     *
     * @param object $data
     * @return void
     */
    public function update($data)
    {
        $oSubcontratacao = RetencaoReceitasSubcontratacao::findOrFail($data->e163_sequencial);
        $oSubcontratacao->e163_valor = $data->e163_valor;
        $oSubcontratacao->e163_valorbase = $data->e163_valorbase;
        $oSubcontratacao->e163_numcgm = $data->e163_numcgm;
        $oSubcontratacao->e163_retencaotiporec = $data->e163_retencaotiporec;
        $oSubcontratacao->save();
    }

    /**
     * Delete subcontratacao
     *
     * @param object $data
     * @return void
     */
    public function delete($data)
    {
        $oSubcontratacao = RetencaoReceitasSubcontratacao::findOrFail($data->e163_sequencial);
        $oSubcontratacao->delete();
    }

    /**
     * Helper para salvar em rotinas legadas
     *
     * @param object $data
     * @return void
     */
    public function saveWithOldConnection($data)
    {
        if (!$this->validateData($data)) {
            throw new Exception('Erro ao validar subcontratacao.');
        }

        $sql = "
            insert into retencaoreceitassubcontratacao (
            e163_retencaoreceitas,
            e163_valor,
            e163_valorbase,
            e163_retencaotiporec,
            e163_numcgm)
            values (
                {$data->e163_retencaoreceitas},
                {$data->e163_valor},
                {$data->e163_valorbase},
                {$data->e163_retencaotiporec},
                {$data->e163_numcgm}
        )";

        $rs = db_query($sql);
        if (!$rs) {
            throw new Exception(pg_last_error());
        }
    }

    /**
     * Validar dados
     *
     * @param object $data
     * @return bool
     */
    private function validateData($data)
    {
        // numcgm
        if (empty($data->e163_numcgm)) {
            return false;
        }

        // retencao
        if (empty($data->e163_retencaotiporec)) {
            return false;
        }

        // retencaoreceitas
        if (empty($data->e163_retencaoreceitas)) {
            return false;
        }

        return true;
    }

    /**
     * Query para detalhamento
     *
     * @param int $retencao - e23_sequencial
     * @return array|null
     */
    public function getSubcontratacoesByRetencao($retencao)
    {
        $data = DB::select("
        select concat(empempenho.e60_codemp, '/', empempenho.e60_anousu) as empenho_numero,
        contratado.z01_numcgm as cgm_contratado,
        subcontratado.z01_numcgm as cgm_subcontratado,
        e23_dtcalculo as data_apropriacao,
        contratado.z01_cgccpf as cpf_cnpj_contratado,
        e23_valorretencao as valorirrfretido_contratado,
        e50_numemp as empenho,
        e50_codord as op,
        contratado.z01_nome as nome_contratado,
        subcontratado.z01_nome as nome_subcontratado,
        subcontratado.z01_cgccpf as cpf_cnpj_subcontratado,
        e21_descricao as retencao_descricao,
        e21_sequencial as codigo_retencao,
        e163_valor as valorirrfretido_subcontratado
        from empenho.retencaoreceitassubcontratacao
        inner join retencaoreceitas on e163_retencaoreceitas = retencaoreceitas.e23_sequencial
        inner join retencaotiporec on e163_retencaotiporec = retencaotiporec.e21_sequencial
        inner join retencaopagordem on retencaoreceitas.e23_retencaopagordem = retencaopagordem.e20_sequencial
        inner join pagordem on retencaopagordem.e20_pagordem = pagordem.e50_codord
        inner join empempenho on empempenho.e60_numemp = pagordem.e50_numemp
        inner join cgm as subcontratado on e163_numcgm = subcontratado.z01_numcgm
        inner join cgm as contratado on e60_numcgm = contratado.z01_numcgm
        where retencaoreceitas.e23_sequencial = {$retencao}
        ");

        return $data;
    }

    /**
     * Query recuperacao db na retencao nota model
     *
     * @param int $retencao e23_sequencial
     * @return object
     */
    public function getSubcontratacao($retencao)
    {
        $data = DB::table('retencaoreceitassubcontratacao')
            ->select([
                'retencaoreceitassubcontratacao.*',
                'cgm.z01_nome as subcontratado',
                'retencaotiporec.e21_descricao as retencao'
            ])
            ->join('retencaotiporec', 'e21_sequencial', '=', 'e163_retencaotiporec')
            ->join('cgm', 'z01_numcgm', '=', 'e163_numcgm')
            ->where('e163_retencaoreceitas', $retencao)
            ->get();

        return $data;
    }

    public function getLancamentosByRetencao($retencao)
    {
        $data = DB::select("
        select c70_codlan                       as codigo,
               c70_data                         as data,
               c53_coddoc || ' - ' || c53_descr as documento,
               c70_valor                        as valor
        from conlancam
            inner join conlancamdoc on c71_codlan = c70_codlan
            inner join conhistdoc on c53_coddoc = c71_coddoc
            inner join conlancamord on c80_codlan = c70_codlan
            inner join pagordem on e50_codord = c80_codord
            inner join retencaopagordem on e20_pagordem = e50_codord
            inner join retencaoreceitas on e23_retencaopagordem = e20_sequencial
            inner join conlancamretencao on c127_conlancam = c70_codlan
        where e23_sequencial = {$retencao} order by c70_codlan
    ");
        return $data;
    }

    public function getRecolhimentoByRetencao($retencao)
    {
        $data = DB::select("
       select k108_slip as slip,
       k107_valor as valor
from retencaoreceitas
    inner join retencaoempagemov on e27_retencaoreceitas = e23_sequencial
    inner join empagemovslips on k107_empagemov = e27_empagemov
    inner join slipempagemovslips on k108_empagemovslips = k107_sequencial
where e23_sequencial = {$retencao}
");
        return $data;
    }
}
