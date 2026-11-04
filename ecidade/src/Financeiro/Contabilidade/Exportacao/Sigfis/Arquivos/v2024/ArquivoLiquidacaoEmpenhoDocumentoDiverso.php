<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Common\Helper;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use stdClass;

class ArquivoLiquidacaoEmpenhoDocumentoDiverso extends ArquivoBase
{
    protected $sNomeArquivo = 'LiquidacaoEmpenhoDocumentoDiverso';

    public function gerarDados()
    {
        $campos = [
            'e60_numemp', 'e60_codemp', 'e60_anousu',
            'e70_valor',
            'o58_orgao', 'o58_unidade',
            'e69_codnota', 'e69_anousu', 'e69_numero', 'e69_serienota', 'e69_dtnota', 'e69_dtrecebe',
            'e50_obs',
            'e71_codord',
            'e177_tipodiverso',
            'z01_nome', 'z01_cgccpf',
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
            ->join('cgm', 'z01_numcgm', 'e60_numcgm')
            ->where('e60_anousu', $this->iAnoUsu)
            ->whereIn('e177_codigo', [3])
            ->whereIn('e60_instit', $this->getListaInstituicoes())
            ->whereBetween('c70_data', [$this->dtDataInicial, $this->dtDataFinal])
            ->orderBy('e60_numemp')
            ->get();

        if ($empenhos->isEmpty()) {
            throw new \Exception('Não foi encontrado nenhum empenho de Liquidação de Empenho - Documento Diverso.');
        }

        $obj = new stdClass();
        $obj->LiquidacoesEmpenhoDocumentosDiversos = [];
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
                        'Nome' => $atestador->z01_nome,
                        'CPF' => $atestador->z01_cgccpf
                    ]
                ];
            }

            $natureza = strlen($empenho->z01_cgccpf) == 11 ? 2 : 1;

            $data = (object)[
                'Identificador' => $empenho->e71_codord,
                'CodigoUnidadeGestora' => $this->sCodigoTribunal,
                'NumeroEmpenho' => $empenho->e60_codemp,
                'AnoEmpenho' => $empenho->e60_anousu,
                'Competencia' => $this->competencia,
                'NumeroLiquidacaoEmpenho' => $empenho->e71_codord,
                'NumeroDocumento' => $empenho->e69_numero,
                'Tipo' => $empenho->e177_tipodiverso,
                'DataEmissao' => $empenho->e69_dtnota,
                'Valor' => $empenho->e70_valor,
                'CpfCnpjNifEmitente' => $empenho->z01_cgccpf,
                'NaturezaEmitente' => $natureza,
                'Nome' => Helper::convertAndLimit("<![CDATA[".$empenho->z01_nome),
                //retirada a informação dos atestadores (count($xmlAtestadores)  == 0 ? null : $xmlAtestadores)
                'Atestadores' => null,
                'DataAtestacao' => $empenho->e69_dtrecebe,
                'AnoLiquidacaoDeEmpenho' => $empenho->e69_anousu,
                'CodigoOrgao' => $empenho->o58_orgao,
                'CodigoUnidadeOrcamentaria' => $empenho->o58_unidade,
            ];

            $obj->LiquidacoesEmpenhoDocumentosDiversos[] = (object)['LiquidacaoEmpenhoDocumentoDiverso' => $data];
        }

        $this->aDados = $obj;
    }
}
