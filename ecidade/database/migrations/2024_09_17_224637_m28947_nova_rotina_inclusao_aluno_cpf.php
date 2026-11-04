<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28947NovaRotinaInclusaoAlunoCpf extends Migration
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
set id_item = 1100826 ,
    descricao = 'Inclusão' , 
    help = 'Inclusão de Aluno' , 
    funcao = 'edu1_verificaalunocadastrado.php' , 
    itemativo = '1' , 
    manutencao = '1' , 
    desctec = 'Inclusão de Aluno' , 
    libcliente = 'true'
where 
    id_item = 1100826;
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
set id_item = 1100826 , 
    descricao = 'Inclusão' , 
    help = 'Inclusão de Aluno' , 
    funcao = 'edu1_alunoabas001.php' , 
    itemativo = '1' , 
    manutencao = '1' , 
    desctec = 'Inclusão de Aluno' , 
    libcliente = 'true'
where 
    id_item = 1100826;
SQL
        );
    }
}
