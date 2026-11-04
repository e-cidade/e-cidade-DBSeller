<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Arquivos\v2024;

use ECidade\Financeiro\Contabilidade\Exportacao\Sigfis\Common\Helper;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\JoinClause;
use stdClass;

class ArquivoLiquidacaoEmpenhoDiaria extends ArquivoBase
{
    protected $sNomeArquivo = 'LiquidacaoEmpenhoDiaria';

    public function gerarDados()
    {
        $campos = [
            'e60_numemp', 'e60_codemp', 'e60_anousu',
            'o58_orgao', 'o58_unidade',
            'e69_codnota', 'e69_anousu',
            'e71_codord',
            'e70_valor',
            'e50_obs',
            'conta',
            'cgmusu.z01_cgccpf',
            'e46_nome',
            'e446_quantidade', 'e446_datainicio', 'e446_datafim', 'e446_tipodiaria', 'e446_estadodestino',
            'e446_destino', 'e446_paisdestino', 'e446_motivo', 'e446_sequencial',
            'e82_codmov',
        ];

        $empenhos = DB::table('empempenho')
            ->select($campos)
            ->join('empnota', 'e69_numemp', 'e60_numemp')
            ->join('empnotaele', 'e70_codnota', 'e69_codnota')
            ->join('pagordem', 'e50_numemp', 'e60_numemp')
            ->join('pagordemnota', function (JoinClause $join) {
                $join->on('e71_codnota', 'e69_codnota')
                    ->on('e71_codord', 'e50_codord');
            })
            ->join('empenho.empord', 'e82_codord', 'e50_codord')
            ->join('emppresta', 'e45_codmov', 'e82_codmov')
            ->join('empprestaitem', function (JoinClause $join) {
                $join->on('e46_numemp', 'e60_numemp')
                    ->on('e46_emppresta', 'e45_sequencial');
            })
            ->join('empprestaitemdiaria', 'e446_empprestaitem', 'e46_codigo')
            ->join('db_usuacgm', 'id_usuario', 'e50_id_usuario')
            ->join('cgm AS cgmusu', 'cgmusu.z01_numcgm', 'cgmlogin')
            ->join('orcdotacao', function (JoinClause $join) {
                $join->on('o58_anousu', 'e60_anousu')
                    ->on('o58_coddot', 'e60_coddot');
            })
            ->join('empelemento', 'e64_numemp', 'e60_numemp')
            ->join('conplanoorcamento', function (JoinClause $join) {
                $join->on('c60_codcon', 'e64_codele')
                    ->on('c60_anousu', 'e60_anousu');
            })
            ->join('planodespesaconplanoorcamento', 'conplanoorcamento_codigo', 'c60_codigo')
            ->join('planodespesa', function (JoinClause $join) {
                $join->on('planodespesa.id', 'planodespesa_id')
                    ->on('exercicio', 'e60_anousu')
                    ->where('uniao', '=', $this->getUtilizaPlanoUniao())
                    ->whereRaw("
                        ( elemento in ('14', '15') or
                          conta in ('33903646', '33909214', '33909215', '44909214', '44909215'))
                    ");
            })
            ->join('empnotasigfistipodocliquidacao', 'e178_empnota', 'e69_codnota')
            ->join('sigfistipodocliquidacao', 'e177_sequencial', 'e178_sigfistipodocliquidacao')
            ->where('e60_anousu', $this->iAnoUsu)
            ->whereIn('e60_instit', $this->getListaInstituicoes())
            ->whereBetween('e69_dtinclusao', [$this->dtDataInicial, $this->dtDataFinal])
            ->where('e177_codigo', 4)
            ->orderBy('e60_numemp')
            ->get();

        if ($empenhos->isEmpty()) {
            throw new \Exception('Não foi encontrado nenhum empenho para o arquivo da Remessa.');
        }

        $obj = new stdClass();
        $obj->LiquidacoesEmpenhoDiarias = [];
        foreach ($empenhos as $empenho) {
            $indicadorDestino = $empenho->e446_tipodiaria;
            $estadoDestino = Helper::convertAndLimit(
                "<![CDATA[".substr(
                    mb_strtoupper($empenho->e446_estadodestino),
                    0,
                    2
                )
            );
            $cidadeDestino = Helper::convertAndLimit("<![CDATA[".$empenho->e446_destino, 59);
            $paisDestino = Helper::convertAndLimit("<![CDATA[".$empenho->e446_paisdestino, 59);

            /*
             *
             * Se o campo IndicadorDestino for "1-Nacional":
             * Os campos EstadoDestino e CidadeDestino deverão ser preenchidos e o campo PaisDestino deve ser nulo.
             *
             * Se o campo IndicadorDestino for "0-Internacional":
             * os campos EstadoDestino e CidadeDestino deverão ser nulos e o campo PaisDestino deve ser preenchido.
             */
            if ($indicadorDestino == 0) {
                $estadoDestino = null;
                $cidadeDestino = null;
            }

            $data = (object)[
                "Identificador" => $empenho->e82_codmov,
                "CodigoUnidadeGestora" => $this->sCodigoTribunal,
                "NumeroEmpenho" => $empenho->e60_codemp,
                "AnoEmpenho" => $empenho->e60_anousu,
                "AnoLiquidacaoDeEmpenho" => $empenho->e69_anousu,
                "Competencia" => $this->competencia,
                "CPFServidor" => $empenho->z01_cgccpf,
                "NumeroDiaria" => $empenho->e446_sequencial,
                "NumeroLiquidacaoEmpenho" => $empenho->e71_codord,
                "Nome" => Helper::convertAndLimit("<![CDATA[".$empenho->e46_nome, 255),
                "Valor" => $empenho->e70_valor,
                "QuantidadeDiarias" => $empenho->e446_quantidade,
                "DataSaida" => $empenho->e446_datainicio,
                "DataRetorno" => $empenho->e446_datafim,
                "IndicadorDestino" => $indicadorDestino,
                "EstadoDestino" => $estadoDestino,
                "CidadeDestino" => $cidadeDestino,
                "PaisDestino" => $paisDestino,
                "ObjetoDiaria" => Helper::convertAndLimit("<![CDATA[".$empenho->e446_motivo, 4000),
                "CodigoOrgao" => $empenho->o58_orgao,
                "CodigoUnidadeOrcamentaria" => $empenho->o58_unidade,
            ];

            $obj->LiquidacoesEmpenhoDiarias[] = (object)['LiquidacaoEmpenhoDiaria' => $data];
        }
        $this->aDados = $obj;
    }
}
