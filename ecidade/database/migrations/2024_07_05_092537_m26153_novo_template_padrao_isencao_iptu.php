<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M26153NovoTemplatePadraoIsencaoIptu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            update db_documentotemplatepadrao
            set db81_nomearquivo = 'documentos/templates/cadastro/modelo_certidao_isencao_iptu.docx'
            where db81_templatetipo = 44
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("
            update db_documentotemplatepadrao
            set db81_nomearquivo = 'documentos/templates/certidao_isencao_iptu.sxw'
            where db81_templatetipo = 44
        ");
    }
}
