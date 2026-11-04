<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25762AlteraCamposLabRequisicao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE laboratorio.lab_requisicao ALTER COLUMN la22_peso TYPE FLOAT;');
        DB::statement('ALTER TABLE laboratorio.lab_requisicao ALTER COLUMN la22_volumeamostra TYPE INTEGER;');

        DB::connection()->getPdo()->exec(<<<SQL

            update db_syscampo set nomecam = 'la22_volumeamostra', conteudo = 'int4', descricao = 'Campo para inserção do volume da amostra coletada do paciente.', valorinicial = '0', rotulo = 'Volume Amostra', nulo = 't', tamanho = 8, maiusculo = 'f', autocompl = 'f', aceitatipo = 1, tipoobj = 'text', rotulorel = 'Volume Amostra' where codcam = 1015474;

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
        DB::statement('ALTER TABLE laboratorio.lab_requisicao ALTER COLUMN la22_peso TYPE NUMERIC(6,3);');
        DB::statement('ALTER TABLE laboratorio.lab_requisicao ALTER COLUMN la22_volumeamostra TYPE NUMERIC (5,3);');

        DB::connection()->getPdo()->exec(<<<SQL

            update db_syscampo set nomecam = 'la22_volumeamostra', conteudo = 'float8', descricao = 'Campo para inserção do volume da amostra coletada do paciente.', valorinicial = '0', rotulo = 'Volume Amostra', nulo = 't', tamanho = 8, maiusculo = 'f', autocompl = 'f', aceitatipo = 4, tipoobj = 'text', rotulorel = 'Volume Amostra' where codcam = 1015474;

SQL
        );
    }
}
