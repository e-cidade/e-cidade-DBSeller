<?php

use Illuminate\Database\Migrations\Migration;

class M24587AdicionandoCampoAtivoAcaoProgramatica extends Migration
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
insert into db_syscampo values(1015648,'fa12_ativo','bool','Verifica se o campo da tabela far_programa está ativo.','true', 'Ativo',1,'f','f','f',5,'text','Ativo');
insert into db_sysarqcamp values(2177,1015648,5,0);
SQL
        );
    }

    public function upEstrutura(){
DB::statement(<<<SQL
ALTER TABLE far_programa ADD COLUMN fa12_ativo boolean default true;
SQL
        );
    }

    public function downDicionario(){

DB::unprepared(<<<SQL
delete from db_sysarqcamp where codcam =  1015648;
delete from db_syscampo where codcam =  1015648;
SQL
        );
    }

    public function downEstrutura(){
DB::statement(<<<SQL
ALTER TABLE far_programa DROP COLUMN fa12_ativo;
SQL
        );
    }
}
