<?php

namespace App\Domain\Financeiro\Contabilidade\Services;

use App\Domain\Financeiro\Contabilidade\Models\Conplano;
use App\Domain\Financeiro\Contabilidade\Models\ConplanoExeContaCorrente;
use App\Domain\Financeiro\Contabilidade\Models\ConplanoExercicio;
use App\Domain\Financeiro\Contabilidade\Models\ConplanoExercicioEmpenho;
use App\Domain\Financeiro\Contabilidade\Models\ConplanoReduzido;
use App\Domain\Financeiro\Contabilidade\Models\ConplanoSistema;
use Exception;
use Illuminate\Support\Facades\DB;
use stdClass;

class ImplantacaoSaldoInicialService
{
    /**
     * @var integer
     */
    protected $exercicioAtual;
    /**
     * @var int
     */
    private $proximoExercicio;
    /**
     * @var integer
     */
    private $instituicao;
    /**
     * @var string
     */
    private $dataInicial;
    /**
     * @var string
     */
    private $dataFinal;

    private $reduzidosProximoExercicio = [];

    public function setExercicio($exercicio)
    {
        $this->exercicioAtual = $exercicio;
        $this->proximoExercicio = $exercicio + 1;
        return $this;
    }

    public function setInstituicao($instituicao)
    {
        $this->instituicao = $instituicao;
        return $this;
    }

    public function processar()
    {
        $this->dataInicial = "{$this->exercicioAtual}-01-01";
        $this->dataFinal = "{$this->exercicioAtual}-12-31";
        $this->apagaSaldoAtual();
        $this->implantarSaldoInicial();
        $this->implantarSaldoInicialPorRecurso();

        $this->implantarSaldoInicialPorRpDemaisContas();
    }


    private function implantarSaldoInicial()
    {
        ConplanoReduzido::query()
            ->select(['c61_anousu', 'c61_reduz', 'c61_instit', 'c61_codigo', 'natureza_saldo_final'])
            ->selectRaw('round(saldo_final, 2) as saldo_final')
            ->balanceteVerificacao($this->dataInicial, $this->dataFinal, true)
            ->where('c61_anousu', $this->exercicioAtual)
            ->where('c61_instit', $this->instituicao)
            ->get()
            ->each(function ($dado) {
                $atualizar = [
                    "c62_codrec" => $dado->c61_codigo,
                    "c62_vlrcre" => 0,
                    "c62_vlrdeb" => 0
                ];

                if ($dado->natureza_saldo_final === 'D') {
                    $atualizar['c62_vlrdeb'] = $dado->saldo_final;
                }

                if ($dado->natureza_saldo_final === 'C') {
                    $atualizar['c62_vlrcre'] = $dado->saldo_final;
                }

                ConplanoExercicio::where("c62_anousu", $this->proximoExercicio)
                    ->where("c62_reduz", $dado->c61_reduz)
                    ->update($atualizar);
            });
    }

    private function implantarSaldoInicialPorRecurso()
    {
        $contaCorrente = ConplanoSistema::find(100);
        $atributo = $contaCorrente->atributos->first()->atributo;
        $sql = sprintf(
            '(select array_agg(c61_reduz) from conplanoreduz where c61_instit = %s and c61_anousu = %s)::int[]',
            $this->instituicao,
            $this->exercicioAtual
        );

        $pl = "select * from contabilidade.balancete_verificacao_por_recurso";
        $dados = DB::select("{$pl}($this->exercicioAtual, '{$this->dataInicial}', '$this->dataFinal', true, $sql)");

        collect($dados)->map(function ($dado) use ($contaCorrente, $atributo) {
            $saldoInicial = new ConplanoExeContaCorrente();
            $saldoInicial->c143_conplanoreduz = $dado->reduzido;
            $saldoInicial->c143_exercicio = $this->proximoExercicio;
            $saldoInicial->contaCorrente()->associate($contaCorrente);
            $saldoInicial->c143_saldo = $dado->saldo_final < 0 ? $dado->saldo_final * -1 : $dado->saldo_final;
            $saldoInicial->c143_natureza = $dado->sinal_final;
            $saldoInicial->save();

            $saldoInicial->atributos()->create([
                'c144_conplanoinfocomplementar' => $atributo->c121_sequencial,
                'c144_valor' => $dado->id_recurso
            ]);
        });
    }

