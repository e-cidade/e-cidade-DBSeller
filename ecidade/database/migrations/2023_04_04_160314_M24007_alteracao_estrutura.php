<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24007AlteracaoEstrutura extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
--
-- Dicionario de Dados
--
insert into db_syscampo values(1014974,'o41_nometribunal','varchar(50)','Nome da unidade no tribunal de contas','', 'Nome da unidade no tribunal de contas',50,'t','t','f',0,'text','Nome da unidade no tribunal de contas');
insert into db_syscampo values(1014975,'o41_codigotribunalxml','varchar(10)','Código do Tribunal para arquivos XML','', 'Código do Tribunal para arquivos XML',10,'t','t','f',0,'text','Código do Tribunal para arquivos XML');

insert into db_sysarqcamp values(757,1014974,11,0);
insert into db_sysarqcamp values(757,1014975,12,0);

--
-- Estrutura
--
alter table orcunidade add column o41_nometribunal varchar(255) default null;
alter table orcunidade add column o41_codigotribunalxml varchar(10) default null;

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
--
-- Dicionario de Dados
--
delete from db_sysarqcamp where codcam in (1014974, 1014975);
delete from db_syscampo where codcam in (1014974, 1014975);

--
-- Estrutura
--
alter table orcunidade drop column o41_nometribunal;
alter table orcunidade drop column o41_codigotribunalxml;

SQL;
        DB::connection()->getPdo()->exec($sql);}
}
