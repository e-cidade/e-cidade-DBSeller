<?php

use Illuminate\Database\Migrations\Migration;

class M25126AlteraNomeCampo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
update db_syscampo set nomecam = 'z01_v_ident', conteudo = 'varchar(20)', descricao = 'Campo para o número do RG', valorinicial = '', rotulo = 'RG', nulo = 't', tamanho = 20, maiusculo = 't', autocompl = 'f', aceitatipo = 0, tipoobj = 'text', rotulorel = 'RG' where codcam = 1008855;
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
        DB::unprepared(<<<SQL
update db_syscampo set nomecam = 'z01_v_ident', conteudo = 'varchar(20)', descricao = 'Identidade', valorinicial = '', rotulo = 'Identidade', nulo = 't', tamanho = 20, maiusculo = 't', autocompl = 'f', aceitatipo = 0, tipoobj = 'text', rotulorel = 'Identidade' where codcam = 1008855;
SQL
        );
    }
}
