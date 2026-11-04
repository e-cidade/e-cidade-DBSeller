<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29714AjusteTabelaEtapadestinomatricula extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ($this->checkSchema() == true) {
            $this->ajustarConstrait();
        } else {
            $this->criarTabela();
        }
        $this->criarDicionario();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            DROP TABLE escola.etapadestinomatricula;
SQL
        );
    }

    //

    public function checkSchema()
    {
        $schemaEtapaDestinoMatricula = DB::select("
            select table_name
            from information_schema.tables
            where table_schema = 'escola'
                and table_name = 'etapadestinomatricula'
        ");

        if (count($schemaEtapaDestinoMatricula) > 0) {
            return true;
        }

        return false;
    }

    public function ajustarConstrait()
    {
        DB::connection()->getPdo()->exec(<<<SQL
        ALTER TABLE escola.etapadestinomatricula 
            DROP CONSTRAINT etapadestinomatricula_ed364_matricula_fk;

        ALTER TABLE escola.etapadestinomatricula 
            DROP CONSTRAINT etapadestinomatricula_ed364_etapadestino_fk;

        ALTER TABLE escola.etapadestinomatricula 
            ADD CONSTRAINT etapadestinomatricula_ed364_matricula_fk 
            FOREIGN KEY (ed364_matricula) REFERENCES escola.matricula(ed60_i_codigo) 
            ON DELETE CASCADE;

        ALTER TABLE escola.etapadestinomatricula 
            ADD CONSTRAINT etapadestinomatricula_ed364_etapadestino_fk 
            FOREIGN KEY (ed364_etapadestino) REFERENCES escola.serie(ed11_i_codigo) 
            ON DELETE SET NULL;
SQL
        );
    }

    public function criarTabela()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            CREATE TABLE escola.etapadestinomatricula (
                ed364_codigo SERIAL PRIMARY KEY,
                ed364_matricula INTEGER NOT NULL,
                ed364_etapadestino INTEGER NOT NULL,

                CONSTRAINT etapadestinomatricula_ed364_matricula_fk
                    FOREIGN KEY (ed364_matricula) REFERENCES escola.matricula(ed60_i_codigo)
                    ON DELETE CASCADE,
                CONSTRAINT etapadestinomatricula_ed364_etapadestino_fk
                    FOREIGN KEY (ed364_etapadestino) REFERENCES escola.serie(ed11_i_codigo)
                    ON DELETE SET NULL
            );

            INSERT INTO escola.etapadestinomatricula
            SELECT nextval('etapadestinomatricula_ed364_codigo_seq'), ed60_i_codigo, alunopossib.ed79_i_serie 
                FROM escola.matricula
                JOIN escola.turma ON turma.ed57_i_codigo = matricula.ed60_i_turma
                JOIN escola.calendario ON calendario.ed52_i_codigo = turma.ed57_i_calendario
                JOIN escola.aluno ON aluno.ed47_i_codigo = matricula.ed60_i_aluno
                JOIN escola.alunocurso ON alunocurso.ed56_i_aluno = aluno.ed47_i_codigo
                JOIN escola.alunopossib ON alunopossib.ed79_i_alunocurso = alunocurso.ed56_i_codigo
                WHERE turma.ed57_i_tipoturma = 7 
                    AND ed52_i_ano IN(2023, 2024) 
                    AND ed60_c_situacao = 'MATRICULADO' 
                    AND ed60_c_concluida = 'S';

SQL
        );
    }

    public function criarDicionario()
    {
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;");
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;");

        //Tabela
        DB::statement("COMMENT ON TABLE escola.etapadestinomatricula IS '{
            \"descricao\": \"Tabela para a rotina de alunos SIGA\",
            \"sigla\": \"ed364\",
            \"dataincl\": \"2024-12-20\",
            \"rotulo\": \"etapadestinomatricula\",
            \"tipotabela\": 0,
            \"naolibclass\": false,
            \"naolibfunc\": false,
            \"naolibprog\": false,
            \"naolibform\": false
            }';
        ");

        //Colunas
        DB::statement("COMMENT ON COLUMN escola.etapadestinomatricula.ed364_codigo IS '{
            \"descricao\": \"Serial da tabela\",
            \"rotulo\": \"ed364_codigo\",
            \"rotulorel\": \"ed364_codigo\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 10,
            \"tipoobj\": \"text\"
            }'
        ");

        DB::statement("COMMENT ON COLUMN escola.etapadestinomatricula.ed364_matricula IS '{
            \"descricao\": \"Numero da matricula do aluno\",
            \"rotulo\": \"ed364_matricula\",
            \"rotulorel\": \"ed364_matricula\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 10,
            \"tipoobj\": \"text\"
            }'
        ");

        DB::statement("COMMENT ON COLUMN escola.etapadestinomatricula.ed364_etapadestino IS '{
            \"descricao\": \"Etapa de destino para o aluno\",
            \"rotulo\": \"ed364_etapadestino\",
            \"rotulorel\": \"ed364_etapadestino\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 10,
            \"tipoobj\": \"text\"
            }'
        ");

        DB::unprepared(<<<SQL
SELECT fc_gera_dicionario_apartir_tabela('escola', 'etapadestinomatricula');

ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
SQL
      );
    }
}
