<?php

namespace App\Domain\RecursosHumanos\Pessoal\Services\Rubrica;

use App\Domain\RecursosHumanos\Pessoal\Enum\TipoFolhaEnum;
use App\Domain\RecursosHumanos\Pessoal\Model\Rubrica\ObservacaoRubrica;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ObservacaoRubricaService
{

    /**
     * Salvar de uma requisição legada
     * @param object
     * @return void
     */
    public function saveFromLegacy(\stdClass $params)
    {
        $observacaoRubrica = new ObservacaoRubrica();
        $observacao = $this->getObservacaoRubricaServidor($params);
        if ($observacao) {
            $observacaoRubrica = ObservacaoRubrica::find($observacao->sequencial);
        }
        $observacaoRubrica->rh315_matricula  = $params->matricula;
        $observacaoRubrica->rh315_rubrica    = $params->rubrica;
        $observacaoRubrica->rh315_anousu     = $params->anousu;
        $observacaoRubrica->rh315_mesusu     = $params->mesusu;
        $observacaoRubrica->rh315_observacao = $params->observacao;
        $observacaoRubrica->rh315_tipo       = TipoFolhaEnum::getTipoFolhaByPonto($params->ponto);
        $observacaoRubrica->rh315_instit     = $params->instit;
        $observacaoRubrica->save();
    }

    /**
     * Busca as informações de uma requisição legada
     * @param object
     * @return ObservacaoRubrica
     */
    public function getObservacaoRubricaServidor(\stdClass $params)
    {
        $observacaoRubrica = ObservacaoRubrica::query();

        if ($params->matricula) {
            $observacaoRubrica->where('rh315_matricula', '=', $params->matricula);
        }
        if ($params->rubrica) {
            $observacaoRubrica->where('rh315_rubrica', '=', $params->rubrica);
        }
        if ($params->anousu) {
            $observacaoRubrica->where('rh315_anousu', '=', $params->anousu);
        }
        if ($params->mesusu) {
            $observacaoRubrica->where('rh315_mesusu', '=', $params->mesusu);
        }
        if ($params->ponto) {
            $observacaoRubrica->where('rh315_tipo', '=', TipoFolhaEnum::getTipoFolhaByPonto($params->ponto));
        }
        if ($params->tabela) {
            $observacaoRubrica->where('rh315_tipo', '=', TipoFolhaEnum::getTipoFolhaByTabela($params->tabela));
        }
        if ($params->instit) {
            $observacaoRubrica->where('rh315_instit', '=', $params->instit);
        }

        return $observacaoRubrica->get(
            ['rh315_observacao as observacao', 'rh315_sequencial as sequencial']
        )->first();
    }

    /**
     * Deleta informações da observação de uma requisição legada
     * @param object
     * @return void
     */
    public function deleteFromLegacy(\stdClass $params)
    {
        ObservacaoRubrica::where('rh315_matricula', '=', $params->matricula)
            ->where('rh315_rubrica', '=', $params->rubrica)
            ->where('rh315_anousu', '=', $params->anousu)
            ->where('rh315_mesusu', '=', $params->mesusu)
            ->where('rh315_tipo', '=', TipoFolhaEnum::getTipoFolhaByPonto($params->ponto))
            ->where('rh315_instit', '=', $params->instit)
            ->delete();
    }

    public function getDadosEmissaoRelatorio(Request $request)
    {
        $query = ObservacaoRubrica::select([
            'rh02_regist as matricula',
            'cgm.z01_nome as nome',
            'rh27_rubric as codigo_rubrica',
            'rh27_descr as descricao_rubrica',
            'rh315_observacao as observacao',
            'rh315_tipo as tipo_folha'

        ])
        ->join('rhpessoal', 'rh01_regist', DB::raw('rh315_matricula and rh01_instit = rh315_instit'))
        ->join('rhrubricas', 'rh27_rubric', DB::raw('rh315_rubrica and rh27_instit = rh315_instit'))
        ->join('cgm', 'z01_numcgm', 'rh01_numcgm')
        ->join('rhpessoalmov', 'rh02_regist', DB::raw("rh01_regist
                                            and rh02_instit = rh01_instit
                                            and rh02_anousu = {$request->ano}
                                            and rh02_mesusu = {$request->mes}
                                        "))
        ->leftJoin('rhpeslocaltrab', 'rh56_seqpes', DB::raw('rh02_seqpes and rh56_princ is true'))
        ->leftJoin('rhlocaltrab', 'rh55_codigo', DB::raw('rh56_localtrab and rh55_instit = rh02_instit'))
        ->leftJoin('rhlota', 'r70_codigo', DB::raw('rh02_lota and r70_instit = rh02_instit'))
        ->leftJoin('rhfuncao', 'rh37_funcao', DB::raw('rh02_funcao and rh37_instit = rh02_instit'))
        ->where('rh315_instit', '=', $request->DB_instit)
        ->where('rh315_anousu', '=', $request->ano)
        ->where('rh315_mesusu', '=', $request->mes);

        $filtrosResumos = [
            1 => '',
            2 => 'r70_codigo',
            3 => 'rh02_regist',
            4 => 'rh55_codigo',
            5 => 'rh37_funcao'
        ];

        if ($request->tipo_filtro && $request->tipo_filtro == 3) {
            $registros = array_map(function ($item) {
                return $item['codigo'];
            }, $request->registros);

            $query->whereIn($filtrosResumos[$request->tipo_resumo], $registros);
        } elseif ($request->tipo_filtro && $request->tipo_filtro == 2) {
            $query->whereBetween($filtrosResumos[$request->tipo_resumo], [$request->inicio, $request->fim]);
        }

        if ($request->rubrica) {
            $query->where('rh27_rubric', '=', $request->rubrica);
        }

        return $query->get();
    }
}
