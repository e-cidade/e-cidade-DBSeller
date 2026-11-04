<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M25663AddColunaArretipo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
        ALTER TABLE arretipo add  k00_taxaadm boolean default false;
        insert into db_syscampo values(1015384,'k00_taxaadm','bool','Permite cobrança de Taxas/Custas administrativa','f', 'Taxa Administrativa',1,'f','f','f',5,'text','Taxa Administrativa');
        insert into db_sysarqcamp values(82,1015384,49,0);
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
        ALTER TABLE arretipo DROP COLUMN k00_taxaadm;
        delete from db_sysarqcamp where codcam = 1015384;
        delete from db_syscampo where codcam = 1015384;
SQL
        );
    }
}
