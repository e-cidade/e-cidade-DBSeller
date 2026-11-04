<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26901UnidadeMedidaSigfis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upEstrutura();
        $this->upDados();
        $this->upDicionario();

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downEstrutura();        
        $this->downDicionario();
    }

    public function upDicionario(){

DB::connection()->getPdo()->exec(<<<SQL

insert into db_sysarquivo values (1011169, 'sigfisunidademedida', 'Unidade de medida do SIGFIS', 'o220', '2024-01-16', 'Unidade de medida', 0, 'f', 'f', 'f', 'f' );
insert into db_sysarqmod values (35,1011169);
insert into db_sysarquivo values (1011170, 'orcprojativunidademedida', 'Unidade de medida do Sigfis vinculado à ação.', 'o221', '2024-01-16', 'Unidade de medida da ação', 0, 'f', 'f', 'f', 'f' );
insert into db_sysarqmod values (35,1011170);
insert into db_syscampo values(1015589,'o220_sequencial','int4','Sequencial da unidade de medida do Sigfis.','0', 'Sequencial',10,'f','f','f',1,'text','Sequencial');
insert into db_syscampo values(1015590,'o220_abreviacao','varchar(6)','Abreviação da unidade de medida do Sigfis','', 'Abreviação da unidade de medida',6,'f','f','f',0,'text','Abreviação da unidade de medida');
insert into db_syscampo values(1015591,'o220_ativo','bool','Verifica se a unidade da medida do Sigfis está ativa.','t', 'Ativo',1,'f','f','f',5,'text','Ativo');
insert into db_syscampo values(1015592,'o220_codigo','int4','Codigo da unidade de medida do sigfis','0', 'Codigo da unidade de medida',10,'f','f','f',1,'text','Codigo da unidade de medida');
insert into db_syscampo values(1015593,'o220_descricao','varchar(250)','Descrição da unidade de medida do Sigfis.','', 'Descrição da unidade de medida',250,'f','f','f',0,'text','Descrição da unidade de medida');
insert into db_syscampo values(1015594,'o221_sequencial','int4','Sequencial','0', 'Sequencial',10,'f','f','f',1,'text','Sequencial');
insert into db_syscampo values(1015595,'o221_orcprojativ','int4','Acao','0', 'Acao',10,'f','f','f',1,'text','Acao');
insert into db_syscampo values(1015596,'o221_anousu','int4','Ano','0', 'Ano',10,'f','f','f',1,'text','Ano');
insert into db_syscampo values(1015597,'o221_unidade','int4','Sequencial da unidade de medida','0', 'Sequencial da unidade de medida',10,'f','f','f',1,'text','Sequencial da unidade de medida');
insert into db_sysarqcamp values(1011170,1015594,1,0);
insert into db_sysarqcamp values(1011170,1015595,2,0);
insert into db_sysarqcamp values(1011170,1015596,3,0);
insert into db_sysarqcamp values(1011170,1015597,4,0);
insert into db_sysarqcamp values(1011169,1015589,1,0);
insert into db_sysarqcamp values(1011169,1015592,2,0);
insert into db_sysarqcamp values(1011169,1015590,3,0);
insert into db_sysarqcamp values(1011169,1015593,4,0);
insert into db_sysarqcamp values(1011169,1015591,5,0);
insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011169,1015589,1,1015589);
insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011170,1015594,1,1015594);
insert into db_sysforkey values(1011170,1015596,1,754,0);
insert into db_sysforkey values(1011170,1015595,2,754,0);
insert into db_sysforkey values(1011170,1015597,1,1011169,0);
insert into db_sysindices values(1008908,'orcprojativunidademedida_in',1011170,'1');
insert into db_syscadind values(1008908,1015595,1);
insert into db_syscadind values(1008908,1015596,2);
insert into db_syssequencia values(1001180, 'sigfisunidademedida_o220_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
update db_sysarqcamp set codsequencia = 1001180 where codarq = 1011169 and codcam = 1015589;
insert into db_syssequencia values(1001181, 'orcprojativunidademedida_o221_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
update db_sysarqcamp set codsequencia = 1001181 where codarq = 1011170 and codcam = 1015594;
SQL
        );
    }

    public function upEstrutura(){

DB::connection()->getPdo()->exec(<<<SQL

CREATE SEQUENCE IF NOT EXISTS orcamento.sigfisunidademedida_o220_sequencial_seq
INCREMENT 1
MINVALUE 1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

CREATE SEQUENCE IF NOT EXISTS orcamento.orcprojativunidademedida_o221_sequencial_seq
INCREMENT 1
MINVALUE 1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

create table orcamento.sigfisunidademedida (
	o220_sequencial int primary key default nextval('sigfisunidademedida_o220_sequencial_seq'), 
    o220_codigo int not null,
    o220_abreviacao varchar(20) ,  
    o220_descricao varchar(250),
    o220_ativo boolean not null default true 
);


create table orcamento.orcprojativunidademedida (
	o221_sequencial int primary key default nextval('orcprojativunidademedida_o221_sequencial_seq'),
	o221_orcprojativ int not null ,
	o221_anousu int not null,
	o221_unidade int not null,
	CONSTRAINT fk_unidademedida_orcprojativ_sigfis foreign key  (o221_unidade) references sigfisunidademedida (o220_sequencial),
	CONSTRAINT fk_unidademedida_orcprojativ foreign key (o221_orcprojativ,o221_anousu) references orcprojativ (o55_projativ,o55_anousu)
);

create unique index orcprojativunidademedida_in on orcprojativunidademedida (o221_orcprojativ,o221_anousu);

select configuracoes.fc_auditoria_cria_funcao('orcamento.sigfisunidademedida');
select configuracoes.fc_auditoria_cria_funcao('orcamento.orcprojativunidademedida');

SQL
        );
    }

    public function upDados(){

DB::connection()->getPdo()->exec(<<<SQL

insert into sigfisunidademedida values 
(nextval('sigfisunidademedida_o220_sequencial_seq'),1,'km','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),2,'kw','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),3,'m²','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),4,'m³','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),5,'m³/s ','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),6,'pop','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),7,'ton','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),8,'ton/dia','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),9,'ton/km','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),10,'unid','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),11,'m','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),12,'kg','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),13,'dia','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),14,'mês','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),15,'h','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),16,'m²/mês','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),17,'m²/km','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),18,'m³/mês','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),19,'Ha','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),20,'unid/mês','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),21,'unid/km','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),22,'ton/m','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),23,'unid/m','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),24,'TR','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),25,'UR','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),26,'cp','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),27,'ml','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),28,'mg','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),29,'Outros','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),30,'cm/col','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),31,'L','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),32,'cm/col','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),33,'cx','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),34,'pct','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),37,'serv','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),38,'rolo','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),39,'Desc','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),40,'Duzia','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),41,'Capsula','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),42,'Frasco','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),43,'Ampola','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),44,'Tubo','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),45,'Jogo','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),46,'Pares','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),50,'Serv/mês','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),51,'min','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),52,'maço','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),53,'g','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),54,'Envelope','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),55,'Frasco/ampola','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),56,'Frasco/bolsa','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),57,'Bisnaga','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),58,'Bolsa','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),59,'Sachê','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),60,'Peça','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),61,'Vidro','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),62,'Bloco','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),63,'Lata','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),64,'m/mês','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),65,'CHP','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),66,'Bombona','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),67,'Gb','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),68,'Cvh','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),69,'semana','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),70,'PF','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),71,'Em','',true),
(nextval('sigfisunidademedida_o220_sequencial_seq'),72,'Almotolia','',true);

