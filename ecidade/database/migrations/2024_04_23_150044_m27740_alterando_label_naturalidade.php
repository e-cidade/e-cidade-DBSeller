<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M27740AlterandoLabelNaturalidade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDicionario();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downDicionario();
    }

    public function upDicionario() {
        DB::unprepared(<<<SQL
update db_syscampo set descricao = 'Município de Nascimento', rotulo = 'Município de Nascimento', rotulorel = 'Município de Nascimento' where codcam = 13484;
SQL
        );
    }

    public function downDicionario() {
        DB::unprepared(<<<SQL
update db_syscampo set descricao = 'Naturalidade', rotulo = 'Naturalidade', rotulorel = 'Naturalidade' where codcam = 13484;
SQL
        );
    }
}
