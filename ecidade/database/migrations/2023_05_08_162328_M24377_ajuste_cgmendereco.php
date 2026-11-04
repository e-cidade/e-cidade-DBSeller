<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24377AjusteCgmendereco extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL

           set work_mem = '4GB';

           select fc_putsession('__disable_audit__', 'true');
           select fc_putsession('__disable_audit_protocolo_cgmfisico__', 'true');

           update cgm set z01_nome = z01_nome
              where z01_numcgm in (select z01_numcgm
                                   from cgm left join cgmendereco on z07_numcgm = z01_numcgm
                                                                 and z07_tipo = 'P'
                                   where z07_tipo is null);

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
