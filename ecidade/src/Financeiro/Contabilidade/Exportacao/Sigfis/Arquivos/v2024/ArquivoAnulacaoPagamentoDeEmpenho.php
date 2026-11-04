<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Common\Helper;

class ArquivoAnulacaoPagamentoDeEmpenho extends ArquivoBase
{
    protected $sNomeArquivo  = 'AnulacaoPagamentoDeEmpenho';
    /**
    * Busca os dados para gerar o Arquivo de Unidade Orçamentária
    */
    public function gerarDados()
    {
        $campos = [
            'o58_orgao',
            'o58_unidade',
            'e60_numemp',
            'e60_codemp',
            'e60_anousu',
            'pagordem.e50_codord',
            'e69_codnota',
            'e69_anousu',
        ];
        $empenhos = [];

        $empenhos = DB::table('empempenho')
            ->select($campos)
            ->distinct()
            ->selectRaw("sum(c70_valor) as valor_anulado")
            ->selectRaw("min(c70_data) as data_anulacao")
            ->selectRaw("string_agg(c72_complem, ' ') as c72_complem")
            ->selectRaw("min(z01_cgccpf) as cpf")
            ->join('empnota', 'e69_numemp', 'e60_numemp')
            ->join('orcdotacao', function (JoinClause $join) {
                $join->on('o58_anousu', 'e60_anousu')
                    ->on('o58_coddot', 'e60_coddot');
            })
            ->join('pagordemnota', 'e71_codnota', 'e69_codnota')
            ->join('pagordem', 'e50_codord', 'e71_codord')
            ->join('conlancamord', 'c80_codord', 'e71_codord')
            ->join('conlancam', 'c70_codlan', 'c80_codlan')
            ->join('conlancamdoc', 'c71_codlan', 'c70_codlan')
            ->join('conlancamcompl', 'c72_codlan', 'c70_codlan')
            ->join('lancamentoscontabeislog AS log', function (JoinClause $join) {
                $join->on('codlan', 'c70_codlan')
                    ->where('tipo_movimento', 1);
            })
            ->join('db_usuacgm', 'db_usuacgm.id_usuario', 'log.id_usuario')
            ->join('cgm AS cgmusu', 'cgmusu.z01_numcgm', 'cgmlogin')
            ->whereIn('c71_coddoc', [6])
            ->whereIn('e60_instit', $this->getListaInstituicoes())
            ->whereBetween('c70_data', [$this->dtDataInicial, $this->dtDataFinal])
            ->orderBy('e60_numemp')
            ->groupBy($campos)
            ->get();

        if ($empenhos->isEmpty()) {
            throw new \Exception('Não foi encontrado nenhum registro para o período');
        }

        $obj = new \stdClass();
        $obj->AnulacoesDePagamentosDeEmpenhos = [];
        
        foreach ($empenhos as $empenho) {
            $dadosAnulacaoPagamentoDeEmpenho = (object)[
                'Identificador' => $empenho->e50_codord,
                'CodigoUnidadeGestora' => $this->sCodigoTribunal,
                'NumeroEmpenho' => $empenho->e60_codemp,
                'AnoEmpenho' =>  $empenho->e60_anousu,
                'Competencia' => $this->competencia,
                'NumeroNotaPagamento' => $empenho->e69_codnota,
                'AnoNotaPagamento' => $empenho->e69_anousu,
                'NumeroAnulacaoPagamento' => $empenho->e50_codord,
                'Tipo' => 2,
                'DataAnulacao' => $empenho->data_anulacao,
                'CpfResponsavel' => $empenho->cpf,
                'Justificativa' => Helper::convertAndLimit("<![CDATA[".$empenho->c72_complem, 4009),
                'Valor' =>  $empenho->valor_anulado,
                'CodigoOrgao' =>  $empenho->o58_orgao,
                'CodigoUnidadeOrcamentaria' =>  $empenho->o58_unidade
            ];

            $obj->AnulacoesDePagamentosDeEmpenhos[] = (object)[
                'AnulacaoPagamentoDeEmpenho' => $dadosAnulacaoPagamentoDeEmpenho
            ];
        }

        $this->aDados =  $obj;
    }
}