SQL
        );
    }

    public function downEstrutura(){

DB::connection()->getPdo()->exec(<<<SQL

DROP TABLE orcprojativunidademedida;
DROP TABLE sigfisunidademedida;

DROP SEQUENCE IF EXISTS orcamento.sigfisunidademedida_o220_sequencial_seq;
DROP SEQUENCE IF EXISTS orcamento.orcprojativunidademedida_o221_sequencial_seq;

SQL
        );

    }

    public function downDicionario(){

DB::connection()->getPdo()->exec(<<<SQL

--Tabela sigfisunidademedida
delete from db_sysarqcamp where codarq = 1011169;
delete from db_sysprikey where codarq = 1011169;
delete from db_syssequencia where codsequencia = 1001180;
delete from db_syscampo where codcam in (1015589,1015590,1015591,1015592,1015593);
delete from db_sysarqmod where codmod = 35 and codarq = 1011169;
delete from db_sysarquivo where codarq = 1011169;


--Tabela orcprojativunidademedida 
delete from db_sysarqcamp where codarq = 1011170;
delete from db_sysprikey where codarq = 1011170;
delete from db_sysforkey where codarq = 1011170;
delete from db_sysindices where codind = 1008908;
delete from db_syscadind where codind = 1008908;
delete from db_syssequencia where codsequencia = 1001181;
delete from db_syscampo where codcam in (1015594,1015595,1015596,1015597);
delete from db_sysarqmod where codmod = 35 and codarq = 1011170;
delete from db_sysarquivo where codarq = 1011170;
SQL
        );
    }

}
