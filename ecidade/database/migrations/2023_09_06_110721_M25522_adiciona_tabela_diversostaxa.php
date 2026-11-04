<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M25522AdicionaTabelaDiversostaxa extends Migration
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
        insert into db_sysarquivo values (1011135, 'diversostaxa', 'Taxas vinculadas ao registro diversos', '', '2023-09-06', 'Diversos Taxa', 0, 'f', 'f', 'f', 'f' );
        insert into db_sysarqmod values (27,1011135);

        insert into db_syscampo values(1015376,'dv15_sequencial','int4','Sequencial diversos taxa','0', 'Sequencial',10,'f','f','f',1,'text','Sequencial');
        insert into db_syscampo values(1015377,'dv15_coddiver','int4','Código Diversos','0', 'Código Diversos',10,'f','f','f',1,'text','Código Diversos');
        insert into db_syscampo values(1015378,'dv15_codtaxa','int4','Código Taxa','0', 'Código Taxa',10,'f','f','f',1,'text','Código Taxa');
        insert into db_syscampo values(1015379,'dv15_valor','float4','Valor da taxa','0', 'Valor',10,'f','f','f',4,'text','Valor');
        insert into db_syscampo values(1015382,'dv15_taxaprincipal','bool','Taxa principal','f', 'Taxa principal',1,'f','f','f',5,'text','Taxa principal');

        insert into db_sysarqcamp values(1011135,1015376,1,0);
        insert into db_sysarqcamp values(1011135,1015377,2,0);
        insert into db_sysarqcamp values(1011135,1015378,3,0);
        insert into db_sysarqcamp values(1011135,1015379,4,0);
        insert into db_sysarqcamp values(1011135,1015382,5,0);

        insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011135,1015376,1,1015376);

        insert into db_sysforkey values(1011135,1015378,1,1010547,0);
        insert into db_sysforkey values(1011135,1015377,1,372,0);

SQL;

        $this->execute($sql);
    }

    private function upEstrutura()
    {
        $sql = <<<SQL
        create table diversos.diversostaxa(
            dv15_sequencial serial,
            dv15_coddiver integer,
            dv15_codtaxa integer,
            dv15_valor float,
            dv15_taxaprincipal boolean,
            constraint diversostaxa_sequ_pk primary key (dv15_sequencial),
            constraint diversostaxa_diversos_fk FOREIGN KEY (dv15_coddiver) REFERENCES diversos.diversos(dv05_coddiver),
            constraint diversostaxa_taxaslancadas_fk FOREIGN KEY (dv15_codtaxa) REFERENCES arrecadacao.taxaslancadas(ar44_sequencial)
        );
SQL;

        $this->execute($sql);
    }

    private function downDicionario()
    {
        $sql = <<<SQL
        delete from db_acount where codarq = 1011135;
        delete from db_sysforkey where codarq = 1011135 and codcam in (1015378, 1015377) and sequen = 1;
        delete from db_sysprikey where codarq = 1011135 and codcam = 1015376 and sequen = 1;
        delete from db_sysarqcamp where codarq = 1011135 and codcam in (1015376,1015377,1015378,1015379,1015382);
        delete from db_syscampo where codcam in (1015376,1015377,1015378,1015379,1015382);
        delete from db_sysarqmod where codmod = 27 and codarq = 1011135;
        delete from db_sysarquivo where codarq = 1011135 and nomearq = 'diversostaxa';
SQL;

        $this->execute($sql);
    }

    private function downEstrutura()
    {
        $sql = <<<SQL
        drop table diversos.diversostaxa;
SQL;

        $this->execute($sql);
    }

    private function execute($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
