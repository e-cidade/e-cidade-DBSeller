<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25423IdentificadorProcessoEncryptado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
        select fc_putsession('DB_anousu','2023');
        select fc_putsession('DB_id_usuario','1');
        select fc_putsession('DB_instit','1');
        select fc_putsession('DB_coddepto','1');
        CREATE EXTENSION IF NOT EXISTS  pgcrypto;
        alter table protocolo.protprocesso add column p58_codproc_crypt varchar(250);
        update  protocolo.protprocesso  set  p58_codproc_crypt = encode(digest(p58_codproc::varchar, 'sha1'),'hex');
        alter table protocolo.protprocesso alter column p58_codproc_crypt SET default encode(digest(CURRVAL('protprocesso_p58_codproc_seq')::varchar, 'sha1'),'hex');
        ALTER TABLE protocolo.protprocesso ADD UNIQUE (p58_codproc_crypt);
        ALTER TABLE protocolo.protprocesso ALTER COLUMN p58_codproc SET DEFAULT  nextval('protprocesso_p58_codproc_seq');
        select public.fc_set_pg_search_path();
        select configuracoes.fc_auditoria_cria_funcao('protocolo.protprocesso')
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
       alter table protocolo.protprocesso drop column p58_codproc_crypt
SQL
        );
    }
}
