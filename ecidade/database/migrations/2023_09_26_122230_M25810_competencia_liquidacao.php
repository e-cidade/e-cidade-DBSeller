<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25810CompetenciaLiquidacao extends Migration
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

    private function upDicionario()
    {
        DB::connection()->getPdo()->exec(
            <<<SQL
           insert into db_sysarquivo values (1010839, 'empcompetencialiquidacao', 'Emp Competência Liquidação', 'e163', '2021-12-07', 'Emp Competencia Liquidação', 0, 'f', 'f', 'f', 'f' );

           insert into db_sysarqmod values (38,1010839);

           insert into db_syscampo values(1013506,'e164_sequencial','int4','Sequencial de Emp Competência Liquidação','0', 'e164_sequencial',111,'f','f','t',1,'text','e164_sequencial');

           insert into db_syscampo values(1013507,'e164_codord','int4','Código da Ordem ','0', 'e164_codord',111,'f','f','f',1,'text','e164_codord');

           insert into db_syscampo values(1013508,'e164_data','date','Data da Competência','null', 'Data da Competência',10,'f','f','f',3,'text','Data da Competência');

           insert into db_sysarqcamp values(1010839,1013506,1,0);

           insert into db_sysarqcamp values(1010839,1013507,2,0);

           insert into db_sysarqcamp values(1010839,1013508,3,0);

           insert into db_syssequencia values(1001021, 'empcompetencialiquidacao_e164_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);

           update db_sysarqcamp set codsequencia = 1001021 where codarq = 1010839 and codcam = 1013506;
SQL
        );
    }

    private function upEstrutura()
    {
        DB::connection()->getPdo()->exec(
            <<<SQL
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

        CREATE SEQUENCE IF NOT EXISTS empenho.empcompetencialiquidacao_e164_sequencial_seq
        INCREMENT 1
        MINVALUE 1
        MAXVALUE 9223372036854775807
        START 1
        CACHE 1;

        CREATE TABLE IF NOT EXISTS empenho.empcompetencialiquidacao (
        e164_sequencial int4 NOT NULL DEFAULT nextval('empenho.empcompetencialiquidacao_e164_sequencial_seq') primary key,
        e164_codord int4 NOT null,
        e164_data varchar(10) NOT null,

        constraint empcompetencialiquidacao_codord_fk FOREIGN KEY (e164_codord) REFERENCES empenho.pagordem(e50_codord));

        SELECT configuracoes.fc_auditoria_cria_funcao('empenho.empcompetencialiquidacao');

        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
SQL
        );
    }

    private function downDicionario()
    {
        DB::connection()->getPdo()->exec(
            <<<SQL
        delete from db_syssequencia where codsequencia in(1001021);

        delete from db_sysprikey where codarq = 1010839;

        delete from db_sysarqcamp where codarq = 1010839;

        delete from db_sysarqmod where codarq in(1010839);

        delete from db_sysarquivo where codarq = 1010839;

        delete from db_syscampo where codcam in(1013506, 1013507, 1013508);
SQL
        );
    }

    private function downEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
        DROP TABLE IF EXISTS empenho.empcompetencialiquidacao;

        DROP SEQUENCE IF EXISTS empenho.empcompetencialiquidacao_ec01_sequencial_seq;
SQL
        );
    }
}
