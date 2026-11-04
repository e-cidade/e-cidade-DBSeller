<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29305LinkComColunaCboocupacaoEMobase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->checkSchema();
        if ($this->checkSchema() == true) {
            $this->dropConstraint();
            $this->upConstraint();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if ($this->checkSchema() == true) {
            $this->dropConstraint();
        }
    }

    public function checkSchema()
    {
        $schemaMobase = DB::select("
            select table_name
            from information_schema.tables
            where	table_schema = 'plugins'
              and table_name = 'mobase'
        ");

        if (count($schemaMobase) > 0) {
            $schemaColumn = DB::select("
            SELECT column_name
                FROM information_schema.columns
                WHERE
                    table_name='mobase'
                    and column_name='mo01_ocupacaopai'
                    or column_name='mo01_ocupacaomae'
                    or column_name='mo01_ocupacaoresp';
            ");

            if (count($schemaColumn) == 3) {
                return true;
            }
        }
        return false;
    }

    public function dropConstraint()
    {
        DB::connection()->getPdo()->exec(<<<SQL
        ALTER TABLE plugins.mobase
            DROP CONSTRAINT IF EXISTS mobase_ocupacaopai_fk,
            DROP CONSTRAINT IF EXISTS mobase_ocupacaomae_fk,
            DROP CONSTRAINT IF EXISTS mobase_ocupacaoresp_fk;
SQL
        );
    }

    public function upConstraint()
    {
        DB::connection()->getPdo()->exec(<<<SQL
        ALTER TABLE plugins.mobase
        ADD CONSTRAINT mobase_ocupacaopai_fk
            FOREIGN KEY (mo01_ocupacaopai) REFERENCES escola.cboocupacao(mo304_codigo),
        ADD CONSTRAINT mobase_ocupacaomae_fk
            FOREIGN KEY (mo01_ocupacaomae) REFERENCES escola.cboocupacao(mo304_codigo),
        ADD CONSTRAINT mobase_ocupacaoresp_fk
            FOREIGN KEY (mo01_ocupacaoresp) REFERENCES escola.cboocupacao(mo304_codigo);
SQL
        );
    }
}
