<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29531RenomearMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
update db_itensmenu
set descricao = 'Ajuste de saldo das contas por atributo MSC',
    help = 'Ajuste de saldo das contas por atributo MSC' ,
    desctec = 'Ajuste de saldo das contas por atributo MSC'
where id_item = 229026;
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
        DB::unprepared(<<<SQL
update db_itensmenu
set descricao = 'Fix Saldo Conta Bancária',
    help = 'Fix Saldo Conta Bancária' ,
    desctec = 'Fix Saldo Conta Bancária'
where id_item = 229026;
SQL
        );
    }
}
