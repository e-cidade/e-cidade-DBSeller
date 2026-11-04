<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25779SigfisCamposRecurso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $campoLayoutFonteRecurso = $this->getLinhaCampo(405, 'Nm_FonteRecurso');
        
        if ($campoLayoutFonteRecurso->count() == 0) {
            DB::insert("insert into configuracoes.db_layoutcampos( db52_codigo ,db52_layoutlinha ,db52_nome ,db52_descr ,db52_layoutformat ,db52_posicao ,db52_default ,db52_tamanho ,db52_ident ,db52_imprimir ,db52_alinha ,db52_obs ,db52_quebraapos ) values ( nextval('db_layoutcampos_db52_codigo_seq') , 405, 'Nm_FonteRecurso', 'Codigo Fonte Recurso', 14, 97, null, 8, 'f', 't', 'd', null, 0)");
        }

        $campoLayoutFonteRecursoGestor = $this->getLinhaCampo(405, 'Nm_FonteRecursoGestor');
        
        if ($campoLayoutFonteRecursoGestor->count() == 0) {
            DB::insert("insert into configuracoes.db_layoutcampos( db52_codigo ,db52_layoutlinha ,db52_nome ,db52_descr ,db52_layoutformat ,db52_posicao ,db52_default ,db52_tamanho ,db52_ident ,db52_imprimir ,db52_alinha ,db52_obs ,db52_quebraapos ) values ( nextval('db_layoutcampos_db52_codigo_seq') ,  405, 'Nm_FonteRecursoGestor', 'Codigo Fonte Recurso Gestor', 14, 105, null, 8, 'f', 't', 'd', null, 0)");
        }

        $campoLayoutEmpenhoRecurso = $this->getLinhaCampo(1013, 'Nm_FonteRecurso');
        
        if ($campoLayoutEmpenhoRecurso->count() == 0) {
            DB::insert("insert into configuracoes.db_layoutcampos( db52_codigo ,db52_layoutlinha ,db52_nome ,db52_descr ,db52_layoutformat ,db52_posicao ,db52_default ,db52_tamanho ,db52_ident ,db52_imprimir ,db52_alinha ,db52_obs ,db52_quebraapos ) values ( nextval('db_layoutcampos_db52_codigo_seq') , 1013, 'Nm_FonteRecurso', 'Codigo Fonte Recurso', 14, 1055, null, 8, 'f', 't', 'd', null, 0)");
        }

        $campoLayoutAltOrcRecurso = $this->getLinhaCampo(408, 'Nm_FonteRecurso');
        
        if ($campoLayoutAltOrcRecurso->count() == 0) {
            DB::insert("insert into configuracoes.db_layoutcampos( db52_codigo ,db52_layoutlinha ,db52_nome ,db52_descr ,db52_layoutformat ,db52_posicao ,db52_default ,db52_tamanho ,db52_ident ,db52_imprimir ,db52_alinha ,db52_obs ,db52_quebraapos ) values ( nextval('db_layoutcampos_db52_codigo_seq') , 408, 'Nm_FonteRecurso', 'Codigo Fonte Recurso', 14, 227, '', 8, 'f', 't', 'd', '', 0)");
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = <<<SQL
        delete from db_layoutcampos where db52_layoutlinha = 405 and db52_nome in ('Nm_FonteRecurso', 'Nm_FonteRecursoGestor');
        delete from db_layoutcampos where db52_layoutlinha = 1013 and db52_nome in ('Nm_FonteRecurso');
        delete from db_layoutcampos where db52_layoutlinha = 408 and db52_nome in ('Nm_FonteRecurso');
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
