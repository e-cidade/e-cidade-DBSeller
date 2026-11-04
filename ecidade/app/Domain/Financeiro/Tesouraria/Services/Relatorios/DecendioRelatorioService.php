<?php

namespace App\Domain\Financeiro\Tesouraria\Services\Relatorios;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Financeiro\Contabilidade\Models\ConplanoReduzido;
use App\Domain\Financeiro\Tesouraria\Models\Decendio;
use App\Domain\Financeiro\Tesouraria\Models\DecendioLancamento;
use App\Domain\Financeiro\Tesouraria\Relatorios\Decendio\DecendioPdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DecendioRelatorioService
{

    private $reduzidos = [];
    /**
     * @var DBConfig
     */
    private $instituicao;

    private function processa($dataInicial, $dataFinal)
    {
        $campos = ['k230_codigo', 'k17_codigo', 'k17_data', 'k17_debito', 'k17_credito', 'k17_valor', 'k17_texto'];

        return \App\Domain\Financeiro\Tesouraria\Models\Decendio::query()
            ->select($campos)
            ->selectRaw('extract(year from k17_data) as exercicio')
            ->with('lancamentos.tipoDesdobramento')
            ->with('lancamentos.lancamento')
            ->join('caixa.slip', 'k17_codigo', 'k230_slip')
            ->where('k17_data', '>=', $dataInicial)
            ->where('k17_data', '<=', $dataFinal)
            ->where('k17_instit', '=', $this->instituicao->codigo)
            ->orderBy('k230_slip')
            ->get()
            ->map(function (Decendio $decendio) {
                return $this->stdDecendio($decendio);
            });
    }

    public function emitir($dataInicial, $dataFinal, $instituicao)
    {
        $this->instituicao = DBConfig::find($instituicao);
        $dados = $this->processa($dataInicial, $dataFinal);

        $periodo = (object)[
            'dataInicial' => Carbon::parse($dataInicial)->format("d/m/Y"),
            'dataFinal' => Carbon::parse($dataFinal)->format("d/m/Y"),

        ];
        $pdf = new DecendioPdf();

        return $pdf->setDados($dados->toArray())
            ->headers('Relatório de Decêndio', $periodo, $this->instituicao->nomeinst)
            ->emitir();
    }

    /**
     * @param Decendio $decendio
     * @return object
     */
    private function stdDecendio(Decendio $decendio)
    {
        return (object)[
            'slip' => $decendio->k17_codigo,
            'data' => Carbon::parse($decendio->k17_data)->format("d/m/Y"),
            'debito' => $this->processaReduzido($decendio->k17_debito, $decendio->exercicio),
            'credito' => $this->processaReduzido($decendio->k17_credito, $decendio->exercicio),
            'valor' => $decendio->k17_valor,
            'observacao' => $decendio->k17_texto,
            'desdobramento' => $this->processaTipoDesdobramento($decendio->lancamentos[0]),
            'lancamentos' => $this->processaLancamentos($decendio->lancamentos)
        ];
    }

    /**
     * @param int $reduzido
     * @param int $exercicio
     * @return object
     */
    private function processaReduzido($reduzido, $exercicio)
    {
        if (!array_key_exists($reduzido, $this->reduzidos)) {
            $reduz = ConplanoReduzido::query()
                ->select(['c61_reduz', 'c60_estrut', 'c60_descr'])
                ->join('contabilidade.conplano', function ($join) {
                    $join->on('c60_codcon', '=', 'c61_codcon')
                        ->on('c60_anousu', '=', 'c61_anousu');
                })
                ->where('c61_anousu', $exercicio)
                ->where('c61_reduz', $reduzido)
                ->where('c61_instit', $this->instituicao->codigo)
                ->first();

            $this->reduzidos[$reduzido] = (object)[
                'reduzido' => $reduz->c61_reduz,
                'estrutural' => $reduz->c60_estrut,
                'descricao' => $reduz->c60_descr,
            ];
        }

        return $this->reduzidos[$reduzido];
    }

    private function processaTipoDesdobramento(DecendioLancamento $lancamento)
    {
        return $lancamento->tipoDesdobramento->k222_descricao;
    }

    private function processaLancamentos($lancamentos)
    {
        return $lancamentos->map(function ($lancamento) {
            $estrutural = $this->getEstruturalReceita($lancamento->lancamento->c70_codlan);
            $documento = $lancamento->lancamento->documentoLancamento->documento;

            $valor = $lancamento->lancamento->c70_valor;
            $valor = $documento->c53_tipo === 101 ? $valor * -1 : $valor;
            return (object)[
                'estrutural' => $estrutural->o57_fonte,
                'lancamento' => $lancamento->lancamento->c70_codlan,
                'documento' => sprintf('%s - %s', $documento->c53_coddoc, $documento->c53_descr),
                'extorno' => $documento->c53_tipo === 101,
                'data' => $lancamento->lancamento->c70_data->format('d/m/Y'),
                'valor' => $valor,
            ];
        });
    }

    private function getEstruturalReceita($c70_codlan)
    {
        return DB::table('contabilidade.conlancamrec')
            ->select('o57_fonte')
            ->join('orcamento.orcreceita', function ($join) {
                $join->on('o70_codrec', '=', 'c74_codrec')
                    ->on('o70_anousu', '=', 'c74_anousu');
            })
            ->join('orcamento.orcfontes', function ($join) {
                $join->on('o57_codfon', '=', 'o70_codfon')
                    ->on('o57_anousu', '=', 'o70_anousu');
            })
            ->where('c74_codlan', $c70_codlan)
            ->first();
    }
}
