<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24516Estrutura extends Migration
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

    public function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_sysarquivo
values (1011105, 'transferencia_cobertura_extra', 'Armazena os slips que foram gerados para realizar transferência de cobertura financeira', 'k192', '2023-06-23', 'transferência de cobertura financeira', 0, 'f', 'f', 'f', 'f' ),
       (1011106, 'transferencia_cobertura_extra_empagemovslips', 'Slips de Apropriados via Retenção', 'k215', '2023-06-23', 'Apropriados via Retenção', 0, 'f', 'f', 'f', 'f' ),
       (1011107, 'transferencia_cobertura_extra_placaixarec', 'Vincula as planilhas com a transferência de cobertura financeira ', 'k216', '2023-06-23', 'Apropriados via Planilhas', 0, 'f', 'f', 'f', 'f' );

insert into db_sysarqmod
values (5,1011105),
       (5,1011106),
       (5,1011107);

insert into db_syscampo
values (1015207,'slip_id','int4','Vínculo com o slip','0', 'Slip',10,'f','f','f',1,'text','Slip'),
       (1015208,'transferencia_cobertura_extra_id','int4','Vínculo com o slip de transferência de cobertura financeira','0', 'Transferência de cobertura financeira',10,'f','f','f',1,'text','Transferência de cobertura financeira'),
       (1015209,'empagemovslips_id','int4','Vínculo com o movimento da retenção','0', 'Movimento da Retenção',10,'f','f','f',1,'text','Movimento da Retenção'),
       (1015210,'placaixarec_id','int4','Item da Planilha','0', 'Item da Planilha',10,'f','f','f',1,'text','Item da Planilha');

insert into db_sysarqcamp
values (1011105,1011345,1,0),
       (1011105,1015207,2,0),
       (1011106,1011345,1,0),
       (1011106,1015208,2,0),
       (1011106,1015209,3,0),
       (1011107,1011345,1,0),
       (1011107,1015208,2,0),
       (1011107,1015210,3,0);

insert into db_sysprikey (codarq,codcam,sequen,camiden)
values (1011105,1011345,1,1011345),
       (1011106,1011345,1,1011345),
       (1011107,1011345,1,1011345);

insert into db_sysforkey
values (1011105,1015207,1,196,0),
       (1011106,1015208,1,1011105,0),
       (1011106,1015209,1,2174,0),
       (1011107,1015208,1,1011105,0);

insert into db_syssequencia
values (1001137, 'transferencia_cobertura_extra_id_seq', 1, 1, 9223372036854775807, 1, 1),
       (1001138, 'transferencia_cobertura_extra_empagemovslips_id_seq', 1, 1, 9223372036854775807, 1, 1),
       (1001139, 'transferencia_cobertura_extra_placaixarec_id_seq', 1, 1, 9223372036854775807, 1, 1);

update db_sysarqcamp set codsequencia = 1001137 where codarq = 1011105 and codcam = 1011345;
update db_sysarqcamp set codsequencia = 1001138 where codarq = 1011106 and codcam = 1011345;
update db_sysarqcamp set codsequencia = 1001139 where codarq = 1011107 and codcam = 1011345;
SQL
        );
    }


    public function upEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
create table caixa.transferencia_cobertura_extra (
     id serial primary key,
     slip_id integer not null,
     foreign key (slip_id) references caixa.slip on delete cascade
);

create table caixa.transferencia_cobertura_extra_empagemovslips (
   id serial primary key,
   transferencia_cobertura_extra_id integer not null,
   empagemovslips_id integer not null,
   foreign key (transferencia_cobertura_extra_id) references caixa.transferencia_cobertura_extra on delete cascade,
   foreign key (empagemovslips_id) references caixa.empagemovslips on delete cascade
);

create table caixa.transferencia_cobertura_extra_placaixarec(
   id serial primary key,
   transferencia_cobertura_extra_id integer not null,
   placaixarec_id integer not null,
   foreign key (transferencia_cobertura_extra_id) references caixa.transferencia_cobertura_extra on delete cascade,
   foreign key (placaixarec_id) references caixa.placaixarec on delete cascade
);
SQL
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_syssequencia where codsequencia in (1001137, 1001138, 1001139);
delete from db_sysarqcamp where codarq in (1011105, 1011106, 1011107);
delete from db_sysprikey where codarq in (1011105, 1011106, 1011107);
delete from db_sysforkey where codarq in (1011105, 1011106, 1011107);
delete from db_syscampo where codcam in (1015207, 1015208, 1015209, 1015210);
delete from db_sysarqmod where codarq in (1011105, 1011106, 1011107);
delete from db_sysarquivo where codarq in (1011105, 1011106, 1011107);
SQL
        );

        DB::connection()->getPdo()->exec(<<<SQL
drop table if exists caixa.transferencia_cobertura_extra_placaixarec;
drop table if exists caixa.transferencia_cobertura_extra_empagemovslips;
drop table if exists caixa.transferencia_cobertura_extra;
SQL
        );
    }
}
