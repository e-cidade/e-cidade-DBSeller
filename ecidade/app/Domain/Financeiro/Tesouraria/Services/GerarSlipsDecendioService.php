<?php

namespace App\Domain\Financeiro\Tesouraria\Services;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use Carbon\Carbon;
use cl_decendio;
use cl_decendiolancamento;
use Exception;
use slip;

class GerarSlipsDecendioService
{
    public function gerarSlips(array $dados, $exercicio, $dataInicial, $dataFinal)
    {
        $observacao = sprintf(
            "Decêndio apurado no período de %s a %s",
            Carbon::parse($dataInicial)->format("d/m/Y"),
            Carbon::parse($dataFinal)->format("d/m/Y")
        );

        $slipsGerados = [];

        $dadosAgrupados = [];
        foreach ($dados as $dado) {
            $this->validarGeracaoLancamentos($dado['lancamentos'], $dado['desdobramento']['codigo']);
            $key = sprintf('%s#%s', $dado['conta_pagadora']['reduzido'], $dado['contaSelecionada']['conta']);
            if (!array_key_exists($key, $dadosAgrupados)) {
                $cgmInstituicao = DBConfig::select('numcgm')->find($dado['instituicao']);
                $dadosAgrupados[$key] = (object) [
                    'credito' => $dado['conta_pagadora']['reduzido'],
                    'debito' => $dado['contaSelecionada']['conta'],
                    'cgm' => $cgmInstituicao->numcgm,
                    'observacao' => $observacao,
                    'desdobramento' => $dado['desdobramento']['codigo'],
                    'lancamentos' => [],
                    'valor' => 0
                ];
            }

            $dadosAgrupados[$key]->lancamentos = array_merge($dadosAgrupados[$key]->lancamentos, $dado['lancamentos']);
            $dadosAgrupados[$key]->valor += $dado['desdobramento']['valor'];
        }

        foreach ($dadosAgrupados as $dado) {
            $slip = new Slip();
            $slip->setContaCredito($dado->credito);
            $slip->setContaDebito($dado->debito);
            $slip->setCaracteristicaPeculiarCredito("000");
            $slip->setCaracteristicaPeculiarDebito("000");
            $slip->setValor($dado->valor);
            $slip->setTipoPagamento(3);
            $slip->setSituacao(1);
            $slip->setNumCgm($dado->cgm);
            $slip->setHistorico(9140);
            $slip->setObservacoes($dado->observacao);
            $slip->save();

            Slip::vincularTipoOperacaoSlip($slip->getSlip(), 17);

            $dao = new cl_decendio();
            $dao->k230_slip = $slip->getSlip();
            $dao->created_at = date('Y-m-d H:i:s');
            $dao->updated_at = date('Y-m-d H:i:s');
            $dao->incluir(null);
            if ($dao->erro_status == 0) {
                throw new Exception('Erro ao salvar vínculo do slip de Decêndio.');
            }

            $this->salvarLancamentosDecendio($dao->k230_codigo, $dado->lancamentos, $dado->desdobramento);

            $slipsGerados[] = $slip->getSlip();
        }

        return $slipsGerados;
    }

    /**
     * @param integer $lancamentos
     * @param integer $desdobramento
     * @return true
     * @throws Exception
     */
    private function validarGeracaoLancamentos($lancamentos, $desdobramento)
    {
        $where = " k231_lancamento in (" . implode(', ', $lancamentos) . ")";
        $where .= " and k231_desdobramento = {$desdobramento}";
        $dao = new cl_decendiolancamento();
        $rs = db_query($dao->sql_query(null, '1', null, $where));
        if ($rs && pg_num_rows($rs) > 0) {
            $msg = 'Não é possível gerar os slips pois um ou mais lançamento já foi processado.';
            throw new Exception($msg, 405);
        }

        return true;
    }

    private function salvarLancamentosDecendio($id, $lancamentos, $desdobramento)
    {
        foreach ($lancamentos as $lancamento) {
            $dao = new cl_decendiolancamento();
            $dao->k231_decendio = $id;
            $dao->k231_lancamento = $lancamento;
            $dao->k231_desdobramento = $desdobramento;
            $dao->created_at = date('Y-m-d H:i:s');
            $dao->updated_at = date('Y-m-d H:i:s');
            $dao->incluir(null);
            if ($dao->erro_status == 0) {
                throw new Exception('Erro ao salvar lançamento do decêndio.');
            }
        }
    }
}
