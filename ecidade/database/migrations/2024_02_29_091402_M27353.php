<?php

use Illuminate\Database\Migrations\Migration;

class M27353 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(<<<SQL
update db_itensmenu set descricao = 'Rotinas administrativas' ,
                        help = 'Rotinas administrativas' ,
                        desctec = 'Rotinas administrativas.'
                    where id_item = 9954;
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
