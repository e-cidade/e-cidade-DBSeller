<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M25522AdicionarTabelaSubtaxas extends Migration
{
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

    private function upDicionario()
    {
        $sql = <<<SQL
        insert into db_sysarquivo values (1011133, 'subtaxas', 'Subtaxas', 'ar54', '2023-09-05', 'Subtaxas', 0, 'f', 'f', 'f', 'f' );
        insert into db_sysarqmod values (54,1011133);

        insert into db_syscampo values(1015370,'ar54_sequencial','int4','Sequencial subtaxa','0', 'Sequencial',10,'f','f','f',1,'text','Sequencial');
        insert into db_syscampo values(1015371,'ar54_taxa','int4','Taxa principal','0', 'Taxa',10,'f','f','f',1,'text','Taxa');
        insert into db_syscampo values(1015372,'ar54_subtaxa','int4','Subtaxa','0', 'Subtaxa',10,'f','f','f',1,'text','Subtaxa');

        insert into db_sysarqcamp values(1011133,1015372,1,0);
        insert into db_sysarqcamp values(1011133,1015371,2,0);
        insert into db_sysarqcamp values(1011133,1015370,3,0);

        insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011133,1015370,1,1015370);

        insert into db_sysforkey values(1011133,1015371,1,1010547,0);
        insert into db_sysforkey values(1011133,1015372,1,1010547,0);
SQL;

        $this->execute($sql);
    }

    private function upEstrutura()
    {
        $sql = <<<SQL
        create table arrecadacao.subtaxas(
            ar54_sequencial serial,
            ar54_taxa integer,
            ar54_subtaxa integer,
            constraint subtaxas_sequ_pk primary key (ar54_sequencial),
            constraint subtaxas_taxa_fk FOREIGN KEY (ar54_taxa) REFERENCES arrecadacao.taxaslancadas(ar44_sequencial),
            constraint subtaxas_subtaxa_fk FOREIGN KEY (ar54_subtaxa) REFERENCES arrecadacao.taxaslancadas(ar44_sequencial)
        );
SQL;

        $this->execute($sql);
    }

    private function downDicionario()
    {
        $sql = <<<SQL
        delete from db_acount where codarq = 1011133;
        delete from db_sysforkey where codarq = 1011133 and codcam in (1015371, 1015372) and sequen = 1;
        delete from db_sysprikey where codarq = 1011133 and codcam = 1015370 and sequen = 1;
        delete from db_sysarqcamp where codarq = 1011133 and codcam in (1015370,1015371,1015372);
        delete from db_syscampo where codcam in (1015370,1015371,1015372);
        delete from db_sysarqmod where codmod = 54 and codarq = 1011133;
        delete from db_sysarquivo where codarq = 1011133 and nomearq = 'subtaxas';
SQL;

        $this->execute($sql);
    }

    private function downEstrutura()
    {
        $sql = <<<SQL
        drop table arrecadacao.subtaxas;
SQL;

        $this->execute($sql);
    }

    private function execute($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
