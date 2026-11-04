<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;


class M21424TabelaConfigAssentPeriodo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upEstrutura();
        $this->upDicionario();
    }

    /**
     * Estrutura da tabela
     *
     * @return void
     */
    public function upEstrutura()
    {
        $sql = <<<SQL
            CREATE TABLE recursoshumanos.configassentperiodo (
                rh512_sequencial serial primary key,
                rh512_instit int unique not null,
                rh512_filtroadicionais boolean default false,
                rh512_filtroassentdepart boolean default false,
                rh512_assentferias text default '[]'
            );

            select configuracoes.fc_auditoria_cria_funcao('recursoshumanos.configassentperiodo');
SQL;

        DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Estrutura do dicionario
     *
     * @return void
     */
    public function upDicionario()
    {
        $sql = <<<SQL
            insert into db_sysarquivo values (1011084, 'configassentperiodo', 'Configuracoes do relatorio de assentamento por periodo.', 'rh512', '2023-05-19', 'Config do relatorio de assentamento por periodo', 0, 'f', 'f', 'f', 'f' );
            insert into db_sysarqmod values (29,1011084);

            insert into db_syscampo values(1015097,'rh512_sequencial','int8','sequencial da configuracao do relatorio de assentamento por periodo','0', 'sequencial da configuracao',10,'f','f','f',1,'text','sequencial da configuracao');
            insert into db_syscampo values(1015098,'rh512_instit','int4','numero da instituicao da configuracao','0', 'numero da instituicao da configuracao',8,'f','f','f',1,'text','numero da instituicao da configuracao');
            insert into db_syscampo values(1015099,'rh512_filtroadicionais','bool','Se o relatorio libera os filtros de lotacao, selecao etc.','f', 'filtros adicionais do relatorio ',1,'f','f','f',5,'text','filtros adicionais do relatorio ');
            insert into db_syscampo values(1015141,'rh512_filtroassentdepart','bool','Filtrar assentamentos do departamento.','f', 'filtroassentdepart',1,'f','f','f',5,'text','filtroassentdepart');
            insert into db_syscampo values(1015142,'rh512_assentferias','text','json com assentamentos de ferias','', 'assentferias',255,'f','f','f',0,'text','assentferias');

            insert into db_sysarqcamp values(1011084,1015097,1,0);
            insert into db_sysarqcamp values(1011084,1015098,2,0);
            insert into db_sysarqcamp values(1011084,1015099,3,0);
            insert into db_sysarqcamp values(1011084,1015142,4,0);
            insert into db_sysarqcamp values(1011084,1015141,5,0);

            insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011084,1015097,1,1015097);

            insert into db_syssequencia values(1001125, 'configassentperiodo_rh512_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
            update db_sysarqcamp set codsequencia = 1001125 where codarq = 1011084 and codcam = 1015097;
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
        $this->downEstrutura();
        $this->downDicionario();
    }

    /**
     * Reverse estrutura da tabela
     *
     * @return void
     */
    public function downEstrutura()
    {
        DB::statement("DROP TABLE recursoshumanos.configassentperiodo");
    }

    /**
     * Reverse estrutura do dicionario
     *
     * @return void
     */
    public function downDicionario()
    {
        $sql = <<<SQL
            delete from db_syssequencia where codsequencia = 1001125;
            delete from db_sysprikey where codarq = 1011084;

            delete from db_sysarqcamp where codarq = 1011084 and codcam between 1015097 and 1015099;
            delete from db_sysarqcamp where codarq = 1011084 and codcam between 1015141 and 1015142;

            delete from db_syscampo where codcam between 1015097 and 1015099;
            delete from db_syscampo where codcam between 1015141 and 1015142;

            delete from db_sysarqmod where codarq = 1011084;
            delete from db_sysarquivo where codarq = 1011084;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
