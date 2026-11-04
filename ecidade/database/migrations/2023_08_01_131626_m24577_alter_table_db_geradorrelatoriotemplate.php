<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24577AlterTableDbGeradorrelatoriotemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            insert into db_syscampo values(1015300,'db15_estorage','int8','Código que representa um arquivo no e-Storage','0', 'Código e-Storage',10,'t','f','f',1,'text','Código e-Storage');
            insert into db_syscampo values(1015301,'db15_extensao_arquivo','varchar(10)','Indica qual a extensão do arquivo.','', 'Extensão do Arquivo',10,'f','t','f',0,'text','Extensão do Arquivo');
            insert into db_sysarqcamp values(2135,1015301,4,0);
            insert into db_sysarqcamp values(2135,1015300,5,0);

            ALTER TABLE configuracoes.db_geradorrelatoriotemplate
                ADD COLUMN db15_estorage BIGINT DEFAULT null,
                ALTER COLUMN db15_sequencial SET DEFAULT nextval('db_geradorrelatoriotemplate_db15_sequencial_seq');

            SELECT configuracoes.fc_auditoria_cria_funcao('configuracaoes.db_geradorrelatoriotemplate');
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
            delete from db_sysarqcamp where codcam in (1015300, 1015301);
            delete from db_syscampo where codcam in (1015300, 1015301);

            ALTER TABLE configuracoes.db_geradorrelatoriotemplate
                ALTER COLUMN db15_sequencial SET DEFAULT 0,
                DROP COLUMN db15_estorage;

            SELECT configuracoes.fc_auditoria_remove_funcao('configuracaoes.db_geradorrelatoriotemplate');
SQL
        );
    }
}
