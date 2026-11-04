<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28921AdicionaCampoPispasepEsocialrubricas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upFc_remove_dicionario();
        $this->upDicionarioPisPasep();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downEstrutura();
    }

    private function upFc_remove_dicionario()
    {
        $sql = <<<SQL
            CREATE OR REPLACE FUNCTION fc_remove_dicionario_tabela(text, text)
                    RETURNS void AS
                $$
                DECLARE
                    sysarquivo INTEGER;
                    sysmodulo  INTEGER;
                    syscampos  INTEGER[];
                    relid      REGCLASS;
                BEGIN
                    -- Salva o "relation id" do catálogo do PostgreSQL
                    relid := format('%I.%I', $1, $2)::regclass;

                    SELECT a.codarq
                    INTO sysarquivo
                    FROM configuracoes.db_sysarquivo a
                            JOIN configuracoes.db_sysarqmod am ON am.codarq = a.codarq
                            JOIN configuracoes.db_sysmodulo m ON am.codmod = m.codmod
                    WHERE regexp_replace(lower(to_ascii(nomemod)), '[^A-Za-z]', '', 'g') = $1
                    AND a.nomearq = $2;

                    IF sysarquivo IS NULL THEN
                        RAISE INFO 'Tabela %.% nao encontrada no dicionario de dados!', $1, $2;
                    END IF;

                    RAISE DEBUG 'sysarquivo: %', sysarquivo;

                    SELECT codmod
                    INTO sysmodulo
                    FROM configuracoes.db_sysmodulo
                    WHERE regexp_replace(lower(to_ascii(nomemod)), '[^A-Za-z]', '', 'g') = $1;

                    SELECT array_agg(codcam)
                    INTO syscampos
                    FROM configuracoes.db_sysarqcamp
                    WHERE codarq = sysarquivo;

                    -- 0) Cria tabelas temporárias para salvar conteúdo anterior
                    RAISE DEBUG 'Dropando tabelas temporarias';

                    DROP TABLE IF EXISTS tmp_processa;
                    DROP TABLE IF EXISTS tmp_sysarquivo;
                    DROP TABLE IF EXISTS tmp_syscampo;
                    DROP TABLE IF EXISTS tmp_sysindices;
                    DROP TABLE IF EXISTS tmp_syscadind;
                    DROP TABLE IF EXISTS tmp_syscampodef;
                    DROP TABLE IF EXISTS tmp_syscampodep;
                    DROP TABLE IF EXISTS tmp_sysforkey;
                    DROP TABLE IF EXISTS tmp_acount;
                    DROP TABLE IF EXISTS tmp_sysclasses;
                    DROP TABLE IF EXISTS tmp_iptutabelasdepend;
                    DROP TABLE IF EXISTS tmp_iptutabelasconfigcampochave;
                    DROP TABLE IF EXISTS tmp_iptutabelasconfigcampocorrecao;
                    DROP TABLE IF EXISTS tmp_iptutabelasconfigvirada;
                    DROP TABLE IF EXISTS tmp_iptutabelasconfig;
                    DROP TABLE IF EXISTS tmp_iptutabelas;

                    RAISE DEBUG 'Criando tabelas temporarias';

                    CREATE TEMP TABLE tmp_processa AS
                    SELECT *
                    FROM db_processa
                    WHERE codarq = sysarquivo;

                    CREATE TEMP TABLE tmp_sysarquivo AS
                    SELECT *, pg_catalog.obj_description(relid, 'pg_class') AS comentario
                    FROM db_sysarquivo
                    WHERE codarq = sysarquivo;

                    CREATE TEMP TABLE tmp_syscampo AS
                    SELECT *,
                        pg_catalog.col_description(relid, (SELECT attnum
                                                            FROM pg_attribute
                                                            WHERE attrelid = relid AND attname = nomecam)) AS comentario
                    FROM db_syscampo
                    WHERE codcam = ANY (syscampos)
                    AND NOT EXISTS (SELECT 1
                                    FROM configuracoes.db_sysarqcamp ac
                                    WHERE ac.codcam = db_syscampo.codcam
                                        AND ac.codarq IS DISTINCT FROM sysarquivo);

                    CREATE TEMP TABLE tmp_syscampodef AS
                    SELECT db_syscampodef.*
                    FROM db_syscampodef
                            INNER JOIN db_syscampo ON db_syscampo.codcam = db_syscampodef.codcam
                    WHERE db_syscampodef.codcam = ANY (syscampos);

                    CREATE TEMP TABLE tmp_syscampodep AS
                    SELECT db_syscampodep.*
                    FROM db_syscampodep
                            INNER JOIN db_syscampo ON db_syscampo.codcam = db_syscampodep.codcam
                    WHERE db_syscampodep.codcam = ANY (syscampos);

                    CREATE TEMP TABLE tmp_sysforkey AS
                    SELECT db_sysforkey.*
                    FROM db_sysforkey
                            INNER JOIN db_syscampo ON db_syscampo.codcam = db_sysforkey.codcam
                    WHERE db_sysforkey.codcam = ANY (syscampos)
                    AND db_sysforkey.codarq <> sysarquivo;

                    CREATE TEMP TABLE tmp_acount AS
                    SELECT db_acount.*
                    FROM db_acount
                    WHERE codarq = sysarquivo;

                    CREATE TEMP TABLE tmp_sysclasses AS
                    SELECT db_sysclasses.*
                    FROM db_sysclasses
                    WHERE codarq = sysarquivo;

                    CREATE TEMP TABLE tmp_syscadind AS
                    SELECT db_syscadind.*
                    FROM db_syscadind
                    WHERE codcam = ANY (syscampos);

                    CREATE TEMP TABLE tmp_sysindices AS
                    SELECT db_sysindices.*
                    FROM db_sysindices
                    WHERE codarq = sysarquivo;

                    CREATE TEMP TABLE tmp_iptutabelasdepend AS
                    SELECT iptutabelasdepend.*
                    FROM iptutabelasdepend
                    WHERE j128_iptutabelas in (SELECT j121_sequencial FROM iptutabelas WHERE j121_codarq = sysarquivo);

                    CREATE TEMP TABLE tmp_iptutabelasconfigcampochave AS
                    SELECT iptutabelasconfigcampochave.*
                    FROM iptutabelasconfigcampochave
                    WHERE j124_iptutabelasconfig in (SELECT j122_sequencial
                                                    FROM iptutabelas
                                                            JOIN iptutabelasconfig ON j122_iptutabelas = j121_sequencial
                                                    WHERE j121_codarq = sysarquivo);

                    CREATE TEMP TABLE tmp_iptutabelasconfigcampocorrecao AS
                    SELECT iptutabelasconfigcampocorrecao.*
                    FROM iptutabelasconfigcampocorrecao
                    WHERE j123_iptutabelasconfig in (SELECT j122_sequencial
                                                    FROM iptutabelas
                                                            JOIN iptutabelasconfig ON j122_iptutabelas = j121_sequencial
                                                    WHERE j121_codarq = sysarquivo);

                    CREATE TEMP TABLE tmp_iptutabelasconfigvirada AS
                    SELECT iptutabelasconfigvirada.*
                    FROM iptutabelasconfigvirada
                    WHERE j129_iptutabelasconfig in (SELECT j122_sequencial
                                                    FROM iptutabelas
                                                            JOIN iptutabelasconfig ON j122_iptutabelas = j121_sequencial
                                                    WHERE j121_codarq = sysarquivo);

                    CREATE TEMP TABLE tmp_iptutabelasconfig AS
                    SELECT iptutabelasconfig.*
                    FROM iptutabelasconfig
                    WHERE j122_iptutabelas in (SELECT j121_sequencial FROM iptutabelas WHERE j121_codarq = sysarquivo);

                    CREATE TEMP TABLE tmp_iptutabelas AS
                    SELECT iptutabelas.*
                    FROM iptutabelas
                    WHERE j121_codarq = sysarquivo;

                    -- 1) Remove tudo
                    RAISE DEBUG 'Deletando registros das tabelas auxiliares';

                    DELETE
                    FROM iptutabelasconfigcampochave
                    WHERE j124_iptutabelasconfig in (SELECT j122_sequencial
                                                    FROM iptutabelas
                                                            JOIN iptutabelasconfig ON j122_iptutabelas = j121_sequencial
                                                    WHERE j121_codarq = sysarquivo);
                    DELETE
                    FROM iptutabelasconfigcampocorrecao
                    WHERE j123_iptutabelasconfig in (SELECT j122_sequencial
                                                    FROM iptutabelas
                                                            JOIN iptutabelasconfig ON j122_iptutabelas = j121_sequencial
                                                    WHERE j121_codarq = sysarquivo);
                    DELETE
                    FROM iptutabelasconfigvirada
                    WHERE j129_iptutabelasconfig in (SELECT j122_sequencial
                                                    FROM iptutabelas
                                                            JOIN iptutabelasconfig ON j122_iptutabelas = j121_sequencial
                                                    WHERE j121_codarq = sysarquivo);
                    DELETE
                    FROM cadastro.iptutabelasconfig
                    WHERE j122_iptutabelas in
                        (SELECT j121_sequencial FROM iptutabelas WHERE j121_codarq = sysarquivo);
                    DELETE
                    FROM cadastro.iptutabelasdepend
                    WHERE j128_iptutabelas in
                        (SELECT j121_sequencial FROM iptutabelas WHERE j121_codarq = sysarquivo);
                    DELETE
                    FROM cadastro.iptutabelasdepend
                    WHERE j128_iptutabelasdepend in
                        (SELECT j121_sequencial FROM iptutabelas WHERE j121_codarq = sysarquivo);
                    DELETE FROM cadastro.iptutabelas WHERE j121_codarq = sysarquivo;
                    DELETE FROM configuracoes.db_sysclasses WHERE codarq = sysarquivo;
                    DELETE FROM configuracoes.db_acount WHERE codarq = sysarquivo;
                    DELETE FROM configuracoes.db_sysarqmod WHERE codarq = sysarquivo AND codmod = sysmodulo;
                    DELETE FROM configuracoes.db_sysforkey WHERE codarq = sysarquivo;
                    DELETE FROM configuracoes.db_sysprikey WHERE codarq = sysarquivo;
                    DELETE FROM configuracoes.db_syscadind WHERE codcam = ANY (syscampos);
                    DELETE FROM configuracoes.db_sysindices WHERE codarq = sysarquivo;
                    DELETE FROM configuracoes.db_sysarqcamp WHERE codarq = sysarquivo AND codcam = ANY (syscampos);
                    DELETE FROM configuracoes.db_syscampodef WHERE codcam = ANY (syscampos);
                    DELETE FROM configuracoes.db_syscampodep WHERE codcam = ANY (syscampos);
                    DELETE FROM configuracoes.db_processa WHERE codarq = sysarquivo;
                    DELETE
                    FROM configuracoes.db_syscampo c
                    WHERE c.codcam = ANY (syscampos)
                    AND NOT EXISTS (SELECT 1
                                    FROM configuracoes.db_sysarqcamp ac
                                    WHERE ac.codcam = c.codcam
                                        AND ac.codarq IS DISTINCT FROM sysarquivo);
                    DELETE FROM configuracoes.db_sysarquivo WHERE codarq = sysarquivo;

                    RETURN;
                END;
                $$
                    LANGUAGE plpgsql;

SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    private function upDicionarioPisPasep()
    {
        $sql = <<<SQL
                ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
                ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

                ALTER TABLE esocial.esocialrubricas ADD COLUMN eso26_codincpispasep varchar(3);

                SELECT fc_atualiza_dicionario_apartir_comentario('table column',
                   'esocial.esocialrubricas.eso26_codincpispasep',
                   '{ "descricao": "Código de incidência da rubrica para o PIS/PASEP sobre a folha de salários",
                      "rotulo": "Incidência de PIS/PASEP",
                      "rotulorel": "Incidência de PIS/PASEP",
                      "maiusculo": false,
                      "autocompl": false,
                      "aceitatipo": 0,
                      "tamanho": 3,
                      "tipoobj": "text"
                    }') ;

                ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
                ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;

SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    private function downEstrutura()
    {
        $sql = <<<SQL
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

        ALTER TABLE esocial.esocialrubricas DROP COLUMN eso26_codincpispasep;

        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}