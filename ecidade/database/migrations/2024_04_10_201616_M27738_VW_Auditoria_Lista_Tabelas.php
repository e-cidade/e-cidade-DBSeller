<?php

use Illuminate\Database\Migrations\Migration;

class M27738VWAuditoriaListaTabelas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            DROP VIEW IF EXISTS configuracoes.vw_auditoria_lista_tabelas;
            CREATE VIEW configuracoes.vw_auditoria_lista_tabelas AS
            SELECT n.nspname AS esquema,
                   r.relname AS nome,
                   r.oid     AS oid
            FROM pg_class r
                     JOIN pg_namespace n ON n.oid = r.relnamespace
                     JOIN db_sysmodulo e ON to_ascii(lower(e.nomemod)) = n.nspname
                                        AND e.ativo is true
            WHERE r.relkind = 'r'

                /* esquemas do PostgreSQL para nao gerar auditoria */
              AND n.nspname !~ '^(pg_|public|information_schema)'

                /* Tabelas do e-cidade para nao gerar auditoria */
              AND r.relname !~ ('(^db_auditoria|^db_acount|^db_logs|^db_usuariosonline|' ||
                                (SELECT string_agg(tabela, '|') FROM configuracoes.fc_auditoria_tabelas_ignorar()) || ')')
            ORDER BY 1, 2;
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
            DROP VIEW IF EXISTS configuracoes.vw_auditoria_lista_tabelas;
            CREATE VIEW configuracoes.vw_auditoria_lista_tabelas AS
            SELECT	n.nspname AS esquema,
            		r.relname AS nome,
            		r.oid     AS oid
            FROM	pg_class r
            		JOIN pg_namespace n ON n.oid = r.relnamespace
            WHERE	r.relkind = 'r'

            		/* esquemas do PostgreSQL para nao gerar auditoria */
            AND		n.nspname !~ '^(pg_|public|information_schema)'

            		/* Tabelas do e-cidade para nao gerar auditoria */
            AND		r.relname !~ ('(^db_auditoria|^db_acount|^db_logs|^db_usuariosonline|' ||
            						(SELECT string_agg(tabela, '|') FROM configuracoes.fc_auditoria_tabelas_ignorar()) || ')')
            ORDER BY 1, 2;
SQL
        );
    }
}
