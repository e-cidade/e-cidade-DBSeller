<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23887AjustrDeParametro extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        $sql = <<<SQL


      update caiparametro set k29_fr_contapagadora = 2;





SQL;

     DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        $sql = <<<SQL


        update caiparametro set k29_fr_contapagadora = 0;
  
  
  
  
  
SQL;
  
       DB::connection()->getPdo()->exec($sql);
    }
}
