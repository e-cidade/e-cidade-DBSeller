<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23389NecessidadeEspecialAluno extends Migration
{
    
    public function UpDD() {
        
        $sql = <<<SQL
--
--necessidadesubdivisao
--
insert into db_sysarquivo values (1011023, 'necessidadesubdivisao', 'Subdivisão da necessidade especial', 'ed185', '2023-02-14', 'Subdivisão da necessidade especial', 0, 'f', 't', 't', 't' );
insert into db_sysarqmod values (1008004,1011023);
insert into db_syscampo values(1014748,'ed185_sequencial','int4','Sequencial','0', 'Sequencial',10,'f','f','f',1,'text','Sequencial');
insert into db_syscampo values(1014749,'ed185_necessidade','int4','Necessidade','0', 'Necessidade',10,'f','f','f',1,'text','Necessidade');
insert into db_syscampo values(1014750,'ed185_descricao','varchar(100)','Descrição','', 'Descrição',100,'f','t','f',0,'text','Descrição');
insert into db_sysarqcamp values(1011023,1014748,1,0);
insert into db_sysarqcamp values(1011023,1014749,2,0);
insert into db_sysarqcamp values(1011023,1014750,3,0);

insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011023,1014748,1,1014750);

insert into db_sysforkey values(1011023,1014749,1,1010050,0);

insert into db_sysindices values(1008846,'necessidadesubdivisao_ed185_necessidade_in',1011023,'0');
insert into db_syscadind values(1008846,1014749,1);
--
--
--


--
--necessidadetipoatendimento
--
insert into db_sysarquivo values (1011024, 'necessidadetipoatendimento', 'Tipo de atendimento da necessidade especial', 'ed186', '2023-02-14', 'Tipo de atendimento da necessidade especial', 0, 'f', 't', 't', 't' );
insert into db_sysarqmod values (1008004,1011024);
insert into db_syscampo values(1014751,'ed186_sequencial','int4','Sequencial','0', 'Sequencial',10,'f','f','f',1,'text','Sequencial');
insert into db_syscampo values(1014752,'ed186_descricao','varchar(100)','Descrição','', 'Descrição',100,'f','t','f',0,'text','Descrição');
insert into db_sysarqcamp values(1011024,1014751,1,0);
insert into db_sysarqcamp values(1011024,1014752,2,0);

insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011024,1014751,1,1014752);

insert into db_sysindices values(1008842,'necessidadetipoatendimento_ed186_descricao_in',1011024,'1');
insert into db_syscadind values(1008842,1014752,1);
--
--
--


--
-- necessidadesubdivisaoaluno
--
insert into db_sysarquivo values (1011025, 'necessidadesubdivisaoaluno', 'Subdivisão da necessidade do aluno', 'ed187', '2023-02-14', 'Subdivisão da necessidade do aluno', 0, 'f', 't', 't', 't' );
insert into db_sysarqmod values (1008004,1011025);
insert into db_syscampo values(1014753,'ed187_sequencial','int4','Sequencial','0', 'Sequencial',10,'f','f','f',1,'text','Sequencial');
insert into db_syscampo values(1014754,'ed187_necessidadesubdivisao','int4','Subdivisão','0', 'Subdivisão',10,'f','f','f',1,'text','Subdivisão');
insert into db_syscampo values(1014755,'ed187_aluno','int4','Aluno','0', 'Aluno',10,'f','f','f',1,'text','Aluno');
insert into db_sysarqcamp values(1011025,1014753,1,0);
insert into db_sysarqcamp values(1011025,1014754,2,0);
insert into db_sysarqcamp values(1011025,1014755,3,0);

insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011025,1014753,1,1014755);

insert into db_sysforkey values(1011025,1014754,1,1011023,0);

insert into db_sysforkey values(1011025,1014755,1,1010051,0);

insert into db_sysindices values(1008847,'necessidadesubdivisaoaluno_ed187_aluno_in',1011025,'0');
insert into db_syscadind values(1008847,1014755,1);

