<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26121AlteracaoNomeMunicipiosCenso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
update censomunic set ed261_c_nome = 'TABOCAO' where ed261_i_codigo = 1708254;
update censomunic set ed261_c_nome = 'ITAPAJE' where ed261_i_codigo = 2306306;
update censomunic set ed261_c_nome = 'CAMPO GRANDE' where ed261_i_codigo = 2401305;
update censomunic set ed261_c_nome = 'OLHO D AGUA DO BORGES' where ed261_i_codigo = 2408409;
update censomunic set ed261_c_nome = 'AMPARO DO SAO FRANCISCO' where ed261_i_codigo = 2800100;
update censomunic set ed261_c_nome = 'MUQUEM DO SAO FRANCISCO' where ed261_i_codigo = 2922250;
update censomunic set ed261_c_nome = 'SANTA TEREZINHA' where ed261_i_codigo = 2928505;
update censomunic set ed261_c_nome = 'DONA EUZEBIA' where ed261_i_codigo = 3122900;
update censomunic set ed261_c_nome = 'PASSA VINTE' where ed261_i_codigo = 3147808;
update censomunic set ed261_c_nome = 'SAO TOME DAS LETRAS' where ed261_i_codigo = 3165206;
update censomunic set ed261_c_nome = 'BIRITIBA MIRIM' where ed261_i_codigo = 3506607;
update censomunic set ed261_c_nome = 'FLORINEA' where ed261_i_codigo = 3516101;
update censomunic set ed261_c_nome = 'MOGI MIRIM' where ed261_i_codigo = 3530805;
update censomunic set ed261_c_nome = 'SAO LUIZ DO PARAITINGA' where ed261_i_codigo = 3550001;
update censomunic set ed261_c_nome = 'GRAO-PARA' where ed261_i_codigo = 4206108;
update censomunic set ed261_c_nome = 'POXOREU' where ed261_i_codigo = 5107008;
update censomunic set ed261_c_nome = 'SANTO ANTONIO DE LEVERGER' where ed261_i_codigo = 5107800;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = <<<SQL
update censomunic set ed261_c_nome = 'ELDORADO DOS CARAJAS' where ed261_i_codigo = 1502954;
update censomunic set ed261_c_nome = 'FORTALEZA DO TABOCAO' where ed261_i_codigo = 1708254;
update censomunic set ed261_c_nome = 'ITAPAGE' where ed261_i_codigo = 2306306;
update censomunic set ed261_c_nome = 'AUGUSTO SEVERO' where ed261_i_codigo = 2401305;
update censomunic set ed261_c_nome = 'OLHO-D AGUA DO BORGES' where ed261_i_codigo = 2408409;
update censomunic set ed261_c_nome = 'AMPARO DE SAO FRANCISCO' where ed261_i_codigo = 2800100;
update censomunic set ed261_c_nome = 'MUQUEM DE SAO FRANCISCO' where ed261_i_codigo = 2922250;
update censomunic set ed261_c_nome = 'SANTA TERESINHA' where ed261_i_codigo = 2928505;
update censomunic set ed261_c_nome = 'DONA EUSEBIA' where ed261_i_codigo = 3122900;
update censomunic set ed261_c_nome = 'PASSA-VINTE' where ed261_i_codigo = 3147808;
update censomunic set ed261_c_nome = 'SAO THOME DAS LETRAS' where ed261_i_codigo = 3165206;
update censomunic set ed261_c_nome = 'BIRITIBA-MIRIM' where ed261_i_codigo = 3506607;
update censomunic set ed261_c_nome = 'FLORINIA' where ed261_i_codigo = 3516101;
update censomunic set ed261_c_nome = 'MOJI MIRIM' where ed261_i_codigo = 3530805;
update censomunic set ed261_c_nome = 'SAO LUIS DO PARAITINGA' where ed261_i_codigo = 3550001;
update censomunic set ed261_c_nome = 'GRAO PARA' where ed261_i_codigo = 4206108;
update censomunic set ed261_c_nome = 'POXOREO' where ed261_i_codigo = 5107008;
update censomunic set ed261_c_nome = 'SANTO ANTONIO DO LEVERGER' where ed261_i_codigo = 5107800;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
