<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Common\Helper;
use ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Xsd\XSDFactory;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use stdClass;

class ArquivoAnulacaoEmpenho extends ArquivoBase
{
    protected $sNomeArquivo = 'AnulacaoDeEmpenho';

    public function gerarDados()
    {
        $campos = [
            'e60_numemp',
            'e60_codemp',
            'e60_anousu',
            'o58_orgao',
            'o58_unidade',
            'e60_vlranu',
            'e94_codanu',
            'e94_data',
            'e94_valor',
            'z01_cgccpf',
            'e94_codanu',
            'e94_motivo',
        ];

        $empenhos = DB::table('empempenho')
            ->select($campos)
            ->join('empanulado', 'e94_numemp', '=', 'e60_numemp')
            ->join('db_usuarios', 'id_usuario', 'e94_usuario')
            ->join('db_usuacgm', 'db_usuacgm.id_usuario', 'db_usuarios.id_usuario')
            ->join('cgm', 'z01_numcgm', '=', 'cgmlogin')
            ->join('orcdotacao', function (JoinClause $join) {
                $join->on('e60_anousu', 'o58_anousu')
                    ->on('e60_coddot', 'o58_coddot');
            })
            ->where('e60_anousu', $this->iAnoUsu)
            ->whereIn('e60_instit', $this->getListaInstituicoes())
            ->whereBetween('e94_data', [$this->dtDataInicial, $this->dtDataFinal])
            ->get();

        if ($empenhos->isEmpty()) {
            throw new \Exception('Não foi encontrardo nenhum empenho para o arquivo de Remessa ');
        }

        $obj = new stdClass();
        $obj->AnulacoesDeEmpenho = [];

        foreach ($empenhos as $empenho) {
            $elementos = DB::table('empanuladoele')
                ->select(['e95_codele', 'e95_valor', 'conta'])
                ->join('conplanoorcamento', 'c60_codcon', 'e95_codele')
                ->join('planodespesaconplanoorcamento', 'conplanoorcamento_codigo', 'c60_codigo')
                ->join('planodespesa', function (JoinClause $join) {
                    $join->on('planodespesa.id', 'planodespesa_id')
                        ->where('uniao', '=', $this->getUtilizaPlanoUniao());
                })
                ->where('e95_codanu', $empenho->e94_codanu)
                ->where('c60_anousu', $empenho->e60_anousu)
                ->where('exercicio', $empenho->e60_anousu)
                ->where('uniao', '=', $this->getUtilizaPlanoUniao())
                ->get()
                ->map(function ($elemento) {
                    return (object)[
                        'Identificador' => $elemento->e95_codele,
                        'Valor' => $elemento->e95_valor,
                        'NaturezaDespesa' => str_pad($elemento->conta, 8, '0', STR_PAD_RIGHT),
                    ];
                })
                ->toArray();

            $data = (object) [
                'Identificador' => $empenho->e94_codanu,
                'CodigoUnidadeGestora' => $this->sCodigoTribunal,
                'NumeroEmpenho' => $empenho->e60_codemp,
                'AnoEmpenho' => $empenho->e60_anousu,
                'Competencia' => $this->competencia,
                'CodigoOrgao' => $empenho->o58_orgao,
                'CodigoUnidadeOrcamentaria' => $empenho->o58_unidade,
                'NumeroAnulacao' => $empenho->e94_codanu,
                'DataAnulacao' => $empenho->e94_data,
                'ValorAnulacao' => $empenho->e94_valor,
                'JustificativaAnulacao' => Helper::convertAndLimit("<![CDATA[".$empenho->e94_motivo, 264),
                'CpfResponsavel' => $empenho->z01_cgccpf,
                'SubElementos' => (object)['AnulacaoEmpenhoSubElemento'=>$elementos],
            ];

            $obj->AnulacoesDeEmpenho[] = (object)['AnulacaoDeEmpenho' => $data];
        }

        $this->aDados = $obj;
    }
}
