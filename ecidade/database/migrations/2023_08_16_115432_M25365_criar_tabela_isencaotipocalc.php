<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M25365CriarTabelaIsencaotipocalc extends Migration
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
        insert into db_sysarquivo values (1011129, 'isencaocalc', 'Tabela para relacionar cálculos permitidos que podem ser isentos por um tipo de isenção.', 'v46', '2023-08-16', 'Cálculos tipo de isenção', 0, 'f', 'f', 'f', 'f' );
        insert into db_sysarqmod values (46,1011129);

        insert into db_syscampo values(1015318,'v46_sequencial','int4','Sequencial isencaocalc','0', 'Sequencial',8,'f','f','f',1,'text','Sequencial');
        update db_syscampo set nomecam = 'v46_sequencial', conteudo = 'int4', descricao = 'Sequencial isencaocalc', valorinicial = '0', rotulo = 'Sequencial', nulo = 'f', tamanho = 8, maiusculo = 'f', autocompl = 'f', aceitatipo = 0, tipoobj = 'text', rotulorel = 'Sequencial' where codcam = 1015318;
        insert into db_syscampo values(1015319,'v46_isencao','int4','Isenção','0', 'Isenção',8,'f','f','f',1,'text','Isenção');
        insert into db_syscampo values(1015320,'v46_cadcalc','int4','Cálculo','0', 'Cálculo',8,'f','f','f',1,'text','Cálculo');
        insert into db_syscampo values(1015321,'v46_percentual','float4','Percentual de isenção','0', 'Percentual',7,'f','f','f',4,'text','Percentual');

        insert into db_sysarqcamp values(1011129,1015318,1,0);
        insert into db_sysarqcamp values(1011129,1015319,2,0);
        insert into db_sysarqcamp values(1011129,1015320,3,0);
        insert into db_sysarqcamp values(1011129,1015321,4,0);

        insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011129,1015318,1,1015318);
        insert into db_sysforkey values(1011129,1015319,1,1709,0);
        insert into db_sysforkey values(1011129,1015320,1,51,0);
SQL;

        $this->executeQuery($sql);
    }

    private function upEstrutura()
    {
        $sql = <<<SQL
        create table tributario.isencaocalc(
            v46_sequencial serial,
            v46_isencao integer not null,
            v46_cadcalc integer not null,
            v46_percentual numeric not null,
            constraint isencaocalc_sequencial_pk PRIMARY KEY (v46_sequencial),
            constraint isencaocalc_isencao_fk foreign key (v46_isencao) references tributario.isencao,
            constraint isencaocalc_cadcalc_fk foreign key (v46_cadcalc) references issqn.cadcalc
        );
SQL;

        $this->executeQuery($sql);
    }

    private function downDicionario()
    {
        $sql = <<<SQL
        delete from db_sysprikey where codarq = 1011129;
        delete from db_sysforkey where codarq = 1011129;
        delete from db_sysarqcamp where codarq = 1011129 and codcam in (1015318,1015319,1015320,1015321);
        delete from db_syscampo where codcam in (1015318,1015319,1015320,1015321) and nomecam in ('v46_sequencial','v46_isencao','v46_cadcalc','v46_percentual');
        delete from db_sysarqmod where codmod = 46 and codarq = 1011129;
        delete from db_acount where codarq = 1011129;
        delete from db_sysarquivo where codarq = 1011129 and nomearq = 'isencaocalc';
SQL;

        $this->executeQuery($sql);
}

    private function downEstrutura()
    {
        $sql = <<<SQL
        drop table if exists tributario.isencaocalc;
SQL;

        $this->executeQuery($sql);
    }

    private function executeQuery($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
