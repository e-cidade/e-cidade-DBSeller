<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25939RemoveDuplicidadeCgmtipoempresa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $sql = <<<SQL

create table w_tipoempresaduplicada_25939 as
select z03_numcgm as cgm,
       z03_tipoempresa,
       count(z03_tipoempresa),
       (
          select min(z03_sequencial)
            from cgmtipoempresa minimo
           where minimo.z03_numcgm = cgmtipoempresa.z03_numcgm
             and minimo.z03_tipoempresa = cgmtipoempresa.z03_tipoempresa
       ) as minimo
  from cgmtipoempresa
 group by z03_numcgm, z03_tipoempresa
 having count(z03_numcgm) > 1;

delete from cgmtipoempresa
  using w_tipoempresaduplicada_25939
  where z03_sequencial <> minimo
    and z03_numcgm = cgm;

create table w_tipoempresa_duplicado_tipodiferente_25939 as
select z03_numcgm as cgm,
       count(z03_tipoempresa),
       (
          select min(z03_sequencial)
            from cgmtipoempresa minimo
           where minimo.z03_numcgm = cgmtipoempresa.z03_numcgm
       ) as minimo
  from cgmtipoempresa
 group by z03_numcgm
 having count(z03_numcgm) > 1;

create table w_bkp_tipoempresa_duplicado_tipodiferente_25939 as
    select *
     from cgmtipoempresa
     join w_tipoempresa_duplicado_tipodiferente_25939 on z03_sequencial = minimo;

delete from cgmtipoempresa
 using w_tipoempresa_duplicado_tipodiferente_25939 where z03_sequencial = minimo;

drop index cgmtipoempresa_numcgm_in;
CREATE UNIQUE INDEX cgmtipoempresa_numcgm_in ON protocolo.cgmtipoempresa(z03_numcgm);





SQL;
            DB::connection()->getPdo()->exec($sql);


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        $sql = <<<SQL

drop table w_tipoempresaduplicada_25939;
drop table w_tipoempresa_duplicado_tipodiferente_25939;
drop table w_bkp_tipoempresa_duplicado_tipodiferente_25939;
DROP INDEX cgmtipoempresa_numcgm_in;
create index cgmtipoempresa_numcgm_in ON protocolo.cgmtipoempresa(z03_numcgm);



SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
