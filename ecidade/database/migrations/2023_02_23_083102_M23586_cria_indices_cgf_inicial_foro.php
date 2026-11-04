<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M23586CriaIndicesCgfInicialForo extends Migration
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

    private function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL

insert into db_sysindices values(1008851,'certter_parcel_in',106,'0');
insert into db_syscadind values(1008851,563,1);
insert into db_sysindices values(1008852,'processoforoinicial_inicial_anulado_in',3070,'0');
insert into db_syscadind values(1008852,17351,1);
insert into db_syscadind values(1008852,17354,2);
update db_sysindices set nomeind = 'processoforopartilha_processoforo_in',campounico = '0' where codind = 3528;
delete from db_syscadind where codind = 3528 and codcam = 18259;
insert into db_syscadind values(3528,18259,1);

SQL
        );
    }

    private function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL


delete from db_syscadind  where codind = 1008851;
delete from db_sysindices where codind = 1008851 and nomeind = 'certter_parcel_in';

delete from db_syscadind  where codind = 1008852 and codcam in (17351, 17354);
delete from db_sysindices where codind = 1008852 and nomeind = 'processoforoinicial_inicial_anulado_in';

delete from db_syscadind  where codind = 3528 and codcam = 18259;
delete from db_sysindices where codind = 3528 and nomeind = 'processoforopartilha_processoforo_in';

SQL
        );
    }


    private function upEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL

drop index if exists processoforo_in;
create index if not exists certter_parcel_in on divida.certter (v14_parcel);
create index if not exists processoforoinicial_inicial_anulado_in on juridico.processoforoinicial(v71_inicial, v71_anulado);
create index if not exists processoforopartilha_v76_processoforo_in on juridico.processoforopartilha(v76_processoforo);

SQL
        );
    }

    private function downEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL

drop index if exists certter_parcel_in;
drop index if exists processoforoinicial_inicial_anulado_in;
drop index if exists processoforopartilha_v76_processoforo_in;

SQL
        );
    }
}
