<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24516EstruturaPlanilha extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDicionario();
        $this->upEscrutura();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downDicionario();
        $this->downEscrutura();
    }

    private function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_sysarquivo values (1011108, 'planilha_gerar_slips_cobertura_extra', 'Dados das Planilhas para gerar o slip de transferência para cobertura extra orçamentária', 'k217', '2023-06-26', '', 0, 'f', 'f', 'f', 'f' );
insert into db_sysarqmod values (5,1011108);

insert into db_syscampo
values (1015211,'credor','int4','Credor é o cgm','0', 'Credor',10,'f','f','f',1,'text','Credor'),
       (1015212,'creditar','int4','Conta que devemos creditar','0', 'Creditar',10,'f','f','f',1,'text','Creditar'),
       (1015213,'debitar','int4','Conta que devemos debitar','0', 'Debitar',10,'f','f','f',1,'text','Debitar'),
       (1015214,'historico','int4','Histórico do slip','0', 'Histórico',10,'f','f','f',1,'text','Histórico'),
       (1015215,'cp','char(3)','Característica Peculiar','000', 'Característica Peculiar',3,'f','t','f',0,'text','Característica Peculiar'),
       (1015216,'tipo_pagamento','int4','Tipo de Pagamento do slip. Deve ser sempre 3 ','3', 'Tipo de Pagamento',10,'f','f','f',1,'text','Tipo de Pagamento'),
       (1015217,'tipo_operacao','int4','Tipo de Operação do slip. Para esses sips deve ser 17','17', 'Tipo de Operação',10,'f','f','f',1,'text','Tipo de Operação'),
       (1015218,'anulado','bool','Se foi anulado','f', 'Anulado',1,'f','f','f',5,'text','Anulado');

insert into db_sysarqcamp
values(1011108,1011345,1,0),
      (1011108,1015210,2,0),
      (1011108,1015211,3,0),
      (1011108,1015212,4,0),
      (1011108,1015213,5,0),
      (1011108,1015214,6,0),
      (1011108,1015215,7,0),
      (1011108,556,8,0),
      (1011108,1015216,9,0),
      (1011108,1015217,10,0),
      (1011108,1015218,11,0);

insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011108,1011345,1,1011345);

insert into db_sysforkey
values (1011108,1015210,1,1024,0),
       (1011108,1015211,1,42,0),
       (1011108,1015212,1,212,0),
       (1011108,1015213,1,212,0),
       (1011108,1015214,1,806,0);

insert into db_syssequencia values(1001140, 'planilha_gerar_slips_cobertura_extra_id_seq', 1, 1, 9223372036854775807, 1, 1);
update db_sysarqcamp set codsequencia = 1001140 where codarq = 1011108 and codcam = 1011345;
insert into db_sysindices values(1008880,'planilha_gerar_slips_cobertura_extra_anludo_in',1011108,'0');
insert into db_syscadind values(1008880,1015218,1);

SQL
        );
    }

    private function upEscrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
create table caixa.planilha_gerar_slips_cobertura_extra (
     id serial primary key,
     placaixarec_id integer not null,
     credor integer not null,
     creditar integer not null,
     debitar integer not null,
     historico integer not null,
     cp char(3) default '000',
     valor numeric(15,2) not null,
     tipo_pagamento integer default 3,
     tipo_operacao integer default 17,
     anulado boolean default false,
     foreign key (placaixarec_id) references caixa.placaixarec on delete cascade,
     foreign key (credor) references protocolo.cgm on delete cascade,
     foreign key (creditar) references caixa.saltes on delete cascade,
     foreign key (debitar) references caixa.saltes on delete cascade,
     foreign key (historico) references contabilidade.conhist on delete cascade
);

CREATE INDEX planilha_gerar_slips_cobertura_extra_anludo_in ON planilha_gerar_slips_cobertura_extra(anulado);
SQL
        );
    }

    private function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_sysprikey where codarq = 1011108;
delete from db_sysforkey where codarq = 1011108;
delete from db_syssequencia where codsequencia = 1001140;
delete from db_sysarqcamp where codarq = 1011108;
delete from db_syscampo where codcam in (1015211,1015212,1015213,1015214,1015215,1015216,1015217,1015218);
delete from db_sysindices where codind = 1008880;
delete from db_syscadind where codind = 1008880;
delete from db_sysarqmod where codarq = 1011108;
delete from db_sysarquivo where codarq = 1011108;
SQL
        );
    }

    private function downEscrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop table if exists caixa.planilha_gerar_slips_cobertura_extra;
SQL
        );
    }
}
