<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CriacaoTabelasProcessamentoFudeb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDicionario();
        $this->upEstrutura();
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

    public function upDicionario()
    {
        $sql  = <<<SQL
            insert into db_sysarquivo values (1011055, 'rhcargosfundeb', 'Tabela referente ao Tipo de Cargos do Fundeb', 'rh283', '2023-03-28', 'rh283', 0, 'f', 'f', 'f', 'f' );
            insert into db_sysarqmod values (28,1011055);
            insert into db_sysarqarq values (0,1011055);
            insert into db_syscampo values (1014930,'rh283_codigo','int4','Codigo do Cargo nos Parametros do Fundeb','0', 'Codigo do Cargo',10,'f','f','f',1,'text','Codigo do Cargo');
            insert into db_syscampo values (1014931,'rh283_descricao','varchar(20)','Descricao do Cargo nos Parametros do Fundeb','', 'Descricao do Cargo',20,'f','t','f',0,'text','Descricao do Cargo');
            insert into db_sysarqcamp values (1011055,1014930,1,0);
            insert into db_sysarqcamp values (1011055,1014931,2,0);
            insert into db_sysprikey (codarq,codcam,sequen,camiden) values (1011055,1014930,1,1014930);
            insert into db_sysarquivo values (1011056, 'rhparametrosfundeb', 'Tabela referente a Paramerizacao dos Cargos para o Cálculo do Fundeb', 'rh284', '2023-03-28', 'rh284', 0, 'f', 'f', 'f', 'f' );
            insert into db_sysarqmod values (28,1011056);
            insert into db_sysarqarq values (0,1011056);
            insert into db_syscampo values (1014932,'rh284_sequencial','int4','Sequencial da tabela','0', 'Sequencial Parametros Fundeb',10,'f','f','f',1,'text','Sequencial Parametros Fundeb');
            insert into db_syscampo values (1014933,'rh284_tipo','int4','Codigo do Cargo, o mesmo codigo da tabela rhcargosfundeb','0', 'Codigo do Cargo',10,'f','f','f',1,'text','Codigo do Cargo');
            insert into db_syscampo values (1014934,'rh284_cargo','int4','Codigo do Cargo o mesno da tabela rhfuncao','0', 'Codigo do Cargo',10,'f','f','f',1,'text','Codigo do Cargo');
            insert into db_syscampo values (1014935,'rh284_local_trabalho','int4','Código do Local de Trabalho nos parâmetros Fundeb','0', 'Codigo do Local de Trabalho',10,'f','f','f',1,'text','Codigo do Local de Trabalho');
            insert into db_syscampo values (1015109,'rh284_assentamento','int4','Cadastro de Assentamento nos parâmetros do Fundeb','0', 'Cadastro de Assentamento ',10,'t','f','f',1,'text','Cadastro de Assentamento');
            insert into db_syscampo values (1014936,'rh284_instituicao','int4','Codigo da Instituicao','0', 'Codigo da Instituicao',10,'f','f','f',1,'text','Codigo da Instituicao');
            insert into db_sysarqcamp values (1011056,1014932,1,0);
            insert into db_sysarqcamp values (1011056,1014933,2,0);
            insert into db_sysarqcamp values (1011056,1014934,3,0);
            insert into db_sysarqcamp values (1011056,1014935,4,0);
            insert into db_sysarqcamp values (1011056,1015109,5,0);
            insert into db_sysarqcamp values (1011056,1014936,6,0);
            insert into db_sysprikey (codarq,codcam,sequen,camiden) values (1011056,1014932,1,1014932);
            insert into db_sysforkey values (1011056,1014933,1,1011055,0);
            insert into db_sysforkey values (1011056,1014934,1,1174,0);
            insert into db_sysforkey values (1011056,1014936,2,1174,0);
            insert into db_sysarquivo values (1011057, 'rhcalculofundeb', 'Cálculo do Fundeb', 'rh285', '2023-03-28', 'rh285', 0, 'f', 'f', 'f', 'f' );
            insert into db_sysarqmod values (28,1011057);
            insert into db_sysarqarq values (0,1011057);
            insert into db_syscampo values (1014938,'rh285_ano','int4','Ano da Competencia do Cálculo do Fundeb','0', 'Ano da Competencia do Cálculo',10,'f','f','f',1,'text','Ano da Competencia do Cálculo');
            insert into db_syscampo values (1014937,'rh285_mes','int4','Mes da Competencia do Cálculo do Fundeb','0', 'Mes da Competencia do Cálculo',10,'f','f','f',1,'text','Mes da Competencia do Cálculo');
            insert into db_syscampo values (1014939,'rh285_valor','float4','Valor Total que sera calculado para os cargos do Fundeb','0', 'Valor do Fundeb',10,'f','f','f',4,'text','Valor do Fundeb');
            insert into db_sysarqcamp values (1011057,1014938,1,0);
            insert into db_sysarqcamp values (1011057,1014937,2,0);
            insert into db_sysarqcamp values (1011057,1014939,3,0);
            insert into db_sysarquivo values (1011058, 'rhprocessamentofundeb', 'Tabela de Processamento do Cálculo do Fundeb', 'rh286', '2023-03-28', 'rh286', 0, 'f', 'f', 'f', 'f' );
            insert into db_sysarqmod values (28,1011058);
            insert into db_syscampo values (1014940,'rh286_sequencial','int4','Sequencial do Cálculo do Processamento do Fundeb','0', 'Sequencial Processamento Fundeb',10,'f','f','f',1,'text','Sequencial Processamento Fundeb');
            insert into db_syscampo values (1014943,'rh286_matricula','int4','Codigo da Matricula do Servidor','0', 'Codigo da Matricula do Servidor',10,'t','f','f',1,'text','Codigo da Matricula do Servidor');
            insert into db_syscampo values (1014946,'rh286_ano','int4','Ano da Competencia do Cálculo do Servidor','0', 'Ano da Competencia do Cálculo',10,'f','f','f',1,'text','Ano da Competencia do Cálculo');
            insert into db_syscampo values (1014947,'rh286_mes','int4','Mes da Competencia do Cálculo do Servidor','0', 'Mes da Competencia do Cálculo',10,'f','f','f',1,'text','Mes da Competencia do Cálculo');
            insert into db_syscampo values (1014941,'rh286_cargo','int4','Codigo do Cargo da Parametrizacao do Fundeb','0', 'Codigo do Cargo ',10,'t','f','f',1,'text','Codigo do Cargo ');
            insert into db_syscampo values (1014942,'rh286_rubrica','varchar(4)','Codigo da Rubrica para Cálculo','', 'Codigo da Rubrica',4,'t','t','f',0,'text','Codigo da Rubrica');
            insert into db_syscampo values (1014944,'rh286_valor','float4','Valor Calculado do Servidor','0', 'Valor Calculado do Servidor',10,'t','f','f',4,'text','Valor Calculado do Servidor');
            insert into db_syscampo values (1014945,'rh286_instituicao','int4','Codigo da Instituicao do Servidor','0', 'Codigo da Instituicao',10,'t','f','f',1,'text','Codigo da Instituicao');
            insert into db_sysarqcamp values (1011058,1014940,1,0);
            insert into db_sysarqcamp values (1011058,1014943,2,0);
            insert into db_sysarqcamp values (1011058,1014946,3,0);
            insert into db_sysarqcamp values (1011058,1014947,4,0);
            insert into db_sysarqcamp values (1011058,1014941,5,0);
            insert into db_sysarqcamp values (1011058,1014942,6,0);
            insert into db_sysarqcamp values (1011058,1014944,7,0);
            insert into db_sysarqcamp values (1011058,1014945,8,0);
            insert into db_sysprikey (codarq,codcam,sequen,camiden) values (1011058,1014940,1,1014940);
            insert into db_syscampo values (1015039,'rh286_local_trabalho','int4','Código do Local de Trabalho na tabela','0', 'Código do Local de Trabalho',10,'t','f','f',1,'text','Código do Local de Trabalho');
            insert into db_sysarqcamp values (1011058,1015039,6,0);
            insert into db_syscampo values (1015041,'rh283_quantidade','int8','Quantidade do Cargo a ser calculado na tabela rhprocessamentofundeb','0', 'Quantidade do Cargo ',10,'t','f','f',1,'text','Quantidade do Cargo ');
            insert into db_sysarqcamp values (1011055,1015041,3,0);

            update db_syscampo set nomecam = 'rh283_descricao', conteudo = 'varchar(20)', descricao = 'Descricao do Cargo nos Parametros do Fundeb', valorinicial = '', rotulo = 'Descricao do Cargo', nulo = 't', tamanho = 20, maiusculo = 't', autocompl = 'f', aceitatipo = 0, tipoobj = 'text', rotulorel = 'Descricao do Cargo' where codcam = 1014931;
            update db_syscampo set nomecam = 'rh284_tipo', conteudo = 'int4', descricao = 'Tipo do Cargo, o mesmo tipo configurado na tabela rhcargosfundeb', valorinicial = '0', rotulo = 'Tipo do Cargo', nulo = 'f', tamanho = 10, maiusculo = 'f', autocompl = 'f', aceitatipo = 1, tipoobj = 'text', rotulorel = 'Tipo do Cargo' where codcam = 1014933;
            update db_sysarqmod SET codmod = 28 WHERE codarq = 1011057;
            update db_syscampo set nomecam = 'rh285_valor', conteudo = 'float4', descricao = 'Valor Total que sera calculado para os cargos do Fundeb', valorinicial = '0', rotulo = 'Valor do Fundeb', nulo = 't', tamanho = 10, maiusculo = 'f', autocompl = 'f', aceitatipo = 4, tipoobj = 'text', rotulorel = 'Valor do Fundeb' where codcam = 1014939;
            update db_syscampo set nomecam = 'rh285_ano', conteudo = 'int4', descricao = 'Ano da Competencia do Cálculo do Fundeb', valorinicial = '0', rotulo = 'Ano da Competencia do Cálculo', nulo = 't', tamanho = 10, maiusculo = 'f', autocompl = 'f', aceitatipo = 1, tipoobj = 'text', rotulorel = 'Ano da Competencia do Cálculo' where codcam = 1014938;
            update db_syscampo set nomecam = 'rh285_mes', conteudo = 'int4', descricao = 'Mes da Competencia do Cálculo do Fundeb', valorinicial = '0', rotulo = 'Mes da Competencia do Cálculo', nulo = 't', tamanho = 10, maiusculo = 'f', autocompl = 'f', aceitatipo = 1, tipoobj = 'text', rotulorel = 'Mes da Competencia do Cálculo' where codcam = 1014937;
            update db_syscampo set nomecam = 'rh284_instituicao', conteudo = 'int4', descricao = 'Codigo da Instituicao', valorinicial = '0', rotulo = 'Codigo da Instituicao', nulo = 'f', tamanho = 10, maiusculo = 'f', autocompl = 'f', aceitatipo = 1, tipoobj = 'text', rotulorel = 'Codigo da Instituicao' where codcam = 1014936;
            update db_syscampo set nomecam = 'rh284_instituicao', conteudo = 'int4', descricao = 'Codigo da Instituicao', valorinicial = '0', rotulo = 'Codigo da Instituicao', nulo = 't', tamanho = 10, maiusculo = 'f', autocompl = 'f', aceitatipo = 1, tipoobj = 'text', rotulorel = 'Codigo da Instituicao' where codcam = 1014936;
            update db_syscampo set nomecam = 'rh284_local_trabalho', conteudo = 'int4', descricao = 'Codigo do Local de Trabalho do Cargo', valorinicial = '0', rotulo = 'Codigo do Local de Trabalho', nulo = 't', tamanho = 10, maiusculo = 'f', autocompl = 'f', aceitatipo = 1, tipoobj = 'text', rotulorel = 'Codigo do Local de Trabalho' where codcam = 1014935;
            update db_syscampo set nomecam = 'rh284_cargo', conteudo = 'int4', descricao = 'Codigo do Cargo o mesno da tabela rhfuncao', valorinicial = '0', rotulo = 'Codigo do Cargo', nulo = 't', tamanho = 10, maiusculo = 'f', autocompl = 'f', aceitatipo = 1, tipoobj = 'text', rotulorel = 'Codigo do Cargo' where codcam = 1014934;
            update db_syscampo set nomecam = 'rh284_tipo', conteudo = 'int4', descricao = 'Tipo do Cargo, o mesmo tipo configurado na tabela rhcargosfundeb', valorinicial = '0', rotulo = 'Tipo do Cargo', nulo = 't', tamanho = 10, maiusculo = 'f', autocompl = 'f', aceitatipo = 1, tipoobj = 'text', rotulorel = 'Tipo do Cargo' where codcam = 1014933;
            update db_syscampo set nomecam = 'rh283_codigo', conteudo = 'int4', descricao = 'Codigo do Cargo nos Parametros do Fundeb', valorinicial = '0', rotulo = 'Codigo do Cargo', nulo = 't', tamanho = 10, maiusculo = 'f', autocompl = 'f', aceitatipo = 1, tipoobj = 'text', rotulorel = 'Codigo do Cargo' where codcam = 1014930;
            update db_syscampo set nomecam = 'rh286_instituicao', conteudo = 'int4', descricao = 'Codigo da Instituicao do Servidor', valorinicial = '0', rotulo = 'Codigo da Instituicao', nulo = 'f', tamanho = 10, maiusculo = 'f', autocompl = 'f', aceitatipo = 1, tipoobj = 'text', rotulorel = 'Codigo da Instituicao' where codcam = 1014945;
            update db_syscampo set nomecam = 'rh286_matricula', conteudo = 'int4', descricao = 'Codigo da Matricula do Servidor', valorinicial = '0', rotulo = 'Codigo da Matricula do Servidor', nulo = 'f', tamanho = 10, maiusculo = 'f', autocompl = 'f', aceitatipo = 1, tipoobj = 'text', rotulorel = 'Codigo da Matricula do Servidor' where codcam = 1014943;
            update db_syscampo set nomecam = 'rh286_rubrica', conteudo = 'varchar(4)', descricao = 'Codigo da Rubrica para Cálculo', valorinicial = '', rotulo = 'Codigo da Rubrica', nulo = 'f', tamanho = 4, maiusculo = 't', autocompl = 'f', aceitatipo = 0, tipoobj = 'text', rotulorel = 'Codigo da Rubrica' where codcam = 1014942;
SQL;
            DB::connection()->getPdo()->exec($sql);
    }

    private function upEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL

