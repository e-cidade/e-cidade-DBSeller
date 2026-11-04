<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24579ImplementacaoSuspensaoOrcamentaria extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "
insert into db_sysarquivo values(1011089, 'suspensaoorcamentaria', 'Armazena as configurações das suspensões de movimentação das dotações para empenho, liquidação e pagamentos.', 'o153', '2023-05-28', 'Suspensão Orçamentária', 0, 'f', 't', 't', 't');
insert into db_sysarqmod values(35, 1011089);
insert into db_syscampo values(1015110, 'o153_sequencial', 'int4', 'Sequencial', '0', 'Sequencial', 10, 'f', 'f', 'f', 1, 'text', 'Sequencial');
insert into db_syscampo values(1015111, 'o153_orgao', 'int4', 'Orgão', '0', 'Orgão', 10, 'f', 'f', 'f', 1, 'text', 'Orgão');
insert into db_syscampo values(1015112, 'o153_unidade', 'int4', 'Unidade', '0', 'Unidade', 10, 'f', 'f', 'f', 1, 'text', 'Unidade');
insert into db_syscampo values(1015114, 'o153_recurso', 'int4', 'Recurso', '0', 'Recurso', 10, 'f', 'f', 'f', 1, 'text', 'Recurso');
insert into db_syscampo values(1015113, 'o153_localizadorgastos', 'int4', 'Anexo', '0', 'Anexo', 10, 'f', 'f', 'f', 1, 'text', 'Anexo');
insert into db_syscampo values(1015115, 'o153_exercicio', 'int4', 'Exercício', '0', 'Exercício', 10, 'f', 'f', 'f', 1, 'text', 'Exercício');
insert into db_syscampo values(1015116, 'o153_suspenderempenho', 'bool', 'Suspender Empenho', 'f', 'Suspender Empenho', 1, 'f', 'f', 'f', 5, 'text', 'Suspender Empenho');
insert into db_syscampodef values(1015116, 't', 'SIM');
insert into db_syscampodef values(1015116, 'f', 'NÃO');
insert into db_syscampo values(1015117, 'o153_suspenderliquidacao', 'bool', 'Suspender Empenho', 'f', 'Suspender Empenho', 1, 'f', 'f', 'f', 5, 'text', 'Suspender Empenho');
insert into db_syscampodef values(1015117, 't', 'SIM');
insert into db_syscampodef values(1015117, 'f', 'NÃO');
insert into db_syscampo values(1015118, 'o153_suspenderpagamento', 'bool', 'Suspender Pagamento', 'f', 'Suspender Pagamento', 1, 'f', 'f', 'f', 5, 'text', 'Suspender Pagamento');
insert into db_syscampodef values(1015118, 't', 'SIM');
insert into db_syscampodef values(1015118, 'f', 'NÃO');
insert into db_sysarqcamp values(1011089, 1015110, 1, 0);
insert into db_sysarqcamp values(1011089, 1015111, 2, 0);
insert into db_sysarqcamp values(1011089, 1015112, 3, 0);
insert into db_sysarqcamp values(1011089, 1015114, 4, 0);
insert into db_sysarqcamp values(1011089, 1015113, 5, 0);
insert into db_sysarqcamp values(1011089, 1015115, 6, 0);
insert into db_sysarqcamp values(1011089, 1015116, 7, 0);
insert into db_sysarqcamp values(1011089, 1015117, 8, 0);
insert into db_sysarqcamp values(1011089, 1015118, 9, 0);
insert into db_sysprikey(codarq, codcam, sequen, camiden) values(1011089, 1015110, 1, 1015110);
insert into db_sysindices values(1008866, 'suspensaoorcamentaria_o153_exercicio_in', 1011089, '0');
insert into db_syscadind values(1008866, 1015115, 1);
insert into db_sysindices values(1008867, 'suspensaoorcamentaria_unique_in', 1011089, '1');
insert into db_syscadind values(1008867, 1015111, 1);
insert into db_syscadind values(1008867, 1015112, 2);
insert into db_syscadind values(1008867, 1015114, 3);
insert into db_syscadind values(1008867, 1015113, 4);
insert into db_syscadind values(1008867, 1015115, 5);
insert into db_syssequencia values(1001129, 'suspensaoorcamentaria_o153_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
update db_sysarqcamp set codsequencia = 1001129 where codarq = 1011089 and codcam = 1015110;

insert into db_itensmenu(id_item, descricao, help, funcao, itemativo, manutencao, desctec, libcliente) values(228936, 'Suspensão Orçamentária', 'Suspensão Orçamentária', 'orc4_suspensaoorcamentaria001.php', '1', '1', 'Configuração da suspensão orçamentária', 'true');
delete from db_menu where id_item_filho = 228936 and modulo = 116;
insert into db_menu(id_item, id_item_filho, menusequencia, modulo) values(3216, 228936, 5, 116);

CREATE SEQUENCE suspensaoorcamentaria_o153_sequencial_seq
INCREMENT 1
MINVALUE 1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

CREATE TABLE suspensaoorcamentaria(
    o153_sequencial		int4 NOT NULL default 0,
    o153_orgao		int4 NOT NULL default 0,
    o153_unidade		int4 NOT NULL default 0,
    o153_recurso		int4 NOT NULL default 0,
    o153_localizadorgastos		int4 NOT NULL default 0,
    o153_exercicio		int4 NOT NULL default 0,
    o153_suspenderempenho		bool NOT NULL default 'f',
    o153_suspenderliquidacao		bool NOT NULL default 'f',
    o153_suspenderpagamento		bool default 'f',
    CONSTRAINT suspensaoorcamentaria_sequ_pk PRIMARY KEY (o153_sequencial));

CREATE UNIQUE INDEX suspensaoorcamentaria_unique_in ON suspensaoorcamentaria(o153_orgao,o153_unidade,o153_recurso,o153_localizadorgastos,o153_exercicio);
CREATE  INDEX suspensaoorcamentaria_o153_exercicio_in ON suspensaoorcamentaria(o153_exercicio);
";
        DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = "
delete from db_menu where id_item_filho = 228936 and modulo = 116;
delete from db_itensmenu where id_item = 228936;

delete from db_syssequencia where codsequencia = 1001129;
delete from db_syscadind where codind in(1008866, 1008867);
delete from db_sysindices where codind in(1008866, 1008867);
delete from db_sysprikey where codarq = 1011089;
delete from db_sysarqcamp where codarq = 1011089;
delete from db_syscampodef where codcam in(1015110, 1015111, 1015112, 1015113, 1015114, 1015115, 1015116, 1015117, 1015118);
delete from db_syscampo where codcam in(1015110, 1015111, 1015112, 1015113, 1015114, 1015115, 1015116, 1015117, 1015118);
delete from db_sysarqmod where codarq = 1011089;
delete from db_sysarquivo where codarq = 1011089;

drop sequence suspensaoorcamentaria_o153_sequencial_seq;
drop table suspensaoorcamentaria;
";
        DB::connection()->getPdo()->exec($sql);
    }
}
