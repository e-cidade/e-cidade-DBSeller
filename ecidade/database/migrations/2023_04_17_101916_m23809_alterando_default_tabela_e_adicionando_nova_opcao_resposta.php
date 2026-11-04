<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23809AlterandoDefaultTabelaEAdicionandoNovaOpcaoResposta extends Migration
{
    /**'
     * Run the migrations.
     *
     * @return voidc,d
     */
    public function up()
    {
        DB::statement("select nextval('avaliacaoperguntaopcao_db104_sequencial_seq'::regclass)");
        DB::statement("select nextval('avaliacaoperguntaopcao_db104_sequencial_seq'::regclass)");
        DB::statement("select nextval('avaliacaoperguntaopcao_db104_sequencial_seq'::regclass)");
        DB::statement("select nextval('avaliacaoperguntaopcao_db104_sequencial_seq'::regclass)");
        DB::statement("select nextval('avaliacaoperguntaopcao_db104_sequencial_seq'::regclass)");
        DB::statement("select nextval('avaliacaoperguntaopcao_db104_sequencial_seq'::regclass)");
        DB::statement("select nextval('avaliacaoperguntaopcao_db104_sequencial_seq'::regclass)");

        DB::statement(
            "
                INSERT INTO habitacao.avaliacaoperguntaopcao
                    VALUES (
                        nextval('avaliacaoperguntaopcao_db104_sequencial_seq'::regclass),
                        3000000,
                        'Estъdio de gravaзгo e ediзгo',
                        'f',
                        'estudio-gravacao-edicao',
                        null,
                        null,
                        'estudio_gravacao_edicao'
                    );
            "
        );

        DB::statement(
            "
                 INSERT INTO habitacao.avaliacaoperguntaopcao
                    VALUES (
                        nextval('avaliacaoperguntaopcao_db104_sequencial_seq'::regclass),
                        4000238,
                        'Tradutor e Intйrprete de Libras para atendimento em outros ambientes da escola que nгo seja sala de aula',
                        't',
                        'tradutor-interprete-libras',
                        0,
                        null,
                        'tradutor_interprete_libras'
                    );

            "
        );

        DB::statement(
            "
                 INSERT INTO habitacao.avaliacaoperguntaopcao
                    VALUES (
                        nextval('avaliacaoperguntaopcao_db104_sequencial_seq'::regclass),
                        4000229,
                        'Materiais pedagуgicos para a educaзгo bilнngue de surdos',
                        'f',
                        'materiais-educacao-bilingue',
                        0,
                        null,
                        'materiais_educacao_bilingue'
                    );

            "
        );

        DB::statement(
            "
                 INSERT INTO habitacao.avaliacaoperguntaopcao
                    VALUES (
                        nextval('avaliacaoperguntaopcao_db104_sequencial_seq'::regclass),
                        3000013,
                        'Educaзгo bilнngue de surdos',
                        'f',
                        'educacao-bilingue-surdos',
                        null,
                        null,
                        'educacao_bilingue_surdos'
                    );

            "
        );

        DB::statement(
            "
                 INSERT INTO habitacao.avaliacaoperguntaopcao
                    VALUES (
                        nextval('avaliacaoperguntaopcao_db104_sequencial_seq'::regclass),
                        3000013,
                        'Educaзгo e Tecnologia de Informaзгo e Comunicaзгo (TIC)',
                        'f',
                        'educacao-tic',
                        null,
                        null,
                        'educacao_tic'
                    );

            "
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("delete from avaliacaoperguntaopcao where db104_identificador = 'estudio-gravacao-edicao'");
        DB::statement("delete from avaliacaoperguntaopcao where db104_identificador = 'tradutor-interprete-libras'");
        DB::statement("delete from avaliacaoperguntaopcao where db104_identificador = 'materiais-educacao-bilingue'");
        DB::statement("delete from avaliacaoperguntaopcao where db104_identificador = 'educacao-bilingue-surdos'");
        DB::statement("delete from avaliacaoperguntaopcao where db104_identificador = 'educacao-tic'");
    }
}
