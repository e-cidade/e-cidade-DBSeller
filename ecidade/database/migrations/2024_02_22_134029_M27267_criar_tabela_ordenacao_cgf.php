<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M27267CriarTabelaOrdenacaoCgf extends Migration
{
    public function up()
    {
        $this->upEstrutura();
        $this->upDicionario();
    }

    public function down()
    {
        $this->downEstrutura();
        $this->downDicionario();
    }

    public function upEstrutura()
    {
        $sql = <<<SQL
        create table caixa.tipoordenacaocgf (
            k240_sequencial serial,
            k240_descricao varchar(30) not null,
            constraint tipoordenacaocgf_sequ_pk primary key (k240_sequencial)
        );
        SELECT configuracoes.fc_auditoria_cria_funcao('caixa.tipoordenacaocgf');
        create table caixa.ordenacaocgf (
            k241_sequencial serial,
            k241_grupo integer not null,
            k241_tipo integer not null,
            constraint ordenacaocgf_sequ_pk primary key (k241_sequencial),
            constraint ordenacaocgf_grupo_fk foreign key (k241_grupo) references caixa.cadtipo,
            constraint ordenacaocgf_tipo_fk foreign key (k241_tipo) references caixa.tipoordenacaocgf
        );
        SELECT configuracoes.fc_auditoria_cria_funcao('caixa.ordenacaocgf');
SQL;
        $this->executeQuery($sql);
    }

    public function upDicionario()
    {
        $sql = <<<SQL
        insert into db_syscampo values(1015626,'k240_sequencial','int4','Sequencial tipo de ordenação da Consulta Geral Financeira.','0', 'Sequencial',8,'f','f','f',1,'text','Sequencial');
        insert into db_syscampo values(1015627,'k240_descricao','varchar(30)','Descrição do tipo de ordenação da Consulta Geral Financeira.','', 'Descrição',30,'f','t','f',0,'text','Descrição');
        insert into db_syscampo values(1015628,'k241_sequencial','int4','Sequencial da ordenação da Consulta Geral Financeira.','0', 'Sequencial',8,'f','f','f',1,'text','Sequencial');
        insert into db_syscampo values(1015629,'k241_grupo','int4','Grupo da ordenação da Consulta Geral Financeira.','0', 'Grupo',8,'f','f','f',1,'text','Grupo');
        insert into db_syscampo values(1015630,'k241_tipo','int4','Tipo de ordenação da Consulta Geral Financeira.','Tipo de ordenação', '',8,'f','f','f',1,'text','Tipo de ordenação');
        insert into db_sysarquivo values (1011173, 'tipoordenacaocgf', 'Tipo de ordenação da Consulta Geral Financeira.', 'k240', '2024-02-22', 'Tipo de ordenação CGF', 0, 'f', 'f', 'f', 'f' );
        insert into db_sysarqmod values (5,1011173);
        insert into db_sysarquivo values (1011174, 'ordenacaocgf', 'Ordenação da Consulta Geral Financeira.', 'k241', '2024-02-22', 'Ordenação CGF', 0, 'f', 'f', 'f', 'f' );
        insert into db_sysarqmod values (5,1011174);
        insert into db_sysarqcamp values(1011173,1015626,1,0);
        insert into db_sysarqcamp values(1011173,1015627,2,0);
        insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011173,1015626,1,1015626);
        insert into db_sysarqcamp values(1011174,1015628,1,0);
        insert into db_sysarqcamp values(1011174,1015629,2,0);
        insert into db_sysarqcamp values(1011174,1015630,3,0);
        insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011174,1015628,1,1015628);
        insert into db_sysforkey values(1011174,1015629,1,410,0);
        insert into db_sysforkey values(1011174,1015630,1,1011173,0);
SQL;
        $this->executeQuery($sql);
    }

    public function downEstrutura()
    {
        $sql = <<<SQL
        drop table caixa.ordenacaocgf;
        drop table caixa.tipoordenacaocgf;
SQL;
        $this->executeQuery($sql);
    }

    public function downDicionario()
    {
        $sql = <<<SQL
        delete from db_sysforkey where codarq = 1011174;
        delete from db_sysprikey where codarq in (1011173, 1011174);
        delete from db_sysarqcamp where codarq in (1011173, 1011174);
        delete from db_sysarqmod where codmod = 5 and codarq in (1011173, 1011174);
        delete from db_sysarquivo where codarq in (1011173, 1011174) and nomearq in ('tipoordenacaocgf','ordenacaocgf');
        delete from db_syscampo where codcam in (1015626,1015627,1015628,1015629,1015630) and nomecam in ('k240_sequencial','k240_descricao','k241_sequencial','k241_grupo','k241_tipo');
SQL;
        $this->executeQuery($sql);
    }

    private function executeQuery($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
