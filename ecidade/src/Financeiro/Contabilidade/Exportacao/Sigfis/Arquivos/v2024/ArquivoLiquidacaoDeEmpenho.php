<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use stdClass;

class ArquivoLiquidacaoDeEmpenho extends ArquivoBase
{
    protected $sNomeArquivo  = 'LiquidacaoDeEmpenho';
    /**
    * Busca os dados para gerar o Arquivo de Unidade Orçamentária
    */
    public function gerarDados()
    {
        $campos = [
        'e60_numemp', 'e60_codemp', 'e60_anousu',
        'e69_codnota', 'e71_codord', 'c70_codlan', 'c70_data',
        'e70_valor','e177_codigo','o58_orgao','o58_unidade',
        'planodespesa.conta as natureza_despesa','z01_cgccpf as cpf'
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
        ->join('db_usuacgm', 'id_usuario', 'e50_id_usuario')
        ->join('cgm', 'z01_numcgm', 'cgmlogin')
        ->join(
            'empnotasigfistipodocliquidacao',
            'empnotasigfistipodocliquidacao.e178_empnota',
            'empnota.e69_codnota'
        )
        ->join(
            'sigfistipodocliquidacao',
            'sigfistipodocliquidacao.e177_sequencial',
            'empnotasigfistipodocliquidacao.e178_sigfistipodocliquidacao'
        )
        ->join('orcdotacao', function ($join) {
            $join->on('e60_anousu', 'o58_anousu')
                ->on('e60_coddot', 'o58_coddot');
        })
        ->join('empelemento', 'empelemento.e64_numemp', 'empempenho.e60_numemp')
        ->join('conplanoorcamento', function ($join) {
            $join->on('conplanoorcamento.c60_codcon', 'empelemento.e64_codele');
            $join->on('conplanoorcamento.c60_anousu', 'empempenho.e60_anousu');
        })
        ->join(
            'planodespesaconplanoorcamento',
            'planodespesaconplanoorcamento.conplanoorcamento_codigo',
            'conplanoorcamento.c60_codigo'
        )
        ->join('planodespesa', function (JoinClause $join) {
            $join->on('planodespesa.id', 'planodespesa_id')
                ->on('exercicio', 'e60_anousu')
                ->where('uniao', '=', $this->getUtilizaPlanoUniao());
        })
        ->whereIn('e60_instit', $this->getListaInstituicoes())
        ->where('e60_anousu', $this->iAnoUsu)
        ->whereBetween('c70_data', [$this->dtDataInicial, $this->dtDataFinal])
        ->get();

        if ($empenhos->isEmpty()) {
            throw new \Exception('Não foi encontrardo nenhum empenho para o arquivo de Remessa ');
        }

        $obj = new \stdClass();
        $obj->LiquidacoesDeEmpenho = [];

        foreach ($empenhos as $empenho) {
            $consignacaoRetencoes = new stdClass();
            $consignacaoRetencoes->ConsignacaoRetencao  = (object) [
            'Identificador' => '0',
            'Valor' => '0'
            ];

            $responsavelLiquidacao = new stdClass();
            $responsavelLiquidacao->Responsavel = [];
            $responsavelLiquidacao->Responsavel[] = (object) [
            'Identificador' => $empenho->c70_codlan,
            'CPF' => $empenho->cpf
            ];

            $subElemento = new stdClass();
            $subElemento->LiquidacaoDeEmpenhoSubElemento = [];
            $subElemento->LiquidacaoDeEmpenhoSubElemento[] = (object) [
            'Identificador' => $empenho->c70_codlan,
            'Valor' => $empenho->e70_valor,
            'NaturezaDespesa' => $empenho->natureza_despesa
            ];

            $dadosLiquidacaoEmpenho = (object)[
            'Identificador' => $empenho->c70_codlan,
            'CodigoUnidadeGestora' => $this->sCodigoTribunal,
            'Ano' => $this->iAnoUsu,
            'Competencia' => $this->competencia,
            'NumeroLiquidacaoEmpenho' =>  $empenho->e71_codord,
            'DataLiquidacao' => $empenho->c70_data,
            'ValorBrutoLiquidacao' => $empenho->e70_valor,
            'NumeroEmpenho' => $empenho->e60_codemp,
            'AnoEmpenho' => $empenho->e60_anousu,
            //@todo - remover a condicional, apenas para testes
            'TipoDocumento' => (!empty($empenho->e177_codigo)) ? $empenho->e177_codigo : 1,
            'CodigoOrgao' => $empenho->o58_orgao,
            'CodigoUnidadeOrcamentaria' => $empenho->o58_unidade,
            'ResponsaveisLiquidacao' => $responsavelLiquidacao,
            'SubElemento' => $subElemento,
            //retirada a informação ref. as consignações
            'ConsignacoesRetencoes' => null,
            ];

            $obj->LiquidacoesDeEmpenho[] = (object)['LiquidacaoDeEmpenho' => $dadosLiquidacaoEmpenho];
        }

            $this->aDados =  $obj;
    }
}
