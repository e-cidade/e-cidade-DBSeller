<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23512NovosCamposEmissaoRelatorioPessoal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
insert into db_syscampo values(1014747,'nome_instituidor_pensao','varchar(100)','Nome do instituidor da pensão','', 'Nome do instituidor da pensão',100,'t','t','f',0,'text','Nome do instituidor da pensão');
insert into relrubcampos values (nextval('relrubcampos_rh120_sequencial_seq'), 'nome_instituidor_pensao', 60, 31);
insert into relrubcampos values (nextval('relrubcampos_rh120_sequencial_seq'), 'rh19_propi', 10, 10);
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
        $sql = <<<SQL
delete from relrubcampos where trim(rh120_campo) = 'nome_instituidor_pensao'; 
delete from relrubcampos where trim(rh120_campo) = 'rh19_propi';
delete from db_syscampo where codcam = 1014747;
SQL;
        DB::connection()->getPdo()->exec($sql);
        
    }
}
