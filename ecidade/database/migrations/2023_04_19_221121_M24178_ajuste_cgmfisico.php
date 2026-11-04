<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24178AjusteCgmfisico extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL

           set work_mem = '5GB';

           select fc_putsession('__disable_audit__', 'true');
           select fc_putsession('__disable_audit_protocolo_cgmfisico__', 'true');

           create temp table cgmfisico_temp as
               select min(cgmfisico.z04_sequencial) as z04_sequencial,
                      cgmfisico.z04_numcgm,
                      case when max(cgmfisico.z04_rhcbo) > 0
                           then max(cgmfisico.z04_rhcbo)
                           else min(cgmfisico.z04_rhcbo)
                      end as z04_rhcbo,
                      case when max(coalesce(cgmfisico.z04_nomesocial,'')) <> ''
                           then max(cgmfisico.z04_nomesocial)
                           else min(cgmfisico.z04_nomesocial)
                      end as z04_nomesocial,
                      case when max(coalesce(cgmfisico.z04_paisnascimento,0)) > 0
                           then max(cgmfisico.z04_paisnascimento)
                           else min(cgmfisico.z04_paisnascimento)
                      end as z04_paisnascimento,
                      case when max(coalesce(cgmfisico.z04_paisnacionalidade,0)) > 0
                           then max(cgmfisico.z04_paisnacionalidade)
                           else min(cgmfisico.z04_paisnacionalidade)
                      end as z04_paisnacionalidade
               from cgmfisico
               group by cgmfisico.z04_numcgm;

           truncate cgmfisico;

           insert into cgmfisico select * from cgmfisico_temp;

           create temp table cgmendereco_temp as
              select max(cgmendereco.z07_sequencial) as sequencial,
                     cgmendereco.z07_numcgm as numcgm,
                     cgmendereco.z07_tipo as tipo
              from cgmendereco
              group by cgmendereco.z07_numcgm, cgmendereco.z07_tipo having count(*) > 1;

           delete from cgmendereco
              using cgmendereco_temp
              where cgmendereco.z07_numcgm = cgmendereco_temp.numcgm
                and cgmendereco.z07_tipo = cgmendereco_temp.tipo
                and cgmendereco.z07_sequencial <> cgmendereco_temp.sequencial;

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
        return true;
    }
}
