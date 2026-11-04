<?php

use Illuminate\Database\Migrations\Migration;

class M27220 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
update db_itensmenu
   set libcliente = 'false'
 where id_item in (3387, 3389, 3388);

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
        DB::connection()->getPdo()->exec(<<<SQL
update db_itensmenu
   set libcliente = 'true'
 where id_item in (3387, 3389, 3388);

SQL
        );
    }
}
