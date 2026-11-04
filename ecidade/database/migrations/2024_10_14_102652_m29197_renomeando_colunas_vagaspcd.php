<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29197RenomeandoColunasVagaspcd extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->updateTable();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->rollbackUpdate();
    }

    private function updateTable()
    {
        if ($this->hasTable()) {
            $columns = DB::select(
                "
                        SELECT column_name FROM information_schema.columns
                            WHERE table_name = 'vagaspcd'
                                AND table_schema = 'plugins'
            "
            );

            Schema::table('plugins.vagaspcd', function (Blueprint $table) use ($columns) {
                foreach ($columns as $column) {
                    if (!($column->column_name == 'created_at' || $column->column_name == 'updated_at')) {
                        // Mudando o prefixo das colunas de mo62 para mo64
                        if (substr($column->column_name, 0, 4) != 'mo64') {
                            $novoNome = str_replace('mo62', 'mo64', $column->column_name);
                            $table->renameColumn($column->column_name, $novoNome);
                        }
                    }
                }
            });

            if (str_contains($this->sequenceName(), 'mo62')) {
                DB::statement("ALTER SEQUENCE vagaspcd_mo62_codigo_seq RENAME TO vagaspcd_mo64_codigo_seq;");
            }
        }
    }

    // Verifica se a tabela plugins.vagaspcd existe
    private function hasTable()
    {
        $tableExists = DB::select(
            "
                            SELECT EXISTS(
                                SELECT 1 FROM information_schema.tables
                                         WHERE table_schema = 'plugins'
                                         AND table_name = 'vagaspcd'
                                         )
       "
        );
        return $tableExists[0]->exists;
    }

    private function rollbackUpdate()
    {
        if ($this->hasTable()) {
            $columns = DB::select(
                "
                            SELECT column_name FROM information_schema.columns
                                WHERE table_name = 'vagaspcd'
                                    AND table_schema = 'plugins'
                       "
            );

            Schema::table('plugins.vagaspcd', function (Blueprint $table) use ($columns) {
                foreach ($columns as $column) {
                    if (!($column->column_name == 'created_at' || $column->column_name == 'updated_at')) {
                        if (substr($column->column_name, 0, 4) != 'mo62') {
                            $novoNome = str_replace('mo64', 'mo62', $column->column_name);
                            $table->renameColumn($column->column_name, $novoNome);
                        }
                    }
                }
            });

            if (str_contains($this->sequenceName(), 'mo64')) {
                DB::statement("ALTER SEQUENCE vagaspcd_mo64_codigo_seq RENAME TO vagaspcd_mo62_codigo_seq;");
            }
        }
    }

    private function sequenceName()
    {
        $sequenceName = DB::select("
            SELECT c.relname AS sequence_name
            FROM pg_class c
            JOIN pg_depend d ON c.oid = d.objid
            JOIN pg_attrdef a ON d.refobjid = a.adrelid AND d.refobjsubid = a.adnum
            JOIN pg_attribute at ON at.attrelid = a.adrelid AND at.attnum = a.adnum
            JOIN pg_class t ON t.oid = a.adrelid
            WHERE t.relname = 'vagaspcd';
        ");

        return $sequenceName[0]->sequence_name;
    }
}
