<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28573ReordenarMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
update db_itensmenu set libcliente = 'false' where id_item = 228633;
update db_itensmenu set libcliente = 'false' where id_item = 228639;

delete from db_menu where id_item = 228634;

insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo )
values (228634, 228810, 1, 209),
       (228634, 9359,   2, 209),
       (228634, 228598, 3, 209),
       (228634, 228475, 4, 209),
       (228634, 8070,   5, 209),
       (228634, 228635, 6, 209),
       (228634, 8065,   7, 209),
       (228634, 228476, 8, 209),
       (228634, 8703,   9, 209),
       (228634, 8699,   10, 209),
       (228634, 8704,   11, 209),
       (228634, 228869, 12, 209),
       (228634, 8067,   13, 209),
       (228634, 8079,   14, 209);

delete from db_menu where id_item = 228640;
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo )
values (228640, 228602, 1, 209),
       (228640, 228641, 2, 209),
       (228640, 8121,   3, 209),
       (228640, 8124,   4, 209),
       (228640, 228643, 5, 209),
       (228640, 10077,  6, 209);
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
update db_itensmenu set  libcliente = 'true' where id_item = 228633;
update db_itensmenu set libcliente = 'true' where id_item = 228639;

delete from db_menu where id_item = 228634;

insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo )
values (228634, 228598,  3, 209),
       (228634, 228475,  4, 209),
       (228634, 228635,  6, 209),
       (228634, 228476,  8, 209),
       (228634, 228478, 14, 209),
       (228634, 228810,  1, 209),
       (228634, 228869, 12, 209);

delete from db_menu where id_item = 228640;
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo )
values (228640,   8121, 4, 209),
       (228640, 228643, 3, 209),
       (228640, 228641, 2, 209),
       (228640, 228602, 1, 209);
SQL
        );
    }
}
