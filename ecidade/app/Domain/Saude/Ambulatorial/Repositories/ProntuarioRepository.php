<?php

namespace App\Domain\Saude\Ambulatorial\Repositories;

use App\Domain\Saude\Ambulatorial\Models\MovimentacaoProntuario;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Domain\Core\Base\Repository\BaseRepository;
use App\Domain\Saude\Ambulatorial\Models\Prontuario;

/**
 * @package App\Domain\Saude\Ambulatorial\Repositories
 */
class ProntuarioRepository extends BaseRepository
{
    protected $modelClass = Prontuario::class;

    /**
     * Considera a menor data dos procedimentos para busca dos atendimentos
     * @param \DateTime $periodoInicio
     * @param \DateTime $periodoFim
     * @param array $unidades
     * @param array|string|\Closure $where
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAtendimentos(\DateTime $periodoInicio, \DateTime $periodoFim, array $unidades, $where = null)
    {
        $dao = new \cl_prontproced;
        $sql = $dao->sql_query_file('', 'sd29_i_codigo', 'sd29_d_data limit 1', 'sd29_i_prontuario = sd24_i_codigo');

        $query = $this->newQuery()
            ->select(['prontuarios.*', 'sd29_d_data'])
            ->join('ambulatorial.prontuario_problemaspaciente', 's171_prontuario', 'sd24_i_codigo')
            ->join('ambulatorial.problemaspaciente', 's170_id', 's171_problemapaciente')
            ->join('plugins.psf_prontuario', 'sd30_i_prontuario', 'sd24_i_codigo')
            ->join('ambulatorial.prontproced', 'sd29_i_codigo', DB::raw("({$sql})"))
            ->whereBetween('sd29_d_data', [$periodoInicio->format('Y-m-d'), $periodoFim->format('Y-m-d')]);

        if (!empty($unidades)) {
            $query->whereIn('sd24_i_unidade', $unidades);
        }

        if ($where) {
            $query->where($where);
        }

        return $query->orderBy('sd29_d_data')->distinct()->get();
    }

    /**
     * Busca os atendimentos de acordo com os filtros informados
     * @param array $filtros
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function buscaMovimentacoes($filtros)
    {
        $unidade = $_SESSION['DB_coddepto'];

        $campos = [
            'sd24_i_codigo',
            'sd102_data',
            'sd102_hora',
            'sd102_codigo',
            'sd24_i_numcgs',
            'z01_v_nome',
            'z01_nome_social',
            'z01_d_nasc',
            'sd78_descricao',
            'sd78_cor',
            's144_c_descr',
            'sd91_descricao',
            'sd91_local',
            'sd102_situacao',
            'cgm1.z01_nome AS encaminhado',
            'cgm2.z01_nome AS atendimento',
            'sd24_c_cadastro',
            'sd24_d_cadastro'
        ];

        $query = $this->newQueryMovimentacao()
            ->select($campos)
            ->where('sd24_i_unidade', $unidade)
            ->where('sd102_situacao', '!=', MovimentacaoProntuario::SITUACAO_ATESTADO_EM_BRANCO);

        $this->aplicaFiltros($query, $filtros);

        $query->orderBy('sd24_i_codigo')
            ->orderBy('sd102_codigo', 'DESC');

        return $query->get();
    }

    /**
     * Busca os pacientes que estão em atendimento na unidade de acordo com os filtros informados
     * @param array $filtros
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function buscaPacienteProntuario($filtros)
    {
        $unidade = $_SESSION['DB_coddepto'];

        $campos = [
            'z01_i_cgsund',
            'z01_v_nome',
        ];

        $query = $this->newQueryMovimentacao()
            ->select($campos)
            ->distinct()
            ->where('sd24_i_unidade', $unidade)
            ->where('sd102_situacao', '!=', MovimentacaoProntuario::SITUACAO_ATESTADO_EM_BRANCO);

        if (!empty($filtros['pacienteNome'])) {
            $query->where('z01_v_nome', 'ilike', "%{$filtros['pacienteNome']}%");
        }

        $this->aplicaFiltros($query, $filtros);

        $query->orderBy('z01_v_nome');

        return $query->get();
    }

    /**
     * @return Builder|\Illuminate\Database\Query\Builder
     */
    private function newQueryMovimentacao()
    {
        return $this->newQuery()
            ->join('unidades', 'sd02_i_codigo', 'sd24_i_unidade')
            ->join('movimentacaoprontuario', 'sd102_prontuarios', 'sd24_i_codigo')
            ->join('setorambulatorial', 'sd91_codigo', 'sd102_setorambulatorial')
            ->join('sau_motivoatendimento', 's144_i_codigo', 'sd24_i_motivo')
            ->join('cgs', 'z01_i_numcgs', 'sd24_i_numcgs')
            ->join('cgs_und', 'z01_i_cgsund', 'sd24_i_numcgs')
            ->leftJoin('prontuariosclassificacaorisco', 'sd101_prontuarios', 'sd24_i_codigo')
            ->leftJoin('classificacaorisco', 'sd78_codigo', 'sd101_classificacaorisco')
            ->leftJoin('especmedico AS m1', 'm1.sd27_i_codigo', 'sd102_profissionalencaminhado')
            ->leftJoin('especmedico AS m2', 'm2.sd27_i_codigo', 'sd102_profissionalatendimento')
            ->leftJoin('unidademedicos AS u1', 'u1.sd04_i_codigo', 'm1.sd27_i_undmed')
            ->leftJoin('unidademedicos AS u2', 'u2.sd04_i_codigo', 'm2.sd27_i_undmed')
            ->leftJoin('medicos AS med1', 'med1.sd03_i_codigo', 'u1.sd04_i_medico')
            ->leftJoin('medicos AS med2', 'med2.sd03_i_codigo', 'u2.sd04_i_medico')
            ->leftJoin('cgm AS cgm1', 'cgm1.z01_numcgm', 'med1.sd03_i_cgm')
            ->leftJoin('cgm AS cgm2', 'cgm2.z01_numcgm', 'med2.sd03_i_cgm');
    }