insert into db_sysindices values(1008848,'necessidadesubdivisaoaluno_ed187_necessidadesubdivisao_in',1011025,'0');
insert into db_syscadind values(1008848,1014754,1);


--
--
--


--
-- necessidadetipoatendimentoaluno
--
insert into db_sysarquivo values (1011026, 'necessidadetipoatendimentoaluno', 'Topo atendimento necessidade aluno', 'ed188', '2023-02-14', 'Topo atendimento necessidade aluno', 0, 'f', 't', 't', 't' );
insert into db_sysarqmod values (1008004,1011026);
insert into db_syscampo values(1014756,'ed188_sequencial','int4','Sequencial','0', 'Sequencial',10,'f','f','f',1,'text','Sequencial');
insert into db_syscampo values(1014757,'ed188_necessidadetipoatendimento','int4','Tipo de Atendimento','0', 'Tipo de Atendimento',10,'f','f','f',1,'text','Tipo de Atendimento');
insert into db_syscampo values(1014758,'ed188_aluno','int4','Aluno','0', 'Aluno',10,'f','f','f',1,'text','Aluno');
insert into db_sysarqcamp values(1011026,1014756,1,0);
insert into db_sysarqcamp values(1011026,1014758,2,0);
insert into db_sysarqcamp values(1011026,1014757,3,0);

insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011026,1014756,1,1014758);

insert into db_sysindices values(1008843,'necessidadetipoatendimentoaluno_ed188_aluno_in',1011026,'0');
insert into db_syscadind values(1008843,1014758,1);

insert into db_sysindices values(1008844,'necessidadetipoatendimentoaluno_ed188_necessidadetipoatendimento_in',1011026,'0');
insert into db_syscadind values(1008844,1014757,1);

insert into db_sysindices values(1008845,'necessidadetipoatendimentoaluno_unique_in',1011026,'1');
insert into db_syscadind values(1008845,1014758,1);
insert into db_syscadind values(1008845,1014757,2);

insert into db_sysforkey values(1011026,1014758,1,1010051,0);
insert into db_sysforkey values(1011026,1014757,1,1011024,0);
--
--
--


--
--necessidadealunocadeirante
--
insert into db_sysarquivo values (1011027, 'necessidadealunocadeirante', 'Necessidade aluno cadeirante', 'ed189', '2023-02-14', 'Necessidade aluno cadeirante', 0, 'f', 't', 't', 't' );
insert into db_sysarqmod values (1008004,1011027);
insert into db_syscampo values(1014759,'ed189_sequencial','int4','Sequencial','0', 'Sequencial',10,'f','f','f',1,'text','Sequencial');
insert into db_syscampo values(1014760,'ed189_aluno','int4','Aluno','0', 'Aluno',10,'f','f','f',1,'text','Aluno');
insert into db_syscampo values(1014761,'ed189_necessidade','int4','Necessidade especial','0', 'Necessidade',10,'f','f','f',1,'text','Necessidade');
insert into db_sysarqcamp values(1011027,1014759,1,0);
insert into db_sysarqcamp values(1011027,1014760,2,0);
insert into db_sysarqcamp values(1011027,1014761,3,0);

insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011027,1014759,1,1014760);

insert into db_sysindices values(1008840,'necessidadealunocadeirante_ed189_aluno_in',1011027,'0');
insert into db_syscadind values(1008840,1014760,1);

insert into db_sysindices values(1008841,'necessidadealunocadeirante_ed189_necessidade_in',1011027,'0');
insert into db_syscadind values(1008841,1014761,1);

insert into db_sysforkey values(1011027,1014760,1,1010051,0);
insert into db_sysforkey values(1011027,1014761,1,1010050,0);
--
--
--

