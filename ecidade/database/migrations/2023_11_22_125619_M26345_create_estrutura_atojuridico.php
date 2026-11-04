<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26345CreateEstruturaAtojuridico extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upEstrutura();
        $this->upDicionario();
        $this->upDados();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downDicionario();
        $this->downEstrutura();
    }

    public function upEstrutura(){
        DB::connection()->getPdo()->exec(<<<SQL

/** =============================   RF 15  =============================*/
create TABLE empenho.tipoatojuridico (
    e165_sequencial integer primary key,
    e165_descricao varchar(120) not null,
    e165_ativo boolean not null default true
);

CREATE SEQUENCE IF NOT EXISTS empenho.emptipoatojuridico_e166_sequencial_seq
INCREMENT 1
MINVALUE 1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

CREATE TABLE empenho.emptipoatojuridico (
    e166_sequencial integer primary key,
    e166_empempenho integer UNIQUE not null,
    e166_tipoatojuridico integer not null,
    CONSTRAINT fk_emptipoatojuridico_tipoatojuridico FOREIGN KEY (e166_tipoatojuridico) REFERENCES empenho.tipoatojuridico(e165_sequencial),
    CONSTRAINT fk_emptipoatojuridico_empempenho FOREIGN KEY (e166_empempenho) REFERENCES empenho.empempenho(e60_numemp)
);

select configuracoes.fc_auditoria_cria_funcao('empenho.tipoatojuridico');
select configuracoes.fc_auditoria_cria_funcao('empenho.emptipoatojuridico');

/** =============================   FIM RF 15  =============================*/

/** =============================   RF 16  =============================*/

create TABLE empenho.justificativaatojuridico (
    e173_sequencial integer primary key,
    e173_descricao varchar(120) not null,
    e173_ativo boolean not null default true
);


create TABLE empenho.justificativainstrumentoprevio (
    e174_sequencial integer primary key,
    e174_descricao varchar(120) not null,
    e174_ativo boolean not null default true
);

CREATE SEQUENCE IF NOT EXISTS empenho.empjustificativaatojuridico_e175_sequencial_seq
INCREMENT 1
MINVALUE 1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

CREATE SEQUENCE IF NOT EXISTS empenho.empjustificativainstrumentoprevio_e176_sequencial_seq
INCREMENT 1
MINVALUE 1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

CREATE TABLE empenho.empjustificativaatojuridico (
    e175_sequencial integer primary key,
    e175_empempenho integer UNIQUE not null,
    e175_justificativaatojuridico integer not null,
    CONSTRAINT fk_empjustificativaatojuridico_empempenho FOREIGN KEY (e175_empempenho) REFERENCES empenho.empempenho(e60_numemp),
    CONSTRAINT fk_empjustificativaatojuridico_atojuridico FOREIGN KEY (e175_justificativaatojuridico) REFERENCES empenho.justificativaatojuridico(e173_sequencial)
);

CREATE TABLE empenho.empjustificativainstrumentoprevio (
    e176_sequencial integer primary key,
    e176_empempenho integer UNIQUE not null,
    e176_justificativainstrumentoprevio integer not null,
    CONSTRAINT fk_empjustificativainstrumentoprevio_empempenho FOREIGN KEY (e176_empempenho) REFERENCES empenho.empempenho(e60_numemp),
    CONSTRAINT fk_empjustificativainstrumentoprevio_instrumentoprevio FOREIGN KEY (e176_justificativainstrumentoprevio) REFERENCES empenho.justificativainstrumentoprevio(e174_sequencial)
);

select configuracoes.fc_auditoria_cria_funcao('empenho.justificativaatojuridico');
select configuracoes.fc_auditoria_cria_funcao('empenho.justificativainstrumentoprevio');
select configuracoes.fc_auditoria_cria_funcao('empenho.empjustificativaatojuridico');
select configuracoes.fc_auditoria_cria_funcao('empenho.empjustificativainstrumentoprevio');

/** =============================  FIM RF 16  =============================*/
SQL
        );
    }

    public function upDicionario(){
        DB::connection()->getPdo()->exec(<<<SQL

/** =============================   RF 15  =============================*/

-- Tabela tipoatojuridico

insert into db_sysarquivo values (1011163, 'tipoatojuridico', 'Tipo de ato jurídico', 'e165', '2023-11-22', 'Ato jurídico', 0, 'f', 'f', 'f', 'f' );
insert into db_sysarqmod values (38,1011163);
insert into db_syscampo values(1015550,'e165_sequencial','int4','Código do tipo de ato jurídico.','0', 'Código',10,'f','f','f',1,'text','Código');
insert into db_syscampo values(1015551,'e165_descricao','varchar(120)','Descrição do tipo de ato jurídico','', 'Descrição',120,'f','f','f',0,'text','Descrição');
insert into db_syscampo values(1015552,'e165_ativo','bool','Verifica se o tipo de ato jurídico está ativo ou não.','f', 'Ativo',1,'f','f','f',5,'text','Ativo');
insert into db_sysarqcamp values(1011163,1015550,1,0);
insert into db_sysarqcamp values(1011163,1015551,2,0);
insert into db_sysarqcamp values(1011163,1015552,3,0);
insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011163,1015550,1,1015550);
update db_syscampo set nomecam = 'e165_ativo', conteudo = 'bool', descricao = 'Verifica se o tipo de ato jurídico está ativo ou não.', valorinicial = 't', rotulo = 'Ativo', nulo = 'f', tamanho = 1, maiusculo = 'f', autocompl = 'f', aceitatipo = 5, tipoobj = 'text', rotulorel = 'Ativo' where codcam = 1015552;

-- Tabela emptipatojuridico

insert into db_sysarquivo values (1011164, 'emptipoatojuridico', 'Tipo do ato jurídico do empenho.', 'e166', '2023-11-22', 'Tipo Ato Jurídico Empenho', 0, 'f', 'f', 'f', 'f' );
insert into db_sysarqmod values (38,1011164);
insert into db_syscampo values(1015553,'e166_sequencial','int4','Código para o tipo de ato jurídico do empenho.','0', 'Código',10,'f','f','f',1,'text','Código');
insert into db_syscampo values(1015554,'e166_empempenho','int4','Número do empenho','0', 'Número Empenho',10,'f','f','f',1,'text','Número Empenho');
insert into db_syscampo values(1015555,'e166_tipoatojuridico','int4','Tipo de ato jurídico.','0', 'Tipo Ato Jurídico',10,'f','f','f',1,'text','Tipo Ato Jurídico');
insert into db_sysarqcamp values(1011164,1015553,1,0);
insert into db_sysarqcamp values(1011164,1015554,2,0);
insert into db_sysarqcamp values(1011164,1015555,3,0);
insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011164,1015553,1,1015553);
insert into db_sysforkey values(1011164,1015554,1,889,0);
insert into db_sysforkey values(1011164,1015555,1,1011163,0);
insert into db_sysindices values(1008904,'emptipoatojuridico_empempenho',1011164,'1');
insert into db_syscadind values(1008904,1015554,1);
insert into db_syssequencia values(1001175, 'emptipoatojuridico_e166_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
update db_sysarqcamp set codsequencia = 1001175 where codarq = 1011164 and codcam = 1015553;

/** =============================   FIM RF 15  =============================*/

/** =============================   RF 16  =============================*/

-- Tabela justificativaatojuridico
insert into db_sysarquivo values (1011165, 'justificativaatojuridico', 'Justificativo do ato jurídico', 'e173', '2023-11-24', 'Justificativa Ato Jurídico', 0, 'f', 'f', 'f', 'f' );
insert into db_sysarqmod values (38,1011165);
insert into db_syscampo values(1015560,'e173_sequencial','int4','Sequencial da justificativa do ato jurídico','0', 'Sequencial',10,'f','f','f',1,'text','Sequencial');
insert into db_syscampo values(1015561,'e173_descricao','varchar(120)','Descrição da justificativa do ato jurídico','', 'Descrição',120,'f','f','f',0,'text','Descrição');
insert into db_syscampo values(1015562,'e173_ativo','bool','Valida se a justificativa do ato jurídico está ativa ou não','f', 'Ativo',1,'f','f','f',5,'text','Ativo');
insert into db_sysarqcamp values(1011165,1015560,1,0);
insert into db_sysarqcamp values(1011165,1015561,2,0);
insert into db_sysarqcamp values(1011165,1015562,3,0);
insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011165,1015560,1,1015560);
update db_syscampo set nomecam = 'e173_ativo', conteudo = 'bool', descricao = 'Valida se a justificativa do ato jurídico está ativa ou não', valorinicial = 't', rotulo = 'Ativo', nulo = 'f', tamanho = 1, maiusculo = 'f', autocompl = 'f', aceitatipo = 5, tipoobj = 'text', rotulorel = 'Ativo' where codcam = 1015562;

