<?php

use App\Domain\Financeiro\Contabilidade\Models\ConplanoExeContaCorrente;
use App\Domain\Financeiro\Contabilidade\Models\ConplanoSistema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M27419ImplantaSaldoConplanoexecontacorrente extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop table if exists w_implantar_contas;
create table w_implantar_contas as
select nextval('conplanoexecontacorrente_id_seq') as id_,
       c61_reduz,
       c61_anousu,
       100 as sistema,
       0 as saldo,
       c60_estrut,
       case
         when SUBSTRING(c60_estrut, 1, 1)::int in (1,2,3,7)
           then 'D'
           else 'C'
       end as natureza,
       c61_codigo as id_recurso
  from conplanoreduz
  join conplano on (c60_codcon, c60_anousu) = (c61_codcon, c61_anousu)
  left join contabilidade.conplanoexecontacorrente on (c143_conplanoreduz, c143_exercicio) = (c61_reduz, c61_anousu)
 where conplanoexecontacorrente.id is null
   and c61_anousu = 2023;
SQL
        );

        /**
         * Implanta o saldo do exercício
         */
        $implantar = DB::select('select * from w_implantar_contas');
        $contaCorrente = ConplanoSistema::find(100);
        $atributo = $contaCorrente->atributos->first()->atributo;
        foreach ($implantar as $dado) {
            $saldoInicial = new ConplanoExeContaCorrente();
            $saldoInicial->c143_conplanoreduz = $dado->c61_reduz;
            $saldoInicial->c143_exercicio = $dado->c61_anousu;
            $saldoInicial->contaCorrente()->associate($contaCorrente);
            $saldoInicial->c143_saldo = $dado->saldo;
            $saldoInicial->c143_natureza = $dado->natureza;
            $saldoInicial->save();

            $saldoInicial->atributos()->create([
                'c144_conplanoinfocomplementar' => $atributo->c121_sequencial,
                'c144_valor' => $dado->id_recurso
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
