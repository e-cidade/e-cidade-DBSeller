
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24492AlterandoTamanhoCampoEmailTabelaGestor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE escolagestorcenso ALTER COLUMN ed325_email TYPE VARCHAR(100)");
        DB::statement("update db_syscampo set conteudo = 'varchar(100)', tamanho = 100 where codcam = 19796");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE escolagestorcenso ALTER COLUMN ed325_email TYPE VARCHAR(40)");
        DB::statement("update db_syscampo set conteudo = 'varchar(40)', tamanho = 40 where codcam = 19796");
    }
}
