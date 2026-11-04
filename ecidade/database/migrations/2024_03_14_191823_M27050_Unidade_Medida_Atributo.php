<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27050UnidadeMedidaAtributo extends Migration
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downDicionario();
        $this->downEstrutura();
    }

    public function upDicionario(){
DB::unprepared(<<<SQL
INSERT INTO db_syscampo VALUES (1015634,'la25_unidademedida','int4','Unidade de medida','0', 'Unidade de medida',10,'t','f','f',1,'text','Unidade de medida');
INSERT INTO db_sysarqcamp VALUES(2899,1015634,10,0);
INSERT INTO db_sysforkey VALUES(2899,1015634,1,2763,0);
SQL
        );
    }

    public function upEstrutura(){
DB::unprepared(<<<SQL
ALTER TABLE lab_atributo ADD column la25_unidademedida int;
ALTER TABLE lab_atributo ADD CONSTRAINT  fk_atributo_unidademedida FOREIGN KEY (la25_unidademedida) 
references lab_undmedida(la13_i_codigo);
SQL
        );
    }
    
    public function downDicionario(){

DB::unprepared(<<<SQL
DELETE FROM db_sysforkey WHERE codcam = 1015634;
DELETE FROM db_sysarqcamp WHERE codcam = 1015634;
DELETE FROM db_syscampo WHERE codcam = 1015634;
SQL
        );
    }  
    
    public function downEstrutura(){
DB::unprepared(<<<SQL
ALTER TABLE lab_atributo DROP column la25_unidademedida;
SQL
        );
    }      
}
