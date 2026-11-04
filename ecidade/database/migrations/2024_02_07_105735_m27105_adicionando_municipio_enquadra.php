<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27105AdicionandoMunicipioEnquadra extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    DB::connection()->getPdo()->exec(<<<SQL
        update avaliacaoperguntaopcao set db104_sequencial = 3003676 , db104_avaliacaopergunta = 3000881 , db104_descricao = 'Empresa enquadrada nos critérios da legislação vigente' , db104_identificador = 'empresa-enquadrada-nos-art-7o-a-9o-d5a2ac5a41cfe0' , db104_aceitatexto = 'false' , db104_peso = 0 , db104_valorresposta = '1' , db104_identificadorcampo = 'indDesFolha_1' where db104_sequencial = 3003676;
        insert into avaliacaoperguntaopcao( db104_sequencial ,db104_avaliacaopergunta ,db104_descricao ,db104_identificador ,db104_aceitatexto ,db104_peso ,db104_valorresposta ,db104_identificadorcampo ) values ( 4001511 ,3000881 ,'Município enquadrado nos criterios da legislação vigente' ,'municipio-enquadrada-8769' ,'false' ,0 ,'2' ,'indDesFolha_2' );
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
        update avaliacaoperguntaopcao set db104_sequencial = 3003676 , db104_avaliacaopergunta = 3000881 , db104_descricao = 'Empresa enquadrada nos critérios da legislação vigente' , db104_identificador = 'empresa-enquadrada-nos-art-7o-a-9o-d5a2ac5a41cfe0' , db104_aceitatexto = 'false' , db104_peso = 0 , db104_valorresposta = '1' , db104_identificadorcampo = 'indDesFolha_1' where db104_sequencial = 3003676;
        delete from avaliacaoperguntaopcao where db104_sequencial = 4001511 and db104_avaliacaopergunta =  3000881;
SQL
    );
    }
}
