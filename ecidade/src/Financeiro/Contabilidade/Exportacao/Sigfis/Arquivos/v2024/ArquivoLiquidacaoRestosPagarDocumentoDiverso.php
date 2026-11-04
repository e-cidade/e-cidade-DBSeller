<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use Carbon\Carbon;
use ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Common\Helper;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use stdClass;

class ArquivoLiquidacaoRestosPagarDocumentoDiverso extends ArquivoBase
{
    protected $sNomeArquivo = 'LiquidacaoRestosPagarDocumentoDiverso';

    public function gerarDados()
    {
        $campos = [
            'e60_numemp', 'e60_codemp', 'e60_anousu',
            'e50_codord', 'e50_obs',
            'o58_orgao', 'o58_unidade',
            'e69_codnota', 'e69_anousu', 'e69_numero', 'e69_serienota', 'e69_dtinclusao', 'e69_dtrecebe',
            'c70_codlan', 'c70_valor', 'c70_data',
            'e177_tipodiverso',
            'z01_nome', 'z01_cgccpf',
        ];
        $empenhos = DB::table('empresto')->distinct()
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
            ->join('cgm', 'z01_numcgm', 'e60_numcgm')
            ->whereIn('e60_instit', $this->getListaInstituicoes())
            ->whereIn('e177_codigo', [3])
            ->whereBetween('c70_data', [$this->dtDataInicial, $this->dtDataFinal])
            ->orderBy('e60_numemp')
            ->get();

        if ($empenhos->isEmpty()) {
            throw new \Exception('Não foi encontrado nenhum empenho de Liquidação de Empenho - Documento Diverso.');
        }

        $obj = new stdClass();
        $obj->LiquidacoesRestosPagarDocumentosDiversos = [];
        foreach ($empenhos as $empenho) {
            $atestadores = DB::table('empnotaatestador')
                ->select(['z01_numcgm', 'z01_nome', 'z01_cgccpf'])
                ->join('cgm', 'z01_numcgm', 'e169_numcgm')
                ->where('e169_empnota', $empenho->e69_codnota)
                ->get()
                ->map(function ($atestador) {
                    return (object)[
                        'Identificador' => $atestador->z01_numcgm,
                        'Nome' => Helper::convertAndLimit("<![CDATA[".$atestador->z01_nome, 264),
                        'CPF' => $atestador->z01_cgccpf,
                    ];
                })->toArray();

            $natureza = strlen($empenho->z01_cgccpf) == 11 ? 2 : 1;
            
            $dataLancamento = new Carbon($empenho->c70_data);

            $data = (object)[
                'Identificador' => $empenho->c70_codlan,
                'CodigoUnidadeGestora' => $this->sCodigoTribunal,
                'NumeroEmpenho' => $empenho->e60_codemp,
                'AnoEmpenho' => $empenho->e60_anousu,
                'Competencia' => $this->competencia,
                'NumeroLiquidacaoRestosPagar' => $empenho->e50_codord,
                'NumeroDocumento' => $empenho->c70_codlan,
                'Tipo' => $empenho->e177_tipodiverso,
                'NaturezaEmitente' => $natureza,
                'DataEmissao' => $empenho->c70_data,
                'Valor' => $empenho->c70_valor,
                'CpfCnpjNifEmitente' => $empenho->z01_cgccpf,
                'Nome' => Helper::convertAndLimit("<![CDATA[".$empenho->z01_nome, 264),
                //retirada a informação dos atestadores ((object)['Atestador' => $atestadores],)
                'Atestadores' => null,
                'DataAtestacao' => $empenho->e69_dtrecebe,
                'AnoLiquidacaoRestosPagar' => $dataLancamento->format('Y'),
                'CodigoOrgao' => $empenho->o58_orgao,
                'CodigoUnidadeOrcamentaria' => $empenho->o58_unidade,
            ];

            $obj->LiquidacoesRestosPagarDocumentosDiversos[] = (object) [
                'LiquidacaoRestosPagarDocumentoDiverso' => $data
            ];
        }

        $this->aDados = $obj;
    }
}
