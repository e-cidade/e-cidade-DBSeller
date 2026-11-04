<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27689AuditoriaAdicionaFila extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
CREATE or REPLACE FUNCTION configuracoes.fc_auditoria_adiciona_acount_fila(INTEGER, INTEGER)
RETURNS void
AS $$
    /* Controle de concorrencia para evitar execucoes simultaneas */
    SELECT pg_advisory_xact_lock(-123456789);

    /* Salva o proximo valor da sequence para ser usada posteriormente nos UPDATEs */
    SELECT fc_putsession('configuracoes.db_auditoria_migracao_sequencial_seq',
        (SELECT last_value+1 FROM configuracoes.db_auditoria_migracao_sequencial_seq)::text);

    /* Gera e insere os lotes de acordo com o tamanho passado por parametro */
    INSERT	INTO configuracoes.db_auditoria_migracao (sequencial, id_acount_ini, id_acount_fim, status)
    SELECT	NEXTVAL('db_auditoria_migracao_sequencial_seq'),
            minimo + (soma * id) - soma + (
            CASE
                WHEN id = 1
                    THEN 0
                    ELSE 1
                END
            ) AS id_acount_ini,
            CASE
                WHEN (minimo + (soma * id)) > maximo
                THEN maximo
                ELSE (minimo + (soma * id))
            END AS id_acount_fim,
            cast('NAO INICIADO' AS TEXT)
    FROM 	(SELECT	(SELECT	min(id_acount)
                    FROM	ONLY db_acount
                    WHERE	id_acount > coalesce($1, 0)) AS minimo,
                    (SELECT	max(id_acount)
                    FROM	ONLY db_acount
                    WHERE	id_acount > coalesce($1, 0)) AS maximo,
                    id,
                    $2 AS soma
            FROM	generate_series(1, (
                        SELECT	ceil((max(id_acount) - min(id_acount) + 1) / $2::float8)
                        FROM	ONLY db_acount
                        WHERE	id_acount > coalesce($1, 0)
                    )::integer) AS id LIMIT 10) AS x
    WHERE (minimo+maximo::bigint) > 0;

    /* Finaliza LOTES que por acaso nao existam nenhum registro na db_acount */
    UPDATE 	db_auditoria_migracao
    SET 	status = 'FINALIZADO',
            inicio = now(),
            fim = clock_timestamp(),
            registros_processados = 0,
            observacoes = 'LOTE DESCARTADO PELA PL fc_auditoria_adiciona_acount_fila() POIS NAO EXISTE NENHUM db_acount PARA MIGRAR'
    WHERE 	sequencial >= fc_getsession('configuracoes.db_auditoria_migracao_sequencial_seq')::integer
    AND 	status = 'NAO INICIADO'
    AND 	NOT EXISTS (SELECT 1 FROM db_acount WHERE id_acount BETWEEN id_acount_ini AND id_acount_fim);

    /* Seta Data/Hora inicio e fim dos lotes */
    UPDATE	db_auditoria_migracao
    SET		datahora_ini = COALESCE((dhi).datahora_ini, NOW() - interval '6 months'),
            datahora_fim = COALESCE((dhi).datahora_fim, NOW()),
            instit       = COALESCE((dhi).instit, (SELECT array_agg(codigo) FROM db_config))
    FROM	(SELECT	sequencial,
                    fc_auditoria_busca_datahora_e_instit((current_date - interval '6 months')::date, id_acount_ini, id_acount_fim) AS dhi
            FROM	db_auditoria_migracao
            WHERE	sequencial >= fc_getsession('configuracoes.db_auditoria_migracao_sequencial_seq')::integer
            AND 	status = 'NAO INICIADO') AS x
    WHERE	db_auditoria_migracao.sequencial = x.sequencial;
$$
LANGUAGE sql;

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
CREATE or REPLACE FUNCTION configuracoes.fc_auditoria_adiciona_acount_fila(INTEGER, INTEGER)
RETURNS void
AS $$
    /* Controle de concorrencia para evitar execucoes simultaneas */
    SELECT pg_advisory_xact_lock(-123456789);

    /* Salva o proximo valor da sequence para ser usada posteriormente nos UPDATEs */
    SELECT fc_putsession('configuracoes.db_auditoria_migracao_sequencial_seq',
        (SELECT last_value+1 FROM configuracoes.db_auditoria_migracao_sequencial_seq)::text);

    /* Gera e insere os lotes de acordo com o tamanho passado por parametro */
    INSERT	INTO configuracoes.db_auditoria_migracao (sequencial, id_acount_ini, id_acount_fim, status)
    SELECT	NEXTVAL('db_auditoria_migracao_sequencial_seq'),
            minimo + (soma * id) - soma + (
            CASE
                WHEN id = 1
                    THEN 0
                    ELSE 1
                END
            ) AS id_acount_ini,
            CASE
                WHEN (minimo + (soma * id)) > maximo
                THEN maximo
                ELSE (minimo + (soma * id))
            END AS id_acount_fim,
            cast('NAO INICIADO' AS TEXT)
    FROM 	(SELECT	(SELECT	min(id_acount)
                    FROM	ONLY db_acount
                    WHERE	id_acount > coalesce($1, 0)) AS minimo,
                    (SELECT	max(id_acount)
                    FROM	ONLY db_acount
                    WHERE	id_acount > coalesce($1, 0)) AS maximo,
                    id,
                    $2 AS soma
            FROM	generate_series(1, (
                        SELECT	ceil((max(id_acount) - min(id_acount) + 1) / $2::float8)
                        FROM	ONLY db_acount
                        WHERE	id_acount > coalesce($1, 0)
                    )::integer) AS id LIMIT 10) AS x
    WHERE (minimo+maximo) > 0;

    /* Finaliza LOTES que por acaso nao existam nenhum registro na db_acount */
    UPDATE 	db_auditoria_migracao
    SET 	status = 'FINALIZADO',
            inicio = now(),
            fim = clock_timestamp(),
            registros_processados = 0,
            observacoes = 'LOTE DESCARTADO PELA PL fc_auditoria_adiciona_acount_fila() POIS NAO EXISTE NENHUM db_acount PARA MIGRAR'
    WHERE 	sequencial >= fc_getsession('configuracoes.db_auditoria_migracao_sequencial_seq')::integer
    AND 	status = 'NAO INICIADO'
    AND 	NOT EXISTS (SELECT 1 FROM db_acount WHERE id_acount BETWEEN id_acount_ini AND id_acount_fim);

    /* Seta Data/Hora inicio e fim dos lotes */
    UPDATE	db_auditoria_migracao
    SET		datahora_ini = COALESCE((dhi).datahora_ini, NOW() - interval '6 months'),
            datahora_fim = COALESCE((dhi).datahora_fim, NOW()),
            instit       = COALESCE((dhi).instit, (SELECT array_agg(codigo) FROM db_config))
    FROM	(SELECT	sequencial,
                    fc_auditoria_busca_datahora_e_instit((current_date - interval '6 months')::date, id_acount_ini, id_acount_fim) AS dhi
            FROM	db_auditoria_migracao
            WHERE	sequencial >= fc_getsession('configuracoes.db_auditoria_migracao_sequencial_seq')::integer
            AND 	status = 'NAO INICIADO') AS x
    WHERE	db_auditoria_migracao.sequencial = x.sequencial;
$$
LANGUAGE sql;

SQL
        );
    }
}
