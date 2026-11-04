<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use stdClass;

class ArquivoPagamentoRestosPagar extends ArquivoBase
{
    protected $sNomeArquivo = 'PagamentoRestosPagar';

    public function gerarDados()
    {
        $campos = [
            'e60_numemp',
            'e60_codemp',
            'e60_anousu',
            'e71_codord',
            'e69_codnota',
            'e69_numero',
            'c70_anousu',
            'e69_anousu',
            'c70_valor',
            'c70_data',
            'c70_codlan'
        ];

        /*
         * Para geracao dessa informacao agrupamos as informacoes do pagamento e liquidacao da OP, sendo:
         * Data do pagamento: menor data paga para o op no periodo
         * Valor do pagamento: somatorio do vlr pago no periodo
         * Ja as contas utilizadas sao desmembradas na liquidacao
         */

        $empenhos = DB::table('empresto')
            ->select($campos)
            ->distinct()
            ->join('empempenho', 'e60_numemp', 'e91_numemp')
            ->join('empnota', 'e69_numemp', 'e60_numemp')
            ->join('pagordemnota', 'e71_codnota', 'e69_codnota')
            ->join('conlancamemp', 'c75_numemp', 'e60_numemp')
            ->join('conlancam', 'c75_codlan', 'c70_codlan')
            ->join('conlancamdoc', 'c71_codlan', 'c70_codlan')
            ->join('conlancamord', function (JoinClause $join) {
                $join->on('c80_codlan', 'c70_codlan')
                    ->on('c80_codord', 'e71_codord');
            })
            ->whereIn('c71_coddoc', [35,37])
            ->whereIn('e60_instit', $this->getListaInstituicoes())
            ->where('e91_anousu', $this->iAnoUsu)
            ->whereBetween('c70_data', [$this->dtDataInicial, $this->dtDataFinal])
            ->orderBy('e60_numemp')
            ->orderBy('e71_codord')
            ->orderBy('e69_codnota')
            ->get();

        if ($empenhos->isEmpty()) {
            throw new \Exception(sprintf(
                'Nao foi encontrado nenhum registro para o arquivo da Remessa de %s',
                'Pagamento de Empenho'
            ));
        }

        $obj = new stdClass();
        $obj->PagamentosRestosPagar = [];
        foreach ($empenhos as $empenho) {
            $data = (object)[
                "Identificador" => $empenho->c70_codlan,
                "CodigoUnidadeGestora" => $this->sCodigoTribunal,
                "Competencia" => $this->competencia,
                "AnoPagamento" => $empenho->c70_anousu,
                "NumeroNotaPagamento" => $empenho->c70_codlan,
                "DataPagamento" => $empenho->c70_data,
                "ValorPagamento" => $empenho->c70_valor,
                "CPFResponsavel" => $this->buscaResponsavel($empenho->e71_codord),
                "LiquidacoesPagamentos" => $this->carregaLiquidacoes($empenho)
            ];

            $obj->PagamentosRestosPagar[] = (object) ['PagamentoRestosPagar' => $data];
        }

        $this->aDados = $obj;
    }

    private function buscaResponsavel($codord)
    {
        return DB::table("lancamentoscontabeislog")
        ->select("z01_cgccpf")
        ->join('db_usuacgm', 'db_usuacgm.id_usuario', 'lancamentoscontabeislog.id_usuario')
        ->join('cgm AS cgmusu', 'cgmusu.z01_numcgm', 'cgmlogin')
        ->join('conlancamord', 'c80_codlan', 'codlan')
        ->join('conlancamdoc', 'c71_codlan', 'codlan')
        ->join('conlancam', 'c70_codlan', 'codlan')
        ->where('tipo_movimento', 1)
        ->whereIn('c71_coddoc', [35, 37])
        ->where('c80_codord', $codord)
        ->whereBetween('c70_data', [$this->dtDataInicial, $this->dtDataFinal])
        ->orderby('c70_data')
        ->first()->z01_cgccpf;
    }