create table pessoal.rhcargosfundeb(
    rh283_codigo bigserial primary key,
    rh283_descricao varchar(20) null,
    rh283_quantidade integer null
);

create table pessoal.rhparametrosfundeb(
    rh284_sequencial bigserial primary key,
    rh284_tipo integer null,
    rh284_cargo integer null,
    rh284_funcao integer null,
    rh284_local_trabalho integer null,
    rh284_assentamento integer null,
    rh284_instituicao integer not null,

    FOREIGN KEY (rh284_tipo) references pessoal.rhcargosfundeb (rh283_codigo) on DELETE CASCADE,
    FOREIGN KEY (rh284_local_trabalho, rh284_instituicao) references pessoal.rhlocaltrab (rh55_codigo, rh55_instit) on DELETE CASCADE
);

CREATE TABLE pessoal.rhcalculofundeb (
    rh285_ano integer not null,
    rh285_mes integer not null,
    rh285_valor double precision not null,
    CONSTRAINT rhcalculofundeb_pk PRIMARY KEY (rh285_ano, rh285_mes)
);

create table pessoal.rhprocessamentofundeb(
    rh286_sequencial bigserial primary key,
    rh286_matricula integer not null,
    rh286_ano integer not null,
    rh286_mes integer not null,
    rh286_cargo integer null,
    rh286_funcao integer null,
    rh286_local_trabalho integer null,
    rh286_rubrica varchar(4) not null,
    rh286_valor double precision not null,
    rh286_instituicao integer not null,

    FOREIGN KEY (rh286_matricula) references pessoal.rhpessoal (rh01_regist) on DELETE CASCADE,
    FOREIGN KEY (rh286_local_trabalho, rh286_instituicao) references pessoal.rhlocaltrab (rh55_codigo, rh55_instit) on DELETE CASCADE,
    FOREIGN KEY (rh286_rubrica, rh286_instituicao) references pessoal.rhrubricas (rh27_rubric, rh27_instit) on DELETE CASCADE
);

