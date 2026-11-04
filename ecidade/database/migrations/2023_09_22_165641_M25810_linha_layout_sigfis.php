<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M25810LinhaLayoutSigfis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $campoLayoutFonteRecurso = $this->getLinhaCampo(669, 'Nm_FonteRecurso');
        
        if ($campoLayoutFonteRecurso->count() == 0) {
            $insert = <<<SQL
        insert into
            db_layoutcampos
        values
            (
                nextval('db_layoutcampos_db52_codigo_seq'),
                669,
                'Nm_FonteRecurso',
                'Codigo Fonte Recurso',
                14,
                315,
                '',
                8,
                'f',
                't',
                'd',
                '',
                0
            )
SQL;
            DB::insert($insert);
        }


        $campoLayoutSubEmpenho = $this->getLinhaCampo(646, 'nu_EmpenhoSup');
        if ($campoLayoutSubEmpenho->count() == 0) {
            $insert = <<<SQL
        insert into
            db_layoutcampos
        values
            (
                nextval('db_layoutcampos_db52_codigo_seq'),
                646,
                'nu_EmpenhoSup',
                'CODIGO DO SUBEMPENHO',
                14,
                183,
                '',
                10,
                'f',
                't',
                'd',
                '',
                0
            )
SQL;
            DB::insert($insert);
        }

        DB::update("update db_layoutlinha SET db51_compacta = false where  db51_codigo = 646");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = <<<SQL
        delete from db_layoutcampos where db52_layoutlinha = 669 and db52_nome in ('Nm_FonteRecurso');
        delete from db_layoutcampos where db52_layoutlinha = 646 and db52_nome in ('nu_EmpenhoSup');
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    
    private function getLinhaCampo($codigolinha = null, $campo = '')
    {
        return DB::table('db_layoutcampos')
            ->where('db52_layoutlinha', '=', $codigolinha)
            ->where('db52_nome', '=', $campo)->get();
    }
}