    private function apagaSaldoAtual()
    {
        $sql = sprintf(
            'select c61_reduz from conplanoreduz where c61_instit = %s and c61_anousu = %s',
            $this->instituicao,
            $this->exercicioAtual
        );

        ConplanoExeContaCorrente::query()
            ->whereRaw("c143_conplanoreduz in ($sql)")
            ->where('c143_exercicio', $this->proximoExercicio)
            ->delete();

        ConplanoExercicioEmpenho::query()
            ->whereRaw("c151_reduzido in ($sql)")
            ->where('c151_exercicio', $this->proximoExercicio)
            ->delete();
    }

    /**
     * Implanta o saldo das contas: 5312, 5317, 5322, 5327, 6311, 6321, 6327
     * @return void
     * @throws Exception
     */
    private function implantarSaldoInicialPorRpDemaisContas()
    {
        $sql = <<<SQL
            with contas as (
                select
                    c60_estrut as estrutural,
                    c60_descr as nome,
                    c60_codigo as codigo_conplano,
                    c61_instit as instituicao,
                    c61_reduz as reduzido,
                    substring(c60_estrut,1 ,1)::int classe,
                    c61_anousu,
                    c60_consistemaconta,
                    c60_identificadorfinanceiro,
                    c56_contabancaria
                from contabilidade.conplano
                join contabilidade.conplanoreduz on (c61_codcon, c61_anousu) = (c60_codcon, c60_anousu)
                left join contabilidade.conplanocontabancaria on conplanocontabancaria.c56_anousu = c61_anousu
                        and conplanocontabancaria.c56_reduz = c61_reduz
                where c60_anousu = {$this->exercicioAtual}
                  and substring(c60_estrut, 1, 4) in
                      ('5312', '5317', '5322', '5327', '6311', '6312', '6317', '6321', '6327')
                  and c61_instit = {$this->instituicao}
            ), saldo_inicial as (
                select contas.reduzido,
                      (case WHEN c151_natureza = 'C' THEN c151_valor * -1 else c151_valor end)::numeric(17, 2) as saldo,
                      c151_empenho as empenho
                from contas
                join contabilidade.conplanoexeempenho on conplanoexeempenho.c151_reduzido = contas.reduzido
                     and c151_exercicio = contas.c61_anousu
            ), debitos as (
                select contas.reduzido,
                       c75_numemp as empenho,
                       'D' as natureza,
                       c69_valor::numeric(17,2)  as valor,
                       c69_codlan
                from contas
                join conlancamval on c69_anousu = c61_anousu
                     and c69_debito = reduzido
                join contabilidade.conlancamdoc on conlancamdoc.c71_codlan = conlancamval.c69_codlan
                join contabilidade.conhistdoc on conhistdoc.c53_coddoc = conlancamdoc.c71_coddoc
                join contabilidade.conlancamemp on c75_codlan = c69_codlan
            ), creditos as (
                select contas.reduzido,
                       c75_numemp as empenho,
                       'C' as natureza,
                       (c69_valor * -1)::numeric(17,2) as valor,
                       c69_codlan
                  from contas
                  join conlancamval on c69_anousu = c61_anousu
                      and c69_credito = reduzido
                  join contabilidade.conlancamdoc on conlancamdoc.c71_codlan = conlancamval.c69_codlan
                  join contabilidade.conhistdoc on conhistdoc.c53_coddoc = conlancamdoc.c71_coddoc
                  join contabilidade.conlancamemp on c75_codlan = c69_codlan
            ), totaliza_movimentacao as (
                select reduzido,
                       empenho,
                       sum(case when natureza = 'D' then valor else 0 end) as valor_debito,
                       sum(case when natureza = 'C' then valor else 0 end) as valor_credito,
                       array_agg(c69_codlan) as lancamentos
                 from (
                    select * from debitos
                    union all
                    select *  from creditos
                ) as x group by 1, 2
            ), unifica as (
                select contas.*,
                       empenho,
                       saldo as saldo_inicial,
                       0 as valor_debito,
                       0 as valor_credito
                from contas
                join saldo_inicial si on si.reduzido = contas.reduzido
                union all
                select contas.*,
                       empenho,
                       0 as saldo_inicial,
                       valor_debito,
                       valor_credito
                  from contas
                  join totaliza_movimentacao mv on mv.reduzido = contas.reduzido
            ), totaliza_geral as (
                select estrutural,
                       codigo_conplano,
                       instituicao,
                       reduzido,
                       c61_anousu,
                       empenho,
                       sum(saldo_inicial) as saldo_inicial,
                       sum(valor_debito) as  valor_debito,
                       sum(valor_credito) as valor_credito,
                       sum(saldo_inicial + valor_debito + valor_credito) as saldo_final
                from unifica
                group by 1,2,3,4,5,6
            )
            select * from totaliza_geral where saldo_final != 0
SQL;
        $implantar = [];
        $dados = DB::select($sql);
        foreach ($dados as $dado) {
            $reduzido = $this->getReduzidoProximoExercicio($dado->reduzido, $dado->estrutural);
            $natureza = $dado->saldo_final < 0 ? 'C' : 'D';

            $implantar[] = [
                'c151_exercicio' => $this->proximoExercicio,
                'c151_reduzido' => $reduzido,
                'c151_empenho' => $dado->empenho,
                'c151_valor' => abs($dado->saldo_final),
                'c151_natureza' => $natureza
            ];

            if (count($implantar) > 100) {
                $this->implantaSaldoContaPorEmpenho($implantar);
                $implantar = [];
            }
        }

        $this->implantaSaldoContaPorEmpenho($implantar);
    }

