<?php

use Illuminate\Database\Migrations\Migration;

class M24559AdicionandoCampoAtivoDistritoSanitario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDicionario();
        $this->upEstrutura();
    }

    public function down()
    {
        $this->downDicionario();
        $this->downEstrutura();
    }

    public function upDicionario(){

DB::unprepared(<<<SQL
insert into db_syscampo values(1015649,'s153_ativo','bool','Verifica se o campo da tabela sau_distritosanitario está ativo.','true', 'Ativo',1,'f','f','f',5,'text','Ativo');
insert into db_sysarqcamp values(3044,1015649,4,0);
SQL
        );
    }

    public function upEstrutura(){
DB::statement(<<<SQL
ALTER TABLE sau_distritosanitario ADD COLUMN s153_ativo boolean default true;
SQL
        );
    }

    public function downDicionario(){

DB::unprepared(<<<SQL
delete from db_sysarqcamp where codcam =  1015649;
delete from db_syscampo where codcam =  1015649;
SQL
        );
    }

    public function downEstrutura(){
DB::statement(<<<SQL
ALTER TABLE sau_distritosanitario DROP COLUMN s153_ativo;
SQL
        );
    }

}