-- Tabela justificativainstrumentoprevio
insert into db_sysarquivo values (1011166, 'justificativainstrumentoprevio', 'Justificativa do auxílio prévio', 'e174', '2023-11-24', 'Justificativa Auxílio Prévio', 0, 'f', 'f', 'f', 'f' );
insert into db_sysarqmod values (38,1011166);
insert into db_syscampo values(1015563,'e174_sequencial','int4','Sequencial da justificativa do auxílio prévio','0', 'Sequencial',10,'f','f','f',1,'text','Sequencial');
insert into db_syscampo values(1015564,'e174_descricao','varchar(120)','Descrição da justificativa do auxílio prévio','', 'Descrição',120,'f','f','f',0,'text','Descrição');
insert into db_syscampo values(1015565,'e174_ativo','bool','Valida se a justificativa do auxílio prévio está ativa ou não','f', 'Ativo',1,'f','f','f',5,'text','Ativo');
insert into db_sysarqcamp values(1011166,1015563,1,0);
insert into db_sysarqcamp values(1011166,1015564,2,0);
insert into db_sysarqcamp values(1011166,1015565,3,0);
insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011166,1015563,1,1015563);
update db_syscampo set nomecam = 'e174_ativo', conteudo = 'bool', descricao = 'Valida se a justificativa do auxílio prévio está ativa ou não', valorinicial = 't', rotulo = 'Ativo', nulo = 'f', tamanho = 1, maiusculo = 'f', autocompl = 'f', aceitatipo = 5, tipoobj = 'text', rotulorel = 'Ativo' where codcam = 1015565;

