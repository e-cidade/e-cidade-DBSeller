<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M21485FiltroContasDepartamento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL

insert into db_syscampo values(1014411,'k29_filtrocontasdepartamento','bool','Filtrar contas por departamento','f', 'Filtrar contas por departamento',1,'f','f','f',5,'text','Filtra contas por departamento');
insert into db_syscampodef values(1014411,'t','SIM');
insert into db_syscampodef values(1014411,'f','NÃO');
insert into db_sysarqcamp values(1503,1014411,14,0);

insert into db_sysarquivo values (1010976, 'saltesdepartamento', 'Armazena os departamentos ligados a conta da tesouraria', 'k212', '2022-07-27', 'Departamentos das contas da tesouraria', 0, 'f', 't', 't', 't' );
insert into db_sysarqmod values (5,1010976);
insert into db_syscampo values(1014412,'k212_sequencial','int4','Sequencial','0', 'Sequencial',10,'f','f','f',1,'text','Sequencial');
insert into db_syscampo values(1014413,'k212_saltes','int4','Conta da Tesouraria','0', 'Conta',10,'f','f','f',1,'text','Conta');
insert into db_syscampo values(1014414,'k212_departamento','int4','Departamento','0', 'Departamento',10,'f','f','f',1,'text','Departamento');
insert into db_syscampo values(1014415,'k212_principal','bool','Departamento Principal','f', 'Departamento Principal',1,'f','f','f',5,'text','Departamento Principal');
insert into db_syscampodef values(1014415,'t','SIM');
insert into db_syscampodef values(1014415,'f','NÃO');

insert into db_sysarqcamp values(1010976,1014412,1,0);
insert into db_sysarqcamp values(1010976,1014413,2,0);
insert into db_sysarqcamp values(1010976,1014414,3,0);
insert into db_sysarqcamp values(1010976,1014415,4,0);

insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1010976,1014412,1,1014413);

insert into db_sysforkey values(1010976,1014413,1,212,0);
insert into db_sysforkey values(1010976,1014414,1,154,0);

insert into db_sysindices values(1008804,'saltesdepartamento_saltes_departamento_unique_in',1010976,'1');
insert into db_syscadind values(1008804,1014413,1);
insert into db_syscadind values(1008804,1014414,2);

insert into db_syssequencia values(1001085, 'saltesdepartamento_k212_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
update db_sysarqcamp set codsequencia = 1001085 where codarq = 1010976 and codcam = 1014412;

alter table caiparametro add column k29_filtrocontasdepartamento bool not null default false;

create table caixa.saltesdepartamento (k212_sequencial integer not null primary key,
                                       k212_saltes integer not null,
                                       k212_departamento integer not null,
                                       k212_principal bool not null default false);

alter table caixa.saltesdepartamento add constraint "saltesdepartamento_k212_saltes_fk" FOREIGN KEY (k212_saltes) REFERENCES caixa.saltes(k13_conta);
alter table caixa.saltesdepartamento add constraint "saltesdepartamento_k212_departamento_fk" FOREIGN KEY (k212_departamento) REFERENCES configuracoes.db_depart(coddepto);
create unique index saltesdepartamento_saltes_departamento_unique_in on caixa.saltesdepartamento(k212_saltes,k212_departamento);

create sequence saltesdepartamento_k212_sequencial_seq;

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

delete from db_sysarqcamp where codcam = 1014411;
delete from db_syscampodef where codcam = 1014411;
delete from db_syscampo where codcam = 1014411;

delete from db_syscadind where codind = 1008804;
delete from db_sysindices where codind = 1008804;
delete from db_syssequencia where codsequencia = 1001085;
delete from db_sysforkey where codarq = 1010976;
delete from db_sysprikey where codarq = 1010976;
delete from db_sysarqcamp where codarq = 1010976;
delete from db_syscampodef where codcam = 1014415;
delete from db_syscampo where codcam in (1014412,1014413,1014414,1014415);
delete from db_sysarqmod where codarq = 1010976;
delete from db_sysarquivo where codarq = 1010976;

alter table caiparametro drop column k29_filtrocontasdepartamento;

drop sequence saltesdepartamento_k212_sequencial_seq;
drop table caixa.saltesdepartamento;

SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