CREATE INDEX rhcargosfundeb_rh283_codigo_in ON pessoal.rhcargosfundeb(rh283_codigo);
CREATE INDEX rhparametrosfundeb_rh284_sequencial_rh284_cargo_rh284_funcao_rh284_local_trabalho_rh284_assentamento_in ON pessoal.rhparametrosfundeb(rh284_sequencial,rh284_cargo,rh284_funcao,rh284_local_trabalho,rh284_assentamento);
CREATE INDEX rhcalculofundeb_rh285_mes_rh285_ano_in ON pessoal.rhcalculofundeb(rh285_mes,rh285_ano);
CREATE INDEX rhprocessamentofundeb_rh286_sequencial_rh286_matricula_rh286_cargo_rh286_funcao_rh286_rubrica_rh286_ano_rh286_mes_in ON pessoal.rhprocessamentofundeb(rh286_sequencial,rh286_matricula,rh286_cargo,rh286_funcao,rh286_rubrica,rh286_ano,rh286_mes);

SELECT configuracoes.fc_auditoria_cria_funcao('pessoal.rhcargosfundeb');
SELECT configuracoes.fc_auditoria_cria_funcao('pessoal.rhparametrosfundeb');
SELECT configuracoes.fc_auditoria_cria_funcao('pessoal.rhcalculofundeb');
SELECT configuracoes.fc_auditoria_cria_funcao('pessoal.rhprocessamentofundeb');