    private function carregaLiquidacoes($dadosPagamento)
    {
        $campos = [
            'o58_orgao',
            'o58_unidade',
            'e69_codnota',
            'e50_codord',
            'e69_anousu',
            'e60_codemp',
            'e60_anousu',
            'c70_codlan',
            'c70_data',
            'c70_anousu',
        ];

        $liquidacoes = DB::table('empempenho')
            ->select($campos)
            ->join('empnota', 'e69_numemp', 'e60_numemp')
            ->join('pagordem', 'e50_numemp', 'e69_numemp')
            ->join('orcdotacao', function (JoinClause $join) {
                $join->on('o58_anousu', 'e60_anousu')
                    ->on('o58_coddot', 'e60_coddot');
            })
            ->join('conlancamemp', 'c75_numemp', 'e60_numemp')
            ->join('conlancamord', function (JoinClause $join) {
                $join->on('c80_codlan', 'c75_codlan')
                    ->on('c80_codord', 'e50_codord');
            })
            ->join('conlancam', 'c75_codlan', 'c70_codlan')
            ->join('conlancamdoc', 'c71_codlan', 'c70_codlan')
            ->join('conhistdoc', 'c53_coddoc', 'c71_coddoc')
            ->join('conlancamnota', function (JoinClause $join) {
                $join->on('c66_codlan', 'c70_codlan')
                    ->on('c66_codnota', 'e69_codnota');
            })
            ->where('c53_tipo', 20)
            ->where('e50_codord', $dadosPagamento->e71_codord)
            ->orderBy('e60_numemp')
            ->get();

        $retornoLiquidacoes = [];
        foreach ($liquidacoes as $dados) {
            $liquidacao = (object)[
                "Identificador" => $dados->e50_codord,
                "AnoEmpenho" => $dados->e60_anousu,
                "NumeroEmpenho" => $dados->e60_codemp,
                'NumeroLiquidacaoRestosPagar' => $dados->e50_codord,
                'AnoLiquidacaoRestosPagar' => $dados->c70_anousu,
                'CodigoOrgao' => $dados->o58_orgao,
                'CodigoUnidadeOrcamentaria' => $dados->o58_unidade,
                'ValorPagamentoLiquidacao' => $dadosPagamento->c70_valor,
                'ContasPagadoras' => []
            ];
            foreach ($this->carregaContasPagadoras($dadosPagamento) as $conta) {
                $liquidacao->ContasPagadoras[] = ['ContaPagadora' => $conta];
            }

            $retornoLiquidacoes[] = (object) ["LiquidacaoPagamento" => $liquidacao];
        }
        return $retornoLiquidacoes;
    }

    private function carregaContasPagadoras($dadosPagamento)
    {
        $campos = [
        'db90_codban',
        'db89_codagencia',
        'db89_digito',
        'db83_sequencial',
        'db83_dvconta',
        'c70_valor',
        ];

        // conta do pagamento
        return DB::table('conlancam')
        ->select($campos)
        ->join('conlancamord', 'c80_codlan', 'c70_codlan')
        ->join('conlancampag', 'c82_codlan', 'c70_codlan')
        ->join('conlancamdoc', 'c71_codlan', 'c70_codlan')
        ->join('contabilidade.conplanoreduz', function (JoinClause $join) {
            $join->on('c61_reduz', 'c82_reduz')
                ->on('c61_anousu', 'c82_anousu');
        })
        ->join('contabilidade.conplanocontabancaria', function (JoinClause $join) {
            $join->on('c56_reduz', 'c61_reduz')
                ->on('c56_anousu', 'c61_anousu');
        })
        ->join('configuracoes.contabancaria', 'c56_contabancaria', 'db83_sequencial')
        ->join('configuracoes.bancoagencia', 'db89_sequencial', 'db83_bancoagencia')
        ->join('configuracoes.db_bancos', 'db90_codban', 'db89_db_bancos')
        ->where('c80_codord', $dadosPagamento->e71_codord)
        ->where('c82_codlan', $dadosPagamento->c70_codlan)
        ->whereIn('c71_coddoc', [35, 37])
        ->whereBetween('c70_data', [$this->dtDataInicial, $this->dtDataFinal])
        ->groupBy(['db90_codban', 'db89_codagencia', 'db89_digito', 'db83_sequencial', 'db83_dvconta', 'c70_valor'])
        ->orderBy('db83_conta')
        ->get()
        ->map(function ($conta) {
            return (object)[
                'Identificador' => $conta->db83_sequencial,
                'ValorContaPagadora' => $conta->c70_valor,
                'Banco' => $conta->db90_codban,
                'Agencia' => $conta->db89_codagencia,
                'ContaBancaria' => $conta->db83_sequencial
            ];
        })->toArray();
    }
}
