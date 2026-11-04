<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25511AcertoEmBase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
        UPDATE protocolo.procandam SET p61_despacho = '' WHERE p61_despacho = 'Criado Processo';
        UPDATE protocolo.procandam SET p61_despacho = 'criado e arquivado' WHERE p61_despacho ~'^Processo [0-9]+ criado$';
        select fc_putsession('DB_anousu','2023');
        select fc_putsession('DB_id_usuario','1');
        select fc_putsession('DB_instit','1');
        select fc_putsession('DB_coddepto','1');
        UPDATE protocolo.protprocesso SET p58_despacho = '' WHERE p58_despacho = 'Criado Processo';
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
        //
    }
}
