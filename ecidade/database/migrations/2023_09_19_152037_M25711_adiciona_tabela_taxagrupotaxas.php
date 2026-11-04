<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M25711AdicionaTabelaTaxagrupotaxas extends Migration
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

    public function upDicionario()
    {
        $sql = <<<SQL
        insert into db_sysarquivo values (1011148, 'taxagrupotaxas', 'Tabela para relacionar Taxas com Grupo de Taxas', 'ar57', '2023-09-21', 'Taxas com Grupo de Taxas', 0, 'f', 'f', 'f', 'f' );
        insert into db_sysarqmod values (54,1011148);
        insert into db_syscampo values(1015465,'ar57_sequencial','int4','Sequencial taxagrupotaxas','0', 'Sequencial',10,'f','f','f',1,'text','Sequencial');
        insert into db_syscampo values(1015466,'ar57_taxa','int4','Taxa','0', 'Taxa',10,'f','f','f',1,'text','Taxa');
        insert into db_syscampo values(1015467,'ar57_grupotaxas','int4','Grupo de Taxas','0', 'Grupo de Taxas',10,'f','f','f',1,'text','Grupo de Taxas');
        insert into db_sysarqcamp values(1011148,1015467,1,0);
        insert into db_sysarqcamp values(1011148,1015466,2,0);
        insert into db_sysarqcamp values(1011148,1015465,3,0);
        insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011148,1015465,1,1015467);
        insert into db_sysforkey values(1011148,1015466,1,1010547,0);
        insert into db_sysforkey values(1011148,1015467,1,1011146,0);
SQL;

        $this->execute($sql);
    }

    public function upEstrutura()
    {
        $sql = <<<SQL
        create table arrecadacao.taxagrupotaxas(
            ar57_sequencial serial,
            ar57_taxa integer not null,
            ar57_grupotaxas integer not null,
            constraint taxagrupotaxas_sequ_pk primary key (ar57_sequencial),
            constraint taxagrupotaxas_taxaslancadas_fk foreign key (ar57_taxa) references arrecadacao.taxaslancadas(ar44_sequencial),
            constraint taxagrupotaxas_grupotaxas_fk foreign key (ar57_grupotaxas) references arrecadacao.grupotaxas(ar55_sequencial)
        );
SQL;

        $this->execute($sql);
    }

    public function downDicionario()
    {
        $sql = <<<SQL
        delete from db_sysforkey where codarq = 1011148 and codcam = 1015466;
        delete from db_sysforkey where codarq = 1011148 and codcam = 1015467;
        delete from db_sysprikey where codarq = 1011148 and codcam = 1015465;
        delete from db_sysarqcamp where codarq = 1011148 and codcam in (1015467,1015466,1015465);
        delete from db_syscampo where codcam in (1015465,1015466,1015467) and nomecam in ('ar57_sequencial','ar57_taxa','ar57_grupotaxas');
        delete from db_sysarqmod where codmod = 54 and codarq = 1011148;
        delete from db_sysarquivo where codarq = 1011148 and nomearq = 'taxagrupotaxas';
SQL;

        $this->execute($sql);
    }

    public function downEstrutura()
    {
        $sql = <<<SQL
        drop table arrecadacao.taxagrupotaxas;
SQL;

        $this->execute($sql);
    }

    private function execute($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
