<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27889AlterandoCamposNulosCgsUnd extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
DB::unprepared(<<<SQL
update cgs_und set z01_v_cgccpf = null where z01_v_cgccpf = 'null';
update cgs_und set z01_v_ident = null where z01_v_ident = 'null';
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
        return;
    }
}
