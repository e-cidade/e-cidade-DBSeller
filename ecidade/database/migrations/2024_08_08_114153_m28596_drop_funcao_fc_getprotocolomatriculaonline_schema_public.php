<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28596DropFuncaoFcGetprotocolomatriculaonlineSchemaPublic extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    
    public function up()
    {
        DB::statement(<<<SQL
DROP FUNCTION IF EXISTS public.fc_getprotocolomatriculaonline;
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
        DB::statement(<<<SQL
CREATE OR REPLACE FUNCTION plugins.fc_getprotocolomatriculaonline(int)
    RETURNS VARCHAR AS
$$
DECLARE
    fase               alias for $1;
    codigos_protocolos integer[];
    protocolo_atual    int;
    ultimo_protocolo   int;
    ano_fase           VARCHAR;
    v_protocolos       RECORD;
BEGIN
    FOR v_protocolos IN SELECT mo12_protocolo FROM plugins.basefase WHERE mo12_fase = fase LOOP
            protocolo_atual := (split_part(v_protocolos.mo12_protocolo::text, '-'::text, 2)::int);
            codigos_protocolos := array_append(codigos_protocolos, protocolo_atual);
        END LOOP;
    ultimo_protocolo := COALESCE((select max(codigo) from (select unnest(codigos_protocolos) as codigo) as x), 0);
    ano_fase := (select mo04_anousu::VARCHAR from plugins.fase f where f.mo04_codigo = fase);
    return lpad(fase::VARCHAR, 4, '0') || '-' || lpad((ultimo_protocolo+1)::VARCHAR, 5, '0') || '-' || ano_fase;
END;
$$ LANGUAGE plpgsql;
SQL
);
    }
}
