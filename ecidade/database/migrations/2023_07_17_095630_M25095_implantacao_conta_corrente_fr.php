<?php

use App\Domain\Financeiro\Contabilidade\Models\ConplanoExeContaCorrente;
use App\Domain\Financeiro\Contabilidade\Models\ConplanoSistema;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25095ImplantacaoContaCorrenteFr extends Migration
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
  select c60_anousu, c60_codcon, c60_estrut,
         case when substring(c60_estrut,1 ,1)::int in (1,3,5,7)
           then 'D'
           else 'C'
         end natureza,
         c61_reduz,
         c61_codigo,
         c62_vlrcre,
         c62_vlrdeb
    from conplano
    join conplanoreduz on (c61_codcon, c61_anousu) = (c60_codcon, c60_anousu)
    left join conplanoexe on (c62_anousu, c62_reduz) = (c61_anousu, c61_reduz)
    left join conplanoatributos on (c120_conplano, c120_anousu) = (c60_codcon, c60_anousu)
              and c120_conplanosistema = 100
  where c60_anousu = 2023
    and c120_sequencial is null;

insert into conplanoatributos (c120_anousu, c120_conplano, c120_infocomplementar, c120_conplanosistema)
 select c60_anousu, c60_codcon, 100, 100
   from w_implantar_contas;
SQL
        );


        /**
         * Implanta o saldo do exercício
         */
        $implantar = DB::select('select * from w_implantar_contas');
        $contaCorrente = ConplanoSistema::find(100);
        $atributo = $contaCorrente->atributos->first()->atributo;
        foreach ($implantar as $dado) {
            $natureza = $dado->natureza;
            $valor = 0;

            if ($dado->c62_vlrcre > 0 || $dado->c62_vlrdeb > 0) {
                if ($dado->c62_vlrcre > 0) {
                    $valor = $dado->c62_vlrcre;
                    $natureza = 'C';
                }
                if ($dado->c62_vlrdeb > 0) {
                    $valor = $dado->c62_vlrdeb;
                    $natureza = 'D';
                }
            }

            $saldoInicial = new ConplanoExeContaCorrente();
            $saldoInicial->c143_conplanoreduz = $dado->c61_reduz;
            $saldoInicial->c143_exercicio = $dado->c60_anousu;
            $saldoInicial->contaCorrente()->associate($contaCorrente);
            $saldoInicial->c143_saldo = $valor;
            $saldoInicial->c143_natureza = $natureza;
            $saldoInicial->save();

            $saldoInicial->atributos()->create([
                'c144_conplanoinfocomplementar' => $atributo->c121_sequencial,
                'c144_valor' => $dado->c61_codigo
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
        DB::connection()->getPdo()->exec(<<<SQL
delete from contabilidade.conplanoexecontacorrente
 using w_implantar_contas
 where c143_conplanoreduz = c61_reduz and c143_exercicio = c60_anousu;

delete from contabilidade.conplanoatributos
using w_implantar_contas
where c120_anousu = c60_anousu and c120_conplano = c60_anousu;
SQL
        );
    }
}