/** Correção no dicionario de dados para instrumento previo ao invés de auxilio prévio */
update db_sysarquivo set nomearq = 'justificativainstrumentoprevio', descricao = 'Justificativa do instrumento prévio', sigla = 'e174', dataincl = '2023-11-27', rotulo = 'Justificativa Instrumento Prévio', tipotabela = 0, naolibclass = 'f', naolibfunc = 'f', naolibprog = 'f', naolibform = 'f' where codarq = 1011166;

update db_syscampo set nomecam = 'e174_sequencial', conteudo = 'int4', descricao = 'Sequencial da justificativa do instrumento prévio', valorinicial = '0', rotulo = 'Sequencial', nulo = 'f', tamanho = 10, maiusculo = 'f', autocompl = 'f', aceitatipo = 1, tipoobj = 'text', rotulorel = 'Sequencial' where codcam = 1015563;
update db_syscampo set nomecam = 'e174_descricao', conteudo = 'varchar(120)', descricao = 'Descrição da justificativa do instrumento prévio', valorinicial = '', rotulo = 'Descrição', nulo = 'f', tamanho = 120, maiusculo = 'f', autocompl = 'f', aceitatipo = 0, tipoobj = 'text', rotulorel = 'Descrição' where codcam = 1015564;
update db_syscampo set nomecam = 'e174_ativo', conteudo = 'bool', descricao = 'Valida se a justificativa do instrumento prévio está ativa ou não', valorinicial = 't', rotulo = 'Ativo', nulo = 'f', tamanho = 1, maiusculo = 'f', autocompl = 'f', aceitatipo = 5, tipoobj = 'text', rotulorel = 'Ativo' where codcam = 1015565;

