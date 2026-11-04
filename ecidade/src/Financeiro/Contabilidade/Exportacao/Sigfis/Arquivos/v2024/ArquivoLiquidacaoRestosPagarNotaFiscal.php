<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;

class ArquivoLiquidacaoRestosPagarNotaFiscal extends ArquivoBase
{
    protected $sNomeArquivo = 'LiquidacaoRestosPagarNotaFiscal';

    public function gerarDados()
    {
        $campos = [
            'e60_numemp', 'e60_codemp', 'e60_anousu',
            'o58_orgao', 'o58_unidade',
            'e50_codord',
            'e69_codnota', 'e69_anousu',
            'c70_codlan',
            'e70_valor',
            'c70_data',
            'e50_obs',
            'e69_anousu', 'e69_numero', 'e69_serienota', 'e69_dtnota', 'e69_dtrecebe', 'e69_outrosdados',
        ];
        $empenhos = DB::table('empresto')
            ->select($campos)
            ->join('empempenho', 'e60_numemp', 'e91_numemp')
            ->join('conlancamemp', 'c75_numemp', 'e60_numemp')
            ->join('conlancam', 'c70_codlan', 'c75_codlan')
            ->join('conlancamdoc', 'c71_codlan', 'c70_codlan')
            ->join('conhistdoc', function (JoinClause $join) {
                $join->on('c53_coddoc', 'c71_coddoc')
                ->where('c53_tipo', 20);
            })
            ->join('conlancamnota', 'c66_codlan', 'c70_codlan')
            ->join('conlancamord', 'c80_codlan', 'c70_codlan')
            ->join('empnota', function (JoinClause $join) {
                $join->on('empnota.e69_numemp', 'empempenho.e60_numemp')
                        ->on('e69_codnota', 'c66_codnota');
            })            ->join('empnotaele', 'e70_codnota', 'e69_codnota')
            ->join('pagordem', 'e50_codord', 'c80_codord')
            ->join('pagordemnota', function (JoinClause $join) {
                $join->on('e71_codnota', 'e69_codnota')
                    ->on('e71_codord', 'e50_codord');
            })
            ->join('empnotasigfistipodocliquidacao', 'e178_empnota', 'e69_codnota')
            ->join('sigfistipodocliquidacao', 'e177_sequencial', 'e178_sigfistipodocliquidacao')
            ->join('orcdotacao', function (JoinClause $join) {
                $join->on('o58_anousu', 'e60_anousu')
                    ->on('o58_coddot', 'e60_coddot');
            })
            ->where('e91_anousu', $this->iAnoUsu)
            ->whereIn('e177_codigo', [1])
            ->whereIn('e60_instit', $this->getListaInstituicoes())
            ->whereBetween('c70_data', [$this->dtDataInicial, $this->dtDataFinal])
            ->orderBy('e60_numemp')
            ->get();

        if ($empenhos->isEmpty()) {
            throw new \Exception('Não foi encontrado nenhum empenho para o arquivo da Remessa.');
        }

        $obj = new \stdClass();
        $obj->LiquidacoesRestosPagarNotasFiscais = [];
        foreach ($empenhos as $empenho) {
            $atestadores = DB::table('empnotaatestador')
                ->select(['z01_numcgm', 'z01_nome', 'z01_cgccpf'])
                ->join('cgm', 'z01_numcgm', 'e169_numcgm')
                ->where('e169_empnota', $empenho->e69_codnota)
                ->get()
                ->map(function ($atestador) {
                    return (object)[
                        'Identificador' => $atestador->z01_numcgm,
                        'NomeAtestador' => $atestador->z01_nome,
                        'CPF' => $atestador->z01_cgccpf,
                    ];
                })->toArray();

            $dadosNota = \JSON::create()->parse($empenho->e69_outrosdados);

            $danfe = 'false';
            $chaveDanfe = null;
            if (!empty($dadosNota->danfe->chave)) {
                $danfe = 'true';
                $chaveDanfe = str_pad(
                    substr(preg_replace('/\s+/', '', ltrim($dadosNota->danfe->chave, '0')), 0, 44),
                    44,
                    "0"
                );
            }

            $data = (object)[
                'Identificador' => $empenho->c70_codlan,
                'CodigoUnidadeGestora' => $this->sCodigoTribunal,
                'NumeroEmpenho' => $empenho->e60_codemp,
                'AnoEmpenho' => $empenho->e60_anousu,
                'Competencia' => $this->competencia,
                'NumeroLiquidacaoRestosPagar' => $empenho->e50_codord,
                'DANFE' => $danfe,
                'ChaveDANFE' => $chaveDanfe,
                'NumeroNF' => $empenho->e69_numero,
                'Serie' => substr($empenho->e69_serienota, 0, 6),
                'Data' => $empenho->e69_dtnota,
                'Valor' => $empenho->e70_valor,
                //retirada a informação dos atestadores ((object)['Atestador' => $atestadores],)
                'Atestadores' => null,
                'DataAtestacao' => $empenho->e69_dtrecebe,
                'AnoLiquidacaoRestosPagar' => (new Carbon($empenho->c70_data))->format('Y'),
                'CodigoOrgao' => $empenho->o58_orgao,
                'CodigoUnidadeOrcamentaria' => $empenho->o58_unidade,
            ];

            $obj->LiquidacoesRestosPagarNotasFiscais[] = (object)['LiquidacaoRestosPagarNotaFiscal' => $data];
        }

        $this->aDados = $obj;
    }
}
