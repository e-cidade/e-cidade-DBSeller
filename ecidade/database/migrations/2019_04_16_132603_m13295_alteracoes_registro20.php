<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M13295AlteracoesRegistro20 extends Migration
{
    public function up()
    {
        DB::unprepared(<<<SQL
            insert into db_syscampo values(1010412,'ed16_local_funcionamento','int4','Campo para o censo, valores aceitos: 0 – A turma não está em local de funcionamento diferenciado 1 – Sala anexa 2 – Unidade de atendimento socioeducativo 3 – Unidade prisional','0', 'Local de funcionamento diferenciado',10,'t','f','f',1,'text','Local de funcionamento diferenciado');
            insert into db_sysarqcamp values(1010039,1010412,9,0);
            alter table sala add column ed16_local_funcionamento int default null;
SQL
        );
    }

    public function down()
    {
        DB::unprepared(<<<SQL
            alter table sala drop column ed16_local_funcionamento;
            delete from db_sysarqcamp where codcam = 1010412;
            delete from db_syscampo where codcam = 1010412;
SQL
        );
    }
}
