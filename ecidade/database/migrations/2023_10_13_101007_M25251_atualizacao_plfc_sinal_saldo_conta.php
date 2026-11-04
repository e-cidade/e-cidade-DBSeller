<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25251AtualizacaoPlfcSinalSaldoConta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop function if exists contabilidade.fc_sinal_saldo_conta(int, numeric);

create or replace function contabilidade.fc_sinal_saldo_conta(classe int, valor numeric)
    returns char
    language plpgsql
as $$
begin
    -- a natureza das contas de classe 1,3,5,7 e D.
    if classe in (1,3,5,7) then
        if ( valor < 0) then
            return 'C';
        else return 'D';
        end if;
    end if;

    -- a natureza das contas de classe 2,4,6,8 e C.
    if classe in (2,4,6,8) then
        if ( valor > 0) then
            return 'D';
        else return 'C';
        end if;
    end if;

end;
$$;
SQL
        );
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
