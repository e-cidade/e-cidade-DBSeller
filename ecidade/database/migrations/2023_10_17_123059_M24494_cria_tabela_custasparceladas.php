<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M24494CriaTabelaCustasparceladas extends Migration
{
    public function up()
    {
        $this->upDicionario();
        $this->upEstrututra();
    }

    public function down()
    {
        $this->downDicionario();
        $this->downEstrututra();
    }

    private function upDicionario()
    {
        $sql = <<<SQL
        insert into db_sysarquivo values (1011150, 'custasparceladas', 'Demonstrativo de custas parceladas', 'ar58', '2023-10-17', 'Custas Parceladas', 0, 'f', 'f', 'f', 'f' );
        insert into db_sysarqmod values (54,1011150);
        insert into db_syscampo values(1015489,'ar58_sequencial','int4','Sequencial custas parceladas','0', 'Sequencial',8,'f','f','f',1,'text','Sequencial');
        insert into db_syscampo values(1015490,'ar58_processoforo','int4','Processo do foro','0', 'Processo do foro',8,'f','f','f',1,'text','Processo do foro');
        insert into db_syscampo values(1015491,'ar58_parcelamento','int4','Termo de parcelamento','0', 'Parcelamento',8,'f','f','f',1,'text','Parcelamento');
        insert into db_syscampo values(1015492,'ar58_taxa','int4','Taxa','0', 'Taxa',8,'f','f','f',1,'text','Taxa');
        insert into db_syscampo values(1015493,'ar58_valor','float4','Valor da custa','0', 'Valor',10,'f','f','f',4,'text','Valor');
        insert into db_sysarqcamp values(1011150,1015489,1,0);
        insert into db_sysarqcamp values(1011150,1015490,2,0);
        insert into db_sysarqcamp values(1011150,1015491,3,0);
        insert into db_sysarqcamp values(1011150,1015492,4,0);
        insert into db_sysarqcamp values(1011150,1015493,5,0);
        insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011150,1015489,1,1015489);
        insert into db_sysforkey values(1011150,1015490,1,3069,0);
        insert into db_sysforkey values(1011150,1015491,1,103,0);
        insert into db_sysforkey values(1011150,1015492,1,3221,0);
SQL;
        $this->executeQuery($sql);
    }

    private function upEstrututra()
    {
        $sql = <<<SQL
        create table arrecadacao.custasparceladas(
            ar58_sequencial serial,
            ar58_processoforo integer,
            ar58_parcelamento integer,
            ar58_taxa integer,
            ar58_valor float,
            CONSTRAINT custasparceladas_seqn_pk PRIMARY KEY (ar58_sequencial),
            CONSTRAINT custasparceladas_processoforo_fk FOREIGN KEY (ar58_processoforo) REFERENCES processoforo,
            CONSTRAINT custasparceladas_termo_fk FOREIGN KEY (ar58_parcelamento) REFERENCES termo,
            CONSTRAINT custasparceladas_taxa_fk FOREIGN KEY (ar58_taxa) REFERENCES taxa
        );
SQL;
        $this->executeQuery($sql);
    }

    private function downDicionario()
    {
        $sql = <<<SQL
        delete from db_sysprikey where codarq = 1011150 and codcam = 1015489;
        delete from db_sysforkey where codarq = 1011150 and codcam in (1015490,1015491,1015492);
        delete from db_sysarqcamp where codarq = 1011150 and codcam in (1015489,1015490,1015491,1015492,1015493);
        delete from db_syscampo where codcam in (1015489,1015490,1015491,1015492,1015493) and nomecam in ('ar58_sequencial','ar58_processoforo','ar58_parcelamento','ar58_taxa','ar58_valor');
        delete from db_sysarqmod where codmod = 54 and codarq = 1011150;
        delete from db_sysarquivo where codarq = 1011150 and nomearq = 'custasparceladas';
SQL;
        $this->executeQuery($sql);
    }

    private function downEstrututra()
    {
        $sql = <<<SQL
        drop table arrecadacao.custasparceladas;
SQL;
        $this->executeQuery($sql);
    }

    private function executeQuery($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
