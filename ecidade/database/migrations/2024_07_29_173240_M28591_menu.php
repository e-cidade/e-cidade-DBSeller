<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28591Menu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
        delete from db_menu where id_item = 228634 and id_item_filho = 8079;

        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo )
        values (228634, 228478,   14, 209);
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
        delete from db_menu where id_item = 228634 and id_item_filho = 8079;

        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo )
        values (228634, 228478,   14, 209);
SQL
        );
    }
}