--
-- necessidadealunobpc
--
insert into db_sysarquivo values (1011028, 'necessidadealunobpc', 'Necessidade bpc do aluno', 'ed190', '2023-02-14', 'Necessidade bpc do aluno', 0, 'f', 't', 't', 't' );
insert into db_sysarqmod values (1008004,1011028);
insert into db_syscampo values(1014762,'ed190_sequencial','int4','Sequencial','0', 'Sequencial',10,'f','f','f',1,'text','Sequencial');
insert into db_syscampo values(1014763,'ed190_aluno','int4','Aluno','0', 'Aluno',10,'f','f','f',1,'text','Aluno');
insert into db_syscampo values(1014764,'ed190_necessidade','int4','Necessidade','0', 'Necessidade',10,'f','f','f',1,'text','Necessidade');
insert into db_sysarqcamp values(1011028,1014762,1,0);
insert into db_sysarqcamp values(1011028,1014763,2,0);
insert into db_sysarqcamp values(1011028,1014764,3,0);

insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011028,1014762,1,1014763);

insert into db_sysindices values(1008838,'necessidadealunobpc_ed190_aluno_in',1011028,'0');
insert into db_syscadind values(1008838,1014763,1);

insert into db_sysindices values(1008839,'necessidadealunobpc_ed190_necessidade_in',1011028,'0');
insert into db_syscadind values(1008839,1014764,1);

insert into db_sysforkey values(1011028,1014763,1,1010051,0);
insert into db_sysforkey values(1011028,1014764,1,1010050,0);
--
--
--

--
-- SEQUENCIAS
--
insert into db_syssequencia values(1001113, 'necessidadealunobpc_ed190_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
update db_sysarqcamp set codsequencia = 1001113 where codarq = 1011028 and codcam = 1014762;
insert into db_syssequencia values(1001114, 'necessidadealunocadeirante_ed189_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
update db_sysarqcamp set codsequencia = 1001114 where codarq = 1011027 and codcam = 1014759;
insert into db_syssequencia values(1001115, 'necessidadetipoatendimentoaluno_ed188_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
update db_sysarqcamp set codsequencia = 1001115 where codarq = 1011026 and codcam = 1014756;
insert into db_syssequencia values(1001116, 'necessidadesubdivisaoaluno_ed187_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
update db_sysarqcamp set codsequencia = 1001116 where codarq = 1011025 and codcam = 1014753;
insert into db_syssequencia values(1001117, 'necessidadetipoatendimento_ed186_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
update db_sysarqcamp set codsequencia = 1001117 where codarq = 1011024 and codcam = 1014751;
insert into db_syssequencia values(1001118, 'necessidadesubdivisao_ed185_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
update db_sysarqcamp set codsequencia = 1001118 where codarq = 1011023 and codcam = 1014748;


--
-- ITENS DE MENU
--
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228879 ,'Deficiências / Altas Habilidades' ,'Deficiências / Altas Habilidades' ,'' ,'1' ,'1' ,'Deficiências / Altas Habilidades' ,'true' );
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228880 ,'Subdivisões da Deficiência' ,'Subdivisões da Deficiência' ,'' ,'1' ,'1' ,'Subdivisões da Deficiência' ,'true' );
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228881 ,'Inclusão' ,'Inclusão' ,'edu1_necessidadesubdivisao001.php?opcao=1' ,'1' ,'1' ,'Inclusão de Subdivisão de necessidade' ,'true' );
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228882 ,'Alteração' ,'Alteração' ,'edu1_necessidadesubdivisao001.php?opcao=2' ,'1' ,'1' ,'Alteração de Subdivisão de necessidade' ,'true' );
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228883 ,'Exclusão' ,'Exclusão' ,'edu1_necessidadesubdivisao001.php?opcao=3' ,'1' ,'1' ,'Exclusão de Subdivisão de necessidade' ,'true' );

insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 3470 ,228879 ,47 ,7159 );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228879 ,228880 ,1 ,7159 );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228880 ,228881 ,1 ,7159 );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228880 ,228882 ,2 ,7159 );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228880 ,228883 ,3 ,7159 );

insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228884 ,'Tipo de Atendimento' ,'Tipo de Atendimento' ,'' ,'1' ,'1' ,'Tipo de atendimento das necessidades especiais' ,'true' );
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228885 ,'Inclusão' ,'Inclusão de tipo de atendimento de necessidades especiais' ,'edu1_necessidadetipoatendimento001.php?opcao=1' ,'1' ,'1' ,'Inclusão de tipo de atendimento de necessidades especiais' ,'true' );
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228886 ,'Alteração' ,'Alteração de tipo de atendimento de necessidades especiais' ,'edu1_necessidadetipoatendimento001.php?opcao=2' ,'1' ,'1' ,'Alteração de tipo de atendimento de necessidades especiais' ,'true' );
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228887 ,'Exclusão' ,'Exclusão tipo de atendimento de necessidades especiais' ,'edu1_necessidadetipoatendimento001.php?opcao=3' ,'1' ,'1' ,'Exclusão tipo de atendimento de necessidades especiais' ,'true' );

insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228879 ,228884 ,2 ,7159 );    
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228884 ,228885 ,1 ,7159 );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228884 ,228886 ,2 ,7159 );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228884 ,228887 ,3 ,7159 );

SQL;
        DB::connection()->getPdo()->exec($sql);
        
    }
    
    public function DownDD() {
        
        $sql = <<<SQL
delete from db_syscadind where codind in (1008843, 1008844, 1008845, 1008847,1008848,1008842,1008846,1008840,
1008841,1008838,1008839);

delete from db_sysindices where codind in (1008843, 1008844, 1008845, 1008847,1008848,1008842,1008846,1008840,
1008841,1008838,1008839);

delete from db_syssequencia where codsequencia in (1001113, 1001114, 1001115, 1001116, 1001117, 1001118);

delete from db_sysprikey where codarq in (1011026,1011025,1011024,1011023,1011027,1011028);

delete from db_sysforkey where codarq in (1011026,1011025,1011024,1011023,1011027,1011028);

delete from db_sysarqcamp where codarq in (1011026,1011025,1011024,1011023,1011027,1011028);

delete from db_syscampo where codcam in (1014759,1014760,1014761,1014762,1014762,1014763,1014764,1014763,1014756, 
1014757, 1014758, 1014753,1014754,1014755, 1014751,1014752,1014748, 1014749, 1014750);

delete from db_sysarqmod where codarq in (1011026,1011025,1011024,1011023,1011027,1011028);

delete from db_sysarquivo where codarq in (1011026,1011025,1011024,1011023,1011027,1011028);

delete from db_menu where id_item_filho in (228879,228880,228881,228882,228883,228884,228885,228886,228887);
delete from db_itensmenu where id_item in (228879,228880,228881,228882,228883,228884,228885,228886,228887);

SQL;
        DB::connection()->getPdo()->exec($sql);
        
    }
    
    public function UpEstrutura() {
        
        $sql = <<<SQL

create table escola.necessidadesubdivisao ( ed185_sequencial int primary key,
                                             ed185_necessidade int references escola.necessidade(ed48_i_codigo),
                                             ed185_descricao varchar(100) not null, 
                                             unique (ed185_necessidade, ed185_descricao));
create sequence escola.necessidadesubdivisao_ed185_sequencial_seq;

create table escola.necessidadetipoatendimento ( ed186_sequencial int primary key,
                                                  ed186_descricao varchar(100) not null unique);
create sequence escola.necessidadetipoatendimento_ed186_sequencial_seq;


create table escola.necessidadesubdivisaoaluno ( ed187_sequencial int primary key,
                                                  ed187_necessidadesubdivisao int references escola.necessidadesubdivisao(ed185_sequencial),
                                                  ed187_aluno int references escola.aluno(ed47_i_codigo),
                                                  unique (ed187_necessidadesubdivisao, ed187_aluno)
                                                  );
create sequence escola.necessidadesubdivisaoaluno_ed187_sequencial_seq;


create table escola.necessidadetipoatendimentoaluno
(
  ed188_sequencial int primary key,
  ed188_necessidadetipoatendimento int references escola.necessidadetipoatendimento(ed186_sequencial),
  ed188_aluno int references escola.aluno(ed47_i_codigo),
  unique (ed188_necessidadetipoatendimento, ed188_aluno)
);
create sequence escola.necessidadetipoatendimentoaluno_ed188_sequencial_seq;


create table escola.necessidadealunocadeirante ( ed189_sequencial int primary key,
                                                  ed189_aluno int references escola.aluno(ed47_i_codigo),
                                                  ed189_necessidade int references escola.necessidade(ed48_i_codigo)
                                                );
create sequence escola.necessidadealunocadeirante_ed189_sequencial_seq;


create table escola.necessidadealunobpc ( ed190_sequencial int primary key,
                                           ed190_aluno int references escola.aluno(ed47_i_codigo),
                                           ed190_necessidade int references escola.necessidade(ed48_i_codigo)
                                                );
create sequence escola.necessidadealunobpc_ed190_sequencial_seq;

create index necessidadealunobpc_ed190_necessidade_in on escola.necessidadealunobpc(ed190_necessidade);

create index necessidadealunobpc_ed190_aluno_in on escola.necessidadealunobpc(ed190_aluno);

create index necessidadealunocadeirante_ed189_necessidade_in on escola.necessidadealunocadeirante(ed189_necessidade);

create index necessidadealunocadeirante_ed189_aluno_in on escola.necessidadealunocadeirante(ed189_aluno);

create index necessidadesubdivisao_ed185_necessidade_in on escola.necessidadesubdivisao(ed185_necessidade);

create index necessidadesubdivisaoaluno_ed187_necessidadesubdivisao_in on escola.necessidadesubdivisaoaluno(ed187_necessidadesubdivisao);

create index necessidadesubdivisaoaluno_ed187_aluno_in on escola.necessidadesubdivisaoaluno(ed187_aluno);

create unique index necessidadetipoatendimento_ed186_descricao_in on escola.necessidadetipoatendimento(ed186_descricao);

create unique index necessidadetipoatendimentoaluno_unique_in on escola.necessidadetipoatendimentoaluno(ed188_aluno,ed188_necessidadetipoatendimento);

create index necessidadetipoatendimentoaluno_ed188_necessidadetipoatendimento_in on escola.necessidadetipoatendimentoaluno(ed188_necessidadetipoatendimento);

create index necessidadetipoatendimentoaluno_ed188_aluno_in on escola.necessidadetipoatendimentoaluno(ed188_aluno);

SQL;
        DB::connection()->getPdo()->exec($sql);

    }
    
    public function DownEstrutura() {
        
        $sql = <<<SQL


DROP TABLE escola.necessidadesubdivisaoaluno;
DROP SEQUENCE escola.necessidadesubdivisaoaluno_ed187_sequencial_seq;

DROP TABLE escola.necessidadetipoatendimentoaluno;
DROP SEQUENCE escola.necessidadetipoatendimentoaluno_ed188_sequencial_seq;

DROP TABLE escola.necessidadealunocadeirante;
DROP SEQUENCE escola.necessidadealunocadeirante_ed189_sequencial_seq;

DROP TABLE escola.necessidadealunobpc;
DROP SEQUENCE escola.necessidadealunobpc_ed190_sequencial_seq;

DROP TABLE escola.necessidadetipoatendimento;
DROP SEQUENCE escola.necessidadetipoatendimento_ed186_sequencial_seq;

DROP TABLE escola.necessidadesubdivisao;
DROP SEQUENCE escola.necessidadesubdivisao_ed185_sequencial_seq;

SQL;
        DB::connection()->getPdo()->exec($sql);
        
    }
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $this->UpDD();
        $this->UpEstrutura();

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        $this->DownDD();
        $this->DownEstrutura();
        
        
    }
}