SQL
);
    }

    private function downEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
SELECT configuracoes.fc_auditoria_remove_funcao('pessoal.rhcargosfundeb');
SELECT configuracoes.fc_auditoria_remove_funcao('pessoal.rhparametrosfundeb');
SELECT configuracoes.fc_auditoria_remove_funcao('pessoal.rhcalculofundeb');
SELECT configuracoes.fc_auditoria_remove_funcao('pessoal.rhprocessamentofundeb');

drop table IF EXISTS pessoal.rhcargosfundeb CASCADE;
drop table IF EXISTS pessoal.rhparametrosfundeb;
drop table IF EXISTS pessoal.rhcalculofundeb;
drop table IF EXISTS pessoal.rhprocessamentofundeb;

SQL
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function downDicionario()
    {
        $sql  = <<<SQL
            delete from db_sysarqmod where codmod = 28 and codarq in (1011055,1011056,1011057,1011058);
            delete from db_sysforkey where codarq = 1011056;
            delete from db_sysarqcamp where codarq in (1011055,1011056,1011057,1011058);
            delete from db_sysarquivo where codarq in (1011055,1011056,1011057,1011058);
            delete from db_sysarqarq where codarq in (1011055,1011056,1011057);
            delete from db_sysprikey where codarq in (1011055,1011056,1011058);
            delete from db_syscampodep where codcam in (1014931,1014933,1014939,1014938,1014937,1014936,1014935,1014934,1014930,1014945,1014943,1014942,1015109);
            delete from db_syscampodef where codcam in (1014931,1014933,1014939,1014938,1014937,1014936,1014935,1014934,1014930,1014945,1014943,1014942,1015109);
            delete from db_syscampo where codcam in (1014930,1014931,1014932,1014933,1014934,1014935,1014936,1014937,1014938,1014939,1014940,1014943,1014946,
            1014941,1014942,1014944,1014945,1014947,1015039,1015041,1015109);
SQL;
            DB::connection()->getPdo()->exec($sql);
    }
}
