<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M21898DicionarioEstrutura extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->tabelasUp();
        $this->dicionarioUp();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->tabelasDown();
        $this->dicionarioDown();
    }

    private function tabelasUp()
    {
        DB::connection()->getPdo()->exec(<<<SQL
create table contabilidade.apropriacaodecimoferias(
    id serial primary key,
    c145_instituicao int not null,
    c145_exercicio int not null,
    c145_mes int not null,
    c145_processado boolean default false,
    created_at timestamp,
    updated_at timestamp,
    foreign key (c145_instituicao) references db_config
);

create table contabilidade.apropriacaodecimoferias_lancamentos(
    id serial primary key,
    c146_apropriacaodecimoferias_id int not null,
    c146_regimeprevidencia int not null,
    c146_conlancam int not null,
    c146_estornar boolean default false,
    created_at timestamp,
    updated_at timestamp,
    foreign key (c146_apropriacaodecimoferias_id) references apropriacaodecimoferias,
    foreign key (c146_regimeprevidencia) references pessoal.regimeprevidencia,
    foreign key (c146_conlancam) references conlancam
);

CREATE UNIQUE INDEX apropriacaodecimoferias_unico ON contabilidade.apropriacaodecimoferias(c145_instituicao,c145_exercicio,c145_mes);

select configuracoes.fc_auditoria_cria_funcao('contabilidade.apropriacaodecimoferias_lancamentos');
select configuracoes.fc_auditoria_cria_funcao('contabilidade.apropriacaodecimoferias');
SQL
        );
    }
    private function dicionarioUp()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_sysarquivo
values (1011080, 'apropriacaodecimoferias', 'Competência da Apropriação', 'c145', '2023-05-15', 'apropriacaodecimoferias', 0, 'f', 'f', 'f', 'f' ),
       (1011081, 'apropriacaodecimoferias_lancamentos', 'Lançamentos da apropriação', 'c146', '2023-05-15', 'apropriacaodecimoferias_lancamentos', 0, 'f', 'f', 'f', 'f' );

insert into db_sysarqmod
values (32,1011080),
       (32,1011081);

insert into db_syscampo
values (1015066,'c145_instituicao','int4','Vínculo com a Instituição','0', 'Instituição',10,'f','f','f',1,'text','Instituição'),
       (1015067,'c145_exercicio','int4','Exercício da competência','0', 'Exercício',10,'f','f','f',1,'text','Exercício'),
       (1015068,'c145_mes','int4','Mês da competência','0', 'Mês',10,'f','f','f',1,'text','Mês'),
       (1015069,'c145_processado','bool','Se competência foi processada.','f', 'Processado',1,'f','f','f',5,'text','Processado'),
       (1015070,'c146_apropriacaodecimoferias_id','int4','Vínculo com a apropriacao','0', 'Apropriacao',10,'f','f','f',1,'text','Apropriacao'),
       (1015071,'c146_regimeprevidencia','int4','Vínculo com Regime de Previdência','0', 'Regime de Previdência',10,'f','f','f',1,'text','Regime de Previdência'),
       (1015072,'c146_conlancam','int4','Vínculo com o lançamento','0', 'Lançamento',10,'f','f','f',1,'text','Lançamento'),
       (1015073,'c146_estornar','bool','identificador para saber quais lançamentos estornar','f', 'Estornar',1,'f','f','f',5,'text','Estornar');

insert into db_sysarqcamp
values (1011080,1011345,1,0),
       (1011080,1015066,2,0),
       (1011080,1015067,3,0),
       (1011080,1015068,4,0),
       (1011080,1015069,5,0),
       (1011080,1012583,6,0),
       (1011080,1012584,7,0),
       (1011081,1011345,1,0),
       (1011081,1015070,2,0),
       (1011081,1015071,3,0),
       (1011081,1015072,4,0),
       (1011081,1015073,5,0),
       (1011081,1012583,6,0),
       (1011081,1012584,7,0);

insert into db_sysprikey
values (1011080,1011345,1,1011345),
       (1011081,1011345,1,1011345);

insert into db_sysforkey
values (1011080,1015066,1,83,0),
       (1011081,1015070,1,1011080,0),
       (1011081,1015071,1,3659,0),
       (1011081,1015072,1,760,0);

insert into db_sysindices values(1008856,'apropriacaodecimoferias_unico',1011080,'1');

insert into db_syscadind
values (1008856,1015066,1),
       (1008856,1015067,2),
       (1008856,1015068,3);


insert into db_syssequencia
values (1001126, 'apropriacaodecimoferias_id_seq', 1, 1, 9223372036854775807, 1, 1),
       (1001127, 'apropriacaodecimoferias_lancamentos_id_seq', 1, 1, 9223372036854775807, 1, 1);

update db_sysarqcamp set codsequencia = 1001126 where codarq = 1011080 and codcam = 1011345;
update db_sysarqcamp set codsequencia = 1001127 where codarq = 1011081 and codcam = 1011345;
SQL
        );
    }

    private function tabelasDown()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop table if exists contabilidade.apropriacaodecimoferias_lancamentos;
drop table if exists contabilidade.apropriacaodecimoferias;
SQL
        );
    }
    private function dicionarioDown()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_syssequencia where codsequencia in(1001126, 1001127);
delete from db_sysprikey where codarq in(1011080, 1011081);
delete from db_sysforkey where codarq in(1011080, 1011081);
delete from db_sysarqcamp where codarq in(1011080, 1011081);
delete from db_syscadind where codind = 1008856;
delete from db_sysindices where codind = 1008856;
delete from db_syscampo where codcam in(1015066, 1015067, 1015068, 1015069, 1015070, 1015071, 1015072, 1015073);
delete from db_sysarqmod where codarq in (1011080, 1011081);
delete from db_sysarquivo where codarq in (1011080, 1011081);
SQL
        );
    }
}
