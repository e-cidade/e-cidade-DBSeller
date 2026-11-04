<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23518InclusaoItemTipoAposentadoria extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upIncluiItem();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downExcluiItem();
    }

    private function upIncluiItem() 
    {
        $sql = <<<SQL
            INSERT INTO pessoal.rhtipoapos (rh88_sequencial,rh88_descricao) VALUES('0605','Pensão por morte - Plano próprio');
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    private function downExcluiItem() 
    {
        $sql = <<<SQL
             DELETE FROM pessoal.rhtipoapos WHERE rh88_sequencial = '0605';
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

}