    /**
     * @param $query
     * @param $filtros
     * @return void
     */
    private function aplicaFiltros($query, $filtros)
    {
        $dataInicial = Carbon::parse($filtros['dataInicial']);
        $dataFinal = Carbon::parse($filtros['dataFinal']);

        $dataInicial = $dataInicial->format('d/m/Y H:i:s');
        $dataFinal = $dataFinal->format('Y-m-d');

        if (!empty($filtros['atendimentoAberto'])) {
            $query->where('sd24_c_digitada', 'N');
        }

        if (!empty($filtros['faa'])) {
            $query->where('sd24_i_codigo', $filtros['faa']);
        }

        if (!empty($filtros['paciente'])) {
            $query->where('sd24_i_numcgs', $filtros['paciente']);
        }

        if (!empty($filtros['classificacaoRisco'])) {
            $query->where('sd78_codigo', $filtros['classificacaoRisco']);
        }

        if (!empty($filtros['motivoAtendimento'])) {
            $query->where('s144_i_codigo', $filtros['motivoAtendimento']);
        }

        if (!empty($filtros['setor'])) {
            $query->whereExists(function ($query) use ($filtros) {
                $sql = MovimentacaoProntuario::select(['sd102_setorambulatorial'])
                    ->whereRaw('sd102_prontuarios = sd24_i_codigo')
                    ->orderBy('sd102_codigo', 'DESC')
                    ->limit(1)
                    ->toSql();
                $query->select(DB::raw(1))
                    ->from(DB::raw("({$sql}) as x"))
                    ->where('x.sd102_setorambulatorial', $filtros['setor']);
            });
        }

        if (!empty($filtros['profissionalEncaminhado'])) {
            $query->whereExists(function ($query) use ($filtros) {
                $sql = MovimentacaoProntuario::select(['sd102_profissionalencaminhado'])
                    ->whereRaw('sd102_prontuarios = sd24_i_codigo')
                    ->orderBy('sd102_codigo', 'DESC')
                    ->limit(1)
                    ->toSql();
                $query->select(DB::raw(1))
                    ->from(DB::raw("({$sql}) as x"))
                    ->where('x.sd102_profissionalencaminhado', $filtros['profissionalEncaminhado']);
            });
        }

        if (!empty($filtros['situacao'])) {
            $query->whereExists(function ($query) use ($filtros) {
                $sql = MovimentacaoProntuario::select(['sd102_situacao'])
                    ->whereRaw('sd102_prontuarios = sd24_i_codigo')
                    ->orderBy('sd102_codigo', 'DESC')
                    ->limit(1)
                    ->toSql();
                $query->select(DB::raw(1))
                    ->from(DB::raw("({$sql}) as x"))
                    ->where('x.sd102_situacao', $filtros['situacao']);
            });
        }

        if (!empty($filtros['profissionalAtendimento'])) {
            $query->whereExists(function ($query) use ($filtros) {
                $sql = MovimentacaoProntuario::select(['sd102_profissionalatendimento'])
                    ->whereRaw('sd102_prontuarios = sd24_i_codigo')
                    ->orderBy('sd102_codigo', 'DESC')
                    ->limit(1)
                    ->toSql();
                $query->select(DB::raw(1))
                    ->from(DB::raw("({$sql}) as x"))
                    ->where('x.sd102_profissionalatendimento', $filtros['profissionalAtendimento']);
            });
        }

        if (!empty($filtros['dataInicial'])) {
            $query->where('sd24_d_cadastro', '>=', $dataInicial);
        }

        if (!empty($filtros['dataFinal'])) {
            $query->where('sd24_d_cadastro', '<=', $dataFinal);
        }
    }
}
