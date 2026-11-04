<?php

namespace App\Domain\Financeiro\Contabilidade\Services;

use App\Domain\Financeiro\Contabilidade\Models\SigfisUnidadeGestoraModel;
use BusinessException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SigfisUnidadeGestoraService
{
    /**
     * Retorna todas as unidade gestora
     *
     * @return Collection|null
     */
    public function all()
    {
        return SigfisUnidadeGestoraModel::select([
            'c179_sequencial AS sequencial',
            'c179_codigo AS codigo',
            'c179_instit AS instit',
            'c179_responsavelfolha as responsavelfolha',
            'c179_codigofolha as codigofolha',
            'c179_cgmordenadordespesa as cgmordenadordespesa'
        ])
        ->selectRaw("c179_instit || ' - ' || nomeinst AS codnomeinstit")
        ->selectRaw("c179_cgmordenadordespesa || ' - ' || z01_nome AS cgmnomeordenador")
        ->join('db_config', 'db_config.codigo', 'c179_instit')
        ->join('cgm', 'c179_cgmordenadordespesa', 'z01_numcgm')
        ->orderBy('c179_instit')
        ->get();
    }

    /**
     * Filtra unidade gestora pelo campo informado
     *
     * @param string $field
     * @param mixed $value
     * @return SigfisUnidadeGestoraModel|null
     */
    public function findBy($field, $value)
    {
        return SigfisUnidadeGestoraModel::where($field, $value)->first();
    }

    /**
     * Retorna um instancia de query build
     *
     * @return Builder
     */
    public function query()
    {
        return SigfisUnidadeGestoraModel::query();
    }

    /**
     * Static para retornar instancia da UG pela instituicao
     *
     * @param int $instit Cod. da instituicao
     * @return SigfisUnidadeGestoraModel
     * @throws BusinessException
     */
    public static function getUnidadeGestora($instit)
    {
        $unidadeGestora = SigfisUnidadeGestoraModel::where('c179_instit', $instit)->first();

        if (empty($unidadeGestora)) {
            throw new BusinessException("
                Unidade Gestora para a Instituição {$instit} não encontrada.\n
                Cadastre em: TCE/RJ > Configurações > Unidade Gestora
            ");
        }

        return $unidadeGestora;
    }

    /**
     * Static para retornar um array com o código de todas as instituicoes que possuem o mesmo código de UG
     *
     * @param int $instit Cod. da UG
     * @return SigfisUnidadeGestoraModel
     */
    public static function getInstituicoesPorCodigoUnidadeGestora($codigoUnidade)
    {
        return SigfisUnidadeGestoraModel::select("c179_instit")
        ->where('c179_codigo', $codigoUnidade)
        ->get()
        ->map(function ($instituicao) {
            return $instituicao->c179_instit;
        })->toArray();
    }

    /**
     * Persiste os dados da unidade gestora
     *
     * @param object $data
     * @return void
     * @throws BusinessException
     */
    public function store($data)
    {
        $existsRecord = $this->query()
            ->where(function ($query) use ($data) {
                $query->where('c179_instit', $data->instit);
            })
            ->when(isset($data->sequencial), function ($query) use ($data) {
                $query->where('c179_sequencial', '<>', $data->sequencial);
            })
            ->first(['c179_sequencial']);

        if ($existsRecord) {
            throw new BusinessException('Instituição já cadastrados');
        }

        if (isset($data->sequencial)) {
            $unidadeGestora = SigfisUnidadeGestoraModel::findOrFail($data->sequencial);
        } else {
            $unidadeGestora = new SigfisUnidadeGestoraModel();
        }

        $unidadeGestora->c179_codigo = $data->codigo;
        $unidadeGestora->c179_instit = $data->instit;
        $unidadeGestora->c179_responsavelfolha = $data->responsavelfolha;
        $unidadeGestora->c179_cgmordenadordespesa = $data->cgmordenadordespesa;
        $unidadeGestora->c179_codigofolha = null;

        if ($data->responsavelfolha === false && isset($data->codigofolha)) {
            $unidadeGestora->c179_codigofolha = $data->codigofolha;
        }

        $unidadeGestora->save();
    }
}
