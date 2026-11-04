<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M25711TabelaGrupoTaxas extends Migration
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
        -- origem do grupo de taxas
        insert into db_sysarquivo values (1011145, 'grupotaxasorigem', 'Origem do grupo de taxas', 'ar56', '2023-09-18', 'Origem grupo de taxas', 0, 'f', 'f', 'f', 'f' );
        insert into db_sysarqmod values (54,1011145);
        insert into db_syscampo values(1015448,'ar56_sequencial','int4','Sequencial grupo de taxas','0', 'Sequencial',10,'f','f','f',1,'text','Sequencial');
        insert into db_syscampo values(1015449,'ar56_descricao','varchar(40)','Descrição do grupo de taxas','', 'Descrição',40,'f','t','f',0,'text','Descrição');
        insert into db_sysarqcamp values(1011145,1015448,1,0);
        insert into db_sysarqcamp values(1011145,1015449,2,0);
        insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011145,1015448,1,1015448);

        -- grupo de taxas
        insert into db_sysarquivo values (1011146, 'grupotaxas', 'Grupo de taxas', 'ar55', '2023-09-18', 'Grupo de taxas', 0, 'f', 'f', 'f', 'f' );
        insert into db_sysarqmod values (54,1011146);
        insert into db_syscampo values(1015450,'ar55_sequencial','int4','Sequencial do grupo de taxas','0', 'Sequencial',10,'f','f','f',1,'text','Sequencial');
        insert into db_syscampo values(1015451,'ar55_descricao','varchar(50)','Descrição do grupo de taxas','', 'Descrição',50,'f','t','f',0,'text','Descrição');
        insert into db_syscampo values(1015452,'ar55_procedenciaprinc','int4','Procedência principal do grupo de taxas','0', 'Procedência principal',10,'f','f','f',1,'text','Procedência principal');
        insert into db_syscampo values(1015453,'ar55_origem','int4','Origem do grupo de taxas','0', 'Origem',10,'f','f','f',1,'text','Origem');
        insert into db_syscampo values(1015454,'ar55_datalimite','date','Data limite do grupo de taxas','null', 'Data limite',10,'f','f','f',1,'text','Data limite');
        insert into db_sysarqcamp values(1011146,1015450,1,0);
        insert into db_sysarqcamp values(1011146,1015451,2,0);
        insert into db_sysarqcamp values(1011146,1015452,3,0);
        insert into db_sysarqcamp values(1011146,1015453,4,0);
        insert into db_sysarqcamp values(1011146,1015454,5,0);
        insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011146,1015450,1,1015450);
        insert into db_sysforkey values(1011146,1015452,1,374,0);
        insert into db_sysforkey values(1011146,1015453,1,1011145,0);
SQL;

        $this->execute($sql);
    }

    public function upEstrutura()
    {
        $sql = <<<SQL
        -- origem do grupo de taxas
        create table arrecadacao.grupotaxasorigem(
            ar56_sequencial serial,
            ar56_descricao varchar(40) not null,
            constraint grupotaxasorigem_sequ_pk primary key (ar56_sequencial)
        );

        select configuracoes.fc_auditoria_cria_funcao('arrecadacao.grupotaxasorigem');

        insert into arrecadacao.grupotaxasorigem (ar56_descricao) values
            ('Todos'),
            ('CGM'),
            ('Matrícula'),
            ('Inscrição');

        -- grupo de taxas
        create table arrecadacao.grupotaxas(
            ar55_sequencial serial,
            ar55_descricao varchar(50) not null,
            ar55_procedenciaprinc integer not null,
            ar55_origem integer not null,
            ar55_datalimite date default null,
            constraint grupotaxas_sequ_pk primary key (ar55_sequencial),
            constraint grupotaxas_procdiver_fk foreign key (ar55_procedenciaprinc) references diversos.procdiver(dv09_procdiver),
            constraint grupotaxas_grupotaxasorigem_fk foreign key (ar55_origem) references arrecadacao.grupotaxasorigem(ar56_sequencial)
        );

        select configuracoes.fc_auditoria_cria_funcao('arrecadacao.grupotaxas');
SQL;

        $this->execute($sql);
    }

    public function downDicionario()
    {
        $sql = <<<SQL
        drop table arrecadacao.grupotaxas;
        drop table arrecadacao.grupotaxasorigem;
SQL;

        $this->execute($sql);
    }

    public function downEstrutura()
    {
        $sql = <<<SQL
        delete from db_sysforkey where codarq = 1011146 and codcam in (1015452,1015453);
        delete from db_sysprikey where codarq in (1011145,1011146) and codcam in (1015448,1015450);
        delete from db_sysarqcamp where codarq in (1011145,1011146) and codcam in (1015448,1015449,1015450,1015451,1015452,1015453,1015454);
        delete from db_syscampo where codcam in (1015448,1015449,1015450,1015451,1015452,1015453,1015454);
        delete from db_sysarqmod where codmod = 54 and codarq in (1011145,1011146);
        delete from db_sysarquivo where codarq in (1011145,1011146) and nomearq in ('grupotaxasorigem', 'grupotaxas');
SQL;

        $this->execute($sql);
    }

    private function execute($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
