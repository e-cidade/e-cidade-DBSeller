<?php

namespace App\Domain\Financeiro\Contabilidade\Services;

use App\Domain\Financeiro\Contabilidade\Models\ConplanoExercicioEmpenho;
use App\Domain\Financeiro\Contabilidade\Models\ConplanoReduzido;
use BusinessException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class MapeamentoEmpenhoRPContaService
{
    private $estruturais = ['5312', '5317', '5322', '5327', '6311', '6312', '6317', '6321', '6327'];

    /**
     * Fetch das contas ja mapeadas do exercício filtrado
     *
     * @param object $filters
     * @return Collection
     */
    public function getContasMapeadas($filters)
    {
        $dataIni = "{$filters->exercicio}-01-01";
        $dataFim = "{$filters->exercicio}-01-01";

        $contasMapeadas = ConplanoExercicioEmpenho::query()
            ->select('c60_estrut', 'c60_descr', 'c151_reduzido', 'c151_exercicio')
            ->selectRaw("(select fc_planosaldonovo_array[1] from
                fc_planosaldonovo_array($filters->exercicio, c151_reduzido, '$dataIni', '$dataFim', true))
                as saldo_conta
            ")
            ->selectRaw("(select fc_planosaldonovo_array[5] from
                fc_planosaldonovo_array($filters->exercicio, c151_reduzido, '$dataIni', '$dataFim', true))
                as sinal_saldo
            ")
            ->selectRaw("coalesce(sum(
                case when c151_natureza = 'C' then c151_valor *-1 else c151_valor end
                ), 0) as saldo_empenhos
            ")
            ->distinct()
            ->join('conplanoreduz', function ($join) {
                $join->on('c61_reduz', 'c151_reduzido')->on('c61_anousu', 'c151_exercicio');
            })
            ->join('conplano', function ($join) {
                $join->on('c60_codcon', 'c61_codcon')->on('c60_anousu', 'c61_anousu');
            })
            ->where('c151_exercicio', $filters->exercicio)
            ->where('c61_instit', $filters->instituicao)
            ->groupBy('c60_estrut', 'c60_descr', 'c151_reduzido', 'c151_exercicio')
            ->get();

        return $contasMapeadas;
    }


    /**
     * Fetch das contas de RP disponiveis para mapeamento
     *
     * @return void
     */
    public function getContasRP($exercicio, $instit)
    {
        $dataIni = "{$exercicio}-01-01";
        $dataFim = "{$exercicio}-01-01";

        $constasRP = ConplanoReduzido::query()
            ->select('c60_estrut', 'c60_descr', 'c61_reduz', 'c61_anousu', 'c61_instit')
            ->selectRaw("(select fc_planosaldonovo_array[1] from
                fc_planosaldonovo_array($exercicio, c61_reduz, '$dataIni', '$dataFim', true)) as saldo
            ")
            ->selectRaw("(
               select coalesce(sum(case when c151_natureza = 'C' then c151_valor *-1 else c151_valor end), 0)
                 from conplanoexeempenho
                where c151_exercicio = c61_anousu
                and c151_reduzido = c61_reduz) as saldo_mapeado
            ")
            ->distinct()
            ->join('conplano', function ($join) {
                $join->on('c60_codcon', 'c61_codcon')->on('c60_anousu', 'c61_anousu');
            })
            ->where('c60_anousu', $exercicio)
            ->where('c61_instit', $instit)
            ->whereIn(DB::raw("substring(c60_estrut, 1, 4)"), $this->estruturais)
            ->whereRaw("
                (select fc_planosaldonovo_array[1] from
                fc_planosaldonovo_array($exercicio, c61_reduz, '$dataIni', '$dataFim', true))::numeric >
                (select coalesce(sum(c151_valor), 0) from conplanoexeempenho
                    where c151_exercicio = c61_anousu
                    and c151_reduzido = c61_reduz
                )::numeric
            ")
            ->orderBy('c60_estrut')
            ->get();

        return $constasRP;
    }

    /**
     * Fetch dos empenhos que compoe o saldo da conta
     *
     * @param object $filters
     * @return Paginator
     */
    public function getEmpenhosConta($filters)
    {
        $filtersEmpenho = false;
        if (isset($filters->empenho) && !empty($filters->empenho)) {
            $filtersEmpenho = $filters->empenho;
        }

        $empenhos = ConplanoExercicioEmpenho::query()
            ->select('c151_codigo', 'e60_numemp', 'z01_nome', 'z01_numcgm', 'c151_valor', 'c151_natureza')
            ->selectRaw("(e60_codemp || '/' || e60_anousu) e60_codemp")
            ->join('empempenho', 'e60_numemp', 'c151_empenho')
            ->join('cgm', 'z01_numcgm', 'e60_numcgm')
            ->where('c151_reduzido', $filters->reduzido)
            ->where('c151_exercicio', $filters->exercicio)
            ->when($filtersEmpenho, function ($query) use ($filtersEmpenho) {
                return $query->where(function ($q) use ($filtersEmpenho) {
                    if (is_numeric($filtersEmpenho)) {
                        $q->where('c151_empenho', $filtersEmpenho);
                    }
                    $q->orWhere('e60_codemp', 'ilike', "{$filtersEmpenho}%");
                    $q->orWhere('z01_nome', 'ilike', "%{$filtersEmpenho}%");
                });
            })
            ->orderBy('e60_numemp')
            ->paginate(20);

        return $empenhos;
    }

    /**
     * Empenhos disponiveis para mapeamento
     *
     * @param object $filters
     * @return Collection
     * @throws BusinessException
     */
    public function getEmpenhos($filters)
    {
        $exercicio = $filters->exercicio;
        $tipoBusca = $filters->tipoBusca;

        $estrutural = $this->getEstrutural($filters->reduzido, $exercicio);
        $conta = [
            "estrutural" => $estrutural,
            "part_estrutural" => substr($estrutural, 0, 4),
            "reduzido" => $filters->reduzido,
        ];

        if ($tipoBusca == 0) {

            /**
             * As contas listadas abaixo buscam os valores dos lançamentos contábeis
             */

            if (in_array($conta['part_estrutural'], ['6311', '6312', '6317'])) {
                return $this->retornaEmpenhosDosLancamentos($conta, $exercicio);
            }
        }

        // confirmado
        // calcular pelo RP as contas 5312, 5317, 5322
        $instit = session('DB_instit');
        $isEmpeenhoExercicio = $this->isEmpenhoDoExercicio($conta['part_estrutural']);

        $queryEmpenhos = DB::table('empempenho')
            ->select(
                'z01_nome',
                'e60_emiss',
                'e60_numemp',
                'e91_vlremp',
                'e91_vlrpag',
                'e91_vlrliq',
                'e91_vlranu'
            )
            ->selectRaw("(e60_codemp || '/' || e60_anousu) e60_codemp")
            ->leftJoin('empresto', function ($join) use ($exercicio) {
                $join->on('e60_numemp', 'e91_numemp')
                    ->where('e91_anousu', $exercicio);
            })
            ->join('empelemento', 'e64_numemp', 'e60_numemp')
            ->join('cgm', 'z01_numcgm', 'e60_numcgm')
            ->where('e60_instit', $instit)
            ->when($isEmpeenhoExercicio, function ($query) use ($exercicio) {
                return $query->where('e60_anousu', $exercicio - 1);
            })
            ->when(!$isEmpeenhoExercicio, function ($query) use ($exercicio) {
                return $query->where('e60_anousu', '<=', $exercicio - 2);
            })
            ->whereNotExists(function ($query) use ($conta) {
                $query->select(DB::raw(1))
                    ->from('conplanoexeempenho')
                    ->whereColumn('c151_empenho', 'e60_numemp')
                    ->whereColumn('c151_exercicio', 'e91_anousu')
                    ->where('c151_reduzido', $conta['reduzido']);
            })
            ->orderBy('e64_numemp');

        $queryEmpenhos = $this->setFilterSaldoOnQuery($queryEmpenhos, $conta, $filters->tipoLiquidacao);

        return $queryEmpenhos
            ->get()
            ->map(function ($empenho) use ($conta) {
                $classe = substr($conta['estrutural'], 0, 1);
                $natureza = ($classe % 2 == 0) ? 'C' : 'D';
                $empenho->saldo = $natureza === 'C' ? $empenho->saldo *-1 : $empenho->saldo;
                $empenho->natureza = $natureza;
                return $empenho;
            });
    }

    /**
     * Filtro do tipo de saldo
     *
     * @param Builder $query
     * @param array $conta
     * @param integer $filtroLiquidacao verificar tipo em self::getFormulaSaldo
     * @param integer $reduzido
     * @return Builder
     * @throws BusinessException
     */
    private function setFilterSaldoOnQuery(Builder $query, $conta, $filtroLiquidacao = 1)
    {
        switch ($conta['part_estrutural']) {
            case '5312':
            case '5317':
                $formula = $this->getFormulaSaldo($filtroLiquidacao);
                $query->selectRaw($formula . ' as saldo');
                $query->whereRaw($formula . " > 0");
                break;

            case '5322':
            case '6321':
            case '5327':
            case '6327':
                $formula = $this->getFormulaSaldo(2);
                $query->selectRaw($formula . ' as saldo');
                $query->whereRaw($formula . " > 0");
                break;

            case '6311':
            case '6312':
            case '6317':
                $formula = $this->getFormulaSaldo(1);
                $query->selectRaw($formula . ' as saldo');
                $query->whereRaw($formula . " > 0");
                break;
        }

        return $query;
    }

    /**
     * Retorna a formula para calculo do saldo
     *
     * 1. Saldo a liquidar empenhos (total)
     * 2. Saldo a pagar liquidado
     * 3. Saldo a liquidar
     * 4. Saldo a liquidar (em liquidacao)
     *
     * @param int $type
     * @return void
     */
    private function getFormulaSaldo($type, $reduzido = null)
    {
        switch ($type) {
            case 1:
                return "round((e91_vlremp - e91_vlranu - e91_vlrliq), 2)";
            case 2:
                return "round((e91_vlrliq - e91_vlrpag), 2)";
            case 3:
                return "(select sum(c70_valor) from conlancam
                    join conlancamdoc ON conlancamdoc.c71_codlan = conlancam.c70_codlan
                    join conlancamemp ON conlancamemp.c75_codlan = conlancam.c70_codlan
                    where conlancamemp.c75_numemp = empempenho.e60_numemp
                    and conlancam.c70_anousu = empempenho.e60_anousu
                    and c71_coddoc = 1011)";
            case 4:
                return "(select sum(c70_valor) from conlancam
                    join conlancamdoc ON conlancamdoc.c71_codlan = conlancam.c70_codlan
                    join conlancamemp ON conlancamemp.c75_codlan = conlancam.c70_codlan
                    where conlancamemp.c75_numemp = empempenho.e60_numemp
                    and conlancam.c70_anousu = empempenho.e60_anousu
                    and c71_coddoc = 1012)";
            default:
                throw new BusinessException("Formula para filtro do saldo n?o encontrada");
        }
    }

    /**
     * Se o estrutural informado eh para filtro
     * no exercicio ou em exercio anterior
     *
     * @param string $estrutural
     * @return boolean
     */
    private function isEmpenhoDoExercicio($estrutural)
    {
        switch ($estrutural) {
            case '5317':
            case '5327':
            case '6317':
            case '6327':
                return true;
            default:
                return false;
        }
    }

    /**
     * Verifica se possui saldo suficiente
     *
     * @param array $empenhos
     * @param numeric $reduzido
     * @param numeric $exercicio
     * @return boolean
     * @throws BusinessException
     */
    private function checkSaldo($empenhos, $reduzido, $exercicio)
    {
        $saldoConta = DB::select("
            select fc_planosaldonovo_array[1] as saldo from
            fc_planosaldonovo_array($exercicio, '$reduzido', '{$exercicio}-01-01', '{$exercicio}-01-01', true)
        ");

        if (empty($saldoConta) && !is_array($saldoConta)) {
            throw new BusinessException("Saldo da conta {$reduzido} no exercicio {$exercicio} inexistente");
        }

        $saldoMapeadoQuery = ConplanoExercicioEmpenho::query()
            ->selectRaw('round(sum(c151_valor), 2) as saldo')
            ->where('c151_exercicio', $exercicio)
            ->where('c151_reduzido', $reduzido)
            ->groupBy('c151_reduzido', 'c151_exercicio')
            ->first();

        $saldoMapeado = empty($saldoMapeadoQuery) ? '0' : $saldoMapeadoQuery->saldo;

        $saldoEmpenhos = collect($empenhos)->reduce(function ($total, $empenho) {
            return $empenho['saldo'] + $total;
        }, 0);

        $saldoEmpenhos = number_format($saldoEmpenhos, 2, '.', '');
        $saldoDisponivel = bcsub($saldoConta[0]->saldo, $saldoMapeado, 2);

        if (bccomp($saldoDisponivel, $saldoEmpenhos, 2) < 0) {
            throw new BusinessException("Saldo da conta {$reduzido} no exercicio {$exercicio} insuficiente");
        } else {
            return true;
        }
    }

    /**
     * Salva os mapeamentos
     *
     * @param object $data
     * @return void
     * @throws BusinessException
     */
    public function saveEmpenhos($data)
    {
        $estrutural = $this->getEstrutural($data->reduzido, $data->exercicio);

        if (!$this->checkSaldo($data->empenhos, $data->reduzido, $data->exercicio)) {
            return false;
        }

        foreach ($data->empenhos as $empenho) {
            $empenho = (object)$empenho;

            $mapeamento = new ConplanoExercicioEmpenho();
            $mapeamento->c151_reduzido = $data->reduzido;
            $mapeamento->c151_empenho = $empenho->e60_numemp;
            $mapeamento->c151_exercicio = $data->exercicio;
            $mapeamento->c151_valor = $empenho->saldo < 0 ? $empenho->saldo * -1 : $empenho->saldo;
            $mapeamento->c151_natureza = $empenho->saldo < 0 ? 'C' : 'D';

            $mapeamento->save();
        }
    }

    /**
     * Delete emepenho mapeado
     *
     * @param int $codigo c151_codigo
     * @return void
     */
    public function deleteEmpenho($codigo)
    {
        $conplanoExeEmpenho = ConplanoExercicioEmpenho::where('c151_codigo', $codigo)->first();

        if (!$conplanoExeEmpenho) {
            throw new BusinessException("N?o foi possivel localizar o mapeamento {$codigo}");
        }

        $conplanoExeEmpenho->delete();
    }

    /**
     * Delete todos os empenhos mapeados
     *
     * @param numeric $reduzido
     * @param numeric $exercicio
     * @return void
     */
    public function deleteAllEmpenhos($reduzido, $exercicio)
    {
        $conplanoExeEmpenho = ConplanoExercicioEmpenho::where('c151_reduzido', $reduzido)
            ->where('c151_exercicio', $exercicio);


        if (!$conplanoExeEmpenho) {
            throw new BusinessException("N?o foi possivel localizar o mapeamento da conta: {$reduzido}");
        }

        $conplanoExeEmpenho->delete();
    }

    /**
     * Retorna o estrutural da conta
     *
     * @param numeric $reduzido
     * @return string
     */
    private function getEstrutural($reduzido, $exercicio)
    {
        $estrutural = DB::table('conplano')
            ->select('c60_estrut')
            ->join('conplanoreduz', function ($join) {
                $join->on('c61_codcon', 'c60_codcon')
                    ->on('c61_anousu', 'c60_anousu');
            })
            ->where('c61_reduz', $reduzido)
            ->where('c61_anousu', $exercicio)
            ->first();

        if (!$estrutural) {
            throw new BusinessException("N?o foi possivel buscar o estrutural de {$reduzido}");
        }

        return $estrutural->c60_estrut;
    }

    private function retornaEmpenhosDosLancamentos($conta, $exercicio)
    {
        $outrosFiltros = $this->outrosFiltrosDadosEmpenhosLancamentos($conta, $exercicio);
        $reduzido = $conta['reduzido'];
        $sql = "
        with debitos as (
            select c75_numemp,
                   c69_valor::numeric(17,2)  as valor_debito
            from conlancamval
            join conlancamemp on c75_codlan = c69_codlan
            join conlancamdoc on c71_codlan = c69_codlan
            join empempenho on e60_numemp = c75_numemp
            where c69_debito = $reduzido
              and $outrosFiltros
        ), creditos as (
         select c75_numemp, (c69_valor *-1)::numeric(17,2) as valor_credito
            from conlancamval
            join conlancamemp on c75_codlan = c69_codlan
            join conlancamdoc on c71_codlan = c69_codlan
            join empempenho on e60_numemp = c75_numemp
            where c69_credito = $reduzido
              and $outrosFiltros
        ), totaliza as (
        select sum(valor_credito) as valor_credito , sum(valor_debito) as valor_debito
            from (
                select c75_numemp, valor_credito, 0 as valor_debito from creditos
                union all
                select c75_numemp, 0 as valor_credito, valor_debito from debitos
            ) as x
        ), unifica as (
            select c75_numemp, sum(valor_credito) as valor_credito , sum(valor_debito) as valor_debito
            from (
                select c75_numemp, valor_credito, 0 as valor_debito from creditos
                union all
                select c75_numemp, 0 as valor_credito, valor_debito from debitos
            ) as x
            group by 1
        ), outros_dados as (
            select z01_nome,
                   e60_emiss,
                   e60_numemp,
                   (e60_codemp || '/' || e60_anousu) e60_codemp,
                   valor_credito,
                   valor_debito,
                   valor_credito + valor_debito as saldo
              from unifica
              join empempenho on e60_numemp = c75_numemp
              join cgm on z01_numcgm = e60_numcgm
             where not exists (
                select 1
                 from conplanoexeempenho
                where c151_empenho = e60_numemp
                  and c151_exercicio = $exercicio
                  and c151_reduzido = $reduzido
             )
        )
        select * from outros_dados where saldo != 0 order by e60_numemp
        ";

        return DB::select($sql);
    }

    /**
     * @param $conta
     * @param $exercicio
     * @return string
     */
    private function outrosFiltrosDadosEmpenhosLancamentos($conta, $exercicio)
    {
        $exercicioAnterior = $exercicio - 1;
        $where = ["c69_anousu = {$exercicioAnterior}"];

        $part = substr($conta['estrutural'], 0, 5);
        if ($part === '63171') {
            $where[] = "c71_coddoc = 1011";
        }

        /**
         * Conta 6312 deve ver os empenhos de RP de exercício anterior
         */
        $parts = ['6312', '6311'];
        if (in_array($conta['part_estrutural'], $parts)) {
            $exercicioAnterior = $exercicio - 1;
            $exercicioEmpenho = $exercicio - 2;
            $where = [
                "c69_anousu = {$exercicioAnterior}",
                "e60_anousu <= $exercicioEmpenho"
            ];

            if ($conta['part_estrutural'] === '6312') {
                $where[] = "c71_coddoc in (39,40, 212,213, 214,215, 216,217)";
            }

            if ($conta['part_estrutural'] === '6311') {
                $where[] = "c71_coddoc in (32,33,34,39,40,212,213,214,215,216,217,2032)";
            }
        }

        return implode(' and ', $where);
    }
}
