<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Fix;

use App\Domain\Financeiro\Contabilidade\Services\LancamentoManualService;
use ECidade\File\Csv\LerCsv;
use Illuminate\Http\UploadedFile;

class LoteLancamentoManualService
{

    /**
     * 0 => "credito"
     * 1 => "debito"
     * 2 => "historico"
     * 3 => "observacao"
     * 4 => "recursoCredito"
     * 5 => "recursoDebito"
     * 6 => "valor"
     *
     * @param UploadedFile $file
     * @return void
     */

    public function processarCsv(UploadedFile $file)
    {
        $processar = $this->buildDados($file);
        $this->criarLancamentos($processar);
    }

    private function buildDadosConta($reduzido, $recurso)
    {
        return [
            'reduzido' => $reduzido,
            'recurso_id' => $recurso,
            'exercicio' => session('DB_anousu'),
            'instituicao' => session('DB_instit'),
        ];
    }

    private function criarLancamentos(array $dados)
    {
        $service = new LancamentoManualService();
        $service->criarLancamentos($dados);
    }

    /**
     * @param UploadedFile $file
     * @return array
     */
    public function buildDados(UploadedFile $file)
    {
        $service = new LancamentoManualService();

        $processar = [
            'data' => date('Y-m-d', session('DB_datausu')),
            'exercicio' => session('DB_anousu'),
            'instituicao' => session('DB_instit'),
            'lancamentos' => [],
            'numeroLote' => $service->proximoLote(session('DB_anousu'), session('DB_instit'))
        ];

        foreach ((new LerCsv($file->path()))->read() as $id => $dadosLinha) {
            if ($id === 0) {
                continue;
            }

            $valor = str_replace(['.', ','], ['', '.'], $dadosLinha[6]);

            $lancamento = [
                'credito' => $this->buildDadosConta($dadosLinha[0], $dadosLinha[4]),
                'debito' => $this->buildDadosConta($dadosLinha[1], $dadosLinha[5]),
                'historico' => [
                    'codigo' => $dadosLinha[2],
                ],
                'recursoCredito' => ['orctiporec_id' => $dadosLinha[4]],
                'recursoDebito' => ['orctiporec_id' => $dadosLinha[5]],
                'observacao' => $dadosLinha[3],
                'valor' => $valor,
                'ajuste_saldo' => $this->buildAjusteSaldo($dadosLinha)
            ];

            $processar['lancamentos'][] = $lancamento;
        }
        return $processar;
    }

    private function buildAjusteSaldo(array $dadosLinha)
    {
        return [
            'funcao' => $dadosLinha[7],
            'subfuncao' => $dadosLinha[8],
            'elemento' => $dadosLinha[9],
            'ai' => $dadosLinha[10],
            'nr' => $dadosLinha[11]
        ];
    }
}
