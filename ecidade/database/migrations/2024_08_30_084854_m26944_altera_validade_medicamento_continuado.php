<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class M26944AlteraValidadeMedicamentoContinuado extends Migration
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

    public function upEstrutura(){
DB::statement(<<<SQL
ALTER TABLE far_parametros ADD COLUMN fa02_altera_validade_medcontinuado boolean default false not null;
SQL
        );
    }

    public function upDicionario(){
DB::unprepared(<<<SQL
/** Insercao do campo fa02_altera_validade_medcontinuado  */
insert into db_syscampo values(1015667,'fa02_altera_validade_medcontinuado','bool','Parâmetro responsável pela alteração da validade inicial do medicamento na rotina do medicamento continuado','false', 'Permite alterar a validade do med. continuado',1,'f','f','f',5,'text','Altera validade medicamento continuado');
/** Exclusao do campo fa02_modelocomprovantetermica */
delete from db_sysarqcamp where codarq = 2103 and codcam = 18513;
delete from db_syscampodef where codcam = 18513;
delete from db_syscampo where codcam = 18513;
/** Trocando a ordem dos campos de acordo com a tabela */
UPDATE db_sysarqcamp set seqarq = 19 where codarq = 2103 and codcam = 1013491;
UPDATE db_sysarqcamp set seqarq = 20 where codarq = 2103 and codcam = 1014215;
UPDATE db_sysarqcamp set seqarq = 21 where codarq = 2103 and codcam = 1014216;
UPDATE db_sysarqcamp set seqarq = 22 where codarq = 2103 and codcam = 1014053;
/** Organizacao do campo fa02_altera_validade_medcontinuado  */
insert into db_sysarqcamp values(2103,1015667,23,0);
SQL
        );     

    }

    public function downEstrutura(){
DB::statement(<<<SQL
ALTER TABLE far_parametros DROP COLUMN fa02_altera_validade_medcontinuado;
SQL
        );
    }

    public function downDicionario(){
DB::unprepared(<<<SQL
delete from db_sysarqcamp where codarq = 2103 and codcam = 1015667;
delete from db_syscampo where codcam = 1015667;
insert into db_syscampo values(18513,'fa02_modelocomprovantetermica','int4','Modelo do Comprovante para impressao em impressoras termicas/nao fiscal',0, 'Modelo do Comprovante',10,'t','f','f',1,'text','Modelo do Comprovante');
insert into db_sysarqcamp values(2103,18513,19,0);
insert into db_syscampodef values (18513,0,'Não Utiliza');
UPDATE db_sysarqcamp set seqarq = 20 where codarq = 2103 and codcam = 1013491;
UPDATE db_sysarqcamp set seqarq = 21 where codarq = 2103 and codcam = 1014053;
UPDATE db_sysarqcamp set seqarq = 22 where codarq = 2103 and codcam = 1014215;
UPDATE db_sysarqcamp set seqarq = 23 where codarq = 2103 and codcam = 1014216;
SQL
        );

    }
}
