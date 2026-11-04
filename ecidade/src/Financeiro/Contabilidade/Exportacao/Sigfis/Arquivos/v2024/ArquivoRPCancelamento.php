<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use Carbon\Carbon;
use ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Common\Helper;
use Illuminate\Database\Capsule\Manager as DB;
use stdClass;

class ArquivoRPCancelamento extends ArquivoBase
{
    protected $sNomeArquivo = 'RestosPagarCancelamento';

    public function gerarDados()
    {
        $campos = [
            'e60_numemp',
            'e60_anousu',
            'e91_anousu',
            'e60_codemp',
            'o58_orgao',
            'o58_unidade',
            'e91_vlremp', 'e91_vlranu', 'e91_vlrliq',
            'e91_vlrliq', 'e91_vlrpag',
            'e94_codanu',
            'e94_data',
            'e94_valor',
            'e94_motivo',
            'e94_empanuladotipo',
            'z01_cgccpf',
        ];

        $empenhos = DB::table('empresto')
            ->select($campos)
            ->join('empenho.empempenho', 'e60_numemp', 'e91_numemp')
            ->join('empenho.empanulado', 'e94_numemp', 'e60_numemp')
            ->join('configuracoes.db_usuacgm', 'id_usuario', 'e94_usuario')
            ->join('protocolo.cgm', 'z01_numcgm', 'cgmlogin')
            ->join('orcamento.orcdotacao', function ($join) {
                $join->on('e60_anousu', 'o58_anousu')
                    ->on('e60_coddot', 'o58_coddot');
            })
            ->where('e91_anousu', $this->iAnoUsu)
            ->whereIn('e60_instit', $this->getListaInstituicoes())
            ->whereBetween('e94_data', [$this->dtDataInicial, $this->dtDataFinal])
            ->get();

        if ($empenhos->isEmpty()) {
            throw new \Exception(sprintf(
                'Não foi encontrado nenhum registro para o arquivo da Remessa de %s',
                'Restos a Pagar - Inscrição'
            ));
        }

        $obj = new stdClass();
        $obj->RestosPagarCancelamentos = [];

        foreach ($empenhos as $empenho) {
            $vaorRPP = $empenho->e91_vlrliq - $empenho->e91_vlrpag;

            $tipo = $vaorRPP > 0 ? 2 : 1;

            $data = (object)[
                'Identificador' => $empenho->e94_codanu,
                'CodigoUnidadeGestora' => $this->sCodigoTribunal,
                'Competencia' => $this->competencia,
                'NumeroEmpenho' => $empenho->e60_codemp,
                'AnoEmpenho' => $empenho->e60_anousu,
                'Tipo' => $tipo,
                'NumeroCancelamento' => $empenho->e94_codanu,
                'DataCancelamento' => $empenho->e94_data,
                'AnoCancelamento' => (new Carbon($empenho->e94_data))->format('Y'),
                'Cpf' => $empenho->z01_cgccpf,
                'Justificativa' => Helper::convertAndLimit("<![CDATA[".$empenho->e94_motivo, 264),
                'Valor' => $empenho->e94_valor,
                'CodigoOrgao' => $empenho->o58_orgao,
                'CodigoUnidadeOrcamentaria' => $empenho->o58_unidade,
                'Liquidacoes' => null
            ];


            $obj->RestosPagarCancelamentos[] = (object)[
                'RestosPagarCancelamento' => $data
            ];
        }

        $this->aDados = $obj;
    }
}
