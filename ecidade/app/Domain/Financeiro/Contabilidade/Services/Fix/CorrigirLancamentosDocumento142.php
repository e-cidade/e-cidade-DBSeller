<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Fix;

use App\Domain\Financeiro\Contabilidade\Models\ConlancamRecurso;
use Illuminate\Support\Facades\DB;

class CorrigirLancamentosDocumento142
{

    private $tableName;
    private $exercicio;

    /**
     * Array com o reduzido da conta 72111 por instituição
     * @var array
     */
    private $contas72111 = [];

    /**
     * Array com o reduzido da conta 8211101 por instituição
     * @var array
     */
    private $contas8211101 = [];

    public function arrumar($exercicio)
    {
        $this->exercicio = $exercicio;
        $this->tableName = 'w_doc142_arrumar_recursos_' . time();

        $this->contas72111 = $this->getReduzidos('72111');
        $this->contas8211101 = $this->getReduzidos('8211101');

        $this->criaTabelaLancamentos();
        $this->deletaConlancamRecurso();
        $this->recriaConlancamRecurso();
    }

    /**
     * Cria uma tabela com os documentos do lançamento 142
     * @return void
     */
    private function criaTabelaLancamentos()
    {
        DB::statement("drop table if exists {$this->tableName}");
        DB::statement(<<<SQL
create table $this->tableName as
select c70_codlan as lancamento,
       c70_anousu as exercicio,
       c69_credito as credito,
       c69_debito as debito,
       reduz_credito.c61_instit as instituicao,
       reduz_credito.c61_codigo as recurso_credito,
       reduz_debito.c61_codigo as recurso_debito
  from contabilidade.conlancam
  join contabilidade.conlancamdoc on c71_codlan = c70_codlan
  join contabilidade.conlancamval on c69_codlan  = c70_codlan
  join contabilidade.conplanoreduz reduz_credito on reduz_credito.c61_reduz = conlancamval.c69_credito
       and reduz_credito.c61_anousu = conlancamval.c69_anousu
  join contabilidade.conplanoreduz reduz_debito on reduz_debito.c61_reduz = conlancamval.c69_debito
       and reduz_debito.c61_anousu = conlancamval.c69_anousu
 where c70_anousu = $this->exercicio
   and c71_coddoc in (142, 143)
   and c69_ordem = 1;
SQL
        );
    }

    private function deletaConlancamRecurso()
    {
        DB::statement(<<<SQL
delete from contabilidade.conlancamrecurso
using {$this->tableName}
where c130_conlancam = lancamento;
SQL
        );
    }

    private function recriaConlancamRecurso()
    {
        $inserts = [];

        $lancamentos = DB::select("select * from {$this->tableName}");
        foreach ($lancamentos as $lancamento) {
            $inserts[] = $this->primeiroLancamento($lancamento, 'D');
            $inserts[] = $this->primeiroLancamento($lancamento, 'C');

            $conta72111 = $this->contas72111[$lancamento->instituicao];
            $conta8211101 = $this->contas8211101[$lancamento->instituicao];

            // segundo lancamento
            $inserts[] = $this->lancamento($lancamento, $conta72111, $lancamento->recurso_debito, 'D');
            $inserts[] = $this->lancamento($lancamento, $conta8211101, $lancamento->recurso_debito, 'C');

            // terceiro lancamento
            $inserts[] = $this->lancamento($lancamento, $conta8211101, $lancamento->recurso_credito, 'D');
            $inserts[] = $this->lancamento($lancamento, $conta72111, $lancamento->recurso_credito, 'C');

            if (count($inserts) >= 100) {
                $this->inserir($inserts);
                $inserts = [];
            }
        }

        if (!empty($inserts)) {
            $this->inserir($inserts);
        }
    }

    private function getReduzidos($estrutural)
    {
        $dados = DB::select(<<<SQL
select c60_estrut, c61_instit, c61_codigo, c61_reduz
  from contabilidade.conplano
  join contabilidade.conplanoreduz on (c60_codcon, c60_anousu) = (c61_codcon, c61_anousu)
 where c60_anousu = $this->exercicio
   and c60_estrut like '{$estrutural}%';
SQL
        );

        $estruturais = [];
        foreach ($dados as $dado) {
            $estruturais[$dado->c61_instit] = $dado->c61_reduz;
        }

        return $estruturais;
    }

    private function lancamento($lancamento, $conta, $recurso, $natureza)
    {
        return [
            'c130_conlancam' => $lancamento->lancamento,
            'c130_orctiporec' => $recurso,
            'c130_conta' => $conta,
            'c130_anousu' => $lancamento->exercicio,
            'c130_natureza' => $natureza
        ];
    }

    private function primeiroLancamento($lancamento, $natureza)
    {
        $recurso = $natureza === 'D' ? $lancamento->recurso_debito : $lancamento->recurso_credito;
        $conta = $natureza === 'D' ? $lancamento->debito : $lancamento->credito;
        return $this->lancamento($lancamento, $conta, $recurso, $natureza);
    }

    private function inserir(array $dados)
    {
        $model = new ConlancamRecurso();
        $model->insert($dados);
    }
}
