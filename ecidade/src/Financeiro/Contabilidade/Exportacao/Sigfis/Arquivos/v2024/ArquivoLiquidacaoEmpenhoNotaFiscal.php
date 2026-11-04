<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;

class ArquivoLiquidacaoEmpenhoNotaFiscal extends ArquivoBase
{
    protected $sNomeArquivo = 'LiquidacaoEmpenhoNotaFiscal';

    public function gerarDados()
    {
        $campos = [
            'e60_numemp', 'e60_codemp', 'e60_anousu',
            'o58_orgao', 'o58_unidade',
            'e69_codnota', 'e69_anousu',
            'e70_valor',
            'e50_obs',
            'e69_anousu', 'e69_numero', 'e69_serienota', 'e69_dtnota', 'e69_dtrecebe', 'e69_outrosdados',
            'e71_codord'
        ];
        $empenhos = DB::table('conlancam')
            ->select($campos)
            ->join('conlancamemp', 'c75_codlan', 'c70_codlan')
            ->join('conlancamdoc', 'c71_codlan', 'c70_codlan')
            ->join('conhistdoc', function (JoinClause $join) {
                $join->on('c53_coddoc', 'c71_coddoc')
                ->whereIn('c53_coddoc', [3, 23, 84, 202, 204, 206, 306, 310, 412, 502, 506]);
            })
            ->join('conlancamnota', 'c66_codlan', 'c70_codlan')
            ->join('conlancamord', 'c80_codlan', 'c70_codlan')
            ->join('empempenho', 'e60_numemp', 'c75_numemp')
            ->join('empnota', function (JoinClause $join) {
                $join->on('empnota.e69_numemp', 'empempenho.e60_numemp')
                        ->on('e69_codnota', 'c66_codnota');
            })
            ->join('empnotaele', 'e70_codnota', 'e69_codnota')
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
            ->where('e60_anousu', $this->iAnoUsu)
            ->where('e177_codigo', 1)
            ->whereIn('e60_instit', $this->getListaInstituicoes())
            ->whereBetween('c70_data', [$this->dtDataInicial, $this->dtDataFinal])
            ->orderBy('e60_numemp')
            ->orderBy('e71_codord')
            ->get();

        if ($empenhos->isEmpty()) {
            throw new \Exception('Não foi encontrado nenhum empenho para o arquivo da Remessa.');
        }

        $obj = new \stdClass();
        $obj->LiquidacoesEmpenhoNotasFiscais = [];
        foreach ($empenhos as $empenho) {
            $atestadores = DB::table('empnotaatestador')
            ->select(['z01_numcgm', 'z01_nome', 'z01_cgccpf'])
            ->join('cgm', 'z01_numcgm', 'e169_numcgm')
            ->where('e169_empnota', $empenho->e69_codnota)
            ->get();

            $xmlAtestadores = [];
            foreach ($atestadores as $atestador) {
                $xmlAtestadores[] = (object)[
                    'Atestador' => (object)[
                        'Identificador' => $atestador->z01_numcgm,
                        'NomeAtestador' => $atestador->z01_nome,
                        'CPF' => $atestador->z01_cgccpf
                    ]
                ];
            }

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
                'Identificador' => $empenho->e71_codord,
                'CodigoUnidadeGestora' => $this->sCodigoTribunal,
                'NumeroEmpenho' => $empenho->e60_codemp,
                'AnoEmpenho' => $empenho->e60_anousu,
                'Competencia' => $this->competencia,
                'NumeroLiquidacaoEmpenho' => $empenho->e71_codord,
                'DANFE' => $danfe,
                'ChaveDANFE' => $chaveDanfe,
                'NumeroNF' => $empenho->e69_numero,
                'Serie' => substr($empenho->e69_serienota, 0, 6),
                'Data' => $empenho->e69_dtnota,
                'Valor' => $empenho->e70_valor,
                //retirada a informação dos atestadores (count($xmlAtestadores)  == 0 ? null : $xmlAtestadores)
                'Atestadores' => null,
                'DataAtestacao' => $empenho->e69_dtrecebe,
                'AnoLiquidacaoEmpenho' => $empenho->e69_anousu,
                'CodigoOrgao' => $empenho->o58_orgao,
                'CodigoUnidadeOrcamentaria' => $empenho->o58_unidade,
            ];

            $obj->LiquidacoesEmpenhoNotasFiscais[] = (object)['LiquidacaoEmpenhoNotaFiscal' => $data];
        }

        $this->aDados = $obj;
    }
}
