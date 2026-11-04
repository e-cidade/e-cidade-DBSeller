<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23764AjusteLabelsBolsafamilia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
update db_syscampo
    set descricao = 'Bolsa Família', rotulo = 'Bolsa Família', rotulorel = 'Bolsa Família' where codcam = 11226;
            
update db_syscampo
    set descricao = 'Bolsa Família 1-NÃO 2-SIM',
        rotulo = 'Bolsa Família',
        rotulorel = 'Bolsa Família' where codcam = 17148;
            
update db_syscampo
    set descricao = 'Bolsa Família', rotulo = 'Bolsa Família', rotulorel = 'Bolsa Família' where codcam = 21499;
            
update db_syscampo
    set descricao = 'Bolsa Família', rotulo = 'Bolsa Família', rotulorel = 'Bolsa Família' where codcam = 1008304;
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
        DB::connection()->getPdo()->exec(<<<SQL
update db_syscampo
    set descricao = 'Auxílio Brasil', rotulo = 'Auxílio Brasil', rotulorel = 'Auxílio Brasil' where codcam = 11226;
            
update db_syscampo
    set descricao = 'Auxílio Brasil 1-NÃO 2-SIM',
        rotulo = 'Auxílio Brasil',
        rotulorel = 'Auxílio Brasil' where codcam = 17148;
            
update db_syscampo
    set descricao = 'Auxílio Brasil', rotulo = 'Auxílio Brasil', rotulorel = 'Auxílio Brasil' where codcam = 21499;
            
update db_syscampo
    set descricao = 'Auxílio Brasil', rotulo = 'Auxílio Brasil', rotulorel = 'Auxílio Brasil' where codcam = 1008304;
SQL
            );
    }
}