    /**
     * Aplica bulk insert na persistência dos dados
     * @param array $dados
     * @return void
     */
    private function implantaSaldoContaPorEmpenho($dados)
    {
        (new ConplanoExercicioEmpenho())->insert($dados);
    }

    /**
     * Retorna o reduzido da conta no próximo exercício
     * Foi feito esse método pq aconteceu de o reduzido mudou de um exercício par o outro
     *
     * @param integer $reduzidoAtual
     * @param string $estrutural
     * @return integer
     * @throws Exception
     */
    private function getReduzidoProximoExercicio($reduzidoAtual, $estrutural)
    {
        if (!array_key_exists($reduzidoAtual, $this->reduzidosProximoExercicio)) {
            // tenta encontrar o mesmo reduzido
            $reduzido = ConplanoReduzido::query()
                ->select('c61_reduz')
                ->where('c61_anousu', $this->proximoExercicio)
                ->where('c61_reduz', $reduzidoAtual)
                ->where('c61_instit', $this->instituicao)
                ->first();
            if (!empty($reduzido)) {
                $this->reduzidosProximoExercicio[$reduzidoAtual] = $reduzido->c61_reduz;
                return $reduzido->c61_reduz;
            }

            // se não encontrar pelo mesmo reduzido, tenta buscar pelo estrutural
            $reduzido = Conplano::query()
                ->reduzidos()
                ->select('c61_reduz')
                ->where('c60_anousu', $this->proximoExercicio)
                ->where('c60_estrut', $estrutural)
                ->where('c61_instit', $this->instituicao)
                ->first();

            if (is_null($reduzido)) {
                $msg = "O estrutural {$estrutural} não possuí cadastrado no plano de contas do exercicio ";
                $msg .= "{$this->proximoExercicio}.";
                throw new Exception($msg);
            }

            $this->reduzidosProximoExercicio[$reduzidoAtual] = $reduzido->c61_reduz;
        }

        return $this->reduzidosProximoExercicio[$reduzidoAtual];
    }
}