-- Tabela empjustificativaatojuridico
insert into db_sysarquivo values (1011167, 'empjustificativaatojuridico', 'Justificativa do ato jurídico vinculada ao empenho', 'e175', '2023-11-24', 'Justificativa Ato Jurídico Empenho', 0, 'f', 'f', 'f', 'f' );
insert into db_sysarqmod values (38,1011167);
insert into db_syscampo values(1015566,'e175_sequencial','int4','Sequencial do vínculo entre a justificativa do ato jurídico e o empenho','0', 'Sequencial',10,'f','f','f',1,'text','Sequencial');
insert into db_syscampo values(1015567,'e175_empempenho','int4','Número do empenho vinculado à justificativa do ato jurídico','0', 'Número Empenho',10,'f','f','f',1,'text','Número Empenho');
insert into db_syscampo values(1015568,'e175_justificativaatojuridico','int4','Justificativa do ato jurídico vinculado ao empenho. ','0', 'Justificativa Ato Jurídico',10,'f','f','f',1,'text','Justificativa Ato Jurídico');
insert into db_sysarqcamp values(1011167,1015566,1,0);
insert into db_sysarqcamp values(1011167,1015567,2,0);
insert into db_sysarqcamp values(1011167,1015568,3,0);
insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011167,1015566,1,1015566);
insert into db_sysforkey values(1011167,1015567,1,889,0);
insert into db_sysforkey values(1011167,1015568,1,1011165,0);
insert into db_sysindices values(1008905,'empjustificativaatojuridico_empempenho',1011167,'1');
insert into db_syscadind values(1008905,1015567,1);
insert into db_syssequencia values(1001176, 'empjustificativaatojuridico_e175_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
update db_sysarqcamp set codsequencia = 1001176 where codarq = 1011167 and codcam = 1015566;

-- Tabela empjustificativainstrumentoprevio
insert into db_sysarquivo values (1011168, 'empjustificativaauxilioprevio', 'Justificativa do auxílio prévio vinculado ao empenho', 'e176', '2023-11-24', 'Justificativa Auxílio Prévio Empenho', 0, 'f', 'f', 'f', 'f' );
insert into db_sysarqmod values (38,1011168);
insert into db_syscampo values(1015569,'e176_sequencial','int4','Sequencial do vínculo entre a justificativo do auxílio prévio e o empenho. ','0', 'Sequencial',10,'f','f','f',1,'text','Sequencial');
insert into db_syscampo values(1015570,'e176_empempenho','int4','Número do empenho vinculado à justificativa do auxílio prévio. ','0', 'Número Empenho',10,'f','f','f',1,'text','Número Empenho');
insert into db_syscampo values(1015571,'e176_justificativaauxilioprevio','int4','Justificativa do auxílio prévio vinculado ao empenho','0', 'Justificativa Auxílio Prévio',10,'f','f','f',1,'text','Justificativa Auxílio Prévio');
insert into db_sysarqcamp values(1011168,1015569,1,0);
insert into db_sysarqcamp values(1011168,1015570,2,0);
insert into db_sysarqcamp values(1011168,1015571,3,0);
insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011168,1015569,1,1015569);
insert into db_sysforkey values(1011168,1015570,1,889,0);
insert into db_sysforkey values(1011168,1015571,1,1011166,0);
insert into db_sysindices values(1008906,'empjustificativaauxilioprevio_empempenho',1011168,'1');
insert into db_syscadind values(1008906,1015570,1);
insert into db_syssequencia values(1001177, 'empjustificativaauxilioprevio_e176_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
update db_sysarqcamp set codsequencia = 1001177 where codarq = 1011168 and codcam = 1015569;

/** Correção no dicionario de dados para instrumento previo ao invés de auxilio prévio */

delete from db_syscadind where codind = 1008906;
delete from db_sysindices where codind = 1008906;
delete from db_syssequencia where codsequencia = 1001177;

update db_sysarquivo set nomearq = 'empjustificativainstrumentoprevio', descricao = 'Justificativa do instrumento prévio vinculado ao empenho', sigla = 'e176', dataincl = '2023-11-27', rotulo = 'Justificativa Instrumento Prévio Empenho', tipotabela = 0, naolibclass = 'f', naolibfunc = 'f', naolibprog = 'f', naolibform = 'f' where codarq = 1011168;
update db_syscampo set nomecam = 'e176_empempenho', conteudo = 'int4', descricao = 'Número do empenho vinculado à justificativa do instrumento prévio.', valorinicial = '0', rotulo = 'Número Empenho', nulo = 'f', tamanho = 10, maiusculo = 'f', autocompl = 'f', aceitatipo = 1, tipoobj = 'text', rotulorel = 'Número Empenho' where codcam = 1015570;
update db_syscampo set nomecam = 'e176_justificativainstrumentoprevio', conteudo = 'int4', descricao = 'Justificativa do instrumento prévio vinculado ao empenho', valorinicial = '0', rotulo = 'Justificativa Instrumento Prévio', nulo = 'f', tamanho = 10, maiusculo = 'f', autocompl = 'f', aceitatipo = 1, tipoobj = 'text', rotulorel = 'Justificativa Instrumento Prévio' where codcam = 1015571;
update db_syscampo set nomecam = 'e176_sequencial', conteudo = 'int4', descricao = 'Sequencial do vínculo entre a justificativo do instrumento prévio e o empenho.', valorinicial = '0', rotulo = 'Sequencial', nulo = 'f', tamanho = 10, maiusculo = 'f', autocompl = 'f', aceitatipo = 1, tipoobj = 'text', rotulorel = 'Sequencial' where codcam = 1015569;

insert into db_sysindices values(1008907,'empjustificativainstrumentoprevio_empempenho',1011168,'1');
insert into db_syscadind values(1008907,1015570,1);
insert into db_syssequencia values(1001178, 'empjustificativainstrumentoprevio_e176_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
update db_sysarqcamp set codsequencia = 1001178 where codarq = 1011168 and codcam = 1015569;

/** =============================  FIM RF 16  =============================*/
SQL
        );
    }

    public function downDicionario(){
        DB::connection()->getPdo()->exec(<<<SQL

/** =============================   RF 15  =============================*/

-- Tabela tipoatojuridico

delete from db_sysarqcamp where codarq = 1011163;
delete from db_sysprikey where codarq = 1011163;
delete from db_syscampo where codcam in (1015550,1015551,1015552);
delete from db_sysarqmod where codmod = 38 and codarq = 1011163;
delete from db_sysarquivo where codarq = 1011163;

-- Tabela emptipatojuridico
delete from db_sysarqcamp where codarq = 1011164;
delete from db_sysprikey where codarq = 1011164;
delete from db_sysforkey where codarq = 1011164;
delete from db_sysindices where codind = 1008904;
delete from db_syscadind where codind = 1008904;
delete from db_syssequencia where codsequencia = 1001175;
delete from db_syscampo where codcam in (1015553,1015554,1015555);
delete from db_sysarqmod where codmod = 38 and codarq = 1011164;
delete from db_sysarquivo where codarq = 1011164;
/** =============================   FIM 15  =============================*/

/** =============================  RF 16  =============================*/

-- Tabela justificativaatojuridico
delete from db_sysarqcamp where codarq = 1011165;
delete from db_sysprikey where codarq = 1011165;
delete from db_syscampo where codcam in (1015560,1015561,1015562);
delete from db_sysarqmod where codmod = 38 and codarq = 1011165;
delete from db_sysarquivo where codarq = 1011165;
-- Tabela justificativainstrumentoprevio
delete from db_sysarqcamp where codarq = 1011166;
delete from db_sysprikey where codarq = 1011166;
delete from db_syscampo where codcam in (1015563,1015564,1015565);
delete from db_sysarqmod where codmod = 38 and codarq = 1011166;
delete from db_sysarquivo where codarq = 1011166;

-- Tabela empjustificativaatojuridico
delete from db_sysarqcamp where codarq = 1011167;
delete from db_sysprikey where codarq = 1011167;
delete from db_sysforkey where codarq = 1011167;
delete from db_sysindices where codind = 1008905;
delete from db_syscadind where codind = 1008905;
delete from db_syssequencia where codsequencia = 1001176;
delete from db_syscampo where codcam in (1015566,1015567,1015568);
delete from db_sysarqmod where codmod = 38 and codarq = 1011167;
delete from db_sysarquivo where codarq = 1011167;
-- Tabela empjustificativainstrumentoprevio
delete from db_sysarqcamp where codarq = 1011168;
delete from db_sysprikey where codarq = 1011168;
delete from db_sysforkey where codarq = 1011168;
delete from db_sysindices where codind = 1008907;
delete from db_syscadind where codind = 1008907;
delete from db_syssequencia where codsequencia = 1001178;
delete from db_syscampo where codcam in (1015569,1015570,1015571);
delete from db_sysarqmod where codmod = 38 and codarq = 1011168;
delete from db_sysarquivo where codarq = 1011168;
/** =============================  FIM RF 16  =============================*/

SQL
        );
    }

    public function downEstrutura(){
        DB::connection()->getPdo()->exec(<<<SQL

/** =============================   RF 15  =============================*/
DROP SEQUENCE IF EXISTS empenho.emptipoatojuridico_e166_sequencial_seq;
DROP TABLE emptipoatojuridico;
DROP TABLE tipoatojuridico;
/** =============================   FIM 15  =============================*/

/** =============================   RF 16  =============================*/

DROP SEQUENCE IF EXISTS empenho.empjustificativaatojuridico_e175_sequencial_seq;
DROP SEQUENCE IF EXISTS empenho.empjustificativainstrumentoprevio_e176_sequencial_seq;
DROP TABLE empjustificativaatojuridico;
DROP TABLE empjustificativainstrumentoprevio;
DROP TABLE justificativaatojuridico;
DROP TABLE justificativainstrumentoprevio;
/** =============================   FIM RF 16  =============================*/
SQL
        );
    }

    public function upDados(){
        DB::connection()->getPdo()->exec(<<<SQL

/** =============================   RF 15  =============================*/
INSERT INTO tipoatojuridico VALUES
    (1,'Contrato'),
    (2,'Convênio'),
    (3,'Reconhecimento de dívida'),
    (4,'Termos de Parceria'),
    (5,'Desapropriação'),
    (6,'Concessões'),
    (7,'Contrato de programa'),
    (8,'Contrato de gestão'),
    (9,'Termo de colaboração/fomento'),
    (99,'Inexistência de Ato Jurídico/Justificativa');
/** =============================   FIM 15  =============================*/

/** =============================   RF 16  =============================*/

INSERT INTO justificativaatojuridico VALUES
    (1,'Valor inferior ao previsto para tomada de preços'),
    (2,'Compra com entrega imediata e integral, não resultando em obrigações futuras'),
    (3,'Concessionárias'),
    (4,'Tarifas e obrigações bancárias'),
    (5,'Taxas,custas,tributos ou emolumentos devidos a outros entes'),
    (6,'Adiantamentos');


INSERT INTO justificativainstrumentoprevio VALUES
    (1,'Concessionárias'),
    (2,'Tarifas e obrigações'),
    (3,'Taxas,custas,tributos ou emolumentos devidos a outros entes'),
    (4,'Adiantamentos');
/** =============================   FIM RF 16  =============================*/
SQL
        );
    }

}
